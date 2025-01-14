<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\AdminReportController;
use App\Http\Controllers\Auth\LoginController;

// Home/Hero page route
Route::get('/', function () {
    return view('welcome');
})->name('home');

// Authentication routes (including register, login, etc.)
Auth::routes();

// Explicitly define logout route
Route::post('/logout', [LoginController::class, 'logout'])
    ->name('logout')
    ->middleware('auth');

// User Dashboard and Related Routes
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'dashboard'])->name('dashboard');
    
    // Report Routes
    Route::get('/submit-report', [ReportController::class, 'create'])->name('reports.create');
    Route::post('/reports', [ReportController::class, 'store'])->name('reports.store');
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/track-report', [ReportController::class, 'trackStatus'])->name('reports.track');
    Route::get('/report-history', [DashboardController::class, 'reportHistory'])->name('reports.history');
    
    // User Profile Routes
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::post('/profile/update', [ProfileController::class, 'update'])->name('profile.update');
});

// Admin Routes (with role-based middleware for admin users)
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/admin/dashboard', [AdminDashboardController::class, 'index'])->name('admin.dashboard');
    Route::get('/admin/reports', [AdminReportController::class, 'index'])->name('admin.reports.index');
    Route::post('/admin/send-warning-letters', [AdminDashboardController::class, 'sendWarningLetters'])
        ->name('admin.sendWarningLetters');
});

// Admin Login Routes (only accessible by guests)
Route::middleware('guest')->group(function () {
    Route::get('/admin/login', [LoginController::class, 'showAdminLoginForm'])
        ->name('admin.login');

    Route::post('/admin/login', [LoginController::class, 'adminLogin'])
        ->name('admin.login.submit');
});
