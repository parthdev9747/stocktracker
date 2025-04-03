<div class="row">
    <div class="col-sm-6 col-md-6">
        <div class="mb-3">
            <label class="form-label">Permission</label>
            <input type="text" class="form-control <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" placeholder="Enter Permission"
                value="<?php echo e(isset($result) ? $result['name'] : old('name')); ?>" name="name" required>
            <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                <div class="invalid-feedback">
                    <strong><?php echo e($message); ?></strong>
                </div>
            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            <div class="invalid-feedback">
                Name is required.
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-md-6">
        <div class="mb-3">
            <label class="form-label">Group name</label>
            <input type="text" class="form-control <?php $__errorArgs = ['group_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                placeholder="Enter Group name" value="<?php echo e(isset($result) ? $result['group_name'] : old('group_name')); ?>"
                name="group_name" required>
            <?php $__errorArgs = ['group_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                <div class="invalid-feedback">
                    <strong><?php echo e($message); ?></strong>
                </div>
            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            <div class="invalid-feedback">
                Group name is required.
            </div>
        </div>
    </div>
</div>
<?php /**PATH C:\laragon\www\stockapp\resources\views/permissions/_form.blade.php ENDPATH**/ ?>