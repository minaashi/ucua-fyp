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
            $table->string('unsafe_condition')->nullable();
            $table->text('other_unsafe_condition')->nullable();
            $table->string('unsafe_act')->nullable();
            $table->text('other_unsafe_act')->nullable();
            $table->text('other_location')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reports', function (Blueprint $table) {
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
