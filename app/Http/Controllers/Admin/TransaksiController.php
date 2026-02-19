<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Transaksi;
use App\Models\Pelanggan;
use Illuminate\Http\Request;

class TransaksiController extends Controller
{
    public function index()
    {
        // Ambil riwayat transaksi beserta data pelanggannya
        $transaksis = Transaksi::with(['pelanggan', 'pelanggan.paket'])->latest()->get();
        // Ambil daftar pelanggan buat di dropdown (yang statusnya Active)
        $pelanggans = Pelanggan::where('status', 'Active')->get();

        return view('admin.transaksi.index', compact('transaksis', 'pelanggans'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'pelanggan_id' => 'required|exists:pelanggans,id',
            'tanggal'      => 'required|date',
            'jumlah'       => 'required|numeric|min:0',
        ]);

        $data['created_by'] = auth()->user()->username ?? 'SYSTEM';

        // Simpan Transaksi
        Transaksi::create($data);

        // OTOMATIS: Ubah status pelanggan jadi 'Lunas'
        $pelanggan = Pelanggan::find($request->pelanggan_id);
        if ($pelanggan) {
            $pelanggan->update(['status_pembayaran' => 'Lunas']);
        }

        return back()->with('success', 'Pembayaran berhasil dicatat & Status pelanggan jadi Lunas!');
    }

    public function update(Request $request, Transaksi $transaksi)
    {
        $data = $request->validate([
            'pelanggan_id' => 'required|exists:pelanggans,id',
            'tanggal'      => 'required|date',
            'jumlah'       => 'required|numeric|min:0',
        ]);

        $data['updated_by'] = auth()->user()->username ?? 'SYSTEM';
        $transaksi->update($data);

        return back()->with('success', 'Data transaksi berhasil diperbarui!');
    }

    public function destroy(Transaksi $transaksi)
    {
        $transaksi->delete();
        return back()->with('success', 'Riwayat transaksi berhasil dihapus!');
    }
}
