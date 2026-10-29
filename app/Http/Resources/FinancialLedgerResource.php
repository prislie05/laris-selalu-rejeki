<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FinancialLedgerResource extends JsonResource
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
            'user_id' => $this->user_id,
            'order_id' => $this->order_id,
            'type' => $this->type,
            'category' => $this->category,
            'amount' => (float) $this->amount,
            'formatted_amount' => 'Rp ' . number_format((float) $this->amount, 0, ',', '.'),
            'description' => $this->description,
            'transaction_date' => $this->transaction_date?->format('Y-m-d'),
            'user_name' => $this->user?->name,
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
