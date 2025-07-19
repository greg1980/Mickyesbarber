<?php

namespace Tests\Unit\Middleware;

use Tests\TestCase;
use App\Http\Middleware\SecurityLoggingMiddleware;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SecurityLoggingMiddlewareTest extends TestCase
{
    use RefreshDatabase;

    protected $middleware;
    protected $request;
    protected $response;

    protected function setUp(): void
    {
        parent::setUp();

        $this->middleware = new SecurityLoggingMiddleware();
        $this->request = Request::create('/test', 'GET');
        $this->response = new Response();
    }

    public function test_middleware_logs_failed_authentication()
    {
        Log::shouldReceive('warning')
            ->once()
            ->with('Security: Failed authentication attempt', \Mockery::type('array'));

        $this->response->setStatusCode(401);

        $this->middleware->handle($this->request, function () {
            return $this->response;
        });
    }

    public function test_middleware_logs_potential_xss_attempts()
    {
        Log::shouldReceive('warning')
            ->once()
            ->with('Security: Potential XSS attempt detected', \Mockery::type('array'));

        $this->request->merge([
            'comment' => '<script>alert("xss")</script>'
        ]);

        $this->middleware->handle($this->request, function () {
            return $this->response;
        });
    }

    public function test_middleware_logs_file_upload_attempts()
    {
        Log::shouldReceive('info')
            ->once()
            ->with('Security: File upload attempt', \Mockery::type('array'));

        $file = \Illuminate\Http\UploadedFile::fake()->image('test.jpg');
        $this->request->files->set('photo', $file);

        $this->middleware->handle($this->request, function () {
            return $this->response;
        });
    }

    public function test_middleware_logs_admin_actions()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin);

        Log::shouldReceive('info')
            ->once()
            ->with('Security: Admin action performed', \Mockery::type('array'));

        $this->request = Request::create('/admin/users', 'GET');
        $this->request->setUserResolver(function () use ($admin) {
            return $admin;
        });

        $this->middleware->handle($this->request, function () {
            return $this->response;
        });
    }

    public function test_middleware_logs_multiple_xss_attempts()
    {
        Log::shouldReceive('warning')
            ->twice()
            ->with('Security: Potential XSS attempt detected', \Mockery::type('array'));

        $this->request->merge([
            'name' => '<script>alert("xss1")</script>',
            'description' => 'javascript:alert("xss2")'
        ]);

        $this->middleware->handle($this->request, function () {
            return $this->response;
        });
    }

    public function test_middleware_logs_transformation_photo_uploads()
    {
        Log::shouldReceive('info')
            ->twice()
            ->with('Security: File upload attempt', \Mockery::type('array'));

        $beforePhoto = \Illuminate\Http\UploadedFile::fake()->image('before.jpg');
        $afterPhoto = \Illuminate\Http\UploadedFile::fake()->image('after.jpg');

        $this->request->files->set('before_photo', $beforePhoto);
        $this->request->files->set('after_photo', $afterPhoto);

        $this->middleware->handle($this->request, function () {
            return $this->response;
        });
    }

    public function test_middleware_logs_admin_user_management_actions()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin);

        Log::shouldReceive('info')
            ->once()
            ->with('Security: Admin action performed', \Mockery::type('array'));

        $this->request = Request::create('/admin/users/1/delete', 'DELETE');
        $this->request->setUserResolver(function () use ($admin) {
            return $admin;
        });

        $this->middleware->handle($this->request, function () {
            return $this->response;
        });
    }

    public function test_middleware_logs_admin_service_management_actions()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin);

        Log::shouldReceive('info')
            ->once()
            ->with('Security: Admin action performed', \Mockery::type('array'));

        $this->request = Request::create('/admin/services', 'POST');
        $this->request->setUserResolver(function () use ($admin) {
            return $admin;
        });

        $this->middleware->handle($this->request, function () {
            return $this->response;
        });
    }

    public function test_middleware_logs_admin_barber_management_actions()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin);

        Log::shouldReceive('info')
            ->once()
            ->with('Security: Admin action performed', \Mockery::type('array'));

        $this->request = Request::create('/admin/barbers/1/approve', 'POST');
        $this->request->setUserResolver(function () use ($admin) {
            return $admin;
        });

        $this->middleware->handle($this->request, function () {
            return $this->response;
        });
    }

    public function test_middleware_logs_guest_requests_without_user()
    {
        Log::shouldReceive('warning')
            ->once()
            ->with('Security: Failed authentication attempt', \Mockery::type('array'));

        $this->response->setStatusCode(401);

        $this->middleware->handle($this->request, function () {
            return $this->response;
        });
    }

    public function test_middleware_logs_xss_attempts_from_guest()
    {
        Log::shouldReceive('warning')
            ->once()
            ->with('Security: Potential XSS attempt detected', \Mockery::type('array'));

        $this->request->merge([
            'search' => '<iframe src="evil.com"></iframe>'
        ]);

        $this->middleware->handle($this->request, function () {
            return $this->response;
        });
    }

    public function test_middleware_truncates_long_xss_payloads()
    {
        Log::shouldReceive('warning')
            ->once()
            ->with('Security: Potential XSS attempt detected', \Mockery::on(function ($data) {
                return strlen($data['input_value']) <= 100;
            }));

        $longPayload = str_repeat('<script>alert("xss")</script>', 50); // 1000+ characters
        $this->request->merge(['comment' => $longPayload]);

        $this->middleware->handle($this->request, function () {
            return $this->response;
        });
    }
}
