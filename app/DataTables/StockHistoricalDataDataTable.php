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
        return (new EloquentDataTable($query))
            ->setRowId('id')
            ->addIndexColumn()
            ->editColumn('trade_date', function ($row) {
                return Carbon::parse($row->trade_date)->format('d-m-Y');
            })
            ->editColumn('opening_price', function ($row) {
                return '₹' . number_format($row->opening_price, 2);
            })
            ->editColumn('high_price', function ($row) {
                return '₹' . number_format($row->high_price, 2);
            })
            ->editColumn('low_price', function ($row) {
                return '₹' . number_format($row->low_price, 2);
            })
            ->editColumn('closing_price', function ($row) {
                return '₹' . number_format($row->closing_price, 2);
            })
            ->editColumn('traded_quantity', function ($row) {
                return number_format($row->traded_quantity);
            })
            ->editColumn('traded_value', function ($row) {
                return '₹' . number_format($row->traded_value, 2);
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
            ->rawColumns(['price_change', 'action']);
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
            ->pageLength(25)
            ->addTableClass('table table-bordered')
            ->language([
                'lengthMenu' => 'Show _MENU_ entries'
            ])
            ->parameters([
                'debug' => true,
                'dom' => 'Bfrtip',
                'scrollX' => true,
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
            Column::computed('action')
                ->title('Action')
                ->exportable(false)
                ->printable(false)
                ->width(80),
        ];
    }

    protected function filename(): string
    {
        return 'StockHistoricalData_' . date('YmdHis');
    }
}
