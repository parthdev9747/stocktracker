<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Constants\ApiEndpoints;
use App\Models\IndexName;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class FetchIndexNames extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'market:fetch-index-names';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch index names from NSE API and store in database';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Fetching index names from NSE API...');

        try {
            // Add necessary headers for NSE API
            $headers = [
                'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36',
                'Accept: application/json, text/plain, */*',
                'Accept-Language: en-US,en;q=0.9',
                'Referer: https://www.nseindia.com/',
            ];

            // Fetch index names data
            $indexNamesData = ApiEndpoints::fetchData(ApiEndpoints::INDEX_NAMES, $headers);

            if (!$indexNamesData || !is_array($indexNamesData)) {
                $this->error('Failed to fetch index names data or invalid response');
                return 1;
            }

            // Process "stn" (Short To Name) data
            if (isset($indexNamesData['stn']) && is_array($indexNamesData['stn'])) {
                $this->info('Processing ' . count($indexNamesData['stn']) . ' short-to-name indices');
                $this->processIndexPairs($indexNamesData['stn'], 'stn');
            }

            $this->info('Index names processing completed');
            return 0;
        } catch (\Exception $e) {
            $this->error('Error processing index names: ' . $e->getMessage());
            Log::error('Index names fetch error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            return 1;
        }
    }

    /**
     * Process index pairs data
     * 
     * @param array $indexPairs Array of index pairs
     * @param string $type Type of mapping (stn or nts)
     */
    private function processIndexPairs(array $indexPairs, string $type)
    {
        foreach ($indexPairs as $pair) {
            if (!is_array($pair) || count($pair) < 2) {
                $this->warn("Skipping invalid index pair");
                continue;
            }

            $shortName = $pair[0] ?? null;
            $fullName = $pair[1] ?? null;

            if (!$shortName || !$fullName) {
                $this->warn("Skipping index pair with missing data");
                continue;
            }

            $data = [
                'index_name' => $fullName,
                'index_code' => $shortName,
                'index_type' => $type,
            ];

            // Check if record exists with the same index code
            $existingRecord = IndexName::where('index_code', $shortName)->first();

            if ($existingRecord) {
                $existingRecord->update($data);
                $this->info("Updated index: {$shortName} -> {$fullName}");
            } else {
                IndexName::create($data);
                $this->info("Created index: {$shortName} -> {$fullName}");
            }
        }
    }
}
