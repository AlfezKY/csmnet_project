<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TagihanMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $nama_pelanggan;
    public $nama_paket;
    public $harga;
    public $jatuh_tempo;

    public function __construct($nama_pelanggan, $nama_paket, $harga, $jatuh_tempo)
    {
        $this->nama_pelanggan = $nama_pelanggan;
        $this->nama_paket = $nama_paket;

        // Format harga jadi Rupiah rapi (misal: 330.000)
        $this->harga = number_format($harga, 0, ',', '.');

        // Tanggal biarin asli dari controller, kita format nanti di blade
        $this->jatuh_tempo = $jatuh_tempo;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Pengingat Tagihan Internet CSMNET',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.tagihan',
        );
    }
}
