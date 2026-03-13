<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pelanggan;
use App\Models\Transaksi; // WAJIB TAMBAH INI
use Illuminate\Http\Request;
use Carbon\Carbon;

class TagihanController extends Controller
{
    public function index()
    {
        $pelanggans = Pelanggan::with('paket')
            ->where('status', 'Active')
            ->where('status_pembayaran', 'Belum Lunas')
            ->orderBy('jatuh_tempo', 'asc')
            ->get();

        return view('admin.tagihan.index', compact('pelanggans'));
    }

    public function action(Request $request, string $id)
    {
        $request->validate([
            'jumlah_bulan' => 'required|integer|min:1'
        ]);

        $pelanggan = Pelanggan::with('paket')->findOrFail($id);

        // 1. Majuin Tanggal Pelanggan
        $tanggalSekarang = $pelanggan->jatuh_tempo ? Carbon::parse($pelanggan->jatuh_tempo) : Carbon::now();
        $jatuhTempoBaru = $tanggalSekarang->addMonths($request->jumlah_bulan);

        $pelanggan->update([
            'status_pembayaran' => 'Lunas',
            'jatuh_tempo'       => $jatuhTempoBaru->format('Y-m-d'),
            'updated_by'        => auth()->user()->username ?? 'SYSTEM'
        ]);

        // 2. OTOMATIS CATAT KE TABEL TRANSAKSI
        Transaksi::create([
            'pelanggan_id' => $pelanggan->id,
            'tanggal'      => now()->format('Y-m-d'),
            'jumlah'       => ($pelanggan->paket->harga ?? 0) * $request->jumlah_bulan,
            'created_by'   => auth()->user()->username ?? 'SYSTEM'
        ]);

        return back()->with('success', "Tagihan {$pelanggan->nama_pelanggan} Lunas & riwayat tercatat di Transaksi!");
    }

    public function bulkAction(Request $request)
    {
        $request->validate([
            'ids'          => 'required|array',
            'jumlah_bulan' => 'required|integer|min:1'
        ]);

        $pelanggans = Pelanggan::with('paket')->whereIn('id', $request->ids)->get();

        foreach ($pelanggans as $pelanggan) {
            $tanggalSekarang = $pelanggan->jatuh_tempo ? Carbon::parse($pelanggan->jatuh_tempo) : Carbon::now();
            $jatuhTempoBaru = $tanggalSekarang->addMonths($request->jumlah_bulan);

            $pelanggan->update([
                'status_pembayaran' => 'Lunas',
                'jatuh_tempo'       => $jatuhTempoBaru->format('Y-m-d'),
                'updated_by'        => auth()->user()->username ?? 'SYSTEM'
            ]);

            // OTOMATIS CATAT KE TABEL TRANSAKSI
            Transaksi::create([
                'pelanggan_id' => $pelanggan->id,
                'tanggal'      => now()->format('Y-m-d'),
                'jumlah'       => ($pelanggan->paket->harga ?? 0) * $request->jumlah_bulan,
                'created_by'   => auth()->user()->username ?? 'SYSTEM'
            ]);
        }

        return back()->with('success', count($request->ids) . " Tagihan massal Lunas & riwayat tercatat di Transaksi!");
    }
}
