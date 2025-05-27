<?php

namespace Database\Factories;

use App\Models\Booking;
use App\Models\User;
use App\Models\Barber;
use Illuminate\Database\Eloquent\Factories\Factory;

class BookingFactory extends Factory
{
    protected $model = Booking::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'barber_id' => Barber::factory(),
            'booking_date' => $this->faker->date(),
            'booking_time' => $this->faker->time('H:i'),
            'service_price' => $this->faker->numberBetween(20, 100),
            'deposit_amount' => $this->faker->numberBetween(5, 25),
            'balance_amount' => $this->faker->numberBetween(5, 75),
            'status' => 'confirmed',
            'notes' => $this->faker->sentence(),
        ];
    }
}
