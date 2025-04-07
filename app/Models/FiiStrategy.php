<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FiiStrategy extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'symbol_id',
        'current_price',
        'high_price',
        'low_price',
        'buy_price',
        'sell_price',
        'status'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'current_price' => 'float',
        'high_price' => 'float',
        'low_price' => 'float',
        'buy_price' => 'float',
        'sell_price' => 'float',
        'profit_loss' => 'float',
        'profit_loss_percentage' => 'float',
        'entry_date' => 'date',
        'exit_date' => 'date',
    ];

    /**
     * Get the stock symbol associated with the strategy.
     */
    public function symbol()
    {
        return $this->belongsTo(PreOpenMarketData::class, 'symbol_id');
    }

    /**
     * Get the historical data associated with this strategy's symbol.
     */
    public function historicalData()
    {
        return $this->hasMany(StockHistoricalData::class, 'symbol_id', 'symbol_id');
    }

    /**
     * Get the highest price from the last 30 days of historical data.
     *
     * @return float|null
     */
    public function getHighestPriceLastThirtyDays()
    {
        $thirtyDaysAgo = now()->subDays(30)->toDateString();

        return $this->historicalData()
            ->where('trade_date', '>=', $thirtyDaysAgo)
            ->max('high_price');
    }

    /**
     * Get the lowest price from the last 30 days of historical data.
     *
     * @return float|null
     */
    public function getLowestPriceLastThirtyDays()
    {
        $thirtyDaysAgo = now()->subDays(30)->toDateString();

        return $this->historicalData()
            ->where('trade_date', '>=', $thirtyDaysAgo)
            ->min('low_price');
    }
}
