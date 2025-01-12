public function handle($request, Closure $next)
{
    if (auth()->user() && auth()->user()->hasRole('admin')) {
        return $next($request);
    }

    // Redirect or send error if user is not admin
    return redirect('/dashboard'); // Or wherever else you'd like to redirect non-admin users
}
