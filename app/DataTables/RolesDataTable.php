<?php

namespace App\DataTables;

use Spatie\Permission\Models\Role;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

class RolesDataTable extends DataTable
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
            ->addColumn('action', function ($row) {
                $editBtn = '';
                $deleteBtn = '';
                $containerStart = '<div class="d-flex justify-content-center gap-2">';
                if (auth()->user()->can('edit-role')) {
                    $editBtn = '<a href="' . route('role.edit', $row->id) . '"  class="btn btn-primary btn-icon waves-effect waves-light"><i class="ri-edit-2-line"></i></a>';
                }
                if (auth()->user()->can('delete-role')) {
                    $usersCount = $row->users()->count();
                    if ($usersCount > 0) {
                        $deleteBtn = ' <button class="btn btn-danger btn-icon waves-effect waves-light cursor-not-allowed" disabled><i class="ri-delete-bin-5-line"></i></button>';
                    } else {
                        // Regular delete button
                        $deleteBtn = ' <button type="button" onclick="deleteRecord(' . $row->id . ')" class="btn btn-danger btn-icon waves-effect waves-light"><i class="ri-delete-bin-5-line"></i></button>';
                    }
                }
                $containerEnd = '</div>';
                return $containerStart . $editBtn . ' ' . $deleteBtn . $containerEnd;
            });
    }

    /**
     * Get the query source of dataTable.
     */
    public function query(Role $model): QueryBuilder
    {
        return $model->newQuery()->where('name', '!=', 'super_admin')->orderBy('id', 'desc');
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('roles-table')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->orderBy(1)
            ->autoWidth(false)
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
            Column::make('DT_RowIndex')->title('No')->searchable(false)->orderable(false),
            Column::make('name')->title("Name")->orderable(true)->addClass('sorting'),
            Column::computed('action')
                ->title(__('Action'))
                ->exportable(false)
                ->printable(false)
                ->width(150)
        ];
    }

    /**
     * Get the filename for export.
     */
    protected function filename(): string
    {
        return 'Roles_' . date('YmdHis');
    }
}
