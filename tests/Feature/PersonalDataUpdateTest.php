<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class PersonalDataUpdateTest extends TestCase
{
    public function test_personal_data_edits_are_saved_and_redirect_to_the_summary(): void
    {
        $this->createUsersTable();
        $user = $this->createUser();

        $this->actingAs($user)
            ->post(route('profile.update'), [
                'first_name' => 'Updated',
                'middle_name' => 'Test',
                'last_name' => 'Student',
                'age' => 24,
                'gender' => 'Female',
                'department' => 'BS Information Technology',
                'phone_number' => '09123456780',
                'cloak_alias' => 'UpdatedAlias',
                'personal_information' => [
                    'date_of_birth' => '2002-01-15',
                    'place_of_birth' => 'Dagupan',
                    'sex' => 'Female',
                    'gender_identity' => 'Woman',
                    'citizenship' => 'Filipino',
                    'blood_type' => 'O+',
                    'height' => 160,
                    'weight' => 55,
                    'landline_number' => 'N/A',
                    'civil_status' => 'Single',
                    'current_address' => 'Updated current address',
                    'home_address' => 'Updated home address',
                    'religion' => 'Catholic',
                    'disabilities_text' => 'None',
                ],
                'student_information' => [
                    'year_level' => 'Third Year',
                    'school_type' => 'State University',
                    'last_school_attended' => 'Pangasinan State University',
                    'financing_sources_text' => 'Parents',
                ],
            ])
            ->assertRedirect(route('personal-data.show'))
            ->assertSessionHas('status');

        $updatedUser = User::findOrFail($user->id);
        $this->assertSame('Updated Test Student', $updatedUser->name);
        $this->assertSame('Updated current address', $updatedUser->personal_information['current_address']);
        $this->assertSame(['Parents'], $updatedUser->student_information['financing_sources']);

        $this->get(route('personal-data.show'))
            ->assertOk()
            ->assertSee('Updated Test Student')
            ->assertSee('Updated current address');
    }

    public function test_employee_record_fields_submit_without_javascript_serialization(): void
    {
        $this->createUsersTable();
        $user = $this->createUser(['account_type' => 'employee']);

        $this->actingAs($user)
            ->get(route('personal-data.edit'))
            ->assertOk()
            ->assertSee('name="employee_education[3][school]"', false)
            ->assertSee('name="civil_service_eligibility[0][career]"', false)
            ->assertSee('name="work_experience[0][position]"', false)
            ->assertSee('name="voluntary_work[0][org_name]"', false)
            ->assertSee('name="gad_training[0][title]"', false);
    }

    public function test_employee_records_save_and_render_under_their_correct_sections(): void
    {
        $this->createUsersTable();
        $user = $this->createUser([
            'role' => 'employee',
            'account_type' => 'employee',
            'department' => 'Teaching',
        ]);

        $collegeEducation = [
            'level' => 'College',
            'school' => 'Pangasinan State University',
            'degree' => 'Bachelor of Science in Information Technology',
            'from' => '2018',
            'to' => '2022',
        ];
        $employeeEducation = [
            0 => ['level' => 'Elementary', 'school' => '', 'from' => '', 'to' => ''],
            1 => ['level' => 'High School', 'school' => '', 'from' => '', 'to' => ''],
            2 => ['level' => 'Vocational/Trade Course', 'school' => '', 'from' => '', 'to' => ''],
            3 => $collegeEducation,
        ];
        $civilService = [[
            'career' => 'Career Service Professional',
            'rating' => '85.50',
            'exam_date' => '2024-01-10',
            'exam_place' => 'Dagupan',
            'license_num' => '123456',
            'release_date' => '2024-02-01',
        ]];
        $workExperience = [[
            'position' => 'GAD Coordinator',
            'department' => 'GAD Office',
            'from' => '2022-01-01',
            'to' => '2025-01-01',
            'appointment_status' => 'Permanent',
            'salary' => '45000',
            'grade_step' => '18-1',
            'govt_service' => 'Yes',
        ]];
        $voluntaryWork = [[
            'org_name' => 'Community Group',
            'org_address' => 'Dagupan',
            'hours' => '40',
            'position' => 'Volunteer',
            'from' => '2021-01-01',
            'to' => '2021-12-01',
        ]];
        $gadTraining = [[
            'title' => 'Gender Sensitivity Training',
            'from' => '2023-01-01',
            'to' => '2023-01-02',
            'hours' => '8',
            'conducted_by' => 'PSU',
        ]];

        $this->actingAs($user)
            ->post(route('profile.update'), [
                'first_name' => 'Employee',
                'middle_name' => '',
                'last_name' => 'User',
                'age' => 35,
                'gender' => 'Female',
                'department' => 'Teaching',
                'phone_number' => '09123456780',
                'cloak_alias' => 'EmployeeAlias',
                'personal_information' => [
                    'date_of_birth' => '1991-01-15',
                    'place_of_birth' => 'Dagupan',
                    'sex' => 'Female',
                    'gender_identity' => 'Woman',
                    'citizenship' => 'Filipino',
                    'blood_type' => 'O+',
                    'height' => 160,
                    'weight' => 55,
                    'landline_number' => 'N/A',
                    'civil_status' => 'Single',
                    'current_address' => 'Current address',
                    'home_address' => 'Home address',
                    'religion' => 'Catholic',
                    'disabilities_text' => 'None',
                ],
                'employee_education' => $employeeEducation,
                'civil_service_eligibility' => $civilService,
                'work_experience' => $workExperience,
                'voluntary_work' => $voluntaryWork,
                'gad_training' => $gadTraining,
            ])
            ->assertRedirect(route('personal-data.show'))
            ->assertSessionHas('status', 'Personal data saved successfully.');

        $updatedUser = User::findOrFail($user->id);
        $this->assertSame([$collegeEducation], $updatedUser->employee_education);
        $this->assertSame($civilService, $updatedUser->civil_service_eligibility);
        $this->assertSame($workExperience, $updatedUser->work_experience);
        $this->assertSame($voluntaryWork, $updatedUser->voluntary_work);
        $this->assertSame($gadTraining, $updatedUser->gad_training);

        $summary = $this->get(route('personal-data.show'))
            ->assertOk()
            ->assertSeeText('Personal data saved successfully.')
            ->assertSeeText('Career Service Professional')
            ->assertSeeText('GAD Coordinator')
            ->assertSeeText('Community Group')
            ->assertSeeText('Gender Sensitivity Training');
        $this->assertMatchesRegularExpression(
            '/<td><strong>College<\\/strong><\\/td>\\s*<td>Pangasinan State University<\\/td>\\s*<td>Bachelor of Science in Information Technology<\\/td>\\s*<td>2018<\\/td>\\s*<td>2022<\\/td>/',
            $summary->getContent()
        );

        $editForm = $this->get(route('personal-data.edit'))->assertOk();
        $this->assertMatchesRegularExpression(
            '/data-level="College">.*?data-school value="Pangasinan State University".*?data-degree value="Bachelor of Science in Information Technology"/s',
            $editForm->getContent()
        );
    }

    private function createUsersTable(): void
    {
        Schema::create('users', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->string('first_name')->nullable();
            $table->string('middle_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('email')->unique();
            $table->string('password');
            $table->string('role')->default('student');
            $table->string('account_type')->nullable();
            $table->string('account_status')->default('active');
            $table->unsignedTinyInteger('age')->nullable();
            $table->string('gender')->nullable();
            $table->string('department')->nullable();
            $table->string('phone_number')->nullable();
            $table->string('cloak_alias')->nullable();
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
    }

    private function createUser(array $attributes = []): User
    {
        return User::create(array_merge([
            'name' => 'Student User',
            'first_name' => 'Student',
            'last_name' => 'User',
            'email' => 'student@example.test',
            'password' => 'password',
            'role' => 'student',
            'account_type' => 'student',
            'account_status' => 'active',
            'age' => 21,
            'gender' => 'Male',
            'department' => 'BS Information Technology',
            'phone_number' => '09123456789',
            'cloak_alias' => 'StudentOld',
        ], $attributes));
    }
}
