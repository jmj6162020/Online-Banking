<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Image extends Model
{
    protected $appends = ['public_url'];

    protected $fillable = [
        'path',
    ];

    public function imageable(): MorphTo
    {
        return $this->morphTo();
    }

    public function publicUrl(): Attribute
    {
        return new Attribute(
            get: fn () => asset('storage/'.$this->path),
        );
    }
}
