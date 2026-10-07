

<?php $__env->startSection('content'); ?>
<style>
    .boses-page {
        --accent: #A594F9;
        --accent-deep: #8f72f5;
        --accent-warm: #facc15;
        --ink: #111827;
        --muted: #6b7280;
        --surface: rgba(255, 255, 255, 0.88);
        --surface-strong: #ffffff;
        --border: rgba(165, 148, 249, 0.12);
        background:
            radial-gradient(circle at top left, rgba(165, 148, 249, 0.14), transparent 28%),
            radial-gradient(circle at right top, rgba(250, 204, 21, 0.16), transparent 22%),
            linear-gradient(180deg, #faf7ff 0%, #f8fafc 60%, #f8fafc 100%);
        border-radius: 1.5rem;
        position: relative;
        overflow: hidden;
    }

    .boses-page::before,
    .boses-page::after {
        content: '';
        position: absolute;
        border-radius: 999px;
        pointer-events: none;
        filter: blur(8px);
        opacity: 0.5;
    }

    .boses-page::before {
        width: 18rem;
        height: 18rem;
        left: -6rem;
        top: 3rem;
        background: rgba(165, 148, 249, 0.08);
    }

    .boses-page::after {
        width: 14rem;
        height: 14rem;
        right: -4rem;
        bottom: 4rem;
        background: rgba(250, 204, 21, 0.12);
    }

    .boses-shell {
        position: relative;
        z-index: 1;
        max-width: 1440px;
        margin: 0 auto;
    }

    .hero-panel,
    .stat-panel,
    .feed-panel,
    .side-panel,
    .suggestion-card,
    .compose-panel,
    .empty-panel {
        background: var(--surface);
        backdrop-filter: blur(10px);
        border: 1px solid var(--border);
        box-shadow: 0 16px 40px rgba(165, 148, 249, 0.08);
    }

    .hero-panel {
        border-radius: 1.6rem;
        overflow: hidden;
        background:
            linear-gradient(135deg, rgba(165, 148, 249, 0.97) 0%, rgba(196, 181, 253, 0.93) 55%, rgba(250, 204, 21, 0.92) 100%);
        color: #fff;
    }

    .composer-shell {
        background: rgba(255, 255, 255, 0.16);
        border: 1px solid rgba(255, 255, 255, 0.22);
        border-radius: 1.2rem;
        padding: 1rem;
    }

    .composer-avatar {
        width: 3rem;
        height: 3rem;
        border-radius: 0.95rem;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: rgba(255, 255, 255, 0.24);
        border: 1px solid rgba(255, 255, 255, 0.3);
        color: #fff;
        flex: 0 0 auto;
    }

    .composer-trigger {
        border: 0;
        background: rgba(255, 255, 255, 0.2);
        border-radius: 999px;
        min-height: 3rem;
        color: rgba(255, 255, 255, 0.92);
        font-weight: 600;
        padding: 0.7rem 1.1rem;
        transition: background 0.15s ease;
    }

    .composer-trigger:hover,
    .composer-trigger:focus {
        background: rgba(255, 255, 255, 0.3);
        color: #fff;
    }

                        const compact = normalized.replace(/(.)\\1+/gu, '$1').replace(/\\s/g, '');
                        const blocked = harshWords.some(function (word) {
                            return new RegExp('(^|\\s)' + word.replace(/[.*+?^${}()|[\\]\\\\]/g, '\\$&') + '(?=\\s|$)', 'i').test(normalized)
                                || compact.includes(word.replace(/(.)\\1+/gu, '$1'));
                        });
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.45rem 0.85rem;
        border-radius: 999px;
        background: rgba(255, 255, 255, 0.16);
        color: rgba(255, 255, 255, 0.95);
        font-size: 0.78rem;
        font-weight: 800;
        letter-spacing: 0.08em;
        text-transform: uppercase;
    }

    .hero-title {
        font-size: clamp(2rem, 4vw, 3.5rem);
        line-height: 1.02;
        font-weight: 900;
        letter-spacing: -0.03em;
        margin-bottom: 0.85rem;
    }

    .hero-title span {
        color: #fff6b0;
    }

    .hero-copy {
        max-width: 46rem;
        color: rgba(255, 255, 255, 0.9);
        font-size: 1.06rem;
        line-height: 1.7;
    }

    .hero-chip {
        display: inline-flex;
        align-items: center;
        gap: 0.45rem;
        padding: 0.6rem 0.9rem;
        border-radius: 999px;
        background: rgba(255, 255, 255, 0.14);
        border: 1px solid rgba(255, 255, 255, 0.18);
        color: rgba(255, 255, 255, 0.92);
        font-size: 0.86rem;
        font-weight: 700;
    }

    .hero-action {
        border: 0;
        background: #fff;
        color: var(--accent-deep);
        font-weight: 800;
        border-radius: 999px;
        padding: 0.9rem 1.35rem;
        box-shadow: 0 14px 25px rgba(76, 29, 149, 0.22);
        transition: transform 0.16s ease, box-shadow 0.16s ease;
    }

    .hero-action:hover {
        transform: translateY(-2px);
        box-shadow: 0 18px 30px rgba(76, 29, 149, 0.26);
        color: var(--accent-deep);
    }

    .hero-secondary {
        background: rgba(255, 255, 255, 0.14);
        border: 1px solid rgba(255, 255, 255, 0.22);
        color: #fff;
        font-weight: 800;
        border-radius: 999px;
        padding: 0.9rem 1.2rem;
    }

    .hero-visual {
        min-height: 100%;
        border-radius: 1.25rem;
        background: rgba(255, 255, 255, 0.14);
        border: 1px solid rgba(255, 255, 255, 0.2);
        position: relative;
        overflow: hidden;
    }

    .hero-visual::before {
        content: '';
        position: absolute;
        inset: 1rem;
        border-radius: 1rem;
        border: 1px dashed rgba(255, 255, 255, 0.2);
    }

    .hero-note {
        position: relative;
        z-index: 1;
        padding: 1.2rem;
    }

    .stat-panel {
        border-radius: 1.15rem;
        padding: 1.15rem 1.2rem;
        height: 100%;
    }

    .stat-label {
        color: var(--muted);
        font-size: 0.78rem;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        font-weight: 800;
    }

    .stat-value {
        color: var(--ink);
        font-size: 1.7rem;
        line-height: 1;
        font-weight: 900;
        margin-top: 0.45rem;
    }

    .stat-pill {
        width: 2.6rem;
        height: 2.6rem;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 0.95rem;
        background: linear-gradient(135deg, rgba(165, 148, 249, 0.16), rgba(250, 204, 21, 0.18));
        color: var(--accent-deep);
    }

    .feed-panel,
    .side-panel,
    .compose-panel,
    .empty-panel,
    .suggestion-card {
        border-radius: 1.35rem;
    }

    .section-title {
        font-size: 1.2rem;
        font-weight: 900;
        color: var(--ink);
        margin-bottom: 0.25rem;
    }

    .section-subtitle {
        color: var(--muted);
        font-size: 0.95rem;
    }

    .search-input {
        border-radius: 999px;
        border: 1px solid rgba(165, 148, 249, 0.14);
        background: #fff;
        padding-left: 2.75rem;
        min-height: 3rem;
        box-shadow: 0 8px 20px rgba(165, 148, 249, 0.05);
    }

    .search-wrap {
        position: relative;
    }

    .search-wrap i {
        position: absolute;
        left: 1rem;
        top: 50%;
        transform: translateY(-50%);
        color: #A594F9;
    }

    .suggestion-card {
        overflow: hidden;
        transition: transform 0.16s ease, box-shadow 0.16s ease, border-color 0.16s ease;
    }

    .suggestion-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 18px 36px rgba(165, 148, 249, 0.12);
        border-color: rgba(165, 148, 249, 0.2);
    }

    .suggestion-top {
        background: linear-gradient(180deg, rgba(165, 148, 249, 0.08), rgba(255, 255, 255, 0));
        border-bottom: 1px solid rgba(165, 148, 249, 0.08);
    }

    .alias-mark {
        width: 2.8rem;
        height: 2.8rem;
        border-radius: 0.95rem;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, rgba(165, 148, 249, 0.16), rgba(250, 204, 21, 0.2));
        color: var(--accent-deep);
        flex: 0 0 auto;
    }

    .suggestion-message {
        color: var(--ink);
        font-size: 1.02rem;
        line-height: 1.75;
        white-space: pre-wrap;
    }

    .meta-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        border-radius: 999px;
        padding: 0.42rem 0.7rem;
        font-size: 0.78rem;
        font-weight: 800;
    }

    .meta-badge.category {
        background: rgba(165, 148, 249, 0.1);
        color: var(--accent-deep);
    }

    .meta-badge.time {
        background: rgba(15, 23, 42, 0.04);
        color: var(--muted);
    }

    .action-btn {
        border-radius: 999px;
        font-weight: 800;
        min-height: 2.6rem;
    }

    .kebab-btn {
        width: 2.6rem;
        height: 2.6rem;
        border-radius: 999px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border: 1px solid rgba(165, 148, 249, 0.14);
        background: #fff;
        color: #6b7280;
    }

    .kebab-btn:hover,
    .kebab-btn:focus {
        background: rgba(165, 148, 249, 0.08);
        color: #4c1d95;
    }

    .action-menu {
        min-width: 11rem;
        border-radius: 1rem;
        border: 1px solid rgba(165, 148, 249, 0.12);
        box-shadow: 0 18px 32px rgba(15, 23, 42, 0.12);
        padding: 0.45rem;
    }

    .action-menu .dropdown-item {
        border-radius: 0.8rem;
        font-weight: 700;
        padding: 0.55rem 0.75rem;
    }

    .action-menu .dropdown-item:hover {
        background: #eef2ff;
        color: #4c1d95;
    }

    .upvote-btn {
        border: 1px solid rgba(165, 148, 249, 0.14);
        background: #fff;
        color: var(--muted);
    }

    .upvote-btn.is-active {
        background: linear-gradient(135deg, rgba(165, 148, 249, 0.1), rgba(250, 204, 21, 0.16));
        color: var(--accent-deep);
    }

    .comment-card {
        background: rgba(248, 250, 252, 0.95);
        border: 1px solid rgba(165, 148, 249, 0.08);
        border-radius: 1rem;
        padding: 0.85rem 0.95rem;
    }

    .comment-author {
        font-weight: 800;
        color: var(--ink);
    }

    .comment-text {
        color: #374151;
        line-height: 1.6;
        white-space: pre-wrap;
    }

    .comment-form {
        border-top: 1px solid rgba(165, 148, 249, 0.08);
        padding-top: 1rem;
    }

    .compose-panel {
        position: sticky;
        top: 6.5rem;
    }

    .compose-icon {
        width: 3rem;
        height: 3rem;
        border-radius: 1rem;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, rgba(165, 148, 249, 0.14), rgba(250, 204, 21, 0.18));
        color: var(--accent-deep);
    }

    .tip-list {
        padding-left: 1.1rem;
        margin-bottom: 0;
        color: var(--muted);
    }

    .tip-list li + li {
        margin-top: 0.55rem;
    }

    .empty-panel {
        padding: 3rem 1.5rem;
        text-align: center;
    }

    .empty-illustration {
        width: 4.2rem;
        height: 4.2rem;
        margin: 0 auto 1rem;
        border-radius: 1.35rem;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, rgba(165, 148, 249, 0.1), rgba(250, 204, 21, 0.14));
        color: var(--accent-deep);
    }

    .modal-content {
        border-radius: 1.4rem;
    }

    .report-content-button {
        color: var(--accent-deep) !important;
        border-color: rgba(143, 114, 245, 0.55) !important;
    }

    .report-content-button:hover,
    .report-content-button:focus-visible {
        color: #fff !important;
        background: var(--accent-deep) !important;
        border-color: var(--accent-deep) !important;
    }

    @media (max-width: 991px) {
        .compose-panel {
            position: static;
        }

        .hero-panel {
            border-radius: 1.3rem;
        }
    }
    @media (max-width: 1199.98px) {
        .boses-page { padding-inline: 1rem; }
        .feed-panel, .compose-panel, .hero-panel { padding: 1.1rem !important; }
    }
    @media (max-width: 767.98px) {
        .boses-page { padding-inline: .65rem; }
        .hero-panel h1 { font-size: 1.55rem; }
        .suggestion-card .suggestion-top, .suggestion-card > .p-4 { padding: 1rem !important; }
        .suggestion-card .d-flex.flex-column.flex-md-row { gap: .7rem !important; }
        .comment-form .input-group { flex-wrap: nowrap; }
        .comment-form .input-group .form-control { min-width: 0; }
        .comment-form .input-group .btn { padding-inline: .8rem !important; }
        .search-wrap { max-width: none !important; }
    }
</style>

<?php
    $totalSuggestions = $allSuggestions->count();
    $totalUpvotes = $allSuggestions->sum('upvotes_count');
    $totalComments = $allSuggestions->sum(function ($suggestion) {
        return $suggestion->comments->count();
    });
?>

<div class="container-fluid boses-page py-3 py-lg-4">
    <div class="boses-shell">
        <div class="hero-panel p-4 p-lg-5 mb-4 position-relative">
            <div class="row g-4 align-items-center">
                <div class="col-lg-12">
                    
                    
                    <div class="composer-shell">
                        <div class="d-flex align-items-center gap-2 gap-md-3">
                            <span class="composer-avatar">
                                <i class="bi bi-person-fill-lock fs-5"></i>
                            </span>
                            <button class="btn composer-trigger text-start flex-grow-1" data-bs-toggle="modal" data-bs-target="#suggestionModal">
                                Write your suggestions...
                            </button>
                            <button class="btn hero-action d-none d-md-inline-flex" data-bs-toggle="modal" data-bs-target="#suggestionModal">
                                <i class="bi bi-send me-2"></i>Post
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <div class="row g-4">
            <div class="col-12">
                <div class="feed-panel p-4 p-lg-4">
                    <div class="d-flex flex-column flex-xl-row align-items-xl-center justify-content-between gap-3 mb-4">
                        <div>
           
                        </div>
                        <div class="d-flex flex-column flex-md-row gap-2 w-100 justify-content-end" style="max-width: 42rem;">
                          
                            <div class="search-wrap w-100" style="max-width: 26rem;">
                                <i class="bi bi-search"></i>
                                <input
                                    type="search"
                                    id="suggestion-search"
                                    class="form-control search-input"
                                    placeholder="Search by keyword or category"
                                    aria-label="Search suggestions"
                                >
                            </div>
                        </div>
                    </div>

                    <div id="suggestion-feed" class="d-grid gap-3 gap-lg-4">
                        <?php $__empty_1 = true; $__currentLoopData = $allSuggestions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sugg): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <?php
                                $suggestionText = strtolower(($sugg->category ?? 'general') . ' ' . $sugg->message . ' ' . optional($sugg->user)->cloak_alias);
                            ?>
                            <article class="card suggestion-card border-0" data-suggestion-card data-suggestion-text="<?php echo e(e($suggestionText)); ?>">
                                <div class="suggestion-top p-4 p-lg-4">
                                    <div class="d-flex flex-column flex-md-row justify-content-between gap-3">
                                        <div class="d-flex align-items-center gap-3">
                                            <div class="alias-mark">
                                                <i class="bi bi-person-fill-lock fs-5"></i>
                                            </div>
                                            <div>
                                                <div class="fw-bold text-dark fs-6"><?php echo e($sugg->user->cloak_alias); ?></div>
                                                <div class="d-flex flex-wrap gap-2 mt-2">
                                                    <span class="meta-badge time">
                                                        <i class="bi bi-clock-history"></i>
                                                        <?php echo e($sugg->created_at->diffForHumans()); ?>

                                                    </span>
                                                    <span class="meta-badge category">
                                                        #<?php echo e($sugg->category ?? 'General'); ?>

                                                    </span>
                                                    <?php if($sugg->status !== 'Approved'): ?>
                                                        <span class="meta-badge time">
                                                            <i class="bi bi-hourglass-split"></i>
                                                            Pending review
                                                        </span>
                                                    <?php endif; ?>
                                                    <?php if($sugg->isUpvotedBy(Auth::id())): ?>
                                                        <span class="meta-badge category">
                                                            <i class="bi bi-hand-thumbs-up-fill"></i>
                                                            You upvoted
                                                        </span>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="d-flex align-items-start gap-2">
                                            <form action="<?php echo e(route('suggestions.upvote', $sugg->id)); ?>" method="POST" data-upvote-form>
                                                <?php echo csrf_field(); ?>
                                                <button type="submit" class="btn action-btn upvote-btn <?php echo e($sugg->isUpvotedBy(Auth::id()) ? 'is-active' : ''); ?> px-3" data-upvote-button aria-label="Toggle upvote">
                                                    <i class="bi bi-hand-thumbs-up<?php echo e($sugg->isUpvotedBy(Auth::id()) ? '-fill' : ''); ?> me-2" data-upvote-icon></i><span data-upvote-count><?php echo e($sugg->upvotes_count ?? 0); ?></span>
                                                </button>
                                            </form>
                                            <form action="<?php echo e(route('suggestions.report', $sugg->id)); ?>" method="POST" class="d-inline report-content-form">
                                                <?php echo csrf_field(); ?>
                                                <input type="hidden" name="reason" value="">
                                                <button type="submit" class="btn btn-outline-secondary btn-sm report-content-button" title="Report post" aria-label="Report post"><i class="bi bi-flag"></i></button>
                                            </form>
                                            <?php if($sugg->user_id === Auth::id()): ?>
                                                <div class="dropdown">
                                                    <button type="button" class="kebab-btn" data-bs-toggle="dropdown" aria-expanded="false" title="More actions">
                                                        <i class="bi bi-three-dots-vertical"></i>
                                                    </button>
                                                    <ul class="dropdown-menu dropdown-menu-end action-menu">
                                                        <li>
                                                            <button type="button" class="dropdown-item" data-bs-toggle="modal" data-bs-target="#editSuggestionModal<?php echo e($sugg->id); ?>">
                                                                <i class="bi bi-pencil-square me-2"></i>Edit
                                                            </button>
                                                        </li>
                                                        <li>
                                                            <form action="<?php echo e(route('suggestions.destroy', $sugg->id)); ?>" method="POST" onsubmit="return confirm('Delete this suggestion permanently?');">
                                                                <?php echo csrf_field(); ?>
                                                                <?php echo method_field('DELETE'); ?>
                                                                <button type="submit" class="dropdown-item text-danger">
                                                                    <i class="bi bi-trash3 me-2"></i>Delete
                                                                </button>
                                                            </form>
                                                        </li>
                                                    </ul>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>

                                <div class="p-4 p-lg-4">
                                    <p class="suggestion-message mb-0"><?php echo e($sugg->message); ?></p>

                                    <div class="d-flex flex-wrap align-items-center gap-2 gap-md-3 mt-4 pt-3" style="border-top:1px solid rgba(124,58,237,0.08);">
                                        <button type="button" class="btn btn-light action-btn px-3" onclick="document.getElementById('comment-input-<?php echo e($sugg->id); ?>').focus();">
                                            <i class="bi bi-chat-dots me-2"></i><?php echo e($sugg->comments->count()); ?> comments
                                        </button>
                                        <span class="text-muted small">Tap the comment field below to reply directly.</span>
                                    </div>

                                    <div class="mt-4 d-grid gap-3">
                                        <?php $__empty_2 = true; $__currentLoopData = $sugg->comments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $comment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_2 = false; ?>
                                            <div class="comment-card">
                                                <div class="d-flex justify-content-between align-items-start gap-3">
                                                    <div class="flex-grow-1">
                                                        <div class="d-flex align-items-center gap-2 mb-2">
                                                            <span class="comment-author"><?php echo e($comment->user ? $comment->user->cloak_alias : 'Anonymous'); ?></span>
                                                            <span class="text-muted small"><?php echo e($comment->created_at->diffForHumans()); ?></span>
                                                        </div>
                                                        <div class="comment-text"><?php echo e($comment->comment); ?></div>
                                                    </div>
                                                    <?php if($comment->user_id === Auth::id()): ?>
                                                                <div class="dropdown">
                                                                    <button type="button" class="kebab-btn" data-bs-toggle="dropdown" aria-expanded="false" title="More actions">
                                                                        <i class="bi bi-three-dots-vertical"></i>
                                                                    </button>
                                                                    <ul class="dropdown-menu dropdown-menu-end action-menu">
                                                                        <li>
                                                                            <button type="button" class="dropdown-item" data-bs-toggle="modal" data-bs-target="#editCommentModal<?php echo e($comment->id); ?>">
                                                                                <i class="bi bi-pencil-square me-2"></i>Edit
                                                                            </button>
                                                                        </li>
                                                                        <li>
                                                                            <form action="<?php echo e(route('comments.destroy', $comment->id)); ?>" method="POST" onsubmit="return confirm('Delete this comment permanently?');">
                                                                                <?php echo csrf_field(); ?>
                                                                                <?php echo method_field('DELETE'); ?>
                                                                                <button type="submit" class="dropdown-item text-danger">
                                                                                    <i class="bi bi-trash3 me-2"></i>Delete
                                                                                </button>
                                                                            </form>
                                                                        </li>
                                                                    </ul>
                                                                </div>
                                                                <?php endif; ?>
                                                            <?php if($comment->user_id !== Auth::id()): ?>
                                                        <form action="<?php echo e(route('comments.report', $comment->id)); ?>" method="POST" class="d-inline report-content-form ms-2">
                                                            <?php echo csrf_field(); ?>
                                                            <input type="hidden" name="reason" value="">
                                                            <button type="submit" class="btn btn-link text-secondary p-0 report-content-button" title="Report comment" aria-label="Report comment"><i class="bi bi-flag"></i></button>
                                                        </form>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_2): ?>
                                            <div class="text-muted small py-1">
                                                No comments yet. Be the first to respond.
                                            </div>
                                        <?php endif; ?>
                                    </div>

                                    <form action="<?php echo e(route('suggestions.comment', $sugg->id)); ?>" method="POST" class="comment-form mt-4">
                                        <?php echo csrf_field(); ?>
                                        <div class="input-group">
                                            <input type="text" id="comment-input-<?php echo e($sugg->id); ?>" name="comment" class="form-control rounded-start-pill" data-moderated-input placeholder="Write a comment..." required>
                                            <button type="submit" class="btn btn-primary rounded-end-pill px-4" data-moderated-submit style="background: linear-gradient(135deg, #A594F9 0%, #8f72f5 100%); border:0; font-weight:800;">
                                                Post
                                            </button>
                                        </div>
                                        <div class="moderation-warning text-danger small mt-2 d-none">Naglalaman ng hindi angkop na salita ang iyong komento.</div>
                                    </form>
                                </div>
                            </article>

                            <div class="modal fade" id="editSuggestionModal<?php echo e($sugg->id); ?>" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered modal-lg">
                                    <div class="modal-content border-0 shadow-lg">
                                        <div class="modal-header border-0 p-4 pb-0">
                                            <div>
                                                <h5 class="fw-bold mb-1">Edit Suggestion</h5>
                                                <p class="text-muted mb-0">Update your own suggestion only.</p>
                                            </div>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <form action="<?php echo e(route('suggestions.update', $sugg->id)); ?>" method="POST">
                                            <?php echo csrf_field(); ?>
                                            <?php echo method_field('PUT'); ?>
                                            <div class="modal-body p-4">
                                                <div class="row g-3">
                                                    <div class="col-md-4">
                                                        <label class="form-label small fw-bold text-muted">CATEGORY</label>
                                                        <select name="category" class="form-select rounded-3" required>
                                                            <option value="Gender-Based Violence Prevention" <?php echo e($sugg->category === 'Gender-Based Violence Prevention' ? 'selected' : ''); ?>>Gender-Based Violence Prevention</option>
                                                            <option value="Gender Equality and Inclusion" <?php echo e($sugg->category === 'Gender Equality and Inclusion' ? 'selected' : ''); ?>>Gender Equality and Inclusion</option>
                                                            <option value="GAD Programs and Services" <?php echo e($sugg->category === 'GAD Programs and Services' ? 'selected' : ''); ?>>GAD Programs and Services</option>
                                                            <option value="Safe Spaces and Student Welfare" <?php echo e($sugg->category === 'Safe Spaces and Student Welfare' ? 'selected' : ''); ?>>Safe Spaces and Student Welfare</option>
                                                            <option value="GAD Policy and Training" <?php echo e($sugg->category === 'GAD Policy and Training' ? 'selected' : ''); ?>>GAD Policy and Training</option>
                                                            <option value="Other GAD Concern" <?php echo e($sugg->category === 'Other GAD Concern' ? 'selected' : ''); ?>>Other GAD Concern</option>
                                                        </select>
                                                    </div>
                                                    <div class="col-md-8">
                                                        <label class="form-label small fw-bold text-muted">YOUR SUGGESTION</label>
                                                        <textarea name="message" class="form-control rounded-3" rows="5" required><?php echo e($sugg->message); ?></textarea>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal-footer border-0 p-4 pt-0">
                                                <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                                                <button type="submit" class="btn btn-primary rounded-pill px-4" style="background: linear-gradient(135deg, #A594F9 0%, #8f72f5 100%); border:0; font-weight:800;">
                                                    Save Changes
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>

                            <?php $__currentLoopData = $sugg->comments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $comment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="modal fade" id="editCommentModal<?php echo e($comment->id); ?>" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content border-0 shadow-lg">
                                            <div class="modal-header border-0 p-4 pb-0">
                                                <div>
                                                    <h5 class="fw-bold mb-1">Edit Comment</h5>
                                                    <p class="text-muted mb-0">Update your own comment only.</p>
                                                </div>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <form action="<?php echo e(route('comments.update', $comment->id)); ?>" method="POST">
                                                <?php echo csrf_field(); ?>
                                                <?php echo method_field('PUT'); ?>
                                                <div class="modal-body p-4">
                                                    <label class="form-label small fw-bold text-muted">COMMENT</label>
                                                    <textarea name="comment" class="form-control rounded-3" rows="4" required><?php echo e($comment->comment); ?></textarea>
                                                </div>
                                                <div class="modal-footer border-0 p-4 pt-0">
                                                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                                                    <button type="submit" class="btn btn-primary rounded-pill px-4" style="background: linear-gradient(135deg, #A594F9 0%, #8f72f5 100%); border:0; font-weight:800;">
                                                        Save Changes
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <div class="empty-panel">
                                <div class="empty-illustration">
                                    <i class="bi bi-megaphone-fill fs-2"></i>
                                </div>
                                <h3 class="h5 fw-bold mb-2">No suggestions yet</h3>
                                <p class="text-muted mb-4">Be the first to open the conversation and share what needs to change.</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<div class="modal fade" id="suggestionModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header border-0 p-4 pb-0">
                <div>
                    <h5 class="fw-bold mb-1">Write Your Suggestions</h5>
                    <p class="text-muted mb-0">Your identity will remain hidden as <?php echo e(Auth::user()->cloak_alias); ?>.</p>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?php echo e(route('suggestions.store')); ?>" method="POST" id="suggestionCreateForm">
                <?php echo csrf_field(); ?>
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label small fw-bold text-muted">CATEGORY</label>
                            <select name="category" class="form-select rounded-3" required>
                                <option value="" disabled selected>Select a category</option>
                                <option value="Gender-Based Violence Prevention">Gender-Based Violence Prevention</option>
                                <option value="Gender Equality and Inclusion">Gender Equality and Inclusion</option>
                                <option value="GAD Programs and Services">GAD Programs and Services</option>
                                <option value="Safe Spaces and Student Welfare">Safe Spaces and Student Welfare</option>
                                <option value="GAD Policy and Training">GAD Policy and Training</option>
                                <option value="Other GAD Concern">Other GAD Concern</option>
                            </select>
                        </div>
                        <div class="col-md-8">
                            <label class="form-label small fw-bold text-muted">YOUR SUGGESTION</label>
                            <textarea name="message" class="form-control rounded-3" rows="5" data-moderated-input placeholder="Write your suggestions..." required></textarea>
                        </div>
                    </div>
                    <div class="alert alert-info border-0 mt-3 mb-0">
                        <i class="bi bi-shield-check me-2"></i>
                        Positive at constructive suggestions only. Ipapakita ang iyong message gamit ang alias na <strong><?php echo e(Auth::user()->cloak_alias); ?></strong>.
                    </div>
                </div>
                <div class="modal-footer border-0 p-4 pt-0">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4" data-moderated-submit style="background: linear-gradient(135deg, #A594F9 0%, #8f72f5 100%); border:0; font-weight:800;">
                        Post Suggestion
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const harshWords = ['bobo', 'tanga', 'gago', 'puta', 'putangina', 'peste', 'piste', 'ukinam', 'bwisit', 'lintik', 'hayop', 'tarantado', 'leche', 'ulol', 'inutil', 'siraulo', 'yawa', 'amaw', 'buang', 'atay', 'kablaaw', 'pukaw', 'ukininam', 'pisti', 'bogo', 'gahi', 'animal', 'stupid', 'idiot', 'dumb', 'moron', 'hate', 'kill', 'trash', 'useless', 'disgusting', 'shit', 'fuck'];
        const normalize = function (value) {
            return value.toLowerCase().replace(/[@]/g, 'a').replace(/[0]/g, 'o').replace(/[1]/g, 'i').replace(/[$]/g, 's').replace(/[3]/g, 'e').replace(/[4]/g, 'a').replace(/[5]/g, 's').replace(/[6]/g, 'g').replace(/[7]/g, 't').replace(/[8]/g, 'b').replace(/[9]/g, 'g').replace(/[^\p{L}\p{N}]+/gu, ' ');
        };
        document.querySelectorAll('[data-moderated-input]').forEach(function (input) {
            const form = input.closest('form');
            const submit = form.querySelector('[data-moderated-submit]');
            const warning = form.querySelector('.moderation-warning');
            input.addEventListener('input', function () {
                const normalized = normalize(input.value);
                const blocked = harshWords.some(function (word) { return new RegExp('(^|\\s)' + word.replace(/[.*+?^${}()|[\\]\\\\]/g, '\\$&') + '(?=\\s|$)', 'i').test(normalized); });
                warning?.classList.toggle('d-none', !blocked);
                if (submit) submit.disabled = blocked;
            });
        });

        document.querySelectorAll('.report-content-form').forEach(function (form) {
            form.addEventListener('submit', function (event) {
                const reasonInput = form.querySelector('input[name="reason"]');
                if (!reasonInput || reasonInput.value.trim() !== '') {
                    return;
                }

                const reason = window.prompt('Why are you reporting this content?');
                if (!reason || reason.trim() === '') {
                    event.preventDefault();
                    return;
                }

                reasonInput.value = reason.trim();
            });
        });

        const createForm = document.getElementById('suggestionCreateForm');
        if (createForm) {
            createForm.addEventListener('submit', function (event) {
                if (createForm.dataset.submitting === 'true') {
                    event.preventDefault();
                    return;
                }

                createForm.dataset.submitting = 'true';
                const submit = createForm.querySelector('[data-moderated-submit]');
                if (submit) {
                    submit.disabled = true;
                    submit.textContent = 'Posting...';
                }
            });
        }

        const searchInput = document.getElementById('suggestion-search');
        const cards = document.querySelectorAll('[data-suggestion-card]');
        const upvoteForms = document.querySelectorAll('[data-upvote-form]');

        if (!searchInput || !cards.length) {
            return;
        }

        searchInput.addEventListener('input', function () {
            const query = this.value.trim().toLowerCase();

            cards.forEach(function (card) {
                const haystack = (card.getAttribute('data-suggestion-text') || '').toLowerCase();
                const isVisible = haystack.includes(query);

                card.style.display = isVisible ? '' : 'none';
            });
        });

        upvoteForms.forEach(function (form) {
            form.addEventListener('submit', async function (event) {
                event.preventDefault();

                const button = form.querySelector('[data-upvote-button]');
                const icon = form.querySelector('[data-upvote-icon]');
                const countEl = form.querySelector('[data-upvote-count]');
                const token = form.querySelector('input[name="_token"]');

                if (!button || !icon || !countEl || !token) {
                    form.submit();
                    return;
                }

                const originalHtml = button.innerHTML;
                button.disabled = true;

                try {
                    const response = await fetch(form.action, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': token.value,
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        }
                    });

                    if (!response.ok) {
                        throw new Error('Request failed');
                    }

                    const payload = await response.json();
                    const isActive = Boolean(payload.upvoted);

                    countEl.textContent = String(payload.upvotes_count ?? countEl.textContent);
                    button.classList.toggle('is-active', isActive);
                    icon.classList.toggle('bi-hand-thumbs-up', !isActive);
                    icon.classList.toggle('bi-hand-thumbs-up-fill', isActive);
                } catch (error) {
                    // Fallback to normal submit if fetch fails for any reason.
                    button.innerHTML = originalHtml;
                    form.submit();
                    return;
                } finally {
                    button.disabled = false;
                }
            });
        });
    });
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\sinag\resources\views/student/boses.blade.php ENDPATH**/ ?>