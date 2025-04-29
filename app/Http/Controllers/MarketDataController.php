<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PreOpenMarketData;
use App\Services\NseApiClient;

class MarketDataController extends Controller
{
    public function index()
    {
        $stocks = PreOpenMarketData::orderBy('symbol')->get();
        return view('market-data.index', compact('stocks'));
    }

    public function fetchData(Request $request)
    {
        $symbol = $request->symbol;

        if (empty($symbol)) {
            return response()->json(['error' => 'Symbol is required'], 400);
        }

        try {
            // Use NseApiClient to fetch data
            $nseApiClient = app(\App\Services\NseApiClient::class);
            $response = $nseApiClient->getSymbolTradeInfoData($symbol);

            if (!$response->successful()) {
                return response()->json(['error' => 'Failed to fetch data from NSE'], 500);
            }

            $data = $response->json();

            // Get additional data for comprehensive analysis
            $symbolDataResponse = $nseApiClient->getSymbolData($symbol);
            if ($symbolDataResponse->successful()) {
                $symbolData = $symbolDataResponse->json();
                // Merge relevant data
                $data = array_merge($data, $symbolData);
            }

            // Calculate metrics
            $metrics = $this->calculateMetrics($data);

            // Add additional market information
            $marketInfo = $this->extractMarketInfo($data);

            return response()->json([
                'data' => $data,
                'metrics' => $metrics,
                'marketInfo' => $marketInfo,
                'lastUpdated' => now()->format('Y-m-d H:i:s')
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    private function extractMarketInfo($data)
    {
        return [
            'symbol' => $data['info']['symbol'] ?? '',
            'companyName' => $data['info']['companyName'] ?? '',
            'industry' => $data['info']['industry'] ?? '',
            'lastPrice' => $data['priceInfo']['lastPrice'] ?? 0,
            'change' => $data['priceInfo']['change'] ?? 0,
            'pChange' => $data['priceInfo']['pChange'] ?? 0,
            'open' => $data['priceInfo']['open'] ?? 0,
            'close' => $data['priceInfo']['close'] ?? 0,
            'high' => $data['priceInfo']['intraDayHighLow']['max'] ?? 0,
            'low' => $data['priceInfo']['intraDayHighLow']['min'] ?? 0,
            'previousClose' => $data['priceInfo']['previousClose'] ?? 0,
            'totalTradedVolume' => $data['priceInfo']['totalTradedVolume'] ?? 0,
            'totalTradedValue' => $data['priceInfo']['totalTradedValue'] ?? 0,
            'yearHigh' => $data['priceInfo']['weekHighLow']['max'] ?? 0,
            'yearLow' => $data['priceInfo']['weekHighLow']['min'] ?? 0,
            'marketCap' => $data['securityInfo']['marketCapitalisation'] ?? 0,
            'faceValue' => $data['securityInfo']['faceValue'] ?? 0,
            'eps' => $data['metadata']['eps'] ?? 0,
            'pe' => $data['metadata']['pe'] ?? null,
            'pb' => $data['metadata']['pb'] ?? 0,
            'deliveryQuantity' => $data['securityWiseDP']['deliveryQuantity'] ?? 0,
            'deliveryPercentage' => $data['securityWiseDP']['deliveryToTradedQuantity'] ?? 0,
        ];
    }

    private function calculateMetrics($data)
    {
        // 1. Delivery Percentage (already available in the API response)
        $deliveryPercentage = $data['securityWiseDP']['deliveryToTradedQuantity'] ?? 0;

        // 2. Demand-Supply Pressure
        $totalBuyQty = $data['marketDeptOrderBook']['totalBuyQuantity'] ?? 0;
        $totalSellQty = $data['marketDeptOrderBook']['totalSellQuantity'] ?? 0;
        $demandSupplyRatio = ($totalSellQty > 0) ? ($totalBuyQty / $totalSellQty) * 100 : 0;

        // 3. Weighted Average Bid and Ask Price
        $bidItems = $data['marketDeptOrderBook']['bid'] ?? [];
        $askItems = $data['marketDeptOrderBook']['ask'] ?? [];

        $weightedBid = $this->calculateWeightedAverage($bidItems);
        $weightedAsk = $this->calculateWeightedAverage($askItems);

        // 4. Average Trade Price
        $tradedVolume = $data['marketDeptOrderBook']['tradeInfo']['totalTradedVolume'] ?? 0;
        $tradedValue = $data['marketDeptOrderBook']['tradeInfo']['totalTradedValue'] ?? 0;
        $avgTradePrice = ($tradedVolume > 0) ? $tradedValue / $tradedVolume : 0;

        // 5. Liquidity Metrics
        $bidAskSpread = $weightedAsk - $weightedBid;
        $bidAskSpreadPercentage = ($weightedBid > 0) ? ($bidAskSpread / $weightedBid) * 100 : 0;
        $impactCost = $data['marketDeptOrderBook']['tradeInfo']['impactCost'] ?? 0;

        // Market depth ratio (buy depth vs sell depth)
        $totalBidQty = array_sum(array_column($bidItems, 'quantity'));
        $totalAskQty = array_sum(array_column($askItems, 'quantity'));
        $marketDepthRatio = ($totalAskQty > 0) ? ($totalBidQty / $totalAskQty) * 100 : 0;

        return [
            'deliveryPercentage' => $deliveryPercentage,
            'demandSupplyRatio' => $demandSupplyRatio,
            'weightedBid' => $weightedBid,
            'weightedAsk' => $weightedAsk,
            'avgTradePrice' => $avgTradePrice,
            'bidAskSpread' => $bidAskSpread,
            'bidAskSpreadPercentage' => $bidAskSpreadPercentage,
            'impactCost' => $impactCost,
            'marketDepthRatio' => $marketDepthRatio
        ];
    }

    private function calculateWeightedAverage($items)
    {
        $totalWeightedPrice = 0;
        $totalQuantity = 0;

        foreach ($items as $item) {
            $price = $item['price'] ?? 0;
            $quantity = $item['quantity'] ?? 0;

            $totalWeightedPrice += ($price * $quantity);
            $totalQuantity += $quantity;
        }

        return ($totalQuantity > 0) ? $totalWeightedPrice / $totalQuantity : 0;
    }
}
