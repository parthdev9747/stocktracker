<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IndicativeNifty50 extends Model
{
    use HasFactory;

    protected $fillable = [
        'date_time',
        'indicative_time',
        'index_name',
        'index_last',
        'index_perc_change',
        'index_time_val',
        'closing_value',
        'final_closing_value',
        'change',
        'per_change',
        'status',
    ];

    protected $casts = [
        'date_time' => 'datetime',
        'indicative_time' => 'datetime',
        'index_last' => 'float',
        'index_perc_change' => 'float',
        'closing_value' => 'float',
        'final_closing_value' => 'float',
        'change' => 'float',
        'per_change' => 'float',
    ];
}