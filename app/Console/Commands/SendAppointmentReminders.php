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
        $tomorrow = Carbon::tomorrow()->format('Y-m-d');
        $bookings = Booking::with('barber.user', 'user')
            ->where('booking_date', $tomorrow)
            ->where('status', 'confirmed')
            ->get();

        foreach ($bookings as $booking) {
            if ($booking->user && $booking->user->email) {
                Mail::to($booking->user->email)->send(new AppointmentReminder($booking));
                $this->info("Reminder sent for booking #{$booking->id}");
            }
        }

        $this->info('Appointment reminders sent successfully.');
    }
}
