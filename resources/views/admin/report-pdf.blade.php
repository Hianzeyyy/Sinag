<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>SINAG Report {{ $report->incident_id }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; color: #172033; font-size: 12px; line-height: 1.5; }
        h1 { color: #5b4b9b; font-size: 22px; margin: 0 0 4px; }
        h2 { color: #5b4b9b; font-size: 14px; border-bottom: 1px solid #d8cdfc; padding-bottom: 5px; margin-top: 22px; }
        .muted { color: #6b7280; }
        .header { border-bottom: 3px solid #a594f9; padding-bottom: 12px; }
        .grid { width: 100%; border-collapse: collapse; }
        .grid td { width: 50%; vertical-align: top; padding: 7px 10px 7px 0; }
        .label { display: block; color: #6b7280; font-size: 10px; text-transform: uppercase; }
        .value { font-weight: bold; }
        .description { background: #f5f3ff; border: 1px solid #d8cdfc; padding: 12px; white-space: pre-wrap; }
        .evidence { margin: 4px 0; }
    </style>
</head>
<body>
    <div class="header">
        <h1>SINAG Confidential Report</h1>
        <div class="muted">Gender and Development Safe Space and Participatory Suggestion System</div>
    </div>

    <h2>Report Details</h2>
    <table class="grid">
        <tr>
            <td><span class="label">Incident ID</span><span class="value">{{ $report->incident_id }}</span></td>
            <td><span class="label">Filed Date</span><span class="value">{{ optional($report->created_at)->format('M d, Y h:i A') }}</span></td>
        </tr>
        <tr>
            <td><span class="label">Reporter Alias</span><span class="value">{{ $report->cloak_alias }}</span></td>
            <td><span class="label">Nature</span><span class="value">{{ $report->nature }}</span></td>
        </tr>
        <tr>
            <td><span class="label">Incident Date</span><span class="value">{{ optional($report->incident_date)->format('M d, Y') ?: 'N/A' }}</span></td>
            <td><span class="label">Incident Time</span><span class="value">{{ $report->incident_time ?: 'N/A' }}</span></td>
        </tr>
        <tr>
            <td><span class="label">Location</span><span class="value">{{ $report->location }}</span></td>
            <td><span class="label">Priority / Status</span><span class="value">{{ $report->priority }} / {{ $report->status }}</span></td>
        </tr>
    </table>

    <h2>Statement of Facts</h2>
    <div class="description">{{ $report->description ?: 'No statement provided.' }}</div>

    <h2>Evidence</h2>
    @forelse($evidenceFiles as $evidenceFile)
        <div class="evidence">{{ basename($evidenceFile) }}</div>
    @empty
        <div class="muted">No evidence files attached.</div>
    @endforelse
</body>
</html>
