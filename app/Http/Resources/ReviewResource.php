<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReviewResource extends JsonResource
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
            'rating' => $this->rating,
            'comment' => $this->comment,
            'item' => new OrderItemResource($this->orderItem),
            'created_at' => $this->created_at,
        ];
    }
}
