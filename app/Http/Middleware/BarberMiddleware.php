<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class BarberMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!$request->user() || !$request->user()->isBarber()) {
            return redirect()->route('dashboard')->with('error', 'Access denied. Barber privileges required.');
        }

        return $next($request);
    }
}
