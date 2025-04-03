<?php

namespace App\DataTables;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class UsersDataTable extends DataTable
{
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->setRowId('id')
            ->addIndexColumn()
            ->editColumn('created_at', function ($row) {
                return $row->created_at->format('d/m/Y H:i:s');
            })
            ->addColumn('action', function ($row) {
                $editBtn = '';
                $deleteBtn = '';
                $containerStart = '<div class="d-flex justify-content-center gap-2">';
                if (auth()->user()->can('edit-user')) {
                    $editBtn = '<a href="' . route('user.edit', $row->id) . '"  class="btn btn-primary btn-icon waves-effect waves-light"><i class="ri-edit-2-line"></i></a>';
                }
                if (auth()->user()->can('delete-user')) {
                    $deleteBtn = ' <button type="button" onclick="deleteRecord(' . $row->id . ')" class="btn btn-danger btn-icon waves-effect waves-light"><i class="ri-delete-bin-5-line"></i></button>';
                }
                $containerEnd = '</div>';
                return $containerStart . $editBtn . ' ' . $deleteBtn . $containerEnd;
            });
    }

    public function query(User $model): QueryBuilder
    {
        return $model->newQuery()->where('id', '!=', auth()->user()->id)->whereDoesntHave('roles', function ($query) {
            $query->whereIn('name', ['super_admin']);
        });;
    }

    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('users-table')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->orderBy(1)
            ->responsive(false)
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
            ->buttons([
                // Button::make('add'),
                // Button::make('excel'),
                // Button::make('csv'),
                // Button::make('pdf'),
                // Button::make('print'),
                Button::make('reset'),
                Button::make('reload'),
            ]);
    }

    public function getColumns(): array
    {
        return [
            Column::make('DT_RowIndex')->title('No')->searchable(false)->orderable(false),
            Column::make('name')->title('Name')->orderable(true)->addClass('sorting'),
            Column::make('email')->title('Email')->orderable(true)->addClass('sorting'),
            Column::computed('action')
                ->title('Action')
                ->exportable(false)
                ->printable(false)
                ->width(150),
        ];
    }

    protected function filename(): string
    {
        return 'Users_' . date('YmdHis');
    }
}
