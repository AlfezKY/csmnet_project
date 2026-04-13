<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pengeluaran;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;

class PengeluaranController extends Controller
{
    public function index(Request $request)
    {
        $query = Pengeluaran::query();

        // 1. Fitur Search (Kategori atau Deskripsi)
        if ($request->filled('q')) {
            $search = $request->q;
            $query->where(function ($q) use ($search) {
                $q->where('kategori', 'like', "%{$search}%")
                    ->orWhere('deskripsi', 'like', "%{$search}%");
            });
        }

        // 2. Filter Kategori Spesifik
        if ($request->filled('kategori')) {
            $query->where('kategori', $request->kategori);
        }

        // 3. Filter Tanggal (Start & End Date)
        if ($request->filled('start_date')) {
            $query->whereDate('tanggal', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('tanggal', '<=', $request->end_date);
        }

        $query->orderBy('tanggal', 'desc')->latest();

        // ==========================================
        // 4. FITUR EXPORT EXCEL (.xls Native)
        // ==========================================
        if ($request->has('export')) {
            $data = $query->get();
            $filename = "Data_Pengeluaran_" . date('Y-m-d') . ".xls";

            $headers = [
                "Content-type"        => "application/vnd.ms-excel",
                "Content-Disposition" => "attachment; filename=$filename",
                "Pragma"              => "no-cache",
                "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
                "Expires"             => "0"
            ];

            $callback = function () use ($data) {
                echo '<table border="1">';
                echo '<tr>
                        <th style="background-color:#2563eb; color:#ffffff;">Tanggal</th>
                        <th style="background-color:#2563eb; color:#ffffff;">Kategori</th>
                        <th style="background-color:#2563eb; color:#ffffff;">Deskripsi</th>
                        <th style="background-color:#2563eb; color:#ffffff;">Jumlah (Rp)</th>
                        <th style="background-color:#2563eb; color:#ffffff;">Dicatat Oleh</th>
                      </tr>';

                foreach ($data as $row) {
                    $tanggal = \Carbon\Carbon::parse($row->tanggal)->format('d/m/Y');
                    echo "<tr>
                            <td>{$tanggal}</td>
                            <td>{$row->kategori}</td>
                            <td>{$row->deskripsi}</td>
                            <td>{$row->jumlah}</td>
                            <td>{$row->created_by}</td>
                          </tr>";
                }
                echo '</table>';
            };

            return response()->stream($callback, 200, $headers);
        }

        $pengeluarans = $query->get();

        // List Kategori untuk Dropdown Filter Popover
        $kategoriList = [
            'Langganan ISP Induk',
            'Pembelian Perangkat (Router, Modem, Kabel)',
            'Perawatan Jaringan',
            'Gaji Karyawan / Teknisi',
            'Biaya Operasional Kantor',
            'Listrik',
            'Internet Kantor',
            'Transportasi',
            'Lainnya'
        ];

        return view('admin.pengeluaran.index', compact('pengeluarans', 'kategoriList'));
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
