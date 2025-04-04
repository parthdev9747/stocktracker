<?php

namespace App\Console\Commands;

use App\Models\PreOpenMarketData;
use App\Models\StockHistoricalData;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Ixudra\Curl\Facades\Curl;

class FetchHistoricalDataCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'stock:fetch-historical-data 
                            {--symbol=all : Symbol ID or "all" for all symbols}
                            {--start-date= : Start date (YYYY-MM-DD)}
                            {--end-date= : End date (YYYY-MM-DD)}
                            {--chunk=10 : Number of symbols to process in each chunk}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch historical stock data from NSE';

    private $baseUrl = 'https://www.nseindia.com';
    private $cookieMaxAge = 900; // 15 minutes in seconds

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $symbolOption = $this->option('symbol');
        $startDate = $this->option('start-date') ? Carbon::parse($this->option('start-date'))->format('Y-m-d') : Carbon::now()->subDays(30)->format('Y-m-d');
        $endDate = $this->option('end-date') ? Carbon::parse($this->option('end-date'))->format('Y-m-d') : Carbon::now()->format('Y-m-d');
        $chunkSize = (int)$this->option('chunk');

        $this->info("Fetching historical data from {$startDate} to {$endDate}");

        // Get symbols to fetch
        if ($symbolOption === 'all') {
            $symbols = PreOpenMarketData::pluck('symbol', 'id')->toArray();
            $this->info("Processing all " . count($symbols) . " symbols");
        } else {
            $symbol = PreOpenMarketData::where('symbol', $symbolOption)->first();
            $symbols = [$symbol->id => $symbol->symbol];
            $this->info("Processing symbol: {$symbol->symbol}");
        }

        $totalFetched = 0;
        $totalUpdated = 0;
        $errors = [];

        // Process symbols in chunks
        $symbolChunks = array_chunk($symbols, $chunkSize, true);
        $chunkCount = count($symbolChunks);

        $this->output->progressStart(count($symbols));

        foreach ($symbolChunks as $chunkIndex => $symbolsChunk) {
            $this->info("Processing chunk " . ($chunkIndex + 1) . " of {$chunkCount}");

            foreach ($symbolsChunk as $symbolId => $symbolName) {
                try {
                    // Get NSE cookies for authentication
                    $auth = $this->getNseCookies();

                    $headers = [
                        'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36',
                        'Accept: application/json, text/plain, */*',
                        'Accept-Language: en-US,en;q=0.9',
                        'Referer: https://www.nseindia.com/',
                        'Authority: www.nseindia.com',
                    ];


                    $url = 'http://localhost:3000/api/equity/historical/' . $symbolName . '?dateStart=' . Carbon::parse($startDate)->format('Y-m-d') . '&dateEnd=' . Carbon::parse($endDate)->format('Y-m-d');

                    $response = Curl::to($url)
                        ->withHeaders($headers)
                        ->asJson(true)
                        ->get();


                    if (empty($response)) {
                        Log::error("Empty API response for URL $url");
                        $errors[] = "Failed to fetch data for {$symbolName}: Empty response";
                        $this->output->progressAdvance();
                        continue;
                    }

                    // Handle the specific response structure
                    // The response is an array containing an object with a 'data' property
                    if (is_array($response) && isset($response[0]) && isset($response[0]['data']) && is_array($response[0]['data'])) {
                        $stockData = $response[0]['data'];
                        $recordsProcessed = 0;

                        foreach ($stockData as $item) {
                            // Check if record already exists
                            $existingRecord = StockHistoricalData::where([
                                'symbol_id' => $symbolId,
                                'trade_date' => $item['CH_TIMESTAMP'],
                            ])->first();

                            $recordData = [
                                'symbol_id' => $symbolId,
                                'series' => $item['CH_SERIES'] ?? null,
                                'market_type' => $item['CH_MARKET_TYPE'] ?? null,
                                'trade_date' => $item['CH_TIMESTAMP'],
                                'high_price' => $item['CH_TRADE_HIGH_PRICE'] ?? null,
                                'low_price' => $item['CH_TRADE_LOW_PRICE'] ?? null,
                                'opening_price' => $item['CH_OPENING_PRICE'] ?? null,
                                'closing_price' => $item['CH_CLOSING_PRICE'] ?? null,
                                'last_traded_price' => $item['CH_LAST_TRADED_PRICE'] ?? null,
                                'previous_close_price' => $item['CH_PREVIOUS_CLS_PRICE'] ?? null,
                                'traded_quantity' => $item['CH_TOT_TRADED_QTY'] ?? null,
                                'traded_value' => $item['CH_TOT_TRADED_VAL'] ?? null,
                                'week_high_52' => $item['CH_52WEEK_HIGH_PRICE'] ?? null,
                                'week_low_52' => $item['CH_52WEEK_LOW_PRICE'] ?? null,
                                'total_trades' => $item['CH_TOTAL_TRADES'] ?? null,
                                'isin' => $item['CH_ISIN'] ?? null,
                                'vwap' => $item['VWAP'] ?? null,
                            ];

                            if ($existingRecord) {
                                $existingRecord->update($recordData);
                                $totalUpdated++;
                            } else {
                                StockHistoricalData::create($recordData);
                                $totalFetched++;
                            }
                            $recordsProcessed++;
                        }
                        $this->info("  - {$symbolName}: Processed {$recordsProcessed} records");
                    } else {
                        // Log the actual response structure for debugging
                        Log::error("Unexpected API response structure for {$symbolName}: " . json_encode($response));
                        $errors[] = "No data found for {$symbolName} in the specified date range";
                        $this->warn("  - {$symbolName}: No data found or unexpected response format");
                    }
                } catch (\Exception $e) {
                    // $errors[] = "Error processing {$symbolName}: " . $e->getMessage();
                    // Log::error("Exception while processing {$symbolName}: " . $e->getMessage());
                    // $this->error("  - {$symbolName}: " . $e->getMessage());
                }

                $this->output->progressAdvance();

                // Add a small delay between requests to avoid rate limiting
                usleep(500000); // 0.5 seconds
            }

            // Add a delay between chunks
            if ($chunkIndex < $chunkCount - 1) {
                $this->info("Waiting before processing next chunk...");
                sleep(2);
            }
        }

        $this->output->progressFinish();

        $this->info("Successfully fetched {$totalFetched} new records and updated {$totalUpdated} existing records.");

        if (count($errors) > 0) {
            $this->warn("Encountered " . count($errors) . " errors:");
            foreach ($errors as $index => $error) {
                if ($index < 10) {
                    $this->warn("  - " . $error);
                } else {
                    $this->warn("  ... and " . (count($errors) - 10) . " more errors");
                    break;
                }
            }
        }

        return 0;
    }

    /**
     * Get NSE cookies for authentication
     * 
     * @return array
     */
    private function getNseCookies()
    {
        // Check if we have valid cookies in cache
        if (
            Cache::has('nse_cookies') &&
            Cache::has('nse_cookie_used_count') &&
            Cache::has('nse_cookie_expiry') &&
            Cache::get('nse_cookie_used_count') <= 10 &&
            Cache::get('nse_cookie_expiry') > time()
        ) {
            // Increment the used count
            Cache::increment('nse_cookie_used_count');

            return [
                'cookies' => Cache::get('nse_cookies'),
                'user_agent' => Cache::get('nse_user_agent')
            ];
        }

        // Generate a random user agent
        $userAgents = [
            'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36',
            'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/92.0.4515.107 Safari/537.36',
            'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/14.1.1 Safari/605.1.15',
            'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:90.0) Gecko/20100101 Firefox/90.0'
        ];
        $userAgent = $userAgents[array_rand($userAgents)];

        $this->info("Getting new NSE cookies...");

        // Make a request to get cookies
        $response = Curl::to($this->baseUrl . '/get-quotes/equity?symbol=TCS')
            ->withHeader('User-Agent: ' . $userAgent)
            ->withHeader('Accept: application/json, text/plain, */*')
            ->withHeader('Accept-Language: en-US,en;q=0.9')
            ->withHeader('Referer: ' . $this->baseUrl)
            ->returnResponseObject()
            ->get();

        $cookies = [];

        if (isset($response->headers['Set-Cookie'])) {
            $setCookies = $response->headers['Set-Cookie'];
            foreach ($setCookies as $cookie) {
                $cookieKeyValue = explode(';', $cookie)[0];
                $cookies[] = $cookieKeyValue;
            }
        }

        $cookieString = implode('; ', $cookies);

        // Store in cache
        Cache::put('nse_cookies', $cookieString, $this->cookieMaxAge);
        Cache::put('nse_user_agent', $userAgent, $this->cookieMaxAge);
        Cache::put('nse_cookie_used_count', 1, $this->cookieMaxAge);
        Cache::put('nse_cookie_expiry', time() + $this->cookieMaxAge, $this->cookieMaxAge);

        return [
            'cookies' => $cookieString,
            'user_agent' => $userAgent
        ];
    }
}
