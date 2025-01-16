<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderItemResource extends JsonResource
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
            'order' => $this->order,
            'product' => new ProductResource($this->product),
            'variant' => $this->variant,
            'quantity' => $this->quantity,
            'refund_request' => $this->refundRequest,
            'review' => $this->review,
        ];
    }
}
