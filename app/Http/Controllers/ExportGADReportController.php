<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Report;
use App\Models\Suggestion;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ExportGADReportController extends Controller
{
    /**
     * Export GAD report in specified format and period.
     *
     * @param string $format  excel, csv, or pdf
     * @param string $period  weekly, monthly, yearly, 30days
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function export(string $format, string $period = '30days')
    {
        $dates = $this->resolvePeriod($period);
        $startDate = $dates['start'];
        $endDate = $dates['end'];
        $periodLabel = $dates['label'];

        // Retrieve data within the selected period
        $users = User::whereBetween('created_at', [$startDate, $endDate])
            ->orderBy('created_at', 'desc')
            ->get();

        $reports = Report::whereBetween('created_at', [$startDate, $endDate])
            ->orderBy('created_at', 'desc')
            ->get();

        $suggestions = Suggestion::with('user')
            ->withCount('upvotes')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->orderBy('created_at', 'desc')
            ->get();

        $data = [
            'period_label' => $periodLabel,
            'start_date'   => $startDate->format('M d, Y'),
            'end_date'     => $endDate->format('M d, Y'),
            'total_users'  => $users->count(),
            'total_reports' => $reports->count(),
            'total_suggestions' => $suggestions->count(),
            'generated_at' => Carbon::now()->format('F d, Y h:i A'),
        ];

        $format = strtolower($format);

        if ($format === 'pdf') {
            $pdf = Pdf::loadView('admin.gad-report-pdf', compact('data', 'period', 'periodLabel', 'users', 'reports', 'suggestions', 'startDate', 'endDate'))
                ->setPaper('a4', 'portrait');

            $filename = "GAD_Report_{$period}_" . Carbon::now()->format('Ymd_His') . ".pdf";
            return $pdf->download($filename);
        }

        if ($format === 'excel' || $format === 'csv') {
            return $this->exportExcelCsv($data, $users, $reports, $suggestions, $period);
        }

        abort(400, 'Unsupported export format.');
    }

    /**
     * Resolve period string to start and end Carbon dates with human labels.
     */
    private function resolvePeriod(string $period): array
    {
        $now = Carbon::now();
        $start = Carbon::now();
        $label = 'Last 30 Days';

        switch (strtolower($period)) {
            case 'weekly':
            case '7days':
                $start = $now->copy()->subDays(7)->startOfDay();
                $label = 'Last 7 Days (Weekly)';
                break;
            case 'monthly':
            case 'month':
                $start = $now->copy()->startOfMonth();
                $label = 'This Month (' . $now->format('F Y') . ')';
                break;
            case '30days':
                $start = $now->copy()->subDays(30)->startOfDay();
                $label = 'Last 30 Days';
                break;
            case 'yearly':
            case 'year':
                $start = $now->copy()->startOfYear();
                $label = 'This Year (' . $now->format('Y') . ')';
                break;
            default:
                $start = $now->copy()->subDays(30)->startOfDay();
                $label = 'Last 30 Days';
                break;
        }

        return [
            'start' => $start,
            'end'   => $now->copy()->endOfDay(),
            'label' => $label,
        ];
    }

    /**
     * Stream CSV formatted for Microsoft Excel.
     */
    private function exportExcelCsv(array $data, $users, $reports, $suggestions, string $period): StreamedResponse
    {
        $filename = "GAD_Report_{$period}_" . Carbon::now()->format('Ymd_His') . ".csv";

        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Pragma'              => 'no-cache',
            'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0',
            'Expires'             => '0',
        ];

        $callback = function () use ($data, $users, $reports, $suggestions) {
            $handle = fopen('php://output', 'w');
            // Write UTF-8 BOM so Excel opens accented & special characters cleanly
            fputs($handle, "\xEF\xBB\xBF");

            // --- HEADER ---
            fputcsv($handle, ['SINAG - GENDER AND DEVELOPMENT (GAD) OFFICE COMPREHENSIVE REPORT']);
            fputcsv($handle, ['Coverage Period:', $data['period_label'] . " ({$data['start_date']} to {$data['end_date']})"]);
            fputcsv($handle, ['Generated At:', $data['generated_at']]);
            fputcsv($handle, []);

            // --- SUMMARY METRICS ---
            fputcsv($handle, ['--- EXECUTIVE SUMMARY ---']);
            fputcsv($handle, ['Metric', 'Count']);
            fputcsv($handle, ['Total New Users Registered', $data['total_users']]);
            fputcsv($handle, ['Total Incident Reports Filed', $data['total_reports']]);
            fputcsv($handle, ['Total Suggestions Submitted', $data['total_suggestions']]);
            fputcsv($handle, []);

            // --- SECTION 1: NEW USERS ---
            fputcsv($handle, ['--- 1. NEW USERS & PERSONAL INFORMATION ---']);
            fputcsv($handle, [
                'User ID',
                'Full Name',
                'Email Address',
                'Phone Number',
                'Account Type / Role',
                'Gender',
                'Age',
                'Department / College',
                'Account Status',
                'Registration Date',
            ]);

            foreach ($users as $user) {
                fputcsv($handle, [
                    $user->id,
                    $user->name,
                    $user->email,
                    $user->phone_number ?? 'N/A',
                    ucfirst($user->role ?? $user->account_type ?? 'User'),
                    $user->gender ?? 'N/A',
                    $user->age ?? 'N/A',
                    $user->department ?? 'N/A',
                    ucfirst($user->account_status ?? 'Active'),
                    optional($user->created_at)->format('Y-m-d H:i:s'),
                ]);
            }
            fputcsv($handle, []);

            // --- SECTION 2: REPORTS ---
            fputcsv($handle, ['--- 2. INCIDENT REPORTS ---']);
            fputcsv($handle, [
                'Incident ID',
                'Cloak / Alias',
                'Nature of Incident',
                'Location',
                'Incident Date',
                'Incident Time',
                'Priority',
                'Status',
                'Filed Date',
            ]);

            foreach ($reports as $report) {
                fputcsv($handle, [
                    $report->incident_id,
                    $report->cloak_alias ?? 'Anonymous',
                    $report->nature,
                    $report->location ?? 'N/A',
                    optional($report->incident_date)->format('Y-m-d') ?: 'N/A',
                    $report->incident_time ?? 'N/A',
                    ucfirst($report->priority ?? 'Normal'),
                    ucfirst($report->status ?? 'Pending'),
                    optional($report->created_at)->format('Y-m-d H:i:s'),
                ]);
            }
            fputcsv($handle, []);

            // --- SECTION 3: SUGGESTIONS ---
            fputcsv($handle, ['--- 3. SUGGESTIONS & FEEDBACK ---']);
            fputcsv($handle, [
                'Suggestion ID',
                'Submitted By',
                'Category',
                'Message / Content',
                'Upvotes Count',
                'Status',
                'Date Submitted',
            ]);

            foreach ($suggestions as $suggestion) {
                $author = $suggestion->is_anonymous ? 'Anonymous' : (optional($suggestion->user)->name ?? 'Unknown');
                fputcsv($handle, [
                    $suggestion->id,
                    $author,
                    $suggestion->category ?? 'General',
                    preg_replace('/\s+/', ' ', $suggestion->message ?? ''),
                    $suggestion->upvotes_count ?? 0,
                    ucfirst($suggestion->status ?? 'Open'),
                    optional($suggestion->created_at)->format('Y-m-d H:i:s'),
                ]);
            }

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }
}
