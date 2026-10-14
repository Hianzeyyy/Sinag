<?php

namespace Tests\Feature;

use App\Models\User;
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

        Schema::create('announcements', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('title');
            $table->longText('body');
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
        $response = $this->actingAs($this->student())->post(route('student.schedule-appointment.store'), $this->appointmentData([
            'format' => 'Face-to-Face',
        ]));

        $response->assertRedirect(route('student.schedule-appointment'))
            ->assertSessionHas('success');
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
            ->assertSee(route('student.report'));
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
