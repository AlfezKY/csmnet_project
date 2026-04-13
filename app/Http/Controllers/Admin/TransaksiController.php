<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Transaksi;
use App\Models\Pelanggan;
use Illuminate\Http\Request;
use Carbon\Carbon; // WAJIB TAMBAH

class TransaksiController extends Controller
{
    public function index(Request $request) // Tambahkan Request $request
    {
        $query = Transaksi::with(['pelanggan', 'pelanggan.paket']);

        // 1. Pencarian (Nama Pelanggan)
        if ($request->filled('q')) {
            $search = $request->q;
            $query->whereHas('pelanggan', function ($q) use ($search) {
                $q->where('nama_pelanggan', 'like', "%{$search}%");
            });
        }

        // 2. Filter by Paket (Relasi ke Pelanggan)
        if ($request->filled('paket_id')) {
            $paket_id = $request->paket_id;
            $query->whereHas('pelanggan', function ($q) use ($paket_id) {
                $q->where('paket_id', $paket_id);
            });
        }

        // 3. Filter Rentang Tanggal Transaksi
        if ($request->filled('start_date')) {
            $query->whereDate('tanggal', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('tanggal', '<=', $request->end_date);
        }

        // Urutkan dari transaksi terbaru
        $query->latest();

        // ==========================================
        // 4. FITUR EXPORT EXCEL (.xls Native)
        // ==========================================
        if ($request->has('export')) {
            $transaksis = $query->get();
            $filename = "Data_Transaksi_" . date('Y-m-d') . ".xls";

            $headers = [
                "Content-type"        => "application/vnd.ms-excel",
                "Content-Disposition" => "attachment; filename=$filename",
                "Pragma"              => "no-cache",
                "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
                "Expires"             => "0"
            ];

            $callback = function () use ($transaksis) {
                echo '<table border="1">';
                echo '<tr>
                        <th style="background-color:#2563eb; color:#ffffff;">Tanggal Transaksi</th>
                        <th style="background-color:#2563eb; color:#ffffff;">Nama Pelanggan</th>
                        <th style="background-color:#2563eb; color:#ffffff;">Paket Internet</th>
                        <th style="background-color:#2563eb; color:#ffffff;">Nominal Pembayaran</th>
                        <th style="background-color:#2563eb; color:#ffffff;">Dicatat Oleh</th>
                      </tr>';

                foreach ($transaksis as $trx) {
                    $tanggal = \Carbon\Carbon::parse($trx->tanggal)->format('Y-m-d');
                    $nama = $trx->pelanggan->nama_pelanggan ?? 'Data Dihapus';
                    $paket = $trx->pelanggan->paket->nama_paket ?? 'Tanpa Paket';

                    echo "<tr>
                            <td>{$tanggal}</td>
                            <td>{$nama}</td>
                            <td>{$paket}</td>
                            <td>{$trx->jumlah}</td>
                            <td>{$trx->created_by}</td>
                          </tr>";
                }
                echo '</table>';
            };

            return response()->stream($callback, 200, $headers);
        }

        $transaksis = $query->get();
        // Pastikan ambil relasi paket untuk kalkulasi JS di View
        $pelanggans = Pelanggan::with('paket')->where('status', 'Active')->get();
        $pakets = \App\Models\Paket::where('status', 'Active')->get(); // Buat dropdown filter

        return view('admin.transaksi.index', compact('transaksis', 'pelanggans', 'pakets'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'pelanggan_id' => 'required|exists:pelanggans,id',
            'tanggal'      => 'required|date',
            'jumlah'       => 'required|numeric|min:0',
            'jumlah_bulan' => 'required|integer|min:0', // 0 jika cuma cicil/bayar tunggakan
        ]);

        $data['created_by'] = auth()->user()->username ?? 'SYSTEM';

        // 1. Simpan Transaksi
        Transaksi::create([
            'pelanggan_id' => $data['pelanggan_id'],
            'tanggal'      => $data['tanggal'],
            'jumlah'       => $data['jumlah'],
            'created_by'   => $data['created_by'],
        ]);

        // 2. Sinkronisasi dengan Data Pelanggan
        $pelanggan = Pelanggan::find($data['pelanggan_id']);
        if ($pelanggan) {
            if ($data['jumlah_bulan'] > 0) {
                // Majuin Tanggal + Lunas
                $tanggalSekarang = $pelanggan->jatuh_tempo ? Carbon::parse($pelanggan->jatuh_tempo) : Carbon::parse($data['tanggal']);
                $pelanggan->update([
                    'status_pembayaran' => 'Lunas',
                    'jatuh_tempo'       => $tanggalSekarang->addMonths($data['jumlah_bulan'])->format('Y-m-d'),
                    'updated_by'        => auth()->user()->username ?? 'SYSTEM'
                ]);
            } else {
                // Cuma Lunasin tanpa majuin bulan
                $pelanggan->update(['status_pembayaran' => 'Lunas']);
            }
        }

        return back()->with('success', 'Transaksi dicatat & Tanggal jatuh tempo diperbarui!');
    }

    public function update(Request $request, Transaksi $transaksi)
    {
        $data = $request->validate([
            'pelanggan_id' => 'required|exists:pelanggans,id',
            'tanggal'      => 'required|date',
            'jumlah'       => 'required|numeric|min:0',
        ]);

        $data['updated_by'] = auth()->user()->username ?? 'SYSTEM';
        $transaksi->update($data);

        return back()->with('success', 'Data transaksi berhasil diperbarui!');
    }

    public function destroy(Transaksi $transaksi)
    {
        $transaksi->delete();
        return back()->with('success', 'Riwayat transaksi berhasil dihapus!');
    }
}
