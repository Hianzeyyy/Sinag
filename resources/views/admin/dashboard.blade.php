@extends('layouts.app')

@section('content')
<style>
    .admin-dashboard-page {
        --purple: #7658df;
        --purple-dark: #4a2d96;
        --purple-soft: #f2edff;
        padding: .5rem;
        animation: dashboardReveal .4s ease-out both;
    }

    @keyframes dashboardReveal {
        from { opacity: 0; transform: translateY(8px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .admin-dashboard-wrap { max-width: 1540px; margin: 0 auto; }
    .dashboard-greeting { color: #28164f; }
    .dashboard-greeting h1 { font-weight: 900; letter-spacing: -.035em; font-size: clamp(1.7rem, 3vw, 2.35rem); }
    .dashboard-greeting p { color: #756c89; }
    /* From Uiverse.io by Gautammsharma - https://uiverse.io/Gautammsharma/wicked-cobra-3
       MIT License, Copyright (c) Gautammsharma (Gautam Sharma) */
    .dashboard-export-button { display:inline-block; padding:.55rem 1.15rem; background:#7658df; color:#fff; border:2px solid #34205f; border-radius:.65rem; box-shadow:4px 4px 0 #34205f; font-weight:800; transition:all .3s ease; cursor:pointer; }
    .dashboard-export-button:hover { color:#7658df; background:#fff; border-color:#7658df; box-shadow:4px 4px 0 #7658df; }
    .dashboard-export-button:active { background:#facc15; box-shadow:none; transform:translateY(4px); }
    .dashboard-card { background:#fff; border:1px solid #eee9f8; border-radius:1.25rem; box-shadow:0 12px 30px rgba(48,27,90,.07); height:100%; overflow:hidden; transition:transform .2s ease, box-shadow .2s ease, background-color .3s ease; }
    .dashboard-card:hover { box-shadow:0 17px 36px rgba(76,42,157,.12); }
    .dashboard-card-head { display:flex; align-items:flex-start; justify-content:space-between; gap:1rem; padding:1.15rem 1.25rem .4rem; }
    .dashboard-card-head h2 { font-size:1rem; font-weight:850; margin:0; color:#32224f; }
    .dashboard-card-head p { font-size:.78rem; color:#8a8298; margin:.25rem 0 0; }
    .dashboard-card-body { padding:1rem 1.25rem 1.25rem; }
    .recent-user-card { display:block; color:inherit; text-decoration:none; }
    .recent-user-card:hover { color:inherit; }
    .avatar-orbit { position:relative; min-height:8rem; margin:.15rem 0 .6rem; overflow:hidden; }
    .recent-user-avatar { display:inline-flex; align-items:center; justify-content:center; width:var(--avatar-size); height:var(--avatar-size); position:absolute; left:var(--avatar-left); top:var(--avatar-top); z-index:var(--avatar-index); border:3px solid #fff; border-radius:50%; overflow:hidden; background:#eee8ff; box-shadow:0 8px 18px rgba(49,31,86,.15); animation:avatarFloat 3.6s ease-in-out infinite; transition:transform .2s ease,box-shadow .2s ease; }
    .recent-user-avatar:nth-child(2) { animation-delay:.35s; }
    .recent-user-avatar:nth-child(3) { animation-delay:.7s; }
    .recent-user-avatar:nth-child(4) { animation-delay:1.05s; }
    .recent-user-avatar:nth-child(5) { animation-delay:1.4s; }
    .recent-user-avatar:hover { z-index:6; transform:scale(1.13) !important; box-shadow:0 12px 25px rgba(76,42,157,.28); }
    .recent-user-avatar img { width:100%;height:100%;object-fit:cover; }
    @keyframes avatarFloat { 0%,100% { transform:translateY(0); } 50% { transform:translateY(-5px); } }
    .resolved-ratio { display:flex; align-items:baseline; gap:.3rem; margin:.35rem 0 .7rem; color:var(--purple-dark); }
    .resolved-ratio strong { font-size:2.55rem; font-weight:900; letter-spacing:-.06em; }
    .resolved-ratio span { font-weight:700; color:#958aa9; }
    /* From Uiverse.io by themrsami - https://uiverse.io/themrsami/lovely-yak-39
       MIT License, Copyright (c) themrsami (themrsami) */
    .resolved-cases-card { position:relative; isolation:isolate; border:0; background:linear-gradient(120deg,#7658df,#a594f9 52%,#db8db8); padding:2px; }
    .resolved-cases-card::before { content:""; position:absolute; inset:0; z-index:-1; border-radius:inherit; background:linear-gradient(120deg,#7658df,#a594f9 52%,#db8db8); opacity:.24; filter:blur(8px); transition:opacity .3s ease; }
    .resolved-cases-card:hover::before { opacity:.48; }
    .resolved-cases-card-inner { height:100%; min-height:10.4rem; border-radius:calc(1.25rem - 2px); background:#fff; }
    .resolved-cases-card .dashboard-card-head { padding-top:1.05rem; }
    .resolved-cases-card .dashboard-card-body { padding-top:.7rem; }
    body[data-admin-theme="dark"] .resolved-cases-card-inner { background:#1b1529; }
    body[data-admin-theme="dark"] .resolved-cases-card .resolved-ratio { color:#d5c6ff; }
    .ratio-track,.analytics-track { height:9px; background:#eee9f7; border-radius:999px; overflow:hidden; }
    .ratio-track span,.analytics-track span { display:block; height:100%; width:var(--bar-width); border-radius:inherit; transform:scaleX(0); transform-origin:left; background:linear-gradient(90deg,#4a2d96,#8f72f5); animation:barGrow .85s .1s ease-out forwards; }
    @keyframes barGrow { to { transform:scaleX(1); } }
    .category-flip-card { min-height:24rem; perspective:1100px; }
    .category-flip-inner { position:relative; height:100%; min-height:24rem; transform-style:preserve-3d; transition:transform .65s cubic-bezier(.2,.72,.25,1); }
    .category-flip-card.is-flipped .category-flip-inner { transform:rotateY(180deg); }
    .category-flip-face { position:absolute; inset:0; display:flex; flex-direction:column; min-height:24rem; backface-visibility:hidden; -webkit-backface-visibility:hidden; }
    .category-flip-back { transform:rotateY(180deg); }
    .category-flip-face .dashboard-card-body { flex:1; min-height:0; }
    .dashboard-chart-toggle { display:grid; place-items:center; width:2.25rem; height:2.25rem; flex:0 0 auto; border:1px solid #e8e1f5; border-radius:.75rem; background:#f7f4ff; color:#6b4bd4; transition:transform .18s ease,background-color .18s ease; }
    .dashboard-chart-toggle:hover { transform:scale(1.08); background:#eee8ff; }
    .category-pie-layout { display:grid; grid-template-columns:minmax(110px, 42%) 1fr; align-items:center; gap:1rem; height:100%; }
    .category-pie { display:block; width:min(100%, 180px); margin:auto; overflow:visible; }
    .category-pie-track { fill:none; stroke:#eee9f7; stroke-width:8; }
    .category-pie-segment { fill:none; stroke-width:8; }
    .category-pie-center { fill:#fff; }
    .category-pie-total { fill:#4a2d96; font-size:5px; font-weight:800; text-anchor:middle; }
    .category-pie-caption { fill:#8a8298; font-size:2.3px; text-anchor:middle; }
    .category-pie-legend { display:grid; gap:.45rem; max-height:15rem; overflow:auto; padding-right:.25rem; }
    .category-pie-legend-item { display:flex; align-items:center; justify-content:space-between; gap:.65rem; color:#635a71; font-size:.75rem; }
    .category-pie-label { display:flex; align-items:center; gap:.45rem; min-width:0; }
    .category-pie-label span:last-child { overflow:hidden; text-overflow:ellipsis; white-space:nowrap; }
    .category-pie-dot { width:.65rem; height:.65rem; flex:0 0 auto; border-radius:50%; }
    .appointments-list { display:grid; gap:.5rem; }
    .appointment-row { display:flex; align-items:center; gap:.7rem; text-decoration:none; padding:.55rem .65rem; border-radius:.85rem; color:#34284c; transition:background .18s ease,transform .18s ease; }
    .appointment-row:hover { background:#f6f2ff; color:#4a2d96; transform:translateX(3px); }
    .appointment-date-chip { width:3.1rem; min-width:3.1rem; text-align:center; border-radius:.7rem; padding:.4rem .2rem; background:#f1eaff; color:#7658df; line-height:1.1; }
    .appointment-date-chip strong { display:block; font-size:1.05rem; }
    .appointment-date-chip small { display:block; font-size:.58rem; font-weight:800; text-transform:uppercase; }
    .appointment-person { min-width:0; flex:1; }
    .appointment-person strong,.appointment-person small { display:block; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; }
    .appointment-person small { color:#877e94; font-size:.74rem; }
    .dashboard-analytics-row { display:flex; justify-content:space-between; gap:1rem; font-size:.79rem; margin-bottom:.35rem; color:#635a71; }
    .dashboard-analytics-row strong { color:#483675; }
    .dashboard-analytics-item { margin-bottom:1rem; }
    .feedback-score { display:flex; align-items:center; gap:.7rem; color:#4a2d96; }
    .feedback-score strong { font-size:2.3rem; line-height:1; font-weight:900; }
    .feedback-stars { color:#e6b800; letter-spacing:.05em; }
    .feedback-entry { border-top:1px solid #f0edf5; padding-top:.7rem; margin-top:.7rem; }
    .feedback-entry p { font-size:.75rem; color:#6f667c; margin:.35rem 0 0; }
    /* From Uiverse.io by Yaya12085 - https://uiverse.io/Yaya12085/tall-goose-27
       MIT License, Copyright (c) Yaya12085 (Yaya Mohamed) */
    .recent-suggestion-card { position:relative; max-width:100%; padding:1rem; border-radius:1rem; color:#c0c3d7; background:#30344c; box-shadow:4px 4px 20px rgba(0,0,0,.2); transition:transform .2s ease,box-shadow .2s ease; }
    .recent-suggestion-card:hover { transform:translateY(-3px); box-shadow:5px 9px 24px rgba(48,52,76,.26); }
    .recent-suggestion-card p { margin:0 0 .4rem; color:#f0efff; white-space:pre-line; overflow-wrap:anywhere; line-height:1.5; }
    .recent-suggestion-meta { display:flex; align-items:center; justify-content:space-between; gap:.75rem; color:#c6e1ed; font-size:.78rem; font-weight:700; }
    .recent-suggestion-footer { display:flex; align-items:center; justify-content:space-between; gap:.75rem; margin-top:.7rem; padding-top:.65rem; border-top:1px solid rgba(255,255,255,.14); color:#c0c3d7; font-size:.75rem; }
    .admin-dashboard-table { min-width:650px; }
    .admin-dashboard-table thead th { background:#f7f4ff; color:#635188; font-size:.69rem; text-transform:uppercase; letter-spacing:.08em; border:0; padding:1rem; }
    .admin-dashboard-table tbody td { border-color:#f0edf5; padding:1rem; }
    .admin-dashboard-table tbody tr:hover { background:#fcfaff; }
    .dashboard-report-status { font-weight:800; }
    .dashboard-report-status.status-warning { background:#f8cb12; color:#4a3500; }
    .dashboard-report-status.status-info { background:#c9e8ff; color:#174b70; }
    .dashboard-report-status.status-success { background:#c9f0dc; color:#145c38; }
    .dashboard-report-status.status-danger { background:#f7c4dd; color:#7e204b; }
    .dashboard-report-status.status-secondary { background:#e9e3f2; color:#4d435b; }
    .dashboard-link { color:#7658df; font-size:.78rem; font-weight:800; text-decoration:none; white-space:nowrap; }
    .dashboard-link:hover { color:#4a2d96; }
    .export-section-choice { display:flex; align-items:center; gap:.7rem; padding:.75rem .85rem; border:1px solid #e8e1f5; border-radius:.8rem; cursor:pointer; }
    .export-section-choice:has(input:checked) { background:#f4f0ff; border-color:#a594f9; color:#4a2d96; }
    .export-preview-list { display:grid; grid-template-columns:repeat(2,minmax(0,1fr)); gap:.65rem; }
    .export-preview-item { padding:.75rem; background:#f7f4ff; border:1px solid #eee8fb; border-radius:.8rem; }
    .export-file-preview { display:block;width:100%;height:min(62vh,760px);min-height:320px;border:1px solid #e8e1f5;border-radius:1rem;background:#fff; }
    body[data-admin-theme="dark"] .export-file-preview { border-color:#3a2d50; }
    .export-preview-item small { color:#81788d; display:block; text-transform:uppercase; letter-spacing:.07em; font-size:.64rem; }
    .export-preview-item strong { display:block; margin-top:.2rem; }
    body[data-admin-theme="dark"] .dashboard-greeting { color:#f0e9ff; }
    body[data-admin-theme="dark"] .dashboard-greeting p { color:#c0b4d7; }
    body[data-admin-theme="dark"] .dashboard-card { background:#1b1529; border-color:#302443; color:#eee8ff; }
    body[data-admin-theme="dark"] .dashboard-card.resolved-cases-card { border:0; background:linear-gradient(120deg,#7658df,#a594f9 52%,#db8db8); }
    body[data-admin-theme="dark"] .dashboard-card-head h2,
    body[data-admin-theme="dark"] .dashboard-analytics-row strong { color:#e5dcff; }
    body[data-admin-theme="dark"] .dashboard-card-head p,
    body[data-admin-theme="dark"] .dashboard-analytics-row,
    body[data-admin-theme="dark"] .appointment-person small,
    body[data-admin-theme="dark"] .feedback-entry p { color:#bdb1d1; }
    body[data-admin-theme="dark"] .recent-user-avatar { border-color:#1b1529; background:#2b2040; }
    body[data-admin-theme="dark"] .ratio-track,
    body[data-admin-theme="dark"] .analytics-track { background:#332846; }
    body[data-admin-theme="dark"] .category-flip-face { background:#1b1529; }
    body[data-admin-theme="dark"] .dashboard-chart-toggle { background:#302348; border-color:#49365f; color:#d5c6ff; }
    body[data-admin-theme="dark"] .dashboard-chart-toggle:hover { background:#3a2d50; }
    body[data-admin-theme="dark"] .category-pie-center { fill:#1b1529; }
    body[data-admin-theme="dark"] .category-pie-track { stroke:#332846; }
    body[data-admin-theme="dark"] .category-pie-total { fill:#e5dcff; }
    body[data-admin-theme="dark"] .category-pie-caption,
    body[data-admin-theme="dark"] .category-pie-legend-item { fill:#bdb1d1; color:#bdb1d1; }
    body[data-admin-theme="dark"] .resolved-ratio { color:#d5c6ff; }
    body[data-admin-theme="dark"] .appointment-row { color:#e8e0f7; }
    body[data-admin-theme="dark"] .appointment-row:hover { background:#2a203e;color:#fff; }
    body[data-admin-theme="dark"] .appointment-date-chip { background:#352851;color:#d9cbff; }
    body[data-admin-theme="dark"] .admin-dashboard-table thead th { background:#241b35;color:#ddd1ff; }
    body[data-admin-theme="dark"] .admin-dashboard-table tbody td { color:#eee8ff;border-color:#332846; }
    body[data-admin-theme="dark"] .admin-dashboard-table tbody tr:hover { background:#251d38; }
    body[data-admin-theme="dark"] .dashboard-report-status.status-warning { background:#594916; color:#ffe989; }
    body[data-admin-theme="dark"] .dashboard-report-status.status-info { background:#173951; color:#c5e8ff; }
    body[data-admin-theme="dark"] .dashboard-report-status.status-success { background:#174431; color:#b8f1d0; }
    body[data-admin-theme="dark"] .dashboard-report-status.status-danger { background:#54243b; color:#ffd0df; }
    body[data-admin-theme="dark"] .dashboard-report-status.status-secondary { background:#3a3047; color:#e4d9f2; }
    body[data-admin-theme="dark"] .feedback-entry { border-color:#332846; }
    @media(max-width:767.98px) {
        .admin-dashboard-page { padding:0; }
        .dashboard-card-head,.dashboard-card-body { padding-left:1rem;padding-right:1rem; }
        .export-preview-list { grid-template-columns:1fr; }
        .category-pie-layout { grid-template-columns:minmax(100px, 38%) 1fr; gap:.7rem; }
    }
    @media(prefers-reduced-motion:reduce) {
        .admin-dashboard-page,.recent-user-avatar,.ratio-track span,.analytics-track span { animation:none;transform:none; }
        .dashboard-card,.dashboard-export-button,.appointment-row,.category-flip-inner,.dashboard-chart-toggle { transition:none; }
    }
</style>

<div class="container-fluid admin-dashboard-page">
    <div class="admin-dashboard-wrap">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4 px-1">
            <div class="dashboard-greeting">
                <p class="small fw-bold text-uppercase mb-1" style="letter-spacing:.11em;color:#8f72f5;">SINAG · Admin workspace</p>
                <h1 class="mb-1">{{ $greeting }}, {{ auth()->user()->first_name ?: explode(' ', trim(auth()->user()->name))[0] }}</h1>
                <p class="mb-0">{{ $dashboardDate }} <span class="mx-2">·</span> Here is the latest campus activity.</p>
            </div>
            <button type="button" class="btn dashboard-export-button px-4 py-2" data-bs-toggle="modal" data-bs-target="#exportGadFormatModal">
                <i class="bi bi-file-earmark-arrow-down me-2"></i>Export report
            </button>
        </div>

        @if(session('success'))
            <div class="alert alert-success rounded-4 border-0 shadow-sm">{{ session('success') }}</div>
        @endif

        <div class="row g-3 mb-4">
            <div class="col-xl-4 col-lg-4">
                <section class="dashboard-card">
                    <div class="dashboard-card-head">
                        <div><h2>Recent accounts</h2><p>Newest sign-ups; hover for a first name or open a profile.</p></div>
                        <a class="dashboard-link" href="{{ route('admin.users') }}">Manage <i class="bi bi-arrow-up-right"></i></a>
                    </div>
                    <div class="dashboard-card-body">
                        <div class="avatar-orbit" role="group" aria-label="Five most recently registered accounts">
                            @forelse($recentUsers as $index => $user)
                                @php
                                    $accountName = trim((string) ($user->first_name ?: explode(' ', trim($user->name))[0]));
                                    $avatarSizes = [58, 48, 42, 36, 34];
                                    $avatarLeft = [7, 29, 48, 67, 83];
                                    $avatarTop = [25, 4, 39, 12, 47];
                                @endphp
                                <a class="recent-user-avatar" href="{{ route('admin.users', ['q' => $user->email, 'open' => $user->id]) }}" title="{{ $accountName }}" aria-label="Open {{ $accountName }}'s account" style="--avatar-size:{{ $avatarSizes[$index] ?? 34 }}px;--avatar-left:{{ $avatarLeft[$index] ?? 83 }}%;--avatar-top:{{ $avatarTop[$index] ?? 47 }}%;--avatar-index:{{ 5 - $index }};">
                                    <img src="{{ route('profile.photo', $user) }}" alt="" data-fallback="{{ strtoupper(substr($accountName, 0, 1)) }}" onerror="this.hidden=true;this.parentElement.textContent=this.parentElement.dataset.fallback;">
                                </a>
                            @empty
                                <div class="text-muted small">No recent accounts.</div>
                            @endforelse
                        </div>
                        <a href="{{ route('admin.users') }}" class="dashboard-link">View user management <i class="bi bi-arrow-right ms-1"></i></a>
                        <p class="small text-muted mt-2 mb-0" aria-label="Account approval summary">
                            Approved accounts: <strong>{{ number_format($approvedAccountCount) }}</strong>
                            <span aria-hidden="true">·</span>
                            Pending review: <strong>{{ number_format($pendingAccountCount) }}</strong>
                        </p>
                    </div>
                </section>
            </div>
            <div class="col-xl-3 col-lg-3">
                <a href="{{ route('admin.reports', ['status' => 'Resolved']) }}" class="dashboard-card resolved-cases-card d-block text-decoration-none">
                    <div class="resolved-cases-card-inner">
                        <div class="dashboard-card-head">
                            <div><h2>Resolved cases</h2><p>Resolved reports out of all reports received.</p></div>
                            <i class="bi bi-check2-circle fs-4" style="color:#8f72f5;"></i>
                        </div>
                        <div class="dashboard-card-body">
                            <div class="resolved-ratio"><strong>{{ $resolvedReports }}</strong><span>/ {{ $totalReports }}</span></div>
                            <div class="ratio-track"><span style="--bar-width:{{ $resolvedPercentage }}%;"></span></div>
                            <div class="d-flex justify-content-between small text-muted mt-2"><span>{{ $resolvedPercentage }}% resolved</span><span>Open cases {{ max(0, $totalReports - $resolvedReports) }}</span></div>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-xl-5 col-lg-5">
                <section class="dashboard-card">
                    <div class="dashboard-card-head">
                        <div><h2>Appointments</h2><p>Today and upcoming requests</p></div>
                        <a class="dashboard-link" href="{{ route('admin.appointments') }}">All appointments <i class="bi bi-arrow-up-right"></i></a>
                    </div>
                    <div class="dashboard-card-body pt-2">
                        <div class="appointments-list">
                            @forelse($upcomingAppointments as $appointment)
                                @php
                                    $scheduledDate = $appointment->scheduled_date;
                                @endphp
                                <a class="appointment-row" href="{{ route('admin.appointments', ['open' => $appointment->id]) }}#appointmentModal{{ $appointment->id }}">
                                    <span class="appointment-date-chip">
                                        <strong>{{ optional($scheduledDate)->format('d') }}</strong>
                                        <small>{{ optional($scheduledDate)->isToday() ? 'Today' : optional($scheduledDate)->format('M') }}</small>
                                    </span>
                                    <span class="appointment-person">
                                        <strong>{{ $appointment->full_name }}</strong>
                                        <small>{{ optional($scheduledDate)->format('M d') }} · {{ optional($appointment->scheduled_time)->format('h:i A') }} · {{ ucfirst($appointment->status) }}</small>
                                    </span>
                                    <i class="bi bi-chevron-right small text-muted"></i>
                                </a>
                            @empty
                                <p class="text-muted small mb-1">No appointments scheduled for today or later.</p>
                            @endforelse
                        </div>
                    </div>
                </section>
            </div>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-xl-5 col-lg-5">
                @php
                    $maxReportCategory = max(1, (int) $reportCategories->max('total'));
                    $reportCategoryTotal = (float) $reportCategories->sum('total');
                    $reportPieColors = ['#4a2d96', '#7658df', '#8f72f5', '#f8cb12', '#34205f', '#c6b7f5'];
                    $reportCategoryOffset = 0.0;
                @endphp
                <section class="dashboard-card category-flip-card">
                    <div class="category-flip-inner">
                        <div class="category-flip-face category-flip-front" aria-hidden="false">
                            <div class="dashboard-card-head">
                                <div><h2>Most used report categories</h2><p>All active incident reports</p></div>
                                <button type="button" class="dashboard-chart-toggle" data-chart-toggle aria-label="Show report category pie chart" aria-pressed="false" title="Show pie chart">
                                    <i class="bi bi-pie-chart-fill" aria-hidden="true"></i>
                                </button>
                            </div>
                            <div class="dashboard-card-body">
                                @forelse($reportCategories as $category)
                                    <div class="dashboard-analytics-item">
                                        <div class="dashboard-analytics-row"><span>{{ $category->label }}</span><strong>{{ $category->total }}</strong></div>
                                        <div class="analytics-track"><span style="--bar-width:{{ round(($category->total / $maxReportCategory) * 100) }}%;"></span></div>
                                    </div>
                                @empty
                                    <p class="text-muted small mb-0">No report data yet.</p>
                                @endforelse
                            </div>
                        </div>
                        <div class="category-flip-face category-flip-back" aria-hidden="true" inert>
                            <div class="dashboard-card-head">
                                <div><h2>Report category breakdown</h2><p>Share of active reports</p></div>
                                <button type="button" class="dashboard-chart-toggle" data-chart-toggle aria-label="Show report category bars" aria-pressed="false" title="Show category bars">
                                    <i class="bi bi-bar-chart-line-fill" aria-hidden="true"></i>
                                </button>
                            </div>
                            <div class="dashboard-card-body">
                                <div class="category-pie-layout">
                                    <svg class="category-pie" viewBox="0 0 42 42" role="img" aria-label="Pie chart showing report categories">
                                        <circle class="category-pie-track" cx="21" cy="21" r="15.9155"></circle>
                                        @foreach($reportCategories as $index => $category)
                                            @php
                                                $reportShare = $reportCategoryTotal > 0 ? (float) $category->total / $reportCategoryTotal * 100 : 0;
                                                $reportShare = round($reportShare, 2);
                                            @endphp
                                            <circle class="category-pie-segment" cx="21" cy="21" r="15.9155" pathLength="100"
                                                stroke="{{ $reportPieColors[$index % count($reportPieColors)] }}"
                                                stroke-dasharray="{{ $reportShare }} {{ 100 - $reportShare }}"
                                                stroke-dashoffset="{{ -$reportCategoryOffset }}"
                                                transform="rotate(-90 21 21)"></circle>
                                            @php $reportCategoryOffset += $reportShare; @endphp
                                        @endforeach
                                        <circle class="category-pie-center" cx="21" cy="21" r="11"></circle>
                                        <text class="category-pie-total" x="21" y="20">{{ (int) $reportCategoryTotal }}</text>
                                        <text class="category-pie-caption" x="21" y="24">reports</text>
                                    </svg>
                                    <div class="category-pie-legend" aria-label="Report category totals">
                                        @forelse($reportCategories as $index => $category)
                                            @php $reportShare = $reportCategoryTotal > 0 ? round((float) $category->total / $reportCategoryTotal * 100) : 0; @endphp
                                            <div class="category-pie-legend-item">
                                                <span class="category-pie-label"><span class="category-pie-dot" style="background:{{ $reportPieColors[$index % count($reportPieColors)] }};"></span><span>{{ $category->label }}</span></span>
                                                <strong>{{ $category->total }} <span class="text-muted">({{ $reportShare }}%)</span></strong>
                                            </div>
                                        @empty
                                            <p class="text-muted small mb-0">No report data to chart.</p>
                                        @endforelse
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
            <div class="col-xl-5 col-lg-5">
                @php
                    $maxSuggestionCategory = max(1, (int) $suggestionCategories->max('total'));
                    $suggestionCategoryTotal = (float) $suggestionCategories->sum('total');
                    $suggestionPieColors = ['#4a2d96', '#7658df', '#8f72f5', '#f8cb12', '#34205f', '#c6b7f5'];
                    $suggestionCategoryOffset = 0.0;
                @endphp
                <section class="dashboard-card category-flip-card">
                    <div class="category-flip-inner">
                        <div class="category-flip-face category-flip-front" aria-hidden="false">
                            <div class="dashboard-card-head">
                                <div><h2>Suggestion categories</h2><p>Community feedback trends</p></div>
                                <button type="button" class="dashboard-chart-toggle" data-chart-toggle aria-label="Show suggestion category pie chart" aria-pressed="false" title="Show pie chart">
                                    <i class="bi bi-pie-chart-fill" aria-hidden="true"></i>
                                </button>
                            </div>
                            <div class="dashboard-card-body">
                                @forelse($suggestionCategories as $category)
                                    <div class="dashboard-analytics-item">
                                        <div class="dashboard-analytics-row"><span>{{ $category->label }}</span><strong>{{ $category->total }}</strong></div>
                                        <div class="analytics-track"><span style="--bar-width:{{ round(($category->total / $maxSuggestionCategory) * 100) }}%;"></span></div>
                                    </div>
                                @empty
                                    <p class="text-muted small mb-0">No suggestion data yet.</p>
                                @endforelse
                            </div>
                        </div>
                        <div class="category-flip-face category-flip-back" aria-hidden="true" inert>
                            <div class="dashboard-card-head">
                                <div><h2>Suggestion breakdown</h2><p>Share by suggestion category</p></div>
                                <button type="button" class="dashboard-chart-toggle" data-chart-toggle aria-label="Show suggestion category bars" aria-pressed="false" title="Show category bars">
                                    <i class="bi bi-bar-chart-line-fill" aria-hidden="true"></i>
                                </button>
                            </div>
                            <div class="dashboard-card-body">
                                <div class="category-pie-layout">
                                    <svg class="category-pie" viewBox="0 0 42 42" role="img" aria-label="Pie chart showing suggestion categories">
                                        <circle class="category-pie-track" cx="21" cy="21" r="15.9155"></circle>
                                        @foreach($suggestionCategories as $index => $category)
                                            @php
                                                $suggestionShare = $suggestionCategoryTotal > 0 ? (float) $category->total / $suggestionCategoryTotal * 100 : 0;
                                                $suggestionShare = round($suggestionShare, 2);
                                            @endphp
                                            <circle class="category-pie-segment" cx="21" cy="21" r="15.9155" pathLength="100"
                                                stroke="{{ $suggestionPieColors[$index % count($suggestionPieColors)] }}"
                                                stroke-dasharray="{{ $suggestionShare }} {{ 100 - $suggestionShare }}"
                                                stroke-dashoffset="{{ -$suggestionCategoryOffset }}"
                                                transform="rotate(-90 21 21)"></circle>
                                            @php $suggestionCategoryOffset += $suggestionShare; @endphp
                                        @endforeach
                                        <circle class="category-pie-center" cx="21" cy="21" r="11"></circle>
                                        <text class="category-pie-total" x="21" y="20">{{ (int) $suggestionCategoryTotal }}</text>
                                        <text class="category-pie-caption" x="21" y="24">suggestions</text>
                                    </svg>
                                    <div class="category-pie-legend" aria-label="Suggestion category totals">
                                        @forelse($suggestionCategories as $index => $category)
                                            @php $suggestionShare = $suggestionCategoryTotal > 0 ? round((float) $category->total / $suggestionCategoryTotal * 100) : 0; @endphp
                                            <div class="category-pie-legend-item">
                                                <span class="category-pie-label"><span class="category-pie-dot" style="background:{{ $suggestionPieColors[$index % count($suggestionPieColors)] }};"></span><span>{{ $category->label }}</span></span>
                                                <strong>{{ $category->total }} <span class="text-muted">({{ $suggestionShare }}%)</span></strong>
                                            </div>
                                        @empty
                                            <p class="text-muted small mb-0">No suggestion data to chart.</p>
                                        @endforelse
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
            <div class="col-xl-2 col-lg-12">
                <section class="dashboard-card">
                    <div class="dashboard-card-head"><div><h2>Ratings & feedback</h2><p>Survey responses</p></div></div>
                    <div class="dashboard-card-body pt-2">
                        <div class="feedback-score"><strong>{{ number_format($averageRating, 1) }}</strong><span><span class="feedback-stars">★★★★★</span><small class="d-block text-muted">{{ $feedbackCount }} ratings</small></span></div>
                        @forelse($recentFeedback->take(3) as $feedback)
                            <div class="feedback-entry">
                                <div class="d-flex justify-content-between gap-2 small"><strong>Anonymous {{ $loop->iteration }}</strong><span class="feedback-stars">{{ str_repeat('★', (int) $feedback->rating) }}</span></div>
                                @if($feedback->feedback)<p>{{ \Illuminate\Support\Str::limit($feedback->feedback, 90) }}</p>@else<p class="fst-italic">No written feedback.</p>@endif
                            </div>
                        @empty
                            <p class="text-muted small mt-3 mb-0">No feedback submitted yet.</p>
                        @endforelse
                    </div>
                </section>

            </div>
        </div>

        <section class="dashboard-card mb-4">
            <div class="dashboard-card-head">
                <div><h2>Recent reports</h2><p>Latest active cases requiring visibility</p></div>
                <a href="{{ route('admin.reports') }}" class="dashboard-link">Manage reports <i class="bi bi-arrow-up-right"></i></a>
            </div>
            <div class="table-responsive mt-2">
                <table class="table align-middle mb-0 admin-dashboard-table">
                    <thead><tr><th class="ps-4">Case</th><th>Category</th><th>Reporter</th><th>Status</th><th>Date received</th><th class="text-end pe-4">Action</th></tr></thead>
                    <tbody>
                        @forelse($recentReports as $report)
                            <tr>
                                <td class="ps-4 fw-bold">#{{ $report->incident_id }}</td>
                                <td>{{ $report->nature }}</td>
                                <td>{{ $report->reporter_display_name }}</td>
                                <td><span class="badge rounded-pill dashboard-report-status status-{{ $report->status_color }}">{{ $report->status }}</span></td>
                                <td class="small text-muted">{{ $report->created_at?->format('M d, Y') }}</td>
                                <td class="text-end pe-4"><a class="btn btn-sm btn-outline-primary rounded-pill px-3" href="{{ route('admin.reports', ['q' => $report->incident_id]) }}">Review</a></td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="text-center text-muted py-4">No active reports.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>

        <section class="dashboard-card mb-4">
            <div class="dashboard-card-head">
                <div><h2>Recent suggestions</h2><p>The latest two community suggestions</p></div>
                <a href="{{ route('admin.suggestions') }}" class="dashboard-link">Manage suggestions <i class="bi bi-arrow-up-right"></i></a>
            </div>
            <div class="dashboard-card-body">
                <div class="row g-3">
                    @forelse($recentSuggestions as $suggestion)
                        @php
                            $suggestionAuthor = $suggestion->is_anonymous
                                ? 'Anonymous'
                                : (optional($suggestion->user)->cloak_alias ?: optional($suggestion->user)->name ?: 'Unknown user');
                        @endphp
                        <div class="col-12 col-md-6">
                            <article class="recent-suggestion-card h-100">
                                <div class="recent-suggestion-meta">
                                    <span>{{ $suggestion->category ?: 'General' }}</span>
                                    <time datetime="{{ optional($suggestion->created_at)->toISOString() }}">{{ optional($suggestion->created_at)->format('M d, Y') }}</time>
                                </div>
                                <p class="mt-2">{{ \Illuminate\Support\Str::limit($suggestion->message, 240) }}</p>
                                <div class="recent-suggestion-footer">
                                    <span>From: {{ $suggestionAuthor }}</span>
                                    <span>{{ $suggestion->admin_status_label ?: 'Received' }}</span>
                                </div>
                            </article>
                        </div>
                    @empty
                        <div class="col-12"><p class="text-muted small mb-0">No suggestions have been submitted yet.</p></div>
                    @endforelse
                </div>
            </div>
        </section>
    </div>
</div>

<div class="modal fade" id="exportGadFormatModal" tabindex="-1" aria-labelledby="exportGadFormatModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 rounded-4 shadow-lg">
            <div class="modal-header border-0">
                <div><h2 class="modal-title h5 fw-bold mb-1" id="exportGadFormatModalLabel">Choose export sections</h2><p class="small text-muted mb-0">Review the generated file before downloading.</p></div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="exportGadReportForm" method="GET" data-export-url-template="{{ route('admin.gad-report.export', ['format' => '__FORMAT__', 'period' => '__PERIOD__']) }}">
                <div class="modal-body pt-0" id="exportGadDataStep">
                    <label class="export-section-choice mb-2"><input type="checkbox" name="include_reports" value="1" checked><span>Incident reports and suggestions</span></label>
                    <label class="export-section-choice"><input type="checkbox" name="include_feedback" value="1" checked><span>Customer feedback and surveys</span></label>
                    <div class="small text-danger mt-2 d-none" id="exportGadDataError">Select at least one section.</div>
                </div>
                <div class="modal-body pt-0 d-none" id="exportGadFormatStep" aria-hidden="true">
                    <label for="exportGadReportFormat" class="form-label fw-semibold">File format</label>
                    <select id="exportGadReportFormat" class="form-select"><option value="pdf">PDF</option><option value="excel">Excel workbook (.xlsx)</option></select>
                </div>
                <div class="modal-body pt-0 d-none" id="exportGadPeriodStep" aria-hidden="true">
                    <label for="exportGadReportPeriod" class="form-label fw-semibold">Date range</label>
                    <select id="exportGadReportPeriod" class="form-select" required>
                        <option value="30days">Last 30 days</option><option value="7days">Last 7 days</option>
                        <option value="q1">Quarter 1 (Jan–Mar)</option><option value="q2">Quarter 2 (Apr–Jun)</option>
                        <option value="q3">Quarter 3 (Jul–Sep)</option><option value="q4">Quarter 4 (Oct–Dec)</option>
                        <option value="midyear">Mid-year (Jan–Jun)</option><option value="annual">Annual (Jan–Dec)</option><option value="lastyear">Last year</option>
                    </select>
                </div>
                <div class="modal-body pt-0 d-none" id="exportGadPreviewStep" aria-hidden="true">
                    <p class="small text-muted mb-2" id="exportPreviewDescription">Loading the generated file preview…</p>
                    <iframe id="exportGadFilePreview" class="export-file-preview" title="Preview of the generated GAD export file"></iframe>
                </div>
                <div class="modal-footer border-0" id="exportGadDataActions">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" id="continueGadData" class="btn text-white rounded-pill px-4" style="background:#8f72f5;">Continue</button>
                </div>
                <div class="modal-footer border-0 d-none" id="exportGadFormatActions" aria-hidden="true">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" id="continueGadExport" class="btn text-white rounded-pill px-4" style="background:#8f72f5;">Continue</button>
                </div>
                <div class="modal-footer border-0 d-none" id="exportGadPeriodActions" aria-hidden="true">
                    <button type="button" id="backToGadExportFormat" class="btn btn-light rounded-pill px-4">Back</button>
                    <button type="button" id="previewGadExport" class="btn text-white rounded-pill px-4" style="background:#8f72f5;">Preview export</button>
                </div>
                <div class="modal-footer border-0 d-none" id="exportGadPreviewActions" aria-hidden="true">
                    <button type="button" id="backToGadExportPeriod" class="btn btn-light rounded-pill px-4">Back</button>
                    <button type="submit" class="btn text-white rounded-pill px-4" style="background:#8f72f5;"><i class="bi bi-download me-1"></i>Export now</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const modal = document.getElementById('exportGadFormatModal');
        const form = document.getElementById('exportGadReportForm');
        const steps = {
            data: document.getElementById('exportGadDataStep'),
            format: document.getElementById('exportGadFormatStep'),
            period: document.getElementById('exportGadPeriodStep'),
            preview: document.getElementById('exportGadPreviewStep'),
        };
        const actions = {
            data: document.getElementById('exportGadDataActions'),
            format: document.getElementById('exportGadFormatActions'),
            period: document.getElementById('exportGadPeriodActions'),
            preview: document.getElementById('exportGadPreviewActions'),
        };
        const titles = {
            data: 'Choose export sections',
            format: 'Choose file format',
            period: 'Choose date range',
            preview: 'Preview export',
        };
        const title = document.getElementById('exportGadFormatModalLabel');
        const previewFrame = document.getElementById('exportGadFilePreview');
        const previewDescription = document.getElementById('exportPreviewDescription');
        let currentPreviewDescription = '';

        document.querySelectorAll('.category-flip-card').forEach(function (card) {
            const front = card.querySelector('.category-flip-front');
            const back = card.querySelector('.category-flip-back');
            const buttons = card.querySelectorAll('[data-chart-toggle]');

            buttons.forEach(function (button) {
                button.addEventListener('click', function () {
                    const showingPie = !card.classList.contains('is-flipped');
                    card.classList.toggle('is-flipped', showingPie);
                    front.inert = showingPie;
                    back.inert = !showingPie;
                    front.setAttribute('aria-hidden', String(showingPie));
                    back.setAttribute('aria-hidden', String(!showingPie));

                    buttons.forEach(function (toggle) {
                        toggle.setAttribute('aria-pressed', String(showingPie));
                        toggle.setAttribute('aria-label', showingPie ? 'Show category bars' : 'Show category pie chart');
                        toggle.title = showingPie ? 'Show category bars' : 'Show pie chart';
                    });
                });
            });
        });

        function showStep(current) {
            Object.keys(steps).forEach(function (name) {
                const isCurrent = name === current;
                steps[name].classList.toggle('d-none', !isCurrent);
                steps[name].setAttribute('aria-hidden', String(!isCurrent));
                actions[name].classList.toggle('d-none', !isCurrent);
                actions[name].setAttribute('aria-hidden', String(!isCurrent));
            });
            title.textContent = titles[current];
        }

        showStep('data');
        document.getElementById('continueGadData').addEventListener('click', function () {
            const selectedCount = form.querySelectorAll('#exportGadDataStep input:checked').length;
            document.getElementById('exportGadDataError').classList.toggle('d-none', selectedCount > 0);
            if (selectedCount > 0) showStep('format');
        });
        document.getElementById('continueGadExport').addEventListener('click', function () {
            showStep('period');
        });
        document.getElementById('backToGadExportFormat').addEventListener('click', function () {
            showStep('format');
        });
        document.getElementById('backToGadExportPeriod').addEventListener('click', function () {
            showStep('period');
        });
        document.getElementById('previewGadExport').addEventListener('click', function () {
            const selected = Array.from(form.querySelectorAll('#exportGadDataStep input:checked'))
                .map((input) => input.name === 'include_reports' ? 'Reports & suggestions' : 'Customer feedback');
            const format = document.getElementById('exportGadReportFormat');
            const period = document.getElementById('exportGadReportPeriod');
            const previewUrl = form.dataset.exportUrlTemplate
                .replace('__FORMAT__', encodeURIComponent(format.value))
                .replace('__PERIOD__', encodeURIComponent(period.value));
            const params = new URLSearchParams();
            form.querySelectorAll('#exportGadDataStep input:checked').forEach(function (input) {
                params.append(input.name, input.value);
            });
            params.set('preview', '1');
            currentPreviewDescription = `${format.options[format.selectedIndex].text} · ${period.options[period.selectedIndex].text} · ${selected.join(', ')}`;
            previewDescription.textContent = `Generating preview: ${currentPreviewDescription}`;
            previewFrame.src = `${previewUrl}?${params.toString()}`;
            showStep('preview');
        });
        previewFrame.addEventListener('load', function () {
            if (previewFrame.src !== 'about:blank') {
                previewDescription.textContent = `Preview ready: ${currentPreviewDescription}`;
            }
        });
        form.addEventListener('submit', function () {
            const format = document.getElementById('exportGadReportFormat').value;
            const period = document.getElementById('exportGadReportPeriod').value;
            form.action = form.dataset.exportUrlTemplate
                .replace('__FORMAT__', encodeURIComponent(format))
                .replace('__PERIOD__', encodeURIComponent(period));
        });
        modal.addEventListener('hidden.bs.modal', function () {
            showStep('data');
            document.getElementById('exportGadDataError').classList.add('d-none');
            previewFrame.src = 'about:blank';
        });
    });
</script>
@endsection
