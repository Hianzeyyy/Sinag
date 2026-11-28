@extends('layouts.app')

@section('content')
@php
    $natureTotal = (float) $reportNatureCounts->sum('total');
    $natureColors = ['#34205f', '#5b3ca8', '#7658df', '#facc15', '#8f72f5', '#c6b7f5', '#9c7d27', '#453563'];
    $natureOffset = 0.0;
@endphp
<div class="container-fluid coordinator-dashboard">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <p class="small fw-bold text-uppercase mb-1 coordinator-eyebrow">SINAG · GAD Coordinator</p>
            <h1 class="h2 fw-bold mb-1">GAD Report Analytics</h1>
            <p class="text-muted mb-0">Report activity, case priorities, and community feedback.</p>
        </div>
        <button type="button" class="dashboard-export-button" data-bs-toggle="modal" data-bs-target="#exportGadFormatModal">
            <i class="bi bi-file-earmark-arrow-down me-2"></i>Export report
        </button>
    </div>

    <div class="row g-3 mb-4">
        @foreach([
            ['Total Reports', $totalReports, 'All active incident reports', 'bi-clipboard-data'],
            ['Emergency', $emergencyReportCount, 'High-priority or emergency reports needing attention', 'bi-exclamation-triangle'],
            ['Pending', (int) $reportCountsByStatus->get('Pending', 0), 'Awaiting review', 'bi-hourglass-split'],
            ['Resolved', (int) $reportCountsByStatus->get('Resolved', 0), 'Cases marked resolved', 'bi-check2-circle'],
        ] as [$label, $value, $hint, $icon])
            <div class="col-sm-6 col-xl-3">
                <section class="coordinator-card coordinator-metric h-100">
                    <div class="coordinator-metric-icon"><i class="bi {{ $icon }}" aria-hidden="true"></i></div>
                    <div class="small fw-bold text-uppercase text-muted">{{ $label }}</div>
                    <div class="coordinator-metric-value" data-count-up="{{ $value }}">{{ number_format($value) }}</div>
                    <div class="small text-muted">{{ $hint }}</div>
                </section>
            </div>
        @endforeach
    </div>

    <div class="row g-4 mb-4">
        <div class="col-lg-5">
            <section class="coordinator-card h-100">
                <div class="coordinator-card-heading">
                    <div><h2>Reports by Status</h2><p>Current case distribution</p></div>
                    <a href="{{ route('admin.report-tracking') }}" class="coordinator-link">Review reports <i class="bi bi-arrow-up-right"></i></a>
                </div>
                <div class="coordinator-card-body">
                    @forelse($reportStatusCounts as $row)
                        @php
                            $percentage = $totalReports > 0 ? ($row->total / $totalReports) * 100 : 0;
                        @endphp
                        <div class="analytics-item">
                            <div class="analytics-row"><span>{{ $row->status }}</span><strong><span data-count-up="{{ $row->total }}">{{ number_format($row->total) }}</span> <small>({{ number_format($percentage, 1) }}%)</small></strong></div>
                            <div class="analytics-track"><span style="--bar-width:{{ $percentage }}%"></span></div>
                        </div>
                    @empty
                        <p class="text-muted mb-0">No incident reports have been recorded.</p>
                    @endforelse
                </div>
            </section>
        </div>

        <div class="col-lg-7">
            <section class="coordinator-card coordinator-flip-card h-100" id="natureChartCard">
                <div class="coordinator-flip-inner">
                    <div class="coordinator-flip-face coordinator-flip-front">
                        <div class="coordinator-card-heading">
                            <div><h2>Reports by Nature</h2><p>Most-used report categories</p></div>
                            <button type="button" class="chart-toggle" data-nature-toggle aria-label="Show report nature pie chart" aria-pressed="false" title="Show pie chart">
                                <i class="bi bi-pie-chart-fill" aria-hidden="true"></i>
                            </button>
                        </div>
                        <div class="coordinator-card-body">
                            @forelse($reportNatureCounts as $row)
                                @php
                                    $percentage = $natureTotal > 0 ? ($row->total / $natureTotal) * 100 : 0;
                                @endphp
                                <div class="analytics-item">
                                    <div class="analytics-row"><span>{{ $row->nature }}</span><strong><span data-count-up="{{ $row->total }}">{{ number_format($row->total) }}</span> <small>({{ number_format($percentage, 1) }}%)</small></strong></div>
                                    <div class="analytics-track"><span style="--bar-width:{{ $percentage }}%"></span></div>
                                </div>
                            @empty
                                <p class="text-muted mb-0">No incident reports have been recorded.</p>
                            @endforelse
                        </div>
                    </div>
                    <div class="coordinator-flip-face coordinator-flip-back" aria-hidden="true" inert>
                        <div class="coordinator-card-heading">
                            <div><h2>Nature Breakdown</h2><p>Share of all active reports</p></div>
                            <button type="button" class="chart-toggle" data-nature-toggle aria-label="Show report nature bars" aria-pressed="true" title="Show category bars">
                                <i class="bi bi-bar-chart-line-fill" aria-hidden="true"></i>
                            </button>
                        </div>
                        <div class="coordinator-card-body">
                            <div class="nature-pie-layout">
                                <svg class="nature-pie" viewBox="0 0 42 42" role="img" aria-label="Pie chart showing reports by nature">
                                    <circle class="nature-pie-track" cx="21" cy="21" r="15.9155"></circle>
                                    @foreach($reportNatureCounts as $index => $row)
                                        @php
                                            $share = $natureTotal > 0 ? round((float) $row->total / $natureTotal * 100, 2) : 0;
                                        @endphp
                                        <circle class="nature-pie-segment" cx="21" cy="21" r="15.9155" pathLength="100"
                                            stroke="{{ $natureColors[$index % count($natureColors)] }}"
                                            style="--pie-dasharray:{{ $share }} {{ 100 - $share }}"
                                            stroke-dashoffset="{{ -$natureOffset }}"
                                            transform="rotate(-90 21 21)"></circle>
                                        @php
                                            $natureOffset += $share;
                                        @endphp
                                    @endforeach
                                    <circle class="nature-pie-center" cx="21" cy="21" r="11"></circle>
                                    <text class="nature-pie-total" x="21" y="20" data-count-up="{{ (int) $natureTotal }}">{{ (int) $natureTotal }}</text>
                                    <text class="nature-pie-caption" x="21" y="24">reports</text>
                                </svg>
                                <div class="nature-pie-legend" aria-label="Report totals by nature">
                                    @forelse($reportNatureCounts as $index => $row)
                                        @php
                                            $share = $natureTotal > 0 ? round((float) $row->total / $natureTotal * 100) : 0;
                                        @endphp
                                        <div class="nature-legend-item">
                                            <span class="nature-legend-label"><i style="background:{{ $natureColors[$index % count($natureColors)] }}"></i><span>{{ $row->nature }}</span></span>
                                            <strong><span data-count-up="{{ $row->total }}">{{ number_format($row->total) }}</span> <small>({{ $share }}%)</small></strong>
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
    </div>

    <section class="coordinator-card mb-4">
        <div class="coordinator-card-heading">
            <div><h2>Priority Overview</h2><p>Use this breakdown to triage high-priority cases first.</p></div>
            <a href="{{ route('admin.report-tracking') }}" class="coordinator-link">Open report tracking <i class="bi bi-arrow-up-right"></i></a>
        </div>
        <div class="coordinator-card-body">
            <div class="row g-3">
                @forelse($reportCountsByPriority as $priorityRow)
                    @php
                        $priorityPercentage = $totalReports > 0 ? ($priorityRow->total / $totalReports) * 100 : 0;
                    @endphp
                    <div class="col-md-4">
                        <div class="priority-tile">
                            <div class="analytics-row"><span>{{ $priorityRow->priority }} priority</span><strong data-count-up="{{ $priorityRow->total }}">{{ number_format($priorityRow->total) }}</strong></div>
                            <div class="analytics-track"><span style="--bar-width:{{ $priorityPercentage }}%"></span></div>
                        </div>
                    </div>
                @empty
                    <div class="col-12"><p class="text-muted mb-0">No priority data available.</p></div>
                @endforelse
            </div>
        </div>
    </section>

    <section class="coordinator-card coordinator-reviews mb-4">
        <div class="coordinator-card-heading">
            <div><h2>Ratings &amp; Reviews</h2><p>Recent feedback from the SINAG community</p></div>
            <a href="{{ route('admin.settings', ['section' => 'feedback']) }}" class="coordinator-link">All reviews <i class="bi bi-arrow-up-right"></i></a>
        </div>
        <div class="coordinator-card-body">
            <a class="coordinator-rating-summary" href="{{ route('admin.settings', ['section' => 'feedback']) }}">
                <strong data-count-up="{{ $averageRating }}" data-count-decimals="1">{{ number_format($averageRating, 1) }}</strong>
                <span><span class="rating-stars" aria-label="{{ number_format($averageRating, 1) }} out of 5 stars">★★★★★</span><small><span data-count-up="{{ $feedbackCount }}">{{ number_format($feedbackCount) }}</span> ratings · View all reviews</small></span>
            </a>
            <div class="row g-3 mt-1">
                @forelse($recentFeedback->take(3) as $feedback)
                    <div class="col-md-4">
                        <a class="coordinator-review" href="{{ route('admin.settings', ['section' => 'feedback']) }}">
                            <div class="d-flex justify-content-between gap-2">
                                <strong>{{ $feedback->user?->name ?: 'Anonymous' }}</strong>
                                <span class="rating-stars" aria-label="{{ $feedback->rating }} out of 5 stars">{{ str_repeat('★', (int) $feedback->rating) }}</span>
                            </div>
                            <small>{{ $feedback->subject ? ucfirst($feedback->subject) : 'System feedback' }} · {{ $feedback->created_at?->format('M d, Y') }}</small>
                            <p>{{ $feedback->feedback ? \Illuminate\Support\Str::limit($feedback->feedback, 150) : 'No written feedback.' }}</p>
                            <span class="review-link">Open Ratings &amp; Reviews <i class="bi bi-arrow-right"></i></span>
                        </a>
                    </div>
                @empty
                    <div class="col-12"><p class="text-muted mb-0">No ratings or reviews have been submitted yet.</p></div>
                @endforelse
            </div>
        </div>
    </section>
</div>

@include('admin.partials.gad-export-modal')

<style>
    .coordinator-dashboard { max-width:1500px; color:#2b2040; }
    .coordinator-eyebrow { color:#5b3ca8; letter-spacing:.11em; }
    .dashboard-export-button { display:inline-block; padding:.65rem 1.15rem; background:#facc15; color:#34205f; border:2px solid #34205f; border-radius:.65rem; box-shadow:4px 4px 0 #34205f; font-weight:800; transition:all .2s ease; cursor:pointer; }
    .dashboard-export-button:hover { color:#facc15; background:#34205f; border-color:#34205f; box-shadow:4px 4px 0 #facc15; }
    .dashboard-export-button:active { background:#eab308; box-shadow:none; transform:translateY(4px); }
    .export-section-choice { display:flex; align-items:center; gap:.7rem; padding:.75rem .85rem; border:1px solid #e8e1f5; border-radius:.8rem; cursor:pointer; }
    .export-section-choice:has(input:checked) { background:#f4f0ff; border-color:#7658df; color:#34205f; }
    .export-file-preview { display:block;width:100%;height:min(62vh,760px);min-height:320px;border:1px solid #e8e1f5;border-radius:1rem;background:#fff; }
    .coordinator-card { overflow:hidden; border:1px solid #e9e3f3; border-radius:1.2rem; background:#fff; box-shadow:0 12px 30px rgba(48,27,90,.07); }
    .coordinator-metric { position:relative; padding:1.25rem; border-top:4px solid #facc15; background:linear-gradient(145deg,#fff 30%,#f4efff); }
    .coordinator-metric-icon { display:grid; width:2.4rem;height:2.4rem;place-items:center;margin-bottom:.75rem;border-radius:.8rem;background:#34205f;color:#facc15;font-size:1.1rem; }
    .coordinator-metric-value { color:#34205f;font-size:2rem;font-weight:900; }
    .coordinator-card-heading { display:flex;align-items:flex-start;justify-content:space-between;gap:1rem;padding:1.2rem 1.3rem .6rem; }
    .coordinator-card-heading h2 { margin:0;color:#34205f;font-size:1rem;font-weight:850; }
    .coordinator-card-heading p { margin:.25rem 0 0;color:#81788d;font-size:.78rem; }
    .coordinator-card-body { padding:.9rem 1.3rem 1.3rem; }
    .coordinator-link { color:#5b3ca8;font-size:.78rem;font-weight:800;white-space:nowrap; }
    .analytics-item { margin-bottom:1.1rem; }
    .analytics-item:last-child { margin-bottom:0; }
    .analytics-row { display:flex;justify-content:space-between;gap:.8rem;margin-bottom:.4rem;color:#554b65;font-size:.8rem; }
    .analytics-row strong { color:#34205f;white-space:nowrap; }
    .analytics-row small { color:#827991;font-weight:600; }
    .analytics-track { height:9px;overflow:hidden;border-radius:999px;background:#eee9f7; }
    .analytics-track span { display:block;width:var(--bar-width);height:100%;border-radius:inherit;background:linear-gradient(90deg,#facc15,#34205f);animation:coordinatorBarGrow .9s cubic-bezier(.2,.72,.25,1) both; }
    @keyframes coordinatorBarGrow { from { width:0; } to { width:var(--bar-width); } }
    .priority-tile { padding:1rem;border:1px solid #eee8f5;border-radius:.9rem;background:#fcfbfe; }
    .coordinator-flip-card { min-height:24rem;perspective:1100px; }
    .coordinator-flip-inner { position:relative;min-height:24rem;height:100%;transform-style:preserve-3d;transition:transform .65s cubic-bezier(.2,.72,.25,1); }
    .coordinator-flip-card.is-flipped .coordinator-flip-inner { transform:rotateY(180deg); }
    .coordinator-flip-face { position:absolute;inset:0;display:flex;flex-direction:column;min-height:24rem;backface-visibility:hidden;-webkit-backface-visibility:hidden; }
    .coordinator-flip-back { transform:rotateY(180deg); }
    .coordinator-flip-face .coordinator-card-body { flex:1;min-height:0;overflow:auto; }
    .chart-toggle { display:grid;width:2.35rem;height:2.35rem;place-items:center;border:1px solid #e7e0f1;border-radius:.75rem;background:#f7f4ff;color:#5b3ca8; }
    .nature-pie-layout { display:grid;grid-template-columns:minmax(125px,40%) 1fr;align-items:center;gap:1rem;min-height:15rem; }
    .nature-pie { display:block;width:min(100%,190px);margin:auto; }
    .nature-pie-track,.nature-pie-segment { fill:none;stroke-width:8; }
    .nature-pie-segment { stroke-dasharray:var(--pie-dasharray);animation:coordinatorPieDraw 1s cubic-bezier(.2,.72,.25,1) both; }
    @keyframes coordinatorPieDraw { from { stroke-dasharray:0 100; } to { stroke-dasharray:var(--pie-dasharray); } }
    .nature-pie-track { stroke:#eee9f7; }
    .nature-pie-center { fill:#fff; }
    .nature-pie-total { fill:#34205f;font-size:5px;font-weight:900;text-anchor:middle; }
    .nature-pie-caption { fill:#81788d;font-size:2.3px;text-anchor:middle; }
    .nature-pie-legend { display:grid;gap:.45rem;max-height:17rem;overflow:auto;padding-right:.3rem; }
    .nature-legend-item,.nature-legend-label { display:flex;align-items:center;gap:.5rem; }
    .nature-legend-item { justify-content:space-between;color:#554b65;font-size:.74rem; }
    .nature-legend-item strong { white-space:nowrap;color:#34205f; }
    .nature-legend-item small { color:#827991;font-weight:600; }
    .nature-legend-label { min-width:0; }
    .nature-legend-label i { width:.65rem;height:.65rem;flex:0 0 auto;border-radius:50%; }
    .nature-legend-label span { overflow:hidden;text-overflow:ellipsis;white-space:nowrap; }
    .coordinator-rating-summary { display:inline-flex;align-items:center;gap:.8rem;color:inherit; }
    .coordinator-rating-summary > strong { color:#34205f;font-size:2.7rem;line-height:1;font-weight:900; }
    .coordinator-rating-summary small { display:block;color:#81788d;font-size:.74rem; }
    .rating-stars { color:#bd8c00;letter-spacing:.04em;white-space:nowrap; }
    .coordinator-review { display:block;height:100%;padding:1rem;border:1px solid #eee8f5;border-radius:.9rem;color:inherit;background:#fcfbfe; }
    .coordinator-review:hover { border-color:#c3a741;color:inherit;background:#fffdf4; }
    .coordinator-review strong { color:#34205f;font-size:.83rem; }
    .coordinator-review small { display:block;margin-top:.25rem;color:#81788d; }
    .coordinator-review p { margin:.75rem 0;color:#655c73;font-size:.8rem; }
    .review-link { color:#5b3ca8;font-size:.73rem;font-weight:800; }
    body[data-admin-theme="dark"] .coordinator-dashboard { color:#eee8ff; }
    body[data-admin-theme="dark"] .coordinator-dashboard .text-muted { color:#bdb1d1 !important; }
    body[data-admin-theme="dark"] .coordinator-dashboard .coordinator-eyebrow { color:#facc15; }
    body[data-admin-theme="dark"] .coordinator-dashboard .coordinator-link,
    body[data-admin-theme="dark"] .coordinator-dashboard .review-link,
    body[data-admin-theme="dark"] .coordinator-dashboard .coordinator-review small,
    body[data-admin-theme="dark"] .coordinator-dashboard .coordinator-rating-summary small { color:#c6b7f5; }
    body[data-admin-theme="dark"] .coordinator-card { border-color:#3a2d50;background:#1b1529;color:#eee8ff; }
    body[data-admin-theme="dark"] .coordinator-metric { background:linear-gradient(145deg,#1b1529 30%,#2b2040); }
    body[data-admin-theme="dark"] .coordinator-card-heading h2,
    body[data-admin-theme="dark"] .coordinator-metric-value,
    body[data-admin-theme="dark"] .analytics-row strong,
    body[data-admin-theme="dark"] .nature-legend-item strong,
    body[data-admin-theme="dark"] .coordinator-rating-summary > strong,
    body[data-admin-theme="dark"] .coordinator-review strong { color:#f0e9ff; }
    body[data-admin-theme="dark"] .analytics-row,
    body[data-admin-theme="dark"] .coordinator-card-heading p,
    body[data-admin-theme="dark"] .coordinator-review p { color:#bdb1d1; }
    body[data-admin-theme="dark"] .priority-tile,
    body[data-admin-theme="dark"] .coordinator-review { border-color:#3a2d50;background:#241b35; }
    body[data-admin-theme="dark"] .coordinator-review:hover { background:#302348; }
    body[data-admin-theme="dark"] .analytics-track,
    body[data-admin-theme="dark"] .nature-pie-track { background:#332846;stroke:#332846; }
    body[data-admin-theme="dark"] .nature-pie-center { fill:#1b1529; }
    body[data-admin-theme="dark"] .nature-pie-total { fill:#eee8ff; }
    body[data-admin-theme="dark"] .nature-pie-caption,
    body[data-admin-theme="dark"] .nature-legend-item,
    body[data-admin-theme="dark"] .nature-legend-item small { fill:#bdb1d1;color:#bdb1d1; }
    body[data-admin-theme="dark"] .chart-toggle { border-color:#49365f;background:#302348;color:#facc15; }
    body[data-admin-theme="dark"] .export-file-preview { border-color:#3a2d50; }
    body[data-admin-theme="dark"] #exportGadFormatModal .modal-content { background:#1b1529;color:#eee8ff; }
    body[data-admin-theme="dark"] #exportGadFormatModal .modal-header { border-color:#3a2d50; }
    body[data-admin-theme="dark"] #exportGadFormatModal .export-section-choice { background:#241b35;color:#eee8ff;border-color:#49365f; }
    body[data-admin-theme="dark"] #exportGadFormatModal .form-select { background:#241b35;color:#eee8ff;border-color:#49365f; }
    @media(max-width:575.98px) { .nature-pie-layout { grid-template-columns:1fr; }.nature-pie { width:150px; }.coordinator-card-heading { flex-wrap:wrap; } }
    @media(prefers-reduced-motion:reduce) {
        .coordinator-flip-inner { transition:none; }
        .analytics-track span,.nature-pie-segment { animation:none; }
    }
</style>
<script>
    const coordinatorDashboard = document.querySelector('.coordinator-dashboard');
    const reduceDashboardMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    if (coordinatorDashboard && !reduceDashboardMotion) {
        const counters = coordinatorDashboard.querySelectorAll('[data-count-up]');
        const targets = Array.from(counters, function (counter) {
            const target = Number(counter.dataset.countUp);
            const decimals = Number(counter.dataset.countDecimals || 0);

            if (!Number.isFinite(target)) {
                return null;
            }

            const numberFormatter = new Intl.NumberFormat(undefined, {
                minimumFractionDigits: decimals,
                maximumFractionDigits: decimals
            });
            counter.textContent = numberFormatter.format(0);

            return { counter: counter, target: target, numberFormatter: numberFormatter };
        }).filter(Boolean);

        const startedAt = performance.now();
        const duration = 1000;

        function updateCounters(now) {
            const progress = Math.min((now - startedAt) / duration, 1);
            const easedProgress = 1 - Math.pow(1 - progress, 3);

            targets.forEach(function (item) {
                item.counter.textContent = item.numberFormatter.format(item.target * easedProgress);
            });

            if (progress < 1) {
                requestAnimationFrame(updateCounters);
            }
        }

        requestAnimationFrame(updateCounters);
    }

    document.querySelectorAll('#natureChartCard [data-nature-toggle]').forEach(function (button) {
        button.addEventListener('click', function () {
            const card = document.getElementById('natureChartCard');
            const front = card.querySelector('.coordinator-flip-front');
            const back = card.querySelector('.coordinator-flip-back');
            const showPie = !card.classList.contains('is-flipped');
            card.classList.toggle('is-flipped', showPie);
            front.inert = showPie;
            back.inert = !showPie;
            front.setAttribute('aria-hidden', String(showPie));
            back.setAttribute('aria-hidden', String(!showPie));
            card.querySelectorAll('[data-nature-toggle]').forEach(function (toggle) {
                toggle.setAttribute('aria-pressed', String(showPie));
            });
        });
    });
</script>
@endsection
