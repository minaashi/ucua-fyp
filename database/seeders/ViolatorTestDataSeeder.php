<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Report;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class ViolatorTestDataSeeder extends Seeder
{
    /**
     * Run the database seeds to add violator information to existing reports.
     */
    public function run(): void
    {
        $this->command->info('🌱 Adding violator information to existing reports...');

        // Get some existing users to use as violators
        $users = User::whereHas('roles', function($query) {
            $query->where('name', 'port_worker');
        })->get();

        if ($users->isEmpty()) {
            $this->command->warn('No port workers found. Creating some test violators...');
            $this->createTestViolators();
            $users = User::whereHas('roles', function($query) {
                $query->where('name', 'port_worker');
            })->get();
        }

        // Update existing reports with violator information
        $reports = Report::whereNull('violator_employee_id')->limit(10)->get();

        foreach ($reports as $index => $report) {
            if ($index % 3 === 0) {
                // Self-reported violation (violator is the reporter)
                $report->update([
                    'violator_employee_id' => $report->user->worker_id ?? $report->employee_id,
                    'violator_name' => $report->user->name ?? 'Self-Reported',
                    'violator_department' => $report->department
                ]);
            } elseif ($index % 3 === 1 && $users->isNotEmpty()) {
                // Another employee violation
                $violator = $users->random();
                $report->update([
                    'violator_employee_id' => $violator->worker_id,
                    'violator_name' => $violator->name,
                    'violator_department' => $violator->department->name ?? 'Operations'
                ]);
            } else {
                // External violator (contractor, visitor)
                $contractorNames = [
                    'John Contractor',
                    'Sarah External',
                    'Mike Visitor',
                    'Lisa Vendor',
                    'David Supplier'
                ];
                
                $contractorDepts = [
                    'External Contractor',
                    'Maintenance Contractor',
                    'Security Contractor',
                    'Cleaning Services',
                    'Visitor'
                ];

                $report->update([
                    'violator_employee_id' => 'EXT' . str_pad($index + 1, 3, '0', STR_PAD_LEFT),
                    'violator_name' => $contractorNames[array_rand($contractorNames)],
                    'violator_department' => $contractorDepts[array_rand($contractorDepts)]
                ]);
            }
        }

        $this->command->info('✅ Violator information added to ' . $reports->count() . ' reports');
    }

    private function createTestViolators()
    {
        $testUsers = [
            [
                'name' => 'Test Violator 1',
                'email' => 'violator1@test.com',
                'password' => bcrypt('password'),
                'worker_id' => 'VIO001',
                'email_verified_at' => now()
            ],
            [
                'name' => 'Test Violator 2', 
                'email' => 'violator2@test.com',
                'password' => bcrypt('password'),
                'worker_id' => 'VIO002',
                'email_verified_at' => now()
            ]
        ];

        foreach ($testUsers as $userData) {
            $user = User::create($userData);
            $user->assignRole('port_worker');
        }
    }
}
