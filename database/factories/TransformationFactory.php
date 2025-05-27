<?php

namespace Database\Factories;

use App\Models\Transformation;
use App\Models\User;
use App\Models\Barber;
use App\Models\Booking;
use Illuminate\Database\Eloquent\Factories\Factory;

class TransformationFactory extends Factory
{
    protected $model = Transformation::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'barber_id' => Barber::factory(),
            'booking_id' => Booking::factory(),
            'before_photo' => null,
            'after_photo' => null,
            'style' => $this->faker->word(),
            'review' => $this->faker->sentence(),
            'rating' => $this->faker->numberBetween(1, 5),
            'status' => 'completed',
            'rejection_reason' => null,
            'created_at' => $this->faker->dateTimeBetween('-12 months', 'now'),
            'updated_at' => now(),
        ];
    }
}
