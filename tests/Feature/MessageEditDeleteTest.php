<?php

namespace Tests\Feature;

use App\Models\Message;
use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class MessageEditDeleteTest extends TestCase
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

        Schema::create('messages', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('sender_id');
            $table->unsignedBigInteger('receiver_id');
            $table->text('message');
            $table->text('original_message')->nullable();
            $table->string('sender_type')->default('student');
            $table->timestamp('read_at')->nullable();
            $table->timestamp('deleted_at')->nullable();
            $table->timestamps();
        });
    }

    public function test_student_edit_preserves_first_original_message(): void
    {
        [$student, $admin] = $this->users();
        $message = $this->message($student, $admin, 'First version', 'student');

        $this->actingAs($student)
            ->postJson(route('student.messages.update', $message->id), ['message' => 'Updated version'])
            ->assertOk()
            ->assertJsonPath('message.original_message', 'First version');

        $this->assertDatabaseHas('messages', [
            'id' => $message->id,
            'message' => 'Updated version',
            'original_message' => 'First version',
        ]);
    }

    public function test_user_cannot_edit_another_users_message(): void
    {
        [$student, $admin] = $this->users();
        $otherStudent = User::create([
            'name' => 'Other Student',
            'email' => 'other@example.test',
            'password' => 'password',
            'role' => 'student',
        ]);
        $message = $this->message($otherStudent, $admin, 'Private message', 'student');

        $this->actingAs($student)
            ->postJson(route('student.messages.update', $message->id), ['message' => 'Tampered'])
            ->assertForbidden();
    }

    public function test_student_delete_keeps_a_content_free_tombstone(): void
    {
        [$student, $admin] = $this->users();
        $message = $this->message($student, $admin, 'Sensitive message', 'student');

        $this->actingAs($student)
            ->deleteJson(route('student.messages.destroy', $message->id))
            ->assertOk();

        $this->assertDatabaseHas('messages', [
            'id' => $message->id,
            'message' => '',
        ]);
        $this->assertNotNull($message->fresh()->deleted_at);
    }

    public function test_admin_can_edit_and_delete_only_the_admins_message(): void
    {
        [$student, $admin] = $this->users();
        $message = $this->message($admin, $student, 'Admin message', 'admin');

        $this->actingAs($admin)
            ->postJson(route('admin.messages.update', $message->id), ['message' => 'Edited by admin'])
            ->assertOk()
            ->assertJsonPath('message.original_message', 'Admin message');

        $this->actingAs($admin)
            ->deleteJson(route('admin.messages.destroy', $message->id))
            ->assertOk();

        $this->assertDatabaseHas('messages', ['id' => $message->id, 'message' => '']);
        $this->assertNotNull($message->fresh()->deleted_at);
    }

    private function users(): array
    {
        $student = User::create([
            'name' => 'Test Student',
            'email' => 'student@example.test',
            'password' => 'password',
            'role' => 'student',
        ]);
        $admin = User::create([
            'name' => 'GAD Admin',
            'email' => 'admin@example.test',
            'password' => 'password',
            'role' => 'admin',
        ]);

        return [$student, $admin];
    }

    private function message(User $sender, User $receiver, string $text, string $senderType): Message
    {
        return Message::create([
            'sender_id' => $sender->id,
            'receiver_id' => $receiver->id,
            'message' => $text,
            'sender_type' => $senderType,
        ]);
    }
}
