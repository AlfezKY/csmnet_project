<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Pelanggan;
use App\Models\Paket;
use App\Models\Komplain;
use App\Models\Pengeluaran;
use App\Models\Transaksi;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $today = Carbon::today();

        // ==========================================
        // 1. FILTER INDIVIDUAL
        // ==========================================

        // Filter Chart Pelanggan Baru
        $pelangganYear = $request->input('pelanggan_year', $today->year);

        // Filter Chart Omzet Per Paket (Pengganti Status Pembayaran)
        $omzetFilter = $request->input('omzet_month', $today->format('Y-m'));
        $omzetYear = Carbon::parse($omzetFilter)->year;
        $omzetMonth = Carbon::parse($omzetFilter)->month;

        // Filter Chart Transaksi Harian
        $trxFilter = $request->input('trx_month', $today->format('Y-m'));
        $trxYear = Carbon::parse($trxFilter)->year;
        $trxMonth = Carbon::parse($trxFilter)->month;

        // Filter Calendar
        $calendarDate = $request->input('calendar_date', $today->format('Y-m-d'));
        $selectedDate = Carbon::parse($calendarDate);
        $calendarMonth = $request->input('calendar_month', $selectedDate->month);
        $calendarYear = $request->input('calendar_year', $selectedDate->year);

        // ==========================================
        // 2. DATA KPI (6 KOTAK ATAS)
        // ==========================================
        $kpi = [
            'totalPelanggan'     => Pelanggan::count(),
            'totalPaket'         => Paket::count(),
            'komplainHariIni'    => Komplain::whereDate('tanggal', $today)->count(),
            'tagihanHariIni'     => Pelanggan::whereDate('jatuh_tempo', $today)->count(),
            'pengeluaranHariIni' => Pengeluaran::whereDate('tanggal', $today)->sum('jumlah'),
            'omzetHariIni'       => Transaksi::whereDate('tanggal', $today)->sum('jumlah'),
        ];

        // ==========================================
        // 3. DATA KALENDER & LIST JATUH TEMPO
        // ==========================================
        $jatuhTempoTerpilih = Pelanggan::with('paket')
            ->whereDate('jatuh_tempo', $selectedDate)
            ->take(5)
            ->get();

        // ==========================================
        // 4. CHART PELANGGAN BARU
        // ==========================================
        $pelangganBaru = [];
        for ($i = 1; $i <= 12; $i++) {
            $pelangganBaru[] = Pelanggan::whereYear('created_at', $pelangganYear)
                ->whereMonth('created_at', $i)
                ->count();
        }

        // ==========================================
        // 5. CHART OMZET PER PAKET (BARU)
        // ==========================================
        // Menghitung total uang masuk berdasarkan paket yang dipakai pelanggan
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
        // 6. CHART PIE PAKET ISP (Kuantitas Pelanggan)
        // ==========================================
        $paketAktif = Pelanggan::whereNotNull('paket_id')
            ->select('paket_id', DB::raw('count(*) as total'))
            ->groupBy('paket_id')
            ->with('paket')
            ->get();

        $pieLabels = $paketAktif->map(fn($item) => $item->paket->nama_paket ?? 'Tanpa Paket')->toArray();
        $pieData = $paketAktif->pluck('total')->toArray();

        // ==========================================
        // 7. CHART TRANSAKSI PER HARI
        // ==========================================
        $daysInTrxMonth = Carbon::createFromDate($trxYear, $trxMonth, 1)->daysInMonth;
        $trxPerHariLabel = [];
        $trxPerHariData = [];

        for ($i = 1; $i <= $daysInTrxMonth; $i++) {
            $trxPerHariLabel[] = $i;
            $trxPerHariData[] = Transaksi::whereYear('tanggal', $trxYear)
                ->whereMonth('tanggal', $trxMonth)
                ->whereDay('tanggal', $i)
                ->count();
        }

        // ==========================================
        // 8. LIST PELANGGAN MENUNGGAK (> 3 HARI)
        // ==========================================
        $tigaHariLalu = Carbon::today()->subDays(3);
        $pelangganOverdue = Pelanggan::with('paket')
            ->where('status', 'Active')
            ->where('status_pembayaran', 'Belum Lunas')
            ->whereDate('jatuh_tempo', '<=', $tigaHariLalu)
            ->orderBy('jatuh_tempo', 'asc')
            ->get();

            // ==========================================
        // 9. CHART KOMPLAIN PER KATEGORI (BAR CHART)
        // ==========================================
        $komplainFilter = $request->input('komplain_month', $today->format('Y-m'));
        $komplainYear = Carbon::parse($komplainFilter)->year;
        $komplainMonth = Carbon::parse($komplainFilter)->month;

        $komplainsRaw = Komplain::whereYear('tanggal', $komplainYear)
                             ->whereMonth('tanggal', $komplainMonth)
                             ->get();

        // Daftar kategori (sesuai dengan KomplainController)
        $kategoriList = ['Kabel Putus', 'Modem LOS Merah', 'Internet Lambat/RTO', 'Ganti Password WiFi', 'Pembayaran/Tagihan', 'Lain-lain', 'Belum Diatur'];

        // Inisialisasi total 0 untuk setiap kategori
        $komplainSeriesData = array_fill_keys($kategoriList, 0);

        // Hitung total akumulasi per kategori dalam bulan tersebut
        foreach ($komplainsRaw as $k) {
            $kat = $k->kategori ?: 'Belum Diatur';
            if (isset($komplainSeriesData[$kat])) {
                $komplainSeriesData[$kat]++;
            } else {
                $komplainSeriesData['Lain-lain']++;
            }
        }

        // Format untuk ApexCharts Bar Chart (Hanya ambil yang ada komplainnya)
        $komplainChartLabels = [];
        $komplainChartData = [];
        foreach ($komplainSeriesData as $name => $total) {
            if ($total > 0) {
                $komplainChartLabels[] = $name;
                $komplainChartData[] = $total;
            }
        }

        return view('admin.dashboard', compact(
            'kpi',
            'jatuhTempoTerpilih',
            'selectedDate',
            'calendarMonth',
            'calendarYear',
            'pelangganYear',
            'pelangganBaru',
            'omzetFilter',
            'omzetMonth',
            'omzetYear',
            'omzetPaketLabels',
            'omzetPaketData',
            'pieLabels',
            'pieData',
            'trxFilter',
            'trxMonth',
            'trxYear',
            'trxPerHariLabel',
            'trxPerHariData',
            'pelangganOverdue',
            'komplainFilter',
            'komplainChartLabels',
            'komplainChartData'
        ));
    }
}
