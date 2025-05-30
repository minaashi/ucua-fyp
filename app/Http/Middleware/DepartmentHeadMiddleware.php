<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class DepartmentHeadMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check() || !auth()->user()->hasRole('department_head')) {
            return redirect()->route('login')->with('error', 'Unauthorized access. Only department heads can access this page.');
        }

        // If accessing department-specific data, ensure they can only see their own department's data
        if ($request->route('department')) {
            $departmentId = $request->route('department')->id;
            if (auth()->user()->department_id !== $departmentId) {
                return redirect()->back()->with('error', 'You can only access your own department\'s data.');
            }
        }

        return $next($request);
    }
} 