<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PreOpenMarketData extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'pre_open_market_data';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'symbol',
        'is_fno',
        'status',
        'price',
        'change',
        'percent_change',
        'last_updated',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'is_fno' => 'boolean',
        'price' => 'float',
        'change' => 'float',
        'percent_change' => 'float',
        'last_updated' => 'datetime',
    ];

    /**
     * Get the historical data records for this stock.
     */
    public function historicalData()
    {
        return $this->hasMany(StockHistoricalData::class, 'symbol_id', 'id');
    }

    /**
     * Get the latest historical data record for this stock.
     */
    public function latestHistoricalData()
    {
        return $this->hasOne(StockHistoricalData::class, 'symbol_id', 'id')->latest('trade_date');
    }
}
