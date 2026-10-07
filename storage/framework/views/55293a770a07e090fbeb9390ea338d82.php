<style>
    .pd-edit-page {
        --pd-violet: #8f72f5;
        --pd-violet-deep: #5b4b9b;
        --pd-violet-soft: #f5f3ff;
        --pd-ink: #172033;
        max-width: 1180px;
        margin: 0 auto;
        padding: clamp(0.25rem, 1.5vw, 1.25rem);
    }

    .pd-page {
        width: 100%;
    }

    .pd-form-card {
        width: 100%;
    }

    .pd-form-note {
        color: #6b7280;
        font-size: 0.9rem;
        line-height: 1.55;
        margin: 0;
    }

    .pd-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 1rem;
    }

    .pd-grid > div {
        min-width: 0;
    }

    .pd-form-section {
        color: var(--pd-violet-deep);
        font-weight: 900;
    }

    .pd-edit-hero {
        position: relative;
        overflow: hidden;
        border-radius: 1.35rem;
        padding: clamp(1.35rem, 3vw, 2.25rem);
        color: #fff;
        background: linear-gradient(125deg, #312e81 0%, #6d55d9 58%, #a594f9 100%);
        box-shadow: 0 20px 42px rgba(76, 29, 149, 0.2);
    }

    .pd-edit-hero::after {
        content: '';
        position: absolute;
        width: 15rem;
        height: 15rem;
        right: -5rem;
        top: -7rem;
        border-radius: 50%;
        background: rgba(250, 204, 21, 0.2);
    }

    .pd-edit-kicker {
        display: inline-flex;
        align-items: center;
        gap: 0.45rem;
        padding: 0.4rem 0.75rem;
        border: 1px solid rgba(255,255,255,0.24);
        border-radius: 999px;
        color: #fff;
        font-size: 0.72rem;
        font-weight: 900;
        letter-spacing: 0.1em;
        text-transform: uppercase;
    }

    .pd-edit-title {
        position: relative;
        z-index: 1;
        margin: 0.75rem 0 0.35rem;
        font-size: clamp(1.65rem, 4vw, 2.55rem);
        font-weight: 900;
        letter-spacing: -0.035em;
    }

    .pd-edit-subtitle {
        position: relative;
        z-index: 1;
        max-width: 46rem;
        margin: 0;
        color: rgba(255,255,255,0.86);
        line-height: 1.65;
    }

    .pd-edit-status {
        position: relative;
        z-index: 1;
        display: inline-flex;
        align-items: center;
        gap: 0.45rem;
        margin-top: 1rem;
        padding: 0.55rem 0.75rem;
        border-radius: 0.7rem;
        background: rgba(255,255,255,0.13);
        font-size: 0.8rem;
        font-weight: 800;
    }

    .pd-edit-card {
        margin-top: 1.1rem;
        padding: clamp(1rem, 2.5vw, 1.6rem);
        border: 1px solid rgba(165,148,249,0.18);
        border-radius: 1.25rem;
        background: linear-gradient(180deg, #fff 0%, #fbfaff 100%);
        box-shadow: 0 16px 38px rgba(76, 29, 149, 0.09);
    }

    .pd-edit-section {
        position: relative;
        margin-top: 1.2rem;
        padding: 1.1rem;
        border: 1px solid rgba(165,148,249,0.16);
        border-radius: 1rem;
        background: #fff;
    }

    .pd-edit-section:first-of-type { margin-top: 0; }

    .pd-edit-section-title {
        display: flex;
        align-items: center;
        gap: 0.65rem;
        margin-bottom: 1rem;
        color: var(--pd-violet-deep);
        font-size: 1rem;
        font-weight: 900;
    }

    .pd-edit-section-title::before {
        content: '';
        width: 0.35rem;
        height: 1.35rem;
        border-radius: 999px;
        background: linear-gradient(180deg, #a594f9, #facc15);
    }

    .pd-edit-page .pd-label {
        display: block;
        color: #4b5563;
        font-size: 0.74rem;
        font-weight: 900;
        letter-spacing: 0.05em;
        margin-bottom: 0.4rem;
    }

    .pd-edit-page .pd-input,
    .pd-edit-page .pd-select,
    .pd-edit-page .pd-textarea {
        min-height: 2.8rem;
        border: 1px solid #ddd6fe;
        border-radius: 0.75rem;
        background: #fcfbff;
        color: var(--pd-ink);
        box-shadow: inset 0 1px 2px rgba(76,29,149,0.03);
        transition: border-color .18s ease, box-shadow .18s ease, background-color .18s ease;
    }

    .pd-edit-page .pd-input:focus,
    .pd-edit-page .pd-select:focus,
    .pd-edit-page .pd-textarea:focus {
        border-color: var(--pd-violet);
        background: #fff;
        box-shadow: 0 0 0 0.2rem rgba(143,114,245,0.15);
    }

    .pd-edit-page .pd-textarea { min-height: 7rem; }

    .pd-edit-actions {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 0.75rem;
        flex-wrap: wrap;
        margin-top: 1.35rem;
        padding-top: 1.1rem;
        border-top: 1px solid #ede9fe;
    }

    .pd-edit-actions .btn { min-height: 2.8rem; }

    @media (max-width: 575.98px) {
        .pd-grid { grid-template-columns: 1fr; }
        .pd-edit-page { padding-inline: 0; }
        .pd-edit-card { padding: 0.75rem; border-radius: 1rem; }
        .pd-edit-section { padding: 0.85rem; }
        .pd-edit-actions { align-items: stretch; flex-direction: column-reverse; }
        .pd-edit-actions .btn { width: 100%; }
    }
</style>

<?php if(session('status')): ?>
    <div class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 3000;">
        <div id="personalDataToast" class="toast border-0 shadow-lg" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="toast-header" style="background:#f5f3ff; color:#5b4b9b;">
                <i class="bi bi-check-circle-fill me-2"></i>
                <strong class="me-auto">SINAG Profile</strong>
                <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
            <div class="toast-body"><?php echo e(session('status')); ?></div>
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const toastElement = document.getElementById('personalDataToast');
            if (toastElement && window.bootstrap) {
                window.bootstrap.Toast.getOrCreateInstance(toastElement, { delay: 4500 }).show();
            }
        });
    </script>
<?php endif; ?>

<?php if($errors->any()): ?>
    <div class="alert alert-warning border-0 shadow-sm rounded-4 mb-4" role="alert">
        <div class="fw-bold mb-1"><i class="bi bi-exclamation-circle me-1"></i> Please check the highlighted information.</div>
        <ul class="mb-0 ps-3">
            <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <li><?php echo e($error); ?></li>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </ul>
    </div>
<?php endif; ?>

<div class="pd-edit-page">
    <div class="pd-edit-hero">
        <div class="pd-edit-kicker"><i class="bi bi-person-vcard"></i> SINAG Personal Profile</div>
        <h1 class="pd-edit-title">Complete your personal information</h1>
        <p class="pd-edit-subtitle">Keep your details accurate so SINAG can provide a more complete and responsive support experience.</p>
        <div class="pd-edit-status"><i class="bi bi-shield-check"></i> Required fields are marked with an asterisk</div>
    </div>

    <div class="pd-edit-card">
    <div class="pd-page">
    <div class="pd-card pd-form-card p-4 mb-4">
        <div class="d-flex flex-column flex-md-row align-items-md-end justify-content-between gap-2 mb-3">
            <div>
                <div class="pd-section-title mb-1">Edit Personal Data</div>
                <p class="pd-form-note">Update your registration information here. Leave a field blank only if you want to clear it.</p>
            </div>
            <div class="pd-badge bg-white text-dark border border-light-subtle">
                <i class="bi bi-pencil-square"></i>
                Editable Profile
            </div>
        </div>

        <form action="<?php echo e(route('profile.update')); ?>" method="POST">
            <?php echo csrf_field(); ?>

            <div class="pd-form-section pd-edit-section" data-section="account">
                <div class="pd-form-section pd-edit-section-title">Account Basics</div>
                <div class="pd-grid">
                    <div>
                        <label class="pd-label">Full Name</label>
                        <input type="text" name="name" value="<?php echo e(old('name', $user->name ?? '')); ?>" class="form-control pd-input" required>
                    </div>
                    <div>
                        <label class="pd-label">Email</label>
                        <input type="email" name="email" value="<?php echo e(old('email', $user->email ?? '')); ?>" class="form-control pd-input" required>
                    </div>
                    <div>
                        <label class="pd-label">Last Name <span class="text-danger">*</span></label>
                        <input type="text" name="last_name" value="<?php echo e(old('last_name', $user->last_name ?? '')); ?>" class="form-control pd-input" required>
                    </div>
                    <div>
                        <label class="pd-label">First Name <span class="text-danger">*</span></label>
                        <input type="text" name="first_name" value="<?php echo e(old('first_name', $user->first_name ?? '')); ?>" class="form-control pd-input" required>
                    </div>
                    <div>
                        <label class="pd-label">Middle Name</label>
                        <input type="text" name="middle_name" value="<?php echo e(old('middle_name', $user->middle_name ?? '')); ?>" class="form-control pd-input">
                    </div>
                    <div>
                        <label class="pd-label">Phone Number <span class="text-danger">*</span></label>
                        <input type="text" name="phone_number" value="<?php echo e(old('phone_number', $user->phone_number ?? '')); ?>" class="form-control pd-input" required>
                    </div>
                    <div>
                        <label class="pd-label">Age <span class="text-danger">*</span></label>
                        <input type="number" name="age" min="1" max="120" value="<?php echo e(old('age', $user->age ?? '')); ?>" class="form-control pd-input" required>
                    </div>
                    <div>
                        <label class="pd-label">Gender <span class="text-danger">*</span></label>
                        <select name="gender" class="form-select pd-select" required>
                            <option value="">Select gender</option>
                            <?php $__currentLoopData = ['Male', 'Female', 'Prefer not to say']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $option): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($option); ?>" <?php echo e(old('gender', $user->gender ?? '') === $option ? 'selected' : ''); ?>><?php echo e($option); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                    <div>
                        <label class="pd-label">Department / Course <span class="text-danger">*</span></label>
                        <input type="text" name="department" value="<?php echo e(old('department', $user->department ?? '')); ?>" class="form-control pd-input" required>
                    </div>
                    <div>
                        <label class="pd-label">Cloak Alias</label>
                        <input type="text" name="cloak_alias" value="<?php echo e(old('cloak_alias', $user->cloak_alias ?? '')); ?>" class="form-control pd-input">
                    </div>
                </div>
            </div>

            <div class="pd-form-section pd-edit-section" data-section="personal">
                <div class="pd-form-section pd-edit-section-title">Personal Information</div>
                <div class="pd-grid">
                    <div><label class="pd-label">Date of Birth <span class="text-danger">*</span></label><input type="date" name="personal_information[date_of_birth]" value="<?php echo e(old('personal_information.date_of_birth', $personal['date_of_birth'] ?? '')); ?>" class="form-control pd-input" required></div>
                    <div><label class="pd-label">Place of Birth <span class="text-danger">*</span></label><input type="text" name="personal_information[place_of_birth]" value="<?php echo e(old('personal_information.place_of_birth', $personal['place_of_birth'] ?? '')); ?>" class="form-control pd-input" required></div>
                    <div><label class="pd-label">Sex <span class="text-danger">*</span></label><select name="personal_information[sex]" class="form-select pd-select" required><option value="">Select sex</option><?php $__currentLoopData = ['Male','Female','Intersex','Prefer not to say']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $option): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($option); ?>" <?php echo e(old('personal_information.sex', $personal['sex'] ?? '') === $option ? 'selected' : ''); ?>><?php echo e($option); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></select></div>
                    <div><label class="pd-label">Gender Identity <span class="text-danger">*</span></label><input type="text" name="personal_information[gender_identity]" value="<?php echo e(old('personal_information.gender_identity', $personal['gender_identity'] ?? '')); ?>" class="form-control pd-input" required></div>
                    <div><label class="pd-label">Citizenship <span class="text-danger">*</span></label><input type="text" name="personal_information[citizenship]" value="<?php echo e(old('personal_information.citizenship', $personal['citizenship'] ?? '')); ?>" class="form-control pd-input" required></div>
                    <div><label class="pd-label">Blood Type <span class="text-danger">*</span></label><select name="personal_information[blood_type]" class="form-select pd-select" required><option value="">Select blood type</option><?php $__currentLoopData = ['A+','A-','B+','B-','AB+','AB-','O+','O-','Unknown']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $option): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($option); ?>" <?php echo e(old('personal_information.blood_type', $personal['blood_type'] ?? '') === $option ? 'selected' : ''); ?>><?php echo e($option); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></select></div>
                    <div><label class="pd-label">Height <span class="text-danger">*</span></label><input type="text" name="personal_information[height]" value="<?php echo e(old('personal_information.height', $personal['height'] ?? '')); ?>" class="form-control pd-input" required></div>
                    <div><label class="pd-label">Weight <span class="text-danger">*</span></label><input type="text" name="personal_information[weight]" value="<?php echo e(old('personal_information.weight', $personal['weight'] ?? '')); ?>" class="form-control pd-input" required></div>
                    <div><label class="pd-label">Landline Number <span class="text-danger">*</span></label><input type="text" name="personal_information[landline_number]" value="<?php echo e(old('personal_information.landline_number', $personal['landline_number'] ?? '')); ?>" class="form-control pd-input" required></div>
                    <div><label class="pd-label">Civil Status <span class="text-danger">*</span></label><select name="personal_information[civil_status]" class="form-select pd-select" required><option value="">Select status</option><?php $__currentLoopData = ['Single','Married','Separated','Widowed','Prefer not to say']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $option): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($option); ?>" <?php echo e(old('personal_information.civil_status', $personal['civil_status'] ?? '') === $option ? 'selected' : ''); ?>><?php echo e($option); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></select></div>
                    <div style="grid-column: 1 / -1;"><label class="pd-label">Current Address <span class="text-danger">*</span></label><textarea name="personal_information[current_address]" class="form-control pd-textarea" required><?php echo e(old('personal_information.current_address', $personal['current_address'] ?? '')); ?></textarea></div>
                    <div style="grid-column: 1 / -1;"><label class="pd-label">Home Address <span class="text-danger">*</span></label><textarea name="personal_information[home_address]" class="form-control pd-textarea" required><?php echo e(old('personal_information.home_address', $personal['home_address'] ?? '')); ?></textarea></div>
                    <div><label class="pd-label">Religion <span class="text-danger">*</span></label><input type="text" name="personal_information[religion]" value="<?php echo e(old('personal_information.religion', $personal['religion'] ?? '')); ?>" class="form-control pd-input" required></div>
                    <div><label class="pd-label">Religion Other</label><input type="text" name="personal_information[religion_other]" value="<?php echo e(old('personal_information.religion_other', $personal['religion_other'] ?? '')); ?>" class="form-control pd-input"></div>
                    <div><label class="pd-label">Disabilities</label><select name="personal_information[disabilities_text]" class="form-select pd-select"><option value="None" <?php echo e(old('personal_information.disabilities_text', $personalDisabilities) === 'None' ? 'selected' : ''); ?>>None</option><?php $__currentLoopData = ['Visual impairment','Hearing impairment','Mobility impairment','Learning disability','Psychosocial disability','Other']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $option): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($option); ?>" <?php echo e(old('personal_information.disabilities_text', $personalDisabilities) === $option ? 'selected' : ''); ?>><?php echo e($option); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></select></div>
                    <div style="grid-column: 1 / -1;"><label class="pd-label">Disability Other</label><input type="text" name="personal_information[disability_other]" value="<?php echo e(old('personal_information.disability_other', $personal['disability_other'] ?? '')); ?>" class="form-control pd-input"></div>
                </div>
            </div>

            <div class="pd-form-section pd-edit-section" data-section="family">
                <div class="pd-form-section pd-edit-section-title">Family Background</div>
                <div class="pd-grid">
                    <div><label class="pd-label">Spouse Name</label><input type="text" name="family_background[spouse_name]" value="<?php echo e(old('family_background.spouse_name', $family['spouse_name'] ?? '')); ?>" class="form-control pd-input"></div>
                    <div><label class="pd-label">Spouse Occupation</label><input type="text" name="family_background[spouse_occupation]" value="<?php echo e(old('family_background.spouse_occupation', $family['spouse_occupation'] ?? '')); ?>" class="form-control pd-input"></div>
                    <div><label class="pd-label">Father's Name</label><input type="text" name="family_background[father_name]" value="<?php echo e(old('family_background.father_name', $family['father_name'] ?? '')); ?>" class="form-control pd-input"></div>
                    <div><label class="pd-label">Father Occupation</label><input type="text" name="family_background[father_occupation]" value="<?php echo e(old('family_background.father_occupation', $family['father_occupation'] ?? '')); ?>" class="form-control pd-input"></div>
                    <div><label class="pd-label">Father Age</label><input type="text" name="family_background[father_age]" value="<?php echo e(old('family_background.father_age', $family['father_age'] ?? '')); ?>" class="form-control pd-input"></div>
                    <div><label class="pd-label">Mother's Name</label><input type="text" name="family_background[mother_name]" value="<?php echo e(old('family_background.mother_name', $family['mother_name'] ?? '')); ?>" class="form-control pd-input"></div>
                    <div><label class="pd-label">Mother Occupation</label><input type="text" name="family_background[mother_occupation]" value="<?php echo e(old('family_background.mother_occupation', $family['mother_occupation'] ?? '')); ?>" class="form-control pd-input"></div>
                    <div><label class="pd-label">Mother Age</label><input type="text" name="family_background[mother_age]" value="<?php echo e(old('family_background.mother_age', $family['mother_age'] ?? '')); ?>" class="form-control pd-input"></div>
                    <div><label class="pd-label">Children Total</label><input type="text" name="family_background[children_total]" value="<?php echo e(old('family_background.children_total', $family['children_total'] ?? '')); ?>" class="form-control pd-input"></div>
                    <div><label class="pd-label">Children Boys</label><input type="text" name="family_background[children_boys]" value="<?php echo e(old('family_background.children_boys', $family['children_boys'] ?? '')); ?>" class="form-control pd-input"></div>
                    <div><label class="pd-label">Children Girls</label><input type="text" name="family_background[children_girls]" value="<?php echo e(old('family_background.children_girls', $family['children_girls'] ?? '')); ?>" class="form-control pd-input"></div>
                    <div><label class="pd-label">Brothers Count</label><input type="text" name="family_background[brothers_count]" value="<?php echo e(old('family_background.brothers_count', $family['brothers_count'] ?? '')); ?>" class="form-control pd-input"></div>
                    <div><label class="pd-label">Sisters Count</label><input type="text" name="family_background[sisters_count]" value="<?php echo e(old('family_background.sisters_count', $family['sisters_count'] ?? '')); ?>" class="form-control pd-input"></div>
                    <div><label class="pd-label">Monthly Income</label><input type="text" name="family_background[monthly_income]" value="<?php echo e(old('family_background.monthly_income', $family['monthly_income'] ?? '')); ?>" class="form-control pd-input"></div>
                    <div><label class="pd-label">Family Type</label><select name="family_background[family_type]" class="form-select pd-select"><option value="">Select</option><?php $__currentLoopData = ['Nuclear','Extended','Single-parent','Blended','Other','Prefer not to say']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $option): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($option); ?>" <?php echo e(old('family_background.family_type', $family['family_type'] ?? '') === $option ? 'selected' : ''); ?>><?php echo e($option); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></select></div>
                    <div><label class="pd-label">Family Type Other</label><input type="text" name="family_background[family_type_other]" value="<?php echo e(old('family_background.family_type_other', $family['family_type_other'] ?? '')); ?>" class="form-control pd-input"></div>
                    <div><label class="pd-label">Home Ownership Type</label><select name="family_background[home_ownership_type]" class="form-select pd-select"><option value="">Select</option><?php $__currentLoopData = ['Owned','Rented','Living with relatives','Government housing','Other','Prefer not to say']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $option): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($option); ?>" <?php echo e(old('family_background.home_ownership_type', $family['home_ownership_type'] ?? '') === $option ? 'selected' : ''); ?>><?php echo e($option); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></select></div>
                    <div><label class="pd-label">Home Ownership Other</label><input type="text" name="family_background[home_ownership_other]" value="<?php echo e(old('family_background.home_ownership_other', $family['home_ownership_other'] ?? '')); ?>" class="form-control pd-input"></div>
                    <div><label class="pd-label">Residential Home Type</label><select name="family_background[residential_home_type]" class="form-select pd-select"><option value="">Select</option><?php $__currentLoopData = ['Concrete','Semi-concrete','Wooden','Apartment/boarding house','Other','Prefer not to say']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $option): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($option); ?>" <?php echo e(old('family_background.residential_home_type', $family['residential_home_type'] ?? '') === $option ? 'selected' : ''); ?>><?php echo e($option); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></select></div>
                    <div><label class="pd-label">Residential Home Other</label><input type="text" name="family_background[residential_home_other]" value="<?php echo e(old('family_background.residential_home_other', $family['residential_home_other'] ?? '')); ?>" class="form-control pd-input"></div>
                </div>
            </div>

            <div class="pd-form-section pd-edit-section" data-section="student">
                <div class="pd-form-section pd-edit-section-title">Student Information</div>
                <div class="pd-grid">
                    <div><label class="pd-label">Year Level <?php if (! ($isEmployee)): ?><span class="text-danger">*</span><?php endif; ?></label><input type="text" name="student_information[year_level]" value="<?php echo e(old('student_information.year_level', $student['year_level'] ?? '')); ?>" class="form-control pd-input" <?php if (! ($isEmployee)): ?> required <?php endif; ?>></div>
                    <div><label class="pd-label">School Type <?php if (! ($isEmployee)): ?><span class="text-danger">*</span><?php endif; ?></label><select name="student_information[school_type]" class="form-select pd-select" <?php if (! ($isEmployee)): ?> required <?php endif; ?>><option value="">Select school type</option><?php $__currentLoopData = ['Public','Private','State University','Other']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $option): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($option); ?>" <?php echo e(old('student_information.school_type', $student['school_type'] ?? '') === $option ? 'selected' : ''); ?>><?php echo e($option); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></select></div>
                    <div><label class="pd-label">Last School Attended <?php if (! ($isEmployee)): ?><span class="text-danger">*</span><?php endif; ?></label><input type="text" name="student_information[last_school_attended]" value="<?php echo e(old('student_information.last_school_attended', $student['last_school_attended'] ?? '')); ?>" class="form-control pd-input" <?php if (! ($isEmployee)): ?> required <?php endif; ?>></div>
                    <div><label class="pd-label">Achievement Other Rank</label><input type="text" name="student_information[achievement_other_rank]" value="<?php echo e(old('student_information.achievement_other_rank', $student['achievement_other_rank'] ?? '')); ?>" class="form-control pd-input"></div>
                    <div style="grid-column: 1 / -1;"><label class="pd-label">Achievements</label><textarea name="student_information[achievements_text]" class="form-control pd-textarea" placeholder="Separate items with commas"><?php echo e(old('student_information.achievements_text', $studentAchievements)); ?></textarea></div>
                    <div style="grid-column: 1 / -1;"><label class="pd-label">Who is financing your schooling? <?php if (! ($isEmployee)): ?><span class="text-danger">*</span><?php endif; ?></label><textarea name="student_information[financing_sources_text]" class="form-control pd-textarea" placeholder="Separate items with commas" <?php if (! ($isEmployee)): ?> required <?php endif; ?>><?php echo e(old('student_information.financing_sources_text', $studentFinancingSources)); ?></textarea></div>
                    <div><label class="pd-label">Working Student Work</label><input type="text" name="student_information[financing_other_work]" value="<?php echo e(old('student_information.financing_other_work', $student['financing_other_work'] ?? '')); ?>" class="form-control pd-input"></div>
                    <div>
                        <label class="pd-label">GAD Training Attended</label>
                        <select name="student_information[gad_training_attended]" class="form-select pd-select">
                            <option value="">Select</option>
                            <option value="1" <?php echo e(old('student_information.gad_training_attended', isset($student['gad_training_attended']) && $student['gad_training_attended'] ? '1' : '') === '1' ? 'selected' : ''); ?>>Yes</option>
                            <option value="0" <?php echo e(old('student_information.gad_training_attended', isset($student['gad_training_attended']) && $student['gad_training_attended'] === false ? '0' : '') === '0' ? 'selected' : ''); ?>>No</option>
                        </select>
                    </div>
                    <div style="grid-column: 1 / -1;"><label class="pd-label">GAD Training Details</label><textarea name="student_information[gad_training_details]" class="form-control pd-textarea"><?php echo e(old('student_information.gad_training_details', $student['gad_training_details'] ?? '')); ?></textarea></div>
                </div>
            </div>

            <div class="pd-form-section pd-edit-section" data-section="other">
                <div class="pd-form-section pd-edit-section-title">Other Information</div>
                <div class="pd-grid">
                    <div style="grid-column: 1 / -1;"><label class="pd-label">Special Skills / Hobbies</label><textarea name="other_information[emp_skills_hobbies]" class="form-control pd-textarea"><?php echo e(old('other_information.emp_skills_hobbies', $otherInformation['emp_skills_hobbies'] ?? '')); ?></textarea></div>
                    <div style="grid-column: 1 / -1;"><label class="pd-label">Non-Academic Distinctions / Recognition</label><textarea name="other_information[emp_distinctions]" class="form-control pd-textarea"><?php echo e(old('other_information.emp_distinctions', $otherInformation['emp_distinctions'] ?? '')); ?></textarea></div>
                    <div style="grid-column: 1 / -1;"><label class="pd-label">Membership in Organization</label><textarea name="other_information[emp_membership]" class="form-control pd-textarea"><?php echo e(old('other_information.emp_membership', $otherInformation['emp_membership'] ?? '')); ?></textarea></div>
                </div>
            </div>

            <?php if(($user->account_type ?? 'student') === 'employee'): ?>
                <div class="pd-form-section pd-edit-section" data-section="employee">
                    <div class="pd-form-section pd-edit-section-title">Employee Records</div>
                    <p class="pd-form-note mb-3">Enter your information in plain text. You may use multiple lines for separate entries.</p>
                    <div class="pd-grid">
                        <div style="grid-column: 1 / -1;"><label class="pd-label">Employee Education</label><textarea name="employee_education_json" class="form-control pd-textarea" rows="8"><?php echo e(old('employee_education_json', $employeeEducationJson)); ?></textarea></div>
                        <div style="grid-column: 1 / -1;"><label class="pd-label">Civil Service Eligibility</label><textarea name="civil_service_eligibility_json" class="form-control pd-textarea" rows="8"><?php echo e(old('civil_service_eligibility_json', $civilServiceJson)); ?></textarea></div>
                        <div style="grid-column: 1 / -1;"><label class="pd-label">Work Experience</label><textarea name="work_experience_json" class="form-control pd-textarea" rows="8"><?php echo e(old('work_experience_json', $workExperienceJson)); ?></textarea></div>
                        <div style="grid-column: 1 / -1;"><label class="pd-label">Voluntary Work</label><textarea name="voluntary_work_json" class="form-control pd-textarea" rows="8"><?php echo e(old('voluntary_work_json', $voluntaryWorkJson)); ?></textarea></div>
                        <div style="grid-column: 1 / -1;"><label class="pd-label">GAD Training</label><textarea name="gad_training_json" class="form-control pd-textarea" rows="8"><?php echo e(old('gad_training_json', $gadTrainingJson)); ?></textarea></div>
                    </div>
                </div>
            <?php endif; ?>

            <div class="pd-edit-actions">
                <a href="<?php echo e(route('personal-data.show')); ?>" class="btn rounded-pill px-4 fw-bold" style="background:#f5f3ff; color:#5b4b9b; border:1px solid #d8cdfc;"><i class="bi bi-arrow-left me-1"></i>Return to Personal Data</a>
                <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold" style="background: linear-gradient(135deg, #A594F9 0%, #8f72f5 100%); border: 0;">
                    <i class="bi bi-save2 me-1"></i> Save Personal Data
                </button>
            </div>

        </form>
    </div>
    </div>
</div>
<?php /**PATH C:\Users\ANGELINE AMBROSIO\sinag\resources\views\profile\_edit-form.blade.php ENDPATH**/ ?>