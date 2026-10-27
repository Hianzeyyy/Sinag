<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>SINAG GAD Office Report</title>
    <style>
        @page { margin: 28px 32px; }
        body { font-family: DejaVu Sans, sans-serif; color: #1e1b4b; font-size: 9px; line-height: 1.4; }
        h1 { margin: 0 0 4px; color: #5b21b6; font-size: 17px; }
        h2 { margin: 18px 0 7px; padding-bottom: 4px; border-bottom: 1px solid #c4b5fd; color: #4c1d95; font-size: 12px; }
        .subtitle, .muted { color: #6b7280; }
        .header { padding-bottom: 10px; border-bottom: 2px solid #8b5cf6; }
        .meta, .summary, .report-table { width: 100%; border-collapse: collapse; }
        .meta { margin: 12px 0; }
        .meta td { padding: 3px 5px 3px 0; }
        .summary { margin: 10px 0 16px; }
        .summary td { width: 33.33%; padding: 9px; border: 1px solid #ddd6fe; background: #f5f3ff; text-align: center; }
        .number { color: #6d28d9; font-size: 18px; font-weight: bold; }
        .label { margin-top: 2px; color: #4b5563; font-size: 8px; font-weight: bold; text-transform: uppercase; }
        .report-table { margin-bottom: 14px; }
        .report-table th { padding: 5px; border: 1px solid #ddd6fe; background: #ede9fe; color: #4c1d95; text-align: left; font-size: 8px; }
        .report-table td { padding: 4px 5px; border: 1px solid #e5e7eb; vertical-align: top; overflow-wrap: break-word; }
        .profile-name { margin: 10px 0 4px; color: #4c1d95; font-size: 10px; }
        .profile-table { width: 100%; margin-bottom: 12px; border-collapse: collapse; }
        .profile-table th, .profile-table td { padding: 4px 5px; border: 1px solid #e5e7eb; vertical-align: top; text-align: left; white-space: pre-line; overflow-wrap: break-word; }
        .profile-table th { width: 24%; background: #f5f3ff; color: #4c1d95; }
        .footer { margin-top: 18px; padding-top: 6px; border-top: 1px solid #e5e7eb; color: #9ca3af; text-align: center; font-size: 8px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>SINAG - Gender and Development (GAD) Office</h1>
        <div class="subtitle">Comprehensive Analytics &amp; Operations Summary Report</div>
    </div>

    <table class="meta">
        <tr>
            <td><strong>Coverage Period:</strong> <?php echo e($data['period_label']); ?> (<?php echo e($data['start_date']); ?> – <?php echo e($data['end_date']); ?>)</td>
            <td><strong>Generated On:</strong> <?php echo e($data['generated_at']); ?></td>
        </tr>
    </table>

    <table class="summary">
        <tr>
            <td><div class="number"><?php echo e($data['total_users']); ?></div><div class="label">New Registered Users</div></td>
            <td><div class="number"><?php echo e($data['total_reports']); ?></div><div class="label">Incidents Reported</div></td>
            <td><div class="number"><?php echo e($data['total_suggestions']); ?></div><div class="label">Suggestions Submitted</div></td>
        </tr>
    </table>

    <h2>1. Registered Users (<?php echo e($users->count()); ?>)</h2>
    <table class="report-table">
        <thead>
            <tr><th>ID</th><th>Name</th><th>Email</th><th>Account Type</th><th>Department</th><th>Status</th><th>Registered</th></tr>
        </thead>
        <tbody>
            <?php $__empty_1 = true; $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr>
                    <td><?php echo e($user->id); ?></td>
                    <td><?php echo e($user->name); ?></td>
                    <td><?php echo e($user->email); ?></td>
                    <td><?php echo e($user->account_type ?? $user->role ?? 'N/A'); ?></td>
                    <td><?php echo e($user->department ?? 'N/A'); ?></td>
                    <td><?php echo e(ucfirst($user->account_status ?? 'Active')); ?></td>
                    <td><?php echo e(optional($user->created_at)->format('M d, Y')); ?></td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr><td colspan="7" class="muted">No users registered during this period.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>

    <h2>2. Incident Reports (<?php echo e($reports->count()); ?>)</h2>
    <table class="report-table">
        <thead>
            <tr><th>Incident ID</th><th>Reporter Alias</th><th>Nature</th><th>Location</th><th>Priority</th><th>Status</th><th>Filed Date</th></tr>
        </thead>
        <tbody>
            <?php $__empty_1 = true; $__currentLoopData = $reports; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $report): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr>
                    <td><?php echo e($report->incident_id); ?></td>
                    <td><?php echo e($report->cloak_alias ?? 'Anonymous'); ?></td>
                    <td><?php echo e($report->nature); ?></td>
                    <td><?php echo e($report->location ?? 'N/A'); ?></td>
                    <td><?php echo e(ucfirst($report->priority ?? 'Normal')); ?></td>
                    <td><?php echo e(ucfirst($report->status ?? 'Pending')); ?></td>
                    <td><?php echo e(optional($report->created_at)->format('M d, Y')); ?></td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr><td colspan="7" class="muted">No incident reports submitted during this period.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>

    <h2>3. Suggestions &amp; Community Feedback (<?php echo e($suggestions->count()); ?>)</h2>
    <table class="report-table">
        <thead>
            <tr><th>ID</th><th>Submitted By</th><th>Category</th><th>Message</th><th>Upvotes</th><th>Date</th></tr>
        </thead>
        <tbody>
            <?php $__empty_1 = true; $__currentLoopData = $suggestions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $suggestion): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr>
                    <td>#<?php echo e($suggestion->id); ?></td>
                    <td><?php echo e($suggestion->is_anonymous ? 'Anonymous' : (optional($suggestion->user)->name ?? 'User')); ?></td>
                    <td><?php echo e($suggestion->category ?? 'General'); ?></td>
                    <td><?php echo e(\Illuminate\Support\Str::limit($suggestion->message, 120)); ?></td>
                    <td><?php echo e($suggestion->upvotes_count ?? 0); ?></td>
                    <td><?php echo e(optional($suggestion->created_at)->format('M d, Y')); ?></td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr><td colspan="6" class="muted">No suggestions submitted during this period.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>

    <h2>4. Student Information Sheets (<?php echo e(count($studentProfiles)); ?>)</h2>
    <?php $__empty_1 = true; $__currentLoopData = $studentProfiles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $profile): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <h3 class="profile-name"><?php echo e($profile['user']->name); ?> (<?php echo e($profile['user']->email); ?>)</h3>
        <table class="profile-table">
            <?php $__currentLoopData = $profile['basic']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $label => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr><th><?php echo e($label); ?></th><td><?php echo e($value); ?></td></tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <?php $__currentLoopData = $profile['sections']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $section => $details): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr><th><?php echo e($section); ?></th><td><?php echo e($details); ?></td></tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </table>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
        <p class="muted">No student information sheets were submitted.</p>
    <?php endif; ?>

    <h2>5. Faculty and Non-Teaching Information Sheets (<?php echo e(count($employeeProfiles)); ?>)</h2>
    <?php $__empty_1 = true; $__currentLoopData = $employeeProfiles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $profile): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <h3 class="profile-name"><?php echo e($profile['user']->name); ?> (<?php echo e($profile['user']->email); ?>)</h3>
        <table class="profile-table">
            <?php $__currentLoopData = $profile['basic']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $label => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr><th><?php echo e($label); ?></th><td><?php echo e($value); ?></td></tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <?php $__currentLoopData = $profile['sections']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $section => $details): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr><th><?php echo e($section); ?></th><td><?php echo e($details); ?></td></tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </table>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
        <p class="muted">No faculty or non-teaching information sheets were submitted.</p>
    <?php endif; ?>

    <div class="footer">SINAG Safe Space &amp; Participatory Suggestion System • Confidential Administrative Report • <?php echo e($data['generated_at']); ?></div>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\sinag\resources\views/admin/gad-report-export-pdf.blade.php ENDPATH**/ ?>