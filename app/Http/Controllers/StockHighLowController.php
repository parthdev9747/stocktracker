<?php

namespace App\Http\Controllers;

use App\DataTables\StockHighLowDataTable;
use App\Models\StockHighLow;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;


class StockHighLowController extends Controller
{
    /**
     * Display a listing of stock high/low analysis.
     */
    public function index(StockHighLowDataTable $dataTable, Request $request)
    {
        return $dataTable->render('stock-high-low.index');
    }

    /**
     * Manually trigger the analysis for a specific period.
     */
    public function analyze(Request $request)
    {
        $days = $request->input('days', 30);

        // Call the command
        Artisan::call('stocks:analyze-high-low', [
            'days' => $days
        ]);

        return redirect()->route('stock-high-low.index')
            ->with('success', "Stock high/low analysis completed for {$days} days period.");
    }
}
