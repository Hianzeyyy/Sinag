<?php

namespace Tests\Feature;

use App\Http\Controllers\ExportGADReportController;
use App\Models\User;
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
            $table->string('incident_id');
            $table->string('cloak_alias')->nullable();
            $table->string('nature');
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

    public function test_excel_export_includes_both_information_sheet_datasets(): void
    {
        User::create([
            'name' => 'Student Example',
            'email' => 'student@example.test',
            'password' => 'password',
            'role' => 'student',
            'account_type' => 'student',
            'personal_information' => ['sex' => 'Female'],
            'family_background' => ['family_type' => 'Nuclear'],
            'student_information' => ['year_level' => 'Second Year'],
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
        ]);

        $response = app(ExportGADReportController::class)->export('excel', '30days');
        $workbookPath = $response->getFile()->getPathname();
        $workbook = new ZipArchive;

        try {
            $this->assertSame(true, $workbook->open($workbookPath));
            $this->assertStringContainsString('Student Information', $workbook->getFromName('xl/workbook.xml'));
            $this->assertStringContainsString('Faculty and Non-Teaching', $workbook->getFromName('xl/workbook.xml'));
            $this->assertStringContainsString('Second Year', $workbook->getFromName('xl/worksheets/sheet5.xml'));
            $this->assertStringContainsString('Bachelor of Arts', $workbook->getFromName('xl/worksheets/sheet6.xml'));
            $this->assertStringContainsString('GAD Orientation', $workbook->getFromName('xl/worksheets/sheet6.xml'));
        } finally {
            $workbook->close();
            @unlink($workbookPath);
        }
    }

    public function test_pdf_export_renders_with_information_sheet_sections(): void
    {
        User::create([
            'name' => 'Student Example',
            'email' => 'student@example.test',
            'password' => 'password',
            'role' => 'student',
            'account_type' => 'student',
            'personal_information' => ['sex' => 'Female'],
            'student_information' => ['year_level' => 'Second Year'],
        ]);

        $response = app(ExportGADReportController::class)->export('pdf', '30days');

        $this->assertSame('application/pdf', $response->headers->get('Content-Type'));
        $this->assertStringStartsWith('%PDF-', $response->getContent());
    }
}
