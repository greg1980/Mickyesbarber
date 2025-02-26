<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\Transformation;

class TransformationAdded extends Mailable
{
    use Queueable, SerializesModels;

    public $transformation;

    public function __construct(Transformation $transformation)
    {
        $this->transformation = $transformation;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'New Transformation Added - MickyesBarbers',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.transformation-added',
        );
    }
}
