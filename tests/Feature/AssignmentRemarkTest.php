<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Report;
use App\Models\Department;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Spatie\Permission\Models\Role;

class AssignmentRemarkTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create roles
        Role::create(['name' => 'ucua_officer', 'guard_name' => 'web']);
        Role::create(['name' => 'port_worker', 'guard_name' => 'web']);
    }

    public function test_ucua_officer_can_assign_department_with_remark()
    {
        // Create a department first
        $department = Department::factory()->create([
            'name' => 'Test Department',
            'is_active' => true
        ]);

        // Create a UCUA officer with the existing department
        $ucuaOfficer = User::factory()->create([
            'department_id' => $department->id
        ]);
        $ucuaOfficer->assignRole('ucua_officer');

        // Create a user and report
        $user = User::factory()->create([
            'department_id' => $department->id
        ]);
        $user->assignRole('port_worker');

        $report = Report::factory()->create([
            'user_id' => $user->id,
            'status' => 'review'
        ]);

        // Act as UCUA officer and assign department with remark
        $response = $this->actingAs($ucuaOfficer)
            ->post(route('ucua.assign-department'), [
                'report_id' => $report->id,
                'department_id' => $department->id,
                'deadline' => now()->addDays(7)->format('Y-m-d'),
                'assignment_remark' => 'This is a test assignment remark for the department.'
            ]);

        // Assert the response
        $response->assertRedirect(route('ucua.dashboard'));
        $response->assertSessionHas('success');

        // Assert the database was updated
        $this->assertDatabaseHas('reports', [
            'id' => $report->id,
            'handling_department_id' => $department->id,
            'assignment_remark' => 'This is a test assignment remark for the department.',
            'status' => 'in_progress'
        ]);
    }

    public function test_assignment_remark_is_displayed_in_admin_view()
    {
        // Create a department first
        $department = Department::factory()->create();

        // Create an admin user
        $admin = User::factory()->create([
            'department_id' => $department->id
        ]);
        Role::create(['name' => 'admin', 'guard_name' => 'web']);
        $admin->assignRole('admin');

        // Create a report with assignment remark
        $report = Report::factory()->create([
            'assignment_remark' => 'This is a test assignment remark.'
        ]);

        // Act as admin and view the report
        $response = $this->actingAs($admin)
            ->get(route('admin.reports.show', $report));

        // Assert the assignment remark is displayed
        $response->assertSee('Assignment Remark');
        $response->assertSee('This is a test assignment remark.');
    }
}
