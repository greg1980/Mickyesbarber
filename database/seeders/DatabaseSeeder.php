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

        // Create a barber user
        $barberUser = User::factory()->create([
            'name' => 'Test Barber',
            'email' => 'test@example.com',
            'password' => bcrypt('password123'),
            'role' => 'barber',
        ]);
        $barber = Barber::factory()->create(['user_id' => $barberUser->id]);

        // Create a customer user
        $customerUser = User::factory()->create([
            'name' => 'Test Customer',
            'email' => 'customer@example.com',
            'password' => bcrypt('password123'),
            'role' => 'customer',
        ]);

        // Create bookings for this barber with the customer
        $bookings = Booking::factory()
            ->count(10)
            ->for($customerUser, 'user')  // Use customer as the booking user
            ->for($barber, 'barber')
            ->create(['status' => 'completed']); // Ensure bookings are completed

        // Create transformations for this barber and these bookings
        foreach ($bookings as $booking) {
            Transformation::factory()
                ->for($customerUser, 'user')  // Use customer as the transformation user
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

        $this->call([
            NotificationSeeder::class,
            ServiceSeeder::class,
            BookingSeeder::class,
        ]);
    }
}
