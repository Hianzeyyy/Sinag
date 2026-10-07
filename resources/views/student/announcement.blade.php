@extends('layouts.app')

@section('content')
<style>
    .announcement-shell {
        background:
            radial-gradient(circle at top left, rgba(165, 148, 249, 0.12), transparent 30%),
            linear-gradient(180deg, #faf7ff 0%, #f8fafc 100%);
        border-radius: 1.35rem;
        padding: 1rem;
    }

    .announcement-card {
        border: 1px solid rgba(165, 148, 249, 0.12);
        border-radius: 1.4rem;
        overflow: hidden;
        box-shadow: 0 16px 38px rgba(165, 148, 249, 0.12);
        background: #fff;
    }

    .announcement-visual {
        min-height: 340px;
        background-size: cover;
        background-position: center;
        background-repeat: no-repeat;
        position: relative;
        overflow: hidden;
    }

    .announcement-visual::after {
        content: '';
        position: absolute;
        inset: 0;
        background: linear-gradient(180deg, rgba(15, 23, 42, 0.08) 0%, rgba(15, 23, 42, 0.18) 100%);
    }

    .announcement-visual::before {
        content: '';
        position: absolute;
        inset: auto 1rem 1rem auto;
        width: 8rem;
        height: 8rem;
        border-radius: 999px;
        background: rgba(255, 255, 255, 0.14);
    }

    .announcement-kicker {
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        padding: 0.36rem 0.72rem;
        border-radius: 999px;
        background: rgba(165, 148, 249, 0.12);
        color: #5b21b6;
        font-size: 0.74rem;
        font-weight: 800;
        letter-spacing: 0.06em;
        text-transform: uppercase;
    }

    .announcement-title {
        font-size: clamp(2rem, 4vw, 3rem);
        line-height: 1.05;
        font-weight: 900;
        color: #111827;
    }

    .announcement-subtitle {
        color: #6b7280;
        font-size: 0.95rem;
    }

    .announcement-lead {
        font-size: 1.06rem;
        line-height: 1.9;
        color: #1f2937;
        font-weight: 700;
    }

    .announcement-body {
        color: #374151;
        line-height: 1.95;
        font-size: 1.03rem;
    }
</style>

<div class="container-fluid announcement-shell py-4">
    <div class="card announcement-card">
        <div class="row g-0">
            <div class="col-lg-7 p-4 p-lg-5 d-flex align-items-center">
                <div>
                    <span class="announcement-kicker mb-3"><i class="bi bi-megaphone-fill"></i> Student Announcement</span>
                    <h1 class="announcement-title mb-2">{{ $announcement->title }}</h1>
                    <p class="announcement-subtitle mb-4">{{ $announcement->user->name ?? 'SINAG Office' }} • {{ optional($announcement->created_at)->format('M d, Y') ?? 'Recently posted' }}</p>
                    <p class="announcement-lead mb-4">{{ \Illuminate\Support\Str::limit($announcement->body ?? $announcement->content ?? '', 190) }}</p>
                    <div class="d-flex flex-wrap gap-2">
                        <a href="{{ route('student.dashboard') }}" class="btn btn-sm rounded-pill px-3 fw-bold" style="background:#A594F9; color:#fff;">Back to dashboard</a>
                        <span class="badge rounded-pill text-bg-light border">Full article view</span>
                    </div>
                </div>
            </div>
            <div class="col-lg-5 announcement-visual" style="background-image:url('{{ asset('images/announcementimage1.png') }}');"></div>
        </div>

        <div class="p-4 p-lg-5">
            <p class="announcement-body mb-0">{{ $announcement->body ?? $announcement->content ?? 'No announcement content available.' }}</p>
        </div>
    </div>
</div>
@endsection