<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Constants\ApiEndpoints;
use App\Models\MarketState;
use App\Models\MarketCap;
use App\Models\IndicativeNifty50;
use App\Models\GiftNifty;
use App\Models\IndexName;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class FetchMarketData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'market:fetch-data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch market data from NSE API and store in database';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Fetching market data from NSE API...');

        try {
            // Add necessary headers for NSE API
            $headers = [
                'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36',
                'Accept: application/json, text/plain, */*',
                'Accept-Language: en-US,en;q=0.9',
                'Referer: https://www.nseindia.com/',
            ];

            // Fetch market status data
            $marketData = ApiEndpoints::fetchData(ApiEndpoints::MARKET_STATUS, $headers);

            if (!$marketData) {
                $this->error('Failed to fetch market data');
                return 1;
            }

            $this->info('Market data fetched successfully');

            // Process market states
            if (isset($marketData['marketState']) && is_array($marketData['marketState'])) {
                $this->processMarketStates($marketData['marketState']);
            }

            // Process market cap
            if (isset($marketData['marketcap'])) {
                $this->processMarketCap($marketData['marketcap']);
            }

            // Process indicative nifty50
            if (isset($marketData['indicativenifty50'])) {
                $this->processIndicativeNifty50($marketData['indicativenifty50']);
            }

            // Process gift nifty
            if (isset($marketData['giftnifty'])) {
                $this->processGiftNifty($marketData['giftnifty']);
            }

            // Fetch and process index names
            $this->fetchAndProcessIndexNames($headers);

            $this->info('Market data processing completed');
            return 0;
        } catch (\Exception $e) {
            $this->error('Error: ' . $e->getMessage());
            Log::error('Market data fetch error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            return 1;
        }
    }

    /**
     * Process market states data
     */
    private function processMarketStates(array $marketStates)
    {
        $this->info('Processing ' . count($marketStates) . ' market states');

        foreach ($marketStates as $state) {
            $tradeDate = null;
            if (!empty($state['tradeDate'])) {
                try {
                    $tradeDate = Carbon::createFromFormat('d-M-Y H:i', $state['tradeDate']);
                } catch (\Exception $e) {
                    try {
                        $tradeDate = Carbon::createFromFormat('d-M-Y', $state['tradeDate']);
                    } catch (\Exception $e) {
                        $tradeDate = null;
                    }
                }
            }

            $updatedTime = null;
            if (!empty($state['updated_time'])) {
                try {
                    $updatedTime = Carbon::createFromFormat('d-M-Y H:i', $state['updated_time']);
                } catch (\Exception $e) {
                    $updatedTime = null;
                }
            }

            $data = [
                'market' => $state['market'] ?? null,
                'market_status' => $state['marketStatus'] ?? null,
                'trade_date' => $tradeDate,
                'index' => $state['index'] ?? null,
                'last' => $state['last'] ?? null,
                'variation' => $state['variation'] ?? null,
                'percent_change' => is_numeric($state['percentChange'] ?? '') ? $state['percentChange'] : 0,
                'market_status_message' => $state['marketStatusMessage'] ?? null,
                'expiry_date' => $state['expiryDate'] ?? null,
                'underlying' => $state['underlying'] ?? null,
                'slick_class' => $state['slickclass'] ?? null,
            ];

            // Check if record exists for today
            $existingRecord = MarketState::where('market', $state['market'])
                ->whereDate('created_at', Carbon::today())
                ->first();

            if ($existingRecord) {
                $existingRecord->update($data);
                $this->info("Updated market state for {$state['market']}");
            } else {
                MarketState::create($data);
                $this->info("Created market state for {$state['market']}");
            }
        }
    }

    /**
     * Process market cap data
     */
    private function processMarketCap(array $marketCap)
    {
        $this->info('Processing market cap data');

        $timeStamp = null;
        if (!empty($marketCap['timeStamp'])) {
            try {
                $timeStamp = Carbon::createFromFormat('d-M-Y', $marketCap['timeStamp'])->toDateString();
            } catch (\Exception $e) {
                $timeStamp = Carbon::today()->toDateString();
            }
        }

        $data = [
            'time_stamp' => $timeStamp,
            'market_cap_in_tr_dollars' => $marketCap['marketCapinTRDollars'] ?? 0,
            'market_cap_in_lac_cr_rupees' => $marketCap['marketCapinLACCRRupees'] ?? 0,
            'market_cap_in_cr_rupees' => $marketCap['marketCapinCRRupees'] ?? 0,
            'market_cap_in_cr_rupees_formatted' => $marketCap['marketCapinCRRupeesFormatted'] ?? '',
            'market_cap_in_lac_cr_rupees_formatted' => $marketCap['marketCapinLACCRRupeesFormatted'] ?? '',
            'underlying' => $marketCap['underlying'] ?? '',
        ];

        // Check if record exists for today
        $existingRecord = MarketCap::whereDate('created_at', Carbon::today())->first();

        if ($existingRecord) {
            $existingRecord->update($data);
            $this->info("Updated market cap for today");
        } else {
            MarketCap::create($data);
            $this->info("Created market cap for today");
        }
    }

    /**
     * Process indicative nifty50 data
     */
    private function processIndicativeNifty50(array $nifty50)
    {
        $this->info('Processing indicative nifty50 data');

        $dateTime = null;
        if (!empty($nifty50['dateTime'])) {
            try {
                $dateTime = Carbon::createFromFormat('d-M-Y H:i', $nifty50['dateTime']);
            } catch (\Exception $e) {
                $dateTime = Carbon::now();
            }
        }

        $indicativeTime = null;
        if (!empty($nifty50['indicativeTime'])) {
            try {
                $indicativeTime = Carbon::parse($nifty50['indicativeTime']);
            } catch (\Exception $e) {
                $indicativeTime = null;
            }
        }

        $data = [
            'date_time' => $dateTime,
            'indicative_time' => $indicativeTime,
            'index_name' => $nifty50['indexName'] ?? '',
            'index_last' => $nifty50['indexLast'] ?? null,
            'index_perc_change' => $nifty50['indexPercChange'] ?? null,
            'index_time_val' => $nifty50['indexTimeVal'] ?? null,
            'closing_value' => $nifty50['closingValue'] ?? null,
            'final_closing_value' => $nifty50['finalClosingValue'] ?? null,
            'change' => $nifty50['change'] ?? null,
            'per_change' => $nifty50['perChange'] ?? null,
            'status' => $nifty50['status'] ?? '',
        ];

        // Check if record exists for today
        $existingRecord = IndicativeNifty50::whereDate('created_at', Carbon::today())->first();

        if ($existingRecord) {
            $existingRecord->update($data);
            $this->info("Updated indicative nifty50 data for today");
        } else {
            IndicativeNifty50::create($data);
            $this->info("Created indicative nifty50 data for today");
        }
    }

    /**
     * Process gift nifty data
     */
    private function processGiftNifty(array $giftNifty)
    {
        $this->info('Processing gift nifty data');

        $timestamp = null;
        if (!empty($giftNifty['TIMESTMP'])) {
            try {
                $timestamp = Carbon::createFromFormat('d-M-Y H:i', $giftNifty['TIMESTMP']);
            } catch (\Exception $e) {
                $timestamp = Carbon::now();
            }
        }

        $data = [
            'instrument_type' => $giftNifty['INSTRUMENTTYPE'] ?? '',
            'symbol' => $giftNifty['SYMBOL'] ?? '',
            'expiry_date' => $giftNifty['EXPIRYDATE'] ?? '',
            'option_type' => $giftNifty['OPTIONTYPE'] ?? null,
            'strike_price' => $giftNifty['STRIKEPRICE'] ?? null,
            'last_price' => $giftNifty['LASTPRICE'] ?? 0,
            'day_change' => $giftNifty['DAYCHANGE'] ?? '',
            'per_change' => $giftNifty['PERCHANGE'] ?? 0,
            'contracts_traded' => $giftNifty['CONTRACTSTRADED'] ?? 0,
            'timestamp' => $timestamp,
            'external_id' => $giftNifty['id'] ?? '',
        ];

        // Check if record exists for today
        $existingRecord = GiftNifty::whereDate('created_at', Carbon::today())->first();

        if ($existingRecord) {
            $existingRecord->update($data);
            $this->info("Updated gift nifty data for today");
        } else {
            GiftNifty::create($data);
            $this->info("Created gift nifty data for today");
        }
    }
}
