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
            'category_id' => $this->category_id,
            'category' => $this->category,
            'name' => $this->name,
            'campus' => $this->campus,
            'sku' => $this->sku,
            'details' => $this->details,
            'images' => $this->images,
            'variants' => $this->variants,
            'reviews' => $this->reviews->load('user'),
            'price_range' => $this->price_range,
            'stock' => $this->stock,
            'total_sold' => $this->total_sold,
            'rating' => $this->rating,
        ];
    }
}
