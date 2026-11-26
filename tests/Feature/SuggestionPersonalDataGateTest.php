<?php

namespace Tests\Feature;

use App\Models\User;
use App\Support\MediaResolver;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Http\UploadedFile;
use Tests\Concerns\FakesCloudinaryMedia;
use Tests\TestCase;

class SuggestionPersonalDataGateTest extends TestCase
{
    use FakesCloudinaryMedia;

    public function test_students_and_employees_cannot_access_reports_until_personal_data_is_complete(): void
    {
        $this->createUsersTable();

        foreach ([
            ['role' => 'student', 'account_type' => 'student', 'email' => 'student@example.test'],
            ['role' => 'employee', 'account_type' => 'employee', 'employee_category' => 'teaching', 'email' => 'teaching@example.test'],
            ['role' => 'employee', 'account_type' => 'employee', 'employee_category' => 'non_teaching', 'email' => 'staff@example.test'],
        ] as $attributes) {
            $user = User::create(array_merge([
                'name' => 'Incomplete User',
                'password' => 'password',
            ], $attributes));

            $this->actingAs($user)
                ->get(route('student.report'))
                ->assertRedirect(route('personal-data.edit'))
                ->assertSessionHas('personal_data_required');

            $this->actingAs($user)
                ->post(route('reports.store'), [])
                ->assertRedirect(route('personal-data.edit'))
                ->assertSessionHas('personal_data_required');
        }
    }

    public function test_users_without_completed_personal_data_cannot_access_or_post_suggestions(): void
    {
        $this->createUsersTable();
        $this->createSuggestionTables();

        $user = User::create([
            'name' => 'Test Student',
            'email' => 'student@example.test',
            'password' => 'password',
            'role' => 'student',
        ]);

        $editProfileUrl = route('personal-data.edit', ['return_to_suggestions' => 1]);

        $this->actingAs($user)
            ->get(route('student.boses'))
            ->assertRedirect($editProfileUrl)
            ->assertSessionHas('personal_data_required');

        $response = $this->from(route('student.boses'))
            ->post(route('suggestions.store'), [
                'category' => 'GAD Programs and Services',
            'message' => 'A suggestion shared from an incomplete profile.',
            ]);

        $response->assertRedirect($editProfileUrl);
        $this->assertDatabaseMissing('suggestions', ['user_id' => $user->id]);
    }

    public function test_gad_coordinator_can_view_suggestions_without_a_student_profile(): void
    {
        $this->createUsersTable();
        $this->createSuggestionTables();

        $coordinator = User::create([
            'name' => 'GAD Coordinator',
            'email' => 'gadcoordinator@example.test',
            'password' => 'password',
            'role' => 'admin',
            'account_type' => 'coordinator',
        ]);
        $this->createSuggestion($coordinator, 'Approved', 'Please add more GAD awareness activities.');

        $this->actingAs($coordinator)
            ->get(route('student.boses'))
            ->assertOk()
            ->assertSee('Community suggestions')
            ->assertSee('Please add more GAD awareness activities.')
            ->assertDontSee('id="suggestionModal"', false)
            ->assertDontSee('Write your suggestions...');

        $securityUser = User::create([
            'name' => 'Security User',
            'email' => 'security@example.test',
            'password' => 'password',
            'role' => 'security',
        ]);

        $this->actingAs($securityUser)
            ->get(route('student.boses'))
            ->assertForbidden();
    }

    public function test_suggestions_page_displays_its_empty_state_instead_of_a_blank_page(): void
    {
        $this->createUsersTable();
        $this->createSuggestionTables();

        $user = $this->makeCompletedStudent([
            'name' => 'Test Student',
            'email' => 'student@example.test',
            'password' => 'password',
            'cloak_alias' => 'StudentTest',
        ]);

        $response = $this->actingAs($user)->get(route('student.boses'));

        $response->assertOk();
        $response->assertSee('Community suggestions');
        $response->assertSee('Search suggestions');
        $response->assertDontSee('suggestion-category-filter');
        $response->assertSee('No suggestions yet');
    }

    public function test_satisfaction_survey_waits_for_a_suggestion_moderation_decision(): void
    {
        $this->createUsersTable();
        $this->createSuggestionTables();
        $this->createSatisfactionSurveysTable();
        Schema::create('reports', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id');
            $table->string('status')->default('Pending');
            $table->timestamps();
        });

        $user = $this->makeCompletedStudent();
        $suggestionId = $this->createSuggestion($user, 'Pending', 'Please add more GAD awareness activities.');

        $this->actingAs($user)
            ->get(route('student.boses'))
            ->assertOk()
            ->assertDontSee('id="customerSatisfactionModal"', false);

        DB::table('suggestions')->where('id', $suggestionId)->update(['status' => 'Approved']);

        $this->get(route('student.boses'))
            ->assertOk()
            ->assertSee('id="customerSatisfactionModal"', false)
            ->assertSee('data-auto-show="true"', false)
            ->assertSee('name="subject" value="suggestion"', false)
            ->assertSee('name="subject_id" value="' . $suggestionId . '"', false);

        $this->get(route('student.vault'))
            ->assertOk()
            ->assertSee('data-auto-show="false"', false)
            ->assertSee('data-suggestion-survey-id="' . $suggestionId . '"', false)
            ->assertSee('title="Click to rate this suggestion"', false)
            ->assertSee('>Rate Survey</button>', false);

        $secondSuggestionId = $this->createSuggestion($user, 'Approved', 'Please add a second awareness activity.');
        $this->get(route('student.boses'))
            ->assertOk()
            ->assertSee('data-auto-show="true"', false)
            ->assertSee('name="subject_id" value="' . $secondSuggestionId . '"', false);

        $this->get(route('student.boses'))
            ->assertOk()
            ->assertSee('data-auto-show="false"', false);

        foreach (range(1, 2) as $_) {
            $this->post(route('customer-satisfaction.store'), [
                'subject' => 'suggestion',
                'subject_id' => $secondSuggestionId,
                'rating' => 5,
                'responses' => [4, 4, 4, 4, 4],
            ])->assertRedirect();
        }

        $this->assertSame(1, DB::table('customer_satisfaction_surveys')
            ->where('user_id', $user->id)
            ->where('subject', 'suggestion')
            ->where('subject_id', $secondSuggestionId)
            ->count());

        DB::table('suggestions')->where('id', $suggestionId)->update(['status' => 'Rejected']);

        $this->get(route('student.boses'))
            ->assertOk()
            ->assertDontSee('id="customerSatisfactionModal"', false);

        $this->post(route('customer-satisfaction.store'), [
            'subject' => 'suggestion',
            'subject_id' => $suggestionId,
            'rating' => 5,
            'responses' => [4, 4, 4, 4, 4],
        ])->assertStatus(409);
    }

    public function test_suggestion_survey_lookup_failure_does_not_block_the_suggestions_page(): void
    {
        $this->createUsersTable();
        $this->createSuggestionTables();
        $this->createSatisfactionSurveysTable();

        $user = $this->makeCompletedStudent();
        $this->createSuggestion($user, 'Approved', 'Community suggestions remain available.');

        $database = \Mockery::mock(DB::getFacadeRoot())->makePartial();
        $database->shouldReceive('table')
            ->once()
            ->with('suggestions')
            ->andThrow(new \Illuminate\Database\QueryException(
                'mysql',
                'select * from suggestions',
                [],
                new \RuntimeException('Survey lookup failed'),
            ));
        DB::swap($database);

        $this->actingAs($user)
            ->get(route('student.boses'))
            ->assertOk()
            ->assertSee('Community suggestions remain available.')
            ->assertSee('The satisfaction survey is temporarily unavailable.')
            ->assertDontSee('id="customerSatisfactionModal"', false);
    }

    public function test_satisfaction_survey_is_recorded_for_the_specific_report(): void
    {
        $this->createUsersTable();
        $this->createSatisfactionSurveysTable();
        Schema::create('reports', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id');
            $table->string('incident_id');
            $table->string('cloak_alias')->nullable();
            $table->string('nature')->nullable();
            $table->string('category')->nullable();
            $table->string('location')->nullable();
            $table->text('description')->nullable();
            $table->json('evidence')->nullable();
            $table->string('priority')->nullable();
            $table->string('status')->default('Pending');
            $table->boolean('is_anonymous')->default(true);
            $table->timestamp('resolution_notice_sent_at')->nullable();
            $table->timestamps();
        });
        Schema::create('suggestions', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id');
            $table->string('category')->nullable();
            $table->text('message')->nullable();
            $table->string('status')->default('Pending');
            $table->timestamps();
        });

        $user = $this->makeCompletedStudent();
        $reportId = DB::table('reports')->insertGetId([
            'user_id' => $user->id,
            'incident_id' => 'SN-TEST',
            'cloak_alias' => 'StudentTest',
            'nature' => 'Catcalling',
            'status' => 'Pending',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->actingAs($user)
            ->get(route('student.vault'))
            ->assertOk()
            ->assertDontSee('id="customerSatisfactionModal"', false);

        $this->post(route('customer-satisfaction.store'), [
            'subject' => 'report',
            'subject_id' => $reportId,
            'rating' => 5,
            'responses' => [4, 4, 4, 4, 4],
        ])->assertStatus(409);

        DB::table('reports')->where('id', $reportId)->update(['status' => 'Resolved']);
        $this->getJson('/api/satisfaction/report/'.$reportId)
            ->assertOk()
            ->assertJsonPath('available', false);
        $this->get(route('student.vault'))
            ->assertOk()
            ->assertDontSee('id="customerSatisfactionModal"', false);

        $this->post(route('customer-satisfaction.store'), [
            'subject' => 'report',
            'subject_id' => $reportId,
            'rating' => 5,
            'responses' => [4, 4, 4, 4, 4],
        ])->assertStatus(409);

        DB::table('reports')->where('id', $reportId)->update(['resolution_notice_sent_at' => now()]);
        $this->getJson('/api/satisfaction/report/'.$reportId)
            ->assertOk()
            ->assertJsonPath('available', true);

        $this->get(route('student.vault'))
            ->assertOk()
            ->assertSee('id="customerSatisfactionModal"', false)
            ->assertSee('data-auto-show="true"', false)
            ->assertSee('data-report-survey-id="' . $reportId . '"', false)
            ->assertSee('title="Click to rate this report"', false)
            ->assertSee('>Rate Survey</button>', false)
            ->assertSee('name="subject_id" value="' . $reportId . '"', false);

        $this->get(route('student.vault'))
            ->assertOk()
            ->assertSee('data-auto-show="false"', false);

        $this->post(route('customer-satisfaction.store'), [
            'subject' => 'report',
            'subject_id' => $reportId,
            'rating' => 5,
            'responses' => [4, 4, 4, 4, 4],
        ])->assertRedirect();

        $this->post(route('customer-satisfaction.store'), [
            'subject' => 'report',
            'subject_id' => $reportId,
            'rating' => 5,
            'responses' => [4, 4, 4, 4, 4],
        ])->assertRedirect();

        $this->assertDatabaseHas('customer_satisfaction_surveys', [
            'user_id' => $user->id,
            'subject' => 'report',
            'subject_id' => $reportId,
            'rating' => 5,
        ]);
        $this->assertSame(1, DB::table('customer_satisfaction_surveys')
            ->where('user_id', $user->id)
            ->where('subject', 'report')
            ->where('subject_id', $reportId)
            ->count());

        $this->get(route('student.vault'))
            ->assertOk()
            ->assertDontSee('id="customerSatisfactionModal"', false);
    }

    public function test_satisfaction_survey_waits_until_a_submitted_report_is_resolved(): void
    {
        $this->createUsersTable();
        $this->createSatisfactionSurveysTable();
        Schema::create('reports', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id');
            $table->string('incident_id')->unique();
            $table->string('cloak_alias');
            $table->string('nature');
            $table->date('incident_date');
            $table->time('incident_time')->nullable();
            $table->string('course')->nullable();
            $table->string('gender')->nullable();
            $table->string('location');
            $table->text('description');
            $table->json('evidence')->nullable();
            $table->string('priority');
            $table->string('status');
            $table->timestamp('resolution_notice_sent_at')->nullable();
            $table->timestamps();
        });
        $this->createReportSupportTables();
        Schema::create('suggestions', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id');
            $table->string('status')->default('Pending');
            $table->timestamps();
        });

        $user = $this->makeCompletedStudent();
        $this->actingAs($user)
            ->post(route('reports.store'), [
                'nature' => 'Catcalling',
                'incident_date' => now()->toDateString(),
                'incident_time' => '12:30',
                'description' => 'A test report description.',
                'location' => 'Test location',
                'gender' => 'Female',
                'privacy_consent' => '1',
            ])
            ->assertRedirect(route('student.report'))
            ->assertSessionHas('report_case_id');

        $this->get(route('student.report'))
            ->assertOk()
            ->assertDontSee('id="customerSatisfactionModal"', false);

        $reportId = (int) DB::table('reports')->value('id');
        DB::table('reports')->where('id', $reportId)->update(['status' => 'Resolved']);
        $this->assertDatabaseHas('reports', [
            'id' => $reportId,
            'user_id' => $user->id,
            'status' => 'Resolved',
        ]);

        $this->get(route('student.vault'))
            ->assertOk()
            ->assertDontSee('id="customerSatisfactionModal"', false);

        $this->post(route('customer-satisfaction.store'), [
            'subject' => 'report',
            'subject_id' => $reportId,
            'rating' => 5,
            'responses' => [4, 4, 4, 4, 4],
        ])->assertStatus(409);

        DB::table('reports')->where('id', $reportId)->update([
            'resolution_notice_sent_at' => now(),
        ]);
        $this->get(route('student.vault'))
            ->assertOk()
            ->assertSee('id="customerSatisfactionModal"', false)
            ->assertSee('data-auto-show="true"', false)
            ->assertSee('data-report-survey-id="' . $reportId . '"', false)
            ->assertSee('name="subject" value="report"', false)
            ->assertSee('name="subject_id" value="' . $reportId . '"', false);

        $this->post(route('customer-satisfaction.store'), [
            'subject' => 'report',
            'subject_id' => $reportId,
            'rating' => 5,
            'responses' => [4, 4, 4, 4, 4],
        ])->assertRedirect();

        $this->assertDatabaseHas('customer_satisfaction_surveys', [
            'user_id' => $user->id,
            'subject' => 'report',
            'subject_id' => $reportId,
        ]);
    }

    public function test_post_validation_errors_are_visible_and_preserve_the_draft(): void
    {
        $this->createUsersTable();
        $this->createSuggestionTables();

        $user = $this->makeCompletedStudent([
            'name' => 'Test Student',
            'email' => 'student@example.test',
            'password' => 'password',
        ]);

        $this->actingAs($user)
            ->from(route('student.boses'))
            ->post(route('suggestions.store'), [
                'category' => 'GAD Programs and Services',
                'message' => 'Short',
            ])
            ->assertRedirect(route('student.boses'))
            ->assertSessionHasErrors('message');

        $this->get(route('student.boses'))
            ->assertOk()
            ->assertSee('The suggestion could not be posted')
            ->assertSee('Short');
    }

    public function test_suggestions_page_renders_existing_posts(): void
    {
        $this->createUsersTable();
        $this->createSuggestionTables();

        $user = $this->makeCompletedStudent([
            'name' => 'Test Student',
            'email' => 'student@example.test',
            'password' => 'password',
            'cloak_alias' => 'StudentTest',
        ]);

        DB::table('suggestions')->insert([
            'user_id' => $user->id,
            'category' => 'Campus Safety',
            'message' => 'Please add more GAD awareness activities.',
            'is_anonymous' => true,
            'status' => 'New',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $response = $this->actingAs($user)->get(route('student.boses'));

        $response->assertOk();
        $response->assertSee('Please add more GAD awareness activities.');
        $response->assertSee('Community suggestions');
        $response->assertSee('Campus Safety');
        $response->assertSee('data-comment-toggle', false);
        $response->assertSee('data-comments-panel', false);
        $response->assertDontSee('No suggestions yet');
    }

    public function test_students_only_see_approved_suggestions_from_other_users(): void
    {
        $this->createUsersTable();
        $this->createSuggestionTables();

        $viewer = $this->makeCompletedStudent();
        $otherUser = $this->makeCompletedStudent([
            'name' => 'Other Student',
            'email' => 'other-student@example.test',
        ]);

        $this->createSuggestion($otherUser, 'Approved', 'Approved suggestion from another student.');
        $this->createSuggestion($otherUser, 'Pending', 'Suggestion still under review from another student.');
        $this->createSuggestion($otherUser, 'Rejected', 'Rejected suggestion from another student.');
        $this->createSuggestion($viewer, 'Pending', 'My suggestion is still under review.');
        $this->createSuggestion($viewer, 'Rejected', 'My rejected suggestion.');

        $this->actingAs($viewer)
            ->get(route('student.boses'))
            ->assertOk()
            ->assertSee('Approved suggestion from another student.')
            ->assertDontSee('Suggestion still under review from another student.')
            ->assertDontSee('Rejected suggestion from another student.')
            ->assertSee('My suggestion is still under review.')
            ->assertSee('My rejected suggestion.');
    }

    public function test_suggestions_api_hides_unapproved_posts_from_other_users(): void
    {
        $this->createUsersTable();
        $this->createSuggestionTables();

        $viewer = $this->makeCompletedStudent();
        $otherUser = $this->makeCompletedStudent([
            'name' => 'Other Student',
            'email' => 'other-student@example.test',
        ]);

        $this->createSuggestion($otherUser, 'Approved', 'Approved API suggestion from another student.');
        $this->createSuggestion($otherUser, 'Pending', 'Pending API suggestion from another student.');
        $this->createSuggestion($otherUser, 'Rejected', 'Rejected API suggestion from another student.');
        $this->createSuggestion($viewer, 'Pending', 'My pending API suggestion.');

        $this->actingAs($viewer)
            ->getJson('/api/suggestions')
            ->assertOk()
            ->assertJsonFragment(['message' => 'Approved API suggestion from another student.'])
            ->assertJsonMissing(['message' => 'Pending API suggestion from another student.'])
            ->assertJsonMissing(['message' => 'Rejected API suggestion from another student.'])
            ->assertJsonFragment(['message' => 'My pending API suggestion.']);
    }

    public function test_users_with_completed_personal_data_can_post_suggestions(): void
    {
        $this->createUsersTable();
        $this->createSuggestionTables(true, false);

        $user = User::create([
            'name' => 'Test Student',
            'email' => 'student@example.test',
            'password' => 'password',
            'role' => 'student',
            'last_name' => 'Student',
            'first_name' => 'Test',
            'phone_number' => '09123456789',
            'age' => 20,
            'gender' => 'Female',
            'department' => 'College of Education',
            'personal_information' => [
                'date_of_birth' => '2006-01-01',
                'place_of_birth' => 'Lingayen',
                'sex' => 'Female',
                'gender_identity' => 'Woman',
                'citizenship' => 'Filipino',
                'blood_type' => 'O+',
                'height' => '160',
                'weight' => '50',
                'landline_number' => 'N/A',
                'civil_status' => 'Single',
                'current_address' => 'Lingayen',
                'home_address' => 'Lingayen',
                'religion' => 'N/A',
            ],
            'student_information' => [
                'year_level' => '2nd Year',
                'school_type' => 'Public',
                'last_school_attended' => 'PSU',
                'financing_sources' => ['Family'],
            ],
        ]);

        $response = $this->actingAs($user)
            ->from(route('student.boses'))
            ->post(route('suggestions.store'), [
                'category' => 'GAD Programs and Services',
                'message' => 'Please add more GAD awareness activities.',
            ]);

        $response->assertRedirect(route('student.boses'));
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('suggestions', [
            'id' => 1,
            'user_id' => $user->id,
            'category' => 'GAD Programs and Services',
            'message' => 'Please add more GAD awareness activities.',
            'status' => 'Pending',
        ]);
    }

    public function test_users_can_toggle_suggestion_upvotes(): void
    {
        $this->createUsersTable();
        $this->createSuggestionTables();

        $user = $this->makeCompletedStudent();
        DB::table('suggestions')->insert([
            'id' => 1,
            'user_id' => $user->id,
            'category' => 'GAD Programs and Services',
            'message' => 'Please add more GAD awareness activities.',
            'is_anonymous' => true,
            'status' => 'Approved',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->actingAs($user)
            ->postJson(route('suggestions.upvote', 1))
            ->assertOk()
            ->assertJson([
                'ok' => true,
                'upvoted' => true,
                'upvotes_count' => 1,
            ]);

        $this->assertDatabaseHas('suggestion_user_upvotes', [
            'id' => 1,
            'suggestion_id' => 1,
            'user_id' => $user->id,
        ]);

        $this->postJson(route('suggestions.upvote', 1))
            ->assertOk()
            ->assertJson([
                'ok' => true,
                'upvoted' => false,
                'upvotes_count' => 0,
            ]);

        $this->assertDatabaseMissing('suggestion_user_upvotes', [
            'suggestion_id' => 1,
            'user_id' => $user->id,
        ]);
    }

    public function test_users_can_post_text_suggestions_when_the_optional_image_column_is_missing(): void
    {
        $this->createUsersTable();
        $this->createSuggestionTables(false, false);

        $user = $this->makeCompletedStudent([
            'name' => 'Test Student',
            'email' => 'student@example.test',
            'password' => 'password',
        ]);

        $this->actingAs($user)
            ->from(route('student.boses'))
            ->post(route('suggestions.store'), [
                'category' => 'GAD Programs and Services',
                'message' => 'Please add more GAD awareness activities.',
            ])
            ->assertRedirect(route('student.boses'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('suggestions', [
            'id' => 1,
            'user_id' => $user->id,
            'category' => 'GAD Programs and Services',
            'message' => 'Please add more GAD awareness activities.',
        ]);
    }

    public function test_suggestion_attachments_are_stored_as_public_cloudinary_images(): void
    {
        $this->fakeCloudinaryMedia();
        $this->createUsersTable();
        $this->createSuggestionTables();

        $user = $this->makeCompletedStudent([
            'name' => 'Test Student',
            'email' => 'student@example.test',
            'password' => 'password',
        ]);

        $this->actingAs($user)
            ->from(route('student.boses'))
            ->post(route('suggestions.store'), [
                'category' => 'GAD Programs and Services',
                'message' => 'Please add more GAD awareness activities.',
                'image' => UploadedFile::fake()->image('suggestion.png'),
            ])
            ->assertRedirect(route('student.boses'))
            ->assertSessionHas('success');

        $path = DB::table('suggestions')->value('image_path');
        $this->assertStringStartsWith('cloudinary://upload/image/sinag/suggestions/', $path);
        $this->assertNotNull(MediaResolver::url($path, ['public', 'public_s3']));
    }

    public function test_users_can_post_suggestions_containing_words_that_include_a_moderated_substring(): void
    {
        $this->createUsersTable();
        $this->createSuggestionTables();

        $user = $this->makeCompletedStudent([
            'name' => 'Test Student',
            'email' => 'student@example.test',
            'password' => 'password',
        ]);

        $response = $this->actingAs($user)
            ->from(route('student.boses'))
            ->post(route('suggestions.store'), [
                'category' => 'GAD Programs and Services',
                'message' => 'Please include more student skills workshops in the annual program.',
            ]);

        $response->assertRedirect(route('student.boses'));
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('suggestions', [
            'user_id' => $user->id,
            'message' => 'Please include more student skills workshops in the annual program.',
        ]);
    }

    private function createUsersTable(): void
    {
        Schema::create('users', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->string('role')->default('student');
            $table->string('account_type')->nullable();
            $table->string('employee_category')->nullable();
            $table->string('cloak_alias')->nullable();
            $table->string('last_name')->nullable();
            $table->string('first_name')->nullable();
            $table->string('phone_number')->nullable();
            $table->unsignedTinyInteger('age')->nullable();
            $table->string('gender')->nullable();
            $table->string('department')->nullable();
            $table->json('personal_information')->nullable();
            $table->json('student_information')->nullable();
            $table->timestamps();
        });
    }

    private function createReportSupportTables(): void
    {
        Schema::create('report_consents', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('version');
            $table->timestamp('accepted_at');
            $table->timestamps();
            $table->unique(['user_id', 'version']);
        });

        Schema::create('report_status_updates', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('report_id');
            $table->unsignedBigInteger('admin_id')->nullable();
            $table->string('from_status')->nullable();
            $table->string('to_status');
            $table->text('details')->nullable();
            $table->timestamps();
        });
    }

    private function makeCompletedStudent(array $overrides = []): User
    {
        return User::create(array_merge([
            'name' => 'Test Student',
            'email' => 'student@example.test',
            'password' => 'password',
            'role' => 'student',
            'last_name' => 'Student',
            'first_name' => 'Test',
            'phone_number' => '09123456789',
            'age' => 20,
            'gender' => 'Female',
            'department' => 'College of Education',
            'personal_information' => [
                'date_of_birth' => '2006-01-01',
                'place_of_birth' => 'Lingayen',
                'sex' => 'Female',
                'gender_identity' => 'Woman',
                'citizenship' => 'Filipino',
                'blood_type' => 'O+',
                'height' => '160',
                'weight' => '50',
                'landline_number' => 'N/A',
                'civil_status' => 'Single',
                'current_address' => 'Lingayen',
                'home_address' => 'Lingayen',
                'religion' => 'N/A',
            ],
            'student_information' => [
                'year_level' => '2nd Year',
                'school_type' => 'Public',
                'last_school_attended' => 'PSU',
                'financing_sources' => ['Family'],
            ],
        ], $overrides));
    }

    private function createSuggestion(User $user, string $status, string $message): int
    {
        return DB::table('suggestions')->insertGetId([
            'user_id' => $user->id,
            'category' => 'GAD Programs and Services',
            'message' => $message,
            'is_anonymous' => true,
            'status' => $status,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    private function createSuggestionTables(bool $withImagePath = true, bool $autoIncrementId = true): void
    {
        Schema::create('suggestions', function (Blueprint $table) use ($withImagePath, $autoIncrementId): void {
            if ($autoIncrementId) {
                $table->id();
            } else {
                $table->unsignedBigInteger('id')->primary();
            }
            $table->foreignId('user_id');
            $table->string('category');
            $table->text('message');
            if ($withImagePath) {
                $table->string('image_path')->nullable();
            }
            $table->boolean('is_anonymous')->default(true);
            $table->string('status')->default('Pending');
            $table->timestamps();
        });

        Schema::create('suggestion_comments', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('suggestion_id');
            $table->foreignId('user_id');
            $table->text('comment');
            $table->timestamps();
        });

        Schema::create('suggestion_user_upvotes', function (Blueprint $table): void {
            $table->unsignedBigInteger('id')->primary();
            $table->foreignId('suggestion_id');
            $table->foreignId('user_id');
            $table->timestamps();
            $table->unique(['suggestion_id', 'user_id']);
        });
    }

    private function createSatisfactionSurveysTable(): void
    {
        Schema::create('customer_satisfaction_surveys', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id');
            $table->string('subject', 20)->nullable();
            $table->unsignedBigInteger('subject_id')->nullable();
            $table->unsignedTinyInteger('rating');
            $table->text('feedback')->nullable();
            $table->json('responses')->nullable();
            $table->timestamps();
        });
    }
}
