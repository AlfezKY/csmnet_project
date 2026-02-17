<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Pelanggan;
use App\Models\Paket;

class DashboardController extends Controller
{
    public function index()
    {
        // Narik data asli dari DB lo
        $data = [
            'totalPelanggan' => Pelanggan::count(),
            'totalPaket'     => Paket::count(),
            'adminName'      => auth()->user()->fullname,
            'role'           => auth()->user()->role,
            // Sementara kita hardcode dulu sampai tabelnya lo buat nanti
            'komplainAktif'  => 0,
            'pendapatan'     => '12.500.000',
        ];

        return view('dashboard.index', $data);
    }
}
