@extends('layouts.app')

@section('content')
<style>
    .event-page {
        padding:clamp(1rem,4vw,3rem) 0;
        color:#2b2040;
    }

    .event-feature {
        position:relative;
        display:flex;
        min-height:clamp(18rem,44vw,30rem);
        align-items:flex-end;
        overflow:hidden;
        isolation:isolate;
        border:1px solid rgba(165,148,249,.25);
        border-radius:1.25rem;
        background:#34205f;
        box-shadow:0 18px 45px rgba(34,20,69,.22);
    }

    /* The event image is isolated from its text so scroll blur never softens the title. */
    .event-feature::before {
        position:absolute;
        inset:-2%;
        z-index:-1;
        content:"";
        background-image:linear-gradient(0deg,rgba(18,10,37,.72),rgba(18,10,37,.02) 70%),var(--event-background-image,linear-gradient(135deg,#7658df,#a594f9 55%,#facc15));
        background-position:center;
        background-repeat:no-repeat;
        background-size:var(--event-background-size,cover);
        filter:blur(var(--event-scroll-blur,0));
        opacity:var(--event-scroll-opacity,1);
        transform:scale(1.02);
        transition:filter .12s linear,opacity .12s linear;
    }

    .event-feature-copy {
        width:min(100%,65rem);
        margin:0 auto;
        padding:clamp(1.5rem,5vw,3.5rem);
        color:#fff;
        text-shadow:0 2px 16px rgba(0,0,0,.4);
    }

    .event-feature-label {
        display:inline-flex;
        margin-bottom:.75rem;
        padding:.35rem .7rem;
        border:1px solid rgba(255,255,255,.4);
        border-radius:999px;
        color:#fff;
        background:rgba(35,21,68,.4);
        font-size:.75rem;
        font-weight:800;
        letter-spacing:.12em;
        text-transform:uppercase;
    }

    .event-feature-title {
        max-width:18ch;
        margin:0;
        font-size:clamp(2rem,6vw,4.2rem);
        font-weight:900;
        line-height:1.05;
    }

    .event-content {
        max-width:65rem;
        margin:1.5rem auto 0;
        padding:clamp(1.25rem,3vw,2rem);
        border:1px solid #e8e1f5;
        border-radius:1rem;
        color:#35294a;
        background:rgba(255,255,255,.92);
        box-shadow:0 10px 32px rgba(48,27,90,.08);
    }

    .event-description {
        margin:0;
        font-size:1.05rem;
        line-height:1.75;
        white-space:pre-line;
    }

    .event-content { opacity: 1; }
    .event-content-zoom { animation: event-content-zoom-out .85s cubic-bezier(.2,.75,.3,1); }

    @keyframes event-content-zoom-out {
        from { opacity: .65; transform: scale(.94); }
        to { opacity: 1; transform: scale(1); }
    }

    .event-typing-text.dashboard-typing::after {
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
        .event-page { padding-top:1rem; }
        .event-feature { min-height:66vw; border-radius:1rem; }
        .event-content { margin-top:1rem; }
    }

    @media (prefers-reduced-motion: reduce) {
        .event-content { animation: none; }
        .event-typing-text.dashboard-typing::after { animation: none; border: 0; }
    }
</style>
<div class="container-fluid event-page">
    <section class="event-feature" aria-label="Event {{ $id }}" @if($image) style="--event-background-image:url('{{ asset($image) }}');" @endif>
        <div class="event-feature-copy">
            <span class="event-feature-label">Event {{ $id }}</span>
            <h1 class="event-feature-title event-typing-text">{{ $title }}</h1>
        </div>
    </section>
    <article class="event-content">
        <p class="event-description event-typing-text">{{ $body }}</p>
    </article>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const textElements = document.querySelectorAll('.event-typing-text');
        const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
        const feature = document.querySelector('.event-feature');
        const content = document.querySelector('.event-content');
        if (!reduceMotion) content?.classList.add('event-content-zoom');

        if (!reduceMotion && feature) {
            const initialBackgroundSize = Math.max(feature.offsetWidth * 2.5, 800);
            let frame = 0;

            function updateFeatureOnScroll() {
                frame = 0;
                const scrollOffset = Math.max(0, window.scrollY - feature.offsetTop);
                const size = Math.max(feature.offsetWidth * 1.15, initialBackgroundSize - scrollOffset / 3);
                const opacity = Math.max(.25, 1 - scrollOffset / Math.max(document.documentElement.scrollHeight, 1) * 1.3);
                feature.style.setProperty('--event-background-size', size + 'px auto');
                feature.style.setProperty('--event-scroll-blur', Math.min(scrollOffset / 100, 8) + 'px');
                feature.style.setProperty('--event-scroll-opacity', String(opacity));
            }

            function queueFeatureUpdate() {
                if (!frame) frame = window.requestAnimationFrame(updateFeatureOnScroll);
            }

            window.addEventListener('scroll', queueFeatureUpdate, { passive:true });
            window.addEventListener('resize', queueFeatureUpdate);
            updateFeatureOnScroll();
        }

        if (reduceMotion) return;
        textElements.forEach(function (element, elementIndex) {
            const fullText = element.textContent.trim();
            if (!fullText) return;
            element.setAttribute('aria-label', fullText);
            element.textContent = '';
            element.classList.add('dashboard-typing');
            let position = 0;
            function typeNext() {
                element.textContent = fullText.slice(0, ++position);
                if (position < fullText.length) window.setTimeout(typeNext, 24);
                else element.classList.remove('dashboard-typing');
            }
            window.setTimeout(typeNext, elementIndex * 180);
        });
    });
</script>
@endsection