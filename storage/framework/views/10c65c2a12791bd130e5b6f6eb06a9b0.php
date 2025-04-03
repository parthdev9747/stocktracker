

<?php $__env->startSection('content'); ?>
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card mb-4">
                <div class="card-header pb-0 d-flex justify-content-between align-items-center">
                    <h6 class="mb-0">Index Details</h6>
                    <a href="<?php echo e(route('index-names.index')); ?>" class="btn btn-sm btn-secondary">
                        <i class="fas fa-arrow-left me-2"></i> Back to List
                    </a>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card bg-gradient-primary shadow-primary border-radius-lg">
                                <div class="card-body p-3">
                                    <div class="row">
                                        <div class="col-8">
                                            <div class="numbers">
                                                <p class="text-white text-sm mb-0 text-uppercase font-weight-bold opacity-7">Index Code</p>
                                                <h5 class="text-white font-weight-bolder mb-0">
                                                    <?php echo e($index->index_code); ?>

                                                </h5>
                                            </div>
                                        </div>
                                        <div class="col-4 text-end">
                                            <div class="icon icon-shape bg-white shadow text-center border-radius-md">
                                                <i class="fas fa-chart-line text-primary opacity-10"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card bg-gradient-info shadow-info border-radius-lg">
                                <div class="card-body p-3">
                                    <div class="row">
                                        <div class="col-8">
                                            <div class="numbers">
                                                <p class="text-white text-sm mb-0 text-uppercase font-weight-bold opacity-7">Mapping Type</p>
                                                <h5 class="text-white font-weight-bolder mb-0">
                                                    <?php echo e(strtoupper($index->index_type)); ?>

                                                </h5>
                                            </div>
                                        </div>
                                        <div class="col-4 text-end">
                                            <div class="icon icon-shape bg-white shadow text-center border-radius-md">
                                                <i class="fas fa-exchange-alt text-info opacity-10"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header pb-0">
                                    <h6 class="mb-0">Index Information</h6>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-bordered">
                                            <tbody>
                                                <tr>
                                                    <th class="w-25">Index Name</th>
                                                    <td><?php echo e($index->index_name); ?></td>
                                                </tr>
                                                <tr>
                                                    <th>Index Code</th>
                                                    <td><?php echo e($index->index_code); ?></td>
                                                </tr>
                                                <tr>
                                                    <th>Index URL</th>
                                                    <td>
                                                        <?php if($index->index_url): ?>
                                                            <a href="<?php echo e($index->index_url); ?>" target="_blank" class="text-primary">
                                                                <?php echo e($index->index_url); ?> <i class="fas fa-external-link-alt ms-1"></i>
                                                            </a>
                                                        <?php else: ?>
                                                            <span class="text-muted">Not available</span>
                                                        <?php endif; ?>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th>Mapping Type</th>
                                                    <td>
                                                        <?php if($index->index_type == 'stn'): ?>
                                                            <span class="badge bg-gradient-primary">Short to Name</span>
                                                        <?php else: ?>
                                                            <span class="badge bg-gradient-info">Name to Short</span>
                                                        <?php endif; ?>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th>Created At</th>
                                                    <td><?php echo e($index->created_at->format('d M Y, H:i:s')); ?></td>
                                                </tr>
                                                <tr>
                                                    <th>Last Updated</th>
                                                    <td><?php echo e($index->updated_at->format('d M Y, H:i:s')); ?></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\laragon\www\stockapp\resources\views/index-names/show.blade.php ENDPATH**/ ?>