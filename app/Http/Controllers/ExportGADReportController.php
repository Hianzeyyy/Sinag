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
     * @param string $period  A supported rolling or calendar report period
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
        $currentUser = auth()->user();
        $isGadCoordinator = $currentUser?->isGadCoordinator() ?? false;
        $includeRegisteredUsers = ! $isGadCoordinator;
        $includeProfileSheets = ! $isGadCoordinator;
        $includeDetailedGadReports = ! $isGadCoordinator;

        // Retrieve data within the selected period
        $users = $includeRegisteredUsers
            ? User::whereBetween('created_at', [$startDate, $endDate])
                ->orderBy('created_at', 'desc')
                ->get()
                ->reject(fn (User $user) => $user->isGadCoordinator())
                ->values()
            : collect();

        $profileUsers = User::whereRaw("LOWER(COALESCE(account_type, role, '')) IN ('student', 'employee')")
            ->orderBy('created_at', 'desc')
            ->get();
        $students = $profileUsers->filter(fn (User $user) => strtolower((string) ($user->account_type ?? $user->role)) === 'student')->values();
        $employees = $profileUsers->filter(fn (User $user) => strtolower((string) ($user->account_type ?? $user->role)) === 'employee')->values();

        $reports = Report::whereBetween('created_at', [$startDate, $endDate])
            ->orderBy('created_at', 'desc')
            ->get();
        $reportUsers = User::whereIn('id', $reports->pluck('user_id')->filter()->unique())->get();

        $suggestions = Suggestion::with('user')
            ->withCount('upvotes')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->orderBy('created_at', 'desc')
            ->get();
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
        $sexDisaggregatedData = $this->sexDisaggregatedData(
            $students,
            $employees,
            $profileUsers->merge($reportUsers)->keyBy('id'),
            $reports,
            $suggestions,
            $satisfactionSurveys,
            $startDate,
            $endDate
        );

        if ($format === 'pdf') {
            $pdf = Pdf::loadView('admin.gad-report-export-pdf', [
                'data' => $data,
                'users' => $users,
                'studentProfiles' => $includeProfileSheets ? $this->profileDetails($students, false) : [],
                'employeeProfiles' => $includeProfileSheets ? $this->profileDetails($employees, true) : [],
                'reports' => $reports,
                'suggestions' => $suggestions,
                'satisfactionSurveys' => $satisfactionSurveys,
                'showSatisfactionNames' => $showSatisfactionNames,
                'includeRegisteredUsers' => $includeRegisteredUsers,
                'includeProfileSheets' => $includeProfileSheets,
                'includeDetailedGadReports' => $includeDetailedGadReports,
                'includeReports' => $includeReports,
                'includeFeedback' => $includeFeedback,
                'surveyFindings' => $surveyFindings,
                'sexDisaggregatedData' => $sexDisaggregatedData,
            ])->setPaper('a4', 'portrait');

            return $pdf->download("GAD_Report_{$period}_" . Carbon::now()->format('Ymd_His') . '.pdf');
        }

        return $this->exportExcelWorkbook($data, $users, $students, $employees, $reports, $suggestions, $satisfactionSurveys, $surveyFindings, $sexDisaggregatedData, $showSatisfactionNames, $includeRegisteredUsers, $includeProfileSheets, $includeDetailedGadReports, $includeReports, $includeFeedback, $period);
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
            case 'q1':
            case 'quarter1':
                $start = $now->copy()->startOfYear();
                $now = $start->copy()->addMonths(2)->endOfMonth();
                $label = 'Quarter 1 (' . $start->format('Y') . ')';
                break;
            case 'q2':
            case 'quarter2':
                $start = $now->copy()->startOfYear()->addMonths(3);
                $now = $start->copy()->addMonths(2)->endOfMonth();
                $label = 'Quarter 2 (' . $start->format('Y') . ')';
                break;
            case 'q3':
            case 'quarter3':
                $start = $now->copy()->startOfYear()->addMonths(6);
                $now = $start->copy()->addMonths(2)->endOfMonth();
                $label = 'Quarter 3 (' . $start->format('Y') . ')';
                break;
            case 'q4':
            case 'quarter4':
                $start = $now->copy()->startOfYear()->addMonths(9);
                $now = $start->copy()->addMonths(2)->endOfMonth();
                $label = 'Quarter 4 (' . $start->format('Y') . ')';
                break;
            case 'midyear':
                $start = $now->copy()->startOfYear();
                $now = $start->copy()->addMonths(5)->endOfMonth();
                $label = 'Mid-Year (January–June ' . $start->format('Y') . ')';
                break;
            case 'annual':
                $start = $now->copy()->startOfYear();
                $now = $start->copy()->endOfYear();
                $label = 'Annual (' . $start->format('Y') . ')';
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

    private function exportExcelWorkbook(array $data, $users, $students, $employees, $reports, $suggestions, $satisfactionSurveys, array $surveyFindings, array $sexDisaggregatedData, bool $showSatisfactionNames, bool $includeRegisteredUsers, bool $includeProfileSheets, bool $includeDetailedGadReports, bool $includeReports, bool $includeFeedback, string $period)
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
            if ($includeRegisteredUsers) {
                $summaryRows[] = ['New Registered Users', $data['total_users']];
            }
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

        if ($includeReports && $includeRegisteredUsers) {
            $sheets['Users'] = [[
                'User ID', 'Full Name', 'Email', 'Phone', 'Account Type', 'Role',
                'Gender', 'Age', 'Department', 'Account Status', 'Registered',
            ]];
        }

        if ($includeReports) {
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

        if ($includeReports && $includeProfileSheets) {
            $sheets['Student Information'] = $this->profileSheetRows($students, false);
            $sheets['Faculty and Non-Teaching'] = $this->profileSheetRows($employees, true);
        }

        if ($includeDetailedGadReports) {
            $sheets['Sex-Disaggregated Data'] = $this->sexDisaggregatedSheetRows($sexDisaggregatedData, $data);
            $sheets['Incident Analytics'] = $this->incidentAnalyticsSheetRows($sexDisaggregatedData, $data);
            $sheets['GAD Training Summary'] = $this->trainingSummarySheetRows($sexDisaggregatedData, $data);
        }
        $sheets['Suggestions by Sex Gender'] = $this->suggestionDisaggregationSheetRows($sexDisaggregatedData, $data);
        $sheets['Satisfaction by Sex Gender'] = $this->satisfactionDisaggregationSheetRows($sexDisaggregatedData, $data);
        $sheets['Data Dictionary'] = [[
            'Field', 'Source Form', 'Database Column(s)', 'Allowed Values', 'Report',
        ], [
            'Sex', 'Student / Faculty Information Sheet', 'users.personal_information.sex; users.gender (legacy fallback)', 'Male, Female', '7-12',
        ], [
            'Gender/SOGIESC', 'Student / Faculty Information Sheet', 'users.personal_information.gender_identity', 'Lesbian, Gay, Bisexual, Transsexual, Cisgender/Heterosexual, Non-Binary, Prefer Not to Say', '7-12',
        ], [
            'Department / Office', 'System-generated account field', 'users.department', 'Database-defined departments/offices', '7-12',
        ], [
            'Account Status / Registration Date', 'System-generated account fields', 'users.account_status; users.created_at', 'Active; Rejected; Pending; database timestamps', '7',
        ], [
            'Student program / major and year level', 'Student Information Sheet', 'users.department; users.student_information.year_level', 'Stored program/major and year-level values', '7-8',
        ], [
            'Student profile and honors', 'Student Information Sheet', 'users.personal_information; users.family_background; users.student_information', 'Stored form values, normalized for report categories', '8',
        ], [
            'Employee profile and appointment', 'Faculty and Non-Teaching Information Sheet', 'users.work_experience; users.employee_education; users.civil_service_eligibility; users.gad_training', 'Stored form values; individual salary excluded', '9',
        ], [
            'Retention / LOA / attrition', 'Pending data source', 'No corresponding fields currently available', 'Status, reason and effective date required', '8',
        ], [
            'Incident Nature / Status / Priority', 'Safe Spaces incident report / system workflow', 'reports.nature; reports.status; reports.priority; reports.location; reports.created_at', 'Database/system-defined values', '10',
        ], [
            'Incident respondent Sex/Gender and relationship', 'Pending data source', 'Respondent fields not currently available', 'Student-to-Student; Employee-to-Student; Employee-to-Employee', '10',
        ], [
            'Suggestions', 'Suggestion form', 'suggestions.category; suggestions.is_anonymous; suggestions.user_id; suggestions.created_at', 'Database-defined categories; anonymous/unlinked as Not disclosed', '11',
        ], [
            'Customer satisfaction responses', 'Customer Satisfaction Survey', 'customer_satisfaction_surveys.responses; customer_satisfaction_surveys.created_at; user_id', 'Valid 1-4 ratings; group shown only at N >= 5', '11',
        ]];

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

        foreach ($includeReports && $includeRegisteredUsers ? $users : [] as $user) {
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

    private function sexDisaggregatedSheetRows(array $partBData, array $data): array
    {
        $rows = [
            ['Part B - Sex-Disaggregated Data Summary'],
            ['Coverage Period', $data['period_label'], $data['start_date'] . ' - ' . $data['end_date']],
            ['Access', 'GAD Administrator only; aggregate data only'],
            ['Logic', 'Male/Female row percentages use their combined count as N; Gender/SOGIESC cells with f < 5 are suppressed as <5.'],
            ['Scope', 'Current population for Reports 1-2; profile reports use records registered within the Coverage Period.'],
            [],
        ];
        $headers = [
            'Report', 'Category', 'Male (f / %)', 'Female (f / %)', 'Total (N)',
            'Lesbian', 'Gay', 'Bisexual', 'Transsexual', 'Cisgender/Heterosexual',
            'Non-Binary', 'Prefer Not to Say',
        ];
        $rows[] = $headers;

        $reportTitles = [
            1 => '7.1 Report 1 - Student Population, New Users and Gender per Department/Program',
            2 => '7.2 Report 2 - Employee Population, New Users and Gender per Department/Office',
            3 => '8.1 Report 3 - Course/Major and Year Level',
            4 => '8.2 Report 4 - Vulnerable Sectors and Socio-Economic Profile',
            5 => '8.3 Report 5 - Academic Background and Honors',
            6 => '8.4 Report 6 - Retention, LOA and Attrition Reasons',
            7 => '8.5 Report 7 - Student GAD Training and Seminar Participation',
            8 => '9.1 Report 8 - Appointment Status and Salary Grade',
            9 => '9.2 Report 9 - Educational Attainment and Civil Service Eligibility',
            10 => '9.3 Report 10 - Vulnerable Sectors and Household Profile',
            11 => '9.4 Report 11 - Leadership, Civic Involvement and Organization Membership',
            12 => '9.5 Report 12 - GAD Training Hours and Capacity Building',
            15 => '10.3 Report 15 - Incident Disaggregation by Sex, Gender and Campus Role',
        ];

        foreach ($reportTitles as $reportNumber => $title) {
            $rows[] = [$title];
            if ($reportNumber === 6) {
                $rows[] = [6, 'Pending data source: retention, LOA, and attrition status/reason/date fields are not present.'];
                continue;
            }

            $reportRows = $partBData['reports'][$reportNumber] ?? [];
            if (!$reportRows) {
                $rows[] = [$reportNumber, 'No records for this period'];
                continue;
            }

            foreach ($reportRows as $reportRow) {
                $rows[] = [
                    $reportNumber,
                    $reportRow['category'],
                    $reportRow['male'] . ' / ' . $reportRow['male_percent'] . '%',
                    $reportRow['female'] . ' / ' . $reportRow['female_percent'] . '%',
                    $reportRow['total'],
                    $reportRow['genders']['Lesbian'],
                    $reportRow['genders']['Gay'],
                    $reportRow['genders']['Bisexual'],
                    $reportRow['genders']['Transsexual'],
                    $reportRow['genders']['Cisgender/Heterosexual'],
                    $reportRow['genders']['Non-Binary'],
                    $reportRow['genders']['Prefer Not to Say'],
                ];
            }
        }

        return $rows;
    }

    private function incidentAnalyticsSheetRows(array $partBData, array $data): array
    {
        $rows = [
            ['Safe Spaces and Incident Analytics'],
            ['Coverage Period', $data['period_label'], $data['start_date'] . ' - ' . $data['end_date']],
            ['Access', 'GAD Administrator only; aggregate data only; reporter aliases are not included.'],
            [],
            ['Report 13 - Departmental Incident Volume and Ranking'],
            ['Department', 'Student Reports', 'Employee Reports', 'Total', '% Share', 'Resolution Rate'],
        ];

        if (!$partBData['incident_departments']) {
            $rows[] = ['No records for this period'];
        } else {
            foreach ($partBData['incident_departments'] as $row) {
                $rows[] = [
                    $row['department'],
                    $row['student'],
                    $row['employee'],
                    $row['total'],
                    $row['share'] . '%',
                    $row['resolution_rate'] . '%',
                ];
            }
        }

        $rows[] = [];
        $rows[] = ['Report 14 - Department x Incident Type, Location, Status and Priority'];
        $rows[] = ['Department', 'Incident Type', 'Count', '#1 Type per Department', 'Most Frequent Location', 'Pending', 'Resolved', 'Priority Counts'];
        if (!$partBData['incident_types']) {
            $rows[] = ['No records for this period'];
        } else {
            foreach ($partBData['incident_types'] as $row) {
                $rows[] = [
                    $row['department'],
                    $row['nature'],
                    $row['count'],
                    $row['top_type'],
                    $row['top_location'],
                    $row['pending'],
                    $row['resolved'],
                    $row['priority_summary'],
                ];
            }
        }

        $rows[] = [];
        $rows[] = ['Table 4 - Sex-Disaggregated Cyber-Harassment Cases by Department'];
        $rows[] = ['Source', 'Cyber-harassment incident records within the selected period, linked to users.department and personal_information.sex. Male/Female counts are distinct linked reporters; case counts include every matching report. Other Status contains values outside Pending, In-Progress / Under Review, and Resolved.'];
        if (!$partBData['cyber_harassment_departments']) {
            $rows[] = ['No cyber-harassment reports for this period'];
        } else {
            $rows[] = ['Department', 'Male Reporters', 'Female Reporters', 'Cyber-Harassment Reports', 'Pending', 'In-Progress', 'Resolved', 'Other Status'];
            foreach ($partBData['cyber_harassment_departments'] as $row) {
                $rows[] = [
                    $row['department'],
                    $row['male'],
                    $row['female'],
                    $row['total'],
                    $row['pending'],
                    $row['in_progress'],
                    $row['resolved'],
                    $row['other'],
                ];
            }
        }

        $rows[] = [];
        $rows[] = ['Report 15 respondent Sex/Gender and relationship fields are pending data source. Complainant breakdown is available in the Sex-Disaggregated Data sheet.'];

        return $rows;
    }

    private function suggestionDisaggregationSheetRows(array $partBData, array $data): array
    {
        $rows = [
            ['Suggestions by Sex and Gender'],
            ['Coverage Period', $data['period_label'], $data['start_date'] . ' - ' . $data['end_date']],
            ['Access', 'GAD Administrator only; anonymous/unlinked submissions are not identified.'],
            ['Logic', 'Anonymous and unlinked submissions are reported separately as Not disclosed. Gender/SOGIESC cells with f < 5 are suppressed as <5.'],
            [],
            ['Category', 'Male (f / %)', 'Female (f / %)', 'Total (N)', 'Lesbian', 'Gay', 'Bisexual', 'Transsexual', 'Cisgender/Heterosexual', 'Non-Binary', 'Prefer Not to Say'],
        ];

        if (!$partBData['suggestion_rows']) {
            $rows[] = ['No records for this period'];
        } else {
            foreach ($partBData['suggestion_rows'] as $row) {
                $rows[] = [
                    $row['category'],
                    $row['male'] . ' / ' . $row['male_percent'] . '%',
                    $row['female'] . ' / ' . $row['female_percent'] . '%',
                    $row['total'],
                    $row['genders']['Lesbian'],
                    $row['genders']['Gay'],
                    $row['genders']['Bisexual'],
                    $row['genders']['Transsexual'],
                    $row['genders']['Cisgender/Heterosexual'],
                    $row['genders']['Non-Binary'],
                    $row['genders']['Prefer Not to Say'],
                ];
            }
        }

        $rows[] = [];
        $rows[] = ['Not Disclosed Suggestions'];
        $rows[] = ['Suggestion Category', 'Anonymous / Unlinked Count'];
        if (!$partBData['anonymous_suggestion_rows']) {
            $rows[] = ['No records for this period'];
        } else {
            foreach ($partBData['anonymous_suggestion_rows'] as $row) {
                $rows[] = [$row['category'], $row['count']];
            }
        }

        return $rows;
    }

    private function satisfactionDisaggregationSheetRows(array $partBData, array $data): array
    {
        $rows = [
            ['Customer Satisfaction and Survey Findings by Sex and Gender'],
            ['Coverage Period', $data['period_label'], $data['start_date'] . ' - ' . $data['end_date']],
            ['Access', 'GAD Administrator only; groups with fewer than 5 valid responses are omitted.'],
            ['Logic', 'Average = mean of valid responses; favorable % = ratings 3 or 4 divided by valid responses.'],
            [],
            ['Question', 'Sex', 'Gender/SOGIESC', 'Responses (N)', 'Average', 'Favorable %', 'Strongly Disagree', 'Disagree', 'Agree', 'Strongly Agree'],
        ];

        if (!$partBData['satisfaction_rows']) {
            $rows[] = ['No groups have at least 5 valid responses for this period'];
        } else {
            foreach ($partBData['satisfaction_rows'] as $row) {
                $rows[] = [
                    $row['question'],
                    $row['sex'],
                    $row['gender'],
                    $row['responses'],
                    $row['average'],
                    $row['favorable'] . '%',
                    $row['counts'][1],
                    $row['counts'][2],
                    $row['counts'][3],
                    $row['counts'][4],
                ];
            }
        }

        return $rows;
    }

    private function trainingSummarySheetRows(array $partBData, array $data): array
    {
        $rows = [
            ['GAD Training Hours and Capacity Building'],
            ['Coverage Period', $data['period_label'], $data['start_date'] . ' - ' . $data['end_date']],
            ['Access', 'GAD Administrator only; names are excluded.'],
            [],
            ['Department', 'Total Training Hours', 'Number of Trainings', 'Training Titles'],
        ];

        if (!$partBData['training_summary']) {
            $rows[] = ['No training records with dates in this period'];
        } else {
            foreach ($partBData['training_summary'] as $row) {
                $rows[] = [
                    $row['department'],
                    $row['hours'],
                    $row['training_count'],
                    $row['titles'] ? implode('; ', $row['titles']) : 'Not reported',
                ];
            }
        }

        return $rows;
    }

    private function sexDisaggregatedData($students, $employees, $usersById, $reports, $suggestions, $satisfactionSurveys, Carbon $startDate, Carbon $endDate): array
    {
        $studentsInPeriod = $students->filter(fn (User $user) => $user->created_at && $user->created_at->betweenIncluded($startDate, $endDate))->values();
        $employeesInPeriod = $employees->filter(fn (User $user) => $user->created_at && $user->created_at->betweenIncluded($startDate, $endDate))->values();

        return [
            'coverage_start' => $startDate->format('M d, Y'),
            'coverage_end' => $endDate->format('M d, Y'),
            'student_count' => $students->count(),
            'employee_count' => $employees->count(),
            'incident_count' => $reports->count(),
            'suggestion_count' => $suggestions->count(),
            'satisfaction_count' => $satisfactionSurveys->count(),
            'reports' => [
                1 => $this->populationRows($students, $startDate, $endDate, false),
                2 => $this->populationRows($employees, $startDate, $endDate, true),
                3 => $this->profileRows($studentsInPeriod, fn (User $user) => [
                    trim(($user->department ?: 'Program not reported') . ' / ' . (data_get($user->student_information, 'year_level') ?: 'Year level not reported')),
                ]),
                4 => $this->profileRows($studentsInPeriod, fn (User $user) => $this->studentSocioeconomicCategories($user)),
                5 => $this->profileRows($studentsInPeriod, fn (User $user) => $this->studentAcademicCategories($user)),
                6 => [],
                7 => $this->profileRows($studentsInPeriod, fn (User $user) => $this->studentTrainingCategories($user)),
                8 => $this->profileRows($employeesInPeriod, fn (User $user) => $this->employeeAppointmentCategories($user)),
                9 => $this->profileRows($employeesInPeriod, fn (User $user) => $this->employeeEducationCategories($user)),
                10 => $this->profileRows($employeesInPeriod, fn (User $user) => $this->employeeHouseholdCategories($user)),
                11 => $this->profileRows($employeesInPeriod, fn (User $user) => $this->employeeOrganizationCategories($user)),
                12 => $this->profileRows($employeesInPeriod, fn (User $user) => $this->employeeTrainingCategories($user, $startDate, $endDate)),
                15 => $this->incidentReporterRows($reports, $usersById),
            ],
            'incident_departments' => $this->incidentDepartmentRows($reports, $usersById),
            'cyber_harassment_departments' => $this->cyberHarassmentDepartmentRows($reports, $usersById),
            'incident_types' => $this->incidentTypeRows($reports, $usersById),
            'suggestion_rows' => $this->suggestionRows($suggestions),
            'anonymous_suggestion_rows' => $this->anonymousSuggestionRows($suggestions),
            'training_summary' => $this->employeeTrainingSummary($employeesInPeriod, $startDate, $endDate),
            'satisfaction_rows' => $this->satisfactionRows($satisfactionSurveys),
        ];
    }

    private function populationRows($users, Carbon $startDate, Carbon $endDate, bool $isEmployee): array
    {
        $records = [];
        foreach ($users as $user) {
            $department = trim((string) ($user->department ?: 'Department not reported'));
            $status = $this->accountStatusLabel((string) ($user->account_status ?: 'Active'));
            $role = $isEmployee
                ? ucfirst(str_replace('_', ' ', trim((string) ($user->employee_category ?: 'Employee category not reported'))))
                : 'Student';
            $base = "{$department} / {$role} / Population / Status: {$status}";
            $records[] = $this->disaggregationRecord($user, $base);

            if ($user->created_at && $user->created_at->betweenIncluded($startDate, $endDate)) {
                $records[] = $this->disaggregationRecord($user, "{$department} / {$role} / New user / Status: {$status}");
            }
        }

        return $this->aggregateDisaggregationRows($records);
    }

    private function profileRows($users, callable $categoryResolver): array
    {
        $records = [];
        foreach ($users as $user) {
            foreach (array_unique(array_filter($categoryResolver($user), fn ($category) => is_string($category) && trim($category) !== '')) as $category) {
                $records[] = $this->disaggregationRecord($user, trim($category));
            }
        }

        return $this->aggregateDisaggregationRows($records);
    }

    private function disaggregationRecord(User $user, string $category): array
    {
        $sex = data_get($user->personal_information, 'sex', $user->gender);

        return [
            'category' => $category,
            'sex' => $this->normalizeSex($sex),
            'gender' => $this->normalizeGenderIdentity(data_get($user->personal_information, 'gender_identity')),
        ];
    }

    private function aggregateDisaggregationRows(array $records): array
    {
        $groups = [];
        foreach ($records as $record) {
            if (!in_array($record['sex'], ['Male', 'Female'], true)) {
                continue;
            }
            $category = $record['category'];
            $groups[$category] ??= [
                'category' => $category,
                'male' => 0,
                'female' => 0,
                'genders' => array_fill_keys(['Lesbian', 'Gay', 'Bisexual', 'Transsexual', 'Cisgender/Heterosexual', 'Non-Binary', 'Prefer Not to Say'], 0),
            ];

            $groups[$category][strtolower($record['sex'])]++;
            if ($record['gender'] !== null) {
                $groups[$category]['genders'][$record['gender']]++;
            }
        }

        ksort($groups, SORT_NATURAL | SORT_FLAG_CASE);

        return array_map(function (array $group): array {
            $total = $group['male'] + $group['female'];
            $group['total'] = $total;
            $group['male_percent'] = $total > 0 ? number_format($group['male'] / $total * 100, 1) : '0.0';
            $group['female_percent'] = $total > 0 ? number_format($group['female'] / $total * 100, 1) : '0.0';
            foreach ($group['genders'] as $gender => $count) {
                $group['genders'][$gender] = $count < 5 ? '<5' : $count;
            }

            return $group;
        }, array_values($groups));
    }

    private function normalizeSex($value): ?string
    {
        return match (strtolower(trim((string) $value))) {
            'male' => 'Male',
            'female' => 'Female',
            default => null,
        };
    }

    private function accountStatusLabel(string $value): string
    {
        return match (strtolower(trim($value))) {
            'active', 'approved' => 'Active',
            'rejected' => 'Rejected',
            'pending', '' => 'Pending',
            default => ucfirst(strtolower(trim($value))),
        };
    }

    private function normalizeGenderIdentity($value): ?string
    {
        $normalized = strtolower(trim((string) $value));

        return match ($normalized) {
            'lesbian' => 'Lesbian',
            'gay' => 'Gay',
            'bisexual', 'bi' => 'Bisexual',
            'transsexual', 'transgender' => 'Transsexual',
            'cisgender/heterosexual', 'cisgender', 'heterosexual' => 'Cisgender/Heterosexual',
            'non-binary', 'nonbinary', 'non binary' => 'Non-Binary',
            'prefer not to say', 'prefer not to disclose' => 'Prefer Not to Say',
            default => null,
        };
    }

    private function studentSocioeconomicCategories(User $user): array
    {
        $categories = [];
        $familyType = strtolower((string) data_get($user->family_background, 'family_type'));
        if (str_contains($familyType, 'single-parent') || str_contains($familyType, 'solo-parent')) {
            $categories[] = 'Solo-parent family';
        }

        $workType = trim((string) data_get($user->student_information, 'financing_other_work'));
        if ($workType !== '') {
            $categories[] = 'Working student / ' . $this->classifyWorkType($workType);
        }

        foreach ($this->valueList(data_get($user->personal_information, 'disabilities')) as $disability) {
            if (strtolower($disability) !== 'none') {
                $categories[] = 'PWD / ' . (strtolower($disability) === 'other' ? 'Other disability' : $disability);
            }
        }

        $income = $this->incomeBracket(data_get($user->family_background, 'monthly_income'));
        if ($income !== null) {
            $categories[] = 'Monthly family income / ' . $income;
        }

        $ownership = trim((string) data_get($user->family_background, 'home_ownership_type'));
        if ($ownership !== '') {
            $categories[] = 'Home ownership / ' . $ownership;
        }

        foreach ($this->valueList(data_get($user->student_information, 'financing_sources')) as $source) {
            $categories[] = 'Schooling financier / ' . $this->classifyFinancier($source);
        }

        return $categories;
    }

    private function studentAcademicCategories(User $user): array
    {
        $categories = [];
        $schoolType = trim((string) data_get($user->student_information, 'school_type'));
        if ($schoolType !== '') {
            $categories[] = 'Feeder school / ' . $schoolType;
        }

        $achievements = $this->valueList(data_get($user->student_information, 'achievements'));
        $otherRank = trim((string) data_get($user->student_information, 'achievement_other_rank'));
        $achievements[] = $otherRank;
        foreach ($achievements as $achievement) {
            $normalized = strtolower($achievement);
            foreach (['valedictorian' => 'Valedictorian', 'salutatorian' => 'Salutatorian', 'honorable mention' => 'Honorable Mention'] as $needle => $label) {
                if (str_contains($normalized, $needle)) {
                    $categories[] = 'Academic honor / ' . $label;
                }
            }
            if ($achievement !== '' && !preg_match('/valedictorian|salutatorian|honorable mention/i', $achievement)) {
                $categories[] = 'Academic honor / Other';
            }
        }

        return $categories;
    }

    private function studentTrainingCategories(User $user): array
    {
        $attended = data_get($user->student_information, 'gad_training_attended');
        if ($attended === null) {
            return [];
        }

        return [trim((string) ($user->department ?: 'Department not reported')) . ' / GAD training / ' . ($attended ? 'Yes' : 'No')];
    }

    private function employeeAppointmentCategories(User $user): array
    {
        $rows = $this->recordList($user->work_experience);
        if (!$rows) {
            return ['Appointment status / Not reported'];
        }

        $categories = [];
        foreach ($rows as $row) {
            if (!is_array($row)) {
                continue;
            }
            $status = $this->classifyAppointmentStatus((string) ($row['appointment_status'] ?? ''));
            $grade = $this->normalizeSalaryGrade((string) ($row['grade_step'] ?? ''));
            $categories[] = trim((string) ($user->department ?: 'Department not reported'))
                . ' / ' . trim((string) ($user->employee_category ?: 'Employee category not reported'))
                . ' / Appointment: ' . $status . ' / Salary grade: ' . $grade;
        }

        return $categories;
    }

    private function employeeEducationCategories(User $user): array
    {
        $categories = [];
        foreach ($this->recordList($user->employee_education) as $row) {
            if (is_array($row) && trim((string) ($row['degree'] ?? '')) !== '') {
                $categories[] = 'Educational attainment / ' . trim((string) $row['degree']);
            }
        }
        foreach ($this->recordList($user->civil_service_eligibility) as $row) {
            if (is_array($row) && trim((string) ($row['career'] ?? '')) !== '') {
                $categories[] = 'Civil service eligibility / ' . trim((string) $row['career']);
            }
        }

        return $categories;
    }

    private function employeeHouseholdCategories(User $user): array
    {
        $categories = [];
        $familyType = strtolower((string) data_get($user->family_background, 'family_type'));
        if (str_contains($familyType, 'single-parent') || str_contains($familyType, 'solo-parent')) {
            $categories[] = 'Solo-parent employee';
        }
        foreach ($this->valueList(data_get($user->personal_information, 'disabilities')) as $disability) {
            if (strtolower($disability) !== 'none') {
                $categories[] = 'PWD / ' . (strtolower($disability) === 'other' ? 'Other disability' : $disability);
            }
        }
        $income = $this->incomeBracket(data_get($user->family_background, 'monthly_income'));
        if ($income !== null) {
            $categories[] = 'Income / ' . $income;
        }
        $children = filter_var(data_get($user->family_background, 'children_total'), FILTER_VALIDATE_INT);
        if ($children !== false && $children !== null) {
            $categories[] = 'Number of children / ' . ($children === 0 ? '0' : ($children <= 2 ? '1–2' : '3 or more'));
        }

        return $categories;
    }

    private function employeeOrganizationCategories(User $user): array
    {
        $text = strtolower(
            (string) data_get($user->other_information, 'emp_membership') . ' '
            . json_encode($user->voluntary_work ?? [], JSON_UNESCAPED_UNICODE)
        );
        $categories = [];
        foreach (['CODI', 'GFPS', 'BAC', 'HRMPSB'] as $organization) {
            if (preg_match('/\\b' . preg_quote(strtolower($organization), '/') . '\\b/', $text)) {
                $categories[] = 'Organization / ' . $organization;
            }
        }
        if (trim($text) !== '' && !$categories) {
            $categories[] = 'Civic / other organization involvement reported';
        }

        return $categories;
    }

    private function employeeTrainingCategories(User $user, Carbon $startDate, Carbon $endDate): array
    {
        $categories = [];
        foreach ($this->recordList($user->gad_training) as $row) {
            if (!is_array($row)) {
                continue;
            }
            $trainingDate = $row['from'] ?? $row['to'] ?? null;
            if (!$trainingDate || !Carbon::hasFormat((string) $trainingDate, 'Y-m-d')) {
                continue;
            }
            $date = Carbon::createFromFormat('Y-m-d', (string) $trainingDate);
            if (!$date->betweenIncluded($startDate, $endDate)) {
                continue;
            }
            $title = trim((string) ($row['title'] ?? 'Training title not reported'));
            $categories[] = trim((string) ($user->department ?: 'Department not reported')) . ' / Training: ' . $title;
        }

        return $categories;
    }

    private function employeeTrainingSummary($employees, Carbon $startDate, Carbon $endDate): array
    {
        $summary = [];
        foreach ($employees as $user) {
            $department = trim((string) ($user->department ?: 'Department not reported'));
            foreach ($this->recordList($user->gad_training) as $row) {
                if (!is_array($row)) {
                    continue;
                }
                $trainingDate = $row['from'] ?? $row['to'] ?? null;
                if (!$trainingDate || !Carbon::hasFormat((string) $trainingDate, 'Y-m-d')) {
                    continue;
                }
                $date = Carbon::createFromFormat('Y-m-d', (string) $trainingDate);
                if (!$date->betweenIncluded($startDate, $endDate)) {
                    continue;
                }
                $summary[$department] ??= ['department' => $department, 'hours' => 0.0, 'training_count' => 0, 'titles' => []];
                $summary[$department]['training_count']++;
                $hours = $row['hours'] ?? null;
                if (is_numeric($hours)) {
                    $summary[$department]['hours'] += (float) $hours;
                }
                $title = trim((string) ($row['title'] ?? ''));
                if ($title !== '') {
                    $summary[$department]['titles'][$title] = $title;
                }
            }
        }

        return array_values(array_map(function (array $row): array {
            $row['hours'] = number_format($row['hours'], 1);
            $row['titles'] = array_values($row['titles']);

            return $row;
        }, $summary));
    }

    private function incomeBracket($value): ?string
    {
        $raw = trim((string) $value);
        if ($raw === '') {
            return null;
        }
        $amount = preg_replace('/[^0-9.]/', '', $raw);
        if ($amount !== '' && is_numeric($amount)) {
            $amount = (float) $amount;

            return match (true) {
                $amount <= 10000 => '10,000 or below',
                $amount < 30000 => '10,001–29,999',
                $amount < 50000 => '30,000–49,999',
                default => '50,000 and above',
            };
        }

        return 'Bracket not normalized';
    }

    private function classifyWorkType(string $value): string
    {
        $normalized = strtolower($value);

        return match (true) {
            str_contains($normalized, 'self') => 'Self-employed',
            str_contains($normalized, 'part') => 'Part-time',
            str_contains($normalized, 'full') => 'Full-time',
            default => 'Other work reported',
        };
    }

    private function classifyFinancier(string $value): string
    {
        $normalized = strtolower($value);

        return match (true) {
            str_contains($normalized, 'parent'), str_contains($normalized, 'guardian') => 'Parent/guardian',
            str_contains($normalized, 'scholar') => 'Scholarship',
            str_contains($normalized, 'self') => 'Self',
            str_contains($normalized, 'relative') => 'Relative',
            default => 'Other financier reported',
        };
    }

    private function classifyAppointmentStatus(string $value): string
    {
        $normalized = strtolower(trim($value));

        return match (true) {
            str_contains($normalized, 'permanent') => 'Permanent',
            str_contains($normalized, 'temporary') => 'Temporary',
            str_contains($normalized, 'contractual'), str_contains($normalized, 'cos') => 'Contractual/COS',
            str_contains($normalized, 'job order') => 'Job Order',
            str_contains($normalized, 'part-time'), str_contains($normalized, 'part time') => 'Part-Time',
            default => 'Not reported or unclassified',
        };
    }

    private function normalizeSalaryGrade(string $value): string
    {
        if (preg_match('/(?:sg\\s*)?\\d{1,2}(?:\\s*\\/\\s*step\\s*\\d{1,2})?/i', trim($value), $matches)) {
            return strtoupper(preg_replace('/\\s+/', ' ', trim($matches[0])));
        }

        return 'Not reported or unclassified';
    }

    private function valueList($value): array
    {
        if (is_array($value)) {
            $items = [];
            foreach ($value as $item) {
                $items = array_merge($items, $this->valueList($item));
            }

            return $items;
        }
        if (!is_scalar($value)) {
            return [];
        }

        return array_values(array_filter(array_map('trim', preg_split('/[,;\r\n]+/', (string) $value) ?: [])));
    }

    private function recordList($value): array
    {
        if (is_array($value)) {
            return array_is_list($value) ? $value : [$value];
        }
        if (is_string($value) && trim($value) !== '') {
            $decoded = json_decode($value, true);

            return is_array($decoded) ? (array_is_list($decoded) ? $decoded : [$decoded]) : [];
        }

        return [];
    }

    private function cyberHarassmentDepartmentRows($reports, $usersById): array
    {
        $departments = [];

        foreach ($reports as $report) {
            $nature = strtolower(trim((string) $report->nature));
            if (!str_contains($nature, 'cyber') || !str_contains($nature, 'harass')) {
                continue;
            }

            $reporter = $usersById->get($report->getAttribute('user_id'));
            $department = trim((string) ($reporter?->department ?: ''));
            $departmentKey = $department !== '' ? $department : 'Not disclosed';
            $departments[$departmentKey] ??= [
                'department' => $departmentKey,
                'male' => 0,
                'female' => 0,
                'total' => 0,
                'pending' => 0,
                'in_progress' => 0,
                'resolved' => 0,
                'other' => 0,
                'male_reporters' => [],
                'female_reporters' => [],
            ];

            $row = &$departments[$departmentKey];
            $row['total']++;
            $sex = $reporter
                ? $this->normalizeSex(data_get($reporter->personal_information, 'sex', $reporter->gender))
                : null;
            if ($reporter && $sex === 'Male') {
                $row['male_reporters'][$reporter->id] = true;
            } elseif ($reporter && $sex === 'Female') {
                $row['female_reporters'][$reporter->id] = true;
            }

            $status = strtolower(trim((string) $report->status));
            if ($status === 'resolved' || $status === 'closed') {
                $row['resolved']++;
            } elseif (str_contains($status, 'progress') || str_contains($status, 'review')) {
                $row['in_progress']++;
            } elseif ($status === 'pending' || $status === '') {
                $row['pending']++;
            } else {
                $row['other']++;
            }
        }
        unset($row);

        if (!$departments) {
            return [];
        }

        foreach ($departments as &$row) {
            $row['male'] = count($row['male_reporters']);
            $row['female'] = count($row['female_reporters']);
            unset($row['male_reporters'], $row['female_reporters']);
        }
        unset($row);

        $total = ['department' => 'UNIVERSITY TOTAL', 'male' => 0, 'female' => 0, 'total' => 0, 'pending' => 0, 'in_progress' => 0, 'resolved' => 0, 'other' => 0];
        foreach ($departments as $row) {
            foreach (['male', 'female', 'total', 'pending', 'in_progress', 'resolved', 'other'] as $field) {
                $total[$field] += $row[$field];
            }
        }
        $departments['UNIVERSITY TOTAL'] = $total;

        return array_values($departments);
    }

    private function incidentDepartmentRows($reports, $usersById): array
    {
        $departments = [];
        foreach ($reports as $report) {
            $reporter = $usersById->get($report->getAttribute('user_id'));
            $department = trim((string) ($reporter?->department ?: 'Not disclosed'));
            $role = strtolower((string) ($reporter?->account_type ?? $reporter?->role));
            $departments[$department] ??= ['department' => $department, 'student' => 0, 'employee' => 0, 'total' => 0, 'resolved' => 0, 'pending' => 0, 'priorities' => ['High' => 0, 'Normal' => 0, 'Low' => 0]];
            $departments[$department]['total']++;
            if ($role === 'student') {
                $departments[$department]['student']++;
            } elseif ($role === 'employee') {
                $departments[$department]['employee']++;
            }
            $status = strtolower((string) $report->status);
            if ($status === 'resolved') {
                $departments[$department]['resolved']++;
            } elseif ($status === 'pending') {
                $departments[$department]['pending']++;
            }
            $priority = ucfirst(strtolower((string) ($report->priority ?: 'Normal')));
            if (isset($departments[$department]['priorities'][$priority])) {
                $departments[$department]['priorities'][$priority]++;
            }
        }
        $totalReports = $reports->count();
        foreach ($departments as &$row) {
            $row['share'] = $totalReports > 0 ? number_format($row['total'] / $totalReports * 100, 1) : '0.0';
            $row['resolution_rate'] = $row['total'] > 0 ? number_format($row['resolved'] / $row['total'] * 100, 1) : '0.0';
            $row['priority_summary'] = 'High ' . $row['priorities']['High'] . ' / Normal ' . $row['priorities']['Normal'] . ' / Low ' . $row['priorities']['Low'];
        }
        unset($row);
        usort($departments, fn (array $a, array $b) => $b['total'] <=> $a['total']);

        return $departments;
    }

    private function incidentTypeRows($reports, $usersById): array
    {
        $groups = [];
        $departmentLocations = [];
        foreach ($reports as $report) {
            $reporter = $usersById->get($report->getAttribute('user_id'));
            $department = trim((string) ($reporter?->department ?: 'Not disclosed'));
            $nature = trim((string) ($report->nature ?: 'Nature not reported'));
            $key = $department . '|' . $nature;
            $groups[$key] ??= ['department' => $department, 'nature' => $nature, 'count' => 0, 'locations' => [], 'pending' => 0, 'resolved' => 0, 'priorities' => []];
            $groups[$key]['count']++;
            $location = trim((string) $report->location);
            if ($location !== '') {
                $groups[$key]['locations'][$location] = ($groups[$key]['locations'][$location] ?? 0) + 1;
                $departmentLocations[$department][$location] = ($departmentLocations[$department][$location] ?? 0) + 1;
            }
            if (strtolower((string) $report->status) === 'resolved') {
                $groups[$key]['resolved']++;
            } elseif (strtolower((string) $report->status) === 'pending') {
                $groups[$key]['pending']++;
            }
            $priority = ucfirst(strtolower((string) ($report->priority ?: 'Normal')));
            if (in_array($priority, ['High', 'Normal', 'Low'], true)) {
                $groups[$key]['priorities'][$priority] = ($groups[$key]['priorities'][$priority] ?? 0) + 1;
            }
        }
        $rows = array_values(array_map(function (array $group): array {
            $group['top_location'] = 'Not reported';
            unset($group['locations']);

            return $group;
        }, $groups));
        foreach ($departmentLocations as $department => $locations) {
            arsort($locations);
            $departmentTopLocation[$department] = (string) array_key_first($locations);
        }
        $topTypes = [];
        foreach ($rows as $row) {
            if (!isset($topTypes[$row['department']]) || $row['count'] > $topTypes[$row['department']]['count']) {
                $topTypes[$row['department']] = ['nature' => $row['nature'], 'count' => $row['count']];
            }
        }
        foreach ($rows as &$row) {
            $row['top_type'] = $topTypes[$row['department']]['nature'];
            $row['top_location'] = $departmentTopLocation[$row['department']] ?? 'Not reported';
            $row['priority_summary'] = 'High ' . ($row['priorities']['High'] ?? 0)
                . ' / Normal ' . ($row['priorities']['Normal'] ?? 0)
                . ' / Low ' . ($row['priorities']['Low'] ?? 0);
        }
        unset($row);
        usort($rows, fn (array $a, array $b) => [$a['department'], -$a['count']] <=> [$b['department'], -$b['count']]);

        return $rows;
    }

    private function incidentReporterRows($reports, $usersById): array
    {
        $records = [];
        foreach ($reports as $report) {
            $reporter = $usersById->get($report->getAttribute('user_id'));
            $role = strtolower((string) ($reporter?->account_type ?? $reporter?->role));
            $sex = $reporter
                ? data_get($reporter->personal_information, 'sex', $reporter->gender)
                : $report->getAttribute('gender');
            $records[] = [
                'category' => ucfirst($role ?: 'Complainant role not disclosed'),
                'sex' => $this->normalizeSex($sex),
                'gender' => $reporter ? $this->normalizeGenderIdentity(data_get($reporter->personal_information, 'gender_identity')) : null,
            ];
        }

        return $this->aggregateDisaggregationRows($records);
    }

    private function suggestionRows($suggestions): array
    {
        $records = [];
        foreach ($suggestions as $suggestion) {
            $category = trim((string) ($suggestion->category ?: 'Category not reported'));
            $user = $suggestion->is_anonymous ? null : $suggestion->user;
            if (!$user) {
                continue;
            }
            $records[] = [
                'category' => $category,
                'sex' => $this->normalizeSex(data_get($user->personal_information, 'sex', $user->gender)),
                'gender' => $this->normalizeGenderIdentity(data_get($user->personal_information, 'gender_identity')),
            ];
        }

        return $this->aggregateDisaggregationRows($records);
    }

    private function anonymousSuggestionRows($suggestions): array
    {
        return $suggestions
            ->filter(fn ($suggestion) => $suggestion->is_anonymous || !$suggestion->user)
            ->groupBy(fn ($suggestion) => trim((string) ($suggestion->category ?: 'Category not reported')))
            ->map(fn ($categorySuggestions, $category) => [
                'category' => $category,
                'count' => $categorySuggestions->count(),
            ])
            ->values()
            ->all();
    }

    private function satisfactionRows($surveys): array
    {
        $questions = [
            'The report or suggestion submission process was easy to use.',
            'The instructions and information were clear.',
            'The system made me feel safe sharing my concern or feedback.',
            'The system responded well to my needs.',
            'I would recommend this system to others who need support.',
        ];
        $groups = [];
        foreach ($surveys as $survey) {
            $user = $survey->user;
            if (!$user || !$this->normalizeSex(data_get($user->personal_information, 'sex', $user->gender))) {
                continue;
            }
            $sex = $this->normalizeSex(data_get($user->personal_information, 'sex', $user->gender));
            $gender = $this->normalizeGenderIdentity(data_get($user->personal_information, 'gender_identity'));
            foreach ($questions as $index => $question) {
                $rating = (int) data_get($survey->responses, $index);
                if ($rating < 1 || $rating > 4) {
                    continue;
                }
                $key = $question . '|' . $sex . '|' . ($gender ?? 'Gender not disclosed');
                $groups[$key] ??= ['question' => $question, 'sex' => $sex, 'gender' => $gender ?? 'Gender not disclosed', 'counts' => [1 => 0, 2 => 0, 3 => 0, 4 => 0]];
                $groups[$key]['counts'][$rating]++;
            }
        }

        $eligibleGroups = array_filter($groups, fn (array $group): bool => array_sum($group['counts']) >= 5);

        return array_values(array_map(function (array $group): array {
            $count = array_sum($group['counts']);
            $group['responses'] = $count;
            $group['average'] = number_format(array_sum(array_map(fn ($rating, $frequency) => $rating * $frequency, array_keys($group['counts']), $group['counts'])) / $count, 2);
            $group['favorable'] = number_format(($group['counts'][3] + $group['counts'][4]) / $count * 100, 1);
            $group['pie_image'] = $this->pieImage($group['counts']);

            return $group;
        }, $eligibleGroups));
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
