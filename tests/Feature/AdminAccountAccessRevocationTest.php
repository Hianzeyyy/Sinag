<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class AdminAccountAccessRevocationTest extends TestCase
{
    public function test_rejecting_an_account_revokes_its_tokens_and_sessions(): void
    {
        Schema::create('users', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->string('role');
            $table->string('account_status')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->timestamps();
        });

        Schema::create('personal_access_tokens', function (Blueprint $table): void {
            $table->id();
            $table->morphs('tokenable');
            $table->string('name');
            $table->string('token', 64)->unique();
            $table->text('abilities')->nullable();
            $table->timestamp('last_used_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
        });

        Schema::create('sessions', function (Blueprint $table): void {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable();
        });

        $admin = User::create([
            'name' => 'GAD Admin',
            'email' => 'admin@example.test',
            'password' => 'password',
            'role' => 'admin',
            'account_status' => 'active',
        ]);
        $user = User::create([
            'name' => 'Student User',
            'email' => 'student@example.test',
            'password' => 'password',
            'role' => 'student',
            'account_status' => 'active',
        ]);

        $user->createToken('mobile-one');
        $user->createToken('mobile-two');
        DB::table('sessions')->insert([
            'id' => 'student-session',
            'user_id' => $user->id,
        ]);

        Mail::fake();

        $this->actingAs($admin)
            ->post(route('admin.users.reject', $user->id), ['rejection_reason' => 'Verification failed'])
            ->assertRedirect();

        $this->assertDatabaseHas('users', ['id' => $user->id, 'account_status' => 'rejected']);
        $this->assertDatabaseCount('personal_access_tokens', 0);
        $this->assertDatabaseMissing('sessions', ['id' => 'student-session']);

        $this->actingAs($user->refresh())
            ->get(route('student.dashboard'))
            ->assertRedirect(route('login'));
    }
}
