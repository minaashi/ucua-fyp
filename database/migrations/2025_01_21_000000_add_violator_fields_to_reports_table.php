<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('reports', function (Blueprint $table) {
            // Add violator identification fields
            $table->string('violator_employee_id')->nullable()->after('employee_id')
                ->comment('Employee ID of the person who committed the violation');
            $table->string('violator_name')->nullable()->after('violator_employee_id')
                ->comment('Name of violator (for non-system users like contractors, visitors)');
            $table->string('violator_department')->nullable()->after('violator_name')
                ->comment('Department of the violator (may differ from reporter department)');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reports', function (Blueprint $table) {
            $table->dropColumn([
                'violator_employee_id',
                'violator_name', 
                'violator_department'
            ]);
        });
    }
};
