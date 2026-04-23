<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next, $role): Response
    {
        if (!Auth::check() || Auth::user()->role !== $role) {
            if (Auth::check()) {
                return Auth::user()->role === 'admin' ? redirect('/admin/dashboard') : redirect('/user/dashboard');
            }
            return redirect('/login');
        }
        return $next($request);
    }
}
