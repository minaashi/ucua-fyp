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
            // Remove the old non_compliance_type column
            $table->dropColumn('non_compliance_type');

            // Add the separate unsafe condition and act columns
            $table->string('unsafe_condition')->nullable()->after('category');
            $table->text('other_unsafe_condition')->nullable()->after('unsafe_condition');
            $table->string('unsafe_act')->nullable()->after('other_unsafe_condition');
            $table->text('other_unsafe_act')->nullable()->after('unsafe_act');
            $table->text('other_location')->nullable()->after('location');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reports', function (Blueprint $table) {
            // Add back the non_compliance_type column
            $table->string('non_compliance_type')->after('phone');

            // Remove the separate columns
            $table->dropColumn([
                'unsafe_condition',
                'other_unsafe_condition',
                'unsafe_act',
                'other_unsafe_act',
                'other_location'
            ]);
        });
    }
};
