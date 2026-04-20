@extends('layouts.app')

@section('title', auth()->user()->role == 'Owner' ? 'Catatan Pemasukkan' : 'Transaksi Pelanggan')

@section('content')
<link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>
    <style>
        /* CSS ini buat maksa Tom Select ngikutin style Tailwind kamu (p-3, rounded-xl, dll) */
        .ts-control { border-radius: 0.75rem !important; border: 1px solid #f3f4f6 !important; background-color: #f9fafb !important; padding: 0.75rem 1rem !important; font-size: 0.875rem !important; box-shadow: none !important; }
        .ts-control.focus { border-color: #3b82f6 !important; box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.2) !important; }
        .ts-dropdown { border-radius: 0.75rem !important; overflow: hidden; box-shadow: 0 10px 25px -5px rgb(0 0 0 / 0.1); border: 1px solid #f3f4f6 !important; }
    </style>
<div x-data="{ 
    openFilter: false,
    openAdd: {{ $errors->any() ? 'true' : 'false' }}, 
    openEdit: false, 
    openDelete: false, 
    editData: {}, 
    deleteUrl: '' 
}">
    <div class="flex justify-between items-end mb-6 px-1">
        <div>
            <h3 class="text-2xl font-bold text-gray-900 tracking-tight">
                {{ auth()->user()->role == 'Owner' ? 'Catatan Pemasukkan' : 'Transaksi Pelanggan' }}
            </h3>
            <p class="text-sm text-gray-500 font-medium mt-1">
                {{ auth()->user()->role == 'Owner' ? 'Pantau semua riwayat pemasukkan dari pelanggan' : 'Catat dan kelola riwayat pembayaran tagihan internet pelanggan' }}
            </p>
        </div>
        
        {{-- Tombol 'Catat Pembayaran' hanya tampil untuk Admin --}}
        @if(auth()->user()->role == 'Admin')
        <button @click="openAdd = true" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-lg text-sm font-bold transition-all shadow-sm flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
            Catat Pembayaran
        </button>
        @endif
    </div>

    {{-- FLOATING TOAST SUCCESS (SMOOTH ANIMATION) --}}
    @if(session('success'))
        <div x-data="{
                show: false,
                progress: 100,
                interval: null,
                startTimer() {
                    this.interval = setInterval(() => {
                        this.progress -= 0.5;
                        if (this.progress <= 0) {
                            clearInterval(this.interval);
                            this.show = false;
                        }
                    }, 20);
                },
                pauseTimer() {
                    clearInterval(this.interval);
                },
                init() {
                    setTimeout(() => {
                        this.show = true;
                        this.startTimer();
                    }, 150);
                }
             }" 
             x-show="show" 
             @mouseenter="pauseTimer()"
             @mouseleave="startTimer()"
             x-transition:enter="transition-all transform ease-out duration-500"
             x-transition:enter-start="opacity-0 translate-x-12 scale-90"
             x-transition:enter-end="opacity-100 translate-x-0 scale-100"
             x-transition:leave="transition-all transform ease-in duration-300"
             x-transition:leave-start="opacity-100 translate-x-0 scale-100"
             x-transition:leave-end="opacity-0 translate-x-12 scale-90"
             x-cloak
             class="fixed top-28 right-6 z-[100] max-w-sm w-auto min-w-[280px] bg-white border border-gray-100 shadow-[0_8px_30px_rgb(0,0,0,0.08)] shadow-emerald-500/10 rounded-xl overflow-hidden flex flex-col cursor-default">
            
            <div class="px-4 py-3 flex items-center gap-3">
                <div class="w-8 h-8 rounded-full bg-emerald-100 text-emerald-600 flex flex-shrink-0 items-center justify-center">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                </div>
                <div class="flex-1">
                    <p class="text-sm font-bold text-gray-800 tracking-tight">{{ session('success') }}</p>
                </div>
                <button @click="show = false" class="flex-shrink-0 text-gray-400 hover:text-red-500 p-1 rounded-md transition-colors ml-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
            <div class="w-full h-1 bg-gray-50">
                <div class="h-full bg-emerald-500 transition-all duration-75 ease-linear" :style="`width: ${progress}%`"></div>
            </div>
        </div>
    @endif

    {{-- FLOATING TOAST ERROR VALIDASI (SMOOTH ANIMATION + LIST) --}}
    @if(session('error') || $errors->any())
        <div x-data="{
                show: false,
                progress: 100,
                interval: null,
                startTimer() {
                    this.interval = setInterval(() => {
                        this.progress -= 0.5;
                        if (this.progress <= 0) {
                            clearInterval(this.interval);
                            this.show = false;
                        }
                    }, 25);
                },
                pauseTimer() {
                    clearInterval(this.interval);
                },
                init() {
                    setTimeout(() => {
                        this.show = true;
                        this.startTimer();
                    }, 150);
                }
             }" 
             x-show="show" 
             @mouseenter="pauseTimer()"
             @mouseleave="startTimer()"
             x-transition:enter="transition-all transform ease-out duration-500"
             x-transition:enter-start="opacity-0 translate-x-12 scale-90"
             x-transition:enter-end="opacity-100 translate-x-0 scale-100"
             x-transition:leave="transition-all transform ease-in duration-300"
             x-transition:leave-start="opacity-100 translate-x-0 scale-100"
             x-transition:leave-end="opacity-0 translate-x-12 scale-90"
             x-cloak
             class="fixed top-28 right-6 z-[100] max-w-sm w-auto min-w-[280px] bg-white border border-gray-100 shadow-[0_8px_30px_rgb(0,0,0,0.08)] shadow-red-500/10 rounded-xl overflow-hidden flex flex-col cursor-default">
            
            <div class="px-4 py-3 flex items-start gap-3">
                <div class="w-8 h-8 rounded-full bg-red-100 text-red-600 flex flex-shrink-0 items-center justify-center mt-0.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                </div>
                <div class="flex-1">
                    @if(session('error'))
                        <p class="text-sm font-bold text-gray-800 tracking-tight">{{ session('error') }}</p>
                    @else
                        <p class="text-sm font-bold text-gray-800 tracking-tight mb-1">Gagal Menyimpan Data:</p>
                        <ul class="list-disc list-inside text-[11px] font-medium text-gray-500">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    @endif
                </div>
                <button @click="show = false" class="flex-shrink-0 text-gray-400 hover:text-red-500 p-1 rounded-md transition-colors ml-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
            <div class="w-full h-1 bg-gray-50">
                <div class="h-full bg-red-500 transition-all duration-75 ease-linear" :style="`width: ${progress}%`"></div>
            </div>
        </div>
    @endif

    {{-- BARIS PENCARIAN & FILTER (STYLE MODERN CLEAN) --}}
    <div class="bg-white rounded-2xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-gray-100 mb-6 flex flex-col md:flex-row md:items-center justify-between overflow-visible transition-all hover:shadow-[0_8px_30px_rgb(0,0,0,0.08)]">
        
        {{-- Input Search Kiri --}}
        <form action="{{ route('transaksi.index') }}" method="GET" class="flex-1 flex items-center m-0 border-b md:border-b-0 border-gray-50 group" id="searchForm">
            {{-- Simpan filter state biar nggak hilang pas search --}}
            <input type="hidden" name="paket_id" value="{{ request('paket_id') }}">
            <input type="hidden" name="start_date" value="{{ request('start_date') }}">
            <input type="hidden" name="end_date" value="{{ request('end_date') }}">
            
            <div class="pl-5 pr-2 text-gray-400 group-focus-within:text-blue-500 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            </div>
            <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari transaksi atas nama pelanggan..." 
                   class="w-full py-4 bg-transparent border-none focus:ring-0 outline-none text-sm font-medium text-gray-700 placeholder-gray-400" 
                   onkeydown="if(event.key === 'Enter') this.form.submit();">
        </form>

        {{-- Kumpulan Tombol Kanan --}}
        <div class="flex items-center justify-end gap-2 p-2 px-3 shrink-0 bg-gray-50/30 md:bg-transparent">
            
            <a href="{{ route('transaksi.index') }}" class="text-[11px] font-black text-gray-400 hover:text-red-500 px-3 py-2 transition-colors tracking-widest uppercase">Reset</a>
            
            <a href="{{ request()->fullUrlWithQuery(['export' => '1']) }}" class="text-[11px] font-black text-gray-400 hover:text-blue-600 px-3 py-2 transition-colors tracking-widest uppercase border-r border-gray-200 pr-4 mr-2" title="Download data tagihan Excel">Export</a>
            
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
                    
                    <h4 class="text-sm font-black text-gray-900 tracking-tight mb-4">Filter Transaksi</h4>
                    
                    <form action="{{ route('transaksi.index') }}" method="GET">
                        <input type="hidden" name="q" value="{{ request('q') }}">
                        
                        <div class="space-y-4">
                            <div>
                                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5 ml-1">Pilih Paket</label>
                                <select name="paket_id" class="w-full p-2.5 bg-gray-50 border border-gray-100 rounded-xl text-sm font-bold text-gray-700 outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-all cursor-pointer">
                                    <option value="">Semua Paket</option>
                                    @foreach($pakets as $paket)
                                        <option value="{{ $paket->id }}" {{ request('paket_id') == $paket->id ? 'selected' : '' }}>{{ $paket->nama_paket }}</option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <div>
                                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5 ml-1">Dari Tanggal (Bayar)</label>
                                <input type="date" name="start_date" value="{{ request('start_date') }}" class="w-full p-2.5 bg-gray-50 border border-gray-100 rounded-xl text-sm font-bold text-gray-700 outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-all cursor-pointer">
                            </div>
                            
                            <div>
                                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5 ml-1">Sampai Tanggal (Bayar)</label>
                                <input type="date" name="end_date" value="{{ request('end_date') }}" class="w-full p-2.5 bg-gray-50 border border-gray-100 rounded-xl text-sm font-bold text-gray-700 outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-all cursor-pointer">
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

    {{-- TABEL DATA TRANSAKSI (DIPERBARUI) --}}
    <div class="relative overflow-x-auto bg-white shadow-sm rounded-2xl border border-gray-100">
        <table class="w-full text-left text-gray-600 border-collapse">
            <thead class="text-[11px] text-gray-400 bg-gray-50/50 border-b border-gray-100 uppercase tracking-widest font-black">
                <tr>
                    <th class="px-6 py-4 whitespace-nowrap">Tanggal Bayar</th>
                    <th class="px-6 py-4 whitespace-nowrap">Nama Pelanggan</th>
                    <th class="px-6 py-4 whitespace-nowrap">Paket ISP</th>
                    <th class="px-6 py-4 text-center whitespace-nowrap">Jumlah Nominal</th>
                    <th class="px-6 py-4 text-center whitespace-nowrap">Dibuat Pada</th>
                    <th class="px-6 py-4 text-right whitespace-nowrap">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($transaksis as $trx)
                <tr class="hover:bg-blue-50/30 transition-colors group">
                    
                    {{-- 1. Tanggal Bayar --}}
                    <td class="px-6 py-4 text-sm font-bold text-gray-600 whitespace-nowrap">
                        {{ \Carbon\Carbon::parse($trx->tanggal)->format('d/m/Y') }}
                    </td>
                    
                    {{-- 2. Nama Pelanggan --}}
                    <td class="px-6 py-4 text-sm font-bold text-gray-900 whitespace-nowrap">
                        {{ $trx->pelanggan->nama_pelanggan ?? 'Pelanggan Dihapus' }}
                    </td>
                    
                    {{-- 3. Paket ISP (CHIP DENGAN HURUF NORMAL/TITLE CASE) --}}
                    <td class="px-6 py-4">
                        <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-bold bg-blue-50 text-blue-600 border border-blue-100 whitespace-nowrap">
                            {{ $trx->pelanggan->paket->nama_paket ?? 'Tanpa Paket' }}
                        </span>
                    </td>
                    
                    {{-- 4. Jumlah Nominal --}}
                    <td class="px-6 py-4 text-sm font-bold text-center text-emerald-600 whitespace-nowrap">
                        Rp {{ number_format($trx->jumlah, 0, ',', '.') }}
                    </td>
                    
                    {{-- 5. Dibuat Pada --}}
                    <td class="px-6 py-4 text-center">
                        <div class="flex flex-col items-center justify-center">
                            <span class="text-sm font-bold text-gray-600 whitespace-nowrap">{{ $trx->created_at->format('d/m/Y') }}</span>
                            <span class="text-[10px] text-gray-400 font-black uppercase tracking-widest mt-0.5">{{ $trx->created_by ?? 'SYSTEM' }}</span>
                        </div>
                    </td>

                    {{-- 6. Aksi --}}
                    <td class="px-6 py-4 text-right">
                        <div class="flex justify-end gap-2">                    
                            {{-- Tombol Edit --}}
                            <button @click="openEdit = true; editData = {{ json_encode($trx) }}" class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-all" title="Edit Data">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                            </button>
                    
                            {{-- Tombol Hapus --}}
                            <button @click="openDelete = true; deleteUrl = '{{ route('transaksi.destroy', $trx->id) }}'" class="p-2 text-red-500 hover:bg-red-50 rounded-lg transition-all" title="Hapus Transaksi">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-12 text-center">
                        <div class="flex flex-col items-center justify-center text-gray-400">
                            <svg class="w-12 h-12 mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                            <span class="text-sm font-bold">Belum ada riwayat pembayaran.</span>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- MODAL TAMBAH (DIPERBARUI ROUNDED-2XL) --}}
    <div x-show="openAdd" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm p-4 transition-all">
        <div class="bg-white w-full max-w-2xl rounded-2xl shadow-2xl p-8" @click.away="openAdd = false">
            <h4 class="text-xl font-bold text-gray-900 mb-1">Catat Pembayaran Baru</h4>
            <p class="text-sm text-gray-500 mb-6">Pilih pelanggan dan tentukan perpanjangan masa aktif</p>
            
            <form action="{{ route('transaksi.store') }}" method="POST" class="space-y-4" 
                  x-data="{ 
                      isSubmitting: false,
                      selectedPelanggan: '',
                      jumlahBulan: 1,
                      nominalTampil: '',
                      hargaPaket: {
                          @foreach($pelanggans as $plg)
                          '{{ $plg->id }}': {{ $plg->paket->harga ?? 0 }},
                          @endforeach
                      },
                      kalkulasi() {
                          if(this.selectedPelanggan) {
                              this.nominalTampil = this.hargaPaket[this.selectedPelanggan] * this.jumlahBulan;
                          } else {
                              this.nominalTampil = '';
                          }
                      }
                  }" 
                  @submit="isSubmitting = true">
                @csrf
                <div>
                    <label class="block text-[10px] font-black text-gray-700 uppercase tracking-widest mb-1.5 ml-1">Pilih Pelanggan</label>
                    <select name="pelanggan_id" 
                            x-model="selectedPelanggan" 
                            @change="kalkulasi()" 
                            x-init="$watch('openAdd', val => { if(val && !$el.tomselect) { new TomSelect($el, {create: false, placeholder: '-- Cari Pelanggan --'}); } })"
                            class="w-full text-sm p-3 bg-gray-50 border border-gray-100 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none font-medium text-gray-800 cursor-pointer transition-all" required>
                        <option value="">-- Cari Pelanggan --</option>
                        @foreach($pelanggans as $plg)
                            @php 
                                $tgl = $plg->jatuh_tempo ? \Carbon\Carbon::parse($plg->jatuh_tempo)->translatedFormat('d M Y') : 'Belum Diatur';
                            @endphp
                            <option value="{{ $plg->id }}">{{ $plg->nama_pelanggan }} (Jatuh Tempo: {{ $tgl }})</option>
                        @endforeach
                    </select>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-[10px] font-black text-gray-700 uppercase tracking-widest mb-1.5 ml-1">Tanggal Bayar</label>
                        <input type="date" name="tanggal" value="{{ date('Y-m-d') }}" class="w-full text-sm p-3 bg-gray-50 border border-gray-100 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none font-bold text-gray-800 transition-all cursor-pointer" required>
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-gray-700 uppercase tracking-widest mb-1.5 ml-1">Perpanjang</label>
                        <select name="jumlah_bulan" x-model="jumlahBulan" @change="kalkulasi()" class="w-full text-sm p-3 bg-gray-50 border border-gray-100 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none font-bold text-gray-800 cursor-pointer transition-all">
                            <option value="1">1 Bulan</option>
                            <option value="2">2 Bulan</option>
                            <option value="3">3 Bulan</option>
                            <option value="6">6 Bulan</option>
                            <option value="12">1 Tahun</option>
                            <option value="0">0 (Hanya Bayar Cicilan)</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-gray-700 uppercase tracking-widest mb-1.5 ml-1">Nominal (Rp)</label>
                        <input type="number" name="jumlah" x-model="nominalTampil" placeholder="0" class="w-full text-sm p-3 bg-gray-50 border border-gray-100 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none font-bold text-gray-900 transition-all" required>
                    </div>
                </div>

                <div class="flex gap-3 pt-6 mt-6 border-t border-slate-100">
                    <button type="button" @click="openAdd = false" class="flex-1 text-sm font-bold text-gray-600 p-3 hover:bg-gray-100 rounded-xl transition-all">Batal</button>
                    <button type="submit" x-bind:disabled="isSubmitting" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white text-sm font-bold rounded-xl p-3 shadow-lg shadow-blue-100 transition-all flex justify-center items-center disabled:opacity-70 disabled:cursor-not-allowed">
                        <span x-show="!isSubmitting">Simpan Transaksi</span>
                        <span x-show="isSubmitting" x-cloak class="flex items-center gap-2">
                            <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- MODAL EDIT (DIPERBARUI ROUNDED-2XL) --}}
    <div x-show="openEdit" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm p-4 transition-all">
        <div class="bg-white w-full max-w-xl rounded-2xl shadow-2xl p-8" @click.away="openEdit = false">
            <h4 class="text-xl font-bold text-gray-900 mb-1">Edit Riwayat Pembayaran</h4>
            <p class="text-sm text-gray-500 mb-6">Perbarui tanggal atau jumlah nominal tagihan</p>

            <form :action="'/transaksi/' + editData.id" method="POST" class="space-y-4" x-data="{ isSubmitting: false }" @submit="isSubmitting = true">
                @csrf @method('PUT')
                <div>
                    <label class="block text-[10px] font-black text-gray-700 uppercase tracking-widest mb-1.5 ml-1">Pilih Pelanggan</label>
                    <select name="pelanggan_id" 
                            x-model="editData.pelanggan_id" 
                            x-init="$watch('openEdit', val => { 
                                if(val) { 
                                    if(!$el.tomselect) { new TomSelect($el, {create: false}); }
                                    /* Paksa update UI Tom Select saat data edit di-klik */
                                    setTimeout(() => $el.tomselect.setValue(editData.pelanggan_id), 50);
                                } 
                            })"
                            class="w-full text-sm p-3 bg-gray-50 border border-gray-100 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none font-medium text-gray-800 cursor-pointer transition-all" required>
                        <option value="">-- Cari Pelanggan --</option>
                        @foreach($pelanggans as $plg)
                            <option value="{{ $plg->id }}">{{ $plg->nama_pelanggan }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-[10px] font-black text-gray-700 uppercase tracking-widest mb-1.5 ml-1">Tanggal Bayar</label>
                        <input type="date" name="tanggal" x-model="editData.tanggal" class="w-full text-sm p-3 bg-gray-50 border border-gray-100 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none font-bold text-gray-800 cursor-pointer transition-all" required>
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-gray-700 uppercase tracking-widest mb-1.5 ml-1">Jumlah Nominal (Rp)</label>
                        <input type="number" name="jumlah" x-model="editData.jumlah" class="w-full text-sm p-3 bg-gray-50 border border-gray-100 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none font-bold text-gray-900 transition-all" required>
                    </div>
                </div>

                <div class="flex gap-3 pt-6 mt-6 border-t border-slate-100">
                    <button type="button" @click="openEdit = false" class="flex-1 text-sm font-bold text-gray-600 p-3 hover:bg-gray-100 rounded-xl transition-all">Batal</button>
                    <button type="submit" x-bind:disabled="isSubmitting" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white text-sm font-bold rounded-xl p-3 shadow-lg shadow-blue-100 transition-all flex justify-center items-center disabled:opacity-70 disabled:cursor-not-allowed">
                        <span x-show="!isSubmitting">Update Perubahan</span>
                        <span x-show="isSubmitting" x-cloak class="flex items-center gap-2">
                            <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- MODAL DELETE (DIPERBARUI ROUNDED-2XL) --}}
    <div x-show="openDelete" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm p-4 transition-all">
        <div class="bg-white w-full max-w-md rounded-2xl shadow-2xl p-8 text-center" @click.away="openDelete = false">
            <div class="w-16 h-16 bg-red-50 text-red-500 rounded-full flex items-center justify-center mx-auto mb-5">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
            </div>
            <h4 class="text-xl font-bold text-gray-900 mb-2">Hapus Transaksi?</h4>
            <p class="text-sm text-gray-500 leading-relaxed mb-8 px-4">Tindakan ini akan menghapus riwayat pembayaran secara permanen.</p>
            <form :action="deleteUrl" method="POST" x-data="{ isSubmitting: false }" @submit="isSubmitting = true">
                @csrf @method('DELETE')
                <div class="flex gap-3">
                    <button type="button" @click="openDelete = false" class="flex-1 text-sm font-bold text-gray-600 p-3 hover:bg-gray-100 rounded-xl transition-all">Batalkan</button>
                    <button type="submit" x-bind:disabled="isSubmitting" class="flex-1 bg-red-600 hover:bg-red-700 text-white text-sm font-bold rounded-xl p-3 shadow-lg shadow-red-100 transition-all flex justify-center items-center disabled:opacity-70 disabled:cursor-not-allowed">
                        <span x-show="!isSubmitting">Ya, Hapus</span>
                        <span x-show="isSubmitting" x-cloak class="flex items-center gap-2">
                            <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection