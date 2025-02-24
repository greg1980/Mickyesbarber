<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\Booking;

class BookingRescheduled extends Mailable
{
    use Queueable, SerializesModels;

    public $booking;
    public $oldDate;
    public $oldTime;

    public function __construct(Booking $booking, $oldDate, $oldTime)
    {
        $this->booking = $booking;
        $this->oldDate = $oldDate;
        $this->oldTime = $oldTime;
    }

    public function build()
    {
        return $this->markdown('emails.bookings.rescheduled')
                    ->subject('Booking Rescheduled Successfully');
    }
}
