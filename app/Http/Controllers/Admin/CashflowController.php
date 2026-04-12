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
                    'keterangan'  => 'Tagihan: ' . ($item->pelanggan->nama_pelanggan ?? 'Pelanggan Dihapus'),
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
                    'keterangan'  => $item->kategori . ($item->deskripsi ? ' - ' . $item->deskripsi : ''),
                    'pemasukkan'  => 0,
                    'pengeluaran' => $item->jumlah,
                    'created_at'  => $item->created_at
                ];
            });

        // Gabungkan dan urutkan berdasarkan tanggal terbaru
        $details = collect($transaksis)->merge($pengeluarans)->sortByDesc('tanggal')->values()->all();

        // --- 2. BUAT DATA RINGKASAN PER TANGGAL ---
        $summary = [];
        $totalPemasukkanBulanIni = 0;
        $totalPengeluaranBulanIni = 0;

        for ($i = 1; $i <= $daysInMonth; $i++) {
            $dateStr = sprintf('%04d-%02d-%02d', $year, $month, $i);

            // Hitung total di hari itu
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

        // Balikkan urutan ringkasan biar tanggal terbaru di atas
        $summary = array_reverse($summary);

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
