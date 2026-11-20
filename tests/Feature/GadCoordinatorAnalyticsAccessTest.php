<?php

namespace Tests\Feature;

use App\Models\Report;
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
            $table->timestamps();
        });

        Schema::create('reports', function (Blueprint $table): void {
            $table->id();
            $table->string('incident_id');
            $table->string('nature')->nullable();
            $table->string('status')->nullable();
            $table->string('priority')->nullable();
            $table->timestamps();
        });
    }

    public function test_coordinator_can_view_read_only_report_analytics_but_not_admin_tools(): void
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

        $response = $this->actingAs($coordinator)
            ->get(route('admin.dashboard'))
            ->assertOk();

        $response
            ->assertViewHas('totalReports', 3)
            ->assertViewHas('reportCountsByStatus', fn ($counts) => (int) $counts->get('Pending') === 1
                && (int) $counts->get('Resolved') === 2)
            ->assertSee('GAD Report Analytics')
            ->assertSee('Total Reports')
            ->assertSee('Harassment')
            ->assertDontSee('PRIVATE-CASE-1')
            ->assertDontSee('User Management')
            ->assertDontSee('Appointments')
            ->assertDontSee('Participatory Suggestions');

        foreach (['admin.users', 'admin.appointments', 'admin.suggestions', 'admin.reports'] as $routeName) {
            $this->get(route($routeName))->assertForbidden();
        }
    }

    public function test_gad_coordinator_is_excluded_from_admin_access_gate(): void
    {
        $coordinator = new User(['role' => 'admin', 'account_type' => 'coordinator']);
        $administrator = new User(['role' => 'admin', 'account_type' => 'admin']);

        $this->assertFalse(Gate::forUser($coordinator)->allows('admin-access'));
        $this->assertTrue(Gate::forUser($coordinator)->allows('gad-analytics-access'));
        $this->assertTrue(Gate::forUser($administrator)->allows('admin-access'));
    }
}
