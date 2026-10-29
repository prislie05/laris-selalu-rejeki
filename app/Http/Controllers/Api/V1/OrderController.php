<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Requests\UserCheckoutRequest;
use App\Http\Resources\OrderResource;
use App\Models\Cart;
use App\Models\FinancialLedger;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Services\AuditLogger;
use App\Services\ReceiptService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class OrderController extends BaseApiController
{
    /**
     * User checkout (either selected cart items or buy-now direct).
     */
    public function checkout(UserCheckoutRequest $request): JsonResponse
    {
        $user = $request->user();
        $validated = $request->validated();
        $paymentMethod = $validated['payment_method'];
        $notes = $validated['notes'] ?? null;

        $itemsToProcess = [];

        if (!empty($validated['product_id'])) {
            // Buy now directly
            $product = Product::findOrFail($validated['product_id']);
            $qty = (int) ($validated['quantity'] ?? 1);

            if ($product->stock < $qty || $product->stock <= 0) {
                return $this->errorResponse('Stok barang tidak mencukupi.', [
                    'stock' => ["Stok {$product->name} tersisa {$product->stock}."],
                ], 422);
            }

            $itemsToProcess[] = [
                'product' => $product,
                'quantity' => $qty,
                'cart_id' => null,
            ];
        } else {
            // Checkout from selected cart items
            $selectedCarts = Cart::with('product')
                ->where('user_id', $user->id)
                ->where('is_selected', true)
                ->get();

            if ($selectedCarts->isEmpty()) {
                return $this->errorResponse('Tidak ada barang yang dipilih untuk dibeli.', [
                    'cart' => ['Pilih minimal 1 barang di keranjang.'],
                ], 422);
            }

            foreach ($selectedCarts as $cart) {
                $product = $cart->product;
                if (!$product || $product->stock < $cart->quantity || $product->stock <= 0) {
                    return $this->errorResponse("Stok barang {$product?->name} tidak mencukupi atau habis.", [
                        'stock' => ["Barang {$product?->name} stok tidak mencukupi."],
                    ], 422);
                }

                $itemsToProcess[] = [
                    'product' => $product,
                    'quantity' => $cart->quantity,
                    'cart_id' => $cart->id,
                ];
            }
        }

        // DB Transaction for ACID execution
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

                // Reduce stock with lock check
                $product->decrement('stock', $qty);

                if ($item['cart_id']) {
                    $cartIdsToDelete[] = $item['cart_id'];
                }
            }

            // Remove processed items from cart
            if (!empty($cartIdsToDelete)) {
                Cart::whereIn('id', $cartIdsToDelete)->delete();
            }

            // Auto-record to Financial Ledger
            FinancialLedger::create([
                'user_id' => $user->id,
                'order_id' => $order->id,
                'type' => 'income',
                'category' => 'Penjualan Etalase',
                'amount' => $totalAmount,
                'description' => "Pembayaran pesanan online {$order->order_number} ({$user->name})",
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

        $order->load(['items', 'user']);

        return $this->successResponse('Pembayaran berhasil! Pesanan Anda telah lunas.', [
            'order' => new OrderResource($order),
        ], 201);
    }

    /**
     * List order history.
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        $query = Order::with(['items', 'seller', 'user'])->orderBy('id', 'desc');

        if ($user->isUser()) {
            $query->where('user_id', $user->id);
        }

        if ($request->filled('source')) {
            $query->where('source', $request->input('source'));
        }

        $orders = $query->paginate(20);

        return $this->successResponse('Riwayat transaksi.', [
            'orders' => OrderResource::collection($orders),
            'pagination' => [
                'current_page' => $orders->currentPage(),
                'last_page' => $orders->lastPage(),
                'total' => $orders->total(),
            ],
        ]);
    }

    /**
     * Show single order detail.
     */
    public function show(Request $request, int $id): JsonResponse
    {
        $user = $request->user();
        $query = Order::with(['items', 'seller', 'user']);

        if ($user->isUser()) {
            $query->where('user_id', $user->id);
        }

        $order = $query->findOrFail($id);

        return $this->successResponse('Detail pesanan.', [
            'order' => new OrderResource($order),
        ]);
    }

    /**
     * Download or stream PDF receipt.
     */
    public function downloadReceipt(Request $request, int $id, ReceiptService $receiptService): mixed
    {
        $user = $request->user();
        $query = Order::query();

        if ($user && $user->isUser()) {
            $query->where('user_id', $user->id);
        }

        $order = $query->findOrFail($id);
        $pdf = $receiptService->generatePdf($order);

        return $pdf->download("struk-{$order->order_number}.pdf");
    }
}
