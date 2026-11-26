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
</div>
<style>
    .admin-data-page .card { background:#fff; }
    .admin-aesthetic-table thead th { background:#f6f2ff; color:#5b4b9b; font-size:.72rem; letter-spacing:.08em; text-transform:uppercase; border:0; padding:1rem; }
    .admin-aesthetic-table tbody td { border-color:#f0edf7; padding:1rem; }
    .admin-aesthetic-table tbody tr:hover { background:#fbf9ff; }
    .archive-delete-btn { background:#fff0f4; border:1px solid #e8a7be; color:#9b2856; font-weight:700; }
    .archive-delete-btn:hover { background:#ffe0e9; border-color:#d97b9e; color:#7e1d45; }
    body[data-admin-theme="dark"] .admin-data-page .card { background:#1b1529; color:#eee8ff; }
    body[data-admin-theme="dark"] .admin-data-page .card-header,
    body[data-admin-theme="dark"] .admin-aesthetic-table thead th { background:#241b35 !important; color:#ddd1ff; }
    body[data-admin-theme="dark"] .admin-aesthetic-table tbody td { color:#eee8ff; border-color:#332846; }
    body[data-admin-theme="dark"] .admin-aesthetic-table tbody tr:hover { background:#251d38; }
    body[data-admin-theme="dark"] .archive-status-badge { background:#302348 !important; color:#ddd1ff !important; }
    body[data-admin-theme="dark"] .archive-delete-btn { background:#4a2035; border-color:#8b3d5d; color:#ffd0df; }
    body[data-admin-theme="dark"] .archive-delete-btn:hover { background:#5a2440; border-color:#a34b70; color:#fff; }
</style>
