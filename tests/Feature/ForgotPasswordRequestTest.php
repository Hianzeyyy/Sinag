<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\UploadedFile;
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
            $table->timestamps();
        });

        Schema::create('password_reset_requests', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('reference_number')->unique();
            $table->string('email');
            $table->string('name');
            $table->string('student_employee_id');
            $table->string('department')->nullable();
            $table->string('id_picture_path');
            $table->text('reason')->nullable();
            $table->string('status')->default('pending');
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

        parse_str((string) parse_url($response->headers->get('Location'), PHP_URL_QUERY), $redirectQuery);
        $this->assertMatchesRegularExpression('/^PRR-[A-Z0-9]{10}$/', $redirectQuery['reference'] ?? '');

        $this->assertDatabaseHas('password_reset_requests', [
            'email' => 'student@example.test',
            'student_employee_id' => 'STU-100',
            'status' => 'pending',
        ]);

        $request = \DB::table('password_reset_requests')->first();
        Storage::disk('local')->assertExists($request->id_picture_path);
    }
}
