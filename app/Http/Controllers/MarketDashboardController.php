<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MarketState;
use App\Models\MarketCap;
use App\Models\IndicativeNifty50;
use App\Models\GiftNifty;
use Carbon\Carbon;

class MarketDashboardController extends Controller
{
    public function index()
    {
        // Get latest market states
        $marketStates = MarketState::whereDate('created_at', Carbon::today())
            ->orderBy('market')
            ->get();

        // Get latest market cap
        $marketCap = MarketCap::latest('time_stamp')->first();

        // Get latest indicative nifty50
        $indicativeNifty = IndicativeNifty50::latest('date_time')->first();

        // Get latest gift nifty
        $giftNifty = GiftNifty::latest('timestamp')->first();

        return view('market.dashboard', compact(
            'marketStates',
            'marketCap',
            'indicativeNifty',
            'giftNifty'
        ));
    }

    public function syncData()
    {
        try {

            \Illuminate\Support\Facades\Artisan::call('market:fetch-data');

            return response()->json([
                'success' => true,
                'message' => 'Market data synchronized successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to synchronize market data: ' . $e->getMessage()
            ]);
        }
    }
}
