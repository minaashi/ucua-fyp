<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureAdmin
{
    public function handle(Request $request, Closure $next)
    {
        // Check if user has admin role
        if ($request->user() && !$request->user()->hasRole('admin')) {
            // redirect the user to a different page if not an admin
            return redirect('/dashboard');
        }

        return $next($request);
    }
}
