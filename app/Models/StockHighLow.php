<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockHighLow extends Model
{
    use HasFactory;

    protected $fillable = [
        'symbol_id',
        'trade_date',
        'is_high',
        'is_low',
        'current_high',
        'current_low',
        'period_high',
        'period_low',
        'period_days',
    ];

    protected $casts = [
        'trade_date' => 'date',
        'is_high' => 'boolean',
        'is_low' => 'boolean',
        'current_high' => 'float',
        'current_low' => 'float',
        'period_high' => 'float',
        'period_low' => 'float',
        'period_days' => 'integer',
    ];

    public function preOpenMarketData()
    {
        return $this->belongsTo(PreOpenMarketData::class, 'symbol_id', 'id');
    }
}