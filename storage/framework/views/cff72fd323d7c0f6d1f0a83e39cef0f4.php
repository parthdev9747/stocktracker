

<?php $__env->startSection('content'); ?>
    <div class="container-fluid py-4">
        <!-- Page header with statistics -->
        <div class="row mb-4">
            <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
                <div class="card card-border card-border-primary">
                    <div class="card-body p-3">
                        <div class="row">
                            <div class="col-8">
                                <div class="numbers">
                                    <p class="text-sm mb-0 text-uppercase fw-semibold">Total Indices</p>
                                    <h5 class="font-weight-bolder mb-0">
                                        <?php echo e($stnIndices->count()); ?>

                                        <span class="text-success text-sm font-weight-bolder ms-1">
                                            <i class="fas fa-chart-line"></i>
                                        </span>
                                    </h5>
                                </div>
                            </div>
                            <div class="col-4 text-end">
                                <div class="avatar-sm rounded-circle bg-soft-primary ms-auto">
                                    <span class="avatar-title rounded-circle bg-soft-primary text-primary">
                                        <i class="ni ni-chart-bar-32 text-lg opacity-10" aria-hidden="true"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
                <div class="card card-border card-border-success">
                    <div class="card-body p-3">
                        <div class="row">
                            <div class="col-8">
                                <div class="numbers">
                                    <p class="text-sm mb-0 text-uppercase fw-semibold">Last Updated</p>
                                    <h5 class="font-weight-bolder mb-0">
                                        <?php echo e($stnIndices->max('updated_at')->diffForHumans()); ?>

                                        <span class="text-success text-sm font-weight-bolder ms-1">
                                            <i class="fas fa-clock"></i>
                                        </span>
                                    </h5>
                                </div>
                            </div>
                            <div class="col-4 text-end">
                                <div class="avatar-sm rounded-circle bg-soft-success ms-auto">
                                    <span class="avatar-title rounded-circle bg-soft-success text-success">
                                        <i class="ni ni-time-alarm text-lg opacity-10" aria-hidden="true"></i>
                                    </span>
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
                    <div class="card-header pb-0 d-flex justify-content-between align-items-center bg-soft-light">
                        <h6 class="mb-0 text-uppercase fw-semibold">Market Indices</h6>
                        <div>
                            <button class="btn btn-sm btn-primary rounded-pill" id="refreshData">
                                <i class="fas fa-sync-alt me-2"></i> Refresh Data
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        

                        <!-- Card Grid Layout -->
                        <div class="row" id="indexCardsContainer">
                            <?php $__empty_1 = true; $__currentLoopData = $stnIndices; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <div class="col-lg-4 col-md-6 mb-4 index-card">
                                    <div class="card h-100 card-hover border-0 shadow-sm">
                                        <div class="card-header p-3 pb-0 bg-transparent">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div>
                                                    <span
                                                        class="badge bg-soft-primary text-primary mb-2 rounded-pill"><?php echo e(substr($index->index_code, 0, 2)); ?></span>
                                                    <h6 class="mb-0 fw-semibold"><?php echo e($index->index_code); ?></h6>
                                                </div>
                                                <div class="text-end">
                                                    <div class="dropdown">
                                                        <a href="javascript:;" class="text-muted"
                                                            id="dropdownMenuButton<?php echo e($index->id); ?>"
                                                            data-bs-toggle="dropdown" aria-expanded="false">
                                                            <i class="fas fa-ellipsis-v"></i>
                                                        </a>
                                                        <ul class="dropdown-menu dropdown-menu-end"
                                                            aria-labelledby="dropdownMenuButton<?php echo e($index->id); ?>">
                                                            <li><a class="dropdown-item"
                                                                    href="<?php echo e(route('index-names.show', $index->id)); ?>"><i
                                                                        class="fas fa-eye me-2"></i>View Details</a></li>
                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="card-body p-3 pt-2">
                                            <div class="index-name mb-3 border-bottom pb-2">
                                                <h5 class="text-primary mb-0 fw-semibold"><?php echo e($index->index_name); ?></h5>
                                            </div>
                                            <div class="d-flex justify-content-between align-items-center">
                                                <span class="badge bg-soft-secondary text-secondary rounded-pill">
                                                    <i class="far fa-clock me-1"></i>
                                                    <?php echo e($index->updated_at->diffForHumans()); ?>

                                                </span>
                                                <a href="<?php echo e(route('index-names.show', $index->id)); ?>"
                                                    class="btn btn-sm btn-soft-primary rounded-pill px-3">
                                                    Details <i class="fas fa-arrow-right text-xs ms-1"></i>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <div class="col-12">
                                    <div class="card border-0 shadow-sm">
                                        <div class="card-body">
                                            <div class="empty-state">
                                                <div class="avatar-lg mx-auto mb-3">
                                                    <div
                                                        class="avatar-title bg-soft-primary text-primary rounded-circle fs-1">
                                                        <i class="fas fa-database"></i>
                                                    </div>
                                                </div>
                                                <h5 class="mb-2">No Data Available</h5>
                                                <p class="text-muted mb-3">No indices found in this category</p>
                                                <button class="btn btn-primary rounded-pill" id="fetchIndices">
                                                    <i class="fas fa-download me-2"></i> Fetch Indices
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>
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
                    <div class="card-header pb-0 bg-soft-light">
                        <h6 class="text-uppercase fw-semibold mb-0">Index Categories</h6>
                    </div>
                    <div class="card-body p-3">
                        <div class="row">
                            <?php
                                $categories = $stnIndices->groupBy(function ($item) {
                                    return substr($item->index_code, 0, 5);
                                });
                                $colors = ['primary', 'info', 'success', 'warning', 'danger'];
                                $bgColors = [
                                    'soft-primary',
                                    'soft-info',
                                    'soft-success',
                                    'soft-warning',
                                    'soft-danger',
                                ];
                            ?>

                            <?php $__currentLoopData = $categories->take(5); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category => $indices): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="col-lg-4 col-md-6 mb-4">
                                    <div class="card border-0 shadow-sm h-100">
                                        <div class="card-body p-4">
                                            <div class="d-flex align-items-center mb-3">
                                                <div class="flex-shrink-0">
                                                    <div class="avatar-sm">
                                                        <div
                                                            class="avatar-title bg-<?php echo e($bgColors[array_rand($bgColors)]); ?> text-<?php echo e($colors[array_rand($colors)]); ?> rounded fs-3">
                                                            <?php echo e(substr($category, 0, 1)); ?>

                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="flex-grow-1 ms-3">
                                                    <h5 class="fw-semibold mb-1"><?php echo e($category); ?></h5>
                                                    <p class="text-muted mb-0"><?php echo e($indices->count()); ?> indices</p>
                                                </div>
                                            </div>
                                            <div class="mt-3">
                                                <ul class="list-group list-group-flush">
                                                    <?php $__currentLoopData = $indices->take(3); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                        <li class="list-group-item bg-transparent px-0 py-1 border-dashed">
                                                            <div class="d-flex align-items-center">
                                                                <div class="flex-shrink-0">
                                                                    <i class="fas fa-chart-line text-muted"></i>
                                                                </div>
                                                                <div class="flex-grow-1 ms-2">
                                                                    <?php echo e($index->index_name); ?>

                                                                </div>
                                                            </div>
                                                        </li>
                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                </ul>
                                            </div>
                                            <div class="mt-3 text-end">
                                                <a class="btn btn-sm btn-soft-primary rounded-pill px-3"
                                                    href="javascript:;">
                                                    View all <i class="fas fa-arrow-right text-xs ms-1"></i>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php $__env->startPush('scripts'); ?>
        <script>
            // JavaScript remains the same
        </script>
    <?php $__env->stopPush(); ?>

    <style>
        .empty-state {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 2rem;
            text-align: center;
        }

        .card-hover {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .card-hover:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.08);
        }

        .index-name {
            min-height: 50px;
            display: flex;
            align-items: center;
        }

        .pagination-primary .page-link {
            color: #5e72e4;
            border-radius: 30px;
            margin: 0 3px;
        }

        .pagination-primary .page-item.active .page-link {
            background-color: #5e72e4;
            border-color: #5e72e4;
            color: white;
            box-shadow: 0 0.125rem 0.25rem rgba(94, 114, 228, 0.4);
        }

        .card-border {
            border-left: 4px solid transparent;
            border-radius: 0.5rem;
        }

        .card-border-primary {
            border-left-color: #5e72e4;
        }

        .card-border-success {
            border-left-color: #2dce89;
        }

        .bg-soft-primary {
            background-color: rgba(94, 114, 228, 0.1) !important;
        }

        .bg-soft-success {
            background-color: rgba(45, 206, 137, 0.1) !important;
        }

        .bg-soft-info {
            background-color: rgba(17, 205, 239, 0.1) !important;
        }

        .bg-soft-warning {
            background-color: rgba(251, 99, 64, 0.1) !important;
        }

        .bg-soft-danger {
            background-color: rgba(245, 54, 92, 0.1) !important;
        }

        .bg-soft-secondary {
            background-color: rgba(130, 134, 139, 0.1) !important;
        }

        .bg-soft-light {
            background-color: rgba(248, 249, 250, 0.5) !important;
        }

        .btn-soft-primary {
            background-color: rgba(94, 114, 228, 0.1);
            color: #5e72e4;
            border: none;
        }

        .btn-soft-primary:hover {
            background-color: #5e72e4;
            color: white;
        }

        .avatar-sm {
            height: 3rem;
            width: 3rem;
        }

        .avatar-lg {
            height: 5rem;
            width: 5rem;
        }

        .avatar-title {
            align-items: center;
            display: flex;
            font-weight: 500;
            height: 100%;
            justify-content: center;
            width: 100%;
        }

        .fw-semibold {
            font-weight: 600 !important;
        }

        .search-box .form-control:focus {
            box-shadow: none;
            border-color: #5e72e4;
        }

        .border-dashed {
            border-style: dashed !important;
        }

        .rounded-pill {
            border-radius: 50rem !important;
        }
    </style>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\laragon\www\stockapp\resources\views/index-names/index.blade.php ENDPATH**/ ?>