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
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\UCUAOfficerController;
use App\Http\Controllers\Auth\UCUALoginController;
use App\Http\Controllers\UCUADashboardController;


// Home/Hero page route
Route::get('/', function () {
    return view('welcome');
})->name('home');

// Authentication routes
Auth::routes();

// User-Specific Routes
Route::middleware(['auth'])->group(function () {
    // User Dashboard
    Route::get('/dashboard', [DashboardController::class, 'dashboard'])->name('dashboard');
    
    // Report Routes
    Route::get('/submit-report', [ReportController::class, 'create'])->name('reports.create');
    Route::post('/reports/store', [ReportController::class, 'store'])->name('reports.store');
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/track-report', [ReportController::class, 'trackStatus'])->name('reports.track');
    Route::get('/report-history', [DashboardController::class, 'reportHistory'])->name('reports.history');
    
    // User Profile Routes
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::post('/profile/update', [ProfileController::class, 'update'])->name('profile.update');
});

    

// Admin Routes
Route::prefix('admin')->group(function () {
    // Guest Admin Routes
    Route::middleware('guest')->group(function () {
        Route::get('/login', [App\Http\Controllers\Auth\LoginController::class, 'showAdminLoginForm'])->name('admin.login');

       Route::post('/login', [App\Http\Controllers\Auth\LoginController::class, 'adminLogin'])
        ->name('admin.login.submit');

        Route::get('/register', function () {
            return view('admin.auth.register');
        })->name('admin.register');
        Route::post('/register', [AdminController::class, 'register'])->name('admin.register.submit');
    });

    // Protected Admin Routes
    Route::middleware(['auth', 'role:admin'])->group(function () {
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('admin.dashboard');

        // Reports Routes
        Route::get('/reports', [AdminReportController::class, 'index'])->name('admin.reports.index');
        Route::post('/reports/{report}/update-status', [AdminReportController::class, 'updateStatus'])->name('admin.reports.update-status');
        Route::delete('/reports/{report}', [AdminReportController::class, 'destroy'])->name('admin.reports.destroy');

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
            ->name('admin.warnings');
        Route::post('/warnings', [AdminWarningController::class, 'store'])
            ->name('admin.warnings.store');
        Route::post('/warnings/{warning}/resend', [AdminWarningController::class, 'resend'])
            ->name('admin.warnings.resend');

        Route::get('/settings', function() {
            return view('admin.settings');
        })->name('admin.settings');
    });
});

// UCUA Officer Routes
Route::middleware(['auth', 'role:ucua_officer'])->prefix('ucua')->name('ucua.')->group(function () {
    Route::get('/dashboard', [UCUADashboardController::class, 'index'])->name('dashboard');
    Route::get('/assign-departments', [UCUADashboardController::class, 'assignDepartmentsPage'])->name('assign-departments-page');
    Route::post('/assign-department', [UCUADashboardController::class, 'assignDepartment'])->name('assign-department');
    Route::post('/add-remarks', [UCUADashboardController::class, 'addRemarks'])->name('add-remarks');
    Route::post('/suggest-warning', [UCUADashboardController::class, 'suggestWarning'])->name('suggest-warning');
    Route::post('/send-reminder', [UCUADashboardController::class, 'sendReminder'])->name('send-reminder');
    Route::get('/warnings', [UCUADashboardController::class, 'warningsPage'])->name('warnings');
    Route::get('/reminders', [UCUADashboardController::class, 'remindersPage'])->name('reminders');
});

// UCUA Officer Login Routes
Route::get('/ucua/login', [UCUALoginController::class, 'showLoginForm'])->name('ucua.login');
Route::post('/ucua/login', [UCUALoginController::class, 'login']);
Route::post('/ucua/logout', [UCUALoginController::class, 'logout'])->name('ucua.logout');

// Explicit logout route
Route::post('/logout', [LoginController::class, 'logout'])
    ->name('logout')
    ->middleware('auth');

