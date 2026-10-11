<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>SINAG GAD Report</title>
    <style>
        @page { margin: 28px; }
        body { font-family: Helvetica, Arial, sans-serif; color: #1f2937; font-size: 10px; }
        h1 { color: #4c1d95; font-size: 18px; margin: 0 0 5px; }
        h2 { color: #4c1d95; font-size: 13px; margin: 22px 0 7px; border-bottom: 1px solid #c4b5fd; padding-bottom: 4px; }
        p { margin: 3px 0; }
        table { width: 100%; border-collapse: collapse; margin-top: 8px; }
        th { background: #ede9fe; color: #4c1d95; text-align: left; }
        th, td { border: 1px solid #d1d5db; padding: 5px; vertical-align: top; }
        .summary td { width: 33%; text-align: center; background: #f5f3ff; }
        .number { display: block; color: #6d28d9; font-size: 18px; font-weight: bold; }
        .muted { color: #6b7280; }
    </style>
</head>
<body>
    <h1>SINAG Gender and Development Office Report</h1>
    <p class="muted">{{ $periodLabel }}: {{ $data['start_date'] }} to {{ $data['end_date'] }}</p>
    <p class="muted">Generated {{ $data['generated_at'] }}</p>

    <table class="summary">
        <tr>
            <td><span class="number">{{ $data['total_users'] }}</span>New users</td>
            <td><span class="number">{{ $data['total_reports'] }}</span>Incident reports</td>
            <td><span class="number">{{ $data['total_suggestions'] }}</span>Suggestions</td>
        </tr>
    </table>

    <h2>Registered users</h2>
    <table>
        <tr><th>ID</th><th>Name</th><th>Email</th><th>Account status</th><th>Registered</th></tr>
        @forelse ($users as $user)
            <tr>
                <td>{{ $user->id }}</td>
                <td>{{ $user->name ?? 'N/A' }}</td>
                <td>{{ $user->email ?? 'N/A' }}</td>
                <td>{{ ucfirst($user->account_status ?? 'Pending') }}</td>
                <td>{{ optional($user->created_at)->format('M d, Y') }}</td>
            </tr>
        @empty
            <tr><td colspan="5">No users were registered during this period.</td></tr>
        @endforelse
    </table>

    <h2>Incident reports</h2>
    <table>
        <tr><th>Incident</th><th>Nature</th><th>Location</th><th>Priority</th><th>Status</th><th>Date</th></tr>
        @forelse ($reports as $report)
            <tr>
                <td>{{ $report->incident_id ?? 'N/A' }}</td>
                <td>{{ $report->nature ?? 'N/A' }}</td>
                <td>{{ $report->location ?? 'N/A' }}</td>
                <td>{{ ucfirst($report->priority ?? 'Normal') }}</td>
                <td>{{ ucfirst($report->status ?? 'Pending') }}</td>
                <td>{{ optional($report->created_at)->format('M d, Y') }}</td>
            </tr>
        @empty
            <tr><td colspan="6">No incident reports were filed during this period.</td></tr>
        @endforelse
    </table>

    <h2>Suggestions and community feedback</h2>
    <table>
        <tr><th>ID</th><th>Submitted by</th><th>Category</th><th>Message</th><th>Upvotes</th><th>Date</th></tr>
        @forelse ($suggestions as $suggestion)
            <tr>
                <td>{{ $suggestion->id }}</td>
                <td>{{ $suggestion->is_anonymous ? 'Anonymous' : (optional($suggestion->user)->name ?? 'User') }}</td>
                <td>{{ $suggestion->category ?? 'General' }}</td>
                <td>{{ \Illuminate\Support\Str::limit($suggestion->message ?? '', 120) }}</td>
                <td>{{ $suggestion->upvotes_count ?? 0 }}</td>
                <td>{{ optional($suggestion->created_at)->format('M d, Y') }}</td>
            </tr>
        @empty
            <tr><td colspan="6">No suggestions were submitted during this period.</td></tr>
        @endforelse
    </table>
</body>
</html>
