

<?php $__env->startSection('content'); ?>
<div class="container mt-5 mb-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <!-- Header -->
            <div class="mb-5">
               <h1 class="h3 fw-bold mb-2" style="color: #A594F9;">
                   <i class="bi bi-calendar-check"></i> Schedule an Appointment
                </h1>
               <p class="text-muted">Request an appointment with the GAD Office. Please fill out the form below with accurate information.</p>
            </div>

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
                                <label for="scheduled_date" class="form-label fw-semibold" style="color: #A594F9;">Preferred Date</label>
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
                                class="btn btn-lg fw-semibold"
                                style="background: linear-gradient(135deg, #A594F9 0%, #c4b5fd 100%); color: white; border: none; transition: all 0.3s ease;">
                                <i class="bi bi-check-circle"></i> Submit Appointment Request
                            </button>
                        </div>

                        <!-- Back Button -->
                        <div class="d-grid gap-2">
                            <a href="<?php echo e(route('student.dashboard')); ?>" class="btn btn-outline-secondary btn-lg fw-semibold">
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
</style>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\ANGELINE AMBROSIO\sinag\resources\views/student/schedule_appointment.blade.php ENDPATH**/ ?>