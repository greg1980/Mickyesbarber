<?php

namespace App\Console\Commands;

use App\Mail\AppointmentReminder;
use App\Models\Booking;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class SendAppointmentReminders extends Command
{
    protected $signature = 'appointments:send-reminders';
    protected $description = 'Send reminders for upcoming appointments';

    public function handle()
    {
        $now = Carbon::now();
        $oneHourLater = $now->copy()->addHour();
        $bookings = Booking::with('barber.user', 'user')
            ->where('status', 'confirmed')
            ->where(function($query) use ($now, $oneHourLater) {
                $query->where(function($q) use ($now, $oneHourLater) {
                    $q->where('booking_date', $now->toDateString())
                      ->whereTime('booking_time', '>=', $now->toTimeString())
                      ->whereTime('booking_time', '<=', $oneHourLater->toTimeString());
                });
            })
            ->get();

        foreach ($bookings as $booking) {
            // Send to customer
            if ($booking->user && $booking->user->email) {
                Mail::to($booking->user->email)->send(new AppointmentReminder($booking));
                $this->info("Reminder sent to customer for booking #{$booking->id}");
            }
            // Send to barber
            if ($booking->barber && $booking->barber->user && $booking->barber->user->email) {
                Mail::to($booking->barber->user->email)->send(new AppointmentReminder($booking));
                $this->info("Reminder sent to barber for booking #{$booking->id}");
            }
        }

        $this->info('Appointment reminders sent successfully.');
    }
}
