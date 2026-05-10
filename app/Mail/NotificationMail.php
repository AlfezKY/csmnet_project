<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

// Sangat disarankan menambahkan 'implements ShouldQueue'
class NotificationMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $messageText;

    public function __construct($messageText)
    {
        // Menerima data dari controller
        $this->messageText = $messageText;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Pemberitahuan Sistem - CSMNET',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.notification',
        );
    }
}
