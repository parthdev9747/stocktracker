
<?php $__env->startSection('title', $module_name); ?>
<?php $__env->startSection('content'); ?>
    <?php $__env->startComponent('components.breadcrumb'); ?>
        <?php $__env->slot('li_1'); ?>
            <?php echo e($module_name); ?>

        <?php $__env->endSlot(); ?>
        <?php $__env->slot('title'); ?>
            Edit <?php echo e($module_name); ?>

        <?php $__env->endSlot(); ?>
    <?php echo $__env->renderComponent(); ?>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header align-items-center d-flex">
                    <h4 class="card-title mb-0 flex-grow-1">Edit <?php echo e($module_name); ?></h4>
                    <div class="flex-shrink-0">
                        <a href="<?php echo e($module_route); ?>" class="btn btn-primary"><i
                                class="ri-arrow-left-line align-bottom me-1"></i> Back</a>
                    </div>
                </div>

                <div class="card-body">
                    <div class="live-preview">
                        <form method="post" class="needs-validation" novalidate
                            action="<?php echo e($module_route . '/' . $result['id']); ?>" enctype="multipart/form-data">
                            <?php echo csrf_field(); ?>
                            <input name="_method" type="hidden" value="PUT">
                            <?php echo $__env->make($module_view . '._form', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="text-end">
                                        <button type="submit" class="btn btn-primary">Update</button>
                                    </div>
                                </div><!--end col-->
                            </div><!--end row-->
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('js'); ?>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\laragon\www\stockapp\resources\views/general/edit.blade.php ENDPATH**/ ?>