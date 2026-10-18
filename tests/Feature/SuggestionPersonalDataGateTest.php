<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class SuggestionPersonalDataGateTest extends TestCase
{
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
        $this->createSuggestionTables();

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
        });
    }
}
