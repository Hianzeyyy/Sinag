@extends('layouts.app')

@section('content')
<style>
    .sinag-violet-badge { background: #d8cdfc; color: #5b4b9b; }
    .sinag-yellow-badge { background: #f8cb12; color: #4a3500; }

    .admin-dashboard-page {
        --accent: #A594F9;
        --accent-deep: #8f72f5;
        --ink: #111827;
        --muted: #6b7280;
        background:
            radial-gradient(circle at top left, rgba(165, 148, 249, 0.12), transparent 34%),
            radial-gradient(circle at right top, rgba(124, 92, 203, 0.12), transparent 22%),
            linear-gradient(180deg, #faf7ff 0%, #f8fafc 62%, #f8fafc 100%);
        border-radius: 1.45rem;
        padding: 1rem;
        animation: adminDashboardReveal .55s ease-out both;
    }

    .admin-dashboard-wrap > * {
        animation: adminDashboardReveal .55s ease-out both;
    }

    .admin-dashboard-wrap > *:nth-child(2) { animation-delay: .08s; }
    .admin-dashboard-wrap > *:nth-child(3) { animation-delay: .16s; }
    .admin-dashboard-wrap > *:nth-child(4) { animation-delay: .24s; }

    @keyframes adminDashboardReveal {
        from {
            opacity: 0;
        }
        to {
            opacity: 1;
        }
    }

    @media (prefers-reduced-motion: reduce) {
        .admin-dashboard-page,
        .admin-dashboard-wrap > * {
            animation: none;
        }
    }

    .admin-dashboard-wrap {
        max-width: 1440px;
        margin: 0 auto;
    }

    .admin-hero {
        border: 0;
        border-radius: 1.4rem;
        overflow: hidden;
        color: #35216d;
        background: linear-gradient(135deg, #f1eaff 0%, #d9c8ff 52%, #c7b5ff 100%);
        box-shadow: 0 20px 42px rgba(165, 148, 249, 0.22);
    }

    .hero-kicker {
        display: inline-flex;
        align-items: center;
        gap: 0.45rem;
        padding: 0.4rem 0.75rem;
        border-radius: 999px;
        font-size: 0.75rem;
        font-weight: 800;
        letter-spacing: 0.08em;
        text-transform: uppercase;
        color: #5b4b9b;
        background: rgba(255, 255, 255, 0.62);
    }

    .hero-title {
        font-weight: 900;
        font-size: clamp(1.9rem, 3vw, 2.6rem);
        line-height: 1.05;
    }

    .hero-copy {
        color: #5b4b9b;
        max-width: 40rem;
    }

    .hero-action {
        border: 0;
        border-radius: 999px;
        background: #8f72f5;
        color: #fff;
        font-weight: 800;
        box-shadow: 0 12px 22px rgba(17, 24, 39, 0.2);
    }

    .hero-action:hover {
        color: #fff;
    }

    .metric-card {
        border: 1px solid rgba(143, 114, 245, 0.14);
        border-radius: 1.1rem;
        color: #35216d;
        text-decoration: none;
        box-shadow: 0 14px 30px rgba(15, 23, 42, 0.14);
        overflow: hidden;
        transition: transform .22s ease, box-shadow .22s ease;
    }

    .metric-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 18px 34px rgba(143, 114, 245, 0.24);
        color: #35216d;
    }

    .metric-card .card-body {
        position: relative;
    }

    .metric-card .text-white,
    .metric-card .text-white-50 {
        color: #35216d !important;
    }

    .metric-card .metric-value {
        color: #4c2a9d;
    }

    .metric-card .card-body::after {
        content: '';
        position: absolute;
        width: 6rem;
        height: 6rem;
        border-radius: 999px;
        right: -2rem;
        top: -2rem;
        background: rgba(255, 255, 255, 0.16);
    }

    .metric-label {
        font-size: 0.72rem;
        letter-spacing: 0.09em;
    }

    .dashboard-panel {
        border: 1px solid rgba(165, 148, 249, 0.14);
        border-radius: 1.2rem;
        box-shadow: 0 14px 34px rgba(165, 148, 249, 0.1);
    }

    .dashboard-panel .card-header {
        background: #fff;
    }

    .reports-table thead {
        background: #f8fafc;
    }

    .reports-table tbody tr:hover {
        background-color: #fbfaff !important;
    }

    .review-btn {
        border: 1px solid #A594F9;
        color: #8f72f5;
        font-weight: 700;
    }

    .review-btn:hover {
        background: #A594F9;
        color: #fff;
    }

    .admin-tool-btn {
        transition: transform 0.2s, box-shadow 0.2s;
        border: 1px solid rgba(165, 148, 249, 0.12);
    }

    .admin-tool-btn:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1) !important;
    }

    .tool-icon {
        width: 2.35rem;
        height: 2.35rem;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 999px;
    }

    .command-tools-card {
        overflow: hidden;
        border-color: rgba(165, 148, 249, 0.18);
        box-shadow: 0 16px 36px rgba(165, 148, 249, 0.12);
    }

    .command-tools-head {
        background: linear-gradient(135deg, rgba(165, 148, 249, 0.18), rgba(196, 181, 253, 0.18));
        border: 1px solid rgba(165, 148, 249, 0.2);
        border-radius: 1rem;
    }

    .command-tools-title {
        font-size: 1.08rem;
        color: #111827;
        font-weight: 900;
    }

    .command-tools-sub {
        color: #6b7280;
        font-size: 0.82rem;
    }

    .survey-choice {
        display: flex;
        align-items: center;
        gap: .55rem;
        padding: .65rem .8rem;
        border: 1px solid #e5e7eb;
        border-radius: .65rem;
        cursor: pointer;
    }

    .survey-choice:has(input:checked) {
        color: #4c2a9d;
        background: #f1eaff;
        border-color: #a594f9;
    }

    .admin-tool-btn {
        position: relative;
        overflow: hidden;
        border: 1px solid rgba(165, 148, 249, 0.18);
        box-shadow: 0 8px 16px rgba(15, 23, 42, 0.06);
    }

    .admin-tool-btn::after {
        content: '';
        position: absolute;
        width: 4.25rem;
        height: 4.25rem;
        border-radius: 999px;
        right: -1.75rem;
        top: -1.75rem;
        background: rgba(255, 255, 255, 0.28);
        pointer-events: none;
    }

    .admin-tool-btn .text-muted {
        color: #334155 !important;
        opacity: 0.9;
    }

    .admin-tool-users {
        background: linear-gradient(135deg, #f5f3ff 0%, #e9d5ff 100%) !important;
    }

    .admin-tool-boses {
        background: linear-gradient(135deg, #eef2ff 0%, #ddd6fe 100%) !important;
    }

    .admin-tool-announcements {
        background: linear-gradient(135deg, #f8fafc 0%, #ede9fe 100%) !important;
    }

    .admin-tool-arrow {
        width: 1.8rem;
        height: 1.8rem;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 999px;
        background: rgba(255, 255, 255, 0.85);
        color: #8f72f5;
        transition: transform 0.2s ease;
        flex: 0 0 auto;
    }

    .admin-tool-btn:hover .admin-tool-arrow {
        transform: translateX(2px);
    }

    .privacy-box {
        border: 1px solid rgba(255, 255, 255, 0.2);
        box-shadow: inset 0 0 0 1px rgba(255, 255, 255, 0.05);
        background: linear-gradient(135deg, #A594F9 0%, #c4b5fd 100%) !important;
    }

    .badge {
        font-size: 0.7rem;
        font-weight: 600;
    }

    @media (max-width: 991px) {
        .admin-dashboard-page {
            padding: 0.55rem;
        }
    }
</style>

<div class="container-fluid admin-dashboard-page">
    <div class="admin-dashboard-wrap">
        @if(session('success'))
            <div class="alert alert-success border-0 shadow-sm rounded-4 mb-4 d-flex align-items-center justify-content-between p-3" role="alert" style="background: #eefdf3; color: #166534;">
                <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-check-circle-fill text-success fs-5"></i>
                    <span class="fw-medium">{{ session('success') }}</span>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="card admin-hero mb-4">
            <div class="card-body p-4 p-lg-5">
                <div class="d-flex flex-column flex-lg-row align-items-lg-center justify-content-between gap-3">
                    <div>
                        <span class="hero-kicker mb-3"><i class="bi bi-shield-check"></i> GAD Command Center</span>
                        <h2 class="hero-title mb-2">GAD Office Analytics</h2>
                        <p class="hero-copy mb-0">Real-time monitoring of campus safety, incident escalation, and response status across the reporting system.</p>
                    </div>
                    <div class="text-lg-end">
                        <button class="btn hero-action px-4 py-2" type="button" data-bs-toggle="modal" data-bs-target="#exportGadFormatModal">
                            <i class="bi bi-file-earmark-arrow-down me-2"></i>Export GAD Report
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-md-6 col-xl-3">
                <a href="{{ route('admin.reports') }}" class="card metric-card h-100" style="background: linear-gradient(135deg, #f1eaff 0%, #d9c8ff 100%);">
                    <div class="card-body text-white">
                        <h6 class="text-white-50 fw-bold text-uppercase metric-label mb-2">Total Reports</h6>
                        <h2 class="metric-value fw-bold mb-1" data-counter="{{ $totalReports ?? 0 }}">0</h2>
                        <small class="text-white-50">Active database records</small>
                    </div>
                </a>
            </div>
            <div class="col-md-6 col-xl-3">
                <a href="{{ route('admin.reports', ['status' => 'Pending']) }}" class="card metric-card h-100" style="background: linear-gradient(135deg, #e8ddff 0%, #cbb8ff 100%);">
                    <div class="card-body text-white">
                        <h6 class="text-white-50 fw-bold text-uppercase metric-label mb-2">Pending Review</h6>
                        <h2 class="metric-value fw-bold mb-1" data-counter="{{ $pendingReports ?? 0 }}">0</h2>
                        <small class="text-white-50">Requires attention</small>
                    </div>
                </a>
            </div>
            <div class="col-md-6 col-xl-3">
                <a href="{{ route('admin.reports', ['status' => 'Resolved']) }}" class="card metric-card h-100" style="background: linear-gradient(135deg, #eee8ff 0%, #d5c7ff 100%);">
                    <div class="card-body text-white">
                        <h6 class="text-white-50 fw-bold text-uppercase metric-label mb-2">Resolved Cases</h6>
                        <h2 class="metric-value fw-bold mb-1" data-counter="{{ $resolvedReports ?? 0 }}">0</h2>
                        <small class="text-white-50">Completed actions</small>
                    </div>
                </a>
            </div>
            <div class="col-md-6 col-xl-3">
                <a href="{{ route('admin.suggestions') }}" class="card metric-card h-100" style="background: linear-gradient(135deg, #e5dcff 0%, #c7b5ff 100%);">
                    <div class="card-body text-white">
                        <h6 class="text-white-50 fw-bold text-uppercase metric-label mb-2">Suggestions</h6>
                        <h2 class="metric-value fw-bold mb-1" data-counter="{{ $totalSuggestions ?? 0 }}">0</h2>
                        <small class="text-white-50">Participatory suggestions feedback</small>
                    </div>

                    <script>
                        document.querySelectorAll('[data-counter]').forEach(function (counter) {
                            const target = Number(counter.dataset.counter || 0);
                            const duration = 700;
                            const startedAt = performance.now();
                            function tick(now) {
                                const progress = Math.min((now - startedAt) / duration, 1);
                                counter.textContent = Math.floor(progress * target).toLocaleString();
                                if (progress < 1) window.requestAnimationFrame(tick);
                            }
                            window.requestAnimationFrame(tick);
                        });
                    </script>
                </a>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-lg-8">
                <div class="card dashboard-panel overflow-hidden h-100">
                    <div class="card-header py-3 border-0">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0 fw-bold text-dark"><i class="bi bi-inbox-fill me-2" style="color: #8f72f5;"></i>Recent Reports</h5>
                            <a href="{{ route('admin.reports') }}" class="btn btn-sm text-decoration-none fw-bold" style="color: #8f72f5;">See All <i class="bi bi-arrow-right"></i></a>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0 reports-table">
                                <thead>
                                    <tr class="small text-muted text-uppercase">
                                        <th class="ps-4">Incident ID</th>
                                        <th>Nature</th>
                                        <th>Status</th>
                                        <th class="text-end pe-4">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($recentReports ?? [] as $report)
                                    <tr>
                                        <td class="ps-4 fw-bold">#{{ $report->incident_id }}</td>
                                        <td class="small">{{ $report->nature }}</td>
                                        <td>
                                            <span class="badge rounded-pill {{ $report->status == 'Pending' ? 'sinag-yellow-badge' : 'sinag-violet-badge' }}">
                                                {{ $report->status }}
                                            </span>
                                        </td>
                                        <td class="text-end pe-4">
                                            <a href="{{ route('admin.reports') }}" class="btn btn-sm rounded-pill px-3 py-1 review-btn">Review</a>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="4" class="text-center py-4 text-muted small">No recent reports found.</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card dashboard-panel h-100 command-tools-card">
                    <div class="card-header py-3 border-0">
                        <div class="command-tools-head d-flex align-items-center justify-content-between gap-3 p-3">
                            <div>
                                <div class="command-tools-title mb-1">Command Tools</div>
                                <div class="command-tools-sub">Quick admin actions and controls</div>
                            </div>
                            <span class="tool-icon" style="background: #e9d5ff; width:2.1rem; height:2.1rem;">
                                <i class="bi bi-lightning-fill" style="color:#8f72f5;"></i>
                            </span>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-3">
                            <a href="{{ route('admin.users') }}" class="btn border-0 text-start py-3 px-4 rounded-4 shadow-sm admin-tool-btn admin-tool-users">
                                <div class="d-flex align-items-center">
                                    <div class="tool-icon me-3" style="background: rgba(255,255,255,0.7);">
                                        <i class="bi bi-people-fill" style="color: #8f72f5;"></i>
                                    </div>
                                    <div>
                                        <span class="d-block fw-bold text-dark small">User Management</span>
                                        <small class="text-muted">Manage GAD personnel</small>
                                    </div>
                                    <span class="admin-tool-arrow ms-auto"><i class="bi bi-arrow-right-short"></i></span>
                                </div>
                            </a>

                            <a href="{{ route('admin.suggestions') }}" class="btn border-0 text-start py-3 px-4 rounded-4 shadow-sm admin-tool-btn admin-tool-boses">
                                <div class="d-flex align-items-center">
                                    <div class="tool-icon me-3" style="background: rgba(255,255,255,0.7);">
                                        <i class="bi bi-chat-square-dots-fill" style="color:#8f72f5;"></i>
                                    </div>
                                    <div>
                                        <span class="d-block fw-bold text-dark small">Participatory Suggestions</span>
                                        <small class="text-muted">Review safe space feedback</small>
                                    </div>
                                    <span class="admin-tool-arrow ms-auto"><i class="bi bi-arrow-right-short"></i></span>
                                </div>
                            </a>

                            <a href="{{ route('admin.gad-schedules.index') }}" class="btn border-0 text-start py-3 px-4 rounded-4 shadow-sm admin-tool-btn" style="background: linear-gradient(135deg, #f8fafc 0%, #eef2ff 100%);">
                                <div class="d-flex align-items-center">
                                    <div class="tool-icon me-3" style="background: rgba(255,255,255,0.8);">
                                        <i class="bi bi-calendar2-week-fill" style="color:#8f72f5;"></i>
                                    </div>
                                    <div>
                                        <span class="d-block fw-bold text-dark small">Office Schedule</span>
                                        <small class="text-muted">Set live meeting availability</small>
                                    </div>
                                    <span class="admin-tool-arrow ms-auto"><i class="bi bi-arrow-right-short"></i></span>
                                </div>
                            </a>

                            <button type="button" class="btn border-0 text-start py-3 px-4 rounded-4 shadow-sm admin-tool-btn" data-bs-toggle="modal" data-bs-target="#editOfficeInfoModal" style="background: linear-gradient(135deg, #f5f3ff 0%, #ede9fe 100%);">
                                <div class="d-flex align-items-center">
                                    <div class="tool-icon me-3" style="background: rgba(255,255,255,0.85);">
                                        <i class="bi bi-telephone-inbound-fill" style="color:#7c3aed;"></i>
                                    </div>
                                    <div>
                                        <span class="d-block fw-bold text-dark small">Office Info & Hotline</span>
                                        <small class="text-muted">{{ $officeInfo['phone_number'] ?? 'Set hotline number' }}</small>
                                    </div>
                                    <span class="admin-tool-arrow ms-auto"><i class="bi bi-pencil-fill" style="font-size: 0.75rem;"></i></span>
                                </div>
                            </button>

                            <div class="p-4 mt-2 rounded-4 text-white privacy-box" style="background: linear-gradient(135deg, #A594F9 0%, #8f72f5 100%);">
                                <h6 class="fw-bold mb-2 small"><i class="bi bi-shield-check me-1"></i>Privacy Protocol</h6>
                                <p class="mb-0" style="font-size: 0.75rem; opacity: 0.82;">
                                    Cloak Protocol is currently encrypting 12 reporter identities. Data access is being logged.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="exportGadFormatModal" tabindex="-1" aria-labelledby="exportGadFormatModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow-lg">
            <div class="modal-header border-0">
                <div>
                    <h2 class="modal-title h5 fw-bold mb-1" id="exportGadFormatModalLabel">Choose file format</h2>
                    <p class="small text-muted mb-0">Select PDF or Excel before choosing the date range.</p>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="exportGadReportForm" method="GET" data-export-url-template="{{ route('admin.gad-report.export', ['format' => '__FORMAT__', 'period' => '__PERIOD__']) }}">
                <div class="modal-body pt-0" id="exportGadDataStep">
                    <label class="form-label fw-semibold">What would you like to export?</label>
                    <div class="d-grid gap-2">
                        <label class="survey-choice">
                            <input type="checkbox" name="include_reports" value="1" checked>
                            <span>Incident reports</span>
                        </label>
                        <label class="survey-choice">
                            <input type="checkbox" name="include_feedback" value="1" checked>
                            <span>Customer feedback and surveys</span>
                        </label>
                    </div>
                    <div class="small text-danger mt-2 d-none" id="exportGadDataError">Select at least one export section.</div>
                </div>
                <div class="modal-body pt-0 d-none" id="exportGadFormatStep" aria-hidden="true">
                    <label for="exportGadReportFormat" class="form-label fw-semibold">File format</label>
                    <select id="exportGadReportFormat" class="form-select">
                        <option value="pdf">PDF</option>
                        <option value="excel">Excel (.xlsx)</option>
                    </select>
                </div>
                <div class="modal-body pt-0 d-none" id="exportGadPeriodStep" aria-hidden="true">
                    <label for="exportGadReportPeriod" class="form-label fw-semibold">Date range</label>
                    <select id="exportGadReportPeriod" class="form-select" required>
                        <option value="30days">Last 30 days</option>
                        <option value="7days">Last 7 days</option>
                        <option value="lastyear">Last year</option>
                    </select>
                </div>
                <div class="modal-footer border-0" id="exportGadDataActions">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" id="continueGadData" class="btn rounded-pill px-4 text-white" style="background:#8f72f5;">Continue</button>
                </div>
                <div class="modal-footer border-0 d-none" id="exportGadFormatActions" aria-hidden="true">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" id="continueGadExport" class="btn rounded-pill px-4 text-white" style="background:#8f72f5;">Continue</button>
                </div>
                <div class="modal-footer border-0 d-none" id="exportGadPeriodActions" aria-hidden="true">
                    <button type="button" id="backToGadExportFormat" class="btn btn-light rounded-pill px-4">Back</button>
                    <button type="submit" class="btn rounded-pill px-4 text-white" style="background:#8f72f5;">Export</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const formatStep = document.getElementById('exportGadFormatStep');
        const dataStep = document.getElementById('exportGadDataStep');
        const dataActions = document.getElementById('exportGadDataActions');
        const periodStep = document.getElementById('exportGadPeriodStep');
        const dataError = document.getElementById('exportGadDataError');
        const formatActions = document.getElementById('exportGadFormatActions');
        const periodActions = document.getElementById('exportGadPeriodActions');
        const title = document.getElementById('exportGadFormatModalLabel');
        const formatTitle = 'Choose file format';
        const periodTitle = 'Choose date range';

        function showExportStep(step) {
            const showData = step === 'data';
            const showFormat = step === 'format';
            const showPeriod = step === 'period';
            dataStep.classList.toggle('d-none', !showData);
            dataStep.setAttribute('aria-hidden', String(!showData));
            dataActions.classList.toggle('d-none', !showData);
            dataActions.setAttribute('aria-hidden', String(!showData));
            formatStep.classList.toggle('d-none', !showFormat);
            formatStep.setAttribute('aria-hidden', String(!showFormat));
            formatActions.classList.toggle('d-none', !showFormat);
            formatActions.setAttribute('aria-hidden', String(!showFormat));
            periodStep.classList.toggle('d-none', !showPeriod);
            periodStep.setAttribute('aria-hidden', String(!showPeriod));
            periodActions.classList.toggle('d-none', !showPeriod);
            periodActions.setAttribute('aria-hidden', String(!showPeriod));
            title.textContent = showData ? 'Choose export sections' : (showPeriod ? periodTitle : formatTitle);
        }

        showExportStep('data');
        document.getElementById('continueGadData').addEventListener('click', function () {
            const selected = document.querySelectorAll('#exportGadDataStep input[type="checkbox"]:checked').length;
            dataError.classList.toggle('d-none', selected > 0);
            if (selected > 0) showExportStep('format');
        });
        document.getElementById('continueGadExport').addEventListener('click', function () {
            showExportStep('period');
        });
        document.getElementById('backToGadExportFormat').addEventListener('click', function () {
            showExportStep('format');
        });
        document.getElementById('exportGadReportForm').addEventListener('submit', function (event) {
            const form = event.currentTarget;
            const format = document.getElementById('exportGadReportFormat').value;
            const period = document.getElementById('exportGadReportPeriod').value;
            form.action = form.dataset.exportUrlTemplate
                .replace('__FORMAT__', encodeURIComponent(format))
                .replace('__PERIOD__', encodeURIComponent(period));
        });

        document.getElementById('exportGadFormatModal').addEventListener('hidden.bs.modal', function () {
            showExportStep('data');
            dataError.classList.add('d-none');
        });
    });
</script>

<div class="modal fade" id="editOfficeInfoModal" tabindex="-1" aria-labelledby="editOfficeInfoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow-lg overflow-hidden">
            <div class="modal-header border-0 p-4 pb-3" style="background: linear-gradient(135deg, #8f72f5 0%, #a594f9 100%); color: #fff;">
                <div>
                    <h5 class="modal-title fw-bold mb-1" id="editOfficeInfoModalLabel"><i class="bi bi-building-gear me-2"></i>GAD Office Info & Contact</h5>
                    <p class="small mb-0 text-white-50">Manage the contact hotline and office details shown to students and faculty.</p>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.office-info.update') }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-uppercase text-muted">GAD Hotline / Phone Number <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0"><i class="bi bi-telephone text-primary"></i></span>
                            <input type="text" name="phone_number" class="form-control bg-light border-start-0 ps-0" value="{{ old('phone_number', $officeInfo['phone_number'] ?? auth()->user()->phone_number ?? '0919-777-7377') }}" placeholder="e.g. 0919-777-7377 or (075) 653-9000" required>
                        </div>
                        <small class="text-muted">This phone number will be clickable (click-to-call) for students and faculty.</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold text-uppercase text-muted">Office Location / Room</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0"><i class="bi bi-geo-alt text-primary"></i></span>
                            <input type="text" name="office_location" class="form-control bg-light border-start-0 ps-0" value="{{ old('office_location', $officeInfo['office_location'] ?? 'Admin Building, Room 105') }}" placeholder="e.g. Admin Building, Room 105">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold text-uppercase text-muted">Office Hours</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0"><i class="bi bi-clock text-primary"></i></span>
                            <input type="text" name="office_hours" class="form-control bg-light border-start-0 ps-0" value="{{ old('office_hours', $officeInfo['office_hours'] ?? 'Mon – Fri, 8:00 AM – 5:00 PM') }}" placeholder="e.g. Mon – Fri, 8:00 AM – 5:00 PM">
                        </div>
                    </div>

                    <div class="mb-2">
                        <label class="form-label small fw-bold text-uppercase text-muted">Official GAD Email</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0"><i class="bi bi-envelope text-primary"></i></span>
                            <input type="email" name="office_email" class="form-control bg-light border-start-0 ps-0" value="{{ old('office_email', $officeInfo['office_email'] ?? auth()->user()->email ?? 'gad@psu.edu.ph') }}" placeholder="e.g. gad@psu.edu.ph">
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 bg-light px-4 py-3">
                    <button type="button" class="btn btn-outline-secondary rounded-3 px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn text-white rounded-3 px-4" style="background: #8f72f5;"><i class="bi bi-check2-circle me-1"></i>Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection