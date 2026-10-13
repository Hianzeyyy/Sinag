@extends('layouts.app')

@section('content')
<style>
    .event-media {
        width: 100%;
        height: 420px;
        border-radius: 0.8rem;
        object-fit: cover;
        animation: event-image-zoom-out 0.85s cubic-bezier(.2,.75,.3,1) both;
    }

    @keyframes event-image-zoom-out {
        from { opacity: .65; transform: scale(1.12); }
        to { opacity: 1; transform: scale(1); }
    }

    .event-title.dashboard-typing::after,
    .event-description.dashboard-typing::after {
        content: '';
        display: inline-block;
        height: 1em;
        margin-left: 0.12em;
        border-right: 2px solid currentColor;
        vertical-align: -0.08em;
        animation: dashboard-caret 0.8s steps(2, start) infinite;
    }

    @keyframes dashboard-caret {
        to { visibility: hidden; }
    }

    @media (max-width: 575.98px) {
        .event-page-card { padding: 1rem !important; }
        .event-media,
        .event-media-placeholder { height: min(420px, 72vw); }
    }

    @media (prefers-reduced-motion: reduce) {
        .event-media { animation: none; }
        .event-title.dashboard-typing::after,
        .event-description.dashboard-typing::after { animation: none; border: 0; }
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
                <h2 class="fw-bold event-title">{{ $title }}</h2>
                <p class="text-muted event-description">{{ $body }}</p>
                <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Pellentesque euismod, nisi vel consectetur interdum, nisl nisi consequat nunc, ut cursus orci lorem ac libero.</p>
            </div>
        </div>
    </div>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const textElements = document.querySelectorAll('.event-title, .event-description');
        if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;

        textElements.forEach(function (element, elementIndex) {
            const fullText = element.textContent.trim();
            if (!fullText) return;

            element.setAttribute('aria-label', fullText);
            element.textContent = '';
            element.classList.add('dashboard-typing');

            let position = 0;
            function typeNext() {
                element.textContent = fullText.slice(0, ++position);
                if (position < fullText.length) {
                    window.setTimeout(typeNext, 24);
                } else {
                    element.classList.remove('dashboard-typing');
                }
            }

            window.setTimeout(typeNext, elementIndex * 180);
        });
    });
</script>
@endsection