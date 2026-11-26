<?php

namespace Tests\Feature;

use App\Mail\ReportResolvedMail;
use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class ReportResolutionNoticeTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Schema::create('users', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->string('role');
            $table->timestamps();
        });

        Schema::create('reports', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id');
            $table->string('incident_id')->unique();
            $table->string('cloak_alias');
            $table->string('nature');
            $table->string('location');
            $table->text('description');
            $table->string('priority')->default('Medium');
            $table->string('status')->default('Pending');
            $table->text('admin_comment')->nullable();
            $table->timestamp('resolution_notice_sent_at')->nullable();
            $table->timestamps();
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

        Schema::create('notifications', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->string('type');
            $table->morphs('notifiable');
            $table->text('data');
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
        });
    }

    public function test_resolving_a_report_sends_one_email_and_one_in_app_notice(): void
    {
        $admin = $this->createUser('GAD Admin', 'admin@example.test', 'admin');
        $user = $this->createUser('Student User', 'student@example.test', 'student');
        $reportId = DB::table('reports')->insertGetId([
            'user_id' => $user->id,
            'incident_id' => 'SINAG-RESOLVED-1',
            'cloak_alias' => 'Student',
            'nature' => 'Harassment',
            'location' => 'Campus',
            'description' => 'Test report',
            'priority' => 'Medium',
            'status' => 'Pending',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        Mail::fake();

        $this->actingAs($admin)
            ->post(route('admin.reports.update', $reportId), [
                'status' => 'Resolved',
                'priority' => 'Medium',
                'admin_comment' => 'The case has been reviewed and resolved.',
            ])
            ->assertSessionHas('success', "Report #SINAG-RESOLVED-1 was resolved and the user was notified by email and in-app.");

        $report = DB::table('reports')->where('id', $reportId)->first();
        $this->assertNotNull($report->resolution_notice_sent_at);
        $notification = DB::table('notifications')
            ->where('notifiable_type', User::class)
            ->where('notifiable_id', $user->id)
            ->first();
        $this->assertNotNull($notification);
        $this->assertSame('report_resolved', json_decode($notification->data, true)['kind']);
        $this->assertSame('SINAG-RESOLVED-1', json_decode($notification->data, true)['incident_id']);
        Mail::assertSent(ReportResolvedMail::class, fn (ReportResolvedMail $mail): bool =>
            $mail->hasTo($user->email)
            && $mail->incidentId === 'SINAG-RESOLVED-1'
        );

        $this->post(route('admin.reports.update', $reportId), [
            'status' => 'Resolved',
            'priority' => 'Medium',
            'admin_comment' => 'Follow-up review completed.',
        ])->assertSessionHas('success', 'Report #SINAG-RESOLVED-1 updated successfully.');

        $this->assertSame(1, DB::table('notifications')->count());
        Mail::assertSent(ReportResolvedMail::class, 1);
    }

    public function test_email_failure_keeps_the_in_app_notice_and_survey_available(): void
    {
        $admin = $this->createUser('GAD Admin', 'admin@example.test', 'admin');
        $user = $this->createUser('Student User', 'student@example.test', 'student');
        $reportId = DB::table('reports')->insertGetId([
            'user_id' => $user->id,
            'incident_id' => 'SINAG-RESOLVED-2',
            'cloak_alias' => 'Student',
            'nature' => 'Harassment',
            'location' => 'Campus',
            'description' => 'Test report',
            'priority' => 'Medium',
            'status' => 'Pending',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $mailer = \Mockery::mock();
        $mailer->shouldReceive('send')
            ->once()
            ->with(\Mockery::type(ReportResolvedMail::class))
            ->andThrow(new \RuntimeException('Mail service unavailable'));
        Mail::shouldReceive('to')->once()->with($user->email)->andReturn($mailer);
        Log::spy();

        $this->actingAs($admin)
            ->post(route('admin.reports.update', $reportId), [
                'status' => 'Resolved',
                'priority' => 'Medium',
                'admin_comment' => 'The case has been reviewed and resolved.',
            ])
            ->assertSessionHas('warning');

        $this->assertNotNull(DB::table('reports')->where('id', $reportId)->value('resolution_notice_sent_at'));
        $this->assertSame(1, DB::table('notifications')->count());
        Log::shouldHaveReceived('error')->once()->with(
            'SINAG report resolution email failed',
            \Mockery::on(fn (array $context): bool => $context['report_id'] === $reportId
                && $context['exception'] === \RuntimeException::class)
        );
    }

    private function createUser(string $name, string $email, string $role): User
    {
        return User::create([
            'name' => $name,
            'email' => $email,
            'password' => 'password',
            'role' => $role,
        ]);
    }
}
