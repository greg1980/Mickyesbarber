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
        $transformationImages = [
            [
                'before' => 'transformations/before-1.jpg',
                'after' => 'transformations/after-1.jpg',
                'style' => 'Classic Gentleman Cut'
            ],
            [
                'before' => 'transformations/before-2.jpg',
                'after' => 'transformations/after-2.jpg',
                'style' => 'Modern Fade'
            ]
        ];

        $randomTransformation = $this->faker->randomElement($transformationImages);

        return [
            'user_id' => User::factory(),
            'barber_id' => Barber::factory(),
            'booking_id' => Booking::factory(),
            'before_photo' => $randomTransformation['before'],
            'after_photo' => $randomTransformation['after'],
            'style' => $randomTransformation['style'],
            'review' => $this->faker->sentence(),
            'rating' => $this->faker->numberBetween(4, 5),
            'status' => 'completed',
            'rejection_reason' => null,
            'created_at' => $this->faker->dateTimeBetween('-12 months', 'now'),
            'updated_at' => now(),
        ];
    }
}
