<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        // 1. PROTEKSI: Hanya Owner yang boleh buka halaman Manajemen Akun
        if (auth()->user()->role !== 'Owner') {
            abort(403, 'Akses ditolak. Hanya Owner yang diizinkan mengakses halaman ini.');
        }

        $users = User::whereIn('role', ['Admin', 'Owner'])->latest()->get();
        return view('admin.users.index', compact('users'));
    }

    public function store(Request $request)
    {
        // 2. PROTEKSI: Hanya Owner yang boleh nambah akun internal
        if (auth()->user()->role !== 'Owner') {
            abort(403, 'Akses ditolak. Hanya Owner yang diizinkan menambah user baru.');
        }

        $request->validate([
            'fullname' => 'required|string|max:255',
            'username' => 'required|string|min:3|unique:users',
            'role'     => 'required|in:Admin,Owner',
            'password' => [
                'required',
                'min:8',
                'regex:/[a-zA-Z]/',
                'regex:/[0-9]/',
            ],
        ], [
            'password.min' => 'Password minimal 8 karakter.',
            'password.regex' => 'Password harus kombinasi huruf dan angka.'
        ]);

        User::create([
            'fullname'   => $request->fullname,
            'username'   => $request->username,
            'role'       => $request->role,
            'password'   => Hash::make($request->password),
            'created_by' => auth()->user()->username ?? 'SYSTEM',
        ]);

        return back()->with('success', 'User internal berhasil ditambahkan!');
    }

   public function update(Request $request, User $user)
{
    // 1. PROTEKSI: Admin gak boleh ngedit Owner, atau ngedit admin lain
    if (auth()->user()->role !== 'Owner' && auth()->user()->id !== $user->id) {
        abort(403, 'Akses ditolak. Anda hanya diizinkan mengedit profil Anda sendiri.');
    }

    // 2. ATURAN VALIDASI BARU (Sekarang Username masuk ke validasi)
    $rules = [
        'fullname' => 'required|string|max:255',
        // Validasi unik, dikecualikan untuk ID user ini sendiri agar tidak error saat tidak ganti username
        'username' => 'required|string|min:3|max:255|unique:users,username,' . $user->id,
    ];

    // 3. PROTEKSI ROLE: Hanya Owner yang boleh ubah hak akses / role
    if (auth()->user()->role === 'Owner' && $request->has('role')) {
        $rules['role'] = 'required|in:Admin,Owner';
    }

    if ($request->filled('password')) {
        $rules['password'] = ['min:8', 'regex:/[a-zA-Z]/', 'regex:/[0-9]/'];
    }

    $request->validate($rules);

    // 4. SIAPKAN DATA YANG AKAN DIUPDATE
    $updateData = [
        'fullname'   => $request->fullname,
        'username'   => $request->username, // 🔥 SEKARANG USERNAME IKUT DISIMPAN
        'password'   => $request->filled('password') ? Hash::make($request->password) : $user->password,
        'updated_by' => auth()->user()->username ?? 'SYSTEM',
    ];

    // Masukkan perubahan role HANYA JIKA dia Owner
    if (auth()->user()->role === 'Owner' && $request->filled('role')) {
        $updateData['role'] = $request->role;
    }

    $user->update($updateData);

    return back()->with('success', 'Data Profil/User berhasil diupdate!');
}

    public function destroy(User $user)
    {
        // 5. PROTEKSI: Admin gak boleh hapus siapa-siapa
        if (auth()->user()->role !== 'Owner') {
            abort(403, 'Akses ditolak. Hanya Owner yang diizinkan menghapus user.');
        }

        $user->delete();
        return back()->with('success', 'User internal berhasil dihapus!');
    }
}
