<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'order_number' => $this->order_number,
            'user_id' => $this->user_id,
            'seller_id' => $this->seller_id,
            'customer_name' => $this->customer_name ?: ($this->user?->name ?? 'Pelanggan Walk-In'),
            'source' => $this->source,
            'total_amount' => (float) $this->total_amount,
            'formatted_total_amount' => 'Rp ' . number_format((float) $this->total_amount, 0, ',', '.'),
            'payment_method' => strtoupper($this->payment_method),
            'cash_received' => $this->cash_received !== null ? (float) $this->cash_received : null,
            'formatted_cash_received' => $this->cash_received !== null ? 'Rp ' . number_format((float) $this->cash_received, 0, ',', '.') : null,
            'cash_change' => $this->cash_change !== null ? (float) $this->cash_change : null,
            'formatted_cash_change' => $this->cash_change !== null ? 'Rp ' . number_format((float) $this->cash_change, 0, ',', '.') : null,
            'status' => $this->status,
            'paid_at' => $this->paid_at?->toIso8601String(),
            'notes' => $this->notes,
            'items' => OrderItemResource::collection($this->whenLoaded('items')),
            'seller' => new UserResource($this->whenLoaded('seller')),
            'user' => new UserResource($this->whenLoaded('user')),
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
