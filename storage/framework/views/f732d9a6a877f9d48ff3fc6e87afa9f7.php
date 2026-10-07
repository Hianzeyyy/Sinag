

<?php $__env->startSection('content'); ?>
<style>
    .sinag-violet-badge { background: #d8cdfc; color: #5b4b9b; }
    .sinag-yellow-badge { background: #f8cb12; color: #4a3500; }

    .admin-dashboard-page {
        --accent: #A594F9;
        --accent-deep: #8f72f5;
        --ink: #111827;
        --muted: #6b7280;
        background:
            radial-gradient(circle at top left, rgba(165, 148, 249, 0.12), transparent 34%),
            radial-gradient(circle at right top, rgba(250, 204, 21, 0.12), transparent 22%),
            linear-gradient(180deg, #faf7ff 0%, #f8fafc 62%, #f8fafc 100%);
        border-radius: 1.45rem;
        padding: 1rem;
    }

    .admin-dashboard-wrap {
        max-width: 1440px;
        margin: 0 auto;
    }

    .admin-hero {
        border: 0;
        border-radius: 1.4rem;
        overflow: hidden;
        color: #fff;
        background: linear-gradient(120deg, #A594F9 0%, #c4b5fd 58%, #facc15 100%);
        box-shadow: 0 20px 42px rgba(165, 148, 249, 0.22);
    }

    .hero-kicker {
        display: inline-flex;
        align-items: center;
        gap: 0.45rem;
        padding: 0.4rem 0.75rem;
        border-radius: 999px;
        font-size: 0.75rem;
        font-weight: 800;
        letter-spacing: 0.08em;
        text-transform: uppercase;
        background: rgba(255, 255, 255, 0.16);
    }

    .hero-title {
        font-weight: 900;
        font-size: clamp(1.9rem, 3vw, 2.6rem);
        line-height: 1.05;
    }

    .hero-copy {
        color: rgba(255, 255, 255, 0.9);
        max-width: 40rem;
    }

    .hero-action {
        border: 0;
        border-radius: 999px;
        background: #fff;
        color: #8f72f5;
        font-weight: 800;
        box-shadow: 0 12px 22px rgba(17, 24, 39, 0.2);
    }

    .hero-action:hover {
        color: #8f72f5;
    }

    .metric-card {
        border: 0;
        border-radius: 1.1rem;
        color: #fff;
        box-shadow: 0 14px 30px rgba(15, 23, 42, 0.14);
        overflow: hidden;
    }

    .metric-card .card-body {
        position: relative;
    }

    .metric-card .card-body::after {
        content: '';
        position: absolute;
        width: 6rem;
        height: 6rem;
        border-radius: 999px;
        right: -2rem;
        top: -2rem;
        background: rgba(255, 255, 255, 0.16);
    }

    .metric-label {
        font-size: 0.72rem;
        letter-spacing: 0.09em;
    }

    .dashboard-panel {
        border: 1px solid rgba(165, 148, 249, 0.14);
        border-radius: 1.2rem;
        box-shadow: 0 14px 34px rgba(165, 148, 249, 0.1);
    }

    .dashboard-panel .card-header {
        background: #fff;
    }

    .reports-table thead {
        background: #f8fafc;
    }

    .reports-table tbody tr:hover {
        background-color: #fbfaff !important;
    }

    .review-btn {
        border: 1px solid #A594F9;
        color: #8f72f5;
        font-weight: 700;
    }

    .review-btn:hover {
        background: #A594F9;
        color: #fff;
    }

    .admin-tool-btn {
        transition: transform 0.2s, box-shadow 0.2s;
        border: 1px solid rgba(165, 148, 249, 0.12);
    }

    .admin-tool-btn:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1) !important;
    }

    .tool-icon {
        width: 2.35rem;
        height: 2.35rem;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 999px;
    }

    .command-tools-card {
        overflow: hidden;
        border-color: rgba(165, 148, 249, 0.18);
        box-shadow: 0 16px 36px rgba(165, 148, 249, 0.12);
    }

    .command-tools-head {
        background: linear-gradient(135deg, rgba(165, 148, 249, 0.18), rgba(196, 181, 253, 0.18));
        border: 1px solid rgba(165, 148, 249, 0.2);
        border-radius: 1rem;
    }

    .command-tools-title {
        font-size: 1.08rem;
        color: #111827;
        font-weight: 900;
    }

    .command-tools-sub {
        color: #6b7280;
        font-size: 0.82rem;
    }

    .admin-tool-btn {
        position: relative;
        overflow: hidden;
        border: 1px solid rgba(165, 148, 249, 0.18);
        box-shadow: 0 8px 16px rgba(15, 23, 42, 0.06);
    }

    .admin-tool-btn::after {
        content: '';
        position: absolute;
        width: 4.25rem;
        height: 4.25rem;
        border-radius: 999px;
        right: -1.75rem;
        top: -1.75rem;
        background: rgba(255, 255, 255, 0.28);
        pointer-events: none;
    }

    .admin-tool-btn .text-muted {
        color: #334155 !important;
        opacity: 0.9;
    }

    .admin-tool-users {
        background: linear-gradient(135deg, #f5f3ff 0%, #e9d5ff 100%) !important;
    }

    .admin-tool-boses {
        background: linear-gradient(135deg, #eef2ff 0%, #ddd6fe 100%) !important;
    }

    .admin-tool-announcements {
        background: linear-gradient(135deg, #f8fafc 0%, #ede9fe 100%) !important;
    }

    .admin-tool-arrow {
        width: 1.8rem;
        height: 1.8rem;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 999px;
        background: rgba(255, 255, 255, 0.85);
        color: #8f72f5;
        transition: transform 0.2s ease;
        flex: 0 0 auto;
    }

    .admin-tool-btn:hover .admin-tool-arrow {
        transform: translateX(2px);
    }

    .privacy-box {
        border: 1px solid rgba(255, 255, 255, 0.2);
        box-shadow: inset 0 0 0 1px rgba(255, 255, 255, 0.05);
        background: linear-gradient(135deg, #A594F9 0%, #c4b5fd 100%) !important;
    }

    .badge {
        font-size: 0.7rem;
        font-weight: 600;
    }

    @media (max-width: 991px) {
        .admin-dashboard-page {
            padding: 0.55rem;
        }
    }
</style>

<div class="container-fluid admin-dashboard-page">
    <div class="admin-dashboard-wrap">
        <div class="card admin-hero mb-4">
            <div class="card-body p-4 p-lg-5">
                <div class="d-flex flex-column flex-lg-row align-items-lg-center justify-content-between gap-3">
                    <div>
                        <span class="hero-kicker mb-3"><i class="bi bi-shield-check"></i> GAD Command Center</span>
                        <h2 class="hero-title mb-2">GAD Office Analytics</h2>
                        <p class="hero-copy mb-0">Real-time monitoring of campus safety, incident escalation, and response status across the reporting system.</p>
                    </div>
                    <div class="text-lg-end">
                        <button class="btn hero-action px-4 py-2">
                            <i class="bi bi-file-earmark-pdf me-2"></i>Export GAD Report
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-md-6 col-xl-3">
                <div class="card metric-card h-100" style="background: linear-gradient(130deg, #8f72f5 0%, #A594F9 100%);">
                    <div class="card-body text-white">
                        <h6 class="text-white-50 fw-bold text-uppercase metric-label mb-2">Total Reports</h6>
                        <h2 class="fw-bold mb-1"><?php echo e($totalReports ?? 0); ?></h2>
                        <small class="text-white-50">Active database records</small>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-xl-3">
                <div class="card metric-card h-100" style="background: linear-gradient(130deg, #c084fc 0%, #facc15 100%);">
                    <div class="card-body text-white">
                        <h6 class="text-white-50 fw-bold text-uppercase metric-label mb-2">Pending Review</h6>
                        <h2 class="fw-bold mb-1"><?php echo e($pendingReports ?? 0); ?></h2>
                        <small class="text-white-50">Requires attention</small>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-xl-3">
                <div class="card metric-card h-100" style="background: linear-gradient(130deg, #A594F9 0%, #c4b5fd 100%);">
                    <div class="card-body text-white">
                        <h6 class="text-white-50 fw-bold text-uppercase metric-label mb-2">Resolved Cases</h6>
                        <h2 class="fw-bold mb-1"><?php echo e($resolvedReports ?? 0); ?></h2>
                        <small class="text-white-50">Completed actions</small>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-xl-3">
                <div class="card metric-card h-100" style="background: linear-gradient(130deg, #A594F9 0%, #c4b5fd 100%);">
                    <div class="card-body text-white">
                        <h6 class="text-white-50 fw-bold text-uppercase metric-label mb-2">Suggestions</h6>
                        <h2 class="fw-bold mb-1">28</h2>
                        <small class="text-white-50">Participatory suggestions feedback</small>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-lg-8">
                <div class="card dashboard-panel overflow-hidden h-100">
                    <div class="card-header py-3 border-0">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0 fw-bold text-dark"><i class="bi bi-inbox-fill me-2" style="color: #8f72f5;"></i>Recent Reports</h5>
                            <a href="<?php echo e(route('admin.reports')); ?>" class="btn btn-sm text-decoration-none fw-bold" style="color: #8f72f5;">See All <i class="bi bi-arrow-right"></i></a>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0 reports-table">
                                <thead>
                                    <tr class="small text-muted text-uppercase">
                                        <th class="ps-4">Incident ID</th>
                                        <th>Nature</th>
                                        <th>Status</th>
                                        <th class="text-end pe-4">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $__empty_1 = true; $__currentLoopData = $recentReports ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $report): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                    <tr>
                                        <td class="ps-4 fw-bold">#<?php echo e($report->incident_id); ?></td>
                                        <td class="small"><?php echo e($report->nature); ?></td>
                                        <td>
                                            <span class="badge rounded-pill <?php echo e($report->status == 'Pending' ? 'sinag-yellow-badge' : 'sinag-violet-badge'); ?>">
                                                <?php echo e($report->status); ?>

                                            </span>
                                        </td>
                                        <td class="text-end pe-4">
                                            <a href="<?php echo e(route('admin.reports')); ?>" class="btn btn-sm rounded-pill px-3 py-1 review-btn">Review</a>
                                        </td>
                                    </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                    <tr>
                                        <td colspan="4" class="text-center py-4 text-muted small">No recent reports found.</td>
                                    </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card dashboard-panel h-100 command-tools-card">
                    <div class="card-header py-3 border-0">
                        <div class="command-tools-head d-flex align-items-center justify-content-between gap-3 p-3">
                            <div>
                                <div class="command-tools-title mb-1">Command Tools</div>
                                <div class="command-tools-sub">Quick admin actions and controls</div>
                            </div>
                            <span class="tool-icon" style="background: #e9d5ff; width:2.1rem; height:2.1rem;">
                                <i class="bi bi-lightning-fill" style="color:#8f72f5;"></i>
                            </span>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-3">
                            <a href="<?php echo e(route('admin.users')); ?>" class="btn border-0 text-start py-3 px-4 rounded-4 shadow-sm admin-tool-btn admin-tool-users">
                                <div class="d-flex align-items-center">
                                    <div class="tool-icon me-3" style="background: rgba(255,255,255,0.7);">
                                        <i class="bi bi-people-fill" style="color: #8f72f5;"></i>
                                    </div>
                                    <div>
                                        <span class="d-block fw-bold text-dark small">User Management</span>
                                        <small class="text-muted">Manage GAD personnel</small>
                                    </div>
                                    <span class="admin-tool-arrow ms-auto"><i class="bi bi-arrow-right-short"></i></span>
                                </div>
                            </a>

                            <a href="<?php echo e(route('admin.suggestions')); ?>" class="btn border-0 text-start py-3 px-4 rounded-4 shadow-sm admin-tool-btn admin-tool-boses">
                                <div class="d-flex align-items-center">
                                    <div class="tool-icon me-3" style="background: rgba(255,255,255,0.7);">
                                        <i class="bi bi-chat-square-dots-fill" style="color:#8f72f5;"></i>
                                    </div>
                                    <div>
                                        <span class="d-block fw-bold text-dark small">Participatory Suggestions</span>
                                        <small class="text-muted">Review safe space feedback</small>
                                    </div>
                                    <span class="admin-tool-arrow ms-auto"><i class="bi bi-arrow-right-short"></i></span>
                                </div>
                            </a>

                            <a href="<?php echo e(route('admin.announcements.index')); ?>" class="btn border-0 text-start py-3 px-4 rounded-4 shadow-sm admin-tool-btn admin-tool-announcements">
                                <div class="d-flex align-items-center">
                                    <div class="tool-icon me-3" style="background: rgba(255,255,255,0.7);">
                                        <i class="bi bi-megaphone-fill" style="color: #A594F9;"></i>
                                    </div>
                                    <div>
                                        <span class="d-block fw-bold text-dark small">Announcements</span>
                                        <small class="text-muted">Post campus updates</small>
                                    </div>
                                    <span class="admin-tool-arrow ms-auto"><i class="bi bi-arrow-right-short"></i></span>
                                </div>
                            </a>

                            <a href="<?php echo e(route('admin.gad-schedules.index')); ?>" class="btn border-0 text-start py-3 px-4 rounded-4 shadow-sm admin-tool-btn" style="background: linear-gradient(135deg, #f8fafc 0%, #eef2ff 100%);">
                                <div class="d-flex align-items-center">
                                    <div class="tool-icon me-3" style="background: rgba(255,255,255,0.8);">
                                        <i class="bi bi-calendar2-week-fill" style="color:#8f72f5;"></i>
                                    </div>
                                    <div>
                                        <span class="d-block fw-bold text-dark small">Office Schedule</span>
                                        <small class="text-muted">Set live meeting availability</small>
                                    </div>
                                    <span class="admin-tool-arrow ms-auto"><i class="bi bi-arrow-right-short"></i></span>
                                </div>
                            </a>

                            <div class="p-4 mt-2 rounded-4 text-white privacy-box" style="background: linear-gradient(135deg, #A594F9 0%, #8f72f5 100%);">
                                <h6 class="fw-bold mb-2 small"><i class="bi bi-shield-check me-1"></i>Privacy Protocol</h6>
                                <p class="mb-0" style="font-size: 0.75rem; opacity: 0.82;">
                                    Cloak Protocol is currently encrypting 12 reporter identities. Data access is being logged.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\ANGELINE AMBROSIO\sinag\resources\views/admin/dashboard.blade.php ENDPATH**/ ?>