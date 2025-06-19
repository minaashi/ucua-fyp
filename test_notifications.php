<?php

require_once 'vendor/autoload.php';

use App\Models\Department;
use App\Models\Report;
use App\Models\Reminder;
use App\Models\User;
use App\Notifications\ReminderNotification;
use Illuminate\Support\Facades\DB;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Testing Notification System...\n\n";

try {
    // Check if notifications table exists and has data
    echo "1. Checking notifications table...\n";
    $notificationCount = DB::table('notifications')->count();
    echo "   Total notifications in database: $notificationCount\n\n";

    // Check recent notifications
    echo "2. Recent notifications:\n";
    $recentNotifications = DB::table('notifications')
        ->orderBy('created_at', 'desc')
        ->limit(5)
        ->get(['id', 'type', 'notifiable_type', 'notifiable_id', 'data', 'read_at', 'created_at']);
    
    foreach ($recentNotifications as $notification) {
        $data = json_decode($notification->data, true);
        $type = $data['type'] ?? 'unknown';
        $read = $notification->read_at ? 'Read' : 'Unread';
        echo "   - ID: {$notification->id}, Type: {$type}, Status: {$read}, Created: {$notification->created_at}\n";
    }
    echo "\n";

    // Check departments and their notifications
    echo "3. Department notification counts:\n";
    $departments = Department::with('notifications')->get();
    foreach ($departments as $dept) {
        $unreadCount = $dept->unreadNotifications()->count();
        $totalCount = $dept->notifications()->count();
        echo "   - {$dept->name}: {$totalCount} total, {$unreadCount} unread\n";
    }
    echo "\n";

    // Check recent reminders
    echo "4. Recent reminders:\n";
    $recentReminders = Reminder::with(['report', 'sentBy'])
        ->orderBy('created_at', 'desc')
        ->limit(5)
        ->get();
    
    foreach ($recentReminders as $reminder) {
        $sentBy = $reminder->sentBy ? $reminder->sentBy->name : 'Unknown';
        echo "   - ID: {$reminder->id}, Type: {$reminder->type}, Report: {$reminder->report_id}, Sent by: {$sentBy}, Created: {$reminder->created_at}\n";
    }
    echo "\n";

    echo "Test completed successfully!\n";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
}
