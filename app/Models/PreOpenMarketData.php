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

    /**
     * Get data formatted for DataTables
     * 
     * @param array $filters Optional filters to apply
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function forDataTable($filters = [])
    {
        $query = self::query()
            ->with('latestHistoricalData')
            ->select('pre_open_market_data.*');

        // Apply symbol filter if provided
        if (!empty($filters['symbol_id']) && $filters['symbol_id'] !== 'all') {
            $query->where('id', $filters['symbol_id']);
        }

        // Apply F&O filter if provided
        if (isset($filters['is_fno'])) {
            $query->where('is_fno', (bool)$filters['is_fno']);
        }

        // Apply status filter if provided
        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        // Apply sorting
        $query->orderBy('symbol');

        return $query->get();
    }

    /**
     * Get the latest pre-open market data for all symbols
     * 
     * @param bool $onlyFno Filter only F&O stocks if true
     * @return \Illuminate\Database\Eloquent\Collection
     */
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

    /**
     * Get the latest pre-open market data with historical data
     * 
     * @param int $days Number of days of historical data to include
     * @param bool $onlyFno Filter only F&O stocks if true
     * @return \Illuminate\Database\Eloquent\Collection
     */
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
}
