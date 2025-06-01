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
        Schema::table('warnings', function (Blueprint $table) {
            // Add workflow status for warning suggestions
            $table->enum('status', ['pending', 'approved', 'rejected', 'sent'])->default('pending')->after('suggested_action');

            // Add admin approval fields
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null')->after('status');
            $table->text('admin_notes')->nullable()->after('approved_by');

            // Add tracking fields
            $table->timestamp('approved_at')->nullable()->after('admin_notes');
            $table->timestamp('sent_at')->nullable()->after('approved_at');

            // Add recipient information
            $table->foreignId('recipient_id')->nullable()->constrained('users')->onDelete('set null')->after('sent_at');
            $table->text('warning_message')->nullable()->after('recipient_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('warnings', function (Blueprint $table) {
            $table->dropForeign(['approved_by']);
            $table->dropForeign(['recipient_id']);
            $table->dropColumn([
                'status',
                'approved_by',
                'admin_notes',
                'approved_at',
                'sent_at',
                'recipient_id',
                'warning_message'
            ]);
        });
    }
};
