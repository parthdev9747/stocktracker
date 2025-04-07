<?php

namespace App\Http\Controllers;

use App\DataTables\HighDeliveryStocksDataTable;
use App\Models\StockHistoricalData;
use Illuminate\Http\Request;

class HighDeliveryStocksController extends Controller
{
    /**
     * Display the high delivery stocks page
     */
    public function index(HighDeliveryStocksDataTable $dataTable)
    {
        return $dataTable->render('high-delivery-stocks.index');
    }
}