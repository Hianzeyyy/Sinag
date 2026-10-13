<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Report;
use App\Models\Suggestion;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Log;

class ExportGADReportController extends Controller
{
    /**
     * Download the GAD report as a PDF or Excel workbook.
     *
     * @param string $format  pdf or excel
     * @param string $period  7days, 30days, lastyear, lastmonth, yearly
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function export(string $format, string $period = '30days')
    {
        $format = strtolower($format);
        if (!in_array($format, ['pdf', 'excel', 'xlsx', 'csv'], true)) {
            abort(400, 'Unsupported export format.');
        }

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

        if ($format === 'pdf') {
            $pdf = Pdf::loadView('admin.gad-report-export-pdf', [
                'data' => $data,
                'users' => $users,
                'reports' => $reports,
                'suggestions' => $suggestions,
            ])->setPaper('a4', 'portrait');

            return $pdf->download("GAD_Report_{$period}_" . Carbon::now()->format('Ymd_His') . '.pdf');
        }

        return $this->exportExcelWorkbook($data, $users, $reports, $suggestions, $period);
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
            case 'lastmonth':
                $start = $now->copy()->subMonthNoOverflow()->startOfMonth();
                $now = $now->copy()->subMonthNoOverflow()->endOfMonth();
                $label = 'Last Month (' . $now->format('F Y') . ')';
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
            case 'lastyear':
                $previousYear = $now->copy()->subYear();
                $start = $previousYear->copy()->startOfYear();
                $now = $previousYear->copy()->endOfYear();
                $label = 'Last Year (' . $now->format('Y') . ')';
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

    private function exportExcelWorkbook(array $data, $users, $reports, $suggestions, string $period)
    {
        $sheets = [
            'Summary' => [
                ['SINAG - GENDER AND DEVELOPMENT (GAD) OFFICE'],
                ['Comprehensive Analytics & Operations Summary Report'],
                ['Coverage Period', $data['period_label']],
                ['Start Date', $data['start_date']],
                ['End Date', $data['end_date']],
                ['Generated On', $data['generated_at']],
                [],
                ['Metric', 'Count'],
                ['New Registered Users', $data['total_users']],
                ['Incidents Reported', $data['total_reports']],
                ['Suggestions Submitted', $data['total_suggestions']],
            ],
            'Users' => [[
                'User ID', 'Full Name', 'Email', 'Phone', 'Account Type', 'Role',
                'Gender', 'Age', 'Department', 'Account Status', 'Registered',
            ]],
            'Incident Reports' => [[
                'Incident ID', 'Reporter Alias', 'Nature', 'Location', 'Incident Date',
                'Incident Time', 'Priority', 'Status', 'Filed Date',
            ]],
            'Suggestions' => [[
                'Suggestion ID', 'Submitted By', 'Category', 'Message', 'Upvotes', 'Status', 'Submitted',
            ]],
        ];

        foreach ($users as $user) {
            $sheets['Users'][] = [
                $user->id,
                $user->name,
                $user->email,
                $user->phone_number ?? 'N/A',
                $user->account_type ?? 'N/A',
                $user->role ?? 'N/A',
                $user->gender ?? 'N/A',
                $user->age ?? 'N/A',
                $user->department ?? 'N/A',
                $user->account_status ?? 'N/A',
                optional($user->created_at)->format('Y-m-d H:i:s'),
            ];
        }

        foreach ($reports as $report) {
            $sheets['Incident Reports'][] = [
                $report->incident_id,
                $report->cloak_alias ?? 'Anonymous',
                $report->nature,
                $report->location ?? 'N/A',
                optional($report->incident_date)->format('Y-m-d') ?: 'N/A',
                $report->incident_time ?? 'N/A',
                $report->priority ?? 'Normal',
                $report->status ?? 'Pending',
                optional($report->created_at)->format('Y-m-d H:i:s'),
            ];
        }

        foreach ($suggestions as $suggestion) {
            $sheets['Suggestions'][] = [
                $suggestion->id,
                $suggestion->is_anonymous ? 'Anonymous' : (optional($suggestion->user)->name ?? 'Unknown'),
                $suggestion->category ?? 'General',
                preg_replace('/\s+/', ' ', $suggestion->message ?? ''),
                $suggestion->upvotes_count ?? 0,
                $suggestion->status ?? 'Open',
                optional($suggestion->created_at)->format('Y-m-d H:i:s'),
            ];
        }

        $temporaryPath = tempnam(sys_get_temp_dir(), 'sinag-gad-');
        if ($temporaryPath === false) {
            throw new \RuntimeException('Could not create a temporary Excel workbook.');
        }

        try {
            $zip = new \ZipArchive();
            if ($zip->open($temporaryPath, \ZipArchive::OVERWRITE) !== true) {
                throw new \RuntimeException('Could not open the temporary Excel workbook.');
            }

            $sheetNames = array_keys($sheets);
            $contentTypes = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
                . '<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">'
                . '<Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>'
                . '<Default Extension="xml" ContentType="application/xml"/>'
                . '<Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/>';

            $workbookSheets = '';
            $workbookRelationships = '';
            foreach ($sheetNames as $index => $sheetName) {
                $sheetNumber = $index + 1;
                $escapedName = $this->escapeXml($sheetName);
                $contentTypes .= '<Override PartName="/xl/worksheets/sheet' . $sheetNumber . '.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/>';
                $workbookSheets .= '<sheet name="' . $escapedName . '" sheetId="' . $sheetNumber . '" r:id="rId' . $sheetNumber . '"/>';
                $workbookRelationships .= '<Relationship Id="rId' . $sheetNumber . '" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet' . $sheetNumber . '.xml"/>';
                $zip->addFromString('xl/worksheets/sheet' . $sheetNumber . '.xml', $this->makeWorksheetXml($sheets[$sheetName]));
            }

            $contentTypes .= '</Types>';
            $zip->addFromString('[Content_Types].xml', $contentTypes);
            $zip->addFromString('_rels/.rels', '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
                . '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
                . '<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="xl/workbook.xml"/>'
                . '</Relationships>');
            $zip->addFromString('xl/workbook.xml', '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
                . '<workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">'
                . '<sheets>' . $workbookSheets . '</sheets></workbook>');
            $zip->addFromString('xl/_rels/workbook.xml.rels', '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
                . '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
                . $workbookRelationships
                . '</Relationships>');

            if (!$zip->close()) {
                throw new \RuntimeException('Could not finish the Excel workbook.');
            }
        } catch (\Throwable $exception) {
            @unlink($temporaryPath);
            Log::error('GAD Excel export failed', [
                'period' => $period,
                'exception' => $exception::class,
                'message' => $exception->getMessage(),
            ]);
            throw $exception;
        }

        return response()->download(
            $temporaryPath,
            'GAD_Report_' . $period . '_' . Carbon::now()->format('Ymd_His') . '.xlsx',
            ['Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet']
        )->deleteFileAfterSend(true);
    }

    private function makeWorksheetXml(array $rows): string
    {
        $xmlRows = '';
        foreach ($rows as $rowIndex => $row) {
            $cells = '';
            foreach (array_values($row) as $columnIndex => $value) {
                $cellReference = $this->excelColumnName($columnIndex + 1) . ($rowIndex + 1);
                $text = $this->escapeXml($value);
                $cells .= '<c r="' . $cellReference . '" t="inlineStr"><is><t xml:space="preserve">' . $text . '</t></is></c>';
            }
            $xmlRows .= '<row r="' . ($rowIndex + 1) . '">' . $cells . '</row>';
        }

        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">'
            . '<sheetData>' . $xmlRows . '</sheetData></worksheet>';
    }

    private function excelColumnName(int $number): string
    {
        $column = '';
        while ($number > 0) {
            $number--;
            $column = chr(65 + ($number % 26)) . $column;
            $number = intdiv($number, 26);
        }

        return $column;
    }

    private function escapeXml($value): string
    {
        $text = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F]/', '', (string) ($value ?? ''));
        return htmlspecialchars($text, ENT_XML1 | ENT_QUOTES, 'UTF-8');
    }
}
