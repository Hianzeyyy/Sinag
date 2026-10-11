<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Report;
use App\Models\Suggestion;
use Barryvdh\DomPDF\Facade\Pdf;

class ExportGADReportController extends Controller
{
    /**
     * Download the comprehensive GAD report as a PDF.
     *
     * @param string $period  weekly, monthly, yearly, 30days
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function export(string $period = '30days')
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

        $pdf = Pdf::loadView('admin.gad-report-pdf', [
            'data' => $data,
            'periodLabel' => $periodLabel,
            'users' => $users,
            'reports' => $reports,
            'suggestions' => $suggestions,
        ])->setPaper('a4', 'portrait');

        return $pdf->download("GAD_Report_{$period}_" . Carbon::now()->format('Ymd_His') . '.pdf');
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

}
