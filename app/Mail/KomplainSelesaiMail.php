<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class KomplainSelesaiMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $komplain;

    public function __construct($komplain)
    {
        $this->komplain = $komplain;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Pemberitahuan: Keluhan Anda Telah Diselesaikan - CSMNET',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.komplain_selesai',
        );
    }
}
