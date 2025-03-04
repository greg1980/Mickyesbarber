<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Barber;

class BarberSeeder extends Seeder
{
    public function run(): void
    {
        $barbers = [
            [
                'name' => 'James Wilson',
                'bio' => 'Master barber with over 10 years of experience specializing in classic cuts and modern fades. Known for attention to detail and creating personalized styles.',
                'profile_photo' => 'barbers/james-wilson.jpg',
                'years_of_experience' => 10,
                'specialties' => ['Classic Cuts', 'Fades', 'Beard Trimming', 'Hot Towel Shave'],
                'is_available' => true,
            ],
            [
                'name' => 'Michael Rodriguez',
                'bio' => 'Creative barber passionate about modern styles and hair design. Expertise in precision fades and artistic patterns. Making clients look and feel their best since 2018.',
                'profile_photo' => 'barbers/michael-rodriguez.jpg',
                'years_of_experience' => 5,
                'specialties' => ['Modern Cuts', 'Hair Design', 'Razor Fades', 'Color Treatment'],
                'is_available' => true,
            ],
            [
                'name' => 'David Thompson',
                'bio' => 'Traditional barber bringing old-school techniques with a modern twist. Specializing in classic gentleman cuts and expert beard sculpting.',
                'profile_photo' => 'barbers/david-thompson.jpg',
                'years_of_experience' => 8,
                'specialties' => ['Traditional Cuts', 'Beard Sculpting', 'Hot Towel Shave', 'Classic Cuts'],
                'is_available' => true,
            ],
        ];

        foreach ($barbers as $barberData) {
            // Create a user account for each barber
            $user = User::create([
                'name' => $barberData['name'],
                'email' => strtolower(str_replace(' ', '.', $barberData['name'])) . '@mickyesbarbers.com',
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
            ]);

            // Create the barber profile
            Barber::create([
                'user_id' => $user->id,
                'name' => $barberData['name'],
                'bio' => $barberData['bio'],
                'profile_photo' => $barberData['profile_photo'],
                'years_of_experience' => $barberData['years_of_experience'],
                'specialties' => $barberData['specialties'],
                'is_available' => $barberData['is_available'],
            ]);
        }
    }
}
