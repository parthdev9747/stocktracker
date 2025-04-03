<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Constants\ApiEndpoints;
use App\Models\NseHoliday;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class FetchHolidayData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'market:fetch-holidays';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch trading holidays from NSE API and store in database';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Fetching trading holidays from NSE API...');

        try {
            // Add necessary headers for NSE API
            $headers = [
                'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36',
                'Accept: application/json, text/plain, */*',
                'Accept-Language: en-US,en;q=0.9',
                'Referer: https://www.nseindia.com/',
            ];

            // Fetch holiday data
            $holidayData = ApiEndpoints::fetchData(ApiEndpoints::HOLIDAY_TRADING, $headers);

            if (!$holidayData) {
                $this->error('Failed to fetch holiday data');
                return 1;
            }

            $this->info('Holiday data fetched successfully');

            // Process holiday data
            $this->processHolidayData($holidayData);

            $this->info('Holiday data processing completed');
            return 0;
        } catch (\Exception $e) {
            $this->error('Error: ' . $e->getMessage());
            Log::error('Holiday data fetch error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            return 1;
        }
    }

    /**
     * Process holiday data
     */
    private function processHolidayData(array $holidayData)
    {
        $segments = ['CM', 'FO', 'CD'];
        $totalHolidays = 0;

        foreach ($segments as $segment) {
            if (isset($holidayData[$segment]) && is_array($holidayData[$segment])) {
                $this->info('Processing ' . count($holidayData[$segment]) . ' holidays for ' . $segment . ' segment');

                foreach ($holidayData[$segment] as $holiday) {
                    $tradingDate = null;
                    if (!empty($holiday['tradingDate'])) {
                        try {
                            $tradingDate = Carbon::parse($holiday['tradingDate']);
                        } catch (\Exception $e) {
                            $this->warn('Invalid date format: ' . $holiday['tradingDate']);
                            continue;
                        }
                    } else {
                        $this->warn('Missing trading date in holiday data');
                        continue;
                    }

                    $data = [
                        'trading_date' => $tradingDate,
                        'day' => $tradingDate->format('l'),
                        'description' => $holiday['description'] ?? 'N/A',
                        'market_segment' => $segment,
                        'exchange' => 'NSE',
                        'year' => $tradingDate->format('Y'),
                    ];

                    // Check if record exists
                    $existingRecord = NseHoliday::where('trading_date', $tradingDate)
                        ->where('market_segment', $segment)
                        ->first();

                    if ($existingRecord) {
                        $existingRecord->update($data);
                        $this->info("Updated holiday: {$tradingDate->format('Y-m-d')} for {$segment}");
                    } else {
                        NseHoliday::create($data);
                        $this->info("Created holiday: {$tradingDate->format('Y-m-d')} for {$segment}");
                        $totalHolidays++;
                    }
                }
            } else {
                $this->warn("No holiday data found for {$segment} segment");
            }
        }

        $this->info("Total new holidays added: {$totalHolidays}");
    }
}
