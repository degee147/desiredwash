<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Assuming you have an 'is_admin' field in your users table
        if (auth()->check() && (auth()->user()->admin or auth()->user()->sa)) {
            return $next($request);
        }
        abort(403, 'Unauthorized');
        // Redirect to home or show unauthorized page if not an admin
        // return redirect('/login')->with('error', 'You do not have access to this section.');
        // return $next($request);
    }
}
