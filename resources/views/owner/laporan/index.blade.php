@extends('layouts.app')
@section('title', 'Laporan & Dashboard')

@section('content')
{{-- HEADER & FILTER KEUANGAN (SEAMLESS) --}}
<div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8" x-data="{ filter: '{{ request('filter_type', 'all') }}' }">
    <div>
        <h3 class="text-2xl font-bold text-gray-900 tracking-tight">Dashboard Laporan</h3>
        <p class="text-sm text-gray-500 font-medium mt-1">Ringkasan keuangan, tiket komplain, dan distribusi pelanggan.</p>
    </div>

    {{-- Filter ditarik ke kanan atas, dibikin lebih compact --}}
    <form action="{{ route('laporan.index') }}" method="GET" class="flex flex-col sm:flex-row items-center gap-2 bg-white p-1.5 rounded-2xl border border-gray-200 shadow-sm relative z-20">
        <select name="filter_type" x-model="filter" class="text-xs p-2 bg-transparent outline-none font-bold text-gray-700 cursor-pointer min-w-[130px]">
            <option value="all">Semua Waktu</option>
            <option value="month">Per Bulan</option>
            <option value="range">Rentang Tanggal</option>
        </select>

        <div class="h-6 w-px bg-gray-200 hidden sm:block"></div>

        <div x-show="filter === 'month'" x-cloak>
            <input type="month" name="month" value="{{ request('month', date('Y-m')) }}" class="text-xs p-2 bg-transparent outline-none font-bold text-gray-700 cursor-pointer">
        </div>

        <div x-show="filter === 'range'" x-cloak class="flex items-center gap-2">
            <input type="date" name="start_date" value="{{ request('start_date') }}" class="text-xs p-2 bg-transparent outline-none font-bold text-gray-700 cursor-pointer">
            <span class="text-xs text-gray-400 font-medium">-</span>
            <input type="date" name="end_date" value="{{ request('end_date') }}" class="text-xs p-2 bg-transparent outline-none font-bold text-gray-700 cursor-pointer">
        </div>

        <input type="hidden" name="chart_year" value="{{ $chartYear }}">

        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white p-2 rounded-xl transition-all shadow-sm ml-1" title="Terapkan Filter">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
        </button>
    </form>
</div>

{{-- 3 KARTU UTAMA (Berwarna, Clean & pakai Icon) --}}
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8 relative z-10">
    
    {{-- KARTU PEMASUKAN --}}
    <div class="relative overflow-hidden bg-gradient-to-br from-emerald-50 to-green-100/60 p-6 rounded-3xl border border-emerald-100 flex items-center gap-5 group transition-all duration-300 hover:shadow-md">
        <div class="absolute -right-4 -bottom-4 w-24 h-24 bg-gradient-to-br from-emerald-200/50 to-green-300/30 rounded-full blur-2xl group-hover:scale-150 transition-transform duration-500"></div>
        
        <div class="w-14 h-14 bg-white/80 backdrop-blur-sm rounded-2xl shadow-sm flex flex-shrink-0 items-center justify-center text-emerald-600 relative z-10 border border-white/50">
            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18L9 11.25l4.306 4.307a11.95 11.95 0 015.814-5.519l2.74-1.22m0 0l-5.94-2.28m5.94 2.28l-2.28 5.941" />
            </svg>
        </div>
        
        <div class="relative z-10 w-full">
            <p class="text-[11px] text-emerald-800/70 font-bold uppercase tracking-widest mb-1">Total Pemasukan</p>
            <h4 class="text-2xl lg:text-3xl font-black text-emerald-950 truncate">Rp {{ number_format($totalPemasukan, 0, ',', '.') }}</h4>
        </div>
    </div>

    {{-- KARTU PENGELUARAN --}}
    <div class="relative overflow-hidden bg-gradient-to-br from-rose-50 to-red-100/60 p-6 rounded-3xl border border-rose-100 flex items-center gap-5 group transition-all duration-300 hover:shadow-md">
        <div class="absolute -right-4 -bottom-4 w-24 h-24 bg-gradient-to-br from-rose-200/50 to-red-300/30 rounded-full blur-2xl group-hover:scale-150 transition-transform duration-500"></div>
        
        <div class="w-14 h-14 bg-white/80 backdrop-blur-sm rounded-2xl shadow-sm flex flex-shrink-0 items-center justify-center text-rose-600 relative z-10 border border-white/50">
            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6L9 12.75l4.286-4.286a11.948 11.948 0 014.306 6.43l.776 2.898m0 0l3.182-5.511m-3.182 5.51l-5.511-3.181" />
            </svg>
        </div>
        
        <div class="relative z-10 w-full">
            <p class="text-[11px] text-rose-800/70 font-bold uppercase tracking-widest mb-1">Total Pengeluaran</p>
            <h4 class="text-2xl lg:text-3xl font-black text-rose-950 truncate">Rp {{ number_format($totalPengeluaran, 0, ',', '.') }}</h4>
        </div>
    </div>

    {{-- KARTU LABA BERSIH (Otomatis ganti warna kalau minus) --}}
    @php 
        $isProfit = $labaBersih >= 0; 
        $bgClass = $isProfit ? 'from-blue-50 to-indigo-100/60 border-blue-100' : 'from-orange-50 to-orange-100/60 border-orange-100';
        $blobClass = $isProfit ? 'from-blue-200/50 to-indigo-300/30' : 'from-orange-200/50 to-orange-300/30';
        $iconColor = $isProfit ? 'text-blue-600' : 'text-orange-600';
        $textColor = $isProfit ? 'text-blue-950' : 'text-orange-950';
        $labelColor = $isProfit ? 'text-blue-800/70' : 'text-orange-800/70';
    @endphp
    <div class="relative overflow-hidden bg-gradient-to-br {{ $bgClass }} p-6 rounded-3xl border flex items-center gap-5 group transition-all duration-300 hover:shadow-md">
        <div class="absolute -right-4 -bottom-4 w-24 h-24 bg-gradient-to-br {{ $blobClass }} rounded-full blur-2xl group-hover:scale-150 transition-transform duration-500"></div>
        
        <div class="w-14 h-14 bg-white/80 backdrop-blur-sm rounded-2xl shadow-sm flex flex-shrink-0 items-center justify-center {{ $iconColor }} relative z-10 border border-white/50">
            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M21 12a2.25 2.25 0 00-2.25-2.25H15a3 3 0 11-6 0H5.25A2.25 2.25 0 003 12m18 0v6a2.25 2.25 0 01-2.25 2.25H5.25A2.25 2.25 0 013 18v-6m18 0V9M3 12V9m18 0a2.25 2.25 0 00-2.25-2.25H5.25A2.25 2.25 0 003 9m18 0V6a2.25 2.25 0 00-2.25-2.25H5.25A2.25 2.25 0 003 6v3" />
            </svg>
        </div>
        
        <div class="relative z-10 w-full">
            <p class="text-[11px] {{ $labelColor }} font-bold uppercase tracking-widest mb-1">Laba Bersih</p>
            <h4 class="text-2xl lg:text-3xl font-black {{ $textColor }} truncate">Rp {{ number_format($labaBersih, 0, ',', '.') }}</h4>
        </div>
    </div>
</div>

{{-- BAGIAN CHARTS & KOMPLAIN --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8 relative z-0">
    
    {{-- KIRI: TREND KEUANGAN --}}
    <div class="lg:col-span-2 relative overflow-hidden bg-gradient-to-br from-white to-slate-50/80 p-6 rounded-3xl border border-slate-100 shadow-sm transition-all duration-300 hover:shadow-md group">
        <div class="absolute -right-20 -top-20 w-64 h-64 bg-gradient-to-br from-blue-100/40 to-indigo-100/30 rounded-full blur-3xl group-hover:scale-110 transition-transform duration-700 pointer-events-none"></div>
        <div class="absolute -left-20 -bottom-20 w-64 h-64 bg-gradient-to-br from-emerald-100/30 to-teal-100/20 rounded-full blur-3xl group-hover:scale-110 transition-transform duration-700 pointer-events-none"></div>
        
        <div class="relative z-10">
            <div class="flex justify-between items-center mb-6">
                <h4 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                    <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z" /></svg>
                    Tren Keuangan
                </h4>
                <form action="{{ route('laporan.index') }}" method="GET">
                    <input type="hidden" name="filter_type" value="{{ $filterType }}">
                    <input type="hidden" name="month" value="{{ request('month') }}">
                    <input type="hidden" name="start_date" value="{{ request('start_date') }}">
                    <input type="hidden" name="end_date" value="{{ request('end_date') }}">
                    
                    <select name="chart_year" onchange="this.form.submit()" class="text-sm p-2 bg-white/60 backdrop-blur-md border border-gray-200/50 rounded-xl font-bold cursor-pointer outline-none focus:ring-2 focus:ring-blue-500 text-gray-700 shadow-sm transition-all hover:bg-white">
                        @for($y = date('Y'); $y >= 2024; $y--)
                            <option value="{{ $y }}" {{ $chartYear == $y ? 'selected' : '' }}>Tahun {{ $y }}</option>
                        @endfor
                    </select>
                </form>
            </div>
            <div id="financialChart" class="w-full h-80"></div>
        </div>
    </div>

    {{-- KANAN: RINGKASAN KOMPLAIN (COMPACT VERSION) --}}
    <div class="lg:col-span-1 relative overflow-hidden bg-gradient-to-br from-white to-slate-50/80 p-6 rounded-3xl border border-slate-100 shadow-sm flex flex-col transition-all duration-300 hover:shadow-md group">
        <div class="absolute -right-10 -bottom-10 w-48 h-48 bg-gradient-to-br from-orange-100/30 to-rose-100/20 rounded-full blur-3xl group-hover:scale-110 transition-transform duration-700 pointer-events-none"></div>
        
        <div class="relative z-10 flex flex-col h-full">
            {{-- Header & Filter --}}
            <div class="flex justify-between items-center mb-4 gap-2">
                <h4 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                    <svg class="w-5 h-5 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" /></svg>
                    Tiket Komplain
                </h4>
                <form action="{{ route('laporan.index') }}" method="GET">
                    <input type="hidden" name="filter_type" value="{{ request('filter_type', 'all') }}">
                    <input type="hidden" name="month" value="{{ request('month') }}">
                    <input type="hidden" name="start_date" value="{{ request('start_date') }}">
                    <input type="hidden" name="end_date" value="{{ request('end_date') }}">
                    <input type="hidden" name="chart_year" value="{{ request('chart_year', date('Y')) }}">
                    
                    <input type="month" name="komplain_month" value="{{ request('komplain_month', date('Y-m')) }}" onchange="this.form.submit()" class="text-[11px] px-2 py-1.5 bg-white/60 backdrop-blur-md border border-gray-200/50 rounded-lg font-bold cursor-pointer outline-none focus:ring-2 focus:ring-orange-500 text-gray-600 shadow-sm transition-all hover:bg-white">
                </form>
            </div>
            
            {{-- 4 Status (Total gabung kesini biar compact) --}}
            <div class="grid grid-cols-4 gap-2 mb-2 relative z-10">
                <div class="bg-slate-50/80 p-2 rounded-xl text-center border border-slate-200/50 flex flex-col justify-center hover:shadow-sm hover:scale-105 transition-all cursor-default">
                    <p class="text-[8px] text-slate-500 font-bold uppercase mb-0.5">Total</p>
                    <h2 class="text-lg font-black text-slate-800">{{ $komplainStats['total'] }}</h2>
                </div>
                <div class="bg-emerald-50/80 p-2 rounded-xl text-center border border-emerald-100/50 flex flex-col justify-center hover:shadow-sm hover:scale-105 transition-all cursor-default">
                    <p class="text-[8px] text-emerald-600 font-black uppercase mb-0.5">Selesai</p>
                    <p class="text-lg font-black text-emerald-700">{{ $komplainStats['done'] }}</p>
                </div>
                <div class="bg-blue-50/80 p-2 rounded-xl text-center border border-blue-100/50 flex flex-col justify-center hover:shadow-sm hover:scale-105 transition-all cursor-default">
                    <p class="text-[8px] text-blue-600 font-black uppercase mb-0.5">Proses</p>
                    <p class="text-lg font-black text-blue-700">{{ $komplainStats['in_progress'] }}</p>
                </div>
                <div class="bg-rose-50/80 p-2 rounded-xl text-center border border-rose-100/50 flex flex-col justify-center hover:shadow-sm hover:scale-105 transition-all cursor-default">
                    <p class="text-[8px] text-rose-600 font-black uppercase mb-0.5">Pending</p>
                    <p class="text-lg font-black text-rose-700">{{ $komplainStats['not_yet'] }}</p>
                </div>
            </div>

            {{-- Horizontal Bar Chart --}}
            <div id="complaintChart" class="w-full flex-grow relative z-10 min-h-[160px] -ml-2 mt-2"></div>
        </div>
    </div>
</div>

{{-- BAWAH: DISTRIBUSI PAKET --}}
<div class="relative overflow-hidden bg-gradient-to-br from-white to-slate-50/80 p-6 rounded-3xl border border-slate-100 shadow-sm w-full md:w-1/2 transition-all duration-300 hover:shadow-md group">
    <div class="absolute -left-10 -top-10 w-48 h-48 bg-gradient-to-br from-fuchsia-100/40 to-pink-100/20 rounded-full blur-3xl group-hover:scale-110 transition-transform duration-700 pointer-events-none"></div>
    <div class="absolute -right-10 -bottom-10 w-48 h-48 bg-gradient-to-br from-amber-100/30 to-yellow-100/20 rounded-full blur-3xl group-hover:scale-110 transition-transform duration-700 pointer-events-none"></div>
    
    <div class="relative z-10">
        <h4 class="text-lg font-bold text-gray-800 mb-6 flex items-center gap-2">
            <svg class="w-5 h-5 text-fuchsia-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z" /></svg>
            Distribusi Pelanggan Berdasarkan Paket
        </h4>
        <div id="packageChart" class="w-full h-72 flex justify-center"></div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    
    const fontFamily = 'Instrument Sans, sans-serif';
    const formatRupiah = (value) => "Rp " + value.toLocaleString('id-ID');

    // 1. CHART TREN KEUANGAN
    const financialOptions = {
        series: [{
            name: 'Pemasukan',
            data: @json($pemasukanBulanan)
        }, {
            name: 'Pengeluaran',
            data: @json($pengeluaranBulanan)
        }],
        chart: { 
            type: 'bar', 
            height: 350, 
            toolbar: { show: false }, 
            fontFamily: fontFamily,
            animations: {
                enabled: true,
                easing: 'easeinout',
                speed: 800,
                animateGradually: { enabled: true, delay: 150 },
                dynamicAnimation: { enabled: true, speed: 350 }
            }
        },
        plotOptions: {
            bar: { 
                horizontal: false, 
                columnWidth: '45%',
                borderRadius: 4,
                borderRadiusApplication: 'end'
            }
        },
        dataLabels: { enabled: false },
        stroke: { show: true, width: 3, colors: ['transparent'] },
        colors: ['#22c55e', '#ef4444'],
        xaxis: {
            categories: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Ags', 'Sep', 'Okt', 'Nov', 'Des'],
            labels: { style: { colors: '#6b7280', fontWeight: 600, fontFamily: fontFamily } },
            axisBorder: { show: false },
            axisTicks: { show: false }
        },
        yaxis: {
            labels: {
                formatter: formatRupiah,
                style: { colors: '#6b7280', fontWeight: 600, fontFamily: fontFamily }
            }
        },
        grid: {
            borderColor: '#f3f4f6',
            strokeDashArray: 4,
            yaxis: { lines: { show: true } }
        },
        fill: { 
            opacity: 1,
            type: 'gradient',
            gradient: {
                shade: 'light',
                type: 'vertical',
                shadeIntensity: 0.25,
                inverseColors: false,
                opacityFrom: 0.95,
                opacityTo: 0.85,
                stops: [50, 100]
            }
        },
        tooltip: {
            theme: 'light',
            y: { formatter: formatRupiah },
            style: { fontSize: '13px', fontFamily: fontFamily }
        },
        legend: {
            position: 'top',
            horizontalAlign: 'right',
            fontFamily: fontFamily,
            fontWeight: 600,
            markers: { radius: 12 }
        }
    };
    new ApexCharts(document.querySelector("#financialChart"), financialOptions).render();

    // 2. CHART KOMPLAIN KATEGORI
    const komplainData = @json($komplainKategori);
    const complaintOptions = {
        series: [{
            name: 'Jumlah Tiket',
            data: komplainData.map(item => item.total)
        }],
        chart: { 
            type: 'bar', 
            height: 250, 
            toolbar: { show: false }, 
            fontFamily: fontFamily
        },
        plotOptions: {
            bar: { 
                horizontal: true, 
                borderRadius: 4, 
                distributed: true,
                barHeight: '60%' 
            }
        },
        colors: ['#3b82f6', '#f59e0b', '#ef4444', '#8b5cf6', '#10b981'],
        dataLabels: { 
            enabled: true, 
            textAnchor: 'start',
            style: { colors: ['#fff'], fontSize: '12px', fontFamily: fontFamily, fontWeight: 'bold' },
            offsetX: 0,
            dropShadow: { enabled: false }
        },
        stroke: { width: 0 },
        xaxis: { 
            categories: komplainData.map(item => item.kategori),
            labels: { show: false },
            axisBorder: { show: false },
            axisTicks: { show: false }
        },
        yaxis: {
            labels: { style: { colors: '#4b5563', fontWeight: 600, fontFamily: fontFamily } }
        },
        grid: { show: false },
        tooltip: {
            theme: 'light',
            style: { fontSize: '12px', fontFamily: fontFamily },
            y: { formatter: function (val) { return val + " Tiket" } }
        },
        legend: { show: false }
    };
    new ApexCharts(document.querySelector("#complaintChart"), complaintOptions).render();

    // 3. CHART DISTRIBUSI PAKET
    const paketData = @json($distribusiPaket);
    const packageOptions = {
        series: paketData.map(item => item.total),
        chart: { 
            type: 'donut', 
            height: 320, 
            fontFamily: fontFamily,
            dropShadow: {
                enabled: true,
                color: '#111827',
                top: 2,
                left: 0,
                blur: 4,
                opacity: 0.05
            }
        },
        labels: paketData.map(item => item.nama_paket),
        colors: ['#3b82f6', '#10b981', '#f59e0b', '#6366f1', '#ec4899', '#14b8a6'],
        dataLabels: { enabled: false },
        stroke: { show: true, colors: '#ffffff', width: 2 },
        plotOptions: {
            pie: { 
                donut: { 
                    size: '70%', 
                    labels: { 
                        show: true, 
                        name: { fontSize: '12px', fontFamily: fontFamily, color: '#6b7280' },
                        value: { fontSize: '24px', fontFamily: fontFamily, fontWeight: 'bold', color: '#111827' },
                        total: { 
                            show: true, 
                            showAlways: true,
                            label: 'Total', 
                            fontSize: '14px',
                            fontFamily: fontFamily,
                            fontWeight: 600,
                            color: '#6b7280'
                        } 
                    } 
                } 
            }
        },
        tooltip: {
            theme: 'light',
            style: { fontSize: '13px', fontFamily: fontFamily },
            y: { formatter: function(val) { return val + " Pelanggan" } }
        },
        legend: { 
            position: 'right', 
            fontFamily: fontFamily,
            fontWeight: 600,
            markers: { radius: 12 }
        }
    };
    new ApexCharts(document.querySelector("#packageChart"), packageOptions).render();

});
</script>
@endpush