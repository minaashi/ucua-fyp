<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run()
    {
        // Create roles if not exists
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $workerRole = Role::firstOrCreate(['name' => 'port_worker']);

        // Create permissions
        $createReportPermission = Permission::firstOrCreate(['name' => 'create_report']);
        $viewReportPermission = Permission::firstOrCreate(['name' => 'view_report']);

        // Assign permissions to roles if not already assigned
        if (!$adminRole->hasPermissionTo($createReportPermission)) {
            $adminRole->givePermissionTo($createReportPermission);
        }

        if (!$adminRole->hasPermissionTo($viewReportPermission)) {
            $adminRole->givePermissionTo($viewReportPermission);
        }

        if (!$workerRole->hasPermissionTo($viewReportPermission)) {
            $workerRole->givePermissionTo($viewReportPermission);
        }
    }
}
