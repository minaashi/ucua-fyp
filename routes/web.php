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
        Route::get('/login', [LoginController::class, 'showAdminLoginForm'])->name('admin.login');
        Route::post('/login', [LoginController::class, 'adminLogin'])->name('admin.login.submit');
        Route::get('/register', function () {
            return view('admin.register');
        })->name('admin.register');
        Route::post('/register', function () {
            return redirect()->route('admin.dashboard.dummy');
        })->name('admin.register.submit');
    });

    // Admin Dummy Pages (no auth required for demo)
    Route::get('/dashboard', function () {
        return view('admin.dashboard.dummy');
    })->name('admin.dashboard.dummy');

    // Admin Reports Routes
    Route::get('/reports', [AdminReportController::class, 'index'])->name('admin.reports.index');
    Route::get('/reports/create', [AdminReportController::class, 'create'])->name('admin.reports.create');
    Route::post('/reports', [AdminReportController::class, 'store'])->name('admin.reports.store');
    Route::get('/reports/{report}', [AdminReportController::class, 'show'])->name('admin.reports.show');
    Route::get('/reports/{report}/edit', [AdminReportController::class, 'edit'])->name('admin.reports.edit');
    Route::put('/reports/{report}', [AdminReportController::class, 'update'])->name('admin.reports.update');
    Route::delete('/reports/{report}', [AdminReportController::class, 'destroy'])->name('admin.reports.destroy');

    Route::get('/users', function () {
        return view('admin.users.dummy');
    })->name('admin.users.dummy');

    Route::get('/warnings', function () {
        return view('admin.warnings.dummy');
    })->name('admin.warnings.dummy');

    Route::get('/settings', function () {
        return view('admin.settings.dummy');
    })->name('admin.settings.dummy');
});

// Explicit logout route
Route::post('/logout', [LoginController::class, 'logout'])
    ->name('logout')
    ->middleware('auth');

