<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Transaksi;
use App\Models\Pengeluaran;
use App\Models\Komplain;
use App\Models\Pelanggan;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class LaporanController extends Controller
{
    public function index(Request $request)
    {
        // ==========================================
        // 1. FILTER UNTUK KARTU UTAMA
        // ==========================================
        $filterType = $request->filter_type ?? 'all'; // all, month, range

        $qTransaksi = Transaksi::query();
        $qPengeluaran = Pengeluaran::query();

        if ($filterType === 'month' && $request->month) {
            $month = date('m', strtotime($request->month));
            $year = date('Y', strtotime($request->month));
            $qTransaksi->whereMonth('tanggal', $month)->whereYear('tanggal', $year);
            $qPengeluaran->whereMonth('tanggal', $month)->whereYear('tanggal', $year);
        } elseif ($filterType === 'range' && $request->start_date && $request->end_date) {
            $qTransaksi->whereBetween('tanggal', [$request->start_date, $request->end_date]);
            $qPengeluaran->whereBetween('tanggal', [$request->start_date, $request->end_date]);
        }

        $totalPemasukan = $qTransaksi->sum('jumlah');
        $totalPengeluaran = $qPengeluaran->sum('jumlah');
        $labaBersih = $totalPemasukan - $totalPengeluaran;

        // [BARU] Total Piutang (Absolute, tidak terpengaruh filter tanggal)
        $totalPiutang = Pelanggan::where('pelanggans.status', 'Active')
            ->where('pelanggans.status_pembayaran', 'Belum Lunas')
            ->join('pakets', 'pelanggans.paket_id', '=', 'pakets.id')
            ->sum('pakets.harga');

        // [BARU] Breakdown Pengeluaran (Terpengaruh filter tanggal)
        $pengeluaranKategori = (clone $qPengeluaran)
            ->selectRaw('kategori, SUM(jumlah) as total')
            ->groupBy('kategori')
            ->get();

        // ==========================================
        // 2. DATA BAR CHART (TREN KEUANGAN 12 BULAN)
        // ==========================================
        $chartYear = $request->chart_year ?? date('Y');

        $pemasukanBulananRaw = Transaksi::selectRaw('MONTH(tanggal) as month, SUM(jumlah) as total')
            ->whereYear('tanggal', $chartYear)
            ->groupBy('month')
            ->pluck('total', 'month')->toArray();

        $pengeluaranBulananRaw = Pengeluaran::selectRaw('MONTH(tanggal) as month, SUM(jumlah) as total')
            ->whereYear('tanggal', $chartYear)
            ->groupBy('month')
            ->pluck('total', 'month')->toArray();

        $pemasukanBulanan = [];
        $pengeluaranBulanan = [];
        for ($i = 1; $i <= 12; $i++) {
            $pemasukanBulanan[] = $pemasukanBulananRaw[$i] ?? 0;
            $pengeluaranBulanan[] = $pengeluaranBulananRaw[$i] ?? 0;
        }

        // ==========================================
        // 3. DATA KOMPLAIN (DENGAN FILTER BULAN KHUSUS)
        // ==========================================
        $komplainMonthFilter = $request->komplain_month ?? date('Y-m');
        $komplainMonth = date('m', strtotime($komplainMonthFilter));
        $komplainYear = date('Y', strtotime($komplainMonthFilter));

        $komplains = Komplain::whereMonth('tanggal', $komplainMonth)
            ->whereYear('tanggal', $komplainYear)
            ->get();

        $komplainStats = [
            'total'       => $komplains->count(),
            'done'        => $komplains->where('status', 'Done')->count(),
            'in_progress' => $komplains->where('status', 'In Progress')->count(),
            'not_yet'     => $komplains->where('status', 'Not Yet')->count(),
        ];

        $kategoriDefault = [
            'Internet Mati' => 0,
            'LOS Merah' => 0,
            'Internet Lambat' => 0,
            'Lain-lain' => 0
        ];

        $kategoriDariDb = $komplains->groupBy('kategori')->map(function ($item) {
            return $item->count();
        })->toArray();

        $kategoriFinal = array_merge($kategoriDefault, $kategoriDariDb);

        $komplainKategori = [];
        foreach ($kategoriFinal as $namaKategori => $total) {
            if (!empty($namaKategori)) {
                $komplainKategori[] = [
                    'kategori' => $namaKategori,
                    'total' => $total
                ];
            }
        }

        // ==========================================
        // 4. DATA PIE CHART (DISTRIBUSI PAKET)
        // ==========================================
        $distribusiPaket = Pelanggan::with('paket')
            ->selectRaw('paket_id, COUNT(*) as total')
            ->groupBy('paket_id')
            ->get()
            ->map(function ($item) {
                return [
                    'nama_paket' => $item->paket ? $item->paket->nama_paket : 'Tanpa Paket',
                    'total' => $item->total
                ];
            });

        return view('owner.laporan.index', compact(
            'totalPemasukan',
            'totalPengeluaran',
            'labaBersih',
            'totalPiutang',
            'pengeluaranKategori',
            'filterType',
            'pemasukanBulanan',
            'pengeluaranBulanan',
            'chartYear',
            'komplainStats',
            'komplainKategori',
            'distribusiPaket'
        ));
    }
}