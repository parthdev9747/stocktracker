<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockHistoricalData extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'symbol_id',
        'series',
        'market_type',
        'trade_date',
        'high_price',
        'low_price',
        'opening_price',
        'closing_price',
        'last_traded_price',
        'previous_close_price',
        'traded_quantity',
        'traded_value',
        'week_high_52',
        'week_low_52',
        'total_trades',
        'isin',
        'vwap',
        'delivery_quantity',
        'delivery_percent',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'trade_date' => 'date',
        'high_price' => 'decimal:2',
        'low_price' => 'decimal:2',
        'opening_price' => 'decimal:2',
        'closing_price' => 'decimal:2',
        'last_traded_price' => 'decimal:2',
        'previous_close_price' => 'decimal:2',
        'traded_quantity' => 'integer',
        'traded_value' => 'decimal:2',
        'week_high_52' => 'decimal:2',
        'week_low_52' => 'decimal:2',
        'total_trades' => 'integer',
        'vwap' => 'decimal:2',
        'delivery_quantity' => 'integer',
        'delivery_percent' => 'decimal:2',
    ];

    /**
     * Get the pre-open market data associated with this historical data.
     */
    public function preOpenMarketData()
    {
        return $this->belongsTo(PreOpenMarketData::class, 'symbol_id', 'id');
    }

    /**
     * Static method to create or update from API data
     */
    public static function createFromApiData(array $data)
    {
        return self::updateOrCreate(
            [
                'symbol' => $data['CH_SYMBOL'],
                'trade_date' => $data['CH_TIMESTAMP'],
            ],
            [
                'series' => $data['CH_SERIES'] ?? null,
                'market_type' => $data['CH_MARKET_TYPE'] ?? null,
                'high_price' => $data['CH_TRADE_HIGH_PRICE'] ?? null,
                'low_price' => $data['CH_TRADE_LOW_PRICE'] ?? null,
                'opening_price' => $data['CH_OPENING_PRICE'] ?? null,
                'closing_price' => $data['CH_CLOSING_PRICE'] ?? null,
                'last_traded_price' => $data['CH_LAST_TRADED_PRICE'] ?? null,
                'previous_close_price' => $data['CH_PREVIOUS_CLS_PRICE'] ?? null,
                'traded_quantity' => $data['CH_TOT_TRADED_QTY'] ?? null,
                'traded_value' => $data['CH_TOT_TRADED_VAL'] ?? null,
                'week_high_52' => $data['CH_52WEEK_HIGH_PRICE'] ?? null,
                'week_low_52' => $data['CH_52WEEK_LOW_PRICE'] ?? null,
                'total_trades' => $data['CH_TOTAL_TRADES'] ?? null,
                'isin' => $data['CH_ISIN'] ?? null,
                'vwap' => $data['VWAP'] ?? null,
                'delivery_quantity' => $data['COP_DELIV_QTY'] ?? null,
                'delivery_percent' => $data['COP_DELIV_PERC'] ?? null,
                'external_id' => $data['_id'] ?? null,
            ]
        );
    }

    /**
     * Get stocks with high delivery volume in the last week
     * 
     * @param int $days Number of days to look back
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getHighDeliveryStocks($days = 7)
    {
        $startDate = now()->subDays($days);

        // Get all symbols with their historical data for the specified period
        $symbols = PreOpenMarketData::with(['historicalData' => function ($query) use ($startDate) {
            $query->where('trade_date', '>=', $startDate)
                ->orderBy('trade_date', 'desc');
        }])->get();

        $filteredSymbols = [];
        $debugInfo = [];

        foreach ($symbols as $symbol) {
            if (!$symbol->historicalData || $symbol->historicalData->count() < 2) {
                continue; // Skip if we don't have at least 2 days of data
            }

            $historicalData = $symbol->historicalData->sortBy('trade_date');

            // Check each day's data against previous day
            $previousDay = null;

            foreach ($historicalData as $day) {
                if ($previousDay) {
                    // Check our criteria
                    $deliveryIncrease = $previousDay->delivery_quantity > 0 ?
                        $day->delivery_quantity / $previousDay->delivery_quantity : 0;

                    // Collect debug info for this symbol
                    $debugInfo[$symbol->symbol] = [
                        'date' => $day->trade_date,
                        'delivery_qty' => $day->delivery_quantity,
                        'prev_delivery_qty' => $previousDay->delivery_quantity,
                        'delivery_increase' => $deliveryIncrease,
                        'delivery_percent' => $day->delivery_percent,
                        'closing_price' => $day->closing_price,
                        'opening_price' => $day->opening_price,
                        'criteria_met' => [
                            'delivery_increase' => $deliveryIncrease >= 3,
                            'delivery_percent' => $day->delivery_percent > 70,
                            'price_condition' => $day->closing_price > $day->opening_price
                        ]
                    ];

                    // Check if any of the criteria are met (more lenient approach)
                    if (
                        $deliveryIncrease >= 3 && // 3 times more delivery than previous day
                        $day->delivery_percent > 70 && // Delivery percent > 70%
                        $day->closing_price > $day->opening_price
                    ) { // Closing price > Opening price

                        $filteredSymbols[] = [
                            'symbol' => $symbol->symbol,
                            'date' => $day->trade_date,
                            'delivery_quantity' => $day->delivery_quantity,
                            'previous_delivery' => $previousDay->delivery_quantity,
                            'delivery_increase' => $deliveryIncrease,
                            'delivery_percent' => $day->delivery_percent,
                            'opening_price' => $day->opening_price,
                            'closing_price' => $day->closing_price,
                            'price_change_percent' => (($day->closing_price - $day->opening_price) / $day->opening_price) * 100,
                            'volume' => $day->traded_quantity
                        ];

                        break; // Found a match, no need to check other days
                    }
                }

                $previousDay = $day;
            }
        }

        // Log debug info if no results found
        if (empty($filteredSymbols) && !empty($debugInfo)) {
            \Log::info('High Delivery Stocks Debug Info', ['debug_info' => $debugInfo]);
        }

        return collect($filteredSymbols);
    }
}
