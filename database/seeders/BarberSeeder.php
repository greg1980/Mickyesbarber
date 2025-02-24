<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Barber;

class BarberSeeder extends Seeder
{
    public function run()
    {
        $barbers = [
            [
                'name' => 'John Smith',
                'bio' => 'Expert in modern cuts and fades with 5 years of experience',
                'profile_photo' => 'https://randomuser.me/api/portraits/men/32.jpg',
                'years_of_experience' => 5,
                'specialties' => ['Fades', 'Modern Cuts', 'Beard Trimming']
            ],
            [
                'name' => 'Mike Johnson',
                'bio' => 'Specializing in classic cuts and styling',
                'profile_photo' => 'https://randomuser.me/api/portraits/men/43.jpg',
                'years_of_experience' => 8,
                'specialties' => ['Classic Cuts', 'Styling', 'Hot Towel Shave']
            ],
            [
                'name' => 'David Wilson',
                'bio' => 'Master barber with expertise in traditional techniques',
                'profile_photo' => 'https://randomuser.me/api/portraits/men/55.jpg',
                'years_of_experience' => 12,
                'specialties' => ['Traditional Cuts', 'Razor Fades', 'Beard Sculpting']
            ],
            [
                'name' => 'James Brown',
                'bio' => 'Contemporary stylist with a passion for modern trends',
                'profile_photo' => 'https://randomuser.me/api/portraits/men/67.jpg',
                'years_of_experience' => 6,
                'specialties' => ['Modern Styles', 'Hair Design', 'Color Treatment']
            ]
        ];

        foreach ($barbers as $barber) {
            Barber::create($barber);
        }
    }
}
