<?php

namespace Tests\Feature\Admin;

use Tests\TestCase;
use App\Models\User;
use App\Models\Barber;
use App\Models\Booking;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;

class AdminUserControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['role' => 'admin']);
    }

    public function test_admin_can_view_users_index()
    {
        $response = $this->actingAs($this->admin)
            ->get('/admin/users');

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->component('Admin/Users')
            ->has('users')
            ->has('totalUsers')
            ->has('activeUsers')
            ->has('inactiveUsers')
        );
    }

    public function test_admin_can_search_users()
    {
        $user1 = User::factory()->create(['name' => 'John Doe']);
        $user2 = User::factory()->create(['name' => 'Jane Smith']);

        $response = $this->actingAs($this->admin)
            ->get('/admin/users?search=John');

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->component('Admin/Users')
            ->where('users.data.0.name', 'John Doe')
        );
    }

    public function test_admin_can_view_trashed_users()
    {
        $user = User::factory()->create();
        $user->delete();

        $response = $this->actingAs($this->admin)
            ->get('/admin/users?trashed=1');

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->component('Admin/Users')
            ->where('users.data.0.id', $user->id)
        );
    }

    public function test_admin_can_get_completed_bookings_count()
    {
        $user = User::factory()->create();
        Booking::factory()->count(3)->create([
            'user_id' => $user->id,
            'status' => 'completed'
        ]);
        Booking::factory()->create([
            'user_id' => $user->id,
            'status' => 'pending'
        ]);

        $response = $this->actingAs($this->admin)
            ->getJson("/admin/users/{$user->id}/completed-bookings");

        $response->assertStatus(200);
        $response->assertJson(['count' => 3]);
    }

    public function test_admin_can_get_user_stats()
    {
        $user = User::factory()->create();
        $lastBooking = Booking::factory()->create([
            'user_id' => $user->id,
            'status' => 'completed',
            'booking_date' => now()->subDays(5)
        ]);
        $upcomingBooking = Booking::factory()->create([
            'user_id' => $user->id,
            'status' => 'confirmed',
            'booking_date' => now()->addDays(7)
        ]);

        $response = $this->actingAs($this->admin)
            ->getJson("/admin/users/{$user->id}/stats");

        $response->assertStatus(200);
        $response->assertJson([
            'last_visited' => $lastBooking->booking_date,
            'upcoming_bookings' => 1
        ]);
    }

    public function test_admin_can_delete_user()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($this->admin)
            ->deleteJson("/admin/users/{$user->id}");

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);
        $this->assertSoftDeleted($user);
    }

    public function test_admin_can_restore_user()
    {
        $user = User::factory()->create();
        $user->delete();

        $response = $this->actingAs($this->admin)
            ->postJson("/admin/users/{$user->id}/restore");

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);
        $this->assertNotSoftDeleted($user);
    }

    public function test_admin_can_get_user_growth_data()
    {
        // Create users over the last few months
        User::factory()->count(5)->create([
            'created_at' => now()->subMonths(2)
        ]);
        User::factory()->count(3)->create([
            'created_at' => now()->subMonth()
        ]);
        User::factory()->count(2)->create([
            'created_at' => now()
        ]);

        $response = $this->actingAs($this->admin)
            ->getJson('/admin/users/growth');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            '*' => ['month', 'count']
        ]);
    }

    public function test_admin_can_get_barber_schedule()
    {
        $barber = Barber::factory()->create();
        $schedule = \App\Models\BarberAvailabilitySchedule::factory()->create([
            'barber_id' => $barber->id,
            'day_of_week' => 'monday',
            'start_time' => '09:00:00',
            'end_time' => '17:00:00',
            'is_available' => true
        ]);

        $response = $this->actingAs($this->admin)
            ->getJson("/admin/barbers/{$barber->id}/schedule");

        $response->assertStatus(200);
        $response->assertJson([
            [
                'day_of_week' => 'monday',
                'start_time' => '09:00:00',
                'end_time' => '17:00:00',
                'is_available' => true
            ]
        ]);
    }

    public function test_admin_can_get_all_barbers()
    {
        $approvedBarber = Barber::factory()->create(['is_approved' => true]);
        $pendingBarber = Barber::factory()->create(['is_approved' => false]);

        $response = $this->actingAs($this->admin)
            ->getJson('/admin/barbers');

        $response->assertStatus(200);
        $response->assertJsonStructure(['barbers']);
        $response->assertJsonCount(1, 'barbers'); // Only approved barbers
    }

    public function test_admin_can_get_barber_booking_stats()
    {
        $barber = Barber::factory()->create();
        Booking::factory()->count(3)->create([
            'barber_id' => $barber->id,
            'status' => 'completed'
        ]);
        Booking::factory()->count(2)->create([
            'barber_id' => $barber->id,
            'status' => 'cancelled'
        ]);

        $response = $this->actingAs($this->admin)
            ->getJson("/admin/barbers/{$barber->id}/booking-stats");

        $response->assertStatus(200);
        $response->assertJson([
            'completed' => 3,
            'cancelled' => 2,
            'total' => 5
        ]);
    }

    public function test_admin_can_get_barber_bookings()
    {
        $barber = Barber::factory()->create();
        $user = User::factory()->create();
        $booking = Booking::factory()->create([
            'barber_id' => $barber->id,
            'user_id' => $user->id
        ]);

        $response = $this->actingAs($this->admin)
            ->getJson("/admin/barbers/{$barber->id}/bookings");

        $response->assertStatus(200);
        $response->assertJsonStructure(['data']);
        $response->assertJson([
            'data' => [
                [
                    'id' => $booking->id,
                    'customer_name' => $user->name
                ]
            ]
        ]);
    }

    public function test_admin_can_revoke_barber_approval()
    {
        $barber = Barber::factory()->create(['is_approved' => true]);
        $user = $barber->user;

        $response = $this->actingAs($this->admin)
            ->postJson("/admin/barbers/{$barber->id}/revoke-approval");

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $barber->refresh();
        $user->refresh();

        $this->assertFalse($barber->is_approved);
        $this->assertEquals('customer', $user->role);
    }

    public function test_admin_can_get_all_barbers_for_management()
    {
        $barber1 = Barber::factory()->create(['is_approved' => true]);
        $barber2 = Barber::factory()->create(['is_approved' => false]);

        $response = $this->actingAs($this->admin)
            ->getJson('/admin/barbers/management');

        $response->assertStatus(200);
        $response->assertJsonStructure(['barbers']);
        $response->assertJsonCount(2, 'barbers'); // All barbers regardless of approval status
    }

    public function test_non_admin_cannot_access_admin_routes()
    {
        $user = User::factory()->create(['role' => 'customer']);

        $response = $this->actingAs($user)
            ->get('/admin/users');

        $response->assertStatus(403);
    }

    public function test_guest_cannot_access_admin_routes()
    {
        $response = $this->get('/admin/users');

        $response->assertRedirect('/login');
    }

    public function test_admin_can_paginate_users()
    {
        User::factory()->count(25)->create();

        $response = $this->actingAs($this->admin)
            ->get('/admin/users?perPage=10');

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->component('Admin/Users')
            ->where('users.per_page', 10)
            ->where('users.total', 26) // 25 + admin user
        );
    }

    public function test_admin_can_filter_users_by_status()
    {
        $verifiedUser = User::factory()->create(['email_verified_at' => now()]);
        $unverifiedUser = User::factory()->create(['email_verified_at' => null]);

        $response = $this->actingAs($this->admin)
            ->get('/admin/users');

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->component('Admin/Users')
            ->where('activeUsers', 2) // admin + verified user
            ->where('inactiveUsers', 1) // unverified user
        );
    }
}
