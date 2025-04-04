<?php

namespace App\DataTables;

use App\Models\PreOpenMarketData;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class PreOpenMarketDataDataTable extends DataTable
{
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->setRowId('id')
            ->addIndexColumn()
            ->editColumn('price', function ($row) {
                if ($row->latestHistoricalData) {
                    return '₹' . number_format($row->latestHistoricalData->closing_price, 2);
                }
                return '<span class="text-muted">N/A</span>';
            })
            ->editColumn('change', function ($row) {
                if ($row->latestHistoricalData && $row->latestHistoricalData->previous_close_price) {
                    $change = $row->latestHistoricalData->closing_price - $row->latestHistoricalData->previous_close_price;
                    $class = $change >= 0 ? 'text-success' : 'text-danger';
                    $sign = $change >= 0 ? '+' : '';
                    return '<span class="' . $class . '">' . $sign . number_format($change, 2) . '</span>';
                }
                return '<span class="text-muted">N/A</span>';
            })
            ->editColumn('percent_change', function ($row) {
                if ($row->latestHistoricalData && $row->latestHistoricalData->previous_close_price) {
                    $change = $row->latestHistoricalData->closing_price - $row->latestHistoricalData->previous_close_price;
                    $percentChange = ($change / $row->latestHistoricalData->previous_close_price) * 100;
                    $class = $percentChange >= 0 ? 'badge bg-success' : 'badge bg-danger';
                    $sign = $percentChange >= 0 ? '+' : '';
                    return '<span class="' . $class . '">' . $sign . number_format($percentChange, 2) . '%</span>';
                }
                return '<span class="text-muted">N/A</span>';
            })
            ->editColumn('is_fno', function ($row) {
                $checked = $row->is_fno ? 'checked' : '';
                return '<div class="form-check form-switch form-switch-sm text-center" dir="ltr">
                            <input type="checkbox" class="form-check-input toggle-fno" onclick="updateFnoStatus(' . $row->id . ')"' . $checked . '>
                        </div>';
            })
            ->editColumn('status', function ($row) {
                $checked = $row->status == 'active' ? 'checked' : '';
                return '<div class="form-check form-switch form-switch-sm text-center" dir="ltr">
                            <input type="checkbox" class="form-check-input toggle-status" onclick="updateStatus(' . $row->id . ')" ' . $checked . '>
                        </div>';
            })
            ->addColumn('action', function ($row) {
                $viewBtn = '';
                $editBtn = '';
                $deleteBtn = '';
                $containerStart = '<div class="d-flex justify-content-center gap-2">';

                $viewBtn = '<a href="' . route('stock-historical-data.index', ['symbol_id' => $row->id]) . '" class="btn btn-info btn-icon waves-effect waves-light" title="View Historical Data"><i class="ri-line-chart-line"></i></a>';

                // if (auth()->user()->can('edit-market-data')) {
                //     $editBtn = '<a href="' . route('pre-open-market-data.edit', $row->id) . '" class="btn btn-primary btn-icon waves-effect waves-light"><i class="ri-edit-2-line"></i></a>';
                // }

                // if (auth()->user()->can('delete-market-data')) {
                //     $deleteBtn = '<button type="button" onclick="deleteMarketData(' . $row->id . ')" class="btn btn-danger btn-icon waves-effect waves-light"><i class="ri-delete-bin-5-line"></i></button>';
                // }

                $containerEnd = '</div>';
                return $containerStart . $viewBtn . ' ' . $editBtn . ' ' . $deleteBtn . $containerEnd;
            })
            ->rawColumns(['change', 'percent_change', 'is_fno', 'status', 'latest_price', 'latest_change', 'latest_percent', 'action']);
    }

    public function query(PreOpenMarketData $model): QueryBuilder
    {
        // Use the enhanced query with latest historical data
        return $model->newQuery()
            ->with('latestHistoricalData')
            ->when(request()->has('is_fno'), function ($query) {
                return $query->where('is_fno', request('is_fno') == 'true');
            })
            ->when(request()->has('status'), function ($query) {
                return $query->where('status', request('status'));
            })
            ->orderBy('symbol', 'asc');
    }

    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('pre-open-market-data-table')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->orderBy(1)
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
            Column::make('price')->title('Price')->orderable(true)->addClass('sorting'),
            Column::make('change')->title('Change')->orderable(true)->addClass('sorting'),
            Column::make('percent_change')->title('% Change')->orderable(true)->addClass('sorting'),
            Column::make('is_fno')->title('F&O')->orderable(true)->addClass('sorting')->width(80),
            Column::make('status')->title('Status')->orderable(true)->addClass('sorting')->width(120),
            Column::computed('action')
                ->title('Action')
                ->exportable(false)
                ->printable(false)
                ->width(120),
        ];
    }

    protected function filename(): string
    {
        return 'PreOpenMarketData_' . date('YmdHis');
    }
}
