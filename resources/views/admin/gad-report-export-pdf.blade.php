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
                <td><div class="number">{{ $data['total_users'] }}</div><div class="label">New Registered Users</div></td>
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

    @if($includeReports)
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
    <h2>2. Incident Reports ({{ $reports->count() }})</h2>
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
    <h2>3. Suggestions &amp; Community Feedback ({{ $suggestions->count() }})</h2>
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

    <h2>{{ $includeReports ? 4 : 3 }}. Customer Satisfaction Survey ({{ $satisfactionSurveys->count() }})</h2>
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

    <h2>{{ $includeReports ? 5 : 4 }}. Survey Findings</h2>
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
    <h2>5. Student Information Sheets ({{ count($studentProfiles) }})</h2>
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

    <h2>6. Faculty and Non-Teaching Information Sheets ({{ count($employeeProfiles) }})</h2>
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
        <h2>7. Population, New Users and Gender</h2>
        <div class="report-meta"><strong>Coverage Period:</strong> {{ $data['period_label'] }} ({{ $data['start_date'] }} – {{ $data['end_date'] }})</div>

        <div class="page-section">
            <h3>7.1 Report 1: Student Population, New Users and Gender per Department/Program</h3>
            <div class="report-meta"><strong>Purpose:</strong> Show student population and newly registered accounts by department/program and Sex/Gender/SOGIESC.<br>
            <strong>Source:</strong> users.department, users.account_type/role, users.account_status, users.created_at, users.personal_information.sex, users.personal_information.gender_identity, users.student_information.<br>
            <strong>Filters:</strong> account_type/role = student; population can include existing accounts; new users require created_at within the Coverage Period. <strong>Access:</strong> GAD Administrator only.</div>
            <table class="sex-table"><thead><tr><th>Category</th><th>Male (f / %)</th><th>Female (f / %)</th><th>Total (N)</th><th>Lesbian</th><th>Gay</th><th>Bisexual</th><th>Transsexual</th><th>Cisgender/Heterosexual</th><th>Non-Binary</th><th>Prefer Not to Say</th></tr></thead>
            <tbody>
                @if($sexDisaggregatedData['student_count'] === 0)
                    <tr><td colspan="11">No records for this period</td></tr>
                @else
                    <tr><td>@{{department/program}}</td><td>@{{f}} / @{{%}}</td><td>@{{f}} / @{{%}}</td><td>@{{N}}</td><td>@{{f}}</td><td>@{{f}}</td><td>@{{f}}</td><td>@{{f}}</td><td>@{{f}}</td><td>@{{f}}</td><td>@{{f}}</td></tr>
                @endif
            </tbody></table>
            <div class="data-note"><strong>Data logic:</strong> Group Sex first (Male, Female, then Prefer Not to Say/unknown), then Gender/SOGIESC. f = distinct student count. Row percentages = f / row N × 100.0; N = Male + Female + undisclosed valid Sex values. Report each account status (Active/Rejected/Pending) separately. Sex/Gender cells with f &lt; 5 display as "&lt;5". Optional bar chart: population and new-user counts per department/program.</div>
            <div class="data-note"><strong>Data note:</strong> Program/major is from users.department or users.student_information; if absent, mark Pending data source.</div>
        </div>

        <div class="page-section">
            <h3>7.2 Report 2: Employee Population, New Users and Gender per Department/Office</h3>
            <div class="report-meta"><strong>Purpose:</strong> Show teaching and non-teaching employee population and registrations by office and Sex/Gender/SOGIESC.<br>
            <strong>Source:</strong> users.department, users.account_type/role, users.employee_category, users.account_status, users.created_at, users.personal_information.sex, users.personal_information.gender_identity.<br>
            <strong>Filters:</strong> account_type/role = employee, split by employee_category Teaching/Non-Teaching. <strong>Access:</strong> GAD Administrator only.</div>
            <table class="sex-table"><thead><tr><th>Category</th><th>Male (f / %)</th><th>Female (f / %)</th><th>Total (N)</th><th>Lesbian</th><th>Gay</th><th>Bisexual</th><th>Transsexual</th><th>Cisgender/Heterosexual</th><th>Non-Binary</th><th>Prefer Not to Say</th></tr></thead>
            <tbody>@if($sexDisaggregatedData['employee_count'] === 0)<tr><td colspan="11">No records for this period</td></tr>@else<tr><td>@{{department/office}}</td><td>@{{f}} / @{{%}}</td><td>@{{f}} / @{{%}}</td><td>@{{N}}</td><td>@{{f}}</td><td>@{{f}}</td><td>@{{f}}</td><td>@{{f}}</td><td>@{{f}}</td><td>@{{f}}</td><td>@{{f}}</td></tr>@endif</tbody></table>
            <div class="data-note"><strong>Data logic:</strong> Filter by employee role and employee_category. f = distinct employee count; row % = f/N × 100.0; N = sum of Male and Female and disclosed/undisclosed valid Sex values. Status counts use Active/Rejected/Pending. Any Gender/SOGIESC cell f &lt; 5 displays "&lt;5".</div>
        </div>

        <h2>8. Student Reports</h2>
        <div class="report-meta"><strong>Coverage Period:</strong> {{ $data['period_label'] }} ({{ $data['start_date'] }} – {{ $data['end_date'] }})</div>

        <div class="page-section">
            <h3>8.1 Report 3: Course/Major and Year Level by Sex and Gender</h3>
            <div class="report-meta"><strong>Purpose:</strong> Compare course/major and year-level enrollment by Sex and Gender/SOGIESC.<br><strong>Source:</strong> users.student_information.year_level, users.student_information.course/major (or users.department), users.personal_information.sex, users.personal_information.gender_identity.<br><strong>Filters:</strong> student role; existing population and optional Coverage Period for new users. <strong>Access:</strong> GAD Administrator only.</div>
            <table class="sex-table"><thead><tr><th>Category</th><th>Male (f / %)</th><th>Female (f / %)</th><th>Total (N)</th><th>Lesbian</th><th>Gay</th><th>Bisexual</th><th>Transsexual</th><th>Cisgender/Heterosexual</th><th>Non-Binary</th><th>Prefer Not to Say</th></tr></thead><tbody>@if($sexDisaggregatedData['student_count'] === 0)<tr><td colspan="11">No records for this period</td></tr>@else<tr><td>@{{course/major/year}}</td><td>@{{f}} / @{{%}}</td><td>@{{f}} / @{{%}}</td><td>@{{N}}</td><td>@{{f}}</td><td>@{{f}}</td><td>@{{f}}</td><td>@{{f}}</td><td>@{{f}}</td><td>@{{f}}</td><td>@{{f}}</td></tr>@endif</tbody></table>
            <div class="data-note"><strong>Data logic:</strong> First group by course/major and year level, then Sex, then Gender/SOGIESC. f = distinct students; row percentages sum to 100.0%; N = sum of row parts. Chart: bar chart by course/major and Sex.</div>
        </div>

        <div class="page-section">
            <h3>8.2 Report 4: Vulnerable Sectors and Socio-Economic Profile</h3>
            <div class="report-meta"><strong>Purpose:</strong> Report solo-parent families, working students, PWD categories, monthly family income, home ownership, and schooling financiers.<br><strong>Source:</strong> personal_information.disabilities/civil_status; family_background.family_type/monthly_income/home_ownership_type; student_information.financing_sources/financing_other_work.<br><strong>Filters:</strong> student role; aggregate only. <strong>Access:</strong> GAD Administrator only.</div>
            <table class="sex-table"><thead><tr><th>Category</th><th>Male (f / %)</th><th>Female (f / %)</th><th>Total (N)</th><th>Lesbian</th><th>Gay</th><th>Bisexual</th><th>Transsexual</th><th>Cisgender/Heterosexual</th><th>Non-Binary</th><th>Prefer Not to Say</th></tr></thead><tbody>@if($sexDisaggregatedData['student_count'] === 0)<tr><td colspan="11">No records for this period</td></tr>@else<tr><td>@{{sector/profile category}}</td><td>@{{f}} / @{{%}}</td><td>@{{f}} / @{{%}}</td><td>@{{N}}</td><td>@{{f}}</td><td>@{{f}}</td><td>@{{f}}</td><td>@{{f}}</td><td>@{{f}}</td><td>@{{f}}</td><td>@{{f}}</td></tr>@endif</tbody></table>
            <div class="data-note"><strong>Data logic:</strong> Create separate category rows for solo-parent, work type, PWD category, monthly income bracket, home ownership, and financing source. f = distinct students matching category; row % = f/N × 100.0; N = sum of displayed Sex parts. Gender cells f &lt; 5 display "&lt;5".</div>
        </div>

        <div class="page-section">
            <h3>8.3 Report 5: Academic Background and Honors</h3>
            <div class="report-meta"><strong>Purpose:</strong> Summarize public/private feeder schools and academic honors.<br><strong>Source:</strong> student_information.school_type, last_school_attended, achievements, achievement_other_rank; personal sex/gender_identity.<br><strong>Filters:</strong> student role. <strong>Access:</strong> GAD Administrator only.</div>
            <table class="sex-table"><thead><tr><th>Category</th><th>Male (f / %)</th><th>Female (f / %)</th><th>Total (N)</th><th>Lesbian</th><th>Gay</th><th>Bisexual</th><th>Transsexual</th><th>Cisgender/Heterosexual</th><th>Non-Binary</th><th>Prefer Not to Say</th></tr></thead><tbody>@if($sexDisaggregatedData['student_count'] === 0)<tr><td colspan="11">No records for this period</td></tr>@else<tr><td>@{{feeder/honor}}</td><td>@{{f}} / @{{%}}</td><td>@{{f}} / @{{%}}</td><td>@{{N}}</td><td>@{{f}}</td><td>@{{f}}</td><td>@{{f}}</td><td>@{{f}}</td><td>@{{f}}</td><td>@{{f}}</td><td>@{{f}}</td></tr>@endif</tbody></table>
            <div class="data-note"><strong>Data logic:</strong> Rows include Public, Private, State University/Other; and Valedictorian, Salutatorian, Honorable Mention, Other rank. f = distinct students; row % and N follow the standard rule. Pie chart optional for feeder-school distribution.</div>
        </div>

        <div class="page-section">
            <h3>8.4 Report 6: Retention, LOA and Attrition Reasons</h3>
            <div class="report-meta"><strong>Purpose:</strong> Analyze retention, leave of absence, and attrition reasons by Sex and Gender/SOGIESC.<br><strong>Source:</strong> Pending source: retention_status, LOA_status, attrition_reason, and effective date fields.<br><strong>Filters:</strong> student role; coverage period. <strong>Access:</strong> GAD Administrator only.</div>
            <table class="sex-table"><thead><tr><th>Category</th><th>Male (f / %)</th><th>Female (f / %)</th><th>Total (N)</th><th>Lesbian</th><th>Gay</th><th>Bisexual</th><th>Transsexual</th><th>Cisgender/Heterosexual</th><th>Non-Binary</th><th>Prefer Not to Say</th></tr></thead><tbody><tr><td>Pending data source</td><td>@{{f}} / @{{%}}</td><td>@{{f}} / @{{%}}</td><td>@{{N}}</td><td>@{{f}}</td><td>@{{f}}</td><td>@{{f}}</td><td>@{{f}}</td><td>@{{f}}</td><td>@{{f}}</td><td>@{{f}}</td></tr></tbody></table>
            <div class="data-note"><strong>Data note:</strong> Marked “Pending data source.” Add student status/history fields for retained, LOA, dropped/attrited and reasons: financial hardship, pregnancy/childcare, health, and harassment.</div>
        </div>

        <div class="page-section">
            <h3>8.5 Report 7: Student GAD Training and Seminar Participation</h3>
            <div class="report-meta"><strong>Purpose:</strong> Show Yes/No GAD training participation per department.<br><strong>Source:</strong> student_information.gad_training_attended, gad_training_details, users.department, personal sex/gender_identity.<br><strong>Filters:</strong> student role; department. <strong>Access:</strong> GAD Administrator only.</div>
            <table class="sex-table"><thead><tr><th>Category</th><th>Male (f / %)</th><th>Female (f / %)</th><th>Total (N)</th><th>Lesbian</th><th>Gay</th><th>Bisexual</th><th>Transsexual</th><th>Cisgender/Heterosexual</th><th>Non-Binary</th><th>Prefer Not to Say</th></tr></thead><tbody>@if($sexDisaggregatedData['student_count'] === 0)<tr><td colspan="11">No records for this period</td></tr>@else<tr><td>@{{department/Yes or No}}</td><td>@{{f}} / @{{%}}</td><td>@{{f}} / @{{%}}</td><td>@{{N}}</td><td>@{{f}}</td><td>@{{f}}</td><td>@{{f}}</td><td>@{{f}}</td><td>@{{f}}</td><td>@{{f}}</td><td>@{{f}}</td></tr>@endif</tbody></table>
            <div class="data-note"><strong>Data logic:</strong> Yes/No normalized from gad_training_attended. f = distinct students; row % and N follow the standard rule. Small-cell rule applies.</div>
        </div>

        <h2>9. Employee Reports</h2>
        <div class="report-meta"><strong>Coverage Period:</strong> {{ $data['period_label'] }} ({{ $data['start_date'] }} – {{ $data['end_date'] }})</div>
        <div class="page-section"><h3>9.1 Report 8: Appointment Status and Salary Grade</h3><div class="report-meta"><strong>Purpose:</strong> Compare appointment status and salary grade across teaching and non-teaching employees.<br><strong>Source:</strong> work_experience.appointment_status/salary/position, users.employee_category/department, personal sex/gender_identity.<br><strong>Filters:</strong> employee role. <strong>Access:</strong> GAD Administrator only.</div><table class="sex-table"><thead><tr><th>Category</th><th>Male (f / %)</th><th>Female (f / %)</th><th>Total (N)</th><th>Lesbian</th><th>Gay</th><th>Bisexual</th><th>Transsexual</th><th>Cisgender/Heterosexual</th><th>Non-Binary</th><th>Prefer Not to Say</th></tr></thead><tbody>@if($sexDisaggregatedData['employee_count'] === 0)<tr><td colspan="11">No records for this period</td></tr>@else<tr><td>@{{appointment/salary/category}}</td><td>@{{f}} / @{{%}}</td><td>@{{f}} / @{{%}}</td><td>@{{N}}</td><td>@{{f}}</td><td>@{{f}}</td><td>@{{f}}</td><td>@{{f}}</td><td>@{{f}}</td><td>@{{f}}</td><td>@{{f}}</td></tr>@endif</tbody></table><div class="data-note"><strong>Data logic:</strong> Rows include Permanent, Temporary, Contractual/COS, Job Order, and Part-Time, split Teaching/Non-Teaching and department. f = distinct employees; row % and N follow the standard rule.</div></div>
        <div class="page-section"><h3>9.2 Report 9: Educational Attainment and Civil Service Eligibility</h3><div class="report-meta"><strong>Purpose:</strong> Disaggregate educational attainment and eligibility credentials.<br><strong>Source:</strong> employee_education, civil_service_eligibility, personal sex/gender_identity.<br><strong>Filters:</strong> employee role. <strong>Access:</strong> GAD Administrator only.</div><table class="sex-table"><thead><tr><th>Category</th><th>Male (f / %)</th><th>Female (f / %)</th><th>Total (N)</th><th>Lesbian</th><th>Gay</th><th>Bisexual</th><th>Transsexual</th><th>Cisgender/Heterosexual</th><th>Non-Binary</th><th>Prefer Not to Say</th></tr></thead><tbody>@if($sexDisaggregatedData['employee_count'] === 0)<tr><td colspan="11">No records for this period</td></tr>@else<tr><td>@{{attainment/eligibility}}</td><td>@{{f}} / @{{%}}</td><td>@{{f}} / @{{%}}</td><td>@{{N}}</td><td>@{{f}}</td><td>@{{f}}</td><td>@{{f}}</td><td>@{{f}}</td><td>@{{f}}</td><td>@{{f}}</td><td>@{{f}}</td></tr>@endif</tbody></table><div class="data-note"><strong>Data logic:</strong> Group by degree/attainment and eligibility (Career Service, RA 1080, CES, CSEE), then Sex, then Gender/SOGIESC. f = distinct employees; row % and N follow the standard rule.</div></div>
        <div class="page-section"><h3>9.3 Report 10: Vulnerable Sectors and Household Profile</h3><div class="report-meta"><strong>Purpose:</strong> Show solo-parent employees, PWD categories, income brackets, and number of children.<br><strong>Source:</strong> personal_information.civil_status/disabilities, family_background.monthly_income/children_total, personal sex/gender_identity.<br><strong>Filters:</strong> employee role. <strong>Access:</strong> GAD Administrator only.</div><table class="sex-table"><thead><tr><th>Category</th><th>Male (f / %)</th><th>Female (f / %)</th><th>Total (N)</th><th>Lesbian</th><th>Gay</th><th>Bisexual</th><th>Transsexual</th><th>Cisgender/Heterosexual</th><th>Non-Binary</th><th>Prefer Not to Say</th></tr></thead><tbody>@if($sexDisaggregatedData['employee_count'] === 0)<tr><td colspan="11">No records for this period</td></tr>@else<tr><td>@{{sector/income/children}}</td><td>@{{f}} / @{{%}}</td><td>@{{f}} / @{{%}}</td><td>@{{N}}</td><td>@{{f}}</td><td>@{{f}}</td><td>@{{f}}</td><td>@{{f}}</td><td>@{{f}}</td><td>@{{f}}</td><td>@{{f}}</td></tr>@endif</tbody></table><div class="data-note"><strong>Data logic:</strong> Income rows: 10,000 or below; 10,000–29,999; 30,000–49,999; 50,000 and above. PWD and household categories use distinct employees; row % and N follow the standard rule.</div></div>
        <div class="page-section"><h3>9.4 Report 11: Leadership, Civic Involvement and Organization Membership</h3><div class="report-meta"><strong>Purpose:</strong> Summarize CODI, GFPS, BAC, HRMPSB, civic organizations, and memberships.<br><strong>Source:</strong> voluntary_work, other_information.emp_membership, work_experience or GAD role fields, personal sex/gender_identity.<br><strong>Filters:</strong> employee role. <strong>Access:</strong> GAD Administrator only.</div><table class="sex-table"><thead><tr><th>Category</th><th>Male (f / %)</th><th>Female (f / %)</th><th>Total (N)</th><th>Lesbian</th><th>Gay</th><th>Bisexual</th><th>Transsexual</th><th>Cisgender/Heterosexual</th><th>Non-Binary</th><th>Prefer Not to Say</th></tr></thead><tbody>@if($sexDisaggregatedData['employee_count'] === 0)<tr><td colspan="11">No records for this period</td></tr>@else<tr><td>@{{role/organization}}</td><td>@{{f}} / @{{%}}</td><td>@{{f}} / @{{%}}</td><td>@{{N}}</td><td>@{{f}}</td><td>@{{f}}</td><td>@{{f}}</td><td>@{{f}}</td><td>@{{f}}</td><td>@{{f}}</td><td>@{{f}}</td></tr>@endif</tbody></table><div class="data-note"><strong>Data logic:</strong> Count distinct employees once per role/organization. Normalize free-text memberships before grouping. f, row %, N, and small-cell suppression follow the standard rule.</div></div>
        <div class="page-section"><h3>9.5 Report 12: GAD Training Hours and Capacity Building</h3><div class="report-meta"><strong>Purpose:</strong> Report GAD training totals by department without individual names.<br><strong>Source:</strong> gad_training.title/hours/date, users.department, personal sex/gender_identity.<br><strong>Filters:</strong> employee role; coverage period from training date if available. <strong>Access:</strong> GAD Administrator only.</div><table class="sex-table"><thead><tr><th>Category</th><th>Male (f / %)</th><th>Female (f / %)</th><th>Total (N)</th><th>Lesbian</th><th>Gay</th><th>Bisexual</th><th>Transsexual</th><th>Cisgender/Heterosexual</th><th>Non-Binary</th><th>Prefer Not to Say</th></tr></thead><tbody>@if($sexDisaggregatedData['employee_count'] === 0)<tr><td colspan="11">No records for this period</td></tr>@else<tr><td>@{{department/training}}</td><td>@{{f}} / @{{%}}</td><td>@{{f}} / @{{%}}</td><td>@{{N}}</td><td>@{{f}}</td><td>@{{f}}</td><td>@{{f}}</td><td>@{{f}}</td><td>@{{f}}</td><td>@{{f}}</td><td>@{{f}}</td></tr>@endif</tbody></table><div class="data-note"><strong>Data logic:</strong> f = participating employee count or training-record count by the agreed rule; N = sum of parts; total hours = sum of numeric hours. List titles only, never names. Optional department bar chart.</div></div>

        <h2>10. Safe Spaces and Incident Analytics</h2>
        <div class="report-meta"><strong>Coverage Period:</strong> {{ $data['period_label'] }} ({{ $data['start_date'] }} – {{ $data['end_date'] }})</div>
        <div class="page-section"><h3>10.1 Report 13: Departmental Incident Volume and Ranking</h3><div class="report-meta"><strong>Purpose:</strong> Rank departments by incident volume and resolution rate.<br><strong>Source:</strong> reports.nature/location/status/priority/created_at/course/gender, users.department/role via reports.user_id.<br><strong>Filters:</strong> reports.created_at within Coverage Period. <strong>Access:</strong> GAD Administrator only.</div><table class="sex-table"><thead><tr><th>Department</th><th>Student Reports</th><th>Employee Reports</th><th>Total (N)</th><th>% Share</th><th>Resolution Rate</th><th>Rank</th><th>Pending</th><th>Resolved</th><th>Priority N</th><th>Highest/Lowest</th></tr></thead><tbody>@if($sexDisaggregatedData['incident_count'] === 0)<tr><td colspan="11">No records for this period</td></tr>@else<tr><td>@{{department}}</td><td>@{{f}}</td><td>@{{f}}</td><td>@{{N}}</td><td>@{{%}}</td><td>@{{resolved/N*100.0}}</td><td>@{{rank}}</td><td>@{{f}}</td><td>@{{f}}</td><td>@{{f}}</td><td>@{{rank label}}</td></tr>@endif</tbody></table><div class="data-note"><strong>Data logic:</strong> % Share = department Total / all incident N × 100.0. Resolution Rate = Resolved ÷ Total × 100.0. Sort Total from highest to lowest.</div></div>
        <div class="page-section"><h3>10.2 Report 14: Department x Incident Type Matrix</h3><div class="report-meta"><strong>Purpose:</strong> Matrix departments against incident types, location, status, and priority.<br><strong>Source:</strong> reports.nature/location/status/priority/created_at and users.department/role.<br><strong>Filters:</strong> reports.created_at within Coverage Period. <strong>Access:</strong> GAD Administrator only.</div><table class="sex-table"><thead><tr><th>Department</th><th>Groping</th><th>Stalking</th><th>Catcalling</th><th>Cyber-harassment</th><th>Sexist Remarks/Jokes</th><th>Gender-based Discrimination</th><th>Other DB Types</th><th>#1 Type</th><th>Top Location</th><th>Pending / Resolved</th></tr></thead><tbody>@if($sexDisaggregatedData['incident_count'] === 0)<tr><td colspan="11">No records for this period</td></tr>@else<tr><td>@{{department}}</td><td>@{{f}}</td><td>@{{f}}</td><td>@{{f}}</td><td>@{{f}}</td><td>@{{f}}</td><td>@{{f}}</td><td>@{{f}}</td><td>@{{type}}</td><td>@{{location}}</td><td>@{{pending}} / @{{resolved}}</td></tr>@endif</tbody></table><div class="data-note"><strong>Data logic:</strong> Include any additional values found in reports.nature. #1 Type = maximum frequency; top location = maximum frequency location; include Pending/Resolved and priority counts.</div></div>
        <div class="page-section"><h3>10.3 Report 15: Incident Disaggregation by Sex, Gender and Campus Role</h3><div class="report-meta"><strong>Purpose:</strong> Aggregate complainants/respondents by Sex, Gender/SOGIESC, and campus role.<br><strong>Source:</strong> reports.user_id/gender/course, users.personal_information.sex/gender_identity/account_type/role.<br><strong>Filters:</strong> reports.created_at within Coverage Period. <strong>Access:</strong> GAD Administrator only.</div><table class="sex-table"><thead><tr><th>Category</th><th>Male (f / %)</th><th>Female (f / %)</th><th>Total (N)</th><th>Lesbian</th><th>Gay</th><th>Bisexual</th><th>Transsexual</th><th>Cisgender/Heterosexual</th><th>Non-Binary</th><th>Prefer Not to Say</th></tr></thead><tbody>@if($sexDisaggregatedData['incident_count'] === 0)<tr><td colspan="11">No records for this period</td></tr>@else<tr><td>@{{complainant/relationship}}</td><td>@{{f}} / @{{%}}</td><td>@{{f}} / @{{%}}</td><td>@{{N}}</td><td>@{{f}}</td><td>@{{f}}</td><td>@{{f}}</td><td>@{{f}}</td><td>@{{f}}</td><td>@{{f}}</td><td>@{{f}}</td></tr>@endif</tbody></table><div class="data-note"><strong>Data logic:</strong> Complainant Sex/Gender comes from linked-user fields or reports.gender fallback. Campus role comes from account type. <strong>Pending data source:</strong> respondent Sex/Gender and structured relationship fields are not currently present.</div></div>

        <h2>11. Suggestions and Satisfaction by Sex and Gender</h2>
        <div class="report-meta"><strong>Coverage Period:</strong> {{ $data['period_label'] }} ({{ $data['start_date'] }} – {{ $data['end_date'] }})</div>
        <div class="page-section"><h3>11.1 Suggestions by Category and Sex/Gender</h3><div class="report-meta"><strong>Purpose:</strong> Disaggregate suggestions and community feedback.<br><strong>Source:</strong> suggestions.category/message/is_anonymous/user_id/created_at, users.personal_information.sex/gender_identity.<br><strong>Filters:</strong> suggestions.created_at within Coverage Period. <strong>Access:</strong> GAD Administrator only.</div><table class="sex-table"><thead><tr><th>Category</th><th>Male (f / %)</th><th>Female (f / %)</th><th>Total (N)</th><th>Lesbian</th><th>Gay</th><th>Bisexual</th><th>Transsexual</th><th>Cisgender/Heterosexual</th><th>Non-Binary</th><th>Prefer Not to Say</th></tr></thead><tbody>@if($sexDisaggregatedData['suggestion_count'] === 0)<tr><td colspan="11">No records for this period</td></tr>@else<tr><td>@{{suggestion category}}</td><td>@{{f}} / @{{%}}</td><td>@{{f}} / @{{%}}</td><td>@{{N}}</td><td>@{{f}}</td><td>@{{f}}</td><td>@{{f}}</td><td>@{{f}}</td><td>@{{f}}</td><td>@{{f}}</td><td>@{{f}}</td></tr>@endif</tbody></table><div class="data-note"><strong>Data logic:</strong> Include GAD Programs and Services, Gender Equality and Inclusion, Gender-Based Violence Prevention, Safe Spaces and Student Welfare, plus other database categories. Anonymous suggestions count as "Not disclosed" and are never linked to a name.</div></div>
        <div class="page-section"><h3>11.2 Customer Satisfaction and Survey Findings by Sex and Gender</h3><div class="report-meta"><strong>Purpose:</strong> Report average rating and favorable response rate where the group has at least 5 responses.<br><strong>Source:</strong> customer_satisfaction_surveys.rating/responses/feedback/user_id/created_at, linked user Sex/Gender/SOGIESC.<br><strong>Filters:</strong> surveys.created_at within Coverage Period; display only groups with N ≥ 5. <strong>Access:</strong> GAD Administrator only.</div><table class="sex-table"><thead><tr><th>Group</th><th>Male Average</th><th>Female Average</th><th>Total (N)</th><th>Lesbian Favorable</th><th>Gay Favorable</th><th>Bisexual Favorable</th><th>Transsexual Favorable</th><th>Cisgender/Heterosexual Favorable</th><th>Non-Binary Favorable</th><th>Prefer Not to Say</th></tr></thead><tbody>@if($sexDisaggregatedData['satisfaction_count'] === 0)<tr><td colspan="11">No records for this period</td></tr>@else<tr><td>@{{survey question/group}}</td><td>@{{average / 5}}</td><td>@{{average / 5}}</td><td>@{{N}}</td><td>@{{%}}</td><td>@{{%}}</td><td>@{{%}}</td><td>@{{%}}</td><td>@{{%}}</td><td>@{{%}}</td><td>@{{%}}</td></tr>@endif</tbody></table><div class="data-note"><strong>Data logic:</strong> Average = mean of valid ratings; favorable % = favorable responses ÷ valid responses × 100.0. Suppress pie/bar charts for groups below N = 5. Small-cell rule applies.</div></div>

        <h2>12. Findings and Recommendations (Automated)</h2>
        <div class="report-meta"><strong>Coverage Period:</strong> {{ $data['period_label'] }} ({{ $data['start_date'] }} – {{ $data['end_date'] }})</div>
        <table class="sex-table"><thead><tr><th>Template</th><th>Rule</th></tr></thead><tbody>
            <tr><td>The largest group in @{{category}} is @{{value}} (@{{%}}).</td><td>Max category frequency; output only if N is sufficient and no small-cell suppression is triggered.</td></tr>
            <tr><td>@{{department}} has the highest incident share at @{{%}}.</td><td>Max department Total / all incident N × 100.0.</td></tr>
            <tr><td>@{{department}} has the highest resolution rate at @{{%}}.</td><td>Max Resolved ÷ Total; require a database-defined minimum denominator before recommendation.</td></tr>
            <tr><td>Recommendation: @{{action_prompt}}.</td><td>Threshold-based prompts only; examples: prioritize an office above a 20% incident-share threshold, strengthen GAD training below 75% favorable satisfaction, or review retention gaps above 10 percentage points.</td></tr>
        </tbody></table>
        <div class="data-note">This section intentionally contains no inferred conclusions or actual results. Narrative findings are generated only after the database values and thresholds are available.</div>

        <h2>13. Annex: Data Dictionary and Traceability</h2>
        <div class="report-meta"><strong>Coverage Period:</strong> {{ $data['period_label'] }} ({{ $data['start_date'] }} – {{ $data['end_date'] }})</div>
        <table class="sex-table"><thead><tr><th>Field</th><th>Source Form</th><th>Database Column Placeholder</th><th>Allowed Values</th><th>Report</th></tr></thead><tbody>
            <tr><td>Sex</td><td>Student / Faculty and Non-Teaching Information Sheet</td><td>@{{users.personal_information.sex}}</td><td>Male; Female; Prefer Not to Say</td><td>7-12</td></tr>
            <tr><td>Gender/SOGIESC</td><td>Student / Faculty and Non-Teaching Information Sheet</td><td>@{{users.personal_information.gender_identity}}</td><td>Lesbian; Gay; Bisexual; Transsexual; Cisgender/Heterosexual; Non-Binary; Prefer Not to Say</td><td>7-12</td></tr>
            <tr><td>Department / Office</td><td>System-generated account field</td><td>@{{users.department}}</td><td>Database-defined departments/offices</td><td>7-12</td></tr>
            <tr><td>Account Type / Role</td><td>System-generated registration field</td><td>@{{users.account_type}} / @{{users.role}}</td><td>Student; Employee; Administrator/coordinator roles</td><td>7-12</td></tr>
            <tr><td>Registration Date</td><td>System-generated</td><td>@{{users.created_at}}</td><td>Timestamp</td><td>7</td></tr>
            <tr><td>Incident Nature</td><td>Safe Spaces incident form</td><td>@{{reports.nature}}</td><td>Groping; Stalking; Catcalling; Cyber-harassment; Sexist Remarks or Jokes; Gender-based Discrimination; other database values</td><td>10, 15</td></tr>
            <tr><td>Incident Status / Priority</td><td>Safe Spaces incident form / system workflow</td><td>@{{reports.status}} / @{{reports.priority}}</td><td>Database/system allowed values</td><td>10</td></tr>
            <tr><td>Suggestion Category</td><td>Suggestion form</td><td>@{{suggestions.category}}</td><td>GAD Programs and Services; Gender Equality and Inclusion; Gender-Based Violence Prevention; Safe Spaces and Student Welfare; other database values</td><td>11</td></tr>
            <tr><td>Survey Rating / Responses</td><td>Customer Satisfaction Survey</td><td>@{{surveys.rating}} / @{{surveys.responses}}</td><td>Rating and question response values from the system</td><td>11</td></tr>
        </tbody></table>
        <div class="data-note"><strong>Open items:</strong> 1) Add Cisgender/Heterosexual, Non-Binary, and Prefer Not to Say to both Information Sheet Gender fields and database validation. 2) Add student retention/LOA/attrition source fields. 3) Add respondent Sex/Gender and relationship fields to incident reports. 4) Renumber duplicate Part A section 5. 5) Restrict/mark full-personal-data Part A sections 5 and 6 as a confidential/restricted annex.</div>
    </div>

    <div class="footer">SINAG Safe Space &amp; Participatory Suggestion System • Confidential Administrative Report • {{ $data['generated_at'] }}</div>
</body>
</html>
