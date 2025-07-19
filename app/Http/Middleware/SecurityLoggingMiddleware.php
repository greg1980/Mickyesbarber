<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Helpers\SecurityHelper;
use Symfony\Component\HttpFoundation\Response;

class SecurityLoggingMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Log potential security events
        $this->logSecurityEvents($request, $response);

        return $response;
    }

    /**
     * Log potential security events.
     */
    protected function logSecurityEvents(Request $request, Response $response): void
    {
        $user = $request->user();
        $userId = $user ? $user->id : 'guest';
        $ip = $request->ip();
        $userAgent = $request->userAgent();
        $url = $request->fullUrl();
        $method = $request->method();

        // Log failed authentication attempts
        if ($response->getStatusCode() === 401) {
            Log::warning('Security: Failed authentication attempt', [
                'user_id' => $userId,
                'ip' => $ip,
                'user_agent' => $userAgent,
                'url' => $url,
                'method' => $method,
            ]);
        }

        // Log potential XSS attempts
        $this->logPotentialXssAttempts($request, $userId, $ip, $userAgent);

        // Log file upload attempts
        if ($request->hasFile('photo') || $request->hasFile('before_photo') || $request->hasFile('after_photo')) {
            $this->logFileUploadAttempt($request, $userId, $ip, $userAgent);
        }

        // Log admin actions
        if (str_contains($url, '/admin/') && $user && $user->role === 'admin') {
            Log::info('Security: Admin action performed', [
                'user_id' => $userId,
                'ip' => $ip,
                'url' => $url,
                'method' => $method,
                'action' => $this->extractActionFromUrl($url),
            ]);
        }
    }

    /**
     * Log potential XSS attempts.
     */
    protected function logPotentialXssAttempts(Request $request, $userId, $ip, $userAgent): void
    {
        $inputs = $request->all();

        foreach ($inputs as $key => $value) {
            if (is_string($value) && SecurityHelper::containsMaliciousContent($value)) {
                Log::warning('Security: Potential XSS attempt detected', [
                    'user_id' => $userId,
                    'ip' => $ip,
                    'user_agent' => $userAgent,
                    'input_key' => $key,
                    'input_value' => substr($value, 0, 100), // Log first 100 chars only
                    'url' => $request->fullUrl(),
                ]);
            }
        }
    }

    /**
     * Log file upload attempts.
     */
    protected function logFileUploadAttempt(Request $request, $userId, $ip, $userAgent): void
    {
        $files = $request->allFiles();

        foreach ($files as $key => $file) {
            if ($file && $file->isValid()) {
                Log::info('Security: File upload attempt', [
                    'user_id' => $userId,
                    'ip' => $ip,
                    'user_agent' => $userAgent,
                    'file_key' => $key,
                    'file_name' => $file->getClientOriginalName(),
                    'file_size' => $file->getSize(),
                    'file_mime' => $file->getMimeType(),
                    'url' => $request->fullUrl(),
                ]);
            }
        }
    }

    /**
     * Extract action from URL for logging.
     */
    protected function extractActionFromUrl(string $url): string
    {
        $path = parse_url($url, PHP_URL_PATH);
        $segments = explode('/', trim($path, '/'));

        if (count($segments) >= 3) {
            return $segments[1] . '/' . $segments[2];
        }

        return $path;
    }
}
