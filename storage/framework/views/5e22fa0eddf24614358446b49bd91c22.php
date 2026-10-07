

<?php $__env->startSection('content'); ?>
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
        <div><div class="text-uppercase text-muted small">Participatory Suggestions</div><h1 class="h3 fw-bold mb-0">Reported Posts and Comments</h1></div>
        <a href="<?php echo e(route('admin.suggestions')); ?>" class="btn btn-outline-secondary rounded-pill"><i class="bi bi-arrow-left me-1"></i>Back to Suggestions</a>
    </div>
    <?php if(session('success')): ?><div class="alert alert-success"><?php echo e(session('success')); ?></div><?php endif; ?>
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="table-responsive"><table class="table align-middle mb-0"><thead class="table-light"><tr><th>Type</th><th>Reported content</th><th>Reason</th><th>Reported by</th><th>Status</th><th class="text-end">Actions</th></tr></thead><tbody>
        <?php $__empty_1 = true; $__currentLoopData = $reports; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $report): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <tr><td><span class="badge text-bg-secondary"><?php echo e($report->comment_id ? 'Comment' : 'Post'); ?></span></td><td class="text-break" style="max-width:320px;"><?php echo e($report->comment_id ? optional($report->comment)->comment : optional($report->suggestion)->message); ?></td><td><?php echo e($report->reason); ?></td><td><?php echo e(optional($report->reporter)->name ?: 'Unknown'); ?></td><td><?php echo e(ucfirst($report->status)); ?></td><td class="text-end"><button type="button" class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#reportModal<?php echo e($report->id); ?>">View</button></td></tr>
            <div class="modal fade" id="reportModal<?php echo e($report->id); ?>" tabindex="-1" aria-labelledby="reportModalLabel<?php echo e($report->id); ?>" aria-hidden="true"><div class="modal-dialog modal-dialog-centered"><div class="modal-content border-0 shadow-lg rounded-4"><div class="modal-header"><h2 class="modal-title h5" id="reportModalLabel<?php echo e($report->id); ?>">Reported <?php echo e($report->comment_id ? 'Comment' : 'Post'); ?></h2><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button></div><div class="modal-body"><div class="small text-muted mb-2">Reason</div><p><?php echo e($report->reason); ?></p><div class="small text-muted mb-2">Content</div><div class="p-3 bg-light rounded-3" style="white-space:pre-wrap;"><?php echo e($report->comment_id ? optional($report->comment)->comment : optional($report->suggestion)->message); ?></div></div><div class="modal-footer"><form method="POST" action="<?php echo e(route('admin.suggestions.reports.dismiss', $report)); ?>"><?php echo csrf_field(); ?><button class="btn btn-outline-secondary" type="submit">Dismiss</button></form><form method="POST" action="<?php echo e(route('admin.suggestions.reports.delete', $report)); ?>" onsubmit="return confirm('Delete this reported content?');"><?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?><button class="btn btn-secondary" type="submit">Delete Content</button></form></div></div></div></div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <tr><td colspan="6" class="text-center py-5 text-muted">No reports yet.</td></tr>
        <?php endif; ?>
        </tbody></table></div><div class="p-3"><?php echo e($reports->links()); ?></div>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\ANGELINE AMBROSIO\sinag\resources\views\admin\suggestion_reports.blade.php ENDPATH**/ ?>