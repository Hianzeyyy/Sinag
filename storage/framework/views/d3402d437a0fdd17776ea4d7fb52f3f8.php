

<?php $__env->startSection('content'); ?>
<style>
    .sinag-violet-badge { background: #d8cdfc; color: #5b4b9b; border-color: #c4b5fd !important; }
    .sinag-yellow-badge { background: #f8cb12; color: #4a3500; border-color: #e5b900 !important; }
    
</style>
<div class="container py-4">
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            
            <li class="breadcrumb-item active fw-bold" aria-current="page" style="color: #A594F9;">
    Report Management
</li>
        </ol>
    </nav>

    <div class="row">
        <div class="col-12">
            <div class="card shadow-lg border-0 rounded-4" style="overflow: hidden;">
                <div style="height: 5px; background: #A594F9;"></div>
                
                <div class="card-header bg-white py-4 d-flex justify-content-between align-items-center border-0">
                    <div>
                        <h4 class="fw-bold mb-0 text-dark">Confidential Abuse Inbox</h4>
                        <p class="text-muted small mb-0">Managing <span class="badge bg-primary-subtle text-primary"><?php echo e($reports->count()); ?> Active Cases</span> </p>
                    </div>
                    <div class="d-flex gap-2">
                        <div class="input-group input-group-sm" style="width: 250px;">
                            <span class="input-group-text bg-light border-end-0"><i class="bi bi-search text-muted"></i></span>
                            <input type="text" id="reportSearch" class="form-control bg-light border-start-0" placeholder="Search Incident ID or Alias...">
                        </div>
                    </div>
                </div>
                
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0" id="reportsTable">
                            <thead class="bg-light text-muted small">
                                <tr>
                                    <th class="ps-4 py-3">DATE FILED</th>
                                    <th>INCIDENT ID</th>
                                    <th>CLOAK ALIAS</th>
                                    <th>NATURE OF ABUSE</th>
                                    <th>PRIORITY</th>
                                    <th>STATUS</th>
                                    <th class="text-end pe-4">ACTION</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__empty_1 = true; $__currentLoopData = $reports; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $report): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr>
                                    <td class="ps-4 small text-muted"><?php echo e($report->created_at->format('M d, Y')); ?></td>
                                    <td><span class="fw-bold text-dark">#<?php echo e($report->incident_id); ?></span></td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="rounded-circle d-flex align-items-center justify-content-center me-2" 
                                                 style="width: 30px; height: 30px; background: <?php echo e($report->priority == 'High' ? '#fef3c7' : '#f3e8ff'); ?>;">
                                                <i class="bi <?php echo e($report->priority == 'High' ? 'bi-shield-exclamation' : 'bi-incognito text-primary'); ?>" style="<?php echo e($report->priority == 'High' ? 'color:#806000;' : ''); ?>"></i>
                                            </div>
                                            <div class="d-flex align-items-center gap-2">
                                                <span class="small fw-medium text-secondary"><?php echo e($report->cloak_alias); ?></span>
                                                <span id="seen-badge-<?php echo e($report->id); ?>" class="badge rounded-pill <?php echo e(is_null($report->seen_at) ? 'sinag-yellow-badge' : 'sinag-violet-badge'); ?>">
                                                    <?php echo e(is_null($report->seen_at) ? 'UNREAD' : 'READ'); ?>

                                                </span>
                                                <?php if(is_null($report->seen_at) && $report->created_at->gt(now()->subDay())): ?>
                                                    <span id="new-badge-<?php echo e($report->id); ?>" class="badge rounded-pill sinag-yellow-badge">NEW</span>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </td>
                                    <td><span class="small"><?php echo e($report->nature); ?></span></td>
                                    <td>
                                        <span class="badge border <?php echo e($report->priority == 'High' ? 'sinag-yellow-badge pulse-animation' : 'sinag-violet-badge'); ?>">
                                            <?php echo e($report->priority); ?>

                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge rounded-pill px-3 py-2 border
                                            <?php echo e($report->status == 'Pending' ? 'sinag-yellow-badge' : ''); ?>

                                            <?php echo e($report->status == 'Under Review' ? 'sinag-violet-badge' : ''); ?>

                                            <?php echo e($report->status == 'Resolved' ? 'sinag-violet-badge' : ''); ?>">
                                            <?php echo e($report->status); ?>

                                        </span>
                                    </td>
                                    <td class="text-end pe-4">
                                        <button class="btn btn-primary btn-sm rounded-pill px-3 shadow-sm" 
                                                style="background: #A594F9; border: none;"
                                                data-bs-toggle="modal" 
                                                data-bs-target="#modal-<?php echo e($report->id); ?>"
                                                data-report-id="<?php echo e($report->id); ?>">
                                            Review Details
                                        </button>
                                        <form action="<?php echo e(route('admin.reports.delete', $report->id)); ?>" method="POST" style="display:inline-block;">
                                            <?php echo csrf_field(); ?>
                                            <?php echo method_field('DELETE'); ?>
                                           <button type="submit" 
        class="btn btn-sm rounded-pill px-3 ms-2 text-white" 
        style="background-color: #ee529e !important; border-color: #ee529e !important;" 
        onclick="return confirm('Are you sure you want to delete this report?')">
    Delete
</button>
                                        </form>
                                    </td>
                                </tr>

                                <div class="modal fade" id="modal-<?php echo e($report->id); ?>" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered modal-lg">
                                        <div class="modal-content border-0 rounded-4 shadow">
                                            <div class="modal-header border-0 pb-0">
                                                <h5 class="fw-bold mt-2 ps-2">Incident Report Details</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body p-4">
                                                <div class="d-flex justify-content-end mb-3">
                                                    <a href="<?php echo e(route('admin.reports.pdf', $report->id)); ?>" class="btn btn-outline-primary rounded-pill" target="_blank">
                                                        <i class="bi bi-file-earmark-pdf me-1"></i> Download PDF
                                                    </a>
                                                </div>
                                                <div class="row g-4">
                                                    <div class="col-md-6">
                                                        <label class="small text-muted d-block">Incident ID</label>
                                                        <span class="fw-bold">#<?php echo e($report->incident_id); ?></span>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="small text-muted d-block">Reporter Alias</label>
                                                        <span class="fw-bold text-primary"><?php echo e($report->cloak_alias); ?></span>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="small text-muted d-block">Location of Incident</label>
                                                        <span class="fw-medium"><i class="bi bi-geo-alt text-danger me-1"></i> <?php echo e($report->location); ?></span>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="small text-muted d-block">Nature of Abuse</label>
                                                        <span class="badge bg-light text-dark border"><?php echo e($report->nature); ?></span>
                                                    </div>
                                                    
                                                    <div class="col-12">
                                                        <label class="small text-muted d-block">Statement of Facts</label>
                                                        <div class="p-3 bg-light rounded-3 small text-dark border shadow-sm mt-1" style="line-height: 1.6;">
                                                            <?php echo e($report->description); ?>

                                                        </div>
                                                    </div>

                                                    <div class="col-12">
                                                        <label class="small text-muted d-block mb-2">Attached Evidence</label>
                                                        <?php
                                                            $evidenceFiles = $report->evidence_files;
                                                        ?>
                                                        <?php if(count($evidenceFiles)): ?>
                                                            <div class="row g-3">
                                                                <?php $__currentLoopData = $evidenceFiles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $idx => $evidencePath): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                                    <?php
                                                                        $extension = pathinfo($evidencePath, PATHINFO_EXTENSION);
                                                                        $isImage = in_array(strtolower($extension), ['jpg', 'jpeg', 'png', 'gif', 'webp']);
                                                                        $evidenceUrl = route('reports.evidence.view', ['report' => $report->id, 'index' => $idx]);
                                                                    ?>
                                                                    <div class="col-12 col-md-6 col-lg-4">
                                                                        <?php if($isImage): ?>
                                                                            <div class="p-2 border rounded-3 bg-light text-center h-100">
                                                                                <img src="<?php echo e($evidenceUrl); ?>"
                                                                                     class="img-fluid rounded shadow-sm"
                                                                                     style="max-height: 220px; cursor: zoom-in;"
                                                                                     onclick="window.open(this.src)">
                                                                                <p class="small text-muted mt-2 mb-0"><i class="bi bi-info-circle me-1"></i>Click image to preview</p>
                                                                            </div>
                                                                        <?php else: ?>
                                                                            <div class="d-flex align-items-center justify-content-center p-3 border rounded-3 bg-light h-100">
                                                                                <i class="bi bi-file-earmark-text fs-1 text-primary me-3"></i>
                                                                                <div class="text-start">
                                                                                    <span class="d-block small fw-bold">Document Attachment</span>
                                                                                    <a href="<?php echo e($evidenceUrl); ?>" target="_blank" class="btn btn-sm btn-outline-primary mt-1">
                                                                                        <i class="bi bi-download me-1"></i> Open File
                                                                                    </a>
                                                                                </div>
                                                                            </div>
                                                                        <?php endif; ?>
                                                                    </div>
                                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                            </div>
                                                        <?php else: ?>
                                                            <div class="p-3 border border-dashed rounded-3 text-center bg-light text-muted">
                                                                <small><i class="bi bi-camera-video-off me-1"></i> No media or files attached to this report.</small>
                                                            </div>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>

                                                <hr class="my-4">

                                                <form action="<?php echo e(route('admin.reports.update', $report->id)); ?>" method="POST">
                                                    <?php echo csrf_field(); ?>
                                                    <div class="row g-3">
                                                        <div class="col-md-6">
                                                            <label class="small fw-bold mb-1">Update Case Status</label>
                                                            <select name="status" class="form-select shadow-sm">
                                                                <option value="Pending" <?php echo e($report->status == 'Pending' ? 'selected' : ''); ?>>Pending</option>
                                                                <option value="Under Review" <?php echo e($report->status == 'Under Review' ? 'selected' : ''); ?>>Under Review</option>
                                                                <option value="Resolved" <?php echo e($report->status == 'Resolved' ? 'selected' : ''); ?>>Resolved</option>
                                                            </select>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <label class="small fw-bold mb-1">Priority Level</label>
                                                            <select name="priority" class="form-select shadow-sm">
                                                                <option value="Low" <?php echo e($report->priority == 'Low' ? 'selected' : ''); ?>>Low Priority</option>
                                                                <option value="Medium" <?php echo e($report->priority == 'Medium' ? 'selected' : ''); ?>>Medium Priority</option>
                                                                <option value="High" <?php echo e($report->priority == 'High' ? 'selected' : ''); ?>>High Priority / Urgent</option>
                                                            </select>
                                                        </div>
                                                        <div class="col-12 mt-4">
                                                            <button type="submit" class="btn btn-primary w-100 py-2 rounded-pill shadow" style="background: #A594F9; border:none;">
                                                                <i class="bi bi-check-circle me-1"></i> Confirm Update
                                                            </button>
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="7" class="text-center py-5">
                                        <div class="text-muted">
                                            <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                            <p class="mb-0">No confidential reports found.</p>
                                        </div>
                                    </td>
                                </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    
</div>

<script>
    // Live Search Functionality
    document.getElementById('reportSearch').addEventListener('keyup', function() {
        let filter = this.value.toUpperCase();
        let rows = document.querySelector("#reportsTable tbody").rows;
        
        for (let i = 0; i < rows.length; i++) {
            let idCol = rows[i].cells[1].textContent.toUpperCase();
            let aliasCol = rows[i].cells[2].textContent.toUpperCase();
            if (idCol.indexOf(filter) > -1 || aliasCol.indexOf(filter) > -1) {
                rows[i].style.display = "";
            } else {
                rows[i].style.display = "none";
            }      
        }
    });

    // Mark report as seen when admin opens the details modal.
    document.querySelectorAll('[id^="modal-"]').forEach((modalEl) => {
        modalEl.addEventListener('shown.bs.modal', function () {
            const reportId = this.id.replace('modal-', '');
            fetch(`<?php echo e(url('/admin/reports')); ?>/${reportId}/seen`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>',
                    'Accept': 'application/json',
                },
            }).then(() => {
                const seenBadge = document.getElementById(`seen-badge-${reportId}`);
                const newBadge = document.getElementById(`new-badge-${reportId}`);

                if (seenBadge) {
                    seenBadge.className = 'badge rounded-pill sinag-violet-badge';
                    seenBadge.textContent = 'READ';
                }

                if (newBadge) {
                    newBadge.remove();
                }
            }).catch(() => {
                // Fail silently: UI will update on next full page load.
            });
        });
    });
</script>

<style>
    .table tbody tr:hover {
        background-color: #fbfaff !important;
        transition: 0.2s;
    }
    .badge { font-weight: 600; font-size: 0.75rem; }
    .pulse-animation { animation: pulse-red 2s infinite; }
    @keyframes pulse-red {
        0% { opacity: 1; transform: scale(1); }
        50% { opacity: 0.7; transform: scale(0.95); }
        100% { opacity: 1; transform: scale(1); }
    }
    .border-dashed { border-style: dashed !important; border-width: 2px !important; }
</style>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\sinag\resources\views/admin/reports.blade.php ENDPATH**/ ?>