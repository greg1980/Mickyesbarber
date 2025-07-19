<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Barber;
use App\Models\Service;
use App\Models\Booking;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;

class BookingControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $user;
    protected $barber;
    protected $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create(['role' => 'customer']);
        $this->barber = Barber::factory()->create(['is_approved' => true]);
        $this->service = Service::factory()->create(['is_active' => true]);
    }

    public function test_user_can_view_booking_index()
    {
        $response = $this->actingAs($this->user)
            ->get('/bookings');

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->component('Booking/Index')
            ->has('bookings')
        );
    }

    public function test_user_can_view_create_booking_page()
    {
        $response = $this->actingAs($this->user)
            ->get('/bookings/create');

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->component('Booking/Create')
            ->has('barbers')
            ->has('services')
        );
    }

    public function test_user_can_create_booking()
    {
        $bookingData = [
            'barber_id' => $this->barber->id,
            'service_id' => $this->service->id,
            'booking_date' => now()->addDays(7)->toDateString(),
            'booking_time' => '14:00:00',
            'notes' => 'Test booking notes'
        ];

        $response = $this->actingAs($this->user)
            ->post('/bookings', $bookingData);

        $response->assertRedirect('/bookings');

        $this->assertDatabaseHas('bookings', [
            'user_id' => $this->user->id,
            'barber_id' => $this->barber->id,
            'service_id' => $this->service->id,
            'booking_date' => $bookingData['booking_date'],
            'booking_time' => $bookingData['booking_time'],
            'notes' => $bookingData['notes'],
            'status' => 'pending'
        ]);
    }

    public function test_user_cannot_create_booking_with_invalid_data()
    {
        $response = $this->actingAs($this->user)
            ->post('/bookings', []);

        $response->assertSessionHasErrors(['barber_id', 'service_id', 'booking_date', 'booking_time']);
    }

    public function test_user_cannot_create_booking_in_past()
    {
        $bookingData = [
            'barber_id' => $this->barber->id,
            'service_id' => $this->service->id,
            'booking_date' => now()->subDays(1)->toDateString(),
            'booking_time' => '14:00:00'
        ];

        $response = $this->actingAs($this->user)
            ->post('/bookings', $bookingData);

        $response->assertSessionHasErrors(['booking_date']);
    }

    public function test_user_can_view_booking_details()
    {
        $booking = Booking::factory()->create([
            'user_id' => $this->user->id,
            'barber_id' => $this->barber->id,
            'service_id' => $this->service->id
        ]);

        $response = $this->actingAs($this->user)
            ->get("/bookings/{$booking->id}");

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->component('Booking/Show')
            ->where('booking.id', $booking->id)
        );
    }

    public function test_user_cannot_view_other_users_booking()
    {
        $otherUser = User::factory()->create();
        $booking = Booking::factory()->create(['user_id' => $otherUser->id]);

        $response = $this->actingAs($this->user)
            ->get("/bookings/{$booking->id}");

        $response->assertStatus(403);
    }

    public function test_user_can_edit_booking()
    {
        $booking = Booking::factory()->create([
            'user_id' => $this->user->id,
            'barber_id' => $this->barber->id,
            'service_id' => $this->service->id,
            'status' => 'pending'
        ]);

        $response = $this->actingAs($this->user)
            ->get("/bookings/{$booking->id}/edit");

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->component('Booking/Edit')
            ->where('booking.id', $booking->id)
        );
    }

    public function test_user_can_update_booking()
    {
        $booking = Booking::factory()->create([
            'user_id' => $this->user->id,
            'barber_id' => $this->barber->id,
            'service_id' => $this->service->id,
            'status' => 'pending'
        ]);

        $updateData = [
            'booking_date' => now()->addDays(14)->toDateString(),
            'booking_time' => '15:00:00',
            'notes' => 'Updated booking notes'
        ];

        $response = $this->actingAs($this->user)
            ->put("/bookings/{$booking->id}", $updateData);

        $response->assertRedirect("/bookings/{$booking->id}");

        $this->assertDatabaseHas('bookings', [
            'id' => $booking->id,
            'booking_date' => $updateData['booking_date'],
            'booking_time' => $updateData['booking_time'],
            'notes' => $updateData['notes']
        ]);
    }

    public function test_user_cannot_update_completed_booking()
    {
        $booking = Booking::factory()->create([
            'user_id' => $this->user->id,
            'status' => 'completed'
        ]);

        $response = $this->actingAs($this->user)
            ->get("/bookings/{$booking->id}/edit");

        $response->assertStatus(403);
    }

    public function test_user_can_cancel_booking()
    {
        $booking = Booking::factory()->create([
            'user_id' => $this->user->id,
            'status' => 'pending'
        ]);

        $response = $this->actingAs($this->user)
            ->delete("/bookings/{$booking->id}");

        $response->assertRedirect('/bookings');

        $booking->refresh();
        $this->assertEquals('cancelled', $booking->status);
    }

    public function test_user_cannot_cancel_completed_booking()
    {
        $booking = Booking::factory()->create([
            'user_id' => $this->user->id,
            'status' => 'completed'
        ]);

        $response = $this->actingAs($this->user)
            ->delete("/bookings/{$booking->id}");

        $response->assertStatus(403);
    }

    public function test_user_can_get_available_times()
    {
        $date = now()->addDays(7)->toDateString();

        $response = $this->actingAs($this->user)
            ->getJson("/bookings/available-times?barber_id={$this->barber->id}&date={$date}");

        $response->assertStatus(200);
        $response->assertJsonStructure(['available_times']);
    }

    public function test_user_can_get_barber_availability()
    {
        $response = $this->actingAs($this->user)
            ->getJson("/bookings/barber-availability/{$this->barber->id}");

        $response->assertStatus(200);
        $response->assertJsonStructure(['availability']);
    }

    public function test_guest_cannot_access_booking_routes()
    {
        $routes = [
            'GET /bookings',
            'GET /bookings/create',
            'POST /bookings',
            'GET /bookings/1',
            'GET /bookings/1/edit',
            'PUT /bookings/1',
            'DELETE /bookings/1'
        ];

        foreach ($routes as $route) {
            [$method, $path] = explode(' ', $route);
            $response = $this->$method($path);
            $response->assertRedirect('/login');
        }
    }

    public function test_user_can_filter_bookings_by_status()
    {
        $pendingBooking = Booking::factory()->create([
            'user_id' => $this->user->id,
            'status' => 'pending'
        ]);
        $completedBooking = Booking::factory()->create([
            'user_id' => $this->user->id,
            'status' => 'completed'
        ]);

        $response = $this->actingAs($this->user)
            ->get('/bookings?status=pending');

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->component('Booking/Index')
            ->where('bookings.data.0.id', $pendingBooking->id)
        );
    }

    public function test_user_can_search_bookings()
    {
        $barber1 = Barber::factory()->create(['user_id' => User::factory()->create(['name' => 'John Barber'])->id]);
        $barber2 = Barber::factory()->create(['user_id' => User::factory()->create(['name' => 'Jane Barber'])->id]);

        $booking1 = Booking::factory()->create([
            'user_id' => $this->user->id,
            'barber_id' => $barber1->id
        ]);
        $booking2 = Booking::factory()->create([
            'user_id' => $this->user->id,
            'barber_id' => $barber2->id
        ]);

        $response = $this->actingAs($this->user)
            ->get('/bookings?search=John');

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->component('Booking/Index')
            ->where('bookings.data.0.id', $booking1->id)
        );
    }

    public function test_booking_requires_valid_barber()
    {
        $bookingData = [
            'barber_id' => 99999, // Non-existent barber
            'service_id' => $this->service->id,
            'booking_date' => now()->addDays(7)->toDateString(),
            'booking_time' => '14:00:00'
        ];

        $response = $this->actingAs($this->user)
            ->post('/bookings', $bookingData);

        $response->assertSessionHasErrors(['barber_id']);
    }

    public function test_booking_requires_valid_service()
    {
        $bookingData = [
            'barber_id' => $this->barber->id,
            'service_id' => 99999, // Non-existent service
            'booking_date' => now()->addDays(7)->toDateString(),
            'booking_time' => '14:00:00'
        ];

        $response = $this->actingAs($this->user)
            ->post('/bookings', $bookingData);

        $response->assertSessionHasErrors(['service_id']);
    }

    public function test_booking_prevents_double_booking()
    {
        $existingBooking = Booking::factory()->create([
            'barber_id' => $this->barber->id,
            'booking_date' => now()->addDays(7)->toDateString(),
            'booking_time' => '14:00:00',
            'status' => 'confirmed'
        ]);

        $bookingData = [
            'barber_id' => $this->barber->id,
            'service_id' => $this->service->id,
            'booking_date' => now()->addDays(7)->toDateString(),
            'booking_time' => '14:00:00'
        ];

        $response = $this->actingAs($this->user)
            ->post('/bookings', $bookingData);

        $response->assertSessionHasErrors(['booking_time']);
    }
}
