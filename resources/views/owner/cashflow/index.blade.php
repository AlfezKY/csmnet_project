@extends('layouts.app')

@section('title', 'Laporan Cashflow')

@section('content')
<div x-data="{ activeTab: 'ringkasan' }">
    
    {{-- HEADER & FILTER --}}
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-4 mb-6 px-1">
        <div>
            <h3 class="text-2xl font-bold text-gray-900 tracking-tight">Arus Kas (Cashflow)</h3>
            <p class="text-sm text-gray-500 font-medium mt-1">Pantau ringkasan dan detail arus uang masuk & keluar.</p>
        </div>

        <form action="{{ route('cashflow.index') }}" method="GET" class="flex gap-2 items-center bg-white p-2 rounded-lg border border-gray-200 shadow-sm">
            <select name="month" class="text-sm border-none bg-transparent font-bold text-gray-700 outline-none cursor-pointer">
                @foreach(range(1, 12) as $m)
                    <option value="{{ sprintf('%02d', $m) }}" {{ $month == sprintf('%02d', $m) ? 'selected' : '' }}>
                        {{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}
                    </option>
                @endforeach
            </select>
            <select name="year" class="text-sm border-none bg-transparent font-bold text-gray-700 outline-none cursor-pointer border-l border-gray-200 pl-2">
                @for($y = date('Y'); $y >= date('Y') - 3; $y--)
                    <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                @endfor
            </select>
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white p-1.5 rounded text-sm transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            </button>
        </form>
    </div>

    {{-- KARTU SUMMARY ATAS --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm">
            <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">Total Pemasukkan</p>
            <h4 class="text-2xl font-black text-green-600">Rp {{ number_format($totalPemasukkanBulanIni, 0, ',', '.') }}</h4>
        </div>
        <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm">
            <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">Total Pengeluaran</p>
            <h4 class="text-2xl font-black text-red-500">Rp {{ number_format($totalPengeluaranBulanIni, 0, ',', '.') }}</h4>
        </div>
        <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm">
            <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">Selisih (Net Bersih)</p>
            <h4 class="text-2xl font-black {{ ($totalPemasukkanBulanIni - $totalPengeluaranBulanIni) < 0 ? 'text-red-500' : 'text-blue-600' }}">
                Rp {{ number_format($totalPemasukkanBulanIni - $totalPengeluaranBulanIni, 0, ',', '.') }}
            </h4>
        </div>
    </div>

    {{-- NAVIGASI TABS --}}
    <div class="flex gap-2 border-b border-gray-200 mb-6">
        <button @click="activeTab = 'ringkasan'" 
                :class="activeTab === 'ringkasan' ? 'border-b-2 border-blue-600 text-blue-600 font-bold' : 'text-gray-500 font-medium hover:text-gray-700'" 
                class="px-4 py-3 text-sm transition-all focus:outline-none">
            Ringkasan Per Tanggal
        </button>
        <button @click="activeTab = 'detail'" 
                :class="activeTab === 'detail' ? 'border-b-2 border-blue-600 text-blue-600 font-bold' : 'text-gray-500 font-medium hover:text-gray-700'" 
                class="px-4 py-3 text-sm transition-all focus:outline-none">
            Rincian Detail Transaksi
        </button>
    </div>

    {{-- TAB 1: RINGKASAN PER TANGGAL --}}
    <div x-show="activeTab === 'ringkasan'" x-cloak class="bg-white shadow-sm rounded-lg border border-gray-200 overflow-hidden">
        <table class="w-full text-left text-gray-600 border-collapse">
            <thead class="text-xs text-gray-400 bg-gray-50/50 border-b border-gray-200 uppercase tracking-widest font-bold">
                <tr>
                    <th class="px-6 py-4 w-48">Tanggal</th>
                    <th class="px-6 py-4 text-right">Pemasukkan (Rp)</th>
                    <th class="px-6 py-4 text-right">Pengeluaran (Rp)</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach($summary as $row)
                <tr class="hover:bg-gray-50/50 transition-colors {{ $row['pemasukkan'] == 0 && $row['pengeluaran'] == 0 ? 'opacity-40' : '' }}">
                    <td class="px-6 py-4 text-sm font-bold text-gray-700">
                        {{ \Carbon\Carbon::parse($row['tanggal'])->translatedFormat('d F Y') }}
                    </td>
                    <td class="px-6 py-4 text-sm font-bold text-right {{ $row['pemasukkan'] > 0 ? 'text-green-600' : 'text-gray-400' }}">
                        {{ number_format($row['pemasukkan'], 0, ',', '.') }}
                    </td>
                    <td class="px-6 py-4 text-sm font-bold text-right {{ $row['pengeluaran'] > 0 ? 'text-red-500' : 'text-gray-400' }}">
                        {{ number_format($row['pengeluaran'], 0, ',', '.') }}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- TAB 2: DETAIL TRANSAKSI --}}
    <div x-show="activeTab === 'detail'" x-cloak class="bg-white shadow-sm rounded-lg border border-gray-200 overflow-hidden">
        <table class="w-full text-left text-gray-600 border-collapse">
            <thead class="text-xs text-gray-400 bg-gray-50/50 border-b border-gray-200 uppercase tracking-widest font-bold">
                <tr>
                    <th class="px-6 py-4 w-40">Tanggal</th>
                    <th class="px-6 py-4">Keterangan</th>
                    <th class="px-6 py-4 text-right w-40">Pemasukkan (Rp)</th>
                    <th class="px-6 py-4 text-right w-40">Pengeluaran (Rp)</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($details as $dtl)
                <tr class="hover:bg-gray-50/50 transition-colors">
                    <td class="px-6 py-4 text-sm font-bold text-gray-500">
                        {{ \Carbon\Carbon::parse($dtl['tanggal'])->format('d/m/Y') }}
                    </td>
                    <td class="px-6 py-4 text-sm font-medium text-gray-800">
                        {{ $dtl['keterangan'] }}
                    </td>
                    <td class="px-6 py-4 text-sm font-bold text-right {{ $dtl['pemasukkan'] > 0 ? 'text-green-600' : 'text-gray-300' }}">
                        {{ $dtl['pemasukkan'] > 0 ? number_format($dtl['pemasukkan'], 0, ',', '.') : '-' }}
                    </td>
                    <td class="px-6 py-4 text-sm font-bold text-right {{ $dtl['pengeluaran'] > 0 ? 'text-red-500' : 'text-gray-300' }}">
                        {{ $dtl['pengeluaran'] > 0 ? number_format($dtl['pengeluaran'], 0, ',', '.') : '-' }}
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="px-6 py-12 text-center text-sm font-bold text-gray-400">Belum ada transaksi di bulan ini.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</div>
@endsection