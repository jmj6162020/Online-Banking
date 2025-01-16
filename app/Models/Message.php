<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    protected $fillable = [
        'campus',
        'first_name',
        'last_name',
        'email',
        'contact_number',
        'message',
        'read',
    ];
}
