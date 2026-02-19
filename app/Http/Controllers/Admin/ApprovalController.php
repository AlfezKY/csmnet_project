<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pelanggan;
use Illuminate\Http\Request;

class ApprovalController extends Controller
{
    // Cuma tampilin yang Pending
    public function index()
    {
        $pelanggans = Pelanggan::with('paket')
            ->where('status', 'Pending')
            ->latest()
            ->get();
        return view('admin.approval.index', compact('pelanggans'));
    }

    // Untuk fungsi Setujui / Tolak per baris
    public function action(Request $request, string $id)
    {
        $pelanggan = Pelanggan::findOrFail($id);
        $action = $request->input('action'); // 'approve' atau 'reject'

        if ($action == 'approve') {
            $pelanggan->update(['status' => 'Active', 'updated_by' => auth()->user()->username ?? 'SYSTEM']);
            return back()->with('success', 'Pelanggan berhasil disetujui dan aktif!');
        } elseif ($action == 'reject') {
            $pelanggan->update(['status' => 'Non Active', 'updated_by' => auth()->user()->username ?? 'SYSTEM']);
            return back()->with('success', 'Pendaftaran pelanggan ditolak (Non-Active)!');
        }

        return back()->with('error', 'Aksi tidak valid.');
    }

    // Untuk fungsi Checkbox Massal (Bulk)
    public function bulkAction(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'action' => 'required|in:approve,reject'
        ]);

        $status = $request->action == 'approve' ? 'Active' : 'Non Active';
        $pesan = $request->action == 'approve' ? 'disetujui' : 'ditolak';

        // Update massal berdasarkan ID yang dicentang
        Pelanggan::whereIn('id', $request->ids)->update([
            'status' => $status,
            'updated_by' => auth()->user()->username ?? 'SYSTEM'
        ]);

        return back()->with('success', count($request->ids) . " Pelanggan berhasil $pesan secara massal!");
    }
}
