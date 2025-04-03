<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Constants\ApiEndpoints;
use App\Models\PreOpenMarketData;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Ixudra\Curl\Facades\Curl;

class FetchPreOpenMarketData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'market:fetch-pre-open-data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch pre-open market data from NSE API and store in database';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Fetching pre-open market data from NSE API...');

        try {
            // Add necessary headers for NSE API
            $headers = [
                'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36',
                'Accept: application/json, text/plain, */*',
                'Accept-Language: en-US,en;q=0.9',
                'Referer: https://www.nseindia.com/',
                'Authority: www.nseindia.com',
            ];

            // Fetch pre-open market data
            $url = 'http://localhost:3000/api/allSymbols';

            $preOpenData = Curl::to($url)
                ->withHeaders($headers)
                ->asJson(true)
                ->get();

            if (empty($preOpenData) || isset($preOpenData['error'])) {
                Log::error("API Error for URL $url: " . json_encode($preOpenData));
                return null;
            }



            if (!$preOpenData) {
                $this->error('Failed to fetch pre-open market data or invalid response');
                return 1;
            }

            $this->info('Processing ' . count($preOpenData) . ' pre-open market data entries');

            foreach ($preOpenData as $stockData) {
                $symbol = $stockData ?? null;

                if (!$symbol) {
                    continue;
                }

                $data = [
                    'symbol' => $symbol,
                    'is_fno' => $stockData['isFNO'] ?? false,
                    'status' => $stockData['status'] ?? 'active',
                    'price' => $stockData['price'] ?? 0,
                    'change' => $stockData['change'] ?? 0,
                    'percent_change' => $stockData['percentChange'] ?? 0,
                    'last_updated' => Carbon::now(),
                ];

                // Check if record exists with the same symbol for today
                $existingRecord = PreOpenMarketData::where('symbol', $symbol)
                    ->whereDate('created_at', Carbon::today())
                    ->first();

                if ($existingRecord) {
                    $existingRecord->update($data);
                    $this->info("Updated pre-open market data for {$symbol}");
                } else {
                    PreOpenMarketData::create($data);
                    $this->info("Created pre-open market data for {$symbol}");
                }
            }

            $this->info('Pre-open market data processing completed');
            return 0;
        } catch (\Exception $e) {
            dump($e->getMessage());
            $this->error('Error processing pre-open market data: ' . $e->getMessage());
            Log::error('Pre-open market data fetch error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            return 1;
        }
    }
}
