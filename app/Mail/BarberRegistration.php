<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\Barber;

class BarberRegistration extends Mailable
{
    use Queueable, SerializesModels;

    public $barber;

    public function __construct(Barber $barber)
    {
        $this->barber = $barber;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Welcome to the MickyesBarbers Team!',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.barber-registration',
        );
    }
}
