<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Helpers\SecurityHelper;

class SecurityHelperTest extends TestCase
{
    public function test_sanitize_html_removes_dangerous_tags()
    {
        $input = '<script>alert("xss")</script><p>Safe content</p>';
        $result = SecurityHelper::sanitizeHtml($input);

        $this->assertStringNotContainsString('<script>', $result);
        $this->assertStringContainsString('<p>Safe content</p>', $result);
    }

    public function test_sanitize_html_allows_safe_tags()
    {
        $input = '<p>Paragraph</p><strong>Bold</strong><em>Italic</em>';
        $result = SecurityHelper::sanitizeHtml($input);

        $this->assertStringContainsString('<p>Paragraph</p>', $result);
        $this->assertStringContainsString('<strong>Bold</strong>', $result);
        $this->assertStringContainsString('<em>Italic</em>', $result);
    }

    public function test_sanitize_html_removes_dangerous_attributes()
    {
        $input = '<p onclick="alert(\'xss\')" class="safe">Content</p>';
        $result = SecurityHelper::sanitizeHtml($input);

        $this->assertStringNotContainsString('onclick', $result);
        $this->assertStringContainsString('class="safe"', $result);
    }

    public function test_sanitize_input_removes_null_bytes()
    {
        $input = "Safe content\0with null bytes";
        $result = SecurityHelper::sanitizeInput($input);

        $this->assertStringNotContainsString("\0", $result);
        $this->assertStringContainsString('Safe content', $result);
    }

    public function test_sanitize_input_removes_control_characters()
    {
        $input = "Safe\x01content\x02with\x03control\x04chars";
        $result = SecurityHelper::sanitizeInput($input);

        $this->assertEquals('Safecontentwithcontrolchars', $result);
    }

    public function test_sanitize_input_preserves_newlines_and_tabs()
    {
        $input = "Line 1\nLine 2\tTabbed content";
        $result = SecurityHelper::sanitizeInput($input);

        $this->assertStringContainsString("\n", $result);
        $this->assertStringContainsString("\t", $result);
    }

    public function test_sanitize_email_validates_correct_emails()
    {
        $validEmails = [
            'test@example.com',
            'user.name@domain.co.uk',
            'user+tag@example.org'
        ];

        foreach ($validEmails as $email) {
            $result = SecurityHelper::sanitizeEmail($email);
            $this->assertEquals($email, $result);
        }
    }

    public function test_sanitize_email_rejects_invalid_emails()
    {
        $invalidEmails = [
            'invalid-email',
            '@example.com',
            'user@',
            'user@.com'
        ];

        foreach ($invalidEmails as $email) {
            $result = SecurityHelper::sanitizeEmail($email);
            $this->assertNull($result);
        }
    }

    public function test_sanitize_url_validates_correct_urls()
    {
        $validUrls = [
            'https://example.com',
            'http://www.example.org/path',
            'https://subdomain.example.co.uk'
        ];

        foreach ($validUrls as $url) {
            $result = SecurityHelper::sanitizeUrl($url);
            $this->assertEquals($url, $result);
        }
    }

    public function test_sanitize_url_rejects_invalid_urls()
    {
        $invalidUrls = [
            'not-a-url',
            'javascript:alert("xss")'
        ];

        foreach ($invalidUrls as $url) {
            $result = SecurityHelper::sanitizeUrl($url);
            $this->assertNull($result);
        }
    }

    public function test_sanitize_file_name_removes_dangerous_characters()
    {
        $input = '../dangerous/path/file<name>:with|bad*chars?';
        $result = SecurityHelper::sanitizeFileName($input);

        $this->assertStringNotContainsString('../', $result);
        $this->assertStringNotContainsString('<', $result);
        $this->assertStringNotContainsString('>', $result);
        $this->assertStringNotContainsString(':', $result);
        $this->assertStringNotContainsString('|', $result);
        $this->assertStringNotContainsString('*', $result);
        $this->assertStringNotContainsString('?', $result);
    }

    public function test_sanitize_file_name_limits_length()
    {
        $longName = str_repeat('a', 300);
        $result = SecurityHelper::sanitizeFileName($longName);

        $this->assertLessThanOrEqual(255, strlen($result));
    }

    public function test_generate_secure_token_creates_hex_string()
    {
        $token = SecurityHelper::generateSecureToken(16);

        $this->assertEquals(32, strlen($token)); // 16 bytes = 32 hex chars
        $this->assertMatchesRegularExpression('/^[a-f0-9]+$/', $token);
    }

    public function test_sanitize_json_ld_validates_json_structure()
    {
        $validJson = '{"name": "Test", "description": "Safe content"}';
        $result = SecurityHelper::sanitizeJsonLd($validJson);

        $this->assertNotEmpty($result);
        $decoded = json_decode($result, true);
        $this->assertNotNull($decoded);
    }

    public function test_sanitize_json_ld_handles_invalid_json()
    {
        $invalidJson = '{"name": "Test", "description": "Unclosed quote}';
        $result = SecurityHelper::sanitizeJsonLd($invalidJson);

        $this->assertEquals('', $result);
    }

    public function test_sanitize_json_ld_escapes_html_in_strings()
    {
        $jsonWithHtml = '{"name": "<script>alert(\'xss\')</script>", "description": "Safe"}';
        $result = SecurityHelper::sanitizeJsonLd($jsonWithHtml);

        $this->assertStringContainsString('&lt;script&gt;', $result);
        $this->assertStringNotContainsString('<script>', $result);
    }

    public function test_contains_malicious_content_detects_script_tags()
    {
        $maliciousInputs = [
            '<script>alert("xss")</script>',
            '<SCRIPT>alert("xss")</SCRIPT>',
            '<script src="evil.js"></script>'
        ];

        foreach ($maliciousInputs as $input) {
            $this->assertTrue(SecurityHelper::containsMaliciousContent($input));
        }
    }

    public function test_contains_malicious_content_detects_javascript_protocol()
    {
        $maliciousInputs = [
            'javascript:alert("xss")',
            'JAVASCRIPT:alert("xss")',
            '<a href="javascript:alert(\'xss\')">Click</a>'
        ];

        foreach ($maliciousInputs as $input) {
            $this->assertTrue(SecurityHelper::containsMaliciousContent($input));
        }
    }

    public function test_contains_malicious_content_detects_event_handlers()
    {
        $maliciousInputs = [
            '<img src="x" onerror="alert(\'xss\')">',
            '<div onclick="alert(\'xss\')">Click me</div>',
            '<p onmouseover="alert(\'xss\')">Hover me</p>'
        ];

        foreach ($maliciousInputs as $input) {
            $this->assertTrue(SecurityHelper::containsMaliciousContent($input));
        }
    }

    public function test_contains_malicious_content_allows_safe_content()
    {
        $safeInputs = [
            'This is safe content',
            '<p>Safe paragraph</p>',
            '<strong>Bold text</strong>',
            'https://example.com',
            'user@example.com'
        ];

        foreach ($safeInputs as $input) {
            $this->assertFalse(SecurityHelper::containsMaliciousContent($input));
        }
    }
}
