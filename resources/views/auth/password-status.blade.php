@extends('layouts.app')

@section('content')
<style>
    .request-status-alert {
        border: 1px solid #d8cdfc;
        color: #5b4b9b;
        background: #f3efff;
    }

    .request-status-button {
        border-color: #8f72f5;
        color: #5b4b9b;
    }

    .request-status-button:hover,
    .request-status-button:focus-visible {
        border-color: #6d55d9;
        color: #fff;
        background: #6d55d9;
    }
</style>
<div class="container py-5" style="max-width: 680px;">
    <div class="card border-0 shadow-lg rounded-4">
        <div class="card-body p-4 p-lg-5">
            <h1 class="h3 fw-bold mb-2">Check Request Status</h1>
            <p class="text-muted">Use the reference number together with your registered email or ID number.</p>
            @php
                $refNumber = request('reference') ?? session('reference') ?? ($reference ?? null);
            @endphp
            @if (session('success') || $refNumber)
                <div class="alert request-status-alert">
                    <div>{{ session('success') ?? 'Request submitted. Keep your reference number to check its status.' }}</div>
                    @if ($refNumber)
                        <div class="mt-2">Your reference number is:</div>
                        <div class="d-flex align-items-center gap-2 mt-1">
                            <div id="passwordResetReference" class="h4 fw-bold mb-0">{{ $refNumber }}</div>
                            <button type="button" class="btn btn-sm request-status-button" onclick="navigator.clipboard?.writeText(document.getElementById('passwordResetReference').textContent.trim()).then(() => this.textContent = 'Copied')">Copy</button>
                        </div>
                        <div class="small mt-2">Save this number. You will need it together with your registered email or ID to check approval.</div>
                    @endif
                </div>
            @endif
            @if (isset($errors) && $errors->any())<div class="alert alert-danger">{{ $errors->first() }}</div>@endif
            @if (!isset($resetRequest))
                <form method="POST" action="{{ route('password.status.check') }}">
                    @csrf
                    <div class="alert request-status-alert mb-3">
                        <strong>Copy your reference number</strong>
                        <div class="small mt-1">Enter the same reference number and your registered PSU email or student/employee ID below.</div>
                    </div>
                    <label class="form-label fw-bold" for="reference_number">Reference number</label>
                    <input id="reference_number" name="reference_number" class="form-control mb-3" value="{{ old('reference_number', $refNumber) }}" required>
                    <label class="form-label fw-bold" for="email_or_id">Registered email or student/employee ID</label>
                    <input id="email_or_id" name="email_or_id" class="form-control" value="{{ old('email_or_id') }}" required>
                    <button class="btn btn-primary w-100 mt-4 fw-bold" type="submit"><i class="bi bi-search me-2"></i>Check Status</button>
                </form>
            @else
                <div class="border rounded-3 p-3 mb-3 bg-light"><div class="small text-muted">Reference number</div><div class="h5 fw-bold mb-0">{{ $resetRequest->reference_number }}</div></div>
                @if ($resetRequest->status === 'approved')
                    <div class="alert request-status-alert">Your password reset request has been approved. The admin does not provide a password. You may now create your own new password.</div>
                    <a class="btn request-status-button w-100 fw-bold" href="{{ route('password.reset.form', [$resetRequest, $token]) }}"><i class="bi bi-key me-2"></i>Create New Password</a>
                @elseif ($resetRequest->status === 'rejected')
                    <div class="alert alert-danger"><strong>Request rejected.</strong>@if($resetRequest->admin_remarks)<div class="mt-1">{{ $resetRequest->admin_remarks }}</div>@endif</div>
                @elseif ($resetRequest->status === 'completed')
                    <div class="alert alert-info">This password reset request has already been completed.</div>
                @else
                    <div class="alert alert-warning">Your request is pending administrator verification.</div>
                @endif
                <a class="btn btn-outline-secondary w-100 mt-2" href="{{ route('password.status.form') }}">Check another request</a>
            @endif
            <a class="d-block text-center mt-3" href="{{ route('login') }}">Back to Login</a>
        </div>
    </div>
</div>
@endsection
