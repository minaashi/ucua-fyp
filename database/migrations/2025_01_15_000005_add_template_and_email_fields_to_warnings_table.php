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
            $table->foreignId('template_id')->nullable()->constrained('warning_templates')->onDelete('set null')->after('warning_message');
            $table->timestamp('email_sent_at')->nullable()->after('template_id');
            $table->enum('email_delivery_status', ['pending', 'sent', 'failed', 'bounced'])->default('pending')->after('email_sent_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('warnings', function (Blueprint $table) {
            $table->dropForeign(['template_id']);
            $table->dropColumn(['template_id', 'email_sent_at', 'email_delivery_status']);
        });
    }
};
