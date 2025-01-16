<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Laravel\Scout\Searchable;

class Order extends Model
{
    use Searchable;

    protected $fillable = [
        'tracking_number',
        'user_id',
        'total_amount',
        'status',
        'campus',
        'payment_type',
    ];

    public function toSearchableArray()
    {
        return [
            'tracking_number' => $this->tracking_number,
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function cancel(): void
    {
        $this->update(['status' => 'cancelled']);

        foreach ($this->items as $item) {
            $product = $item->product;
            $product->increment('stock', $item->quantity);
        }
    }

    public function salaryDeduction()
    {
        return $this->hasOne(SalaryDeduction::class);
    }
}
