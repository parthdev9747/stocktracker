<?php

namespace App\Console\Commands;

use App\Models\StockHighLow;
use App\Models\StockHistoricalData;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class AnalyzeStockHighLow extends Command
{
    protected $signature = 'stocks:analyze-high-low {days=30}';
    protected $description = 'Analyze stocks for high and low in the specified number of days';

    public function handle()
    {
        $days = $this->argument('days');
        $this->info("Analyzing stocks for {$days}-day high and low...");

        // Get the date range
        $endDate = Carbon::today();
        $startDate = Carbon::today()->subDays($days);

        // Get all unique symbols with data in the last 30 days that are FNO stocks
        $symbols = StockHistoricalData::select('stock_historical_data.symbol_id')
            ->join('pre_open_market_data', 'stock_historical_data.symbol_id', '=', 'pre_open_market_data.id')
            ->where('pre_open_market_data.is_fno', true)
            ->where('stock_historical_data.trade_date', '>=', $startDate)
            ->where('stock_historical_data.trade_date', '<=', $endDate)
            ->groupBy('stock_historical_data.symbol_id')
            ->get()
            ->pluck('symbol_id');

        // Check if we found any symbols
        if ($symbols->isEmpty()) {
            $this->error("No FNO stocks found with data in the specified date range.");
            return Command::FAILURE;
        }

        $this->info("Found " . count($symbols) . " FNO stocks to analyze.");

        $bar = $this->output->createProgressBar(count($symbols));
        $bar->start();

        // Clear existing records
        StockHighLow::truncate();

        $processedCount = 0;
        $highLowCount = 0;

        foreach ($symbols as $symbolId) {
            // Get the latest record
            $latestRecord = StockHistoricalData::where('symbol_id', $symbolId)
                ->orderBy('trade_date', 'desc')
                ->first();

            if (!$latestRecord) {
                $this->comment("No latest record found for symbol ID: $symbolId");
                $bar->advance();
                continue;
            }

            // Get the high and low for the last N days (excluding today)
            $highLowStats = StockHistoricalData::where('symbol_id', $symbolId)
                ->where('trade_date', '>=', $startDate)
                ->where('trade_date', '<', Carbon::parse($latestRecord->trade_date)) // Exclude latest date
                ->select(
                    DB::raw('MAX(high_price) as period_high'),
                    DB::raw('MIN(low_price) as period_low')
                )
                ->first();

            if (!$highLowStats || is_null($highLowStats->period_high) || is_null($highLowStats->period_low)) {
                $this->comment("No historical data found for symbol ID: $symbolId");
                $bar->advance();
                continue;
            }

            $processedCount++;

            // Check if today's high is greater than the period high
            $isNewHigh = $latestRecord->high_price > $highLowStats->period_high;

            // Check if today's low is less than the period low
            $isNewLow = $latestRecord->low_price < $highLowStats->period_low;

            // Save the result if it's a new high or low
            if ($isNewHigh || $isNewLow) {
                StockHighLow::create([
                    'symbol_id' => $symbolId,
                    'trade_date' => $latestRecord->trade_date,
                    'is_high' => $isNewHigh,
                    'is_low' => $isNewLow,
                    'current_high' => $latestRecord->high_price,
                    'current_low' => $latestRecord->low_price,
                    'period_high' => $highLowStats->period_high,
                    'period_low' => $highLowStats->period_low,
                    'period_days' => $days,
                ]);
                $highLowCount++;
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info("Analysis completed! Processed $processedCount stocks, found $highLowCount new highs/lows.");

        return Command::SUCCESS;
    }
}
