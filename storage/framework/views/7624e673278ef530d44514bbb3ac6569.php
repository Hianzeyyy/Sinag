
<?php $__env->startSection('content'); ?>
<div class="container py-4">
    <div class="announcement-hero rounded-4 p-3 p-lg-3 mb-3 shadow-sm position-relative overflow-hidden">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
            <div>
                <h2 class="fw-bold mb-1 fs-4" style="color:#A594F9;"><i class="bi bi-megaphone-fill me-2"></i> Announcements</h2>
                <p class="mb-0 text-muted small">Manage your posted updates for students and campus community.</p>
            </div>
            <a href="<?php echo e(route('admin.announcements.create')); ?>" class="btn fw-bold shadow-sm px-3 py-2" style="background: linear-gradient(90deg, #facc15 60%, #c4b5fd 100%); color:#8f72f5; border:none; border-radius:10px;">
            <i class="bi bi-plus-circle me-1"></i> New Announcement
            </a>
        </div>
    </div>

    <?php if(session('success')): ?>
        <div class="alert border-0 rounded-4 shadow-sm d-flex align-items-center" style="background:#ecfdf5; color:#065f46;">
            <i class="bi bi-check-circle-fill me-2"></i> <?php echo e(session('success')); ?>

        </div>
    <?php endif; ?>

    <div class="card border-0 shadow-lg rounded-4" style="background: rgba(255,255,255,0.92); box-shadow: 0 8px 32px #a594f922;">
        <div class="card-body p-0">
            <div class="table-responsive">
            <table class="table table-hover mb-0 align-middle" style="border-radius: 16px; overflow: hidden; min-width: 980px;">
                <thead style="background: linear-gradient(90deg, #c4b5fd 60%, #facc15 100%); color:#1e1b4b;">
                    <tr>
                        <th class="ps-4"><i class="bi bi-megaphone"></i> Title</th>
                        <th><i class="bi bi-card-text"></i> Content</th>
                        <th><i class="bi bi-person-circle"></i> Posted By</th>
                        <th><i class="bi bi-calendar-event"></i> Date</th>
                        <th class="text-end pe-4"><i class="bi bi-gear"></i> Actions</th>
                    </tr>
                </thead>
                <tbody>
                <?php $__empty_1 = true; $__currentLoopData = $announcements; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $announcement): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr style="border-bottom:1px solid #ede9fe;">
                        <td class="fw-bold ps-4" style="color:#8f72f5;"><?php echo e($announcement->title); ?></td>
                        <td style="max-width: 420px;">
                            <div class="small text-dark" style="white-space: pre-line;"><?php echo e(\Illuminate\Support\Str::limit($announcement->body, 160)); ?></div>
                        </td>
                        <td><?php echo e($announcement->user->name ?? 'Admin'); ?></td>
                        <td><?php echo e($announcement->created_at->format('M d, Y')); ?></td>
                        <td class="text-end pe-4">
                            <a href="<?php echo e(route('admin.announcements.edit', $announcement->id)); ?>" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                                <i class="bi bi-pencil-square me-1"></i> Edit
                            </a>
                            <form action="<?php echo e(route('admin.announcements.destroy', $announcement->id)); ?>" method="POST" class="d-inline" onsubmit="return confirm('Delete this announcement?');">
                                <?php echo csrf_field(); ?>
                                <?php echo method_field('DELETE'); ?>
                                <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill px-3 ms-1">
                                    <i class="bi bi-trash me-1"></i> Delete
                                </button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr><td colspan="5" class="text-center text-muted py-4">No announcements yet.</td></tr>
                <?php endif; ?>
                </tbody>
            </table>
            </div>
        </div>
    </div>
    <div class="mt-3"><?php echo e($announcements->links()); ?></div>
</div>

<style>
    .announcement-hero {
        background: linear-gradient(115deg, #ede9fe 0%, #f5f3ff 55%, #fef9c3 100%);
        border: 1px solid #e9d5ff;
    }

    .table-hover tbody tr:hover {
        background-color: #fbfaff;
    }
</style>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\ANGELINE AMBROSIO\sinag\resources\views\admin\announcements\index.blade.php ENDPATH**/ ?>