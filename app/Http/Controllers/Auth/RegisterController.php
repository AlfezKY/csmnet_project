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
            'email'    => 'nullable|email|unique:users',
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
                'jatuh_tempo'       => null,
                'status_pembayaran' => 'Belum Lunas',
                'status'            => 'Pending',
                'created_by'        => 'SELF_REGISTER',
            ]);

            Auth::login($user);
        });

        // ==========================================
// 3. FITUR KIRIM WA OTOMATIS WABLAS (UPDATED)
// ==========================================
$domain = env('WABLAS_DOMAIN'); 
$token  = env('WABLAS_TOKEN');  

if ($domain && $token) {
    $phone = $validatedData['no_wa'];
    // Bersihkan nomor dari karakter non-angka
    $phone = preg_replace('/[^0-9]/', '', $phone);
    
    if (str_starts_with($phone, '0')) {
        $phone = '62' . substr($phone, 1);
    }

    $pesan = "Halo kak *{$validatedData['fullname']}*,\n\nTerima kasih telah mengajukan pemasangan baru di CSMNET. Data pendaftaran Anda sudah kami terima dan saat ini berstatus *Menunggu Validasi*.\n\nMohon kesediaannya untuk menunggu ya kak, Admin kami akan segera memproses pendaftaran dan menghubungi kakak secepatnya. 🙏";

    try {
    $apiUrl = rtrim($domain, '/') . "/api/v2/send-message";

    $response = Http::withoutVerifying()->withHeaders([
        'Authorization' => $token,
        'Accept'        => 'application/json',
    ])->post($apiUrl, [
        'data' => [ // <--- Wablas minta dibungkus array 'data'
            [
                'phone'   => $phone,
                'message' => $pesan,
                'isGroup' => 'false',
            ]
        ]
    ]);

    if ($response->failed()) {
        Log::error('Wablas API Error: ' . $response->body());
    } else {
        Log::info('WA Berhasil Terkirim: ' . $response->body());
    }

} catch (\Exception $e) {
    Log::error('Wablas Exception: ' . $e->getMessage());
}
}

        return redirect()->route('client-portal')->with('success', 'Registrasi Berhasil! Menunggu persetujuan admin.');
    }
}