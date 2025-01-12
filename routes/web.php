<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AdminDashboardController;

// Home/Hero page route
Route::get('/', function () {
    return view('welcome');
})->name('home');

// Authentication routes
Auth::routes();

// Explicitly define logout route
Route::post('/logout', [App\Http\Controllers\Auth\LoginController::class, 'logout'])
    ->name('logout')
    ->middleware('auth');

// User Dashboard and Related Routes
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'dashboard'])->name('dashboard');
    Route::get('/submit-report', [DashboardController::class, 'submitReport'])->name('reports.submit');
    Route::get('/track-report', [DashboardController::class, 'trackReport'])->name('reports.track');
    Route::get('/report-history', [DashboardController::class, 'reportHistory'])->name('reports.history');
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::post('/profile/update', [ProfileController::class, 'update'])->name('profile.update');
});

// Admin Routes
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/admin/dashboard', [AdminDashboardController::class, 'index'])->name('admin.dashboard');
    
    Route::resource('admin/reports', ReportController::class)->names([
        'index' => 'admin.reports.index',
        'create' => 'admin.reports.create',
        'store' => 'admin.reports.store',
        'show' => 'admin.reports.show',
        'edit' => 'admin.reports.edit',
        'update' => 'admin.reports.update',
        'destroy' => 'admin.reports.destroy',
    ]);
    
    Route::post('/admin/send-warning-letters', [AdminDashboardController::class, 'sendWarningLetters'])
        ->name('admin.sendWarningLetters');
});
