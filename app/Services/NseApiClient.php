<?php

namespace App\Services;

use GuzzleHttp\Cookie\CookieJar;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class NseApiClient
{
    private $baseUrl = 'https://www.nseindia.com';
    private $cookieMaxAge = 900; // 15 minutes in seconds

    /**
     * Register HTTP client macros
     */
    public function __construct()
    {
        $this->registerHttpMacros();
    }

    /**
     * Register HTTP client macros
     */
    private function registerHttpMacros()
    {
        if (!PendingRequest::hasMacro('withCookieJar')) {
            PendingRequest::macro('withCookieJar', function (CookieJar $cookieJar) {
                $this->options['cookies'] = $cookieJar;
                return $this;
            });
        }
    }

    /**
     * Prepare HTTP request with standard headers
     */
    public function prepareRequestWithHeaders(): PendingRequest
    {
        return Http::withHeaders([
            'Accept' => '*/*',
            'Accept-Encoding' => 'gzip, deflate', // Modified to only include recognized encodings
            'Accept-Language' => 'en-US,en;q=0.9',
            'Connection' => 'keep-alive',
            'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.132 Safari/537.36',
            'Referer' => 'https://www.nseindia.com/',
        ])->timeout(30)
            ->withOptions([
                'decode_content' => true, // Ensure content is decoded properly
                'verify' => false, // Skip SSL verification if needed
            ]);
    }

    /**
     * Get authorization cookies from NSE
     */
    public function getAuthorisationCookies(): CookieJar
    {
        // Check if we have valid cookies in cache
        if (Cache::has('nse_cookie_jar') && Cache::get('nse_cookie_expiry', 0) > time()) {
            return Cache::get('nse_cookie_jar');
        }

        $response = $this->prepareRequestWithHeaders()
            ->get($this->baseUrl);

        $cookieJar = $response->cookies();

        // Store in cache
        Cache::put('nse_cookie_jar', $cookieJar, $this->cookieMaxAge);
        Cache::put('nse_cookie_expiry', time() + $this->cookieMaxAge, $this->cookieMaxAge);

        return $cookieJar;
    }

    /**
     * Get quote equity data for a symbol
     */
    public function getQuoteEquityResponse(string $symbol): Response
    {
        $authorisationCookies = $this->getAuthorisationCookies();

        return $this->prepareRequestWithHeaders()
            ->withCookieJar($authorisationCookies)
            ->get("{$this->baseUrl}/api/quote-equity?symbol={$symbol}");
    }

    /**
     * Make an authenticated request to the NSE API with retry logic
     */
    private function makeAuthenticatedRequest(string $endpoint, array $params = [], string $symbol = null): Response
    {
        // Force refresh cookies to ensure we have valid authentication
        $this->clearCookieCache();
        $authorisationCookies = $this->getAuthorisationCookies();

        // First visit the quote page to establish a proper session if symbol is provided
        if ($symbol) {
            $this->prepareRequestWithHeaders()
                ->withCookieJar($authorisationCookies)
                ->get("{$this->baseUrl}/get-quotes/equity?symbol=" . urlencode($symbol));

            // Small delay to simulate human behavior
            usleep(500000); // 0.5 seconds
        }

        // Make the actual API request
        $response = $this->prepareRequestWithHeaders()
            ->withCookieJar($authorisationCookies)
            ->get("{$this->baseUrl}{$endpoint}", $params);

        // If we get a 401, try one more time with fresh cookies
        if ($response->status() === 401) {
            $this->clearCookieCache();
            $authorisationCookies = $this->getAuthorisationCookies();

            // Visit the main page and then the quote page
            $this->prepareRequestWithHeaders()
                ->withCookieJar($authorisationCookies)
                ->get($this->baseUrl);

            usleep(1000000); // 1 second delay

            if ($symbol) {
                $this->prepareRequestWithHeaders()
                    ->withCookieJar($authorisationCookies)
                    ->get("{$this->baseUrl}/get-quotes/equity?symbol=" . urlencode($symbol));

                usleep(1000000); // 1 second delay
            }

            // Try the API request again
            $response = $this->prepareRequestWithHeaders()
                ->withCookieJar($authorisationCookies)
                ->get("{$this->baseUrl}{$endpoint}", $params);
        }

        return $response;
    }

    /**
     * Get historical data for a symbol
     */
    public function getHistoricalData(string $symbol, string $fromDate, string $toDate, string $series = 'EQ'): Response
    {
        return $this->makeAuthenticatedRequest(
            '/api/historical/securityArchives',
            [
                'from' => $fromDate,
                'to' => $toDate,
                'symbol' => $symbol,
                'dataType' => 'priceVolumeDeliverable',
                'series' => $series
            ],
            $symbol
        );
    }

    /**
     * Get equity data for a symbol
     */
    public function getSymbolData(string $symbol): Response
    {
        return $this->makeAuthenticatedRequest(
            '/api/quote-equity',
            ['symbol' => $symbol],
            $symbol
        );
    }

    /**
     * Parse symbol metadata from API response
     */
    public function parseSymbolMetadata(array $data): array
    {
        return [
            'is_fno' => $data['info']['isFNOSec'] ?? false,
            'is_slb' => $data['info']['isSLBSec'] ?? false,
            'is_etf' => $data['info']['isETFSec'] ?? false,
            'is_suspended' => $data['info']['isSuspended'] ?? false,
            'is_delisted' => $data['info']['isDelisted'] ?? false,
            'isin' => $data['info']['isin'] ?? null,
            'listing_date' => $data['info']['listingDate'] ?? null,
            'industry' => $data['info']['industry'] ?? null,
            'face_value' => $data['securityInfo']['faceValue'] ?? null,
            'issued_size' => $data['securityInfo']['issuedSize'] ?? null,
        ];
    }

    /**
     * Clear cookie cache to force new authentication
     */
    private function clearCookieCache(): void
    {
        Cache::forget('nse_cookie_jar');
        Cache::forget('nse_cookie_expiry');
    }
}
