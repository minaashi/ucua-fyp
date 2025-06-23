<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\AdminReportController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AdminUserController;
use App\Http\Controllers\AdminWarningController;
use App\Http\Controllers\AdminSettingsController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\UCUAOfficerController;
use App\Http\Controllers\Auth\UCUALoginController;
use App\Http\Controllers\UCUADashboardController;
use App\Http\Controllers\Department\AuthController as DepartmentAuthController;
use App\Http\Controllers\Department\DashboardController as DepartmentDashboardController;
use App\Http\Controllers\OtpVerificationController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\HelpController;

// Home/Hero page route
Route::get('/', function () {
    return view('welcome');
})->name('home');

// Route to serve attachment files
Route::get('/attachment/{filename}', function ($filename) {
    $path = storage_path('app/public/reports/' . $filename);

    if (!file_exists($path)) {
        abort(404, 'File not found');
    }

    $mimeType = mime_content_type($path);
    $headers = [
        'Content-Type' => $mimeType,
        'Content-Disposition' => 'inline; filename="' . $filename . '"'
    ];

    return response()->file($path, $headers);
})->name('attachment.view');

// Department Routes
Route::group(['prefix' => 'department'], function () {
    // Public routes (no auth required)
    Route::get('login', [DepartmentAuthController::class, 'showLoginForm'])->name('department.login');
    Route::post('login', [DepartmentAuthController::class, 'login'])->name('department.login.submit');
    Route::post('logout', [DepartmentAuthController::class, 'logout'])->name('department.logout');

    // Protected routes (require department auth)
    Route::middleware(['auth:department'])->group(function () {
        Route::get('dashboard', [DepartmentDashboardController::class, 'index'])->name('department.dashboard');
        Route::get('pending-reports', [DepartmentDashboardController::class, 'pendingReports'])->name('department.pending-reports');
        Route::get('resolved-reports', [DepartmentDashboardController::class, 'resolvedReports'])->name('department.resolved-reports');

        // Report Actions
        Route::get('reports/{report}', [DepartmentDashboardController::class, 'showReport'])->name('department.report.show');
        Route::post('resolve-report', [DepartmentDashboardController::class, 'resolveReport'])->name('department.resolve-report');
        Route::post('add-remarks', [DepartmentDashboardController::class, 'addRemarks'])->name('department.add-remarks');
        Route::post('reports/{report}/export', [DepartmentDashboardController::class, 'exportReport'])->name('department.report.export');
        Route::post('reports/{report}/accept', [DepartmentDashboardController::class, 'acceptReport'])->name('department.report.accept');
        Route::post('reports/{report}/reject', [DepartmentDashboardController::class, 'rejectReport'])->name('department.report.reject');

        // Notification routes
        Route::get('notifications', [DepartmentDashboardController::class, 'notifications'])->name('department.notifications');
        Route::post('notifications/{notification}/mark-read', [DepartmentDashboardController::class, 'markNotificationAsRead'])->name('department.notifications.mark-read');
        Route::post('notifications/mark-all-read', [DepartmentDashboardController::class, 'markAllNotificationsAsRead'])->name('department.notifications.mark-all-read');

        // User lookup route for violator identification
        Route::get('lookup-user/{employeeId}', [DepartmentDashboardController::class, 'lookupUser'])->name('department.lookup-user');
    });
});

// Authentication routes
Auth::routes();

// User-Specific Routes
Route::middleware(['auth', 'email.verified', 'security'])->group(function () {
    // User Dashboard
    Route::get('/dashboard', [DashboardController::class, 'dashboard'])->name('dashboard');

    // Report Routes
    Route::get('/submit-report', [ReportController::class, 'create'])->name('reports.create');
    Route::post('/reports/store', [ReportController::class, 'store'])->name('reports.store');
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/track-report', [ReportController::class, 'trackStatus'])->name('reports.track');
    Route::get('/report-history', [DashboardController::class, 'reportHistory'])->name('reports.history');
    Route::get('/reports/{report}/details', [DashboardController::class, 'showReportDetails'])->name('reports.details');

    // User Profile Routes
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::post('/profile/update', [ProfileController::class, 'update'])->name('profile.update');
});

    

// Admin Routes
Route::prefix('admin')->group(function () {
    // Admin logout route (accessible to authenticated users)
    Route::post('/logout', [App\Http\Controllers\Auth\LoginController::class, 'logout'])->name('admin.logout');

    // Guest Admin Routes
    Route::middleware('guest')->group(function () {
        Route::get('/login', [App\Http\Controllers\Auth\LoginController::class, 'showAdminLoginForm'])->name('admin.login');

       Route::post('/login', [App\Http\Controllers\Auth\LoginController::class, 'adminLogin'])
        ->name('admin.login.submit');

        Route::post('/register', [AdminController::class, 'register'])->name('admin.register.submit');
    });

    // Protected Admin Routes
    Route::middleware(['auth', 'role:admin', 'security'])->group(function () {
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('admin.dashboard');

        // Reports Routes
        Route::get('/reports', [AdminReportController::class, 'index'])->name('admin.reports.index');
        Route::get('/reports/{report}', [AdminReportController::class, 'show'])->name('admin.reports.show');
        Route::post('/reports/{report}/update-status', [AdminReportController::class, 'updateStatus'])->name('admin.reports.update-status');
        Route::delete('/reports/{report}', [AdminReportController::class, 'destroy'])->name('admin.reports.destroy');
        Route::put('/admin/reports/{report}', [App\Http\Controllers\AdminReportController::class, 'update'])->name('admin.reports.update');

        // New routes for accepting and rejecting reports
        Route::post('/reports/{report}/accept', [AdminReportController::class, 'acceptReport'])->name('admin.reports.accept');
        Route::post('/reports/{report}/reject', [AdminReportController::class, 'rejectReport'])->name('admin.reports.reject');

        // Enhanced comment system
        Route::post('/add-remarks', [AdminReportController::class, 'addRemarks'])->name('admin.add-remarks');

        // User Management Routes
        Route::get('/users', [AdminUserController::class, 'index'])
            ->name('admin.users.index');
        Route::post('/users', [AdminUserController::class, 'store'])
            ->name('admin.users.store');
        Route::put('/users/{user}', [AdminUserController::class, 'update'])
            ->name('admin.users.update');
        Route::delete('/users/{user}', [AdminUserController::class, 'destroy'])
            ->name('admin.users.destroy');

        // Department Management Routes
        Route::get('/departments', [DepartmentController::class, 'index'])->name('admin.departments.index');
        Route::get('/departments/create', [DepartmentController::class, 'create'])->name('admin.departments.create');
        Route::post('/departments', [DepartmentController::class, 'store'])->name('admin.departments.store');
        Route::get('/departments/{department}/edit', [DepartmentController::class, 'edit'])->name('admin.departments.edit'); 
        Route::put('/departments/{department}', [DepartmentController::class, 'update'])->name('admin.departments.update');
        Route::delete('/departments/{department}', [DepartmentController::class, 'destroy'])->name('admin.departments.destroy');
        Route::get('/departments/{department}/staff', [DepartmentController::class, 'getDepartmentStaff'])->name('admin.departments.staff');

        // Warning Management Routes
        Route::get('/warnings', [AdminWarningController::class, 'index'])
            ->name('admin.warnings.index');
        Route::get('/warnings/{warning}/details', [AdminWarningController::class, 'getDetails'])
            ->name('admin.warnings.details');
        Route::post('/warnings/{warning}/approve', [AdminWarningController::class, 'approve'])
            ->name('admin.warnings.approve');
        Route::post('/warnings/{warning}/reject', [AdminWarningController::class, 'reject'])
            ->name('admin.warnings.reject');
        Route::post('/warnings/{warning}/send', [AdminWarningController::class, 'send'])
            ->name('admin.warnings.send');
        Route::post('/warnings/{warning}/resend', [AdminWarningController::class, 'resend'])
            ->name('admin.warnings.resend');

        Route::get('/settings', [AdminSettingsController::class, 'index'])->name('admin.settings');
        Route::post('/settings/profile', [AdminSettingsController::class, 'updateProfile'])->name('admin.settings.profile');
        Route::post('/settings/system', [AdminSettingsController::class, 'updateSystemSettings'])->name('admin.settings.system');
    });
});

// UCUA Officer Routes
Route::middleware(['auth', 'role:ucua_officer', 'security'])->prefix('ucua')->name('ucua.')->group(function () {
    Route::get('/dashboard', [UCUADashboardController::class, 'index'])->name('dashboard');
    Route::get('/report/{report}', [UCUADashboardController::class, 'showReport'])->name('report.show');
    Route::get('/assign-departments', [UCUADashboardController::class, 'assignDepartmentsPage'])->name('assign-departments-page');
    Route::post('/assign-department', [UCUADashboardController::class, 'assignDepartment'])->name('assign-department');

    Route::post('/suggest-warning', [UCUADashboardController::class, 'suggestWarning'])->name('suggest-warning');
    Route::post('/send-reminder', [UCUADashboardController::class, 'sendReminder'])->name('send-reminder');
    Route::post('/add-remarks', [UCUADashboardController::class, 'addRemarks'])->name('add-remarks');

    // Debug route for testing reminders
    Route::get('/test-reminder', function() {
        $reminderCount = App\Models\Reminder::count();
        $jobCount = DB::table('jobs')->count();
        $recentLogs = collect(file('storage/logs/laravel.log'))->filter(function($line) {
            return strpos($line, 'Reminder') !== false;
        })->take(-5);

        return response()->json([
            'reminder_count' => $reminderCount,
            'job_count' => $jobCount,
            'recent_reminder_logs' => $recentLogs->values()
        ]);
    });
    Route::get('/warnings', [UCUADashboardController::class, 'warningsPage'])->name('warnings');
    Route::get('/warnings/{warning}/details', [UCUADashboardController::class, 'getWarningDetails'])->name('warnings.details');
    Route::get('/reports/{report}/existing-warnings', [UCUADashboardController::class, 'getExistingWarnings'])->name('reports.existing-warnings');
    Route::get('/reminders', [UCUADashboardController::class, 'remindersPage'])->name('reminders');

    // Warning Analytics Routes
    Route::get('/analytics', [App\Http\Controllers\WarningAnalyticsController::class, 'index'])->name('analytics');
    Route::get('/analytics/data', [App\Http\Controllers\WarningAnalyticsController::class, 'getAnalyticsData'])->name('analytics.data');
    Route::get('/analytics/trends', [App\Http\Controllers\WarningAnalyticsController::class, 'getTrendsData'])->name('analytics.trends');
    Route::get('/analytics/repeat-offenders', [App\Http\Controllers\WarningAnalyticsController::class, 'getRepeatOffenders'])->name('analytics.repeat-offenders');
    Route::get('/analytics/departments', [App\Http\Controllers\WarningAnalyticsController::class, 'getDepartmentStats'])->name('analytics.departments');
    Route::get('/analytics/escalations', [App\Http\Controllers\WarningAnalyticsController::class, 'getEscalationStats'])->name('analytics.escalations');
    Route::get('/analytics/export', [App\Http\Controllers\WarningAnalyticsController::class, 'exportReport'])->name('analytics.export');
    Route::get('/analytics/dashboard-data', [App\Http\Controllers\WarningAnalyticsController::class, 'getDashboardData'])->name('analytics.dashboard-data');
});

// UCUA Officer Login Routes
Route::middleware(['guest'])->group(function () {
    Route::get('/ucua/login', [UCUALoginController::class, 'showLoginForm'])->name('ucua.login');
    Route::post('/ucua/login', [UCUALoginController::class, 'login']);
});
Route::post('/ucua/logout', [UCUALoginController::class, 'logout'])->name('ucua.logout');

// Explicit logout route
Route::post('/logout', [LoginController::class, 'logout'])
    ->name('logout')
    ->middleware('auth');

// OTP Verification Routes (Registration) - No email verification required
Route::middleware(['auth'])->group(function () {
    Route::get('/email/verify', function () {
        return view('auth.verify-otp-notice');
    })->name('verification.notice');

    Route::get('/otp/verify', [OtpVerificationController::class, 'showOtpForm'])->name('otp.form');
    Route::post('/otp/verify', [OtpVerificationController::class, 'verifyOtp'])->name('otp.verify');
    Route::post('/otp/resend', [RegisterController::class, 'resendOtp'])->name('otp.resend');

    // Worker ID generation API
    Route::get('/api/next-worker-id', [RegisterController::class, 'getNextWorkerId'])->name('api.next-worker-id');
});

// Help System Routes
Route::middleware(['auth', 'email.verified'])->group(function () {
    // Regular User Help
    Route::get('/help', [HelpController::class, 'userHelp'])->name('help.user');
    Route::get('/help/search', [HelpController::class, 'search'])->name('help.search');
});

// Admin Help Routes
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/admin/help', [HelpController::class, 'adminHelp'])->name('help.admin');
    Route::get('/admin/help/search', [HelpController::class, 'adminSearch'])->name('help.admin.search');
});

// UCUA Officer Help Routes
Route::middleware(['auth', 'role:ucua_officer'])->group(function () {
    Route::get('/ucua/help', [HelpController::class, 'ucuaHelp'])->name('help.ucua');
    Route::get('/ucua/help/search', [HelpController::class, 'ucuaSearch'])->name('help.ucua.search');
});

// Department Help Routes
Route::middleware(['auth:department'])->group(function () {
    Route::get('/department/help', [HelpController::class, 'departmentHelp'])->name('help.department');
    Route::get('/department/help/search', [HelpController::class, 'departmentSearch'])->name('help.department.search');
});

// HOD (Head of Department) Routes - for User models with department_head role
Route::middleware(['auth', 'role:department_head', 'security'])->prefix('hod')->name('hod.')->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\HODController::class, 'index'])->name('dashboard');
    Route::get('/pending-reports', [App\Http\Controllers\HODController::class, 'pendingReports'])->name('pending-reports');
    Route::get('/resolved-reports', [App\Http\Controllers\HODController::class, 'resolvedReports'])->name('resolved-reports');
    Route::get('/report/{report}', [App\Http\Controllers\HODController::class, 'showReport'])->name('report.show');

    // Notification routes
    Route::get('/notifications', [App\Http\Controllers\HODController::class, 'notifications'])->name('notifications');
    Route::post('/notifications/{notification}/mark-read', [App\Http\Controllers\HODController::class, 'markNotificationAsRead'])->name('notifications.mark-read');
    Route::post('/notifications/mark-all-read', [App\Http\Controllers\HODController::class, 'markAllNotificationsAsRead'])->name('notifications.mark-all-read');
});

// Login OTP Verification Routes
Route::get('/login/otp', [App\Http\Controllers\Auth\LoginOtpController::class, 'showOtpForm'])->name('login.otp.form');
Route::post('/login/otp/verify', [App\Http\Controllers\Auth\LoginOtpController::class, 'verifyOtp'])->name('login.otp.verify');
Route::post('/login/otp/resend', [App\Http\Controllers\Auth\LoginOtpController::class, 'resendOtp'])->name('login.otp.resend');

// Session Management Routes
Route::middleware(['auth'])->group(function () {
    Route::post('/api/extend-session', [App\Http\Controllers\SessionController::class, 'extendSession'])->name('session.extend');
});





