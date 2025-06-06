<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Spatie\Permission\Models\Role;

class FixPortWorkerRoles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fix:port-worker-roles';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Assign port_worker role to existing users who don\'t have any role assigned';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔧 Fixing Port Worker Role Assignments...');
        $this->info('=====================================');

        // Ensure port_worker role exists
        $portWorkerRole = Role::firstOrCreate(['name' => 'port_worker']);
        $this->info("✅ Port worker role ensured: {$portWorkerRole->name}");

        // Find users without any roles (excluding admin users)
        $usersWithoutRoles = User::whereDoesntHave('roles')
            ->where('is_admin', '!=', 1)
            ->orWhere('is_admin', null)
            ->get();

        $this->info("🔍 Found {$usersWithoutRoles->count()} users without roles");

        if ($usersWithoutRoles->count() === 0) {
            $this->info('✅ All users already have roles assigned!');
            return 0;
        }

        $this->info('📋 Users to be assigned port_worker role:');
        
        $bar = $this->output->createProgressBar($usersWithoutRoles->count());
        $bar->start();

        $assignedCount = 0;
        foreach ($usersWithoutRoles as $user) {
            // Skip if user is already admin or has specific roles
            if ($user->is_admin || $user->hasAnyRole(['admin', 'ucua_officer', 'department_head'])) {
                $bar->advance();
                continue;
            }

            // Assign port_worker role
            $user->assignRole('port_worker');
            $assignedCount++;
            
            $this->newLine();
            $this->info("  ✅ {$user->name} ({$user->email}) → port_worker");
            
            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);

        $this->info("🎉 Successfully assigned port_worker role to {$assignedCount} users!");
        
        // Show updated statistics
        $this->info('📊 Updated Statistics:');
        $totalUsers = User::count();
        $adminUsers = User::role('admin')->count();
        $portWorkers = User::role('port_worker')->count();
        $ucuaOfficers = User::role('ucua_officer')->count();
        
        $this->table(
            ['Role', 'Count'],
            [
                ['Total Users', $totalUsers],
                ['Admin Users', $adminUsers],
                ['Port Workers', $portWorkers],
                ['UCUA Officers', $ucuaOfficers],
            ]
        );

        $this->info('✅ Port worker role assignment completed!');
        $this->info('💡 The admin dashboard should now show the correct port worker count.');
        
        return 0;
    }
}
