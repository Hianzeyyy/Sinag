

<?php $__env->startSection('content'); ?>
<?php
    $isEmployee = ($user->account_type ?? $user->role) === 'employee';
    $personal = $user->personal_information ?? [];
    $family = $user->family_background ?? [];
    $student = $user->student_information ?? [];
    $employeeEducation = $user->employee_education ?? [];
    $civilService = $user->civil_service_eligibility ?? [];
    $workExperience = $user->work_experience ?? [];
    $voluntaryWork = $user->voluntary_work ?? [];
    $gadTraining = $user->gad_training ?? [];
    $otherInformation = $user->other_information ?? [];
    $employeeEducationJson = data_get($employeeEducation, 'text', is_array($employeeEducation) ? json_encode($employeeEducation, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) : $employeeEducation);
    $civilServiceJson = data_get($civilService, 'text', is_array($civilService) ? json_encode($civilService, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) : $civilService);
    $workExperienceJson = data_get($workExperience, 'text', is_array($workExperience) ? json_encode($workExperience, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) : $workExperience);
    $voluntaryWorkJson = data_get($voluntaryWork, 'text', is_array($voluntaryWork) ? json_encode($voluntaryWork, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) : $voluntaryWork);
    $gadTrainingJson = data_get($gadTraining, 'text', is_array($gadTraining) ? json_encode($gadTraining, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) : $gadTraining);
    $personalDisabilities = implode(', ', $personal['disabilities'] ?? []);
    $studentAchievements = implode(', ', $student['achievements'] ?? []);
    $studentFinancingSources = implode(', ', $student['financing_sources'] ?? []);
?>

<style>
    .pd-page {
        max-width: 1240px;
        margin: 0 auto;
        padding: 1.5rem;
    }

    .pd-hero {
        background: linear-gradient(135deg, #A594F9 0%, #8f72f5 55%, #facc15 140%);
        border-radius: 1.6rem;
        padding: 1.5rem;
        color: #fff;
        box-shadow: 0 18px 38px rgba(165, 148, 249, 0.18);
        position: relative;
        overflow: hidden;
    }

    .pd-hero:before {
        content: '';
        position: absolute;
        inset: 0;
        background: radial-gradient(circle at top right, rgba(255,255,255,0.18), transparent 35%);
        pointer-events: none;
    }

    .pd-title {
        font-size: clamp(2rem, 4vw, 3rem);
        font-weight: 900;
        margin: 0;
        letter-spacing: -0.04em;
    }

    .pd-subtitle {
        color: rgba(255,255,255,0.9);
        font-weight: 600;
        margin-top: 0.4rem;
    }

    .pd-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        border-radius: 999px;
        padding: 0.4rem 0.8rem;
        background: rgba(255,255,255,0.16);
        color: #fff;
        font-size: 0.78rem;
        font-weight: 800;
        letter-spacing: 0.04em;
        text-transform: uppercase;
    }

    .pd-card {
        background: #fff;
        border: 1px solid rgba(165,148,249,0.14);
        border-radius: 1.2rem;
        box-shadow: 0 14px 34px rgba(165,148,249,0.1);
        overflow: hidden;
    }

    .pd-section-title {
        font-weight: 900;
        color: #111827;
        margin-bottom: 0.9rem;
    }

    .pd-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 1rem;
    }

    @media (max-width: 767.98px) {
        .pd-grid {
            grid-template-columns: 1fr;
        }
    }

    .pd-item {
        background: linear-gradient(180deg, rgba(165,148,249,0.06), rgba(250,247,255,0.9));
        border: 1px solid rgba(165,148,249,0.12);
        border-radius: 1rem;
        padding: 1rem;
    }

    .pd-label {
        font-size: 0.72rem;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        color: #6b7280;
        font-weight: 800;
        margin-bottom: 0.35rem;
    }

    .pd-value {
        font-size: 1rem;
        font-weight: 800;
        color: #111827;
        word-break: break-word;
    }

    .pd-note {
        color: #6b7280;
        font-size: 0.92rem;
    }

    .pd-table-wrap {
        overflow-x: auto;
        border-radius: 1rem;
        border: 1px solid rgba(165,148,249,0.12);
    }

    .pd-table {
        width: 100%;
        border-collapse: collapse;
        min-width: 860px;
    }

    .pd-table th,
    .pd-table td {
        border-bottom: 1px solid rgba(165,148,249,0.12);
        border-right: 1px solid rgba(165,148,249,0.10);
        padding: 0.75rem 0.85rem;
        vertical-align: top;
        color: #111827;
        background: #fff;
    }

    .pd-table th {
        background: #f8f7ff;
        font-size: 0.72rem;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        font-weight: 900;
        color: #6b7280;
    }

    .pd-table tr:last-child td {
        border-bottom: 0;
    }

    .pd-pill-list {
        display: flex;
        flex-wrap: wrap;
        gap: 0.5rem;
    }

    .pd-pill {
        border-radius: 999px;
        background: rgba(165,148,249,0.10);
        border: 1px solid rgba(165,148,249,0.12);
        padding: 0.35rem 0.75rem;
        font-weight: 700;
        color: #4c1d95;
        font-size: 0.9rem;
    }

    .pd-form-card {
        background: linear-gradient(180deg, #ffffff 0%, #faf7ff 100%);
        border: 1px solid rgba(165,148,249,0.16);
    }

    .pd-form-section {
        font-size: 1rem;
        font-weight: 900;
        color: #4c1d95;
        margin-top: 1rem;
        margin-bottom: 0.75rem;
    }

    .pd-form-note {
        font-size: 0.88rem;
        color: #6b7280;
        margin-bottom: 0;
    }

    .pd-input,
    .pd-textarea,
    .pd-select {
        border-radius: 0.95rem;
        border: 1px solid rgba(165,148,249,0.18);
        box-shadow: none !important;
    }

    .pd-textarea {
        min-height: 108px;
    }
</style>

<div class="pd-page">
    <?php if($errors->any()): ?>
        <div class="alert alert-danger border-0 shadow-sm rounded-4 mb-4">
            <div class="fw-bold mb-2">Please fix the following:</div>
            <ul class="mb-0 ps-3">
                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <li><?php echo e($error); ?></li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
        </div>
    <?php endif; ?>

    <?php if(session('status')): ?>
        <div class="alert alert-success border-0 shadow-sm rounded-4 mb-4"><?php echo e(session('status')); ?></div>
    <?php endif; ?>

    <div class="pd-hero mb-4">
        <div class="position-relative">
            <div class="d-flex flex-column flex-md-row align-items-start justify-content-between gap-3">
                <div>
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <div class="pd-badge">
                            <i class="bi bi-file-earmark-person"></i>
                            Personal Data
                        </div>
                    </div>
                    <h1 class="pd-title"><?php echo e($user->name); ?></h1>
                    <div class="pd-subtitle">This page shows everything saved from your registration form.</div>
                </div>
                <div class="text-md-end">
                    <div class="pd-badge mb-2"><?php echo e(ucfirst($user->account_type ?? 'student')); ?></div>
                    <div class="pd-badge"><?php echo e(ucfirst($user->role ?? 'student')); ?></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Editable form removed to avoid duplication with summary view -->

    <div class="pd-card p-4 mb-4">
        <div class="d-flex justify-content-between align-items-start mb-2">
            <div class="pd-section-title mb-0">Account Basics</div>
            <div class="d-flex flex-wrap gap-2 justify-content-end">
                <a href="<?php echo e(url()->previous()); ?>" class="btn btn-sm btn-outline-secondary rounded-pill px-3">
                    <i class="bi bi-arrow-left me-1"></i> Return
                </a>
                <?php if(!($adminViewer ?? false)): ?>
                <a href="<?php echo e(route('personal-data.edit')); ?>" class="btn btn-sm rounded-pill px-3" style="border-color:#8f72f5;color:#6d55d9;">
                    <i class="bi bi-pencil-square me-1"></i> Edit Personal Data
                </a>
                <?php endif; ?>
            </div>
        </div>
        <div class="pd-grid">
            <div class="pd-item"><div class="pd-label">Full Name</div><div class="pd-value"><?php echo e($user->name ?? 'N/A'); ?></div></div>
            <div class="pd-item"><div class="pd-label">Last Name</div><div class="pd-value"><?php echo e($user->last_name ?? 'N/A'); ?></div></div>
            <div class="pd-item"><div class="pd-label">First Name</div><div class="pd-value"><?php echo e($user->first_name ?? 'N/A'); ?></div></div>
            <div class="pd-item"><div class="pd-label">Middle Name</div><div class="pd-value"><?php echo e($user->middle_name ?? 'N/A'); ?></div></div>
            <div class="pd-item"><div class="pd-label">Email</div><div class="pd-value"><?php echo e($user->email ?? 'N/A'); ?></div></div>
            <div class="pd-item"><div class="pd-label">Phone Number</div><div class="pd-value"><?php echo e($user->phone_number ?? 'N/A'); ?></div></div>
            <div class="pd-item"><div class="pd-label">Age</div><div class="pd-value"><?php echo e($user->age ?? 'N/A'); ?></div></div>
            <div class="pd-item"><div class="pd-label">Gender</div><div class="pd-value"><?php echo e($user->gender ?? 'N/A'); ?></div></div>
            <div class="pd-item"><div class="pd-label">Department / Course</div><div class="pd-value"><?php echo e($user->department ?? 'N/A'); ?></div></div>
            <div class="pd-item"><div class="pd-label">Account Type</div><div class="pd-value"><?php echo e(ucfirst($user->account_type ?? 'student')); ?></div></div>
            <div class="pd-item"><div class="pd-label">Role</div><div class="pd-value"><?php echo e(ucfirst($user->role ?? 'student')); ?></div></div>
            <div class="pd-item"><div class="pd-label">Cloak Alias</div><div class="pd-value"><?php echo e($user->cloak_alias ?? 'N/A'); ?></div></div>
            <div class="pd-item"><div class="pd-label">Account Status</div><div class="pd-value"><?php echo e(ucfirst($user->account_status ?? 'active')); ?></div></div>
        </div>
    </div>

    <div class="pd-card p-4 mb-4">
        <div class="pd-section-title">Personal Information</div>
        <div class="pd-grid">
            <div class="pd-item"><div class="pd-label">Date of Birth</div><div class="pd-value"><?php echo e($personal['date_of_birth'] ?? 'N/A'); ?></div></div>
            <div class="pd-item"><div class="pd-label">Place of Birth</div><div class="pd-value"><?php echo e($personal['place_of_birth'] ?? 'N/A'); ?></div></div>
            <div class="pd-item"><div class="pd-label">Sex</div><div class="pd-value"><?php echo e($personal['sex'] ?? 'N/A'); ?></div></div>
            <div class="pd-item"><div class="pd-label">Gender Identity</div><div class="pd-value"><?php echo e($personal['gender_identity'] ?? 'N/A'); ?></div></div>
            <div class="pd-item"><div class="pd-label">Citizenship</div><div class="pd-value"><?php echo e($personal['citizenship'] ?? 'N/A'); ?></div></div>
            <div class="pd-item"><div class="pd-label">Blood Type</div><div class="pd-value"><?php echo e($personal['blood_type'] ?? 'N/A'); ?></div></div>
            <div class="pd-item"><div class="pd-label">Height</div><div class="pd-value"><?php echo e($personal['height'] ?? 'N/A'); ?></div></div>
            <div class="pd-item"><div class="pd-label">Weight</div><div class="pd-value"><?php echo e($personal['weight'] ?? 'N/A'); ?></div></div>
            <div class="pd-item"><div class="pd-label">Landline Number</div><div class="pd-value"><?php echo e($personal['landline_number'] ?? 'N/A'); ?></div></div>
            <div class="pd-item"><div class="pd-label">Civil Status</div><div class="pd-value"><?php echo e($personal['civil_status'] ?? 'N/A'); ?></div></div>
            <div class="pd-item" style="grid-column: 1 / -1;"><div class="pd-label">Current Address</div><div class="pd-value"><?php echo e($personal['current_address'] ?? 'N/A'); ?></div></div>
            <div class="pd-item" style="grid-column: 1 / -1;"><div class="pd-label">Home Address</div><div class="pd-value"><?php echo e($personal['home_address'] ?? 'N/A'); ?></div></div>
            <div class="pd-item"><div class="pd-label">Religion</div><div class="pd-value"><?php echo e($personal['religion'] ?? 'N/A'); ?></div></div>
            <div class="pd-item"><div class="pd-label">Religion Other</div><div class="pd-value"><?php echo e($personal['religion_other'] ?? 'N/A'); ?></div></div>
            <div class="pd-item" style="grid-column: 1 / -1;">
                <div class="pd-label">Disabilities</div>
                <div class="pd-pill-list">
                    <?php $__empty_1 = true; $__currentLoopData = ($personal['disabilities'] ?? []); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <span class="pd-pill"><?php echo e($item); ?></span>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <div class="pd-value">N/A</div>
                    <?php endif; ?>
                </div>
            </div>
            <div class="pd-item" style="grid-column: 1 / -1;"><div class="pd-label">Disability Other</div><div class="pd-value"><?php echo e($personal['disability_other'] ?? 'N/A'); ?></div></div>
        </div>
    </div>

    <div class="pd-card p-4 mb-4">
        <div class="pd-section-title">Family Background</div>
        <div class="pd-grid">
            <div class="pd-item"><div class="pd-label">Spouse Name</div><div class="pd-value"><?php echo e($family['spouse_name'] ?? 'N/A'); ?></div></div>
            <div class="pd-item"><div class="pd-label">Spouse Occupation</div><div class="pd-value"><?php echo e($family['spouse_occupation'] ?? 'N/A'); ?></div></div>
            <div class="pd-item"><div class="pd-label">Father's Name</div><div class="pd-value"><?php echo e($family['father_name'] ?? 'N/A'); ?></div></div>
            <div class="pd-item"><div class="pd-label">Father Occupation</div><div class="pd-value"><?php echo e($family['father_occupation'] ?? 'N/A'); ?></div></div>
            <div class="pd-item"><div class="pd-label">Father Age</div><div class="pd-value"><?php echo e($family['father_age'] ?? 'N/A'); ?></div></div>
            <div class="pd-item"><div class="pd-label">Mother's Name</div><div class="pd-value"><?php echo e($family['mother_name'] ?? 'N/A'); ?></div></div>
            <div class="pd-item"><div class="pd-label">Mother Occupation</div><div class="pd-value"><?php echo e($family['mother_occupation'] ?? 'N/A'); ?></div></div>
            <div class="pd-item"><div class="pd-label">Mother Age</div><div class="pd-value"><?php echo e($family['mother_age'] ?? 'N/A'); ?></div></div>
            <div class="pd-item"><div class="pd-label">Children Total</div><div class="pd-value"><?php echo e($family['children_total'] ?? 'N/A'); ?></div></div>
            <div class="pd-item"><div class="pd-label">Children Boys</div><div class="pd-value"><?php echo e($family['children_boys'] ?? 'N/A'); ?></div></div>
            <div class="pd-item"><div class="pd-label">Children Girls</div><div class="pd-value"><?php echo e($family['children_girls'] ?? 'N/A'); ?></div></div>
            <div class="pd-item"><div class="pd-label">Brothers Count</div><div class="pd-value"><?php echo e($family['brothers_count'] ?? 'N/A'); ?></div></div>
            <div class="pd-item"><div class="pd-label">Sisters Count</div><div class="pd-value"><?php echo e($family['sisters_count'] ?? 'N/A'); ?></div></div>
            <div class="pd-item"><div class="pd-label">Monthly Income</div><div class="pd-value"><?php echo e($family['monthly_income'] ?? 'N/A'); ?></div></div>
            <div class="pd-item"><div class="pd-label">Family Type</div><div class="pd-value"><?php echo e($family['family_type'] ?? 'N/A'); ?></div></div>
            <div class="pd-item"><div class="pd-label">Family Type Other</div><div class="pd-value"><?php echo e($family['family_type_other'] ?? 'N/A'); ?></div></div>
            <div class="pd-item"><div class="pd-label">Home Ownership Type</div><div class="pd-value"><?php echo e($family['home_ownership_type'] ?? 'N/A'); ?></div></div>
            <div class="pd-item"><div class="pd-label">Home Ownership Other</div><div class="pd-value"><?php echo e($family['home_ownership_other'] ?? 'N/A'); ?></div></div>
            <div class="pd-item"><div class="pd-label">Residential Home Type</div><div class="pd-value"><?php echo e($family['residential_home_type'] ?? 'N/A'); ?></div></div>
            <div class="pd-item"><div class="pd-label">Residential Home Other</div><div class="pd-value"><?php echo e($family['residential_home_other'] ?? 'N/A'); ?></div></div>
        </div>
    </div>

    <div class="pd-card p-4 mb-4">
        <div class="pd-section-title">Student Information</div>
        <div class="pd-grid">
            <div class="pd-item"><div class="pd-label">Year Level</div><div class="pd-value"><?php echo e($student['year_level'] ?? 'N/A'); ?></div></div>
            <div class="pd-item"><div class="pd-label">School Type</div><div class="pd-value"><?php echo e($student['school_type'] ?? 'N/A'); ?></div></div>
            <div class="pd-item"><div class="pd-label">Last School Attended</div><div class="pd-value"><?php echo e($student['last_school_attended'] ?? 'N/A'); ?></div></div>
            <div class="pd-item"><div class="pd-label">Achievement Other Rank</div><div class="pd-value"><?php echo e($student['achievement_other_rank'] ?? 'N/A'); ?></div></div>
            <div class="pd-item" style="grid-column: 1 / -1;">
                <div class="pd-label">Achievements Earned</div>
                <div class="pd-pill-list">
                    <?php $__empty_1 = true; $__currentLoopData = ($student['achievements'] ?? []); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <span class="pd-pill"><?php echo e($item); ?></span>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <div class="pd-value">N/A</div>
                    <?php endif; ?>
                </div>
            </div>
            <div class="pd-item" style="grid-column: 1 / -1;">
                <div class="pd-label">Who is financing your schooling?</div>
                <div class="pd-pill-list">
                    <?php $__empty_1 = true; $__currentLoopData = ($student['financing_sources'] ?? []); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <span class="pd-pill"><?php echo e($item); ?></span>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <div class="pd-value">N/A</div>
                    <?php endif; ?>
                </div>
            </div>
            <div class="pd-item"><div class="pd-label">Working Student Work</div><div class="pd-value"><?php echo e($student['financing_other_work'] ?? 'N/A'); ?></div></div>
            <div class="pd-item"><div class="pd-label">GAD Training Attended</div><div class="pd-value"><?php echo e(isset($student['gad_training_attended']) && $student['gad_training_attended'] !== null ? ($student['gad_training_attended'] ? 'Yes' : 'No') : 'N/A'); ?></div></div>
            <div class="pd-item" style="grid-column: 1 / -1;"><div class="pd-label">GAD Training Details</div><div class="pd-value"><?php echo e($student['gad_training_details'] ?? 'N/A'); ?></div></div>
        </div>
    </div>

    <?php if(($user->account_type ?? 'student') === 'employee'): ?>
    <div class="pd-card p-4 mb-4">
        <div class="pd-section-title">Employee Education</div>
        <div class="pd-table-wrap">
            <table class="pd-table">
                <thead>
                    <tr>
                        <th>Level</th>
                        <th>Name of School</th>
                        <th>Degree / Course</th>
                        <th>From</th>
                        <th>To</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__currentLoopData = ['Elementary','High School','Vocational/Trade Course','College','Graduate Studies']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $level): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td><strong><?php echo e($level); ?></strong></td>
                            <td><?php echo e($employeeEducation[$index]['school'] ?? 'N/A'); ?></td>
                            <td><?php echo e($employeeEducation[$index]['degree'] ?? 'N/A'); ?></td>
                            <td><?php echo e($employeeEducation[$index]['from'] ?? 'N/A'); ?></td>
                            <td><?php echo e($employeeEducation[$index]['to'] ?? 'N/A'); ?></td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="pd-card p-4 mb-4">
        <div class="pd-section-title">Civil Service Eligibility</div>
        <div class="pd-table-wrap mb-3">
            <table class="pd-table">
                <thead>
                    <tr>
                        <th>Career Service / RA 1080</th>
                        <th>Rating</th>
                        <th>Date of Examination</th>
                        <th>Place of Examination</th>
                        <th>License Number</th>
                        <th>Date of Release</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__currentLoopData = ($civilService ?? []); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td><?php echo e($row['career'] ?? 'N/A'); ?></td>
                            <td><?php echo e($row['rating'] ?? 'N/A'); ?></td>
                            <td><?php echo e($row['exam_date'] ?? 'N/A'); ?></td>
                            <td><?php echo e($row['exam_place'] ?? 'N/A'); ?></td>
                            <td><?php echo e($row['license_num'] ?? 'N/A'); ?></td>
                            <td><?php echo e($row['release_date'] ?? 'N/A'); ?></td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
        </div>
        <div class="pd-note">If nothing was entered, this table will stay empty on your profile.</div>
    </div>

    <div class="pd-card p-4 mb-4">
        <div class="pd-section-title">Work Experience</div>
        <div class="pd-table-wrap mb-3">
            <table class="pd-table">
                <thead>
                    <tr>
                        <th>Inclusive Date</th>
                        <th>Position / Title</th>
                        <th>Department / Agency / Office / Company</th>
                        <th>Monthly Salary</th>
                        <th>Salary Grade / Step</th>
                        <th>Status of Appointment</th>
                        <th>Government Service</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__currentLoopData = ($workExperience ?? []); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td><?php echo e(trim(($row['from'] ?? 'N/A') . ' - ' . ($row['to'] ?? 'N/A'))); ?></td>
                            <td><?php echo e($row['position'] ?? 'N/A'); ?></td>
                            <td><?php echo e($row['department'] ?? 'N/A'); ?></td>
                            <td><?php echo e($row['salary'] ?? 'N/A'); ?></td>
                            <td><?php echo e($row['grade_step'] ?? 'N/A'); ?></td>
                            <td><?php echo e($row['appointment_status'] ?? 'N/A'); ?></td>
                            <td><?php echo e($row['govt_service'] ?? 'N/A'); ?></td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="pd-card p-4 mb-4">
        <div class="pd-section-title">Voluntary Work</div>
        <div class="pd-table-wrap mb-3">
            <table class="pd-table">
                <thead>
                    <tr>
                        <th>Name of Organization</th>
                        <th>Address of Organization</th>
                        <th>Inclusive Date</th>
                        <th>Number of Hours</th>
                        <th>Position / Nature of Work</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__currentLoopData = ($voluntaryWork ?? []); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td><?php echo e($row['org_name'] ?? 'N/A'); ?></td>
                            <td><?php echo e($row['org_address'] ?? 'N/A'); ?></td>
                            <td><?php echo e(trim(($row['from'] ?? 'N/A') . ' - ' . ($row['to'] ?? 'N/A'))); ?></td>
                            <td><?php echo e($row['hours'] ?? 'N/A'); ?></td>
                            <td><?php echo e($row['position'] ?? 'N/A'); ?></td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="pd-card p-4 mb-4">
        <div class="pd-section-title">GAD Related Training / Seminar</div>
        <div class="pd-table-wrap mb-3">
            <table class="pd-table">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Inclusive Date</th>
                        <th>Number of Hours</th>
                        <th>Conducted / Sponsored By</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__currentLoopData = ($gadTraining ?? []); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td><?php echo e($row['title'] ?? 'N/A'); ?></td>
                            <td><?php echo e(trim(($row['from'] ?? 'N/A') . ' - ' . ($row['to'] ?? 'N/A'))); ?></td>
                            <td><?php echo e($row['hours'] ?? 'N/A'); ?></td>
                            <td><?php echo e($row['conducted_by'] ?? 'N/A'); ?></td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="pd-card p-4 mb-4">
        <div class="pd-section-title">Other Information</div>
        <div class="pd-grid">
            <div class="pd-item" style="grid-column: 1 / -1;">
                <div class="pd-label">Special Skills / Hobbies</div>
                <div class="pd-value"><?php echo e($otherInformation['emp_skills_hobbies'] ?? 'N/A'); ?></div>
            </div>
            <div class="pd-item" style="grid-column: 1 / -1;">
                <div class="pd-label">Non-Academic Distinctions / Recognition</div>
                <div class="pd-value"><?php echo e($otherInformation['emp_distinctions'] ?? 'N/A'); ?></div>
            </div>
            <div class="pd-item" style="grid-column: 1 / -1;">
                <div class="pd-label">Membership in Organization</div>
                <div class="pd-value"><?php echo e($otherInformation['emp_membership'] ?? 'N/A'); ?></div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <div class="pd-card p-4 mb-4">
        <div class="pd-section-title">Quick Actions</div>
        <div class="d-flex flex-wrap gap-2">
            <?php if($adminViewer ?? false): ?>
                <a href="<?php echo e(route('admin.users')); ?>" class="btn btn-primary rounded-pill px-4 fw-bold" style="background: linear-gradient(135deg, #A594F9 0%, #8f72f5 100%); border: 0;">Back to User Management</a>
            <?php else: ?>
                <a href="<?php echo e(route('profile.show')); ?>" class="btn btn-primary rounded-pill px-4 fw-bold" style="background: linear-gradient(135deg, #A594F9 0%, #8f72f5 100%); border: 0;">Open Profile</a>
                <a href="<?php echo e(route('home')); ?>" class="btn btn-light rounded-pill px-4 fw-bold">Back to Home</a>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\ANGELINE AMBROSIO\sinag\resources\views\profile\personal-data.blade.php ENDPATH**/ ?>