<?php

namespace Database\Factories;

use App\Models\Barber;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class BarberFactory extends Factory
{
    protected $model = Barber::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'bio' => $this->faker->sentence(),
            'is_approved' => true,
            'availability' => true,
        ];
    }
}
