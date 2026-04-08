<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pengeluaran;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;

class PengeluaranController extends Controller
{
    public function index()
    {
        // Tampilkan pengeluaran terbaru di atas
        $pengeluarans = Pengeluaran::orderBy('tanggal', 'desc')->latest()->get();
        return view('admin.pengeluaran.index', compact('pengeluarans'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'tanggal'     => 'required|date',
            'kategori'    => 'required|string|max:255',
            'deskripsi'   => 'nullable|string',
            'jumlah'      => 'required|integer|min:0',
            'bukti_bayar' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048', // Max 2MB
        ]);

        $filePath = null;

        if ($request->hasFile('bukti_bayar')) {
            $file = $request->file('bukti_bayar');
            $fileName = time() . '_' . Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('UploadFiles'), $fileName);
            $filePath = 'UploadFiles/' . $fileName;
        }

        Pengeluaran::create([
            'tanggal'     => $request->tanggal,
            'kategori'    => $request->kategori,
            'deskripsi'   => $request->deskripsi,
            'jumlah'      => $request->jumlah,
            'bukti_bayar' => $filePath,
            'created_by'  => auth()->user()->username ?? 'SYSTEM',
        ]);

        return back()->with('success', 'Data pengeluaran berhasil dicatat!');
    }

    public function update(Request $request, Pengeluaran $pengeluaran)
    {
        $request->validate([
            'tanggal'     => 'required|date',
            'kategori'    => 'required|string|max:255',
            'deskripsi'   => 'nullable|string',
            'jumlah'      => 'required|integer|min:0',
            'bukti_bayar' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);

        $filePath = $pengeluaran->bukti_bayar;

        if ($request->hasFile('bukti_bayar')) {
            // Hapus file lama jika ada
            if ($filePath && File::exists(public_path($filePath))) {
                File::delete(public_path($filePath));
            }

            // Upload file baru
            $file = $request->file('bukti_bayar');
            $fileName = time() . '_' . Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('UploadFiles'), $fileName);
            $filePath = 'UploadFiles/' . $fileName;
        }

        $pengeluaran->update([
            'tanggal'     => $request->tanggal,
            'kategori'    => $request->kategori,
            'deskripsi'   => $request->deskripsi,
            'jumlah'      => $request->jumlah,
            'bukti_bayar' => $filePath,
            'updated_by'  => auth()->user()->username ?? 'SYSTEM',
        ]);

        return back()->with('success', 'Data pengeluaran berhasil diperbarui!');
    }

    public function destroy(Pengeluaran $pengeluaran)
    {
        // Hapus file fisik sebelum hapus database
        if ($pengeluaran->bukti_bayar && File::exists(public_path($pengeluaran->bukti_bayar))) {
            File::delete(public_path($pengeluaran->bukti_bayar));
        }

        $pengeluaran->delete();
        return back()->with('success', 'Data pengeluaran berhasil dihapus!');
    }
}
