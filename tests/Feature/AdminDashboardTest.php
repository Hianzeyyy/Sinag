<?php

namespace Tests\Feature;

use App\Models\AppointmentRequest;
use App\Models\Report;
use App\Models\ReportStatusUpdate;
use App\Models\CustomerSatisfactionSurvey;
use App\Models\Suggestion;
use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class AdminDashboardTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Schema::create('users', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->string('first_name')->nullable();
            $table->string('email')->unique();
            $table->string('password');
            $table->string('role')->default('student');
            $table->string('account_type')->nullable();
            $table->string('account_status')->nullable();
            $table->string('department')->nullable();
            $table->string('admin_theme')->default('light');
            $table->string('profile_photo_path')->nullable();
            $table->timestamp('last_seen_at')->nullable();
            $table->timestamps();
        });

        Schema::create('reports', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('incident_id');
            $table->string('nature')->nullable();
            $table->string('status')->nullable();
            $table->string('priority')->nullable();
            $table->text('admin_comment')->nullable();
            $table->boolean('is_anonymous')->default(true);
            $table->timestamp('seen_at')->nullable();
            $table->timestamp('resolution_notice_sent_at')->nullable();
            $table->timestamp('archived_at')->nullable();
            $table->unsignedBigInteger('archived_by')->nullable();
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
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('category')->nullable();
            $table->text('message')->nullable();
            $table->string('status')->nullable();
            $table->boolean('is_anonymous')->default(false);
            $table->unsignedBigInteger('views_count')->default(0);
            $table->timestamp('archived_at')->nullable();
            $table->unsignedBigInteger('archived_by')->nullable();
            $table->timestamps();
        });

        Schema::create('suggestion_comments', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('suggestion_id');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->text('comment');
            $table->timestamps();
        });

        Schema::create('suggestion_user_upvotes', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('suggestion_id');
            $table->unsignedBigInteger('user_id');
            $table->timestamps();
        });

        Schema::create('appointment_requests', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('full_name');
            $table->string('urgency_level')->nullable();
            $table->date('scheduled_date');
            $table->time('scheduled_time');
            $table->string('status');
            $table->timestamps();
        });

        Schema::create('messages', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('sender_id');
            $table->unsignedBigInteger('receiver_id');
            $table->text('message');
            $table->string('sender_type')->default('student');
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
        });

        Schema::create('password_reset_requests', function (Blueprint $table): void {
            $table->id();
            $table->string('status');
            $table->timestamps();
        });

        Schema::create('suggestion_reports', function (Blueprint $table): void {
            $table->id();
            $table->string('status');
            $table->timestamps();
        });

        Schema::create('customer_satisfaction_surveys', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('subject')->nullable();
            $table->unsignedTinyInteger('rating');
            $table->text('feedback')->nullable();
            $table->json('responses')->nullable();
            $table->timestamps();
        });
    }

    public function test_admin_dashboard_shows_recent_accounts_cases_and_deep_linked_appointments(): void
    {
        $admin = User::create([
            'name' => 'GAD Admin',
            'email' => 'admin@example.test',
            'password' => 'password',
            'role' => 'admin',
            'account_status' => 'active',
            'admin_theme' => 'dark',
        ]);
        $reporter = User::create([
            'name' => 'Mara Santos',
            'first_name' => 'Mara',
            'email' => 'mara@example.test',
            'password' => 'password',
            'role' => 'student',
            'account_type' => 'student',
            'account_status' => 'active',
        ]);
        User::create([
            'name' => 'Legacy Approved User',
            'email' => 'legacy-approved@example.test',
            'password' => 'password',
            'role' => 'student',
            'account_status' => 'approved',
        ]);
        User::create([
            'name' => 'Pending User',
            'email' => 'pending@example.test',
            'password' => 'password',
            'role' => 'student',
            'account_status' => 'pending',
        ]);
        Report::create([
            'user_id' => $reporter->id,
            'incident_id' => 'SN-OPEN-1',
            'nature' => 'Stalking',
            'status' => 'Resolved',
            'priority' => 'Medium',
            'is_anonymous' => false,
        ]);
        $suggestion = Suggestion::create([
            'user_id' => $reporter->id,
            'category' => 'Safe spaces',
            'message' => 'Student idea for safer spaces',
            'is_anonymous' => true,
            'status' => 'new',
        ]);
        $suggestion->comments()->create([
            'user_id' => $reporter->id,
            'comment' => 'This would help students.',
        ]);
        $suggestion->upvotes()->attach($reporter->id);
        $suggestion->views_count = 7;
        $suggestion->save();
        CustomerSatisfactionSurvey::create([
            'user_id' => $reporter->id,
            'rating' => 5,
            'feedback' => 'Helpful and clear.',
        ]);
        $appointment = AppointmentRequest::create([
            'user_id' => $reporter->id,
            'full_name' => 'Mara Santos',
            'urgency_level' => 'Medium',
            'scheduled_date' => today()->toDateString(),
            'scheduled_time' => '10:30:00',
            'status' => 'approved',
        ]);

        $response = $this->actingAs($admin)->get(route('admin.dashboard'))->assertOk();
        $document = new \DOMDocument();
        $previousLibxmlErrorSetting = libxml_use_internal_errors(true);
        $document->loadHTML($response->getContent());
        libxml_clear_errors();
        libxml_use_internal_errors($previousLibxmlErrorSetting);

        $xpath = new \DOMXPath($document);
        $this->assertSame(1, $xpath->query(
            '//main[contains(concat(" ", normalize-space(@class), " "), " admin-main ")]'
            .'//*[contains(concat(" ", normalize-space(@class), " "), " admin-dashboard-page ")]'
        )->length);
        $darkTableStylesAreInHead = false;
        foreach ($xpath->query('//head/style') as $style) {
            if (str_contains(
                $style->textContent,
                'body[data-admin-theme="dark"] .admin-main :is(table, .table) :is(th, td)'
            )) {
                $darkTableStylesAreInHead = true;
                break;
            }
        }
        $this->assertTrue($darkTableStylesAreInHead);

        $response->assertSee('Recent accounts')
            ->assertSee('Newest sign-ups; hover for a first name or open a profile.')
            ->assertSeeText('Approved accounts: 2')
            ->assertSeeText('Pending review: 1')
            ->assertSee('Resolved Cases')
            ->assertSee('Resolved reports out of all reports received.')
            ->assertSeeText('100% resolved')
            ->assertSee('Resolved / total reports')
            ->assertSeeText('1 / 1')
            ->assertSee('data-admin-theme="dark"', false)
            ->assertSee('main-content admin-main', false)
            ->assertSee('body[data-admin-theme="dark"] .admin-main :is(table, .table) :is(th, td)', false)
            ->assertSee(':is(th, td) :not(.badge):not(.btn)', false)
            ->assertSee('background: #1b1529 !important;', false)
            ->assertSee('.admin-main > .container-fluid', false)
            ->assertSee('function resetAdminScroll()', false)
            ->assertSee("window.addEventListener('load', resetAdminScroll", false)
            ->assertDontSee('if (window.location.hash) return;', false)
            ->assertSee('open='.$reporter->id)
            ->assertSee(route('admin.appointments', ['open' => $appointment->id]), false)
            ->assertSee('Most used report categories')
            ->assertSee('Suggestion categories')
            ->assertSee('data-chart-toggle')
            ->assertSee('category-pie')
            ->assertSee('avatar-orbit')
            ->assertSee('Ratings & feedback', false)
            ->assertSee('Anonymous 1')
            ->assertSee('Recent suggestions')
            ->assertSee('Student idea for safer spaces')
            ->assertSee('From: Anonymous')
            ->assertSee('1 like')
            ->assertSee('1 comment')
            ->assertSee('7 views')
            ->assertSee('Export report')
            ->assertSee('exportGadFilePreview')
            ->assertSee('admin-sidebar-toggle-floating')
            ->assertSee('admin-mobile-sidebar-toggle')
            ->assertSee('admin-dashboard-nav-row')
            ->assertSee('bi-chevron-right admin-sidebar-toggle-icon')
            ->assertDontSee('aria-label="Close admin navigation"');
    }

    public function test_admin_report_list_displays_latest_tracking_details(): void
    {
        $admin = User::create([
            'name' => 'GAD Admin',
            'email' => 'admin@example.test',
            'password' => 'password',
            'role' => 'admin',
            'account_status' => 'active',
        ]);
        $reporter = User::create([
            'name' => 'Mara Santos',
            'email' => 'mara@example.test',
            'password' => 'password',
            'role' => 'student',
            'account_type' => 'student',
            'account_status' => 'active',
        ]);
        $report = Report::create([
            'user_id' => $reporter->id,
            'incident_id' => 'SN-TRACK-1',
            'nature' => 'Harassment',
            'status' => 'Under Review',
            'priority' => 'Medium',
            'is_anonymous' => false,
        ]);
        ReportStatusUpdate::create([
            'report_id' => $report->id,
            'admin_id' => $admin->id,
            'from_status' => 'Pending',
            'to_status' => 'Under Review',
            'details' => 'The case is being reviewed and the reporter has been contacted.',
        ]);

        $this->actingAs($admin)
            ->get(route('admin.reports'))
            ->assertOk()
            ->assertSee('REPORT TRACKING')
            ->assertSee('reporter-alias-icon')
            ->assertSee('body[data-admin-theme="dark"] .reports-table-wrap .reporter-alias-icon', false)
            ->assertSee('1 timeline update')
            ->assertSee('Report tracking timeline')
            ->assertSee('Send this report to the GAD Coordinator')
            ->assertSee('Recommended for high-priority cases.')
            ->assertSee('Report submitted')
            ->assertSee('The case is being reviewed and the reporter has been contacted.');

        Report::create([
            'user_id' => $reporter->id,
            'incident_id' => 'SN-TRACK-2',
            'nature' => 'Bullying',
            'status' => 'Pending',
            'priority' => 'Low',
            'is_anonymous' => false,
        ]);
        $this->get(route('admin.reports'))
            ->assertOk()
            ->assertSee('Awaiting first admin progress update');
    }

    public function test_admin_can_forward_a_report_to_the_coordinator_and_track_the_forward(): void
    {
        $admin = User::create([
            'name' => 'GAD Admin',
            'email' => 'admin@example.test',
            'password' => 'password',
            'role' => 'admin',
            'account_type' => 'admin',
            'account_status' => 'active',
        ]);
        $coordinator = User::create([
            'name' => 'GAD Coordinator',
            'email' => 'coordinator@example.test',
            'password' => 'password',
            'role' => 'admin',
            'account_type' => 'coordinator',
            'account_status' => 'active',
        ]);
        $report = Report::create([
            'incident_id' => 'SN-HIGH-1',
            'nature' => 'Stalking',
            'status' => 'Pending',
            'priority' => 'High',
        ]);

        $this->actingAs($admin)
            ->post(route('admin.reports.update', $report->id), [
                'status' => 'Pending',
                'priority' => 'High',
                'admin_comment' => 'The case is being reviewed.',
                'send_to_coordinator' => '1',
                '_report_id' => $report->id,
            ])
            ->assertRedirect()
            ->assertSessionHas('success', "Report #{$report->incident_id} updated successfully and forwarded to the GAD Coordinator.");

        $this->assertDatabaseHas('messages', [
            'sender_id' => $admin->id,
            'receiver_id' => $coordinator->id,
            'sender_type' => 'admin',
        ]);
        $this->assertDatabaseHas('reports', [
            'id' => $report->id,
            'priority' => 'High',
            'admin_comment' => 'The case is being reviewed.',
        ]);
        $trackingDetails = ReportStatusUpdate::where('report_id', $report->id)->value('details');
        $this->assertStringContainsString('This report was sent to the GAD Coordinator for follow-up.', $trackingDetails);
        $this->assertStringContainsString(
            route('admin.report-tracking', ['report_id' => $report->id]),
            \App\Models\Message::where('receiver_id', $coordinator->id)->value('message')
        );
    }

    public function test_forwarding_without_a_coordinator_is_reported_and_does_not_save_a_false_forward(): void
    {
        $admin = User::create([
            'name' => 'GAD Admin',
            'email' => 'admin@example.test',
            'password' => 'password',
            'role' => 'admin',
            'account_type' => 'admin',
            'account_status' => 'active',
        ]);
        $report = Report::create([
            'incident_id' => 'SN-NO-COORDINATOR',
            'status' => 'Pending',
            'priority' => 'High',
        ]);

        $this->actingAs($admin)
            ->from(route('admin.reports'))
            ->post(route('admin.reports.update', $report->id), [
                'status' => 'Pending',
                'priority' => 'High',
                'admin_comment' => 'The case is being reviewed.',
                'send_to_coordinator' => '1',
                '_report_id' => $report->id,
            ])
            ->assertRedirect(route('admin.reports'))
            ->assertSessionHasErrors('send_to_coordinator');

        $this->assertDatabaseCount('messages', 0);
        $this->assertDatabaseMissing('report_status_updates', ['report_id' => $report->id]);
    }

    public function test_dashboard_greeting_uses_philippine_local_time(): void
    {
        $admin = User::create([
            'name' => 'GAD Admin',
            'email' => 'admin@example.test',
            'password' => 'password',
            'role' => 'admin',
            'account_status' => 'active',
        ]);

        \Carbon\Carbon::setTestNow(\Carbon\Carbon::parse('2026-11-26 13:09:00', 'UTC'));

        try {
            $this->actingAs($admin)
                ->get(route('admin.dashboard'))
                ->assertOk()
                ->assertSee('Good evening')
                ->assertSee('Thursday, November 26, 2026');
        } finally {
            \Carbon\Carbon::setTestNow();
        }
    }

    public function test_activity_log_and_archive_pages_render_their_empty_states(): void
    {
        $admin = User::create([
            'name' => 'GAD Admin',
            'email' => 'admin@example.test',
            'password' => 'password',
            'role' => 'admin',
            'account_status' => 'active',
        ]);
        $admin->forceFill(['last_seen_at' => now()])->save();

        $this->actingAs($admin)
            ->get(route('admin.activity-log'))
            ->assertRedirect(route('admin.settings', ['section' => 'activity']));

        $this->get(route('admin.settings', ['section' => 'activity']))
            ->assertOk()
            ->assertSee('Settings')
            ->assertSee(route('admin.settings', ['section' => 'archive']), false)
            ->assertSee(route('admin.settings', ['section' => 'activity']), false)
            ->assertSee('Recent Account Activity')
            ->assertSee('latest recorded visit')
            ->assertSee('Online recently');

        $this->get(route('admin.settings'))
            ->assertOk()
            ->assertSee('Dark Mode')
            ->assertSee('Light Mode')
            ->assertDontSee('Midnight Purple');

        $this->get(route('admin.archive'))
            ->assertRedirect(route('admin.settings', ['section' => 'archive']));

        $this->get(route('admin.settings', ['section' => 'archive']))
            ->assertOk()
            ->assertSee('Settings')
            ->assertSee('Archived Reports')
            ->assertSee('No archived reports.')
            ->assertSee('Archived Suggestions')
            ->assertSee('No archived suggestions.');
    }

    public function test_only_archived_reports_and_suggestions_can_be_permanently_deleted(): void
    {
        $admin = User::create([
            'name' => 'GAD Admin',
            'email' => 'admin@example.test',
            'password' => 'password',
            'role' => 'admin',
            'account_status' => 'active',
        ]);
        $activeReport = Report::create([
            'incident_id' => 'SN-ACTIVE-1',
            'status' => 'Pending',
        ]);
        $archivedReport = Report::create([
            'incident_id' => 'SN-ARCHIVED-1',
            'status' => 'Resolved',
            'archived_at' => now(),
            'archived_by' => $admin->id,
        ]);
        $statusUpdate = ReportStatusUpdate::create([
            'report_id' => $archivedReport->id,
            'admin_id' => $admin->id,
            'from_status' => 'Under Review',
            'to_status' => 'Resolved',
            'details' => 'The case has been resolved.',
        ]);
        $activeSuggestion = \App\Models\Suggestion::create([
            'category' => 'General',
            'status' => 'Pending',
        ]);
        $archivedSuggestion = \App\Models\Suggestion::create([
            'category' => 'General',
            'status' => 'Approved',
            'archived_at' => now(),
            'archived_by' => $admin->id,
        ]);
        \DB::table('suggestion_comments')->insert([
            'suggestion_id' => $archivedSuggestion->id,
            'user_id' => $admin->id,
            'comment' => 'Review note',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $archivedSuggestion->upvotes()->attach($admin->id);

        $this->actingAs($admin);
        $this->get(route('admin.settings', ['section' => 'archive']))
            ->assertOk()
            ->assertSee(route('admin.archive.reports.delete', $archivedReport->id), false)
            ->assertSee(route('admin.archive.suggestions.delete', $archivedSuggestion->id), false);

        $this->delete(route('admin.archive.reports.delete', $activeReport->id))
            ->assertNotFound();
        $this->delete(route('admin.archive.suggestions.delete', $activeSuggestion->id))
            ->assertNotFound();

        $this->delete(route('admin.archive.reports.delete', $archivedReport->id))
            ->assertRedirect()
            ->assertSessionHas('success', 'Report #SN-ARCHIVED-1 permanently deleted.');
        $this->delete(route('admin.archive.suggestions.delete', $archivedSuggestion->id))
            ->assertRedirect()
            ->assertSessionHas('success', 'Suggestion permanently deleted.');

        $this->assertDatabaseMissing('reports', ['id' => $archivedReport->id]);
        $this->assertDatabaseMissing('report_status_updates', ['id' => $statusUpdate->id]);
        $this->assertDatabaseMissing('suggestions', ['id' => $archivedSuggestion->id]);
        $this->assertDatabaseMissing('suggestion_comments', ['suggestion_id' => $archivedSuggestion->id]);
        $this->assertDatabaseMissing('suggestion_user_upvotes', ['suggestion_id' => $archivedSuggestion->id]);
        $this->assertDatabaseHas('reports', ['id' => $activeReport->id]);
        $this->assertDatabaseHas('suggestions', ['id' => $activeSuggestion->id]);
    }

    public function test_settings_ratings_page_shows_analytics_and_written_reviews(): void
    {
        $admin = User::create([
            'name' => 'GAD Admin',
            'email' => 'admin@example.test',
            'password' => 'password',
            'role' => 'admin',
            'account_status' => 'active',
        ]);
        $reviewer = User::create([
            'name' => 'Mara Santos',
            'email' => 'mara@example.test',
            'password' => 'password',
            'role' => 'student',
            'account_type' => 'student',
            'account_status' => 'active',
        ]);
        \App\Models\CustomerSatisfactionSurvey::create([
            'user_id' => $reviewer->id,
            'subject' => 'system',
            'rating' => 5,
            'feedback' => 'The reporting process felt clear and supportive.',
        ]);
        \App\Models\CustomerSatisfactionSurvey::create([
            'user_id' => $reviewer->id,
            'subject' => 'system',
            'rating' => 3,
            'feedback' => null,
        ]);

        $this->actingAs($admin)
            ->get(route('admin.settings', ['section' => 'feedback']))
            ->assertOk()
            ->assertSee('Ratings &amp; Reviews', false)
            ->assertSee('Average rating')
            ->assertSee('4.0')
            ->assertSee('Total ratings')
            ->assertSee('Written reviews')
            ->assertSee('Anonymous 1')
            ->assertDontSee('Mara Santos')
            ->assertSee('The reporting process felt clear and supportive.')
            ->assertSee('Rating distribution');
    }
}
