<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The authentication / authorization services for the application.
     *
     * @var array
     */
    protected $policies = [
        \App\Models\Report::class => \App\Policies\ReportPolicy::class,
        \App\Models\User::class => \App\Policies\UserPolicy::class,
        \App\Models\Department::class => \App\Policies\DepartmentPolicy::class,
    ];

    /**
     * Register any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();
    }
}
