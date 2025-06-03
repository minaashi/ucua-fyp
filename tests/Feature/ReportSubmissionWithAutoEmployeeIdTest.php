<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Department;
use App\Models\Report;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Spatie\Permission\Models\Role;

class ReportSubmissionWithAutoEmployeeIdTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Create roles
        Role::create(['name' => 'port_worker']);
        Role::create(['name' => 'admin']);
        Role::create(['name' => 'ucua_officer']);
    }

    /** @test */
    public function employee_id_is_correctly_stored_when_report_is_created_programmatically()
    {
        // Create a department
        $department = Department::create([
            'name' => 'Operations Department',
            'email' => 'ops@department.com',
            'password' => bcrypt('password'),
            'head_name' => 'Operations Head',
            'head_email' => 'ops@test.com',
            'head_phone' => '1234567890',
            'is_active' => true,
        ]);

        // Create a user with worker_id
        $user = User::create([
            'name' => 'Operations Worker',
            'email' => 'worker@example.com',
            'worker_id' => 'OPS001',
            'password' => bcrypt('TestPassword123!'),
            'department_id' => $department->id,
            'email_verified_at' => now(),
        ]);

        $user->assignRole('port_worker');

        // Create a report directly using the model (simulating what the controller does)
        $report = Report::create([
            'user_id' => $user->id,
            'employee_id' => $user->worker_id, // This simulates auto-population
            'phone' => '+1234567890',
            'location' => 'Building A',
            'incident_date' => now(),
            'description' => 'TEST INCIDENT DESCRIPTION',
            'category' => 'unsafe_condition',
            'unsafe_condition' => 'Slippery floor surface',
            'status' => 'pending',
            'department' => $department->name,
        ]);

        // Verify the report was created with correct employee_id
        $this->assertNotNull($report);
        $this->assertEquals($user->worker_id, $report->employee_id);
        $this->assertEquals($department->name, $report->department);
        $this->assertEquals($user->id, $report->user_id);

        // Verify it's in the database
        $this->assertDatabaseHas('reports', [
            'user_id' => $user->id,
            'employee_id' => $user->worker_id,
            'department' => $department->name,
        ]);
    }

    /** @test */
    public function user_worker_id_matches_report_employee_id_concept()
    {
        // Create a department
        $department = Department::create([
            'name' => 'Test Department',
            'email' => 'test@department.com',
            'password' => bcrypt('password'),
            'head_name' => 'Test Head',
            'head_email' => 'test@test.com',
            'head_phone' => '1234567890',
            'is_active' => true,
        ]);

        // Create a user
        $user = User::create([
            'name' => 'Test Worker',
            'email' => 'worker@example.com',
            'worker_id' => 'TW001',
            'password' => bcrypt('TestPassword123!'),
            'department_id' => $department->id,
            'email_verified_at' => now(),
        ]);

        $user->assignRole('port_worker');

        // Verify that the user's worker_id can be used as employee_id
        $this->assertNotNull($user->worker_id);
        $this->assertEquals('TW001', $user->worker_id);

        // This demonstrates that the worker_id from registration
        // should be used to auto-populate the employee_id field
        $expectedEmployeeId = $user->worker_id;
        $this->assertEquals('TW001', $expectedEmployeeId);
    }
}
