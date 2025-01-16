<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Variant extends Model
{
    protected $appends = ['total_sold'];

    protected $fillable = [
        'name',
        'price',
        'quantity',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function totalSold(): Attribute
    {
        return new Attribute(
            get: function () {
                return $this->orderItems()
                    ->whereHas('order', function ($query) {
                        $query->where('status', 'completed');
                    })
                    ->sum('quantity');
            }
        );
    }
}
