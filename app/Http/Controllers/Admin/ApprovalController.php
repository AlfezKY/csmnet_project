<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pelanggan;
use Illuminate\Http\Request;

class ApprovalController extends Controller
{
    // Cuma tampilin yang Pending
    public function index(Request $request)
    {
        $query = Pelanggan::with('paket')->where('status', 'Pending');

        // 1. Pencarian Detail (Nama atau Alamat)
        if ($request->filled('q')) {
            $query->where(function ($q) use ($request) {
                $q->where('nama_pelanggan', 'like', '%' . $request->q . '%')
                    ->orWhere('alamat', 'like', '%' . $request->q . '%');
            });
        }

        // 2. Filter by Date Range (Start Date & End Date)
        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        // Selalu urutkan dari yang terbaru
        $query->orderByRaw('DATE(created_at) = CURDATE() DESC')
              ->orderBy('created_at', 'asc');

        // 3. FITUR EXPORT EXCEL (.xls Native)
        if ($request->has('export')) {
            $pelanggans = $query->get();
            $filename = "Data_Approval_" . date('Y-m-d') . ".xls";

            $headers = [
                "Content-type"        => "application/vnd.ms-excel",
                "Content-Disposition" => "attachment; filename=$filename",
                "Pragma"              => "no-cache",
                "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
                "Expires"             => "0"
            ];

            $callback = function () use ($pelanggans) {
                // Render Excel menggunakan HTML Table (Trik Native PHP)
                echo '<table border="1">';
                echo '<tr>
                        <th style="background-color:#1e40af; color:#ffffff;">Nama Lengkap</th>
                        <th style="background-color:#1e40af; color:#ffffff;">Paket Internet</th>
                        <th style="background-color:#1e40af; color:#ffffff;">Alamat</th>
                        <th style="background-color:#1e40af; color:#ffffff;">No WA</th>
                        <th style="background-color:#1e40af; color:#ffffff;">Tanggal Daftar</th>
                      </tr>';

                foreach ($pelanggans as $plg) {
                    $paket = $plg->paket->nama_paket ?? 'Tanpa Paket';
                    $tanggal = $plg->created_at->format('Y-m-d H:i');
                    // Tanda kutip tunggal (') sebelum no_wa biar angka 0 di depan gak hilang di Excel
                    echo "<tr>
                            <td>{$plg->nama_pelanggan}</td>
                            <td>{$paket}</td>
                            <td>{$plg->alamat}</td>
                            <td>'{$plg->no_wa}</td>
                            <td>{$tanggal}</td>
                          </tr>";
                }
                echo '</table>';
            };

            return response()->stream($callback, 200, $headers);
        }

        $pelanggans = $query->get();
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
