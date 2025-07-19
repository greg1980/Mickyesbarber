<?php

namespace Tests\Unit\Models;

use Tests\TestCase;
use App\Models\Booking;
use App\Models\User;
use App\Models\Barber;
use App\Models\Service;
use Illuminate\Foundation\Testing\RefreshDatabase;

class BookingTest extends TestCase
{
    use RefreshDatabase;

    public function test_booking_can_be_created()
    {
        $user = User::factory()->create();
        $barber = Barber::factory()->create();
        $service = Service::factory()->create();

        $booking = Booking::factory()->create([
            'user_id' => $user->id,
            'barber_id' => $barber->id,
            'service_id' => $service->id,
            'booking_date' => '2024-01-15',
            'booking_time' => '14:00:00',
            'status' => 'pending'
        ]);

        $this->assertDatabaseHas('bookings', [
            'user_id' => $user->id,
            'barber_id' => $barber->id,
            'service_id' => $service->id,
            'status' => 'pending'
        ]);

        // Check date and time separately due to casting
        $this->assertEquals('2024-01-15', $booking->booking_date->toDateString());
        $this->assertEquals('14:00:00', $booking->booking_time->format('H:i:s'));
    }

    public function test_booking_has_user_relationship()
    {
        $user = User::factory()->create();
        $booking = Booking::factory()->create(['user_id' => $user->id]);

        $this->assertInstanceOf(User::class, $booking->user);
        $this->assertEquals($user->id, $booking->user->id);
    }

    public function test_booking_has_barber_relationship()
    {
        $barber = Barber::factory()->create();
        $booking = Booking::factory()->create(['barber_id' => $barber->id]);

        $this->assertInstanceOf(Barber::class, $booking->barber);
        $this->assertEquals($barber->id, $booking->barber->id);
    }

    public function test_booking_has_service_relationship()
    {
        $service = Service::factory()->create();
        $booking = Booking::factory()->create(['service_id' => $service->id]);

        $this->assertInstanceOf(Service::class, $booking->service);
        $this->assertEquals($service->id, $booking->service->id);
    }

    public function test_booking_has_transformation_relationship()
    {
        $booking = Booking::factory()->create();
        $transformation = \App\Models\Transformation::factory()->create(['booking_id' => $booking->id]);

        $this->assertInstanceOf(\App\Models\Transformation::class, $booking->transformation);
        $this->assertEquals($transformation->id, $booking->transformation->id);
    }

    public function test_booking_can_calculate_balance_amount()
    {
        $booking = Booking::factory()->create([
            'service_price' => 100.00,
            'amount_paid' => 25.00
        ]);

        $balance = $booking->calculateBalanceAmount();
        $this->assertEquals(75.00, $balance);
    }

    public function test_booking_requires_user_id()
    {
        $this->expectException(\Illuminate\Database\QueryException::class);
        Booking::factory()->create(['user_id' => null]);
    }

    public function test_booking_requires_barber_id()
    {
        $this->expectException(\Illuminate\Database\QueryException::class);
        Booking::factory()->create(['barber_id' => null]);
    }

    public function test_booking_requires_booking_date()
    {
        $this->expectException(\Illuminate\Database\QueryException::class);
        Booking::factory()->create(['booking_date' => null]);
    }

    public function test_booking_requires_booking_time()
    {
        $this->expectException(\Illuminate\Database\QueryException::class);
        Booking::factory()->create(['booking_time' => null]);
    }

    public function test_booking_casts_service_price_as_decimal()
    {
        $booking = Booking::factory()->create(['service_price' => 50.50]);

        // Decimal casting returns string in Laravel
        $this->assertIsString($booking->service_price);
        $this->assertEquals('50.50', $booking->service_price);
    }

    public function test_booking_casts_amount_paid_as_decimal()
    {
        $booking = Booking::factory()->create(['amount_paid' => 25.75]);

        // Decimal casting returns string in Laravel
        $this->assertIsString($booking->amount_paid);
        $this->assertEquals('25.75', $booking->amount_paid);
    }

    public function test_booking_casts_deposit_paid_as_boolean()
    {
        $booking = Booking::factory()->create(['deposit_paid' => true]);

        $this->assertIsBool($booking->deposit_paid);
        $this->assertTrue($booking->deposit_paid);
    }

    public function test_booking_casts_booking_date_as_date()
    {
        $booking = Booking::factory()->create(['booking_date' => '2024-01-15']);

        $this->assertInstanceOf(\Carbon\Carbon::class, $booking->booking_date);
        $this->assertEquals('2024-01-15', $booking->booking_date->toDateString());
    }
}
