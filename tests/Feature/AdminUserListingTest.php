<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class AdminUserListingTest extends TestCase
{
    public function test_admin_user_list_includes_the_signed_in_admin(): void
    {
        Schema::create('users', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->string('role')->default('student');
            $table->string('account_type')->default('student');
            $table->string('employee_category')->nullable();
            $table->string('department')->nullable();
            $table->unsignedTinyInteger('age')->nullable();
            $table->string('gender')->nullable();
            $table->string('phone_number')->nullable();
            $table->string('cloak_alias')->nullable();
            $table->string('profile_photo_path')->nullable();
            $table->string('id_image_path')->nullable();
            $table->string('selfie_image_path')->nullable();
            $table->string('account_status')->default('active');
            $table->string('availability_status')->default('active');
            $table->timestamp('last_seen_at')->nullable();
            $table->timestamps();
        });

        Schema::create('reports', function (Blueprint $table): void {
            $table->id();
            $table->timestamp('seen_at')->nullable();
        });

        Schema::create('suggestions', function (Blueprint $table): void {
            $table->id();
            $table->string('status');
        });

        Schema::create('appointment_requests', function (Blueprint $table): void {
            $table->id();
            $table->string('status');
        });

        Schema::create('messages', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('receiver_id');
            $table->timestamp('read_at')->nullable();
        });

        Schema::create('password_reset_requests', function (Blueprint $table): void {
            $table->id();
            $table->string('status');
        });

        Schema::create('suggestion_reports', function (Blueprint $table): void {
            $table->id();
            $table->string('status');
        });

        $admin = User::create([
            'name' => 'GAD Admin',
            'email' => 'admin@example.test',
            'password' => 'password',
            'role' => 'admin',
            'account_type' => 'admin',
            'account_status' => 'active',
        ]);

        $this->actingAs($admin)
            ->get(route('admin.users'))
            ->assertOk()
            ->assertSeeText($admin->name)
            ->assertSeeText($admin->email);
    }
}
