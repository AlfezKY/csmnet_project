@extends('layouts.app')

@section('title', 'Dashboard - CSMNET')

@section('content')

{{-- Inject CSS Animasi & Scrollbar Premium --}}
<style>
    @keyframes fadeUp {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .animate-fade-up { animation: fadeUp 0.6s ease-out forwards; opacity: 0; }
    .delay-100 { animation-delay: 100ms; }
    .delay-200 { animation-delay: 200ms; }
    .delay-300 { animation-delay: 300ms; }
    .delay-400 { animation-delay: 400ms; }
    
    .custom-scrollbar::-webkit-scrollbar { width: 4px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
    
    .glass-card {
        background: rgba(255, 255, 255, 0.85);
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);
        border: 1px solid rgba(255, 255, 255, 0.5);
    }
</style>

<div class="animate-fade-up">
    <div class="mb-8 relative z-10 flex flex-col md:flex-row md:items-end justify-between gap-4">
        <div>
            <h2 class="text-3xl font-black text-gray-900 tracking-tight">Dashboard Overview</h2>
            <p class="text-sm text-gray-500 font-medium mt-1">Pantau performa dan metrik operasional CSMNET secara real-time.</p>
        </div>
        <div class="text-xs font-bold text-gray-400 bg-white/60 px-4 py-2 rounded-full border border-gray-200 shadow-sm">
            {{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}
        </div>
    </div>
</div>

{{-- ROW 1: 6 KPI CARDS (PREMIUM FLOATING STYLE) --}}
<div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-6 gap-5 mb-8 relative z-10">
    
    {{-- 1. Pelanggan (Ke halaman semua pelanggan) --}}
    <a href="{{ route('pelanggan.index') }}" class="block relative group animate-fade-up delay-100 outline-none">
        <div class="absolute inset-0 bg-gradient-to-r from-blue-400 to-indigo-400 rounded-3xl blur-xl opacity-20 group-hover:opacity-40 transition-opacity duration-500"></div>
        <div class="glass-card relative p-6 rounded-3xl shadow-sm flex flex-col justify-between h-full hover:-translate-y-1.5 transition-transform duration-500">
            <div class="flex justify-between items-start mb-4">
                <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-blue-500 to-indigo-600 text-white flex items-center justify-center shadow-lg shadow-blue-500/30">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                </div>
            </div>
            <div>
                <h3 class="text-3xl font-black text-gray-900 tracking-tight">{{ $kpi['totalPelanggan'] }}</h3>
                <p class="text-[11px] text-gray-500 font-bold uppercase tracking-wider mt-1">Total Pelanggan</p>
            </div>
        </div>
    </a>

    {{-- 2. Paket ISP (Ke halaman semua paket) --}}
    <a href="{{ route('paket.index') }}" class="block relative group animate-fade-up delay-100 outline-none">
        <div class="absolute inset-0 bg-gradient-to-r from-fuchsia-400 to-pink-400 rounded-3xl blur-xl opacity-20 group-hover:opacity-40 transition-opacity duration-500"></div>
        <div class="glass-card relative p-6 rounded-3xl shadow-sm flex flex-col justify-between h-full hover:-translate-y-1.5 transition-transform duration-500">
            <div class="flex justify-between items-start mb-4">
                <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-fuchsia-500 to-pink-600 text-white flex items-center justify-center shadow-lg shadow-fuchsia-500/30">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2m-2-4h.01M17 16h.01"></path></svg>
                </div>
            </div>
            <div>
                <h3 class="text-3xl font-black text-gray-900 tracking-tight">{{ $kpi['totalPaket'] }}</h3>
                <p class="text-[11px] text-gray-500 font-bold uppercase tracking-wider mt-1">Total Paket ISP</p>
            </div>
        </div>
    </a>

    {{-- 3. Komplain (Difilter Hari Ini) --}}
    <a href="{{ route('komplain.index', ['start_date' => date('Y-m-d'), 'end_date' => date('Y-m-d')]) }}" class="block relative group animate-fade-up delay-200 outline-none">
        <div class="absolute inset-0 bg-gradient-to-r from-rose-400 to-red-400 rounded-3xl blur-xl opacity-20 group-hover:opacity-40 transition-opacity duration-500"></div>
        <div class="glass-card relative p-6 rounded-3xl shadow-sm flex flex-col justify-between h-full hover:-translate-y-1.5 transition-transform duration-500">
            <div class="flex justify-between items-start mb-4">
                <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-rose-500 to-red-600 text-white flex items-center justify-center shadow-lg shadow-rose-500/30">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                </div>
                <span class="bg-rose-50 text-rose-600 text-[10px] font-bold px-2 py-1 rounded-lg">Hari Ini</span>
            </div>
            <div>
                <h3 class="text-3xl font-black text-gray-900 tracking-tight">{{ $kpi['komplainHariIni'] }}</h3>
                <p class="text-[11px] text-gray-500 font-bold uppercase tracking-wider mt-1">Tiket Komplain</p>
            </div>
        </div>
    </a>

    {{-- 4. Tagihan (Difilter Jatuh Tempo Hari Ini) --}}
    <a href="{{ route('tagihan.index', ['start_date' => date('Y-m-d'), 'end_date' => date('Y-m-d')]) }}" class="block relative group animate-fade-up delay-200 outline-none">
        <div class="absolute inset-0 bg-gradient-to-r from-amber-400 to-orange-400 rounded-3xl blur-xl opacity-20 group-hover:opacity-40 transition-opacity duration-500"></div>
        <div class="glass-card relative p-6 rounded-3xl shadow-sm flex flex-col justify-between h-full hover:-translate-y-1.5 transition-transform duration-500">
            <div class="flex justify-between items-start mb-4">
                <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-amber-400 to-orange-500 text-white flex items-center justify-center shadow-lg shadow-amber-500/30">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                </div>
                <span class="bg-orange-50 text-orange-600 text-[10px] font-bold px-2 py-1 rounded-lg">Hari Ini</span>
            </div>
            <div>
                <h3 class="text-3xl font-black text-gray-900 tracking-tight">{{ $kpi['tagihanHariIni'] }}</h3>
                <p class="text-[11px] text-gray-500 font-bold uppercase tracking-wider mt-1">Jatuh Tempo</p>
            </div>
        </div>
    </a>

    {{-- 5. Pengeluaran (Difilter Hari Ini) --}}
    <a href="{{ route('pengeluaran.index', ['start_date' => date('Y-m-d'), 'end_date' => date('Y-m-d')]) }}" class="block relative group animate-fade-up delay-300 outline-none">
        <div class="absolute inset-0 bg-gradient-to-r from-slate-400 to-gray-400 rounded-3xl blur-xl opacity-20 group-hover:opacity-40 transition-opacity duration-500"></div>
        <div class="glass-card relative p-6 rounded-3xl shadow-sm flex flex-col justify-between h-full hover:-translate-y-1.5 transition-transform duration-500">
            <div class="flex justify-between items-start mb-4">
                <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-slate-600 to-gray-800 text-white flex items-center justify-center shadow-lg shadow-slate-500/30">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.25 6L9 12.75l4.286-4.286a11.948 11.948 0 014.306 6.43l.776 2.898m0 0l3.182-5.511m-3.182 5.51l-5.511-3.181"></path></svg>
                </div>
                {{-- Tag Hari Ini Warna Slate --}}
                <span class="bg-slate-100 text-slate-600 text-[10px] font-bold px-2 py-1 rounded-lg">Hari Ini</span>
            </div>
            <div>
                <h3 class="text-lg font-black text-gray-900 tracking-tight">Rp {{ number_format($kpi['pengeluaranHariIni'], 0, ',', '.') }}</h3>
                <p class="text-[11px] text-gray-500 font-bold uppercase tracking-wider mt-1">Pengeluaran</p>
            </div>
        </div>
    </a>

    {{-- 6. Omzet / Transaksi Masuk (Difilter Hari Ini) --}}
    <a href="{{ route('transaksi.index', ['start_date' => date('Y-m-d'), 'end_date' => date('Y-m-d')]) }}" class="block relative group animate-fade-up delay-300 outline-none">
        <div class="absolute inset-0 bg-gradient-to-r from-emerald-400 to-teal-400 rounded-3xl blur-xl opacity-20 group-hover:opacity-40 transition-opacity duration-500"></div>
        <div class="glass-card relative p-6 rounded-3xl shadow-sm flex flex-col justify-between h-full hover:-translate-y-1.5 transition-transform duration-500">
            <div class="flex justify-between items-start mb-4">
                <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-emerald-400 to-teal-500 text-white flex items-center justify-center shadow-lg shadow-emerald-500/30">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.25 18L9 11.25l4.306 4.307a11.95 11.95 0 015.814-5.519l2.74-1.22m0 0l-5.94-2.28m5.94 2.28l-2.28 5.941"></path></svg>
                </div>
                {{-- Tag Hari Ini Warna Emerald --}}
                <span class="bg-emerald-50 text-emerald-600 text-[10px] font-bold px-2 py-1 rounded-lg">Hari Ini</span>
            </div>
            <div>
                <h3 class="text-lg font-black text-emerald-600 tracking-tight">Rp {{ number_format($kpi['omzetHariIni'], 0, ',', '.') }}</h3>
                <p class="text-[11px] text-gray-500 font-bold uppercase tracking-wider mt-1">Omzet</p>
            </div>
        </div>
    </a>
</div>

{{-- ROW 2: CHART PELANGGAN BARU & CALENDAR --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6 relative z-0 animate-fade-up delay-300">
    
    {{-- CHART KIRI (Pertumbuhan Pelanggan) --}}
    <div class="lg:col-span-2 relative group">
        <div class="absolute inset-0 bg-gradient-to-br from-blue-100/40 to-indigo-100/20 rounded-3xl blur-2xl group-hover:opacity-70 transition-opacity duration-700 pointer-events-none"></div>
        <div class="glass-card relative p-7 rounded-3xl shadow-sm flex flex-col h-full transition-all duration-300 hover:shadow-md">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
                <div>
                    <h4 class="text-xl font-bold text-gray-900 flex items-center gap-2">
                        <span class="w-3 h-8 rounded-full bg-blue-500"></span>
                        Pertumbuhan Pelanggan Baru
                    </h4>
                </div>
                
                <form method="GET">
                    @foreach(request()->except('pelanggan_year') as $key => $value) <input type="hidden" name="{{ $key }}" value="{{ $value }}"> @endforeach
                    <select name="pelanggan_year" onchange="this.form.submit()" class="text-sm px-4 py-2 bg-white border border-gray-200 rounded-xl font-bold cursor-pointer outline-none focus:ring-2 focus:ring-blue-500 text-gray-600 shadow-sm transition-all hover:bg-gray-50 appearance-none pr-8 relative">
                        @for($y = date('Y'); $y >= date('Y') - 3; $y--)
                            <option value="{{ $y }}" {{ $pelangganYear == $y ? 'selected' : '' }}>Tahun {{ $y }}</option>
                        @endfor
                    </select>
                </form>
            </div>
            <div id="chartPelangganBaru" class="w-full flex-1 min-h-[250px]"></div>
        </div>
    </div>

    {{-- KALENDER & LIST KANAN --}}
    <div class="lg:col-span-1 relative group">
        <div class="glass-card relative p-6 rounded-3xl shadow-sm flex flex-col h-full transition-all duration-300 hover:shadow-md">
            <h4 class="text-lg font-bold text-gray-900 mb-5 flex items-center gap-2">
                <span class="w-2.5 h-6 rounded-full bg-amber-500"></span>
                Jadwal Jatuh Tempo
            </h4>
            
            {{-- Kalender Widget --}}
            <div class="bg-gray-50/50 rounded-2xl p-5 border border-gray-100 shadow-inner mb-5">
                <div class="flex justify-between items-center mb-4">
                    <a href="{{ request()->fullUrlWithQuery(['calendar_month' => $calendarMonth == 1 ? 12 : $calendarMonth - 1, 'calendar_year' => $calendarMonth == 1 ? $calendarYear - 1 : $calendarYear]) }}" class="w-8 h-8 flex items-center justify-center bg-white rounded-lg text-gray-500 hover:text-indigo-600 hover:shadow shadow-sm transition-all border border-gray-200">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M15 19l-7-7 7-7"></path></svg>
                    </a>
                    <span class="font-black text-gray-800 text-sm tracking-widest uppercase">{{ \Carbon\Carbon::createFromDate($calendarYear, $calendarMonth, 1)->translatedFormat('F Y') }}</span>
                    <a href="{{ request()->fullUrlWithQuery(['calendar_month' => $calendarMonth == 12 ? 1 : $calendarMonth + 1, 'calendar_year' => $calendarMonth == 12 ? $calendarYear + 1 : $calendarYear]) }}" class="w-8 h-8 flex items-center justify-center bg-white rounded-lg text-gray-500 hover:text-indigo-600 hover:shadow shadow-sm transition-all border border-gray-200">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M9 5l7 7-7 7"></path></svg>
                    </a>
                </div>
                
                <div class="grid grid-cols-7 gap-1 text-[10px] font-black text-gray-400 mb-2 text-center uppercase tracking-wider">
                    <span>Mg</span><span>Sn</span><span>Sl</span><span>Rb</span><span>Km</span><span>Jm</span><span>Sb</span>
                </div>
                <div class="grid grid-cols-7 gap-1 text-sm font-bold text-center">
                    @php
                        $firstDay = \Carbon\Carbon::createFromDate($calendarYear, $calendarMonth, 1);
                        $startOffset = $firstDay->dayOfWeek;
                        $daysInMonth = $firstDay->daysInMonth;
                    @endphp
                    @for($i = 0; $i < $startOffset; $i++)
                        <div class="py-2"></div>
                    @endfor
                    @for($day = 1; $day <= $daysInMonth; $day++)
                        @php 
                            $loopDate = \Carbon\Carbon::createFromDate($calendarYear, $calendarMonth, $day)->format('Y-m-d');
                            $isSelected = ($loopDate == $selectedDate->format('Y-m-d'));
                            $isToday = ($loopDate == date('Y-m-d'));
                        @endphp
                        
                        <a href="{{ request()->fullUrlWithQuery(['calendar_date' => $loopDate, 'calendar_month' => $calendarMonth, 'calendar_year' => $calendarYear]) }}" 
                           class="block py-2 rounded-xl transition-all duration-300 {{ $isSelected ? 'bg-gradient-to-br from-indigo-500 to-indigo-600 text-white shadow-lg shadow-indigo-300 scale-105 font-black' : ($isToday ? 'bg-indigo-50 text-indigo-700 border border-indigo-200' : 'text-gray-600 hover:bg-white hover:shadow-sm hover:scale-110') }}">
                            {{ $day }}
                        </a>
                    @endfor
                </div>
            </div>

            <div class="flex items-center justify-between mb-3">
                <h5 class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Tagihan Teratas</h5>
                <span class="text-[10px] font-black text-indigo-600 bg-indigo-50 px-2 py-1 rounded-md">{{ $selectedDate->translatedFormat('d M') }}</span>
            </div>
            
            <div class="flex-1 space-y-2 overflow-y-auto custom-scrollbar pr-1 max-h-[160px]">
                @forelse($jatuhTempoTerpilih as $plg)
                <div class="flex justify-between items-center bg-white p-3 rounded-2xl border border-gray-100 shadow-sm hover:shadow-md hover:border-amber-200 transition-all cursor-default group/list">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-amber-100 to-orange-100 text-orange-600 flex items-center justify-center text-xs font-black shadow-inner border border-white">
                            {{ strtoupper(substr($plg->nama_pelanggan, 0, 2)) }}
                        </div>
                        <div>
                            <p class="text-xs font-bold text-gray-900 group-hover/list:text-indigo-600 transition-colors">{{ $plg->nama_pelanggan }}</p>
                            <p class="text-[10px] text-gray-500 font-medium mt-0.5">{{ $plg->paket->nama_paket ?? 'Tanpa Paket' }}</p>
                        </div>
                    </div>
                </div>
                @empty
                <div class="text-center p-6 text-xs font-bold text-gray-400 border-2 border-dashed border-gray-200 rounded-2xl bg-gray-50/50 flex flex-col items-center justify-center gap-2">
                    <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    <span>Aman! Tidak ada tagihan.</span>
                </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

{{-- ROW 3: OMZET PER PAKET & PIE CHART --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6 relative z-0 animate-fade-up delay-400">
    
    {{-- CHART KIRI (Pendapatan) --}}
    <div class="lg:col-span-2 relative group">
        <div class="glass-card relative p-7 rounded-3xl shadow-sm flex flex-col h-full transition-all duration-300 hover:shadow-md">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
                <h4 class="text-xl font-bold text-gray-900 flex items-center gap-2">
                    <span class="w-3 h-8 rounded-full bg-emerald-500"></span>
                    Pendapatan Berdasarkan Paket
                </h4>
                
                <form method="GET">
                    @foreach(request()->except('omzet_month') as $key => $value) <input type="hidden" name="{{ $key }}" value="{{ $value }}"> @endforeach
                    <input type="month" name="omzet_month" value="{{ $omzetFilter }}" onchange="this.form.submit()" class="text-sm px-4 py-2 bg-white border border-gray-200 rounded-xl font-bold cursor-pointer outline-none focus:ring-2 focus:ring-emerald-500 text-gray-600 shadow-sm transition-all hover:bg-gray-50">
                </form>
            </div>
            
            @if(empty($omzetPaketData))
                <div class="flex-1 flex flex-col items-center justify-center min-h-[250px] border-2 border-dashed border-gray-100 rounded-3xl bg-gray-50/50">
                    <svg class="w-12 h-12 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 12H4m16 0a8 8 0 11-16 0 8 8 0 0116 0z"></path></svg>
                    <p class="text-sm font-bold text-gray-400">Belum ada data pemasukan di bulan ini.</p>
                </div>
            @else
                <div id="chartOmzetPaket" class="w-full flex-1 min-h-[250px]"></div>
            @endif
        </div>
    </div>

    {{-- CHART KANAN (Kuantitas Pie) --}}
    <div class="relative group">
        <div class="glass-card relative p-7 rounded-3xl shadow-sm flex flex-col justify-center h-full transition-all duration-300 hover:shadow-md">
            <h4 class="text-lg font-bold text-gray-900 mb-6 flex items-center justify-center gap-2">
                <span class="w-2.5 h-6 rounded-full bg-fuchsia-500"></span>
                Kuantitas Pemakaian ISP
            </h4>
            <div id="chartPieISP" class="w-full h-64 flex justify-center drop-shadow-md"></div>
        </div>
    </div>
</div>

{{-- ROW 4: AKTIVITAS TRANSAKSI & PELANGGAN MENUNGGAK --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8 relative z-0 animate-fade-up delay-400">
    
    {{-- CHART KIRI (Aktivitas Transaksi) --}}
    <div class="lg:col-span-2 relative group">
        <div class="glass-card relative p-7 rounded-3xl shadow-sm flex flex-col h-full transition-all duration-300 hover:shadow-md">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
                <h4 class="text-xl font-bold text-gray-900 flex items-center gap-2">
                    <span class="w-3 h-8 rounded-full bg-purple-500"></span>
                    Aktivitas Transaksi Harian
                </h4>
                
                <form method="GET">
                    @foreach(request()->except('trx_month') as $key => $value) <input type="hidden" name="{{ $key }}" value="{{ $value }}"> @endforeach
                    <input type="month" name="trx_month" value="{{ $trxFilter }}" onchange="this.form.submit()" class="text-sm px-4 py-2 bg-white border border-gray-200 rounded-xl font-bold cursor-pointer outline-none focus:ring-2 focus:ring-purple-500 text-gray-600 shadow-sm transition-all hover:bg-gray-50">
                </form>
            </div>
            <div id="chartTransaksiPerHari" class="w-full flex-1 min-h-[300px]"></div>
        </div>
    </div>

    {{-- LIST KANAN (Pelanggan Menunggak > 3 Hari) --}}
    <div class="relative group">
        <div class="absolute inset-0 bg-gradient-to-br from-red-100/40 to-rose-100/20 rounded-3xl blur-2xl group-hover:opacity-70 transition-opacity duration-700 pointer-events-none"></div>
        <div class="glass-card relative p-6 rounded-3xl shadow-sm flex flex-col h-full transition-all duration-300 hover:shadow-md border-t-4 border-red-500">
            <h4 class="text-lg font-bold text-gray-900 mb-1 flex items-center gap-2">
                <span class="w-2.5 h-6 rounded-full bg-red-500"></span>
                Peringatan Tunggakan
            </h4>
            <p class="text-[11px] text-gray-500 font-medium mb-5">Telat lebih dari 3 hari, perlu ditindaklanjuti.</p>

            <div class="flex-1 space-y-3 overflow-y-auto custom-scrollbar pr-1 max-h-[300px]">
                @forelse($pelangganOverdue as $plg)
                    @php
                        $telatHari = \Carbon\Carbon::parse($plg->jatuh_tempo)->diffInDays(\Carbon\Carbon::now());
                    @endphp
                    <div class="flex justify-between items-center bg-red-50/50 p-3 rounded-2xl border border-red-100 shadow-sm hover:bg-red-50 transition-all">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-red-100 text-red-600 flex items-center justify-center text-xs font-black shadow-inner border border-white">
                                {{ $telatHari }}H
                            </div>
                            <div>
                                <p class="text-xs font-bold text-gray-900">{{ $plg->nama_pelanggan }}</p>
                                <p class="text-[10px] text-red-500 font-bold mt-0.5">{{ $plg->paket->nama_paket ?? 'Tanpa Paket' }}</p>
                            </div>
                        </div>
                        <a href="{{ route('pelanggan.index', ['q' => $plg->nama_pelanggan]) }}" class="text-[10px] bg-red-600 hover:bg-red-700 text-white px-3 py-2 rounded-lg font-bold transition-colors shadow-md shadow-red-200" title="Cek & Isolir Pelanggan">
                            Cek
                        </a>
                    </div>
                @empty
                    <div class="text-center p-6 text-xs font-bold text-emerald-600 border-2 border-dashed border-emerald-200 rounded-2xl bg-emerald-50/50 flex flex-col items-center justify-center gap-2 h-full min-h-[200px]">
                        <svg class="w-8 h-8 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <span>Mantap! Tidak ada yang menunggak.</span>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const fontFamily = 'Instrument Sans, sans-serif';
    const formatRupiah = (value) => "Rp " + value.toLocaleString('id-ID');

    // 1. CHART PELANGGAN BARU (BAR)
    const optionsPelanggan = {
        series: [{ name: 'Pelanggan Baru', data: @json($pelangganBaru) }],
        chart: { 
            type: 'bar', height: '100%', minHeight: 250, toolbar: { show: false }, fontFamily: fontFamily,
            animations: { enabled: true, easing: 'easeinout', speed: 800 }
        },
        colors: ['#3b82f6'],
        plotOptions: { bar: { borderRadius: 6, columnWidth: '35%', borderRadiusApplication: 'end' } },
        dataLabels: { enabled: false },
        xaxis: {
            categories: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Ags', 'Sep', 'Okt', 'Nov', 'Des'],
            labels: { style: { colors: '#64748b', fontWeight: 600 } },
            axisBorder: { show: false }, axisTicks: { show: false }
        },
        yaxis: { labels: { style: { colors: '#64748b', fontWeight: 600 } } },
        grid: { borderColor: '#f1f5f9', strokeDashArray: 4 },
        fill: { type: 'gradient', gradient: { shade: 'light', type: 'vertical', opacityFrom: 1, opacityTo: 0.7, stops: [0, 100] } },
        tooltip: { theme: 'light', style: { fontSize: '13px', fontFamily: fontFamily } }
    };
    new ApexCharts(document.querySelector("#chartPelangganBaru"), optionsPelanggan).render();

    // 2. CHART PENDAPATAN PER PAKET (HORIZONTAL BAR)
    @if(!empty($omzetPaketData))
    const optionsOmzet = {
        series: [{ name: 'Total Omzet', data: @json($omzetPaketData) }],
        chart: { type: 'bar', height: '100%', minHeight: 250, toolbar: { show: false }, fontFamily: fontFamily },
        colors: ['#10b981', '#3b82f6', '#f59e0b', '#8b5cf6', '#ec4899', '#14b8a6'], 
        plotOptions: { 
            bar: { horizontal: true, distributed: true, borderRadius: 8, barHeight: '55%' } 
        },
        dataLabels: { 
            enabled: true, 
            formatter: function(val) { return formatRupiah(val); },
            style: { fontSize: '12px', fontWeight: 800, colors: ['#fff'] }, 
            dropShadow: { enabled: true, top: 1, left: 1, blur: 1, color: '#000', opacity: 0.2 } 
        },
        xaxis: { categories: @json($omzetPaketLabels), labels: { show: false }, axisBorder: { show: false }, axisTicks: { show: false } },
        yaxis: { labels: { style: { colors: '#475569', fontWeight: 700, fontSize: '13px' } } },
        grid: { show: false }, 
        tooltip: { theme: 'light', style: { fontSize: '13px', fontFamily: fontFamily }, y: { formatter: function(val) { return formatRupiah(val); } } }, 
        legend: { show: false }
    };
    new ApexCharts(document.querySelector("#chartOmzetPaket"), optionsOmzet).render();
    @endif

    // 3. PIE CHART DISTRIBUSI PAKET
    const pieData = @json($pieData);
    const pieLabels = @json($pieLabels);
    const optionsPie = {
        series: pieData.length > 0 ? pieData : [1], 
        labels: pieLabels.length > 0 ? pieLabels : ['Belum Ada Data'],
        chart: { type: 'donut', height: 300, fontFamily: fontFamily },
        colors: ['#8b5cf6', '#3b82f6', '#0ea5e9', '#10b981', '#f59e0b', '#f43f5e'],
        dataLabels: { enabled: false }, 
        stroke: { show: true, colors: '#ffffff', width: 3 },
        plotOptions: { 
            pie: { 
                donut: { 
                    size: '72%', 
                    labels: { 
                        show: true, 
                        name: { fontSize: '12px', fontWeight: 600, color: '#64748b' },
                        value: { fontSize: '28px', fontWeight: 900, color: '#0f172a' },
                        total: { show: true, showAlways: true, label: 'TOTAL', fontSize: '11px', fontWeight: 800, color: '#94a3b8' } 
                    } 
                } 
            } 
        },
        legend: { position: 'bottom', fontWeight: 600, fontSize: '12px', markers: { radius: 12, width: 10, height: 10 }, itemMargin: { horizontal: 8, vertical: 4 } }, 
        tooltip: { theme: 'light', style: { fontSize: '13px', fontFamily: fontFamily } }
    };
    new ApexCharts(document.querySelector("#chartPieISP"), optionsPie).render();

    // 4. CHART TOTAL TRANSAKSI PER HARI (AREA CHART PREMIUM)
    // 4. CHART TOTAL TRANSAKSI PER HARI (AREA CHART PREMIUM)
    // Ambil bulan dan tahun dari filter (Bulan dipaksa jadi 2 digit, misal: 4 jadi 04)
    const trxSelectedMonth = '{{ str_pad($trxMonth, 2, "0", STR_PAD_LEFT) }}';
    const trxSelectedYear = '{{ $trxYear }}';

    const optionsTrx = {
        series: [{ name: 'Transaksi', data: @json($trxPerHariData) }],
        chart: { type: 'area', height: '100%', minHeight: 300, toolbar: { show: true , tools: {
                    download: false, // Sembunyikan tombol download kalau tidak perlu
                    selection: true,
                    zoom: false,
                    zoomin: false,
                    zoomout: false,
                    pan: true, // Tombol tangan untuk geser kiri-kanan
                    reset: false
                },}, fontFamily: fontFamily },
        colors: ['#8b5cf6'], 
        stroke: { curve: 'smooth', width: 4 },
        fill: { 
            type: 'gradient', 
            gradient: { shadeIntensity: 1, opacityFrom: 0.4, opacityTo: 0.0, stops: [0, 100] } 
        },
        dataLabels: { enabled: false },
        xaxis: { 
            categories: @json($trxPerHariLabel), 
            labels: { style: { colors: '#64748b', fontWeight: 600 } }, 
            axisBorder: { show: false }, 
            axisTicks: { show: false },
            crosshairs: { show: true, width: 1, stroke: { color: '#cbd5e1', dashArray: 4 } }
        },
        yaxis: { labels: { style: { colors: '#64748b', fontWeight: 600 } } },
        grid: { borderColor: '#f1f5f9', strokeDashArray: 4 }, 
        tooltip: { 
            theme: 'light', 
            style: { fontSize: '13px', fontFamily: fontFamily },
            x: {
                formatter: function(val) {
                    // Tambahin '0' di depan angka satuan (misal 8 jadi 08)
                    let day = val.toString().padStart(2, '0');
                    return `${day}/${trxSelectedMonth}/${trxSelectedYear}`;
                }
            }
        },
        markers: { size: 0, hover: { size: 6, sizeOffset: 3, colors: ['#fff'], strokeColors: '#8b5cf6', strokeWidth: 3 } }
    };
    new ApexCharts(document.querySelector("#chartTransaksiPerHari"), optionsTrx).render();
});
</script>
@endpush