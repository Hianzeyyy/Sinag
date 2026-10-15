@extends('layouts.app')

@section('content')

<div class="fw-wrap">
    <div class="fw-topbar">
        <div class="fw-topbar-left">
            <div class="fw-icon-box">
                <i class="bi bi-megaphone-fill"></i>
            </div>
            <div>
                <h1 class="fw-page-title">Participatory Suggestions</h1>
                <p class="fw-page-sub">Review, approve, or remove suggestions shared by users.</p>
            </div>
        </div>
        <div class="fw-count-chip">
            <span class="fw-count-num">{{ $suggestions->count() }}</span>
            <span class="fw-count-text">{{ $suggestions->count() == 1 ? 'Post' : 'Posts' }}</span>
        </div>
        <a href="{{ route('admin.suggestions.reports') }}" class="btn btn-outline-secondary rounded-pill fw-bold">
            <i class="bi bi-flag me-1"></i> View Reports @if(($pendingSuggestionReports ?? 0) > 0)<span class="badge bg-secondary ms-1">{{ $pendingSuggestionReports }}</span>@endif
        </a>
    </div>

    <div class="card border-0 shadow-sm rounded-4 overflow-hidden fw-table-card">
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">Category</th>
                        <th>Submitted by</th>
                        <th>Suggestion</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th class="text-end pe-4">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($suggestions as $suggestion)
                        @php
                            $authorName = $suggestion->is_anonymous
                                ? 'Anonymous'
                                : (optional($suggestion->user)->cloak_alias ?: optional($suggestion->user)->name ?: 'Unknown user');
                            $statusLabel = $suggestion->admin_status_label;
                        @endphp
                        <tr>
                            <td class="ps-4 fw-semibold">{{ $suggestion->category ?: 'General' }}</td>
                            <td>{{ $authorName }}</td>
                            <td class="text-break" style="min-width:16rem;max-width:32rem;">{{ \Illuminate\Support\Str::limit($suggestion->message, 180) }}</td>
                            <td>
                                @if($statusLabel !== '')
                                    <span class="badge rounded-pill {{ strtolower($statusLabel) === 'approved' ? 'sinag-status-approved' : (strtolower($statusLabel) === 'rejected' ? 'sinag-status-rejected' : 'sinag-status-pending') }}">{{ $statusLabel }}</span>
                                @endif
                            </td>
                            <td class="text-nowrap">{{ optional($suggestion->created_at)->format('M d, Y') }}</td>
                            <td class="text-end text-nowrap pe-4">
                                <button type="button" class="btn btn-sm btn-outline-primary rounded-pill" data-bs-toggle="modal" data-bs-target="#suggestionModal{{ $suggestion->id }}">
                                    <i class="bi bi-eye me-1"></i>View
                                </button>
                                <form action="{{ route('admin.suggestions.delete', $suggestion->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this suggestion?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill" aria-label="Delete suggestion">
                                        <i class="bi bi-trash3"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center py-5 text-muted">No suggestions have been submitted yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @foreach($suggestions as $suggestion)
        <div class="modal fade" id="suggestionModal{{ $suggestion->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content border-0 shadow-lg">
                    <div class="modal-header">
                        <div>
                            <div class="text-muted small text-uppercase">{{ $suggestion->category }}</div>
                            <h5 class="modal-title fw-bold">Suggestion #{{ $suggestion->id }}</h5>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="d-flex justify-content-between gap-3 mb-3">
                            <span class="fw-semibold">{{ $suggestion->is_anonymous ? 'Anonymous' : optional($suggestion->user)->name }}</span>
                            <span class="text-muted small">{{ $suggestion->created_at->format('M d, Y h:i A') }}</span>
                        </div>
                        <div class="p-3 rounded-3 border bg-light" style="white-space:pre-wrap;">{{ $suggestion->message }}</div>
                        @if($suggestion->admin_remarks)
                            <div class="alert alert-light border mt-3 mb-0"><strong>Previous review comment:</strong> {{ $suggestion->admin_remarks }}</div>
                        @endif
                    </div>
                    <div class="modal-footer">
                        <form action="{{ route('admin.suggestions.review', $suggestion->id) }}" method="POST">
                            @csrf
                            <input type="hidden" name="action" value="approve">
                            <label class="visually-hidden" for="approveRemarks{{ $suggestion->id }}">Approval comment</label>
                            <textarea id="approveRemarks{{ $suggestion->id }}" name="admin_remarks" class="form-control mb-2" rows="2" maxlength="1000" placeholder="Why approve? (optional)"></textarea>
                            <button type="submit" class="btn btn-secondary">Approve Suggestion</button>
                        </form>
                        <form action="{{ route('admin.suggestions.review', $suggestion->id) }}" method="POST">
                            @csrf
                            <input type="hidden" name="action" value="reject">
                            <label class="visually-hidden" for="rejectRemarks{{ $suggestion->id }}">Rejection comment</label>
                            <textarea id="rejectRemarks{{ $suggestion->id }}" name="admin_remarks" class="form-control mb-2" rows="2" maxlength="1000" placeholder="Why reject? (optional)"></textarea>
                            <button type="submit" class="btn btn-outline-secondary">Reject Suggestion</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
</div>

<style>
/* ── Reset / Base ─────────────────────────── */
*, *::before, *::after { box-sizing: border-box; }

.fw-wrap {
    padding: 28px 32px 60px;
    background: #f4f3fb;
    min-height: 100vh;
    font-family: 'Segoe UI', system-ui, sans-serif;
}

/* ── Top Bar ──────────────────────────────── */
.fw-topbar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    background: #fff;
    border-radius: 18px;
    padding: 18px 24px;
    margin-bottom: 28px;
    border: 1px solid #ebe8ff;
    box-shadow: 0 2px 12px rgba(124,58,237,0.06);
    gap: 16px;
    flex-wrap: wrap;
}
.fw-topbar-left {
    display: flex;
    align-items: center;
    gap: 16px;
}
.fw-icon-box {
    width: 48px; height: 48px;
    background: linear-gradient(135deg, #7c3aed, #a78bfa);
    border-radius: 14px;
    display: flex; align-items: center; justify-content: center;
    color: #fff;
    font-size: 1.2rem;
    flex-shrink: 0;
    box-shadow: 0 4px 14px rgba(124,58,237,0.3);
}
.fw-page-title {
    font-size: 1.35rem;
    font-weight: 800;
    color: #1e1b4b;
    margin: 0 0 2px;
    letter-spacing: -0.02em;
}
.fw-page-sub {
    font-size: 0.75rem;
    color: #9ca3af;
    margin: 0;
}
.fw-count-chip {
    background: #f5f3ff;
    border: 1px solid #ddd6fe;
    border-radius: 14px;
    padding: 10px 20px;
    text-align: center;
    min-width: 72px;
}
.fw-count-num {
    display: block;
    font-size: 1.6rem;
    font-weight: 800;
    color: #7c3aed;
    line-height: 1;
}
.fw-count-text {
    font-size: 0.65rem;
    color: #a78bfa;
    text-transform: uppercase;
    letter-spacing: 0.1em;
    font-weight: 700;
}

.sinag-status-approved { background: #d8cdfc; color: #5b4b9b; }
.sinag-status-rejected { background: #fff8d6; color: #806000; }
.sinag-status-pending { background: #f8cb12; color: #4a3500; }
.fw-grid { display: block; }
.fw-table-card .table { min-width: 760px; }
.fw-table-card td { vertical-align: middle; }

/* ── Grid ─────────────────────────────────── */
.fw-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 20px;
}
@media (max-width: 768px) {
    .fw-grid { grid-template-columns: 1fr; }
    .fw-wrap { padding: 16px; }
}

/* ── Card ─────────────────────────────────── */
.fw-card {
    background: #fff;
    border-radius: 18px;
    border: 1px solid #ede9fe;
    display: flex;
    overflow: hidden;
    box-shadow: 0 2px 10px rgba(124,58,237,0.05);
    transition: transform 0.22s ease, box-shadow 0.22s ease;
    animation: fadeUp 0.4s ease both;
    min-height: 200px;
}
.fw-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 10px 28px rgba(124,58,237,0.12);
}
@keyframes fadeUp {
    from { opacity: 0; transform: translateY(16px); }
    to   { opacity: 1; transform: translateY(0); }
}

/* Left colored bar */
.fw-bar {
    width: 5px;
    background: var(--c);
    flex-shrink: 0;
    border-radius: 0;
}

.fw-card-inner {
    flex: 1;
    padding: 20px 22px;
    display: flex;
    flex-direction: column;
    gap: 14px;
}

/* ── Card Head ────────────────────────────── */
.fw-card-head {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 8px;
}
.fw-cat {
    display: flex;
    align-items: center;
    gap: 6px;
    background: var(--cl);
    border: 1px solid color-mix(in srgb, var(--c) 20%, transparent);
    padding: 4px 12px 4px 8px;
    border-radius: 999px;
}
.fw-cat-icon {
    color: var(--c);
    font-size: 0.72rem;
    line-height: 1;
}
.fw-cat-label {
    font-size: 0.72rem;
    font-weight: 700;
    color: #374151;
    letter-spacing: 0.01em;
}
.fw-time {
    font-size: 0.68rem;
    color: #9ca3af;
    white-space: nowrap;
}

/* ── Message ──────────────────────────────── */
.fw-body { flex: 1; }
.fw-msg {
    font-size: 0.9rem;
    color: #374151;
    line-height: 1.75;
    margin: 0;
    display: -webkit-box;
    -webkit-line-clamp: 4;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

/* ── Footer ───────────────────────────────── */
.fw-card-foot {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding-top: 14px;
    border-top: 1px solid #f3f4f6;
    gap: 8px;
}
.fw-author {
    display: flex;
    align-items: center;
    gap: 8px;
    min-width: 0;
}
.fw-ava {
    width: 30px; height: 30px;
    background: var(--cl);
    border: 1.5px solid color-mix(in srgb, var(--c) 30%, transparent);
    border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    color: var(--c);
    font-size: 0.78rem;
    flex-shrink: 0;
}
.fw-author-name {
    font-size: 0.78rem;
    font-weight: 600;
    color: #6b7280;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.fw-actions {
    display: flex;
    align-items: center;
    gap: 8px;
    flex-shrink: 0;
}
.fw-btn-view {
    display: inline-flex;
    align-items: center;
    padding: 6px 16px;
    border-radius: 999px;
    background: linear-gradient(135deg, #7c3aed, #a78bfa);
    color: #fff;
    font-size: 0.72rem;
    font-weight: 700;
    text-decoration: none;
    transition: opacity 0.15s, transform 0.15s;
    box-shadow: 0 2px 8px rgba(124,58,237,0.25);
}
.fw-btn-view:hover {
    opacity: 0.9;
    transform: scale(1.04);
    color: #fff;
    text-decoration: none;
}
.fw-btn-del {
    width: 32px; height: 32px;
    border-radius: 50%;
    border: 1px solid #d8cdfc;
    background: #f3efff;
    color: #7658df;
    font-size: 0.75rem;
    display: flex; align-items: center; justify-content: center;
    cursor: pointer;
    transition: background 0.15s, transform 0.1s;
    padding: 0;
}
.fw-btn-del:hover {
    background: #fee2e2;
    transform: scale(1.08);
}

/* ── Empty ────────────────────────────────── */
.fw-empty {
    grid-column: 1 / -1;
    text-align: center;
    padding: 80px 24px;
    background: #fff;
    border-radius: 20px;
    border: 1.5px dashed #ddd6fe;
}
.fw-empty-icon {
    font-size: 3rem;
    color: #c4b5fd;
    display: block;
    margin-bottom: 16px;
}
.fw-empty-title {
    font-size: 1.05rem;
    font-weight: 700;
    color: #4b5563;
    margin: 0 0 6px;
}
.fw-empty-sub {
    font-size: 0.82rem;
    color: #9ca3af;
    margin: 0;
}
@media (max-width: 1199.98px) {
    .fw-wrap { padding: 20px 18px 40px; }
    .fw-grid { gap: 14px; }
    .fw-card-inner { padding: 16px; }
}
@media (max-width: 767.98px) {
    .fw-wrap { padding: 12px 10px 28px; }
    .fw-topbar { padding: 14px; border-radius: 14px; margin-bottom: 16px; }
    .fw-topbar-left { gap: 10px; }
    .fw-icon-box { width: 40px; height: 40px; border-radius: 11px; }
    .fw-page-title { font-size: 1.1rem; }
    .fw-page-sub { font-size: .68rem; }
    .fw-count-chip { padding: 7px 12px; min-width: 58px; }
    .fw-grid { grid-template-columns: 1fr; gap: 12px; }
    .fw-table-card .table { min-width: 720px; }
    .fw-card { min-height: 0; border-radius: 14px; }
    .fw-card-head { align-items: flex-start; flex-direction: column; }
    .fw-card-inner { padding: 14px; gap: 10px; }
    .fw-card-foot { align-items: flex-start; flex-direction: column; }
    .fw-actions { width: 100%; justify-content: flex-end; }
}
</style>

@endsection
