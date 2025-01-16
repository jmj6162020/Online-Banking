<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class CartItem extends Model
{
    protected $fillable = [
        'user_id',
        'guest_id',
        'product_id',
        'variant_id',
        'quantity',
        'selected',
    ];

    public function scopeCurrentSession(Builder $query): Builder
    {
        $userId = Auth::id();
        $guestId = Session::get('guest_id');

        return $query->where(function ($query) use ($userId, $guestId) {
            if ($userId) {
                $query->where('user_id', $userId);
            } else {
                $query->where('guest_id', $guestId);
            }
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function variant(): BelongsTo
    {
        return $this->belongsTo(Variant::class);
    }
}
