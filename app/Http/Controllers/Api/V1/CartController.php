<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Requests\StoreCartItemRequest;
use App\Http\Resources\CartResource;
use App\Models\Cart;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CartController extends BaseApiController
{
    /**
     * Display user's cart items with computed total.
     */
    public function index(Request $request): JsonResponse
    {
        $carts = Cart::with('product')
            ->where('user_id', $request->user()->id)
            ->get();

        $selectedTotal = 0;
        $totalItems = 0;

        foreach ($carts as $cart) {
            $product = $cart->product;
            // Auto uncheck if out of stock
            if ($product && $product->stock <= 0 && $cart->is_selected) {
                $cart->is_selected = false;
                $cart->save();
            }

            if ($cart->is_selected && $product && $product->stock > 0) {
                $selectedTotal += ((float) $product->price * $cart->quantity);
                $totalItems += $cart->quantity;
            }
        }

        return $this->successResponse('Keranjang belanja.', [
            'items' => CartResource::collection($carts),
            'selected_total' => $selectedTotal,
            'formatted_selected_total' => 'Rp ' . number_format($selectedTotal, 0, ',', '.'),
            'total_items_count' => $totalItems,
        ]);
    }

    /**
     * Add product to cart.
     */
    public function store(StoreCartItemRequest $request): JsonResponse
    {
        $userId = $request->user()->id;
        $productId = $request->input('product_id');
        $quantity = (int) $request->input('quantity', 1);

        $product = Product::findOrFail($productId);

        if ($product->stock <= 0) {
            return $this->errorResponse('Barang ini sedang habis stok.', [
                'stock' => ['Stok barang sudah habis.'],
            ], 422);
        }

        $cart = Cart::where('user_id', $userId)
            ->where('product_id', $productId)
            ->first();

        if ($cart) {
            $newQuantity = $cart->quantity + $quantity;
            if ($newQuantity > $product->stock) {
                $newQuantity = $product->stock;
            }
            $cart->update([
                'quantity' => $newQuantity,
                'is_selected' => true,
            ]);
        } else {
            $cart = Cart::create([
                'user_id' => $userId,
                'product_id' => $productId,
                'quantity' => min($quantity, $product->stock),
                'is_selected' => true,
            ]);
        }

        $cart->load('product');

        return $this->successResponse('Barang berhasil dimasukkan ke keranjang.', [
            'item' => new CartResource($cart),
        ], 201);
    }

    /**
     * Update cart item quantity or selection state.
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $cart = Cart::with('product')
            ->where('user_id', $request->user()->id)
            ->findOrFail($id);

        if ($request->has('is_selected')) {
            $isSelected = (bool) $request->input('is_selected');
            if ($isSelected && $cart->product->stock <= 0) {
                return $this->errorResponse('Barang yang habis tidak dapat dipilih.', [
                    'stock' => ['Stok barang 0 (Habis).'],
                ], 422);
            }
            $cart->is_selected = $isSelected;
        }

        if ($request->has('quantity')) {
            $newQty = (int) $request->input('quantity');
            if ($newQty <= 0) {
                $cart->delete();
                return $this->successResponse('Barang dihapus dari keranjang.');
            }
            if ($newQty > $cart->product->stock) {
                return $this->errorResponse('Jumlah melebihi stok yang tersedia.', [
                    'stock' => ["Stok tersisa hanya {$cart->product->stock}."],
                ], 422);
            }
            $cart->quantity = $newQty;
        }

        $cart->save();

        return $this->successResponse('Keranjang berhasil diperbarui.', [
            'item' => new CartResource($cart),
        ]);
    }

    /**
     * Remove item from cart.
     */
    public function destroy(Request $request, int $id): JsonResponse
    {
        $cart = Cart::where('user_id', $request->user()->id)->findOrFail($id);
        $cart->delete();

        return $this->successResponse('Barang berhasil dihapus dari keranjang.');
    }
}
