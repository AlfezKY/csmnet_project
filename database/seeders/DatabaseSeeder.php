<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Paket;
use App\Models\Pelanggan;
use App\Models\Transaksi;
use App\Models\Pengeluaran;
use App\Models\Komplain;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use Faker\Factory as Faker;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create('id_ID');

        // Rentang waktu: 1 Agustus 2025 - 31 Agustus 2026
        $startDate = Carbon::create(2025, 8, 1, 0, 0, 0);
        $endDate = Carbon::create(2026, 8, 31, 23, 59, 59);
        $cutoffDate = Carbon::create(2026, 4, 21, 0, 0, 0); // Batas tanggal untuk status lunas

        // ==========================================
        // 1. SEEDING ADMIN & OWNER
        // ==========================================
        $tglAdmin = Carbon::create(2025, 7, 25, 8, 0, 0);
        
        User::create([
            'fullname'   => 'Super Administrator',
            'username'   => 'admin',
            'password'   => Hash::make('password'),
            'role'       => 'Admin',
            'status'     => 'Active',
            'created_by' => 'SYSTEM',
            'created_at' => $tglAdmin,
        ]);

        User::create([
            'fullname'   => 'Bapak Pemilik',
            'username'   => 'owner',
            'password'   => Hash::make('password'),
            'role'       => 'Owner',
            'status'     => 'Active',
            'created_by' => 'SYSTEM',
            'created_at' => $tglAdmin,
        ]);

        // ==========================================
        // 2. SEEDING PAKET INTERNET (Sesuai Referensi)
        // ==========================================
        $pakets = [
            Paket::create(['nama_paket' => 'Paket Hemat', 'kecepatan' => '5 Mbps', 'harga' => 165000, 'status' => 'Active', 'is_show' => true, 'keypoint' => 'Cocok untuk 1-2 perangkat, Browsing & Sosmed lancar', 'created_at' => $tglAdmin]),
            Paket::create(['nama_paket' => 'Paket Reguler', 'kecepatan' => '10 Mbps', 'harga' => 220000, 'status' => 'Active', 'is_show' => true, 'keypoint' => 'Ideal untuk keluarga kecil, Streaming HD, Sekolah Online', 'created_at' => $tglAdmin]),
            Paket::create(['nama_paket' => 'Paket Gamer', 'kecepatan' => '20 Mbps', 'harga' => 270000, 'status' => 'Active', 'is_show' => true, 'keypoint' => 'Gaming lancar, Streaming 4K tanpa buffering, WFH optimal', 'created_at' => $tglAdmin]),
            Paket::create(['nama_paket' => 'Paket Sultan', 'kecepatan' => '30 Mbps', 'harga' => 330000, 'status' => 'Active', 'is_show' => true, 'keypoint' => 'Koneksi dewa untuk heavy duty, Kantor skala kecil, Download cepat', 'created_at' => $tglAdmin]),
        ];

        // ==========================================
        // 3. SEEDING PELANGGAN (Angka Acak 150 - 250 Data)
        // ==========================================
        $pelanggans = [];
        $jumlahPelanggan = mt_rand(150, 250);

        for ($i = 0; $i < $jumlahPelanggan; $i++) {
            // Tanggal daftar acak antara Agustus 2025 - Agustus 2026
            $randomTimestamp = mt_rand($startDate->timestamp, $endDate->timestamp);
            $tglDaftar = Carbon::createFromTimestamp($randomTimestamp);

            $nama = $faker->name;
            $username = strtolower(preg_replace('/[^A-Za-z0-9]/', '', $nama)) . mt_rand(10, 999);

            $randStatus = mt_rand(1, 100);
            if ($randStatus <= 70) {
                $statusAkun = 'Active';
            } elseif ($randStatus <= 90) {
                $statusAkun = 'Non Active'; 
            } else {
                $statusAkun = 'Pending'; 
            }

            // Sync User status enum: Active / Non Active (Pending dipetakan ke Active sementara, di Pelanggan baru Pending)
            $userStatus = ($statusAkun == 'Pending') ? 'Active' : $statusAkun;

            $user = User::create([
                'fullname'   => $nama,
                'username'   => $username,
                'email'      => $faker->unique()->safeEmail,
                'password'   => Hash::make('password'),
                'role'       => 'Pelanggan',
                'status'     => $userStatus,
                'created_by' => 'SYSTEM',
                'created_at' => $tglDaftar,
            ]);

            $paketId = $pakets[mt_rand(0, 3)]->id;
            
            // Set Jatuh Tempo 1 Bulan setelah daftar (Jika tidak Pending)
            $jatuhTempo = null;
            if ($statusAkun != 'Pending') {
                // Jatuh tempo diacak untuk bulan berjalan
                $jatuhTempo = $tglDaftar->copy()->addMonths(mt_rand(1, 6)); 
            }

            // LOGIKA LUNAS: Jika jatuh tempo di atas 21 April 2026, WAJIB LUNAS
            if ($jatuhTempo && $jatuhTempo->greaterThan($cutoffDate)) {
                $statusPembayaran = 'Lunas';
            } else {
                $statusPembayaran = ($statusAkun == 'Active' && mt_rand(1, 100) > 20) ? 'Lunas' : 'Belum Lunas';
            }

            $pelanggan = Pelanggan::create([
                'user_id'           => $user->id,
                'paket_id'          => $paketId,
                'nama_pelanggan'    => $nama,
                'alamat'            => $faker->streetAddress . ', ' . $faker->city,
                'no_wa'             => '08' . mt_rand(11, 99) . mt_rand(1000000, 9999999),
                'jatuh_tempo'       => $jatuhTempo ? $jatuhTempo->format('Y-m-d') : null,
                'status_pembayaran' => $statusPembayaran,
                'status'            => $statusAkun,
                'created_by'        => 'SYSTEM',
                'created_at'        => $tglDaftar,
                'updated_at'        => $tglDaftar,
            ]);

            $pelanggans[] = $pelanggan;
        }

        // ==========================================
        // 4. SEEDING TRANSAKSI PEMASUKAN (Banyak Data agar Untung!)
        // ==========================================
        foreach ($pelanggans as $plg) {
            if ($plg->status == 'Pending') continue;

            $startBulan = Carbon::parse($plg->created_at)->startOfMonth();
            $endBulan = $plg->jatuh_tempo ? Carbon::parse($plg->jatuh_tempo)->startOfMonth() : $tglDaftar->copy()->addMonths(3)->startOfMonth();
            
            // Generate pembayaran untuk tiap bulan aktifnya (Membengkakkan Pemasukan)
            while ($startBulan->lessThanOrEqualTo($endBulan)) {
                if (mt_rand(1, 100) <= 95) { // 95% chance bayar
                    $tglBayar = $startBulan->copy()->addDays(mt_rand(1, 20));
                    Transaksi::create([
                        'pelanggan_id' => $plg->id,
                        'tanggal'      => $tglBayar->format('Y-m-d'),
                        'jumlah'       => $plg->paket->harga ?? 165000,
                        'created_by'   => 'admin',
                        'created_at'   => $tglBayar,
                    ]);
                }
                $startBulan->addMonth();
            }
        }

        // ==========================================
        // 5. SEEDING PENGELUARAN (120 - 180 Data, Dibuat Irit agar Cashflow Positif)
        // ==========================================
        $kategoriList = [
            'Langganan ISP Induk', 'Pembelian Perangkat (Router, Modem, Kabel)', 
            'Perawatan Jaringan', 'Gaji Karyawan / Teknisi', 'Biaya Operasional Kantor', 
            'Listrik', 'Internet Kantor', 'Transportasi', 'Lainnya'
        ];

        $jumlahPengeluaran = mt_rand(120, 180);

        for ($i = 0; $i < $jumlahPengeluaran; $i++) {
            $randomTimestamp = mt_rand($startDate->timestamp, $endDate->timestamp);
            $tglAcak = Carbon::createFromTimestamp($randomTimestamp);
            $kategori = $kategoriList[array_rand($kategoriList)];

            // Setup Pengeluaran dibuat jauh lebih kecil dari total Pemasukan Transaksi (agar profit)
            if ($kategori == 'Langganan ISP Induk' || $kategori == 'Gaji Karyawan / Teknisi') {
                $nominal = mt_rand(10, 25) * 100000; // Cuma 1jt - 2.5jt
            } else {
                $nominal = mt_rand(1, 20) * 10000; // Cuma 10rb - 200rb
            }

            Pengeluaran::create([
                'tanggal'     => $tglAcak->format('Y-m-d'),
                'kategori'    => $kategori,
                'deskripsi'   => 'Pengeluaran untuk ' . $kategori,
                'jumlah'      => $nominal,
                'created_by'  => 'admin',
                'created_at'  => $tglAcak
            ]);
        }

        // ==========================================
        // 6. SEEDING KOMPLAIN (Angka Acak 100 - 250 Data)
        // ==========================================
        $kategoriKomplain = ['Kabel Putus', 'Modem LOS Merah', 'Internet Lambat/RTO', 'Ganti Password WiFi', 'Pembayaran/Tagihan', 'Lain-lain'];
        $priorityKomplain = ['Low', 'Medium', 'High'];
        $statusKomplain = ['Not Yet', 'In Progress', 'Done'];

        $jumlahKomplain = mt_rand(100, 250);

        for ($i = 0; $i < $jumlahKomplain; $i++) {
            $randomTimestamp = mt_rand($startDate->timestamp, $endDate->timestamp);
            $tglKomplain = Carbon::createFromTimestamp($randomTimestamp);

            // Kebanyakan diset 'Done' biar KPI dashboardnya kelihatan bagus
            $randStatus = mt_rand(1, 100);
            if ($randStatus <= 80) {
                $status = 'Done';
            } elseif ($randStatus <= 90) {
                $status = 'In Progress';
            } else {
                $status = 'Not Yet';
            }

            Komplain::create([
                'pelanggan_id' => $pelanggans[mt_rand(0, count($pelanggans) - 1)]->id,
                'tanggal'      => $tglKomplain->format('Y-m-d'),
                'keluhan'      => 'Keluhan pelanggan test ' . $faker->sentence(5),
                'kategori'     => $kategoriKomplain[array_rand($kategoriKomplain)],
                'priority'     => $priorityKomplain[array_rand($priorityKomplain)],
                'status'       => $status,
                'created_by'   => 'client',
                'created_at'   => $tglKomplain,
                'updated_at'   => $status == 'Done' ? $tglKomplain->copy()->addHours(mt_rand(1, 48)) : $tglKomplain
            ]);
        }
    }
}