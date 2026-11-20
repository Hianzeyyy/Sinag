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
        .pie-grid { margin-top: 10px; width: 100%; }
        .pie-card { float: left; width: 47%; min-height: 170px; margin: 0 2% 14px 0; padding: 8px; border: 1px solid #ddd6fe; page-break-inside: avoid; }
        .pie-card strong { display: block; min-height: 28px; color: #4c1d95; font-size: 8px; }
        .pie-card img { display: block; width: 92px; height: 92px; margin: 6px auto; }
        .pie-legend { color: #4b5563; font-size: 7px; }
        .pie-legend span { display: block; margin-top: 2px; }
        .pie-legend i { display: inline-block; width: 7px; height: 7px; margin-right: 4px; border-radius: 50%; }
        .footer { margin-top: 18px; padding-top: 6px; border-top: 1px solid #e5e7eb; color: #9ca3af; text-align: center; font-size: 8px; }
        .divider { page-break-before: always; padding: 140px 34px 0; text-align: center; }
        .divider h1 { font-size: 20px; }
        .divider-note { max-width: 520px; margin: 16px auto; color: #4b5563; font-size: 9px; }
        .part-b h2 { margin-top: 20px; }
        .part-b h3 { margin: 12px 0 5px; color: #4c1d95; font-size: 10px; }
        .sex-table { width: 100%; border-collapse: collapse; table-layout: fixed; font-size: 6px; margin: 6px 0 8px; }
        .sex-table th { padding: 4px 2px; border: 1px solid #ddd6fe; background: #ede9fe; color: #4c1d95; text-align: center; }
        .sex-table td { padding: 4px 2px; border: 1px solid #e5e7eb; text-align: center; overflow-wrap: break-word; }
        .sex-table th:first-child, .sex-table td:first-child { width: 16%; text-align: left; }
        .report-meta { margin: 4px 0 6px; color: #4b5563; font-size: 8px; }
        .data-note { margin: 5px 0 10px; color: #6b7280; font-size: 7px; }
        .page-section { page-break-inside: avoid; }
    </style>
</head>
<body>
    @php
        $sectionOffset = $includeReports && ! $includeRegisteredUsers ? -1 : 0;
    @endphp
    <div class="header">
        <h1>SINAG - Gender and Development (GAD) Office</h1>
        <div class="subtitle">Comprehensive Analytics &amp; Operations Summary Report</div>
    </div>


    <table class="meta">
        <tr>
            <td><strong>Coverage Period:</strong> {{ $data['period_label'] }} ({{ $data['start_date'] }} – {{ $data['end_date'] }})</td>
            <td><strong>Generated On:</strong> {{ $data['generated_at'] }}</td>
        </tr>
    </table>

    <table class="summary">
        <tr>
            @if($includeReports)
                @if($includeRegisteredUsers)
                <td><div class="number">{{ $data['total_users'] }}</div><div class="label">New Registered Users</div></td>
                @endif
                <td><div class="number">{{ $data['total_reports'] }}</div><div class="label">Incidents Reported</div></td>
            @endif
        </tr>
        @if($includeReports && $includeFeedback)
            <tr>
                <td colspan="3"><div class="number">{{ $data['total_suggestions'] }}</div><div class="label">Suggestions Submitted</div></td>
            </tr>
        @endif
        @if($includeFeedback)
            <tr>
                <td colspan="{{ $includeReports ? 3 : 1 }}"><div class="number">{{ $data['average_satisfaction'] ?: 'N/A' }} / 5</div><div class="label">Average Customer Satisfaction ({{ $data['total_satisfaction_surveys'] }} responses)</div></td>
            </tr>
        @endif
    </table>

    @if($includeReports && $includeRegisteredUsers)
    <h2>1. Registered Users ({{ $users->count() }})</h2>
    <table class="report-table">
        <thead>
            <tr><th>ID</th><th>Name</th><th>Email</th><th>Account Type</th><th>Department</th><th>Status</th><th>Registered</th></tr>
        </thead>
        <tbody>
            @forelse($users as $user)
                <tr>
                    <td>{{ $user->id }}</td>
                    <td>{{ $user->name }}</td>
                    <td>{{ $user->email }}</td>
                    <td>{{ $user->account_type ?? $user->role ?? 'N/A' }}</td>
                    <td>{{ $user->department ?? 'N/A' }}</td>
                    <td>{{ ucfirst($user->account_status ?? 'Active') }}</td>
                    <td>{{ optional($user->created_at)->format('M d, Y') }}</td>
                </tr>
            @empty
                <tr><td colspan="7" class="muted">No users registered during this period.</td></tr>
            @endforelse
        </tbody>
    </table>
    @endif

    @if($includeReports)
    <h2>{{ 2 + $sectionOffset }}. Incident Reports ({{ $reports->count() }})</h2>
    <table class="report-table">
        <thead>
            <tr><th>Incident ID</th><th>Reporter Alias</th><th>Nature</th><th>Location</th><th>Priority</th><th>Status</th><th>Filed Date</th></tr>
        </thead>
        <tbody>
            @forelse($reports as $report)
                <tr>
                    <td>{{ $report->incident_id }}</td>
                    <td>{{ $report->cloak_alias ?? 'Anonymous' }}</td>
                    <td>{{ $report->nature }}</td>
                    <td>{{ $report->location ?? 'N/A' }}</td>
                    <td>{{ ucfirst($report->priority ?? 'Normal') }}</td>
                    <td>{{ ucfirst($report->status ?? 'Pending') }}</td>
                    <td>{{ optional($report->created_at)->format('M d, Y') }}</td>
                </tr>
            @empty
                <tr><td colspan="7" class="muted">No incident reports submitted during this period.</td></tr>
            @endforelse
        </tbody>
    </table>
    @endif

    @if($includeFeedback)
    @if($includeReports)
    <h2>{{ 3 + $sectionOffset }}. Suggestions &amp; Community Feedback ({{ $suggestions->count() }})</h2>
    <table class="report-table">
        <thead><tr><th>ID</th><th>Submitted By</th><th>Category</th><th>Message</th><th>Upvotes</th><th>Date</th></tr></thead>
        <tbody>
            @forelse($suggestions as $suggestion)
                <tr>
                    <td>#{{ $suggestion->id }}</td>
                    <td>{{ $suggestion->is_anonymous ? 'Anonymous' : (optional($suggestion->user)->name ?? 'User') }}</td>
                    <td>{{ $suggestion->category ?? 'General' }}</td>
                    <td>{{ \Illuminate\Support\Str::limit($suggestion->message, 120) }}</td>
                    <td>{{ $suggestion->upvotes_count ?? 0 }}</td>
                    <td>{{ optional($suggestion->created_at)->format('M d, Y') }}</td>
                </tr>
            @empty
                <tr><td colspan="6" class="muted">No suggestions submitted during this period.</td></tr>
            @endforelse
        </tbody>
    </table>
    @endif

    <h2>{{ ($includeReports ? 4 : 3) + $sectionOffset }}. Customer Satisfaction Survey ({{ $satisfactionSurveys->count() }})</h2>
    <table class="report-table">
        <thead><tr><th>Respondent</th><th>Rating</th><th>Survey Responses</th><th>Feedback</th><th>Date</th></tr></thead>
        <tbody>
            @forelse($satisfactionSurveys as $survey)
                <tr>
                    <td>{{ $showSatisfactionNames ? (optional($survey->user)->name ?? 'Unknown') : 'Anonymous' }}</td>
                    <td>{{ $survey->rating }} / 5</td>
                    <td>{{ json_encode($survey->responses ?? [], JSON_UNESCAPED_UNICODE) }}</td>
                    <td>{{ $survey->feedback ?? '—' }}</td>
                    <td>{{ optional($survey->created_at)->format('M d, Y') }}</td>
                </tr>
            @empty
                <tr><td colspan="5" class="muted">No satisfaction surveys submitted during this period.</td></tr>
            @endforelse
        </tbody>
    </table>

    <h2>{{ ($includeReports ? 5 : 4) + $sectionOffset }}. Survey Findings</h2>
    <table class="report-table">
        <thead><tr><th>Question</th><th>Responses</th><th>Average</th><th>Favorable</th><th>Finding</th></tr></thead>
        <tbody>
            @foreach($surveyFindings as $finding)
                <tr>
                    <td>{{ $finding['question'] }}</td>
                    <td>{{ $finding['responses'] }}</td>
                    <td>{{ $finding['average_score'] }}</td>
                    <td>{{ $finding['favorable_percentage'] }}%</td>
                    <td>{{ $finding['finding'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <div class="pie-grid">
        @foreach($surveyFindings as $finding)
            <div class="pie-card">
                <strong>{{ $finding['question'] }}</strong>
                @if($finding['pie_image'])
                    <img src="data:image/png;base64,{{ $finding['pie_image'] }}" alt="Survey response distribution">
                @endif
                <div class="pie-legend">
                    <span><i style="background:#ef4444"></i>Strongly disagree ({{ $finding['counts'][1] }})</span>
                    <span><i style="background:#f59e0b"></i>Disagree ({{ $finding['counts'][2] }})</span>
                    <span><i style="background:#84cc16"></i>Agree ({{ $finding['counts'][3] }})</span>
                    <span><i style="background:#22c55e"></i>Strongly agree ({{ $finding['counts'][4] }})</span>
                </div>
            </div>
            @if($loop->iteration % 2 === 0)
                <div style="clear:both"></div>
            @endif
        @endforeach
    </div>
    <div style="clear:both"></div>
    @endif

    @if($includeReports)
    <h2>{{ 5 + $sectionOffset }}. Student Information Sheets ({{ count($studentProfiles) }})</h2>
    @forelse($studentProfiles as $profile)
        <h3 class="profile-name">{{ $profile['user']->name }} ({{ $profile['user']->email }})</h3>
        <table class="profile-table">
            @foreach($profile['basic'] as $label => $value)
                <tr><th>{{ $label }}</th><td>{{ $value }}</td></tr>
            @endforeach
            @foreach($profile['sections'] as $section => $details)
                <tr><th>{{ $section }}</th><td>{{ $details }}</td></tr>
            @endforeach
        </table>
    @empty
        <p class="muted">No student information sheets were submitted.</p>
    @endforelse

    <h2>{{ 6 + $sectionOffset }}. Faculty and Non-Teaching Information Sheets ({{ count($employeeProfiles) }})</h2>
    @forelse($employeeProfiles as $profile)
        <h3 class="profile-name">{{ $profile['user']->name }} ({{ $profile['user']->email }})</h3>
        <table class="profile-table">
            @foreach($profile['basic'] as $label => $value)
                <tr><th>{{ $label }}</th><td>{{ $value }}</td></tr>
            @endforeach
            @foreach($profile['sections'] as $section => $details)
                <tr><th>{{ $section }}</th><td>{{ $details }}</td></tr>
            @endforeach
        </table>
    @empty
        <p class="muted">No faculty or non-teaching information sheets were submitted.</p>
    @endforelse
    @endif

    <div class="divider part-b">
        <h1>Part B – Sex-Disaggregated Data Summary</h1>
        <div class="divider-note">
            Purpose: support GAD planning, Safe Spaces monitoring, inclusive service delivery, and aggregate administrative reporting.
            Legal basis: RA 9710 Magna Carta of Women; RA 11313 Safe Spaces Act; RA 9262 Anti-Violence Against Women and Their Children Act;
            PCW GADtimpala MC 2022-06; and RA 10173 Data Privacy Act.
        </div>
        <div class="muted">Coverage Period: {{ $data['period_label'] }} ({{ $data['start_date'] }} – {{ $data['end_date'] }})</div>
    </div>

    <div class="part-b">
        <h2>{{ 7 + $sectionOffset }}. Population, New Users and Gender</h2>
        <div class="report-meta"><strong>Coverage Period:</strong> {{ $data['period_label'] }} ({{ $data['start_date'] }} – {{ $data['end_date'] }})</div>
        <div class="data-note">Student and employee population totals are current account counts; new-user counts use registration dates in this Coverage Period. Student and employee form-based breakdowns use accounts registered in this Coverage Period because the forms do not retain historical versions. All outputs are aggregate-only.</div>

        <div class="page-section">
            <h3>7.1 Report 1: Student Population, New Users and Gender per Department/Program</h3>
            <div class="report-meta"><strong>Purpose:</strong> Show student population and newly registered accounts by department/program and Sex/Gender/SOGIESC.<br>
            <strong>Source:</strong> users.department, users.account_type/role, users.account_status, users.created_at, users.personal_information.sex, users.personal_information.gender_identity, users.student_information.<br>
            <strong>Filters:</strong> account_type/role = student; population can include existing accounts; new users require created_at within the Coverage Period. <strong>Access:</strong> GAD Administrator only.</div>
            <table class="sex-table"><thead><tr><th>Category</th><th>Male (f / %)</th><th>Female (f / %)</th><th>Total (N)</th><th>Lesbian</th><th>Gay</th><th>Bisexual</th><th>Transsexual</th><th>Cisgender/Heterosexual</th><th>Non-Binary</th><th>Prefer Not to Say</th></tr></thead>
            @include('admin.partials.gad-sex-disaggregation-table', ['rows' => $sexDisaggregatedData['reports'][1]])
            </table>
            <div class="data-note"><strong>Data logic:</strong> Group Sex first (Male, Female, then Prefer Not to Say/unknown), then Gender/SOGIESC. f = distinct student count. Row percentages use the Male/Female counts; N = Male f + Female f, as also stated below each table. Sex values other than Male/Female are excluded from the standard table denominator. Report each account status (Active/Rejected/Pending) separately. Gender cells with f &lt; 5 display as "&lt;5". Optional bar chart: population and new-user counts per department/program.</div>
            <div class="data-note"><strong>Data note:</strong> Program/major is from users.department or users.student_information; if absent, mark Pending data source.</div>
        </div>

        <div class="page-section">
            <h3>7.2 Report 2: Employee Population, New Users and Gender per Department/Office</h3>
            <div class="report-meta"><strong>Purpose:</strong> Show teaching and non-teaching employee population and registrations by office and Sex/Gender/SOGIESC.<br>
            <strong>Source:</strong> users.department, users.account_type/role, users.employee_category, users.account_status, users.created_at, users.personal_information.sex, users.personal_information.gender_identity.<br>
            <strong>Filters:</strong> account_type/role = employee, split by employee_category Teaching/Non-Teaching. <strong>Access:</strong> GAD Administrator only.</div>
            <table class="sex-table"><thead><tr><th>Category</th><th>Male (f / %)</th><th>Female (f / %)</th><th>Total (N)</th><th>Lesbian</th><th>Gay</th><th>Bisexual</th><th>Transsexual</th><th>Cisgender/Heterosexual</th><th>Non-Binary</th><th>Prefer Not to Say</th></tr></thead>
            @include('admin.partials.gad-sex-disaggregation-table', ['rows' => $sexDisaggregatedData['reports'][2]])
            </table>
            <div class="data-note"><strong>Data logic:</strong> Filter by employee role and employee_category. f = distinct employee count; row % and N follow the standard Male/Female rule stated below the table. Sex values other than Male/Female are excluded from the standard table denominator. Status counts use Active/Rejected/Pending. Any Gender/SOGIESC cell f &lt; 5 displays "&lt;5".</div>
        </div>

        <h2>{{ 8 + $sectionOffset }}. Student Reports</h2>
        <div class="report-meta"><strong>Coverage Period:</strong> {{ $data['period_label'] }} ({{ $data['start_date'] }} – {{ $data['end_date'] }})</div>

        <div class="page-section">
            <h3>8.1 Report 3: Course/Major and Year Level by Sex and Gender</h3>
            <div class="report-meta"><strong>Purpose:</strong> Compare course/major and year-level enrollment by Sex and Gender/SOGIESC.<br><strong>Source:</strong> users.student_information.year_level, users.student_information.course/major (or users.department), users.personal_information.sex, users.personal_information.gender_identity.<br><strong>Filters:</strong> student role; existing population and optional Coverage Period for new users. <strong>Access:</strong> GAD Administrator only.</div>
            <table class="sex-table"><thead><tr><th>Category</th><th>Male (f / %)</th><th>Female (f / %)</th><th>Total (N)</th><th>Lesbian</th><th>Gay</th><th>Bisexual</th><th>Transsexual</th><th>Cisgender/Heterosexual</th><th>Non-Binary</th><th>Prefer Not to Say</th></tr></thead>
            @include('admin.partials.gad-sex-disaggregation-table', ['rows' => $sexDisaggregatedData['reports'][3]])</table>
            <div class="data-note"><strong>Data logic:</strong> First group by course/major and year level, then Sex, then Gender/SOGIESC. f = distinct students; row percentages sum to 100.0%; N = sum of row parts. Chart: bar chart by course/major and Sex.</div>
        </div>

        <div class="page-section">
            <h3>8.2 Report 4: Vulnerable Sectors and Socio-Economic Profile</h3>
            <div class="report-meta"><strong>Purpose:</strong> Report solo-parent families, working students, PWD categories, monthly family income, home ownership, and schooling financiers.<br><strong>Source:</strong> personal_information.disabilities/civil_status; family_background.family_type/monthly_income/home_ownership_type; student_information.financing_sources/financing_other_work.<br><strong>Filters:</strong> student role; aggregate only. <strong>Access:</strong> GAD Administrator only.</div>
            <table class="sex-table"><thead><tr><th>Category</th><th>Male (f / %)</th><th>Female (f / %)</th><th>Total (N)</th><th>Lesbian</th><th>Gay</th><th>Bisexual</th><th>Transsexual</th><th>Cisgender/Heterosexual</th><th>Non-Binary</th><th>Prefer Not to Say</th></tr></thead>
            @include('admin.partials.gad-sex-disaggregation-table', ['rows' => $sexDisaggregatedData['reports'][4]])</table>
            <div class="data-note"><strong>Data logic:</strong> Create separate category rows for solo-parent, work type, PWD category, monthly income bracket, home ownership, and financing source. f = distinct students matching category; row % = f/N × 100.0; N = sum of displayed Sex parts. Gender cells f &lt; 5 display "&lt;5".</div>
        </div>

        <div class="page-section">
            <h3>8.3 Report 5: Academic Background and Honors</h3>
            <div class="report-meta"><strong>Purpose:</strong> Summarize public/private feeder schools and academic honors.<br><strong>Source:</strong> student_information.school_type, last_school_attended, achievements, achievement_other_rank; personal sex/gender_identity.<br><strong>Filters:</strong> student role. <strong>Access:</strong> GAD Administrator only.</div>
            <table class="sex-table"><thead><tr><th>Category</th><th>Male (f / %)</th><th>Female (f / %)</th><th>Total (N)</th><th>Lesbian</th><th>Gay</th><th>Bisexual</th><th>Transsexual</th><th>Cisgender/Heterosexual</th><th>Non-Binary</th><th>Prefer Not to Say</th></tr></thead>
            @include('admin.partials.gad-sex-disaggregation-table', ['rows' => $sexDisaggregatedData['reports'][5]])</table>
            <div class="data-note"><strong>Data logic:</strong> Rows include Public, Private, State University/Other; and Valedictorian, Salutatorian, Honorable Mention, Other rank. f = distinct students; row % and N follow the standard rule. Pie chart optional for feeder-school distribution.</div>
        </div>

        <div class="page-section">
            <h3>8.4 Report 6: Retention, LOA and Attrition Reasons</h3>
            <div class="report-meta"><strong>Purpose:</strong> Analyze retention, leave of absence, and attrition reasons by Sex and Gender/SOGIESC.<br><strong>Source:</strong> Pending source: retention_status, LOA_status, attrition_reason, and effective date fields.<br><strong>Filters:</strong> student role; coverage period. <strong>Access:</strong> GAD Administrator only.</div>
            <table class="sex-table"><thead><tr><th>Category</th><th>Male (f / %)</th><th>Female (f / %)</th><th>Total (N)</th><th>Lesbian</th><th>Gay</th><th>Bisexual</th><th>Transsexual</th><th>Cisgender/Heterosexual</th><th>Non-Binary</th><th>Prefer Not to Say</th></tr></thead><tbody><tr><td colspan="11">Pending data source: retention, LOA, and attrition status/reason/date fields are not present.</td></tr></tbody></table>
            <div class="data-note"><strong>Data note:</strong> Marked “Pending data source.” Add student status/history fields for retained, LOA, dropped/attrited and reasons: financial hardship, pregnancy/childcare, health, and harassment.</div>
        </div>

        <div class="page-section">
            <h3>8.5 Report 7: Student GAD Training and Seminar Participation</h3>
            <div class="report-meta"><strong>Purpose:</strong> Show Yes/No GAD training participation per department.<br><strong>Source:</strong> student_information.gad_training_attended, gad_training_details, users.department, personal sex/gender_identity.<br><strong>Filters:</strong> student role; department. <strong>Access:</strong> GAD Administrator only.</div>
            <table class="sex-table"><thead><tr><th>Category</th><th>Male (f / %)</th><th>Female (f / %)</th><th>Total (N)</th><th>Lesbian</th><th>Gay</th><th>Bisexual</th><th>Transsexual</th><th>Cisgender/Heterosexual</th><th>Non-Binary</th><th>Prefer Not to Say</th></tr></thead>
            @include('admin.partials.gad-sex-disaggregation-table', ['rows' => $sexDisaggregatedData['reports'][7]])</table>
            <div class="data-note"><strong>Data logic:</strong> Yes/No normalized from gad_training_attended. f = distinct students; row % and N follow the standard rule. Small-cell rule applies.</div>
        </div>

        <h2>{{ 9 + $sectionOffset }}. Employee Reports</h2>
        <div class="report-meta"><strong>Coverage Period:</strong> {{ $data['period_label'] }} ({{ $data['start_date'] }} – {{ $data['end_date'] }})</div>
        <div class="page-section">
            <h3>9.1 Report 8: Appointment Status and Salary Grade</h3>
            <div class="report-meta"><strong>Purpose:</strong> Compare appointment status and salary grade across teaching and non-teaching employees.<br><strong>Source:</strong> work_experience.appointment_status/grade_step, users.employee_category/department, personal sex/gender_identity.<br><strong>Filters:</strong> employee role. <strong>Access:</strong> GAD Administrator only.</div>
            <table class="sex-table"><thead><tr><th>Category</th><th>Male (f / %)</th><th>Female (f / %)</th><th>Total (N)</th><th>Lesbian</th><th>Gay</th><th>Bisexual</th><th>Transsexual</th><th>Cisgender/Heterosexual</th><th>Non-Binary</th><th>Prefer Not to Say</th></tr></thead>
            @include('admin.partials.gad-sex-disaggregation-table', ['rows' => $sexDisaggregatedData['reports'][8]])</table>
            <div class="data-note"><strong>Data logic:</strong> Rows include the appointment status and salary grade/step recorded for each employee; individual salary amounts are excluded. f, row % and N use Male/Female counts; gender cells below 5 are suppressed.</div>
        </div>
        <div class="page-section">
            <h3>9.2 Report 9: Educational Attainment and Civil Service Eligibility</h3>
            <div class="report-meta"><strong>Purpose:</strong> Disaggregate educational attainment and eligibility credentials.<br><strong>Source:</strong> employee_education.degree, civil_service_eligibility.career, personal sex/gender_identity.<br><strong>Filters:</strong> employee role. <strong>Access:</strong> GAD Administrator only.</div>
            <table class="sex-table"><thead><tr><th>Category</th><th>Male (f / %)</th><th>Female (f / %)</th><th>Total (N)</th><th>Lesbian</th><th>Gay</th><th>Bisexual</th><th>Transsexual</th><th>Cisgender/Heterosexual</th><th>Non-Binary</th><th>Prefer Not to Say</th></tr></thead>
            @include('admin.partials.gad-sex-disaggregation-table', ['rows' => $sexDisaggregatedData['reports'][9]])</table>
            <div class="data-note"><strong>Data logic:</strong> Group by degree or eligibility record, then Sex and Gender/SOGIESC. f, row % and N use the Male/Female distribution.</div>
        </div>
        <div class="page-section">
            <h3>9.3 Report 10: Vulnerable Sectors and Household Profile</h3>
            <div class="report-meta"><strong>Purpose:</strong> Show solo-parent employees, PWD categories, income brackets, and number of children.<br><strong>Source:</strong> personal_information.disabilities, family_background.family_type/monthly_income/children_total.<br><strong>Filters:</strong> employee role. <strong>Access:</strong> GAD Administrator only.</div>
            <table class="sex-table"><thead><tr><th>Category</th><th>Male (f / %)</th><th>Female (f / %)</th><th>Total (N)</th><th>Lesbian</th><th>Gay</th><th>Bisexual</th><th>Transsexual</th><th>Cisgender/Heterosexual</th><th>Non-Binary</th><th>Prefer Not to Say</th></tr></thead>
            @include('admin.partials.gad-sex-disaggregation-table', ['rows' => $sexDisaggregatedData['reports'][10]])</table>
            <div class="data-note"><strong>Data logic:</strong> Income rows: 10,000 or below; 10,001–29,999; 30,000–49,999; 50,000 and above. Children are grouped into 0, 1–2, or 3 and above.</div>
        </div>
        <div class="page-section">
            <h3>9.4 Report 11: Leadership, Civic Involvement and Organization Membership</h3>
            <div class="report-meta"><strong>Purpose:</strong> Summarize CODI, GFPS, BAC, HRMPSB, civic organizations, and memberships.<br><strong>Source:</strong> voluntary_work, other_information.emp_membership.<br><strong>Filters:</strong> employee role. <strong>Access:</strong> GAD Administrator only.</div>
            <table class="sex-table"><thead><tr><th>Category</th><th>Male (f / %)</th><th>Female (f / %)</th><th>Total (N)</th><th>Lesbian</th><th>Gay</th><th>Bisexual</th><th>Transsexual</th><th>Cisgender/Heterosexual</th><th>Non-Binary</th><th>Prefer Not to Say</th></tr></thead>
            @include('admin.partials.gad-sex-disaggregation-table', ['rows' => $sexDisaggregatedData['reports'][11]])</table>
            <div class="data-note"><strong>Data logic:</strong> Count employees in the listed committee/organization categories without outputting individual membership text. Leadership role details are unavailable when not explicitly recorded.</div>
        </div>
        <div class="page-section">
            <h3>9.5 Report 12: GAD Training Hours and Capacity Building</h3>
            <div class="report-meta"><strong>Purpose:</strong> Report GAD training totals by department without individual names.<br><strong>Source:</strong> gad_training.title/hours/from/to, users.department.<br><strong>Filters:</strong> employee role and training start/end date within the Coverage Period. <strong>Access:</strong> GAD Administrator only.</div>
            <table class="sex-table"><thead><tr><th>Category</th><th>Male (f / %)</th><th>Female (f / %)</th><th>Total (N)</th><th>Lesbian</th><th>Gay</th><th>Bisexual</th><th>Transsexual</th><th>Cisgender/Heterosexual</th><th>Non-Binary</th><th>Prefer Not to Say</th></tr></thead>
            @include('admin.partials.gad-sex-disaggregation-table', ['rows' => $sexDisaggregatedData['reports'][12]])</table>
            <table class="report-table"><thead><tr><th>Department</th><th>Total Training Hours</th><th>Number of Trainings</th><th>Training Titles</th></tr></thead><tbody>
                @forelse($sexDisaggregatedData['training_summary'] as $trainingSummary)
                    <tr><td>{{ $trainingSummary['department'] }}</td><td>{{ $trainingSummary['hours'] }}</td><td>{{ $trainingSummary['training_count'] }}</td><td>{{ $trainingSummary['titles'] ? implode('; ', $trainingSummary['titles']) : 'Not reported' }}</td></tr>
                @empty
                    <tr><td colspan="4">No training records with dates in this period.</td></tr>
                @endforelse
            </tbody></table>
            <div class="data-note"><strong>Data logic:</strong> Training records are date-filtered; title rows are aggregate and contain no staff names. Records without parseable dates are excluded from this period.</div>
        </div>

        <h2>{{ 10 + $sectionOffset }}. Safe Spaces and Incident Analytics</h2>
        <div class="report-meta"><strong>Coverage Period:</strong> {{ $data['period_label'] }} ({{ $data['start_date'] }} – {{ $data['end_date'] }})</div>
        <div class="page-section">
            <h3>10.1 Report 13: Departmental Incident Volume and Ranking</h3>
            <div class="report-meta"><strong>Purpose:</strong> Rank departments by incident volume and resolution rate.<br><strong>Source:</strong> reports.nature/location/status/priority/created_at and linked users.department/role.<br><strong>Filters:</strong> reports.created_at within Coverage Period. <strong>Access:</strong> GAD Administrator only.</div>
            <table class="sex-table"><thead><tr><th>Department</th><th>Student Reports</th><th>Employee Reports</th><th>Total (N)</th><th>% Share</th><th>Resolution Rate</th><th>Rank</th><th>Pending</th><th>Resolved</th><th>Priority Counts</th></tr></thead><tbody>
                @forelse($sexDisaggregatedData['incident_departments'] as $index => $incidentDepartment)
                    <tr><td>{{ $incidentDepartment['department'] }}</td><td>{{ $incidentDepartment['student'] }}</td><td>{{ $incidentDepartment['employee'] }}</td><td>{{ $incidentDepartment['total'] }}</td><td>{{ $incidentDepartment['share'] }}%</td><td>{{ $incidentDepartment['resolution_rate'] }}%</td><td>{{ $index + 1 }}</td><td>{{ $incidentDepartment['pending'] }}</td><td>{{ $incidentDepartment['resolved'] }}</td><td>{{ $incidentDepartment['priority_summary'] }}</td></tr>
                @empty
                    <tr><td colspan="10">No records for this period</td></tr>
                @endforelse
            </tbody></table>
            <div class="data-note"><strong>Data logic:</strong> % Share = department Total / all incident N × 100.0. Resolution Rate = Resolved ÷ Total × 100.0. Sorted highest to lowest. Unlinked incidents are grouped as Not disclosed.</div>
        </div>
        <div class="page-section">
            <h3>10.2 Report 14: Department x Incident Type Matrix</h3>
            <div class="report-meta"><strong>Purpose:</strong> Matrix departments against incident types, location, status, and priority.<br><strong>Source:</strong> reports.nature/location/status/priority/created_at and linked users.department.<br><strong>Filters:</strong> reports.created_at within Coverage Period. <strong>Access:</strong> GAD Administrator only.</div>
            <table class="sex-table"><thead><tr><th>Department</th><th>Incident Type</th><th>Count</th><th>#1 Type per Department</th><th>Most Frequent Location</th><th>Pending</th><th>Resolved</th><th>Priority Counts</th></tr></thead><tbody>
                @forelse($sexDisaggregatedData['incident_types'] as $incidentType)
                    <tr><td>{{ $incidentType['department'] }}</td><td>{{ $incidentType['nature'] }}</td><td>{{ $incidentType['count'] }}</td><td>{{ $incidentType['top_type'] }}</td><td>{{ $incidentType['top_location'] }}</td><td>{{ $incidentType['pending'] }}</td><td>{{ $incidentType['resolved'] }}</td><td>{{ $incidentType['priority_summary'] }}</td></tr>
                @empty
                    <tr><td colspan="8">No records for this period</td></tr>
                @endforelse
            </tbody></table>
            <div class="data-note"><strong>Data logic:</strong> Incident types are grouped from reports.nature, including all database values. Top type and location are selected by highest frequency. Status and high-priority counts are grouped per department/type.</div>
        </div>
        <div class="page-section">
            <h3>10.3 Table 4: Sex-Disaggregated Cyber-Harassment Cases by Department</h3>
            <div class="report-meta"><strong>Purpose:</strong> Summarize cyber-harassment reports by department, reporter sex, and case status.<br><strong>Source:</strong> reports.nature, reports.status, reports.user_id and linked users.department/personal_information.sex.<br><strong>Filters:</strong> cyber-harassment reports created within the Coverage Period. <strong>Access:</strong> GAD Administrator only.</div>
            <table class="sex-table">
                <thead>
                    <tr><th>Department</th><th colspan="2">Total Reporters (M/F)</th><th>Cyber-Harassment Reports</th><th colspan="4">Case Status Counts</th></tr>
                    <tr><th></th><th>Male</th><th>Female</th><th>Total Count</th><th>Pending</th><th>In-Progress</th><th>Resolved</th><th>Other Status</th></tr>
                </thead>
                <tbody>
                    @forelse($sexDisaggregatedData['cyber_harassment_departments'] as $department)
                        <tr style="{{ $department['department'] === 'UNIVERSITY TOTAL' ? 'font-weight: bold; background: #ede9fe;' : '' }}">
                            <td>{{ $department['department'] }}</td>
                            <td>{{ $department['male'] }}</td>
                            <td>{{ $department['female'] }}</td>
                            <td>{{ $department['total'] }}</td>
                            <td>{{ $department['pending'] }}</td>
                            <td>{{ $department['in_progress'] }}</td>
                            <td>{{ $department['resolved'] }}</td>
                            <td>{{ $department['other'] }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="8">No cyber-harassment reports for this period.</td></tr>
                    @endforelse
                </tbody>
            </table>
            <div class="data-note"><strong>Data note:</strong> Male/Female counts are distinct linked reporters based on users' current department and sex fields; case counts include every matching incident record in the selected Coverage Period. Missing reporter sex is not included in the M/F columns; unlinked or missing department values appear as Not disclosed. Other Status contains statuses outside Pending, In-Progress/Under Review, and Resolved.</div>
        </div>
        <div class="page-section">
            <h3>10.4 Report 15: Incident Disaggregation by Sex, Gender and Campus Role</h3>
            <div class="report-meta"><strong>Purpose:</strong> Aggregate complainants/respondents by Sex, Gender/SOGIESC, and campus role.<br><strong>Source:</strong> reports.user_id/gender, users.personal_information.sex/gender_identity/account_type/role.<br><strong>Filters:</strong> reports.created_at within Coverage Period. <strong>Access:</strong> GAD Administrator only.</div>
            <table class="sex-table"><thead><tr><th>Category</th><th>Male (f / %)</th><th>Female (f / %)</th><th>Total (N)</th><th>Lesbian</th><th>Gay</th><th>Bisexual</th><th>Transsexual</th><th>Cisgender/Heterosexual</th><th>Non-Binary</th><th>Prefer Not to Say</th></tr></thead>
            @include('admin.partials.gad-sex-disaggregation-table', ['rows' => $sexDisaggregatedData['reports'][15]])</table>
            <div class="data-note"><strong>Data logic:</strong> Complainant Sex/Gender comes from linked-user fields or reports.gender fallback. Respondent Sex/Gender and Student-to-Student, Employee-to-Student, Employee-to-Employee relationship fields are pending; no respondent identity is inferred.</div>
        </div>

        <h2>{{ 11 + $sectionOffset }}. Suggestions and Satisfaction by Sex and Gender</h2>
        <div class="report-meta"><strong>Coverage Period:</strong> {{ $data['period_label'] }} ({{ $data['start_date'] }} – {{ $data['end_date'] }})</div>
        <div class="page-section">
            <h3>11.1 Suggestions by Category and Sex/Gender</h3>
            <div class="report-meta"><strong>Purpose:</strong> Disaggregate suggestions and community feedback.<br><strong>Source:</strong> suggestions.category/is_anonymous/user_id/created_at and linked users' Sex/Gender.<br><strong>Filters:</strong> suggestions.created_at within Coverage Period. <strong>Access:</strong> GAD Administrator only.</div>
            <table class="sex-table"><thead><tr><th>Category</th><th>Male (f / %)</th><th>Female (f / %)</th><th>Total (N)</th><th>Lesbian</th><th>Gay</th><th>Bisexual</th><th>Transsexual</th><th>Cisgender/Heterosexual</th><th>Non-Binary</th><th>Prefer Not to Say</th></tr></thead>
            @include('admin.partials.gad-sex-disaggregation-table', ['rows' => $sexDisaggregatedData['suggestion_rows']])</table>
            <table class="report-table"><thead><tr><th>Suggestion Category</th><th>Not Disclosed (Anonymous / Unlinked) f</th></tr></thead><tbody>
                @forelse($sexDisaggregatedData['anonymous_suggestion_rows'] as $anonymousSuggestion)
                    <tr><td>{{ $anonymousSuggestion['category'] }}</td><td>{{ $anonymousSuggestion['count'] }}</td></tr>
                @empty
                    <tr><td colspan="2">No anonymous or unlinked suggestions for this period.</td></tr>
                @endforelse
            </tbody></table>
            <div class="data-note"><strong>Data logic:</strong> Group by stored category. Anonymous or unlinked submissions appear as Not disclosed and are never linked to a name. Male/Female percentages use the Sex values actually disclosed for each category.</div>
        </div>
        <div class="page-section">
            <h3>11.2 Customer Satisfaction and Survey Findings by Sex and Gender</h3>
            <div class="report-meta"><strong>Purpose:</strong> Report average and favorable responses for groups with at least 5 valid responses.<br><strong>Source:</strong> customer_satisfaction_surveys.responses/created_at/user_id and linked users' Sex/Gender.<br><strong>Filters:</strong> Coverage Period; display only groups with N ≥ 5. <strong>Access:</strong> GAD Administrator only.</div>
            <table class="sex-table"><thead><tr><th>Question</th><th>Sex</th><th>Gender/SOGIESC</th><th>Responses (N)</th><th>Average</th><th>Favorable</th></tr></thead><tbody>
                @forelse($sexDisaggregatedData['satisfaction_rows'] as $satisfactionRow)
                    <tr><td>{{ $satisfactionRow['question'] }}</td><td>{{ $satisfactionRow['sex'] }}</td><td>{{ $satisfactionRow['gender'] }}</td><td>{{ $satisfactionRow['responses'] }}</td><td>{{ $satisfactionRow['average'] }} / 4</td><td>{{ $satisfactionRow['favorable'] }}%</td></tr>
                @empty
                    <tr><td colspan="6">No groups have at least 5 valid responses for this period.</td></tr>
                @endforelse
            </tbody></table>
            <div class="pie-grid">
                @foreach($sexDisaggregatedData['satisfaction_rows'] as $satisfactionRow)
                    <div class="pie-card">
                        <strong>{{ $satisfactionRow['question'] }} — {{ $satisfactionRow['sex'] }} / {{ $satisfactionRow['gender'] }}</strong>
                        @if($satisfactionRow['pie_image'])
                            <img src="data:image/png;base64,{{ $satisfactionRow['pie_image'] }}" alt="Survey response distribution">
                        @endif
                        <div class="pie-legend">
                            <span><i style="background:#ef4444"></i>Strongly disagree ({{ $satisfactionRow['counts'][1] }})</span>
                            <span><i style="background:#f59e0b"></i>Disagree ({{ $satisfactionRow['counts'][2] }})</span>
                            <span><i style="background:#84cc16"></i>Agree ({{ $satisfactionRow['counts'][3] }})</span>
                            <span><i style="background:#22c55e"></i>Strongly agree ({{ $satisfactionRow['counts'][4] }})</span>
                        </div>
                    </div>
                @endforeach
            </div><div style="clear:both"></div>
            <div class="data-note"><strong>Data logic:</strong> Average = mean of valid ratings; favorable % = responses rated 3 or 4 ÷ valid responses × 100.0. Groups below N = 5 are omitted. Only aggregate outputs are shown.</div>
        </div>

        <h2>{{ 12 + $sectionOffset }}. Findings and Recommendations (Automated)</h2>
        <div class="report-meta"><strong>Coverage Period:</strong> {{ $data['period_label'] }} ({{ $data['start_date'] }} – {{ $data['end_date'] }})</div>
        @php
            $largestStudentGroup = collect($sexDisaggregatedData['reports'][1])->sortByDesc('total')->first();
            $highestIncidentDepartment = collect($sexDisaggregatedData['incident_departments'])->first();
            $lowSatisfactionGroup = collect($sexDisaggregatedData['satisfaction_rows'])->first(fn ($row) => (float) $row['favorable'] < 75);
        @endphp
        <table class="sex-table"><thead><tr><th>Database-driven finding</th><th>Action prompt rule</th></tr></thead><tbody>
            @if($largestStudentGroup)
                <tr><td>The largest student population/status group is {{ $largestStudentGroup['category'] }} ({{ $largestStudentGroup['total'] }}; {{ $largestStudentGroup['male_percent'] }}% Male, {{ $largestStudentGroup['female_percent'] }}% Female).</td><td>Use the group breakdown for planning; do not infer causes from counts alone.</td></tr>
            @endif
            @if($highestIncidentDepartment)
                <tr><td>{{ $highestIncidentDepartment['department'] }} has the highest incident share at {{ $highestIncidentDepartment['share'] }}%.</td><td>Prompt GAD Office review when incident share is at least 20%; review context and avoid causal claims.</td></tr>
                <tr><td>{{ $highestIncidentDepartment['department'] }} has a resolution rate of {{ $highestIncidentDepartment['resolution_rate'] }}%.</td><td>Prompt workflow review when the rate is below 75% and the department has at least 5 incidents.</td></tr>
            @endif
            @if($lowSatisfactionGroup)
                <tr><td>Favorable survey responses are {{ $lowSatisfactionGroup['favorable'] }}% for {{ $lowSatisfactionGroup['question'] }} ({{ $lowSatisfactionGroup['sex'] }}; {{ $lowSatisfactionGroup['gender'] }}).</td><td>Prompt review of service access and safety when favorable responses are below 75%; the displayed group meets N ≥ 5.</td></tr>
            @endif
            @if(!$largestStudentGroup && !$highestIncidentDepartment && !$lowSatisfactionGroup)
                <tr><td colspan="2">No findings can be generated from the available records for this period.</td></tr>
            @endif
        </tbody></table>
        <div class="data-note">Findings are generated from the database aggregates above. Recommendation thresholds are prompts for administrator review, not conclusions about actual causes or outcomes.</div>

        <h2>{{ 13 + $sectionOffset }}. Annex: Data Dictionary and Traceability</h2>
        <div class="report-meta"><strong>Coverage Period:</strong> {{ $data['period_label'] }} ({{ $data['start_date'] }} – {{ $data['end_date'] }})</div>
        <table class="sex-table"><thead><tr><th>Field</th><th>Source Form</th><th>Database Column Placeholder</th><th>Allowed Values</th><th>Report</th></tr></thead><tbody>
            <tr><td>Sex</td><td>Student / Faculty and Non-Teaching Information Sheet</td><td>@{{users.personal_information.sex}}</td><td>Male; Female; Prefer Not to Say</td><td>7-12</td></tr>
            <tr><td>Gender/SOGIESC</td><td>Student / Faculty and Non-Teaching Information Sheet</td><td>@{{users.personal_information.gender_identity}}</td><td>Lesbian; Gay; Bisexual; Transsexual; Cisgender/Heterosexual; Non-Binary; Prefer Not to Say</td><td>7-12</td></tr>
            <tr><td>Department / Office</td><td>System-generated account field</td><td>@{{users.department}}</td><td>Database-defined departments/offices</td><td>7-12</td></tr>
            <tr><td>Account Type / Role</td><td>System-generated registration field</td><td>@{{users.account_type}} / @{{users.role}}</td><td>Student; Employee; Administrator/coordinator roles</td><td>7-12</td></tr>
            <tr><td>Registration Date</td><td>System-generated</td><td>@{{users.created_at}}</td><td>Timestamp</td><td>7</td></tr>
            <tr><td>Student program / major and year level</td><td>Student Information Sheet</td><td>@{{users.department}} / @{{users.student_information.year_level}}</td><td>Stored program/major and year-level values</td><td>7-8</td></tr>
            <tr><td>Student family, work, disability, income, housing and financing profile</td><td>Student Information Sheet</td><td>@{{users.family_background.*}} / @{{users.personal_information.disabilities}} / @{{users.student_information.financing_sources}}</td><td>Stored form values; work and income categories normalized for aggregation</td><td>8</td></tr>
            <tr><td>Feeder school and honors</td><td>Student Information Sheet</td><td>@{{users.student_information.school_type}} / @{{users.student_information.achievements}}</td><td>Public; Private; stored achievement values</td><td>8</td></tr>
            <tr><td>Retention / LOA / attrition reason</td><td>Pending data source</td><td>Field not currently available</td><td>Financial hardship; pregnancy/childcare; health; harassment; other</td><td>8</td></tr>
            <tr><td>Student GAD training</td><td>Student Information Sheet</td><td>@{{users.student_information.gad_training_attended}}</td><td>Yes; No; Not reported</td><td>8</td></tr>
            <tr><td>Employee appointment and salary grade/step</td><td>Faculty and Non-Teaching Information Sheet</td><td>@{{users.work_experience.*}}</td><td>Permanent; Temporary; Contractual/COS; Job Order; Part-Time; stored grade/step values</td><td>9</td></tr>
            <tr><td>Employee education and civil service eligibility</td><td>Faculty and Non-Teaching Information Sheet</td><td>@{{users.employee_education}} / @{{users.civil_service_eligibility}}</td><td>Stored attainment and eligibility values, including Career Service, RA 1080, CES, CSEE</td><td>9</td></tr>
            <tr><td>Employee household and disability profile</td><td>Faculty and Non-Teaching Information Sheet</td><td>@{{users.family_background.*}} / @{{users.personal_information.disabilities}}</td><td>Stored values; income normalized to report brackets</td><td>9</td></tr>
            <tr><td>Leadership, civic involvement and memberships</td><td>Faculty and Non-Teaching Information Sheet</td><td>@{{users.other_information.emp_membership}} / @{{users.voluntary_work}}</td><td>Stored organizations and involvement values</td><td>9</td></tr>
            <tr><td>Employee GAD training hours and titles</td><td>Faculty and Non-Teaching Information Sheet</td><td>@{{users.gad_training}}</td><td>Stored training titles and hours</td><td>9</td></tr>
            <tr><td>Incident Nature</td><td>Safe Spaces incident form</td><td>@{{reports.nature}}</td><td>Groping; Stalking; Catcalling; Cyber-harassment; Sexist Remarks or Jokes; Gender-based Discrimination; other database values</td><td>10, 15</td></tr>
            <tr><td>Incident Status / Priority</td><td>Safe Spaces incident form / system workflow</td><td>@{{reports.status}} / @{{reports.priority}}</td><td>Database/system allowed values</td><td>10</td></tr>
            <tr><td>Incident respondent Sex/Gender and relationship</td><td>Pending data source</td><td>Respondent fields not currently available</td><td>Student-to-Student; Employee-to-Student; Employee-to-Employee</td><td>10</td></tr>
            <tr><td>Suggestion Category</td><td>Suggestion form</td><td>@{{suggestions.category}}</td><td>GAD Programs and Services; Gender Equality and Inclusion; Gender-Based Violence Prevention; Safe Spaces and Student Welfare; other database values</td><td>11</td></tr>
            <tr><td>Suggestion anonymity and author link</td><td>System-generated suggestion fields</td><td>@{{suggestions.is_anonymous}} / @{{suggestions.user_id}}</td><td>Anonymous / linked / unlinked; anonymous or unlinked totals reported as Not disclosed</td><td>11</td></tr>
            <tr><td>Survey Rating / Responses</td><td>Customer Satisfaction Survey</td><td>@{{surveys.rating}} / @{{surveys.responses}}</td><td>Rating and question response values from the system</td><td>11</td></tr>
        </tbody></table>
        <div class="data-note"><strong>Open items:</strong> 1) Add Cisgender/Heterosexual, Non-Binary, and Prefer Not to Say to both Information Sheet Gender fields and database validation. 2) Add student retention/LOA/attrition source fields. 3) Add respondent Sex/Gender and relationship fields to incident reports. 4) Renumber duplicate Part A section 5. 5) Restrict/mark full-personal-data Part A sections 5 and 6 as a confidential/restricted annex.</div>
    </div>

    <div class="footer">SINAG Safe Space &amp; Participatory Suggestion System • Confidential Administrative Report • {{ $data['generated_at'] }}</div>
</body>
</html>
