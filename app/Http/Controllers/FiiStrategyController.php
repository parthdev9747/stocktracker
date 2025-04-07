<?php

namespace App\Http\Controllers;

use App\DataTables\FiiStrategyDataTable;
use App\Models\FiiStrategy;
use App\Models\PreOpenMarketData;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class FiiStrategyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(FiiStrategyDataTable $dataTable)
    {
        $statuses = ['Bought', 'Sold', 'Check', 'Sell Next Day', 'Buy Next Day', 'Hold', 'None'];
        
        return $dataTable->render('fii-strategy.index', compact('statuses'));
    }

    /**
     * Update the status of a strategy.
     */
    public function updateStatus(Request $request, FiiStrategy $strategy)
    {
        $validated = $request->validate([
            'status' => 'required|string|in:Bought,Sold,Check,Sell Next Day,Buy Next Day,Hold,None',
            'buy_price' => 'nullable|numeric',
            'sell_price' => 'nullable|numeric',
            'notes' => 'nullable|string',
        ]);
        
        // If status is changing to Bought, set entry date
        if ($validated['status'] === 'Bought' && $strategy->status !== 'Bought') {
            $validated['entry_date'] = now()->toDateString();
        }
        
        // If status is changing to Sold, set exit date and calculate profit/loss
        if ($validated['status'] === 'Sold' && $strategy->status !== 'Sold') {
            $validated['exit_date'] = now()->toDateString();
            
            if ($strategy->buy_price && $validated['sell_price']) {
                $validated['profit_loss'] = $validated['sell_price'] - $strategy->buy_price;
                $validated['profit_loss_percentage'] = ($validated['profit_loss'] / $strategy->buy_price) * 100;
            }
        }
        
        $strategy->update($validated);
        
        return response()->json(['success' => true, 'message' => 'Status updated successfully']);
    }

    /**
     * Refresh the FII Strategy data.
     */
    public function refresh()
    {
        Artisan::call('fii:update-strategy');
        
        return redirect()->route('fii-strategy.index')
            ->with('success', 'FII Strategy data refreshed successfully');
    }
}