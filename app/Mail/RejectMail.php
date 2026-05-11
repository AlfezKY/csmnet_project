<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class RejectMail extends Mailable
{
    use Queueable, SerializesModels;

    public $pelanggan;
    public $reason;

    public function __construct($pelanggan, $reason)
    {
        $this->pelanggan = $pelanggan;
        $this->reason = $reason;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Informasi Pendaftaran Layanan CSMNET',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.reject',
        );
    }
}
