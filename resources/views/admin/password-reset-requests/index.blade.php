@extends('layouts.app')

@section('content')
<div class="container-fluid py-3">
    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-2 mb-4">
        <div><h1 class="h3 fw-bold mb-1">Password Reset Requests</h1><p class="text-muted mb-0">Review identity evidence before approving a password reset.</p></div>
        <span class="badge password-reset-pending-count">{{ $requests->where('status', 'pending')->count() }} pending on this page</span>
    </div>
    @if (session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden password-reset-card">
        <div class="table-responsive password-reset-table-wrap"><table class="table align-middle mb-0 password-reset-table"><thead><tr><th>Requester</th><th>Reference</th><th>ID number</th><th>Status</th><th>Submitted</th><th class="text-end">Action</th></tr></thead><tbody>
        @forelse ($requests as $item)
            <tr><td>{{ $item->name }}<div class="small text-muted">{{ $item->email }}</div></td><td class="fw-bold">{{ $item->reference_number }}</td><td>{{ $item->student_employee_id }}</td><td><span class="badge password-status-{{ $item->status }}">{{ ucfirst($item->status) }}</span></td><td>{{ $item->created_at->format('M d, Y h:i A') }}</td><td><a class="btn btn-sm rounded-pill px-3 password-reset-review-btn" href="{{ route('admin.password-resets.show', $item) }}"><i class="bi bi-eye me-1"></i>Review</a></td></tr>
        @empty
            <tr><td colspan="6" class="text-center py-5 text-muted">No password reset requests yet.</td></tr>
        @endforelse
        </tbody></table></div>
        @if ($requests->hasPages())<div class="p-3">{{ $requests->links() }}</div>@endif
    </div>
</div>
<style>
    .password-reset-card { border-top: 5px solid #a594f9; }
    .password-reset-pending-count {
        background: #ede9fe;
        color: #5b4b9b;
        border: 1px solid #d8cdfc;
        border-radius: 999px;
        padding: .6rem .9rem;
        white-space: nowrap;
    }
    @media (max-width: 767.98px) {
        .password-reset-pending-count { align-self: flex-start; }
    }
    .password-reset-table-wrap { overscroll-behavior-x: contain; }
    .password-reset-table { min-width: 850px; }
    .password-reset-table thead th {
        padding: .95rem .8rem;
        background: #f5f2ff;
        color: #5b4b9b;
        border-bottom: 1px solid #e8e0ff;
        font-size: .75rem;
        font-weight: 800;
        letter-spacing: .04em;
        text-transform: uppercase;
        white-space: nowrap;
    }
    .password-reset-table tbody td { padding: .9rem .8rem; }
    .password-reset-table-wrap th:first-child,
    .password-reset-table-wrap td:first-child {
        position: sticky;
        left: 0;
        z-index: 2;
        background: #fff;
    }
    .password-reset-table-wrap th:first-child,
    .password-reset-table-wrap td:first-child { min-width: 13rem; }
    .password-reset-table-wrap thead th { z-index: 3; }
    .password-reset-table-wrap tbody tr:nth-child(even) { background: #fcfbff; }
    .password-reset-table-wrap tbody tr:hover { background: #f5f2ff; }
    .password-reset-review-btn { background: #8f72f5; color: #fff; border: 0; }
    .password-reset-review-btn:hover,
    .password-reset-review-btn:focus { background: #7658df; color: #fff; }
    .password-status-approved { background: #d8cdfc; color: #5b4b9b; }
    .password-status-rejected { background: #f7c4dd; color: #9d285d; }
    .password-status-pending { background: #ede9fe; color: #6d55d9; }
</style>
@endsection
