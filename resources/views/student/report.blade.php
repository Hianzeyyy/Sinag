@extends('layouts.app')

@section('content')
<style>
    body {
        background: linear-gradient(135deg, #f3e8ff 0%, #f8fafc 100%) !important;
    }
    .report-page {
        height: auto;
        min-height: 0;
        padding-bottom: max(2rem, env(safe-area-inset-bottom));
        touch-action: pan-y;
    }
    .report-page .row,
    .report-page [class*="col-"],
    .report-glass,
    .report-glass .card-body,
    .report-glass form {
        min-width: 0;
        height: auto;
        overflow: visible;
    }
    .report-glass {
        background: rgba(255,255,255,0.85);
        box-shadow: 0 8px 40px #a594f922, 0 1.5px 8px #a594f911;
        backdrop-filter: blur(8px);
        border: 1.5px solid #ede9fe;
        border-radius: 24px;
    }
    .report-title {
        font-size: clamp(1.55rem, 4vw, 2.1rem);
        font-weight: 900;
        letter-spacing: 1.2px;
        background: linear-gradient(90deg, #A594F9 0%, #7658df 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }
    .report-anon {
        background: linear-gradient(90deg, #f3efff 0%, #e8e0ff 100%);
        border-radius: 12px;
        border-left: 6px solid #8f72f5;
        box-shadow: 0 2px 12px #a594f922;
    }
    /* From Uiverse.io by adamgiebl - https://uiverse.io/adamgiebl/new-bird-34
       MIT License, Copyright (c) adamgiebl (Adam Giebl) */
    .report-btn {
        position:relative;
        display:flex;
        align-items:center;
        overflow:hidden;
        height:2.8em;
        padding:.35em 3.3em .35em 1.2em;
        border:0;
        border-radius:.9em;
        color:#fff;
        background:#7658df;
        box-shadow:inset 0 0 1.6em -.6em #4a2d96;
        font-size:1rem;
        font-weight:700;
        letter-spacing:.05em;
        cursor:pointer;
        transition:background-color .2s ease;
    }
    .report-btn-icon {
        position:absolute;
        right:.3em;
        display:flex;
        align-items:center;
        justify-content:center;
        width:2.2em;
        height:2.2em;
        margin-left:1em;
        border-radius:.7em;
        color:#7658df;
        background:#fff;
        box-shadow:.1em .1em .6em .2em #4a2d96;
        transition:all .3s ease;
    }
    .report-btn-icon svg { width:1.1em; transition:transform .3s ease; }
    .report-btn:hover .report-btn-icon { width:calc(100% - .6em); }
    .report-btn:hover .report-btn-icon svg { transform:translateX(.1em); }
    .report-btn:active .report-btn-icon { transform:scale(.95); }
    .report-btn:focus-visible { outline:3px solid #4a2d96; outline-offset:3px; }
    .report-help {
        background: rgba(255,255,255,0.95);
        border-radius: 15px;
        box-shadow: 0 2px 12px #a594f911;
        border: 1.5px solid #ede9fe;
    }
    @media (max-width: 575.98px) {
        .report-page { padding-inline: .75rem; }
        .report-glass { border-radius: 1rem; }
        .report-glass .card-body { padding: 1rem !important; }
    }
</style>
<div class="container-fluid py-4 report-page">
    <div class="row justify-content-center">
        <div class="col-md-10 col-lg-8">
            {{-- Header Section --}}
            <div class="mb-4 text-center">
                <img src="{{ asset('images/gadlogo.png') }}" alt="GAD Logo" class="mb-3" style="width:74px; height:74px; border-radius:50%; object-fit:cover; box-shadow:0 8px 24px rgba(124,58,237,0.25);">
                <h2 class="fw-bold report-title mb-1">File a Safe Spaces Report</h2>
                <p class="small fw-semibold mb-0" style="color:#A594F9;">This is a safe space for students and employees. We are here to listen and help.</p>
            </div>

            @if(session('success'))
                <div id="reportSuccessAlert" class="sinag-persistent-notice alert border-0 shadow-sm mb-4" style="background:#f1eaff; color:#4c2a9d; border-radius:14px; border-left:6px solid #8f72f5 !important;">
                    <div class="d-flex align-items-start gap-2">
                        <i class="bi bi-check-circle-fill mt-1" style="color:#8f72f5;"></i>
                        <div>
                            <div class="fw-bold">Report Delivered</div>
                            <div>{{ session('success') }}</div>
                            @if(session('report_case_id'))
                                <div class="mt-3 d-flex flex-column flex-md-row align-items-md-center gap-2">
                                    <span class="badge rounded-pill" style="background:#ddd1ff;color:#4c2a9d;">Case ID: {{ session('report_case_id') }}</span>
                                    <a href="{{ route('student.vault') }}" class="btn btn-sm rounded-pill px-3 text-white" style="background:#8f72f5;">
                                        <i class="bi bi-safe2 me-1"></i> Track This Case in Stealth Vault
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endif

            @if($errors->any())
                <div class="sinag-persistent-notice alert border-0 shadow-sm mb-4" style="background:#fef2f2; color:#991b1b; border-radius:14px; border-left:6px solid #ef4444 !important;">
                    <div class="d-flex align-items-start gap-2">
                        <i class="bi bi-exclamation-triangle-fill mt-1"></i>
                        <div>
                            <div class="fw-bold">Submission Error</div>
                            <ul class="mb-0 ps-3">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Main Form Card --}}
            <div class="card border-0 shadow-sm report-glass">
                <div class="card-body p-4 p-md-5">
                    <form action="{{ route('reports.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        
                        {{-- Incident Category --}}
                        <div class="mb-4">
                            <label class="form-label fw-bold small text-uppercase" style="letter-spacing: 1px; color:#A594F9;">Type of Incident</label>
                            <select id="nature-select" name="nature" class="form-select form-control-lg border-0 bg-white shadow-sm" required style="border-radius: 14px; font-size: 1rem;">
                                <option value="" selected disabled>Select a category...</option>
                                <option value="Catcalling" {{ old('nature') === 'Catcalling' ? 'selected' : '' }}>Catcalling / Wolf-whistling</option>
                                <option value="Cyber-harassment" {{ old('nature') === 'Cyber-harassment' ? 'selected' : '' }}>Cyber-harassment</option>
                                <option value="Domestic Concern Affecting School/Work" {{ old('nature') === 'Domestic Concern Affecting School/Work' ? 'selected' : '' }}>Domestic Concern Affecting School/Work</option>
                                <option value="Gender-based Discrimination" {{ old('nature') === 'Gender-based Discrimination' ? 'selected' : '' }}>Gender-based Discrimination</option>
                                <option value="Groping" {{ old('nature') === 'Groping' ? 'selected' : '' }}>Groping / Unwanted Touching</option>
                                <option value="Retaliation or Intimidation" {{ old('nature') === 'Retaliation or Intimidation' ? 'selected' : '' }}>Retaliation or Intimidation</option>
                                <option value="Sexist Remarks or Jokes" {{ old('nature') === 'Sexist Remarks or Jokes' ? 'selected' : '' }}>Sexist Remarks or Jokes</option>
                                <option value="Stalking" {{ old('nature') === 'Stalking' ? 'selected' : '' }}>Stalking</option>
                                <option value="Unequal Opportunities" {{ old('nature') === 'Unequal Opportunities' ? 'selected' : '' }}>Unequal Opportunities</option>
                                <option value="Other GAD-related Concern" {{ old('nature') === 'Other GAD-related Concern' ? 'selected' : '' }}>Other GAD-related Concern</option>
                            </select>
                            <div id="other-nature-wrapper" class="mt-3" style="display:none;">
                                <label for="other-nature-input" class="form-label fw-bold small text-uppercase" style="letter-spacing: 1px; color:#A594F9;">Specify Incident</label>
                                <input
                                    type="text"
                                    id="other-nature-input"
                                    name="other_nature"
                                    value="{{ old('other_nature') }}"
                                    class="form-control border-0 bg-white shadow-sm"
                                    placeholder="Type the specific incident type"
                                    style="border-radius: 14px; font-size: 1rem;"
                                >
                                <small class="text-muted">Required when you select Other GAD-related Concern.</small>
                            </div>
                        </div>

                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-uppercase" style="letter-spacing: 1px; color:#A594F9;">Department / Program</label>
                                <select name="course" class="form-select border-0 bg-white shadow-sm" required style="border-radius: 14px; font-size: 1rem;">
                                    @php
                                        $reportAccountType = strtolower((string) (Auth::user()->account_type ?? Auth::user()->role));
                                        $reportEmployeeCategory = strtolower((string) Auth::user()->employee_category);
                                        $reportDepartment = old('course', Auth::user()->department);
                                    @endphp
                                    @if($reportAccountType === 'employee')
                                        @if($reportEmployeeCategory === 'non_teaching')
                                            @foreach([
                                                'Non-Teaching',
                                                'Office of the Registrar',
                                                'Human Resource Office',
                                                'Finance Office',
                                                'Library',
                                                'IT Services',
                                                'Student Affairs Office',
                                            ] as $department)
                                                <option value="{{ $department }}" {{ $reportDepartment === $department ? 'selected' : '' }}>{{ $department }}</option>
                                            @endforeach
                                        @else
                                            @foreach([
                                                'College of Technology and Business',
                                                'College of Education',
                                            ] as $department)
                                                <option value="{{ $department }}" {{ $reportDepartment === $department ? 'selected' : '' }}>{{ $department }}</option>
                                            @endforeach
                                        @endif
                                    @else
                                        @foreach([
                                            'Bachelor Industrial Technology - Major in Electrical Technology',
                                            'Bachelor Industrial Technology - Major in Food Management Service',
                                            'Bachelor Industrial Technology - Major in Mechanical Technology',
                                            'Bachelor of Elementary Education (BEEd)',
                                            'Bachelor of Technology and Livelihood Education (BTLEd)',
                                            'Bachelor Secondary Education (BSEd) - Major in English',
                                            'Bachelor Secondary Education (BSEd) - Major in Math',
                                            'Bachelor Secondary Education (BSEd) - Major in Science',
                                            'BS Business Administration (BSBA)',
                                            'BS Information Technology (BSIT)',
                                        ] as $department)
                                            <option value="{{ $department }}" {{ $reportDepartment === $department ? 'selected' : '' }}>{{ $department }}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-uppercase" style="letter-spacing: 1px; color:#A594F9;">Gender</label>
                                <select name="gender" class="form-select border-0 bg-white shadow-sm" required style="border-radius: 14px; font-size: 1rem;">
                                    <option value="" disabled {{ old('gender') ? '' : 'selected' }}>Select gender...</option>
                                    <option value="Female" {{ old('gender') === 'Female' ? 'selected' : '' }}>Female</option>
                                    <option value="Male" {{ old('gender') === 'Male' ? 'selected' : '' }}>Male</option>
                                </select>
                            </div>
                        </div>

                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-uppercase" style="letter-spacing: 1px; color:#A594F9;">Incident Date</label>
                                <input
                                    type="date"
                                    name="incident_date"
                                    value="{{ old('incident_date') }}"
                                    max="{{ now()->toDateString() }}"
                                    class="form-control border-0 bg-white shadow-sm"
                                    style="border-radius: 14px; font-size: 1rem;"
                                    required
                                >
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-uppercase" style="letter-spacing: 1px; color:#A594F9;">Incident Time</label>
                                <input
                                    type="time"
                                    name="incident_time"
                                    value="{{ old('incident_time') }}"
                                    class="form-control border-0 bg-white shadow-sm"
                                    style="border-radius: 14px; font-size: 1rem;"
                                    required
                                >
                            </div>
                        </div>

                        {{-- Incident Description --}}
                        <div class="mb-4">
                            <label class="form-label fw-bold small text-uppercase" style="letter-spacing: 1px; color:#A594F9;">Detailed Description</label>
                            <textarea name="description" class="form-control border-0 bg-white shadow-sm" rows="5"
                                placeholder="Tell us what happened... (When, Where, Who)" 
                                style="border-radius: 14px; font-size: 1rem;" required>{{ old('description') }}</textarea>
                            <div class="form-text text-muted mt-2">
                                <i class="bi bi-info-circle me-1"></i> Be as specific as possible to help us assist you better.
                            </div>
                        </div>


                        {{-- Incident Location --}}
                        <div class="mb-4">
                            <label class="form-label fw-bold small text-uppercase" style="letter-spacing: 1px; color:#A594F9;">Location of Incident</label>
                            <input type="text" name="location" value="{{ old('location') }}" class="form-control border-0 bg-white shadow-sm" placeholder="Where did it happen?" style="border-radius: 14px; font-size: 1rem;" required>
                        </div>

                        {{-- Evidence Upload --}}
                        <div class="mb-4">
                            <label class="form-label fw-bold small text-uppercase" style="letter-spacing: 1px; color:#A594F9;">Attach Evidence (Optional)</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white border-0" style="border-radius: 12px 0 0 12px;">
                                    <i class="bi bi-paperclip text-secondary"></i>
                                </span>
                                <input type="file" name="evidence[]" class="form-control border-0 bg-white shadow-sm" id="inputGroupFile02" accept="image/jpeg,image/png,image/gif,image/webp,video/mp4,.jpg,.jpeg,.png,.gif,.webp,.mp4" multiple style="border-radius: 0 12px 12px 0;">
                            </div>
                            <small class="text-muted">Upload images (max 10 MB each) or MP4 videos (max 50 MB each).</small>
                        </div>

                        <hr class="my-4 opacity-25">

                        {{-- Anonymity Toggle --}}
                        <div class="p-3 mb-4 report-anon">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="is_anonymous" id="anonymousSwitch" value="1" {{ old('is_anonymous', true) ? 'checked' : '' }}>
                                <label class="form-check-label fw-bold text-dark" for="anonymousSwitch"><i class="bi bi-incognito me-1"></i>Report Anonymously</label>
                            </div>
                            <small id="anonymousHelpText" class="d-block text-muted mt-1"
                                   data-alias="Your real name will be hidden. Only your cloak alias ({{ Auth::user()->cloak_alias }}) will be shown to the GAD office."
                                   data-name="Your name ({{ Auth::user()->name }}) will be shown to the GAD office.">
                                {{ old('is_anonymous', true) ? 'Your real name will be hidden. Only your cloak alias (' . Auth::user()->cloak_alias . ') will be shown to the GAD office.' : 'Your name (' . Auth::user()->name . ') will be shown to the GAD office.' }}
                            </small>
                        </div>

                        <div class="mt-4 p-3 rounded-3 report-hotline" style="background:#f3efff; border:1px solid #d8cdfc;">
                            <p class="mb-1 fw-bold small" style="color:#5b4b9b;"><i class="bi bi-telephone-fill me-1"></i> Emergency Hotline</p>
                            <p class="mb-0 small text-muted">For immediate assistance, call GAD Emergency Hotline: <span class="fw-bold text-dark">(075) 123-4567</span></p>
                        </div>

                        {{-- Action Buttons --}}
                        <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
                            <button type="reset" class="btn btn-light px-4 fw-bold text-muted" style="border-radius: 10px;">Clear Form</button>
                            <button type="submit" class="report-btn">
                                Submit Report
                                <span class="report-btn-icon" aria-hidden="true">
                                    <svg height="24" width="24" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M0 0h24v24H0z" fill="none"></path>
                                        <path d="M16.172 11l-5.364-5.364 1.414-1.414L20 12l-7.778 7.778-1.414-1.414L16.172 13H4v-2z" fill="currentColor"></path>
                                    </svg>
                                </span>
                            </button>
                        </div>
                        <input type="hidden" name="privacy_consent" id="reportPrivacyConsent" value="{{ old('privacy_consent') }}">
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="reportPrivacyConsentModal" tabindex="-1" aria-labelledby="reportPrivacyConsentTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow">
            <div class="modal-header border-0">
                <h5 class="modal-title fw-bold" id="reportPrivacyConsentTitle">Report Terms and Data Privacy</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body pt-0">
                <p class="text-muted">Before submitting, please review how your report information is handled:</p>
                <ul class="small text-muted ps-3">
                    <li>Report details and evidence may be reviewed by authorized GAD staff for case handling.</li>
                    <li>Share only the information needed to address your concern.</li>
                    <li>Choosing anonymous does not hide identifying information included in your report.</li>
                </ul>
                <div class="form-check mt-3">
                    <input class="form-check-input" type="checkbox" id="reportPrivacyConsentCheck">
                    <label class="form-check-label fw-semibold" for="reportPrivacyConsentCheck">
                        I have read and agree to this report privacy notice.
                    </label>
                </div>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-light rounded-pill px-3" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn text-white rounded-pill px-4" id="acceptReportPrivacyConsent" style="background:#8f72f5;" disabled>
                    Agree and Continue
                </button>
            </div>
        </div>
    </div>
</div>

@if(session('success'))
<script>
    window.addEventListener('load', function () {
        const successAlert = document.getElementById('reportSuccessAlert');
        if (successAlert) {
            successAlert.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
    });
</script>
@endif

<script>
    window.addEventListener('load', function () {
        const anonymousSwitch = document.getElementById('anonymousSwitch');
        const anonymousHelpText = document.getElementById('anonymousHelpText');
        anonymousSwitch?.addEventListener('change', function () {
            if (anonymousHelpText) {
                anonymousHelpText.textContent = anonymousSwitch.checked
                    ? anonymousHelpText.dataset.alias
                    : anonymousHelpText.dataset.name;
            }
        });

        const natureSelect = document.getElementById('nature-select');
        const otherWrapper = document.getElementById('other-nature-wrapper');
        const otherInput = document.getElementById('other-nature-input');
        const otherOption = 'Other GAD-related Concern';
        const reportForm = document.querySelector('.report-glass form');
        const privacyConsentAccepted = @json($reportConsentAccepted);
        const privacyConsentInput = document.getElementById('reportPrivacyConsent');
        const privacyConsentModalElement = document.getElementById('reportPrivacyConsentModal');
        const privacyConsentModal = privacyConsentModalElement && window.bootstrap
            ? window.bootstrap.Modal.getOrCreateInstance(privacyConsentModalElement)
            : null;
        const privacyConsentCheck = document.getElementById('reportPrivacyConsentCheck');
        const acceptPrivacyConsentButton = document.getElementById('acceptReportPrivacyConsent');

        reportForm?.addEventListener('submit', function (event) {
            if (!privacyConsentAccepted && privacyConsentInput?.value !== '1') {
                event.preventDefault();
                privacyConsentModal?.show();
            }
        });

        privacyConsentCheck?.addEventListener('change', function () {
            if (acceptPrivacyConsentButton) {
                acceptPrivacyConsentButton.disabled = !privacyConsentCheck.checked;
            }
        });

        acceptPrivacyConsentButton?.addEventListener('click', function () {
            if (!privacyConsentCheck?.checked || !privacyConsentInput || !reportForm) {
                return;
            }

            privacyConsentInput.value = '1';
            privacyConsentModal?.hide();
            reportForm.requestSubmit();
        });

        @if($errors->has('privacy_consent'))
            privacyConsentModal?.show();
        @endif

        function toggleOtherNatureField() {
            const isOtherSelected = natureSelect && natureSelect.value === otherOption;

            if (otherWrapper) {
                otherWrapper.style.display = isOtherSelected ? 'block' : 'none';
            }

            if (otherInput) {
                otherInput.required = !!isOtherSelected;
                if (!isOtherSelected) {
                    otherInput.value = '';
                }
            }
        }

        if (natureSelect) {
            natureSelect.addEventListener('change', toggleOtherNatureField);
            natureSelect.form?.addEventListener('reset', function () {
                window.setTimeout(toggleOtherNatureField, 0);
            });
            toggleOtherNatureField();
        }
    });
</script>
@endsection