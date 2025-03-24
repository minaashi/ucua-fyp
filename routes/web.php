<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\AdminReportController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\AdminController;

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
        Route::post('/login', [App\Http\Controllers\Auth\LoginController::class, 'adminLogin']);
        Route::get('/register', function () {
            return view('admin.auth.register');
        })->name('admin.register');
        Route::post('/register', [AdminController::class, 'register'])->name('admin.register.submit');
    });

    // Protected Admin Routes
    Route::middleware(['auth', 'role:admin'])->group(function () {
        Route::get('/dashboard', function() {
            return view('admin.dashboard');
        })->name('admin.dashboard');


        Route::get('/reports', function(){
            return view('admin.reports');
        })->name('admin.reports.index');

        Route::get('/users', function () {
            return view('admin.users');
        })->name('admin.users');
        
        Route::get('/warnings', function () {
            return view('admin.warnings');
        })->name('admin.warnings');

        Route::get('/settings', function() {
            return view('admin.settings');
        })->name('admin.settings');
    });
});

// Explicit logout route
Route::post('/logout', [LoginController::class, 'logout'])
    ->name('logout')
    ->middleware('auth');

