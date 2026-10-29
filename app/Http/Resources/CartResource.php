<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CartResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $product = $this->product;
        $unitPrice = $product ? (float) $product->price : 0;
        $subtotal = $unitPrice * $this->quantity;

        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'product_id' => $this->product_id,
            'quantity' => (int) $this->quantity,
            'is_selected' => (bool) $this->is_selected,
            'product' => new ProductResource($this->whenLoaded('product')),
            'subtotal' => $subtotal,
            'formatted_subtotal' => 'Rp ' . number_format($subtotal, 0, ',', '.'),
            'can_be_checked_out' => $product && (int) $product->stock >= $this->quantity && (int) $product->stock > 0,
        ];
    }
}
