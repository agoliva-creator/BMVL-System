<?php

namespace App\Mail;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class BookingReceipt extends Mailable
{
    use Queueable, SerializesModels;

    // Create a public variable to hold the booking data
    public $booking;

    public function __construct(Booking $booking)
    {
        // Pass the booking data into the class when it gets created
        $this->booking = $booking;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Your Booking Receipt - BMVL Sports',
        );
    }

    public function content(): Content
    {
        // This tells Laravel which Blade view to use for the email's HTML
        return new Content(
            view: 'emails.receipt',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}