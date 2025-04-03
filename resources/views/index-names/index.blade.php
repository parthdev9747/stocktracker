@extends('layouts.master')

@section('content')
    <div class="container-fluid py-4">
        <!-- Page header with statistics -->
        <div class="row mb-4">
            <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
                <div class="card">
                    <div class="card-body p-3">
                        <div class="row">
                            <div class="col-8">
                                <div class="numbers">
                                    <p class="text-sm mb-0 text-uppercase font-weight-bold">Total Indices</p>
                                    <h5 class="font-weight-bolder">
                                        {{ $stnIndices->count() }}
                                    </h5>
                                </div>
                            </div>
                            <div class="col-4 text-end">
                                <div class="icon icon-shape bg-gradient-primary shadow-primary text-center rounded-circle">
                                    <i class="ni ni-chart-bar-32 text-lg opacity-10" aria-hidden="true"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
                <div class="card">
                    <div class="card-body p-3">
                        <div class="row">
                            <div class="col-8">
                                <div class="numbers">
                                    <p class="text-sm mb-0 text-uppercase font-weight-bold">Last Updated</p>
                                    <h5 class="font-weight-bolder">
                                        {{ $stnIndices->max('updated_at')->diffForHumans() }}
                                    </h5>
                                </div>
                            </div>
                            <div class="col-4 text-end">
                                <div class="icon icon-shape bg-gradient-success shadow-success text-center rounded-circle">
                                    <i class="ni ni-time-alarm text-lg opacity-10" aria-hidden="true"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card mb-4">
                    <div class="card-header pb-0 d-flex justify-content-between align-items-center">
                        <h6 class="mb-0">Market Indices</h6>
                        <div>
                            <button class="btn btn-sm btn-primary" id="refreshData">
                                <i class="fas fa-sync-alt me-2"></i> Refresh Data
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <!-- Search bar -->
                        <div class="mb-4">
                            <div class="input-group">
                                <span class="input-group-text text-body"><i class="fas fa-search"
                                        aria-hidden="true"></i></span>
                                <input type="text" class="form-control" id="indexSearch"
                                    placeholder="Search for indices...">
                            </div>
                        </div>

                        <!-- Card Grid Layout -->
                        <div class="row" id="indexCardsContainer">
                            @forelse($stnIndices as $index)
                                <div class="col-lg-4 col-md-6 mb-4 index-card">
                                    <div class="card h-100 card-hover">
                                        <div class="card-header p-3 pb-0">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div>
                                                    <span class="badge bg-gradient-primary mb-2">{{ substr($index->index_code, 0, 2) }}</span>
                                                    <h6 class="mb-0">{{ $index->index_code }}</h6>
                                                </div>
                                                <div class="text-end">
                                                    <div class="dropdown">
                                                        <a href="javascript:;" class="text-secondary" id="dropdownMenuButton{{ $index->id }}" data-bs-toggle="dropdown" aria-expanded="false">
                                                            <i class="fas fa-ellipsis-v"></i>
                                                        </a>
                                                        <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton{{ $index->id }}">
                                                            <li><a class="dropdown-item" href="{{ route('index-names.show', $index->id) }}">View Details</a></li>
                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="card-body p-3 pt-2">
                                            <div class="index-name mb-3">
                                                <h5 class="text-gradient text-primary mb-0">{{ $index->index_name }}</h5>
                                            </div>
                                            <div class="d-flex justify-content-between align-items-center">
                                                <span class="text-xs text-muted">
                                                    <i class="far fa-clock me-1"></i>
                                                    Updated {{ $index->updated_at->diffForHumans() }}
                                                </span>
                                                <a href="{{ route('index-names.show', $index->id) }}" class="btn btn-link text-primary text-sm mb-0 px-0 ms-auto">
                                                    View Details
                                                    <i class="fas fa-arrow-right text-xs ms-1"></i>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="col-12">
                                    <div class="card">
                                        <div class="card-body">
                                            <div class="empty-state">
                                                <i class="fas fa-database fa-3x text-secondary mb-3"></i>
                                                <p class="text-sm mb-0">No indices found in this category</p>
                                                <button class="btn btn-sm btn-outline-primary mt-3" id="fetchIndices">
                                                    <i class="fas fa-download me-2"></i> Fetch Indices
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforelse
                        </div>

                        <!-- Pagination -->
                        <div class="d-flex justify-content-center mt-4" id="indexPagination">
                            <!-- Pagination will be added by JavaScript -->
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Index Categories Section -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header pb-0">
                        <h6>Index Categories</h6>
                    </div>
                    <div class="card-body p-3">
                        <div class="row">
                            @php
                                $categories = $stnIndices->groupBy(function ($item) {
                                    return substr($item->index_code, 0, 5);
                                });
                                $colors = ['primary', 'info', 'success', 'warning', 'danger'];
                            @endphp

                            @foreach ($categories->take(5) as $category => $indices)
                                <div class="col-lg-4 col-md-6 mb-4">
                                    <div
                                        class="card card-background shadow-none card-background-mask-{{ $colors[array_rand($colors)] }} h-100">
                                        <div class="full-background"
                                            style="background-image: url('/assets/img/curved-images/white-curved.jpeg')">
                                        </div>
                                        <div class="card-body position-relative z-index-1 d-flex flex-column h-100 p-3">
                                            <h5 class="text-white font-weight-bolder mb-4 pt-2">{{ $category }}</h5>
                                            <p class="text-white text-sm">{{ $indices->count() }} indices</p>
                                            <ul class="list-group list-group-flush">
                                                @foreach ($indices->take(3) as $index)
                                                    <li
                                                        class="list-group-item bg-transparent border-0 ps-0 pt-0 text-white">
                                                        {{ $index->index_name }}
                                                    </li>
                                                @endforeach
                                            </ul>
                                            <a class="text-white text-sm font-weight-bold mb-0 icon-move-right mt-auto"
                                                href="javascript:;">
                                                View all
                                                <i class="fas fa-arrow-right text-sm ms-1" aria-hidden="true"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            $(document).ready(function() {
                // Initialize pagination
                const itemsPerPage = 9;
                const $indexCards = $('.index-card');
                const totalPages = Math.ceil($indexCards.length / itemsPerPage);
                
                function showPage(page) {
                    const startIndex = (page - 1) * itemsPerPage;
                    const endIndex = startIndex + itemsPerPage;
                    
                    $indexCards.hide();
                    $indexCards.slice(startIndex, endIndex).show();
                    
                    updatePagination(page);
                }
                
                function updatePagination(currentPage) {
                    let paginationHtml = '';
                    
                    if (totalPages > 1) {
                        paginationHtml += '<ul class="pagination pagination-primary justify-content-center">';
                        
                        // Previous button
                        paginationHtml += `<li class="page-item ${currentPage === 1 ? 'disabled' : ''}">
                            <a class="page-link" href="javascript:;" data-page="${currentPage - 1}" aria-label="Previous">
                                <span aria-hidden="true">&laquo;</span>
                            </a>
                        </li>`;
                        
                        // Page numbers
                        for (let i = 1; i <= totalPages; i++) {
                            paginationHtml += `<li class="page-item ${i === currentPage ? 'active' : ''}">
                                <a class="page-link" href="javascript:;" data-page="${i}">${i}</a>
                            </li>`;
                        }
                        
                        // Next button
                        paginationHtml += `<li class="page-item ${currentPage === totalPages ? 'disabled' : ''}">
                            <a class="page-link" href="javascript:;" data-page="${currentPage + 1}" aria-label="Next">
                                <span aria-hidden="true">&raquo;</span>
                            </a>
                        </li>`;
                        
                        paginationHtml += '</ul>';
                    }
                    
                    $('#indexPagination').html(paginationHtml);
                    
                    // Add click event to pagination links
                    $('.page-link').on('click', function() {
                        const page = parseInt($(this).data('page'));
                        if (!isNaN(page) && page > 0 && page <= totalPages) {
                            showPage(page);
                        }
                    });
                }
                
                // Show first page initially
                if ($indexCards.length > 0) {
                    showPage(1);
                }

                // Search functionality
                $('#indexSearch').on('keyup', function() {
                    const searchTerm = $(this).val().toLowerCase();
                    
                    if (searchTerm.length > 0) {
                        $('.index-card').each(function() {
                            const indexCode = $(this).find('h6').text().toLowerCase();
                            const indexName = $(this).find('h5').text().toLowerCase();
                            
                            if (indexCode.includes(searchTerm) || indexName.includes(searchTerm)) {
                                $(this).show();
                            } else {
                                $(this).hide();
                            }
                        });
                        
                        // Hide pagination when searching
                        $('#indexPagination').hide();
                    } else {
                        // Show pagination and reset to first page when search is cleared
                        $('#indexPagination').show();
                        showPage(1);
                    }
                });

                // Refresh data button
                $('#refreshData').on('click', function() {
                    const button = $(this);
                    button.html('<i class="fas fa-spinner fa-spin me-2"></i> Refreshing...');
                    button.prop('disabled', true);

                    // Make an AJAX call to run the command
                    $.ajax({
                        url: '{{ route('index-names.refresh') }}',
                        type: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            if (response.success) {
                                // Show success notification
                                toastr.success('Index data refreshed successfully!');
                                // Reload the page after a short delay
                                setTimeout(function() {
                                    window.location.reload();
                                }, 1500);
                            } else {
                                toastr.error('Failed to refresh index data.');
                                button.html('<i class="fas fa-sync-alt me-2"></i> Refresh Data');
                                button.prop('disabled', false);
                            }
                        },
                        error: function() {
                            toastr.error('An error occurred while refreshing data.');
                            button.html('<i class="fas fa-sync-alt me-2"></i> Refresh Data');
                            button.prop('disabled', false);
                        }
                    });
                });

                // Fetch indices button (for empty state)
                $('#fetchIndices').on('click', function() {
                    $('#refreshData').click();
                });
            });
        </script>
    @endpush

    <style>
        .empty-state {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 2rem;
        }

        .card-hover {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .card-hover:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
        }

        .index-name {
            min-height: 50px;
            display: flex;
            align-items: center;
        }

        .card-background-mask-primary {
            background-size: cover;
            background-position: center;
        }
        
        .pagination-primary .page-link {
            color: #5e72e4;
        }
        
        .pagination-primary .page-item.active .page-link {
            background-color: #5e72e4;
            border-color: #5e72e4;
            color: white;
        }
    </style>
@endsection
