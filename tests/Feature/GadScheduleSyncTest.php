<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class GadScheduleSyncTest extends TestCase
{
    public function test_admin_schedule_changes_persist_and_are_available_in_the_student_feed(): void
    {
        Schema::create('users', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->string('role')->default('student');
            $table->timestamps();
        });

        Schema::create('gad_schedules', function (Blueprint $table): void {
            $table->id();
            $table->string('title');
            $table->string('location');
            $table->dateTime('available_from');
            $table->dateTime('available_until')->nullable();
            $table->text('details')->nullable();
            $table->string('status')->default('active');
            $table->unsignedBigInteger('created_by');
            $table->timestamps();
        });

        $admin = User::create([
            'name' => 'GAD Admin',
            'email' => 'admin@example.test',
            'password' => 'password',
            'role' => 'admin',
        ]);

        $date = '2028-05-04';
        $this->actingAs($admin)->postJson(route('admin.gad-schedules.sync'), [
            'updates' => [[
                'date' => $date,
                'availability_type' => 'available',
                'start_time' => null,
                'end_time' => null,
                'remove' => false,
            ]],
        ])->assertOk()->assertJsonPath('schedules.0.date', $date);

        $this->assertDatabaseHas('gad_schedules', [
            'title' => 'Available (All Day)',
            'available_from' => $date . ' 08:00:00',
            'status' => 'active',
        ]);

        $this->actingAs($admin)->postJson(route('admin.gad-schedules.sync'), [
            'updates' => [[
                'date' => $date,
                'availability_type' => 'hours',
                'start_time' => '10:00',
                'end_time' => '14:00',
                'remove' => false,
            ]],
        ])->assertOk();

        $this->assertSame(1, DB::table('gad_schedules')->whereDate('available_from', $date)->count());
        $this->assertDatabaseHas('gad_schedules', [
            'title' => 'Office Hours',
            'available_from' => $date . ' 10:00:00',
            'available_until' => $date . ' 14:00:00',
        ]);

        $this->getJson(route('schedule.feed'))
            ->assertOk()
            ->assertJsonPath('schedules.0.date', $date)
            ->assertJsonPath('schedules.0.start_time', '10:00')
            ->assertJsonPath('schedules.0.end_time', '14:00');
    }
}
