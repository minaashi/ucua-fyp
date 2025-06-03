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
        // Drop the existing notifications table (old structure)
        Schema::dropIfExists('notifications');

        // Create the proper Laravel notifications table structure
        Schema::create('notifications', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('type');
            $table->morphs('notifiable');
            $table->text('data');
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
        });

        // Note: We don't restore old data as it has a different structure
        // The old notifications were user-specific, new ones are for departments
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop the Laravel notifications table
        Schema::dropIfExists('notifications');

        // Recreate the old structure (for rollback purposes)
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->text('message');
            $table->boolean('is_read')->default(false);
            $table->timestamps();
        });
    }
};
