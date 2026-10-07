@extends('layouts.app')

@section('content')
<link href="https://fonts.googleapis.com/css2?family=Sora:wght@400;600;700;800&family=DM+Sans:wght@400;500;600&display=swap" rel="stylesheet">

<style>
    .profile-page {
        min-height: calc(100vh - 7rem);
        padding: clamp(1rem, 3vw, 2.5rem);
        background: #f7f8fc;
    }

    .profile-shell {
        max-width: 1240px;
        margin: 0 auto;
    }

    .profile-hero {
        border: 1px solid #e9e5ff;
        border-radius: 1.25rem;
        overflow: hidden;
        background: #fff;
        box-shadow: 0 18px 45px rgba(48, 35, 105, 0.08);
        position: relative;
    }

    .profile-hero::before {
        content: '';
        display: block;
        height: 0.5rem;
        background: linear-gradient(90deg, #8f72f5, #a594f9 55%, #facc15);
    }

    .profile-cover {
        display: none;
        height: 200px;
        background:
            radial-gradient(circle at top left, rgba(255,255,255,0.2), transparent 28%),
            radial-gradient(circle at right top, rgba(255,255,255,0.15), transparent 25%);
    }

    .profile-head {
        padding: clamp(1.25rem, 3vw, 2rem);
        position: relative;
        z-index: 2;
    }

    .profile-head > .d-flex { align-items: center !important; }

    .profile-avatar-xl {
        width: 124px;
        height: 124px;
        border-radius: 50%;
        object-fit: cover;
        border: 0;
        box-shadow: 0 10px 24px rgba(48,35,105,0.16);
        background: #eef0f5;
        flex-shrink: 0;
    }

    .profile-name {
        font-size: clamp(2rem, 4vw, 3.5rem);
        font-weight: 900;
        margin: 0;
        line-height: 1;
        letter-spacing: -0.04em;
        color: #111827;
    }

    .profile-meta {
        color: #6b7280;
        line-height: 1.65;
    }

    .profile-action-row {
        display: flex;
        margin-top: 0.8rem;
    }

    .profile-action-btn {
        border: 0;
        border-radius: 999px;
        padding: 0.52rem 1.08rem;
        font-weight: 800;
        background: #f3efff;
        color: #6d55d9;
        text-decoration: underline;
        text-underline-offset: 3px;
        transition: background 0.2s ease, transform 0.2s ease;
    }

    .profile-action-btn:hover {
        background: #e8e0ff;
        color: #5b4b9b;
        transform: translateY(-1px);
    }

    .profile-card {
        border: 1px solid rgba(165,148,249,0.14);
        border-radius: 1rem;
        box-shadow: 0 14px 34px rgba(48,35,105,0.07);
        overflow: hidden;
        background: #fff;
    }

    .profile-card.soft-panel {
        background: linear-gradient(180deg, rgba(255,255,255,0.98), rgba(250,247,255,0.95));
    }

    .profile-stat {
        background: #fff;
        border: 1px solid rgba(165,148,249,0.14);
        border-radius: 1rem;
        padding: 1.15rem 1rem;
        height: 100%;
        box-shadow: 0 10px 28px rgba(48,35,105,0.05);
    }

    .profile-label {
        font-size: 0.72rem;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        color: #6b7280;
        font-weight: 800;
    }

    .profile-value {
        font-weight: 900;
        color: #111827;
        font-size: 1.05rem;
        margin-top: 0.3rem;
    }

    .profile-section-title {
        font-weight: 900;
        color: #111827;
        margin-bottom: 0.75rem;
        font-size: 1.05rem;
        letter-spacing: 0.01em;
    }

    .profile-detail-list {
        display: grid;
        gap: 0.9rem;
    }

    .profile-detail {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 1rem;
        padding-bottom: 0.9rem;
        border-bottom: 1px solid rgba(165,148,249,0.12);
    }

    .profile-detail .label::after {
        content: '';
        display: block;
        width: 1.5rem;
        height: 2px;
        margin-top: 0.35rem;
        background: #a594f9;
    }

    @media (max-width: 767.98px) {
        .profile-head > .d-flex { align-items: flex-start !important; }
        .profile-name { font-size: 2.25rem; }
        .profile-detail { gap: 0.75rem; }
        .profile-detail .value { max-width: 58%; }
    }

    .profile-detail:last-child {
        padding-bottom: 0;
        border-bottom: 0;
    }

    .profile-detail .label {
        font-size: 0.76rem;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        color: #6b7280;
        font-weight: 800;
    }

    .profile-detail .value {
        font-weight: 800;
        color: #111827;
        text-align: right;
    }

    .profile-edit-grid {
        display: grid;
        gap: 0.95rem;
    }

    @media (min-width: 768px) {
        .profile-edit-grid.two-col {
            grid-template-columns: 1fr 1fr;
        }
    }

    .profile-input {
        border-radius: 0.95rem;
        border: 1px solid rgba(165,148,249,0.22);
        background: rgba(255,255,255,0.95);
        box-shadow: inset 0 1px 0 rgba(255,255,255,0.6);
    }

    .profile-input:focus {
        border-color: rgba(143,114,245,0.55);
        box-shadow: 0 0 0 0.2rem rgba(165,148,249,0.14);
    }
</style>

<div class="profile-page">
    <div class="profile-shell">
        @if(session('status'))
            <div class="alert alert-success border-0 shadow-sm rounded-4 mb-4">{{ session('status') }}</div>
        @endif

        <div class="profile-hero mb-4">
            <div class="profile-head">
                <div class="d-flex flex-column flex-md-row align-items-start gap-3 gap-md-4">
                    <div class="text-center text-md-start">
                        @if($user->profile_photo_path)
                            <img src="{{ asset('storage/' . $user->profile_photo_path) }}" alt="Profile photo" class="profile-avatar-xl">
                        @else
                            <div class="profile-avatar-xl d-flex align-items-center justify-content-center fw-black" style="font-size:5rem; color:#A594F9;">{{ strtoupper(substr($user->name, 0, 1)) }}</div>
                        @endif

                        <form class="d-inline" action="{{ route('profile.photo.update') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <input id="profilePhotoInput" type="file" name="profile_photo" accept="image/png,image/jpeg,image/jpg,image/webp" class="d-none" onchange="this.form.submit()">
                        </form>

                        <div class="profile-action-row justify-content-center justify-content-md-start">
                            @if(auth()->id() === $user->id)
                                <button type="button" class="profile-action-btn" onclick="document.getElementById('profilePhotoInput').click();">Change Profile</button>
                                <button type="button" class="profile-action-btn" data-bs-toggle="modal" data-bs-target="#editProfileModal">Edit Information</button>
                            @endif
                            @if(auth()->user()->role === 'admin' && auth()->id() !== $user->id)
                                <a href="{{ route('admin.users.personal-data', $user->id) }}" class="profile-action-btn text-decoration-none">View Full Information</a>
                            @endif
                        </div>
                    </div>

                    <div class="pb-2 pt-md-4">
                        <h1 class="profile-name">{{ $user->name }}</h1>
                        <div class="profile-meta mt-2">
                            <div class="fw-semibold">{{ $user->cloak_alias ?? 'Anonymous' }}</div>
                            <div class="small">{{ $user->role === 'admin' ? 'GAD Administrator' : ucfirst($user->account_type ?? 'student') . ' Profile' }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-12">
                <div class="profile-stat">
                    <div class="row g-3">
                        <div class="col-md-3"><div class="profile-label">Email</div><div class="profile-value">{{ $user->email }}</div></div>
                        <div class="col-md-2"><div class="profile-label">Age</div><div class="profile-value">{{ $user->age ?? 'N/A' }}</div></div>
                        <div class="col-md-4"><div class="profile-label">Course / Department</div><div class="profile-value">{{ $user->department ?? 'N/A' }}</div></div>
                        <div class="col-md-3"><div class="profile-label">Contact Number</div><div class="profile-value">{{ $user->phone_number ?? 'N/A' }}</div></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="profile-card soft-panel p-4 mb-4">
            <div class="profile-section-title">Personal and Verification Details</div>
            <div class="row g-4">
                <div class="col-lg-6">
                    <div class="profile-detail-list">
                        <div class="profile-detail"><div class="label">Gender</div><div class="value">{{ $user->gender ?? data_get($user->personal_information, 'sex') ?? data_get($user->personal_information, 'gender_identity') ?? 'N/A' }}</div></div>
                        <div class="profile-detail"><div class="label">Account Type</div><div class="value">{{ ucfirst($user->account_type ?? 'student') }}{{ $user->employee_category ? ' - ' . ucfirst(str_replace('_', ' ', $user->employee_category)) : '' }}</div></div>
                        <div class="profile-detail"><div class="label">Phone</div><div class="value">{{ $user->phone_number ?: 'N/A' }}</div></div>
                        <div class="profile-detail"><div class="label">Account Status</div><div class="value">{{ ucfirst($user->account_status ?? 'active') }}</div></div>
                        @if(auth()->id() === $user->id)
                            <form method="POST" action="{{ route('profile.availability.update') }}" class="profile-detail align-items-center">
                                @csrf
                                <label class="label mb-0" for="availability_status">Availability</label>
                                <select class="form-select form-select-sm w-auto" id="availability_status" name="availability_status" onchange="this.form.submit()">
                                    @foreach(['active' => 'Active', 'busy' => 'Busy', 'do_not_disturb' => 'Do not disturb'] as $value => $label)
                                        <option value="{{ $value }}" {{ ($user->availability_status ?? 'active') === $value ? 'selected' : '' }}>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </form>
                        @else
                            <div class="profile-detail"><div class="label">Availability</div><div class="value">{{ ucfirst(str_replace('_', ' ', $user->availability_status ?? 'active')) }}</div></div>
                        @endif
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="profile-detail-list">
                        @php($verificationImage = $user->id_image_path ?: $user->selfie_image_path)
                        @if($verificationImage)
                            <div class="profile-detail align-items-start">
                                <div class="label">ID Verification</div>
                                <button type="button" class="btn p-0 border-0 bg-transparent" data-bs-toggle="modal" data-bs-target="#verificationImageModal" aria-label="View ID verification image">
                                    <img src="{{ asset('storage/' . $verificationImage) }}" alt="Student ID photo" class="img-thumbnail profile-verification-image">
                                </button>
                            </div>
                        @else
                            <div class="profile-detail"><div class="label">ID Verification</div><div class="value text-muted">No photo uploaded</div></div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .profile-verification-image {
        width: clamp(180px, 22vw, 280px);
        height: clamp(140px, 18vw, 210px);
        max-width: 100%;
        object-fit: contain;
        background: #f8fafc;
    }
</style>

@if($verificationImage)
    <div class="modal fade" id="verificationImageModal" tabindex="-1" aria-labelledby="verificationImageModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 rounded-4 overflow-hidden">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold" id="verificationImageModalLabel">ID Verification</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center p-3">
                    <img src="{{ asset('storage/' . $verificationImage) }}" alt="Student ID photo" class="img-fluid rounded-3 border" style="max-height: 75vh; object-fit: contain;">
                </div>
            </div>
        </div>
    </div>
@endif

<div class="modal fade" id="editProfileModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content rounded-4 overflow-hidden">
            <div class="modal-header" style="background:#f8fbff;">
                <h5 class="modal-title fw-bold">Edit Information</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('profile.update') }}" method="POST" class="p-4">
                @csrf
                <div class="profile-edit-grid two-col">
                    <div>
                        <label class="form-label fw-bold text-uppercase small text-muted">Name</label>
                        <input type="text" name="name" value="{{ old('name', $user->name) }}" class="form-control profile-input" required>
                        @error('name')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>
                    <div>
                        <label class="form-label fw-bold text-uppercase small text-muted">Email</label>
                        <input type="email" name="email" value="{{ old('email', $user->email) }}" class="form-control profile-input" required>
                        @error('email')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>
                    <div>
                        <label class="form-label fw-bold text-uppercase small text-muted">Age</label>
                        <input type="number" name="age" value="{{ old('age', $user->age) }}" class="form-control profile-input" min="1" max="120">
                        @error('age')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>
                    <div>
                        <label class="form-label fw-bold text-uppercase small text-muted">Gender</label>
                        <input type="text" name="gender" value="{{ old('gender', $user->gender) }}" class="form-control profile-input" placeholder="Male / Female / Prefer not to say">
                        @error('gender')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>
                    <div>
                        <label class="form-label fw-bold text-uppercase small text-muted">Department / Course</label>
                        <input type="text" name="department" value="{{ old('department', $user->department) }}" class="form-control profile-input" placeholder="Your course or department">
                        @error('department')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>
                    <div>
                        <label class="form-label fw-bold text-uppercase small text-muted">Phone</label>
                        <input type="text" name="phone_number" value="{{ old('phone_number', $user->phone_number) }}" class="form-control profile-input" placeholder="09xxxxxxxxx">
                        @error('phone_number')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>
                </div>

                <div class="mt-3">
                    <label class="form-label fw-bold text-uppercase small text-muted">Cloak Alias</label>
                    <input type="text" name="cloak_alias" value="{{ old('cloak_alias', $user->cloak_alias) }}" class="form-control profile-input" placeholder="Anonymous alias">
                    @error('cloak_alias')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                </div>

                <div class="d-flex justify-content-end gap-2 pt-4">
                    <button type="button" class="btn btn-light rounded-pill px-4 fw-bold" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn rounded-pill px-4 fw-bold text-white" style="background: linear-gradient(135deg, #8f72f5 0%, #A594F9 100%); border: 0;">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>
@if(false)
{{-- Legacy profile styles below are retained for reference but must not render outside the page section.
/* ── STAT CHIPS ─────────────────────────── */
.sp-stats {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 0.75rem;
    margin-bottom: 1.25rem;
}

.sp-stat-chip {
    background: var(--violet-pale);
    border-radius: 14px;
    padding: 1rem 0.75rem;
    text-align: center;
}

.sp-stat-chip.gold {
    background: linear-gradient(135deg, #FEF3C7, #FDE68A);
}

.sp-stat-num {
    font-family: 'Sora', sans-serif;
    font-size: 1.5rem;
    font-weight: 800;
    color: var(--violet);
    line-height: 1;
}
.sp-stat-chip.gold .sp-stat-num { color: #92400E; }
.sp-stat-label {
    font-size: 0.68rem;
    font-weight: 600;
    color: var(--muted);
    margin-top: 0.3rem;
    text-transform: uppercase;
    letter-spacing: 0.05em;
}

/* ── ACTIVITY / PLACEHOLDER ─────────────── */
.sp-activity-placeholder {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 2.5rem 1rem;
    color: var(--muted);
    gap: 0.75rem;
    text-align: center;
}
.sp-activity-placeholder svg { width: 40px; height: 40px; opacity: 0.3; }
.sp-activity-placeholder p { font-size: 0.85rem; }

/* ── EDIT SECTION ───────────────────────── */
.sp-edit-card {
    background: var(--card);
    border-radius: var(--radius);
    border: 1px solid var(--border);
    padding: 1.5rem;
    box-shadow: var(--shadow);
    margin-top: 1.5rem;
}

.sp-edit-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 1.25rem;
    cursor: pointer;
    user-select: none;
}

.sp-edit-toggle {
    background: none;
    border: none;
    color: var(--violet);
    font-size: 0.85rem;
    font-weight: 600;
    cursor: pointer;
    font-family: 'DM Sans', sans-serif;
    display: flex;
    align-items: center;
    gap: 0.3rem;
}

.sp-form-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1rem;
}
@media (max-width: 560px) { .sp-form-grid { grid-template-columns: 1fr; } }

.sp-field { display: flex; flex-direction: column; gap: 0.4rem; }
.sp-field.full { grid-column: 1 / -1; }

.sp-field label {
    font-size: 0.72rem;
    font-weight: 700;
    color: var(--muted);
    text-transform: uppercase;
    letter-spacing: 0.07em;
}

.sp-field input,
.sp-field select {
    background: var(--surface);
    border: 1.5px solid var(--border);
    border-radius: 12px;
    padding: 0.65rem 0.9rem;
    font-size: 0.9rem;
    color: var(--text);
    font-family: 'DM Sans', sans-serif;
    outline: none;
    transition: border-color 0.18s, box-shadow 0.18s;
}
.sp-field input:focus,
.sp-field select:focus {
    border-color: var(--violet-light);
    box-shadow: 0 0 0 3px rgba(124,58,237,0.1);
}

.sp-save-btn {
    margin-top: 1.25rem;
    width: 100%;
    padding: 0.75rem;
    background: var(--violet);
    color: #fff;
    font-family: 'Sora', sans-serif;
    font-size: 0.95rem;
    font-weight: 700;
    border: none;
    border-radius: 14px;
    cursor: pointer;
    box-shadow: 0 4px 16px rgba(124,58,237,0.28);
    transition: background 0.18s, transform 0.15s;
    letter-spacing: 0.02em;
}
.sp-save-btn:hover { background: #6D28D9; transform: translateY(-1px); }

/* ── BADGE ──────────────────────────────── */
.sp-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.3rem;
    background: linear-gradient(135deg, var(--violet), var(--gold));
    color: #fff;
    font-size: 0.7rem;
    font-weight: 700;
    padding: 0.25rem 0.7rem;
    border-radius: 999px;
    letter-spacing: 0.04em;
}

/* ── VERIFIED DOT ───────────────────────── */
.sp-verified {
    display: inline-block;
    width: 18px;
    height: 18px;
    background: var(--violet);
    border-radius: 50%;
    margin-left: 6px;
    vertical-align: middle;
    position: relative;
    top: -2px;
}
.sp-verified::after {
    content: '✓';
    position: absolute;
    inset: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #fff;
    font-size: 10px;
    font-weight: 800;
}
</style>

<div class="sp-page">

    {{-- COVER --}}
    <div class="sp-cover">
        <div class="sp-cover-deco sp-cover-deco-1"></div>
        <div class="sp-cover-deco sp-cover-deco-2"></div>
        <div class="sp-cover-deco sp-cover-deco-3"></div>
        
    </div>

    <div class="sp-container">

        {{-- IDENTITY STRIP --}}
        <div class="sp-identity">

            {{-- Avatar --}}
            <div class="sp-avatar-wrap">
                @if($user->profile_photo_path)
                    <img src="{{ asset('storage/' . $user->profile_photo_path) }}"
                         class="sp-avatar" alt="{{ $user->name }}">
                @else
                    <div class="sp-avatar">{{ strtoupper(substr($user->name, 0, 1)) }}</div>
                @endif

                <form action="{{ route('profile.photo.update') }}" method="POST"
                      enctype="multipart/form-data" id="avatarForm">
                    @csrf
                    <input type="file" name="profile_photo" id="avatarInput"
                           class="d-none" onchange="document.getElementById('avatarForm').submit()">
                </form>

                <button class="sp-avatar-btn" onclick="document.getElementById('avatarInput').click();"
                        title="Change photo">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                        <path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"/>
                        <circle cx="12" cy="13" r="4"/>
                    </svg>
                </button>
            </div>

            {{-- Name + alias --}}
            <div class="sp-name-block">
                <div class="sp-name">
                    {{ $user->name }}
                    <span class="sp-verified"></span>
                </div>
                <div class="sp-alias">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="12" height="12">
                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/>
                    </svg>
                    {{ $user->cloak_alias ?? 'Anonymous' }}
                </div>
            </div>

            {{-- Action buttons --}}
            <div class="sp-actions">
                <a href="#editSection" class="sp-btn sp-btn-outline"
                   onclick="toggleEdit(event)">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14">
                        <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                        <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                    </svg>
                    Edit Profile
                </a>
            </div>
        </div>

        <div class="sp-divider"></div>

        {{-- MAIN GRID --}}
        <div class="sp-grid">

            {{-- LEFT COLUMN --}}
            <div>
                {{-- Info card --}}
                <div class="sp-card">
                    <div class="sp-card-title">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="12" r="10"/><path d="M12 16v-4M12 8h.01"/>
                        </svg>
                        About
                    </div>

                    <div class="sp-info-row">
                        <div class="sp-info-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/>
                                <polyline points="22,6 12,13 2,6"/>
                            </svg>
                        </div>
                        <div>
                            <div class="sp-info-label">Email</div>
                            <div class="sp-info-value">{{ $user->email }}</div>
                        </div>
                    </div>

                    <div class="sp-info-row">
                        <div class="sp-info-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07A19.5 19.5 0 0 1 4.69 13.5a19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 3.6 2.69h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L7.91 10a16 16 0 0 0 6 6l.91-.91a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 21.73 17.5z"/>
                            </svg>
                        </div>
                        <div>
                            <div class="sp-info-label">Phone</div>
                            <div class="sp-info-value">{{ $user->phone_number ?? 'N/A' }}</div>
                        </div>
                    </div>

                    <div class="sp-info-row">
                        <div class="sp-info-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                                <circle cx="12" cy="7" r="4"/>
                            </svg>
                        </div>
                        <div>
                            <div class="sp-info-label">Age</div>
                            <div class="sp-info-value">{{ $user->age ?? 'N/A' }}</div>
                        </div>
                    </div>

                    <div class="sp-info-row">
                        <div class="sp-info-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M22 10v6M2 10l10-5 10 5-10 5z"/>
                                <path d="M6 12v5c3 3 9 3 12 0v-5"/>
                            </svg>
                        </div>
                        <div>
                            <div class="sp-info-label">Course / Department</div>
                            <div class="sp-info-value">{{ $user->department ?? 'N/A' }}</div>
                        </div>
                    </div>
                </div>

                {{-- Membership badge --}}
                <div class="sp-card" style="margin-top:1rem; text-align:center; padding:1.25rem;">
                    <div style="margin-bottom:0.6rem;">
                        <span class="sp-badge">⭐ SINAG Member</span>
                    </div>
                    <div style="font-size:0.78rem; color:var(--muted);">Safety & Integrity Network</div>
                </div>
            </div>

            {{-- RIGHT COLUMN --}}
            <div>
                {{-- Quick stats --}}
              

               

        {{-- EDIT SECTION --}}
        <div class="sp-edit-card" id="editSection" style="display:none;">
            <div class="sp-edit-header" onclick="toggleEdit()">
                <div class="sp-card-title" style="margin:0;">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14">
                        <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                        <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                    </svg>
                    Edit Information
                </div>
                <button class="sp-edit-toggle">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" width="16" height="16" id="editChevron">
                        <polyline points="18 15 12 9 6 15"/>
                    </svg>
                    Close
                </button>
            </div>

            <form action="{{ route('profile.update') }}" method="POST">
                @csrf
                @method('PUT')

                <div class="sp-form-grid">
                    <div class="sp-field full">
                        <label>Full Name</label>
                        <input type="text" name="name" value="{{ $user->name }}" placeholder="Your full name">
                    </div>
                    <div class="sp-field">
                        <label>Age</label>
                        <input type="number" name="age" value="{{ $user->age }}" placeholder="22">
                    </div>
                    <div class="sp-field">
                        <label>Phone Number</label>
                        <input type="text" name="phone_number" value="{{ $user->phone_number }}" placeholder="09XXXXXXXXX">
                    </div>
                    <div class="sp-field full">
                        <label>Course / Department</label>
                        <input type="text" name="department" value="{{ $user->department }}" placeholder="e.g. BSCS, College of Engineering">
                    </div>
                    <div class="sp-field full">
                        <label>Alias / Display Name</label>
                        <input type="text" name="cloak_alias" value="{{ $user->cloak_alias }}" placeholder="e.g. StudentFPGX">
                    </div>
                </div>

                <button type="submit" class="sp-save-btn">Save Changes</button>
            </form>
        </div>

    </div>
</div>

<script>
function toggleEdit(e) {
    if (e) e.preventDefault();
    const section = document.getElementById('editSection');
    const isHidden = section.style.display === 'none';
    section.style.display = isHidden ? 'block' : 'none';
    if (isHidden) {
        setTimeout(() => section.scrollIntoView({ behavior: 'smooth', block: 'start' }), 50);
    }
}
</script>
--}}
@endif
@endsection