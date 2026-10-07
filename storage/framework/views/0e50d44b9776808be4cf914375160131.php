

<?php $__env->startSection('content'); ?>
<style>
    .event-media {
        width: 100%;
        height: 420px;
        border-radius: 0.8rem;
        object-fit: cover;
    }

    @media (max-width: 575.98px) {
        .event-page-card { padding: 1rem !important; }
        .event-media,
        .event-media-placeholder { height: min(420px, 72vw); }
    }
</style>
<div class="container py-5">
    <div class="card event-page-card rounded-4 p-4">
        <div class="row g-4 align-items-start">
            <div class="col-12 col-lg-7">
                <?php if($image): ?>
                    <img src="<?php echo e(asset($image)); ?>" alt="Event <?php echo e($id); ?>" class="event-media">
                <?php else: ?>
                    <div class="event-media event-media-placeholder" style="background:#f3f4f6;"></div>
                <?php endif; ?>
            </div>
            <div class="col-12 col-lg-5">
                <h2 class="fw-bold"><?php echo e($title); ?></h2>
                <p class="text-muted"><?php echo e($body); ?></p>
                <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Pellentesque euismod, nisi vel consectetur interdum, nisl nisi consequat nunc, ut cursus orci lorem ac libero.</p>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\ANGELINE AMBROSIO\sinag\resources\views\student\event.blade.php ENDPATH**/ ?>