<?php

namespace App\DataTables;

use App\Models\StockHighLow;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class StockHighLowDataTable extends DataTable
{
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->setRowId('id')
            ->addIndexColumn()
            ->editColumn('trade_date', function ($row) {
                return Carbon::parse($row->trade_date)->format('d-m-Y');
            })
            ->editColumn('current_high', function ($row) {
                $class = $row->is_high ? 'bg-success text-white px-2 py-1 rounded' : '';
                return '<span class="' . $class . '">' . number_format($row->current_high, 2) . '</span>';
            })
            ->editColumn('period_high', function ($row) {
                return number_format($row->period_high, 2);
            })
            ->editColumn('current_low', function ($row) {
                $class = $row->is_low ? 'bg-danger text-white px-2 py-1 rounded' : '';
                return '<span class="' . $class . '">' . number_format($row->current_low, 2) . '</span>';
            })
            ->editColumn('period_low', function ($row) {
                return number_format($row->period_low, 2);
            })
            ->addColumn('symbol', function ($row) {
                return $row->preOpenMarketData->symbol ?? 'N/A';
            })
            ->addColumn('status', function ($row) {
                $html = '';
                if ($row->is_high) {
                    $html .= '<span class="badge bg-success me-1">New High</span>';
                }
                if ($row->is_low) {
                    $html .= '<span class="badge bg-danger">New Low</span>';
                }
                return $html;
            })
            ->addColumn('high_diff', function ($row) {
                $diff = $row->current_high - $row->period_high;
                $diffPercent = $row->period_high ? ($diff / $row->period_high) * 100 : 0;
                $class = 'text-success';
                return '<span class="' . $class . '">+' . number_format($diff, 2) . ' (+' . number_format($diffPercent, 2) . '%)</span>';
            })
            ->addColumn('low_diff', function ($row) {
                $diff = $row->current_low - $row->period_low;
                $diffPercent = $row->period_low ? ($diff / $row->period_low) * 100 : 0;
                $class = 'text-danger';
                return '<span class="' . $class . '">' . number_format($diff, 2) . ' (' . number_format($diffPercent, 2) . '%)</span>';
            })
            ->addColumn('action', function ($row) {
                $containerStart = '<div class="d-flex justify-content-center gap-2">';

                // View historical data button
                $viewBtn = '<a href="' . route('stock-historical-data.index', ['symbol_id' => $row->symbol_id]) . '" class="btn btn-info btn-icon waves-effect waves-light" title="View Historical Data"><i class="ri-eye-line"></i></a>';

                // TradingView chart button - using NSE symbol format
                $symbol = $row->preOpenMarketData->symbol ?? '';
                if ($symbol) {
                    $tradingViewUrl = 'https://www.tradingview.com/chart/?symbol=NSE:' . $symbol . '&interval=1D';
                    $tradingViewBtn = '<a href="' . $tradingViewUrl . '" target="_blank" class="btn btn-primary btn-icon waves-effect waves-light" title="Open TradingView Chart"><i class="ri-line-chart-line"></i></a>';
                } else {
                    $tradingViewBtn = '';
                }

                $containerEnd = '</div>';
                return $containerStart . $viewBtn . $tradingViewBtn . $containerEnd;
            })
            ->filterColumn('symbol', function ($query, $keyword) {
                $query->whereHas('preOpenMarketData', function ($q) use ($keyword) {
                    $q->where('symbol', 'like', "%{$keyword}%");
                });
            })
            ->filterColumn('trade_date', function ($query, $keyword) {
                $query->whereRaw("DATE_FORMAT(trade_date, '%d-%m-%Y') like ?", ["%{$keyword}%"]);
            })
            ->filterColumn('status', function ($query, $keyword) {
                if (stripos('new high', $keyword) !== false) {
                    $query->where('is_high', true);
                } elseif (stripos('new low', $keyword) !== false) {
                    $query->where('is_low', true);
                }
            })
            ->rawColumns(['status', 'action', 'current_high', 'current_low', 'high_diff', 'low_diff']);
    }

    public function query(StockHighLow $model): QueryBuilder
    {
        $query = $model->newQuery()->with('preOpenMarketData');

        // Filter by type (high, low, or both)
        if ($this->request()->has('type')) {
            $type = $this->request()->get('type');
            if ($type === 'high') {
                $query->where('is_high', true);
            } elseif ($type === 'low') {
                $query->where('is_low', true);
            }
        }

        // Filter by date
        if ($this->request()->has('date') && !empty($this->request()->get('date'))) {
            $query->where('trade_date', Carbon::parse($this->request()->get('date'))->format('Y-m-d'));
        } else {
            // Default to today
            $query->where('trade_date', Carbon::today()->format('Y-m-d'));
        }

        return $query;
    }

    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('stock-high-low-table')
            ->columns($this->getColumns())
            ->minifiedAjax(route('stock-high-low.index'), "
                data.type = $('#filter_type').val();
                data.date = $('#filter_date').val();
            ")
            ->orderBy(1)
            ->responsive(true)
            ->autoWidth(false)
            ->selectStyleSingle()
            ->pageLength(50)
            ->lengthMenu([[25, 50, 100, -1], [25, 50, 100, 'All']])
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
            Column::make('trade_date')->title('Date')->orderable(true)->addClass('sorting'),
            Column::make('status')->title('Status')->orderable(false)->addClass('sorting'),
            Column::make('current_high')->title('Current High')->orderable(true)->addClass('sorting'),
            Column::make('period_high')->title('Period High')->orderable(true)->addClass('sorting'),
            Column::make('high_diff')->title('High Diff')->orderable(false)->addClass('sorting'),
            Column::make('current_low')->title('Current Low')->orderable(true)->addClass('sorting'),
            Column::make('period_low')->title('Period Low')->orderable(true)->addClass('sorting'),
            Column::make('low_diff')->title('Low Diff')->orderable(false)->addClass('sorting'),
            Column::computed('action')
                ->title('Action')
                ->exportable(false)
                ->printable(false)
                ->width(80),
        ];
    }

    protected function filename(): string
    {
        return 'StockHighLow_' . date('YmdHis');
    }
}
