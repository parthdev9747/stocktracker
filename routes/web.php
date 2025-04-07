<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\HomeController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\MarketDashboardController;
use App\Http\Controllers\TradingHolidaysController;
use App\Http\Controllers\PreOpenMarketDataController;
use App\Http\Controllers\NseIndicesController;
use App\Http\Controllers\IndexNameController;
use App\Http\Controllers\StockHistoricalDataController;
use App\Http\Controllers\StockHighLowController;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware(['redirect.authenticated'])->group(function () {
    Route::get('/', function () {
        return view('auth.login');
    });
});


Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [HomeController::class, 'index'])->name('dashboard');

    Route::resource('/users', UserController::class);

    Route::resource('role', RoleController::class);
    Route::resource('permission', PermissionController::class);
    Route::resource('user', UserController::class);

    // Market Dashboard Route
    Route::get('/market-dashboard', [MarketDashboardController::class, 'index'])
        ->name('market.dashboard');
    Route::post('/market/sync', [MarketDashboardController::class, 'syncData'])->name('market.sync');

    // NSE Indices routes
    Route::get('/indices', [NseIndicesController::class, 'index'])->name('indices.index');
    Route::post('/indices/sync', [NseIndicesController::class, 'sync'])->name('indices.sync');


    Route::get('/symbol', [PreOpenMarketDataController::class, 'index'])->name('symbol.index');
    Route::post('/symbol/refresh', [PreOpenMarketDataController::class, 'refresh'])->name('symbol.refresh');
    Route::post('symbol/toggle-fno', [PreOpenMarketDataController::class, 'toggleFno'])->name('symbol.toggle-fno');
    Route::post('symbol/toggle-status', [PreOpenMarketDataController::class, 'toggleStatus'])->name('symbol.toggle-status');

    Route::get('/index-names', [IndexNameController::class, 'index'])->name('index-names.index');
    Route::get('/index-names/{id}', [IndexNameController::class, 'show'])->name('index-names.show');
    Route::post('/index-names/refresh', [IndexNameController::class, 'refresh'])->name('index-names.refresh');

    Route::get('/holidays', [TradingHolidaysController::class, 'index'])->name('holidays');
    Route::get('/holidays/data', [TradingHolidaysController::class, 'getHolidaysData'])->name('holidays.data');
    Route::get('/holidays/sync', [TradingHolidaysController::class, 'sync'])->name('holidays.sync');

    Route::get('stock-historical-data', [StockHistoricalDataController::class, 'index'])->name('stock-historical-data.index');
    Route::get('stock-historical-data/{id}', [StockHistoricalDataController::class, 'show'])->name('stock-historical-data.show');
    Route::post('stock-historical-data/fetch', [StockHistoricalDataController::class, 'fetchData'])->name('stock-historical-data.fetch');

    // Stock High/Low Analysis Routes
    Route::get('stock-high-low', [StockHighLowController::class, 'index'])->name('stock-high-low.index');
    Route::post('stock-high-low/analyze', [StockHighLowController::class, 'analyze'])->name('stock-high-low.analyze');
    // Add these routes to your web.php file
    Route::get('/commands', [App\Http\Controllers\CommandController::class, 'index'])->name('commands.index');
    Route::post('/commands/run', [App\Http\Controllers\CommandController::class, 'run'])->name('commands.run');
    Route::post('/commands/run-all', [App\Http\Controllers\CommandController::class, 'runAll'])->name('commands.run-all');
    // FII Strategy Routes
    Route::get('/fii-strategy', [App\Http\Controllers\FiiStrategyController::class, 'index'])->name('fii-strategy.index');
    Route::post('/fii-strategy/{strategy}/update-status', [App\Http\Controllers\FiiStrategyController::class, 'updateStatus'])->name('fii-strategy.update-status');
    Route::post('/fii-strategy/refresh', [App\Http\Controllers\FiiStrategyController::class, 'refresh'])->name('fii-strategy.refresh');
    // High Delivery Stocks routes
    Route::get('/high-delivery-stocks', [App\Http\Controllers\HighDeliveryStocksController::class, 'index'])->name('high-delivery-stocks.index');
    // NSE Chart routes
    Route::get('/nse-chart/{symbol}', [App\Http\Controllers\NseChartController::class, 'show'])->name('nse-chart.show');
    Route::get('/api/nse-chart/{symbol}', [App\Http\Controllers\NseChartController::class, 'fetchChartData'])->name('nse-chart.data');
});

Auth::routes();
