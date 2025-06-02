<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\DB;

class FixUcuaGuards extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ucua:fix-guards';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix UCUA officer guard assignments and ensure all roles use web guard';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔧 Fixing UCUA guard assignments...');

        // 1. Ensure all roles use web guard
        $this->fixRoleGuards();

        // 2. Fix any UCUA officers that might have wrong role assignments
        $this->fixUcuaOfficerRoles();

        // 3. Clean up any orphaned role assignments
        $this->cleanupOrphanedRoles();

        $this->info('✅ UCUA guard assignments fixed successfully!');
        
        return 0;
    }

    private function fixRoleGuards()
    {
        $this->info('📝 Fixing role guards...');

        // Ensure ucua_officer role exists with web guard
        $ucuaRole = Role::firstOrCreate([
            'name' => 'ucua_officer',
            'guard_name' => 'web'
        ]);

        // Remove any ucua_officer roles with ucua guard
        Role::where('name', 'ucua_officer')
            ->where('guard_name', 'ucua')
            ->delete();

        $this->info('   ✓ UCUA officer role fixed to use web guard');

        // Ensure other roles use web guard
        $roles = ['admin', 'port_worker', 'user', 'department_head'];
        foreach ($roles as $roleName) {
            Role::firstOrCreate([
                'name' => $roleName,
                'guard_name' => 'web'
            ]);
        }

        $this->info('   ✓ All roles ensured to use web guard');
    }

    private function fixUcuaOfficerRoles()
    {
        $this->info('👤 Fixing UCUA officer role assignments...');

        // Find all users with ucua_officer role
        $ucuaOfficers = User::whereHas('roles', function($query) {
            $query->where('name', 'ucua_officer');
        })->get();

        foreach ($ucuaOfficers as $officer) {
            // Remove all existing role assignments
            $officer->roles()->detach();
            
            // Re-assign ucua_officer role with web guard
            $officer->assignRole('ucua_officer');
            
            $this->info("   ✓ Fixed role assignment for: {$officer->name} ({$officer->email})");
        }

        $this->info("   ✓ Fixed {$ucuaOfficers->count()} UCUA officer(s)");
    }

    private function cleanupOrphanedRoles()
    {
        $this->info('🧹 Cleaning up orphaned role assignments...');

        // Remove any role assignments that reference non-existent roles
        DB::table('model_has_roles')
            ->whereNotExists(function ($query) {
                $query->select(DB::raw(1))
                      ->from('roles')
                      ->whereRaw('roles.id = model_has_roles.role_id');
            })
            ->delete();

        $this->info('   ✓ Orphaned role assignments cleaned up');
    }
}
