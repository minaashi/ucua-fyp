<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Department;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Spatie\Permission\Models\Role;

class ReportEmployeeIdAutoPopulationTest extends TestCase
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
    public function employee_id_is_auto_populated_from_user_worker_id_in_report_form()
    {
        // Create a department
        $department = Department::create([
            'name' => 'Test Department',
            'email' => 'test@department.com',
            'password' => bcrypt('password'),
            'head_name' => 'Test Head',
            'head_email' => 'head@test.com',
            'head_phone' => '1234567890',
            'is_active' => true,
        ]);

        // Create a user with worker_id
        $user = User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'worker_id' => 'TU001',
            'password' => bcrypt('TestPassword123!'),
            'department_id' => $department->id,
            'email_verified_at' => now(),
        ]);

        $user->assignRole('port_worker');

        // Act as the user and visit the report creation page
        $response = $this->actingAs($user)
            ->get(route('reports.create'));

        // Assert the response is successful
        $response->assertStatus(200);

        // Assert that the employee_id field contains the user's worker_id
        $response->assertSee('value="' . $user->worker_id . '"', false);
        
        // Assert that the field is readonly
        $response->assertSee('readonly', false);
        
        // Assert that the auto-population message is shown
        $response->assertSee('Auto-populated from your registration');
    }

    /** @test */
    public function employee_id_field_handles_empty_worker_id_gracefully()
    {
        // Create a department
        $department = Department::create([
            'name' => 'Test Department',
            'email' => 'test@department.com',
            'password' => bcrypt('password'),
            'head_name' => 'Test Head',
            'head_email' => 'head@test.com',
            'head_phone' => '1234567890',
            'is_active' => true,
        ]);

        // Create a user without worker_id
        $user = User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'worker_id' => null, // No worker_id
            'password' => bcrypt('TestPassword123!'),
            'department_id' => $department->id,
            'email_verified_at' => now(),
        ]);

        $user->assignRole('port_worker');

        // Act as the user and visit the report creation page
        $response = $this->actingAs($user)
            ->get(route('reports.create'));

        // Assert the response is successful
        $response->assertStatus(200);

        // Assert that the employee_id field is empty but still readonly
        $response->assertSee('value=""', false);
        $response->assertSee('readonly', false);
    }

    /** @test */
    public function department_is_still_auto_populated_correctly()
    {
        // Create a department
        $department = Department::create([
            'name' => 'Security Department',
            'email' => 'security@department.com',
            'password' => bcrypt('password'),
            'head_name' => 'Security Head',
            'head_email' => 'security@test.com',
            'head_phone' => '1234567890',
            'is_active' => true,
        ]);

        // Create a user
        $user = User::create([
            'name' => 'Security User',
            'email' => 'security@example.com',
            'worker_id' => 'SEC001',
            'password' => bcrypt('TestPassword123!'),
            'department_id' => $department->id,
            'email_verified_at' => now(),
        ]);

        $user->assignRole('port_worker');

        // Act as the user and visit the report creation page
        $response = $this->actingAs($user)
            ->get(route('reports.create'));

        // Assert the response is successful
        $response->assertStatus(200);

        // Assert that both employee_id and department are auto-populated
        $response->assertSee('value="' . $user->worker_id . '"', false);
        $response->assertSee('value="' . $department->name . '"', false);
    }
}
