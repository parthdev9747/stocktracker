<?php

namespace App\Console\Commands;

use App\Models\FiiStrategy;
use App\Models\PreOpenMarketData;
use App\Models\StockHistoricalData;
use Carbon\Carbon;
use Illuminate\Console\Command;

class UpdateFiiStrategy extends Command
{
    protected $signature = 'fii:update-strategy';
    protected $description = 'Update FII Strategy table with FNO stocks and their current prices';

    public function handle()
    {
        $this->info('Updating FII Strategy table...');

        // Get all FNO stocks
        $fnoStocks = PreOpenMarketData::where('is_fno', true)->get();

        $bar = $this->output->createProgressBar(count($fnoStocks));
        $bar->start();

        $updatedCount = 0;
        $newCount = 0;

        foreach ($fnoStocks as $stock) {
            // Get the latest historical data for the stock
            $latestData = StockHistoricalData::where('symbol_id', $stock->id)
                ->orderBy('trade_date', 'desc')
                ->first();

            if (!$latestData) {
                $bar->advance();
                continue;
            }

            // Check if the stock already exists in the FII Strategy table
            $strategy = FiiStrategy::where('symbol_id', $stock->id)->first();

            if ($strategy) {
                // Update existing record with basic data
                $updateData = [
                    'current_price' => $latestData->closing_price,
                    'high_price' => $latestData->high_price,
                    'low_price' => $latestData->low_price,
                ];

                // Get highest and lowest prices from the last 30 days using model methods
                $highestPrice = $strategy->getHighestPriceLastThirtyDays();
                $lowestPrice = $strategy->getLowestPriceLastThirtyDays();

                // Check if current high price is the highest in past 30 days
                $isHighest = $latestData->high_price >= $highestPrice;

                // Check if current low price is the lowest in past 30 days
                $isLowest = $latestData->low_price <= $lowestPrice;

                // If current high is highest in 30 days, calculate sell price
                if ($isHighest && $highestPrice) {
                    // Calculate sell price: high_price - (high_price * 1.579 / 100)
                    $deduction = $highestPrice * 1.579 / 100;
                    $sellPrice = $highestPrice - $deduction;

                    // Update with calculated sell price and empty buy price
                    $updateData['sell_price'] = $sellPrice;
                    $updateData['buy_price'] = null;
                    $updateData['notes'] = "30-day high detected. Sell price calculated at " . number_format($sellPrice, 2);
                    $updateData['status'] = 'Check';
                }
                // If current low is lowest in 30 days, calculate buy price
                else if ($isLowest && $lowestPrice) {
                    // Calculate buy price: low_price + (low_price * 1.579 / 100)
                    $addition = $lowestPrice * 1.579 / 100;
                    $buyPrice = $lowestPrice + $addition;

                    // Update with calculated buy price and empty sell price
                    $updateData['buy_price'] = $buyPrice;
                    $updateData['sell_price'] = null;
                    $updateData['notes'] = "30-day low detected. Buy price calculated at " . number_format($buyPrice, 2);
                    $updateData['status'] = 'Check';
                }

                // Check if current price is below sell price and update status
                if ($strategy->sell_price && $latestData->closing_price < $strategy->sell_price) {
                    $updateData['status'] = 'Sell Next Day';
                    $updateData['notes'] = ($updateData['notes'] ?? '') . " Current price below sell target.";
                }

                // Check if current price is above buy price and update status
                if ($strategy->buy_price && $latestData->closing_price > $strategy->buy_price) {
                    $updateData['status'] = 'Buy Next Day';
                    $updateData['notes'] = ($updateData['notes'] ?? '') . " Current price above buy target.";
                }

                $strategy->update($updateData);
                $updatedCount++;
            } else {
                // Create new record
                FiiStrategy::create([
                    'symbol_id' => $stock->id,
                    'current_price' => $latestData->closing_price,
                    'high_price' => $latestData->high_price,
                    'low_price' => $latestData->low_price,
                    'status' => 'None',
                ]);
                $newCount++;
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);
        $this->info("FII Strategy table updated successfully!");
        $this->info("New records: $newCount");
        $this->info("Updated records: $updatedCount");

        return Command::SUCCESS;
    }
}
