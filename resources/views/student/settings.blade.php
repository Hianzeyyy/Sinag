@extends('layouts.app')

@section('content')
<div class="container py-4 student-settings-page">
    <div class="mb-4">
        <p class="text-uppercase small fw-bold mb-1" style="color:#8f72f5; letter-spacing:.12em;">Your preferences</p>
        <h1 class="fw-bold mb-2">Settings</h1>
        <p class="text-muted mb-0">Manage the appearance of SINAG and your personal information.</p>
    </div>

    <div class="row g-4">
        <div class="col-12 col-lg-7">
            <section class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <span class="settings-icon"><i class="bi bi-palette-fill"></i></span>
                        <div>
                            <h2 class="h5 fw-bold mb-1">Appearance</h2>
                            <p class="small text-muted mb-0">Choose how SINAG looks on this device.</p>
                        </div>
                    </div>
                    <fieldset class="border-0 p-0 m-0">
                        <legend class="visually-hidden">Color theme</legend>
                        <div class="row g-3">
                            <div class="col-sm-6">
                                <label class="theme-choice" for="studentThemeLight">
                                    <input class="form-check-input" type="radio" name="student_theme" id="studentThemeLight" value="light">
                                    <span class="theme-preview theme-preview-light" aria-hidden="true"><i class="bi bi-sun-fill"></i></span>
                                    <span>
                                        <strong class="d-block">Light mode</strong>
                                        <small class="text-muted">Bright, classic appearance</small>
                                    </span>
                                </label>
                            </div>
                            <div class="col-sm-6">
                                <label class="theme-choice" for="studentThemeDark">
                                    <input class="form-check-input" type="radio" name="student_theme" id="studentThemeDark" value="dark">
                                    <span class="theme-preview theme-preview-dark" aria-hidden="true"><i class="bi bi-moon-stars-fill"></i></span>
                                    <span>
                                        <strong class="d-block">Dark mode</strong>
                                        <small class="text-muted">Midnight purple appearance</small>
                                    </span>
                                </label>
                            </div>
                        </div>
                    </fieldset>
                    <div class="small text-muted mt-3" id="themePreferenceStatus" role="status" aria-live="polite">
                        Your choice is saved on this device.
                    </div>
                </div>
            </section>
        </div>

        <div class="col-12 col-lg-5">
            <section class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body p-4 d-flex flex-column align-items-start">
                    <span class="settings-icon mb-3"><i class="bi bi-person-vcard-fill"></i></span>
                    <h2 class="h5 fw-bold mb-2">Personal data</h2>
                    <p class="text-muted mb-4">Review the personal information connected to your SINAG account.</p>
                    <a href="{{ route('personal-data.show') }}" class="btn student-settings-link rounded-pill mt-auto">
                        <i class="bi bi-person-lines-fill me-2"></i>View personal data
                    </a>
                </div>
            </section>
        </div>
    </div>
</div>

<style>
    .student-settings-page .card {
        background: #fff;
        color: #281b42;
        border: 1px solid #eee8f7 !important;
    }

    .settings-icon {
        display: inline-flex;
        width: 2.75rem;
        height: 2.75rem;
        align-items: center;
        justify-content: center;
        flex: 0 0 2.75rem;
        border-radius: .9rem;
        color: #7658df;
        background: #f0eaff;
        font-size: 1.15rem;
    }

    .theme-choice {
        display: flex;
        align-items: center;
        gap: .75rem;
        height: 100%;
        padding: .85rem;
        border: 1px solid #e8e1f2;
        border-radius: 1rem;
        cursor: pointer;
        transition: border-color .18s ease, box-shadow .18s ease, transform .18s ease;
    }

    .theme-choice:has(input:checked) {
        border-color: #8f72f5;
        box-shadow: 0 0 0 3px rgba(143, 114, 245, .12);
    }

    .theme-choice:hover {
        transform: translateY(-2px);
    }

    .theme-choice .form-check-input {
        margin: 0;
        flex: 0 0 auto;
        accent-color: #7658df;
    }

    .theme-preview {
        display: inline-flex;
        width: 2.5rem;
        height: 2.5rem;
        align-items: center;
        justify-content: center;
        border-radius: .8rem;
    }

    .theme-preview-light {
        color: #7658df;
        background: #f0eaff;
    }

    .theme-preview-dark {
        color: #f4edff;
        background: #21182e;
    }

    .student-settings-link {
        padding: .65rem 1rem;
        border: 1px solid #d6c9f5;
        color: #5b3db5;
        font-weight: 700;
    }

    .student-settings-link:hover,
    .student-settings-link:focus-visible {
        background: #f2edff;
        border-color: #a594f9;
        color: #4c2b9b;
    }

    @media (prefers-reduced-motion: reduce) {
        .theme-choice { transition: none; }
        .theme-choice:hover { transform: none; }
    }
</style>

<script>
    (function () {
        const themeRadios = document.querySelectorAll('input[name="student_theme"]');
        const status = document.getElementById('themePreferenceStatus');

        function setTheme(theme) {
            if (!['light', 'dark'].includes(theme)) return;
            document.documentElement.dataset.studentTheme = theme;
            try {
                localStorage.setItem('sinag-student-theme', theme);
                status.textContent = `${theme === 'dark' ? 'Dark' : 'Light'} mode is saved on this device.`;
            } catch (error) {
                console.error('Unable to save the SINAG theme preference:', error);
                status.textContent = 'The theme changed, but this browser could not save the preference.';
            }
        }

        const savedTheme = document.documentElement.dataset.studentTheme || 'light';
        themeRadios.forEach((radio) => {
            radio.checked = radio.value === savedTheme;
        });

        themeRadios.forEach((radio) => {
            radio.addEventListener('change', () => setTheme(radio.value));
        });
    })();
</script>
@endsection
