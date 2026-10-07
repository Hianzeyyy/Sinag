<?php $__env->startSection('content'); ?>
<style>
    nav, .navbar { display: none !important; }
    body { overflow-x: hidden; overflow-y: auto; }

    .register-shell {
        min-height: 100vh;
    }

    .register-card {
        max-height: none;
    }

    .register-form-panel {
        max-height: none;
        overflow-y: visible;
        overscroll-behavior: contain;
        scrollbar-width: thin;
        background: linear-gradient(145deg, #8f72f5 0%, #a594f9 58%, #c4b5fd 100%);
        color: #fff;
    }

    .register-form-panel::-webkit-scrollbar {
        width: 10px;
    }

    .register-form-panel::-webkit-scrollbar-thumb {
        background: rgba(148, 163, 184, 0.55);
        border-radius: 999px;
    }

    .register-card input.form-control,
    .register-card select.form-select,
    .register-card input[type="file"] {
        background: #fff !important;
        color: #111827 !important;
        border: 1px solid rgba(148, 163, 184, 0.32) !important;
    }

    .register-card .input-group-text {
        background: #fff !important;
        color: #111827 !important;
        border: 1px solid rgba(148, 163, 184, 0.32) !important;
    }

    .register-card .password-toggle {
        border: 1px solid rgba(148, 163, 184, 0.32);
        border-left: 0;
        background: #fff;
        color: #6d55d9;
        min-width: 2.85rem;
    }

    .register-card .password-toggle:hover,
    .register-card .password-toggle:focus {
        background: #f5f3ff;
        color: #5b4b9b;
    }

    .register-card input::-ms-reveal,
    .register-card input::-ms-clear {
        display: none;
    }

    .register-logo-pair { display: flex; align-items: center; justify-content: center; gap: 0.35rem; }
    .register-logo-pair img { width: 76px !important; aspect-ratio: 1; object-fit: contain; }
    .register-logo-pair .register-gad-logo { border-radius: 50%; clip-path: circle(50%); mix-blend-mode: multiply; }

    .register-login-link {
        color: #facc15 !important;
        font-size: 1rem;
        font-weight: 800;
    }

    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(30px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .register-shell {
        background: linear-gradient(145deg, #f6f3ff 0%, #eee9ff 52%, #fff9df 100%) !important;
        position: relative !important;
        min-height: 100vh;
        height: auto !important;
        padding: 1.5rem 0 !important;
    }

    .register-card {
        background: rgba(255,255,255,0.82) !important;
        backdrop-filter: blur(18px);
    }

    .register-card > .row > .col-md-4 {
        background: linear-gradient(145deg, #a594f9 0%, #8f72f5 62%, #facc15 150%) !important;
        border-right: 0 !important;
        min-height: 0 !important;
        height: auto !important;
        align-items: center !important;
        justify-content: center !important;
    }

    .register-card > .row > .col-md-4 img { width: 104px !important; }

    .register-card > .row > .col-md-4 > .flex-grow-1 {
        flex-grow: 0 !important;
    }

    @media (max-width: 767.98px) {
        .register-shell { align-items: flex-start !important; padding: 0.75rem 0 !important; }
        .register-shell > .container { width: 100%; padding-inline: 0.75rem; }
        .register-card { max-height: none; border-radius: 1.25rem !important; }
        .register-card > .row > .col-md-4 { min-height: 220px !important; height: auto !important; padding: 1.25rem !important; justify-content: center; }
        .register-card > .row > .col-md-4 img { width: 76px !important; margin-bottom: 0.75rem !important; }
        .register-card > .row > .col-md-4 h1 { font-size: 2rem !important; }
        .register-card > .row > .col-md-4 p { margin-top: 0.5rem !important; }
        .register-form-panel { max-height: none; overflow: visible; padding: 1.5rem !important; }
    }
</style>

<?php if($errors->any()): ?>
    <div class="position-fixed top-0 start-50 translate-middle-x mt-3 alert alert-warning shadow-lg border-0" style="z-index: 2000; max-width: min(92vw, 620px);">
        <strong>Registration could not be completed.</strong>
        <ul class="mb-0 ps-3">
            <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <li><?php echo e($error); ?></li>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </ul>
    </div>
<?php endif; ?>

<div class="container-fluid min-vh-100 d-flex align-items-center justify-content-center p-0 register-shell" 
    style="background: linear-gradient(135deg, #A594F9 0%, #c4b5fd 56%, #8f72f5 100%); position: fixed; top: 0; left: 0; width: 100vw; height: 100vh;">
    
    <div class="container" style="position: relative; z-index: 10; animation: fadeInUp 0.8s ease-out;">
        <div class="row justify-content-center">
            <div class="col-lg-10 col-xl-9">
                 <div class="card border-0 rounded-4 shadow-lg overflow-hidden register-card" 
                     style="background: rgba(255, 255, 255, 0.04); backdrop-filter: blur(20px); border: 1px solid rgba(255, 255, 255, 0.1) !important;">
                    
                    <div class="row g-0">
                        <!-- LEFT PANEL: CENTERED LOGO AND TEXT -->
                        <div class="col-md-4 d-flex flex-column p-5 text-center" 
                             style="background: rgba(0, 0, 0, 0.2); border-right: 1px solid rgba(255, 255, 255, 0.05); min-height: 80vh;">
                            
                            <!-- I-center ang grupo na ito sa gitna ng rectangle -->
                            <div class="flex-grow-1 d-flex flex-column justify-content-center align-items-center">
                                <div class="register-logo-pair mb-4" aria-label="Pangasinan State University Gender and Development">
                                    <img src="<?php echo e(asset('images/psulogo.png')); ?>" alt="PSU Logo" style="filter: drop-shadow(0 0 20px rgba(165, 148, 249, 0.35));">
                                    <img src="<?php echo e(asset('images/gadlogo.png')); ?>" alt="GAD Logo" class="register-gad-logo" style="filter: drop-shadow(0 0 20px rgba(165, 148, 249, 0.35));">
                                </div>
                                
                                <h1 class="fw-bold mb-0 display-5" style="letter-spacing: 5px; text-shadow: 0 4px 15px rgba(0,0,0,0.5);">
                                    <span style="color: #FFFFFF">SI</span><span style="color: #facc15;">NAG</span>
                                </h1>
                                
                                <p class="text-white-50 small text-uppercase fw-bold mt-3" style="letter-spacing: 3px; font-size: 0.65rem;">
                                    Gender and Development Safe Space and Participatory Suggestion System
                                </p>
                            </div>

                            <!-- Encryption Active sa bottom -->
                            <div class="opacity-50 pt-4">
                                <p class="text-white-50 mb-0" style="font-size: 0.6rem; letter-spacing: 1px;">
                                    <i class="bi bi-shield-shaded me-1"></i> ENCRYPTION ACTIVE
                                </p>
                            </div>
                        </div>

                        <!-- RIGHT PANEL: FORM -->
                        <div class="col-md-8 p-4 p-lg-5 register-form-panel">
                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <div>
                                    <h3 class="text-white fw-bold mb-0">Create Account</h3>
                                    <p class="text-white small mb-0">Join the SINAG network today.</p>
                                </div>
                            </div>

                            <form method="POST" action="<?php echo e(route('register')); ?>" enctype="multipart/form-data" id="registerForm">
                                <?php echo csrf_field(); ?>
                                <div class="row">
                                    <div class="col-md-12 mb-3">
                                        <label class="form-label text-white small fw-bold text-uppercase">Full Name</label>
                                        <input type="text" class="form-control" name="name" value="<?php echo e(old('name')); ?>" required placeholder="Juan Dela Cruz">
                                    </div>
                                    <div class="col-md-12 mb-3">
                                        <label class="form-label text-white small fw-bold text-uppercase">Institutional Email</label>
                                        <input type="email" class="form-control" name="email" value="<?php echo e(old('email')); ?>" required placeholder="username@psu.edu.ph">
                                        <div id="emailHelp" class="form-text text-white-50">Students use an email beginning with a number; employees use one beginning with a letter.</div>
                                    </div>
                                    <div class="col-md-12 mb-3">
                                        <label class="form-label text-white small fw-bold text-uppercase">Account Type</label>
                                        <select class="form-select" name="account_type" id="accountType" required>
                                            <option value="" disabled <?php echo e(old('account_type') ? '' : 'selected'); ?>>Choose account type</option>
                                            <option value="student" <?php echo e(old('account_type') === 'student' ? 'selected' : ''); ?>>Student</option>
                                            <option value="employee" <?php echo e(old('account_type') === 'employee' ? 'selected' : ''); ?>>Employee</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label text-white small fw-bold text-uppercase">Age</label>
                                        <input type="number" class="form-control" name="age" value="<?php echo e(old('age')); ?>" min="1" max="120" required>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label text-white small fw-bold text-uppercase">Gender</label>
                                        <select class="form-select" name="gender" required>
                                            <option value="" disabled <?php echo e(old('gender') ? '' : 'selected'); ?>>Choose gender</option>
                                            <?php $__currentLoopData = ['Male', 'Female', 'Prefer not to say']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $option): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <option value="<?php echo e($option); ?>" <?php echo e(old('gender') === $option ? 'selected' : ''); ?>><?php echo e($option); ?></option>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </select>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label text-white small fw-bold text-uppercase">Department / Course</label>
                                        <select class="form-select" name="department" id="department" required data-old-department="<?php echo e(old('department')); ?>">
                                            <option value="" disabled <?php echo e(old('department') ? '' : 'selected'); ?>>Choose course or department</option>
                                        </select>
                                    </div>
                                    <div class="col-md-12 mb-3 d-none" id="employeeCategoryField">
                                        <label class="form-label text-white small fw-bold text-uppercase">Employee Category</label>
                                        <select class="form-select" name="employee_category" id="employeeCategory">
                                            <option value="" disabled <?php echo e(old('employee_category') ? '' : 'selected'); ?>>Choose employee category</option>
                                            <option value="teaching" <?php echo e(old('employee_category') === 'teaching' ? 'selected' : ''); ?>>Teaching</option>
                                            <option value="non_teaching" <?php echo e(old('employee_category') === 'non_teaching' ? 'selected' : ''); ?>>Non-teaching</option>
                                        </select>
                                    </div>
                                    <div class="col-md-12 mb-3">
                                        <label class="form-label text-white small fw-bold text-uppercase">Profile Picture</label>
                                        <input type="file" class="form-control" name="profile_photo" accept="image/*" required>
                                    </div>
                                    <div class="col-md-12 mb-3">
                                        <label class="form-label text-white small fw-bold text-uppercase">Photo of your Student ID</label>
                                        <input type="file" class="form-control" name="selfie_image" accept="image/*" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label text-white small fw-bold text-uppercase">Password</label>
                                        <div class="input-group">
                                            <input type="password" class="form-control" name="password" id="registerPassword" autocomplete="new-password" required>
                                            <button type="button" class="btn password-toggle" data-password-target="registerPassword" aria-label="Show password"><i class="bi bi-eye"></i></button>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-4">
                                        <label class="form-label text-white small fw-bold text-uppercase">Confirm Password</label>
                                        <div class="input-group">
                                            <input type="password" class="form-control" name="password_confirmation" id="registerPasswordConfirmation" autocomplete="new-password" required>
                                            <button type="button" class="btn password-toggle" data-password-target="registerPasswordConfirmation" aria-label="Show password"><i class="bi bi-eye"></i></button>
                                        </div>
                                    </div>
                                </div>

                                <div class="d-grid gap-3">
                                    <button type="submit" class="btn btn-lg fw-bold text-dark py-3" style="background: #facc15; border-radius: 12px;">
                                        REGISTER ACCOUNT
                                    </button>
                                    <div class="text-center">
                                        <p class="text-white-50 small">Already have an account? <a href="<?php echo e(route('login')); ?>" class="register-login-link">Log In</a></p>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    const registerForm = document.getElementById('registerForm');
    registerForm.addEventListener('submit', function () {
        const email = registerForm.querySelector('[name="email"]').value.trim().toLowerCase();
        const accounts = JSON.parse(localStorage.getItem('sinag.login.accounts') || '[]');
        if (email && email.includes('@')) {
            localStorage.setItem('sinag.login.accounts', JSON.stringify([email].concat(accounts.filter(function (account) { return account !== email; })).slice(0, 10)));
        }
    });

    const accountType = document.getElementById('accountType');
    const department = document.getElementById('department');
    const employeeCategoryField = document.getElementById('employeeCategoryField');
    const employeeCategory = document.getElementById('employeeCategory');
    const oldDepartment = department.dataset.oldDepartment;

    const departmentOptions = {
        student: [
            ['Bachelor Industrial Technology - Major in Electrical Technology', 'Bachelor Industrial Technology - Major in Electrical Technology'],
            ['Bachelor Industrial Technology - Major in Food Management Service', 'Bachelor Industrial Technology - Major in Food Management Service'],
            ['Bachelor Industrial Technology - Major in Mechanical Technology', 'Bachelor Industrial Technology - Major in Mechanical Technology'],
            ['Bachelor of Elementary Education (BEEd)', 'Bachelor of Elementary Education (BEEd)'],
            ['Bachelor of Technology and Livelihood Education (BTLEd)', 'Bachelor of Technology and Livelihood Education (BTLEd)'],
            ['Bachelor Secondary Education (BSEd) - Major in English', 'Bachelor Secondary Education (BSEd) - Major in English'],
            ['Bachelor Secondary Education (BSEd) - Major in Math', 'Bachelor Secondary Education (BSEd) - Major in Math'],
            ['Bachelor Secondary Education (BSEd) - Major in Science', 'Bachelor Secondary Education (BSEd) - Major in Science'],
            ['BS Business Administration (BSBA)', 'BS Business Administration (BSBA)'],
            ['BS Information Technology (BSIT)', 'BS Information Technology (BSIT)']
        ],
        employee: [
            ['College of Technology and Business', 'College of Technology and Business'],
            ['College of Education', 'College of Education']
        ]
    };

    function updateDepartmentOptions() {
        const type = accountType.value;
        department.innerHTML = '<option value="" disabled selected>Choose course or department</option>';
        (departmentOptions[type] || []).forEach(function ([value, label]) {
            const option = new Option(label, value, false, value === oldDepartment);
            department.add(option);
        });
        const isEmployee = type === 'employee';
        employeeCategoryField.classList.toggle('d-none', !isEmployee);
        employeeCategory.required = isEmployee;
        if (!isEmployee) employeeCategory.value = '';
    }

    accountType.addEventListener('change', updateDepartmentOptions);
    updateDepartmentOptions();

    document.querySelectorAll('.password-toggle').forEach(function (button) {
        button.addEventListener('click', function () {
            const input = document.getElementById(button.dataset.passwordTarget);
            const icon = button.querySelector('i');
            const isHidden = input.type === 'password';
            input.type = isHidden ? 'text' : 'password';
            icon.classList.toggle('bi-eye', !isHidden);
            icon.classList.toggle('bi-eye-slash', isHidden);
            button.setAttribute('aria-label', isHidden ? 'Hide password' : 'Show password');
        });
    });
</script>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\sinag\resources\views/auth/register.blade.php ENDPATH**/ ?>