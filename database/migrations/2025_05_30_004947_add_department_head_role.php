<?php

use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class AddDepartmentHeadRole extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Create department_head role
        $role = Role::create(['name' => 'department_head']);

        // Assign department_head role to existing HODs
        $hodEmails = [
            'hod.ict@ucua.edu.my',
            'hod.finance@ucua.edu.my',
            'hod.hr@ucua.edu.my'
        ];

        foreach ($hodEmails as $email) {
            $user = User::where('email', $email)->first();
            if ($user) {
                $user->assignRole('department_head');
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove department_head role from users
        $users = User::role('department_head')->get();
        foreach ($users as $user) {
            $user->removeRole('department_head');
        }

        // Delete the role
        Role::where('name', 'department_head')->delete();
    }
}
