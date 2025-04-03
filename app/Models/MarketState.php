<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MarketState extends Model
{
    use HasFactory;

    protected $fillable = [
        'market',
        'market_status',
        'trade_date',
        'index',
        'last',
        'variation',
        'percent_change',
        'market_status_message',
        'expiry_date',
        'underlying',
        'updated_time',
        'slick_class',
    ];

    protected $casts = [
        'trade_date' => 'datetime',
        'updated_time' => 'datetime',
        'percent_change' => 'float',
    ];
}
