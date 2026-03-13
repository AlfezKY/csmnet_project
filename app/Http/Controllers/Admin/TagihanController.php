<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pelanggan;
use Illuminate\Http\Request;
use Carbon\Carbon; // <--- WAJIB TAMBAH INI BUAT NGITUNG TANGGAL

class TagihanController extends Controller
{
    // Nampilin data yang nunggak
    public function index()
    {
        $pelanggans = Pelanggan::with('paket')
            ->where('status', 'Active')
            ->where('status_pembayaran', 'Belum Lunas')
            ->orderBy('jatuh_tempo', 'asc')
            ->get();

        return view('admin.tagihan.index', compact('pelanggans'));
    }

    // Aksi Tandai Lunas (Satuan)
    public function action(Request $request, string $id)
    {
        $request->validate([
            'jumlah_bulan' => 'required|integer|min:1'
        ]);

        $pelanggan = Pelanggan::findOrFail($id);

        // Hitung tanggal jatuh tempo yang baru (Majuin X bulan)
        $tanggalSekarang = $pelanggan->jatuh_tempo ? Carbon::parse($pelanggan->jatuh_tempo) : Carbon::now();
        $jatuhTempoBaru = $tanggalSekarang->addMonths($request->jumlah_bulan);

        $pelanggan->update([
            'status_pembayaran' => 'Lunas',
            'jatuh_tempo'       => $jatuhTempoBaru->format('Y-m-d'), // Simpan tanggal barunya
            'updated_by'        => auth()->user()->username ?? 'SYSTEM'
        ]);

        return back()->with('success', "Tagihan {$pelanggan->nama_pelanggan} berhasil dibayar untuk {$request->jumlah_bulan} bulan ke depan!");
    }

    // Aksi Tandai Lunas (Massal / Checkbox)
    public function bulkAction(Request $request)
    {
        $request->validate([
            'ids'          => 'required|array',
            'jumlah_bulan' => 'required|integer|min:1'
        ]);

        // Karena tiap pelanggan tanggalnya beda-beda, kita harus update satu-satu pake looping
        $pelanggans = Pelanggan::whereIn('id', $request->ids)->get();

        foreach ($pelanggans as $pelanggan) {
            $tanggalSekarang = $pelanggan->jatuh_tempo ? Carbon::parse($pelanggan->jatuh_tempo) : Carbon::now();
            $jatuhTempoBaru = $tanggalSekarang->addMonths($request->jumlah_bulan);

            $pelanggan->update([
                'status_pembayaran' => 'Lunas',
                'jatuh_tempo'       => $jatuhTempoBaru->format('Y-m-d'),
                'updated_by'        => auth()->user()->username ?? 'SYSTEM'
            ]);
        }

        return back()->with('success', count($request->ids) . " Tagihan pelanggan berhasil diproses untuk {$request->jumlah_bulan} bulan!");
    }
}
