<?php

namespace App\DataTables;

use App\Models\StockHistoricalData;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class HighDeliveryStocksDataTable extends DataTable
{
    public function dataTable($query)
    {
        return datatables()
            ->of($query)
            ->addIndexColumn()
            ->editColumn('date', function ($row) {
                return Carbon::parse($row['date'])->format('d-m-Y');
            })
            ->editColumn('delivery_increase', function ($row) {
                return '<span class="text-success fw-bold">' . number_format($row['delivery_increase'], 2) . 'x</span>';
            })
            ->editColumn('delivery_percent', function ($row) {
                return '<span class="badge bg-success">' . number_format($row['delivery_percent'], 2) . '%</span>';
            })
            ->editColumn('price_change_percent', function ($row) {
                $class = $row['price_change_percent'] > 0 ? 'text-success' : 'text-danger';
                $sign = $row['price_change_percent'] > 0 ? '+' : '';
                return '<span class="' . $class . ' fw-bold">' . $sign . number_format($row['price_change_percent'], 2) . '%</span>';
            })
            ->editColumn('opening_price', function ($row) {
                return '₹' . number_format($row['opening_price'], 2);
            })
            ->editColumn('closing_price', function ($row) {
                return '₹' . number_format($row['closing_price'], 2);
            })
            ->editColumn('delivery_quantity', function ($row) {
                return number_format($row['delivery_quantity']);
            })
            ->editColumn('previous_delivery', function ($row) {
                return number_format($row['previous_delivery']);
            })
            ->editColumn('volume', function ($row) {
                return number_format($row['volume']);
            })
            ->addColumn('action', function ($row) {
                $containerStart = '<div class="d-flex justify-content-center gap-2">';

                // View historical data button
                $viewBtn = '<a href="' . route('stock-historical-data.index', ['symbol' => $row['symbol']]) . '" class="btn btn-info btn-icon waves-effect waves-light" title="View Historical Data"><i class="ri-eye-line"></i></a>';

                // TradingView chart button - using NSE symbol format
                $tradingViewUrl = 'https://www.tradingview.com/chart/?symbol=NSE:' . $row['symbol'] . '&interval=1D';
                $tradingViewBtn = '<a href="' . $tradingViewUrl . '" target="_blank" class="btn btn-primary btn-icon waves-effect waves-light" title="Open TradingView Chart"><i class="ri-line-chart-line"></i></a>';

                $containerEnd = '</div>';
                return $containerStart . $viewBtn . $tradingViewBtn . $containerEnd;
            })
            ->rawColumns(['delivery_increase', 'delivery_percent', 'price_change_percent', 'action']);
    }

    public function query()
    {
        $days = $this->request()->get('days', 7);
        return StockHistoricalData::getHighDeliveryStocks($days);
    }

    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('high-delivery-stocks-table')
            ->columns($this->getColumns())
            ->minifiedAjax(route('high-delivery-stocks.index'), "
                data.days = $('#days-filter').val();
            ")
            ->orderBy(2, 'desc') // Order by delivery increase by default
            ->responsive(true)
            ->autoWidth(false)
            ->selectStyleSingle()
            ->pageLength(25)
            ->lengthMenu([[10, 25, 50, 100, -1], [10, 25, 50, 100, 'All']])
            ->addTableClass('table table-bordered table-sm compact-table')
            ->language([
                'lengthMenu' => 'Show _MENU_ entries',
                'search' => 'Search:'
            ])
            ->parameters([
                'debug' => true,
                'dom' => '<"d-flex justify-content-between align-items-center mb-3"<"d-flex align-items-center"l><"d-flex"f>>rtip',
                'scrollX' => true,
                'searchDelay' => 500,
                'initComplete' => '
                    function() {
                        $("head").append("<style>.compact-table td, .compact-table th { padding: 0.15rem 0.5rem; font-size: 0.85rem; white-space: nowrap; } .compact-table .btn-icon { padding: 0.15rem; height: auto; width: auto; } .compact-table .btn-icon i { font-size: 0.85rem; }</style>");
                    }
                ',
            ])
            ->buttons([
                Button::make('reset'),
                Button::make('reload'),
                Button::make('excel'),
                Button::make('csv'),
                Button::make('print'),
            ]);
    }

    public function getColumns(): array
    {
        return [
            Column::make('DT_RowIndex')->title('No')->searchable(false)->orderable(false)->width(50),
            Column::make('symbol')->title('Symbol')->orderable(true)->addClass('sorting'),
            Column::make('delivery_increase')->title('Delivery Increase')->orderable(true)->addClass('sorting'),
            Column::make('delivery_percent')->title('Delivery %')->orderable(true)->addClass('sorting'),
            Column::make('opening_price')->title('Open Price')->orderable(true)->addClass('sorting'),
            Column::make('closing_price')->title('Close Price')->orderable(true)->addClass('sorting'),
            Column::make('price_change_percent')->title('Price Change %')->orderable(true)->addClass('sorting'),
            Column::make('date')->title('Date')->orderable(true)->addClass('sorting'),
            Column::make('delivery_quantity')->title('Delivery Qty')->orderable(true)->addClass('sorting'),
            Column::make('previous_delivery')->title('Prev Delivery')->orderable(true)->addClass('sorting'),
            Column::make('volume')->title('Volume')->orderable(true)->addClass('sorting'),
            Column::computed('action')
                ->title('Action')
                ->exportable(false)
                ->printable(false)
                ->width(80),
        ];
    }

    protected function filename(): string
    {
        return 'HighDeliveryStocks_' . date('YmdHis');
    }
}