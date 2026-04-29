<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pelanggan;
use App\Models\Paket;
use App\Models\User;
use App\Models\Transaksi; // Pastikan model Transaksi (jika namanya ini) ikut terpanggil
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class PelangganController extends Controller
{
    public function index(Request $request)
    {
        $query = Pelanggan::with(['paket', 'user'])
            ->orderByRaw("CASE WHEN status = 'Active' THEN 0 ELSE 1 END") // 1. Prioritas utama: Status Active di atas
            ->orderByRaw("jatuh_tempo IS NULL ASC")                       // 2. Data tanpa tanggal jatuh tempo taruh paling bawah
            ->orderByRaw("CASE WHEN jatuh_tempo >= CURRENT_DATE THEN 0 ELSE 1 END") // 3. Tanggal >= Hari ini naik ke atas, yang sudah lewat turun ke bawah
            ->orderByRaw("jatuh_tempo ASC");

        // 1. Search Box
        if ($request->filled('q')) {
            $search = $request->q;
            $query->where(function ($q) use ($search) {
                $q->where('nama_pelanggan', 'like', "%{$search}%")
                    ->orWhere('alamat', 'like', "%{$search}%");
            });
        }

        // 2. Filter Paket & Status
        if ($request->filled('paket_id')) {
            $query->where('paket_id', $request->paket_id);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('status_pembayaran')) {
            $query->where('status_pembayaran', $request->status_pembayaran);
        }

        // 3. Filter Rentang Jatuh Tempo
        if ($request->filled('jatuh_tempo_start')) {
            $query->whereDate('jatuh_tempo', '>=', $request->jatuh_tempo_start);
        }
        if ($request->filled('jatuh_tempo_end')) {
            $query->whereDate('jatuh_tempo', '<=', $request->jatuh_tempo_end);
        }

        // 4. Filter Rentang Tanggal Bergabung (created_at)
        if ($request->filled('bergabung_start')) {
            $query->whereDate('created_at', '>=', $request->bergabung_start);
        }
        if ($request->filled('bergabung_end')) {
            $query->whereDate('created_at', '<=', $request->bergabung_end);
        }

        // ==========================================
        // FITUR EXPORT EXCEL (.xls Native)
        // ==========================================
        if ($request->has('export')) {
            $pelanggans = $query->get();
            $filename = "Data_Pelanggan_" . date('Y-m-d') . ".xls";

            $headers = [
                "Content-type"        => "application/vnd.ms-excel",
                "Content-Disposition" => "attachment; filename=$filename",
                "Pragma"              => "no-cache",
                "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
                "Expires"             => "0"
            ];

            $callback = function () use ($pelanggans) {
                echo '<table border="1">';
                // Header dengan warna biru khas CSMNET
                echo '<tr>
                        <th style="background-color:#2563eb; color:#ffffff;">Nama Lengkap</th>
                        <th style="background-color:#2563eb; color:#ffffff;">Paket Internet</th>
                        <th style="background-color:#2563eb; color:#ffffff;">Alamat</th>
                        <th style="background-color:#2563eb; color:#ffffff;">No WA</th>
                        <th style="background-color:#2563eb; color:#ffffff;">Jatuh Tempo</th>
                        <th style="background-color:#2563eb; color:#ffffff;">Pembayaran</th>
                        <th style="background-color:#2563eb; color:#ffffff;">Status Layanan</th>
                        <th style="background-color:#2563eb; color:#ffffff;">Tgl Bergabung</th>
                      </tr>';

                foreach ($pelanggans as $plg) {
                    $paket = $plg->paket->nama_paket ?? 'Tanpa Paket';
                    $tanggal = $plg->jatuh_tempo ? \Carbon\Carbon::parse($plg->jatuh_tempo)->format('Y-m-d') : '-';
                    $tglGabung = $plg->created_at ? $plg->created_at->format('Y-m-d') : '-';

                    echo "<tr>
                            <td>{$plg->nama_pelanggan}</td>
                            <td>{$paket}</td>
                            <td>{$plg->alamat}</td>
                            <td>'{$plg->no_wa}</td>
                            <td>{$tanggal}</td>
                            <td>{$plg->status_pembayaran}</td>
                            <td>{$plg->status}</td>
                            <td>{$tglGabung}</td>
                          </tr>";
                }
                echo '</table>';
            };

            return response()->stream($callback, 200, $headers);
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
            'jatuh_tempo'    => 'nullable|date',
            'status'         => 'required|in:Active,Non Active,Pending',
            'alamat'         => 'required|string',
        ];

        if ($request->filled('username')) {
            $rules['username']    = 'required|string|max:255|unique:users,username';
            $rules['email']       = 'nullable|email|max:255|unique:users,email';
            $rules['password']    = 'required|string|min:8';
            $rules['user_status'] = 'required|in:Active,Non Active,Pending';
        }

        $data = $request->validate($rules);

        DB::transaction(function () use ($data, $request) {
            $userId = null;

            if ($request->filled('username')) {
                $user = User::create([
                    'fullname'   => $data['nama_pelanggan'],
                    'username'   => $data['username'],
                    'email'      => $data['email'] ?? null,
                    'password'   => Hash::make($data['password']),
                    'role'       => 'Pelanggan',
                    'status'     => $data['user_status'],
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
                'jatuh_tempo'       => empty($data['jatuh_tempo']) ? null : $data['jatuh_tempo'],
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
            'jatuh_tempo'       => 'nullable|date',
            'status_pembayaran' => 'required|in:Lunas,Belum Lunas',
            'status'            => 'required|in:Active,Non Active,Pending',
            'alamat'            => 'required|string',
        ];

        if ($pelanggan->user_id || $request->filled('username')) {
            $userId = $pelanggan->user_id;

            $rules['username'] = $userId
                ? "required|string|max:255|unique:users,username,{$userId}"
                : "required|string|max:255|unique:users,username";

            $rules['email'] = $userId
                ? "nullable|email|max:255|unique:users,email,{$userId}"
                : "nullable|email|max:255|unique:users,email";

            if (!$userId || $request->filled('password')) {
                $rules['password'] = 'required|string|min:8';
            }

            $rules['user_status'] = 'required|in:Active,Non Active,Pending';
        }

        $data = $request->validate($rules);

        DB::transaction(function () use ($data, $request, $pelanggan) {

            if ($pelanggan->user_id) {
                $user = User::find($pelanggan->user_id);
                if ($user) {
                    $user->username = $data['username'];
                    $user->email    = $data['email'] ?? null;
                    $user->status   = $data['user_status'];
                    if ($request->filled('password')) {
                        $user->password = Hash::make($data['password']);
                    }
                    $user->updated_by = auth()->user()->username ?? 'SYSTEM';
                    $user->save();
                }
            } else if ($request->filled('username')) {
                $user = User::create([
                    'fullname'   => $data['nama_pelanggan'],
                    'username'   => $data['username'],
                    'email'      => $data['email'] ?? null,
                    'password'   => Hash::make($data['password']),
                    'role'       => 'Pelanggan',
                    'status'     => $data['user_status'],
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
                'jatuh_tempo'       => empty($data['jatuh_tempo']) ? null : $data['jatuh_tempo'],
                'status_pembayaran' => $data['status_pembayaran'],
                'status'            => $data['status'],
                'updated_by'        => auth()->user()->username ?? 'SYSTEM',
            ]);
        });

        return back()->with('success', 'Data pelanggan berhasil diperbarui!');
    }

    public function destroy(Pelanggan $pelanggan)
    {
        DB::transaction(function () use ($pelanggan) {
            $userId = $pelanggan->user_id;

            // 1. Hapus semua data transaksi yang berelasi dengan pelanggan ini
            // Menggunakan query builder jika model Transaksi belum di-import atau belum direlasikan
            DB::table('transaksis')->where('pelanggan_id', $pelanggan->id)->delete();

            // 2. Hapus data pelanggan (anak dari user, tapi parent dari transaksi)
            $pelanggan->delete();

            // 3. Terakhir, hapus data user yang terhubung (parent utama)
            if ($userId) {
                User::find($userId)?->delete();
            }
        });

        return back()->with('success', 'Pelanggan beserta seluruh transaksinya berhasil dihapus!');
    }
}