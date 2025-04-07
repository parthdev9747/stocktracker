@extends('layouts.master')
@section('title')
    Stock High/Low Analysis
@endsection
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">Stock High/Low Analysis</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Stock Analysis</a></li>
                        <li class="breadcrumb-item active">High/Low Analysis</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Filters</h5>
                </div>
                <div class="card-body">
                    <form id="filter-form" class="row g-3">
                        <div class="col-md-4">
                            <label for="filter_type" class="form-label">Type</label>
                            <select id="filter_type" class="form-select">
                                <option value="">All</option>
                                <option value="high">New High</option>
                                <option value="low">New Low</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="filter_date" class="form-label">Date</label>
                            <input type="date" class="form-control" id="filter_date" value="{{ date('Y-m-d') }}">
                        </div>
                        <div class="col-md-4 d-flex align-items-end">
                            <button type="button" id="apply-filters" class="btn btn-primary me-2">Apply Filters</button>
                            <button type="button" id="reset-filters" class="btn btn-secondary">Reset</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        Stocks at Period High/Low
                    </h5>
                </div>
                <div class="card-body">
                    {{ $dataTable->table() }}
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
    {{ $dataTable->scripts(attributes: ['type' => 'module']) }}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Wait for DataTables to be fully initialized
            $(document).on('init.dt', function() {
                if (typeof window.LaravelDataTables !== 'undefined' &&
                    typeof window.LaravelDataTables['stock-high-low-table'] !== 'undefined') {

                    const table = window.LaravelDataTables['stock-high-low-table'];

                    // Apply filters
                    document.getElementById('apply-filters').addEventListener('click', function() {
                        table.draw();
                    });

                    // Reset filters
                    document.getElementById('reset-filters').addEventListener('click', function() {
                        document.getElementById('filter_type').value = '';
                        document.getElementById('filter_date').value = '{{ date('Y-m-d') }}';
                        table.draw();
                    });
                }
            });
        });
    </script>
@endpush
