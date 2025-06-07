<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Report;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('reports', function (Blueprint $table) {
            $table->string('formatted_id')->unique()->nullable()->after('id');
        });

        // Update existing reports with formatted IDs
        $this->updateExistingReports();
    }

    /**
     * Update existing reports with formatted IDs
     */
    private function updateExistingReports(): void
    {
        $reports = Report::orderBy('id')->get();

        foreach ($reports as $index => $report) {
            $formattedId = 'RPT-' . str_pad($index + 1, 3, '0', STR_PAD_LEFT);
            $report->update(['formatted_id' => $formattedId]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reports', function (Blueprint $table) {
            $table->dropColumn('formatted_id');
        });
    }
};
