@extends('layouts.master')
@section('title')
    NSE Indices
@endsection

@section('content')
    @component('components.breadcrumb')
        @slot('li_1')
            Market
        @endslot
        @slot('title')
            NSE Indices
        @endslot
    @endcomponent

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header border-0 align-items-center d-flex">
                    <h4 class="card-title mb-0 flex-grow-1">NSE Indices Explorer</h4>
                    <div>
                        <button type="button" class="btn btn-primary sync-btn" id="syncDataBtn">
                            <i class="ri-refresh-line align-middle me-1"></i> Sync Indices
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Stats Cards section with improved layout -->
                    <div class="row mb-4">
                        <div class="col-md-4 col-xl-2">
                            <div class="card card-animate overflow-hidden">
                                <div class="card-body bg-primary p-4">
                                    <div class="d-flex align-items-center">
                                        <div class="flex-grow-1">
                                            <h2 class="mb-1 text-white fw-semibold">{{ $stats['total'] }}</h2>
                                            <p class="text-white-75 mb-0 fs-13">Total Indices</p>
                                        </div>
                                        <div class="flex-shrink-0">
                                            <div class="avatar-sm rounded-circle bg-soft-light">
                                                <span class="avatar-title rounded-circle fs-3 text-white">
                                                    <i class="ri-bar-chart-box-line"></i>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4 col-xl-2">
                            <div class="card card-animate overflow-hidden">
                                <div class="card-body bg-success p-4">
                                    <div class="d-flex align-items-center">
                                        <div class="flex-grow-1">
                                            <h2 class="mb-1 text-white fw-semibold">{{ $stats['derivative_eligible'] }}</h2>
                                            <p class="text-white-75 mb-0 fs-13">F&O Eligible</p>
                                        </div>
                                        <div class="flex-shrink-0">
                                            <div class="avatar-sm rounded-circle bg-soft-light">
                                                <span class="avatar-title rounded-circle fs-3 text-white">
                                                    <i class="ri-line-chart-line"></i>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4 col-xl-2">
                            <div class="card card-animate overflow-hidden">
                                <div class="card-body bg-info p-4">
                                    <div class="d-flex align-items-center">
                                        <div class="flex-grow-1">
                                            <h2 class="mb-1 text-white fw-semibold">{{ $stats['broad_market'] }}</h2>
                                            <p class="text-white-75 mb-0 fs-13">Broad Market</p>
                                        </div>
                                        <div class="flex-shrink-0">
                                            <div class="avatar-sm rounded-circle bg-soft-light">
                                                <span class="avatar-title rounded-circle fs-3 text-white">
                                                    <i class="ri-stock-line"></i>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4 col-xl-2">
                            <div class="card card-animate overflow-hidden">
                                <div class="card-body bg-warning p-4">
                                    <div class="d-flex align-items-center">
                                        <div class="flex-grow-1">
                                            <h2 class="mb-1 text-white fw-semibold">{{ $stats['sectoral'] }}</h2>
                                            <p class="text-white-75 mb-0 fs-13">Sectoral</p>
                                        </div>
                                        <div class="flex-shrink-0">
                                            <div class="avatar-sm rounded-circle bg-soft-light">
                                                <span class="avatar-title rounded-circle fs-3 text-white">
                                                    <i class="ri-building-line"></i>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4 col-xl-2">
                            <div class="card card-animate overflow-hidden">
                                <div class="card-body bg-danger p-4">
                                    <div class="d-flex align-items-center">
                                        <div class="flex-grow-1">
                                            <h2 class="mb-1 text-white fw-semibold">{{ $stats['thematic'] }}</h2>
                                            <p class="text-white-75 mb-0 fs-13">Thematic</p>
                                        </div>
                                        <div class="flex-shrink-0">
                                            <div class="avatar-sm rounded-circle bg-soft-light">
                                                <span class="avatar-title rounded-circle fs-3 text-white">
                                                    <i class="ri-focus-3-line"></i>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4 col-xl-2">
                            <div class="card card-animate overflow-hidden">
                                <div class="card-body bg-secondary p-4">
                                    <div class="d-flex align-items-center">
                                        <div class="flex-grow-1">
                                            <h2 class="mb-1 text-white fw-semibold">{{ $stats['strategy'] }}</h2>
                                            <p class="text-white-75 mb-0 fs-13">Strategy</p>
                                        </div>
                                        <div class="flex-shrink-0">
                                            <div class="avatar-sm rounded-circle bg-soft-light">
                                                <span class="avatar-title rounded-circle fs-3 text-white">
                                                    <i class="ri-pie-chart-line"></i>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Filter section with improved design -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Filter Indices</h5>
                        </div>
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-md-12">
                                    <div class="d-flex flex-wrap gap-2">
                                        <button type="button"
                                            class="btn btn-soft-primary btn-sm {{ request('category') == '' ? 'active' : '' }}"
                                            data-category="">
                                            All Categories
                                        </button>
                                        @foreach ($categories as $category)
                                            <button type="button"
                                                class="btn btn-soft-primary btn-sm {{ request('category') == $category ? 'active' : '' }}"
                                                data-category="{{ $category }}">
                                                {{ $category }}
                                            </button>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                            <form action="{{ route('indices.index') }}" method="GET" id="filterForm">
                                <input type="hidden" name="category" id="categoryInput" value="{{ request('category') }}">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <div class="search-box">
                                            <input type="text" class="form-control search" name="search"
                                                placeholder="Search indices by name..." value="{{ request('search') }}">
                                            <i class="ri-search-line search-icon"></i>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-check form-switch form-switch-success mt-2">
                                            <input class="form-check-input" type="checkbox" name="derivative"
                                                value="1" id="derivativeSwitch"
                                                {{ request('derivative') ? 'checked' : '' }}
                                                onchange="document.getElementById('filterForm').submit()">
                                            <label class="form-check-label" for="derivativeSwitch">F&O Eligible
                                                Only</label>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <button type="submit" class="btn btn-primary w-100">
                                            <i class="ri-filter-3-line align-bottom me-1"></i> Apply
                                        </button>
                                    </div>
                                    <div class="col-md-2">
                                        <a href="{{ route('indices.index') }}" class="btn btn-soft-danger w-100">
                                            <i class="ri-refresh-line align-bottom me-1"></i> Reset
                                        </a>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Category cards with improved design -->
                    <div class="row">
                        @foreach ($indices->groupBy('category') as $category => $categoryIndices)
                            <div class="col-lg-6 mb-4">
                                <div class="card ribbon-box border shadow-none mb-lg-0">
                                    <div class="card-header bg-primary text-white d-flex align-items-center">
                                        <h5 class="card-title text-white mb-0 flex-grow-1">{{ $category }}</h5>
                                        <span class="badge badge-soft-light fs-12">{{ $categoryIndices->count() }}</span>
                                    </div>
                                    <div class="card-body">
                                        <div class="d-flex flex-wrap gap-2">
                                            @foreach ($categoryIndices as $index)
                                                {{-- <p
                                                    class="badge {{ $index->is_derivative_eligible ? 'bg-success-subtle text-success' : 'bg-primary-subtle text-warning' }} fs-12">
                                                    {{ $index->name }}
                                                    @if ($index->is_derivative_eligible)
                                                        <i class="ri-check-line align-middle ms-1"
                                                            data-bs-toggle="tooltip" title="F&O Eligible"></i>
                                                    @endif
                                                </p> --}}
                                                <span
                                                    class="badge border fs-12 {{ $index->is_derivative_eligible ? 'border-success text-success' : 'border-dark text-body' }}">
                                                    {{ $index->name }}
                                                    @if ($index->is_derivative_eligible)
                                                        <i class="ri-check-line align-middle ms-1"
                                                            data-bs-toggle="tooltip" title="F&O Eligible"></i>
                                                    @endif
                                                </span>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Empty state with improved design -->
                    @if ($indices->isEmpty())
                        <div class="card">
                            <div class="card-body p-4 text-center">
                                <lord-icon src="https://cdn.lordicon.com/msoeawqm.json" trigger="loop"
                                    colors="primary:#405189,secondary:#0ab39c" style="width:75px;height:75px">
                                </lord-icon>
                                <h5 class="mt-4">No indices found</h5>
                                <p class="text-muted mb-4">We couldn't find any indices matching your search criteria.</p>
                                <a href="{{ route('indices.index') }}" class="btn btn-primary">
                                    <i class="ri-refresh-line align-bottom me-1"></i> Reset Filters
                                </a>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script>
        // Handle category button selection
        document.querySelectorAll('.btn[data-category]').forEach(button => {
            button.addEventListener('click', function() {
                document.getElementById('categoryInput').value = this.dataset.category;
                document.getElementById('filterForm').submit();
            });
        });

        document.getElementById('syncDataBtn').addEventListener('click', function() {
            this.disabled = true;
            this.innerHTML = '<i class="ri-loader-4-line align-middle me-1 spin"></i> Syncing...';

            // Make AJAX request to trigger the sync
            fetch('{{ route('indices.sync') }}', {
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
                            text: data.message || 'Indices synchronized successfully',
                            icon: 'success',
                            showConfirmButton: false,
                            timer: 1500
                        });
                    } else {
                        // Show error message
                        Swal.fire({
                            title: 'Error!',
                            text: data.message || 'Failed to synchronize indices',
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

        // Initialize tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
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
