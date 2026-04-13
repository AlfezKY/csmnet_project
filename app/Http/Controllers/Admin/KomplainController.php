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
    // [ADMIN] Nampilin Semua Komplain
    public function index(Request $request)
    {
        $query = Komplain::with(['pelanggan', 'pelanggan.paket']);

        // 1. Filter Pencarian (Nama Pelanggan atau Detail Keluhan)
        if ($request->filled('q')) {
            $search = $request->q;
            $query->where(function ($q) use ($search) {
                $q->where('keluhan', 'like', "%{$search}%")
                    ->orWhereHas('pelanggan', function ($subQ) use ($search) {
                        $subQ->where('nama_pelanggan', 'like', "%{$search}%");
                    });
            });
        }

        // 2. Filter Kategori
        if ($request->filled('kategori')) {
            $query->where('kategori', $request->kategori);
        }

        // 3. Filter Rentang Tanggal
        if ($request->filled('start_date')) {
            $query->whereDate('tanggal', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('tanggal', '<=', $request->end_date);
        }

        $query->latest();

        // ==========================================
        // 4. FITUR EXPORT EXCEL (.xls Native)
        // ==========================================
        if ($request->has('export')) {
            $komplains = $query->get();
            $filename = "Data_Komplain_" . date('Y-m-d') . ".xls";

            $headers = [
                "Content-type"        => "application/vnd.ms-excel",
                "Content-Disposition" => "attachment; filename=$filename",
                "Pragma"              => "no-cache",
                "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
                "Expires"             => "0"
            ];

            $callback = function () use ($komplains) {
                echo '<table border="1">';
                echo '<tr>
                        <th style="background-color:#2563eb; color:#ffffff;">Tanggal</th>
                        <th style="background-color:#2563eb; color:#ffffff;">Nama Pelanggan</th>
                        <th style="background-color:#2563eb; color:#ffffff;">No WA</th>
                        <th style="background-color:#2563eb; color:#ffffff;">Kategori</th>
                        <th style="background-color:#2563eb; color:#ffffff;">Keluhan</th>
                        <th style="background-color:#2563eb; color:#ffffff;">Prioritas</th>
                        <th style="background-color:#2563eb; color:#ffffff;">Status</th>
                      </tr>';

                foreach ($komplains as $kp) {
                    $tanggal = \Carbon\Carbon::parse($kp->tanggal)->format('Y-m-d');
                    $nama = $kp->pelanggan->nama_pelanggan ?? 'Data Dihapus';
                    $wa = $kp->pelanggan->no_wa ?? '-';
                    $kategori = $kp->kategori ?? 'Belum Diatur';

                    echo "<tr>
                            <td>{$tanggal}</td>
                            <td>{$nama}</td>
                            <td>'{$wa}</td>
                            <td>{$kategori}</td>
                            <td>{$kp->keluhan}</td>
                            <td>{$kp->priority}</td>
                            <td>{$kp->status}</td>
                          </tr>";
                }
                echo '</table>';
            };

            return response()->stream($callback, 200, $headers);
        }

        $komplains = $query->get();
        $pelanggans = Pelanggan::all();

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
