<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pelanggan;
use App\Models\Paket;
use Illuminate\Http\Request;

class PelangganController extends Controller
{
    public function index()
    {
        // Ambil pelanggan beserta data paketnya
        $pelanggans = Pelanggan::with('paket')->latest()->get();
        // Ambil semua paket untuk pilihan di Dropdown Modal
        $pakets = Paket::where('status', 'Active')->get();

        return view('admin.pelanggan.index', compact('pelanggans', 'pakets'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nama_pelanggan' => 'required|string|max:255',
            'no_wa'          => 'required|string|max:20',
            'paket_id'       => 'required|exists:pakets,id',
            'jatuh_tempo'    => 'required|integer|min:1|max:31',
            'status'         => 'required|in:Active,Non Active,Pending',
            'alamat'         => 'required|string',
        ]);

        $data['created_by'] = auth()->user()->username ?? 'SYSTEM';
        Pelanggan::create($data);

        return back()->with('success', 'Pelanggan baru berhasil ditambahkan!');
    }

    public function update(Request $request, Pelanggan $pelanggan)
    {
        $data = $request->validate([
            'nama_pelanggan' => 'required|string|max:255',
            'no_wa'          => 'required|string|max:20',
            'paket_id'       => 'required|exists:pakets,id',
            'jatuh_tempo'    => 'required|integer|min:1|max:31',
            'status_pembayaran' => 'required|in:Lunas,Belum Lunas',
            'status'         => 'required|in:Active,Non Active,Pending',
            'alamat'         => 'required|string',
        ]);

        $data['updated_by'] = auth()->user()->username ?? 'SYSTEM';

        // Langsung update, nggak perlu findOrFail lagi
        $pelanggan->update($data);

        return back()->with('success', 'Data pelanggan berhasil diperbarui!');
    }

    public function destroy(Pelanggan $pelanggan)
    {
        $pelanggan->delete();
        return back()->with('success', 'Pelanggan berhasil dihapus!');
    }
}