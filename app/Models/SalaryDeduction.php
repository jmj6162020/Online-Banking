<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SalaryDeduction extends Model
{
    protected $fillable = [
        'student_1',
        'student_1_yrlvl',

        'student_2',
        'student_2_yrlvl',

        'student_3',
        'student_3_yrlvl',

        'student_4',
        'student_4_yrlvl',

        'starting_date',
        'ending_date',
        'amount',

        'order_id',
    ];

    protected function casts()
    {
        return [
            'starting_date' => 'date',
            'ending_date' => 'date',
        ];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}
