<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Notification;
use App\Models\User;
use Carbon\Carbon;

class NotificationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get a test user (assuming you have at least one user in the database)
        $user = User::first();

        if (!$user) {
            $this->command->info('No users found. Please run UserSeeder first.');
            return;
        }

        // Create various test notifications
        $notifications = [
            [
                'type' => 'appointment',
                'title' => 'Upcoming Appointment',
                'message' => 'You have an appointment tomorrow at 2:00 PM with John Doe',
                'data' => [
                    'booking_id' => 1,
                    'booking_date' => Carbon::tomorrow()->format('Y-m-d'),
                    'booking_time' => '14:00:00',
                ],
                'created_at' => Carbon::now()->subHours(2),
            ],
            [
                'type' => 'payment',
                'title' => 'Payment Received',
                'message' => 'Your payment of $50.00 has been received for appointment #123',
                'data' => [
                    'amount' => 50.00,
                    'booking_id' => 123,
                ],
                'created_at' => Carbon::now()->subHours(5),
            ],
            [
                'type' => 'system',
                'title' => 'Welcome to Mickyes Coiffure!',
                'message' => 'Thank you for joining our platform. We\'re excited to have you!',
                'data' => null,
                'created_at' => Carbon::now()->subDays(1),
                'is_read' => true,
                'read_at' => Carbon::now()->subDays(1)->addMinutes(5),
            ],
            [
                'type' => 'appointment',
                'title' => 'Appointment Reminder',
                'message' => 'Don\'t forget your appointment today at 3:30 PM',
                'data' => [
                    'booking_id' => 2,
                    'booking_date' => Carbon::today()->format('Y-m-d'),
                    'booking_time' => '15:30:00',
                ],
                'created_at' => Carbon::now()->subMinutes(30),
            ],
            [
                'type' => 'system',
                'title' => 'New Feature Available',
                'message' => 'You can now upload before/after photos of your haircuts!',
                'data' => [
                    'feature' => 'photo_upload',
                ],
                'created_at' => Carbon::now()->subHours(1),
            ],
        ];

        foreach ($notifications as $notification) {
            Notification::create([
                'user_id' => $user->id,
                'type' => $notification['type'],
                'title' => $notification['title'],
                'message' => $notification['message'],
                'data' => $notification['data'],
                'is_read' => $notification['is_read'] ?? false,
                'read_at' => $notification['read_at'] ?? null,
                'created_at' => $notification['created_at'],
                'updated_at' => $notification['created_at'],
            ]);
        }

        $this->command->info('Test notifications created successfully!');
    }
}
