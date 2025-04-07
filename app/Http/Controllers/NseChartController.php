<?php

namespace App\Http\Controllers;

use App\Services\NseApiClient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class NseChartController extends Controller
{
    protected $nseApiClient;

    /**
     * Create a new controller instance.
     */
    public function __construct(NseApiClient $nseApiClient)
    {
        $this->nseApiClient = $nseApiClient;
    }

    /**
     * Display chart data for a specific symbol
     */
    public function show($symbol)
    {
        return view('nse-chart.show', compact('symbol'));
    }

    /**
     * Fetch chart data from NSE API
     */
    public function fetchChartData(Request $request, $symbol)
    {
        $cacheKey = 'nse_chart_data_' . $symbol;
        $cacheDuration = 60; // Cache for 1 minute
        
        // Try to get from cache first
        if (Cache::has($cacheKey)) {
            return response()->json(Cache::get($cacheKey));
        }
        
        try {
            $response = $this->nseApiClient->getSymbolChartData($symbol);
            
            if ($response->successful()) {
                $data = $response->json();
                
                // Cache the response
                Cache::put($cacheKey, $data, $cacheDuration);
                
                return response()->json($data);
            } else {
                return response()->json(['error' => 'Failed to fetch data from NSE'], 500);
            }
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}