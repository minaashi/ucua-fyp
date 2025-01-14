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
// These routes are related to user authentication (register, login, etc.)
Auth::routes();

// Explicitly define logout route for authenticated users
Route::post('/logout', [LoginController::class, 'logout'])
    ->name('logout')
    ->middleware('auth');

// User Routes (authenticated routes for users)
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

// Admin Dummy Registration Route
Route::get('/admin/register', function () {
    return view('admin.register');  // Admin dummy registration view
})->name('admin.register');

// Handle Admin Dummy Registration Form Submission (no data saving)
Route::post('/admin/register', function () {
    return redirect()->route('admin.dashboard');  // Redirect to admin dashboard (no saving)
})->name('admin.register.submit');

// Admin Routes (Dummy Admin Pages, using middleware 'auth' and role 'admin')
Route::middleware(['auth'])->prefix('admin')->group(function () {
    // Admin Dashboard Route (Dummy page only)
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('admin.dashboard');
    // Admin Reports Route (Dummy page only)
    Route::get('/reports', [AdminReportController::class, 'index'])->name('admin.reports.index');
    // Admin Send Warning Letters (Dummy functionality)
    Route::post('/send-warning-letters', [AdminDashboardController::class, 'sendWarningLetters'])
        ->name('admin.sendWarningLetters');
});

// Admin Login Routes (Only for guests, means not logged in users)
Route::middleware('guest')->prefix('admin')->group(function () {
    Route::get('/login', [LoginController::class, 'showAdminLoginForm'])->name('admin.login');
    Route::post('/login', [LoginController::class, 'adminLogin'])->name('admin.login.submit');
});

