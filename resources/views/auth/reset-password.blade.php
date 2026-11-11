@extends('layouts.app')

@section('content')
<div class="container py-5" style="max-width: 560px;">
    <div class="card border-0 shadow-lg rounded-4">
        <div class="card-body p-4 p-lg-5">
            <h1 class="h3 fw-bold mb-2">Create New Password</h1>
            <p class="text-muted">Your approved reset link is valid for a single use.</p>
            @if (isset($errors) && $errors->any())<div class="alert alert-danger">{{ $errors->first() }}</div>@endif
            <form method="POST" action="{{ route('password.reset.submit', [$passwordResetRequest, $token]) }}">
                @csrf
                <label class="form-label fw-bold" for="password">New password</label>
                <div class="input-group mb-1">
                    <input id="password" name="password" type="password" class="form-control" minlength="8" required autocomplete="new-password">
                    <button class="btn btn-outline-secondary" type="button" data-password-toggle="password" aria-label="Show password"><i class="bi bi-eye"></i></button>
                </div>
                <div class="form-text mb-3">Use at least 8 characters.</div>
                <label class="form-label fw-bold" for="password_confirmation">Confirm new password</label>
                <div class="input-group">
                    <input id="password_confirmation" name="password_confirmation" type="password" class="form-control" minlength="8" required autocomplete="new-password">
                    <button class="btn btn-outline-secondary" type="button" data-password-toggle="password_confirmation" aria-label="Show password"><i class="bi bi-eye"></i></button>
                </div>
                <button class="btn btn-primary w-100 mt-4 fw-bold" type="submit"><i class="bi bi-check-circle me-2"></i>Update Password</button>
            </form>
        </div>
    </div>
</div>
<script>
    document.querySelectorAll('[data-password-toggle]').forEach(function (button) {
        button.addEventListener('click', function () {
            const input = document.getElementById(button.dataset.passwordToggle);
            const visible = input.type === 'password';
            input.type = visible ? 'text' : 'password';
            button.setAttribute('aria-label', visible ? 'Hide password' : 'Show password');
            button.querySelector('i').className = visible ? 'bi bi-eye-slash' : 'bi bi-eye';
        });
    });
</script>
@endsection
