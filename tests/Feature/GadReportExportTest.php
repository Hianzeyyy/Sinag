<?php

namespace Tests\Feature;

use App\Http\Controllers\ExportGADReportController;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;
use ZipArchive;

class GadReportExportTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Schema::create('users', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->string('role')->default('student');
            $table->string('account_type')->nullable();
            $table->string('phone_number')->nullable();
            $table->string('age')->nullable();
            $table->string('gender')->nullable();
            $table->string('department')->nullable();
            $table->string('employee_category')->nullable();
            $table->string('account_status')->nullable();
            $table->json('personal_information')->nullable();
            $table->json('family_background')->nullable();
            $table->json('student_information')->nullable();
            $table->json('employee_education')->nullable();
            $table->json('civil_service_eligibility')->nullable();
            $table->json('work_experience')->nullable();
            $table->json('voluntary_work')->nullable();
            $table->json('gad_training')->nullable();
            $table->json('other_information')->nullable();
            $table->timestamps();
        });

        Schema::create('reports', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('incident_id');
            $table->string('cloak_alias')->nullable();
            $table->string('nature');
            $table->string('gender')->nullable();
            $table->string('location')->nullable();
            $table->string('priority')->nullable();
            $table->string('status')->nullable();
            $table->date('incident_date')->nullable();
            $table->string('incident_time')->nullable();
            $table->timestamps();
        });

        Schema::create('suggestions', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('category')->nullable();
            $table->text('message')->nullable();
            $table->boolean('is_anonymous')->default(false);
            $table->string('status')->nullable();
            $table->timestamps();
        });

        Schema::create('suggestion_user_upvotes', function (Blueprint $table): void {
            $table->unsignedBigInteger('suggestion_id');
            $table->unsignedBigInteger('user_id');
        });
    }

    public function test_excel_export_includes_student_and_employee_forms_outside_registration_period(): void
    {
        User::create([
            'name' => 'Student Example',
            'email' => 'student@example.test',
            'password' => 'password',
            'role' => 'student',
            'account_type' => 'student',
            'department' => 'Biology',
            'account_status' => 'Active',
            'personal_information' => ['sex' => 'Female'],
            'family_background' => ['family_type' => 'Nuclear'],
            'student_information' => ['year_level' => 'Second Year'],
            'created_at' => now()->subDays(60),
            'updated_at' => now()->subDays(60),
        ]);

        User::create([
            'name' => 'Employee Example',
            'email' => 'employee@example.test',
            'password' => 'password',
            'role' => 'employee',
            'account_type' => 'employee',
            'personal_information' => ['sex' => 'Male'],
            'employee_education' => [['degree' => 'Bachelor of Arts']],
            'gad_training' => [['title' => 'GAD Orientation']],
            'created_at' => now()->subDays(90),
            'updated_at' => now()->subDays(90),
        ]);

        $response = app(ExportGADReportController::class)->export('excel', '30days');
        $workbookPath = $response->getFile()->getPathname();
        $workbook = new ZipArchive;

        try {
            $this->assertSame(true, $workbook->open($workbookPath));
            $this->assertStringContainsString('Student Information', $workbook->getFromName('xl/workbook.xml'));
            $this->assertStringContainsString('Faculty and Non-Teaching', $workbook->getFromName('xl/workbook.xml'));
            $this->assertStringContainsString('GAD Training Summary', $workbook->getFromName('xl/workbook.xml'));
            $studentSheet = $workbook->getFromName('xl/worksheets/sheet5.xml');
            $employeeSheet = $workbook->getFromName('xl/worksheets/sheet6.xml');
            $this->assertStringContainsString('Student Example', $studentSheet);
            $this->assertStringContainsString('Year level', $studentSheet);
            $this->assertStringContainsString('Second Year', $studentSheet);
            $this->assertStringContainsString('Employee Example', $employeeSheet);
            $this->assertStringContainsString('1 - Degree', $employeeSheet);
            $this->assertStringContainsString('Bachelor of Arts', $employeeSheet);
            $this->assertStringContainsString('GAD Orientation', $employeeSheet);
            $workbookXml = $workbook->getFromName('xl/workbook.xml');
            $this->assertStringContainsString('Sex-Disaggregated Data', $workbookXml);
            $this->assertStringContainsString('Incident Analytics', $workbookXml);
            $sexSheet = $workbook->getFromName('xl/worksheets/sheet7.xml');
            $this->assertStringContainsString('Biology / Student / Population / Status: Active', $sexSheet);
            $this->assertStringContainsString('1 / 100.0%', $sexSheet);
            $this->assertStringNotContainsString('{{f}}', $sexSheet);
            $this->assertStringContainsString('Data Dictionary', $workbookXml);
        } finally {
            $workbook->close();
            @unlink($workbookPath);
        }
    }

    public function test_registered_users_export_excludes_the_gad_coordinator(): void
    {
        User::create([
            'name' => 'GAD Coordinator',
            'email' => 'gadcoordinator@psu.edu.ph',
            'password' => 'password',
            'role' => 'admin',
            'account_type' => 'coordinator',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        User::create([
            'name' => 'Student Example',
            'email' => 'student@example.test',
            'password' => 'password',
            'role' => 'student',
            'account_type' => 'student',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $response = app(ExportGADReportController::class)->export('excel', '30days');
        $workbookPath = $response->getFile()->getPathname();
        $workbook = new ZipArchive;

        try {
            $this->assertSame(true, $workbook->open($workbookPath));
            $usersSheet = $workbook->getFromName('xl/worksheets/sheet2.xml');
            $this->assertStringContainsString('Student Example', $usersSheet);
            $this->assertStringNotContainsString('GAD Coordinator', $usersSheet);
            $this->assertStringNotContainsString('gadcoordinator@psu.edu.ph', $usersSheet);
        } finally {
            $workbook->close();
            @unlink($workbookPath);
        }
    }

    public function test_coordinator_excel_omits_registered_users_profiles_and_detailed_gad_reports(): void
    {
        $coordinator = User::create([
            'name' => 'GAD Coordinator',
            'email' => 'gadcoordinator@psu.edu.ph',
            'password' => 'password',
            'role' => 'admin',
            'account_type' => 'coordinator',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        User::create([
            'name' => 'Student Example',
            'email' => 'student@example.test',
            'password' => 'password',
            'role' => 'student',
            'account_type' => 'student',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        User::create([
            'name' => 'Student Private Example',
            'email' => 'student-private@example.test',
            'password' => 'password',
            'role' => 'student',
            'account_type' => 'student',
            'department' => 'Biology',
            'personal_information' => ['sex' => 'Female'],
            'student_information' => ['year_level' => 'Private Year Level'],
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        User::create([
            'name' => 'Employee Private Example',
            'email' => 'employee-private@example.test',
            'password' => 'password',
            'role' => 'employee',
            'account_type' => 'employee',
            'department' => 'Human Resources',
            'work_experience' => ['appointment_status' => 'Private Appointment Status'],
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->actingAs($coordinator);
        $response = app(ExportGADReportController::class)->export('excel', '30days');
        $workbookPath = $response->getFile()->getPathname();
        $workbook = new ZipArchive;

        try {
            $this->assertSame(true, $workbook->open($workbookPath));
            $workbookXml = $workbook->getFromName('xl/workbook.xml');
            $summarySheet = $workbook->getFromName('xl/worksheets/sheet1.xml');
            $this->assertStringNotContainsString('name="Users"', $workbookXml);
            $this->assertStringNotContainsString('name="Student Information"', $workbookXml);
            $this->assertStringNotContainsString('name="Faculty and Non-Teaching"', $workbookXml);
            $this->assertStringContainsString('Incident Reports', $workbookXml);
            $this->assertStringNotContainsString('Sex-Disaggregated Data', $workbookXml);
            $this->assertStringNotContainsString('Incident Analytics', $workbookXml);
            $this->assertStringNotContainsString('GAD Training Summary', $workbookXml);
            $this->assertStringNotContainsString('New Registered Users', $summarySheet);
            $this->assertStringContainsString('Incidents Reported', $summarySheet);

            $worksheetXml = '';
            for ($sheetNumber = 1; $sheetNumber <= $workbook->numFiles; $sheetNumber++) {
                $worksheetXml .= (string) $workbook->getFromName("xl/worksheets/sheet{$sheetNumber}.xml");
            }
            $this->assertStringNotContainsString('Student Private Example', $worksheetXml);
            $this->assertStringNotContainsString('student-private@example.test', $worksheetXml);
            $this->assertStringNotContainsString('Employee Private Example', $worksheetXml);
            $this->assertStringNotContainsString('employee-private@example.test', $worksheetXml);
        } finally {
            $workbook->close();
            @unlink($workbookPath);
        }
    }

    public function test_coordinator_pdf_omits_registered_users_profiles_and_detailed_gad_reports(): void
    {
        $coordinator = User::create([
            'name' => 'GAD Coordinator',
            'email' => 'gadcoordinator@psu.edu.ph',
            'password' => 'password',
            'role' => 'admin',
            'account_type' => 'coordinator',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        User::create([
            'name' => 'Student Private Example',
            'email' => 'student-private@example.test',
            'password' => 'password',
            'role' => 'student',
            'account_type' => 'student',
            'student_information' => ['year_level' => 'Private Year Level'],
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        User::create([
            'name' => 'Employee Private Example',
            'email' => 'employee-private@example.test',
            'password' => 'password',
            'role' => 'employee',
            'account_type' => 'employee',
            'work_experience' => ['appointment_status' => 'Private Appointment Status'],
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $pdf = \Mockery::mock(\Barryvdh\DomPDF\PDF::class);
        $pdf->shouldReceive('setPaper')->once()->with('a4', 'portrait')->andReturnSelf();
        $pdf->shouldReceive('download')->once()->andReturn(response('PDF'));
        Pdf::shouldReceive('loadView')
            ->once()
            ->withArgs(function (string $viewName, array $viewData): bool {
                $html = view($viewName, $viewData)->render();

                $this->assertStringNotContainsString('Registered Users', $html);
                $this->assertStringNotContainsString('New Registered Users', $html);
                $this->assertStringNotContainsString('Student Information Sheets (', $html);
                $this->assertStringNotContainsString('Faculty and Non-Teaching Information Sheets', $html);
                $this->assertStringNotContainsString('Student Private Example', $html);
                $this->assertStringNotContainsString('student-private@example.test', $html);
                $this->assertStringNotContainsString('Employee Private Example', $html);
                $this->assertStringNotContainsString('employee-private@example.test', $html);
                foreach ([
                    '7. Population, New Users and Gender',
                    '7.1 Report 1:',
                    '7.2 Report 2:',
                    '8. Student Reports',
                    '8.1 Report 3:',
                    '8.2 Report 4:',
                    '8.3 Report 5:',
                    '8.4 Report 6:',
                    '8.5 Report 7:',
                    '9. Employee Reports',
                    '9.1 Report 8:',
                    '9.2 Report 9:',
                    '9.3 Report 10:',
                    '9.4 Report 11:',
                    '9.5 Report 12:',
                    '10. Safe Spaces and Incident Analytics',
                    '10.1 Report 13:',
                    '10.2 Report 14:',
                    '10.3 Table 4:',
                    '10.4 Report 15:',
                ] as $restrictedSection) {
                    $this->assertStringNotContainsString($restrictedSection, $html);
                }
                $this->assertStringContainsString('Suggestions and Satisfaction by Sex and Gender', $html);
                $this->assertStringContainsString('1. Incident Reports', $html);

                return true;
            })
            ->andReturn($pdf);

        $this->actingAs($coordinator);
        app(ExportGADReportController::class)->export('pdf', '30days');
    }

    public function test_cyber_harassment_table_uses_linked_database_department_sex_and_status_values(): void
    {
        $users = [
            User::create([
                'name' => 'Nursing Reporter',
                'email' => 'nursing@example.test',
                'password' => 'password',
                'role' => 'coordinator',
                'account_type' => 'coordinator',
                'department' => 'School of Nursing',
                'personal_information' => ['sex' => 'Female'],
            ]),
            User::create([
                'name' => 'Arts Reporter',
                'email' => 'arts@example.test',
                'password' => 'password',
                'role' => 'employee',
                'account_type' => 'employee',
                'department' => 'College of Media Arts',
                'personal_information' => ['sex' => 'Male'],
            ]),
        ];

        foreach ([
            [$users[0], 'Pending'],
            [$users[1], 'Under Review'],
            [$users[1], 'Dismissed'],
        ] as $index => [$user, $status]) {
            \App\Models\Report::create([
                'user_id' => $user->id,
                'incident_id' => 'CASE-' . $index,
                'nature' => 'Cyber-harassment',
                'status' => $status,
            ]);
        }

        \App\Models\Report::create([
            'user_id' => $users[0]->id,
            'incident_id' => 'CASE-OTHER',
            'nature' => 'Catcalling',
            'status' => 'Pending',
        ]);

        $response = app(ExportGADReportController::class)->export('excel', '30days');
        $workbookPath = $response->getFile()->getPathname();
        $workbook = new ZipArchive;

        try {
            $this->assertSame(true, $workbook->open($workbookPath));
            $incidentAnalyticsSheet = $workbook->getFromName('xl/worksheets/sheet8.xml');
            $tableStart = strpos($incidentAnalyticsSheet, 'Table 4 - Sex-Disaggregated Cyber-Harassment Cases by Department');
            $this->assertNotFalse($tableStart);
            $tableSection = substr($incidentAnalyticsSheet, $tableStart, strpos($incidentAnalyticsSheet, 'Report 15 respondent', $tableStart) - $tableStart);
            $document = new \DOMDocument;
            $this->assertTrue($document->loadXML($incidentAnalyticsSheet));
            $xpath = new \DOMXPath($document);
            $xpath->registerNamespace('xlsx', 'http://schemas.openxmlformats.org/spreadsheetml/2006/main');
            $tableRows = [];
            foreach ($xpath->query('//xlsx:sheetData/xlsx:row') as $sheetRow) {
                $values = [];
                foreach ($xpath->query('./xlsx:c/xlsx:is/xlsx:t', $sheetRow) as $cell) {
                    $values[] = $cell->textContent;
                }
                if (in_array($values[0] ?? '', ['School of Nursing', 'College of Media Arts', 'UNIVERSITY TOTAL'], true)) {
                    $tableRows[$values[0]] = $values;
                }
            }

            $this->assertStringContainsString('School of Nursing', $tableSection);
            $this->assertStringContainsString('College of Media Arts', $tableSection);
            $this->assertStringContainsString('UNIVERSITY TOTAL', $tableSection);
            $this->assertStringContainsString('Other Status', $tableSection);
            $this->assertStringNotContainsString('College of Engineering (COE)', $tableSection);
            $this->assertStringNotContainsString('CASE-OTHER', $tableSection);
            $this->assertSame(['School of Nursing', '0', '1', '1', '1', '0', '0', '0'], $tableRows['School of Nursing']);
            $this->assertSame(['College of Media Arts', '1', '0', '2', '0', '1', '0', '1'], $tableRows['College of Media Arts']);
            $this->assertSame(['UNIVERSITY TOTAL', '1', '1', '3', '1', '1', '0', '1'], $tableRows['UNIVERSITY TOTAL']);
        } finally {
            $workbook->close();
            @unlink($workbookPath);
        }
    }

    public function test_pdf_export_renders_information_sheet_sections(): void
    {
        User::create([
            'name' => 'Student Example',
            'email' => 'student@example.test',
            'password' => 'password',
            'role' => 'student',
            'account_type' => 'student',
            'personal_information' => ['sex' => 'Female'],
            'student_information' => ['year_level' => 'Second Year'],
            'created_at' => now()->subDays(60),
            'updated_at' => now()->subDays(60),
        ]);

        $response = app(ExportGADReportController::class)->export('pdf', '30days');

        $this->assertSame('application/pdf', $response->headers->get('Content-Type'));
        $this->assertStringStartsWith('%PDF-', $response->getContent());
    }

    public function test_feedback_only_export_excludes_report_and_profile_sheets(): void
    {
        request()->merge(['include_feedback' => '1']);

        $response = app(ExportGADReportController::class)->export('excel', '30days');
        $workbookPath = $response->getFile()->getPathname();
        $workbook = new ZipArchive;

        try {
            $this->assertSame(true, $workbook->open($workbookPath));
            $workbookXml = $workbook->getFromName('xl/workbook.xml');
            $this->assertStringContainsString('Customer Satisfaction', $workbookXml);
            $this->assertStringContainsString('Survey Findings', $workbookXml);
            $this->assertStringNotContainsString('name="Suggestions"', $workbookXml);
            $this->assertStringNotContainsString('Incident Reports', $workbookXml);
            $this->assertStringNotContainsString('Student Information', $workbookXml);
            $this->assertStringNotContainsString('Faculty and Non-Teaching', $workbookXml);
            $this->assertStringNotContainsString('Users', $workbookXml);
        } finally {
            $workbook->close();
            @unlink($workbookPath);
        }
    }

    public function test_calendar_report_periods_cover_quarters_midyear_and_full_year(): void
    {
        Carbon::setTestNow('2026-11-19 12:00:00');
        $controller = app(ExportGADReportController::class);
        $resolvePeriod = new \ReflectionMethod($controller, 'resolvePeriod');

        try {
            $expected = [
                'q1' => ['2026-01-01', '2026-03-31', 'Quarter 1 (2026)'],
                'q2' => ['2026-04-01', '2026-06-30', 'Quarter 2 (2026)'],
                'q3' => ['2026-07-01', '2026-09-30', 'Quarter 3 (2026)'],
                'q4' => ['2026-10-01', '2026-12-31', 'Quarter 4 (2026)'],
                'midyear' => ['2026-01-01', '2026-06-30', 'Mid-Year (January–June 2026)'],
                'annual' => ['2026-01-01', '2026-12-31', 'Annual (2026)'],
            ];

            foreach ($expected as $period => [$start, $end, $label]) {
                $resolved = $resolvePeriod->invoke($controller, $period);
                $this->assertSame($start, $resolved['start']->toDateString());
                $this->assertSame($end, $resolved['end']->toDateString());
                $this->assertSame($label, $resolved['label']);
            }
        } finally {
            Carbon::setTestNow();
        }
    }

    public function test_sex_disaggregated_rows_use_database_records_and_suppress_small_gender_cells(): void
    {
        $students = collect();
        foreach (range(1, 5) as $index) {
            $students->push(User::create([
                'name' => 'Student ' . $index,
                'email' => 'student' . $index . '@example.test',
                'password' => 'password',
                'role' => 'student',
                'account_type' => 'student',
                'department' => 'Biology',
                'personal_information' => [
                    'sex' => 'Male',
                    'gender_identity' => $index === 1 ? 'Lesbian' : 'Cisgender/Heterosexual',
                ],
                'student_information' => ['year_level' => 'Second Year'],
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }

        $method = new \ReflectionMethod(ExportGADReportController::class, 'sexDisaggregatedData');
        $reportData = $method->invoke(
            app(ExportGADReportController::class),
            $students,
            collect(),
            collect(),
            collect(),
            collect(),
            collect(),
            Carbon::now()->startOfDay(),
            Carbon::now()->endOfDay()
        );
        $studentRow = collect($reportData['reports'][3])->first();

        $this->assertSame('Biology / Second Year', $studentRow['category']);
        $this->assertSame(5, $studentRow['male']);
        $this->assertSame('100.0', $studentRow['male_percent']);
        $this->assertSame(5, $studentRow['total']);
        $this->assertSame('<5', $studentRow['genders']['Lesbian']);
        $this->assertSame('<5', $studentRow['genders']['Cisgender/Heterosexual']);
        $this->assertArrayNotHasKey('name', $studentRow);
    }
}
