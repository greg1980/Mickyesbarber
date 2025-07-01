<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Service;

class ServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $services = [
            [
                'name' => 'Classic Haircut',
                'slug' => 'classic-cut',
                'description' => 'From fades to tapers, textured crops to classic gentleman\'s cuts. Our experienced barbers deliver precision cuts that enhance your natural style.',
                'price' => 30.00,
                'icon' => 'ScissorsIcon',
                'image' => '/images/services/classic-cut.jpg',
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'name' => 'Kids Haircut',
                'slug' => 'kids-cut',
                'description' => 'Specialized in making children feel comfortable and confident. We create fun, age-appropriate styles in a welcoming environment.',
                'price' => 25.00,
                'icon' => 'UserGroupIcon',
                'image' => '/images/services/kids-cut.jpg',
                'is_active' => true,
                'sort_order' => 2,
            ],
            [
                'name' => 'Hair Coloring',
                'slug' => 'hair-color',
                'description' => 'Express yourself with our professional coloring services. From subtle highlights to bold fashion colors, we help you achieve your desired look.',
                'price' => 45.00,
                'icon' => 'SwatchIcon',
                'image' => '/images/services/hair-color.jpg',
                'is_active' => true,
                'sort_order' => 3,
            ],
            [
                'name' => 'Beard Grooming',
                'slug' => 'beard-grooming',
                'description' => 'Complete beard care including trimming, shaping, and hot towel treatments. Keep your facial hair looking sharp and well-maintained.',
                'price' => 20.00,
                'icon' => 'SparklesIcon',
                'image' => '/images/services/beard-grooming.jpg',
                'is_active' => true,
                'sort_order' => 4,
            ],
            [
                'name' => 'Mobile Service',
                'slug' => 'mobile-service',
                'description' => 'Can\'t make it to the shop? Our professional barbers come to you. Enjoy the same high-quality service in the comfort of your home.',
                'price' => 50.00,
                'icon' => 'HomeIcon',
                'image' => '/images/services/home-service.jpg',
                'is_active' => true,
                'sort_order' => 5,
            ],
            [
                'name' => 'Special Treatment',
                'slug' => 'special-treatment',
                'description' => 'Pamper yourself with our premium treatments. From hot towel treatments to scalp massages, we offer services that go beyond the basics.',
                'price' => 35.00,
                'icon' => 'SparklesIcon',
                'image' => '/images/services/special-treatment.jpg',
                'is_active' => true,
                'sort_order' => 6,
            ],
        ];

        foreach ($services as $service) {
            Service::create($service);
        }
    }
}
