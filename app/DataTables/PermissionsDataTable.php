<?php

namespace App\DataTables;

use Spatie\Permission\Models\Permission;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

class PermissionsDataTable extends DataTable
{
    /**
     * Build the DataTable class.
     *
     * @param QueryBuilder $query Results from query() method.
     */
    public function dataTable(QueryBuilder $query)
    {
        return (new EloquentDataTable($query))
            ->setRowId('id')
            ->addIndexColumn()
            ->editColumn('created_at', function ($row) {
                return $row->created_at->format('d/m/Y');
            })
            ->editColumn('group_name', function ($row) {
                return $row->group_name ?? '-';
            })
            ->addColumn('action', function ($row) {
                $containerStart = '<div class="d-flex justify-content-center gap-2">';
                $editBtn = '<a href="' . route('permission.edit', $row->id) . '"  class="btn btn-primary btn-icon waves-effect waves-light"><i class="ri-edit-2-line"></i></a>';
                $deleteBtn = ' <button type="button" onclick="deleteRecord(' . $row->id . ')" class="btn btn-danger btn-icon waves-effect waves-light"><i class="ri-delete-bin-5-line"></i></button>';
                $containerEnd = '</div>';
                return $containerStart . $editBtn . ' ' . $deleteBtn . $containerEnd;
            });
    }

    /**
     * Get the query source of dataTable.
     */
    public function query(Permission $model): QueryBuilder
    {
        return $model->newQuery()->orderBy('id', 'desc');
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('permissions-table')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->orderBy(1)
            ->selectStyleSingle()
            ->pageLength(10)
            ->addTableClass('table table-bordered')
            ->language([
                'lengthMenu' => 'Show _MENU_ entries'
            ])
            ->parameters([
                'debug' => true
            ])
            ->buttons([]);
    }

    /**
     * Get the dataTable columns definition.
     */
    public function getColumns(): array
    {
        return [
            Column::make('DT_RowIndex')->title('No')->searchable(false),
            Column::make('name')->title('Name')->orderable(true)->addClass('sorting'),
            Column::make('group_name')->title('Group name')->orderable(true)->addClass('sorting'),
            Column::make('created_at'),
            Column::computed('action')
                ->exportable(false)
                ->printable(false)
                ->width(60)
                ->addClass('text-center'),
        ];
    }

    /**
     * Get the filename for export.
     */
    protected function filename(): string
    {
        return 'Permissions_' . date('YmdHis');
    }
}
