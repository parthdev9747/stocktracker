<?php

namespace App\Http\Controllers;

use App\Models\NseIndex;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\View;

class NseIndicesController extends Controller
{

    protected $moduleName;
    protected $moduleRoute;
    protected $moduleView = "indices";
    protected $model;

    function __construct(NseIndex $model)
    {
        $this->moduleName = 'index';
        $this->moduleRoute = url('indices');
        $this->model = $model;

        // Add middleware here instead of using the HasMiddleware interface
        $this->middleware('permission:list-indices|sync-indices', ['only' => ['index']]);
        $this->middleware('permission:sync-indices', ['only' => ['sync']]);

        View::share('module_name', $this->moduleName);
        View::share('module_route', $this->moduleRoute);
        View::share('module_view', $this->moduleView);
    }
    /**
     * Display a listing of the NSE indices.
     */
    public function index(Request $request)
    {
        // Get all unique categories
        $categories = NseIndex::select('category')->distinct()->orderBy('category')->pluck('category');

        // Filter by category if provided
        $query = NseIndex::query();

        if ($request->has('category') && $request->category) {
            $query->where('category', $request->category);
        }

        if ($request->has('derivative') && $request->derivative) {
            $query->where('is_derivative_eligible', true);
        }

        if ($request->has('search') && $request->search) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        // Get indices with pagination
        $indices = $query->orderBy('category')->orderBy('name')->get();

        // Get counts for dashboard
        $stats = [
            'total' => NseIndex::count(),
            'derivative_eligible' => NseIndex::where('is_derivative_eligible', true)->count(),
            'broad_market' => NseIndex::where('category', 'Broad Market Indices')->count(),
            'sectoral' => NseIndex::where('category', 'Sectoral Indices')->count(),
            'thematic' => NseIndex::where('category', 'Thematic Indices')->count(),
            'strategy' => NseIndex::where('category', 'Strategy Indices')->count(),
        ];

        return view('indices.index', compact('indices', 'categories', 'stats'));
    }

    /**
     * Sync indices data from NSE API.
     */
    public function sync()
    {
        try {
            Artisan::call('market:fetch-indices');
            return response()->json([
                'success' => true,
                'message' => 'NSE indices data synchronized successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to synchronize NSE indices data: ' . $e->getMessage()
            ]);
        }
    }
}
