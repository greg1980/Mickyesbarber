<?php

namespace App\Helpers;

class SecurityHelper
{
    /**
     * Sanitize HTML content to prevent XSS attacks.
     */
    public static function sanitizeHtml(string $input): string
    {
        // Remove potentially dangerous HTML tags and attributes
        $allowedTags = '<p><br><strong><em><u><ol><ul><li><h1><h2><h3><h4><h5><h6>';
        $allowedAttributes = 'class,id,style';

        // Strip all HTML tags except allowed ones
        $cleaned = strip_tags($input, $allowedTags);

        // Remove potentially dangerous attributes
        $cleaned = preg_replace('/<[^>]*\s(on\w+\s*=\s*["\'][^"\']*["\'])/i', '', $cleaned);
        $cleaned = preg_replace('/<[^>]*\s(javascript\s*:)/i', '', $cleaned);
        $cleaned = preg_replace('/<[^>]*\s(data\s*:)/i', '', $cleaned);

        return $cleaned;
    }

    /**
     * Sanitize user input for safe database storage.
     */
    public static function sanitizeInput(string $input): string
    {
        // Remove null bytes
        $input = str_replace(chr(0), '', $input);

        // Remove control characters except newlines and tabs
        $input = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/', '', $input);

        // Trim whitespace
        $input = trim($input);

        return $input;
    }

    /**
     * Validate and sanitize email address.
     */
    public static function sanitizeEmail(string $email): ?string
    {
        $email = filter_var(trim($email), FILTER_SANITIZE_EMAIL);

        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return $email;
        }

        return null;
    }

    /**
     * Validate and sanitize URL.
     */
    public static function sanitizeUrl(string $url): ?string
    {
        $url = filter_var(trim($url), FILTER_SANITIZE_URL);

        if (filter_var($url, FILTER_VALIDATE_URL)) {
            return $url;
        }

        return null;
    }

    /**
     * Sanitize file name to prevent directory traversal attacks.
     */
    public static function sanitizeFileName(string $fileName): string
    {
        // Remove directory traversal characters
        $fileName = str_replace(['../', '..\\', './', '.\\'], '', $fileName);

        // Remove null bytes and control characters
        $fileName = str_replace(chr(0), '', $fileName);
        $fileName = preg_replace('/[\x00-\x1F\x7F]/', '', $fileName);

        // Remove potentially dangerous characters
        $fileName = preg_replace('/[<>:"|?*]/', '', $fileName);

        // Limit length
        if (strlen($fileName) > 255) {
            $fileName = substr($fileName, 0, 255);
        }

        return $fileName;
    }

    /**
     * Generate a secure random token.
     */
    public static function generateSecureToken(int $length = 32): string
    {
        return bin2hex(random_bytes($length));
    }

    /**
     * Validate JSON-LD content for XSS prevention.
     */
    public static function sanitizeJsonLd(string $jsonLd): string
    {
        // Decode JSON to validate structure
        $decoded = json_decode($jsonLd, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            return '';
        }

        // Recursively sanitize all string values
        $sanitized = self::sanitizeJsonArray($decoded);

        return json_encode($sanitized, JSON_UNESCAPED_SLASHES);
    }

    /**
     * Recursively sanitize JSON array values.
     */
    private static function sanitizeJsonArray(array $array): array
    {
        $sanitized = [];

        foreach ($array as $key => $value) {
            if (is_string($value)) {
                $sanitized[$key] = htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
            } elseif (is_array($value)) {
                $sanitized[$key] = self::sanitizeJsonArray($value);
            } else {
                $sanitized[$key] = $value;
            }
        }

        return $sanitized;
    }

    /**
     * Check if a string contains potentially malicious content.
     */
    public static function containsMaliciousContent(string $input): bool
    {
        $patterns = [
            '/<script\b[^<]*(?:(?!<\/script>)<[^<]*)*<\/script>/mi',
            '/javascript\s*:/i',
            '/vbscript\s*:/i',
            '/on\w+\s*=/i',
            '/data\s*:/i',
            '/<iframe\b[^>]*>/i',
            '/<object\b[^>]*>/i',
            '/<embed\b[^>]*>/i',
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $input)) {
                return true;
            }
        }

        return false;
    }
}
