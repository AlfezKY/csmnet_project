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

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ==========================================
        // 1. SEEDING ADMIN & OWNER
        // ==========================================
        User::create([
            'fullname'   => 'Super Administrator',
            'username'   => 'admin',
            'password'   => Hash::make('password'),
            'role'       => 'Admin',
            'status'     => 'Active',
            'created_by' => 'SYSTEM'
        ]);

        User::create([
            'fullname'   => 'Bapak Pemilik',
            'username'   => 'owner',
            'password'   => Hash::make('password'),
            'role'       => 'Owner',
            'status'     => 'Active',
            'created_by' => 'SYSTEM'
        ]);

        // ==========================================
        // 2. SEEDING PAKET INTERNET
        // ==========================================
        $pakets = [
            Paket::create(['nama_paket' => 'Hemat 10 Mbps', 'kecepatan' => '10 Mbps', 'harga' => 150000, 'status' => 'Active', 'is_show' => true]),
            Paket::create(['nama_paket' => 'Keluarga 20 Mbps', 'kecepatan' => '20 Mbps', 'harga' => 200000, 'status' => 'Active', 'is_show' => true]),
            Paket::create(['nama_paket' => 'Gamer 50 Mbps', 'kecepatan' => '50 Mbps', 'harga' => 350000, 'status' => 'Active', 'is_show' => true]),
            Paket::create(['nama_paket' => 'Sultan 100 Mbps', 'kecepatan' => '100 Mbps', 'harga' => 600000, 'status' => 'Active', 'is_show' => true]),
        ];

        // ==========================================
        // 3. SEEDING PELANGGAN (20 Orang)
        // ==========================================
        $pelanggans = [];
        $namaPelanggans = ['Budi', 'Andi', 'Siti', 'Joko', 'Dewi', 'Rina', 'Agus', 'Tono', 'Ayu', 'Rizky', 'Ivan', 'Reza', 'Putri', 'Sari', 'Eko', 'Dwi', 'Tri', 'Bayu', 'Gilang', 'Nia'];

        foreach ($namaPelanggans as $index => $nama) {
            $user = User::create([
                'fullname'   => $nama . ' Pelanggan',
                'username'   => strtolower($nama),
                'password'   => Hash::make('password'),
                'role'       => 'Pelanggan',
                'status'     => 'Active',
                'created_by' => 'SYSTEM'
            ]);

            // Random pilih paket, mayoritas milih paket 1 atau 2
            $paketPilihan = $pakets[mt_rand(0, 10) > 7 ? mt_rand(2, 3) : mt_rand(0, 1)];

            $pelanggans[] = Pelanggan::create([
                'user_id'           => $user->id,
                'paket_id'          => $paketPilihan->id,
                'nama_pelanggan'    => $nama . ' Pelanggan',
                'alamat'            => 'Jl. Dummy Blok A No. ' . ($index + 1),
                'no_wa'             => '0812' . mt_rand(10000000, 99999999),
                'jatuh_tempo'       => Carbon::now()->addDays(mt_rand(1, 28))->format('Y-m-d'),
                'status_pembayaran' => mt_rand(1, 10) > 2 ? 'Lunas' : 'Belum Lunas', // 80% Lunas
                'status'            => 'Active',
                'created_by'        => 'SYSTEM'
            ]);
        }

        // ==========================================
        // 4. SEEDING TRANSAKSI & PENGELUARAN (6 Bulan Terakhir)
        // ==========================================
        $kategoriPengeluaran = ['Perawatan Jaringan', 'Biaya Operasional Kantor', 'Listrik', 'Transportasi', 'Lainnya'];
        $now = Carbon::now();

        for ($i = 0; $i < 6; $i++) {
            $bulanEvaluasi = $now->copy()->subMonths($i);

            // --- PEMASUKAN ---
            // Asumsi 90% pelanggan bayar tiap bulan
            foreach ($pelanggans as $pelanggan) {
                if (mt_rand(1, 100) <= 90) {
                    Transaksi::create([
                        'pelanggan_id' => $pelanggan->id,
                        'tanggal'      => $bulanEvaluasi->copy()->setDay(mt_rand(1, 28))->format('Y-m-d'),
                        'jumlah'       => $pelanggan->paket->harga,
                        'created_by'   => 'admin'
                    ]);
                }
            }

            // --- PENGELUARAN RUTIN ---
            // Tagihan ISP Induk (Biasa gede nilainya)
            Pengeluaran::create([
                'tanggal'     => $bulanEvaluasi->copy()->startOfMonth()->addDays(5)->format('Y-m-d'),
                'kategori'    => 'Langganan ISP Induk',
                'deskripsi'   => 'Bayar Bandwidth Utama',
                'jumlah'      => 2000000, // 2 Juta
                'created_by'  => 'owner'
            ]);

            // Pengeluaran Random 3-5 kali sebulan
            $jmlPengeluaranRandom = mt_rand(3, 5);
            for ($j = 0; $j < $jmlPengeluaranRandom; $j++) {
                Pengeluaran::create([
                    'tanggal'     => $bulanEvaluasi->copy()->setDay(mt_rand(1, 28))->format('Y-m-d'),
                    'kategori'    => $kategoriPengeluaran[array_rand($kategoriPengeluaran)],
                    'deskripsi'   => 'Pengeluaran operasional dummy',
                    'jumlah'      => mt_rand(5, 50) * 10000, // Antara 50rb - 500rb
                    'created_by'  => 'admin'
                ]);
            }
        }

        // ==========================================
        // 5. SEEDING KOMPLAIN (Fokus Bulan Ini & Kemarin)
        // ==========================================
        $kategoriKomplain = ['Internet Mati', 'LOS Merah', 'Internet Lambat', 'Lain-lain'];
        $statusKomplain = ['Not Yet', 'In Progress', 'Done'];
        $priorityKomplain = ['Low', 'Medium', 'High'];

        // Bikin 25 komplain buat bulan ini (Biar dashboard bulan ini RAME)
        for ($k = 0; $k < 25; $k++) {
            Komplain::create([
                'pelanggan_id' => $pelanggans[array_rand($pelanggans)]->id,
                'tanggal'      => $now->copy()->subDays(mt_rand(0, 25))->format('Y-m-d'),
                'keluhan'      => 'Keluhan dummy pelanggan untuk testing chart',
                'kategori'     => $kategoriKomplain[array_rand($kategoriKomplain)],
                'priority'     => $priorityKomplain[array_rand($priorityKomplain)],
                // Perbanyak yang Done biar metriknya keliatan bagus
                'status'       => mt_rand(1, 10) > 3 ? 'Done' : $statusKomplain[array_rand($statusKomplain)],
                'created_by'   => 'client'
            ]);
        }

        // Bikin 15 komplain buat bulan lalu (Biar bisa di-filter ke bulan kemaren)
        for ($k = 0; $k < 15; $k++) {
            Komplain::create([
                'pelanggan_id' => $pelanggans[array_rand($pelanggans)]->id,
                'tanggal'      => $now->copy()->subMonth()->subDays(mt_rand(0, 25))->format('Y-m-d'),
                'keluhan'      => 'Keluhan lama bulan lalu',
                'kategori'     => $kategoriKomplain[array_rand($kategoriKomplain)],
                'priority'     => $priorityKomplain[array_rand($priorityKomplain)],
                'status'       => 'Done', // Asumsi bulan lalu udah beres semua
                'created_by'   => 'client'
            ]);
        }
    }
}
