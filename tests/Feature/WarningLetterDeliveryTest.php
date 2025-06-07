<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Warning;
use App\Models\Report;
use App\Models\Department;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WarningLetterDeliveryTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;
    protected $systemUser;
    protected $department;
    protected $report;

    protected function setUp(): void
    {
        parent::setUp();

        // Create test department
        $this->department = Department::create([
            'name' => 'Test Department',
            'email' => 'test@department.com',
            'password' => bcrypt('password123')
        ]);

        // Create test admin user
        $this->admin = User::create([
            'name' => 'Test Admin',
            'email' => 'admin@test.com',
            'password' => bcrypt('password'),
            'is_admin' => true,
            'is_ucua_officer' => false,
            'department_id' => $this->department->id,
            'worker_id' => 'ADM001'
        ]);

        // Assign admin role
        $this->admin->assignRole('admin');

        // Create test system user (violator with email)
        $this->systemUser = User::create([
            'name' => 'System User',
            'email' => 'system.user@test.com',
            'password' => bcrypt('password'),
            'is_admin' => false,
            'is_ucua_officer' => false,
            'department_id' => $this->department->id,
            'worker_id' => 'SYS001'
        ]);
    }

    /** @test */
    public function warning_can_be_sent_via_email_for_system_users()
    {
        // Create report with system user as violator
        $report = Report::create([
            'user_id' => $this->systemUser->id,
            'employee_id' => $this->systemUser->worker_id,
            'violator_employee_id' => $this->systemUser->worker_id,
            'violator_name' => $this->systemUser->name,
            'department' => $this->department->name,
            'phone' => '+1234567890',
            'category' => 'unsafe_act',
            'location' => 'Test Location',
            'description' => 'Test safety violation description',
            'unsafe_act' => 'Not wearing safety equipment',
            'incident_date' => now()->subDays(1),
            'deadline' => now()->addDays(7),
            'status' => 'review',
            'handling_department_id' => $this->department->id
        ]);

        // Create approved warning
        $warning = Warning::create([
            'report_id' => $report->id,
            'suggested_by' => $this->admin->id,
            'type' => 'moderate',
            'reason' => 'Safety equipment violation',
            'suggested_action' => 'Attend safety training',
            'status' => 'approved',
            'approved_by' => $this->admin->id,
            'approved_at' => now()
        ]);

        // Test that warning can be sent via email
        $this->assertTrue($warning->canBeSentViaEmail());
        $this->assertTrue($warning->isInternalViolator());
        $this->assertFalse($warning->isExternalViolator());
        $this->assertEquals('Ready to Send', $warning->getDeliveryStatus());
    }

    /** @test */
    public function warning_system_only_handles_internal_violators()
    {
        // Create report with external violator (no email)
        $report = Report::create([
            'user_id' => $this->systemUser->id,
            'employee_id' => $this->systemUser->worker_id,
            'violator_employee_id' => 'EXT001',
            'violator_name' => 'External Violator',
            'department' => $this->department->name,
            'phone' => '+1234567890',
            'category' => 'unsafe_act',
            'location' => 'Test Location',
            'description' => 'Test safety violation description',
            'unsafe_act' => 'Not wearing safety equipment',
            'incident_date' => now()->subDays(1),
            'deadline' => now()->addDays(7),
            'status' => 'review',
            'handling_department_id' => $this->department->id
        ]);

        // Create approved warning
        $warning = Warning::create([
            'report_id' => $report->id,
            'suggested_by' => $this->admin->id,
            'type' => 'moderate',
            'reason' => 'Safety equipment violation',
            'suggested_action' => 'Attend safety training',
            'status' => 'approved',
            'approved_by' => $this->admin->id,
            'approved_at' => now()
        ]);

        // Test that external violators are properly identified
        $this->assertFalse($warning->canBeSentViaEmail());
        $this->assertTrue($warning->isExternalViolator());
        $this->assertFalse($warning->isInternalViolator());
        $this->assertEquals('External - Manual Delivery', $warning->getDeliveryStatus());
    }

    /** @test */
    public function warning_shows_correct_status_when_violator_not_identified()
    {
        // Create report without violator identification
        $report = Report::create([
            'user_id' => $this->systemUser->id,
            'employee_id' => $this->systemUser->worker_id,
            'department' => $this->department->name,
            'phone' => '+1234567890',
            'category' => 'unsafe_act',
            'location' => 'Test Location',
            'description' => 'Test safety violation description',
            'unsafe_act' => 'Not wearing safety equipment',
            'incident_date' => now()->subDays(1),
            'deadline' => now()->addDays(7),
            'status' => 'review',
            'handling_department_id' => $this->department->id
        ]);

        // Create approved warning
        $warning = Warning::create([
            'report_id' => $report->id,
            'suggested_by' => $this->admin->id,
            'type' => 'moderate',
            'reason' => 'Safety equipment violation',
            'suggested_action' => 'Attend safety training',
            'status' => 'approved',
            'approved_by' => $this->admin->id,
            'approved_at' => now()
        ]);

        // Test that warning shows correct status
        $this->assertFalse($warning->canBeSentViaEmail());
        $this->assertEquals('Violator Not Identified', $warning->getDeliveryStatus());
    }

    /** @test */
    public function pending_warning_shows_correct_status()
    {
        $report = Report::create([
            'user_id' => $this->systemUser->id,
            'employee_id' => $this->systemUser->worker_id,
            'department' => $this->department->name,
            'phone' => '+1234567890',
            'category' => 'unsafe_act',
            'location' => 'Test Location',
            'description' => 'Test safety violation description',
            'unsafe_act' => 'Not wearing safety equipment',
            'incident_date' => now()->subDays(1),
            'deadline' => now()->addDays(7),
            'status' => 'review',
            'handling_department_id' => $this->department->id
        ]);

        // Create pending warning
        $warning = Warning::create([
            'report_id' => $report->id,
            'suggested_by' => $this->admin->id,
            'type' => 'moderate',
            'reason' => 'Safety equipment violation',
            'suggested_action' => 'Attend safety training',
            'status' => 'pending'
        ]);

        // Test that pending warning shows correct status
        $this->assertFalse($warning->canBeSentViaEmail());
        $this->assertEquals('Pending Approval', $warning->getDeliveryStatus());
    }

    /** @test */
    public function ucua_officer_cannot_suggest_warning_for_external_violators()
    {
        // Create UCUA officer
        $ucuaOfficer = User::create([
            'name' => 'UCUA Officer',
            'email' => 'ucua@test.com',
            'password' => bcrypt('password'),
            'is_admin' => false,
            'is_ucua_officer' => true,
            'department_id' => $this->department->id,
            'worker_id' => 'UCUA001'
        ]);

        // Assign UCUA officer role
        $ucuaOfficer->assignRole('ucua_officer');

        // Create report with external violator
        $report = Report::create([
            'user_id' => $this->systemUser->id,
            'employee_id' => $this->systemUser->worker_id,
            'violator_employee_id' => 'EXT001',
            'violator_name' => 'External Violator',
            'department' => $this->department->name,
            'phone' => '+1234567890',
            'category' => 'unsafe_act',
            'location' => 'Test Location',
            'description' => 'Test safety violation description',
            'unsafe_act' => 'Not wearing safety equipment',
            'incident_date' => now()->subDays(1),
            'deadline' => now()->addDays(7),
            'status' => 'review',
            'handling_department_id' => $this->department->id
        ]);

        // Try to suggest warning for external violator
        $response = $this->actingAs($ucuaOfficer)
            ->post(route('ucua.suggest-warning'), [
                'report_id' => $report->id,
                'warning_type' => 'moderate',
                'warning_reason' => 'Safety violation',
                'suggested_action' => 'Training required'
            ]);

        // Assert error response
        $response->assertRedirect();
        $response->assertSessionHas('error');
        $response->assertSessionHasErrors(false);

        // Assert no warning was created
        $this->assertEquals(0, Warning::where('report_id', $report->id)->count());
    }
}
