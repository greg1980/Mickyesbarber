<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        if (!$request->user() || $request->user()->role !== $role) {
            abort(403, 'Unauthorized action.');
        }

        // Check if barber is approved
        if ($role === 'barber' && $request->user()->role === 'barber') {
            if (!$request->user()->barber || !$request->user()->barber->is_approved) {
                abort(403, 'Your barber profile is not approved yet. Please wait for admin approval.');
            }
        }

        return $next($request);
    }
}
