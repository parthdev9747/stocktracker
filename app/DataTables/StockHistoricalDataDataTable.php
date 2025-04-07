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

class StockHistoricalDataDataTable extends DataTable
{
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        // Get min and max values for the current query to highlight them
        $dateRangeStats = $this->getDateRangeStats($query);

        return (new EloquentDataTable($query))
            ->setRowId('id')
            ->addIndexColumn()
            ->editColumn('trade_date', function ($row) {
                return Carbon::parse($row->trade_date)->format('d-m-Y');
            })
            ->editColumn('opening_price', function ($row) {
                return number_format($row->opening_price, 2);
            })
            ->editColumn('high_price', function ($row) use ($dateRangeStats) {
                $isHighest = $dateRangeStats && $row->high_price == $dateRangeStats->max_high;
                $class = $isHighest ? 'bg-success text-white px-2 py-1 rounded' : '';
                return '<span class="' . $class . '">' . number_format($row->high_price, 2) . '</span>';
            })
            ->editColumn('low_price', function ($row) use ($dateRangeStats) {
                $isLowest = $dateRangeStats && $row->low_price == $dateRangeStats->min_low;
                $class = $isLowest ? 'bg-danger text-white px-2 py-1 rounded' : '';
                return '<span class="' . $class . '">' . number_format($row->low_price, 2) . '</span>';
            })
            ->editColumn('closing_price', function ($row) {
                return number_format($row->closing_price, 2);
            })
            ->editColumn('traded_quantity', function ($row) {
                return number_format($row->traded_quantity);
            })
            ->editColumn('traded_value', function ($row) {
                return number_format($row->traded_value, 2);
            })
            ->addColumn('price_change', function ($row) {
                $change = $row->closing_price - $row->opening_price;
                $changePercent = $row->opening_price ? ($change / $row->opening_price) * 100 : 0;
                $class = $change >= 0 ? 'text-success' : 'text-danger';
                $sign = $change >= 0 ? '+' : '';
                return '<span class="' . $class . '">' . $sign . number_format($change, 2) . ' (' . $sign . number_format($changePercent, 2) . '%)</span>';
            })
            ->addColumn('symbol', function ($row) {
                return $row->preOpenMarketData->symbol ?? 'N/A';
            })
            ->addColumn('action', function ($row) {
                $viewBtn = '';
                $containerStart = '<div class="d-flex justify-content-center gap-2">';

                $viewBtn = '<a href="' . route('stock-historical-data.show', $row->id) . '" class="btn btn-info btn-icon waves-effect waves-light"><i class="ri-eye-line"></i></a>';

                $containerEnd = '</div>';
                return $containerStart . $viewBtn . $containerEnd;
            })
            ->filterColumn('symbol', function ($query, $keyword) {
                $query->whereHas('preOpenMarketData', function ($q) use ($keyword) {
                    $q->where('symbol', 'like', "%{$keyword}%");
                });
            })
            ->filterColumn('trade_date', function ($query, $keyword) {
                $query->whereRaw("DATE_FORMAT(trade_date, '%d-%m-%Y') like ?", ["%{$keyword}%"]);
            })
            ->filterColumn('opening_price', function ($query, $keyword) {
                $query->whereRaw("FORMAT(opening_price, 2) like ?", ["%{$keyword}%"]);
            })
            ->filterColumn('high_price', function ($query, $keyword) {
                $query->whereRaw("FORMAT(high_price, 2) like ?", ["%{$keyword}%"]);
            })
            ->filterColumn('low_price', function ($query, $keyword) {
                $query->whereRaw("FORMAT(low_price, 2) like ?", ["%{$keyword}%"]);
            })
            ->filterColumn('closing_price', function ($query, $keyword) {
                $query->whereRaw("FORMAT(closing_price, 2) like ?", ["%{$keyword}%"]);
            })
            ->filterColumn('traded_quantity', function ($query, $keyword) {
                $query->whereRaw("FORMAT(traded_quantity, 0) like ?", ["%{$keyword}%"]);
            })
            ->filterColumn('price_change', function ($query, $keyword) {
                // Search in the calculated price change
                $query->whereRaw(
                    "FORMAT(closing_price - opening_price, 2) like ? OR FORMAT(((closing_price - opening_price) / opening_price) * 100, 2) like ?",
                    ["%{$keyword}%", "%{$keyword}%"]
                );
            })
            ->rawColumns(['price_change', 'action', 'high_price', 'low_price']);
    }

    /**
     * Get the min and max values for the current date range
     */
    private function getDateRangeStats(QueryBuilder $query): ?object
    {
        // Clone the query to avoid modifying the original
        $statsQuery = clone $query;

        return $statsQuery->selectRaw('
            MIN(low_price) as min_low, 
            MAX(high_price) as max_high
        ')->first();
    }

    public function query(StockHistoricalData $model): QueryBuilder
    {
        $query = $model->newQuery()->with('preOpenMarketData');

        // Apply symbol filter
        if ($this->request()->has('symbol_id') && !empty($this->request()->get('symbol_id')) && $this->request()->get('symbol_id') !== 'all') {
            $query->where('symbol_id', $this->request()->get('symbol_id'));
        }

        // Apply date range filter
        if ($this->request()->has('start_date') && !empty($this->request()->get('start_date'))) {
            $query->where('trade_date', '>=', Carbon::parse($this->request()->get('start_date'))->format('Y-m-d'));
        } else {
            // Default to 1 month if no start date is provided
            $query->where('trade_date', '>=', Carbon::now()->subMonth()->format('Y-m-d'));
        }

        if ($this->request()->has('end_date') && !empty($this->request()->get('end_date'))) {
            $query->where('trade_date', '<=', Carbon::parse($this->request()->get('end_date'))->format('Y-m-d'));
        }

        return $query->orderBy('trade_date', 'desc');
    }

    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('stock-historical-data-table')
            ->columns($this->getColumns())
            ->minifiedAjax(route('stock-historical-data.index'), "
                data.symbol_id = $('#filter_symbol').val();
                data.start_date = $('#filter_start_date').val();
                data.end_date = $('#filter_end_date').val();
            ")
            ->orderBy(2, 'desc')
            ->responsive(true)
            ->autoWidth(false)
            ->selectStyleSingle()
            ->pageLength(30)
            ->addTableClass('table table-bordered table-sm compact-table')
            ->language([
                'lengthMenu' => 'Show _MENU_ entries'
            ])
            ->parameters([
                'debug' => true,
                'dom' => 'Bfrtip',
                'scrollX' => true,
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
            Column::make('trade_date')->title('Date')->orderable(true)->addClass('sorting'),
            Column::make('opening_price')->title('Open')->orderable(true)->addClass('sorting'),
            Column::make('high_price')->title('High')->orderable(true)->addClass('sorting'),
            Column::make('low_price')->title('Low')->orderable(true)->addClass('sorting'),
            Column::make('closing_price')->title('Close')->orderable(true)->addClass('sorting'),
            Column::make('price_change')->title('Change')->orderable(false)->addClass('sorting'),
            Column::make('traded_quantity')->title('Volume')->orderable(true)->addClass('sorting'),
            Column::make('delivery_quantity')->title('Delivery Qty')->orderable(true)->addClass('sorting'),
            Column::make('delivery_percent')->title('Delivery %')->orderable(true)->addClass('sorting'),
            // Column::computed('action')
            //     ->title('Action')
            //     ->exportable(false)
            //     ->printable(false)
            //     ->width(80),
        ];
    }

    protected function filename(): string
    {
        return 'StockHistoricalData_' . date('YmdHis');
    }
}
