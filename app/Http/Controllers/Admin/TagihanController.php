<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pelanggan;
use Illuminate\Http\Request;

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
        $pelanggan = Pelanggan::findOrFail($id);

        $pelanggan->update([
            'status_pembayaran' => 'Lunas',
            'updated_by' => auth()->user()->username ?? 'SYSTEM'
        ]);

        return back()->with('success', "Tagihan {$pelanggan->nama_pelanggan} berhasil ditandai Lunas!");
    }

    // Aksi Tandai Lunas (Massal / Checkbox)
    public function bulkAction(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
        ]);

        Pelanggan::whereIn('id', $request->ids)->update([
            'status_pembayaran' => 'Lunas',
            'updated_by' => auth()->user()->username ?? 'SYSTEM'
        ]);

        return back()->with('success', count($request->ids) . ' Tagihan pelanggan berhasil ditandai Lunas secara massal!');
    }
}
