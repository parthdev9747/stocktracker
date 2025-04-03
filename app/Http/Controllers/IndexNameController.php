<?php

namespace App\Http\Controllers;

use App\Models\IndexName;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class IndexNameController extends Controller
{
    /**
     * Display a listing of the index names.
     */
    public function index()
    {
        // Get all index names grouped by type
        $stnIndices = IndexName::where('index_type', 'stn')->orderBy('index_name')->get();

        return view('index-names.index', compact('stnIndices'));
    }

    /**
     * Show details for a specific index.
     */
    public function show($id)
    {
        $index = IndexName::findOrFail($id);
        return view('index-names.show', compact('index'));
    }

    /**
     * Refresh index names data by running the command.
     */
    public function refresh(Request $request)
    {
        try {
            Artisan::call('market:fetch-index-names');
            return response()->json(['success' => true, 'message' => 'Index data refreshed successfully']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to refresh index data: ' . $e->getMessage()], 500);
        }
    }
}
