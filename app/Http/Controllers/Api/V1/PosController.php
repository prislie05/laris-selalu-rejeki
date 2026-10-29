<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Requests\PosCheckoutRequest;
use App\Http\Resources\OrderResource;
use App\Models\FinancialLedger;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Services\AuditLogger;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PosController extends BaseApiController
{
    /**
     * Process POS cashier transaction.
     */
    public function checkout(PosCheckoutRequest $request): JsonResponse
    {
        $seller = $request->user();
        $validated = $request->validated();
        $paymentMethod = $validated['payment_method'];
        $customerName = $validated['customer_name'] ?? 'Pelanggan Walk-In';
        $itemsInput = $validated['items'];

        // Step 1: Pre-calculate total and validate stocks
        $totalAmount = 0;
        $itemsToProcess = [];

        foreach ($itemsInput as $input) {
            $product = Product::findOrFail($input['product_id']);
            $qty = (int) $input['quantity'];

            if ($product->stock < $qty) {
                return $this->errorResponse("Stok tidak mencukupi untuk {$product->name}.", [
                    'stock' => ["Stok {$product->name} tersisa {$product->stock}, permintaan: {$qty}."],
                ], 422);
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

        // Validate cash received
        $cashReceived = null;
        $cashChange = null;

        if ($paymentMethod === 'cash') {
            $cashReceived = (float) ($validated['cash_received'] ?? 0);
            if ($cashReceived < $totalAmount) {
                return $this->errorResponse('Uang yang diterima kurang dari total belanja.', [
                    'cash_received' => [
                        "Total belanja: Rp " . number_format($totalAmount, 0, ',', '.') .
                        ", uang diterima: Rp " . number_format($cashReceived, 0, ',', '.') . "."
                    ],
                ], 422);
            }
            $cashChange = $cashReceived - $totalAmount;
        }

        // DB Transaction for atomic POS order
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
                'user_id' => null, // Walk-in customer
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

        $order->load(['items', 'seller']);

        return $this->successResponse('Transaksi POS berhasil! Struk telah diterbitkan.', [
            'order' => new OrderResource($order),
            'receipt_url' => route('orders.receipt', $order->id),
        ], 201);
    }
}
