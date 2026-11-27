@extends('layouts.app')

@section('content')
<div class="container py-4 admin-settings-page">
    <div class="mb-4">
        <p class="text-uppercase small fw-bold mb-1 settings-eyebrow">{{ auth()->user()->isGadCoordinator() ? 'Coordinator preferences' : 'Admin preferences' }}</p>
        <h1 class="h3 fw-bold mb-1">Settings</h1>
        <p class="text-muted mb-0">{{ $section === 'feedback' ? 'Review submitted ratings, written feedback, and overall satisfaction trends.' : 'Manage your profile access and dashboard appearance.' }}</p>
    </div>
    @if(session('success'))<div class="alert alert-success rounded-4">{{ session('success') }}</div>@endif
    @if($errors->any())<div class="alert alert-danger rounded-4">{{ $errors->first() }}</div>@endif

    <nav class="settings-sections nav nav-pills mb-4" aria-label="Settings sections">
        <a class="nav-link {{ $section === 'appearance' ? 'active' : '' }}" href="{{ route('admin.settings') }}" @if($section === 'appearance') aria-current="page" @endif>
            <i class="bi bi-palette me-1"></i>Appearance
        </a>
        @unless(auth()->user()->isGadCoordinator())
            <a class="nav-link {{ $section === 'archive' ? 'active' : '' }}" href="{{ route('admin.settings', ['section' => 'archive']) }}" @if($section === 'archive') aria-current="page" @endif>
                <i class="bi bi-archive me-1"></i>Archive
            </a>
            <a class="nav-link {{ $section === 'activity' ? 'active' : '' }}" href="{{ route('admin.settings', ['section' => 'activity']) }}" @if($section === 'activity') aria-current="page" @endif>
                <i class="bi bi-clock-history me-1"></i>Activity Log
            </a>
        @endunless
        <a class="nav-link {{ $section === 'feedback' ? 'active' : '' }}" href="{{ route('admin.settings', ['section' => 'feedback']) }}" @if($section === 'feedback') aria-current="page" @endif>
            <i class="bi bi-star-half me-1"></i>Ratings &amp; Reviews
        </a>
    </nav>

    @if($section === 'appearance')
    <div class="row g-4">
        <div class="col-lg-7">
            <section class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-4">
                    <h2 class="h5 fw-bold mb-1"><i class="bi bi-palette me-2" style="color:#8f72f5;"></i>Dashboard appearance</h2>
                    <p class="small text-muted mb-4">Choose light mode or dark mode for your dashboard.</p>
                    <form method="POST" action="{{ route('admin.settings.update') }}">
                        @csrf @method('PATCH')
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="theme-choice {{ (auth()->user()->admin_theme ?? 'light') === 'light' ? 'selected' : '' }}">
                                    <input type="radio" name="theme" value="light" {{ (auth()->user()->admin_theme ?? 'light') === 'light' ? 'checked' : '' }}>
                                    <span class="theme-preview theme-preview-light"><i class="bi bi-sun-fill"></i></span>
                                    <span><strong>Light Mode</strong><small>Bright workspace</small></span>
                                </label>
                            </div>
                            <div class="col-md-6">
                                <label class="theme-choice {{ (auth()->user()->admin_theme ?? 'light') === 'dark' ? 'selected' : '' }}">
                                    <input type="radio" name="theme" value="dark" {{ (auth()->user()->admin_theme ?? 'light') === 'dark' ? 'checked' : '' }}>
                                    <span class="theme-preview theme-preview-dark"><i class="bi bi-moon-stars-fill"></i></span>
                                    <span><strong>Dark Mode</strong><small>Low-light dashboard</small></span>
                                </label>
                            </div>
                        </div>
                        <button class="btn text-white rounded-pill px-4 mt-4" style="background:#8f72f5;" type="submit"><i class="bi bi-check2 me-1"></i>Save appearance</button>
                    </form>
                </div>
            </section>
        </div>
        <div class="col-lg-5">
            <section class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body p-4">
                    <h2 class="h5 fw-bold mb-3"><i class="bi bi-person-circle me-2 settings-icon"></i>{{ auth()->user()->isGadCoordinator() ? 'Coordinator profile' : 'Administrator profile' }}</h2>
                    <div class="d-flex align-items-center gap-3 mb-4">
                        <img src="{{ route('profile.photo', auth()->user()) }}" alt="{{ auth()->user()->isGadCoordinator() ? 'Coordinator' : 'Administrator' }} profile photo" class="rounded-circle shadow-sm" style="width:64px;height:64px;object-fit:cover;">
                        <div><strong class="d-block">{{ auth()->user()->name }}</strong><small class="text-muted">{{ auth()->user()->email }}</small></div>
                    </div>
                    <a href="{{ route('profile.show') }}" class="btn btn-outline-primary rounded-pill"><i class="bi bi-person-vcard me-1"></i>View profile</a>
                </div>
            </section>
        </div>
    </div>
    @elseif($section === 'archive')
        @include('admin.archive')
    @elseif($section === 'feedback')
        @include('admin.feedback-analytics')
    @else
        @include('admin.activity-log')
    @endif
</div>
<style>
    .admin-settings-page .card { background:#fff; }
    .settings-eyebrow,.settings-icon { color:#5b3ca8; }
    .settings-sections { gap:.45rem; padding:.35rem; background:#f6f2ff; border-radius:1rem; width:max-content; max-width:100%; }
    .settings-sections .nav-link { border-radius:.75rem; color:#5b4b82; font-weight:700; white-space:nowrap; }
    .settings-sections .nav-link.active { background:#34205f; color:#facc15; box-shadow:0 4px 12px rgba(52,32,95,.25); }
    .theme-choice { display:flex; align-items:center; gap:.8rem; height:100%; min-height:86px; border:1px solid #e8e1f5; border-radius:1rem; padding:.8rem; cursor:pointer; transition:border-color .2s,box-shadow .2s,transform .2s; }
    .theme-choice:hover { transform:translateY(-2px); }
    .theme-choice.selected { border-color:#5b3ca8; box-shadow:0 0 0 3px #5b3ca81c; }
    .theme-choice input { accent-color:#5b3ca8; }
    .theme-choice strong,.theme-choice small { display:block; }
    .theme-choice small { color:#777; font-size:.78rem; }
    .theme-preview { display:grid;place-items:center;width:42px;height:42px;border-radius:.8rem; }
    .theme-preview-light { background:#f1eaff;color:#7658df; }
    .theme-preview-dark { background:#211936;color:#c6b7f5; }
    body[data-admin-theme="dark"] .admin-settings-page .card { background:#1b1529;color:#eee8ff; }
    body[data-admin-theme="dark"] .settings-sections { background:#211936; }
    body[data-admin-theme="dark"] .settings-sections .nav-link { color:#c9bde8; }
    body[data-admin-theme="dark"] .settings-sections .nav-link.active { color:#fff; }
    body[data-admin-theme="dark"] .theme-choice { border-color:#3a2d50; }
    body[data-admin-theme="dark"] .theme-choice small { color:#c1b6d7; }
</style>
@endsection
