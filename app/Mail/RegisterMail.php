<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class RegisterMail extends Mailable // Tambahin 'implements ShouldQueue' kalau nanti mau dipakein antrean
{
    use Queueable, SerializesModels;

    public $dataPelanggan;

    public function __construct($dataPelanggan)
    {
        $this->dataPelanggan = $dataPelanggan;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Detail Pendaftaran Layanan CSMNET',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.register',
        );
    }
}
