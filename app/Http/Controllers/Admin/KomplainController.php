<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Komplain;
use App\Models\Pelanggan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View; // WAJIB TAMBAH INI

class KomplainController extends Controller
{
    // List Kategori Dummy (Taruh sini biar gampang dipanggil di Index)
    protected $kategoriList = [
        'Kabel Putus',
        'Modem LOS Merah',
        'Internet Lambat/RTO',
        'Ganti Password WiFi',
        'Pembayaran/Tagihan',
        'Lain-lain'
    ];

    // Bikin constructor buat ngeshare $kategoriList ke semua view di controller ini otomatis
    public function __construct()
    {
        View::share('kategoriList', $this->kategoriList);
    }

    // [ADMIN] Nampilin Semua Komplain
    public function index()
    {
        $komplains = Komplain::with(['pelanggan', 'pelanggan.paket'])->latest()->get();
        $pelanggans = Pelanggan::all();
        // $kategoriList udah di-share dari constructor, nggak usah dikirim manual lagi

        return view('admin.komplain.index', compact('komplains', 'pelanggans'));
    }

    // [ADMIN] Simpan Komplain Manual
    public function storeAdmin(Request $request)
    {
        $request->validate([
            'pelanggan_id' => 'required|exists:pelanggans,id',
            'keluhan'      => 'required|string',
            'kategori'     => 'required|string',
            'priority'     => 'required|in:Low,Medium,High',
            'status'       => 'required|in:Not Yet,In Progress,Done',
        ]);

        Komplain::create([
            'pelanggan_id' => $request->pelanggan_id,
            'tanggal'      => now()->format('Y-m-d'),
            'keluhan'      => $request->keluhan,
            'kategori'     => $request->kategori,
            'priority'     => $request->priority,
            'status'       => $request->status,
            'created_by'   => auth()->user()->username ?? 'SYSTEM',
        ]);

        return back()->with('success', 'Komplain baru berhasil dicatat secara manual!');
    }

    // [PUBLIC/CLIENT] Nangkep Submit Form dari Landing Page
    public function store(Request $request)
    {
        $request->validate([
            'keluhan'  => 'required|string',
        ]);

        $pelanggan = Pelanggan::where('user_id', auth()->id())->first();

        if (!$pelanggan) {
            return back()->with('error', 'Gagal! Akun Anda belum tertaut dengan data Pelanggan.');
        }

        Komplain::create([
            'pelanggan_id' => $pelanggan->id,
            'tanggal'      => now()->format('Y-m-d'),
            'keluhan'      => $request->keluhan,
            'kategori'     => null,
            'priority'     => 'Medium',
            'status'       => 'Not Yet',
            'created_by'   => auth()->user()->username,
        ]);

        return back()->with('success', 'Laporan gangguan berhasil dikirim. Tim teknisi kami akan segera menindaklanjuti!');
    }

    // [ADMIN] Update Status & Prioritas Komplain
    public function update(Request $request, Komplain $komplain)
    {
        $data = $request->validate([
            'kategori' => 'nullable|string',
            'priority' => 'required|in:Low,Medium,High',
            'status'   => 'required|in:Not Yet,In Progress,Done',
        ]);

        $data['updated_by'] = auth()->user()->username ?? 'SYSTEM';
        $komplain->update($data);

        return back()->with('success', 'Status & Kategori komplain berhasil diperbarui!');
    }

    // [ADMIN] Hapus Komplain
    public function destroy(Komplain $komplain)
    {
        $komplain->delete();
        return back()->with('success', 'Riwayat komplain berhasil dihapus!');
    }
}
