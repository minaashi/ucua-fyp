<?php

namespace Tests\Feature;

use App\Mail\WarningLetterMail;
use App\Models\User;
use App\Models\Warning;
use App\Models\Report;
use App\Models\Department;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class WarningLetterEmailTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;
    protected $user;
    protected $department;
    protected $report;
    protected $warning;

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

        // Create test regular user (violator)
        $this->user = User::create([
            'name' => 'Test User',
            'email' => 'user@test.com',
            'password' => bcrypt('password'),
            'is_admin' => false,
            'is_ucua_officer' => false,
            'department_id' => $this->department->id,
            'worker_id' => 'USR001'
        ]);

        // Create test report
        $this->report = Report::create([
            'user_id' => $this->user->id,
            'employee_id' => $this->user->worker_id,
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

        // Create test warning
        $this->warning = Warning::create([
            'report_id' => $this->report->id,
            'suggested_by' => $this->admin->id,
            'type' => 'moderate',
            'reason' => 'Safety equipment violation',
            'suggested_action' => 'Attend safety training and ensure proper equipment usage',
            'status' => 'approved',
            'approved_by' => $this->admin->id,
            'approved_at' => now(),
            'recipient_id' => $this->user->id
        ]);
    }

    /** @test */
    public function warning_letter_email_can_be_created_without_pdf_attachment()
    {
        // Create the mailable
        $mailable = new WarningLetterMail($this->warning, $this->user, []);

        // Assert that no attachments are present
        $attachments = $mailable->attachments();
        $this->assertEmpty($attachments, 'Warning letter email should not have any attachments');
    }

    /** @test */
    public function warning_letter_email_contains_comprehensive_content()
    {
        Mail::fake();

        // Create the mailable
        $mailable = new WarningLetterMail($this->warning, $this->user, []);

        // Get the email content
        $content = $mailable->content();

        // Assert the view is correct
        $this->assertEquals('emails.warning-letter', $content->view);

        // Assert required data is passed to the view
        $viewData = $content->with;
        $this->assertArrayHasKey('warning', $viewData);
        $this->assertArrayHasKey('recipient', $viewData);
        $this->assertArrayHasKey('report', $viewData);
        $this->assertEquals($this->warning->id, $viewData['warning']->id);
        $this->assertEquals($this->user->id, $viewData['recipient']->id);
        $this->assertEquals($this->report->id, $viewData['report']->id);
    }

    /** @test */
    public function warning_letter_email_has_correct_subject()
    {
        // Create the mailable
        $mailable = new WarningLetterMail($this->warning, $this->user, []);

        // Get the envelope
        $envelope = $mailable->envelope();
        
        // Assert subject contains warning ID and report ID
        $expectedSubject = "Safety Warning Letter {$this->warning->formatted_id} - Report #{$this->report->id}";
        $this->assertEquals($expectedSubject, $envelope->subject);
    }

    /** @test */
    public function warning_letter_email_includes_cc_recipients()
    {
        $ccRecipients = [
            ['email' => 'supervisor@test.com', 'name' => 'Test Supervisor'],
            'hod@test.com'
        ];

        // Create the mailable with CC recipients
        $mailable = new WarningLetterMail($this->warning, $this->user, $ccRecipients);

        // Get the envelope
        $envelope = $mailable->envelope();
        
        // Assert CC recipients are included
        $this->assertNotEmpty($envelope->cc);
        $this->assertContains('supervisor@test.com', $envelope->cc);
        $this->assertContains('hod@test.com', $envelope->cc);
    }

    /** @test */
    public function warning_letter_email_handles_failed_delivery()
    {
        // Create the mailable
        $mailable = new WarningLetterMail($this->warning, $this->user, []);

        // Simulate a failed delivery
        $exception = new \Exception('Email delivery failed');
        $mailable->failed($exception);

        // Assert warning status was updated
        $this->warning->refresh();
        $this->assertEquals('failed', $this->warning->email_delivery_status);
    }

    /** @test */
    public function default_warning_message_does_not_reference_attachments()
    {
        // Create the mailable
        $mailable = new WarningLetterMail($this->warning, $this->user, []);

        // Use reflection to access private method
        $reflection = new \ReflectionClass($mailable);
        $method = $reflection->getMethod('getDefaultWarningMessage');
        $method->setAccessible(true);
        
        $defaultMessage = $method->invoke($mailable);
        
        // Assert the message doesn't reference attachments
        $this->assertStringNotContainsString('attached', $defaultMessage);
        $this->assertStringNotContainsString('attachment', $defaultMessage);
        $this->assertStringContainsString('warning details below', $defaultMessage);
    }
}
