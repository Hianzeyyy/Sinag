

<?php $__env->startSection('content'); ?>
<div class="container py-5" style="max-width: 560px;">
    <div class="card border-0 shadow-lg rounded-4">
        <div class="card-body p-4 p-lg-5">
            <h1 class="h3 fw-bold mb-2">Create New Password</h1>
            <p class="text-muted">Your approved reset link is valid for a single use.</p>
            <?php if($errors->any()): ?><div class="alert alert-danger"><?php echo e($errors->first()); ?></div><?php endif; ?>
            <form method="POST" action="<?php echo e(route('password.reset.submit', [$passwordResetRequest, $token])); ?>">
                <?php echo csrf_field(); ?>
                <label class="form-label fw-bold" for="password">New password</label>
                <input id="password" name="password" type="password" class="form-control mb-3" minlength="8" required autocomplete="new-password">
                <label class="form-label fw-bold" for="password_confirmation">Confirm new password</label>
                <input id="password_confirmation" name="password_confirmation" type="password" class="form-control" minlength="8" required autocomplete="new-password">
                <button class="btn btn-primary w-100 mt-4 fw-bold" type="submit"><i class="bi bi-check-circle me-2"></i>Update Password</button>
            </form>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\ANGELINE AMBROSIO\sinag\resources\views\auth\reset-password.blade.php ENDPATH**/ ?>