@extends('layouts.app')

@section('content')
<style>
    nav, .navbar { display: none !important; }
    .reset-shell { max-width: 760px; margin: 2rem auto; }
    .reset-card { border: 0; border-radius: 1.25rem; box-shadow: 0 18px 45px rgba(76, 29, 149, .14); }
    .reset-card .card-header { background: linear-gradient(135deg, #8f72f5, #c4b5fd); color: #fff; border: 0; }
</style>
<div class="container reset-shell">
    <div class="card reset-card overflow-hidden">
        <div class="card-header p-4">
            <h1 class="h3 fw-bold mb-1"><i class="bi bi-shield-lock me-2"></i>Request Password Reset</h1>
            <p class="mb-0">An administrator will verify your identity before you create a new password.</p>
        </div>
        <div class="card-body p-4 p-lg-5">
            @if ($errors->any())
                <div class="alert alert-danger"><ul class="mb-0">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
            @endif
            <form method="POST" action="{{ route('password.code.request') }}" enctype="multipart/form-data">
                @csrf
                <div class="row g-3">
                    <div class="col-md-6"><label class="form-label fw-bold" for="email">Registered email</label><input id="email" name="email" type="email" class="form-control" value="{{ old('email') }}" required></div>
                    <div class="col-md-6"><label class="form-label fw-bold" for="student_employee_id">Student/employee ID number</label><input id="student_employee_id" name="student_employee_id" class="form-control" value="{{ old('student_employee_id') }}" required></div>
                    <div class="col-md-6"><label class="form-label fw-bold" for="name">Full name</label><input id="name" name="name" class="form-control" value="{{ old('name') }}" required></div>
                    <div class="col-md-6"><label class="form-label fw-bold" for="department">Course, department, or office <span class="text-muted fw-normal">(optional)</span></label><input id="department" name="department" class="form-control" value="{{ old('department') }}"></div>
                    <div class="col-12"><label class="form-label fw-bold" for="id_picture">Student/employee ID picture</label><input id="id_picture" name="id_picture" type="file" class="form-control" accept=".jpg,.jpeg,.png,image/jpeg,image/png" required><div class="form-text">JPG, JPEG, or PNG only. Maximum 2 MB. The file is kept private.</div></div>
                    <div class="col-12"><label class="form-label fw-bold" for="reason">Reason for password reset <span class="text-muted fw-normal">(optional)</span></label><textarea id="reason" name="reason" class="form-control" rows="4" maxlength="2000">{{ old('reason') }}</textarea></div>
                </div>
                <div class="d-flex flex-column flex-sm-row gap-2 mt-4">
                    <button class="btn btn-primary flex-fill fw-bold" type="submit"><i class="bi bi-send me-2"></i>Submit Request</button>
                    <a class="btn btn-outline-secondary flex-fill" href="{{ route('password.status.form') }}"><i class="bi bi-search me-2"></i>Check Request Status</a>
                    <a class="btn btn-outline-secondary flex-fill" href="{{ route('login') }}">Back to Login</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
