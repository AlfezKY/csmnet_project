@extends('layouts.app')

@section('title', 'Tagihan Pelanggan')

@section('content')
<div x-data="{ 
    openFilter: false,
    selectedIds: [], 
    allIds: {{ $pelanggans->pluck('id')->toJson() }},
    allData: [
        @foreach($pelanggans as $p)
            { id: '{{ $p->id }}', harga: {{ $p->paket->harga ?? 0 }} },
        @endforeach
    ],
    
    openConfirm: false, 
    confirmUrl: '', 
    confirmText: '',
    
    // Variabel kalkulator satuan
    hargaPaket: 0,
    jumlahBulan: 1,
    diskonPersen: 0,
    biayaLain: 0, // TAMBAHAN BIAYA LAIN

    // Variabel kalkulator massal
    openBulkConfirm: false,
    bulkJumlahBulan: 1,
    bulkDiskonPersen: 0,
    bulkBiayaLain: 0, // TAMBAHAN BIAYA LAIN MASSAL

    isSubmitting: false,

    get isAllSelected() {
        return this.selectedIds.length === this.allIds.length && this.allIds.length > 0;
    },
    toggleAll() {
        if (this.isAllSelected) {
            this.selectedIds = [];
        } else {
            this.selectedIds = [...this.allIds];
        }
    },

    // Hitung Total Bayar Satuan
    get totalTagihan() {
        let baseTotal = this.hargaPaket * this.jumlahBulan;
        let diskonAmount = baseTotal * (this.diskonPersen / 100);
        let afterDiscount = Math.max(0, baseTotal - diskonAmount);
        return afterDiscount + (Number(this.biayaLain) || 0); // DITAMBAH BIAYA LAIN
    },

    // Hitung Total Bayar Massal
    get totalBulkTagihan() {
        let totalHargaPerBulan = 0;
        this.selectedIds.forEach(id => {
            let plg = this.allData.find(p => p.id == id);
            if(plg) totalHargaPerBulan += plg.harga;
        });
        let baseTotal = totalHargaPerBulan * this.bulkJumlahBulan;
        let diskonAmount = baseTotal * (this.bulkDiskonPersen / 100);
        let afterDiscount = Math.max(0, baseTotal - diskonAmount);
        
        // Biaya lain dikalikan jumlah pelanggan yang dipilih
        let totalBiayaLain = (Number(this.bulkBiayaLain) || 0) * this.selectedIds.length; 
        return afterDiscount + totalBiayaLain;
    },

    // Format Rupiah Otomatis
    formatRupiah(angka) {
        return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(angka);
    }
}">
    <div class="flex justify-between items-end mb-6 px-1">
        <div>
            <h3 class="text-2xl font-bold text-gray-900 tracking-tight">Daftar Tagihan</h3>
            <p class="text-sm text-gray-500 font-medium mt-1">Pelanggan aktif yang belum melakukan pembayaran</p>
        </div>
        
        <div x-show="selectedIds.length > 0" x-cloak class="flex items-center gap-2 bg-gray-900 p-1.5 rounded-lg shadow-lg" x-transition>
            <span class="text-xs text-white font-bold px-3" x-text="selectedIds.length + ' Dipilih'"></span>
            
            {{-- Tombol Kirim WA Massal --}}
            <form action="{{ route('tagihan.bulk-ingatkan') }}" method="POST" class="m-0" 
                  x-data="{ isSendingBulk: false }" 
                  @submit="if(confirm('Kirim WA pengingat ke ' + selectedIds.length + ' pelanggan terpilih?')) { isSendingBulk = true; return true; } else { return false; }">
                @csrf
                <template x-for="id in selectedIds" :key="id">
                    <input type="hidden" name="ids[]" :value="id">
                </template>
                <button type="submit" x-bind:disabled="isSendingBulk" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-md text-xs font-bold transition-all flex items-center gap-1.5 disabled:opacity-70 disabled:cursor-not-allowed">
                    <span x-show="!isSendingBulk" class="flex items-center gap-1.5">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.405-.883-.733-1.48-1.638-1.653-1.935-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51a12.8 12.8 0 00-.57-.01c-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                        WA Massal
                    </span>
                    <span x-show="isSendingBulk" x-cloak class="flex items-center gap-1.5">
                        <svg class="animate-spin w-4 h-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                        Mengirim...
                    </span>
                </button>
            </form>

            {{-- Tombol Lunas Massal --}}
            <button @click="openBulkConfirm = true; bulkJumlahBulan = 1; bulkDiskonPersen = 0; bulkBiayaLain = 0;" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md text-xs font-bold transition-all flex items-center gap-1.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                Lunas Massal
            </button>
        </div>
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

    {{-- FLOATING TOAST ERROR VALIDASI (SMOOTH ANIMATION) --}}
    @if(session('error') || $errors->any())
        <div x-data="{
                show: false,
                progress: 100,
                interval: null,
                startTimer() {
                    // Durasinya dilambatkan sedikit (25) karena ada list error agar sempat dibaca
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
                        <p class="text-sm font-bold text-gray-800 tracking-tight mb-1">Terjadi Kesalahan:</p>
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
        <form action="{{ route('tagihan.index') }}" method="GET" class="flex-1 flex items-center m-0 border-b md:border-b-0 border-gray-50 group" id="searchForm">
            <input type="hidden" name="paket_id" value="{{ request('paket_id') }}">
            <input type="hidden" name="start_date" value="{{ request('start_date') }}">
            <input type="hidden" name="end_date" value="{{ request('end_date') }}">
            
            <div class="pl-5 pr-2 text-gray-400 group-focus-within:text-blue-500 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            </div>
            <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari tagihan atas nama atau alamat..." 
                   class="w-full py-4 bg-transparent border-none focus:ring-0 outline-none text-sm font-medium text-gray-700 placeholder-gray-400" 
                   onkeydown="if(event.key === 'Enter') this.form.submit();">
        </form>

        {{-- Kumpulan Tombol Kanan --}}
        <div class="flex items-center justify-end gap-2 p-2 px-3 shrink-0 bg-gray-50/30 md:bg-transparent">
            <a href="{{ route('tagihan.index') }}" class="text-[11px] font-black text-gray-400 hover:text-red-500 px-3 py-2 transition-colors tracking-widest uppercase">Reset</a>
            <a href="{{ request()->fullUrlWithQuery(['export' => '1']) }}" class="text-[11px] font-black text-gray-400 hover:text-blue-600 px-3 py-2 transition-colors tracking-widest uppercase border-r border-gray-200 pr-4 mr-2" title="Download data tagihan Excel">Export</a>
            
            {{-- Wrapper Tombol Filter & Popover Dialog --}}
            <div class="relative">
                <button @click="openFilter = !openFilter" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-xl text-xs font-bold flex items-center gap-2 transition-all active:scale-95 shadow-lg shadow-blue-500/20 relative z-10">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path></svg>
                    Filter
                </button>

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
                    
                    <h4 class="text-sm font-black text-gray-900 tracking-tight mb-4">Filter Tagihan</h4>
                    
                    <form action="{{ route('tagihan.index') }}" method="GET">
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
                                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5 ml-1">Dari Jatuh Tempo</label>
                                <input type="date" name="start_date" value="{{ request('start_date') }}" class="w-full p-2.5 bg-gray-50 border border-gray-100 rounded-xl text-sm font-bold text-gray-700 outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-all cursor-pointer">
                            </div>
                            <div>
                                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5 ml-1">Sampai Jatuh Tempo</label>
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

    <div class="relative overflow-x-auto bg-white shadow-sm rounded-lg border border-gray-200">
        <table class="w-full text-left text-gray-600 border-collapse">
            <thead class="text-xs text-gray-400 bg-gray-50/50 border-b border-gray-200 uppercase tracking-widest font-bold">
                <tr>
                    <th class="px-4 py-4 w-12 text-center">
                        <input type="checkbox" :checked="isAllSelected" @change="toggleAll" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded cursor-pointer focus:ring-blue-500">
                    </th>
                    <th class="px-6 py-4">Nama Pelanggan</th>
                    <th class="px-6 py-4">Paket & Harga</th>
                    <th class="px-6 py-4">No WA</th>
                    <th class="px-6 py-4 text-center">Jatuh Tempo</th>
                    <th class="px-6 py-4 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($pelanggans as $plg)
                <tr class="hover:bg-gray-50/50 transition-colors group" :class="selectedIds.includes('{{ $plg->id }}') ? 'bg-blue-50/30' : ''">
                    <td class="px-4 py-4 text-center">
                        <input type="checkbox" value="{{ $plg->id }}" x-model="selectedIds" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded cursor-pointer focus:ring-blue-500">
                    </td>
                    <td class="px-6 py-4 text-sm font-bold text-gray-900">{{ $plg->nama_pelanggan }}</td>
                    
                    <td class="px-6 py-4">
                        <div class="flex flex-col">
                            <span class="text-sm font-bold text-blue-600">{{ $plg->paket->nama_paket ?? 'Tanpa Paket' }}</span>
                            <span class="text-[11px] font-medium text-gray-500 mt-0.5">Rp {{ number_format($plg->paket->harga ?? 0, 0, ',', '.') }}</span>
                        </div>
                    </td>
                    
                    <td class="px-6 py-4 text-sm text-gray-500 font-medium">{{ $plg->no_wa }}</td>
                    
                    <td class="px-6 py-4 text-center">
                        <span class="px-3 py-1 rounded-md text-[10px] font-bold uppercase border bg-red-50 text-red-600 border-red-100">
                            {{ $plg->jatuh_tempo ? \Carbon\Carbon::parse($plg->jatuh_tempo)->translatedFormat('d M Y') : '-' }}
                        </span>
                    </td>
                    
                    <td class="px-6 py-4 text-right">
                        <div class="flex justify-end gap-2">
                            <button @click="
                                openConfirm = true; 
                                confirmUrl = '{{ route('tagihan.action', $plg->id) }}'; 
                                confirmText = 'Tandai tagihan {{ $plg->nama_pelanggan }} sebagai Lunas?';
                                hargaPaket = {{ $plg->paket->harga ?? 0 }};
                                jumlahBulan = 1;
                                diskonPersen = 0;
                                biayaLain = 0;
                            " class="px-2.5 py-1 bg-blue-50 text-blue-700 border border-blue-100 hover:bg-blue-600 hover:text-white rounded text-[11px] font-bold transition-all" title="Proses Pembayaran">
                                Bayar
                            </button>

                            <form action="{{ route('tagihan.ingatkan', $plg->id) }}" method="POST" class="inline" 
                                  x-data="{ isSending: false }" 
                                  @submit="if(confirm('Kirim WA otomatis ke {{ $plg->nama_pelanggan }}?')) { isSending = true; return true; } else { return false; }">
                                @csrf
                                <button type="submit" x-bind:disabled="isSending" class="px-2.5 py-1 bg-green-50 text-green-700 border border-green-100 hover:bg-green-500 hover:text-white rounded text-[11px] font-bold transition-all flex items-center gap-1.5 disabled:opacity-70 disabled:cursor-not-allowed group">
                                    <span x-show="!isSending" class="flex items-center gap-1.5">
                                        <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.405-.883-.733-1.48-1.638-1.653-1.935-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51a12.8 12.8 0 00-.57-.01c-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                                        Ingatkan
                                    </span>
                                    <span x-show="isSending" x-cloak class="flex items-center gap-1.5">
                                        <svg class="animate-spin w-3.5 h-3.5 text-green-600 group-hover:text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                        Mengirim...
                                    </span>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-12 text-center">
                        <div class="flex flex-col items-center justify-center">
                            <div class="w-12 h-12 bg-green-50 text-green-500 rounded-full flex items-center justify-center mb-3">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            </div>
                            <p class="text-sm text-gray-500 font-bold">Semua tagihan bulan ini sudah lunas!</p>
                            <p class="text-xs text-gray-400 mt-1">Bagus, tidak ada pelanggan yang menunggak.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- MODAL BAYAR SATUAN --}}
    <div x-show="openConfirm" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm p-4 transition-all">
        <div class="bg-white w-full max-w-lg rounded-2xl shadow-2xl p-8 text-center" @click.away="openConfirm = false">
            <div class="w-16 h-16 bg-blue-50 text-blue-500 rounded-full flex items-center justify-center mx-auto mb-5">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
            </div>
            <h4 class="text-xl font-bold text-gray-900 mb-2">Proses Pembayaran</h4>
            <p class="text-sm text-gray-500 leading-relaxed mb-6 px-4" x-text="confirmText"></p>
            
            <form :action="confirmUrl" method="POST" @submit="isSubmitting = true">
                @csrf @method('PUT')
                
                {{-- BARIS 1: DURASI & DISKON --}}
                <div class="grid grid-cols-2 gap-3 mb-3 text-left">
                    <div>
                        <label class="block text-[10px] font-bold text-gray-700 uppercase mb-2">Durasi (Bulan)</label>
                        <select name="jumlah_bulan" x-model.number="jumlahBulan" class="w-full text-sm p-3 bg-gray-50 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none font-bold text-gray-800 cursor-pointer transition-all">
                            <option value="1">1 Bulan</option>
                            <option value="2">2 Bulan</option>
                            <option value="3">3 Bulan</option>
                            <option value="6">6 Bulan</option>
                            <option value="12">12 Bulan</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-[10px] font-bold text-gray-700 uppercase mb-2">Diskon (%)</label>
                        <div class="relative">
                            <input type="number" name="diskon" x-model.number="diskonPersen" min="0" max="100" class="w-full text-sm p-3 pr-8 bg-gray-50 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none font-bold text-gray-800 transition-all">
                            <span class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-500 font-bold">%</span>
                        </div>
                    </div>
                </div>

                {{-- BARIS 2: BIAYA LAIN --}}
                <div class="mb-5 text-left">
                    <label class="block text-[10px] font-bold text-gray-700 uppercase mb-2">Biaya Lain (Opsional)</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-500 font-bold">Rp</span>
                        <input type="number" name="biaya_lain" x-model.number="biayaLain" min="0" placeholder="0" class="w-full text-sm p-3 pl-10 bg-gray-50 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none font-bold text-gray-800 transition-all">
                    </div>
                </div>

                {{-- DISPLAY TOTAL BAYAR LIVE --}}
                <div class="mb-6 p-4 bg-blue-50 border border-blue-100 rounded-xl flex items-center justify-between">
                    <div class="text-left">
                        <span class="block text-[10px] font-extrabold text-blue-600 uppercase tracking-widest mb-0.5">Total Tagihan</span>
                        <span class="text-xs font-medium text-blue-500" x-text="jumlahBulan + ' Bulan x Rp ' + (hargaPaket/1000) + 'k' + (biayaLain > 0 ? ' + Biaya Lain' : '')"></span>
                    </div>
                    <div class="text-xl font-black text-blue-700 tracking-tight" x-text="formatRupiah(totalTagihan)">
                        0
                    </div>
                </div>

                <div class="flex gap-3">
                    <button type="button" @click="openConfirm = false" class="flex-1 text-sm font-bold text-gray-600 p-3 hover:bg-gray-100 rounded-xl transition-all">Batal</button>
                    <button type="submit" :disabled="isSubmitting" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white text-sm font-bold rounded-xl p-3 shadow-lg transition-all flex justify-center items-center disabled:opacity-70 disabled:cursor-not-allowed">
                        <span x-show="!isSubmitting">Konfirmasi Lunas</span>
                        <span x-show="isSubmitting" x-cloak class="flex items-center gap-2">
                            <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- MODAL BAYAR MASSAL --}}
    <div x-show="openBulkConfirm" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm p-4 transition-all">
        <div class="bg-white w-full max-w-lg rounded-2xl shadow-2xl p-8 text-center" @click.away="openBulkConfirm = false">
            <div class="w-16 h-16 bg-blue-50 text-blue-500 rounded-full flex items-center justify-center mx-auto mb-5">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
            
            <h4 class="text-xl font-bold text-gray-900 mb-2">Bayar Massal</h4>
            <p class="text-sm text-gray-500 mb-6">
                Yakin ingin memproses <span class="text-blue-600 font-bold" x-text="selectedIds.length"></span> tagihan pelanggan sekaligus?
            </p>

            <form action="{{ route('tagihan.bulk') }}" method="POST" @submit="isSubmitting = true">
                @csrf
                <template x-for="id in selectedIds" :key="id">
                    <input type="hidden" name="ids[]" :value="id">
                </template>
                
                {{-- GRID UBAH JADI 3 KOLOM --}}
                <div class="grid grid-cols-3 gap-3 mb-5 text-left">
                    <div>
                        <label class="block text-[10px] font-bold text-gray-700 uppercase mb-2">Durasi (Bulan)</label>
                        <select name="jumlah_bulan" x-model.number="bulkJumlahBulan" class="w-full text-sm p-3 bg-gray-50 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none font-bold text-gray-800 cursor-pointer transition-all">
                            <option value="1">1 Bulan</option>
                            <option value="2">2 Bulan</option>
                            <option value="3">3 Bulan</option>
                            <option value="6">6 Bulan</option>
                            <option value="12">12 Bulan</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-[10px] font-bold text-gray-700 uppercase mb-2">Diskon (%)</label>
                        <div class="relative">
                            <input type="number" name="diskon" x-model.number="bulkDiskonPersen" min="0" max="100" class="w-full text-sm p-3 pr-8 bg-gray-50 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none font-bold text-gray-800 transition-all">
                            <span class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-500 font-bold">%</span>
                        </div>
                    </div>
                    
                    <div>
                        <label class="block text-[10px] font-bold text-gray-700 uppercase mb-2" title="Dikalikan jumlah pelanggan yang dipilih">Biaya Lain/Trx</label>
                        <input type="number" name="biaya_lain" x-model.number="bulkBiayaLain" min="0" placeholder="0" class="w-full text-sm p-3 bg-gray-50 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none font-bold text-gray-800 transition-all">
                    </div>
                </div>

                {{-- DISPLAY TOTAL BAYAR MASSAL LIVE --}}
                <div class="mb-6 p-4 bg-blue-50 border border-blue-100 rounded-xl flex items-center justify-between">
                    <div class="text-left">
                        <span class="block text-[10px] font-extrabold text-blue-600 uppercase tracking-widest mb-0.5">Total Penerimaan</span>
                        <span class="text-xs font-medium text-blue-500" x-text="selectedIds.length + ' Pelanggan' + (bulkBiayaLain > 0 ? ' + Biaya Lain' : '')"></span>
                    </div>
                    <div class="text-xl font-black text-blue-700 tracking-tight" x-text="formatRupiah(totalBulkTagihan)">
                        0
                    </div>
                </div>

                <div class="flex gap-3">
                    <button type="button" @click="openBulkConfirm = false" class="flex-1 text-sm font-bold text-gray-600 p-3 hover:bg-gray-100 rounded-xl transition-all">Batalkan</button>
                    <button type="submit" :disabled="isSubmitting" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white text-sm font-bold rounded-xl p-3 shadow-lg shadow-blue-100 transition-all flex justify-center items-center disabled:opacity-70 disabled:cursor-not-allowed">
                        <span x-show="!isSubmitting">Proses Semua</span>
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