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
        table.data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        table.data-table th {
            background-color: #ede9fe;
            color: #4c1d95;
            font-size: 9.5px;
            font-weight: bold;
            text-align: left;
            padding: 6px 7px;
            border: 1px solid #ddd6fe;
            text-transform: uppercase;
        }
        table.data-table td {
            font-size: 9.5px;
            padding: 5px 7px;
            border: 1px solid #e5e7eb;
            vertical-align: top;
        }
        table.data-table tr:nth-child(even) {
            background-color: #faf5ff;
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

    <!-- 1. NEW USERS -->
    <h2 class="section-title">1. New Registered Users & Personal Information ({{ $users->count() }})</h2>
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 5%;">#</th>
                <th style="width: 22%;">Name / Email</th>
                <th style="width: 14%;">Phone / Contact</th>
                <th style="width: 8%;">Age / Gender</th>
                <th style="width: 25%;">Department</th>
                <th style="width: 12%;">Role / Status</th>
                <th style="width: 14%;">Registered At</th>
            </tr>
        </thead>
        <tbody>
            @forelse($users as $index => $u)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>
                        <strong>{{ $u->name }}</strong><br>
                        <span style="color: #6b7280;">{{ $u->email }}</span>
                    </td>
                    <td>{{ $u->phone_number ?? 'N/A' }}</td>
                    <td>{{ $u->age ?? 'N/A' }} / {{ $u->gender ?? 'N/A' }}</td>
                    <td>{{ $u->department ?? 'N/A' }}</td>
                    <td>
                        {{ ucfirst($u->role ?? 'User') }}<br>
                        <span class="badge badge-{{ $u->account_status == 'active' || $u->account_status == 'approved' ? 'active' : ($u->account_status == 'rejected' ? 'rejected' : 'pending') }}">
                            {{ ucfirst($u->account_status ?? 'Pending') }}
                        </span>
                    </td>
                    <td>{{ optional($u->created_at)->format('M d, Y') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" style="text-align: center; color: #6b7280; padding: 12px;">No new users registered during this period.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- 2. INCIDENT REPORTS -->
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

    <!-- 3. SUGGESTIONS -->
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
