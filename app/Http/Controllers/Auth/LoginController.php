<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    /**
     * Tampilkan Halaman Login
     */
    public function index()
    {
        return view('auth.login');
    }

    /**
     * Proses Login & Redirect berdasarkan Role
     */
    public function authenticate(Request $request)
    {
        // 1. Validasi Input
        $credentials = $request->validate([
            'username' => ['required'],
            'password' => ['required'],
        ]);

        // 2. Coba Login
        if (Auth::attempt($credentials)) {

            // --- TAMBAHAN: BLOKIR JIKA STATUS NON ACTIVE ---
            if (Auth::user()->status === 'Non Active') {
                // Langsung logout-in lagi
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                // Balikin ke halaman login bawa pesan alert
                return back()->with('loginError', 'Akun tidak aktif, hubungi admin untuk informasi lebih lanjut.');
            }

            // Regenerate session biar aman dari hijacking (hanya untuk yang lolos)
            $request->session()->regenerate();

            // Ambil role user yang baru login
            $role = Auth::user()->role;

            // 3. Pengaturan Lalu Lintas (Redirect)
            if ($role === 'Admin' || $role === 'Owner') {
                return redirect()->intended('/dashboard');
            }

            if ($role === 'Pelanggan') {
                return redirect()->intended('/client-portal');
            }
        }

        // 4. Jika Gagal (Kredensial Salah)
        return back()->with('loginError', 'Login Gagal! Username atau password salah.');
    }

    /**
     * Proses Logout
     */
    public function logout(Request $request)
    {
        Auth::logout();

        // Hapus session dan token biar bener-bener bersih
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
