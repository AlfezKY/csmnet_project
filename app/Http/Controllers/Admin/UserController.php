<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller; // Wajib biar gak error
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        // Ambil semua user untuk ditampilkan di tabel
        $users = User::latest()->get();
        return view('admin.users.index', compact('users'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'fullname' => 'required|string|max:255',
            'username' => 'required|string|min:3|unique:users',
            'role'     => 'required|in:Admin,Owner,Pelanggan',
            'password' => [
                'required',
                'min:8',
                'regex:/[a-zA-Z]/', // Harus ada huruf
                'regex:/[0-9]/',    // Harus ada angka
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
            'created_by' => auth()->user()->username ?? 'SYSTEM', // Catat siapa yang bikin
        ]);

        return back()->with('success', 'User berhasil ditambahkan!');
    }

    public function update(Request $request, User $user)
    {
        $rules = [
            'fullname' => 'required|string|max:255',
            'role'     => 'required|in:Admin,Owner,Pelanggan',
        ];

        if ($request->filled('password')) {
            $rules['password'] = ['min:8', 'regex:/[a-zA-Z]/', 'regex:/[0-9]/'];
        }

        $request->validate($rules);

        $user->update([
            'fullname'   => $request->fullname,
            'role'       => $request->role,
            'password'   => $request->filled('password') ? Hash::make($request->password) : $user->password,
            'updated_by' => auth()->user()->username ?? 'SYSTEM', // Catat siapa yang update
        ]);

        return back()->with('success', 'User berhasil diupdate!');
    }

    public function destroy(User $user)
    {
        $user->delete();
        return back()->with('success', 'User berhasil dihapus!');
    }
}
