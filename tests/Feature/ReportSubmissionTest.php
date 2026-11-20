<?php

namespace Tests\Feature;

use App\Models\Report;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ReportSubmissionTest extends TestCase
{
    public function test_report_page_keeps_the_hotline_and_submit_action_after_the_form(): void
    {
        Schema::create('users', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->string('role')->default('student');
            $table->string('cloak_alias')->nullable();
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->unsignedTinyInteger('age')->nullable();
            $table->string('gender')->nullable();
            $table->string('department')->nullable();
            $table->string('phone_number')->nullable();
            $table->json('personal_information')->nullable();
            $table->json('student_information')->nullable();
            $table->timestamps();
        });

        $user = $this->completeStudent();

        $response = $this->actingAs($user)->get(route('student.report'));

        $response->assertOk()
            ->assertSee('Emergency Hotline')
            ->assertSee('Submit Report')
            ->assertSee('Student main navigation')
            ->assertSee('id="studentSidebar"', false)
            ->assertSee('id="studentSidebarBackdrop"', false)
            ->assertSee('aria-controls="studentSidebar"', false)
            ->assertSee(route('student.dashboard'))
            ->assertSee(route('student.messaging'))
            ->assertSee(route('profile.show'))
            ->assertSee(route('personal-data.show'))
            ->assertDontSee('SINAG Menu');

        $html = $response->getContent();
        $this->assertSame(1, preg_match('/<aside class="sidebar student-sidebar".*?<\/aside>/s', $html, $studentSidebar));
        $this->assertStringNotContainsString(route('profile.show'), $studentSidebar[0]);
        $this->assertStringNotContainsString(route('personal-data.show'), $studentSidebar[0]);
        $this->assertLessThan(strpos($html, 'Submit Report'), strpos($html, 'Emergency Hotline'));
    }

    public function test_report_submission_succeeds_when_optional_metadata_columns_are_not_migrated(): void
    {
        Schema::create('users', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->string('role')->default('student');
            $table->string('cloak_alias')->nullable();
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->unsignedTinyInteger('age')->nullable();
            $table->string('gender')->nullable();
            $table->string('department')->nullable();
            $table->string('phone_number')->nullable();
            $table->json('personal_information')->nullable();
            $table->json('student_information')->nullable();
            $table->timestamps();
        });

        Schema::create('reports', function (Blueprint $table): void {
            $table->unsignedBigInteger('id')->primary();
            $table->foreignId('user_id');
            $table->string('incident_id')->unique();
            $table->string('cloak_alias');
            $table->string('nature');
            $table->date('incident_date')->nullable();
            $table->string('location');
            $table->text('description')->nullable();
            $table->string('priority')->default('Medium');
            $table->string('status')->default('Pending');
            $table->json('evidence')->nullable();
            $table->timestamps();
        });

        $user = $this->completeStudent();

        DB::table('reports')->insert([
            'id' => 4,
            'user_id' => $user->id,
            'incident_id' => 'SN-EXIST',
            'cloak_alias' => 'StudentTest',
            'nature' => 'Catcalling',
            'location' => 'Existing location',
            'description' => 'Existing report.',
            'priority' => 'Medium',
            'status' => 'Pending',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $response = $this->actingAs($user)->post(route('reports.store'), [
            'nature' => 'Catcalling',
            'course' => 'BS Information Technology (BSIT)',
            'gender' => 'Female',
            'incident_date' => now()->toDateString(),
            'incident_time' => '12:30',
            'description' => 'A test report description.',
            'location' => 'Test location',
        ]);

        $response->assertRedirect(route('student.report'));
        $response->assertSessionHas('success');
        $this->assertDatabaseCount('reports', 2);
        $this->assertDatabaseHas('reports', [
            'id' => 5,
            'user_id' => $user->id,
            'nature' => 'Catcalling',
            'location' => 'Test location',
        ]);
    }

    public function test_report_submission_succeeds_with_current_metadata_columns(): void
    {
        Storage::fake('local');
        Storage::fake('public');

        Schema::create('users', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->string('role')->default('student');
            $table->string('cloak_alias')->nullable();
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->unsignedTinyInteger('age')->nullable();
            $table->string('gender')->nullable();
            $table->string('department')->nullable();
            $table->string('phone_number')->nullable();
            $table->json('personal_information')->nullable();
            $table->json('student_information')->nullable();
            $table->timestamps();
        });

        Schema::create('reports', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id');
            $table->string('incident_id')->unique();
            $table->string('cloak_alias');
            $table->string('nature');
            $table->date('incident_date')->nullable();
            $table->time('incident_time')->nullable();
            $table->string('course')->nullable();
            $table->string('gender')->nullable();
            $table->string('location');
            $table->text('description')->nullable();
            $table->string('priority')->default('Medium');
            $table->string('status')->default('Pending');
            $table->json('evidence')->nullable();
            $table->timestamps();
        });

        $user = $this->completeStudent();

        $response = $this->actingAs($user)->post(route('reports.store'), [
            'nature' => 'Catcalling',
            'course' => 'BS Information Technology (BSIT)',
            'gender' => 'Female',
            'incident_date' => now()->toDateString(),
            'incident_time' => '12:30',
            'description' => 'A test report description.',
            'location' => 'Test location',
            'evidence' => [UploadedFile::fake()->image('incident.png')],
        ]);

        $response->assertRedirect(route('student.report'));
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('reports', [
            'id' => 1,
            'user_id' => $user->id,
            'nature' => 'Catcalling',
            'incident_time' => '12:30',
            'course' => 'BS Information Technology (BSIT)',
            'gender' => 'Female',
        ]);

        $report = Report::where('user_id', $user->id)->firstOrFail();
        $evidencePath = $report->evidence_files[0];

        Storage::disk('local')->assertExists($evidencePath);
        Storage::disk('public')->assertMissing($evidencePath);
        $this->actingAs($user)
            ->get(route('reports.evidence.view', ['report' => $report->id, 'index' => 0]))
            ->assertOk();
    }

    private function completeStudent(): User
    {
        return User::create([
            'name' => 'Test Student',
            'first_name' => 'Test',
            'last_name' => 'Student',
            'email' => 'student@example.test',
            'password' => 'password',
            'role' => 'student',
            'cloak_alias' => 'StudentTest',
            'phone_number' => '09123456789',
            'age' => 21,
            'gender' => 'Female',
            'department' => 'BS Information Technology',
            'personal_information' => [
                'date_of_birth' => '2005-01-01',
                'place_of_birth' => 'Dagupan City',
                'sex' => 'Female',
                'gender_identity' => 'Woman',
                'citizenship' => 'Filipino',
                'blood_type' => 'O+',
                'height' => '160 cm',
                'weight' => '50 kg',
                'landline_number' => 'N/A',
                'civil_status' => 'Single',
                'current_address' => 'Dagupan City',
                'home_address' => 'Dagupan City',
                'religion' => 'N/A',
            ],
            'student_information' => [
                'year_level' => '1st Year',
                'school_type' => 'Public',
                'last_school_attended' => 'Pangasinan State University',
                'financing_sources' => ['Family'],
            ],
        ]);
    }
}
