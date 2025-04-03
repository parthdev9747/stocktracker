{{-- File: resources/views/users/index.blade.php --}}

@extends('layouts.master')

@section('title', $module_name)

@section('content')
    <div class="container-fluid">
        <div class="card">
            <div class="card-header align-items-center d-flex">
                <h4 class="card-title mb-0 flex-grow-1">{{ $module_name }} List</h4>
            </div>

            <div class="card-body">
                <!-- Filters -->
                <div class="mb-4">
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <label for="filter_fno" class="form-label">F&O Filter</label>
                            <select id="filter_fno" class="form-select">
                                <option value="">All Stocks</option>
                                <option value="1">F&O Stocks Only</option>
                                <option value="0">Non-F&O Stocks Only</option>
                            </select>
                        </div>
                    </div>
                    <div class="table-responsive-sm table-responsive-md table-responsive-lg">
                        {{ $dataTable->table() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
    {{ $dataTable->scripts(attributes: ['type' => 'module']) }}
    <script>
        function deleteRecord(id) {
            let url = '{{ $module_route }}/' + id;
            deleteRecordByAjax(url, "{{ $module_name }}", 'pre-open-market-data-table');
        }

        function updateStatus(id) {
            let url = '{{ $module_route }}/toggle-status';
            changeStatusByAjax(url, 'pre-open-market-data-table', id);
        }

        function updateFnoStatus(id) {
            let url = '{{ $module_route }}/toggle-fno';
            changeStatusByAjax(url, 'pre-open-market-data-table', id);
        }

        // F&O Filter functionality
        $(document).ready(function() {
            $('#filter_fno').on('change', function() {
                window.LaravelDataTables['pre-open-market-data-table'].column(5).search($(this).val())
                .draw();
            });
        });
    </script>
@endpush
