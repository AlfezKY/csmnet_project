<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Transaksi;
use App\Models\Pengeluaran;
use Illuminate\Http\Request;
use Carbon\Carbon;

class CashflowController extends Controller
{
    public function index(Request $request)
    {
        // Default ke bulan & tahun saat ini kalau tidak ada filter
        $month = $request->input('month', Carbon::now()->format('m'));
        $year = $request->input('year', Carbon::now()->format('Y'));

        $date = Carbon::createFromDate($year, $month, 1);
        $daysInMonth = $date->daysInMonth;

        // --- 1. AMBIL DATA DETAIL ---
        // Pemasukkan (Dari Transaksi Pelanggan)
        $transaksis = Transaksi::with('pelanggan')
            ->whereMonth('tanggal', $month)
            ->whereYear('tanggal', $year)
            ->get()
            ->map(function ($item) {
                return [
                    'tanggal'     => $item->tanggal,
                    'kategori'    => 'Pembayaran Tagihan', // Kategori dipisah
                    'keterangan'  => $item->pelanggan->nama_pelanggan ?? 'Pelanggan Dihapus', // Keterangan asli
                    'pemasukkan'  => $item->jumlah,
                    'pengeluaran' => 0,
                    'created_at'  => $item->created_at
                ];
            });

        // Pengeluaran
        $pengeluarans = Pengeluaran::whereMonth('tanggal', $month)
            ->whereYear('tanggal', $year)
            ->get()
            ->map(function ($item) {
                return [
                    'tanggal'     => $item->tanggal,
                    'kategori'    => $item->kategori, // Kategori dipisah
                    'keterangan'  => $item->deskripsi ?: '-', // Keterangan asli
                    'pemasukkan'  => 0,
                    'pengeluaran' => $item->jumlah,
                    'created_at'  => $item->created_at
                ];
            });

        // Gabungkan dan urutkan berdasarkan tanggal terbaru
        $details = collect($transaksis)->merge($pengeluarans)->sortByDesc('tanggal')->values();

        // --- 2. FITUR PENCARIAN (Berdasarkan Keterangan ATAU Kategori) ---
        if ($request->filled('q')) {
            $search = strtolower($request->q);
            $details = $details->filter(function ($item) use ($search) {
                return str_contains(strtolower($item['keterangan']), $search) || str_contains(strtolower($item['kategori']), $search);
            })->values();
        }

        $details = $details->all();

        // --- 3. BUAT DATA RINGKASAN PER TANGGAL ---
        $summary = [];
        $totalPemasukkanBulanIni = 0;
        $totalPengeluaranBulanIni = 0;

        for ($i = 1; $i <= $daysInMonth; $i++) {
            $dateStr = sprintf('%04d-%02d-%02d', $year, $month, $i);

            $income = collect($details)->where('tanggal', $dateStr)->sum('pemasukkan');
            $expense = collect($details)->where('tanggal', $dateStr)->sum('pengeluaran');

            $summary[] = [
                'tanggal'     => $dateStr,
                'pemasukkan'  => $income,
                'pengeluaran' => $expense,
            ];

            $totalPemasukkanBulanIni += $income;
            $totalPengeluaranBulanIni += $expense;
        }

        $summary = array_reverse($summary);

        // --- 4. FITUR EXPORT EXCEL (.xls Native) ---
        if ($request->has('export')) {
            $filename = "Laporan_Cashflow_" . $year . "_" . $month . ".xls";
            $headers = [
                "Content-type"        => "application/vnd.ms-excel",
                "Content-Disposition" => "attachment; filename=$filename",
                "Pragma"              => "no-cache",
                "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
                "Expires"             => "0"
            ];

            $callback = function () use ($details, $month, $year) {
                echo '<table border="1">';
                echo '<tr>
                        <th style="background-color:#2563eb; color:#ffffff;">Tanggal</th>
                        <th style="background-color:#2563eb; color:#ffffff;">Kategori</th>
                        <th style="background-color:#2563eb; color:#ffffff;">Keterangan</th>
                        <th style="background-color:#2563eb; color:#ffffff;">Pemasukkan (Rp)</th>
                        <th style="background-color:#2563eb; color:#ffffff;">Pengeluaran (Rp)</th>
                      </tr>';

                foreach ($details as $dtl) {
                    $tanggal = \Carbon\Carbon::parse($dtl['tanggal'])->format('d/m/Y');
                    echo "<tr>
                            <td>{$tanggal}</td>
                            <td>{$dtl['kategori']}</td>
                            <td>{$dtl['keterangan']}</td>
                            <td>{$dtl['pemasukkan']}</td>
                            <td>{$dtl['pengeluaran']}</td>
                          </tr>";
                }
                echo '</table>';
            };

            return response()->stream($callback, 200, $headers);
        }

        return view('owner.cashflow.index', compact(
            'summary',
            'details',
            'month',
            'year',
            'totalPemasukkanBulanIni',
            'totalPengeluaranBulanIni'
        ));
    }
}
