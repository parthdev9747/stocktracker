<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\NseIndex;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class FetchNseIndicesData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'market:fetch-indices';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch NSE indices data from equity-master API and store in database';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Fetching NSE indices data from equity-master API...');

        try {
            // Add necessary headers for NSE API
            $headers = [
                'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36',
                'Accept: application/json, text/plain, */*',
                'Accept-Language: en-US,en;q=0.9',
                'Referer: https://www.nseindia.com/',
            ];

            // Initialize cURL
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, 'https://www.nseindia.com/api/equity-master');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);

            // Execute cURL request
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($httpCode !== 200 || !$response) {
                $this->error('Failed to fetch NSE indices data. HTTP Code: ' . $httpCode);
                return 1;
            }

            $data = json_decode($response, true);

            if (!$data || !is_array($data)) {
                $this->error('Invalid response format from NSE API');
                return 1;
            }

            $this->info('Processing NSE indices data...');
            $totalIndices = 0;
            $newIndices = 0;

            // Process each category of indices
            foreach ($data as $category => $indices) {
                if (!is_array($indices)) {
                    continue;
                }

                $this->info("Processing category: $category");
                $totalIndices += count($indices);

                foreach ($indices as $indexName) {
                    // Check if this is a derivative eligible index
                    $isDerivativeEligible = ($category === 'Indices Eligible In Derivatives');

                    // Check if index already exists
                    $existingIndex = NseIndex::where('name', $indexName)
                        ->where('category', $category)
                        ->first();

                    if ($existingIndex) {
                        // Update if needed
                        if ($existingIndex->is_derivative_eligible !== $isDerivativeEligible) {
                            $existingIndex->update([
                                'is_derivative_eligible' => $isDerivativeEligible
                            ]);
                            $this->info("Updated index: $indexName");
                        }
                    } else {
                        // Create new index
                        NseIndex::create([
                            'name' => $indexName,
                            'category' => $category,
                            'is_derivative_eligible' => $isDerivativeEligible
                        ]);
                        $newIndices++;
                        $this->info("Added new index: $indexName");
                    }
                }
            }

            $this->info("NSE indices data processing completed. Total indices: $totalIndices, New indices: $newIndices");
            return 0;
        } catch (\Exception $e) {
            $this->error('Error: ' . $e->getMessage());
            Log::error('NSE indices data fetch error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            return 1;
        }
    }
}