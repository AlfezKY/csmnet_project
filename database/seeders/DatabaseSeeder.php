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
        $tahun = 2026;

        // ==========================================
        // 1. SEEDING ADMIN & OWNER
        // ==========================================
        User::create([
            'fullname'   => 'Super Administrator',
            'username'   => 'admin',
            'password'   => Hash::make('password'),
            'role'       => 'Admin',
            'status'     => 'Active',
            'created_by' => 'SYSTEM',
            'created_at' => Carbon::create($tahun, 1, 1, 8, 0, 0),
        ]);

        User::create([
            'fullname'   => 'Bapak Pemilik',
            'username'   => 'owner',
            'password'   => Hash::make('password'),
            'role'       => 'Owner',
            'status'     => 'Active',
            'created_by' => 'SYSTEM',
            'created_at' => Carbon::create($tahun, 1, 1, 8, 0, 0),
        ]);

        // ==========================================
        // 2. SEEDING PAKET INTERNET (Sesuai Request)
        // ==========================================
        $pakets = [
            Paket::create(['nama_paket' => '5 Mbps', 'kecepatan' => '5 Mbps', 'harga' => 165000, 'status' => 'Active', 'is_show' => true, 'keypoint' => 'Cocok untuk 1-2 perangkat, Browsing & Sosmed lancar']),
            Paket::create(['nama_paket' => '10 Mbps', 'kecepatan' => '10 Mbps', 'harga' => 220000, 'status' => 'Active', 'is_show' => true, 'keypoint' => 'Ideal untuk keluarga kecil, Streaming HD, Sekolah Online']),
            Paket::create(['nama_paket' => '20 Mbps', 'kecepatan' => '20 Mbps', 'harga' => 270000, 'status' => 'Active', 'is_show' => true, 'keypoint' => 'Gaming lancar, Streaming 4K tanpa buffering, WFH optimal']),
            Paket::create(['nama_paket' => '30 Mbps', 'kecepatan' => '30 Mbps', 'harga' => 330000, 'status' => 'Active', 'is_show' => true, 'keypoint' => 'Koneksi dewa untuk heavy duty, Kantor skala kecil, Download cepat']),
        ];

        // ==========================================
        // 3. SEEDING PELANGGAN (Angka Acak 138 - 187 Orang)
        // ==========================================
        $pelanggans = [];
        $jumlahPelanggan = mt_rand(138, 187);

        for ($i = 0; $i < $jumlahPelanggan; $i++) {
            // Tanggal daftar acak sepanjang tahun 2026
            $tglDaftar = Carbon::create($tahun, mt_rand(1, 12), mt_rand(1, 28), mt_rand(8, 17), mt_rand(0, 59), mt_rand(0, 59));

            $nama = $faker->name;
            $username = strtolower(preg_replace('/[^A-Za-z0-9]/', '', $nama)) . mt_rand(10, 999);

            // Distribusi status akun yang realistis
            $randStatus = mt_rand(1, 100);
            if ($randStatus <= 82) {
                $statusAkun = 'Active';
            } elseif ($randStatus <= 92) {
                $statusAkun = 'Non Active'; // Udah putus langganan
            } else {
                $statusAkun = 'Pending'; // Baru daftar, belum dipasang
            }

            $user = User::create([
                'fullname'   => $nama,
                'username'   => $username,
                'email'      => $faker->unique()->safeEmail,
                'password'   => Hash::make('password'),
                'role'       => 'Pelanggan',
                'status'     => $statusAkun,
                'created_by' => 'SYSTEM',
                'created_at' => $tglDaftar,
            ]);

            // Pemilihan Paket (5Mbps & 10Mbps paling laris)
            $randPaket = mt_rand(1, 100);
            if ($randPaket <= 35) $paketId = $pakets[0]->id;
            elseif ($randPaket <= 70) $paketId = $pakets[1]->id;
            elseif ($randPaket <= 90) $paketId = $pakets[2]->id;
            else $paketId = $pakets[3]->id;

            $statusPembayaran = ($statusAkun == 'Active' && mt_rand(1, 100) > 15) ? 'Lunas' : 'Belum Lunas';

            $pelanggan = Pelanggan::create([
                'user_id'           => $user->id,
                'paket_id'          => $paketId,
                'nama_pelanggan'    => $nama,
                'alamat'            => $faker->streetAddress . ', RT 0' . mt_rand(1, 9) . '/RW 0' . mt_rand(1, 9) . ', ' . $faker->city,
                'no_wa'             => '08' . mt_rand(11, 99) . mt_rand(1000000, 9999999),
                'jatuh_tempo'       => $statusAkun == 'Active' ? $tglDaftar->copy()->addMonths(mt_rand(1, 6))->format('Y-m-d') : null,
                'status_pembayaran' => $statusPembayaran,
                'status'            => $statusAkun,
                'created_by'        => 'SYSTEM',
                'created_at'        => $tglDaftar,
                'updated_at'        => $tglDaftar,
            ]);

            $pelanggans[] = $pelanggan;
        }

        // ==========================================
        // 4. SEEDING TRANSAKSI PEMASUKAN
        // ==========================================
        // Ngisi transaksi berdasarkan umur registrasi pelanggan biar organiknya kelihatan
        foreach ($pelanggans as $plg) {
            // Kalau pending, berarti belum pernah bayar
            if ($plg->status == 'Pending') continue;

            $bulanDaftar = Carbon::parse($plg->created_at)->month;
            $tglJatuhTempo = Carbon::parse($plg->created_at)->day;

            // Berapa bulan dia aktif? Kalau Non Active, mungkin cuma tahan beberapa bulan
            $bulanAkhir = $plg->status == 'Non Active' ? min(12, $bulanDaftar + mt_rand(1, 5)) : 12;

            for ($bulan = $bulanDaftar; $bulan <= $bulanAkhir; $bulan++) {
                // 90% chance dia bayar tiap bulannya
                if (mt_rand(1, 100) <= 90) {
                    $tglBayar = Carbon::create($tahun, $bulan, $tglJatuhTempo)->addDays(mt_rand(-5, 7));

                    if ($tglBayar->year > $tahun) {
                        $tglBayar = Carbon::create($tahun, 12, mt_rand(20, 30));
                    }

                    Transaksi::create([
                        'pelanggan_id' => $plg->id,
                        'tanggal'      => $tglBayar->format('Y-m-d'),
                        'jumlah'       => $plg->paket->harga ?? 0,
                        'created_by'   => 'admin',
                        'created_at'   => $tglBayar,
                    ]);
                }
            }
        }

        // ==========================================
        // 5. SEEDING PENGELUARAN (Angka Acak 164 - 215 Data)
        // ==========================================
        $kategoriList = [
            'Langganan ISP Induk',
            'Pembelian Perangkat (Router, Modem, Kabel)',
            'Perawatan Jaringan',
            'Gaji Karyawan / Teknisi',
            'Biaya Operasional Kantor',
            'Listrik',
            'Internet Kantor',
            'Transportasi',
            'Lainnya'
        ];

        $deskripsiAcak = [
            'Beli HTB 2 port 3 pcs',
            'Bensin tim teknisi penarikan kabel',
            'Beli Dropcore 1000m',
            'Kopi, rokok, gorengan teknisi lembur',
            'Beli Router ZTE F609 Bekas 10 Pcs',
            'Servis Splicer',
            'Token listrik server pusat',
            'Air galon kantor',
            'Beli Tang Crimping',
            'Konektor SC UPC 2 box',
            'Paku klem, isolasi, kabel ties',
            'Makan siang perbaikan tiang rubuh',
            'Ganti ban motor operasional'
        ];

        $jumlahPengeluaran = mt_rand(164, 215);

        for ($i = 0; $i < $jumlahPengeluaran; $i++) {
            $tglAcak = Carbon::create($tahun, mt_rand(1, 12), mt_rand(1, 28), mt_rand(8, 21), mt_rand(0, 59));
            $kategori = $kategoriList[array_rand($kategoriList)];

            // Nominalnya dibikin keriting (misal kelipatan 5000 biar realistis)
            // Misal: 115.000, 3.425.000, 45.000
            $nominal = mt_rand(5, 500) * 5000;

            // Khusus gaji & langganan ISP nominalnya digedein
            if ($kategori == 'Langganan ISP Induk' || $kategori == 'Gaji Karyawan / Teknisi') {
                $nominal = mt_rand(30, 80) * 100000; // 3jt - 8jt
            }

            Pengeluaran::create([
                'tanggal'     => $tglAcak->format('Y-m-d'),
                'kategori'    => $kategori,
                'deskripsi'   => ($kategori == 'Langganan ISP Induk' || $kategori == 'Gaji Karyawan / Teknisi') ? 'Pengeluaran bulanan rutin' : $deskripsiAcak[array_rand($deskripsiAcak)],
                'jumlah'      => $nominal,
                'created_by'  => 'admin',
                'created_at'  => $tglAcak
            ]);
        }

        // ==========================================
        // 6. SEEDING KOMPLAIN (Angka Acak 142 - 198 Data)
        // ==========================================
        $kategoriKomplain = ['Kabel Putus', 'Modem LOS Merah', 'Internet Lambat/RTO', 'Ganti Password WiFi', 'Pembayaran/Tagihan', 'Lain-lain'];
        $keluhanText = [
            'Min, internet mati nih lampu PON warna merah kedap kedip.',
            'Tolong cek ping, kalau main game patah-patah dari semalem.',
            'Kabel di tiang depan rumah putus kena truk lewat.',
            'Mau ganti password wifi dong lupa caranya.',
            'Udah bayar via transfer tapi inet masih keisolir, tolong dicek ya.',
            'Lampu indikator inet mati, modem udah di-restart tetep gabisa.',
            'Youtube muter doang gak mau jalan, padahal sinyal full.',
            'Tolong teknisi suruh ke rumah, kabelnya kegigit tikus kayaknya.'
        ];
        $priorityKomplain = ['Low', 'Medium', 'High'];

        $jumlahKomplain = mt_rand(142, 198);

        for ($i = 0; $i < $jumlahKomplain; $i++) {
            $tglKomplain = Carbon::create($tahun, mt_rand(1, 12), mt_rand(1, 28), mt_rand(7, 23), mt_rand(0, 59));

            // Logic Status Komplain:
            // Kalau kejadian di awal/pertengahan tahun, harusnya udah "Done"
            // Kalau kejadian di bulan 11 atau 12, ada kemungkinan masih gantung
            if ($tglKomplain->month >= 11 && mt_rand(1, 100) > 40) {
                $status = mt_rand(1, 10) > 4 ? 'In Progress' : 'Not Yet';
            } else {
                $status = 'Done';
            }

            Komplain::create([
                // Pastikan ambil pelanggan yang Active atau Non Active (yang pernah pake)
                'pelanggan_id' => $pelanggans[mt_rand(0, count($pelanggans) - 1)]->id,
                'tanggal'      => $tglKomplain->format('Y-m-d'),
                'keluhan'      => $keluhanText[array_rand($keluhanText)],
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
