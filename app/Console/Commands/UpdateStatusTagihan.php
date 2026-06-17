<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Pelanggan;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class UpdateStatusTagihan extends Command
{
    // Nama perintah untuk dijalankan di terminal
    protected $signature = 'tagihan:update-status';
    protected $description = 'Otomatis ubah status Lunas menjadi Belum Lunas pada H-3 jatuh tempo';

    public function handle()
    {
        $today = Carbon::today();
        Log::info('WOY CRON JALAN NIH BROK!');
        // Kita cari tanggal H+3 dari hari ini.
        // Karena kita mau nagih di H-3, berarti kita buka gerbang tagihannya 3 hari sebelum jatuh tempo.
        $h3 = $today->copy()->addDays(3)->format('Y-m-d');

        // Cari pelanggan aktif & lunas, yang jatuh temponya 3 hari lagi (atau kurang dari itu)
        $pelanggans = Pelanggan::where('status', 'Active')
            ->where('status_pembayaran', 'Lunas')
            ->whereDate('jatuh_tempo', '<=', $h3)
            ->get();

        $count = 0;

        foreach ($pelanggans as $plg) {
            $plg->update([
                'status_pembayaran' => 'Belum Lunas',
                'updated_by' => 'SYSTEM-CRON'
            ]);
            $count++;
        }

        if ($count > 0) {
            $this->info("Berhasil membuka tagihan untuk {$count} pelanggan (H-3 Jatuh Tempo).");
            Log::info("CRON UPDATE TAGIHAN: {$count} pelanggan diubah ke Belum Lunas (Masuk list tagihan H-3)");
        } else {
            $this->info("Aman. Tidak ada pelanggan yang memasuki H-3 jatuh tempo hari ini.");
        }
    }
}
