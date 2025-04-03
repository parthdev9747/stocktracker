<?php

namespace App\Http\Controllers;

use App\Models\PreOpenMarketData;
use App\Models\StockHistoricalData;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;
use Ixudra\Curl\Facades\Curl;
use App\Constants\ApiEndpoints;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Artisan;
use App\DataTables\StockHistoricalDataDataTable;

class StockHistoricalDataController extends Controller
{
    private $baseUrl = 'https://www.nseindia.com';
    private $cookieMaxAge = 900; // 15 minutes in seconds

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

    /**
     * Display the historical data form
     */
    public function index(StockHistoricalDataDataTable $dataTable)
    {
        $symbols = PreOpenMarketData::orderBy('symbol')->pluck('symbol', 'id');
        return $dataTable->render('stock-historical-data.index', compact('symbols'));
    }

    /**
     * Fetch historical data based on selected criteria
     */
    public function fetchData(Request $request)
    {
        $request->validate([
            'symbol_id' => 'required',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $startDate = Carbon::parse($request->start_date)->format('Y-m-d');
        $endDate = Carbon::parse($request->end_date)->format('Y-m-d');

        // Dispatch the command to run in the background
        Artisan::call('stock:fetch-historical-data', [
            '--symbol' => $request->symbol_id != 'all' ? PreOpenMarketData::find($request->symbol_id)->symbol : 'all',
            '--start-date' => $startDate,
            '--end-date' => $endDate,
            '--chunk' => 10
        ]);

        return redirect()->route('stock-historical-data.index')
            ->with('success', "Historical data fetch process has been started in the background. The data will be available shortly.");
    }
}
