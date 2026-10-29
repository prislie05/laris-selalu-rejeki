<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
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
            'name' => $this->name,
            'slug' => $this->slug,
            'category' => $this->category,
            'description' => $this->description,
            'price' => (float) $this->price,
            'formatted_price' => 'Rp ' . number_format((float) $this->price, 0, ',', '.'),
            'stock' => (int) $this->stock,
            'image_url' => $this->image_url,
            'is_active' => (bool) $this->is_active,
            'is_out_of_stock' => (int) $this->stock <= 0,
            'is_low_stock' => (int) $this->stock > 0 && (int) $this->stock <= 5,
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
