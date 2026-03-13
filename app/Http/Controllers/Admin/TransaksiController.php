<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Transaksi;
use App\Models\Pelanggan;
use Illuminate\Http\Request;
use Carbon\Carbon; // WAJIB TAMBAH

class TransaksiController extends Controller
{
    public function index()
    {
        $transaksis = Transaksi::with(['pelanggan', 'pelanggan.paket'])->latest()->get();
        // Pastikan ambil relasi paket untuk kalkulasi JS di View
        $pelanggans = Pelanggan::with('paket')->where('status', 'Active')->get();

        return view('admin.transaksi.index', compact('transaksis', 'pelanggans'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'pelanggan_id' => 'required|exists:pelanggans,id',
            'tanggal'      => 'required|date',
            'jumlah'       => 'required|numeric|min:0',
            'jumlah_bulan' => 'required|integer|min:0', // 0 jika cuma cicil/bayar tunggakan
        ]);

        $data['created_by'] = auth()->user()->username ?? 'SYSTEM';

        // 1. Simpan Transaksi
        Transaksi::create([
            'pelanggan_id' => $data['pelanggan_id'],
            'tanggal'      => $data['tanggal'],
            'jumlah'       => $data['jumlah'],
            'created_by'   => $data['created_by'],
        ]);

        // 2. Sinkronisasi dengan Data Pelanggan
        $pelanggan = Pelanggan::find($data['pelanggan_id']);
        if ($pelanggan) {
            if ($data['jumlah_bulan'] > 0) {
                // Majuin Tanggal + Lunas
                $tanggalSekarang = $pelanggan->jatuh_tempo ? Carbon::parse($pelanggan->jatuh_tempo) : Carbon::parse($data['tanggal']);
                $pelanggan->update([
                    'status_pembayaran' => 'Lunas',
                    'jatuh_tempo'       => $tanggalSekarang->addMonths($data['jumlah_bulan'])->format('Y-m-d'),
                    'updated_by'        => auth()->user()->username ?? 'SYSTEM'
                ]);
            } else {
                // Cuma Lunasin tanpa majuin bulan
                $pelanggan->update(['status_pembayaran' => 'Lunas']);
            }
        }

        return back()->with('success', 'Transaksi dicatat & Tanggal jatuh tempo diperbarui!');
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
