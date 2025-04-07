@extends('layouts.master')

@section('title')
    FII Strategy
@endsection

@section('css')
    <style>
        .refresh-btn {
            position: fixed;
            bottom: 30px;
            right: 30px;
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background-color: #4361ee;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
            z-index: 1000;
            transition: all 0.3s ease;
        }

        .refresh-btn:hover {
            transform: rotate(180deg);
            background-color: #3a56d4;
        }

        .refresh-icon {
            font-size: 24px;
        }

        .filter-bar {
            background-color: white;
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 20px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        }

        .hot-stocks-header {
            background-color: #28a745;
            color: white;
            padding: 10px 15px;
            border-radius: 5px 5px 0 0;
            font-weight: 600;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
    </style>
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">FII Strategy</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Market</a></li>
                        <li class="breadcrumb-item active">FII Strategy</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row mb-4">
        <div class="col-12">
            <div class="filter-bar">
                <div class="row align-items-end">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="status-filter" class="form-label">Filter by Status</label>
                            <select id="status-filter" class="form-select">
                                <option value="all">All Statuses</option>
                                @foreach ($statuses as $status)
                                    <option value="{{ $status }}">{{ $status }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3 d-flex">
                        <form action="{{ route('fii-strategy.refresh') }}" method="POST" id="refreshForm">
                            @csrf
                            <button type="submit" class="btn btn-primary shadow-md" title="Refresh Data">
                                <i class="ri-refresh-line me-1"></i> Sync Data
                            </button>
                        </form>
                    </div>
                    {{-- <div class="col-md-3">
                        <div class="form-group">
                            <label for="price-filter" class="form-label">Price Range</label>
                            <select id="price-filter" class="form-select">
                                <option value="all">All Prices</option>
                                <option value="0-500">₹0 - ₹500</option>
                                <option value="500-1000">₹500 - ₹1000</option>
                                <option value="1000-2000">₹1000 - ₹2000</option>
                                <option value="2000+">₹2000+</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="search-filter" class="form-label">Search Symbol</label>
                            <input type="text" id="search-filter" class="form-control"
                                placeholder="Enter symbol name...">
                        </div>
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button id="reset-filters" class="btn btn-secondary w-100">Reset Filters</button>
                    </div> --}}
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    {{ $dataTable->table() }}
                </div>
            </div>
        </div>
    </div>

    <!-- Update Status Modal -->
    <div class="modal fade" id="updateStatusModal" tabindex="-1" aria-labelledby="updateStatusModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white p-3">
                    <h5 class="modal-title text-white" id="updateStatusModalLabel">Update Strategy</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="updateStatusForm">
                        <input type="hidden" id="strategy_id" name="strategy_id">

                        <div class="mb-4 text-center">
                            <h3 id="modal-symbol" class="fw-bold mb-2">AARTIIND</h3>
                            <div class="badge bg-light text-dark p-2">
                                Current Price: ₹<span id="modal-current-price">374.05</span>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6 mb-3">
                                <label for="buy_price" class="form-label">Buy Price</label>
                                <div class="input-group">
                                    <span class="input-group-text">₹</span>
                                    <input type="number" class="form-control" id="buy_price" name="buy_price"
                                        step="0.01" readonly>
                                </div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="sell_price" class="form-label">Sell Price</label>
                                <div class="input-group">
                                    <span class="input-group-text">₹</span>
                                    <input type="number" class="form-control" id="sell_price" name="sell_price"
                                        step="0.01" readonly>
                                </div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="status" class="form-label">Status</label>
                                <div class="input-group">
                                    <select class="form-select" id="statusval" name="status"
                                        style="width: auto; min-width: 150px;">
                                        <option value="">Select Status</option>
                                        @foreach ($statuses as $status)
                                            <option value="{{ $status }}">{{ $status }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>



                        {{-- <div class="mb-3">
                            <label for="notes" class="form-label">Notes</label>
                            <textarea class="form-control" id="notes" name="notes" rows="3"
                                placeholder="Add your strategy notes here..."></textarea>
                        </div> --}}
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="saveStatusBtn">
                        <i class="ri-save-line me-1"></i> Save Changes
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
    {{ $dataTable->scripts() }}

    <script>
        $(document).ready(function() {
            // Apply filters when changed
            $('#status-filter, #price-filter').on('change', function() {
                window.LaravelDataTables['fii-strategy-table'].draw();
            });

            // Reset filters
            $('#reset-filters').on('click', function() {
                $('#status-filter').val('all');
                $('#price-filter').val('all');
                $('#search-filter').val('');
                window.LaravelDataTables['fii-strategy-table'].search('').draw();
            });

            // Search filter
            $('#search-filter').on('keyup', function() {
                window.LaravelDataTables['fii-strategy-table'].search($(this).val()).draw();
            });

            // Update Status Modal
            $(document).on('click', '.update-status-btn', function() {
                const strategyId = $(this).data('strategy-id');
                const symbol = $(this).data('symbol');
                const currentPrice = $(this).data('current-price');
                const currentStatus = $(this).data('current-status');
                const buyPrice = $(this).data('buy-price');
                const sellPrice = $(this).data('sell-price');
                //const notes = $(this).data('notes');

                $('#strategy_id').val(strategyId);
                $('#modal-symbol').text(symbol);
                $('#modal-current-price').text(currentPrice);
                $('#statusval').val(currentStatus);
                $('#buy_price').val(buyPrice);
                $('#sell_price').val(sellPrice);
                //$('#notes').val(notes);
            });

            // Save Status Changes
            $('#saveStatusBtn').on('click', function() {
                const strategyId = $('#strategy_id').val();
                const status = $('#statusval').val();
                const buyPrice = $('#buy_price').val();
                const sellPrice = $('#sell_price').val();
                //const notes = $('#notes').val();

                $.ajax({
                    url: `/fii-strategy/${strategyId}/update-status`,
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        status: status,
                        buy_price: buyPrice,
                        sell_price: sellPrice,
                        notes: notes
                    },
                    success: function(response) {
                        if (response.success) {
                            $('#updateStatusModal').modal('hide');
                            // Reload the datatable to reflect changes
                            window.LaravelDataTables['fii-strategy-table'].ajax.reload();
                        }
                    },
                    error: function(xhr) {
                        alert('Error updating status. Please try again.');
                        console.error(xhr.responseText);
                    }
                });
            });
        });
    </script>
@endpush
