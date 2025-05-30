<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Auth;
use App\Models\Department;

class DepartmentServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        Auth::provider('departments', function ($app, array $config) {
            return new \Illuminate\Auth\EloquentUserProvider(
                $app['hash'],
                Department::class
            );
        });
    }
} 