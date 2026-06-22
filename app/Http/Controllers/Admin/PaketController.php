<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Paket;
use Illuminate\Http\Request;

class PaketController extends Controller
{
    public function index()
    {
        $pakets = Paket::latest()->get();
        // Hitung berapa paket yang lagi tayang di depan buat nampilin alert "Minimal 3" di View
        $tampilCount = Paket::where('is_show', true)->count();

        return view('admin.paket.index', compact('pakets', 'tampilCount'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nama_paket' => 'required|string|max:255',
            'kecepatan'  => 'required|string|max:255',
            'harga'      => 'required|numeric|min:0',
            'status'     => 'required|in:Active,Non Active,Pending',
            'deskripsi'  => 'nullable|string',
            'keypoint'   => 'nullable|string',
        ]);

        // Cek toggle (kalau dicentang nilainya 1, kalau nggak 0)
        $isShow = $request->has('is_show') ? 1 : 0;

        // VALIDASI MAKSIMAL 4 PAKET
        if ($isShow) {
            $currentShowCount = Paket::where('is_show', true)->count();
            if ($currentShowCount >= 4) {
                return back()->withErrors(['is_show' => 'Maksimal hanya 4 paket yang bisa ditampilkan di halaman depan!'])->withInput();
            }
        }

        $data['is_show']    = $isShow;
        $data['created_by'] = auth()->user()->username ?? 'SYSTEM';
        Paket::create($data);

        return back()->with('success', 'Paket internet baru berhasil ditambahkan!');
    }

    public function update(Request $request, Paket $paket)
    {
        $data = $request->validate([
            'nama_paket' => 'required|string|max:255',
            'kecepatan'  => 'required|string|max:255',
            'harga'      => 'required|numeric|min:0',
            'status'     => 'required|in:Active,Non Active,Pending',
            'deskripsi'  => 'nullable|string',
            'keypoint'   => 'nullable|string',
        ]);

        $isShow = $request->has('is_show') ? 1 : 0;

        // 1. CEK MAKSIMAL 4 (Kalau admin mau nambah tayangan ke etalase)
        if ($isShow && !$paket->is_show) {
            // Cek paket lain yang is_show = true (exclude paket ini sendiri)
            $currentShowCount = Paket::where('is_show', true)->where('id', '!=', $paket->id)->count();
            if ($currentShowCount >= 4) {
                return back()->withErrors(['is_show' => 'Maksimal hanya 4 paket yang bisa ditampilkan di halaman depan!'])->withInput();
            }
        }

        // 2. CEK MINIMAL 3 (Cegah Admin asal matiin centang)
        if (!$isShow && $paket->is_show) {
            $currentShowCount = Paket::where('is_show', true)->count();
            // Kalau sisa tayangan ngepas 3, tolak pencabutan centangnya!
            if ($currentShowCount <= 3) {
                return back()->withErrors(['is_show' => 'Gagal! Minimal harus tersisa 3 paket yang tayang di halaman depan.'])->withInput();
            }
        }

        $data['is_show']    = $isShow;
        $data['updated_by'] = auth()->user()->username ?? 'SYSTEM';
        $paket->update($data);

        return back()->with('success', 'Data paket berhasil diperbarui!');
    }

    public function destroy(Paket $paket)
    {
        // 1. CEK MINIMAL 3 SEBELUM DIHAPUS (Biar halaman depan gak bolong)
        if ($paket->is_show) {
            $currentShowCount = Paket::where('is_show', true)->count();
            if ($currentShowCount <= 3) {
                return back()->with('error', 'Tidak bisa menghapus! Paket ini sedang tayang dan minimal harus ada 3 paket di halaman depan.');
            }
        }

        // 2. SOFT DELETE (Ganti status jadi Non Active, jangan delete beneran biar data pelanggan aman)
        $paket->update([
            'status'     => 'Non Active',
            'is_show'    => 0, // Otomatis turun dari etalase depan
            'updated_by' => auth()->user()->username ?? 'SYSTEM'
        ]);

        return back()->with('success', 'Paket internet berhasil dinonaktifkan! Data pelanggan tetap aman.');
    }
}