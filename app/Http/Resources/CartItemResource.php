<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CartItemResource extends JsonResource
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
            'user' => $this->user,
            'guest_id' => $this->guest_id,
            'product' => new ProductResource($this->product),
            'variant' => $this->variant,
            'quantity' => $this->quantity,
            'selected' => $this->selected,
        ];
    }
}
