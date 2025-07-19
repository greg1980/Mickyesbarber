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

        // Add Content Security Policy (disabled in development for Vite compatibility)
        if (app()->environment('production')) {
            $response->headers->set('Content-Security-Policy', "default-src 'self'; script-src 'self' 'unsafe-inline' 'unsafe-eval' https://js.stripe.com; style-src 'self' 'unsafe-inline' https://fonts.bunny.net; font-src 'self' https://fonts.bunny.net; img-src 'self' data: https:; connect-src 'self' https://api.stripe.com; frame-src https://js.stripe.com;");
        } else {
            // Explicitly remove any existing CSP headers in development
            $response->headers->remove('Content-Security-Policy');
            $response->headers->remove('X-Content-Security-Policy');
        }
        // Note: CSP disabled in development to allow Vite dev server

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
