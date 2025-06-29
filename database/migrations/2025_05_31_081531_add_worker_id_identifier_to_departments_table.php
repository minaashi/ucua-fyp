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
        Schema::table('departments', function (Blueprint $table) {
            $table->string('worker_id_identifier')->nullable()->after('name');
        });

        // Populate worker_id_identifier for existing departments
        $this->populateWorkerIdIdentifiers();
    }

    /**
     * Populate worker_id_identifier for existing departments
     */
    private function populateWorkerIdIdentifiers(): void
    {
        $departmentMappings = [
            'UCUA Department' => 'UCUA',
            'Port Security Department (PSD)' => 'PSD',
            'PSD' => 'PSD',
            'Operations Department' => 'OPS',
            'Operations' => 'OPS',
            'Maintenance Department' => 'MNT',
            'Maintenance' => 'MNT',
            'Safety Department' => 'SAF',
            'Safety' => 'SAF',
            'Security Department' => 'SEC',
            'Security' => 'SEC',
        ];

        foreach ($departmentMappings as $departmentName => $identifier) {
            DB::table('departments')
                ->where('name', 'LIKE', "%{$departmentName}%")
                ->whereNull('worker_id_identifier')
                ->update(['worker_id_identifier' => $identifier]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('departments', function (Blueprint $table) {
            $table->dropColumn('worker_id_identifier');
        });
    }
};
