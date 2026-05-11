<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ApproveMail extends Mailable
{
    use Queueable, SerializesModels;

    public $pelanggan;

    public function __construct($pelanggan)
    {
        $this->pelanggan = $pelanggan;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Pendaftaran CSMNET Berhasil Diaktifkan!',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.approve',
        );
    }
}
