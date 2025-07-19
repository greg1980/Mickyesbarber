<?php

namespace Database\Factories;

use App\Models\Service;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Service>
 */
class ServiceFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Service::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->words(2, true),
            'slug' => $this->faker->slug(),
            'description' => $this->faker->paragraph(),
            'price' => $this->faker->randomFloat(2, 10, 100),
            'icon' => $this->faker->word(),
            'image' => $this->faker->imageUrl(),
            'is_active' => true,
            'sort_order' => $this->faker->numberBetween(1, 100),
        ];
    }

    /**
     * Indicate that the service is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }

    /**
     * Create a classic haircut service.
     */
    public function classicHaircut(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Classic Haircut',
            'slug' => 'classic-haircut',
            'description' => 'Professional classic haircut service',
            'price' => 25.00,
            'icon' => 'ScissorsIcon',
        ]);
    }

    /**
     * Create a beard grooming service.
     */
    public function beardGrooming(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Beard Grooming',
            'slug' => 'beard-grooming',
            'description' => 'Professional beard trimming and grooming',
            'price' => 15.00,
            'icon' => 'BeardIcon',
        ]);
    }
}
