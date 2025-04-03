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
});

Auth::routes();
