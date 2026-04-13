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
    protected $description = 'Otomatis ubah status Lunas menjadi Belum Lunas jika sudah masuk tanggal jatuh tempo';

    public function handle()
    {
        $today = Carbon::today()->format('Y-m-d');

        // Cari pelanggan aktif & lunas, yang jatuh temponya HARI INI atau SEBELUM HARI INI
        // (Pakai '<=' buat jaga-jaga kalau kemarin server sempet mati, jadi nggak kelewat)
        $pelanggans = Pelanggan::where('status', 'Active')
            ->where('status_pembayaran', 'Lunas')
            ->whereDate('jatuh_tempo', '<=', $today)
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
            $this->info("Berhasil mengubah {$count} pelanggan menjadi Belum Lunas.");
            Log::info("CRON UPDATE TAGIHAN: {$count} pelanggan diubah ke Belum Lunas pada {$today}");
        } else {
            $this->info("Aman. Tidak ada pelanggan yang memasuki jatuh tempo hari ini.");
        }
    }
}
