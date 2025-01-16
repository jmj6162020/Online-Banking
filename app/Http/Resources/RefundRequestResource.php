<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RefundRequestResource extends JsonResource
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
            'campus' => $this->campus,
            'user' => $this->user,
            'item' => new OrderItemResource($this->item),
            'reason' => $this->reason,
            'images' => $this->images,
            'created_at' => $this->created_at,
            'quantity' => $this->quantity,
            'total' => $this->total,
            'status' => $this->status,
        ];
    }
}
