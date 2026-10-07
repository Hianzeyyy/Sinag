

<?php $__env->startSection('content'); ?>
<style>
    .sinag-violet-badge { background: #d8cdfc; color: #5b4b9b; border-color: #c4b5fd !important; }
    .sinag-yellow-badge { background: #f8cb12; color: #4a3500; border-color: #e5b900 !important; }
    #viewUserModal .modal-content { transform: translateY(24px) scale(.97); opacity: 0; transition: transform .28s ease, opacity .28s ease; }
    #viewUserModal.show .modal-content { transform: translateY(0) scale(1); opacity: 1; }
    #viewUserModal .view-modal-body > * { opacity: 0; transform: translateY(10px); transition: opacity .28s ease .12s, transform .28s ease .12s; }
    #viewUserModal.show .view-modal-body > * { opacity: 1; transform: translateY(0); }
    @media (prefers-reduced-motion: reduce) {
        #viewUserModal .modal-content, #viewUserModal .view-modal-body > * { transition: none; transform: none; }
    }
    @media (max-width: 767.98px) {
        .user-management-table th:nth-child(1), .user-management-table td:nth-child(1),
        .user-management-table th:nth-child(3), .user-management-table td:nth-child(3),
        .user-management-table th:nth-child(4), .user-management-table td:nth-child(4),
        .user-management-table th:nth-child(5), .user-management-table td:nth-child(5) { display: none; }
        .user-management-table th:last-child, .user-management-table td:last-child { width: 72px; }
        .user-management-table td:nth-child(2) { max-width: calc(100vw - 150px); }
        .user-management-table td:nth-child(2) .fw-bold { display: block; max-width: 145px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
        .user-management-table td:nth-child(2) .small { display: block; max-width: 145px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
        .user-management-table .view-user-btn { padding-inline: .6rem !important; font-size: .75rem; }
        .user-management-table .view-user-btn i { margin-right: 0 !important; }
        .user-management-table .view-user-btn { font-size: 0; }
        .user-management-table .view-user-btn i { font-size: .85rem; }
        .user-management-table .view-user-btn::after { content: 'View'; font-size: .75rem; margin-left: .25rem; }
        .users-filter-row { row-gap: .45rem !important; }
    }
</style>

<div class="container py-4">
    <?php
        $pendingCount = (int) ($userCounts->pending ?? 0);
        $activeCount = (int) ($userCounts->active ?? 0);
        $rejectedCount = (int) ($userCounts->rejected ?? 0);
        $departmentLabels = [
            'Bachelor Secondary Education' => 'Bachelor of Secondary Education',
            'Bachelor Secondary Education (BSEd)' => 'Bachelor of Secondary Education',
            'Bachelor Secondary Education (BSEd) - Major in English' => 'Bachelor of Secondary Education - Major in English',
            'Bachelor Secondary Education (BSEd) - Major in Math' => 'Bachelor of Secondary Education - Major in Math',
            'Bachelor Secondary Education (BSEd) - Major in Science' => 'Bachelor of Secondary Education - Major in Science',
            'BSEd' => 'Bachelor of Secondary Education',
            'Bachelor of Elementary Education' => 'Bachelor of Elementary Education',
            'Bachelor of Elementary Education (BEEd)' => 'Bachelor of Elementary Education',
            'BEEd' => 'Bachelor of Elementary Education',
            'Bachelor of Technology and Livelihood Education' => 'Bachelor of Technology and Livelihood Education',
            'Bachelor of Technology and Livelihood Education (BTLEd)' => 'Bachelor of Technology and Livelihood Education',
            'BTLEd' => 'Bachelor of Technology and Livelihood Education',
            'Bachelor Industrial Technology' => 'Bachelor in Industrial Technology',
            'Bachelor in Industrial Technology' => 'Bachelor in Industrial Technology',
            'Bachelor Industrial Technology - Major in Electrical Technology' => 'Bachelor in Industrial Technology - Major in Electrical Technology',
            'Bachelor Industrial Technology - Major in Food Management Service' => 'Bachelor in Industrial Technology - Major in Food Management Service',
            'Bachelor Industrial Technology - Major in Mechanical Technology' => 'Bachelor in Industrial Technology - Major in Mechanical Technology',
            'BIT' => 'Bachelor in Industrial Technology',
            'BS Information Technology' => 'Bachelor of Science in Information Technology',
            'BS Information Technology (BSIT)' => 'Bachelor of Science in Information Technology',
            'BSIT' => 'Bachelor of Science in Information Technology',
            'Bsa' => 'Bachelor of Science in Accountancy',
            'BSA' => 'Bachelor of Science in Accountancy',
            'Bsit' => 'Bachelor of Science in Information Technology',
            'BS Business Administration' => 'Bachelor of Science in Business Administration',
            'BS Business Administration (BSBA)' => 'Bachelor of Science in Business Administration',
            'BSBA' => 'Bachelor of Science in Business Administration',
        ];
    ?>

    <form method="GET" action="<?php echo e(route('admin.users')); ?>" id="userFiltersForm">
        <div class="row mb-4 align-items-center">
            <div class="col-md-6">
                <h3 class="fw-bold mb-1" style="color: #A594F9;">User Management</h3>
                <p class="text-muted small mb-0">Manage registered student and employee accounts.</p>
            </div>
            <div class="col-md-6">
                <div class="d-flex gap-2 justify-content-md-end mt-3 mt-md-0">
                    <div class="input-group input-group-sm w-75 shadow-sm">
                        <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span>
                        <input type="search" id="userSearch" name="q" value="<?php echo e(request('q')); ?>" class="form-control border-start-0" placeholder="Search name, email, phone...">
                    </div>
                    <button type="submit" class="btn btn-sm btn-primary px-3"><i class="bi bi-search me-1"></i>Search</button>
                </div>
            </div>
        </div>

        <div class="d-flex flex-wrap gap-2 mb-3" aria-label="Account status filters">
            <button type="submit" name="status" value="active" class="btn btn-sm sinag-violet-badge rounded-pill px-3 status-filter-btn">Active: <?php echo e($activeCount); ?></button>
            <button type="submit" name="status" value="pending" class="btn btn-sm sinag-yellow-badge rounded-pill px-3 status-filter-btn">Pending: <?php echo e($pendingCount); ?></button>
            <button type="submit" name="status" value="rejected" class="btn btn-sm bg-secondary-subtle text-secondary rounded-pill px-3 status-filter-btn">Rejected: <?php echo e($rejectedCount); ?></button>
        </div>

        <div class="row g-2 mb-4 users-filter-row">
            <div class="col-md-3">
                <select id="accountTypeFilter" name="account_type" class="form-select form-select-sm">
                    <option value="">All Account Types</option>
                    <option value="student" <?php echo e(request('account_type') === 'student' ? 'selected' : ''); ?>>Students</option>
                    <option value="teaching" <?php echo e(request('account_type') === 'teaching' ? 'selected' : ''); ?>>Teaching</option>
                    <option value="non_teaching" <?php echo e(request('account_type') === 'non_teaching' ? 'selected' : ''); ?>>Non-teaching</option>
                </select>
            </div>
            <div class="col-md-3">
                <select id="genderFilter" name="gender" class="form-select form-select-sm">
                    <option value="">All Genders</option>
                    <option value="Male" <?php echo e(request('gender') === 'Male' ? 'selected' : ''); ?>>Male</option>
                    <option value="Female" <?php echo e(request('gender') === 'Female' ? 'selected' : ''); ?>>Female</option>
                    <option value="Prefer not to say" <?php echo e(request('gender') === 'Prefer not to say' ? 'selected' : ''); ?>>Prefer not to say</option>
                </select>
            </div>
            <div class="col-md-3">
                <select id="departmentFilter" name="department" class="form-select form-select-sm">
                    <option value="">All Departments</option>
                    <?php $__currentLoopData = $departments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $departmentValue => $departmentLabel): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($departmentValue); ?>" <?php echo e(request('department') === $departmentValue ? 'selected' : ''); ?>><?php echo e($departmentLabel); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
        </div>
    </form>

    <?php if(session('success')): ?>
        <div class="alert alert-success"><?php echo e(session('success')); ?></div>
    <?php endif; ?>
    <?php if(session('error')): ?>
        <div class="alert alert-danger"><?php echo e(session('error')); ?></div>
    <?php endif; ?>

    <div class="card border-0 shadow-lg rounded-4 overflow-hidden">
        <div style="height: 5px; background: linear-gradient(90deg, #A594F9, #c4b5fd);"></div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 user-management-table" id="usersTable">
                <thead class="bg-light text-muted small">
                    <tr>
                        <th class="ps-4 py-3">DATE JOINED</th>
                        <th>USER NAME</th>
                        <th>EMAIL</th>
                        <th>ACCOUNT TYPE</th>
                        <th>STATUS</th>
                        <th class="text-end pe-4">ACTION</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <?php
                            $accountStatus = $user->account_status ?? 'pending';
                            $accountType = $user->role === 'admin' ? 'admin' : ($user->account_type ?? 'student');
                            $displayType = $user->role === 'admin' ? 'Administrator' : ($accountType === 'employee' ? ucfirst(str_replace('_', ' ', $user->employee_category ?? 'employee')) : 'Student');
                            $fullDepartment = $departmentLabels[$user->department] ?? ($user->department ?: 'Department not provided');
                            $isOnline = $user->last_seen_at && $user->last_seen_at->greaterThan(now()->subMinutes(5));
                            $presenceStatus = $isOnline ? (($user->availability_status ?? 'active') === 'do_not_disturb' ? 'busy' : ($user->availability_status ?: 'active')) : 'offline';
                        ?>
                        <tr>
                            <td class="ps-4 small text-muted"><?php echo e(optional($user->created_at)->format('M d, Y')); ?></td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="rounded-circle d-flex align-items-center justify-content-center me-3 shadow-sm" style="width:40px;height:40px;background:<?php echo e($user->role === 'admin' ? '#A594F9' : '#f3e8ff'); ?>;color:<?php echo e($user->role === 'admin' ? '#fff' : '#A594F9'); ?>;font-weight:bold;">
                                        <?php echo e(strtoupper(substr($user->name, 0, 1))); ?>

                                    </div>
                                    <div><span class="d-block fw-bold text-dark"><?php echo e($user->name); ?></span><span class="d-block small text-muted"><?php echo e($fullDepartment); ?></span></div>
                                </div>
                            </td>
                            <td><span class="small text-dark"><?php echo e($user->email); ?></span></td>
                            <td><span class="badge bg-light text-dark border rounded-pill px-3 py-2"><?php echo e($displayType); ?></span></td>
                            <td>
                                <?php if(in_array($accountStatus, ['active', 'approved'], true)): ?><span class="badge sinag-violet-badge rounded-pill px-3 py-2">ACTIVE</span>
                                <?php elseif($accountStatus === 'pending'): ?><span class="badge sinag-yellow-badge rounded-pill px-3 py-2">PENDING</span>
                                <?php else: ?><span class="badge bg-secondary-subtle text-secondary rounded-pill px-3 py-2">REJECTED</span><?php endif; ?>
                            </td>
                            <td class="text-end pe-4">
                                <button type="button" class="btn btn-primary btn-sm rounded-pill px-3 shadow-sm view-user-btn" style="background:#A594F9;border:none;" data-bs-toggle="modal" data-bs-target="#viewUserModal"
                                    data-user-name="<?php echo e($user->name); ?>" data-user-email="<?php echo e($user->email); ?>" data-user-id="UID-<?php echo e(str_pad($user->id, 5, '0', STR_PAD_LEFT)); ?>" data-user-type="<?php echo e($displayType); ?>" data-user-department="<?php echo e($fullDepartment); ?>" data-user-status="<?php echo e(ucfirst($accountStatus)); ?>" data-user-phone="<?php echo e($user->phone_number ?: 'Not provided'); ?>" data-user-gender="<?php echo e($user->gender ?: 'Not provided'); ?>" data-user-age="<?php echo e($user->age ?: 'Not provided'); ?>" data-user-joined="<?php echo e(optional($user->created_at)->format('F d, Y h:i A')); ?>" data-user-photo="<?php echo e($user->profile_photo_path ? asset('storage/' . $user->profile_photo_path) : ''); ?>" data-user-id-picture="<?php echo e(($user->id_image_path ?: $user->selfie_image_path) ? asset('storage/' . ($user->id_image_path ?: $user->selfie_image_path)) : ''); ?>" data-personal-data-url="<?php echo e(route('admin.users.personal-data', $user->id)); ?>" data-approve-url="<?php echo e($user->role !== 'admin' && $accountStatus === 'pending' ? route('admin.users.approve', $user->id) : ''); ?>" data-reject-url="<?php echo e($user->role !== 'admin' && $accountStatus === 'pending' ? route('admin.users.reject', $user->id) : ''); ?>">
                                    <i class="bi bi-eye me-1"></i>View
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr><td colspan="6" class="text-center py-5 text-muted">No users found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <div class="p-3"><?php echo e($users->onEachSide(1)->links()); ?></div>
    </div>
</div>

<div class="modal fade" id="viewUserModal" tabindex="-1" aria-labelledby="viewUserModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 rounded-4 shadow-lg">
            <div class="modal-header border-0 bg-light"><div><div class="small text-uppercase text-muted fw-bold">Account Profile</div><h2 class="modal-title h5 fw-bold mb-0" id="viewUserModalLabel">User details</h2></div><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close user details"></button></div>
            <div class="modal-body p-4 view-modal-body">
                <div class="d-flex align-items-center gap-3 mb-4 pb-4 border-bottom"><div id="viewUserAvatar" class="rounded-3 d-flex align-items-center justify-content-center fw-bold" style="width:88px;height:88px;background:#f3e8ff;color:#8f72f5;font-size:2rem;">U</div><div><h3 class="h4 mb-1" id="viewUserName">User</h3><p class="mb-1 text-muted" id="viewUserType"></p><p class="mb-0 small text-muted" id="viewUserId"></p></div></div>
                <div class="row g-3">
                    <div class="col-sm-6"><div class="bg-light rounded-3 p-3"><div class="small text-muted fw-bold text-uppercase mb-1">Email</div><div class="fw-semibold small" id="viewUserEmail"></div></div></div>
                    <div class="col-sm-6"><div class="bg-light rounded-3 p-3"><div class="small text-muted fw-bold text-uppercase mb-1">Account status</div><div class="fw-semibold" id="viewUserStatus"></div></div></div>
                    <div class="col-sm-6"><div class="bg-light rounded-3 p-3"><div class="small text-muted fw-bold text-uppercase mb-1">Course / Department</div><div class="fw-semibold small" id="viewUserDepartment"></div></div></div>
                    <div class="col-sm-6"><div class="bg-light rounded-3 p-3"><div class="small text-muted fw-bold text-uppercase mb-1">Phone</div><div class="fw-semibold small" id="viewUserPhone"></div></div></div>
                    <div class="col-sm-6"><div class="bg-light rounded-3 p-3"><div class="small text-muted fw-bold text-uppercase mb-1">Gender</div><div class="fw-semibold small" id="viewUserGender"></div></div></div>
                    <div class="col-sm-6"><div class="bg-light rounded-3 p-3"><div class="small text-muted fw-bold text-uppercase mb-1">Age</div><div class="fw-semibold small" id="viewUserAge"></div></div></div>
                </div>
                <div id="viewUserIdPictureWrap" class="mt-4 d-none"><div class="small text-muted fw-bold text-uppercase mb-2">ID Picture</div><img id="viewUserIdPicture" src="" alt="Selected user's ID picture" class="img-fluid rounded-3 border" style="max-height:320px;object-fit:contain;"></div>
                <div class="text-muted small mt-4"><i class="bi bi-calendar-event me-1"></i>Registered <span id="viewUserJoined"></span></div>
            </div>
            <div class="modal-footer border-0 bg-light justify-content-between"><div class="d-flex gap-2"><a id="viewPersonalDataLink" href="#" class="btn btn-outline-secondary"><i class="bi bi-person-vcard me-1"></i>Full personal information</a><form id="viewApproveForm" method="POST" class="d-none"><?php echo csrf_field(); ?><button type="submit" class="btn" style="background:#8f72f5;color:#fff;"><i class="bi bi-check-circle me-1"></i>Approve</button></form><form id="viewRejectForm" method="POST" class="d-none"><?php echo csrf_field(); ?><input type="hidden" name="rejection_reason" value="Rejected by administrator"><button type="submit" class="btn btn-warning"><i class="bi bi-x-circle me-1"></i>Reject</button></form></div><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button></div>
        </div>
    </div>
</div>

<script>
    const filterForm = document.getElementById('userFiltersForm');
    const accountTypeSelect = document.getElementById('accountTypeFilter');
    const departmentSelect = document.getElementById('departmentFilter');
        accountTypeSelect?.addEventListener('change', function () {
            departmentSelect.value = '';
            filterForm.submit();
        });
    departmentSelect?.addEventListener('change', function () { filterForm.submit(); });
    document.getElementById('genderFilter')?.addEventListener('change', function () { filterForm.submit(); });

    document.querySelectorAll('.view-user-btn').forEach(function (button) {
        button.addEventListener('click', function () {
            const data = this.dataset;
            ['Name', 'Type', 'Id', 'Email', 'Status', 'Department', 'Gender', 'Age', 'Joined'].forEach(function (field) {
                const target = document.getElementById('viewUser' + field);
                if (target) target.textContent = data['user' + field];
            });
            const phoneTarget = document.getElementById('viewUserPhone');
            if (phoneTarget) {
                if (data.userPhone && data.userPhone !== 'Not provided' && data.userPhone !== 'N/A') {
                    phoneTarget.innerHTML = `<a href="tel:${data.userPhone}" class="text-decoration-none text-primary"><i class="bi bi-telephone-outbound me-1"></i>${data.userPhone}</a>`;
                } else {
                    phoneTarget.textContent = 'Not provided';
                }
            }
            document.getElementById('viewUserModalLabel').textContent = data.userName;
            const avatar = document.getElementById('viewUserAvatar');
            avatar.textContent = data.userName.charAt(0).toUpperCase();
            avatar.style.backgroundImage = data.userPhoto ? `url("${data.userPhoto}")` : '';
            avatar.style.backgroundSize = 'cover';
            avatar.style.backgroundPosition = 'center';
            avatar.style.color = data.userPhoto ? 'transparent' : '#8f72f5';
            const idPictureWrap = document.getElementById('viewUserIdPictureWrap');
            const idPicture = document.getElementById('viewUserIdPicture');
            idPictureWrap.classList.toggle('d-none', !data.userIdPicture);
            idPicture.src = data.userIdPicture || '';
            const approveForm = document.getElementById('viewApproveForm');
            const rejectForm = document.getElementById('viewRejectForm');
            document.getElementById('viewPersonalDataLink').href = data.personalDataUrl;
            approveForm.classList.toggle('d-none', !data.approveUrl);
            rejectForm.classList.toggle('d-none', !data.rejectUrl);
            approveForm.action = data.approveUrl || '#';
            rejectForm.action = data.rejectUrl || '#';
        });
    });
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\sinag\resources\views/admin/users-list.blade.php ENDPATH**/ ?>