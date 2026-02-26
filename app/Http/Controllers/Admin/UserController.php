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
        // 1. FILTER: HANYA TAMPILKAN ORANG DALAM (Admin & Owner)
        $users = User::whereIn('role', ['Admin', 'Owner'])->latest()->get();
        return view('admin.users.index', compact('users'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'fullname' => 'required|string|max:255',
            'username' => 'required|string|min:3|unique:users',
            'role'     => 'required|in:Admin,Owner', // 2. FILTER: Hapus opsi Pelanggan
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
        $rules = [
            'fullname' => 'required|string|max:255',
            'role'     => 'required|in:Admin,Owner', // 3. FILTER: Hapus opsi Pelanggan
        ];

        if ($request->filled('password')) {
            $rules['password'] = ['min:8', 'regex:/[a-zA-Z]/', 'regex:/[0-9]/'];
        }

        $request->validate($rules);

        $user->update([
            'fullname'   => $request->fullname,
            'role'       => $request->role,
            'password'   => $request->filled('password') ? Hash::make($request->password) : $user->password,
            'updated_by' => auth()->user()->username ?? 'SYSTEM',
        ]);

        return back()->with('success', 'Data User internal berhasil diupdate!');
    }

    public function destroy(User $user)
    {
        $user->delete();
        return back()->with('success', 'User internal berhasil dihapus!');
    }
}
