<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Report;
use App\Models\Warning;
use App\Models\Department;
use Spatie\Permission\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;

class AdminWarningApprovalTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $admin;
    protected $ucuaOfficer;
    protected $department;
    protected $report;
    protected $warning;

    protected function setUp(): void
    {
        parent::setUp();

        // Create roles first
        Role::create(['name' => 'admin', 'guard_name' => 'web']);
        Role::create(['name' => 'ucua_officer', 'guard_name' => 'web']);
        Role::create(['name' => 'port_worker', 'guard_name' => 'web']);

        // Create admin user
        $this->admin = User::factory()->create([
            'email' => 'admin@test.com',
            'name' => 'Test Admin'
        ]);
        $this->admin->assignRole('admin');

        // Create UCUA officer
        $this->ucuaOfficer = User::factory()->create([
            'email' => 'ucua@test.com',
            'name' => 'Test UCUA Officer'
        ]);
        $this->ucuaOfficer->assignRole('ucua_officer');

        // Create department
        $this->department = Department::factory()->create([
            'name' => 'Test Department'
        ]);

        // Create report with violator identified
        $this->report = Report::create([
            'user_id' => $this->ucuaOfficer->id,
            'employee_id' => 'EMP001',
            'department' => 'Operations',
            'phone' => '123-456-7890',
            'unsafe_condition' => 'Poor Lighting',
            'unsafe_act' => 'Not Wearing PPE',
            'location' => 'Dock A',
            'incident_date' => now(),
            'description' => 'Test safety violation',
            'status' => 'pending',
            'category' => 'unsafe_act',
            'is_anonymous' => false,
            'violator_employee_id' => 'EMP001',
            'violator_name' => 'John Doe',
            'violator_department' => 'Operations',
            'handling_department_id' => $this->department->id
        ]);

        // Create warning suggestion
        $this->warning = Warning::create([
            'report_id' => $this->report->id,
            'suggested_by' => $this->ucuaOfficer->id,
            'type' => 'minor',
            'reason' => 'Safety violation observed',
            'suggested_action' => 'Attend safety training',
            'status' => 'pending'
        ]);
    }

    /** @test */
    public function admin_can_view_warning_details_with_violator_information()
    {
        $response = $this->actingAs($this->admin)
            ->getJson("/admin/warnings/{$this->warning->id}/details");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'violator' => [
                    'name' => 'John Doe',
                    'employee_id' => 'EMP001',
                    'department' => 'Operations',
                    'is_system_user' => false
                ]
            ]);

        // Check that HTML contains violator information
        $this->assertStringContainsString('John Doe', $response->json('html'));
        $this->assertStringContainsString('EMP001', $response->json('html'));
        $this->assertStringContainsString('Operations', $response->json('html'));
    }

    /** @test */
    public function admin_cannot_approve_warning_without_violator_identification()
    {
        // Create report without violator identification
        $reportWithoutViolator = Report::create([
            'user_id' => $this->ucuaOfficer->id,
            'employee_id' => 'EMP002',
            'department' => 'Operations',
            'phone' => '123-456-7890',
            'unsafe_condition' => 'Poor Lighting',
            'unsafe_act' => 'Not Wearing PPE',
            'location' => 'Dock A',
            'incident_date' => now(),
            'description' => 'Test safety violation',
            'status' => 'pending',
            'category' => 'unsafe_act',
            'is_anonymous' => false,
            'violator_employee_id' => null,
            'violator_name' => null,
            'violator_department' => null
        ]);

        $warningWithoutViolator = Warning::create([
            'report_id' => $reportWithoutViolator->id,
            'suggested_by' => $this->ucuaOfficer->id,
            'type' => 'minor',
            'reason' => 'Safety violation observed',
            'suggested_action' => 'Attend safety training',
            'status' => 'pending'
        ]);

        $response = $this->actingAs($this->admin)
            ->post("/admin/warnings/{$warningWithoutViolator->id}/approve", [
                'warning_message' => 'Test warning message',
                'admin_notes' => 'Test notes'
            ]);

        $response->assertRedirect()
            ->assertSessionHas('error', 'Cannot approve warning: Violator has not been identified yet. Investigation is required to identify the person involved.');

        // Verify warning status hasn't changed
        $this->assertEquals('pending', $warningWithoutViolator->fresh()->status);
    }

    /** @test */
    public function admin_can_approve_warning_with_identified_violator()
    {
        $response = $this->actingAs($this->admin)
            ->post("/admin/warnings/{$this->warning->id}/approve", [
                'warning_message' => 'Official warning for safety violation',
                'admin_notes' => 'Approved after review'
            ]);

        $response->assertRedirect()
            ->assertSessionHas('success', 'Warning suggestion approved successfully.');

        // Verify warning was approved
        $this->warning->refresh();
        $this->assertEquals('approved', $this->warning->status);
        $this->assertEquals($this->admin->id, $this->warning->approved_by);
        $this->assertEquals('Official warning for safety violation', $this->warning->warning_message);
        $this->assertEquals('Approved after review', $this->warning->admin_notes);
    }

    /** @test */
    public function warning_details_response_includes_violator_null_when_not_identified()
    {
        // Create report without violator identification
        $reportWithoutViolator = Report::create([
            'user_id' => $this->ucuaOfficer->id,
            'employee_id' => 'EMP003',
            'department' => 'Operations',
            'phone' => '123-456-7890',
            'unsafe_condition' => 'Poor Lighting',
            'unsafe_act' => 'Not Wearing PPE',
            'location' => 'Dock A',
            'incident_date' => now(),
            'description' => 'Test safety violation',
            'status' => 'pending',
            'category' => 'unsafe_act',
            'is_anonymous' => false,
            'violator_employee_id' => null,
            'violator_name' => null,
            'violator_department' => null
        ]);

        $warningWithoutViolator = Warning::create([
            'report_id' => $reportWithoutViolator->id,
            'suggested_by' => $this->ucuaOfficer->id,
            'type' => 'minor',
            'reason' => 'Safety violation observed',
            'suggested_action' => 'Attend safety training',
            'status' => 'pending'
        ]);

        $response = $this->actingAs($this->admin)
            ->getJson("/admin/warnings/{$warningWithoutViolator->id}/details");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'violator' => null
            ]);

        // Check that HTML contains "not identified" message
        $this->assertStringContainsString('Violator Not Identified', $response->json('html'));
        $this->assertStringContainsString('Investigation Required', $response->json('html'));
    }
}
