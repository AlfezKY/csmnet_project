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
        return view('admin.paket.index', compact('pakets'));
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

        $data['updated_by'] = auth()->user()->username ?? 'SYSTEM';
        $paket->update($data);

        return back()->with('success', 'Data paket berhasil diperbarui!');
    }

    public function destroy(Paket $paket)
    {
        $paket->delete();
        return back()->with('success', 'Paket internet berhasil dihapus!');
    }
}
