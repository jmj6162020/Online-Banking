<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Scout\Searchable;

class Product extends Model
{
    use Searchable;
    use SoftDeletes;

    protected $appends = ['price_range', 'stock', 'total_sold'];

    protected $fillable = [
        'category_id',
        'name',
        'campus',
        'sku',
        'details',
    ];

    public function toSearchableArray()
    {
        return [
            'name' => $this->name,
            'sku' => $this->sku,
        ];
    }

    public function priceRange(): Attribute
    {
        return new Attribute(
            get: fn () => $this->calculatePriceRange(),
        );
    }

    private function calculatePriceRange(): string
    {
        $prices = $this->variants()->pluck('price');

        if ($prices->isEmpty()) {
            return 'N/A';
        }

        $minPrice = $prices->min();
        $maxPrice = $prices->max();

        return $minPrice === $maxPrice
            ? number_format($minPrice, 2)
            : number_format($minPrice, 2).' - '.number_format($maxPrice, 2);
    }

    public function stock(): Attribute
    {
        return new Attribute(
            get: fn () => $this->variants()->sum('quantity'),
        );
    }

    public function totalSold(): Attribute
    {
        return new Attribute(
            get: fn () => $this->variants->sum('total_sold'),
        );
    }

    public function rating(): Attribute
    {
        return new Attribute(
            get: fn () => $this->reviews()->avg('rating'),
        );
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function images(): MorphMany
    {
        return $this->morphMany(Image::class, 'imageable');
    }

    public function variants(): HasMany
    {
        return $this->hasMany(Variant::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function reviews(): HasManyThrough
    {
        return $this->hasManyThrough(
            Review::class,
            OrderItem::class,
            'product_id',
            'order_item_id',
            'id',
            'id',
        );
    }
}
