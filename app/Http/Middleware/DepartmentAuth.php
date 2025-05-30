<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class DepartmentAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::guard('department')->check()) {
            return redirect()->route('department.login');
        }

        // Set the authenticated department in the session
        $request->session()->put('auth.department', Auth::guard('department')->user());

        return $next($request);
    }
}
