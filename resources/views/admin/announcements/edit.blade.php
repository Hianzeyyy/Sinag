@extends('layouts.app')
@section('content')
<div class="container py-4">
    <div class="announcement-hero rounded-4 p-3 p-lg-3 mb-3 shadow-sm">
        <h2 class="fw-bold mb-1 fs-4" style="color:#A594F9;"><i class="bi bi-pencil-square me-2"></i>Edit Announcement</h2>
        <p class="text-muted mb-0 small">Update details to keep your campus notice accurate and timely.</p>
    </div>

    <div class="card border-0 rounded-4 shadow-lg announcement-card">
        <div class="card-body p-4 p-lg-5">
    @if ($errors->any())
        <div class="alert alert-danger border-0 rounded-4">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    <form method="POST" action="{{ route('admin.announcements.update', $announcement->id) }}">
        @csrf
        @method('PUT')
        <div class="mb-3">
            <label for="title" class="form-label fw-semibold text-dark">Title</label>
            <input type="text" class="form-control form-control-lg rounded-3 border-0 shadow-sm" id="title" name="title" value="{{ old('title', $announcement->title) }}" required>
        </div>
        <div class="mb-3">
            <label for="body" class="form-label fw-semibold text-dark">Body</label>
            <textarea class="form-control rounded-3 border-0 shadow-sm" id="body" name="body" rows="8" required>{{ old('body', $announcement->body) }}</textarea>
        </div>

        <div class="d-flex flex-wrap gap-2 mt-4">
            <button type="submit" class="btn fw-bold px-4 py-2" style="background:linear-gradient(90deg,#A594F9 0%,#c4b5fd 100%); color:#fff; border:none; border-radius:12px;">
                <i class="bi bi-check2-circle me-1"></i> Save Changes
            </button>
            <a href="{{ route('admin.announcements.index') }}" class="btn btn-light fw-semibold px-4 py-2" style="border-radius:12px;">Cancel</a>
        </div>
    </form>
        </div>
    </div>
</div>

<style>
    .announcement-hero {
        background: linear-gradient(115deg, #ede9fe 0%, #f5f3ff 55%, #fef9c3 100%);
        border: 1px solid #e9d5ff;
    }

    .announcement-card {
        background: rgba(255,255,255,0.92);
        box-shadow: 0 8px 32px #a594f922;
    }
</style>
@endsection
