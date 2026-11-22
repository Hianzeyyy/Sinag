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

    public function test_employee_record_serializer_is_rendered_without_a_personal_data_gate_flash(): void
    {
        $this->createUsersTable();
        $user = $this->createUser(['account_type' => 'employee']);

        $this->actingAs($user)
            ->get(route('personal-data.edit'))
            ->assertOk()
            ->assertSee("setJson('employeeEducationJson', education)", false)
            ->assertSee('data-education-row', false);
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
