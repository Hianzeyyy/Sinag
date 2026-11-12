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

    <div class="footer">SINAG Safe Space &amp; Participatory Suggestion System • Confidential Administrative Report • {{ $data['generated_at'] }}</div>
</body>
</html>
