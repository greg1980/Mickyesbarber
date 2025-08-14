<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\BusinessHours;

class BusinessHoursSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear existing business hours
        BusinessHours::truncate();

        // Create default business hours
        $defaultHours = [
            [
                'day_of_week' => 'monday',
                'open_time' => '10:00',
                'close_time' => '18:00',
                'is_open' => true,
                'display_text' => null,
                'sort_order' => 1
            ],
            [
                'day_of_week' => 'tuesday',
                'open_time' => '10:00',
                'close_time' => '18:00',
                'is_open' => true,
                'display_text' => null,
                'sort_order' => 2
            ],
            [
                'day_of_week' => 'wednesday',
                'open_time' => '10:00',
                'close_time' => '18:00',
                'is_open' => true,
                'display_text' => null,
                'sort_order' => 3
            ],
            [
                'day_of_week' => 'thursday',
                'open_time' => '10:00',
                'close_time' => '18:00',
                'is_open' => true,
                'display_text' => null,
                'sort_order' => 4
            ],
            [
                'day_of_week' => 'friday',
                'open_time' => '10:00',
                'close_time' => '19:00',
                'is_open' => true,
                'display_text' => null,
                'sort_order' => 5
            ],
            [
                'day_of_week' => 'saturday',
                'open_time' => '10:00',
                'close_time' => '19:00',
                'is_open' => true,
                'display_text' => null,
                'sort_order' => 6
            ],
            [
                'day_of_week' => 'sunday',
                'open_time' => null,
                'close_time' => null,
                'is_open' => false,
                'display_text' => 'Closed',
                'sort_order' => 7
            ],
        ];

        foreach ($defaultHours as $hour) {
            BusinessHours::create($hour);
        }

        $this->command->info('Business hours seeded successfully!');
    }
}
