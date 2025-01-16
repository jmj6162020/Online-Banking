<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Laravel\Scout\Searchable;

class RefundRequest extends Model
{
    use Searchable;

    protected $fillable = [
        'user_id',
        'reason',
        'status',
        'campus',
        'quantity',
        'total',
        'category',
    ];

    public function toSearchableArray()
    {
        return array_merge($this->toArray(), [
            'order.tracking_number' => $this->item->order->tracking_number,
        ]);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(OrderItem::class, 'order_item_id', 'id');
    }

    public function images(): MorphMany
    {
        return $this->morphMany(Image::class, 'imageable');
    }
}
