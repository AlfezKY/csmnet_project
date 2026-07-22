<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Pelanggan;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Mail\TagihanMail;
use App\Mail\NotificationMail; // Buat ngirim email suspend yang umum

class KirimTagihanOtomatis extends Command
{
    // Nama command dan deskripsinya
    protected $signature = 'tagihan:kirim-otomatis';
    protected $description = 'Kirim email pengingat (H-3 s/d H-1) dan suspend otomatis pada Hari H jatuh tempo';

    public function handle()
    {
        Log::info('Scheduler KirimTagihanOtomatis dijalankan pada ' . Carbon::now()->toDateTimeString());
        $today = Carbon::today();

        // Kita butuh tanggal H-3, H-2, H-1 dari hari ini.
        // Karena reminder dikirim SEBELUM jatuh tempo, berarti kita cari yang 
        // jatuh temponya 1, 2, atau 3 hari KE DEPAN.
        $h1 = $today->copy()->addDay()->format('Y-m-d');
        $h2 = $today->copy()->addDays(2)->format('Y-m-d');
        $h3 = $today->copy()->addDays(3)->format('Y-m-d');

        $todayFormatted = $today->format('Y-m-d');

        // AMBIL SEMUA PELANGGAN AKTIF YANG BELUM LUNAS
        $pelanggans = Pelanggan::with('paket')
            ->where('status', 'Active')
            ->where('status_pembayaran', 'Belum Lunas')
            ->get();

        if ($pelanggans->isEmpty()) {
            $this->info("Tidak ada tagihan yang perlu diproses hari ini.");
            return;
        }

        $countReminder = 0;
        $countSuspend = 0;

        foreach ($pelanggans as $plg) {
            if (!$plg->jatuh_tempo || !$plg->email) continue;

            $jatuhTempo = $plg->jatuh_tempo;

            // ==========================================
            // LOGIC 1: PENGINGAT (H-3, H-2, H-1)
            // ==========================================
            if (in_array($jatuhTempo, [$h1, $h2, $h3])) {
                $harga = $plg->paket->harga ?? 0;
                $paket = $plg->paket->nama_paket ?? 'Internet';

                try {
                    Mail::to($plg->email)->send(new TagihanMail(
                        $plg->nama_pelanggan,
                        $paket,
                        $harga,
                        $jatuhTempo
                    ));
                    $countReminder++;
                    $this->info("Pengingat (H-" . Carbon::parse($jatuhTempo)->diffInDays($today) . ") dikirim ke: {$plg->nama_pelanggan}");
                } catch (\Exception $e) {
                    Log::error("Gagal kirim email reminder ke {$plg->nama_pelanggan}: " . $e->getMessage());
                }
            }

            // ==========================================
            // LOGIC 2: SUSPEND OTOMATIS (HARI H JATUH TEMPO)
            // ==========================================
            elseif ($jatuhTempo === $todayFormatted || $jatuhTempo < $todayFormatted) {
                // Suspend Pelanggan
                $plg->status = 'Non Active';
                
                // Pastikan status pembayaran menjadi Belum Lunas
                $plg->status_pembayaran = 'Belum Lunas'; 
                
                // Unbind paket internet (set null agar kembali ke opsi "-- Pilih Paket Internet --")
                // Sesuaikan 'paket_id' dengan nama kolom foreign key paket di database kamu
                $plg->paket_id = null; 
                
                $plg->save();

                // Suspend User Login (jika ada) biar dia nggak bisa akses Client Portal
                if ($plg->user_id) {
                    User::where('id', $plg->user_id)->update(['status' => 'Non Active']);
                }

                // Kirim Notifikasi Penangguhan (pakai NotificationMail biasa, bukan struk TagihanMail)
                $pesanSuspend = "Halo kak **{$plg->nama_pelanggan}**,\n\nMohon maaf, layanan internet CSMNET dan akses akun Anda saat ini kami *
                *Tangguhkan Sementara (Non Aktif)** karena telah melewati batas waktu pembayaran jatuh tempo pada tanggal " .
                Carbon::parse($jatuhTempo)->translatedFormat('d F Y') . ".\n\nMohon segera melunasi tagihan agar layanan dapat
                kembali diaktifkan.";

                try {
                    Mail::to($plg->email)->send(new NotificationMail($pesanSuspend));
                    $countSuspend++;
                    $this->info("Akun disuspend otomatis: {$plg->nama_pelanggan}");
                } catch (\Exception $e) {
                    Log::error("Gagal kirim email suspend ke {$plg->nama_pelanggan}: " . $e->getMessage());
                }
            }
        }

        $this->info("Proses selesai. {$countReminder} Reminder dikirim, {$countSuspend} Akun disuspend.");
    }
}
