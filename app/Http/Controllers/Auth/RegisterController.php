<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Pelanggan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Mail\RegisterMail;

class RegisterController extends Controller
{
    public function index()
    {
        // Gak perlu lagi narik data Paket
        return view('auth.register');
    }

    public function store(Request $request)
    {
        // 1. Validasi Tanpa Paket
        $validatedData = $request->validate([
            'fullname' => 'required|string|max:255',
            'username' => 'required|string|min:4|max:20|unique:users',
            'email'    => 'required|email|unique:users',
            'no_wa'    => 'required|string|max:20',
            'alamat'   => 'required|string',
            
            'password' => [
                'required',
                'min:8',
                'regex:/[0-9]/',
                'confirmed'
            ],
        ], [
            'password.min'       => 'Password minimal harus 8 karakter.',
            'password.regex'     => 'Password harus mengandung setidaknya 1 angka.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
            'email.email'        => 'Format email tidak valid.',
            'username.unique'    => 'Username ini sudah dipakai.',
        ]);

        // 2. Eksekusi Hit 2 Table
        DB::transaction(function () use ($validatedData) {

            $user = User::create([
                'fullname'   => $validatedData['fullname'],
                'username'   => $validatedData['username'],
                'email'      => $validatedData['email'],
                'password'   => Hash::make($validatedData['password']),
                'role'       => 'Pelanggan',
                'status'     => 'Pending',
                'created_by' => 'SELF_REGISTER',
            ]);

            Pelanggan::create([
                'user_id'           => $user->id,
                'paket_id'          => null, // Dikosongkan, nunggu diisi Admin
                'nama_pelanggan'    => $validatedData['fullname'],
                'alamat'            => $validatedData['alamat'],
                'no_wa'             => $validatedData['no_wa'],
                'email'             => $validatedData['email'],
                'jatuh_tempo'       => null,
                'status_pembayaran' => 'Belum Lunas',
                'status'            => 'Pending',
                'created_by'        => 'SELF_REGISTER',
            ]);

            Auth::login($user);
        });

        // ==========================================
        // 3. FITUR KIRIM EMAIL OTOMATIS (REGISTER MAIL)
        // ==========================================
        try {
            // Kita langsung passing $validatedData ke emailnya
            Mail::to($validatedData['email'])->send(new RegisterMail($validatedData));
            Log::info('Email Pendaftaran Berhasil Terkirim ke: ' . $validatedData['email']);

            return redirect()->route('client-portal')->with('success', 'Registrasi Berhasil! Email informasi telah dikirim.');
        } catch (\Exception $e) {
            Log::error('Email Exception: ' . $e->getMessage());

            return redirect()->route('client-portal')->with('error', 'Registrasi berhasil, tapi sistem gagal mengirim email notifikasi: ' . $e->getMessage());
        }

        return redirect()->route('client-portal')->with('success', 'Registrasi Berhasil! Menunggu persetujuan admin.');
    }
}