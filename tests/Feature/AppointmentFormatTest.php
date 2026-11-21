<?php

namespace Tests\Feature;

use App\Models\User;
use App\Notifications\AccountStatusNotification;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class AppointmentFormatTest extends TestCase
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
            $table->string('department')->nullable();
            $table->timestamps();
        });

        Schema::create('appointment_requests', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('full_name');
            $table->string('student_id')->nullable();
            $table->string('purpose_of_visit');
            $table->string('urgency_level');
            $table->date('scheduled_date');
            $table->time('scheduled_time');
            $table->longText('description');
            $table->string('format');
            $table->string('status')->default('pending');
            $table->longText('admin_notes')->nullable();
            $table->longText('cancellation_reason')->nullable();
            $table->timestamps();
        });

        Schema::create('gad_schedules', function (Blueprint $table): void {
            $table->id();
            $table->string('title');
            $table->string('location')->nullable();
            $table->dateTime('available_from');
            $table->dateTime('available_until')->nullable();
            $table->text('details')->nullable();
            $table->string('status')->default('active');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
        });

        Schema::create('announcements', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('title');
            $table->longText('body');
            $table->timestamps();
        });

        Schema::create('messages', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('sender_id');
            $table->unsignedBigInteger('receiver_id');
            $table->text('message');
            $table->text('original_message')->nullable();
            $table->dateTime('deleted_at')->nullable();
            $table->string('sender_type')->nullable();
            $table->dateTime('read_at')->nullable();
            $table->timestamps();
        });
    }

    public function test_appointment_format_is_required(): void
    {
        $user = $this->student();

        $response = $this->actingAs($user)->post(route('student.schedule-appointment.store'), $this->appointmentData());

        $response->assertSessionHasErrors('format');
        $this->assertDatabaseCount('appointment_requests', 0);
    }

    public function test_successful_submission_returns_to_schedule_form_with_confirmation(): void
    {
        $response = $this->actingAs($this->student())->from(route('student.schedule-appointment'))->post(route('student.schedule-appointment.store'), $this->appointmentData([
            'format' => 'Face-to-Face',
        ]));

        $response->assertRedirect(route('student.schedule-appointment'))
            ->assertSessionHas('success');
    }

    public function test_student_can_submit_appointment_when_database_does_not_auto_generate_ids(): void
    {
        Schema::drop('appointment_requests');
        Schema::create('appointment_requests', function (Blueprint $table): void {
            $table->unsignedBigInteger('id')->primary();
            $table->unsignedBigInteger('user_id');
            $table->string('full_name');
            $table->string('student_id')->nullable();
            $table->string('purpose_of_visit');
            $table->string('urgency_level');
            $table->date('scheduled_date');
            $table->time('scheduled_time');
            $table->longText('description');
            $table->string('format');
            $table->string('status')->default('pending');
            $table->longText('admin_notes')->nullable();
            $table->longText('cancellation_reason')->nullable();
            $table->timestamps();
        });

        $user = $this->student();
        $this->actingAs($user)
            ->post(route('student.schedule-appointment.store'), $this->appointmentData([
                'format' => 'Face-to-Face',
            ]))
            ->assertRedirect(route('student.schedule-appointment'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('appointment_requests', [
            'id' => 1,
            'user_id' => $user->id,
            'format' => 'Face-to-Face',
        ]);
    }

    public function test_student_cannot_request_an_appointment_on_an_unavailable_date(): void
    {
        $date = now()->addDays(3)->toDateString();
        DB::table('gad_schedules')->insert([
            'title' => 'Unavailable / Closed',
            'available_from' => $date . ' 00:00:00',
            'available_until' => $date . ' 23:59:59',
            'details' => 'unavailable',
            'status' => 'unavailable',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $response = $this->actingAs($this->student())->post(route('student.schedule-appointment.store'), $this->appointmentData([
            'format' => 'Face-to-Face',
            'scheduled_date' => $date,
        ]));

        $response->assertSessionHasErrors('scheduled_date');
        $this->assertDatabaseCount('appointment_requests', 0);
    }

    public function test_appointment_form_offers_format_choices_and_user_faq_assistant(): void
    {
        $response = $this->actingAs($this->student())->get(route('student.schedule-appointment'));

        $response->assertOk()
            ->assertSee('Appointment Format')
            ->assertSee('value="Face-to-Face"', false)
            ->assertSee('value="Online"', false)
            ->assertSee('id="gadFaqWidget"', false)
            ->assertSee('GAD Guide');
    }

    public function test_student_dashboard_shows_floating_gad_faq_assistant(): void
    {
        $response = $this->actingAs($this->student())->get(route('student.dashboard'));

        $response->assertOk()
            ->assertSee('id="gadFaqWidget"', false)
            ->assertSee('GAD Guide')
            ->assertSee('What is Republic Act 9262?')
            ->assertSee(route('personal-data.edit'))
            ->assertDontSee(route('student.report'), false);
    }

    public function test_selected_appointment_format_is_saved(): void
    {
        $user = $this->student();

        $response = $this->actingAs($user)->post(route('student.schedule-appointment.store'), $this->appointmentData([
            'format' => 'Online',
        ]));

        $response->assertRedirect(route('student.schedule-appointment'));
        $this->assertDatabaseHas('appointment_requests', [
            'user_id' => $user->id,
            'format' => 'Online',
        ]);
    }

    public function test_satisfaction_survey_opens_only_for_completed_online_appointments(): void
    {
        Schema::create('customer_satisfaction_surveys', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id');
            $table->string('subject', 20)->nullable();
            $table->unsignedBigInteger('subject_id')->nullable();
            $table->unsignedTinyInteger('rating');
            $table->text('feedback')->nullable();
            $table->json('responses')->nullable();
            $table->timestamps();
        });

        $user = $this->student();
        $onlineCompletedId = $this->createAppointment($user, 'completed');
        $onlinePendingId = $this->createAppointment($user, 'pending');
        $faceToFaceCompletedId = DB::table('appointment_requests')->insertGetId([
            'user_id' => $user->id,
            'full_name' => $user->name,
            'student_id' => 'PSU-1001',
            'purpose_of_visit' => 'Not specified',
            'urgency_level' => 'Medium',
            'scheduled_date' => now()->addDay()->toDateString(),
            'scheduled_time' => '10:30',
            'description' => 'Test appointment',
            'format' => 'Face-to-Face',
            'status' => 'completed',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->actingAs($user)
            ->get(route('student.appointments'))
            ->assertOk()
            ->assertSee('id="customerSatisfactionModal"', false)
            ->assertSee('name="subject" value="appointment"', false)
            ->assertSee('name="subject_id" value="' . $onlineCompletedId . '"', false)
            ->assertDontSee('name="subject_id" value="' . $onlinePendingId . '"', false)
            ->assertDontSee('name="subject_id" value="' . $faceToFaceCompletedId . '"', false);

        foreach ([$onlinePendingId, $faceToFaceCompletedId] as $unavailableId) {
            $this->post(route('customer-satisfaction.store'), [
                'subject' => 'appointment',
                'subject_id' => $unavailableId,
                'rating' => 5,
                'responses' => [4, 4, 4, 4, 4],
            ])->assertStatus(409);
        }

        $this->post(route('customer-satisfaction.store'), [
            'subject' => 'appointment',
            'subject_id' => $onlineCompletedId,
            'rating' => 5,
            'responses' => [4, 4, 4, 4, 4],
        ])->assertRedirect();

        $this->assertDatabaseHas('customer_satisfaction_surveys', [
            'user_id' => $user->id,
            'subject' => 'appointment',
            'subject_id' => $onlineCompletedId,
        ]);
    }

    public function test_student_can_cancel_pending_appointment(): void
    {
        $user = $this->student();
        $appointmentId = DB::table('appointment_requests')->insertGetId([
            'user_id' => $user->id,
            'full_name' => 'Test Student',
            'student_id' => 'PSU-1001',
            'purpose_of_visit' => 'Not specified',
            'urgency_level' => 'Medium',
            'scheduled_date' => now()->addDay()->toDateString(),
            'scheduled_time' => '10:30',
            'description' => 'I would like to request an appointment.',
            'format' => 'Online',
            'status' => 'pending',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $response = $this->actingAs($user)->post(route('student.appointments.cancel', $appointmentId));

        $response->assertRedirect(route('student.appointments'))
            ->assertSessionHas('success', 'Appointment cancelled.');
        $this->assertDatabaseHas('appointment_requests', [
            'id' => $appointmentId,
            'status' => 'cancelled',
        ]);

        $this->get(route('student.appointments'))
            ->assertOk()
            ->assertSee('You cancelled this appointment.');
    }

    public function test_student_can_delete_only_cancelled_or_rejected_appointments(): void
    {
        $user = $this->student();
        $cancelledId = $this->createAppointment($user, 'cancelled');
        $rejectedId = $this->createAppointment($user, 'rejected');
        $pendingId = $this->createAppointment($user, 'pending');

        $this->actingAs($user)
            ->get(route('student.appointments'))
            ->assertOk()
            ->assertSee('You cancelled this appointment.')
            ->assertSee('The GAD Office rejected this appointment request.');

        $this->actingAs($user)
            ->delete(route('student.appointments.delete', $cancelledId))
            ->assertRedirect(route('student.appointments'))
            ->assertSessionHas('success', 'Appointment record deleted.');

        $this->assertDatabaseMissing('appointment_requests', ['id' => $cancelledId]);

        $this->delete(route('student.appointments.delete', $rejectedId))
            ->assertRedirect(route('student.appointments'));
        $this->assertDatabaseMissing('appointment_requests', ['id' => $rejectedId]);

        $this->delete(route('student.appointments.delete', $pendingId))->assertForbidden();
        $this->assertDatabaseHas('appointment_requests', ['id' => $pendingId, 'status' => 'pending']);
    }

    public function test_admin_can_search_students_and_employees_by_name_or_appointment_id(): void
    {
        $student = $this->student();
        $student->update(['name' => 'Alex Student']);
        DB::table('appointment_requests')->insert([
            'user_id' => $student->id,
            'full_name' => 'Alex Student',
            'student_id' => 'PSU-2026-1042',
            'purpose_of_visit' => 'Not specified',
            'urgency_level' => 'Medium',
            'scheduled_date' => now()->addDay()->toDateString(),
            'scheduled_time' => '10:30',
            'description' => 'Appointment for search test',
            'format' => 'Online',
            'status' => 'pending',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $admin = User::create([
            'name' => 'Test Admin',
            'email' => 'admin@example.test',
            'password' => 'password',
            'role' => 'admin',
        ]);

        $this->actingAs($admin)
            ->getJson(route('admin.messages.search-users', ['q' => 'Alex']))
            ->assertOk()
            ->assertJsonPath('users.0.name', 'Alex Student')
            ->assertJsonPath('users.0.student_employee_id', 'PSU-2026-1042');

        $this->getJson(route('admin.messages.search-users', ['q' => 'PSU-2026-1042']))
            ->assertOk()
            ->assertJsonPath('users.0.id', $student->id);
    }

    public function test_student_can_read_and_end_only_their_urgent_call(): void
    {
        $student = $this->student();
        $callId = 'call-student-test';
        Cache::put('urgent_calls', [[
            'id' => $callId,
            'room' => 'sinag-urgent-test',
            'status' => 'joined',
            'student_id' => $student->id,
            'student_name' => $student->name,
        ]], now()->addHour());

        $this->actingAs($student)
            ->getJson(route('student.urgent.status', $callId))
            ->assertOk()
            ->assertJsonPath('status', 'joined');

        $this->postJson(route('student.urgent.end', $callId))
            ->assertOk()
            ->assertJsonPath('status', 'ended');
        $this->getJson(route('student.urgent.status', $callId))
            ->assertOk()
            ->assertJsonPath('status', 'ended');
    }

    public function test_admin_can_end_a_shared_urgent_call(): void
    {
        $student = $this->student();
        $admin = User::create([
            'name' => 'Test Admin',
            'email' => 'admin@example.test',
            'password' => 'password',
            'role' => 'admin',
        ]);
        $callId = 'call-admin-test';
        Cache::put('urgent_calls', [[
            'id' => $callId,
            'room' => 'sinag-urgent-test',
            'status' => 'joined',
            'student_id' => $student->id,
            'student_name' => $student->name,
        ]], now()->addHour());

        $this->actingAs($admin)
            ->postJson(route('admin.urgent.calls.end', $callId))
            ->assertOk()
            ->assertJsonPath('status', 'ended');

        $this->actingAs($student)
            ->getJson(route('student.urgent.status', $callId))
            ->assertOk()
            ->assertJsonPath('status', 'ended');
    }

    public function test_admin_can_start_a_video_call_for_a_student(): void
    {
        Cache::forget('urgent_calls');
        $student = $this->student();
        $admin = User::create([
            'name' => 'Test Admin',
            'email' => 'admin@example.test',
            'password' => 'password',
            'role' => 'admin',
        ]);

        $response = $this->actingAs($admin)
            ->post(route('admin.urgent.calls.start', $student->id));
        $call = Cache::get('urgent_calls')[0];

        $response->assertRedirect(route('admin.urgent.calls', ['call_id' => $call['id']]));
        $this->assertSame('admin', $call['initiated_by']);
        $this->assertSame($student->id, $call['student_id']);
        $this->assertSame('waiting', $call['status']);
    }

    public function test_student_can_accept_only_an_admin_initiated_call_for_their_account(): void
    {
        $student = $this->student();
        $otherStudent = User::create([
            'name' => 'Other Student',
            'email' => 'other-student@example.test',
            'password' => 'password',
            'role' => 'student',
        ]);
        Cache::put('urgent_calls', [[
            'id' => 'incoming-admin-call',
            'room' => 'sinag-urgent-incoming',
            'status' => 'waiting',
            'initiated_by' => 'admin',
            'student_id' => $student->id,
            'student_name' => $student->name,
        ]], now()->addHour());

        $this->actingAs($student)
            ->get(route('student.urgent', ['call_id' => 'incoming-admin-call']))
            ->assertOk()
            ->assertSee('const incomingCallId = "incoming-admin-call";', false);

        $this->actingAs($otherStudent)
            ->postJson(route('student.urgent.join', 'incoming-admin-call'))
            ->assertNotFound();

        $this->actingAs($student)
            ->postJson(route('student.urgent.join', 'incoming-admin-call'))
            ->assertOk()
            ->assertJsonPath('status', 'joined')
            ->assertJsonPath('room', 'sinag-urgent-incoming');

        $this->assertSame('joined', Cache::get('urgent_calls')[0]['status']);
    }

    public function test_user_notification_feed_includes_unread_messages_and_call_status(): void
    {
        Schema::create('notifications', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->string('type');
            $table->morphs('notifiable');
            $table->text('data');
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
        });
        $student = $this->student();
        $student->notify(new AccountStatusNotification('approved'));
        $admin = User::create([
            'name' => 'Test Admin',
            'email' => 'admin@example.test',
            'password' => 'password',
            'role' => 'admin',
        ]);
        $messageId = DB::table('messages')->insertGetId([
            'sender_id' => $admin->id,
            'receiver_id' => $student->id,
            'message' => 'The GAD Office replied.',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        Cache::put('urgent_calls', [[
            'id' => 'call-notification-test',
            'status' => 'waiting',
            'initiated_by' => 'admin',
            'student_id' => $student->id,
        ]], now()->addHour());

        $this->actingAs($student)
            ->getJson(route('student.notifications.feed'))
            ->assertOk()
            ->assertJsonPath('unread_count', 1)
            ->assertJsonPath('latest_unread_id', $messageId)
            ->assertJsonPath('latest_call.id', 'call-notification-test')
            ->assertJsonPath('latest_call.initiated_by', 'admin')
            ->assertJsonPath('latest_call.status', 'waiting')
            ->assertJsonPath('app_notification.message', 'Your SINAG account has been approved. You can now sign in.');

        $this->assertNotNull(DB::table('notifications')->value('read_at'));
    }

    private function student(): User
    {
        return User::create([
            'name' => 'Test Student',
            'email' => 'student@example.test',
            'password' => 'password',
            'role' => 'student',
        ]);
    }

    private function appointmentData(array $overrides = []): array
    {
        return array_merge([
            'full_name' => 'Test Student',
            'student_id' => 'PSU-1001',
            'urgency_level' => 'Medium',
            'scheduled_date' => now()->addDay()->toDateString(),
            'scheduled_time' => '10:30',
            'description' => 'I would like to request an appointment.',
        ], $overrides);
    }

    private function createAppointment(User $user, string $status): int
    {
        return DB::table('appointment_requests')->insertGetId([
            'user_id' => $user->id,
            'full_name' => $user->name,
            'student_id' => 'PSU-1001',
            'purpose_of_visit' => 'Not specified',
            'urgency_level' => 'Medium',
            'scheduled_date' => now()->addDay()->toDateString(),
            'scheduled_time' => '10:30',
            'description' => 'Test appointment',
            'format' => 'Online',
            'status' => $status,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
