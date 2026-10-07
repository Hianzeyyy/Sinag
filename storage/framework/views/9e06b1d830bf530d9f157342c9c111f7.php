

<?php $__env->startSection('content'); ?>
<div class="container-fluid py-3">
    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-2 mb-4">
        <div><h1 class="h3 fw-bold mb-1">Password Reset Requests</h1><p class="text-muted mb-0">Review identity evidence before approving a password reset.</p></div>
        <span class="badge text-bg-warning fs-6"><?php echo e($requests->where('status', 'pending')->count()); ?> pending on this page</span>
    </div>
    <?php if(session('success')): ?><div class="alert alert-success"><?php echo e(session('success')); ?></div><?php endif; ?>
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="table-responsive"><table class="table align-middle mb-0"><thead class="table-light"><tr><th>Reference</th><th>Requester</th><th>ID number</th><th>Status</th><th>Submitted</th><th></th></tr></thead><tbody>
        <?php $__empty_1 = true; $__currentLoopData = $requests; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <tr><td class="fw-bold"><?php echo e($item->reference_number); ?></td><td><?php echo e($item->name); ?><div class="small text-muted"><?php echo e($item->email); ?></div></td><td><?php echo e($item->student_employee_id); ?></td><td><span class="badge text-bg-<?php echo e($item->status === 'pending' ? 'warning' : ($item->status === 'approved' ? 'success' : ($item->status === 'rejected' ? 'danger' : 'secondary'))); ?>"><?php echo e(ucfirst($item->status)); ?></span></td><td><?php echo e($item->created_at->format('M d, Y h:i A')); ?></td><td><a class="btn btn-sm btn-outline-primary" href="<?php echo e(route('admin.password-resets.show', $item)); ?>">Review</a></td></tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <tr><td colspan="6" class="text-center py-5 text-muted">No password reset requests yet.</td></tr>
        <?php endif; ?>
        </tbody></table></div>
        <?php if($requests->hasPages()): ?><div class="p-3"><?php echo e($requests->links()); ?></div><?php endif; ?>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\ANGELINE AMBROSIO\sinag\resources\views\admin\password-reset-requests\index.blade.php ENDPATH**/ ?>