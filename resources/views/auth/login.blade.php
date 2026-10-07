@extends('layouts.app')

@section('content')
<style>
    nav, .navbar { display: none !important; }
    body { overflow-x: hidden; overflow-y: auto; }

    .login-page {
        position: relative;
        min-height: 100dvh;
        width: 100vw;
        margin-left: calc(50% - 50vw);
        box-sizing: border-box;
        overflow-x: hidden;
        padding: clamp(1rem, 4vw, 2.5rem) 1rem;
        background:
            radial-gradient(circle at 50% 42%, rgba(214, 204, 255, 0.92) 0%, rgba(180, 162, 247, 0.58) 28%, transparent 58%),
            radial-gradient(ellipse at 8% 12%, rgba(117, 82, 194, 0.5) 0%, transparent 38%),
            radial-gradient(ellipse at 92% 88%, rgba(91, 52, 159, 0.58) 0%, transparent 42%),
            linear-gradient(135deg, #8f72f5 0%, #b8a7f5 46%, #7652bb 100%);
        background-attachment: fixed;
    }

    .login-page::before,
    .login-page::after {
        content: '';
        position: absolute;
        width: min(46vw, 600px);
        height: min(46vw, 600px);
        border-radius: 50%;
        pointer-events: none;
        filter: blur(18px);
        opacity: 0.24;
        animation: loginMeshFloat 13s ease-in-out infinite alternate;
    }

    .login-page::before {
        top: -20%;
        left: -10%;
        background: rgba(250, 204, 21, 0.18);
    }

    .login-page::after {
        right: -12%;
        bottom: -20%;
        background: rgba(91, 52, 159, 0.3);
        animation-delay: -6s;
    }

    .login-glow {
        position: absolute;
        top: 50%;
        left: 50%;
        width: min(80vw, 900px);
        height: min(80vh, 700px);
        transform: translate(-50%, -50%);
        background: radial-gradient(circle, rgba(165, 148, 249, 0.16) 0%, transparent 70%);
        pointer-events: none;
    }

    .login-shell {
        position: relative;
        z-index: 10;
        width: min(100%, 1100px);
        margin: auto;
        animation: fadeInUp 0.8s ease-out;
    }

    .login-card {
        background: rgba(255, 255, 255, 0.05);
        backdrop-filter: blur(20px);
        border: 1px solid rgba(255, 255, 255, 0.12) !important;
    }

    .login-brand-panel {
        background: linear-gradient(160deg, rgba(122, 91, 196, 0.34), rgba(165, 148, 249, 0.2));
        border-right: 1px solid rgba(255, 255, 255, 0.14);
    }

    .login-logo { width: min(140px, 38vw); }
    .login-logo-pair { display: flex; align-items: center; justify-content: center; gap: 0.35rem; }
    .login-logo-pair img { width: min(96px, 20vw); aspect-ratio: 1; object-fit: contain; }
    .login-logo-pair .login-gad-logo { border-radius: 50%; clip-path: circle(50%); mix-blend-mode: multiply; }
    .login-brand-name { font-size: clamp(2.5rem, 5vw, 3.5rem); }
    .login-form-panel { min-width: 0; }
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

    .login-submit,
    .login-signup {
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

    .login-submit,
    .login-signup {
        position: relative;
        isolation: isolate;
        overflow: hidden;
        transition: transform 0.2s ease, box-shadow 0.2s ease, background-color 0.2s ease;
        will-change: transform;
    }

    .login-submit::before,
    .login-signup::before {
        content: '';
        position: absolute;
        inset: 0;
        z-index: -1;
        background: linear-gradient(110deg, transparent 20%, rgba(255, 255, 255, 0.38) 48%, transparent 72%);
        transform: translateX(-120%);
        transition: transform 0.45s ease;
    }

    .login-submit:hover::before,
    .login-submit:focus-visible::before,
    .login-signup:hover::before,
    .login-signup:focus-visible::before {
        transform: translateX(120%);
    }

    .login-submit:hover,
    .login-submit:focus-visible,
    .login-signup:hover,
    .login-signup:focus-visible {
        transform: translateY(-3px);
        box-shadow: 0 12px 24px rgba(15, 23, 42, 0.22) !important;
    }

    .login-submit:active,
    .login-signup:active {
        transform: translateY(-1px) scale(0.99);
    }

    .login-submit:focus-visible,
    .login-signup:focus-visible {
        outline: 3px solid rgba(255, 255, 255, 0.72);
        outline-offset: 3px;
    }

    .login-signup {
        background: linear-gradient(135deg, #f8cb12 0%, #f6b90a 100%) !important;
        color: #111827 !important;
        border-color: #f8cb12 !important;
    }

    .login-signup:hover,
    .login-signup:focus-visible {
        background: linear-gradient(135deg, #e8bb08 0%, #d9a900 100%) !important;
        color: #111827 !important;
    }

    .login-alert { background: #f3efff; color: #5b4b9b; border-left: 4px solid #8f72f5; }

    .login-forgot {
        color: #facc15;
        transition: color 0.2s ease, text-shadow 0.2s ease;
    }

    .login-forgot:hover,
    .login-forgot:focus-visible {
        color: #fff3a3;
        text-shadow: 0 0 12px rgba(250, 204, 21, 0.42);
    }

    .reset-modal-card { background: linear-gradient(145deg, #f8f7ff, #ede9fe); border: 1px solid rgba(143, 114, 245, 0.25); }

    @media (max-width: 767.98px) {
        .login-page { align-items: flex-start !important; padding-block: 1rem; }
        .login-brand-panel { border-right: 0; border-bottom: 1px solid rgba(255, 255, 255, 0.08); padding: 2rem 1.25rem !important; }
        .login-brand-panel .mt-5 { margin-top: 1.5rem !important; }
        .login-form-panel { padding: 1.25rem !important; }
        .login-form-panel .mb-5 { margin-bottom: 1.5rem !important; }
    }

    @media (max-width: 420px) {
        .login-page { padding-inline: 0.65rem; }
        .login-card { border-radius: 1rem !important; }
        .login-brand-panel { padding-block: 1.5rem !important; }
        .login-logo { margin-bottom: 0.75rem !important; }
        .login-brand-panel p { letter-spacing: 2px !important; }
        .login-form-panel { padding: 1rem !important; }
        .login-form-panel .d-flex.justify-content-end { margin-bottom: 1.25rem !important; }
        .login-form-panel .d-flex.justify-content-between { align-items: flex-start !important; gap: 0.5rem; }
        .login-form-panel .d-flex.justify-content-between a { text-align: right; }
        .login-actions { gap: 0.65rem !important; }
        .login-actions > * { width: 100%; }
    }

    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(30px); }
        to { opacity: 1; transform: translateY(0); }
    }

    @keyframes loginMeshFloat {
        from { transform: translate3d(-3%, -2%, 0) scale(1); }
        to { transform: translate3d(5%, 4%, 0) scale(1.08); }
    }

    @media (prefers-reduced-motion: reduce) {
        .login-page::before,
        .login-page::after { animation: none; }
    }
</style>

<div class="login-page d-flex align-items-center justify-content-center">
    
    <div class="login-glow"></div>

    <div class="login-shell">
        <div class="row justify-content-center">
            <div class="col-12 col-lg-10 col-xl-9">
                <div class="card login-card border-0 rounded-4 shadow-lg overflow-hidden">
                    
                    <div class="row g-0">
                        <div class="login-brand-panel col-md-5 d-flex flex-column align-items-center justify-content-center p-5 text-center">
                            <div class="login-logo-pair mb-4" aria-label="Pangasinan State University Gender and Development">
                                <img src="{{ asset('images/psulogo.png') }}" alt="PSU Logo" class="login-logo" style="filter: drop-shadow(0 0 20px rgba(165, 148, 249, 0.35));">
                                <img src="{{ asset('images/gadlogo.png') }}" alt="GAD Logo" class="login-logo login-gad-logo" style="filter: drop-shadow(0 0 20px rgba(165, 148, 249, 0.35));">
                            </div>
                            <h1 class="login-brand-name fw-bold mb-0" style="letter-spacing: 5px; text-shadow: 0 4px 15px rgba(0,0,0,0.5);">
                                <span style="color: #ede9fe;">SI</span><span style="color: #facc15;">NAG</span>
                            </h1>
                            <p class="text-white small text-uppercase fw-bold mt-3" style="letter-spacing: 3px; font-size: 0.7rem; max-width: 200px;">
                                Gender and Development Safe Space and Participatory Suggestion System
                            </p>
                            <div class="mt-5 pt-4 border-top border-secondary w-75 opacity-50">
                               
                            </div>
                        </div>

                        <div class="login-form-panel col-md-7 p-4 p-lg-5">
                            @if(session('status'))
                                <div class="login-alert alert border-0 mb-3">
                                    <i class="bi bi-check-circle-fill me-1"></i> {{ session('status') }}
                                </div>
                            @endif

                            @if(session('error'))
                                <div class="login-alert alert border-0 mb-3">
                                    <i class="bi bi-exclamation-triangle-fill me-1"></i> {{ session('error') }}
                                </div>
                            @endif

                            <div class="d-flex justify-content-end mb-5">
                                
                            </div>

                            <form method="POST" action="{{ route('login') }}" id="loginForm" autocomplete="off">
                                @csrf

                                <div class="mb-4">
                                    <label class="form-label text-white small fw-bold text-uppercase" style="letter-spacing: 1px;">Username</label>
                                    <div class="login-input-group input-group shadow-sm">
                                        <span class="login-input-icon input-group-text bg-dark border-0 text-white-50">
                                            <i class="bi bi-envelope-at fs-5"></i>
                                        </span>
                                                                                         <input id="email" type="email" class="login-input form-control form-control-lg bg-dark border-0 text-white py-3" 
                                                                                             name="email" value="{{ old('email', request()->cookie('remembered_email', '')) }}" required autocomplete="off" readonly onfocus="this.removeAttribute('readonly')" autofocus 
                                               placeholder="admin@psu.edu.ph" style="font-size: 1rem;">
                                    </div>
                                </div>

                                <div class="mb-4">
                                    <div class="mb-2">
                                        <label class="form-label text-white small fw-bold text-uppercase mb-0" style="letter-spacing: 1px;">Password</label>
                                    </div>
                                    
                                    <div class="position-relative">
                                        <div class="login-input-group input-group shadow-sm">
                                            <span class="login-input-icon input-group-text bg-dark border-0 text-white-50">
                                                <i class="bi bi-key fs-5"></i>
                                            </span>
                                              <input id="password" type="text" class="login-input login-password-hidden form-control form-control-lg bg-dark border-0 text-white py-3 pe-5" 
                                                  name="login_password" required autocomplete="off" style="font-size: 1rem;">
                                              <input type="hidden" name="password" id="passwordValue">
                                        </div>
                                        <button class="btn p-0 bg-transparent text-white-50 border-0" type="button" id="togglePassword" 
                                                style="position: absolute; right: 20px; top: 50%; transform: translateY(-50%); z-index: 10;">
                                            <i class="bi bi-eye-slash fs-5" id="eyeIcon" style="opacity: 0.6; color: #000 !important;"></i>
                                        </button>
                                    </div>
                                </div>

                                <div class="mb-4 px-1">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="remember" id="remember" value="1" {{ old('remember') || request()->hasCookie('remembered_email') ? 'checked' : '' }}>
                                        <label class="form-check-label text-white small" for="remember">Keep session active</label>
                                    </div>
                                </div>

                                <div class="login-actions d-flex flex-column flex-sm-row gap-3">
                                    <button type="submit" class="login-submit btn btn-lg shadow-lg fw-bold text-dark py-3 fs-5 flex-fill" 
                                            style="background: #414bd0; border: none; border-radius: 12px; transition: 0.3s; letter-spacing: 3px; min-width: 0;">
                                        LOG IN
                                    </button>

                                    <a href="{{ route('register') }}" class="login-signup btn btn-lg shadow-lg fw-bold py-3 fs-5 flex-fill d-inline-flex align-items-center justify-content-center text-decoration-none" 
                                       style="background: rgba(255, 255, 255, 0.12); color: #ffffff; border: 1px solid rgba(255, 255, 255, 0.22); border-radius: 12px; transition: 0.3s; letter-spacing: 3px; min-width: 0;">
                                        SIGN UP
                                    </a>
                                </div>

                                <div class="text-center mt-3">
                                    <a class="login-forgot text-decoration-none small fw-bold" href="{{ route('password.code.form') }}">
                                        Forgot Password?
                                    </a>
                                    <span class="text-white-50 mx-2">|</span>
                                    <a class="login-forgot text-decoration-none small fw-bold" href="{{ route('password.status.form') }}">
                                        Check Request Status
                                    </a>
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
    const loginPassword = document.querySelector('#password');
    const passwordValue = document.querySelector('#passwordValue');
    const loginForm = document.querySelector('#loginForm');

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
</script>
@endsection