@extends('layouts.app')

@section('title', 'Laporan Cashflow')

@section('content')
<div x-data="{ 
    activeTab: new URLSearchParams(window.location.search).get('tab') || 'ringkasan', 
    openFilter: false 
}">
    
    {{-- HEADER --}}
    <div class="flex flex-col md:flex-row justify-between md:items-end gap-4 mb-6 px-1">
        <div>
            <h3 class="text-2xl font-bold text-gray-900 tracking-tight">Arus Kas (Cashflow)</h3>
            <p class="text-sm text-gray-500 font-medium mt-1">Pantau ringkasan dan detail arus uang masuk & keluar.</p>
        </div>
        
        {{-- INDIKATOR PERIODE (BULAN & TAHUN) DI KANAN TANPA CARD --}}
        <div class="flex items-center gap-3 shrink-0">
            <div class="bg-blue-50 text-blue-600 p-2 rounded-xl">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
            </div>
            <div class="flex flex-col">
                <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest leading-none mb-1">Periode Laporan</span>
                <span class="text-xl font-black text-gray-900 leading-none tracking-tight uppercase">{{ \Carbon\Carbon::create()->month($month)->translatedFormat('F') }} {{ $year }}</span>
            </div>
        </div>
    </div>

    {{-- BARIS PENCARIAN & FILTER (STYLE MODERN CLEAN) --}}
    <div class="bg-white rounded-2xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-gray-100 mb-6 flex flex-col md:flex-row md:items-center justify-between overflow-visible transition-all hover:shadow-[0_8px_30px_rgb(0,0,0,0.08)]">
        
        {{-- Input Search Kiri --}}
        <form action="{{ route('cashflow.index') }}" method="GET" class="flex-1 flex items-center m-0 border-b md:border-b-0 border-gray-50 group" id="searchForm">
            {{-- Simpan state filter bulan & tahun --}}
            <input type="hidden" name="month" value="{{ $month }}">
            <input type="hidden" name="year" value="{{ $year }}">
            <input type="hidden" name="tab" :value="activeTab">
            
            <div class="pl-5 pr-2 text-gray-400 group-focus-within:text-blue-500 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            </div>
            <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari keterangan arus kas..." 
                   class="w-full py-4 bg-transparent border-none focus:ring-0 outline-none text-sm font-medium text-gray-700 placeholder-gray-400" 
                   onkeydown="if(event.key === 'Enter') this.form.submit();">
        </form>

        {{-- Kumpulan Tombol Kanan --}}
        <div class="flex items-center justify-end gap-2 p-2 px-3 shrink-0 bg-gray-50/30 md:bg-transparent">
            <a href="{{ route('cashflow.index') }}" class="text-[11px] font-black text-gray-400 hover:text-red-500 px-3 py-2 transition-colors tracking-widest uppercase">Reset</a>
            
            <a href="{{ request()->fullUrlWithQuery(['export' => '1']) }}" class="text-[11px] font-black text-gray-400 hover:text-blue-600 px-3 py-2 transition-colors tracking-widest uppercase border-r border-gray-200 pr-4 mr-2" title="Download data sebagai Excel">Export</a>
            
            {{-- Wrapper Tombol Filter & Popover Dialog --}}
            <div class="relative">
                <button @click="openFilter = !openFilter" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-xl text-xs font-bold flex items-center gap-2 transition-all active:scale-95 shadow-lg shadow-blue-500/20 relative z-10">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path></svg>
                    Filter
                </button>

                {{-- POPOVER DIALOG FILTER --}}
                <div x-show="openFilter" 
                     @click.away="openFilter = false"
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 translate-y-2 scale-95"
                     x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                     x-transition:leave="transition ease-in duration-150"
                     x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                     x-transition:leave-end="opacity-0 translate-y-2 scale-95"
                     x-cloak 
                     class="absolute right-0 top-full mt-3 w-80 bg-white rounded-2xl shadow-[0_10px_40px_rgb(0,0,0,0.1)] border border-gray-100 p-5 z-[100]">
                    
                    <h4 class="text-sm font-black text-gray-900 tracking-tight mb-4">Pilih Bulan & Tahun</h4>
                    
                    <form action="{{ route('cashflow.index') }}" method="GET">
                        <input type="hidden" name="q" value="{{ request('q') }}">
                        <input type="hidden" name="tab" :value="activeTab">
                        
                        <div class="space-y-4">
                            <div>
                                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5 ml-1">Bulan</label>
                                <select name="month" class="w-full p-2.5 bg-gray-50 border border-gray-100 rounded-xl text-sm font-bold text-gray-700 outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-all cursor-pointer">
                                    @foreach(range(1, 12) as $m)
                                        <option value="{{ sprintf('%02d', $m) }}" {{ $month == sprintf('%02d', $m) ? 'selected' : '' }}>
                                            {{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <div>
                                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5 ml-1">Tahun</label>
                                <select name="year" class="w-full p-2.5 bg-gray-50 border border-gray-100 rounded-xl text-sm font-bold text-gray-700 outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-all cursor-pointer">
                                    @for($y = date('Y'); $y >= date('Y') - 3; $y--)
                                        <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                                    @endfor
                                </select>
                            </div>
                        </div>

                        <div class="mt-6">
                            <button type="submit" class="w-full py-2.5 bg-blue-600 text-white text-xs font-bold rounded-xl hover:bg-blue-700 transition-all shadow-md shadow-blue-500/20">Terapkan Filter</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- KARTU SUMMARY ATAS --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-5 mb-8">
        <div class="bg-white p-6 rounded-3xl border border-gray-100 shadow-[0_8px_30px_rgb(0,0,0,0.04)] relative overflow-hidden group">
            <div class="absolute inset-0 bg-gradient-to-r from-emerald-400 to-teal-400 opacity-0 group-hover:opacity-10 transition-opacity duration-500"></div>
            <p class="text-[11px] font-black text-gray-400 uppercase tracking-widest mb-2 relative z-10">Total Pemasukkan</p>
            <h4 class="text-3xl font-black text-emerald-600 relative z-10">Rp {{ number_format($totalPemasukkanBulanIni, 0, ',', '.') }}</h4>
        </div>
        <div class="bg-white p-6 rounded-3xl border border-gray-100 shadow-[0_8px_30px_rgb(0,0,0,0.04)] relative overflow-hidden group">
            <div class="absolute inset-0 bg-gradient-to-r from-rose-400 to-red-400 opacity-0 group-hover:opacity-10 transition-opacity duration-500"></div>
            <p class="text-[11px] font-black text-gray-400 uppercase tracking-widest mb-2 relative z-10">Total Pengeluaran</p>
            <h4 class="text-3xl font-black text-rose-500 relative z-10">Rp {{ number_format($totalPengeluaranBulanIni, 0, ',', '.') }}</h4>
        </div>
        <div class="bg-white p-6 rounded-3xl border border-gray-100 shadow-[0_8px_30px_rgb(0,0,0,0.04)] relative overflow-hidden group">
            <div class="absolute inset-0 bg-gradient-to-r from-blue-400 to-indigo-400 opacity-0 group-hover:opacity-10 transition-opacity duration-500"></div>
            <p class="text-[11px] font-black text-gray-400 uppercase tracking-widest mb-2 relative z-10">Selisih (Net Bersih)</p>
            <h4 class="text-3xl font-black {{ ($totalPemasukkanBulanIni - $totalPengeluaranBulanIni) < 0 ? 'text-red-500' : 'text-blue-600' }} relative z-10">
                Rp {{ number_format($totalPemasukkanBulanIni - $totalPengeluaranBulanIni, 0, ',', '.') }}
            </h4>
        </div>
    </div>

    {{-- NAVIGASI TABS --}}
    <div class="flex gap-2 border-b border-gray-200 mb-6">
        <button @click="activeTab = 'ringkasan'" 
                :class="activeTab === 'ringkasan' ? 'border-b-2 border-blue-600 text-blue-600 font-bold' : 'text-gray-500 font-medium hover:text-gray-700 hover:border-gray-300'" 
                class="px-4 py-3 text-sm transition-all focus:outline-none">
            Ringkasan Per Tanggal
        </button>
        <button @click="activeTab = 'detail'" 
                :class="activeTab === 'detail' ? 'border-b-2 border-blue-600 text-blue-600 font-bold' : 'text-gray-500 font-medium hover:text-gray-700 hover:border-gray-300'" 
                class="px-4 py-3 text-sm transition-all focus:outline-none">
            Rincian Detail Transaksi
        </button>
    </div>

    {{-- TAB 1: RINGKASAN PER TANGGAL (MODERN CARD VIEW) --}}
    <div x-show="activeTab === 'ringkasan'" x-cloak class="space-y-4">
        @php 
            $hasData = false; 
            // Memastikan Carbon menggunakan bahasa Indonesia
            \Carbon\Carbon::setLocale('id');
        @endphp
        
        @foreach($summary as $row)
            {{-- Filter: Hanya tampilkan hari yang ada transaksinya --}}
            @if($row['pemasukkan'] > 0 || $row['pengeluaran'] > 0)
                @php $hasData = true; @endphp
                <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-[0_2px_10px_rgb(0,0,0,0.02)] flex flex-col md:flex-row md:items-center justify-between gap-4 hover:border-blue-100 hover:shadow-[0_8px_30px_rgb(0,0,0,0.06)] transition-all group">
                    
                    {{-- Kiri: Ikon Tanggal --}}
                    <div class="flex items-center gap-4 w-full md:w-1/4">
                        <div class="bg-gray-50 text-gray-600 rounded-xl w-14 h-14 flex flex-col items-center justify-center shrink-0 border border-gray-100 group-hover:bg-blue-50 group-hover:text-blue-600 group-hover:border-blue-200 transition-colors">
                            {{-- Angka Tanggal --}}
                            <span class="text-xl font-black leading-none">{{ \Carbon\Carbon::parse($row['tanggal'])->format('d') }}</span>
                            {{-- Nama Bulan (Singkat: Jan, Feb, Mar...) --}}
                            <span class="text-[10px] font-bold uppercase tracking-wider mt-1">{{ \Carbon\Carbon::parse($row['tanggal'])->translatedFormat('M') }}</span>
                        </div>
                        <div>
                            <div class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-0.5">Hari</div>
                            {{-- Nama Hari (Senin, Selasa, dst) --}}
                            <div class="font-bold text-gray-900 text-sm">{{ \Carbon\Carbon::parse($row['tanggal'])->translatedFormat('l') }}</div>
                        </div>
                    </div>

                    {{-- Tengah: Pemasukkan & Pengeluaran --}}
                    <div class="flex-1 grid grid-cols-2 gap-4 w-full bg-gray-50/50 rounded-xl p-3 border border-gray-50 md:bg-transparent md:border-none md:p-0">
                        <div class="flex flex-col">
                            <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1 flex items-center gap-1">
                                <svg class="w-3 h-3 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path></svg>
                                Pemasukkan
                            </span>
                            <span class="text-sm sm:text-base font-black {{ $row['pemasukkan'] > 0 ? 'text-emerald-600' : 'text-gray-300' }}">
                                Rp {{ number_format($row['pemasukkan'], 0, ',', '.') }}
                            </span>
                        </div>
                        <div class="flex flex-col">
                            <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1 flex items-center gap-1">
                                <svg class="w-3 h-3 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 10l7-7m0 0l7 7m-7-7v18"></path></svg>
                                Pengeluaran
                            </span>
                            <span class="text-sm sm:text-base font-black {{ $row['pengeluaran'] > 0 ? 'text-rose-500' : 'text-gray-300' }}">
                                Rp {{ number_format($row['pengeluaran'], 0, ',', '.') }}
                            </span>
                        </div>
                    </div>

                    {{-- Kanan: Netto Harian --}}
                    <div class="w-full md:w-1/4 md:text-right flex flex-col justify-center border-t md:border-t-0 md:border-l border-gray-100 pt-4 md:pt-0 md:pl-5">
                        <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Netto Harian</span>
                        @php $netto = $row['pemasukkan'] - $row['pengeluaran']; @endphp
                        <span class="text-lg font-black {{ $netto > 0 ? 'text-blue-600' : ($netto < 0 ? 'text-red-500' : 'text-gray-900') }}">
                            {{ $netto > 0 ? '+' : '' }}Rp {{ number_format($netto, 0, ',', '.') }}
                        </span>
                    </div>
                </div>
            @endif
        @endforeach

        {{-- Jika Kosong --}}
        @if(!$hasData)
            <div class="bg-white rounded-3xl p-12 border border-gray-100 flex flex-col items-center justify-center text-center shadow-[0_2px_10px_rgb(0,0,0,0.02)]">
                <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mb-4">
                    <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                </div>
                <h4 class="text-base font-bold text-gray-900 mb-1">Belum Ada Transaksi</h4>
                <p class="text-sm font-medium text-gray-500">Tidak ada aktifitas keuangan bulan ini.</p>
            </div>
        @endif
    </div>

    {{-- TAB 2: DETAIL TRANSAKSI (MODERN CARD VIEW) --}}
    <div x-show="activeTab === 'detail'" x-cloak class="space-y-4">
        @forelse($details as $dtl)
            <div class="bg-white rounded-2xl p-4 border border-gray-100 shadow-[0_2px_10px_rgb(0,0,0,0.02)] flex flex-col md:flex-row md:items-center justify-between gap-4 hover:border-blue-100 hover:shadow-[0_8px_30px_rgb(0,0,0,0.06)] transition-all group">
                
                {{-- Kiri & Tengah: Ikon & Info --}}
                <div class="flex items-center gap-4 flex-1 min-w-0">
                    {{-- Ikon Bulat (Hijau untuk Masuk, Merah untuk Keluar) --}}
                    <div class="w-12 h-12 rounded-xl flex items-center justify-center shrink-0 border border-transparent {{ $dtl['pemasukkan'] > 0 ? 'bg-emerald-50 text-emerald-600 group-hover:border-emerald-200' : 'bg-rose-50 text-rose-600 group-hover:border-rose-200' }} transition-colors">
                        @if($dtl['pemasukkan'] > 0)
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path></svg>
                        @else
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 10l7-7m0 0l7 7m-7-7v18"></path></svg>
                        @endif
                    </div>

                    <div class="min-w-0 flex-1">
                        {{-- Baris Atas: Tanggal & Kategori --}}
                        <div class="flex items-center gap-2 mb-1">
                            <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest leading-none">
                                {{ \Carbon\Carbon::parse($dtl['tanggal'])->translatedFormat('d M Y') }}
                            </span>
                            <span class="text-gray-300 text-[10px]">•</span>
                            <span class="text-[10px] font-black uppercase tracking-widest leading-none {{ $dtl['pemasukkan'] > 0 ? 'text-emerald-500' : 'text-rose-500' }}">
                                {{ $dtl['kategori'] }}
                            </span>
                        </div>
                        {{-- Judul/Keterangan --}}
                        <h4 class="font-bold text-gray-900 truncate text-sm md:text-base" title="{{ $dtl['keterangan'] }}">
                            {{ $dtl['keterangan'] }}
                        </h4>
                    </div>
                </div>

                {{-- Kanan: Nominal Uang --}}
                <div class="w-full md:w-auto md:text-right flex flex-row md:flex-col items-center md:items-end justify-between border-t md:border-t-0 border-gray-100 pt-3 md:pt-0 shrink-0 pl-16 md:pl-0">
                    <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-0.5 md:block hidden">
                        {{ $dtl['pemasukkan'] > 0 ? 'Pemasukkan' : 'Pengeluaran' }}
                    </span>
                    <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest block md:hidden">
                        Nominal
                    </span>
                    <div class="font-black text-base md:text-lg {{ $dtl['pemasukkan'] > 0 ? 'text-emerald-600' : 'text-rose-600' }}">
                        {{ $dtl['pemasukkan'] > 0 ? '+' : '-' }} Rp {{ number_format($dtl['pemasukkan'] > 0 ? $dtl['pemasukkan'] : $dtl['pengeluaran'], 0, ',', '.') }}
                    </div>
                </div>
            </div>
        @empty
            <div class="bg-white rounded-3xl p-12 border border-gray-100 flex flex-col items-center justify-center text-center shadow-[0_2px_10px_rgb(0,0,0,0.02)]">
                <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mb-4">
                    <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                </div>
                <h4 class="text-base font-bold text-gray-900 mb-1">Tidak Ada Rincian</h4>
                <p class="text-sm font-medium text-gray-500">Tidak ada detail transaksi untuk bulan ini.</p>
            </div>
        @endforelse
    </div>

</div>
@endsection