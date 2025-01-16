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
            'tracking_number' => $this->tracking_number,
            'user' => $this->user,
            'items' => new OrderItemCollection($this->items),
            'total_amount' => $this->total_amount,
            'status' => $this->status,
            'campus' => $this->campus,
            'created_at' => $this->created_at,
            'payment_type' => $this->payment_type,
            'salary_deduction' => $this->salaryDeduction,
        ];
    }
}
