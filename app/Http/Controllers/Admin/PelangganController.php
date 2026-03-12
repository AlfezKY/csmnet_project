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
    public function index(Request $request)
    {
        $query = Pelanggan::with(['paket', 'user'])->latest();

        if ($request->filled('q')) {
            $search = $request->q;
            $query->where(function ($q) use ($search) {
                $q->where('nama_pelanggan', 'like', "%{$search}%")
                    ->orWhere('alamat', 'like', "%{$search}%");
            });
        }

        if ($request->filled('paket_id')) {
            $query->where('paket_id', $request->paket_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('status_pembayaran')) {
            $query->where('status_pembayaran', $request->status_pembayaran);
        }

        $pelanggans = $query->get();
        $pakets = Paket::where('status', 'Active')->get();

        return view('admin.pelanggan.index', compact('pelanggans', 'pakets'));
    }

    public function store(Request $request)
    {
        $rules = [
            'nama_pelanggan' => 'required|string|max:255',
            'no_wa'          => 'required|string|max:20',
            'paket_id'       => 'nullable|exists:pakets,id',
            // Validasi min:0 biar angka 0 lolos
            'jatuh_tempo'    => 'nullable|integer|min:0|max:31',
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
                    'status'     => $data['status'],
                    'created_by' => auth()->user()->username ?? 'SYSTEM',
                ]);
                $userId = $user->id;
            }

            Pelanggan::create([
                'user_id'           => $userId,
                'paket_id'          => empty($data['paket_id']) ? null : $data['paket_id'],
                'nama_pelanggan'    => $data['nama_pelanggan'],
                'alamat'            => $data['alamat'],
                'no_wa'             => $data['no_wa'],
                // JADIKAN 0 JIKA KOSONG:
                'jatuh_tempo'       => empty($data['jatuh_tempo']) ? 0 : $data['jatuh_tempo'],
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
            'paket_id'          => 'nullable|exists:pakets,id',
            // Validasi min:0 biar angka 0 lolos
            'jatuh_tempo'       => 'nullable|integer|min:0|max:31',
            'status_pembayaran' => 'required|in:Lunas,Belum Lunas',
            'status'            => 'required|in:Active,Non Active,Pending',
            'alamat'            => 'required|string',
        ];

        if ($request->has('edit_account')) {
            $userId = $pelanggan->user_id;

            $rules['username'] = $userId
                ? "required|string|max:255|unique:users,username,{$userId}"
                : "required|string|max:255|unique:users,username";

            if (!$userId || $request->filled('password')) {
                $rules['password'] = 'required|string|min:8';
            }
        }

        $data = $request->validate($rules);

        DB::transaction(function () use ($data, $request, $pelanggan) {

            if ($pelanggan->user_id) {
                $user = User::find($pelanggan->user_id);
                if ($user) {
                    $user->status = $data['status'];

                    if ($request->has('edit_account')) {
                        $user->username = $data['username'];
                        if ($request->filled('password')) {
                            $user->password = Hash::make($data['password']);
                        }
                    }

                    $user->updated_by = auth()->user()->username ?? 'SYSTEM';
                    $user->save();
                }
            } else if ($request->has('edit_account')) {
                $user = User::create([
                    'fullname'   => $data['nama_pelanggan'],
                    'username'   => $data['username'],
                    'password'   => Hash::make($data['password']),
                    'role'       => 'Pelanggan',
                    'status'     => $data['status'],
                    'created_by' => auth()->user()->username ?? 'SYSTEM',
                ]);
                $pelanggan->user_id = $user->id;
            }

            $pelanggan->update([
                'user_id'           => $pelanggan->user_id,
                'paket_id'          => empty($data['paket_id']) ? null : $data['paket_id'],
                'nama_pelanggan'    => $data['nama_pelanggan'],
                'alamat'            => $data['alamat'],
                'no_wa'             => $data['no_wa'],
                // JADIKAN 0 JIKA KOSONG:
                'jatuh_tempo'       => empty($data['jatuh_tempo']) ? 0 : $data['jatuh_tempo'],
                'status_pembayaran' => $data['status_pembayaran'],
                'status'            => $data['status'],
                'updated_by'        => auth()->user()->username ?? 'SYSTEM',
            ]);
        });

        return back()->with('success', 'Data pelanggan berhasil diperbarui!');
    }

    public function destroy(Pelanggan $pelanggan)
    {
        if ($pelanggan->user_id) {
            User::find($pelanggan->user_id)?->delete();
        }

        $pelanggan->delete();
        return back()->with('success', 'Pelanggan berhasil dihapus!');
    }
}
