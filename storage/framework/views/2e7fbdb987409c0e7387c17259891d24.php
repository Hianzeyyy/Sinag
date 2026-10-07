

<?php $__env->startSection('content'); ?>
<div class="container py-5" style="max-width: 680px;">
    <div class="card border-0 shadow-lg rounded-4">
        <div class="card-body p-4 p-lg-5">
            <h1 class="h3 fw-bold mb-2">Check Request Status</h1>
            <p class="text-muted">Use the reference number together with your registered email or ID number.</p>
            <?php if(session('success')): ?>
                <div class="alert alert-success">
                    <div><?php echo e(session('success')); ?></div>
                    <?php if(request('reference')): ?>
                        <div class="mt-2">Your reference number is:</div>
                        <div class="h4 fw-bold mb-0"><?php echo e(request('reference')); ?></div>
                        <div class="small mt-2">Save this number. You will need it together with your registered email or ID to check approval.</div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
            <?php if($errors->any()): ?><div class="alert alert-danger"><?php echo e($errors->first()); ?></div><?php endif; ?>
            <?php if(!isset($resetRequest)): ?>
                <form method="POST" action="<?php echo e(route('password.status.check')); ?>">
                    <?php echo csrf_field(); ?>
                    <label class="form-label fw-bold" for="reference_number">Reference number</label>
                    <input id="reference_number" name="reference_number" class="form-control mb-3" value="<?php echo e(old('reference_number', request('reference'))); ?>" required>
                    <label class="form-label fw-bold" for="email_or_id">Registered email or student/employee ID</label>
                    <input id="email_or_id" name="email_or_id" class="form-control" value="<?php echo e(old('email_or_id')); ?>" required>
                    <button class="btn btn-primary w-100 mt-4 fw-bold" type="submit"><i class="bi bi-search me-2"></i>Check Status</button>
                </form>
            <?php else: ?>
                <div class="border rounded-3 p-3 mb-3 bg-light"><div class="small text-muted">Reference number</div><div class="h5 fw-bold mb-0"><?php echo e($resetRequest->reference_number); ?></div></div>
                <?php if($resetRequest->status === 'approved'): ?>
                    <div class="alert alert-success">Your password reset request has been approved. The admin does not provide a password. You may now create your own new password.</div>
                    <a class="btn btn-success w-100 fw-bold" href="<?php echo e(route('password.reset.form', [$resetRequest, $token])); ?>"><i class="bi bi-key me-2"></i>Create New Password</a>
                <?php elseif($resetRequest->status === 'rejected'): ?>
                    <div class="alert alert-danger"><strong>Request rejected.</strong><?php if($resetRequest->admin_remarks): ?><div class="mt-1"><?php echo e($resetRequest->admin_remarks); ?></div><?php endif; ?></div>
                <?php elseif($resetRequest->status === 'completed'): ?>
                    <div class="alert alert-info">This password reset request has already been completed.</div>
                <?php else: ?>
                    <div class="alert alert-warning">Your request is pending administrator verification.</div>
                <?php endif; ?>
                <a class="btn btn-outline-secondary w-100 mt-2" href="<?php echo e(route('password.status.form')); ?>">Check another request</a>
            <?php endif; ?>
            <a class="d-block text-center mt-3" href="<?php echo e(route('login')); ?>">Back to Login</a>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\ANGELINE AMBROSIO\sinag\resources\views\auth\password-status.blade.php ENDPATH**/ ?>