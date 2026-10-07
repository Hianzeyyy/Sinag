@extends('layouts.app')

@section('content')
<style>
    .event-media {
        width: 100%;
        height: 420px;
        border-radius: 0.8rem;
        object-fit: cover;
    }

    @media (max-width: 575.98px) {
        .event-page-card { padding: 1rem !important; }
        .event-media,
        .event-media-placeholder { height: min(420px, 72vw); }
    }
</style>
<div class="container py-5">
    <div class="card event-page-card rounded-4 p-4">
        <div class="row g-4 align-items-start">
            <div class="col-12 col-lg-7">
                @if($image)
                    <img src="{{ asset($image) }}" alt="Event {{ $id }}" class="event-media">
                @else
                    <div class="event-media event-media-placeholder" style="background:#f3f4f6;"></div>
                @endif
            </div>
            <div class="col-12 col-lg-5">
                <h2 class="fw-bold">{{ $title }}</h2>
                <p class="text-muted">{{ $body }}</p>
                <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Pellentesque euismod, nisi vel consectetur interdum, nisl nisi consequat nunc, ut cursus orci lorem ac libero.</p>
            </div>
        </div>
    </div>
</div>
@endsection