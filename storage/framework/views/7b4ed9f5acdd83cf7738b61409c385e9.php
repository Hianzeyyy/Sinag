

<?php $__env->startSection('content'); ?>

<div class="fw-wrap">

    
    <div class="fw-topbar">
        <div class="fw-topbar-left">
            <div class="fw-icon-box">
                <i class="bi bi-megaphone-fill"></i>
            </div>
            <div>
                <h1 class="fw-page-title">Freedom Wall</h1>
                <p class="fw-page-sub">Students post freely here · Admin may remove inappropriate content</p>
            </div>
        </div>
        <div class="fw-count-chip">
            <span class="fw-count-num"><?php echo e($suggestions->count()); ?></span>
            <span class="fw-count-text"><?php echo e($suggestions->count() == 1 ? 'Post' : 'Posts'); ?></span>
        </div>
        <a href="<?php echo e(route('admin.suggestions.reports')); ?>" class="btn btn-outline-secondary rounded-pill fw-bold">
            <i class="bi bi-flag me-1"></i> See All Reports <?php if(($pendingSuggestionReports ?? 0) > 0): ?><span class="badge bg-secondary ms-1"><?php echo e($pendingSuggestionReports); ?></span><?php endif; ?>
        </a>
    </div>

    
    <div class="fw-grid">
        <?php $__empty_1 = true; $__currentLoopData = $suggestions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $suggestion): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>

        <?php
            $themes = [
                'Lighting'      => ['color' => '#f59e0b', 'light' => '#fffbeb', 'icon' => 'bi-lightbulb-fill'],
                'Security'      => ['color' => '#f8cb12', 'light' => '#fff8d6', 'icon' => 'bi-shield-fill'],
                'Facilities'    => ['color' => '#3b82f6', 'light' => '#eff6ff', 'icon' => 'bi-building-fill'],
                'Campus Safety' => ['color' => '#7c3aed', 'light' => '#f5f3ff', 'icon' => 'bi-exclamation-triangle-fill'],
            ];
            $t = $themes[$suggestion->category] ?? ['color' => '#A594F9', 'light' => '#f5f3ff', 'icon' => 'bi-chat-fill'];
        ?>

        <div class="fw-card" style="--c: <?php echo e($t['color']); ?>; --cl: <?php echo e($t['light']); ?>; animation-delay: <?php echo e($i * 0.06); ?>s">

            
            <div class="fw-bar"></div>

            <div class="fw-card-inner">

                
                <div class="fw-card-head">
                    <div class="fw-cat">
                        <span class="fw-cat-icon"><i class="bi <?php echo e($t['icon']); ?>"></i></span>
                        <span class="fw-cat-label"><?php echo e($suggestion->category); ?></span>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge rounded-pill <?php echo e(strtolower($suggestion->status) === 'approved' ? 'sinag-status-approved' : (strtolower($suggestion->status) === 'rejected' ? 'sinag-status-rejected' : 'sinag-status-pending')); ?>"><?php echo e($suggestion->status); ?></span>
                        <span class="fw-time"><i class="bi bi-clock me-1"></i><?php echo e($suggestion->created_at->diffForHumans()); ?></span>
                    </div>
                </div>

                
                <div class="fw-body">
                    <p class="fw-msg"><?php echo e($suggestion->message); ?></p>
                </div>

                
                <div class="fw-card-foot">
                    <div class="fw-author">
                        <div class="fw-ava">
                            <i class="bi <?php echo e($suggestion->is_anonymous ? 'bi-incognito' : 'bi-person-fill'); ?>"></i>
                        </div>
                        <span class="fw-author-name">
                            <?php echo e($suggestion->is_anonymous ? 'Anonymous' : $suggestion->user->name); ?>

                        </span>
                    </div>

                    <div class="fw-actions">
                        <button type="button" class="fw-btn-view border-0" data-bs-toggle="modal" data-bs-target="#suggestionModal<?php echo e($suggestion->id); ?>">
                            <i class="bi bi-eye me-1"></i> View
                        </button>
                        <form action="<?php echo e(route('suggestions.destroy', $suggestion->id)); ?>" method="POST"
                              onsubmit="return confirm('Delete this post from the freedom wall?');" style="display:inline">
                            <?php echo csrf_field(); ?>
                            <?php echo method_field('DELETE'); ?>
                            <button type="submit" class="fw-btn-del">
                                <i class="bi bi-trash3"></i>
                            </button>
                        </form>
                    </div>
                </div>

            </div>
        </div>

        <div class="modal fade" id="suggestionModal<?php echo e($suggestion->id); ?>" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content border-0 shadow-lg">
                    <div class="modal-header">
                        <div>
                            <div class="text-muted small text-uppercase"><?php echo e($suggestion->category); ?></div>
                            <h5 class="modal-title fw-bold">Suggestion #<?php echo e($suggestion->id); ?></h5>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="d-flex justify-content-between gap-3 mb-3">
                            <span class="fw-semibold"><?php echo e($suggestion->is_anonymous ? 'Anonymous' : optional($suggestion->user)->name); ?></span>
                            <span class="text-muted small"><?php echo e($suggestion->created_at->format('M d, Y h:i A')); ?></span>
                        </div>
                        <div class="p-3 rounded-3 border bg-light" style="white-space:pre-wrap;"><?php echo e($suggestion->message); ?></div>
                    </div>
                    <div class="modal-footer">
                        <form action="<?php echo e(route('admin.suggestions.review', $suggestion->id)); ?>" method="POST">
                            <?php echo csrf_field(); ?>
                            <input type="hidden" name="action" value="approve">
                            <button type="submit" class="btn btn-secondary">Approve Suggestion</button>
                        </form>
                        <form action="<?php echo e(route('admin.suggestions.review', $suggestion->id)); ?>" method="POST">
                            <?php echo csrf_field(); ?>
                            <input type="hidden" name="action" value="reject">
                            <button type="submit" class="btn btn-outline-secondary">Reject Suggestion</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
        <div class="fw-empty">
            <i class="bi bi-chat-left-dots fw-empty-icon"></i>
            <p class="fw-empty-title">No posts yet</p>
            <p class="fw-empty-sub">Students' feedback will appear here once they start posting.</p>
        </div>
        <?php endif; ?>
    </div>

</div>

<style>
/* ── Reset / Base ─────────────────────────── */
*, *::before, *::after { box-sizing: border-box; }

.fw-wrap {
    padding: 28px 32px 60px;
    background: #f4f3fb;
    min-height: 100vh;
    font-family: 'Segoe UI', system-ui, sans-serif;
}

/* ── Top Bar ──────────────────────────────── */
.fw-topbar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    background: #fff;
    border-radius: 18px;
    padding: 18px 24px;
    margin-bottom: 28px;
    border: 1px solid #ebe8ff;
    box-shadow: 0 2px 12px rgba(124,58,237,0.06);
    gap: 16px;
    flex-wrap: wrap;
}
.fw-topbar-left {
    display: flex;
    align-items: center;
    gap: 16px;
}
.fw-icon-box {
    width: 48px; height: 48px;
    background: linear-gradient(135deg, #7c3aed, #a78bfa);
    border-radius: 14px;
    display: flex; align-items: center; justify-content: center;
    color: #fff;
    font-size: 1.2rem;
    flex-shrink: 0;
    box-shadow: 0 4px 14px rgba(124,58,237,0.3);
}
.fw-page-title {
    font-size: 1.35rem;
    font-weight: 800;
    color: #1e1b4b;
    margin: 0 0 2px;
    letter-spacing: -0.02em;
}
.fw-page-sub {
    font-size: 0.75rem;
    color: #9ca3af;
    margin: 0;
}
.fw-count-chip {
    background: #f5f3ff;
    border: 1px solid #ddd6fe;
    border-radius: 14px;
    padding: 10px 20px;
    text-align: center;
    min-width: 72px;
}
.fw-count-num {
    display: block;
    font-size: 1.6rem;
    font-weight: 800;
    color: #7c3aed;
    line-height: 1;
}
.fw-count-text {
    font-size: 0.65rem;
    color: #a78bfa;
    text-transform: uppercase;
    letter-spacing: 0.1em;
    font-weight: 700;
}

.sinag-status-approved { background: #d8cdfc; color: #5b4b9b; }
.sinag-status-rejected { background: #fff8d6; color: #806000; }
.sinag-status-pending { background: #f8cb12; color: #4a3500; }

/* ── Grid ─────────────────────────────────── */
.fw-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 20px;
}
@media (max-width: 768px) {
    .fw-grid { grid-template-columns: 1fr; }
    .fw-wrap { padding: 16px; }
}

/* ── Card ─────────────────────────────────── */
.fw-card {
    background: #fff;
    border-radius: 18px;
    border: 1px solid #ede9fe;
    display: flex;
    overflow: hidden;
    box-shadow: 0 2px 10px rgba(124,58,237,0.05);
    transition: transform 0.22s ease, box-shadow 0.22s ease;
    animation: fadeUp 0.4s ease both;
    min-height: 200px;
}
.fw-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 10px 28px rgba(124,58,237,0.12);
}
@keyframes fadeUp {
    from { opacity: 0; transform: translateY(16px); }
    to   { opacity: 1; transform: translateY(0); }
}

/* Left colored bar */
.fw-bar {
    width: 5px;
    background: var(--c);
    flex-shrink: 0;
    border-radius: 0;
}

.fw-card-inner {
    flex: 1;
    padding: 20px 22px;
    display: flex;
    flex-direction: column;
    gap: 14px;
}

/* ── Card Head ────────────────────────────── */
.fw-card-head {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 8px;
}
.fw-cat {
    display: flex;
    align-items: center;
    gap: 6px;
    background: var(--cl);
    border: 1px solid color-mix(in srgb, var(--c) 20%, transparent);
    padding: 4px 12px 4px 8px;
    border-radius: 999px;
}
.fw-cat-icon {
    color: var(--c);
    font-size: 0.72rem;
    line-height: 1;
}
.fw-cat-label {
    font-size: 0.72rem;
    font-weight: 700;
    color: #374151;
    letter-spacing: 0.01em;
}
.fw-time {
    font-size: 0.68rem;
    color: #9ca3af;
    white-space: nowrap;
}

/* ── Message ──────────────────────────────── */
.fw-body { flex: 1; }
.fw-msg {
    font-size: 0.9rem;
    color: #374151;
    line-height: 1.75;
    margin: 0;
    display: -webkit-box;
    -webkit-line-clamp: 4;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

/* ── Footer ───────────────────────────────── */
.fw-card-foot {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding-top: 14px;
    border-top: 1px solid #f3f4f6;
    gap: 8px;
}
.fw-author {
    display: flex;
    align-items: center;
    gap: 8px;
    min-width: 0;
}
.fw-ava {
    width: 30px; height: 30px;
    background: var(--cl);
    border: 1.5px solid color-mix(in srgb, var(--c) 30%, transparent);
    border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    color: var(--c);
    font-size: 0.78rem;
    flex-shrink: 0;
}
.fw-author-name {
    font-size: 0.78rem;
    font-weight: 600;
    color: #6b7280;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.fw-actions {
    display: flex;
    align-items: center;
    gap: 8px;
    flex-shrink: 0;
}
.fw-btn-view {
    display: inline-flex;
    align-items: center;
    padding: 6px 16px;
    border-radius: 999px;
    background: linear-gradient(135deg, #7c3aed, #a78bfa);
    color: #fff;
    font-size: 0.72rem;
    font-weight: 700;
    text-decoration: none;
    transition: opacity 0.15s, transform 0.15s;
    box-shadow: 0 2px 8px rgba(124,58,237,0.25);
}
.fw-btn-view:hover {
    opacity: 0.9;
    transform: scale(1.04);
    color: #fff;
    text-decoration: none;
}
.fw-btn-del {
    width: 32px; height: 32px;
    border-radius: 50%;
    border: 1px solid #d8cdfc;
    background: #f3efff;
    color: #7658df;
    font-size: 0.75rem;
    display: flex; align-items: center; justify-content: center;
    cursor: pointer;
    transition: background 0.15s, transform 0.1s;
    padding: 0;
}
.fw-btn-del:hover {
    background: #fee2e2;
    transform: scale(1.08);
}

/* ── Empty ────────────────────────────────── */
.fw-empty {
    grid-column: 1 / -1;
    text-align: center;
    padding: 80px 24px;
    background: #fff;
    border-radius: 20px;
    border: 1.5px dashed #ddd6fe;
}
.fw-empty-icon {
    font-size: 3rem;
    color: #c4b5fd;
    display: block;
    margin-bottom: 16px;
}
.fw-empty-title {
    font-size: 1.05rem;
    font-weight: 700;
    color: #4b5563;
    margin: 0 0 6px;
}
.fw-empty-sub {
    font-size: 0.82rem;
    color: #9ca3af;
    margin: 0;
}
@media (max-width: 1199.98px) {
    .fw-wrap { padding: 20px 18px 40px; }
    .fw-grid { gap: 14px; }
    .fw-card-inner { padding: 16px; }
}
@media (max-width: 767.98px) {
    .fw-wrap { padding: 12px 10px 28px; }
    .fw-topbar { padding: 14px; border-radius: 14px; margin-bottom: 16px; }
    .fw-topbar-left { gap: 10px; }
    .fw-icon-box { width: 40px; height: 40px; border-radius: 11px; }
    .fw-page-title { font-size: 1.1rem; }
    .fw-page-sub { font-size: .68rem; }
    .fw-count-chip { padding: 7px 12px; min-width: 58px; }
    .fw-grid { grid-template-columns: 1fr; gap: 12px; }
    .fw-card { min-height: 0; border-radius: 14px; }
    .fw-card-head { align-items: flex-start; flex-direction: column; }
    .fw-card-inner { padding: 14px; gap: 10px; }
    .fw-card-foot { align-items: flex-start; flex-direction: column; }
    .fw-actions { width: 100%; justify-content: flex-end; }
}
</style>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\sinag\resources\views/admin/suggestions.blade.php ENDPATH**/ ?>