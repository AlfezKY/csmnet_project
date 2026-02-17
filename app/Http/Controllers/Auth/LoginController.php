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
            // Regenerate session biar aman dari hijacking
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

        // 4. Jika Gagal
        return back()->with('loginError', 'Login Gagal! Pastikan akun Anda aktif.');
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
