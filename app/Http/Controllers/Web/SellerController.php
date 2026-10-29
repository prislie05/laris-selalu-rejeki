<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\PosCheckoutRequest;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
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

class SellerController extends Controller
{
    /**
     * Tab 1: Toko & Kasir POS.
     */
    public function shop(Request $request): View
    {
        $products = Product::where('is_active', true)->orderBy('name', 'asc')->get();

        // Stok Tertipis (Urutkan dari stok paling sedikit)
        $thinnestProducts = Product::where('is_active', true)
            ->orderBy('stock', 'asc')
            ->limit(5)
            ->get();

        $recentPosOrder = null;
        if ($request->session()->has('last_order_id')) {
            $recentPosOrder = Order::with('items')->find($request->session()->get('last_order_id'));
        }

        return view('seller.shop', compact('products', 'thinnestProducts', 'recentPosOrder'));
    }

    /**
     * Process Kasir POS transaction.
     */
    public function posCheckout(PosCheckoutRequest $request): RedirectResponse
    {
        $seller = Auth::user();
        $validated = $request->validated();
        $paymentMethod = $validated['payment_method'];
        $customerName = $validated['customer_name'] ?? 'Pelanggan Toko';
        $itemsInput = $validated['items'];

        $totalAmount = 0;
        $itemsToProcess = [];

        foreach ($itemsInput as $input) {
            $product = Product::findOrFail($input['product_id']);
            $qty = (int) $input['quantity'];

            if ($product->stock < $qty) {
                return back()->with('error', "Stok {$product->name} tidak cukup (sisa: {$product->stock}).")->withInput();
            }

            $subtotal = (float) $product->price * $qty;
            $totalAmount += $subtotal;

            $itemsToProcess[] = [
                'product' => $product,
                'quantity' => $qty,
                'unit_price' => (float) $product->price,
                'subtotal' => $subtotal,
            ];
        }

        $cashReceived = null;
        $cashChange = null;

        if ($paymentMethod === 'cash') {
            $cashReceived = (float) ($validated['cash_received'] ?? 0);
            if ($cashReceived < $totalAmount) {
                return back()->with('error', "Uang yang diterima (Rp " . number_format($cashReceived, 0, ',', '.') .
                    ") kurang dari total belanja (Rp " . number_format($totalAmount, 0, ',', '.') . ")!")->withInput();
            }
            $cashChange = $cashReceived - $totalAmount;
        }

        $order = DB::transaction(function () use (
            $seller,
            $itemsToProcess,
            $totalAmount,
            $paymentMethod,
            $cashReceived,
            $cashChange,
            $customerName,
            $validated
        ) {
            $orderNumber = 'POS-' . date('Ymd') . '-' . strtoupper(Str::random(6));

            $order = Order::create([
                'order_number' => $orderNumber,
                'user_id' => null,
                'seller_id' => $seller->id,
                'customer_name' => $customerName,
                'source' => 'pos_cashier',
                'total_amount' => $totalAmount,
                'payment_method' => $paymentMethod,
                'cash_received' => $cashReceived,
                'cash_change' => $cashChange,
                'status' => 'paid',
                'paid_at' => Carbon::now(),
                'notes' => $validated['notes'] ?? 'Transaksi Kasir POS',
            ]);

            foreach ($itemsToProcess as $item) {
                /** @var Product $product */
                $product = $item['product'];
                $qty = $item['quantity'];

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'unit_price' => $item['unit_price'],
                    'quantity' => $qty,
                    'subtotal' => $item['subtotal'],
                ]);

                $product->decrement('stock', $qty);
            }

            FinancialLedger::create([
                'user_id' => $seller->id,
                'order_id' => $order->id,
                'type' => 'income',
                'category' => 'Penjualan Kasir POS',
                'amount' => $totalAmount,
                'description' => "Kasir POS {$order->order_number} ({$customerName})",
                'transaction_date' => Carbon::today()->toDateString(),
            ]);

            AuditLogger::log(
                action: 'POS_SALE',
                model: $order,
                newValues: [
                    'order_number' => $order->order_number,
                    'total_amount' => $totalAmount,
                    'payment_method' => $paymentMethod,
                    'cash_received' => $cashReceived,
                    'cash_change' => $cashChange,
                ]
            );

            return $order;
        });

        return redirect()->route('seller.history')
            ->with('success', "Transaksi Kasir POS Berhasil! Struk #{$order->order_number} tercetak.")
            ->with('last_order_id', $order->id);
    }

    /**
     * Tab 2: Edit & Manajemen Produk (Create, Update, Delete).
     */
    public function products(Request $request): View
    {
        $search = $request->input('search');
        $query = Product::orderBy('id', 'desc');

        if ($search) {
            $query->where('name', 'like', "%{$search}%")->orWhere('category', 'like', "%{$search}%");
        }

        $products = $query->get();
        $categories = ['Sembako', 'Makanan Ringan', 'Minuman', 'Kebutuhan Rumah', 'Bumbu Dapur', 'Lainnya'];

        return view('seller.products', compact('products', 'categories', 'search'));
    }

    /**
     * Store new product.
     */
    public function storeProduct(StoreProductRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $validated['slug'] = Str::slug($validated['name']) . '-' . Str::random(5);
        $validated['is_active'] = true;

        Product::create($validated);

        return back()->with('success', "Barang '{$validated['name']}' berhasil ditambahkan ke etalase!");
    }

    /**
     * Update product.
     */
    public function updateProduct(UpdateProductRequest $request, int $id): RedirectResponse
    {
        $product = Product::findOrFail($id);
        $product->update($request->validated());

        return back()->with('success', "Barang '{$product->name}' berhasil diperbarui!");
    }

    /**
     * Delete product.
     */
    public function destroyProduct(int $id): RedirectResponse
    {
        $product = Product::findOrFail($id);
        $name = $product->name;
        $product->delete();

        return back()->with('success', "Barang '{$name}' berhasil dihapus dari etalase.");
    }

    /**
     * Tab 3: History (Penjualan Toko & Download Struk).
     */
    public function history(Request $request): View
    {
        $query = Order::with(['items', 'seller', 'user'])->orderBy('id', 'desc');

        if ($request->filled('source')) {
            $query->where('source', $request->input('source'));
        }

        $orders = $query->get();

        $highlightOrderId = session('last_order_id');
        $highlightOrder = $highlightOrderId ? Order::with(['items', 'seller', 'user'])->find($highlightOrderId) : null;

        return view('seller.history', compact('orders', 'highlightOrder'));
    }

    /**
     * Download receipt PDF.
     */
    public function downloadReceipt(int $id, ReceiptService $receiptService): mixed
    {
        $order = Order::findOrFail($id);
        $pdf = $receiptService->generatePdf($order);

        return $pdf->download("struk-{$order->order_number}.pdf");
    }
}
