<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PerformanceMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Add performance headers
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
        $response->headers->set('X-XSS-Protection', '1; mode=block');

        // Add security headers
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');

        // Add cache headers for static assets
        if ($request->is('*.css') || $request->is('*.js') || $request->is('*.png') || $request->is('*.jpg') || $request->is('*.jpeg') || $request->is('*.gif') || $request->is('*.svg') || $request->is('*.webp')) {
            $response->headers->set('Cache-Control', 'public, max-age=31536000, immutable');
        } elseif ($request->is('images/*')) {
            $response->headers->set('Cache-Control', 'public, max-age=86400');
        } else {
            // For HTML pages, use shorter cache
            $response->headers->set('Cache-Control', 'public, max-age=3600');
        }

        return $response;
    }
}
