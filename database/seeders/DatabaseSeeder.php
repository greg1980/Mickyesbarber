<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Barber;
use App\Models\Transformation;
use App\Models\Booking;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        // Create a user and a barber
        $user = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt('password123'),
        ]);
        $barber = Barber::factory()->create(['user_id' => $user->id]);

        // Create bookings for this barber
        $bookings = Booking::factory()
            ->count(10)
            ->for($user, 'user')
            ->for($barber, 'barber')
            ->create();

        // Create transformations for this barber and these bookings
        foreach ($bookings as $booking) {
            Transformation::factory()
                ->for($user, 'user')
                ->for($barber, 'barber')
                ->for($booking, 'booking')
                ->create();
        }

        // Create specified admin user
        User::factory()->create([
            'name' => 'Sammy',
            'email' => 'sammy@yahoo.com',
            'password' => bcrypt('Gregdognba123@'),
            'role' => 'admin',
        ]);
    }
}
