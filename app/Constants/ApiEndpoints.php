<?php

namespace App\Constants;

use Ixudra\Curl\Facades\Curl;
use Illuminate\Support\Facades\Log;

class ApiEndpoints
{
    const GLOSSARY = '/api/cmsContent?url=/glossary';
    const HOLIDAY_TRADING = '/api/holiday-master?type=trading';
    const HOLIDAY_CLEARING = '/api/holiday-master?type=clearing';
    const MARKET_STATUS = '/api/marketStatus';
    const MARKET_TURNOVER = '/api/market-turnover';
    const ALL_INDICES = '/api/allIndices';
    const INDEX_NAMES = '/api/index-names';
    const CIRCULARS = '/api/circulars';
    const LATEST_CIRCULARS = '/api/latest-circular';
    const EQUITY_MASTER = '/api/equity-master';
    const MARKET_DATA_PRE_OPEN = '/api/market-data-pre-open?key=ALL';
    const MERGED_DAILY_REPORTS_CAPITAL = '/api/merged-daily-reports?key=favCapital';
    const MERGED_DAILY_REPORTS_DERIVATIVES = '/api/merged-daily-reports?key=favDerivatives';
    const MERGED_DAILY_REPORTS_DEBT = '/api/merged-daily-reports?key=favDebt';
    const REFER = 'https://www.nseindia.com/api/market-data-pre-open?key=ALL';
    const HISTORICAL_DATA = 'https://www.nseindia.com/api/historical/cm/equity';


    /**
     * Base API URL
     */
    const BASE_URL = 'https://www.nseindia.com'; // Replace with your actual base URL

    /**
     * Fetch data from an API endpoint using Ixudra/Curl
     *
     * @param string $endpoint The API endpoint to fetch data from
     * @param array $headers Optional headers to include in the request
     * @return array|null The decoded JSON response or null on failure
     */
    public static function fetchData(string $endpoint, array $headers = []): ?array
    {
        $url = self::BASE_URL . $endpoint;

        $response = Curl::to($url)
            ->withHeaders($headers)
            ->asJson(true)
            ->get();

        if (empty($response) || isset($response['error'])) {
            Log::error("API Error for URL $url: " . json_encode($response));
            return null;
        }

        return $response;
    }

    /**
     * Get market status data
     *
     * @return array|null
     */
    public static function getMarketStatus(): ?array
    {
        return self::fetchData(self::MARKET_STATUS);
    }

    /**
     * Post data to an API endpoint
     *
     * @param string $endpoint The API endpoint
     * @param array $data The data to post
     * @param array $headers Optional headers
     * @return array|null The response
     */
    public static function postData(string $endpoint, array $data, array $headers = []): ?array
    {
        $url = self::BASE_URL . $endpoint;

        $response = Curl::to($url)
            ->withHeaders($headers)
            ->withData($data)
            ->asJson(true)
            ->post();

        if (empty($response) || isset($response['error'])) {
            Log::error("API Error for URL $url: " . json_encode($response));
            return null;
        }

        return $response;
    }
}
