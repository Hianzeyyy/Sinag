<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class SuggestionAdminReviewTest extends TestCase
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

        Schema::create('suggestions', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id');
            $table->string('category');
            $table->text('message');
            $table->string('image_path')->nullable();
            $table->boolean('is_anonymous')->default(true);
            $table->string('status')->default('Pending');
            $table->text('admin_remarks')->nullable();
            $table->timestamps();
        });
    }

    public function test_admin_can_save_a_comment_with_each_review_decision(): void
    {
        $author = User::create([
            'name' => 'Suggestion Author',
            'email' => 'author@example.test',
            'password' => 'password',
            'role' => 'student',
        ]);
        $admin = User::create([
            'name' => 'Test Admin',
            'email' => 'admin@example.test',
            'password' => 'password',
            'role' => 'admin',
        ]);

        $approvedId = $this->createSuggestion($author, 'Please add a support program.');
        $rejectedId = $this->createSuggestion($author, 'Please review this proposal.');

        $this->actingAs($admin)
            ->post(route('admin.suggestions.review', $approvedId), [
                'action' => 'approve',
                'admin_remarks' => 'This proposal aligns with the GAD plan.',
            ])
            ->assertRedirect();

        $this->post(route('admin.suggestions.review', $rejectedId), [
            'action' => 'reject',
            'admin_remarks' => 'Please provide a clearer implementation plan.',
        ])->assertRedirect();

        $this->assertDatabaseHas('suggestions', [
            'id' => $approvedId,
            'status' => 'Approved',
            'admin_remarks' => 'This proposal aligns with the GAD plan.',
        ]);
        $this->assertDatabaseHas('suggestions', [
            'id' => $rejectedId,
            'status' => 'Rejected',
            'admin_remarks' => 'Please provide a clearer implementation plan.',
        ]);
    }

    private function createSuggestion(User $author, string $message): int
    {
        return DB::table('suggestions')->insertGetId([
            'user_id' => $author->id,
            'category' => 'GAD Programs and Services',
            'message' => $message,
            'is_anonymous' => true,
            'status' => 'Pending',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
