@extends('layouts.master')

@section('title')
    Historical Data
@endsection

@section('content')
    @component('components.breadcrumb')
        @slot('li_1')
            Market
        @endslot
        @slot('title')
            Historical Data
        @endslot
    @endcomponent
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

                        <!-- Time period filter buttons -->
                        <div class="mb-3">
                            <div class="btn-group" role="group" aria-label="Time period filters">
                                <button type="button" class="btn btn-outline-primary time-filter"
                                    data-days="1">1D</button>
                                <button type="button" class="btn btn-outline-primary time-filter"
                                    data-days="7">1W</button>
                                <button type="button" class="btn btn-primary time-filter active" data-days="30">1M</button>
                                <button type="button" class="btn btn-outline-primary time-filter"
                                    data-days="90">3M</button>
                                <button type="button" class="btn btn-outline-primary time-filter"
                                    data-days="180">6M</button>
                                <button type="button" class="btn btn-outline-primary time-filter"
                                    data-days="365">1Y</button>
                            </div>
                        </div>

                        <div class="mb-4">
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="filter_symbol">Symbol</label>
                                        <select id="filter_symbol" class="form-select" data-choices
                                            id="choices-filter-symbol">
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
                            <select name="symbol_id" id="symbol_id" class="form-select" data-choices id="choices-symbol"
                                required>
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
            // Set default end date to today
            const today = new Date();
            const todayStr = today.toISOString().substr(0, 10);
            $('#end_date').val(todayStr);
            $('#filter_end_date').val(todayStr);

            // Set default start date to 30 days ago (1M)
            let defaultDays = 30;
            let startDate = new Date();
            startDate.setDate(startDate.getDate() - defaultDays);
            let startDateStr = startDate.toISOString().substr(0, 10);
            $('#start_date').val(startDateStr);
            $('#filter_start_date').val(startDateStr);

            // Check for symbol_id in URL and set the dropdown value
            const urlParams = new URLSearchParams(window.location.search);
            const symbolIdParam = urlParams.get('symbol_id');
            if (symbolIdParam) {
                $('#filter_symbol').val(symbolIdParam).trigger('change');

                // If choices.js is initialized on the select
                if (typeof Choices !== 'undefined') {
                    const filterSymbolElement = document.getElementById('filter_symbol');
                    if (filterSymbolElement) {
                        const filterSymbolInstance = filterSymbolElement.choices;
                        if (filterSymbolInstance) {
                            filterSymbolInstance.setChoiceByValue(symbolIdParam);
                        }
                    }
                }

                // Trigger filter after a short delay to ensure the select is updated
                setTimeout(function() {
                    window.LaravelDataTables['stock-historical-data-table'].draw();
                }, 100);
            }

            // Time period filter buttons
            $('.time-filter').on('click', function() {
                // Remove active class from all buttons
                $('.time-filter').removeClass('active btn-primary').addClass('btn-outline-primary');
                // Add active class to clicked button
                $(this).addClass('active btn-primary').removeClass('btn-outline-primary');

                const days = $(this).data('days');
                const endDate = new Date();
                const startDate = new Date();
                startDate.setDate(startDate.getDate() - days);

                // Update date inputs
                $('#filter_start_date').val(startDate.toISOString().substr(0, 10));
                $('#filter_end_date').val(endDate.toISOString().substr(0, 10));

                // Trigger filter
                window.LaravelDataTables['stock-historical-data-table'].draw();
            });

            // Apply filters
            $('#apply_filters').click(function() {
                window.LaravelDataTables['stock-historical-data-table'].draw();
            });

            // Reset filters
            $('#reset_filters').click(function() {
                $('#filter_symbol').val('all').trigger('change');

                // Reset to default 30 days (1M)
                const today = new Date();
                const thirtyDaysAgo = new Date();
                thirtyDaysAgo.setDate(thirtyDaysAgo.getDate() - 30);

                $('#filter_start_date').val(thirtyDaysAgo.toISOString().substr(0, 10));
                $('#filter_end_date').val(today.toISOString().substr(0, 10));

                // Reset active button to 1M
                $('.time-filter').removeClass('active btn-primary').addClass('btn-outline-primary');
                $('.time-filter[data-days="30"]').addClass('active btn-primary').removeClass(
                    'btn-outline-primary');

                window.LaravelDataTables['stock-historical-data-table'].draw();
            });

            // Pass filter values to DataTable
            window.LaravelDataTables['stock-historical-data-table'].on('preXhr.dt', function(e, settings, data) {
                data.symbol_id = $('#filter_symbol').val();
                data.start_date = $('#filter_start_date').val();
                data.end_date = $('#filter_end_date').val();
            });
        });
    </script>
@endpush
