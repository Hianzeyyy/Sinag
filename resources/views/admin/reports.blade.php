@extends('layouts.app')

@section('content')
<style>
    .reports-table-wrap { overscroll-behavior-x: contain; }
    .reports-table-wrap th:first-child,
    .reports-table-wrap td:first-child {
        position: sticky;
        left: 0;
        z-index: 2;
        min-width: 8.5rem;
        background: #fff;
    }
    .reports-table-wrap thead th:first-child {
        z-index: 3;
        background: #f8f9fa;
    }
    .reports-table-wrap table { min-width: 1080px; }
    .report-tracking-cell { min-width: 190px; max-width: 250px; }
    .report-tracking-count { display:inline-flex; align-items:center; gap:.35rem; color:#5b3ca8; font-size:.75rem; font-weight:800; }
    .report-tracking-latest { display:block; margin-top:.2rem; color:#746b82; font-size:.72rem; line-height:1.35; }
    body[data-admin-theme="dark"] .report-tracking-count { color:#d5c6ff; }
    body[data-admin-theme="dark"] .report-tracking-latest { color:#bdb1d1; }
    .reporter-avatar { background: #f3e8ff; }
    .reporter-avatar-priority { background: #fef3c7; }
    .reporter-alias-icon { color: #0d6efd; }
    .reporter-priority-icon { color: #806000; }
    body[data-admin-theme="dark"] .reports-table-wrap .reporter-avatar { background: #342645 !important; }
    body[data-admin-theme="dark"] .reports-table-wrap .reporter-avatar-priority { background: #44371a !important; }
    body[data-admin-theme="dark"] .reports-table-wrap .reporter-alias-icon { color: #d5c6ff !important; }
    body[data-admin-theme="dark"] .reports-table-wrap .reporter-priority-icon { color: #f8cb12 !important; }
</style>
<style>
    /* From Uiverse.io by vinodjangid07 - https://uiverse.io/vinodjangid07/smart-emu-83
       MIT License, Copyright (c) vinodjangid07 (Vinod Jangid) */
    .report-archive-button {
        position:relative; display:inline-flex; width:50px; height:50px; align-items:center; justify-content:center;
        overflow:hidden; border:0 !important; border-radius:50%; background:#211936 !important; color:#fff !important;
        box-shadow:0 0 20px rgba(0,0,0,.16); transition:width .3s ease,border-radius .3s ease,background-color .3s ease;
    }
    .report-archive-button::before { position:absolute; top:-20px; left:50%; content:"Archive"; color:#fff; font-size:2px; opacity:0; transform:translateX(-50%); transition:all .3s ease; }
    .report-archive-button i { font-size:12px; transition:transform .3s ease; }
    .report-archive-button:hover,.report-archive-button:focus-visible { width:140px; border-radius:50px; background:#ff4545 !important; }
    .report-archive-button:hover::before,.report-archive-button:focus-visible::before { font-size:13px; opacity:1; transform:translate(-50%,30px); }
    .report-archive-button:hover i,.report-archive-button:focus-visible i { transform:translateY(60%); }
    .report-archive-button:focus-visible { outline:3px solid #a594f9; outline-offset:2px; }

    .sinag-violet-badge { background: #d8cdfc; color: #5b4b9b; border-color: #c4b5fd !important; }
    .sinag-yellow-badge { background: #f8cb12; color: #4a3500; border-color: #e5b900 !important; }
    
</style>
<div class="container py-4">
    @if($errors->any())
        <div class="alert alert-danger rounded-3" role="alert">{{ $errors->first() }}</div>
    @endif
    @if(session('success'))
        <div class="alert alert-success rounded-3" role="status">{{ session('success') }}</div>
    @endif
    @if(session('warning'))
        <div class="alert alert-warning rounded-3" role="alert">{{ session('warning') }}</div>
    @endif
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            
            <li class="breadcrumb-item active fw-bold" aria-current="page" style="color: #A594F9;">
    Report Management
</li>
        </ol>
    </nav>

    <div class="row">
        <div class="col-12">
            <div class="card shadow-lg border-0 rounded-4" style="overflow: hidden;">
                <div style="height: 5px; background: #A594F9;"></div>
                
                <div class="card-header bg-white py-4 d-flex justify-content-between align-items-center border-0">
                    <div>
                        <h4 class="fw-bold mb-0 text-dark">Confidential Abuse Inbox</h4>
                        <p class="text-muted small mb-0">Managing <span class="badge bg-primary-subtle text-primary">{{ $reports->total() }} {{ request()->filled('report_id') && $reports->first()?->archived_at ? 'Archived Report' : 'Active Cases' }}</span> </p>
                    </div>
                    <div class="d-flex gap-2">
                        <form method="GET" action="{{ route('admin.reports') }}" class="input-group input-group-sm" style="width: 250px;">
                            <span class="input-group-text bg-light border-end-0"><i class="bi bi-search text-muted"></i></span>
                            <input type="text" name="q" id="reportSearch" value="{{ request('q') }}" maxlength="100" class="form-control bg-light border-start-0" placeholder="Search Incident ID or Alias...">
                        </form>
                    </div>
                </div>
                
                <div class="card-body p-0">
                    <div class="table-responsive reports-table-wrap">
                        <table class="table table-hover align-middle mb-0" id="reportsTable">
                            <thead class="bg-light text-muted small">
                                <tr>
                                    <th class="ps-4 py-3">INCIDENT ID</th>
                                    <th>DATE FILED</th>
                                    <th>REPORTED BY</th>
                                    <th>NATURE OF ABUSE</th>
                                    <th>PRIORITY</th>
                                    <th>STATUS</th>
                                    <th>REPORT TRACKING</th>
                                    <th class="text-end pe-4">ACTION</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($reports as $report)
                                @php
                                    $reportStatusUpdates = $report->relationLoaded('statusUpdates') ? $report->statusUpdates : collect();
                                    $latestStatusUpdate = $reportStatusUpdates->last();
                                @endphp
                                <tr>
                                    <td class="ps-4"><span class="fw-bold text-dark">#{{ $report->incident_id }}</span></td>
                                    <td class="small text-muted">{{ $report->created_at->format('M d, Y') }}</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="reporter-avatar {{ $report->priority == 'High' ? 'reporter-avatar-priority' : '' }} rounded-circle d-flex align-items-center justify-content-center me-2"
                                                 style="width: 30px; height: 30px;">
                                                <i class="bi {{ $report->priority == 'High' ? 'bi-shield-exclamation reporter-priority-icon' : 'bi-incognito reporter-alias-icon' }}"></i>
                                            </div>
                                            <div class="d-flex align-items-center gap-2">
                                                <span class="small fw-medium text-secondary">{{ $report->reporter_display_name }}</span>
                                                <span id="seen-badge-{{ $report->id }}" class="badge rounded-pill {{ is_null($report->seen_at) ? 'sinag-yellow-badge' : 'sinag-violet-badge' }}">
                                                    {{ is_null($report->seen_at) ? 'UNREAD' : 'READ' }}
                                                </span>
                                                @if(is_null($report->seen_at) && $report->created_at->gt(now()->subDay()))
                                                    <span id="new-badge-{{ $report->id }}" class="badge rounded-pill sinag-yellow-badge">NEW</span>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td><span class="small">{{ $report->nature }}</span></td>
                                    <td>
                                        <span class="badge border {{ $report->priority == 'High' ? 'sinag-yellow-badge pulse-animation' : 'sinag-violet-badge' }}">
                                            {{ $report->priority }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge rounded-pill px-3 py-2 border
                                            {{ $report->status == 'Pending' ? 'sinag-yellow-badge' : '' }}
                                            {{ $report->status == 'Under Review' ? 'sinag-violet-badge' : '' }}
                                            {{ $report->status == 'Resolved' ? 'sinag-violet-badge' : '' }}">
                                            {{ $report->status }}
                                        </span>
                                    </td>
                                    <td class="report-tracking-cell">
                                        <span class="report-tracking-count"><i class="bi bi-clock-history"></i>{{ $reportStatusUpdates->count() }} timeline {{ \Illuminate\Support\Str::plural('update', $reportStatusUpdates->count()) }}</span>
                                        @if($latestStatusUpdate)
                                            <span class="report-tracking-latest"><strong>{{ $latestStatusUpdate->to_status }}</strong>@if($latestStatusUpdate->created_at) · {{ $latestStatusUpdate->created_at->format('M d, Y') }}@endif</span>
                                            @if($latestStatusUpdate->details)
                                                <span class="report-tracking-latest">{{ \Illuminate\Support\Str::limit($latestStatusUpdate->details, 65) }}</span>
                                            @endif
                                        @else
                                            <span class="report-tracking-latest">Awaiting the first admin update.</span>
                                        @endif
                                    </td>
                                    <td class="text-end pe-4">
                                        <button class="sinag-review-button"
                                                data-bs-toggle="modal" 
                                                data-bs-target="#modal-{{ $report->id }}"
                                                data-report-id="{{ $report->id }}">
                                            <span>Review</span>
                                        </button>
                                        @unless($report->archived_at)
                                            <form action="{{ route('admin.reports.delete', $report->id) }}" method="POST" style="display:inline-block;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                        class="btn btn-sm rounded-pill ms-2 text-white report-archive-button"
                                                        aria-label="Move report {{ $report->incident_id }} to archive"
                                                        title="Archive report"
                                                        onclick="return confirm('Move this report to the archive?')">
                                                    <i class="bi bi-archive" aria-hidden="true"></i>
                                                </button>
                                            </form>
                                        @endunless
                                    </td>
                                </tr>

                                <div class="modal fade" id="modal-{{ $report->id }}" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered modal-lg">
                                        <div class="modal-content border-0 rounded-4 shadow">
                                            <div class="modal-header border-0 pb-0">
                                                <h5 class="fw-bold mt-2 ps-2">Incident Report Details</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body p-4">
                                                <div class="d-flex justify-content-end mb-3">
                                                    <a href="{{ route('admin.reports.pdf', $report->id) }}" class="btn btn-outline-primary rounded-pill" target="_blank">
                                                        <i class="bi bi-file-earmark-pdf me-1"></i> Download PDF
                                                    </a>
                                                </div>
                                                <div class="row g-4">
                                                    <div class="col-md-6">
                                                        <label class="small text-muted d-block">Incident ID</label>
                                                        <span class="fw-bold">#{{ $report->incident_id }}</span>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="small text-muted d-block">{{ $report->is_anonymous ? 'Reporter Alias' : 'Reporter Name' }}</label>
                                                        <span class="fw-bold text-primary">{{ $report->reporter_display_name }}</span>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="small text-muted d-block">Location of Incident</label>
                                                        <span class="fw-medium"><i class="bi bi-geo-alt text-danger me-1"></i> {{ $report->location }}</span>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="small text-muted d-block">Nature of Abuse</label>
                                                        <span class="badge bg-light text-dark border">{{ $report->nature }}</span>
                                                    </div>
                                                    
                                                    <div class="col-12">
                                                        <label class="small text-muted d-block">Statement of Facts</label>
                                                        <div class="p-3 bg-light rounded-3 small text-dark border shadow-sm mt-1" style="line-height: 1.6;">
                                                            {{ $report->description }}
                                                        </div>
                                                    </div>

                                                    <div class="col-12">
                                                        <label class="small text-muted d-block mb-2">Attached Evidence</label>
                                                        @php
                                                            $evidenceFiles = $report->evidence_files;
                                                        @endphp
                                                        @if(count($evidenceFiles))
                                                            <div class="row g-3">
                                                                @foreach($evidenceFiles as $idx => $evidencePath)
                                                                    @php
                                                                        $extension = pathinfo($evidencePath, PATHINFO_EXTENSION);
                                                                        $isImage = in_array(strtolower($extension), ['jpg', 'jpeg', 'png', 'gif', 'webp']);
                                                                        $isVideo = strtolower($extension) === 'mp4' || str_contains($evidencePath, '/video/');
                                                                        $evidenceUrl = route('reports.evidence.view', ['report' => $report->id, 'index' => $idx]);
                                                                    @endphp
                                                                    <div class="col-12 col-md-6 col-lg-4">
                                                                        @if($isImage)
                                                                            <div class="p-2 border rounded-3 bg-light text-center h-100">
                                                                                <img src="{{ $evidenceUrl }}"
                                                                                     class="img-fluid rounded shadow-sm"
                                                                                     style="max-height: 220px; cursor: zoom-in;"
                                                                                     onclick="window.open(this.src)">
                                                                                <p class="small text-muted mt-2 mb-0"><i class="bi bi-info-circle me-1"></i>Click image to preview</p>
                                                                            </div>
                                                                        @elseif($isVideo)
                                                                            <div class="p-2 border rounded-3 bg-light">
                                                                                <video class="w-100 rounded" controls preload="metadata" src="{{ $evidenceUrl }}" aria-label="Attached report video"></video>
                                                                                <p class="small text-muted mt-2 mb-0"><i class="bi bi-camera-video me-1"></i>MP4 video evidence</p>
                                                                            </div>
                                                                        @else
                                                                            <div class="p-3 border rounded-3 bg-light text-muted small">Unsupported legacy attachment.</div>
                                                                        @endif
                                                                    </div>
                                                                @endforeach
                                                            </div>
                                                        @else
                                                            <div class="p-3 border border-dashed rounded-3 text-center bg-light text-muted">
                                                                <small><i class="bi bi-camera-video-off me-1"></i> No media or files attached to this report.</small>
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>

                                                @if(!$report->archived_at)
                                                <hr class="my-4">
                                                <form action="{{ route('admin.reports.update', $report->id) }}" method="POST">
                                                    @csrf
                                                    <input type="hidden" name="_report_id" value="{{ $report->id }}">
                                                    <div class="row g-3">
                                                        <div class="col-md-6">
                                                            <label class="small fw-bold mb-1">Update Case Status</label>
                                                            <select name="status" class="form-select shadow-sm">
                                                                <option value="Pending" {{ $report->status == 'Pending' ? 'selected' : '' }}>Pending</option>
                                                                <option value="Under Review" {{ $report->status == 'Under Review' ? 'selected' : '' }}>Under Review</option>
                                                                <option value="Resolved" {{ $report->status == 'Resolved' ? 'selected' : '' }}>Resolved</option>
                                                            </select>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <label class="small fw-bold mb-1">Priority Level</label>
                                                            <select name="priority" class="form-select shadow-sm">
                                                                <option value="Low" {{ $report->priority == 'Low' ? 'selected' : '' }}>Low Priority</option>
                                                                <option value="Medium" {{ $report->priority == 'Medium' ? 'selected' : '' }}>Medium Priority</option>
                                                                <option value="High" {{ $report->priority == 'High' ? 'selected' : '' }}>High Priority / Urgent</option>
                                                            </select>
                                                        </div>
                                                        <div class="col-12">
                                                            <label class="small fw-bold mb-1" for="reportAdminComment{{ $report->id }}">Comment for the Reporter</label>
                                                            <textarea id="reportAdminComment{{ $report->id }}" name="admin_comment" class="form-control shadow-sm" rows="3" maxlength="4000" placeholder="Add the detailed case update the reporter can view">{{ old('admin_comment') }}</textarea>
                                                            <div class="form-text report-tracking-helper">Each saved update appears in the reporter's tracking timeline.</div>
                                                        </div>
                                                        <div class="col-12">
                                                            <div class="form-check form-switch report-coordinator-forward rounded-3 p-3 ps-5">
                                                                <input class="form-check-input" type="checkbox" name="send_to_coordinator" value="1" id="sendToCoordinator{{ $report->id }}" @checked(old('_report_id') == $report->id && old('send_to_coordinator') === '1')>
                                                                <label class="form-check-label" for="sendToCoordinator{{ $report->id }}">
                                                                    <strong class="d-block">Send this report to the GAD Coordinator</strong>
                                                                    <span class="small">Recommended for high-priority cases. The case reference and status will be sent, and this action will be recorded in the tracking timeline.</span>
                                                                </label>
                                                            </div>
                                                        </div>
                                                        <div class="col-12">
                                                            <section class="report-tracking-timeline rounded-4 border p-3" aria-label="Report tracking timeline">
                                                                <div class="d-flex flex-wrap justify-content-between align-items-start gap-2 mb-3">
                                                                    <div>
                                                                        <h6 class="fw-bold mb-1">Report tracking timeline</h6>
                                                                        <p class="small text-muted mb-0">Monitor submission and every recorded case update.</p>
                                                                    </div>
                                                                    <span class="badge rounded-pill bg-light text-dark border">Current: {{ $report->status ?: 'Pending' }}</span>
                                                                </div>
                                                                <ol class="report-status-timeline mb-0">
                                                                    <li>
                                                                        <strong>Report submitted</strong>
                                                                        <span class="d-block small text-muted">{{ $report->created_at?->format('M d, Y h:i A') ?? 'Submission time unavailable' }}</span>
                                                                        <p class="small mb-0 mt-1">Received by the GAD Office with {{ strtolower((string) ($report->priority ?: 'Normal')) }} priority.</p>
                                                                    </li>
                                                                    @forelse($reportStatusUpdates as $update)
                                                                        <li>
                                                                            <strong>{{ $update->from_status ?: 'Case' }} → {{ $update->to_status }}</strong>
                                                                            <span class="d-block small text-muted">{{ $update->created_at?->format('M d, Y h:i A') ?? 'Update time unavailable' }}</span>
                                                                            @if($update->details)
                                                                                <p class="small mb-0 mt-1 report-status-detail">{{ $update->details }}</p>
                                                                            @endif
                                                                        </li>
                                                                    @empty
                                                                        <li class="report-status-pending">
                                                                            <strong>Awaiting first admin progress update</strong>
                                                                            <span class="d-block small text-muted">Add a status update and reporter-facing details below to continue tracking.</span>
                                                                        </li>
                                                                    @endforelse
                                                                </ol>
                                                            </section>
                                                        </div>
                                                        <div class="col-12 mt-4">
                                                            <button type="submit" class="btn btn-primary w-100 py-2 rounded-pill shadow" style="background: #A594F9; border:none;">
                                                                <i class="bi bi-check-circle me-1"></i> Confirm Update
                                                            </button>
                                                        </div>
                                                    </div>
                                                </form>
                                                @else
                                                    <div class="alert alert-secondary mb-0" role="status">This report is archived and available for viewing only.</div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @empty
                                <tr>
                                    <td colspan="8" class="text-center py-5">
                                        <div class="text-muted">
                                            <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                            <p class="mb-0">No confidential reports found.</p>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="d-flex justify-content-center mt-3">{{ $reports->links() }}</div>

    
</div>

<script>
    // Live Search Functionality
    document.getElementById('reportSearch').addEventListener('keyup', function() {
        let filter = this.value.toUpperCase();
        let rows = document.querySelector("#reportsTable tbody").rows;
        
        for (let i = 0; i < rows.length; i++) {
            let idCol = rows[i].cells[0].textContent.toUpperCase();
            let aliasCol = rows[i].cells[2].textContent.toUpperCase();
            if (idCol.indexOf(filter) > -1 || aliasCol.indexOf(filter) > -1) {
                rows[i].style.display = "";
            } else {
                rows[i].style.display = "none";
            }      
        }
    });

    // Mark report as seen when admin opens the details modal.
    document.querySelectorAll('[id^="modal-"]').forEach((modalEl) => {
        modalEl.addEventListener('shown.bs.modal', function () {
            const reportId = this.id.replace('modal-', '');
            fetch(`{{ url('/admin/reports') }}/${reportId}/seen`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                },
            }).then(() => {
                const seenBadge = document.getElementById(`seen-badge-${reportId}`);
                const newBadge = document.getElementById(`new-badge-${reportId}`);

                if (seenBadge) {
                    seenBadge.className = 'badge rounded-pill sinag-violet-badge';
                    seenBadge.textContent = 'READ';
                }

                if (newBadge) {
                    newBadge.remove();
                }
            }).catch(() => {
                // Fail silently: UI will update on next full page load.
            });
        });
    });

    window.addEventListener('load', function () {
        const reportId = @json(request('report_id'));
        const modal = reportId ? document.getElementById(`modal-${reportId}`) : null;
        if (modal) window.bootstrap.Modal.getOrCreateInstance(modal).show();
    });
</script>

<style>
    .report-tracking-timeline { background:#fff; border-color:#e8e1f5 !important; }
    .report-tracking-helper { color:#6f6680; }
    .report-archive-button { background:#ee529e !important; border-color:#ee529e !important; }
    .report-coordinator-forward { background:#f7f4ff; border:1px solid #e8e1f5; }
    .report-coordinator-forward .form-check-input { margin-left:-2rem; }
    .report-status-timeline { list-style:none; position:relative; padding:0 0 0 1.35rem; }
    .report-status-timeline::before { content:""; position:absolute; top:.3rem; bottom:.5rem; left:.35rem; width:2px; background:#ddd3f3; }
    .report-status-timeline li { position:relative; padding:0 0 1.1rem; }
    .report-status-timeline li:last-child { padding-bottom:0; }
    .report-status-timeline li::before { content:""; position:absolute; top:.25rem; left:-1.29rem; width:.65rem; height:.65rem; border:2px solid #8f72f5; border-radius:50%; background:#fff; }
    .report-status-timeline .report-status-pending::before { border-color:#c4b5fd; background:#eee8ff; }
    .report-status-detail { white-space:pre-wrap; overflow-wrap:anywhere; }
    body[data-admin-theme="dark"] .report-tracking-timeline { background:#1b1529 !important; border-color:#3a2d50 !important; }
    body[data-admin-theme="dark"] .report-tracking-helper { color:#d5c6e8 !important; }
    body[data-admin-theme="dark"] .report-coordinator-forward { background:#241b35; border-color:#49365f; color:#eee8ff; }
    body[data-admin-theme="dark"] .report-status-timeline::before { background:#49365f; }
    body[data-admin-theme="dark"] .report-status-timeline li::before { background:#1b1529; }
    body[data-admin-theme="dark"] .report-status-timeline .report-status-pending::before { background:#302348; }

    .table tbody tr:hover {
        background-color: #fbfaff !important;
        transition: 0.2s;
    }
    .badge { font-weight: 600; font-size: 0.75rem; }
    .pulse-animation { animation: pulse-red 2s infinite; }
    @keyframes pulse-red {
        0% { opacity: 1; transform: scale(1); }
        50% { opacity: 0.7; transform: scale(0.95); }
        100% { opacity: 1; transform: scale(1); }
    }
    .border-dashed { border-style: dashed !important; border-width: 2px !important; }
</style>
@endsection