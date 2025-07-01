<?php

namespace App\Console\Commands;

use App\Mail\AppointmentReminder;
use App\Models\Booking;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;
use App\Models\Notification;

class SendAppointmentReminders extends Command
{
    protected $signature = 'appointments:send-reminders';
    protected $description = 'Send reminders for upcoming appointments';

    public function handle()
    {
        $now = Carbon::now();
        $oneHourLater = $now->copy()->addHour();
        $oneDayLater = $now->copy()->addDay();

        // 1 hour reminders
        $hourBookings = Booking::with('barber.user', 'user')
            ->where('status', 'confirmed')
            ->where(function($query) use ($now, $oneHourLater) {
                $query->where(function($q) use ($now, $oneHourLater) {
                    $q->where('booking_date', $now->toDateString())
                      ->whereTime('booking_time', '>=', $now->toTimeString())
                      ->whereTime('booking_time', '<=', $oneHourLater->toTimeString());
                });
            })
            ->get();

        // 1 day reminders
        $dayBookings = Booking::with('barber.user', 'user')
            ->where('status', 'confirmed')
            ->where('booking_date', $oneDayLater->toDateString())
            ->get();

        // Helper to send both email and in-app notification
        $sendReminder = function($booking, $user, $type) {
            $reminderType = $type === 'hour' ? 'reminder_1hr' : 'reminder_1day';
            $title = $type === 'hour' ? 'Appointment in 1 Hour' : 'Appointment Tomorrow';
            $message = $type === 'hour'
                ? sprintf('You have an appointment at %s.', Carbon::parse($booking->booking_time)->format('g:i A'))
                : sprintf('You have an appointment tomorrow at %s.', Carbon::parse($booking->booking_time)->format('g:i A'));

            // Check if notification already sent
            $alreadySent = Notification::where('user_id', $user->id)
                ->where('type', $reminderType)
                ->where('data->booking_id', $booking->id)
                ->exists();
            if ($alreadySent) return;

            // In-app notification
            Notification::create([
                'user_id' => $user->id,
                'type' => $reminderType,
                'title' => $title,
                'message' => $message,
                'data' => [
                    'booking_id' => $booking->id,
                    'booking_date' => $booking->booking_date,
                    'booking_time' => $booking->booking_time,
                ],
            ]);

            // Email
            if ($user->email) {
                Mail::to($user->email)->send(new \App\Mail\AppointmentReminder($booking));
            }
        };

        foreach ($hourBookings as $booking) {
            if ($booking->user) $sendReminder($booking, $booking->user, 'hour');
            if ($booking->barber && $booking->barber->user) $sendReminder($booking, $booking->barber->user, 'hour');
        }
        foreach ($dayBookings as $booking) {
            if ($booking->user) $sendReminder($booking, $booking->user, 'day');
            if ($booking->barber && $booking->barber->user) $sendReminder($booking, $booking->barber->user, 'day');
        }

        $this->info('Appointment reminders sent successfully.');
    }
}
