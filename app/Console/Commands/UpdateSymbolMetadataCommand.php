<?php

namespace App\Console\Commands;

use App\Models\PreOpenMarketData;
use App\Services\NseApiClient;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class UpdateSymbolMetadataCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'stock:update-symbol-metadata
                            {--symbol=all : Symbol to update or "all" for all symbols}
                            {--chunk=10 : Number of symbols to process in each chunk}
                            {--from-id= : Start processing from this symbol ID}
                            {--to-id= : Process symbols up to this ID}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update symbol metadata from NSE API';

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
        $chunkSize = (int)$this->option('chunk');
        $fromId = $this->option('from-id');
        $toId = $this->option('to-id');

        // Get symbols to update
        if ($fromId && $toId) {
            // Get symbols by ID range
            $symbols = PreOpenMarketData::whereBetween('id', [$fromId, $toId])
                ->pluck('symbol', 'id')
                ->toArray();
            $this->info("Processing symbols with IDs from {$fromId} to {$toId}: " . count($symbols) . " symbols");
        } elseif ($symbolOption === 'all') {
            $symbols = PreOpenMarketData::pluck('symbol', 'id')->toArray();
            $this->info("Processing all " . count($symbols) . " symbols");
        } else {
            $symbol = PreOpenMarketData::where('symbol', $symbolOption)->first();
            if (!$symbol) {
                $this->error("Symbol {$symbolOption} not found");
                return 1;
            }
            $symbols = [$symbol->id => $symbol->symbol];
            $this->info("Processing symbol: {$symbol->symbol}");
        }

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
                    $this->info("Requesting metadata for {$symbolName}");

                    $response = $this->apiClient->getSymbolData($symbolName);

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

                    // Parse metadata
                    $metadata = $this->apiClient->parseSymbolMetadata($responseData);

                    if ($metadata["is_fno"] == false) {
                        $metadata["status"] = "inactive";
                    } else {
                        $metadata["status"] = "active";
                    }
                    // Update symbol in database
                    $symbol = PreOpenMarketData::find($symbolId);
                    $symbol->update($metadata);

                    $totalUpdated++;

                    $this->info("  - {$symbolName}: Updated metadata (FNO: " . ($metadata['is_fno'] ? 'Yes' : 'No') . ")");
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

        $this->info("Successfully updated metadata for {$totalUpdated} symbols.");

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
