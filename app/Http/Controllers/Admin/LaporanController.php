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
        // 1. FILTER UNTUK KARTU UTAMA & SETUP DEFAULT
        // ==========================================
        $filterType = $request->filter_type ?? 'all'; // all, month, range

        // Bikin default bulan & tahun berdasarkan filter utama (agar chart di bawah otomatis sinkron)
        $defaultFilterMonth = ($filterType === 'month' && $request->month) ? $request->month : date('Y-m');
        $defaultFilterYear = ($filterType === 'month' && $request->month) ? date('Y', strtotime($request->month)) : date('Y');

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

        // Total Piutang (Absolute, tidak terpengaruh filter tanggal)
        $totalPiutang = Pelanggan::where('pelanggans.status', 'Active')
            ->where('pelanggans.status_pembayaran', 'Belum Lunas')
            ->join('pakets', 'pelanggans.paket_id', '=', 'pakets.id')
            ->sum('pakets.harga');

        // ==========================================
        // 2. DATA BAR CHART (TREN KEUANGAN 12 BULAN)
        // ==========================================
        $chartYear = $request->chart_year ?: $defaultFilterYear;

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
        $komplainFilter = $request->komplain_month ?: $defaultFilterMonth;
        $komplainMonth = date('m', strtotime($komplainFilter));
        $komplainYear = date('Y', strtotime($komplainFilter));

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
        // 4. DATA OMZET PER PAKET
        // ==========================================
        $omzetFilter = $request->omzet_month ?: $defaultFilterMonth;
        $omzetYear = Carbon::parse($omzetFilter)->year;
        $omzetMonth = Carbon::parse($omzetFilter)->month;

        $omzetPerPaket = Transaksi::join('pelanggans', 'transaksis.pelanggan_id', '=', 'pelanggans.id')
            ->leftJoin('pakets', 'pelanggans.paket_id', '=', 'pakets.id')
            ->whereYear('transaksis.tanggal', $omzetYear)
            ->whereMonth('transaksis.tanggal', $omzetMonth)
            ->select(DB::raw('COALESCE(pakets.nama_paket, "Tanpa Paket") as nama_paket'), DB::raw('SUM(transaksis.jumlah) as total_omzet'))
            ->groupBy('nama_paket')
            ->orderByDesc('total_omzet')
            ->get();

        $omzetPaketLabels = $omzetPerPaket->pluck('nama_paket')->toArray();
        $omzetPaketData = $omzetPerPaket->pluck('total_omzet')->toArray();

        // ==========================================
        // 5. DATA BREAKDOWN PENGELUARAN (DENGAN FILTER BULAN KHUSUS)
        // ==========================================
        $pengeluaranFilter = $request->pengeluaran_month ?: $defaultFilterMonth;
        $pengeluaranYear = Carbon::parse($pengeluaranFilter)->year;
        $pengeluaranMonth = Carbon::parse($pengeluaranFilter)->month;

        $pengeluaranKategori = Pengeluaran::whereYear('tanggal', $pengeluaranYear)
            ->whereMonth('tanggal', $pengeluaranMonth)
            ->selectRaw('kategori, SUM(jumlah) as total')
            ->groupBy('kategori')
            ->get();

        // Menyimpan total spesifik untuk bulan ini guna validasi render grafik di view
        $totalPengeluaranChart = $pengeluaranKategori->sum('total');

        return view('owner.laporan.index', compact(
            'totalPemasukan',
            'totalPengeluaran',
            'labaBersih',
            'totalPiutang',
            'filterType',
            'pemasukanBulanan',
            'pengeluaranBulanan',
            'chartYear',
            'komplainFilter',      // <-- VARIABEL BARU YANG DI-PASS
            'komplainStats',
            'komplainKategori',
            'omzetFilter',
            'omzetPaketLabels',
            'omzetPaketData',
            'pengeluaranFilter',
            'pengeluaranKategori',
            'totalPengeluaranChart'
        ));
    }
}