<div class="container-fluid py-4 admin-data-page">
    <div class="d-flex flex-wrap align-items-end justify-content-between gap-3 mb-4">
        <div>
            <p class="text-uppercase small fw-bold mb-1" style="color:#8f72f5;letter-spacing:.1em;">Records</p>
            <h1 class="h3 fw-bold mb-1">Archive</h1>
            <p class="text-muted mb-0">Archived reports and suggestions remain recoverable here.</p>
        </div>
        <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary rounded-pill"><i class="bi bi-arrow-left me-1"></i>Dashboard</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success rounded-4">{{ session('success') }}</div>
    @endif

    <section class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden">
        <div class="card-header bg-white border-0 p-4 d-flex align-items-center justify-content-between">
            <div><h2 class="h5 fw-bold mb-1">Archived Reports</h2><p class="small text-muted mb-0">{{ $archivedReports->total() }} total</p></div>
            <i class="bi bi-folder2-open fs-3" style="color:#8f72f5;"></i>
        </div>
        <div class="table-responsive">
            <table class="table align-middle mb-0 admin-aesthetic-table">
                <thead><tr><th class="ps-4">Case</th><th>Category</th><th>Status</th><th>Archived</th><th class="text-end pe-4">Action</th></tr></thead>
                <tbody>
                    @forelse($archivedReports as $report)
                        <tr>
                            <td class="ps-4"><strong>#{{ $report->incident_id }}</strong><small class="d-block text-muted">{{ $report->reporter_display_name }}</small></td>
                            <td>{{ $report->nature }}</td>
                            <td><span class="badge rounded-pill archive-status-badge">{{ $report->status }}</span></td>
                            <td class="text-muted small">{{ optional($report->archived_at)->format('M d, Y h:i A') }}</td>
                            <td class="text-end pe-4">
                                <div class="d-inline-flex align-items-center gap-2">
                                <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#archiveReportModal{{ $report->id }}">
                                    <i class="bi bi-eye me-1"></i>View report
                                </button>
                                <form method="POST" action="{{ route('admin.archive.reports.restore', $report->id) }}" class="m-0">
                                    @csrf @method('PATCH')
                                    <button class="btn btn-sm btn-outline-primary rounded-pill px-3" type="submit"><i class="bi bi-arrow-counterclockwise me-1"></i>Restore</button>
                                </form>
                                <form method="POST" action="{{ route('admin.archive.reports.delete', $report->id) }}" class="m-0" onsubmit="return confirm('Permanently delete this report and its evidence? This cannot be undone.');">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm archive-delete-btn rounded-pill text-nowrap" type="submit" aria-label="Permanently delete report {{ $report->incident_id }}" title="Permanently delete">
                                        <i class="bi bi-trash3 me-1" aria-hidden="true"></i>Delete permanently
                                    </button>
                                </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center text-muted py-5">No archived reports.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-3">{{ $archivedReports->links() }}</div>
    </section>

    <section class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="card-header bg-white border-0 p-4 d-flex align-items-center justify-content-between">
            <div><h2 class="h5 fw-bold mb-1">Archived Suggestions</h2><p class="small text-muted mb-0">{{ $archivedSuggestions->total() }} total</p></div>
            <i class="bi bi-chat-square-text fs-3" style="color:#8f72f5;"></i>
        </div>
        <div class="table-responsive">
            <table class="table align-middle mb-0 admin-aesthetic-table">
                <thead><tr><th class="ps-4">Category</th><th>Suggestion</th><th>Status</th><th>Archived</th><th class="text-end pe-4">Action</th></tr></thead>
                <tbody>
                    @forelse($archivedSuggestions as $suggestion)
                        <tr>
                            <td class="ps-4 fw-semibold">{{ $suggestion->category ?: 'General' }}</td>
                            <td class="text-break" style="min-width:16rem;">{{ \Illuminate\Support\Str::limit($suggestion->message, 160) }}</td>
                            <td><span class="badge rounded-pill archive-status-badge">{{ $suggestion->status }}</span></td>
                            <td class="text-muted small">{{ optional($suggestion->archived_at)->format('M d, Y h:i A') }}</td>
                            <td class="text-end pe-4">
                                <div class="d-inline-flex align-items-center gap-2">
                                <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#archiveSuggestionModal{{ $suggestion->id }}">
                                    <i class="bi bi-eye me-1"></i>View suggestion
                                </button>
                                <form method="POST" action="{{ route('admin.archive.suggestions.restore', $suggestion->id) }}" class="m-0">
                                    @csrf @method('PATCH')
                                    <button class="btn btn-sm btn-outline-primary rounded-pill px-3" type="submit"><i class="bi bi-arrow-counterclockwise me-1"></i>Restore</button>
                                </form>
                                <form method="POST" action="{{ route('admin.archive.suggestions.delete', $suggestion->id) }}" class="m-0" onsubmit="return confirm('Permanently delete this suggestion and its image? This cannot be undone.');">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm archive-delete-btn rounded-pill text-nowrap" type="submit" aria-label="Permanently delete suggestion {{ $suggestion->id }}" title="Permanently delete">
                                        <i class="bi bi-trash3 me-1" aria-hidden="true"></i>Delete permanently
                                    </button>
                                </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center text-muted py-5">No archived suggestions.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-3">{{ $archivedSuggestions->links() }}</div>
    </section>

    @foreach($archivedReports as $report)
        <div class="modal fade archive-detail-modal" id="archiveReportModal{{ $report->id }}" tabindex="-1" aria-labelledby="archiveReportModalTitle{{ $report->id }}" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-xl">
                <div class="modal-content border-0 rounded-4 shadow">
                    <div class="modal-header border-0 pb-0">
                        <div>
                            <p class="text-uppercase small fw-bold mb-1 archive-modal-eyebrow">Archived report</p>
                            <h2 class="modal-title h4 fw-bold" id="archiveReportModalTitle{{ $report->id }}">#{{ $report->incident_id }} · {{ $report->nature ?: 'Report details' }}</h2>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body pt-3">
                        <div class="row g-3 mb-4">
                            <div class="col-sm-6 col-lg-3"><div class="archive-detail-field"><small>Reported by</small><strong>{{ $report->reporter_display_name }}</strong></div></div>
                            <div class="col-sm-6 col-lg-3"><div class="archive-detail-field"><small>Incident date</small><strong>{{ optional($report->incident_date)->format('M d, Y') ?: 'Not provided' }}</strong></div></div>
                            <div class="col-sm-6 col-lg-3"><div class="archive-detail-field"><small>Priority</small><strong>{{ $report->priority ?: 'Unspecified' }}</strong></div></div>
                            <div class="col-sm-6 col-lg-3"><div class="archive-detail-field"><small>Status</small><strong>{{ $report->status ?: 'Unspecified' }}</strong></div></div>
                            <div class="col-12"><div class="archive-detail-field"><small>Location</small><strong>{{ $report->location ?: 'Not provided' }}</strong></div></div>
                        </div>
                        <h3 class="h6 fw-bold">Description</h3>
                        <p class="archive-detail-description mb-4">{{ $report->description ?: 'No description was provided.' }}</p>
                        <h3 class="h6 fw-bold">Evidence</h3>
                        @if($report->evidence_files)
                            <div class="row g-3">
                                @foreach($report->evidence_files as $index => $path)
                                    @php
                                        $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));
                                        $isImage = in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp'], true);
                                        $isVideo = $extension === 'mp4' || str_contains($path, '/video/');
                                        $evidenceUrl = route('reports.evidence.view', ['report' => $report->id, 'index' => $index]);
                                    @endphp
                                    <div class="col-md-6">
                                        @if($isImage)
                                            <a href="{{ $evidenceUrl }}" target="_blank" rel="noopener noreferrer"><img src="{{ $evidenceUrl }}" alt="Evidence for report #{{ $report->incident_id }}" class="img-fluid rounded-3 archive-evidence-preview"></a>
                                        @elseif($isVideo)
                                            <video src="{{ $evidenceUrl }}" class="w-100 rounded-3" controls preload="metadata"></video>
                                        @else
                                            <a class="btn btn-sm btn-outline-primary rounded-pill" href="{{ $evidenceUrl }}" target="_blank" rel="noopener noreferrer"><i class="bi bi-paperclip me-1"></i>Open evidence file</a>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-muted mb-0">No evidence attached.</p>
                        @endif
                    </div>
                    <div class="modal-footer border-0 pt-0"><button type="button" class="btn btn-outline-secondary rounded-pill px-4" data-bs-dismiss="modal">Close</button></div>
                </div>
            </div>
        </div>
    @endforeach

    @foreach($archivedSuggestions as $suggestion)
        <div class="modal fade archive-detail-modal" id="archiveSuggestionModal{{ $suggestion->id }}" tabindex="-1" aria-labelledby="archiveSuggestionModalTitle{{ $suggestion->id }}" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg">
                <div class="modal-content border-0 rounded-4 shadow">
                    <div class="modal-header border-0 pb-0">
                        <div>
                            <p class="text-uppercase small fw-bold mb-1 archive-modal-eyebrow">Archived suggestion</p>
                            <h2 class="modal-title h4 fw-bold" id="archiveSuggestionModalTitle{{ $suggestion->id }}">{{ $suggestion->category ?: 'General suggestion' }}</h2>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body pt-3">
                        <div class="row g-3 mb-4">
                            <div class="col-sm-6"><div class="archive-detail-field"><small>Submitted by</small><strong>{{ $suggestion->is_anonymous ? 'Anonymous' : ($suggestion->user?->name ?: 'Unknown') }}</strong></div></div>
                            <div class="col-sm-6"><div class="archive-detail-field"><small>Status</small><strong>{{ $suggestion->status ?: 'Unspecified' }}</strong></div></div>
                        </div>
                        <h3 class="h6 fw-bold">Suggestion</h3>
                        <p class="archive-detail-description mb-4">{{ $suggestion->message ?: 'No suggestion details were provided.' }}</p>
                        @if($imageUrl = \App\Support\MediaResolver::url($suggestion->image_path, ['public', 'public_s3']))
                            <h3 class="h6 fw-bold">Attachment</h3>
                            <img src="{{ $imageUrl }}" alt="Suggestion attachment" class="img-fluid rounded-3 archive-evidence-preview">
                        @endif
                    </div>
                    <div class="modal-footer border-0 pt-0"><button type="button" class="btn btn-outline-secondary rounded-pill px-4" data-bs-dismiss="modal">Close</button></div>
                </div>
            </div>
        </div>
    @endforeach
</div>
<style>
    .admin-data-page .card { background:#fff; }
    .admin-aesthetic-table thead th { background:#f6f2ff; color:#5b4b9b; font-size:.72rem; letter-spacing:.08em; text-transform:uppercase; border:0; padding:1rem; }
    .admin-aesthetic-table tbody td { border-color:#f0edf7; padding:1rem; }
    .admin-aesthetic-table tbody tr:hover { background:#fbf9ff; }
    .archive-delete-btn { background:#fff0f4; border:1px solid #e8a7be; color:#9b2856; font-weight:700; }
    .archive-delete-btn:hover { background:#ffe0e9; border-color:#d97b9e; color:#7e1d45; }
    .archive-detail-modal .modal-content { background:#fff; color:#241d35; }
    .archive-modal-eyebrow { color:#7658df; letter-spacing:.1em; }
    .archive-detail-field { height:100%; padding:1rem; border:1px solid #eee9f8; border-radius:1rem; background:#fbfaff; }
    .archive-detail-field small { display:block; margin-bottom:.35rem; color:#756b88; }
    .archive-detail-field strong { display:block; overflow-wrap:anywhere; }
    .archive-detail-description { padding:1rem; border-radius:1rem; background:#f8f6fc; white-space:pre-wrap; overflow-wrap:anywhere; }
    .archive-evidence-preview { max-height:24rem; object-fit:contain; background:#f5f2fa; }
    body[data-admin-theme="dark"] .admin-data-page .card { background:#1b1529; color:#eee8ff; }
    body[data-admin-theme="dark"] .admin-data-page .card-header,
    body[data-admin-theme="dark"] .admin-aesthetic-table thead th { background:#241b35 !important; color:#ddd1ff; }
    body[data-admin-theme="dark"] .admin-aesthetic-table tbody td { color:#eee8ff; border-color:#332846; }
    body[data-admin-theme="dark"] .admin-aesthetic-table tbody tr:hover { background:#251d38; }
    body[data-admin-theme="dark"] .archive-status-badge { background:#302348 !important; color:#ddd1ff !important; }
    body[data-admin-theme="dark"] .archive-delete-btn { background:#4a2035; border-color:#8b3d5d; color:#ffd0df; }
    body[data-admin-theme="dark"] .archive-delete-btn:hover { background:#5a2440; border-color:#a34b70; color:#fff; }
    body[data-admin-theme="dark"] .archive-detail-modal .modal-content { background:#1b1529; color:#eee8ff; }
    body[data-admin-theme="dark"] .archive-detail-field { border-color:#3a2d50; background:#241b35; }
    body[data-admin-theme="dark"] .archive-detail-field small { color:#c3b4df; }
    body[data-admin-theme="dark"] .archive-detail-description { background:#241b35; }
    body[data-admin-theme="dark"] .archive-evidence-preview { background:#241b35; }
</style>
