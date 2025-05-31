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
            $table->dropColumn(['unsafe_condition', 'other_unsafe_condition', 'unsafe_act', 'other_unsafe_act', 'other_location']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reports', function (Blueprint $table) {
            // We would typically re-add the columns here if rolling back
            // However, since we are replacing them with new tables,
            // re-adding them in the down method is not necessary for this specific refactor.
            // If you need to roll back completely, you would need to add the column definitions here.
        });
    }
};
