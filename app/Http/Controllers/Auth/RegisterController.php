<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class RegisterController extends Controller
{
    public function index()
    {
        return view('auth.register');
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'fullname' => 'required|max:255',
            'username' => 'required|min:4|max:20|unique:users',
            'password' => 'required|min:5|max:255',
            'email'    => 'nullable|email|unique:users',
        ]);

        // Enkripsi Password
        $validatedData['password'] = Hash::make($validatedData['password']);

        // Default Value sesuai diagram lo
        $validatedData['role'] = 'Pelanggan';
        $validatedData['status'] = 'Active';
        $validatedData['created_by'] = 'SELF_REGISTER';

        User::create($validatedData);

        return redirect('/')->with('success', 'Registrasi Berhasil! Silakan Login.');
    }
}