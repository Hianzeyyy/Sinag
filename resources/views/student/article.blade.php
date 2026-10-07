@extends('layouts.app')

@section('content')
<style>
    .article-shell {
        background: linear-gradient(180deg, #faf7ff 0%, #f8fafc 100%);
        border-radius: 1.35rem;
        padding: 1rem;
    }

    .article-card {
        border: 0;
        border-radius: 1.4rem;
        overflow: hidden;
        box-shadow: 0 16px 38px rgba(165, 148, 249, 0.12);
        border: 1px solid rgba(165, 148, 249, 0.12);
        background: #fff;
    }

    .article-hero {
        height: min(52vh, 500px);
        min-height: 320px;
        background-size: cover;
        background-position: center;
        background-repeat: no-repeat;
    }

    .article-kicker {
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        padding: 0.35rem 0.7rem;
        border-radius: 999px;
        background: rgba(165, 148, 249, 0.12);
        color: #5b21b6;
        font-size: 0.74rem;
        font-weight: 800;
        letter-spacing: 0.06em;
        text-transform: uppercase;
    }

    .article-title {
        font-size: clamp(2rem, 4vw, 3rem);
        line-height: 1.05;
        font-weight: 900;
        color: #111827;
    }

    .article-subtitle {
        color: #6b7280;
        font-size: 1rem;
    }

    .article-lead {
        font-size: 1.08rem;
        line-height: 1.85;
        color: #1f2937;
        font-weight: 700;
    }

    .article-body {
        color: #4b5563;
        line-height: 1.9;
        font-size: 1rem;
    }
</style>

<div class="container-fluid article-shell py-4">
    <div class="card article-card">
        <div class="article-hero" style="background-image:url('{{ asset($article['image']) }}');"></div>
        <div class="p-4 p-lg-5">
            <div class="d-flex flex-wrap align-items-center gap-2 mb-3">
                <span class="article-kicker"><i class="bi bi-file-earmark-text"></i> {{ $article['type'] }}</span>
                <a href="{{ route('student.dashboard') }}" class="btn btn-sm rounded-pill px-3 fw-bold" style="background:#A594F9; color:#fff;">Back to dashboard</a>
            </div>
            <h1 class="article-title mb-2">{{ $article['title'] }}</h1>
            <p class="article-subtitle mb-4">{{ $article['subtitle'] }}</p>
            <p class="article-lead mb-3">{{ $article['lead'] }}</p>
            <p class="article-body mb-0">{{ $article['body'] }}</p>
        </div>
    </div>
</div>
@endsection