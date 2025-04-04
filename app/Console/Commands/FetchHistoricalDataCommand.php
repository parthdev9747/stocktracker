<?php

namespace App\Console\Commands;

use App\Models\PreOpenMarketData;
use App\Models\StockHistoricalData;
use App\Services\NseApiClient;
use Carbon\Carbon;
use GuzzleHttp\Cookie\CookieJar;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

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
                            {--chunk=10 : Number of symbols to process in each chunk}
                            {--from-id= : Start processing from this symbol ID}
                            {--to-id= : Process symbols up to this ID}
                            {--is-fno : Only process symbols that are in F&O segment}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch historical stock data from NSE';

    /**
     * @var NseApiClient
     */
    protected $apiClient;

    /**
     * Create a new command instance.
     */
    public function __construct(NseApiClient $apiClient)
    {
        parent::__construct();
        $this->apiClient = $apiClient;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $symbolOption = $this->option('symbol');
        $startDate = $this->option('start-date') ? Carbon::parse($this->option('start-date'))->format('Y-m-d') : Carbon::now()->subDays(30)->format('Y-m-d');
        $endDate = $this->option('end-date') ? Carbon::parse($this->option('end-date'))->format('Y-m-d') : Carbon::now()->format('Y-m-d');
        $chunkSize = (int)$this->option('chunk');
        $fromId = $this->option('from-id');
        $toId = $this->option('to-id');
        $isFno = $this->option('is-fno');

        $this->info("Fetching historical data from {$startDate} to {$endDate}");

        // Get symbols to fetch
        $query = PreOpenMarketData::query();

        // Apply FNO filter if requested
        if ($isFno) {
            $query->where('is_fno', true);
            $this->info("Filtering for F&O symbols only");
        }

        if ($fromId && $toId) {
            // Get symbols by ID range
            $query->whereBetween('id', [$fromId, $toId]);
            $symbols = $query->pluck('symbol', 'id')->toArray();
            $this->info("Processing symbols with IDs from {$fromId} to {$toId}: " . count($symbols) . " symbols");
        } elseif ($symbolOption === 'all') {
            $symbols = $query->pluck('symbol', 'id')->toArray();
            $this->info("Processing " . count($symbols) . " symbols");
        } else {
            $symbol = PreOpenMarketData::where('symbol', $symbolOption)->first();
            if (!$symbol) {
                $this->error("Symbol {$symbolOption} not found");
                return 1;
            }

            // if ($isFno && !$symbol->is_fno) {
            //     $this->error("Symbol {$symbolOption} is not in F&O segment");
            //     return 1;
            // }

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
                    $fromDate = Carbon::parse($startDate)->format('d-m-Y');
                    $toDate = Carbon::parse($endDate)->format('d-m-Y');

                    $this->info("Requesting data for {$symbolName} from {$fromDate} to {$toDate}");

                    $response = $this->apiClient->getHistoricalData($symbolName, $fromDate, $toDate);

                    if (!$response->successful()) {
                        Log::error("Failed API response for {$symbolName}: " . $response->status());
                        $errors[] = "Failed to fetch data for {$symbolName}: HTTP " . $response->status();
                        $this->output->progressAdvance();
                        continue;
                    }

                    $responseData = $response->json();

                    if (empty($responseData)) {
                        Log::error("Empty API response for {$symbolName}");
                        $errors[] = "Failed to fetch data for {$symbolName}: Empty response";
                        $this->output->progressAdvance();
                        continue;
                    }

                    // Handle the response structure
                    if (isset($responseData['data']) && is_array($responseData['data'])) {
                        $stockData = $responseData['data'];
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
                                'delivery_quantity' => $item['COP_DELIV_QTY'] ?? null,
                                'delivery_percent' => $item['COP_DELIV_PERC'] ?? null,
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
                        Log::error("Unexpected API response structure for {$symbolName}: " . json_encode($responseData));
                        $errors[] = "No data found for {$symbolName} in the specified date range";
                        $this->warn("  - {$symbolName}: No data found or unexpected response format");
                    }
                } catch (\Exception $e) {
                    $errors[] = "Error processing {$symbolName}: " . $e->getMessage();
                    Log::error("Exception while processing {$symbolName}: " . $e->getMessage());
                    $this->error("  - {$symbolName}: " . $e->getMessage());
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
}
