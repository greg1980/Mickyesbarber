<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Log;

class AdminMiddleware
{
    public function __construct()
    {
        Log::info('AdminMiddleware constructed');
    }

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        Log::info('AdminMiddleware handling request', [
            'user' => $request->user() ? [
                'id' => $request->user()->id,
                'name' => $request->user()->name,
                'is_admin' => $request->user()->is_admin,
            ] : null,
            'path' => $request->path(),
            'method' => $request->method(),
        ]);

        if (!$request->user() || !$request->user()->is_admin) {
            Log::warning('Access denied in AdminMiddleware', [
                'user_exists' => (bool) $request->user(),
                'is_admin' => $request->user() ? $request->user()->is_admin : null,
            ]);
            return redirect()->route('dashboard')->with('error', 'Access denied. Admin privileges required.');
        }

        return $next($request);
    }
}
