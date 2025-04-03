



<?php $__env->startSection('title', $module_name); ?>

<?php $__env->startSection('content'); ?>
    <div class="container-fluid">
        <div class="card">
            <div class="card-header align-items-center d-flex">
                <h4 class="card-title mb-0 flex-grow-1"><?php echo e($module_name); ?> List</h4>
            </div>

            <div class="card-body">
                <!-- Filters -->
                <div class="mb-4">
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <label for="filter_fno" class="form-label">F&O Filter</label>
                            <select id="filter_fno" class="form-select">
                                <option value="">All Stocks</option>
                                <option value="1">F&O Stocks Only</option>
                                <option value="0">Non-F&O Stocks Only</option>
                            </select>
                        </div>
                    </div>
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
            deleteRecordByAjax(url, "<?php echo e($module_name); ?>", 'pre-open-market-data-table');
        }

        function updateStatus(id) {
            let url = '<?php echo e($module_route); ?>/toggle-status';
            changeStatusByAjax(url, 'pre-open-market-data-table', id);
        }

        function updateFnoStatus(id) {
            let url = '<?php echo e($module_route); ?>/toggle-fno';
            changeStatusByAjax(url, 'pre-open-market-data-table', id);
        }

        // F&O Filter functionality
        $(document).ready(function() {
            $('#filter_fno').on('change', function() {
                window.LaravelDataTables['pre-open-market-data-table'].column(5).search($(this).val())
                .draw();
            });
        });
    </script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\laragon\www\stockapp\resources\views/pre-open-market-data/index.blade.php ENDPATH**/ ?>