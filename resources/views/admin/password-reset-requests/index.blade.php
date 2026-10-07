@extends('layouts.app')

@section('content')
<div class="container-fluid py-3">
    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-2 mb-4">
        <div><h1 class="h3 fw-bold mb-1">Password Reset Requests</h1><p class="text-muted mb-0">Review identity evidence before approving a password reset.</p></div>
        <span class="badge text-bg-warning fs-6">{{ $requests->where('status', 'pending')->count() }} pending on this page</span>
    </div>
    @if (session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="table-responsive"><table class="table align-middle mb-0"><thead class="table-light"><tr><th>Reference</th><th>Requester</th><th>ID number</th><th>Status</th><th>Submitted</th><th></th></tr></thead><tbody>
        @forelse ($requests as $item)
            <tr><td class="fw-bold">{{ $item->reference_number }}</td><td>{{ $item->name }}<div class="small text-muted">{{ $item->email }}</div></td><td>{{ $item->student_employee_id }}</td><td><span class="badge text-bg-{{ $item->status === 'pending' ? 'warning' : ($item->status === 'approved' ? 'success' : ($item->status === 'rejected' ? 'danger' : 'secondary')) }}">{{ ucfirst($item->status) }}</span></td><td>{{ $item->created_at->format('M d, Y h:i A') }}</td><td><a class="btn btn-sm btn-outline-primary" href="{{ route('admin.password-resets.show', $item) }}">Review</a></td></tr>
        @empty
            <tr><td colspan="6" class="text-center py-5 text-muted">No password reset requests yet.</td></tr>
        @endforelse
        </tbody></table></div>
        @if ($requests->hasPages())<div class="p-3">{{ $requests->links() }}</div>@endif
    </div>
</div>
@endsection
