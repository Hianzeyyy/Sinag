<?php

namespace Tests\Feature;

use App\Models\Report;
use App\Models\ReportStatusUpdate;
use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class GadCoordinatorAnalyticsAccessTest extends TestCase
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
            $table->string('account_type')->nullable();
            $table->string('account_status')->nullable();
            $table->string('cloak_alias')->nullable();
            $table->string('admin_theme')->default('light');
            $table->timestamps();
        });

        Schema::create('customer_satisfaction_surveys', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('subject')->nullable();
            $table->unsignedBigInteger('subject_id')->nullable();
            $table->unsignedTinyInteger('rating');
            $table->text('feedback')->nullable();
            $table->json('responses')->nullable();
            $table->timestamps();
        });

        Schema::create('reports', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('incident_id');
            $table->string('nature')->nullable();
            $table->string('status')->nullable();
            $table->string('priority')->nullable();
            $table->boolean('is_anonymous')->default(true);
            $table->date('incident_date')->nullable();
            $table->string('location')->nullable();
            $table->text('description')->nullable();
            $table->json('evidence')->nullable();
            $table->text('admin_comment')->nullable();
            $table->timestamp('seen_at')->nullable();
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

        Schema::create('suggestions', function (Blueprint $table): void {
            $table->id();
            $table->string('status')->nullable();
            $table->timestamps();
        });

        Schema::create('appointment_requests', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('status')->nullable();
            $table->timestamps();
        });

        Schema::create('password_reset_requests', function (Blueprint $table): void {
            $table->id();
            $table->string('status')->nullable();
            $table->timestamps();
        });

        Schema::create('suggestion_reports', function (Blueprint $table): void {
            $table->id();
            $table->string('status')->nullable();
            $table->timestamps();
        });

        Schema::create('messages', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('sender_id');
            $table->unsignedBigInteger('receiver_id');
            $table->text('message');
            $table->text('original_message')->nullable();
            $table->timestamp('deleted_at')->nullable();
            $table->string('sender_type')->default('student');
            $table->timestamp('read_at')->nullable();
            $table->boolean('is_anonymous')->default(false);
            $table->timestamps();
        });
    }

    public function test_coordinator_can_view_report_analytics_but_not_admin_tools(): void
    {
        $coordinator = User::create([
            'name' => 'GAD Coordinator',
            'email' => 'gadcoordinator@example.test',
            'password' => 'password',
            'role' => 'admin',
            'account_type' => 'coordinator',
            'account_status' => 'active',
        ]);
        Report::create(['incident_id' => 'PRIVATE-CASE-1', 'nature' => 'Harassment', 'status' => 'Pending']);
        Report::create(['incident_id' => 'PRIVATE-CASE-2', 'nature' => 'Harassment', 'status' => 'Resolved']);
        Report::create(['incident_id' => 'PRIVATE-CASE-3', 'nature' => 'Discrimination', 'status' => 'Resolved']);
        User::create([
            'name' => 'Active Student',
            'email' => 'active-student@example.test',
            'password' => 'password',
            'role' => 'student',
            'account_type' => 'student',
            'account_status' => 'active',
        ]);
        User::create([
            'name' => 'Pending Employee',
            'email' => 'pending-employee@example.test',
            'password' => 'password',
            'role' => 'employee',
            'account_type' => 'employee',
            'account_status' => 'pending',
        ]);
        User::create([
            'name' => 'Rejected Student',
            'email' => 'rejected-student@example.test',
            'password' => 'password',
            'role' => 'student',
            'account_type' => 'student',
            'account_status' => 'rejected',
        ]);

        $response = $this->actingAs($coordinator)
            ->get(route('admin.dashboard'))
            ->assertOk();

        $response
            ->assertViewHas('totalReports', 3)
            ->assertViewHas('reportCountsByStatus', fn ($counts) => (int) $counts->get('Pending') === 1
                && (int) $counts->get('Resolved') === 2)
            ->assertViewMissing('registeredUserSummary')
            ->assertSee('GAD Report Analytics')
            ->assertSeeText('Export report')
            ->assertSeeText('Preview export')
            ->assertSeeText('Export now')
            ->assertSee('value="pdf"', false)
            ->assertSee('value="excel"', false)
            ->assertSee(route('student.boses'), false)
            ->assertSeeText('Suggestions')
            ->assertSee(route('student.messaging'), false)
            ->assertDontSee(route('admin.messages'), false)
            ->assertSeeText('Messages')
            ->assertDontSeeText('Community Directory')
            ->assertSee('Total Reports')
            ->assertDontSee('Registered Users Summary')
            ->assertDontSee('Total Registered Users')
            ->assertDontSee('Pending Approval')
            ->assertSee('Harassment')
            ->assertDontSee('Pending Employee')
            ->assertDontSee('PRIVATE-CASE-1')
            ->assertDontSee('User Management')
            ->assertDontSee('Appointments')
            ->assertDontSee('Participatory Suggestions');

        foreach (['admin.users', 'admin.appointments', 'admin.suggestions', 'admin.reports'] as $routeName) {
            $this->get(route($routeName))->assertForbidden();
        }
    }

    public function test_coordinator_can_open_report_details_and_tracking_history(): void
    {
        $coordinator = User::create([
            'name' => 'GAD Coordinator',
            'email' => 'gadcoordinator@example.test',
            'password' => 'password',
            'role' => 'admin',
            'account_type' => 'coordinator',
            'account_status' => 'active',
        ]);
        $reporter = User::create([
            'name' => 'Report Owner',
            'email' => 'owner@example.test',
            'password' => 'password',
            'role' => 'student',
            'account_type' => 'student',
            'account_status' => 'active',
        ]);
        $report = Report::create([
            'user_id' => $reporter->id,
            'incident_id' => 'SN-TRACK-1',
            'nature' => 'Stalking',
            'status' => 'Under Review',
            'priority' => 'High',
            'incident_date' => today()->toDateString(),
            'location' => 'Library',
            'description' => 'The report details for tracking.',
        ]);
        ReportStatusUpdate::create([
            'report_id' => $report->id,
            'to_status' => 'Under Review',
            'details' => 'Assigned to the GAD review team.',
        ]);
        Report::create([
            'incident_id' => 'SN-TRACK-2',
            'nature' => 'Bullying',
            'status' => 'Pending',
            'priority' => 'Low',
        ]);

        $this->actingAs($coordinator)
            ->get(route('admin.report-tracking', ['report_id' => $report->id]))
            ->assertOk()
            ->assertSee('Report Details & Analytics', false)
            ->assertSee('SN-TRACK-1')
            ->assertDontSee('SN-TRACK-2')
            ->assertSee('Assigned to the GAD review team.');
    }

    public function test_coordinator_dashboard_emergency_card_counts_active_high_priority_reports(): void
    {
        $coordinator = User::create([
            'name' => 'GAD Coordinator',
            'email' => 'gadcoordinator@example.test',
            'password' => 'password',
            'role' => 'admin',
            'account_type' => 'coordinator',
            'account_status' => 'active',
        ]);
        Report::create(['incident_id' => 'HIGH-ACTIVE', 'status' => 'Under Review', 'priority' => 'High']);
        Report::create(['incident_id' => 'HIGH-RESOLVED', 'status' => 'Resolved', 'priority' => 'High']);
        Report::create(['incident_id' => 'STATUS-EMERGENCY', 'status' => 'Emergency', 'priority' => 'Low']);

        $this->actingAs($coordinator)
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('Emergency')
            ->assertSee('data-count-up="2"', false);
    }

    public function test_coordinator_can_update_report_status_priority_and_reporter_visible_note(): void
    {
        $coordinator = User::create([
            'name' => 'GAD Coordinator',
            'email' => 'gadcoordinator@example.test',
            'password' => 'password',
            'role' => 'admin',
            'account_type' => 'coordinator',
            'account_status' => 'active',
        ]);
        $report = Report::create([
            'incident_id' => 'SN-UPDATE-1',
            'nature' => 'Stalking',
            'status' => 'Pending',
            'priority' => 'Medium',
        ]);

        $this->actingAs($coordinator)
            ->post(route('admin.report-tracking.update', $report->id), [
                'status' => 'Under Review',
                'priority' => 'High',
                'admin_comment' => 'A coordinator is reviewing this report.',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('reports', [
            'id' => $report->id,
            'status' => 'Under Review',
            'priority' => 'High',
            'admin_comment' => 'A coordinator is reviewing this report.',
        ]);
        $this->assertDatabaseHas('report_status_updates', [
            'report_id' => $report->id,
            'admin_id' => $coordinator->id,
            'to_status' => 'Under Review',
            'details' => 'A coordinator is reviewing this report.',
        ]);
        Report::create([
            'incident_id' => 'SN-UPDATE-LOW',
            'nature' => 'Catcalling',
            'status' => 'Pending',
            'priority' => 'Low',
        ]);

        $this->get(route('admin.report-tracking', ['report_id' => $report->id]))
            ->assertOk()
            ->assertSee('A coordinator is reviewing this report.');

        $this->get(route('admin.report-tracking'))
            ->assertOk()
            ->assertSeeInOrder(['SN-UPDATE-1', 'SN-UPDATE-LOW'])
            ->assertSee('High priority first');
    }

    public function test_coordinator_can_save_theme_and_open_reviews_but_not_admin_settings_sections(): void
    {
        $coordinator = User::create([
            'name' => 'GAD Coordinator',
            'email' => 'gadcoordinator@example.test',
            'password' => 'password',
            'role' => 'admin',
            'account_type' => 'coordinator',
            'account_status' => 'active',
        ]);

        $this->actingAs($coordinator)
            ->get(route('admin.settings'))
            ->assertOk()
            ->assertSee('Coordinator profile')
            ->assertSee('Dark Mode')
            ->assertSee('requestSubmit()', false)
            ->assertSee('saved automatically')
            ->assertDontSee('Save appearance');

        $this->patch(route('admin.settings.update'), ['theme' => 'dark'])
            ->assertRedirect();
        $this->assertDatabaseHas('users', ['id' => $coordinator->id, 'admin_theme' => 'dark']);

        $this->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('data-student-theme-enabled="true"', false)
            ->assertSee('data-student-theme="dark"', false)
            ->assertSee('coordinator-navbar')
            ->assertSeeText('Settings')
            ->assertSeeText('Log out');

        $this->get(route('admin.settings', ['section' => 'archive']))->assertForbidden();
        $this->get(route('admin.settings', ['section' => 'activity']))->assertForbidden();
        $this->get(route('admin.settings', ['section' => 'feedback']))->assertOk();
    }

    public function test_coordinator_dashboard_links_named_recent_reviews_to_review_settings(): void
    {
        $coordinator = User::create([
            'name' => 'GAD Coordinator',
            'email' => 'gadcoordinator@example.test',
            'password' => 'password',
            'role' => 'admin',
            'account_type' => 'coordinator',
            'account_status' => 'active',
        ]);
        $reviewer = User::create([
            'name' => 'Review Author',
            'email' => 'reviewer@example.test',
            'password' => 'password',
            'role' => 'student',
            'account_type' => 'student',
            'account_status' => 'active',
        ]);
        \App\Models\CustomerSatisfactionSurvey::create([
            'user_id' => $reviewer->id,
            'subject' => 'platform',
            'rating' => 5,
            'feedback' => 'Helpful and easy to use.',
        ]);

        $this->actingAs($coordinator)
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('Review Author')
            ->assertSee(route('admin.settings', ['section' => 'feedback']), false);
    }

    public function test_gad_coordinator_can_open_messages_and_send_a_message(): void
    {
        $coordinator = User::create([
            'name' => 'GAD Coordinator',
            'email' => 'gadcoordinator@example.test',
            'password' => 'password',
            'role' => 'admin',
            'account_type' => 'coordinator',
            'account_status' => 'active',
        ]);
        $gadAdmin = User::create([
            'name' => 'GAD Administrator',
            'email' => 'gadadmin@example.test',
            'password' => 'password',
            'role' => 'admin',
            'account_type' => 'admin',
            'account_status' => 'active',
        ]);

        $this->actingAs($coordinator)
            ->get(route('student.messaging'))
            ->assertOk()
            ->assertViewIs('student.messaging')
            ->assertSeeText('GAD Office')
            ->assertDontSee('Send as Anonymous');

        $this->get(route('admin.messages'))->assertForbidden();

        $this->postJson(route('student.messages.store'), [
            'message' => 'Hello from the GAD Coordinator',
            'is_anonymous' => true,
        ])->assertOk()
            ->assertJsonPath('message.message', 'Hello from the GAD Coordinator')
            ->assertJsonPath('message.is_anonymous', false);

        $this->assertDatabaseHas('messages', [
            'sender_id' => $coordinator->id,
            'receiver_id' => $gadAdmin->id,
            'message' => 'Hello from the GAD Coordinator',
            'sender_type' => 'student',
        ]);

        $this->actingAs($gadAdmin)
            ->get(route('admin.messages'))
            ->assertOk()
            ->assertSeeText('GAD Coordinator')
            ->assertSee(route('admin.messages', ['student_id' => $coordinator->id]), false);

        $this->actingAs($gadAdmin)
            ->get(route('admin.messages', ['student_id' => $coordinator->id]))
            ->assertOk()
            ->assertViewHas('selectedStudent', fn ($user) => $user->is($coordinator));

        $this->postJson(route('admin.messages.store'), [
            'student_id' => $coordinator->id,
            'message' => 'Reply from the GAD Administrator',
        ])->assertOk()
            ->assertJsonPath('message.message', 'Reply from the GAD Administrator');

        $this->actingAs($coordinator)
            ->getJson(route('student.messages.feed'))
            ->assertOk()
            ->assertJsonCount(2, 'messages')
            ->assertJsonPath('messages.0.is_anonymous', false)
            ->assertJsonPath('messages.1.message', 'Reply from the GAD Administrator');
    }

    public function test_admin_message_thread_displays_the_selected_alias_or_anonymous_label(): void
    {
        $student = User::create([
            'name' => 'Test Student',
            'email' => 'student@example.test',
            'password' => 'password',
            'role' => 'student',
            'account_type' => 'student',
            'account_status' => 'active',
            'cloak_alias' => 'Silver Sparrow',
        ]);
        $admin = User::create([
            'name' => 'GAD Administrator',
            'email' => 'gadadmin@example.test',
            'password' => 'password',
            'role' => 'admin',
            'account_type' => 'admin',
            'account_status' => 'active',
        ]);
        \App\Models\Message::create([
            'sender_id' => $student->id,
            'receiver_id' => $admin->id,
            'message' => 'Private concern',
            'sender_type' => 'student',
            'is_anonymous' => true,
        ]);
        \App\Models\Message::create([
            'sender_id' => $student->id,
            'receiver_id' => $admin->id,
            'message' => 'Follow-up',
            'sender_type' => 'student',
            'is_anonymous' => false,
        ]);

        $this->actingAs($admin)
            ->get(route('admin.messages', ['student_id' => $student->id]))
            ->assertOk()
            ->assertSee('Anonymous')
            ->assertSee('Silver Sparrow')
            ->assertSee('message-identity-label', false);
    }

    public function test_gad_admins_share_coordinator_conversations(): void
    {
        $coordinator = User::create([
            'name' => 'GAD Coordinator',
            'email' => 'gadcoordinator@example.test',
            'password' => 'password',
            'role' => 'admin',
            'account_type' => 'coordinator',
            'account_status' => 'active',
        ]);
        User::create([
            'name' => 'First GAD Administrator',
            'email' => 'gadadmin-first@example.test',
            'password' => 'password',
            'role' => 'admin',
            'account_type' => 'admin',
            'account_status' => 'active',
        ]);
        $signedInAdmin = User::create([
            'name' => 'Second GAD Administrator',
            'email' => 'gadadmin-second@example.test',
            'password' => 'password',
            'role' => 'admin',
            'account_type' => 'admin',
            'account_status' => 'active',
        ]);

        $this->actingAs($coordinator)
            ->postJson(route('student.messages.store'), ['message' => 'Please contact me'])
            ->assertOk();

        $this->actingAs($signedInAdmin)
            ->get(route('admin.messages'))
            ->assertOk()
            ->assertSeeText('GAD Coordinator')
            ->assertSee(route('admin.messages', ['student_id' => $coordinator->id]), false);

        $this->getJson(route('admin.messages.feed'))
            ->assertOk()
            ->assertJsonPath('unread_count', 1)
            ->assertJsonPath('latest_unread_student_id', $coordinator->id);

        $this->postJson(route('admin.messages.store'), [
            'student_id' => $coordinator->id,
            'message' => 'We received your message',
        ])->assertOk();

        $this->actingAs($coordinator)
            ->getJson(route('student.messages.feed'))
            ->assertOk()
            ->assertJsonCount(2, 'messages')
            ->assertJsonPath('messages.1.message', 'We received your message');
    }

    public function test_deleted_messages_are_hidden_from_coordinator_and_admin_threads(): void
    {
        $coordinator = User::create([
            'name' => 'GAD Coordinator',
            'email' => 'gadcoordinator@example.test',
            'password' => 'password',
            'role' => 'admin',
            'account_type' => 'coordinator',
            'account_status' => 'active',
        ]);
        $gadAdmin = User::create([
            'name' => 'GAD Administrator',
            'email' => 'gadadmin@example.test',
            'password' => 'password',
            'role' => 'admin',
            'account_type' => 'admin',
            'account_status' => 'active',
        ]);
        $deletedMessage = \App\Models\Message::create([
            'sender_id' => $coordinator->id,
            'receiver_id' => $gadAdmin->id,
            'message' => '',
            'deleted_at' => now(),
            'sender_type' => 'student',
        ]);

        $this->actingAs($coordinator)
            ->get(route('student.messaging'))
            ->assertOk()
            ->assertDontSee('data-message-id="' . $deletedMessage->id . '"');
        $this->getJson(route('student.messages.feed'))
            ->assertOk()
            ->assertJsonCount(0, 'messages');

        $this->actingAs($gadAdmin)
            ->get(route('admin.messages', ['student_id' => $coordinator->id]))
            ->assertOk()
            ->assertDontSee('data-message-id="' . $deletedMessage->id . '"');
        $this->getJson(route('admin.messages.feed', ['student_id' => $coordinator->id]))
            ->assertOk()
            ->assertJsonCount(0, 'messages');
    }

    public function test_unavailable_message_recipient_returns_to_conversation_list_instead_of_404(): void
    {
        $admin = User::create([
            'name' => 'GAD Administrator',
            'email' => 'gadadmin@example.test',
            'password' => 'password',
            'role' => 'admin',
            'account_type' => 'admin',
            'account_status' => 'active',
        ]);
        $otherAdmin = User::create([
            'name' => 'Private Administrator',
            'email' => 'private-admin@example.test',
            'password' => 'password',
            'role' => 'admin',
            'account_type' => 'admin',
            'account_status' => 'active',
        ]);

        $this->actingAs($admin)
            ->get(route('admin.messages', ['student_id' => $otherAdmin->id]))
            ->assertOk()
            ->assertViewIs('admin.messages')
            ->assertViewHas('selectedStudent', null)
            ->assertViewHas('selectedStudentId', null)
            ->assertSeeText('This conversation is no longer available.')
            ->assertDontSeeText('Private Administrator');
    }

    public function test_gad_coordinator_is_excluded_from_admin_access_gate(): void
    {
        $coordinator = new User();
        $coordinator->fill(['role' => 'admin', 'account_type' => 'coordinator']);
        $legacyCoordinator = new User();
        $legacyCoordinator->fill([
            'role' => 'admin',
            'account_type' => 'admin',
            'email' => 'gadcoordinator@psu.edu.ph',
        ]);
        $administrator = new User();
        $administrator->fill(['role' => 'admin', 'account_type' => 'admin']);

        $this->assertFalse(Gate::forUser($coordinator)->allows('admin-access'));
        $this->assertTrue(Gate::forUser($coordinator)->allows('gad-analytics-access'));
        $this->assertFalse(Gate::forUser($legacyCoordinator)->allows('admin-access'));
        $this->assertTrue(Gate::forUser($legacyCoordinator)->allows('gad-analytics-access'));
        $this->assertTrue(Gate::forUser($administrator)->allows('admin-access'));
    }
}
