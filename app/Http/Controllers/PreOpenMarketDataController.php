<?php

namespace App\Http\Controllers;

use App\DataTables\PreOpenMarketDataDataTable;
use App\Models\PreOpenMarketData;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Carbon\Carbon;
use Illuminate\Support\Facades\View;


class PreOpenMarketDataController extends Controller
{

    protected $moduleName;
    protected $moduleRoute;
    protected $moduleView = "pre-open-market-data";
    protected $model;

    public function __construct(PreOpenMarketData $model)
    {

        $this->moduleName = 'Symbol';
        $this->moduleRoute = url('symbol');
        $this->model = $model;

        View::share('module_name', $this->moduleName);
        View::share('module_route', $this->moduleRoute);
        View::share('module_view', $this->moduleView);
    }
    /**
     * Display a listing of pre-open market data.
     */
    public function index(PreOpenMarketDataDataTable $dataTable)
    {
        return $dataTable->render('pre-open-market-data.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(PreOpenMarketData $preOpenMarketData)
    {
        return view('pre-open-market-data.show', compact('preOpenMarketData'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(PreOpenMarketData $preOpenMarketData)
    {
        return view('pre-open-market-data.edit', compact('preOpenMarketData'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, PreOpenMarketData $preOpenMarketData)
    {
        $validated = $request->validate([
            'symbol' => 'required|string|max:255',
            'is_fno' => 'boolean',
            'status' => 'nullable|string|max:255',
            'price' => 'required|numeric',
            'change' => 'required|numeric',
            'percent_change' => 'required|numeric',
        ]);

        $preOpenMarketData->update($validated);

        return redirect()->route('pre-open-market-data.index')
            ->with('success', 'Market data updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PreOpenMarketData $preOpenMarketData)
    {
        $preOpenMarketData->delete();

        return response()->json(['success' => true]);
    }

    /**
     * Update status via AJAX
     */
    public function updateStatus(Request $request)
    {
        $validated = $request->validate([
            'id' => 'required|exists:pre_open_market_data,id',
            'status' => 'required|string|max:255',
        ]);

        $preOpenMarketData = PreOpenMarketData::findOrFail($validated['id']);
        $preOpenMarketData->status = $validated['status'];
        $preOpenMarketData->save();

        return response()->json(['success' => true]);
    }

    /**
     * Toggle F&O status via AJAX
     */
    /**
     * Toggle F&O status
     */
    public function toggleFno(Request $request)
    {
        $id = $request->id;
        $response = [];
        $data = $this->model->findOrFail($id);
        if ($data) {
            $status = ($data->is_fno == 0) ? 1 : 0;
            $data->is_fno = $status;
            $data->save();

            $response['message'] = 'Symbol FNO status updated successfully';
            $response['status'] = true;
        } else {
            $response['message'] = "Symbol not Found!";
            $response['status'] = false;
        }
        return response()->json($response);
    }

    /**
     * Toggle status
     */
    public function toggleStatus(Request $request)
    {
        $id = $request->id;
        $response = [];
        $data = $this->model->findOrFail($id);
        if ($data) {
            $status = ($data->status == 'inactive') ? 'active' : 'inactive';
            $data->status = $status;
            $data->save();

            $response['message'] = 'Symbol status updated successfully';
            $response['status'] = true;
        } else {
            $response['message'] = "Symbol not Found!";
            $response['status'] = false;
        }
        return response()->json($response);
    }

    /**
     * Refresh pre-open market data by running the command.
     */
    public function refresh(Request $request)
    {
        try {
            Artisan::call('market:fetch-pre-open-data');
            return response()->json(['success' => true, 'message' => 'Pre-open market data refreshed successfully']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to refresh pre-open market data: ' . $e->getMessage()], 500);
        }
    }
}
