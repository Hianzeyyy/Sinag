<?php $__env->startSection('content'); ?>
<style>
    nav, .navbar { display: none !important; }
    body { overflow-x: clip; }

    .login-page {
        position: relative;
        min-height: 100dvh;
        align-items: flex-start !important;
        width: 100vw;
        margin-left: calc(50% - 50vw);
        box-sizing: border-box;
        overflow-x: clip;
        overflow-y: visible;
        padding: 1rem 1rem 2rem;
        background:
            radial-gradient(ellipse at 50% 4%, rgba(255,255,255,.95), transparent 55%),
            linear-gradient(180deg, #f6f4ff 0%, #eeeaff 58%, #dcd5ff 100%);
    }
    .login-page::before {
        content: '';
        position: absolute;
        z-index: 0;
        left: -10%;
        right: -10%;
        bottom: 0;
        height: 10rem;
        pointer-events: none;
        opacity: .96;
        background:
            radial-gradient(ellipse 25% 60% at 10% 100%, #fff 0 55%, transparent 57%),
            radial-gradient(ellipse 30% 76% at 31% 100%, #fff 0 55%, transparent 57%),
            radial-gradient(ellipse 31% 66% at 56% 100%, #fff 0 56%, transparent 58%),
            radial-gradient(ellipse 26% 78% at 78% 100%, #fff 0 56%, transparent 58%),
            radial-gradient(ellipse 24% 61% at 96% 100%, #fff 0 56%, transparent 58%),
            linear-gradient(to bottom, transparent 0 40%, #fff 72%);
        filter: drop-shadow(0 -10px 18px rgba(255,255,255,.58));
        animation: cloudDrift 42s ease-in-out infinite alternate;
    }

    .login-page::after {
        content: '';
        position: absolute;
        z-index: 0;
        left: -15%;
        right: -15%;
        bottom: 0;
        height: 8rem;
        pointer-events: none;
        opacity: .72;
        background:
            radial-gradient(ellipse 27% 66% at 7% 100%, #fff 0 55%, transparent 57%),
            radial-gradient(ellipse 34% 82% at 35% 100%, #fff 0 55%, transparent 57%),
            radial-gradient(ellipse 30% 75% at 66% 100%, #fff 0 55%, transparent 57%),
            radial-gradient(ellipse 29% 74% at 93% 100%, #fff 0 55%, transparent 57%),
            linear-gradient(to bottom, transparent 0 45%, #fff 76%);
        filter: blur(2px);
        animation: cloudDrift 58s ease-in-out infinite alternate-reverse;
    }

    .login-shell {
        position: relative;
        z-index: 2;
        width: min(100%, 1040px);
        margin: 0 auto;
        animation: fadeInUp 0.8s ease-out;
    }

    .login-card {
        background: #fff;
        border: 1px solid rgba(111, 91, 190, 0.14) !important;
        box-shadow: 0 24px 80px rgba(75, 58, 139, .18) !important;
    }

    .login-logo { width: min(140px, 38vw); }
    .login-logo-pair { display: flex; align-items: center; justify-content: center; gap: 0.35rem; }
    .login-logo-pair img { width: min(96px, 20vw); aspect-ratio: 1; object-fit: contain; }
    .login-logo-pair .login-gad-logo { border-radius: 50%; clip-path: circle(50%); mix-blend-mode: multiply; }
    .login-brand-name { font-size: clamp(2.5rem, 5vw, 3.5rem); }
    .login-form-panel { min-width: 0; width: 100%; padding: 1.5rem !important; }
    .login-input-group {
        border-radius: 12px;
        overflow: hidden;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .login-input-group:focus-within {
        transform: translateY(-2px);
        box-shadow: 0 10px 24px rgba(53, 38, 126, 0.24), 0 0 0 3px rgba(250, 204, 21, 0.22) !important;
    }

    .login-input-group:focus-within .login-input-icon {
        background: #3038a8 !important;
        color: #fff !important;
    }
    .login-input-icon { padding-inline: clamp(0.85rem, 3vw, 1.5rem); }
    .login-input { min-width: 0; font-size: 1rem !important; }
    .login-password-hidden { -webkit-text-security: disc; }
    .login-actions { min-width: 0; }
    .login-actions > * { min-height: 52px; min-width: 0; }

    .login-submit {
        border-radius: 999px !important;
        letter-spacing: 0.16em !important;
        box-shadow: 0 10px 22px rgba(53, 38, 126, 0.2) !important;
    }

    .login-submit {
        background: linear-gradient(135deg, #414bd0 0%, #5964e8 100%) !important;
        color: #fff !important;
    }

    .login-submit:hover,
    .login-submit:focus-visible {
        background: linear-gradient(135deg, #353db8 0%, #4d58d8 100%) !important;
        color: #fff !important;
    }

    .login-submit {
        position: relative;
        isolation: isolate;
        overflow: hidden;
        transition: transform 0.2s ease, box-shadow 0.2s ease, background-color 0.2s ease;
        will-change: transform;
    }

    .login-submit::before {
        content: '';
        position: absolute;
        inset: 0;
        z-index: -1;
        background: linear-gradient(110deg, transparent 20%, rgba(255, 255, 255, 0.38) 48%, transparent 72%);
        transform: translateX(-120%);
        transition: transform 0.45s ease;
    }

    .login-submit:hover::before,
    .login-submit:focus-visible::before {
        transform: translateX(120%);
    }

    .login-submit:hover,
    .login-submit:focus-visible {
        transform: translateY(-3px);
        box-shadow: 0 12px 24px rgba(15, 23, 42, 0.22) !important;
    }

    .login-submit:active {
        transform: translateY(-1px) scale(0.99);
    }

    .login-submit:focus-visible {
        outline: 3px solid rgba(255, 255, 255, 0.72);
        outline-offset: 3px;
    }

    .login-alert { background: #f3efff; color: #5b4b9b; border-left: 4px solid #8f72f5; }

    .login-forgot {
        color: #5b43b2;
        transition: color 0.2s ease, text-shadow 0.2s ease;
    }

    .login-forgot:hover,
    .login-forgot:focus-visible {
        color: #35216d;
        text-decoration: underline !important;
    }

    .reset-modal-card { background: linear-gradient(145deg, #f8f7ff, #ede9fe); border: 1px solid rgba(143, 114, 245, 0.25); }

    .login-form-panel { display: flex; flex-direction: column; }
    .auth-stage {
        display: grid;
        grid-template-columns: minmax(0, 1fr) minmax(0, 1fr);
        align-items: stretch;
        position: relative;
        min-height: 0;
        flex: 0 0 auto;
        overflow: visible;
        border-radius: 1rem;
        background: #fff;
        box-shadow: 0 10px 36px rgba(64, 48, 119, .12);
    }
    .auth-pane {
        position: relative;
        grid-area: 1 / 1;
        width: 100%;
        height: auto;
        min-height: 0;
        padding: clamp(1.5rem, 3vw, 2.75rem);
        color: #25213e;
        background: #fff;
        transition: transform .6s ease, opacity .35s ease, visibility .35s ease;
        overflow: visible;
    }
    .auth-pane .text-white { color: #302b4c !important; }
    .auth-pane .text-white-50 { color: #6f6a83 !important; }
    .auth-pane .form-control,
    .auth-pane .form-select {
        color: #25213e;
        background-color: #f5f4fa;
        border: 1px solid #e2dfed;
        border-radius: .65rem;
    }
    .auth-pane .form-control:focus,
    .auth-pane .form-select:focus {
        border-color: #8f72f5;
        box-shadow: 0 0 0 .2rem rgba(143, 114, 245, .16);
    }
    .sign-in-pane { z-index: 2; }
    .sign-up-pane { display: none; left: 0; z-index: 1; transform: translateX(100%); }
    .auth-stage.right-panel-active .sign-in-pane { display: none; z-index: 1; transform: translateX(100%); }
    .auth-stage.right-panel-active .sign-up-pane { display: block; z-index: 3; transform: translateX(100%); }
    .auth-overlay {
        position: absolute;
        inset: 0 0 0 auto;
        z-index: 5;
        width: 50%;
        padding: 2rem;
        display: flex;
        align-items: center;
        justify-content: center;
        text-align: center;
        color: #302653;
        background: linear-gradient(145deg, #f1edff, #e6e0ff 58%, #d6cdff);
        transition: transform .6s ease;
    }
    .auth-overlay-copy { position: absolute; inset: 0; display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 1.5rem; transition: opacity .25s ease, transform .6s ease; }
    .auth-overlay-left { opacity: 0; visibility: hidden; transform: translateX(-15%); }
    .auth-stage.right-panel-active .auth-overlay { transform: translateX(-100%); }
    .auth-stage.right-panel-active .auth-overlay-right { opacity: 0; visibility: hidden; transform: translateX(15%); }
    .auth-stage.right-panel-active .auth-overlay-left { opacity: 1; visibility: visible; transform: translateX(0); }
    .auth-overlay-copy p { max-width: 24rem; color: #625b7f; }
    .auth-brand-logo { display: flex; align-items: center; justify-content: center; gap: .75rem; margin-bottom: 1rem; }
    .auth-brand-logo img { width: clamp(3rem, 7vw, 5rem); height: clamp(3rem, 7vw, 5rem); object-fit: contain; }
    .auth-brand-logo img:nth-child(2) { border-radius: 50%; clip-path: circle(49%); mix-blend-mode: multiply; }
    .auth-brand-title { margin: 0 0 .5rem; color: #5b43b2; font-weight: 900; letter-spacing: .24em; font-size: clamp(2rem, 4vw, 3rem); }
    .auth-mobile-brand, .mobile-signup-prompt, .auth-mobile-signin-dock { display: none; }
    .auth-overlay-account { margin: .35rem 0 .7rem; color: #625b7f; font-weight: 600; }
    .auth-pane .form-control, .auth-pane .form-select { min-height: 44px; font-size: .9rem; }
    .auth-pane .form-label { color: #4d4862 !important; letter-spacing: .04em; }
    .auth-pane .login-input-icon { background: #eeeaf9 !important; color: #6a55bd !important; }
    .auth-pane .login-input { background: #f5f4fa !important; color: #25213e !important; }
    .auth-pane .form-check-label { color: #5d5870 !important; }
    .auth-pane .login-forgot { color: #5b43b2; }
    .auth-pane .login-actions { justify-content: center; }
    .auth-switch-signup {
        min-height: 44px;
        border: 0;
        border-radius: .65rem;
        color: #5b43b2;
        background: #f3f0ff;
        font-size: .92rem;
        transition: background .2s ease, color .2s ease;
    }
    .auth-switch-signup:hover,
    .auth-switch-signup:focus-visible { color: #fff; background: #6b55c4; }
    .auth-toggle {
        border: 1px solid #735bd0;
        border-radius: 999px;
        padding: .65rem 1.4rem;
        color: #5b43b2;
        background: #fff;
        font-weight: 700;
        letter-spacing: .08em;
        transition: color .2s ease, background .2s ease, transform .2s ease;
    }
    .auth-toggle:hover, .auth-toggle:focus-visible { background: #5b43b2; color: #fff; transform: translateY(-2px); }
    .register-fields { display: grid; grid-template-columns: repeat(6, minmax(0, 1fr)); }
    .register-submit {
        display: inline-flex;
        width: auto !important;
        max-width: 100%;
        min-height: 0;
        align-items: center;
        justify-content: center;
        padding: .65rem 1.35rem !important;
        font-size: .9rem;
        line-height: 1.25;
        white-space: nowrap;
    }
    .register-fields > .col-12.register-submit-wrap {
        grid-column: 1 / -1;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: .55rem;
    }
    .register-links { display: flex; justify-content: center; align-items: center; flex-wrap: wrap; gap: .35rem; text-align: center; }
    .register-submit {
        width: min(100%, 15rem) !important;
        min-height: 3.2rem;
        padding: .8rem 1.25rem !important;
        font-size: 1rem;
    }
    .register-fields > .col-12 { width: auto; max-width: none; flex: none; }
    .register-fields > .col-6 { width: auto; max-width: none; flex: none; grid-column: span 3; }
    .register-fields > .col-12:not(.register-name-field) { grid-column: span 3; }
    .register-fields > .register-name-field { grid-column: span 2; }
    .register-fields > .col-12.d-none { display: none !important; }
    .register-fields > .col-12.register-submit-wrap { grid-column: 1 / -1; }
    .auth-overlay-copy p { font-size: .9rem; line-height: 1.5; }
    .register-password { padding-right: 2.75rem; }
    .register-password-toggle {
        position: absolute;
        top: 50%;
        right: .45rem;
        z-index: 3;
        padding: .35rem;
        border: 0;
        color: #6b55c4;
        background: transparent;
        transform: translateY(-50%);
    }
    .register-password::-ms-reveal { display: none; }

    @media (max-width: 991.98px) {
        .login-page { align-items: flex-start !important; padding-block: 1rem 2rem; }
        .login-form-panel { padding: .75rem !important; }
        .auth-mobile-brand { display: block; padding: .75rem 1rem 0; text-align: center; }
        .auth-mobile-brand .auth-brand-logo { margin-bottom: .25rem; }
        .auth-mobile-brand .auth-brand-logo img { width: 3rem; height: 3rem; }
        .auth-mobile-brand .auth-brand-title { margin-bottom: .65rem; font-size: 2rem; }
        .mobile-signup-prompt { display: flex; align-items: center; justify-content: center; gap: .5rem; margin: 1rem 0 0; color: #625b7f; font-size: .85rem; }
        .mobile-signup-prompt button { padding: .4rem .9rem; font-size: .78rem; }
        .login-form-panel.signup-active .mobile-signup-prompt { display: none; }
        .auth-stage { display: block; min-height: 0; overflow: visible; background: transparent; box-shadow: none; }
        .auth-pane {
            position: relative;
            inset: auto;
            width: 100%;
            height: auto;
            max-height: none;
            padding: 1.5rem;
            overflow: visible;
            transform: none !important;
            transition: none;
        }

        .auth-pane .login-actions { flex-direction: column; }
        .register-fields { grid-template-columns: repeat(6, minmax(0, 1fr)); }
        .register-fields > .col-12:not(.register-name-field) { grid-column: span 3; }
    }

    @media (max-width: 575.98px) {
        .register-fields > .col-6,
        .register-fields > .col-12:not(.register-name-field),
        .register-fields > .register-name-field { grid-column: span 6; }
    }

    @media (max-width: 991.98px) {
        .auth-overlay { display: none; }
        .sign-in-pane { display: block; }
        .sign-up-pane { display: none; }
        .auth-stage.right-panel-active .sign-in-pane { display: none; }
        .auth-stage.right-panel-active .sign-up-pane { display: block; }
        .auth-pane [data-auth-toggle="signup"],
        .auth-pane [data-auth-toggle="signin"] { min-height: 44px; }
        .auth-mobile-signin-dock {
            position: static;
            width: 100%;
            display: none;
            align-items: center;
            justify-content: center;
            gap: .35rem;
            margin-top: 1rem;
            padding: .65rem .75rem;
            border: 1px solid rgba(111, 91, 190, .15);
            border-radius: .75rem;
            color: #514675;
            background: rgba(255, 255, 255, .96);
        }
        .login-page.signup-active .auth-mobile-signin-dock { display: flex; }
        .auth-mobile-signin-dock button { padding: 0; border: 0; color: #5b43b2; background: transparent; font-weight: 800; }
    }

    @media (max-width: 420px) {
        .login-page { padding-inline: 0.65rem; }
        .login-card { border-radius: 1rem !important; }
        .login-form-panel { padding: 1rem !important; }
        .auth-stage { min-height: 0; }
        .auth-pane { padding: 1rem; }
        .login-page::before, .login-page::after { height: 12rem; }
    }

    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(30px); }
        to { opacity: 1; transform: translateY(0); }
    }

    @keyframes cloudDrift {
        from { transform: translate3d(-2%, 0, 0); }
        to { transform: translate3d(3%, 0, 0); }
    }

    @media (prefers-reduced-motion: reduce) {
        .login-shell,
        .login-page::before,
        .login-page::after,
        .auth-pane,
        .auth-overlay { animation: none; transition: none; }
    }
</style>

<div class="login-page d-flex align-items-center justify-content-center">
    
    <div class="login-shell">
        <div class="card login-card border-0 rounded-4 overflow-hidden">
                        <div class="login-form-panel">
                            <?php if(session('status')): ?>
                                <div class="login-alert alert border-0 mb-3">
                                    <i class="bi bi-check-circle-fill me-1"></i> <?php echo e(session('status')); ?>

                                </div>
                            <?php endif; ?>

                            <?php if(session('error')): ?>
                                <div class="login-alert alert border-0 mb-3">
                                    <i class="bi bi-exclamation-triangle-fill me-1"></i> <?php echo e(session('error')); ?>

                                </div>
                            <?php endif; ?>

                            <?php if($errors->any()): ?>
                                <div class="alert alert-warning py-2 mb-3" role="alert">
                                    <ul class="mb-0 small">
                                        <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><li><?php echo e($error); ?></li><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </ul>
                                </div>
                            <?php endif; ?>

                            <div class="auth-mobile-brand" aria-label="SINAG, Pangasinan State University Gender and Development">
                                <div class="auth-brand-logo">
                                    <img src="<?php echo e(asset('images/psulogo.png')); ?>" alt="PSU Logo">
                                    <img src="<?php echo e(asset('images/gadlogo.png')); ?>" alt="GAD Logo">
                                </div>
                                <h1 class="auth-brand-title">SINAG</h1>
                            </div>
                            <div class="auth-stage <?php echo e(($showRegisterPanel ?? request()->routeIs('register')) ? 'right-panel-active' : ''); ?>" id="authStage">
                            <form method="POST" action="<?php echo e(route('login')); ?>" id="loginForm" class="auth-pane sign-in-pane" autocomplete="off">
                                <?php echo csrf_field(); ?>

                                <div class="mb-4">
                                    <label class="form-label small fw-bold text-uppercase" style="letter-spacing: 1px;">Username</label>
                                    <div class="login-input-group input-group shadow-sm">
                                        <span class="login-input-icon input-group-text border-0">
                                            <i class="bi bi-envelope-at fs-5"></i>
                                        </span>
                                        <input id="email" type="email" class="login-input form-control form-control-lg border-0 py-3"
                                               name="email" value="<?php echo e(old('email', request()->cookie('remembered_email', ''))); ?>" required autocomplete="off" readonly onfocus="this.removeAttribute('readonly')" autofocus
                                               placeholder="username@psu.edu.ph" style="font-size: 1rem;">
                                    </div>
                                </div>

                                <div class="mb-4">
                                    <div class="mb-2">
                                        <label class="form-label small fw-bold text-uppercase mb-0" style="letter-spacing: 1px;">Password</label>
                                    </div>
                                    
                                    <div class="position-relative">
                                        <div class="login-input-group input-group shadow-sm">
                                            <span class="login-input-icon input-group-text border-0">
                                                <i class="bi bi-key fs-5"></i>
                                            </span>
                                              <input id="password" type="text" class="login-input login-password-hidden form-control form-control-lg border-0 py-3 pe-5"
                                                  name="login_password" required autocomplete="off" style="font-size: 1rem;">
                                              <input type="hidden" name="password" id="passwordValue">
                                        </div>
                                        <button class="btn p-0 bg-transparent border-0 text-secondary" type="button" id="togglePassword"
                                                style="position: absolute; right: 20px; top: 50%; transform: translateY(-50%); z-index: 10;"
                                                title="Toggle password visibility" aria-label="Toggle password visibility">
                                            <i class="bi bi-eye-slash fs-5" id="eyeIcon" style="opacity: 0.9; transition: opacity 0.2s ease, transform 0.2s ease;"></i>
                                        </button>
                                    </div>
                                </div>

                                <div class="mb-4 px-1">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="remember" id="remember" value="1" <?php echo e(old('remember') || request()->hasCookie('remembered_email') ? 'checked' : ''); ?>>
                                        <label class="form-check-label small" for="remember">Keep session active</label>
                                    </div>
                                </div>

                                <div class="login-actions d-grid gap-2">
                                    <button type="submit" class="login-submit btn btn-lg shadow-lg fw-bold text-dark py-3 fs-5 flex-fill" 
                                            style="background: #414bd0; border: none; border-radius: 12px; transition: 0.3s; letter-spacing: 3px; min-width: 0;">
                                        LOG IN
                                    </button>
                                </div>

                                <div class="text-center mt-3">
                                    <a class="login-forgot text-decoration-none small fw-bold" href="<?php echo e(route('password.code.form')); ?>">
                                        Forgot Password?
                                    </a>
                                    <span class="text-white-50 mx-2">|</span>
                                    <a class="login-forgot text-decoration-none small fw-bold" href="<?php echo e(route('password.status.form')); ?>">
                                        Check Request Status
                                    </a>
                                </div>
                                <div class="mobile-signup-prompt">
                                    <span>Don't have an account?</span>
                                    <button type="button" class="auth-toggle" data-auth-toggle="signup">SIGN UP</button>
                                </div>
                            </form>

                            <form method="POST" action="<?php echo e(route('register')); ?>" enctype="multipart/form-data" id="registerForm" class="auth-pane sign-up-pane">
                                <?php echo csrf_field(); ?>
                                <h3 class="text-white fw-bold mb-1">Create Account</h3>
                                <p class="text-white-50 small mb-3">Join the SINAG network today.</p>
                                <div class="row g-2 register-fields">
                                    <div class="col-12 col-sm-4 register-name-field">
                                        <label class="form-label small fw-bold text-uppercase">First Name</label>
                                        <input type="text" class="form-control" name="first_name" value="<?php echo e(old('first_name')); ?>" maxlength="255" required autocomplete="given-name" placeholder="Juan">
                                    </div>
                                    <div class="col-12 col-sm-4 register-name-field">
                                        <label class="form-label small fw-bold text-uppercase">Middle Name</label>
                                        <input type="text" class="form-control" name="middle_name" value="<?php echo e(old('middle_name')); ?>" maxlength="255" autocomplete="additional-name" placeholder="Middle name (if any)">
                                    </div>
                                    <div class="col-12 col-sm-4 register-name-field">
                                        <label class="form-label small fw-bold text-uppercase">Last Name</label>
                                        <input type="text" class="form-control" name="last_name" value="<?php echo e(old('last_name')); ?>" maxlength="255" required autocomplete="family-name" placeholder="Dela Cruz">
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label small fw-bold text-uppercase">Institutional Email</label>
                                        <input type="email" class="form-control" name="email" value="<?php echo e(old('email')); ?>" required placeholder="username@psu.edu.ph">
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label small fw-bold text-uppercase">Contact Number</label>
                                        <input type="tel" class="form-control" name="phone_number" value="<?php echo e(old('phone_number')); ?>" inputmode="numeric" pattern="[0-9]+" maxlength="50" required placeholder="e.g. 09123456789">
                                    </div>
                                    <div class="col-12 col-sm-6">
                                        <label class="form-label small fw-bold text-uppercase">Account Type</label>
                                        <select class="form-select" name="account_type" id="accountType" required>
                                            <option value="" disabled <?php echo e(old('account_type') ? '' : 'selected'); ?>>Choose type</option>
                                            <option value="student" <?php echo e(old('account_type') === 'student' ? 'selected' : ''); ?>>Student</option>
                                            <option value="employee" <?php echo e(old('account_type') === 'employee' ? 'selected' : ''); ?>>Employee</option>
                                        </select>
                                    </div>
                                    <div class="col-6 col-sm-3">
                                        <label class="form-label small fw-bold text-uppercase">Age</label>
                                        <input type="number" class="form-control" name="age" value="<?php echo e(old('age')); ?>" min="1" max="120" required>
                                    </div>
                                    <div class="col-6 col-sm-3">
                                        <label class="form-label small fw-bold text-uppercase">Gender</label>
                                        <select class="form-select" name="gender" required>
                                            <option value="" disabled <?php echo e(old('gender') ? '' : 'selected'); ?>>Choose</option>
                                            <?php $__currentLoopData = ['Male', 'Female', 'Prefer not to say']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $option): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <option value="<?php echo e($option); ?>" <?php echo e(old('gender') === $option ? 'selected' : ''); ?>><?php echo e($option); ?></option>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </select>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label small fw-bold text-uppercase">Department / Course</label>
                                        <select class="form-select" name="department" id="department" required data-old-department="<?php echo e(old('department')); ?>">
                                            <option value="" disabled <?php echo e(old('department') ? '' : 'selected'); ?>>Choose course or department</option>
                                        </select>
                                    </div>
                                    <div class="col-12 d-none" id="employeeCategoryField">
                                        <label class="form-label small fw-bold text-uppercase">Employee Category</label>
                                        <select class="form-select" name="employee_category" id="employeeCategory">
                                            <option value="" disabled <?php echo e(old('employee_category') ? '' : 'selected'); ?>>Choose employee category</option>
                                            <option value="teaching" <?php echo e(old('employee_category') === 'teaching' ? 'selected' : ''); ?>>Teaching</option>
                                            <option value="non_teaching" <?php echo e(old('employee_category') === 'non_teaching' ? 'selected' : ''); ?>>Non-teaching</option>
                                        </select>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label small fw-bold text-uppercase">Profile Picture</label>
                                        <input type="file" class="form-control" name="profile_photo" accept="image/*" required>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label small fw-bold text-uppercase">Photo of your Student ID</label>
                                        <input type="file" class="form-control" name="selfie_image" accept="image/*" required>
                                    </div>
                                    <div class="col-12 col-sm-6">
                                        <label class="form-label small fw-bold text-uppercase">Password</label>
                                        <div class="position-relative">
                                            <input type="password" class="form-control register-password" name="password" id="registerPassword" autocomplete="new-password" required>
                                            <button type="button" class="register-password-toggle" data-password-target="registerPassword" aria-label="Show password" aria-pressed="false"><i class="bi bi-eye"></i></button>
                                        </div>
                                    </div>
                                    <div class="col-12 col-sm-6">
                                        <label class="form-label small fw-bold text-uppercase">Confirm Password</label>
                                        <div class="position-relative">
                                            <input type="password" class="form-control register-password" name="password_confirmation" id="registerPasswordConfirmation" autocomplete="new-password" required>
                                            <button type="button" class="register-password-toggle" data-password-target="registerPasswordConfirmation" aria-label="Show password" aria-pressed="false"><i class="bi bi-eye"></i></button>
                                        </div>
                                    </div>
                                    <div class="col-12 register-submit-wrap mt-2">
                                        <button type="submit" class="btn fw-bold text-dark register-submit" style="background:#facc15;border-radius:12px;">REGISTER ACCOUNT</button>
                                        <div class="register-links small">
                                            <a class="login-forgot text-decoration-none fw-bold" href="<?php echo e(route('password.code.form')); ?>">Forgot Password?</a>
                                            <span class="text-muted" aria-hidden="true">|</span>
                                            <a class="login-forgot text-decoration-none fw-bold" href="<?php echo e(route('password.status.form')); ?>">Check Request Status</a>
                                        </div>
                                    </div>
                                </div>
                                <div class="auth-mobile-signin-dock">
                                    Already have an account? <button type="button" data-auth-toggle="signin">Log in</button>
                                </div>
                            </form>

                            <div class="auth-overlay">
                                <div class="auth-overlay-copy auth-overlay-right">
                                    <div class="auth-brand-logo" aria-label="Pangasinan State University Gender and Development">
                                        <img src="<?php echo e(asset('images/psulogo.png')); ?>" alt="PSU Logo">
                                        <img src="<?php echo e(asset('images/gadlogo.png')); ?>" alt="GAD Logo">
                                    </div>
                                    <h1 class="auth-brand-title">SINAG</h1>
                                    <p>Gender and Development Safe Space and Participatory Suggestion System for Employees and Students of Pangasinan State University Asingan Campus</p>
                                    <div class="auth-overlay-account">Don't have an account?</div>
                                    <button class="auth-toggle" type="button" data-auth-toggle="signup">SIGN UP</button>
                                </div>
                                <div class="auth-overlay-copy auth-overlay-left">
                                    <div class="auth-brand-logo" aria-label="Pangasinan State University Gender and Development">
                                        <img src="<?php echo e(asset('images/psulogo.png')); ?>" alt="PSU Logo">
                                        <img src="<?php echo e(asset('images/gadlogo.png')); ?>" alt="GAD Logo">
                                    </div>
                                    <h1 class="auth-brand-title">SINAG</h1>
                                    <h2 class="fw-bold">Welcome Back!</h2>
                                    <p>Gender and Development Safe Space and Participatory Suggestion System for Employees and Students of Pangasinan State University Asingan Campus</p>
                                    <div class="auth-overlay-account">Already have an account?</div>
                                    <button class="auth-toggle" type="button" data-auth-toggle="signin">SIGN IN</button>
                                </div>
                            </div>
                            </div>
                        </div>

        </div>
    </div>
</div>

<script>
    const authStage = document.getElementById('authStage');
    const loginForm = document.getElementById('loginForm');
    const registerForm = document.getElementById('registerForm');
    function setAuthPanel(showSignup) {
        authStage.classList.toggle('right-panel-active', showSignup);
        document.querySelector('.login-form-panel').classList.toggle('signup-active', showSignup);
        document.querySelector('.login-page').classList.toggle('signup-active', showSignup);
        loginForm.querySelectorAll('input, button, select, textarea').forEach(function (field) {
            field.disabled = showSignup;
        });
        registerForm.querySelectorAll('input, button, select, textarea').forEach(function (field) {
            field.disabled = !showSignup;
        });
    }
    document.querySelectorAll('[data-auth-toggle]').forEach(function (button) {
        button.addEventListener('click', function () {
            const showSignup = button.dataset.authToggle === 'signup';
            setAuthPanel(showSignup);
            if (showSignup) document.getElementById('registerPassword')?.focus({ preventScroll: true });
            else document.getElementById('email')?.focus({ preventScroll: true });
        });
    });

    setAuthPanel(authStage.classList.contains('right-panel-active'));

    const loginPassword = document.querySelector('#password');
    const passwordValue = document.querySelector('#passwordValue');

    window.addEventListener('pageshow', function () {
        // Clear a restored password only when the login page is loaded again, never before submit.
        loginPassword.value = '';
        passwordValue.value = '';
    });

    const togglePassword = document.querySelector('#togglePassword');
    const password = document.querySelector('#password');
    const eyeIcon = document.querySelector('#eyeIcon');

    togglePassword.addEventListener('click', function () {
        password.classList.toggle('login-password-hidden');
        eyeIcon.classList.toggle('bi-eye');
        eyeIcon.classList.toggle('bi-eye-slash');
    });

    loginForm.addEventListener('submit', function () {
        passwordValue.value = loginPassword.value;
    });

    document.querySelectorAll('.register-password-toggle').forEach(function (button) {
        button.addEventListener('click', function () {
            const input = document.getElementById(button.dataset.passwordTarget);
            const icon = button.querySelector('i');
            const isHidden = input.type === 'password';
            input.type = isHidden ? 'text' : 'password';
            icon.classList.toggle('bi-eye', !isHidden);
            icon.classList.toggle('bi-eye-slash', isHidden);
            button.setAttribute('aria-label', isHidden ? 'Hide password' : 'Show password');
            button.setAttribute('aria-pressed', String(isHidden));
        });
    });

    registerForm.addEventListener('submit', function () {
        const email = registerForm.querySelector('[name="email"]').value.trim().toLowerCase();
        const accounts = JSON.parse(localStorage.getItem('sinag.login.accounts') || '[]');
        if (email && email.includes('@')) {
            localStorage.setItem('sinag.login.accounts', JSON.stringify([email].concat(accounts.filter(account => account !== email)).slice(0, 10)));
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
        department.innerHTML = '<option value="" disabled selected>Choose course or department</option>';
        (departmentOptions[accountType.value] || []).forEach(function ([value, label]) {
            department.add(new Option(label, value, false, value === oldDepartment));
        });
        const isEmployee = accountType.value === 'employee';
        employeeCategoryField.classList.toggle('d-none', !isEmployee);
        employeeCategory.required = isEmployee;
        if (!isEmployee) employeeCategory.value = '';
    }
    accountType.addEventListener('change', updateDepartmentOptions);
    updateDepartmentOptions();

</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\sinag\resources\views/auth/login.blade.php ENDPATH**/ ?>