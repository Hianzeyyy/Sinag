

<?php $__env->startSection('content'); ?>
<style>
    .student-dashboard-page {
        --accent: #A594F9;
        --accent-deep: #8f72f5;
        --accent-soft: #c4b5fd;
        --ink: #111827;
        --muted: #6b7280;
        background: transparent;
        border-radius: 0;
        padding: 0 1rem 1rem;
        margin-bottom: -3rem;
    }

    .student-dashboard-wrap {
        max-width: 1440px;
        margin: 0 auto;
    }

    .dashboard-hero {
        border: 0;
        border-radius: 1.5rem;
        overflow: hidden;
        color: #fff;
        background: linear-gradient(120deg, #8f72f5 0%, #A594F9 52%, #facc15 100%);
        box-shadow: 0 22px 46px rgba(165, 148, 249, 0.22);
    }

    .hero-kicker,
    .section-kicker {
        display: inline-flex;
        align-items: center;
        gap: 0.45rem;
        padding: 0.42rem 0.8rem;
        border-radius: 999px;
        font-size: 0.78rem;
        font-weight: 800;
        letter-spacing: 0.08em;
        text-transform: uppercase;
    }

    .hero-kicker {
        background: rgba(255, 255, 255, 0.14);
    }

    .hero-title {
        font-size: clamp(2rem, 4vw, 3.2rem);
        line-height: 1.02;
        font-weight: 900;
        letter-spacing: -0.03em;
    }

    .hero-copy {
        max-width: 54rem;
        color: rgba(255, 255, 255, 0.92);
        font-size: 1.03rem;
        line-height: 1.75;
    }

    .hero-pill {
        display: inline-flex;
        align-items: center;
        gap: 0.45rem;
        padding: 0.65rem 0.92rem;
        border-radius: 999px;
        background: rgba(255, 255, 255, 0.14);
        border: 1px solid rgba(255, 255, 255, 0.16);
        color: rgba(255, 255, 255, 0.96);
        font-size: 0.86rem;
        font-weight: 700;
    }

    .hero-btn {
        border: 0;
        background: #fff;
        color: var(--accent-deep);
        font-weight: 800;
        border-radius: 999px;
        padding: 0.9rem 1.25rem;
        box-shadow: 0 14px 24px rgba(76, 29, 149, 0.2);
    }

    .hero-btn:hover {
        color: var(--accent-deep);
        transform: translateY(-1px);
    }

    .hero-btn-alt {
        background: rgba(255, 255, 255, 0.12);
        border: 1px solid rgba(255, 255, 255, 0.18);
        color: #fff;
        font-weight: 800;
        border-radius: 999px;
        padding: 0.9rem 1.2rem;
    }

    .dashboard-card,
    .stat-card,
    .action-card,
    .announcement-card,
    .reference-card {
        background: rgba(255, 255, 255, 0.9);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(165, 148, 249, 0.12);
        box-shadow: 0 16px 38px rgba(165, 148, 249, 0.08);
        border-radius: 1.3rem;
    }

    .stat-card {
        overflow: hidden;
        color: #fff;
        min-height: 120px;
        position: relative;
    }

    .stat-card::after {
        content: '';
        position: absolute;
        width: 5.5rem;
        height: 5.5rem;
        border-radius: 999px;
        right: -1.8rem;
        top: -1.8rem;
        background: rgba(255, 255, 255, 0.16);
    }

    .stat-label {
        font-size: 0.72rem;
        letter-spacing: 0.1em;
        text-transform: uppercase;
        font-weight: 800;
    }

    .section-title {
        font-weight: 900;
        letter-spacing: -0.02em;
        color: var(--ink);
        font-size: 1.1rem;
    }

    .section-subtitle {
        color: var(--muted);
        font-size: 0.93rem;
    }

    .dashboard-panel {
        border: 1px solid rgba(165, 148, 249, 0.14);
        border-radius: 1.25rem;
        box-shadow: 0 14px 34px rgba(165, 148, 249, 0.1);
        overflow: hidden;
    }

    .dashboard-panel .card-header {
        background: #fff;
        border-bottom: 1px solid rgba(229, 231, 235, 0.8);
    }

    .announcement-item {
        border: 1px solid rgba(165, 148, 249, 0.11);
        border-radius: 1rem;
        background: linear-gradient(180deg, #fff 0%, #fbfaff 100%);
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .announcement-item:hover {
        transform: translateY(-2px);
        box-shadow: 0 14px 28px rgba(165, 148, 249, 0.1);
    }

    .announcement-link {
        color: inherit;
    }

    .announcement-link:hover {
        color: inherit;
    }

    .announcement-meta {
        color: var(--muted);
        font-size: 0.8rem;
    }

    .daily-quote-card {
        border: 1px solid rgba(165, 148, 249, 0.14);
        border-radius: 1.25rem;
        background: linear-gradient(135deg, rgba(165, 148, 249, 0.12) 0%, rgba(255, 255, 255, 0.9) 52%, rgba(250, 204, 21, 0.1) 100%);
        box-shadow: 0 14px 34px rgba(165, 148, 249, 0.08);
    }

    .daily-quote-label {
        color: #8f72f5;
        font-size: 0.72rem;
        font-weight: 900;
        letter-spacing: 0.12em;
        text-transform: uppercase;
    }

    .daily-quote-text {
        color: #111827;
        font-size: clamp(1.02rem, 1.5vw, 1.22rem);
        line-height: 1.85;
        font-weight: 700;
    }

    .chip {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        padding: 0.35rem 0.7rem;
        border-radius: 999px;
        background: rgba(165, 148, 249, 0.12);
        color: #5b21b6;
        font-size: 0.74rem;
        font-weight: 800;
        letter-spacing: 0.04em;
        text-transform: uppercase;
    }

    .quick-action-btn {
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 0.95rem 1rem;
        border-radius: 999px;
        font-weight: 800;
        text-decoration: none;
        background: linear-gradient(135deg, #eef2ff 0%, #e9d5ff 100%);
        color: #5b21b6;
        border: 1px solid rgba(165, 148, 249, 0.14);
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .quick-action-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 20px rgba(165, 148, 249, 0.12);
        color: #4c1d95;
    }

    .quick-action-btn.primary {
        background: linear-gradient(135deg, #8f72f5 0%, #A594F9 100%);
        color: #fff;
    }

    .carousel-shell {
        border-radius: 1.3rem;
        overflow: hidden;
        box-shadow: 0 16px 38px rgba(165, 148, 249, 0.12);
    }

    .dashboard-carousel .carousel-item {
        min-height: 320px;
        background: #111827;
    }

    .dashboard-carousel .carousel-item img {
        width: 100%;
        height: 320px;
        object-fit: cover;
        opacity: 0.92;
    }

    .dashboard-carousel .carousel-caption {
        left: 0;
        right: 0;
        bottom: 0;
        text-align: left;
        background: linear-gradient(180deg, rgba(17, 24, 39, 0) 0%, rgba(17, 24, 39, 0.7) 100%);
        padding: 1.5rem;
    }

    .carousel-caption-kicker {
        color: #fff;
        font-size: 0.72rem;
        font-weight: 900;
        letter-spacing: 0.08em;
        text-transform: uppercase;
    }

    .carousel-caption-title {
        color: #fff;
        font-weight: 900;
        font-size: clamp(1rem, 1.8vw, 1.5rem);
        margin: 0.45rem 0 0;
        text-shadow: 0 4px 14px rgba(0, 0, 0, 0.35);
    }

    .carousel-caption-text {
        color: rgba(255, 255, 255, 0.92);
        margin-bottom: 0;
        text-shadow: 0 3px 10px rgba(0, 0, 0, 0.3);
        max-width: 44rem;
    }

    .reference-card {
        overflow: hidden;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .reference-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 18px 42px rgba(165, 148, 249, 0.14);
    }

    .reference-card-visual {
        width: 100%;
        min-height: 240px;
        background-size: cover;
        background-position: center;
        background-repeat: no-repeat;
        background-color: #fff;
    }

    .reference-card-content {
        padding: 1rem 1rem 1.05rem;
    }

    .reference-card-title {
        font-size: 1rem;
        font-weight: 900;
        color: #1f2937;
        margin: 0;
        line-height: 1.35;
    }

    .reference-card-body {
        color: #6b7280;
        font-size: 0.93rem;
        line-height: 1.6;
        margin-top: 0.4rem;
    }

    .reference-link {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        color: #8f72f5;
        font-weight: 800;
        font-size: 0.85rem;
    }

    .ref-group {
        border-top: 1px solid rgba(229, 231, 235, 0.8);
        padding-top: 1rem;
    }

    .student-dashboard-footer {
        width: 100vw;
        margin-top: 2rem;
        margin-left: calc(50% - 50vw);
        padding: 1.35rem clamp(1rem, 5vw, 4rem);
        border-radius: 0;
        background: linear-gradient(120deg, #312e81 0%, #6d55d9 62%, #8f72f5 100%);
        color: rgba(255, 255, 255, 0.9);
        box-shadow: 0 18px 36px rgba(76, 29, 149, 0.16);
    }

    .student-dashboard-footer-brand {
        color: #fff;
        font-size: 1.05rem;
        font-weight: 900;
        letter-spacing: 0.08em;
    }

    .student-dashboard-footer a {
        color: #fff;
        font-weight: 800;
        text-decoration: none;
    }

    .student-dashboard-footer a:hover {
        color: #facc15;
    }

    .dashboard-removed {
        display: none !important;
    }

    .gad-card-carousel {
        position: relative;
        width: 100vw;
        margin-left: calc(50% - 50vw);
        height: auto;
        overflow: hidden;
        border-radius: 1rem;
        background: #111827;
    }

    .gad-card-carousel::before {
        content: '';
        position: absolute;
        inset: -1.5rem;
        z-index: 0;
        background-image: var(--gad-background-image);
        background-position: center;
        background-size: cover;
        filter: blur(22px);
        opacity: .7;
        transform: scale(1.08);
    }

    .gad-card-stage {
        position: relative;
        display: block;
        width: 100vw;
        height: auto;
        margin: 0 auto;
        z-index: 1;
        transition: transform .55s ease;
    }

    .gad-card-slide {
        position: absolute;
        inset: 0;
        width: 100vw;
        height: auto;
        pointer-events: none;
        transition: transform .55s ease, opacity .55s ease, filter .55s ease;
        opacity: 0;
    }

    .gad-card-slide-title {
        position: absolute;
        right: 0;
        bottom: 0;
        left: 0;
        padding: 1.25rem 1.5rem 1rem;
        color: #fff;
        font-size: clamp(.9rem, 1.8vw, 1.2rem);
        font-weight: 800;
        background: linear-gradient(transparent, rgba(17, 24, 39, .82));
        opacity: 0;
        transform: translateY(10px);
        transition: opacity .25s ease, transform .25s ease;
    }

    .gad-card-slide:hover .gad-card-slide-title,
    .gad-card-slide:focus-visible .gad-card-slide-title {
        opacity: 1;
        transform: translateY(0);
    }

    .gad-card-slide img {
        width: min(100%, 1200px);
        height: auto;
        margin-inline: auto;
        display: block;
        object-position: center;
    }

    .gad-card-slide.is-center {
        opacity: 1;
        pointer-events: auto;
    }

    .gad-card-control {
        position: absolute;
        top: 50%;
        z-index: 10;
        width: 2.8rem;
        height: 2.8rem;
        border: 0;
        border-radius: 50%;
        background: #fff;
        color: #8f72f5;
        box-shadow: 0 8px 20px rgba(76,29,149,0.16);
        transform: translateY(-50%);
    }

    .gad-card-control.prev { left: 1.25rem; }
    .gad-card-control.next { right: 1.25rem; }

    .gad-card-suns {
        position: absolute;
        bottom: 0.8rem;
        left: 50%;
        z-index: 12;
        display: flex;
        gap: 0.55rem;
        transform: translateX(-50%);
    }

    .gad-card-sun {
        width: 1.25rem;
        height: 1.25rem;
        padding: 0;
        border: 0;
        background: transparent;
        color: #cbd5e1;
        font-size: 1.05rem;
        line-height: 1;
        transition: color .25s ease, transform .25s ease;
    }

    .gad-card-sun.is-active {
        color: #facc15;
        transform: scale(1.25);
    }

    .dashboard-news-events {
        margin: 0 0 1.5rem;
        padding: 1.25rem;
        border-radius: 1.35rem;
        background: rgba(255,255,255,0.92);
        box-shadow: 0 16px 38px rgba(165,148,249,0.1);
    }

    .dashboard-news-events h2 {
        color: #1f2937;
        font-size: 1.25rem;
        font-weight: 900;
    }

    .dashboard-event-card {
        height: 100%;
        overflow: hidden;
        border: 1px solid rgba(165,148,249,0.16);
        border-radius: 1rem;
        background: #fff;
        text-decoration: none;
        transition: transform .2s ease, box-shadow .2s ease;
    }

    .dashboard-event-card:hover { transform: translateY(-4px); box-shadow: 0 16px 28px rgba(76,29,149,0.12); }
    .dashboard-event-card img { width: 100%; height: 145px; object-fit: cover; }
    .dashboard-event-card-body { padding: .9rem; color: #4b5563; }
    .dashboard-event-card-title { color: #5b4b9b; font-weight: 900; }

    @media (max-width: 700px) {
        .gad-card-stage { width: 100vw; height: auto; }
        .gad-card-slide { width: 100vw; height: auto; }
        .gad-card-control.prev { left: .65rem; }
        .gad-card-control.next { right: .65rem; }
    }

    .vision-mission-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 1rem;
        margin: 0 0 1.5rem;
        padding: 1.5rem;
        border-radius: 1.4rem;
        background: rgba(255,255,255,0.92);
        box-shadow: 0 16px 38px rgba(165,148,249,0.1);
    }

    .vision-mission-panel {
        min-height: 190px;
        padding: 1.25rem;
        border: 1px solid rgba(165,148,249,0.18);
        border-radius: 1.1rem;
        background: linear-gradient(145deg, #fff 0%, #faf8ff 100%);
    }

    .vision-mission-panel h2 {
        margin: 0 0 0.9rem;
        color: #2854b8;
        font-size: clamp(1.6rem, 3vw, 2.35rem);
        font-weight: 900;
    }

    .vision-mission-panel p {
        margin: 0;
        color: #536174;
        font-size: 1rem;
        line-height: 1.8;
    }

    @media (max-width: 991px) {
        .student-dashboard-page {
            padding: 0.55rem;
            margin-bottom: -1.5rem;
        }

        .dashboard-carousel .carousel-item,
        .dashboard-carousel .carousel-item img {
            height: 260px;
            min-height: 260px;
        }
    }

    @media (max-width: 575px) {
        .student-dashboard-page { margin-bottom: -1.5rem; }

        .vision-mission-grid {
            grid-template-columns: 1fr;
            padding: 0.8rem;
        }

        .dashboard-carousel .carousel-item,
        .dashboard-carousel .carousel-item img {
            height: 220px;
            min-height: 220px;
        }
    }
</style>

<div class="container-fluid student-dashboard-page">
    <div class="student-dashboard-wrap">
        <div class="gad-card-carousel mb-4" aria-label="GAD events carousel">
            <div class="gad-card-stage" id="gadCardStage">
                <?php $__currentLoopData = [
                    ['file' => 'carousel1', 'title' => 'Stop Violence Against Women'],
                    ['file' => 'carousel2', 'title' => 'Gender Equality and Inclusive Society'],
                    ['file' => 'carousel3', 'title' => 'Gender and Development Awareness'],
                    ['file' => 'carousel4', 'title' => 'Safe Space and Support'],
                ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $carousel): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <a class="gad-card-slide rounded-xl overflow-hidden" data-gad-index="<?php echo e($loop->index); ?>" href="<?php echo e(route('student.event', ['id' => $loop->iteration])); ?>">
                        <img class="w-full h-auto" src="<?php echo e(asset('images/' . $carousel['file'] . '.jpg')); ?>" alt="<?php echo e($carousel['title']); ?>">
                        <span class="gad-card-slide-title"><?php echo e($carousel['title']); ?></span>
                    </a>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
            <button type="button" class="gad-card-control prev" id="gadCardPrev" aria-label="Previous GAD event"><i class="bi bi-chevron-left"></i></button>
            <button type="button" class="gad-card-control next" id="gadCardNext" aria-label="Next GAD event"><i class="bi bi-chevron-right"></i></button>
            <div class="gad-card-suns" aria-label="GAD event position">
                <?php $__currentLoopData = [1, 2, 3, 4]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sun): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <button type="button" class="gad-card-sun" data-gad-target="<?php echo e($loop->index); ?>" aria-label="Show GAD event <?php echo e($sun); ?>"><i class="bi bi-sun-fill"></i></button>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>

        <div class="carousel-shell dashboard-removed mb-4">
            <div id="studentDashboardCarousel" class="carousel slide dashboard-carousel" data-bs-ride="carousel">
                <div class="carousel-indicators">
                    <button type="button" data-bs-target="#studentDashboardCarousel" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
                    <button type="button" data-bs-target="#studentDashboardCarousel" data-bs-slide-to="1" aria-label="Slide 2"></button>
                    <button type="button" data-bs-target="#studentDashboardCarousel" data-bs-slide-to="2" aria-label="Slide 3"></button>
                </div>
                <div class="carousel-inner">
                    <div class="carousel-item active">
                        <a href="<?php echo e(route('student.event', ['id' => 1])); ?>" class="d-block text-decoration-none">
                            <img src="<?php echo e(asset('images/gadevent1.jpg')); ?>" alt="Campus event 1">
                        </a>
                        <div class="carousel-caption">
                            <div class="carousel-caption-kicker">Campus Event</div>
                            <h3 class="carousel-caption-title">A safer campus starts with informed students.</h3>
                            <p class="carousel-caption-text mb-2">Tap through the current GAD stories and event highlights to stay connected with the office.</p>
                            <a href="<?php echo e(route('student.event', ['id' => 1])); ?>" class="btn btn-sm btn-light rounded-pill px-3 fw-bold">View event</a>
                        </div>
                    </div>
                    <div class="carousel-item">
                        <a href="<?php echo e(route('student.event', ['id' => 2])); ?>" class="d-block text-decoration-none">
                            <img src="<?php echo e(asset('images/gadevent3.jpg')); ?>" alt="Campus event 3">
                        </a>
                        <div class="carousel-caption">
                            <div class="carousel-caption-kicker">Awareness Drive</div>
                            <h3 class="carousel-caption-title">Know the channels for reports, support, and guidance.</h3>
                            <p class="carousel-caption-text mb-2">Use the dashboard tools below whenever you need a fast way to reach the right service.</p>
                            <a href="<?php echo e(route('student.event', ['id' => 2])); ?>" class="btn btn-sm btn-light rounded-pill px-3 fw-bold">View event</a>
                        </div>
                    </div>
                    <div class="carousel-item">
                        <a href="<?php echo e(route('student.event', ['id' => 3])); ?>" class="d-block text-decoration-none">
                            <img src="<?php echo e(asset('images/gadevent4.jpg')); ?>" alt="Campus event 4">
                        </a>
                        <div class="carousel-caption">
                            <div class="carousel-caption-kicker">Support Spotlight</div>
                            <h3 class="carousel-caption-title">Visible support makes reporting easier.</h3>
                            <p class="carousel-caption-text mb-2">Student safety information is kept close to the top so you do not have to hunt for it.</p>
                            <a href="<?php echo e(route('student.event', ['id' => 3])); ?>" class="btn btn-sm btn-light rounded-pill px-3 fw-bold">View event</a>
                        </div>
                    </div>
                </div>
                <button class="carousel-control-prev" type="button" data-bs-target="#studentDashboardCarousel" data-bs-slide="prev">
                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Previous</span>
                </button>
                <button class="carousel-control-next" type="button" data-bs-target="#studentDashboardCarousel" data-bs-slide="next">
                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Next</span>
                </button>
            </div>
        </div>

        <div class="card dashboard-hero dashboard-removed mb-4">
            <div class="card-body p-4 p-lg-5">
                <div class="d-flex flex-column flex-lg-row align-items-lg-center justify-content-between gap-4">
                    <div>
                        <h1 class="hero-title mb-3">Your safe space, updates, and campus support in one place.</h1>
                        <p class="hero-copy mb-4">Check announcements, jump into reports or suggestions, and keep the important references within reach. The goal is to keep the dashboard informative first, with the references shown below as simple image previews.</p>
                    </div>
                    <!-- Side action buttons removed per request -->
                </div>
            </div>
        </div>

        <div class="card daily-quote-card dashboard-removed mb-4">
            <div class="card-body p-4 p-lg-4 d-flex flex-column flex-lg-row align-items-lg-center justify-content-between gap-3">
                <div>
                    <div class="daily-quote-label mb-2"><i class="bi bi-stars me-1"></i>Daily Quote</div>
                    <div class="daily-quote-text"><?php echo e($motivationQuote ?? 'Your safety matters.'); ?></div>
                </div>
                <div class="text-lg-end text-muted small fw-semibold" style="max-width: 18rem;">
                    A new reminder appears each day to keep support visible and close.
                </div>
            </div>
        </div>

        <div class="row g-3 dashboard-removed mb-4">
            <div class="col-md-6 col-xl-3">
                <div class="stat-card" style="background: linear-gradient(130deg, #8f72f5 0%, #A594F9 100%);">
                    <div class="position-relative h-100 p-3">
                        <div class="stat-label text-white-50 mb-2">Announcements</div>
                        <h2 class="fw-bold mb-1"><?php echo e(($announcements ?? collect())->count()); ?></h2>
                        <small class="text-white-50">Latest campus updates</small>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-xl-3">
                <div class="stat-card" style="background: linear-gradient(130deg, #c084fc 0%, #facc15 100%);">
                    <div class="position-relative h-100 p-3">
                        <div class="stat-label text-white-50 mb-2">Quick Response</div>
                        <h2 class="fw-bold mb-1">24/7</h2>
                        <small class="text-white-50">Message GAD anytime</small>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-xl-3">
                <div class="stat-card" style="background: linear-gradient(130deg, #A594F9 0%, #c4b5fd 100%);">
                    <div class="position-relative h-100 p-3">
                        <div class="stat-label text-white-50 mb-2">Safety Tools</div>
                        <h2 class="fw-bold mb-1">4</h2>
                        <small class="text-white-50">Report, vault, suggestions, urgent call</small>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-xl-3">
                <div class="stat-card" style="background: linear-gradient(130deg, #8f72f5 0%, #c4b5fd 100%);">
                    <div class="position-relative h-100 p-3">
                        <div class="stat-label text-white-50 mb-2">Support Quote</div>
                        <h2 class="fw-bold mb-1">Go</h2>
                        <small class="text-white-50"><?php echo e($motivationQuote ?? 'Your voice is valid.'); ?></small>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4 dashboard-removed mb-4">
            <div class="col-lg-8">
                <div class="card dashboard-panel h-100">
                    <div class="card-header py-3 border-0">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <div class="section-title mb-1">Recent Announcements</div>
                                <div class="section-subtitle">Updates from the GAD office and school administrators.</div>
                            </div>
                            <a href="<?php echo e(route('student.messaging')); ?>" class="btn btn-sm rounded-pill px-3 fw-bold" style="background:#A594F9; color:#fff;">Open Messages</a>
                        </div>
                    </div>
                    <div class="card-body p-3 p-lg-4">
                        <?php $__empty_1 = true; $__currentLoopData = ($announcements ?? collect())->take(3); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $announcement): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <a href="<?php echo e(route('student.announcement', ['id' => $announcement->id])); ?>" class="announcement-link d-block text-decoration-none mb-3">
                                <div class="announcement-item p-3 h-100">
                                    <div class="d-flex align-items-start justify-content-between gap-3">
                                        <div>
                                            <div class="chip mb-2">Announcement</div>
                                            <h5 class="fw-bold mb-1 text-dark"><?php echo e($announcement->title); ?></h5>
                                            <div class="announcement-meta mb-2">
                                                <i class="bi bi-calendar-event me-1"></i><?php echo e(optional($announcement->created_at)->format('M d, Y') ?? 'Recently posted'); ?>

                                            </div>
                                        </div>
                                        <span class="chip flex-shrink-0">Open</span>
                                    </div>
                                    <p class="mb-0 text-muted" style="line-height:1.75;"><?php echo e(\Illuminate\Support\Str::limit($announcement->body ?? $announcement->content ?? '', 180)); ?></p>
                                </div>
                            </a>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <div class="text-center py-5 text-muted">
                                <i class="bi bi-megaphone-fill fs-2 d-block mb-2"></i>
                                No announcements yet.
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card dashboard-panel h-100">
                    <div class="card-header py-3 border-0">
                        <div class="section-title mb-1">Quick Actions</div>
                        <div class="section-subtitle">Jump straight to the tools you use most.</div>
                    </div>
                    <div class="card-body p-4">
                        <div class="d-grid gap-3">
                            <a href="<?php echo e(route('student.report')); ?>" class="quick-action-btn primary">File a Report</a>
                            <a href="<?php echo e(route('student.vault')); ?>" class="quick-action-btn">Stealth Vault</a>
                            <a href="<?php echo e(route('student.boses')); ?>" class="quick-action-btn">Community Suggestions</a>
                            <a href="<?php echo e(route('student.messaging')); ?>" class="quick-action-btn">Message GAD</a>
                        </div>
                        <div class="mt-4 p-3 rounded-4" style="background: rgba(124,58,237,0.05); border:1px solid rgba(124,58,237,0.08);">
                            <div class="fw-bold mb-2" style="color:#8f72f5;">Quick reminders</div>
                            <ul class="small mb-0 ps-3" style="color:#4b5563; line-height:1.7;">
                                <li>Reports can be filed anonymously.</li>
                                <li>Your vault keeps your submissions organized.</li>
                                <li>Community Suggestions is where approved suggestions stay open to the community.</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="vision-mission-grid">
            <section class="vision-mission-panel">
                <h2>VISION</h2>
                <p>To be a gender-responsive industry-driven State University in the ASEAN region by 2030</p>
            </section>
            <section class="vision-mission-panel">
                <h2>MISSION</h2>
                <p>The Pangasinan State University shall provide a gender-sensitive, human-centered, resilient, and sustainable academic environment to develop dynamic, future-ready, and empowered individuals.</p>
            </section>
        </div>

        <section class="dashboard-news-events">
            <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-2 mb-3">
                <div>
                    <h2 class="mb-1">News and Events</h2>
                    <p class="text-muted mb-0">Stay connected with GAD activities and campus updates.</p>
                </div>
            </div>
            <div class="row g-3">
                <?php $__currentLoopData = [
                    ['id' => 1, 'image' => 'gadeventnew1.png', 'title' => 'GAD Graphics Launch', 'text' => 'Explore the latest GAD visual stories and campus messages.'],
                    ['id' => 2, 'image' => 'gadevent2.1.jpg', 'title' => 'Awareness and Support Week', 'text' => 'Learn where to find reporting, privacy, and support channels.'],
                    ['id' => 3, 'image' => 'gadevent3.1.jpg', 'title' => 'Student Safety Connect', 'text' => 'Discover activities that connect students with GAD support.'],
                    ['id' => 4, 'image' => 'gadevent4.1.jpg', 'title' => 'Support Spotlight', 'text' => 'Keep visible support close whenever you need guidance.'],
                    ['id' => 5, 'image' => 'gadevent3.jpg', 'title' => 'Community Awareness', 'text' => 'Build a safer, more informed, and inclusive campus community.'],
                ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $event): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="col-6 col-lg-4 col-xl">
                        <a href="<?php echo e(route('student.event', ['id' => $event['id']])); ?>" class="dashboard-event-card d-block">
                            <img src="<?php echo e(asset('images/' . $event['image'])); ?>" alt="<?php echo e($event['title']); ?>">
                            <div class="dashboard-event-card-body">
                                <div class="dashboard-event-card-title"><?php echo e($event['title']); ?></div>
                                <div class="small mt-1"><?php echo e($event['text']); ?></div>
                                <div class="small fw-bold mt-2" style="color:#8f72f5;">Open event <i class="bi bi-arrow-right"></i></div>
                            </div>
                        </a>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </section>

        <div class="row g-4 dashboard-removed mb-4">
            <div class="col-12">
                <div class="card dashboard-panel h-100">
                    <div class="card-header py-3 border-0">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <div class="section-title mb-1">Reference Library</div>
                                <div class="section-subtitle">Image previews only. Click a card to open the article page.</div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-4">
                        <?php
                            $referencePreviewItems = [
                                ['slug' => 'hivaids', 'title' => 'HIV/AIDS Awareness', 'summary' => 'HIV/AIDS awareness helps students understand prevention, care, and responsible behavior.', 'image' => 'images/hivaids.jpg'],
                                ['slug' => 'antisexualharassmentpolicy', 'title' => 'Anti-Sexual Harassment Policy', 'summary' => 'This policy explains how the institution defines and handles sexual harassment.', 'image' => 'images/antisexualharassmentpolicy.jpg'],
                                ['slug' => 'ra9262', 'title' => 'Republic Act 9262', 'summary' => 'Republic Act 9262 protects women and children from abuse and violence in intimate or family relationships.', 'image' => 'images/ra9262.jpg'],
                                ['slug' => 'ra8353', 'title' => 'Republic Act 8353', 'summary' => 'Republic Act 8353 strengthens the legal definition of rape and the protection of survivors.', 'image' => 'images/ra8353.jpg'],
                                ['slug' => 'ra9208', 'title' => 'Republic Act 9208', 'summary' => 'Republic Act 9208 targets trafficking in persons, especially women and children.', 'image' => 'images/ra9208.jpg'],
                                ['slug' => 'ra9710', 'title' => 'Republic Act 9710', 'summary' => 'Republic Act 9710 affirms the rights and dignity of women and girls.', 'image' => 'images/ra9710.jpg'],
                            ];
                        ?>

                        <div class="row g-3">
                            <?php $__currentLoopData = $referencePreviewItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $reference): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="col-6 col-md-4 col-xl-2">
                                    <div class="reference-card h-100" style="cursor: default;">
                                        <div class="reference-card-visual" style="background-image:url('<?php echo e(asset($reference['image'])); ?>'); min-height: 200px;"></div>
                                        <div class="reference-card-content text-center">
                                            <div class="reference-card-title"><?php echo e($reference['title']); ?></div>
                                            <div class="reference-card-body"><?php echo e($reference['summary']); ?></div>
                                            <a href="<?php echo e(route('student.reference', ['slug' => $reference['slug']])); ?>" class="reference-link mt-2 d-inline-block">
                                                <i class="bi bi-arrow-right-short me-1"></i>Read More
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <footer class="student-dashboard-footer">
            <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
                <div class="d-flex align-items-center gap-2">
                    <img src="<?php echo e(asset('images/gadlogo.png')); ?>" alt="SINAG GAD logo" style="width:38px;height:38px;object-fit:cover;border-radius:50%;">
                    <div>
                        <div class="student-dashboard-footer-brand">SI<span style="color:#facc15;">NAG</span></div>
                        <div class="small mt-1">Safety and Integrity Network for Abuse and Gender-bias</div>
                    </div>
                </div>
                <div class="small text-md-end">
                    <div>PSU Gender and Development Office</div>
                    <div class="mt-1"><i class="bi bi-envelope me-1"></i> gad@psu.edu.ph</div>
                    <div class="mt-1"><i class="bi bi-shield-check me-1"></i> Your safety matters.</div>
                </div>
            </div>
    </footer>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const slides = Array.from(document.querySelectorAll('.gad-card-slide'));
        const suns = Array.from(document.querySelectorAll('.gad-card-sun'));
        const stage = document.getElementById('gadCardStage');
        if (!stage || !slides.length) return;

        let activeIndex = 0;
        function resizeStage() {
            const image = slides[activeIndex].querySelector('img');
            if (!image || !image.naturalWidth || !image.naturalHeight) return;

            const imageWidth = Math.min(stage.clientWidth, 1200);
            const stageHeight = imageWidth * image.naturalHeight / image.naturalWidth;
            stage.style.height = stageHeight + 'px';
            slides.forEach(function (slide) { slide.style.height = stageHeight + 'px'; });
        }
        function renderGadCards() {
            const carousel = stage.closest('.gad-card-carousel');
            const activeImage = slides[activeIndex].querySelector('img');
            carousel.style.setProperty('--gad-background-image', 'url("' + activeImage.src + '")');
            stage.style.transform = 'none';
            slides.forEach(function (slide, index) {
                slide.classList.toggle('is-center', index === activeIndex);
            });
            suns.forEach(function (sun, index) {
                sun.classList.toggle('is-active', index === activeIndex);
            });
            resizeStage();
        }
        function advanceSlide() {
            activeIndex = (activeIndex + 1) % slides.length;
            renderGadCards();
        }
        document.getElementById('gadCardNext')?.addEventListener('click', advanceSlide);
        document.getElementById('gadCardPrev')?.addEventListener('click', function () { activeIndex = (activeIndex - 1 + slides.length) % slides.length; renderGadCards(); });
        suns.forEach(function (sun) {
            sun.addEventListener('click', function () {
                activeIndex = Number(sun.dataset.gadTarget);
                renderGadCards();
            });
        });
        slides.forEach(function (slide) {
            slide.querySelector('img').addEventListener('load', renderGadCards);
        });
        window.addEventListener('resize', resizeStage);
        renderGadCards();
        window.setInterval(advanceSlide, 8000);
    });
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\ANGELINE AMBROSIO\sinag\resources\views\student\dashboard.blade.php ENDPATH**/ ?>