<div class="container-fluid py-4 admin-data-page">
    <div class="mb-4">
        <p class="text-uppercase small fw-bold mb-1" style="color:#8f72f5;letter-spacing:.1em;">Administration</p>
        <h1 class="h3 fw-bold mb-1">Recent Account Activity</h1>
        <p class="text-muted mb-0">Shows the latest recorded visit for each account.</p>
    </div>
    <div class="alert border-0 rounded-4 small mb-4" style="background:#f3efff;color:#5b4b9b;">
        <i class="bi bi-info-circle me-1"></i>This view uses each account's last-seen timestamp; it is not a log of individual actions.
    </div>
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="table-responsive">
            <table class="table align-middle mb-0 admin-aesthetic-table">
                <thead><tr><th class="ps-4">Account</th><th>Account Type</th><th>Department</th><th>Last Active</th><th class="text-end pe-4">Presence</th></tr></thead>
                <tbody>
                    @forelse($recentUsers as $user)
                        @php
                            $isRecent = $user->last_seen_at && $user->last_seen_at->greaterThan(now()->subMinutes(5));
                        @endphp
                        <tr>
                            <td class="ps-4">
                                <div class="d-flex align-items-center gap-3">
                                    <img src="{{ route('profile.photo', $user) }}" alt="" class="rounded-circle" style="width:40px;height:40px;object-fit:cover;">
                                    <div><strong>{{ $user->name }}</strong><small class="d-block text-muted">{{ $user->email }}</small></div>
                                </div>
                            </td>
                            <td>{{ ucfirst(str_replace('_', ' ', $user->account_type ?? $user->role)) }}</td>
                            <td>{{ $user->department ?: '—' }}</td>
                            <td class="small text-muted">{{ $user->last_seen_at?->format('M d, Y h:i A') ?: 'Not recorded' }}</td>
                            <td class="text-end pe-4"><span class="badge rounded-pill {{ $isRecent ? 'bg-success-subtle text-success' : 'bg-secondary-subtle text-secondary' }}">{{ $isRecent ? 'Online recently' : 'Offline' }}</span></td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center text-muted py-5">No account activity has been recorded yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-3">{{ $recentUsers->links() }}</div>
    </div>
</div>
<style>
    .admin-data-page .card { background:#fff; }
    .admin-aesthetic-table thead th { background:#f6f2ff; color:#5b4b9b; font-size:.72rem; letter-spacing:.08em; text-transform:uppercase; border:0; padding:1rem; }
    .admin-aesthetic-table tbody td { border-color:#f0edf7; padding:1rem; }
    .admin-aesthetic-table tbody tr:hover { background:#fbf9ff; }
    body[data-admin-theme="dark"] .admin-data-page .card { background:#1b1529; color:#eee8ff; }
    body[data-admin-theme="dark"] .admin-aesthetic-table thead th { background:#241b35; color:#ddd1ff; }
    body[data-admin-theme="dark"] .admin-aesthetic-table tbody td { color:#eee8ff; border-color:#332846; }
    body[data-admin-theme="dark"] .admin-aesthetic-table tbody tr:hover { background:#251d38; }
</style>
