@extends('layouts.master')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="card-title mb-0">Stock Historical Data</h5>
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                                data-bs-target="#fetchDataModal">
                                <i class="ri-download-2-line align-middle me-1"></i> Fetch New Data
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        @if (session('success'))
                            <div class="alert alert-success">
                                {{ session('success') }}
                            </div>
                        @endif

                        @if (session('errors'))
                            <div class="alert alert-danger">
                                <ul>
                                    @foreach (session('errors') as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <div class="mb-4">
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="filter_symbol">Symbol</label>
                                        <select id="filter_symbol" class="form-select" data-choices id="choices-filter-symbol">
                                            <option value="all">All Symbols</option>
                                            @foreach ($symbols as $id => $symbol)
                                                <option value="{{ $id }}">{{ $symbol }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="filter_start_date">Start Date</label>
                                        <input type="date" id="filter_start_date" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="filter_end_date">End Date</label>
                                        <input type="date" id="filter_end_date" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-3 d-flex align-items-end">
                                    <button id="apply_filters" class="btn btn-primary me-2">Apply Filters</button>
                                    <button id="reset_filters" class="btn btn-secondary">Reset</button>
                                </div>
                            </div>
                        </div>

                        {{ $dataTable->table() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Fetch Data Modal -->
    <div class="modal fade" id="fetchDataModal" tabindex="-1" role="dialog" aria-labelledby="fetchDataModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form action="{{ route('stock-historical-data.fetch') }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="fetchDataModalLabel">Fetch Historical Data</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group mb-3">
                            <label for="symbol_id">Symbol</label>
                            <select name="symbol_id" id="symbol_id" class="form-select" data-choices id="choices-symbol" required>
                                <option value="">Select Symbol</option>
                                <option value="all">All Symbols</option>
                                @foreach ($symbols as $id => $symbol)
                                    <option value="{{ $id }}">{{ $symbol }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group mb-3">
                            <label for="start_date">Start Date</label>
                            <input type="date" name="start_date" id="start_date" class="form-control" required>
                        </div>
                        <div class="form-group mb-3">
                            <label for="end_date">End Date</label>
                            <input type="date" name="end_date" id="end_date" class="form-control" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Fetch Data</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('js')
    {{ $dataTable->scripts() }}
    <script>
        $(document).ready(function() {
            // Apply filters
            $('#apply_filters').click(function() {
                window.LaravelDataTables['stock-historical-data-table'].draw();
            });

            // Reset filters
            $('#reset_filters').click(function() {
                $('#filter_symbol').val('all').trigger('change');
                $('#filter_start_date').val('');
                $('#filter_end_date').val('');
                window.LaravelDataTables['stock-historical-data-table'].draw();
            });

            // Set default end date to today
            $('#end_date').val(new Date().toISOString().substr(0, 10));
            // Set default start date to 30 days ago
            let thirtyDaysAgo = new Date();
            thirtyDaysAgo.setDate(thirtyDaysAgo.getDate() - 30);
            $('#start_date').val(thirtyDaysAgo.toISOString().substr(0, 10));

            // Pass filter values to DataTable
            window.LaravelDataTables['stock-historical-data-table'].on('preXhr.dt', function(e, settings, data) {
                data.symbol_id = $('#filter_symbol').val();
                data.start_date = $('#filter_start_date').val();
                data.end_date = $('#filter_end_date').val();
            });
        });
    </script>
@endpush
