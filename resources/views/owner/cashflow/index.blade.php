@extends('layouts.app')

@section('title', 'Laporan Cashflow')

@section('content')
<div x-data="{ 
    activeTab: new URLSearchParams(window.location.search).get('tab') || 'ringkasan', 
    openFilter: false 
}">
    
    {{-- HEADER --}}
    <div class="flex justify-between items-end mb-6 px-1">
        <div>
            <h3 class="text-2xl font-bold text-gray-900 tracking-tight">Arus Kas (Cashflow)</h3>
            <p class="text-sm text-gray-500 font-medium mt-1">Pantau ringkasan dan detail arus uang masuk & keluar bulan {{ \Carbon\Carbon::create()->month($month)->translatedFormat('F') }} {{ $year }}.</p>
        </div>
    </div>

    {{-- BARIS PENCARIAN & FILTER (STYLE MODERN CLEAN) --}}
    <div class="bg-white rounded-2xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-gray-100 mb-6 flex flex-col md:flex-row md:items-center justify-between overflow-visible transition-all hover:shadow-[0_8px_30px_rgb(0,0,0,0.08)]">
        
        {{-- Input Search Kiri --}}
        <form action="{{ route('cashflow.index') }}" method="GET" class="flex-1 flex items-center m-0 border-b md:border-b-0 border-gray-50 group" id="searchForm">
            {{-- Simpan state filter bulan & tahun --}}
            <input type="hidden" name="month" value="{{ $month }}">
            <input type="hidden" name="year" value="{{ $year }}">
            
            
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

    {{-- TAB 1: RINGKASAN PER TANGGAL --}}
    <div x-show="activeTab === 'ringkasan'" x-cloak class="relative overflow-x-auto bg-white shadow-sm rounded-2xl border border-gray-100">
        <table class="w-full text-left text-gray-600 border-collapse">
            <thead class="text-[11px] text-gray-400 bg-gray-50/50 border-b border-gray-100 uppercase tracking-widest font-black">
                <tr>
                    <th class="px-6 py-4 w-48">Tanggal</th>
                    <th class="px-6 py-4 text-right">Pemasukkan (Rp)</th>
                    <th class="px-6 py-4 text-right">Pengeluaran (Rp)</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @foreach($summary as $row)
                <tr class="hover:bg-blue-50/30 transition-colors {{ $row['pemasukkan'] == 0 && $row['pengeluaran'] == 0 ? 'opacity-40' : '' }}">
                    <td class="px-6 py-4 text-sm font-bold text-gray-900 whitespace-nowrap">
                        {{ \Carbon\Carbon::parse($row['tanggal'])->translatedFormat('d F Y') }}
                    </td>
                    <td class="px-6 py-4 text-sm font-black text-right {{ $row['pemasukkan'] > 0 ? 'text-emerald-600' : 'text-gray-400' }}">
                        {{ number_format($row['pemasukkan'], 0, ',', '.') }}
                    </td>
                    <td class="px-6 py-4 text-sm font-black text-right {{ $row['pengeluaran'] > 0 ? 'text-rose-500' : 'text-gray-400' }}">
                        {{ number_format($row['pengeluaran'], 0, ',', '.') }}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- TAB 2: DETAIL TRANSAKSI --}}
    <div x-show="activeTab === 'detail'" x-cloak class="relative overflow-x-auto bg-white shadow-sm rounded-2xl border border-gray-100">
        <table class="w-full text-left text-gray-600 border-collapse">
            <thead class="text-[11px] text-gray-400 bg-gray-50/50 border-b border-gray-100 uppercase tracking-widest font-black">
                <tr>
                    <th class="px-6 py-4 w-36 whitespace-nowrap">Tanggal</th>
                    <th class="px-6 py-4 w-48">Kategori</th>
                    <th class="px-6 py-4">Keterangan</th>
                    <th class="px-6 py-4 text-right w-40">Pemasukkan (Rp)</th>
                    <th class="px-6 py-4 text-right w-40">Pengeluaran (Rp)</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($details as $dtl)
                <tr class="hover:bg-blue-50/30 transition-colors">
                    
                    {{-- 1. TANGGAL --}}
                    <td class="px-6 py-4 text-sm font-bold text-gray-900 whitespace-nowrap">
                        {{ \Carbon\Carbon::parse($dtl['tanggal'])->format('d/m/Y') }}
                    </td>
                    
                    {{-- 2. KATEGORI (CHIP/BADGE) --}}
                    <td class="px-6 py-4">
                        @if($dtl['pemasukkan'] > 0)
                            <span class="inline-block px-3 py-1 bg-emerald-50 text-emerald-600 text-[10px] font-black uppercase tracking-wider rounded-md border border-emerald-100 whitespace-nowrap">
                                {{ $dtl['kategori'] }}
                            </span>
                        @else
                            <span class="inline-block px-3 py-1 bg-rose-50 text-rose-600 text-[10px] font-black uppercase tracking-wider rounded-md border border-rose-100 whitespace-nowrap">
                                {{ $dtl['kategori'] }}
                            </span>
                        @endif
                    </td>
                    
                    {{-- 3. KETERANGAN --}}
                    <td class="px-6 py-4">
                        <p class="text-sm font-bold text-gray-700 line-clamp-2" title="{{ $dtl['keterangan'] }}">
                            {{ $dtl['keterangan'] }}
                        </p>
                    </td>
                    
                    {{-- 4. PEMASUKKAN --}}
                    <td class="px-6 py-4 text-sm font-black text-right {{ $dtl['pemasukkan'] > 0 ? 'text-emerald-600' : 'text-gray-300' }}">
                        {{ $dtl['pemasukkan'] > 0 ? number_format($dtl['pemasukkan'], 0, ',', '.') : '-' }}
                    </td>
                    
                    {{-- 5. PENGELUARAN --}}
                    <td class="px-6 py-4 text-sm font-black text-right {{ $dtl['pengeluaran'] > 0 ? 'text-rose-500' : 'text-gray-300' }}">
                        {{ $dtl['pengeluaran'] > 0 ? number_format($dtl['pengeluaran'], 0, ',', '.') : '-' }}
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-12 text-center">
                        <div class="flex flex-col items-center justify-center text-gray-400">
                            <svg class="w-12 h-12 mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                            <span class="text-sm font-bold">Tidak ada data arus kas di bulan ini.</span>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</div>
@endsection