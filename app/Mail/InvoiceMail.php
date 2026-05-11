<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class InvoiceMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $nama_pelanggan;
    public $nama_paket;
    public $rincian; // Ubah jadi variabel rincian (array)
    public $jatuh_tempo_baru;

    public function __construct($nama_pelanggan, $nama_paket, $rincian, $jatuh_tempo_baru)
    {
        $this->nama_pelanggan = $nama_pelanggan;
        $this->nama_paket = $nama_paket;
        $this->rincian = $rincian;
        $this->jatuh_tempo_baru = $jatuh_tempo_baru;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Bukti Pembayaran Layanan Internet CSMNET',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.invoice',
        );
    }
}
