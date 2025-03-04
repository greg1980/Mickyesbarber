<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Transformation;
use App\Models\Barber;

class TransformationSeeder extends Seeder
{
    public function run(): void
    {
        $barbers = Barber::all();

        foreach ($barbers as $barber) {
            // Add 5 transformations for each barber with varying ratings
            for ($i = 0; $i < 5; $i++) {
                Transformation::create([
                    'barber_id' => $barber->id,
                    'after_photo' => 'transformations/sample-' . rand(1, 5) . '.jpg',
                    'description' => 'Amazing transformation by ' . $barber->name,
                    'rating' => rand(4, 5), // Random rating between 4 and 5 for realistic high ratings
                    'review' => 'Great experience! Very professional and skilled barber.',
                    'created_at' => now()->subDays(rand(1, 30)), // Random date within last 30 days
                ]);
            }
        }
    }
}
