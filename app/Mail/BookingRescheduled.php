<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\Booking;

class BookingRescheduled extends Mailable
{
    use Queueable, SerializesModels;

    public $booking;
    public $oldDate;
    public $oldTime;

    public function __construct(Booking $booking, array $oldDateTime)
    {
        $this->booking = $booking;
        $this->oldDate = $oldDateTime['date'];
        $this->oldTime = $oldDateTime['time'];
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Booking Rescheduled - MickyesBarbers',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.booking-rescheduled',
        );
    }
}
