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
        Schema::create('warning_templates', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('violation_type', ['unsafe_act', 'unsafe_condition', 'general']);
            $table->enum('warning_level', ['minor', 'moderate', 'severe']);
            $table->string('subject_template');
            $table->text('body_template');
            $table->boolean('is_active')->default(true);
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->integer('version')->default(1);
            $table->timestamps();

            $table->index(['violation_type', 'warning_level']);
            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('warning_templates');
    }
};
