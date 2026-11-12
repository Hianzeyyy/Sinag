<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Report;
use App\Models\Suggestion;
use App\Models\CustomerSatisfactionSurvey;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

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
        $request = request();
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

        $profileUsers = User::whereRaw("LOWER(COALESCE(account_type, role, '')) IN ('student', 'employee')")
            ->orderBy('created_at', 'desc')
            ->get();
        $students = $profileUsers->filter(fn (User $user) => strtolower((string) ($user->account_type ?? $user->role)) === 'student')->values();
        $employees = $profileUsers->filter(fn (User $user) => strtolower((string) ($user->account_type ?? $user->role)) === 'employee')->values();

        $reports = Report::whereBetween('created_at', [$startDate, $endDate])
            ->orderBy('created_at', 'desc')
            ->get();

        $suggestions = Suggestion::with('user')
            ->withCount('upvotes')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->orderBy('created_at', 'desc')
            ->get();
        $currentUser = auth()->user();
        $showSatisfactionNames = strtolower((string) ($currentUser?->account_type)) === 'coordinator'
            || strtolower((string) ($currentUser?->email)) === 'gadcoordinator@psu.edu.ph';
        $hasSectionSelection = $request->has('include_reports') || $request->has('include_feedback');
        $includeReports = $hasSectionSelection ? $request->boolean('include_reports') : true;
        $includeFeedback = $hasSectionSelection ? $request->boolean('include_feedback') : true;
        if (!$includeReports && !$includeFeedback) {
            abort(422, 'Select at least one export section.');
        }
        $satisfactionSurveys = $includeFeedback && Schema::hasTable('customer_satisfaction_surveys')
            ? CustomerSatisfactionSurvey::with('user')
                ->whereBetween('created_at', [$startDate, $endDate])
                ->latest()
                ->get()
            : collect();
        $surveyFindings = $this->surveyFindings($satisfactionSurveys);

        $data = [
            'period_label' => $periodLabel,
            'start_date'   => $startDate->format('M d, Y'),
            'end_date'     => $endDate->format('M d, Y'),
            'total_users'  => $users->count(),
            'total_reports' => $reports->count(),
            'total_suggestions' => $suggestions->count(),
            'total_satisfaction_surveys' => $satisfactionSurveys->count(),
            'average_satisfaction' => round((float) $satisfactionSurveys->avg('rating'), 2),
            'generated_at' => Carbon::now()->format('F d, Y h:i A'),
        ];

        if ($format === 'pdf') {
            $pdf = Pdf::loadView('admin.gad-report-export-pdf', [
                'data' => $data,
                'users' => $users,
                'studentProfiles' => $this->profileDetails($students, false),
                'employeeProfiles' => $this->profileDetails($employees, true),
                'reports' => $reports,
                'suggestions' => $suggestions,
                'satisfactionSurveys' => $satisfactionSurveys,
                'showSatisfactionNames' => $showSatisfactionNames,
                'includeReports' => $includeReports,
                'includeFeedback' => $includeFeedback,
                'surveyFindings' => $surveyFindings,
            ])->setPaper('a4', 'portrait');

            return $pdf->download("GAD_Report_{$period}_" . Carbon::now()->format('Ymd_His') . '.pdf');
        }

        return $this->exportExcelWorkbook($data, $users, $students, $employees, $reports, $suggestions, $satisfactionSurveys, $surveyFindings, $showSatisfactionNames, $includeReports, $includeFeedback, $period);
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

    private function exportExcelWorkbook(array $data, $users, $students, $employees, $reports, $suggestions, $satisfactionSurveys, array $surveyFindings, bool $showSatisfactionNames, bool $includeReports, bool $includeFeedback, string $period)
    {
        $summaryRows = [
            ['SINAG - GENDER AND DEVELOPMENT (GAD) OFFICE'],
            ['Comprehensive Analytics & Operations Summary Report'],
            ['Coverage Period', $data['period_label']],
            ['Start Date', $data['start_date']],
            ['End Date', $data['end_date']],
            ['Generated On', $data['generated_at']],
            [],
            ['Metric', 'Count'],
        ];
        if ($includeReports) {
            $summaryRows[] = ['New Registered Users', $data['total_users']];
            $summaryRows[] = ['Incidents Reported', $data['total_reports']];
        }
        if ($includeFeedback) {
            if ($includeReports) {
                $summaryRows[] = ['Suggestions Submitted', $data['total_suggestions']];
            }
            $summaryRows[] = ['Customer Satisfaction Responses', $data['total_satisfaction_surveys']];
            $summaryRows[] = ['Average System Rating', $data['average_satisfaction'] ?: 'N/A'];
        }

        $sheets = [
            'Summary' => $summaryRows,
        ];

        if ($includeReports) {
            $sheets['Users'] = [[
                'User ID', 'Full Name', 'Email', 'Phone', 'Account Type', 'Role',
                'Gender', 'Age', 'Department', 'Account Status', 'Registered',
            ]];
            $sheets['Incident Reports'] = [[
                'Incident ID', 'Reporter Alias', 'Nature', 'Location', 'Incident Date',
                'Incident Time', 'Priority', 'Status', 'Filed Date',
            ]];
        }

        if ($includeReports && $includeFeedback) {
            $sheets['Suggestions'] = [[
                'Suggestion ID', 'Submitted By', 'Category', 'Message', 'Upvotes', 'Status', 'Submitted',
            ]];
        }

        if ($includeReports) {
            $sheets['Student Information'] = $this->profileSheetRows($students, false);
            $sheets['Faculty and Non-Teaching'] = $this->profileSheetRows($employees, true);
        }

        if ($includeFeedback) {
            $sheets['Customer Satisfaction'] = [[
                $showSatisfactionNames ? 'Respondent Name' : 'Respondent',
                'Rating', 'Survey Responses', 'Feedback', 'Submitted',
            ]];
            $sheets['Survey Findings'] = [[
                'Question', 'Responses', 'Average Score', 'Favorable Responses',
                'Strongly Disagree', 'Disagree', 'Agree', 'Strongly Agree', 'Finding',
            ]];
            foreach ($surveyFindings as $finding) {
                $sheets['Survey Findings'][] = [
                    $finding['question'],
                    $finding['responses'],
                    $finding['average_score'],
                    $finding['favorable_percentage'].'%',
                    $finding['counts'][1],
                    $finding['counts'][2],
                    $finding['counts'][3],
                    $finding['counts'][4],
                    $finding['finding'],
                ];
            }
        }

        foreach ($includeReports ? $users : [] as $user) {
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

        foreach ($includeReports ? $reports : [] as $report) {
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

        foreach ($includeReports && $includeFeedback ? $suggestions : [] as $suggestion) {
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

        foreach ($includeFeedback ? $satisfactionSurveys : [] as $survey) {
            $sheets['Customer Satisfaction'][] = [
                $showSatisfactionNames ? (optional($survey->user)->name ?? 'Unknown') : 'Anonymous',
                $survey->rating,
                json_encode($survey->responses ?? [], JSON_UNESCAPED_UNICODE),
                $survey->feedback ?? '',
                optional($survey->created_at)->format('Y-m-d H:i:s'),
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

    private function surveyFindings($surveys): array
    {
        $questions = [
            'The report or suggestion submission process was easy to use.',
            'The instructions and information were clear.',
            'The system made me feel safe sharing my concern or feedback.',
            'The system responded well to my needs.',
            'I would recommend this system to others who need support.',
        ];

        return collect($questions)->map(function (string $question, int $index) use ($surveys): array {
            $answers = $surveys
                ->map(fn ($survey) => (int) data_get($survey->responses, $index))
                ->filter(fn (int $answer) => $answer >= 1 && $answer <= 4)
                ->values();
            $count = $answers->count();
            $average = $count > 0 ? round((float) $answers->avg(), 2) : null;
            $favorable = $count > 0
                ? round(($answers->filter(fn (int $answer) => $answer >= 3)->count() / $count) * 100)
                : 0;
            $counts = collect([1, 2, 3, 4])->mapWithKeys(
                fn (int $choice) => [$choice => $answers->filter(fn (int $answer) => $answer === $choice)->count()]
            )->all();

            return [
                'question' => $question,
                'responses' => $count,
                'average_score' => $average ?? 'N/A',
                'favorable_percentage' => $favorable,
                'counts' => $counts,
                'pie_image' => $this->pieImage($counts),
                'finding' => $count === 0
                    ? 'No responses recorded.'
                    : ($favorable >= 75 ? 'Strength: respondents were generally favorable.'
                        : ($favorable >= 50 ? 'Mixed finding: responses were divided.'
                            : 'Priority finding: respondents were generally unfavorable.')),
            ];
        })->all();
    }

    private function pieImage(array $counts): ?string
    {
        if (!function_exists('imagecreatetruecolor')) {
            return null;
        }

        $image = imagecreatetruecolor(220, 220);
        imagealphablending($image, true);
        $background = imagecolorallocate($image, 255, 255, 255);
        imagefill($image, 0, 0, $background);
        $colors = [
            1 => imagecolorallocate($image, 239, 68, 68),
            2 => imagecolorallocate($image, 245, 158, 11),
            3 => imagecolorallocate($image, 132, 204, 22),
            4 => imagecolorallocate($image, 34, 197, 94),
        ];
        $total = array_sum($counts);
        $start = 270;
        if ($total === 0) {
            imagefilledellipse($image, 110, 110, 176, 176, imagecolorallocate($image, 209, 213, 219));
        } else {
            foreach ($counts as $choice => $count) {
                if ($count === 0) {
                    continue;
                }
                $end = $start + (360 * $count / $total);
                imagefilledarc($image, 110, 110, 176, 176, $start, $end, $colors[$choice], IMG_ARC_PIE);
                $percentage = round(($count / $total) * 100);
                $label = $percentage.'%';
                $labelAngle = deg2rad(($start + $end) / 2);
                $labelX = 110 + (int) round(cos($labelAngle) * 55);
                $labelY = 110 + (int) round(sin($labelAngle) * 55);
                $labelWidth = imagefontwidth(3) * strlen($label);
                imagestring($image, 3, $labelX - (int) ($labelWidth / 2), $labelY - 6, $label, imagecolorallocate($image, 255, 255, 255));
                $start = $end;
            }
        }

        ob_start();
        imagepng($image);
        $contents = ob_get_clean();
        imagedestroy($image);

        return is_string($contents) ? base64_encode($contents) : null;
    }

    private function profileSheetRows($users, bool $isEmployee): array
    {
        $rows = [['User ID', 'Full Name', 'Email', 'Phone', 'Account Type', 'Department / Course', 'Section', 'Field', 'Value']];

        foreach ($users as $user) {
            $basicInformation = [
                'Age' => $user->age,
                'Gender' => $user->gender,
                'Employee Category' => $user->employee_category,
            ];
            foreach ($basicInformation as $field => $value) {
                if ($value !== null && $value !== '') {
                    $rows[] = $this->profileSheetRow($user, 'Account Information', $field, (string) $value);
                }
            }

            foreach ($this->profileSections($user, $isEmployee) as $section => $values) {
                foreach ($this->flattenProfileFields($values) as $field => $value) {
                    $rows[] = $this->profileSheetRow($user, $section, $field, $value);
                }
            }
        }

        return $rows;
    }

    private function profileSheetRow(User $user, string $section, string $field, string $value): array
    {
        return [
            $user->id,
            $user->name,
            $user->email,
            $user->phone_number ?? '',
            $user->account_type ?? $user->role ?? '',
            $user->department ?? '',
            $section,
            $field,
            $value,
        ];
    }

    private function profileDetails($users, bool $isEmployee): array
    {
        return $users->map(fn (User $user) => [
            'user' => $user,
            'basic' => [
                'User ID' => $user->id,
                'Full Name' => $user->name,
                'Email' => $user->email,
                'Phone' => $user->phone_number ?? 'N/A',
                'Account Type' => $user->account_type ?? $user->role ?? 'N/A',
                'Department / Course' => $user->department ?? 'N/A',
                'Age' => $user->age ?? 'N/A',
                'Gender' => $user->gender ?? 'N/A',
                'Employee Category' => $user->employee_category ?? 'N/A',
            ],
            'sections' => array_map(
                fn (array $values) => $this->formatProfileSection($values),
                $this->profileSections($user, $isEmployee)
            ),
        ])->all();
    }

    private function profileSections(User $user, bool $isEmployee): array
    {
        $sections = [
            'Personal Information' => $user->personal_information ?? [],
            'Family Background' => $user->family_background ?? [],
        ];

        if ($isEmployee) {
            $sections += [
                'Employee Education' => $user->employee_education ?? [],
                'Civil Service Eligibility' => $user->civil_service_eligibility ?? [],
                'Work Experience' => $user->work_experience ?? [],
                'Voluntary Work' => $user->voluntary_work ?? [],
                'GAD Training' => $user->gad_training ?? [],
                'Other Information' => $user->other_information ?? [],
            ];
        } else {
            $sections['Student Information'] = $user->student_information ?? [];
        }

        return $sections;
    }

    private function formatProfileSection(array $values): string
    {
        $lines = [];
        foreach ($this->flattenProfileFields($values) as $field => $value) {
            $lines[] = $field . ': ' . $value;
        }

        return $lines ? implode("\n", $lines) : 'N/A';
    }

    private function flattenProfileFields(array $values, string $prefix = ''): array
    {
        $fields = [];

        if (array_is_list($values)) {
            foreach ($values as $index => $value) {
                $field = trim($prefix . ' ' . ($index + 1));
                if (is_array($value)) {
                    $fields += $this->flattenProfileFields($value, $field);
                } else {
                    $formatted = $this->formatProfileValue($value);
                    if ($formatted !== '') {
                        $fields[$field] = $formatted;
                    }
                }
            }

            return $fields;
        }

        foreach ($values as $key => $value) {
            $label = ucfirst(str_replace('_', ' ', (string) $key));
            $field = $prefix === '' ? $label : $prefix . ' - ' . $label;
            if (is_array($value)) {
                $fields += $this->flattenProfileFields($value, $field);
            } else {
                $formatted = $this->formatProfileValue($value);
                if ($formatted !== '') {
                    $fields[$field] = $formatted;
                }
            }
        }

        return $fields;
    }

    private function formatProfileValue($value): string
    {
        if ($value === null || $value === '') {
            return '';
        }

        if (is_bool($value)) {
            return $value ? 'Yes' : 'No';
        }

        return is_scalar($value) ? (string) $value : '';
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
