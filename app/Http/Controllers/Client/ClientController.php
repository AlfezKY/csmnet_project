<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Pelanggan;
use App\Models\Transaksi;
use App\Models\User; // <--- WAJIB TAMBAH INI
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ClientController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Tarik data pelanggan berdasarkan user yang lagi login
        $pelanggan = Pelanggan::with('paket')->where('user_id', $user->id)->first();

        // Tarik 10 riwayat transaksi terakhir pelanggan ini
        $transaksis = collect();
        if ($pelanggan) {
            $transaksis = Transaksi::where('pelanggan_id', $pelanggan->id)
                ->orderBy('tanggal', 'desc')
                ->take(10)
                ->get();
        }

        // Arahkan ke view portal yang baru
        return view('client.portal', compact('user', 'pelanggan', 'transaksis'));
    }

    public function update(Request $request)
    {
        // Ambil user langsung dari Model biar Intelephense (VS Code) gak rewel
        $user = User::find(Auth::id());

        $pelanggan = Pelanggan::where('user_id', $user->id)->first();

        $request->validate([
            'fullname' => 'required|string|max:255',
            'no_wa'    => 'nullable|string|max:20',
            'email'    => 'nullable|email|max:255',
            'password' => 'nullable|min:6',
            'alamat'   => 'nullable|string',
        ]);

        // 1. Update data Akun Login (User)
        $user->fullname = $request->fullname;
        if ($request->email) $user->email = $request->email;
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }
        $user->save(); // Garis merahnya pasti hilang sekarang!

        // 2. Update data Detail Pelanggan (jika akun sudah ditautkan oleh admin)
        if ($pelanggan) {
            $pelanggan->nama_pelanggan = $request->fullname;
            if ($request->no_wa) $pelanggan->no_wa = $request->no_wa;
            if ($request->alamat) $pelanggan->alamat = $request->alamat;
            $pelanggan->save();
        }

        return back()->with('success', 'Data profil Anda berhasil diperbarui!');
    }
}
