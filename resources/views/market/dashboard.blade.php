@extends('layouts.master')
@section('title')
    Market Dashboard
@endsection
@push('css')
    <!-- Apex Charts CSS -->
    <link href="{{ URL::asset('assets/libs/apexcharts/apexcharts.min.css') }}" rel="stylesheet">
@endpush
@section('content')
    @component('components.breadcrumb')
        @slot('li_1')
            Dashboard
        @endslot
        @slot('title')
            Market Dashboard
        @endslot
    @endcomponent

    <div class="row">
        <div class="col-xl-12">
            <div class="card crm-widget">
                <div class="card-body p-0">
                    <div class="row row-cols-xxl-5 row-cols-md-3 row-cols-1 g-0">
                        <div class="col">
                            <div class="py-4 px-3">
                                <h5 class="text-muted text-uppercase fs-13">NIFTY 50</h5>
                                <div class="d-flex align-items-center">
                                    <div class="flex-grow-1">
                                        <h2 class="mb-0">{{ $indicativeNifty->final_closing_value ?? 'N/A' }}</h2>
                                    </div>
                                    <div class="flex-shrink-0">
                                        @if (isset($indicativeNifty->per_change) && $indicativeNifty->per_change > 0)
                                            <span class="badge bg-success-subtle text-success"><i
                                                    class="ri-arrow-up-line align-middle"></i>
                                                {{ $indicativeNifty->per_change }}%</span>
                                        @elseif(isset($indicativeNifty->per_change) && $indicativeNifty->per_change < 0)
                                            <span class="badge bg-danger-subtle text-danger"><i
                                                    class="ri-arrow-down-line align-middle"></i>
                                                {{ abs($indicativeNifty->per_change) }}%</span>
                                        @else
                                            <span class="badge  text-muted">0.00%</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col">
                            <div class="py-4 px-3">
                                <h5 class="text-muted text-uppercase fs-13">GIFT NIFTY</h5>
                                <div class="d-flex align-items-center">
                                    <div class="flex-grow-1">
                                        <h2 class="mb-0">{{ $giftNifty->last_price ?? 'N/A' }}</h2>
                                    </div>
                                    <div class="flex-shrink-0">
                                        @php
                                            $perChange = isset($giftNifty->per_change)
                                                ? trim($giftNifty->per_change)
                                                : null;
                                            $perChangeValue = is_numeric($perChange) ? floatval($perChange) : 0;
                                        @endphp
                                        @if ($perChangeValue > 0)
                                            <span class="badge bg-success-subtle text-success"><i
                                                    class="ri-arrow-up-line align-middle"></i> {{ $perChangeValue }}%</span>
                                        @elseif($perChangeValue < 0)
                                            <span class="badge bg-danger-subtle text-danger"><i
                                                    class="ri-arrow-down-line align-middle"></i>
                                                {{ abs($perChangeValue) }}%</span>
                                        @else
                                            <span class="badge  text-muted">0.00%</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col">
                            <div class="mt-3 mt-md-0 py-4 px-3">
                                <h5 class="text-muted text-uppercase fs-13">MARKET CAP</h5>
                                <div class="d-flex align-items-center">
                                    <div class="flex-grow-1">
                                        <h2 class="mb-0">₹{{ $marketCap->market_cap_in_lac_cr_rupees_formatted ?? 'N/A' }}
                                            Lac Cr</h2>
                                    </div>
                                    <div class="flex-shrink-0">
                                        <span class="badge bg-info-subtle text-info"><i
                                                class="ri-currency-line align-middle"></i>
                                            ${{ $marketCap->market_cap_in_tr_dollars ?? 'N/A' }} Tr</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col">
                            <div class="mt-3 mt-md-0 py-4 px-3">
                                <h5 class="text-muted text-uppercase fs-13">LAST UPDATED</h5>
                                <div class="d-flex align-items-center">
                                    <div class="flex-grow-1">
                                        <h4 class="mb-0">
                                            {{ isset($indicativeNifty->date_time) ? $indicativeNifty->date_time->format('d M Y, h:i A') : 'N/A' }}
                                        </h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col">
                            <div class="mt-3 mt-lg-0 py-4 px-3">
                                <h5 class="text-muted text-uppercase fs-13">STATUS</h5>
                                <div class="d-flex align-items-center">
                                    <div class="flex-grow-1">
                                        @php
                                            $capitalMarket = $marketStates->where('market', 'Capital Market')->first();
                                            $status = $capitalMarket ? $capitalMarket->market_status : 'Unknown';
                                        @endphp
                                        @if ($status == 'Open')
                                            <h4 class="mb-0 text-success">MARKET OPEN</h4>
                                        @elseif($status == 'Closed')
                                            <h4 class="mb-0 text-danger">MARKET CLOSED</h4>
                                        @else
                                            <h4 class="mb-0 text-warning">{{ $status }}</h4>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-header border-0 align-items-center d-flex">
                    <h4 class="card-title mb-0 flex-grow-1">Market States</h4>
                    <div>
                        <button type="button" class="btn btn-soft-primary btn-sm me-2" id="syncDataBtn">
                            <i class="ri-refresh-line align-middle me-1"></i> Sync Data
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        @forelse($marketStates as $state)
                            <div class="col-xl-3 col-md-6 mb-4">
                                <div class="card h-100 border-0 shadow-sm transition-all"
                                    style="transition: all 0.3s ease; box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075) !important; 
                                    @if ($state->market_status == 'Open') border-top: 3px solid #0ab39c !important; 
                                    @elseif($state->market_status == 'Closed') border-top: 3px solid #f06548 !important; 
                                    @else border-top: 3px solid #f7b84b !important; @endif"
                                    onmouseover="this.style.transform='translateY(-5px)'; this.style.boxShadow='0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important';"
                                    onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 0.125rem 0.25rem rgba(0, 0, 0, 0.075) !important';">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                            <h5 class="fw-bold text-primary mb-0">{{ ucfirst($state->market) }}</h5>
                                            @if ($state->market_status == 'Open')
                                                <span class="badge bg-success-subtle text-success fs-12 px-3">Open</span>
                                            @elseif($state->market_status == 'Closed')
                                                <span class="badge bg-danger-subtle text-danger fs-12 px-3">Closed</span>
                                            @else
                                                <span
                                                    class="badge bg-warning-subtle text-warning fs-12 px-3">{{ $state->market_status }}</span>
                                            @endif
                                        </div>

                                        <div class=" p-2 rounded mb-3">
                                            @if ($state->index)
                                                <div class="d-flex justify-content-between">
                                                    <span class="text-muted fs-13">Index</span>
                                                    <span class="fw-medium">{{ ucfirst($state->index) }}</span>
                                                </div>
                                            @endif

                                            @if ($state->last)
                                                <div class="d-flex justify-content-between mt-2">
                                                    <span class="text-muted fs-13">Last</span>
                                                    <span class="fw-medium">{{ $state->last }}</span>
                                                </div>
                                            @endif

                                            @if (is_numeric($state->variation))
                                                <div class="d-flex justify-content-between mt-2">
                                                    <span class="text-muted fs-13">Change</span>
                                                    @if ($state->variation > 0)
                                                        <span
                                                            class="fw-medium text-success">+{{ $state->variation }}</span>
                                                    @elseif($state->variation < 0)
                                                        <span class="fw-medium text-danger">{{ $state->variation }}</span>
                                                    @else
                                                        <span class="fw-medium">{{ $state->variation }}</span>
                                                    @endif
                                                </div>
                                            @endif

                                            @if (is_numeric($state->percent_change))
                                                <div class="d-flex justify-content-between mt-2">
                                                    <span class="text-muted fs-13">% Change</span>
                                                    @if ($state->percent_change > 0)
                                                        <span
                                                            class="fw-medium text-success">+{{ $state->percent_change }}%</span>
                                                    @elseif($state->percent_change < 0)
                                                        <span
                                                            class="fw-medium text-danger">{{ $state->percent_change }}%</span>
                                                    @else
                                                        <span class="fw-medium">{{ $state->percent_change }}%</span>
                                                    @endif
                                                </div>
                                            @endif
                                        </div>

                                        <div class="mt-3 pt-2 border-top">
                                            <p class="text-muted mb-0 fs-12">{{ $state->market_status_message }}</p>
                                        </div>

                                        @if ($state->trade_date)
                                            <div class="mt-3 text-end">
                                                <small class="text-muted fs-11">
                                                    <i class="ri-time-line align-bottom"></i>
                                                    {{ $state->trade_date->format('d M Y, h:i A') }}
                                                </small>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="col-12">
                                <div class="alert alert-info">
                                    No market state data available
                                </div>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-4">
            <div class="card card-height-100">
                <div class="card-header align-items-center d-flex">
                    <h4 class="card-title mb-0 flex-grow-1">GIFT Nifty Details</h4>
                </div>
                <div class="card-body">
                    @if ($giftNifty)
                        <div class="p-3 bg-soft-primary rounded mb-3">
                            <div class="d-flex align-items-center">
                                <div class="flex-grow-1">
                                    <h4 class="fs-22 fw-semibold mb-0">{{ $giftNifty->last_price }}</h4>
                                    <p class="text-muted mb-0">{{ $giftNifty->symbol }}</p>
                                </div>
                                <div class="flex-shrink-0">
                                    @php
                                        $perChange = isset($giftNifty->per_change)
                                            ? trim($giftNifty->per_change)
                                            : null;
                                        $perChangeValue = is_numeric($perChange) ? floatval($perChange) : 0;
                                    @endphp
                                    @if ($perChangeValue > 0)
                                        <span class="badge bg-success-subtle text-success fs-12 px-3">
                                            <i class="ri-arrow-up-line align-middle"></i> {{ $perChangeValue }}%
                                        </span>
                                    @elseif($perChangeValue < 0)
                                        <span class="badge bg-danger-subtle text-danger fs-12 px-3">
                                            <i class="ri-arrow-down-line align-middle"></i> {{ abs($perChangeValue) }}%
                                        </span>
                                    @else
                                        <span class="badge  text-muted fs-12 px-3">0.00%</span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted fs-13">Instrument Type</span>
                            <span class="fw-medium">{{ $giftNifty->instrument_type }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted fs-13">Expiry Date</span>
                            <span class="fw-medium">{{ $giftNifty->expiry_date }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted fs-13">Option Type</span>
                            <span class="fw-medium">{{ $giftNifty->option_type ?? 'N/A' }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted fs-13">Strike Price</span>
                            <span class="fw-medium">{{ $giftNifty->strike_price ?? 'N/A' }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted fs-13">Day Change</span>
                            <span class="fw-medium">{{ $giftNifty->day_change }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted fs-13">Contracts Traded</span>
                            <span class="fw-medium">{{ number_format($giftNifty->contracts_traded) }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted fs-13">External ID</span>
                            <span class="fw-medium">{{ $giftNifty->external_id }}</span>
                        </div>

                        <div class="mt-3 pt-2 border-top">
                            <div class="text-end">
                                <small class="text-muted fs-11">
                                    <i class="ri-time-line align-bottom"></i>
                                    {{ $giftNifty->timestamp ? $giftNifty->timestamp->format('d M Y, h:i A') : 'N/A' }}
                                </small>
                            </div>
                        </div>
                    @else
                        <div class="alert alert-info mb-0">
                            No GIFT Nifty data available
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-xl-4">
            <div class="card">
                <div class="card-header align-items-center d-flex">
                    <h4 class="card-title mb-0 flex-grow-1">Indicative Nifty 50</h4>
                </div>
                <div class="card-body">
                    @if ($indicativeNifty)
                        <div class="p-3 bg-soft-success rounded mb-3">
                            <div class="d-flex align-items-center">
                                <div class="flex-grow-1">
                                    <h4 class="fs-22 fw-semibold mb-0">{{ $indicativeNifty->final_closing_value }}</h4>
                                    <p class="text-muted mb-0">{{ $indicativeNifty->index_name }}</p>
                                </div>
                                <div class="flex-shrink-0">
                                    @if ($indicativeNifty->per_change > 0)
                                        <span class="badge bg-success-subtle text-success fs-12 px-3">
                                            <i class="ri-arrow-up-line align-middle"></i>
                                            {{ $indicativeNifty->per_change }}%
                                        </span>
                                    @elseif($indicativeNifty->per_change < 0)
                                        <span class="badge bg-danger-subtle text-danger fs-12 px-3">
                                            <i class="ri-arrow-down-line align-middle"></i>
                                            {{ abs($indicativeNifty->per_change) }}%
                                        </span>
                                    @else
                                        <span class="badge  text-muted fs-12 px-3">0.00%</span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted fs-13">Closing Value</span>
                            <span class="fw-medium">{{ $indicativeNifty->closing_value }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted fs-13">Final Closing Value</span>
                            <span class="fw-medium">{{ $indicativeNifty->final_closing_value }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted fs-13">Change</span>
                            <span class="fw-medium">
                                @if ($indicativeNifty->change > 0)
                                    <span class="text-success">+{{ $indicativeNifty->change }}</span>
                                @elseif($indicativeNifty->change < 0)
                                    <span class="text-danger">{{ $indicativeNifty->change }}</span>
                                @else
                                    <span>{{ $indicativeNifty->change }}</span>
                                @endif
                            </span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted fs-13">Index Last</span>
                            <span class="fw-medium">{{ $indicativeNifty->index_last }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted fs-13">Index % Change</span>
                            <span class="fw-medium">{{ $indicativeNifty->index_perc_change }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted fs-13">Index Time Value</span>
                            <span class="fw-medium">{{ $indicativeNifty->index_time_val }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted fs-13">Status</span>
                            @if ($indicativeNifty->status == 'Open')
                                <span class="badge bg-success-subtle text-success fs-12 px-3">Open</span>
                            @elseif($indicativeNifty->status == 'CLOSE')
                                <span class="badge bg-danger-subtle text-danger fs-12 px-3">Closed</span>
                            @else
                                <span
                                    class="badge bg-warning-subtle text-warning fs-12 px-3">{{ $indicativeNifty->status }}</span>
                            @endif
                        </div>

                        <div class="mt-3 pt-2 border-top">
                            <div class="d-flex justify-content-between">
                                <small class="text-muted fs-11">
                                    <i class="ri-calendar-line align-bottom"></i>
                                    {{ $indicativeNifty->date_time ? $indicativeNifty->date_time->format('d M Y') : 'N/A' }}
                                </small>
                                <small class="text-muted fs-11">
                                    <i class="ri-time-line align-bottom"></i>
                                    {{ $indicativeNifty->indicative_time ? $indicativeNifty->indicative_time->format('h:i A') : 'N/A' }}
                                </small>
                            </div>
                        </div>
                    @else
                        <div class="alert alert-info mb-0">
                            No Indicative Nifty 50 data available
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-xl-4">
            <div class="card">
                <div class="card-header align-items-center d-flex">
                    <h4 class="card-title mb-0 flex-grow-1">Market Cap Details</h4>
                </div>
                <div class="card-body">
                    @if ($marketCap)
                        <div class="p-3 bg-soft-info rounded mb-3">
                            <div class="d-flex align-items-center">
                                <div class="flex-grow-1">
                                    <h4 class="fs-22 fw-semibold mb-0">
                                        ₹{{ $marketCap->market_cap_in_lac_cr_rupees_formatted }}</h4>
                                    <p class="text-muted mb-0">Market Cap (₹ Lac Cr)</p>
                                </div>
                                <div class="flex-shrink-0">
                                    <span class="badge bg-info-subtle text-info fs-12 px-3">
                                        <i class="ri-currency-line align-middle"></i>
                                        ${{ $marketCap->market_cap_in_tr_dollars }} Tr
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted fs-13">Market Cap (₹ Cr)</span>
                            <span class="fw-medium">{{ $marketCap->market_cap_in_cr_rupees_formatted }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted fs-13">Market Cap (₹ Cr) Raw</span>
                            <span class="fw-medium">{{ number_format($marketCap->market_cap_in_cr_rupees) }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted fs-13">Market Cap (₹ Lac Cr) Raw</span>
                            <span class="fw-medium">{{ number_format($marketCap->market_cap_in_lac_cr_rupees, 2) }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted fs-13">Underlying</span>
                            <span class="fw-medium">{{ $marketCap->underlying }}</span>
                        </div>

                        <div class="mt-3 pt-2 border-top">
                            <div class="text-end">
                                <small class="text-muted fs-11">
                                    <i class="ri-calendar-line align-bottom"></i>
                                    {{ $marketCap->time_stamp ? $marketCap->time_stamp->format('d M Y') : 'N/A' }}
                                </small>
                            </div>
                        </div>
                    @else
                        <div class="alert alert-info mb-0">
                            No Market Cap data available
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

@endsection
@push('js')
    <!-- apexcharts -->
    <script src="{{ URL::asset('/assets/libs/apexcharts/apexcharts.min.js') }}"></script>

    <script>
        // Auto refresh the page every 5 minutes
        setTimeout(function() {
            location.reload();
        }, 5 * 60 * 1000);

        // Sync Data Button
        document.getElementById('syncDataBtn').addEventListener('click', function() {
            this.disabled = true;
            this.innerHTML = '<i class="ri-loader-4-line align-middle me-1 spin"></i> Syncing...';

            // Make AJAX request to trigger the cron job
            fetch('{{ route('market.sync') }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Show success message
                        Swal.fire({
                            title: 'Success!',
                            text: data.message || 'Data synchronized successfully',
                            icon: 'success',
                            showConfirmButton: false,
                            timer: 1500
                        });
                    } else {
                        // Show error message
                        Swal.fire({
                            title: 'Error!',
                            text: data.message || 'Failed to synchronize data',
                            icon: 'error',
                            confirmButtonText: 'OK'
                        });
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire({
                        title: 'Error!',
                        text: 'An unexpected error occurred',
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                })
                .finally(() => {
                    // Re-enable button and refresh page after a short delay
                    setTimeout(() => {
                        this.disabled = false;
                        location.reload();
                    }, 2000);
                });
        });
    </script>

    <style>
        .spin {
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            from {
                transform: rotate(0deg);
            }

            to {
                transform: rotate(360deg);
            }
        }
    </style>
@endpush
