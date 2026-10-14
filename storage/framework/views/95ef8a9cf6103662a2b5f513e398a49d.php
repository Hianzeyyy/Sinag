

<?php $__env->startSection('content'); ?>
<div class="container schedule-appointment-page mt-5 mb-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <!-- Header -->
            <div class="mb-5">
               <h1 class="h3 fw-bold mb-2" style="color: #A594F9;">
                   <i class="bi bi-calendar-check"></i> Schedule an Appointment
                </h1>
               <p class="text-muted">Request an appointment with the GAD Office. Please fill out the form below with accurate information.</p>
            </div>

            <?php if(session('success')): ?>
                <div class="alert alert-success alert-dismissible fade show rounded-4" role="status">
                    <i class="bi bi-check-circle-fill me-2"></i><?php echo e(session('success')); ?>

                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <!-- Form Card -->
            <div class="card border-0 shadow-lg rounded-4 overflow-hidden">
                <div class="card-header bg-gradient" style="background: linear-gradient(135deg, #A594F9 0%, #c4b5fd 100%); padding: 2rem;">
                    
                </div>

                <div class="card-body p-4">
                    <form method="POST" action="<?php echo e(route('student.schedule-appointment.store')); ?>">
                        <?php echo csrf_field(); ?>

                        <!-- Full Name -->
                        <div class="mb-4">
                          <label for="full_name" class="form-label fw-semibold" style="color: #A594F9;">
                           <i class="bi bi-person-fill"></i> Full Name
                        </label>
                            <input 
                                type="text" 
                                class="form-control form-control-lg <?php $__errorArgs = ['full_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                id="full_name" 
                                name="full_name"
                                value="<?php echo e(old('full_name', Auth::user()->name)); ?>"
                                required>
                            <?php $__errorArgs = ['full_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <div class="invalid-feedback d-block"><?php echo e($message); ?></div>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>

                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <label for="scheduled_date" class="form-label fw-semibold mb-0" style="color: #A594F9;">Preferred Date</label>
                                    <a href="<?php echo e(route('student.gad-schedule')); ?>" target="_blank" class="small text-decoration-none fw-bold" style="color: #8b5cf6;">
                                        <i class="bi bi-calendar3 me-1"></i>View Office Hours
                                    </a>
                                </div>
                                <input type="date" class="form-control form-control-lg <?php $__errorArgs = ['scheduled_date'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="scheduled_date" name="scheduled_date" value="<?php echo e(old('scheduled_date')); ?>" min="<?php echo e(now()->toDateString()); ?>" required>
                                <?php $__errorArgs = ['scheduled_date'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback d-block"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                            <div class="col-md-6">
                                <label for="scheduled_time" class="form-label fw-semibold" style="color: #A594F9;">Preferred Time</label>
                                <input type="time" class="form-control form-control-lg <?php $__errorArgs = ['scheduled_time'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="scheduled_time" name="scheduled_time" value="<?php echo e(old('scheduled_time')); ?>" required>
                                <?php $__errorArgs = ['scheduled_time'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback d-block"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                        </div>

                        <fieldset class="mb-4">
                            <legend class="form-label fw-semibold mb-2" style="color: #A594F9;">
                                <i class="bi bi-camera-video"></i> Appointment Format
                            </legend>
                            <p class="small text-muted mb-3">Choose how you would like to meet with the GAD Office.</p>
                            <div class="row g-3">
                                <?php $__currentLoopData = ['Face-to-Face' => ['bi-people-fill', 'Meet at the GAD Office'], 'Online' => ['bi-camera-video-fill', 'Meet through an online session']]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $format => [$icon, $description]): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <div class="col-md-6">
                                        <input class="btn-check" type="radio" name="format" id="format_<?php echo e(strtolower(str_replace('-', '_', $format))); ?>" value="<?php echo e($format); ?>" autocomplete="off" <?php echo e(old('format') === $format ? 'checked' : ''); ?> required>
                                        <label class="appointment-format-option" for="format_<?php echo e(strtolower(str_replace('-', '_', $format))); ?>">
                                            <i class="bi <?php echo e($icon); ?> fs-4"></i>
                                            <span><strong><?php echo e($format); ?></strong><small><?php echo e($description); ?></small></span>
                                        </label>
                                    </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>
                            <?php $__errorArgs = ['format'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <div class="invalid-feedback d-block"><?php echo e($message); ?></div>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </fieldset>

                        <!-- Student/Employee ID -->
                        <div class="mb-4">
                          <label for="student_id" class="form-label fw-semibold" style="color: #A594F9;">
                          <i class="bi bi-card-text"></i> Student/Employee ID
                           </label>
                            <input 
                                type="text" 
                                class="form-control form-control-lg <?php $__errorArgs = ['student_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                id="student_id" 
                                name="student_id"
                                value="<?php echo e(old('student_id')); ?>"
                                placeholder="e.g., 2024-1001"
                                required>
                            <?php $__errorArgs = ['student_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <div class="invalid-feedback d-block"><?php echo e($message); ?></div>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>

                        <!-- Urgency Level -->
                        <div class="mb-4">
                            <label class="form-label fw-semibold" style="color: #A594F9;">
                             <i class="bi bi-exclamation-triangle"></i> Urgency Level
                              </label>
                            <div class="d-flex gap-3">
                                <div class="form-check">
                                    <input 
                                        class="form-check-input" 
                                        type="radio" 
                                        name="urgency_level" 
                                        id="urgency_low" 
                                        value="Low"
                                        <?php echo e(old('urgency_level') === 'Low' ? 'checked' : ''); ?>>
                                    <label class="form-check-label" for="urgency_low">
                                        Low
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input 
                                        class="form-check-input" 
                                        type="radio" 
                                        name="urgency_level" 
                                        id="urgency_medium" 
                                        value="Medium"
                                        <?php echo e(old('urgency_level') === 'Medium' || !old('urgency_level') ? 'checked' : ''); ?>>
                                    <label class="form-check-label" for="urgency_medium">
                                        Medium
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input 
                                        class="form-check-input" 
                                        type="radio" 
                                        name="urgency_level" 
                                        id="urgency_high" 
                                        value="High"
                                        <?php echo e(old('urgency_level') === 'High' ? 'checked' : ''); ?>>
                                    <label class="form-check-label" for="urgency_high">
                                        High
                                    </label>
                                </div>
                            </div>
                            <?php $__errorArgs = ['urgency_level'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <div class="invalid-feedback d-block"><?php echo e($message); ?></div>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>

                        <!-- Brief Description -->
                        <div class="mb-4">
                           <label for="description" class="form-label fw-semibold" style="color: #A594F9;">
                            <i class="bi bi-pencil-square"></i> Brief Description of Concern
                            </label>
                            <textarea 
                                class="form-control <?php $__errorArgs = ['description'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                id="description" 
                                name="description"
                                rows="5"
                                placeholder="Please provide a brief overview of what you'd like to discuss..."
                                required><?php echo e(old('description')); ?></textarea>
                            <?php $__errorArgs = ['description'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <div class="invalid-feedback d-block"><?php echo e($message); ?></div>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>

                        <!-- Submit Button -->
                        <div class="d-grid gap-2 mt-5 mb-3">
                            <button 
                                type="submit" 
                                class="btn fw-semibold schedule-submit-btn"
                                style="background: linear-gradient(135deg, #A594F9 0%, #c4b5fd 100%); color: white; border: none; transition: all 0.3s ease;">
                                <i class="bi bi-check-circle"></i> Submit Appointment Request
                            </button>
                        </div>

                        <!-- Back Button -->
                        <div class="d-grid gap-2">
                            <a href="<?php echo e(route('student.dashboard')); ?>" class="btn btn-outline-secondary fw-semibold">
                                <i class="bi bi-arrow-left"></i> Back to Dashboard
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Info Box -->
            <div class="alert alert-info border-0 rounded-4 mt-4" style="background: #e7f3ff; color: #0c5395;">
                <i class="bi bi-info-circle"></i>
                <strong>What Happens Next?</strong>
                <p class="mb-0 mt-2">
                    Your appointment request will be reviewed by the GAD Office. You will receive a notification once your request has been approved or if additional information is needed.
                </p>
            </div>
        </div>
    </div>
</div>

<style>
    .schedule-submit-btn,
    .schedule-appointment-page .btn-outline-secondary {
        min-height: 2.75rem;
        padding: 0.7rem 1rem;
        border-radius: 0.8rem;
    }

    .schedule-appointment-page {
        padding-bottom: max(1.5rem, env(safe-area-inset-bottom));
    }

    .form-control:focus,
    .form-control.is-invalid:focus {
        border-color: #A594F9;
        box-shadow: 0 0 0 0.2rem rgba(165, 148, 249, 0.25);
    }

    .form-check-input:checked {
        background-color: #A594F9;
        border-color: #A594F9;
    }

    .form-check-input:focus {
        border-color: #A594F9;
        box-shadow: 0 0 0 0.25rem rgba(165, 148, 249, 0.25);
    }

    .appointment-format-option {
        display: flex;
        min-height: 5.25rem;
        align-items: center;
        gap: .9rem;
        padding: 1rem;
        border: 1px solid #ddd6fe;
        border-radius: .9rem;
        color: #5b4b9b;
        background: #fff;
        cursor: pointer;
        transition: transform .2s ease, border-color .2s ease, box-shadow .2s ease, background .2s ease;
    }

    .appointment-format-option:hover {
        transform: translateY(-2px);
        border-color: #A594F9;
        box-shadow: 0 8px 18px rgba(165, 148, 249, .18);
    }

    .appointment-format-option span,
    .appointment-format-option small {
        display: block;
    }

    .appointment-format-option small {
        margin-top: .15rem;
        color: #64748b;
    }

    .btn-check:checked + .appointment-format-option {
        border-color: #8f72f5;
        background: #f5f3ff;
        box-shadow: 0 0 0 2px rgba(143, 114, 245, .2);
    }

    .btn-check:focus-visible + .appointment-format-option {
        outline: 3px solid rgba(143, 114, 245, .35);
        outline-offset: 2px;
    }

    @media (max-width: 575.98px) {
        .schedule-appointment-page {
            margin-top: 1rem !important;
            padding-inline: 0.75rem;
        }

        .schedule-appointment-page > .row > .col-lg-8 > .mb-5 {
            margin-bottom: 1.5rem !important;
        }

        .schedule-appointment-page .card-body {
            padding: 1rem !important;
        }

        .schedule-appointment-page .form-control-lg {
            min-height: 2.75rem;
            font-size: 1rem;
        }

        .schedule-appointment-page .btn {
            font-size: 0.95rem;
        }
    }
</style>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\sinag\resources\views/student/schedule_appointment.blade.php ENDPATH**/ ?>