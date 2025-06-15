<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Populate worker_id_identifier for existing departments
        $this->populateWorkerIdIdentifiers();
    }

    /**
     * Populate worker_id_identifier for existing departments
     */
    private function populateWorkerIdIdentifiers(): void
    {
        $departmentMappings = [
            // Exact matches first
            'UCUA Department' => 'UCUA',
            'Port Security Department (PSD)' => 'PSD',
            'Operations Department' => 'OPS',
            'Maintenance Department' => 'MNT',
            'Safety Department' => 'SAF',
            'Security Department' => 'SEC',

            // Partial matches for variations
            'PSD' => 'PSD',
            'Operations' => 'OPS',
            'Maintenance' => 'MNT',
            'Safety' => 'SAF',
            'Security' => 'SEC',
            'SS Department' => 'SS',
        ];

        foreach ($departmentMappings as $departmentName => $identifier) {
            // Try exact match first
            $updated = DB::table('departments')
                ->where('name', $departmentName)
                ->whereNull('worker_id_identifier')
                ->update(['worker_id_identifier' => $identifier]);

            // If no exact match, try partial match
            if ($updated === 0) {
                DB::table('departments')
                    ->where('name', 'LIKE', "%{$departmentName}%")
                    ->whereNull('worker_id_identifier')
                    ->update(['worker_id_identifier' => $identifier]);
            }
        }

        // Log the results
        $departments = DB::table('departments')->select('id', 'name', 'worker_id_identifier')->get();
        foreach ($departments as $dept) {
            echo "Department ID {$dept->id}: {$dept->name} -> {$dept->worker_id_identifier}\n";
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Reset worker_id_identifier to null
        DB::table('departments')->update(['worker_id_identifier' => null]);
    }
};
