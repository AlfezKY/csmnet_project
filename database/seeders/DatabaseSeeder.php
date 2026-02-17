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
        // 1. Bikin User Admin buat Login
        $admin = User::create([
            'fullname' => 'Risky Alfarez',
            'username' => 'admin_risky',
            'password' => Hash::make('Kabel123'), // Password minimal 8 karakter + angka
            'role'     => 'Admin',
            'status'   => 'Active',
            'created_by' => 'SYSTEM'
        ]);

        // 2. Bikin Paket Internet
        $paketA = Paket::create([
            'nama_paket' => 'Paket Hemat 10Mbps',
            'kecepatan'  => '10 Mbps',
            'harga'      => 150000, // Tipe data Integer
            'status'     => 'Active',
        ]);

        $paketB = Paket::create([
            'nama_paket' => 'Paket Ngebut 20Mbps',
            'kecepatan'  => '20 Mbps',
            'harga'      => 250000,
            'status'     => 'Active',
        ]);

        // 3. Bikin Pelanggan Contoh
        Pelanggan::create([
            'user_id'         => null, // Belum punya akun login
            'paket_id'        => $paketA->id, // Relasi ke Paket A (Otomatis pake UUID)
            'nama_pelanggan'  => 'Budi Budiman',
            'alamat'          => 'Jl. Merdeka No. 123, Jakarta',
            'no_wa'           => '081234567890',
            'jatuh_tempo'     => 10, // Tanggal 10 tiap bulan
            'status_pembayaran' => 'Belum Lunas',
            'status'          => 'Active',
        ]);

        Pelanggan::create([
            'user_id'         => null,
            'paket_id'        => $paketB->id,
            'nama_pelanggan'  => 'Siti Aminah',
            'alamat'          => 'Perum Elok Blok C4, Jakarta',
            'no_wa'           => '08987654321',
            'jatuh_tempo'     => 20,
            'status_pembayaran' => 'Lunas',
            'status'          => 'Active',
        ]);
    }
}
