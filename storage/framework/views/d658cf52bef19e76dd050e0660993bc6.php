

<?php $__env->startSection('content'); ?>
<div class="container-fluid py-4">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-5">
        <div>
            <h1 class="h3 fw-bold mb-1" style="color: #a78bfa;">
    <i class="bi bi-calendar-check"></i> My Appointments
</h1>
            <p class="text-muted"></p>
        </div>
        <div class="d-flex gap-2">
            <a href="<?php echo e(route('student.gad-schedule')); ?>" class="btn btn-lg fw-semibold btn-outline-secondary" style="border-radius: 0.75rem;">
                <i class="bi bi-calendar3 me-1"></i> GAD Office Hours
            </a>
            <a href="<?php echo e(route('student.schedule-appointment')); ?>" class="btn btn-lg fw-semibold" style="background: linear-gradient(135deg, #A594F9 0%, #c4b5fd 100%); color: white; border: none; border-radius: 0.75rem;">
                <i class="bi bi-plus-circle"></i> New Appointment
            </a>
        </div>
    </div>

    <?php if(session('success')): ?>
        <div class="alert alert-dismissible fade show" role="alert" style="background: #f3efff; border: 1px solid #d8cdfc; color: #5b4b9b;">
            <i class="bi bi-check-circle"></i> <?php echo e(session('success')); ?>

            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if($errors->has('appointment')): ?>
        <div class="alert alert-warning"><?php echo e($errors->first('appointment')); ?></div>
    <?php endif; ?>

    <?php if($appointments->isEmpty()): ?>
        <!-- Empty State -->
        <div class="text-center py-5">
            <div class="mb-4">
                <i class="bi bi-inbox" style="font-size: 3rem; color: #ccc;"></i>
            </div>
            <h5 class="text-muted">No Appointments Yet</h5>
            <p class="text-muted mb-4">You haven't submitted any appointment requests yet.</p>
            <a href="<?php echo e(route('student.schedule-appointment')); ?>" class="btn btn-lg fw-semibold" style="background: linear-gradient(135deg, #A594F9 0%, #c4b5fd 100%); color: white; border: none;">
                <i class="bi bi-calendar-plus"></i> Schedule Your First Appointment
            </a>
        </div>
    <?php else: ?>
        <!-- Appointments List -->
        <div class="row g-4">
            <?php $__currentLoopData = $appointments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $appointment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="col-12">
                    <div class="card border-0 shadow-sm rounded-4 overflow-hidden appointment-list-card" style="min-height: 280px;">
                        <div class="row g-0 h-100">
                            <!-- Left Side: Appointment Details -->
                            <div class="col-lg-8">
                                <div class="card-body p-4">
                                    <!-- Title & Meta Info -->
                                    <div class="d-flex justify-content-between align-items-start mb-3">
                                        <div>
                                            <h5 class="fw-bold text-dark mb-1">Appointment Request</h5>
                                            <small class="text-muted">Request ID: <?php echo e($appointment->id); ?></small>
                                        </div>
                                        <span class="badge rounded-pill" style="background: #f3f4f6; color: #6b7280; font-size: 0.85rem;">
                                            Submitted: <?php echo e($appointment->created_at->format('M d')); ?>

                                        </span>
                                    </div>

                                    <hr class="my-3" style="border-color: #e5e7eb;">

                                    <!-- Appointment Details Grid -->
                                    <div class="row g-3 mb-4">
                                        <!-- Urgency -->
                                        <div class="col-md-3">
                                            <label class="form-label fw-semibold text-dark small mb-1">Urgency</label>
                                            <div>
                                                <?php if($appointment->urgency_level === 'High'): ?>
                                                    <span class="badge" style="background:#8f72f5; color:#fff;"><?php echo e($appointment->urgency_level); ?></span>
                                                <?php elseif($appointment->urgency_level === 'Medium'): ?>
                                                    <span class="badge bg-warning text-dark"><?php echo e($appointment->urgency_level); ?></span>
                                                <?php else: ?>
                                                    <span class="badge bg-info"><?php echo e($appointment->urgency_level); ?></span>
                                                <?php endif; ?>
                                            </div>
                                        </div>

                                        <!-- Submitted -->
                                        <div class="col-md-6">
                                            <label class="form-label fw-semibold text-dark small mb-1">Submitted</label>
                                            <p class="mb-0 text-muted" style="font-size: 0.95rem;">
                                                <?php echo e($appointment->created_at->format('M d, Y \a\t h:i A')); ?>

                                            </p>
                                            <?php if($appointment->updated_at && $appointment->updated_at->gt($appointment->created_at)): ?>
                                                <p class="mb-0 small text-muted mt-1"><i class="bi bi-clock-history me-1"></i>Recent update: <?php echo e($appointment->updated_at->format('M d, Y \a\t h:i A')); ?></p>
                                            <?php endif; ?>
                                        </div>
                                    </div>

                                    <!-- Description -->
                                    <div class="mb-3">
                                        <label class="form-label fw-semibold text-dark small">Your Concern</label>
                                        <p class="mb-0 text-muted" style="font-size: 0.9rem; line-height: 1.5;"><?php echo e($appointment->description); ?></p>
                                    </div>
                                </div>
                            </div>

                            <!-- Right Side: Status & Admin Notes (Sidebar) -->
                            <div class="col-lg-4 border-start" style="background: linear-gradient(135deg, #f8f7ff 0%, #faf8ff 100%);">
                                <div class="card-body p-4 h-100 d-flex flex-column">
                                    <!-- Status Header -->
                                    <div class="mb-4">
                                        <label class="form-label fw-semibold text-dark small">Status</label>
                                        <div>
                                            <?php if($appointment->status === 'pending'): ?>
                                                <span class="badge bg-warning text-dark rounded-pill" style="font-size: 0.95rem; padding: 0.5rem 1rem;">
                                                    <i class="bi bi-clock"></i> Pending Review
                                                </span>
                                                <p class="small text-muted mt-2">Waiting for GAD Office review</p>
                                            <?php elseif($appointment->status === 'completed'): ?>
                                                <span class="badge rounded-pill" style="background:#d8cdfc; color:#5b4b9b; font-size: 0.95rem; padding: 0.5rem 1rem;">
                                                    <i class="bi bi-check-circle"></i> Completed
                                                </span>
                                                <p class="small text-muted mt-2">Your concern has been reviewed by the GAD Office.</p>
                                            <?php elseif($appointment->status === 'approved'): ?>
                                                <span class="badge bg-info rounded-pill" style="font-size: 0.95rem; padding: 0.5rem 1rem;">
                                                    <i class="bi bi-info-circle"></i> Approved
                                                </span>
                                                <p class="small text-muted mt-2">Your appointment has been approved!</p>
                                            <?php else: ?>
                                                <span class="badge rounded-pill" style="background:#ede9fe; color:#7658df; font-size: 0.95rem; padding: 0.5rem 1rem;">
                                                    <i class="bi bi-x-circle"></i> Rejected
                                                </span>
                                                <p class="small text-muted mt-2">Your appointment request has been declined</p>
                                            <?php endif; ?>
                                        </div>
                                    </div>

                                    <!-- Admin Notes -->
                                    <div class="flex-grow-1">
                                        <label class="form-label fw-semibold text-dark small">
                                            <i class="bi bi-chat-dots"></i> GAD Office Notes
                                        </label>
                                        <?php if($appointment->admin_notes): ?>
                                            <div class="alert alert-light border rounded-3 p-3" style="border-color: #A594F9; background: white; font-size: 0.9rem; line-height: 1.5;">
                                                <?php echo e($appointment->admin_notes); ?>

                                            </div>
                                        <?php else: ?>
                                            <p class="text-muted small" style="font-style: italic;">No notes from GAD Office yet</p>
                                        <?php endif; ?>
                                    </div>

                                    <div class="mt-auto pt-3">
                                    <?php if($appointment->status === 'pending'): ?>
                                        <button type="button" class="btn btn-sm btn-outline-primary w-100 rounded-3 fw-semibold mb-2" data-bs-toggle="modal" data-bs-target="#editAppointment<?php echo e($appointment->id); ?>">
                                            <i class="bi bi-pencil"></i> Edit Date and Time
                                        </button>
                                        <form method="POST" action="<?php echo e(route('student.appointments.cancel', $appointment->id)); ?>" onsubmit="return confirm('Cancel this appointment?');">
                                            <?php echo csrf_field(); ?>
                                            <button type="submit" class="btn btn-sm btn-outline-secondary w-100 rounded-3 fw-semibold">Cancel Appointment</button>
                                        </form>
                                    <?php elseif(in_array($appointment->status, ['rejected', 'cancelled'])): ?>
                                        <div class="mt-auto pt-3">
                                            <a href="<?php echo e(route('student.schedule-appointment')); ?>" class="btn btn-sm btn-outline-secondary w-100 rounded-3 fw-semibold">
                                                <i class="bi bi-plus-circle"></i> Schedule Another
                                            </a>
                                        </div>
                                    <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <?php if($appointment->status === 'pending'): ?>
                    <div class="modal fade" id="editAppointment<?php echo e($appointment->id); ?>" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header"><h5 class="modal-title">Edit Appointment</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                                <form method="POST" action="<?php echo e(route('student.appointments.update', $appointment->id)); ?>">
                                    <?php echo csrf_field(); ?>
                                    <?php echo method_field('PUT'); ?>
                                    <div class="modal-body">
                                        <label class="form-label">Date</label>
                                        <input type="date" name="scheduled_date" class="form-control mb-3" value="<?php echo e(optional($appointment->scheduled_date)->format('Y-m-d')); ?>" min="<?php echo e(now()->toDateString()); ?>" required>
                                        <label class="form-label">Time</label>
                                        <input type="time" name="scheduled_time" class="form-control" value="<?php echo e(optional($appointment->scheduled_time)->format('H:i')); ?>" required>
                                    </div>
                                    <div class="modal-footer"><button type="submit" class="btn btn-primary">Save Changes</button></div>
                                </form>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    <?php endif; ?>
</div>

<style>
    .appointment-list-card {
        transition: all 0.3s ease;
    }

    .appointment-list-card:hover {
        box-shadow: 0 0.5rem 2rem rgba(165, 148, 249, 0.15) !important;
    }

    @media (max-width: 991.98px) {
        .appointment-list-card {
            margin-bottom: 1rem;
        }

        .appointment-list-card .col-lg-8,
        .appointment-list-card .col-lg-4 {
            border-start: none !important;
            border-top: 1px solid #e5e7eb !important;
        }
    }
</style>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\sinag\resources\views/student/appointments.blade.php ENDPATH**/ ?>