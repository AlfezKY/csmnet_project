<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Paket;
use App\Models\Pelanggan;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ==========================================
        // 1. SEEDING USERS (Admin, Owner, Client)
        // ==========================================

        // User 1: Admin
        User::create([
            'fullname'   => 'Super Administrator',
            'username'   => 'admin',
            'password'   => Hash::make('password'),
            'role'       => 'Admin',
            'status'     => 'Active',
            'created_by' => 'SYSTEM'
        ]);

        // User 2: Owner
        User::create([
            'fullname'   => 'Bapak Pemilik',
            'username'   => 'owner',
            'password'   => Hash::make('password'),
            'role'       => 'Owner',
            'status'     => 'Active',
            'created_by' => 'SYSTEM'
        ]);

        // User 3: Client (Contoh user yang juga pelanggan)
        $userClient = User::create([
            'fullname'   => 'Mas Pelanggan Setia',
            'username'   => 'client',
            'password'   => Hash::make('password'),
            'role'       => 'Pelanggan',
            'status'     => 'Active',
            'created_by' => 'SYSTEM'
        ]);

        // ==========================================
        // 2. SEEDING PAKET INTERNET (3 Data)
        // ==========================================

        $paket1 = Paket::create([
            'nama_paket' => 'Paket Basic Home',
            'kecepatan'  => '10 Mbps',
            'harga'      => 150000,
            'deskripsi'  => 'Cocok untuk browsing ringan dan chat.',
            'keypoint'   => 'Fiber Optic, Unlimited Kuota',
            'status'     => 'Active',
            'is_show'    => true, // Tampil di Landing Page
            'created_by' => 'SYSTEM'
        ]);

        $paket2 = Paket::create([
            'nama_paket' => 'Paket Family Stream',
            'kecepatan'  => '30 Mbps',
            'harga'      => 250000,
            'deskripsi'  => 'Ideal untuk streaming HD dan WFH.',
            'keypoint'   => 'Gratis Router, Prioritas Trafik',
            'status'     => 'Active',
            'is_show'    => true,
            'created_by' => 'SYSTEM'
        ]);

        $paket3 = Paket::create([
            'nama_paket' => 'Paket Gamer Pro',
            'kecepatan'  => '100 Mbps',
            'harga'      => 500000,
            'deskripsi'  => 'Latency rendah untuk gaming kompetitif.',
            'keypoint'   => 'IP Public Static, VIP Support',
            'status'     => 'Active',
            'is_show'    => true,
            'created_by' => 'SYSTEM'
        ]);

        // ==========================================
        // 3. SEEDING PELANGGAN (3 Data)
        // ==========================================

        // Pelanggan 1: Terhubung dengan User 'client' (Active & Belum Lunas -> Masuk Tagihan)
        Pelanggan::create([
            'user_id'           => $userClient->id,
            'paket_id'          => $paket2->id,
            'nama_pelanggan'    => 'Mas Pelanggan Setia',
            'alamat'            => 'Jl. Sudirman No. 10, Jakarta Pusat',
            'no_wa'             => '081234567890',
            'jatuh_tempo'       => 5,
            'status_pembayaran' => 'Belum Lunas',
            'status'            => 'Active',
            'created_by'        => 'SYSTEM'
        ]);

        // Pelanggan 2: Belum punya akun login (Pending -> Masuk Approval)
        Pelanggan::create([
            'user_id'           => null,
            'paket_id'          => $paket1->id,
            'nama_pelanggan'    => 'Budi Santoso (Baru Daftar)',
            'alamat'            => 'Gg. Kancil No. 45, Bandung',
            'no_wa'             => '089876543210',
            'jatuh_tempo'       => 15,
            'status_pembayaran' => 'Belum Lunas',
            'status'            => 'Pending', // Ini bakal muncul di menu Approval
            'created_by'        => 'SYSTEM'
        ]);

        // Pelanggan 3: Pelanggan Lama (Active & Lunas)
        Pelanggan::create([
            'user_id'           => null,
            'paket_id'          => $paket3->id,
            'nama_pelanggan'    => 'Sultan Gaming',
            'alamat'            => 'Komplek Elite Blok A1, Surabaya',
            'no_wa'             => '085512345678',
            'jatuh_tempo'       => 20,
            'status_pembayaran' => 'Lunas',
            'status'            => 'Active',
            'created_by'        => 'SYSTEM'
        ]);
    }
}
