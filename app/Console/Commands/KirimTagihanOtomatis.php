<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Pelanggan;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class KirimTagihanOtomatis extends Command
{
    // Nama perintah yang nanti dijalankan (php artisan tagihan:kirim-otomatis)
    protected $signature = 'tagihan:kirim-otomatis';
    protected $description = 'Kirim pengingat WhatsApp otomatis untuk pelanggan yang jatuh tempo hari ini';

    public function handle()
    {
        $today = Carbon::today()->format('Y-m-d');

        // 1. Ambil pelanggan aktif yang belum lunas dan jatuh temponya HARI INI
        $pelanggans = Pelanggan::with('paket')
            ->where('status', 'Active')
            ->where('status_pembayaran', 'Belum Lunas')
            ->whereDate('jatuh_tempo', $today)
            ->get();

        if ($pelanggans->isEmpty()) {
            $this->info("Tidak ada tagihan jatuh tempo hari ini ({$today}).");
            return;
        }

        $domain = env('WABLAS_DOMAIN');
        $token = env('WABLAS_TOKEN');

        foreach ($pelanggans as $plg) {
            // Format Nomor HP
            $phone = $plg->no_wa;
            if (str_starts_with($phone, '0')) {
                $phone = '62' . substr($phone, 1);
            }

            $harga = number_format($plg->paket->harga ?? 0, 0, ',', '.');
            $paket = $plg->paket->nama_paket ?? 'Internet';

            $pesan = "Halo kak *{$plg->nama_pelanggan}*, ini adalah pengingat otomatis. Tagihan internet CSMNET paket *{$paket}* sebesar *Rp {$harga}* jatuh tempo pada hari ini. Mohon segera melakukan pembayaran. Terima kasih 🙏";

            try {
                $response = Http::withHeaders(['Authorization' => $token])
                    ->post("{$domain}/api/send-message", [
                        'phone'   => $phone,
                        'message' => $pesan,
                    ]);

                if ($response->successful()) {
                    $this->info("Berhasil mengirim ke: {$plg->nama_pelanggan}");
                } else {
                    Log::error("Gagal mengirim WA ke {$plg->nama_pelanggan}: " . $response->body());
                }
            } catch (\Exception $e) {
                Log::error("Error sistem WA: " . $e->getMessage());
            }
        }

        $this->info("Proses pengiriman otomatis selesai.");
    }
}
