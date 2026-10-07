@extends('layouts.app')

@section('content')
<div class="container py-4">
    @php
        $pendingCount = (int) ($userCounts->pending ?? 0);
        $activeCount = (int) ($userCounts->active ?? 0);
        $rejectedCount = (int) ($userCounts->rejected ?? 0);
    @endphp

    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            
            
        </ol>
    </nav>

    <form method="GET" action="{{ route('admin.users') }}" id="userFiltersForm">
    <div class="row mb-4 align-items-center">
        <div class="col-md-6">
            <h3 class="fw-bold mb-1" style="color: #A594F9;">
    User Management
</h3>
            <p class="text-muted small mb-0">Gender and Development Safe Space and Participatory Suggestion System</p>
        </div>
        <div class="col-md-6">
            <div class="d-flex gap-2 justify-content-md-end mt-3 mt-md-0">
                <div class="input-group input-group-sm w-75 shadow-sm">
                    <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span>
                    <input type="search" id="userSearch" name="q" value="{{ request('q') }}" class="form-control border-start-0" placeholder="Search name, email, phone, department, age...">
                </div>
            </div>
        </div>
    </div>

    @if($pendingCount > 0)
        <div class="alert border-0 rounded-4 shadow-sm mb-3" style="background:#fffbeb; color:#92400e; border-left:5px solid #f59e0b;">
            <i class="bi bi-bell-fill me-1"></i>
            You have <strong>{{ $pendingCount }}</strong> pending registration{{ $pendingCount > 1 ? 's' : '' }} waiting for review.
        </div>
    @endif

    <div class="d-flex flex-wrap gap-2 mb-3">
        <button class="btn btn-sm rounded-pill px-3 user-status-filter active" data-status="all">All <span class="badge bg-light text-dark ms-1">{{ $userCounts->total ?? 0 }}</span></button>
        <button class="btn btn-sm rounded-pill px-3 user-status-filter" data-status="pending">Pending <span class="badge sinag-yellow-badge ms-1">{{ $pendingCount }}</span></button>
        <button class="btn btn-sm rounded-pill px-3 user-status-filter" data-status="active">Active <span class="badge sinag-violet-badge ms-1">{{ $activeCount }}</span></button>
        <button class="btn btn-sm rounded-pill px-3 user-status-filter" data-status="rejected">Rejected <span class="badge sinag-yellow-badge ms-1">{{ $rejectedCount }}</span></button>
    </div>
    <div class="d-flex justify-content-center mt-4">
        {{ $users->onEachSide(1)->links() }}
    </div>

    <div class="row g-2 mb-4">
        <div class="col-md-2">
            <select id="accountTypeFilter" name="account_type" class="form-select form-select-sm">
                <option value="">All Account Types</option>
                <option value="student" {{ request('account_type') === 'student' ? 'selected' : '' }}>Students</option>
                <option value="teaching" {{ request('account_type') === 'teaching' ? 'selected' : '' }}>Teaching</option>
                <option value="non_teaching" {{ request('account_type') === 'non_teaching' ? 'selected' : '' }}>Non-teaching</option>
            </select>
        </div>
        <div class="col-md-2">
            <select id="genderFilter" name="gender" class="form-select form-select-sm">
                <option value="">All Genders</option>
                <option value="Male" {{ request('gender') === 'Male' ? 'selected' : '' }}>Male</option>
                <option value="Female" {{ request('gender') === 'Female' ? 'selected' : '' }}>Female</option>
                <option value="Prefer not to say" {{ request('gender') === 'Prefer not to say' ? 'selected' : '' }}>Prefer not to say</option>
            </select>
        </div>
        <div class="col-md-3">
            <select id="departmentFilter" name="department" class="form-select form-select-sm">
                <option value="">All Departments</option>
                <option value="Bachelor of Science in Accountancy">BSA</option>
                <option value="Bachelor Industrial Technology - Major in Electrical Technology">Electrical Technology</option>
                <option value="Bachelor Industrial Technology - Major in Food Management Service">Food Management</option>
                <option value="Bachelor Industrial Technology - Major in Mechanical Technology">Mechanical Technology</option>
                <option value="Bachelor of Elementary Education">BEEd</option>
                <option value="Bachelor of Technology and Livelihood Education">BTLEd</option>
                <option value="Bachelor Secondary Education">BSEd</option>
                <option value="BS Business Administration">BSBA</option>
                <option value="BS Information Technology" {{ request('department') === 'BS Information Technology' ? 'selected' : '' }}>BSIT</option>
            </select>
        </div>
    </div>
    </form>

    @if(session('success'))
        <div class="alert border-0 rounded-4 shadow-sm mb-3" style="background:#f3efff; color:#5b4b9b; border-left:5px solid #8f72f5;">
            <i class="bi bi-check-circle-fill me-1"></i> {{ session('success') }}
        </div>
    @endif

    <div class="card border-0 shadow-lg rounded-4 overflow-hidden">
        <div style="height: 5px; background: linear-gradient(90deg, #A594F9, #c4b5fd);"></div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" id="userTable">
                    <thead class="bg-light">
                        <tr class="small text-muted text-uppercase">
                            <th class="ps-4 py-3">User Profile</th>
                            <th>Role & Status</th>
                            <th>Contact</th>
                            <th>Details</th>
                            <th>Date Joined</th>
                            <th class="text-end pe-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $user)
                        @php
                            $accountStatus = $user->account_status ?? 'pending';
                            $statusFilter = in_array($accountStatus, ['active', 'approved'], true) ? 'active' : $accountStatus;
                            $accountType = $user->role === 'employee' ? 'employee' : ($user->account_type ?? 'student');
                            $availability = $user->availability_status ?? 'active';
                        @endphp
                        <tr class="user-tile" tabindex="0" data-profile-modal="userProfileModal{{ $user->id }}" data-status="{{ $statusFilter }}" data-account-type="{{ $accountType === 'employee' ? ($user->employee_category ?? 'employee') : 'student' }}" data-gender="{{ $user->gender }}" data-age="{{ $user->age }}" data-department="{{ $user->department }}" data-search="{{ strtolower(implode(' ', array_filter([$user->name, $user->email, $user->phone_number, $user->department, $user->employee_category, $user->gender, $user->age, $accountType]))) }}">
                            <td class="ps-4">
                                <div class="d-flex align-items-center">
                                    <div class="rounded-circle d-flex align-items-center justify-content-center me-3 shadow-sm border border-2 border-white"
                                         style="width: 45px; height: 45px; background: {{ $user->role == 'admin' ? '#A594F9' : '#f3e8ff' }}; color: {{ $user->role == 'admin' ? 'white' : '#A594F9' }}; font-weight: bold; font-size: 1.1rem;">
                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <span class="d-block fw-bold text-dark mb-0">{{ $user->name }}</span>
                                        <span class="d-block small text-muted mt-1">{{ $user->department ?: 'Department not provided' }}</span>
                                    </div>
                                </div>
                            </td>

                            <td>
                                @if($user->role == 'admin')
                                    <span class="badge bg-primary rounded-pill px-3 py-2 shadow-sm">
                                        <i class="bi bi-shield-check me-1"></i> GAD Administrator
                                    </span>
                                @else
                                    <span class="badge bg-light text-dark border rounded-pill px-3 py-2">
                                        <i class="bi bi-person me-1"></i> {{ ucfirst($accountType) }} Account
                                    </span>
                                @endif

                                <div class="mt-2">
                                    @if(in_array($accountStatus, ['active', 'approved'], true))
                                        <span class="badge sinag-violet-badge rounded-pill px-3 py-2">ACTIVE</span>
                                    @elseif(($user->account_status ?? 'pending') === 'pending')
                                        <span class="badge bg-warning-subtle text-warning border border-warning-subtle rounded-pill px-3 py-2">PENDING</span>
                                    @else
                                        <span class="badge sinag-yellow-badge rounded-pill px-3 py-2">REJECTED</span>
                                    @endif
                                </div>
                                <div class="small text-muted mt-1">{{ $availability === 'active' ? 'Online' : 'Offline' }}</div>
                            </td>

                            <td>
                                <div class="small">
                                    <div class="text-dark fw-medium"><i class="bi bi-envelope-at me-1"></i>{{ $user->email }}</div>
                                    <div class="text-dark fw-medium mt-1"><i class="bi bi-phone me-1"></i><a href="tel:{{ $user->phone_number }}">{{ $user->phone_number ?? 'No phone submitted' }}</a></div>
                                </div>
                            </td>

                            <td>
                                <div class="small text-dark fw-medium">
                                    <div><i class="bi bi-calendar-event me-1"></i>Age: {{ $user->age ?? 'N/A' }}</div>
                                    <div class="mt-1"><i class="bi bi-gender-ambiguous me-1"></i>Gender: {{ $user->gender ?? 'N/A' }}</div>
                                    <div class="mt-1"><i class="bi bi-mortarboard me-1"></i>Dept: {{ $user->department ? Str::limit($user->department, 15) : 'N/A' }}</div>
                                    <div class="mt-1 text-muted"><i class="bi bi-person-vcard me-1"></i>Student ID Photo: {{ ($user->id_image_path ?: $user->selfie_image_path) ? 'Uploaded' : 'No file' }}</div>
                                </div>
                            </td>

                            <td class="small text-secondary">
                                <span class="d-block text-dark">{{ $user->created_at->format('M d, Y') }}</span>
                                <span class="text-muted" style="font-size: 0.7rem;">{{ $user->created_at->diffForHumans() }}</span>
                            </td>

                            <td class="text-end pe-4">
                                @if($user->role !== 'admin' && ($user->account_status ?? 'pending') === 'pending')
                                    <form action="{{ route('admin.users.approve', $user->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm rounded-pill px-3" style="background:#8f72f5;color:#fff;border:0;">Approve</button>
                                    </form>

                                    <form action="{{ route('admin.users.reject', $user->id) }}" method="POST" class="d-inline reject-user-form">
                                        @csrf
                                        <input type="hidden" name="rejection_reason" value="">
                                        <button type="submit" class="btn btn-sm rounded-pill px-3 ms-1" style="background:#facc15;color:#4a3500;border:0;">Reject</button>
                                    </form>
                                @else
                                    <span class="text-muted small">Open tile for profile</span>
                                @endif
                            </td>
                        </tr>
                        <div class="modal fade" id="userProfileModal{{ $user->id }}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered modal-lg">
                                <div class="modal-content user-resume-modal">
                                    <div class="modal-header border-0">
                                        <div><div class="small text-uppercase text-muted">Account Profile</div><h5 class="modal-title fw-bold">{{ $user->name }}</h5></div>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="resume-heading">
                                            @if($user->profile_photo_path)
                                                <img src="{{ asset('storage/' . $user->profile_photo_path) }}" alt="Profile photo" class="resume-avatar">
                                            @else
                                                <div class="resume-avatar resume-initial">{{ strtoupper(substr($user->name, 0, 1)) }}</div>
                                            @endif
                                            <div><h4 class="mb-1">{{ $user->name }}</h4><div class="text-muted">{{ ucfirst($accountType) }}{{ $user->employee_category ? ' - ' . ucfirst(str_replace('_', ' ', $user->employee_category)) : '' }}</div><div class="text-muted small">UID-{{ str_pad($user->id, 5, '0', STR_PAD_LEFT) }}</div></div>
                                        </div>
                                        <div class="resume-grid">
                                            <div><span>Email</span><strong>{{ $user->email }}</strong></div>
                                            <div><span>Department / Course</span><strong>{{ $user->department ?: 'N/A' }}</strong></div>
                                            <div><span>Age</span><strong>{{ $user->age ?: 'N/A' }}</strong></div>
                                            <div><span>Gender</span><strong>{{ $user->gender ?: 'N/A' }}</strong></div>
                                            <div><span>Phone</span><strong>@if($user->phone_number)<a href="tel:{{ $user->phone_number }}" class="text-decoration-none text-primary"><i class="bi bi-telephone-outbound me-1"></i>{{ $user->phone_number }}</a>@else N/A @endif</strong></div>
                                            <div><span>Status</span><strong>{{ ucfirst($accountStatus) }}</strong></div>
                                        </div>
                                        <div class="resume-id-section">
                                            <div class="small text-uppercase text-muted fw-bold mb-2">ID Verification</div>
                                            @php($verificationImage = $user->id_image_path ?: $user->selfie_image_path)
                                            @if($verificationImage)
                                                <img src="{{ asset('storage/' . $verificationImage) }}" alt="ID verification" class="resume-id-image">
                                            @else
                                                <div class="text-muted small">No ID image uploaded.</div>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="modal-footer justify-content-between border-0">
                                        <span class="small text-muted">Registered {{ optional($user->created_at)->format('M d, Y') }}</span>
                                        <a href="{{ route('admin.users.personal-data', $user->id) }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-person-vcard me-1"></i>Full personal information</a>
                                        @if($user->role !== 'admin' && $accountStatus === 'pending')
                                            <div class="d-flex gap-2">
                                                <form action="{{ route('admin.users.approve', $user->id) }}" method="POST">@csrf<button class="btn btn-sm" style="background:#8f72f5;color:#fff;">Approve</button></form>
                                                <form action="{{ route('admin.users.reject', $user->id) }}" method="POST" class="reject-user-form">@csrf<input type="hidden" name="rejection_reason" value=""><button class="btn btn-sm" style="background:#facc15;color:#4a3500;">Reject</button></form>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <div class="text-muted">
                                    <i class="bi bi-people fs-1 d-block mb-2"></i>
                                    <p class="mb-0">No registered users found.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<style>
    .sinag-violet-badge { background: #d8cdfc; color: #5b4b9b; }
    .sinag-yellow-badge { background: #f8cb12; color: #4a3500; }
    .user-tile { cursor: pointer; }
    .user-tile:focus-visible { outline: 3px solid #a594f9; outline-offset: 2px; }
    .user-resume-modal { border: 0; border-radius: 1rem; overflow: hidden; }
    .resume-heading { display: flex; align-items: center; gap: 1rem; padding-bottom: 1rem; border-bottom: 1px solid #ede9fe; }
    .resume-avatar { width: 84px; height: 84px; border-radius: 0.75rem; object-fit: cover; border: 1px solid #ddd6fe; }
    .resume-initial { display: flex; align-items: center; justify-content: center; background: #f3efff; color: #8f72f5; font-size: 2rem; font-weight: 800; }
    .resume-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 0.85rem; padding: 1rem 0; }
    .resume-grid > div { padding: 0.75rem; border: 1px solid #ede9fe; border-radius: 0.6rem; background: #faf9ff; }
    .resume-grid span { display: block; color: #6b7280; font-size: 0.7rem; text-transform: uppercase; letter-spacing: .06em; }
    .resume-grid strong { display: block; margin-top: .25rem; color: #111827; overflow-wrap: anywhere; }
    .resume-id-section { border-top: 1px solid #ede9fe; padding-top: 1rem; }
    .resume-id-image { display: block; width: 100%; max-height: 420px; object-fit: contain; border: 1px solid #e5e7eb; border-radius: .75rem; background: #f8fafc; }
    @media (max-width: 575.98px) { .resume-grid { grid-template-columns: 1fr; } }

    @media (min-width: 1200px) {
        #userTable { display: block; }
        #userTable thead { display: none; }
        #userTable tbody {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 0.85rem;
            padding: 0.85rem;
            background: #f8fafc;
        }
        #userTable tbody tr[data-status] {
            display: flex;
            flex-direction: column;
            min-width: 0;
            padding: 0.9rem;
            border: 1px solid #e5e7eb;
            border-top: 4px solid #a594f9;
            border-radius: 0.55rem;
            background: #fff;
            min-height: 0;
        }
        #userTable tbody tr[data-status] td { display: none; border: 0; }
        #userTable tbody tr[data-status] td:first-child,
        #userTable tbody tr[data-status] td:last-child { display: block; padding: 0 !important; text-align: left !important; }
        #userTable tbody tr[data-status] td:last-child { margin-top: 0.75rem; padding-top: 0.75rem !important; border-top: 1px solid #ede9fe; }
    }
</style>

<script>
    document.querySelectorAll('.user-tile').forEach(function (tile) {
        const openProfile = function (event) {
            if (event.target.closest('form, button, a, input, select, textarea')) return;
            bootstrap.Modal.getOrCreateInstance(document.getElementById(tile.dataset.profileModal)).show();
        };
        tile.addEventListener('click', openProfile);
        tile.addEventListener('keydown', function (event) {
            if (event.key === 'Enter' || event.key === ' ') {
                event.preventDefault();
                openProfile(event);
            }
        });
    });

    let currentStatusFilter = 'all';
    let currentAccountTypeFilter = '';
    let currentGenderFilter = '';
    let currentDepartmentFilter = '';
    const userTableBody = document.querySelector('#userTable tbody');
    const allRows = Array.from(userTableBody.querySelectorAll('tr[data-status]'));

    function getAgeRange(age) {
        age = parseInt(age);
        if (age >= 18 && age <= 25) return '18-25';
        if (age >= 26 && age <= 35) return '26-35';
        if (age >= 36 && age <= 50) return '36-50';
        if (age > 50) return '50+';
        return '';
    }

    function applyFilters() {
        const searchFilter = document.getElementById('userSearch').value.toUpperCase();
        const tbody = userTableBody;
        
        let filteredRows = allRows.filter(row => {
            const searchable = (row.dataset.search || '').toUpperCase();
            const status = row.dataset.status === 'approved' ? 'active' : (row.dataset.status || 'pending');
            const matchesSearch = searchable.indexOf(searchFilter) > -1;
            const matchesStatus = currentStatusFilter === 'all' || status === currentStatusFilter;
            const matchesAccountType = !currentAccountTypeFilter || row.dataset.accountType === currentAccountTypeFilter;
            const matchesGender = !currentGenderFilter || row.dataset.gender === currentGenderFilter;
            const matchesDept = !currentDepartmentFilter || row.dataset.department.toLowerCase().includes(currentDepartmentFilter.toLowerCase());

            return matchesSearch && matchesStatus && matchesAccountType && matchesGender && matchesDept;
        });

        // Clear and rebuild table
        tbody.innerHTML = '';
        if (filteredRows.length === 0) {
            tbody.innerHTML = '<tr><td colspan="6" class="text-center py-5"><div class="text-muted"><i class="bi bi-people fs-1 d-block mb-2"></i><p class="mb-0">No users match your filters.</p></div></td></tr>';
        } else {
            filteredRows.forEach(row => tbody.appendChild(row));
        }
    }

    const userFiltersForm = document.getElementById('userFiltersForm');
    document.getElementById('userSearch').addEventListener('keydown', function (event) {
        if (event.key === 'Enter') userFiltersForm.submit();
    });

    document.querySelectorAll('.user-status-filter').forEach((btn) => {
        btn.addEventListener('click', function () {
            document.querySelectorAll('.user-status-filter').forEach((b) => b.classList.remove('active'));
            this.classList.add('active');
            currentStatusFilter = this.dataset.status;
            applyFilters();
        });
    });

    document.getElementById('genderFilter').addEventListener('change', function() {
        userFiltersForm.submit();
    });

    document.getElementById('accountTypeFilter').addEventListener('change', function() {
        userFiltersForm.submit();
    });

    document.getElementById('departmentFilter').addEventListener('change', function() {
        userFiltersForm.submit();
    });


    document.querySelectorAll('.reject-user-form').forEach((form) => {
        form.addEventListener('submit', function (e) {
            const reason = prompt('Enter rejection reason (optional):', 'Please provide clearer verification images.');
            if (reason === null) {
                e.preventDefault();
                return;
            }
            this.querySelector('input[name="rejection_reason"]').value = reason;
        });
    });
</script>

<style>
    .table tbody tr:hover {
        background-color: #fbfaff !important;
        transition: 0.2s ease-in-out;
    }

    .badge {
        font-weight: 600;
        font-size: 0.75rem;
    }

    .user-status-filter {
        background: #a594f9;
        color: #fff;
        border: 1px solid #ddd6fe;
    }

    .user-status-filter:hover,
    .user-status-filter:focus {
        background: #8f72f5;
        color: #fff;
    }

    .user-status-filter.active {
        background: #A594F9;
        color: #fff;
        border-color: #A594F9;
    }

    @media (min-width: 1200px) {
        #userTable { display: block; }
        #userTable thead { display: none; }
        #userTable tbody {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 1rem;
            padding: 1rem;
            background: #f8fafc;
        }
        #userTable tbody tr[data-status] {
            display: flex;
            flex-direction: column;
            min-width: 0;
            padding: 1rem;
            border: 1px solid #e5e7eb;
            border-top: 4px solid #a594f9;
            border-radius: 1rem;
            background: #fff;
            box-shadow: 0 8px 20px rgba(76,29,149,0.07);
        }
        #userTable tbody tr[data-status] td {
            display: block;
            width: 100%;
            padding: .45rem 0 !important;
            border: 0;
            text-align: left !important;
        }
        #userTable tbody tr[data-status] td:last-child {
            margin-top: auto;
            padding-top: .8rem !important;
            border-top: 1px solid #ede9fe;
        }
        #userTable tbody tr[data-status] td .text-end { text-align: left !important; }
    }

    @media (max-width: 1199.98px) {
        .table-responsive { overflow-x: visible; }
        #userTable { display: block; }
        #userTable thead { display: none; }
        #userTable tbody {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 0.85rem;
            padding: 0.85rem;
            background: #f8fafc;
        }
        #userTable tbody tr[data-status] {
            display: flex;
            flex-direction: column;
            min-width: 0;
            padding: 0.9rem;
            border: 1px solid #e5e7eb;
            border-top: 4px solid #a594f9;
            border-radius: 1rem;
            background: #fff;
        }
        #userTable tbody tr[data-status] td {
            display: block;
            width: 100%;
            padding: 0.4rem 0 !important;
            border: 0;
            text-align: left !important;
        }
        #userTable tbody tr[data-status] td:last-child {
            margin-top: auto;
            padding-top: 0.75rem !important;
            border-top: 1px solid #ede9fe;
        }
    }

    @media (max-width: 767.98px) {
        #userTable tbody { grid-template-columns: 1fr; }
    }
</style>
@endsection
