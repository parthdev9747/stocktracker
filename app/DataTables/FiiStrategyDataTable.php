<?php

namespace App\DataTables;

use App\Models\FiiStrategy;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class FiiStrategyDataTable extends DataTable
{
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->setRowId('id')
            ->addIndexColumn()
            ->editColumn('current_price', function ($row) {
                return number_format($row->current_price, 2);
            })
            ->editColumn('high_price', function ($row) {
                return number_format($row->high_price, 2);
            })
            ->editColumn('low_price', function ($row) {
                return number_format($row->low_price, 2);
            })
            ->editColumn('buy_price', function ($row) {
                return $row->buy_price ? number_format($row->buy_price, 2) : '-';
            })
            ->editColumn('sell_price', function ($row) {
                return $row->sell_price ? number_format($row->sell_price, 2) : '-';
            })
            ->editColumn('profit_loss', function ($row) {
                if (!$row->profit_loss) return '-';

                $class = $row->profit_loss > 0 ? 'text-success' : 'text-danger';
                $sign = $row->profit_loss > 0 ? '+' : '';
                return '<span class="' . $class . '">' . $sign . number_format($row->profit_loss, 2) . '</span>';
            })
            ->editColumn('profit_loss_percentage', function ($row) {
                if (!$row->profit_loss_percentage) return '-';

                $class = $row->profit_loss_percentage > 0 ? 'text-success' : 'text-danger';
                $sign = $row->profit_loss_percentage > 0 ? '+' : '';
                return '<span class="' . $class . '">' . $sign . number_format($row->profit_loss_percentage, 2) . '%</span>';
            })
            ->addColumn('symbol', function ($row) {
                return $row->symbol->symbol ?? 'N/A';
            })
            ->addColumn('status_badge', function ($row) {
                $statusClass = str_replace(' ', '-', $row->status);
                return '<span class="status-badge status-' . $statusClass . '">' . $row->status . '</span>';
            })
            ->addColumn('action', function ($row) {
                $containerStart = '<div class="d-flex justify-content-center gap-2">';

                // Edit button
                $editBtn = '<button type="button" class="btn btn-primary btn-icon waves-effect waves-light update-status-btn" 
                    data-bs-toggle="modal" 
                    data-bs-target="#updateStatusModal" 
                    data-strategy-id="' . $row->id . '"
                    data-symbol="' . ($row->symbol->symbol ?? 'Unknown') . '"
                    data-current-price="' . $row->current_price . '"
                    data-current-status="' . $row->status . '"
                    data-buy-price="' . $row->buy_price . '"
                    data-sell-price="' . $row->sell_price . '"
                    data-notes="' . $row->notes . '">
                    <i class="ri-edit-line"></i>
                </button>';

                // View historical data button
                $viewBtn = '<a href="' . route('stock-historical-data.index', ['symbol_id' => $row->symbol_id]) . '" class="btn btn-info btn-icon waves-effect waves-light" title="View Historical Data"><i class="ri-eye-line"></i></a>';

                // TradingView chart button - using NSE symbol format
                $symbol = $row->symbol->symbol ?? '';
                if ($symbol) {
                    $tradingViewUrl = 'https://www.tradingview.com/chart/?symbol=NSE:' . $symbol . '&interval=1D';
                    $tradingViewBtn = '<a href="' . $tradingViewUrl . '" target="_blank" class="btn btn-success btn-icon waves-effect waves-light" title="Open TradingView Chart"><i class="ri-line-chart-line"></i></a>';
                } else {
                    $tradingViewBtn = '';
                }

                $containerEnd = '</div>';
                return $containerStart . $editBtn . $viewBtn . $tradingViewBtn . $containerEnd;
            })
            ->filterColumn('symbol', function ($query, $keyword) {
                $query->whereHas('symbol', function ($q) use ($keyword) {
                    $q->where('symbol', 'like', "%{$keyword}%");
                });
            })
            ->filterColumn('status_badge', function ($query, $keyword) {
                $query->where('status', 'like', "%{$keyword}%");
            })
            ->rawColumns(['status_badge', 'action', 'profit_loss', 'profit_loss_percentage']);
    }

    public function query(FiiStrategy $model): QueryBuilder
    {
        $query = $model->newQuery()->with('symbol');

        // Filter by status
        if ($this->request()->has('status') && !empty($this->request()->get('status'))) {
            $status = $this->request()->get('status');
            if ($status !== 'all') {
                $query->where('status', $status);
            }
        }

        // Filter by price range
        if ($this->request()->has('price_range') && !empty($this->request()->get('price_range'))) {
            $priceRange = $this->request()->get('price_range');

            if ($priceRange === '0-500') {
                $query->whereBetween('current_price', [0, 500]);
            } elseif ($priceRange === '500-1000') {
                $query->whereBetween('current_price', [500.01, 1000]);
            } elseif ($priceRange === '1000-2000') {
                $query->whereBetween('current_price', [1000.01, 2000]);
            } elseif ($priceRange === '2000+') {
                $query->where('current_price', '>', 2000);
            }
        }

        return $query;
    }

    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('fii-strategy-table')
            ->columns($this->getColumns())
            ->minifiedAjax(route('fii-strategy.index'), "
                data.status = $('#status-filter').val();
                data.price_range = $('#price-filter').val();
            ")
            ->orderBy(1)
            ->responsive(true)
            ->autoWidth(false)
            ->selectStyleSingle()
            ->pageLength(50)
            ->lengthMenu([[25, 50, 100, -1], [25, 50, 100, 'All']])
            ->addTableClass('table table-bordered table-hover')
            ->language([
                'lengthMenu' => 'Show _MENU_ entries',
                'search' => 'Search:'
            ])
            ->parameters([
                'debug' => true,
                'dom' => '<"d-flex justify-content-between align-items-center mb-3"<"d-flex align-items-center"l><"d-flex"f>>rtip',
                'scrollX' => true,
                'searchDelay' => 500,
                'initComplete' => "
                    function() {
                        var styleElement = document.createElement('style');
                        styleElement.textContent = `
                            .status-badge {
                                font-weight: bold;
                                padding: 6px 12px;
                                border-radius: 20px;
                                display: inline-block;
                                width: 100%;
                                text-align: center;
                            }
                            .status-Bought { background-color: #d1f7c4; color: #2b7515; }
                            .status-Sold { background-color: #ffd1d1; color: #8c2e2e; }
                            .status-Check { background-color: #fff8c4; color: #8c7215; }
                            .status-Hold { background-color: #e0e0ff; color: #2e2e8c; }
                            .status-None { background-color: #f0f0f0; color: #666; }
                            .status-Sell-Next-Day { background-color: #ffccd5; color: #8c152b; }
                            .status-Buy-Next-Day { background-color: #c4f7e0; color: #156b54; }
                        `;
                        document.head.appendChild(styleElement);
                    }
                ",
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
            Column::make('current_price')->title('Current Price')->orderable(true)->addClass('sorting text-end'),
            Column::make('high_price')->title('High Price')->orderable(true)->addClass('sorting text-end'),
            Column::make('low_price')->title('Low Price')->orderable(true)->addClass('sorting text-end'),
            Column::make('buy_price')->title('Buy Price')->orderable(true)->addClass('sorting text-end'),
            Column::make('sell_price')->title('Sell Price')->orderable(true)->addClass('sorting text-end'),
            Column::make('status_badge')->title('Status')->orderable(true)->addClass('sorting text-center'),
            Column::computed('action')
                ->title('Action')
                ->exportable(false)
                ->printable(false)
                ->width(120)
                ->addClass('text-center'),
        ];
    }

    protected function filename(): string
    {
        return 'FiiStrategy_' . date('YmdHis');
    }
}
