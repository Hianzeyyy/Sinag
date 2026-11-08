<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\PasswordResetRequest;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ForgotPasswordRequestTest extends TestCase
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
            $table->string('account_status')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->timestamps();
        });

        Schema::create('password_reset_requests', function (Blueprint $table): void {
            $table->unsignedBigInteger('id')->primary();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('reference_number')->unique();
            $table->string('email');
            $table->string('name');
            $table->string('student_employee_id');
            $table->string('department')->nullable();
            $table->string('id_picture_path');
            $table->text('reason')->nullable();
            $table->string('status')->default('pending');
            $table->foreignId('admin_id')->nullable()->constrained('users')->nullOnDelete();
            $table->text('admin_remarks')->nullable();
            $table->string('reset_token_hash')->nullable();
            $table->timestamp('reset_token_expires_at')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });

        Storage::fake('local');
    }

    public function test_password_reset_request_saves_the_private_id_picture_and_reference(): void
    {
        User::create([
            'name' => 'Test Student',
            'email' => 'student@example.test',
            'password' => 'password',
            'role' => 'student',
        ]);

        $response = $this->post(route('password.code.request'), [
            'email' => 'student@example.test',
            'student_employee_id' => 'STU-100',
            'name' => 'Test Student',
            'id_picture' => UploadedFile::fake()->image('student-id.png'),
        ]);
        $response->assertRedirect();
        $response->assertSessionHas('success');
        $this->get($response->headers->get('Location'))->assertOk();

        parse_str((string) parse_url($response->headers->get('Location'), PHP_URL_QUERY), $redirectQuery);
        $this->assertMatchesRegularExpression('/^PRR-[A-Z0-9]{10}$/', $redirectQuery['reference'] ?? '');

        $this->assertDatabaseHas('password_reset_requests', [
            'id' => 1,
            'email' => 'student@example.test',
            'student_employee_id' => 'STU-100',
            'status' => 'pending',
        ]);

        $request = \DB::table('password_reset_requests')->first();
        Storage::disk('local')->assertExists($request->id_picture_path);
    }

    public function test_password_reset_request_matches_email_without_case_sensitivity(): void
    {
        User::create([
            'name' => 'Test Student',
            'email' => 'Student@Example.Test',
            'password' => 'password',
            'role' => 'student',
        ]);

        $response = $this->post(route('password.code.request'), [
            'email' => ' student@example.test ',
            'student_employee_id' => 'STU-100',
            'name' => 'Test Student',
            'id_picture' => UploadedFile::fake()->image('student-id.png'),
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');
        $this->get($response->headers->get('Location'))->assertOk();
        $this->assertDatabaseHas('password_reset_requests', [
            'id' => 1,
            'email' => 'Student@Example.Test',
            'status' => 'pending',
        ]);
    }

    public function test_password_reset_request_reports_unregistered_email_without_creating_a_request(): void
    {
        $response = $this->from(route('password.code.form'))->post(route('password.code.request'), [
            'email' => 'missing@example.test',
            'student_employee_id' => 'STU-100',
            'name' => 'Test Student',
            'id_picture' => UploadedFile::fake()->image('student-id.png'),
        ]);

        $response->assertRedirect(route('password.code.form'))
            ->assertSessionHasErrors('email');
        $this->assertDatabaseCount('password_reset_requests', 0);
    }

    public function test_admin_approval_allows_the_requester_to_reset_their_password(): void
    {
        $user = User::create([
            'name' => 'Test Student',
            'email' => 'student@example.test',
            'password' => 'old-password',
            'role' => 'student',
        ]);
        $admin = User::create([
            'name' => 'Test Admin',
            'email' => 'admin@example.test',
            'password' => 'admin-password',
            'role' => 'admin',
        ]);

        $this->post(route('password.code.request'), [
            'email' => $user->email,
            'student_employee_id' => 'STU-100',
            'name' => $user->name,
            'id_picture' => UploadedFile::fake()->image('student-id.png'),
        ])->assertRedirect();

        $resetRequest = PasswordResetRequest::query()->firstOrFail();

        $this->actingAs($admin)
            ->post(route('admin.password-resets.approve', $resetRequest))
            ->assertRedirect()
            ->assertSessionHas('success');

        $this->assertDatabaseHas('password_reset_requests', [
            'id' => $resetRequest->id,
            'status' => 'approved',
            'admin_id' => $admin->id,
        ]);

        $statusResponse = $this->actingAs($user)->post(route('password.status.check'), [
            'reference_number' => $resetRequest->reference_number,
            'email_or_id' => $user->email,
        ])->assertOk();

        $token = $statusResponse->viewData('token');
        $this->assertIsString($token);

        $this->post(route('password.reset.submit', [$resetRequest, $token]), [
            'password' => 'new-strong-password',
            'password_confirmation' => 'new-strong-password',
        ])->assertRedirect(route('login'));

        $this->assertTrue(Hash::check('new-strong-password', $user->fresh()->password));
    }
}
