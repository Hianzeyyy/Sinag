@extends('layouts.app')

@section('content')
<div class="container-fluid py-4 coordinator-reports-page">
    <div class="d-flex flex-wrap justify-content-between align-items-end gap-3 mb-4">
        <div>
            <p class="text-uppercase small fw-bold mb-1 coordinator-eyebrow">GAD Office · Report management</p>
            <h1 class="h3 fw-bold mb-1">Report Details & Analytics</h1>
            <p class="text-muted mb-0">Review and update report status, priority, and reporter-visible notes.</p>
        </div>
        <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-primary rounded-pill"><i class="bi bi-graph-up me-1"></i>Analytics dashboard</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success rounded-4">{{ session('success') }}</div>
    @endif
    @if($errors->any())
        <div class="alert alert-danger rounded-4">{{ $errors->first() }}</div>
    @endif

    <div class="row g-3 mb-4">
        @foreach(['Pending', 'Under Review', 'Resolved', 'Emergency'] as $status)
            <div class="col-6 col-lg-3">
                <div class="card border-0 shadow-sm rounded-4 h-100 coordinator-stat">
                    <div class="card-body p-3 p-lg-4">
                        <div class="small text-muted text-uppercase fw-bold">{{ $status }}</div>
                        <div class="fs-2 fw-bold" data-coordinator-count="{{ $reportCountsByStatus[$status] ?? 0 }}">0</div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="row g-4 mb-4">
        <div class="col-lg-5">
            <section class="card border-0 shadow-sm rounded-4 h-100 overflow-hidden">
                <div class="card-header bg-white border-0 p-4"><h2 class="h5 fw-bold mb-0">Reports by status</h2></div>
                <div class="card-body pt-0">
                    @forelse($reportCountsByStatus as $status => $total)
                        @php
                            $maxStatusCount = max(1, (int) $reportCountsByStatus->max());
                        @endphp
                        <div class="analytics-bar-row mb-3">
                            <div class="d-flex justify-content-between small mb-1"><span>{{ $status }}</span><strong>{{ $total }}</strong></div>
                            <div class="analytics-bar-track"><span style="--bar-width:{{ round(($total / $maxStatusCount) * 100) }}%"></span></div>
                        </div>
                    @empty
                        <p class="text-muted mb-0">No report data yet.</p>
                    @endforelse
                </div>
            </section>
        </div>
        <div class="col-lg-7">
            <section class="card border-0 shadow-sm rounded-4 h-100 overflow-hidden">
                <div class="card-header bg-white border-0 p-4"><h2 class="h5 fw-bold mb-0">Most reported categories</h2></div>
                <div class="card-body pt-0">
                    @forelse($reportCategories as $category)
                        @php
                            $maxCategoryCount = max(1, (int) $reportCategories->max('total'));
                        @endphp
                        <div class="analytics-bar-row mb-3">
                            <div class="d-flex justify-content-between small mb-1"><span>{{ $category->label }}</span><strong>{{ $category->total }}</strong></div>
                            <div class="analytics-bar-track"><span style="--bar-width:{{ round(($category->total / $maxCategoryCount) * 100) }}%"></span></div>
                        </div>
                    @empty
                        <p class="text-muted mb-0">No report data yet.</p>
                    @endforelse
                </div>
            </section>
        </div>
        <div class="col-12">
            <section class="card border-0 shadow-sm rounded-4 overflow-hidden">
                <div class="card-header bg-white border-0 p-4">
                    <h2 class="h5 fw-bold mb-1">Reports by priority</h2>
                    <p class="small text-muted mb-0">High-priority cases are surfaced first in the tracking queue.</p>
                </div>
                <div class="card-body pt-0">
                    <div class="row g-3">
                        @forelse($reportCountsByPriority as $priority)
                            @php
                                $priorityPercentage = $totalReports > 0 ? ($priority->total / $totalReports) * 100 : 0;
                            @endphp
                            <div class="col-md-4">
                                <div class="analytics-bar-row mb-0">
                                    <div class="d-flex justify-content-between small mb-1"><span>{{ $priority->priority }}</span><strong>{{ number_format($priority->total) }} ({{ number_format($priorityPercentage, 1) }}%)</strong></div>
                                    <div class="analytics-bar-track"><span style="--bar-width:{{ round($priorityPercentage) }}%"></span></div>
                                </div>
                            </div>
                        @empty
                            <div class="col-12"><p class="text-muted mb-0">No report data yet.</p></div>
                        @endforelse
                    </div>
                </div>
            </section>
        </div>
    </div>

    <section class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="card-header bg-white border-0 p-4 d-flex flex-wrap align-items-center justify-content-between gap-2">
            <div><h2 class="h5 fw-bold mb-1">Report tracking</h2><p class="small text-muted mb-0">Case details and reporter-visible progress updates.</p></div>
            <span class="badge rounded-pill px-3 py-2 coordinator-report-count">{{ $reports->total() }} active reports · High priority first</span>
        </div>
        <div class="table-responsive">
            <table class="table align-middle mb-0 admin-aesthetic-table">
                <thead><tr><th class="ps-4">Case</th><th>Reporter</th><th>Category</th><th>Priority</th><th>Status</th><th>Updated</th><th class="text-end pe-4">Details</th></tr></thead>
                <tbody>
                    @forelse($reports as $report)
                        <tr>
                            <td class="ps-4"><strong>#{{ $report->incident_id }}</strong></td>
                            <td>{{ $report->reporter_display_name }}</td>
                            <td>{{ $report->nature }}</td>
                            <td><span class="badge rounded-pill {{ $report->priority === 'High' ? 'coordinator-high-priority' : 'bg-light text-dark' }}">{{ $report->priority ?: 'Unspecified' }}</span></td>
                            <td><span class="badge rounded-pill bg-light text-dark">{{ $report->status }}</span></td>
                            <td class="small text-muted">{{ $report->updated_at?->diffForHumans() }}</td>
                            <td class="text-end pe-4"><button class="btn btn-sm btn-outline-primary rounded-pill px-3" type="button" data-bs-toggle="modal" data-bs-target="#coordinatorReport{{ $report->id }}">View details</button></td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="text-center text-muted py-5">No active reports.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-3">{{ $reports->links() }}</div>
    </section>

    @foreach($reports as $report)
        <div class="modal fade" id="coordinatorReport{{ $report->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg">
                <div class="modal-content border-0 rounded-4 shadow">
                    <div class="modal-header border-0">
                        <div><div class="small text-uppercase text-muted">Case details</div><h2 class="h5 modal-title fw-bold">#{{ $report->incident_id }}</h2></div>
                        <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row g-3 mb-4">
                            <div class="col-sm-6"><small class="text-muted d-block">Reporter</small><strong>{{ $report->reporter_display_name }}</strong></div>
                            <div class="col-sm-6"><small class="text-muted d-block">Status / Priority</small><strong>{{ $report->status }} · {{ $report->priority }}</strong></div>
                            <div class="col-sm-6"><small class="text-muted d-block">Incident date</small><strong>{{ optional($report->incident_date)->format('M d, Y') ?: 'Not provided' }}</strong></div>
                            <div class="col-sm-6"><small class="text-muted d-block">Location</small><strong>{{ $report->location }}</strong></div>
                            <div class="col-12"><small class="text-muted d-block">Statement</small><div class="rounded-3 bg-light p-3" style="white-space:pre-wrap;">{{ $report->description }}</div></div>
                        </div>
                        <h3 class="h6 fw-bold">Tracking history</h3>
                        <div class="d-grid gap-2 mb-4">
                            @php
                                $reportStatusUpdates = $report->relationLoaded('statusUpdates') ? $report->statusUpdates : collect();
                            @endphp
                            @forelse($reportStatusUpdates as $update)
                                <div class="rounded-3 border p-3">
                                    <div class="d-flex justify-content-between gap-2"><strong>{{ $update->to_status }}</strong><small class="text-muted">{{ $update->created_at->format('M d, Y h:i A') }}</small></div>
                                    @if($update->details)<p class="small text-muted mb-0 mt-2 tracking-note">{{ $update->details }}</p>@endif
                                    @if($update->admin)<small class="text-muted d-block mt-2">Updated by {{ $update->admin->name }}</small>@endif
                                </div>
                            @empty
                                <div class="rounded-3 border p-3"><strong>{{ $report->status }}</strong><small class="d-block text-muted">{{ $report->created_at->format('M d, Y h:i A') }} · Report received</small></div>
                            @endforelse
                        </div>
                        @if($report->evidence_files)
                            <h3 class="h6 fw-bold">Evidence</h3>
                            <div class="row g-3">
                                @foreach($report->evidence_files as $index => $path)
                                    @php
                                        $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));
                                        $isImage = in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp'], true);
                                        $isVideo = $extension === 'mp4' || str_contains($path, '/video/');
                                        $url = route('reports.evidence.view', ['report' => $report->id, 'index' => $index]);
                                    @endphp
                                    <div class="col-md-6">
                                        @if($isImage)<img src="{{ $url }}" alt="Report evidence" class="img-fluid rounded-3">
                                        @elseif($isVideo)<video src="{{ $url }}" class="w-100 rounded-3" controls preload="metadata"></video>
                                        @else<span class="small text-muted">Unsupported legacy attachment.</span>@endif
                                    </div>
                                @endforeach
                            </div>
                        @endif
                        <hr class="my-4">
                        <section aria-label="Update report tracking">
                            <h3 class="h6 fw-bold mb-1">Update report</h3>
                            <p class="small text-muted mb-3">The note is added to the report's tracking history and is visible to the reporter.</p>
                            <form method="POST" action="{{ route('admin.report-tracking.update', ['id' => $report->id, 'report_id' => $report->id]) }}">
                                @csrf
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label small fw-bold" for="coordinatorStatus{{ $report->id }}">Status</label>
                                        <select class="form-select" id="coordinatorStatus{{ $report->id }}" name="status" required>
                                            @foreach(['Pending', 'Under Review', 'Resolved'] as $status)
                                                <option value="{{ $status }}" @selected($report->status === $status)>{{ $status }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label small fw-bold" for="coordinatorPriority{{ $report->id }}">Priority</label>
                                        <select class="form-select" id="coordinatorPriority{{ $report->id }}" name="priority">
                                            @foreach(['Low', 'Medium', 'High'] as $priority)
                                                <option value="{{ $priority }}" @selected($report->priority === $priority)>{{ $priority }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label small fw-bold" for="coordinatorNote{{ $report->id }}">Note for reporter</label>
                                        <textarea class="form-control" id="coordinatorNote{{ $report->id }}" name="admin_comment" rows="3" minlength="5" maxlength="4000" required placeholder="Add a clear case update for the reporter's tracking timeline"></textarea>
                                    </div>
                                    <div class="col-12 d-flex justify-content-end">
                                        <button class="btn coordinator-save-button rounded-pill px-4" type="submit"><i class="bi bi-check2-circle me-1"></i>Save report update</button>
                                    </div>
                                </div>
                            </form>
                        </section>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
</div>
<style>
    .coordinator-eyebrow { color:#5b3ca8;letter-spacing:.1em; }
    .coordinator-reports-page { color:#2b2040; }
    .coordinator-reports-page .card { background:#fff; }
    .analytics-bar-track { height:9px;background:#eee9f8;border-radius:20px;overflow:hidden; }
    .analytics-bar-track span { display:block;width:var(--bar-width);height:100%;border-radius:20px;background:linear-gradient(90deg,#facc15,#34205f);transform:scaleX(0);transform-origin:left;animation:coordinatorBar .7s ease-out forwards; }
    @keyframes coordinatorBar { to { transform:scaleX(1); } }
    .coordinator-stat { border-bottom:3px solid #facc15 !important; }
    .coordinator-report-count { background:#f8f2d9;color:#34205f; }
    .coordinator-high-priority { background:#facc15;color:#34205f; }
    .coordinator-save-button { border:0;background:#34205f;color:#facc15;font-weight:800; }
    .coordinator-save-button:hover { background:#facc15;color:#34205f; }
    .tracking-note { white-space:pre-wrap;overflow-wrap:anywhere; }
    .admin-aesthetic-table thead th { background:#f6f2ff;color:#34205f;font-size:.72rem;letter-spacing:.08em;text-transform:uppercase;border:0;padding:1rem; }
    .admin-aesthetic-table tbody td { border-color:#f0edf7;padding:1rem; }
    .admin-aesthetic-table tbody tr:hover { background:#fbf9ff; }
    .coordinator-reports-page .text-muted { color:#756c89 !important; }
    body[data-admin-theme="dark"] .coordinator-reports-page .card { background:#1b1529;color:#eee8ff; }
    body[data-admin-theme="dark"] .coordinator-reports-page,
    body[data-admin-theme="dark"] .coordinator-reports-page h1,
    body[data-admin-theme="dark"] .coordinator-reports-page h2,
    body[data-admin-theme="dark"] .coordinator-reports-page h3 { color:#eee8ff; }
    body[data-admin-theme="dark"] .coordinator-reports-page .coordinator-eyebrow { color:#facc15; }
    body[data-admin-theme="dark"] .coordinator-reports-page .text-muted,
    body[data-admin-theme="dark"] .coordinator-reports-page .card-header p { color:#c1b6d7 !important; }
    body[data-admin-theme="dark"] .coordinator-reports-page .card-header,
    body[data-admin-theme="dark"] .admin-aesthetic-table thead th { background:#241b35 !important;color:#ddd1ff; }
    body[data-admin-theme="dark"] .coordinator-reports-page .admin-aesthetic-table {
        --bs-table-bg:#1b1529;
        --bs-table-color:#eee8ff;
        --bs-table-border-color:#332846;
        --bs-table-hover-bg:#251d38;
        --bs-table-hover-color:#fff;
        background:#1b1529;
        color:#eee8ff;
    }
    body[data-admin-theme="dark"] .coordinator-reports-page .admin-aesthetic-table tbody td {
        background-color:#1b1529 !important;
        color:#eee8ff !important;
        border-color:#332846;
        box-shadow:inset 0 0 0 9999px #1b1529;
    }
    body[data-admin-theme="dark"] .coordinator-reports-page .admin-aesthetic-table tbody tr:hover td {
        background-color:#251d38 !important;
        box-shadow:inset 0 0 0 9999px #251d38;
    }
    body[data-admin-theme="dark"] .coordinator-reports-page .admin-aesthetic-table .bg-light {
        background:#302348 !important;
        color:#e7ddfb !important;
    }
    body[data-admin-theme="dark"] .coordinator-reports-page .pagination .page-link {
        border-color:#49365f;
        background:#241b35;
        color:#e7ddfb;
    }
    body[data-admin-theme="dark"] .coordinator-reports-page .pagination .page-item.active .page-link {
        border-color:#34205f;
        background:#34205f;
        color:#facc15;
    }
    body[data-admin-theme="dark"] .coordinator-report-count { background:#403819;color:#ffe77a; }
    body[data-admin-theme="dark"] .coordinator-high-priority { background:#5a4918;color:#ffe77a; }
    body[data-admin-theme="dark"] .coordinator-reports-page .modal-content { background:#1b1529;color:#eee8ff; }
    body[data-admin-theme="dark"] .coordinator-reports-page .modal-header { border-color:#3a2d50; }
    body[data-admin-theme="dark"] .coordinator-reports-page .modal-content .form-control,
    body[data-admin-theme="dark"] .coordinator-reports-page .modal-content .form-select { background:#241b35;border-color:#49365f;color:#eee8ff; }
    body[data-admin-theme="dark"] .coordinator-reports-page .modal-content .bg-light { background:#241b35 !important;color:#eee8ff !important; }
    @media(prefers-reduced-motion:reduce) { .analytics-bar-track span { animation:none;transform:none; } }
</style>
<script>
    document.querySelectorAll('[data-coordinator-count]').forEach((counter) => {
        const target = Number(counter.dataset.coordinatorCount || 0);
        const start = performance.now();
        const tick = (now) => {
            const progress = Math.min((now - start) / 650, 1);
            counter.textContent = Math.floor(target * progress).toLocaleString();
            if (progress < 1) requestAnimationFrame(tick);
        };
        requestAnimationFrame(tick);
    });

    (function () {
        const reportId = new URLSearchParams(window.location.search).get('report_id');
        const reportModal = reportId && document.getElementById(`coordinatorReport${reportId}`);
        if (reportModal && window.bootstrap) {
            window.bootstrap.Modal.getOrCreateInstance(reportModal).show();
        }
    })();
</script>
@endsection
