<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>SINAG GAD Office Comprehensive Report</title>
    <style>
        @page {
            margin: 25px 30px;
        }
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #1e1b4b;
            font-size: 11px;
            line-height: 1.4;
        }
        .header {
            border-bottom: 2px solid #8b5cf6;
            padding-bottom: 12px;
            margin-bottom: 18px;
        }
        .header h1 {
            color: #5b21b6;
            font-size: 18px;
            margin: 0 0 4px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .header .subtitle {
            color: #6b7280;
            font-size: 11px;
        }
        .meta-table {
            width: 100%;
            margin-bottom: 15px;
            border-collapse: collapse;
        }
        .meta-table td {
            padding: 4px 0;
            font-size: 11px;
        }
        .meta-label {
            color: #6b7280;
            font-weight: 600;
            width: 18%;
        }
        .meta-value {
            color: #111827;
            font-weight: 500;
        }
        .summary-cards {
            width: 100%;
            margin-bottom: 20px;
            border-collapse: separate;
            border-spacing: 10px 0;
        }
        .summary-card {
            background-color: #f5f3ff;
            border: 1px solid #ddd6fe;
            border-radius: 6px;
            padding: 10px;
            text-align: center;
            width: 33.33%;
        }
        .summary-card .number {
            font-size: 20px;
            font-weight: bold;
            color: #6d28d9;
            margin-bottom: 2px;
        }
        .summary-card .label {
            font-size: 10px;
            color: #4b5563;
            text-transform: uppercase;
            font-weight: 600;
        }
        h2.section-title {
            color: #4c1d95;
            font-size: 13px;
            border-bottom: 1px solid #c4b5fd;
            padding-bottom: 4px;
            margin-top: 20px;
            margin-bottom: 8px;
            text-transform: uppercase;
        }
        h3.sub-section {
            color: #5b21b6;
            font-size: 11px;
            margin-top: 12px;
            margin-bottom: 6px;
            padding-left: 4px;
            border-left: 3px solid #a78bfa;
        }
        table.data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        table.data-table th {
            background-color: #ede9fe;
            color: #4c1d95;
            font-size: 9px;
            font-weight: bold;
            text-align: left;
            padding: 5px 5px;
            border: 1px solid #ddd6fe;
            text-transform: uppercase;
        }
        table.data-table td {
            font-size: 9px;
            padding: 4px 5px;
            border: 1px solid #e5e7eb;
            vertical-align: top;
        }
        table.data-table tr:nth-child(even) {
            background-color: #faf5ff;
        }
        .detail-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 8px;
        }
        .detail-table td {
            font-size: 9px;
            padding: 3px 6px;
            border: 1px solid #e5e7eb;
            vertical-align: top;
        }
        .detail-table .dl {
            color: #6b7280;
            font-weight: 600;
            width: 25%;
            background-color: #f9fafb;
        }
        .detail-table .dv {
            color: #111827;
            width: 25%;
        }
        .badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 8.5px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .badge-active { background-color: #dcfce7; color: #15803d; }
        .badge-pending { background-color: #fef9c3; color: #854d0e; }
        .badge-rejected { background-color: #fee2e2; color: #991b1b; }
        .badge-high { background-color: #fee2e2; color: #b91c1c; }
        .badge-normal { background-color: #e0e7ff; color: #3730a3; }
        .badge-low { background-color: #f3f4f6; color: #4b5563; }
        .user-block {
            margin-bottom: 14px;
            border: 1px solid #ddd6fe;
            border-radius: 4px;
            overflow: hidden;
        }
        .user-block-header {
            background-color: #ede9fe;
            padding: 6px 10px;
            font-size: 10px;
            font-weight: bold;
            color: #4c1d95;
        }
        .user-block-body {
            padding: 6px 10px;
        }
        .footer {
            margin-top: 25px;
            border-top: 1px solid #e5e7eb;
            padding-top: 8px;
            font-size: 9px;
            color: #9ca3af;
            text-align: center;
        }
        .page-break {
            page-break-after: always;
        }
    </style>
</head>
<body>

    <div class="header">
        <h1>SINAG - Gender and Development (GAD) Office</h1>
        <div class="subtitle">Comprehensive Analytics & Operations Summary Report</div>
    </div>

    <table class="meta-table">
        <tr>
            <td class="meta-label">Coverage Period:</td>
            <td class="meta-value">{{ $periodLabel }} ({{ $data['start_date'] }} &ndash; {{ $data['end_date'] }})</td>
            <td class="meta-label">Generated On:</td>
            <td class="meta-value">{{ $data['generated_at'] }}</td>
        </tr>
    </table>

    <table class="summary-cards">
        <tr>
            <td class="summary-card">
                <div class="number">{{ $data['total_users'] }}</div>
                <div class="label">New Registered Users</div>
            </td>
            <td class="summary-card">
                <div class="number">{{ $data['total_reports'] }}</div>
                <div class="label">Incidents Reported</div>
            </td>
            <td class="summary-card">
                <div class="number">{{ $data['total_suggestions'] }}</div>
                <div class="label">Suggestions Submitted</div>
            </td>
        </tr>
    </table>

    <!-- ================================================================ -->
    <!-- 1. REGISTERED USERS WITH COMPLETE PERSONAL DATA                  -->
    <!-- ================================================================ -->
    <h2 class="section-title">1. Registered Users &amp; Complete Personal Data ({{ $users->count() }})</h2>

    @forelse($users as $index => $u)
        @php
            $pi = $u->personal_information ?? [];
            $fb = $u->family_background ?? [];
            $si = $u->student_information ?? [];
            $oi = $u->other_information ?? [];
            $isStudent = in_array($u->account_type ?? $u->role, ['student']);
            $isEmployee = ($u->account_type ?? $u->role) === 'employee';
        @endphp

        <div class="user-block">
            <div class="user-block-header">
                #{{ $index + 1 }} — {{ $u->last_name ?? '' }}, {{ $u->first_name ?? '' }} {{ $u->middle_name ?? '' }}
                ({{ $u->email }})
                <span class="badge badge-{{ $u->account_status == 'active' || $u->account_status == 'approved' ? 'active' : ($u->account_status == 'rejected' ? 'rejected' : 'pending') }}" style="float: right;">
                    {{ ucfirst($u->account_status ?? 'Pending') }}
                </span>
            </div>
            <div class="user-block-body">

                {{-- A. Basic Account Info --}}
                <h3 class="sub-section">A. Basic Account Information</h3>
                <table class="detail-table">
                    <tr>
                        <td class="dl">User ID</td>
                        <td class="dv">{{ $u->id }}</td>
                        <td class="dl">Display Name</td>
                        <td class="dv">{{ $u->name }}</td>
                    </tr>
                    <tr>
                        <td class="dl">Email</td>
                        <td class="dv">{{ $u->email }}</td>
                        <td class="dl">Phone Number</td>
                        <td class="dv">{{ $u->phone_number ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td class="dl">Account Type</td>
                        <td class="dv">{{ ucfirst($u->account_type ?? 'N/A') }}</td>
                        <td class="dl">Employee Category</td>
                        <td class="dv">{{ ucfirst($u->employee_category ?? 'N/A') }}</td>
                    </tr>
                    <tr>
                        <td class="dl">Role</td>
                        <td class="dv">{{ ucfirst($u->role ?? 'User') }}</td>
                        <td class="dl">Department</td>
                        <td class="dv">{{ $u->department ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td class="dl">Age</td>
                        <td class="dv">{{ $u->age ?? 'N/A' }}</td>
                        <td class="dl">Gender</td>
                        <td class="dv">{{ $u->gender ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td class="dl">Cloak Alias</td>
                        <td class="dv">{{ $u->cloak_alias ?? 'N/A' }}</td>
                        <td class="dl">Registered</td>
                        <td class="dv">{{ optional($u->created_at)->format('M d, Y h:i A') }}</td>
                    </tr>
                </table>

                {{-- B. Personal Information --}}
                <h3 class="sub-section">B. Personal Information</h3>
                <table class="detail-table">
                    <tr>
                        <td class="dl">Date of Birth</td>
                        <td class="dv">{{ $pi['date_of_birth'] ?? 'N/A' }}</td>
                        <td class="dl">Place of Birth</td>
                        <td class="dv">{{ $pi['place_of_birth'] ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td class="dl">Sex</td>
                        <td class="dv">{{ $pi['sex'] ?? 'N/A' }}</td>
                        <td class="dl">Gender Identity</td>
                        <td class="dv">{{ $pi['gender_identity'] ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td class="dl">Citizenship</td>
                        <td class="dv">{{ $pi['citizenship'] ?? 'N/A' }}</td>
                        <td class="dl">Blood Type</td>
                        <td class="dv">{{ $pi['blood_type'] ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td class="dl">Height</td>
                        <td class="dv">{{ $pi['height'] ?? 'N/A' }}</td>
                        <td class="dl">Weight</td>
                        <td class="dv">{{ $pi['weight'] ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td class="dl">Landline Number</td>
                        <td class="dv">{{ $pi['landline_number'] ?? 'N/A' }}</td>
                        <td class="dl">Civil Status</td>
                        <td class="dv">{{ $pi['civil_status'] ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td class="dl">Religion</td>
                        <td class="dv">{{ $pi['religion'] ?? 'N/A' }}@if(!empty($pi['religion_other'])) ({{ $pi['religion_other'] }})@endif</td>
                        <td class="dl">Disabilities</td>
                        <td class="dv">
                            @if(!empty($pi['disabilities']) && is_array($pi['disabilities']))
                                {{ implode(', ', $pi['disabilities']) }}
                            @else
                                {{ $pi['disabilities'] ?? 'None' }}
                            @endif
                            @if(!empty($pi['disability_other'])) ({{ $pi['disability_other'] }})@endif
                        </td>
                    </tr>
                    <tr>
                        <td class="dl">Current Address</td>
                        <td class="dv" colspan="3">{{ $pi['current_address'] ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td class="dl">Home Address</td>
                        <td class="dv" colspan="3">{{ $pi['home_address'] ?? 'N/A' }}</td>
                    </tr>
                </table>

                {{-- C. Family Background --}}
                <h3 class="sub-section">C. Family Background</h3>
                <table class="detail-table">
                    <tr>
                        <td class="dl">Spouse Name</td>
                        <td class="dv">{{ $fb['spouse_name'] ?? 'N/A' }}</td>
                        <td class="dl">Spouse Occupation</td>
                        <td class="dv">{{ $fb['spouse_occupation'] ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td class="dl">Father's Name</td>
                        <td class="dv">{{ $fb['father_name'] ?? 'N/A' }}</td>
                        <td class="dl">Father's Occupation</td>
                        <td class="dv">{{ $fb['father_occupation'] ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td class="dl">Father's Age</td>
                        <td class="dv">{{ $fb['father_age'] ?? 'N/A' }}</td>
                        <td class="dl">Mother's Name</td>
                        <td class="dv">{{ $fb['mother_name'] ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td class="dl">Mother's Occupation</td>
                        <td class="dv">{{ $fb['mother_occupation'] ?? 'N/A' }}</td>
                        <td class="dl">Mother's Age</td>
                        <td class="dv">{{ $fb['mother_age'] ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td class="dl">Total Children</td>
                        <td class="dv">{{ $fb['children_total'] ?? 'N/A' }} (Boys: {{ $fb['children_boys'] ?? '0' }}, Girls: {{ $fb['children_girls'] ?? '0' }})</td>
                        <td class="dl">Siblings</td>
                        <td class="dv">Brothers: {{ $fb['brothers_count'] ?? '0' }}, Sisters: {{ $fb['sisters_count'] ?? '0' }}</td>
                    </tr>
                    <tr>
                        <td class="dl">Monthly Income</td>
                        <td class="dv">{{ $fb['monthly_income'] ?? 'N/A' }}</td>
                        <td class="dl">Family Type</td>
                        <td class="dv">{{ $fb['family_type'] ?? 'N/A' }}@if(!empty($fb['family_type_other'])) ({{ $fb['family_type_other'] }})@endif</td>
                    </tr>
                    <tr>
                        <td class="dl">Home Ownership</td>
                        <td class="dv">{{ $fb['home_ownership_type'] ?? 'N/A' }}@if(!empty($fb['home_ownership_other'])) ({{ $fb['home_ownership_other'] }})@endif</td>
                        <td class="dl">Residential Home Type</td>
                        <td class="dv">{{ $fb['residential_home_type'] ?? 'N/A' }}@if(!empty($fb['residential_home_other'])) ({{ $fb['residential_home_other'] }})@endif</td>
                    </tr>
                </table>

                {{-- D. Student Information (if student) --}}
                @if($isStudent)
                    <h3 class="sub-section">D. Student Information</h3>
                    <table class="detail-table">
                        <tr>
                            <td class="dl">Year Level</td>
                            <td class="dv">{{ $si['year_level'] ?? 'N/A' }}</td>
                            <td class="dl">School Type</td>
                            <td class="dv">{{ $si['school_type'] ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <td class="dl">Last School Attended</td>
                            <td class="dv" colspan="3">{{ $si['last_school_attended'] ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <td class="dl">Achievements</td>
                            <td class="dv" colspan="3">
                                @if(!empty($si['achievements']) && is_array($si['achievements']))
                                    {{ implode(', ', $si['achievements']) }}
                                @else
                                    {{ $si['achievements'] ?? 'N/A' }}
                                @endif
                                @if(!empty($si['achievement_other_rank'])) — Other Rank: {{ $si['achievement_other_rank'] }}@endif
                            </td>
                        </tr>
                        <tr>
                            <td class="dl">Financing Sources</td>
                            <td class="dv" colspan="3">
                                @if(!empty($si['financing_sources']) && is_array($si['financing_sources']))
                                    {{ implode(', ', $si['financing_sources']) }}
                                @else
                                    {{ $si['financing_sources'] ?? 'N/A' }}
                                @endif
                                @if(!empty($si['financing_other_work'])) — Other: {{ $si['financing_other_work'] }}@endif
                            </td>
                        </tr>
                        <tr>
                            <td class="dl">GAD Training Attended</td>
                            <td class="dv">{{ isset($si['gad_training_attended']) ? ($si['gad_training_attended'] ? 'Yes' : 'No') : 'N/A' }}</td>
                            <td class="dl">Training Details</td>
                            <td class="dv">{{ $si['gad_training_details'] ?? 'N/A' }}</td>
                        </tr>
                    </table>
                @endif

                {{-- E-I. Employee Sections (if employee) --}}
                @if($isEmployee)
                    {{-- Employee Education --}}
                    @php $empEd = $u->employee_education ?? []; @endphp
                    @if(!empty($empEd))
                        <h3 class="sub-section">D. Employee Education</h3>
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Level</th>
                                    <th>School Name</th>
                                    <th>Degree/Course</th>
                                    <th>From</th>
                                    <th>To</th>
                                    <th>Units</th>
                                    <th>Graduated</th>
                                    <th>Honors</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($empEd as $i => $rec)
                                    <tr>
                                        <td>{{ $i + 1 }}</td>
                                        <td>{{ $rec['level'] ?? 'N/A' }}</td>
                                        <td>{{ $rec['school_name'] ?? 'N/A' }}</td>
                                        <td>{{ $rec['degree_course'] ?? 'N/A' }}</td>
                                        <td>{{ $rec['year_from'] ?? 'N/A' }}</td>
                                        <td>{{ $rec['year_to'] ?? 'N/A' }}</td>
                                        <td>{{ $rec['units_earned'] ?? 'N/A' }}</td>
                                        <td>{{ $rec['year_graduated'] ?? 'N/A' }}</td>
                                        <td>{{ $rec['academic_honors'] ?? 'N/A' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif

                    {{-- Civil Service Eligibility --}}
                    @php $cse = $u->civil_service_eligibility ?? []; @endphp
                    @if(!empty($cse))
                        <h3 class="sub-section">E. Civil Service Eligibility</h3>
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Eligibility Title</th>
                                    <th>Rating</th>
                                    <th>Date of Exam</th>
                                    <th>Place of Exam</th>
                                    <th>License No.</th>
                                    <th>Validity</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($cse as $i => $rec)
                                    <tr>
                                        <td>{{ $i + 1 }}</td>
                                        <td>{{ $rec['eligibility_title'] ?? $rec['title'] ?? 'N/A' }}</td>
                                        <td>{{ $rec['rating'] ?? 'N/A' }}</td>
                                        <td>{{ $rec['date_of_exam'] ?? $rec['exam_date'] ?? 'N/A' }}</td>
                                        <td>{{ $rec['place_of_exam'] ?? $rec['exam_place'] ?? 'N/A' }}</td>
                                        <td>{{ $rec['license_number'] ?? 'N/A' }}</td>
                                        <td>{{ $rec['license_validity'] ?? 'N/A' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif

                    {{-- Work Experience --}}
                    @php $we = $u->work_experience ?? []; @endphp
                    @if(!empty($we))
                        <h3 class="sub-section">F. Work Experience</h3>
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Position</th>
                                    <th>Dept/Agency</th>
                                    <th>Salary</th>
                                    <th>Grade</th>
                                    <th>Status</th>
                                    <th>From</th>
                                    <th>To</th>
                                    <th>Gov't</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($we as $i => $rec)
                                    <tr>
                                        <td>{{ $i + 1 }}</td>
                                        <td>{{ $rec['position_title'] ?? $rec['position'] ?? 'N/A' }}</td>
                                        <td>{{ $rec['department_agency'] ?? $rec['department'] ?? 'N/A' }}</td>
                                        <td>{{ $rec['monthly_salary'] ?? $rec['salary'] ?? 'N/A' }}</td>
                                        <td>{{ $rec['salary_grade'] ?? 'N/A' }}</td>
                                        <td>{{ $rec['status_of_appointment'] ?? $rec['status'] ?? 'N/A' }}</td>
                                        <td>{{ $rec['date_from'] ?? $rec['from'] ?? 'N/A' }}</td>
                                        <td>{{ $rec['date_to'] ?? $rec['to'] ?? 'N/A' }}</td>
                                        <td>{{ $rec['government_service'] ?? $rec['govt_service'] ?? 'N/A' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif

                    {{-- Voluntary Work --}}
                    @php $vw = $u->voluntary_work ?? []; @endphp
                    @if(!empty($vw))
                        <h3 class="sub-section">G. Voluntary Work / Civic Engagement</h3>
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Organization</th>
                                    <th>Position / Nature of Work</th>
                                    <th>From</th>
                                    <th>To</th>
                                    <th>Hours</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($vw as $i => $rec)
                                    <tr>
                                        <td>{{ $i + 1 }}</td>
                                        <td>{{ $rec['organization_name'] ?? $rec['organization'] ?? 'N/A' }}</td>
                                        <td>{{ $rec['position'] ?? $rec['nature_of_work'] ?? 'N/A' }}</td>
                                        <td>{{ $rec['date_from'] ?? $rec['from'] ?? 'N/A' }}</td>
                                        <td>{{ $rec['date_to'] ?? $rec['to'] ?? 'N/A' }}</td>
                                        <td>{{ $rec['number_of_hours'] ?? $rec['hours'] ?? 'N/A' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif

                    {{-- GAD Training --}}
                    @php $gt = $u->gad_training ?? []; @endphp
                    @if(!empty($gt))
                        <h3 class="sub-section">H. GAD Training / Learning & Development</h3>
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Training Title</th>
                                    <th>From</th>
                                    <th>To</th>
                                    <th>Hours</th>
                                    <th>Type of LD</th>
                                    <th>Conducted By</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($gt as $i => $rec)
                                    <tr>
                                        <td>{{ $i + 1 }}</td>
                                        <td>{{ $rec['training_title'] ?? $rec['title'] ?? 'N/A' }}</td>
                                        <td>{{ $rec['date_from'] ?? $rec['from'] ?? 'N/A' }}</td>
                                        <td>{{ $rec['date_to'] ?? $rec['to'] ?? 'N/A' }}</td>
                                        <td>{{ $rec['number_of_hours'] ?? $rec['hours'] ?? 'N/A' }}</td>
                                        <td>{{ $rec['type_of_ld'] ?? $rec['type'] ?? 'N/A' }}</td>
                                        <td>{{ $rec['conducted_by'] ?? $rec['sponsor'] ?? 'N/A' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif

                    {{-- Other Information --}}
                    @if(!empty($oi))
                        <h3 class="sub-section">I. Other Information</h3>
                        <table class="detail-table">
                            <tr>
                                <td class="dl">Skills / Hobbies</td>
                                <td class="dv" colspan="3">{{ $oi['emp_skills_hobbies'] ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <td class="dl">Non-Academic Distinctions</td>
                                <td class="dv" colspan="3">{{ $oi['emp_distinctions'] ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <td class="dl">Membership in Associations</td>
                                <td class="dv" colspan="3">{{ $oi['emp_membership'] ?? 'N/A' }}</td>
                            </tr>
                        </table>
                    @endif
                @endif

            </div>
        </div>

        @if(!$loop->last)
            <div class="page-break"></div>
        @endif

    @empty
        <p style="text-align: center; color: #6b7280; padding: 20px;">No users registered during this period.</p>
    @endforelse

    <div class="page-break"></div>

    <!-- ================================================================ -->
    <!-- 2. INCIDENT REPORTS                                              -->
    <!-- ================================================================ -->
    <h2 class="section-title">2. Incident Reports ({{ $reports->count() }})</h2>
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 12%;">Incident ID</th>
                <th style="width: 14%;">Reporter Alias</th>
                <th style="width: 20%;">Nature</th>
                <th style="width: 22%;">Location</th>
                <th style="width: 12%;">Priority</th>
                <th style="width: 10%;">Status</th>
                <th style="width: 10%;">Filed Date</th>
            </tr>
        </thead>
        <tbody>
            @forelse($reports as $r)
                <tr>
                    <td><strong>{{ $r->incident_id }}</strong></td>
                    <td>{{ $r->cloak_alias ?? 'Anonymous' }}</td>
                    <td>{{ $r->nature }}</td>
                    <td>{{ $r->location ?? 'N/A' }}</td>
                    <td>
                        <span class="badge badge-{{ strtolower($r->priority) == 'high' ? 'high' : (strtolower($r->priority) == 'normal' ? 'normal' : 'low') }}">
                            {{ ucfirst($r->priority ?? 'Normal') }}
                        </span>
                    </td>
                    <td>
                        <span class="badge badge-{{ in_array(strtolower($r->status), ['resolved', 'completed', 'approved']) ? 'active' : 'pending' }}">
                            {{ ucfirst($r->status ?? 'Pending') }}
                        </span>
                    </td>
                    <td>{{ optional($r->created_at)->format('M d, Y') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" style="text-align: center; color: #6b7280; padding: 12px;">No incident reports submitted during this period.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- ================================================================ -->
    <!-- 3. SUGGESTIONS                                                   -->
    <!-- ================================================================ -->
    <h2 class="section-title">3. Suggestions & Community Feedback ({{ $suggestions->count() }})</h2>
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 8%;">ID</th>
                <th style="width: 16%;">Submitted By</th>
                <th style="width: 16%;">Category</th>
                <th style="width: 40%;">Message / Content</th>
                <th style="width: 8%;">Upvotes</th>
                <th style="width: 12%;">Date</th>
            </tr>
        </thead>
        <tbody>
            @forelse($suggestions as $s)
                <tr>
                    <td>#{{ $s->id }}</td>
                    <td>{{ $s->is_anonymous ? 'Anonymous' : (optional($s->user)->name ?? 'User') }}</td>
                    <td><strong>{{ $s->category ?? 'General' }}</strong></td>
                    <td>{{ \Illuminate\Support\Str::limit($s->message, 120) }}</td>
                    <td style="text-align: center;">{{ $s->upvotes_count ?? 0 }}</td>
                    <td>{{ optional($s->created_at)->format('M d, Y') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" style="text-align: center; color: #6b7280; padding: 12px;">No suggestions submitted during this period.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        SINAG Safe Space & Participatory Suggestion System &bull; Confidential Administrative Report &bull; Generated {{ $data['generated_at'] }}
    </div>

</body>
</html>
