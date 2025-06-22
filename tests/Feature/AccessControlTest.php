<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Report;
use App\Models\Department;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Spatie\Permission\Models\Role;

class AccessControlTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create roles
        Role::create(['name' => 'admin', 'guard_name' => 'web']);
        Role::create(['name' => 'ucua_officer', 'guard_name' => 'web']);
        Role::create(['name' => 'department_head', 'guard_name' => 'web']);
        Role::create(['name' => 'port_worker', 'guard_name' => 'web']);
    }

    /** @test */
    public function regular_user_cannot_access_admin_pages()
    {
        $user = User::factory()->create();
        $user->assignRole('port_worker');

        $this->actingAs($user)
            ->get('/admin/dashboard')
            ->assertStatus(403);

        $this->actingAs($user)
            ->get('/admin/users')
            ->assertStatus(403);
    }

    /** @test */
    public function regular_user_cannot_access_ucua_pages()
    {
        $user = User::factory()->create();
        $user->assignRole('port_worker');

        $this->actingAs($user)
            ->get('/ucua/dashboard')
            ->assertStatus(403);

        $this->actingAs($user)
            ->get('/ucua/assign-departments')
            ->assertStatus(403);
    }

    /** @test */
    public function regular_user_cannot_access_hod_pages()
    {
        $user = User::factory()->create();
        $user->assignRole('port_worker');

        $this->actingAs($user)
            ->get('/hod/dashboard')
            ->assertStatus(403);

        $this->actingAs($user)
            ->get('/hod/pending-reports')
            ->assertStatus(403);
    }

    /** @test */
    public function regular_user_can_only_view_own_reports()
    {
        $department = Department::factory()->create();
        
        $user1 = User::factory()->create(['department_id' => $department->id]);
        $user1->assignRole('port_worker');
        
        $user2 = User::factory()->create(['department_id' => $department->id]);
        $user2->assignRole('port_worker');

        $report1 = Report::factory()->create(['user_id' => $user1->id]);
        $report2 = Report::factory()->create(['user_id' => $user2->id]);

        // User1 can view their own report
        $this->actingAs($user1)
            ->get("/reports/{$report1->id}/details")
            ->assertStatus(200);

        // User1 cannot view user2's report
        $this->actingAs($user1)
            ->get("/reports/{$report2->id}/details")
            ->assertStatus(403);
    }

    /** @test */
    public function hod_can_only_view_own_department_reports()
    {
        $department1 = Department::factory()->create();
        $department2 = Department::factory()->create();
        
        $hod1 = User::factory()->create(['department_id' => $department1->id]);
        $hod1->assignRole('department_head');
        
        $hod2 = User::factory()->create(['department_id' => $department2->id]);
        $hod2->assignRole('department_head');

        $report1 = Report::factory()->create(['handling_department_id' => $department1->id]);
        $report2 = Report::factory()->create(['handling_department_id' => $department2->id]);

        // HOD1 can view reports assigned to their department
        $this->actingAs($hod1)
            ->get("/hod/report/{$report1->id}")
            ->assertStatus(200);

        // HOD1 cannot view reports assigned to other departments
        $this->actingAs($hod1)
            ->get("/hod/report/{$report2->id}")
            ->assertStatus(403);
    }

    /** @test */
    public function admin_can_access_all_areas()
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $admin->assignRole('admin');

        $this->actingAs($admin)
            ->get('/admin/dashboard')
            ->assertStatus(200);

        $this->actingAs($admin)
            ->get('/admin/users')
            ->assertStatus(200);

        $this->actingAs($admin)
            ->get('/admin/reports')
            ->assertStatus(200);
    }

    /** @test */
    public function ucua_officer_can_access_ucua_areas()
    {
        $ucua = User::factory()->create();
        $ucua->assignRole('ucua_officer');

        $this->actingAs($ucua)
            ->get('/ucua/dashboard')
            ->assertStatus(200);

        $this->actingAs($ucua)
            ->get('/ucua/assign-departments')
            ->assertStatus(200);
    }

    /** @test */
    public function ucua_officer_cannot_access_admin_areas()
    {
        $ucua = User::factory()->create();
        $ucua->assignRole('ucua_officer');

        $this->actingAs($ucua)
            ->get('/admin/dashboard')
            ->assertStatus(403);

        $this->actingAs($ucua)
            ->get('/admin/users')
            ->assertStatus(403);
    }

    /** @test */
    public function department_head_can_access_hod_areas()
    {
        $department = Department::factory()->create();
        $hod = User::factory()->create(['department_id' => $department->id]);
        $hod->assignRole('department_head');

        $this->actingAs($hod)
            ->get('/hod/dashboard')
            ->assertStatus(200);

        $this->actingAs($hod)
            ->get('/hod/pending-reports')
            ->assertStatus(200);
    }

    /** @test */
    public function department_head_cannot_access_admin_or_ucua_areas()
    {
        $department = Department::factory()->create();
        $hod = User::factory()->create(['department_id' => $department->id]);
        $hod->assignRole('department_head');

        $this->actingAs($hod)
            ->get('/admin/dashboard')
            ->assertStatus(403);

        $this->actingAs($hod)
            ->get('/ucua/dashboard')
            ->assertStatus(403);
    }

    /** @test */
    public function users_can_only_update_own_profile()
    {
        $user1 = User::factory()->create();
        $user1->assignRole('port_worker');
        
        $user2 = User::factory()->create();
        $user2->assignRole('port_worker');

        // User1 can update their own profile
        $this->actingAs($user1)
            ->post('/profile/update', [
                'name' => 'Updated Name',
                'email' => $user1->email,
            ])
            ->assertRedirect();

        // User1 cannot update user2's profile through admin routes
        $this->actingAs($user1)
            ->put("/admin/users/{$user2->id}", [
                'name' => 'Hacked Name',
                'email' => $user2->email,
                'role' => 'port_worker'
            ])
            ->assertStatus(403);
    }
}
