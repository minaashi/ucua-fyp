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
        Schema::create('admin_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value');
            $table->enum('type', ['string', 'integer', 'boolean', 'json'])->default('string');
            $table->string('description')->nullable();
            $table->timestamps();
        });

        // Insert default settings
        DB::table('admin_settings')->insert([
            [
                'key' => 'auto_archive_days',
                'value' => '30',
                'type' => 'integer',
                'description' => 'Number of days after which resolved reports are automatically archived',
                'created_at' => now(),
                'updated_at' => now()
            ]
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admin_settings');
    }
};
