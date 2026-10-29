<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserCheckoutRequest;
use App\Models\Cart;
use App\Models\FinancialLedger;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Services\AuditLogger;
use App\Services\ReceiptService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;

class UserController extends Controller
{
    /**
     * Tab 1: Belanja (Shopping Catalog).
     */
    public function shop(Request $request): View
    {
        $search = $request->input('search');
        $selectedCategory = $request->input('category', 'Semua');

        $categories = Product::where('is_active', true)
            ->distinct()
            ->pluck('category')
            ->toArray();

        $query = Product::where('is_active', true);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('category', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($selectedCategory && $selectedCategory !== 'Semua') {
            $query->where('category', $selectedCategory);
        }

        $products = $query->orderBy('id', 'desc')->get();
        $cartCount = Cart::where('user_id', Auth::id())->sum('quantity');

        return view('user.shop', compact('products', 'categories', 'search', 'selectedCategory', 'cartCount'));
    }

    /**
     * Tab 2: Keranjang (Shopping Cart).
     */
    public function cart(): View
    {
        $user = Auth::user();
        $carts = Cart::with('product')
            ->where('user_id', $user->id)
            ->get();

        // Update items that might have gone out of stock
        foreach ($carts as $cart) {
            if ($cart->product && $cart->product->stock <= 0 && $cart->is_selected) {
                $cart->is_selected = false;
                $cart->save();
            }
        }

        $selectedTotal = $carts->where('is_selected', true)
            ->filter(fn ($c) => $c->product && $c->product->stock > 0)
            ->sum(fn ($c) => (float) $c->product->price * $c->quantity);

        return view('user.cart', compact('carts', 'selectedTotal'));
    }

    /**
     * Add product to cart.
     */
    public function addToCart(Request $request): RedirectResponse
    {
        $request->validate([
            'product_id' => ['required', 'exists:products,id'],
            'quantity' => ['nullable', 'integer', 'min:1'],
        ]);

        $product = Product::findOrFail($request->product_id);

        if ($product->stock <= 0) {
            return back()->with('error', "Maaf, stok {$product->name} sedang habis.");
        }

        $qty = (int) ($request->quantity ?? 1);
        $cart = Cart::where('user_id', Auth::id())->where('product_id', $product->id)->first();

        if ($cart) {
            $cart->quantity = min($cart->quantity + $qty, $product->stock);
            $cart->is_selected = true;
            $cart->save();
        } else {
            Cart::create([
                'user_id' => Auth::id(),
                'product_id' => $product->id,
                'quantity' => min($qty, $product->stock),
                'is_selected' => true,
            ]);
        }

        return back()->with('success', "{$product->name} berhasil ditambahkan ke keranjang!");
    }

    /**
     * Update cart item quantity or selected status.
     */
    public function updateCart(Request $request, int $id): RedirectResponse
    {
        $cart = Cart::with('product')->where('user_id', Auth::id())->findOrFail($id);

        if ($request->has('action')) {
            if ($request->action === 'increment') {
                if ($cart->quantity < $cart->product->stock) {
                    $cart->increment('quantity');
                } else {
                    return back()->with('error', "Stok maksimal {$cart->product->stock} tercapai.");
                }
            } elseif ($request->action === 'decrement') {
                if ($cart->quantity > 1) {
                    $cart->decrement('quantity');
                } else {
                    $cart->delete();
                    return back()->with('success', 'Barang dihapus dari keranjang.');
                }
            } elseif ($request->action === 'toggle_select') {
                if ($cart->product->stock > 0) {
                    $cart->is_selected = !$cart->is_selected;
                    $cart->save();
                } else {
                    return back()->with('error', 'Barang habis tidak dapat dicentang.');
                }
            }
        }

        return back();
    }

    /**
     * Remove item from cart.
     */
    public function removeFromCart(int $id): RedirectResponse
    {
        Cart::where('user_id', Auth::id())->where('id', $id)->delete();
        return back()->with('success', 'Barang berhasil dihapus dari keranjang.');
    }

    /**
     * Process checkout for selected cart items or buy-now direct item.
     */
    public function checkout(UserCheckoutRequest $request): RedirectResponse
    {
        $user = Auth::user();
        $validated = $request->validated();
        $paymentMethod = $validated['payment_method'];
        $notes = $validated['notes'] ?? null;

        $itemsToProcess = [];

        if (!empty($validated['product_id'])) {
            $product = Product::findOrFail($validated['product_id']);
            $qty = (int) ($validated['quantity'] ?? 1);

            if ($product->stock < $qty || $product->stock <= 0) {
                return back()->with('error', "Stok {$product->name} tidak mencukupi atau habis.");
            }

            $itemsToProcess[] = [
                'product' => $product,
                'quantity' => $qty,
                'cart_id' => null,
            ];
        } else {
            $selectedCarts = Cart::with('product')
                ->where('user_id', $user->id)
                ->where('is_selected', true)
                ->get();

            if ($selectedCarts->isEmpty()) {
                return back()->with('error', 'Silakan centang minimal 1 barang di keranjang sebelum checkout.');
            }

            foreach ($selectedCarts as $cart) {
                $product = $cart->product;
                if (!$product || $product->stock < $cart->quantity || $product->stock <= 0) {
                    return back()->with('error', "Stok barang {$product?->name} tidak mencukupi atau telah habis.");
                }

                $itemsToProcess[] = [
                    'product' => $product,
                    'quantity' => $cart->quantity,
                    'cart_id' => $cart->id,
                ];
            }
        }

        $order = DB::transaction(function () use ($user, $itemsToProcess, $paymentMethod, $notes) {
            $totalAmount = 0;
            foreach ($itemsToProcess as $item) {
                $totalAmount += ((float) $item['product']->price * $item['quantity']);
            }

            $orderNumber = 'ORD-' . date('Ymd') . '-' . strtoupper(Str::random(6));

            $order = Order::create([
                'order_number' => $orderNumber,
                'user_id' => $user->id,
                'seller_id' => null,
                'customer_name' => $user->name,
                'source' => 'user_checkout',
                'total_amount' => $totalAmount,
                'payment_method' => $paymentMethod,
                'cash_received' => null,
                'cash_change' => null,
                'status' => 'paid',
                'paid_at' => Carbon::now(),
                'notes' => $notes,
            ]);

            $cartIdsToDelete = [];

            foreach ($itemsToProcess as $item) {
                /** @var Product $product */
                $product = $item['product'];
                $qty = $item['quantity'];
                $subtotal = (float) $product->price * $qty;

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'unit_price' => $product->price,
                    'quantity' => $qty,
                    'subtotal' => $subtotal,
                ]);

                $product->decrement('stock', $qty);

                if ($item['cart_id']) {
                    $cartIdsToDelete[] = $item['cart_id'];
                }
            }

            if (!empty($cartIdsToDelete)) {
                Cart::whereIn('id', $cartIdsToDelete)->delete();
            }

            FinancialLedger::create([
                'user_id' => $user->id,
                'order_id' => $order->id,
                'type' => 'income',
                'category' => 'Penjualan Etalase',
                'amount' => $totalAmount,
                'description' => "Pembelian online {$order->order_number} ({$user->name})",
                'transaction_date' => Carbon::today()->toDateString(),
            ]);

            AuditLogger::log(
                action: 'CHECKOUT_USER',
                model: $order,
                newValues: [
                    'order_number' => $order->order_number,
                    'total_amount' => $totalAmount,
                    'payment_method' => $paymentMethod,
                ]
            );

            return $order;
        });

        return redirect()->route('user.history')->with('success', "Pembayaran Berhasil! Pesanan #{$order->order_number} telah Lunas.");
    }

    /**
     * Tab 3: History (Pesanan Saya).
     */
    public function history(): View
    {
        $orders = Order::with('items')
            ->where('user_id', Auth::id())
            ->orderBy('id', 'desc')
            ->get();

        return view('user.history', compact('orders'));
    }

    /**
     * Download receipt PDF for user order.
     */
    public function downloadReceipt(int $id, ReceiptService $receiptService): mixed
    {
        $order = Order::where('user_id', Auth::id())->findOrFail($id);
        $pdf = $receiptService->generatePdf($order);

        return $pdf->download("struk-{$order->order_number}.pdf");
    }
}
