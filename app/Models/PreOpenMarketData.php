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
        'series',
        'open_price',
        'high_price',
        'low_price',
        'prev_close',
        'last_price',
        'change',
        'p_change',
        'total_traded_volume',
        'total_traded_value',
        'total_buy_quantity',
        'total_sell_quantity',
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
        'open_price' => 'float',
        'high_price' => 'float',
        'low_price' => 'float',
        'prev_close' => 'float',
        'last_price' => 'float',
        'change' => 'float',
        'p_change' => 'float',
        'total_traded_volume' => 'integer',
        'total_traded_value' => 'float',
        'total_buy_quantity' => 'integer',
        'total_sell_quantity' => 'integer',
        'is_fno' => 'boolean',
        'is_slb' => 'boolean',
        'is_etf' => 'boolean',
        'is_suspended' => 'boolean',
        'is_delisted' => 'boolean',
        'face_value' => 'float',
        'issued_size' => 'integer',
    ];

    public function latestHistoricalData()
    {
        return $this->hasOne(StockHistoricalData::class, 'symbol_id', 'id')
            ->latest('trade_date')
            ->select([
                'id',
                'symbol_id',
                'trade_date',
                'opening_price',
                'high_price',
                'low_price',
                'closing_price',
                'last_traded_price',
                'previous_close_price',
                'traded_quantity',
                'traded_value',
                'vwap',
                'total_trades'
            ]);
    }

    public static function getLatest($onlyFno = false)
    {
        $query = self::query();

        if ($onlyFno) {
            $query->where('is_fno', true);
        }

        return $query->orderBy('last_updated', 'desc')
            ->orderBy('symbol')
            ->get();
    }

    public static function getLatestWithHistorical($days = 30, $onlyFno = false)
    {
        $query = self::query();

        if ($onlyFno) {
            $query->where('is_fno', true);
        }

        return $query->with(['latestHistoricalData', 'historicalData' => function ($query) use ($days) {
            $query->where('trade_date', '>=', now()->subDays($days))
                ->orderBy('trade_date', 'desc');
        }])
            ->orderBy('last_updated', 'desc')
            ->orderBy('symbol')
            ->get();
    }

    /**
     * Get the historical data for this symbol.
     */
    public function historicalData()
    {
        return $this->hasMany(StockHistoricalData::class, 'symbol_id', 'id');
    }
}
