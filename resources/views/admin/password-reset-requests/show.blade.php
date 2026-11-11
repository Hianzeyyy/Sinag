@extends('layouts.app')

@section('content')
<div class="container-fluid py-3" style="max-width: 1100px;">
    <a href="{{ route('admin.password-resets.index') }}" class="btn btn-link px-0 mb-3"><i class="bi bi-arrow-left me-1"></i>Back to requests</a>
    @if (session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
    @if ($errors->any())<div class="alert alert-danger">{{ $errors->first() }}</div>@endif
    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-2 mb-4"><div><h1 class="h3 fw-bold mb-1">{{ $passwordResetRequest->reference_number }}</h1><p class="text-muted mb-0">Submitted {{ $passwordResetRequest->created_at->format('M d, Y h:i A') }}</p></div><span class="badge password-status-{{ $passwordResetRequest->status }} fs-6">{{ ucfirst($passwordResetRequest->status) }}</span></div>
    <div class="row g-4">
        <div class="col-lg-7"><div class="card border-0 shadow-sm rounded-4"><div class="card-body p-4"><h2 class="h5 fw-bold mb-3">Submitted details</h2><dl class="row mb-0"><dt class="col-sm-5">Full name</dt><dd class="col-sm-7">{{ $passwordResetRequest->name }}</dd><dt class="col-sm-5">Registered email</dt><dd class="col-sm-7">{{ $passwordResetRequest->email }}</dd><dt class="col-sm-5">Student/employee ID</dt><dd class="col-sm-7">{{ $passwordResetRequest->student_employee_id }}</dd><dt class="col-sm-5">Course/department/office</dt><dd class="col-sm-7">{{ $passwordResetRequest->department ?: 'Not provided' }}</dd><dt class="col-sm-5">Reason</dt><dd class="col-sm-7">{{ $passwordResetRequest->reason ?: 'Not provided' }}</dd></dl></div></div>
            <div class="card border-0 shadow-sm rounded-4 mt-4"><div class="card-body p-4"><h2 class="h5 fw-bold mb-3">Registered user comparison</h2>@if($passwordResetRequest->user)<dl class="row mb-0"><dt class="col-sm-5">Account name</dt><dd class="col-sm-7">{{ $passwordResetRequest->user->name }}</dd><dt class="col-sm-5">Account email</dt><dd class="col-sm-7">{{ $passwordResetRequest->user->email }}</dd><dt class="col-sm-5">Department</dt><dd class="col-sm-7">{{ $passwordResetRequest->user->department ?: 'Not provided' }}</dd><dt class="col-sm-5">Account status</dt><dd class="col-sm-7">{{ ucfirst($passwordResetRequest->user->account_status ?? 'active') }}</dd></dl>@else<div class="alert alert-warning mb-0">No registered user record is linked to this email.</div>@endif</div></div>
        </div>
        <div class="col-lg-5"><div class="card border-0 shadow-sm rounded-4"><div class="card-body p-4"><h2 class="h5 fw-bold mb-3">Private ID picture</h2><img src="{{ route('admin.password-resets.id-picture', $passwordResetRequest) }}" alt="Submitted ID picture" class="img-fluid rounded border mb-3" style="max-height: 360px; width: 100%; object-fit: contain;"><div class="small text-muted mb-3">This image is served only to authorized admins.</div>
            @if ($passwordResetRequest->status === 'pending')
                <form method="POST" action="{{ route('admin.password-resets.approve', $passwordResetRequest) }}" class="mb-3">@csrf<button class="btn password-approve-btn w-100 fw-bold" type="submit"><i class="bi bi-check-circle me-2"></i>Approve Request</button></form>
                <form method="POST" action="{{ route('admin.password-resets.reject', $passwordResetRequest) }}">@csrf<label class="form-label fw-bold" for="admin_remarks">Rejection remarks</label><textarea id="admin_remarks" name="admin_remarks" class="form-control mb-2" rows="3" required></textarea><button class="btn password-reject-btn w-100 fw-bold" type="submit"><i class="bi bi-x-circle me-2"></i>Reject Request</button></form>
            @elseif ($passwordResetRequest->admin_remarks)<div class="alert alert-light mb-0"><strong>Admin remarks</strong><div>{{ $passwordResetRequest->admin_remarks }}</div></div>@endif
        </div></div></div>
    </div>
</div>
<style>
    .password-status-approved, .password-approve-btn { background: #d8cdfc; color: #5b4b9b; }
    .password-status-rejected, .password-reject-btn { background: #f7c4dd; color: #9d285d; }
    .password-status-pending { background: #ede9fe; color: #6d55d9; }
    .password-approve-btn:hover, .password-reject-btn:hover { filter: brightness(.96); color: inherit; }
</style>
@endsection
