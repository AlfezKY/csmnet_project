<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pelanggan;
use App\Models\Paket;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class PelangganController extends Controller
{
    public function index()
    {
        // Tambahin 'user' di dalam with() biar bisa narik data username lamanya
        $pelanggans = Pelanggan::with(['paket', 'user'])->latest()->get();
        $pakets = Paket::where('status', 'Active')->get();

        return view('admin.pelanggan.index', compact('pelanggans', 'pakets'));
    }

    public function store(Request $request)
    {
        $rules = [
            'nama_pelanggan' => 'required|string|max:255',
            'no_wa'          => 'required|string|max:20',
            'paket_id'       => 'required|exists:pakets,id',
            'jatuh_tempo'    => 'required|integer|min:1|max:31',
            'status'         => 'required|in:Active,Non Active,Pending',
            'alamat'         => 'required|string',
        ];

        if ($request->has('create_account')) {
            $rules['username'] = 'required|string|max:255|unique:users,username';
            $rules['password'] = 'required|string|min:8';
        }

        $data = $request->validate($rules);

        DB::transaction(function () use ($data, $request) {
            $userId = null;

            if ($request->has('create_account')) {
                $user = User::create([
                    'fullname'   => $data['nama_pelanggan'],
                    'username'   => $data['username'],
                    'password'   => Hash::make($data['password']),
                    'role'       => 'Pelanggan',
                    'status'     => 'Active',
                    'created_by' => auth()->user()->username ?? 'SYSTEM',
                ]);
                $userId = $user->id;
            }

            Pelanggan::create([
                'user_id'           => $userId,
                'paket_id'          => $data['paket_id'],
                'nama_pelanggan'    => $data['nama_pelanggan'],
                'alamat'            => $data['alamat'],
                'no_wa'             => $data['no_wa'],
                'jatuh_tempo'       => $data['jatuh_tempo'],
                'status_pembayaran' => 'Belum Lunas',
                'status'            => $data['status'],
                'created_by'        => auth()->user()->username ?? 'SYSTEM',
            ]);
        });

        return back()->with('success', 'Pelanggan baru berhasil ditambahkan!');
    }

    public function update(Request $request, Pelanggan $pelanggan)
    {
        $rules = [
            'nama_pelanggan'    => 'required|string|max:255',
            'no_wa'             => 'required|string|max:20',
            'paket_id'          => 'required|exists:pakets,id',
            'jatuh_tempo'       => 'required|integer|min:1|max:31',
            'status_pembayaran' => 'required|in:Lunas,Belum Lunas',
            'status'            => 'required|in:Active,Non Active,Pending',
            'alamat'            => 'required|string',
        ];

        // LOGIKA TAMBAHAN UNTUK EDIT AKUN
        if ($request->has('edit_account')) {
            $userId = $pelanggan->user_id;

            // Jika sudah punya akun, abaikan username miliknya sendiri saat cek unique
            $rules['username'] = $userId
                ? "required|string|max:255|unique:users,username,{$userId}"
                : "required|string|max:255|unique:users,username";

            // Jika pelanggan belum punya akun, ATAU password diisi saat edit
            if (!$userId || $request->filled('password')) {
                $rules['password'] = 'required|string|min:8';
            }
        }

        $data = $request->validate($rules);

        DB::transaction(function () use ($data, $request, $pelanggan) {
            // Urusin Akunnya dulu
            if ($request->has('edit_account')) {
                if ($pelanggan->user_id) {
                    // KONDISI 1: Update Akun Lama
                    $user = User::find($pelanggan->user_id);
                    $user->username = $data['username'];
                    // Update password cuma kalau diisi
                    if ($request->filled('password')) {
                        $user->password = Hash::make($data['password']);
                    }
                    $user->updated_by = auth()->user()->username ?? 'SYSTEM';
                    $user->save();
                } else {
                    // KONDISI 2: Bikinin Akun Baru
                    $user = User::create([
                        'fullname'   => $data['nama_pelanggan'],
                        'username'   => $data['username'],
                        'password'   => Hash::make($data['password']),
                        'role'       => 'Pelanggan',
                        'status'     => 'Active',
                        'created_by' => auth()->user()->username ?? 'SYSTEM',
                    ]);
                    // Tautkan ID user baru ke pelanggan ini
                    $pelanggan->user_id = $user->id;
                }
            }

            // Urusin Update Profil Pelanggannya
            $pelanggan->update([
                'user_id'           => $pelanggan->user_id, // Biar tetep nempel
                'paket_id'          => $data['paket_id'],
                'nama_pelanggan'    => $data['nama_pelanggan'],
                'alamat'            => $data['alamat'],
                'no_wa'             => $data['no_wa'],
                'jatuh_tempo'       => $data['jatuh_tempo'],
                'status_pembayaran' => $data['status_pembayaran'],
                'status'            => $data['status'],
                'updated_by'        => auth()->user()->username ?? 'SYSTEM',
            ]);
        });

        return back()->with('success', 'Data pelanggan berhasil diperbarui!');
    }

    public function destroy(Pelanggan $pelanggan)
    {
        $pelanggan->delete();
        return back()->with('success', 'Pelanggan berhasil dihapus!');
    }
}
