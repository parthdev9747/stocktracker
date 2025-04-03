<?php $__env->startSection('title', $module_name); ?>

<?php $__env->startSection('content'); ?>
    <div class="container-fluid">
        <div class="card">
            <div class="card-header align-items-center d-flex">
                <h4 class="card-title mb-0 flex-grow-1"><?php echo e($module_name); ?> List</h4>
                <div class="flex-shrink-0">
                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->any('add-role')): ?>
                        <a href="<?php echo e(route('role.create')); ?>" class="btn btn-primary">
                            <i class="ri-add-line align-bottom me-1"></i>
                            Add New
                        </a>
                    <?php endif; ?>
                </div>
            </div>

            <div class="card-body">
                <!-- Filters -->
                <div class="mb-4">
                    <div class="table-responsive-sm table-responsive-md table-responsive-lg">
                        <?php echo e($dataTable->table()); ?>

                    </div>
                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('js'); ?>
    <?php echo e($dataTable->scripts(attributes: ['type' => 'module'])); ?>

    <script>
        function deleteRecord(id) {
            let url = '<?php echo e($module_route); ?>/' + id;
            deleteRecordByAjax(url, "<?php echo e($module_name); ?>", 'roles-table');
        }
    </script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\laragon\www\stockapp\resources\views/roles/index.blade.php ENDPATH**/ ?>