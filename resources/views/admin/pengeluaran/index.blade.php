@extends('layouts.app')

@section('title', 'Catatan Pengeluaran')

@section('content')
@if ($errors->any())
    <div class="mb-6 p-4 bg-red-50 border border-red-100 text-red-700 text-sm font-bold rounded-lg">
        <p class="mb-1 uppercase">Gagal Menyimpan Data:</p>
        <ul class="list-disc list-inside text-xs font-medium">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

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
        <h3 class="text-2xl font-bold text-gray-900 tracking-tight">Catatan Pengeluaran</h3>
        <p class="text-sm text-gray-500 font-medium mt-1">Kelola data arus kas keluar operasional CSMNET</p>
    </div>
    
    <button @click="openAdd = true" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-lg text-sm font-bold transition-all shadow-sm flex items-center gap-2">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
        Catat Pengeluaran
    </button>
</div>

@if(session('success'))
    <div class="mb-6 p-4 bg-green-50 border border-green-100 text-green-700 text-sm font-bold rounded-lg flex items-center gap-3">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
        {{ session('success') }}
    </div>
@endif

{{-- BARIS PENCARIAN & FILTER (STYLE MODERN CLEAN) --}}
<div class="bg-white rounded-2xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-gray-100 mb-6 flex flex-col md:flex-row md:items-center justify-between overflow-visible transition-all hover:shadow-[0_8px_30px_rgb(0,0,0,0.08)]">
    
    {{-- Input Search Kiri --}}
    <form action="{{ route('pengeluaran.index') }}" method="GET" class="flex-1 flex items-center m-0 border-b md:border-b-0 border-gray-50 group" id="searchForm">
        <input type="hidden" name="kategori" value="{{ request('kategori') }}">
        <input type="hidden" name="start_date" value="{{ request('start_date') }}">
        <input type="hidden" name="end_date" value="{{ request('end_date') }}">
        
        <div class="pl-5 pr-2 text-gray-400 group-focus-within:text-blue-500 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
        </div>
        <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari kategori atau deskripsi..." 
               class="w-full py-4 bg-transparent border-none focus:ring-0 outline-none text-sm font-medium text-gray-700 placeholder-gray-400" 
               onkeydown="if(event.key === 'Enter') this.form.submit();">
    </form>

    {{-- Kumpulan Tombol Kanan --}}
    <div class="flex items-center justify-end gap-2 p-2 px-3 shrink-0 bg-gray-50/30 md:bg-transparent">
        <a href="{{ route('pengeluaran.index') }}" class="text-[11px] font-black text-gray-400 hover:text-red-500 px-3 py-2 transition-colors tracking-widest uppercase">Reset</a>
        
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
                
                <h4 class="text-sm font-black text-gray-900 tracking-tight mb-4">Filter Pengeluaran</h4>
                
                <form action="{{ route('pengeluaran.index') }}" method="GET">
                    <input type="hidden" name="q" value="{{ request('q') }}">
                    
                    <div class="space-y-4">
                        <div>
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5 ml-1">Kategori Pengeluaran</label>
                            <select name="kategori" class="w-full p-2.5 bg-gray-50 border border-gray-100 rounded-xl text-sm font-bold text-gray-700 outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-all cursor-pointer">
                                <option value="">Semua Kategori</option>
                                @foreach($kategoriList as $kat)
                                    <option value="{{ $kat }}" {{ request('kategori') == $kat ? 'selected' : '' }}>{{ $kat }}</option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div>
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5 ml-1">Dari Tanggal</label>
                            <input type="date" name="start_date" value="{{ request('start_date') }}" class="w-full p-2.5 bg-gray-50 border border-gray-100 rounded-xl text-sm font-bold text-gray-700 outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-all cursor-pointer">
                        </div>
                        
                        <div>
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5 ml-1">Sampai Tanggal</label>
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
                    <th class="px-6 py-4 w-32">Tanggal</th>
                    <th class="px-6 py-4">Kategori</th>
                    <th class="px-6 py-4 w-1/3">Deskripsi</th>
                    
                    {{-- Sembunyikan Header Jumlah (Rp) untuk Admin --}}
                    @if(auth()->user()->role == 'Owner')
                    <th class="px-6 py-4 text-right">Jumlah (Rp)</th>
                    @endif
                    
                    <th class="px-6 py-4 text-center">Bukti</th>
                    <th class="px-6 py-4 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($pengeluarans as $out)
                <tr class="hover:bg-gray-50/50 transition-colors group">
                    <td class="px-6 py-4 text-sm text-gray-900 font-bold whitespace-nowrap">
                        {{-- Format d/m/Y --}}
                        {{ \Carbon\Carbon::parse($out->tanggal)->format('d/m/Y') }}
                    </td>
                    <td class="px-6 py-4">
                        {{-- Style Chip buat Kategori --}}
                        <span class="inline-flex items-center px-2.5 py-1 rounded-md text-[11px] font-bold bg-blue-50 text-blue-600 border border-blue-100">                            {{ $out->kategori }}
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        <p class="text-sm text-gray-600 font-medium">{{ $out->deskripsi ?? '-' }}</p>
                    </td>
                    
                    {{-- Sembunyikan Isi Nominal Jumlah (Rp) untuk Admin --}}
                    @if(auth()->user()->role == 'Owner')
                    <td class="px-6 py-4 text-right">
                        <span class="text-sm font-bold text-gray-700">
                            Rp {{ number_format($out->jumlah, 0, ',', '.') }}
                        </span>
                    </td>
                    @endif
                    
                    <td class="px-6 py-4 text-center">
                        @if($out->bukti_bayar)
                            <a href="{{ asset($out->bukti_bayar) }}" target="_blank" class="inline-flex items-center gap-1 px-2 py-1 text-xs font-bold text-blue-600 bg-blue-50 hover:bg-blue-100 rounded transition-colors" title="Lihat Lampiran">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path></svg>
                                Lihat
                            </a>
                        @else
                            <span class="text-xs text-gray-400 font-medium">-</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-right">
                        <div class="flex justify-end gap-2">
                            <button @click="openEdit = true; editData = {{ json_encode($out) }}" class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-all" title="Edit Data">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                            </button>
                            <button @click="openDelete = true; deleteUrl = '{{ route('pengeluaran.destroy', $out->id) }}'" class="p-2 text-red-500 hover:bg-red-50 rounded-lg transition-all" title="Hapus Data">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    {{-- Sesuaikan colspan agar tabel tetap rapi saat kolom disembunyikan --}}
                    <td colspan="{{ auth()->user()->role == 'Owner' ? '6' : '5' }}" class="px-6 py-12 text-center">
                        <div class="flex flex-col items-center justify-center">
                            <div class="w-12 h-12 bg-gray-50 text-gray-400 rounded-full flex items-center justify-center mb-3">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </div>
                            <p class="text-sm text-gray-500 font-bold">Belum ada catatan pengeluaran.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- MODAL TAMBAH PENGELUARAN --}}
    <div x-show="openAdd" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm p-4 transition-all">
        <div class="bg-white w-full max-w-lg rounded-xl shadow-2xl p-8" @click.away="openAdd = false">
            <h4 class="text-xl font-bold text-gray-900 mb-6">Catat Pengeluaran Baru</h4>
            
            <form action="{{ route('pengeluaran.store') }}" method="POST" enctype="multipart/form-data" x-data="{ isSubmitting: false }" @submit="isSubmitting = true"> 
                @csrf
                <div class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Tanggal</label>
                            <input type="date" name="tanggal" value="{{ date('Y-m-d') }}" class="w-full text-sm p-3 bg-gray-50 border border-gray-100 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none font-bold text-gray-700" required>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Kategori</label>
                            <div class="relative">
                                <select name="kategori" class="w-full text-sm p-3 pr-10 bg-gray-50 border border-gray-100 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none font-bold text-gray-700 appearance-none cursor-pointer" required>
                                    <option value="" disabled selected>Pilih Kategori...</option>
                                    <option value="Langganan ISP Induk">Langganan ISP Induk</option>
                                    <option value="Pembelian Perangkat (Router, Modem, Kabel)">Pembelian Perangkat (Router, Modem, Kabel)</option>
                                    <option value="Perawatan Jaringan">Perawatan Jaringan</option>
                                    <option value="Gaji Karyawan / Teknisi">Gaji Karyawan / Teknisi</option>
                                    <option value="Biaya Operasional Kantor">Biaya Operasional Kantor</option>
                                    <option value="Listrik">Listrik</option>
                                    <option value="Internet Kantor">Internet Kantor</option>
                                    <option value="Transportasi">Transportasi</option>
                                    <option value="Lainnya">Lainnya</option>
                                </select>
                                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-gray-400">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Total Pengeluaran (Rp)</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-500 font-bold text-sm">Rp</span>
                            <input type="number" name="jumlah" min="0" placeholder="50000" class="w-full text-sm p-3 pl-10 bg-gray-50 border border-gray-100 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none font-bold text-gray-700" required>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Bukti Bayar (Opsional)</label>
                        <input type="file" name="bukti_bayar" accept=".pdf,image/jpeg,image/png,image/jpg" class="w-full text-sm p-2 bg-gray-50 border border-gray-100 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none font-medium text-gray-700">
                        <p class="text-[10px] text-gray-500 mt-1">*Format: JPG, PNG, PDF. Maks 2MB.</p>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Deskripsi / Keterangan</label>
                        <textarea name="deskripsi" rows="3" placeholder="Contoh: Pembayaran tagihan Indihome bulan Maret..." class="w-full text-sm p-3 bg-gray-50 border border-gray-100 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none font-medium"></textarea>
                    </div>
                </div>

                <div class="flex gap-3 pt-6 mt-6 border-t border-slate-100">
                    <button type="button" @click="openAdd = false" class="flex-1 text-sm font-bold text-gray-500 p-3 hover:bg-gray-100 rounded-lg transition-all">Batal</button>
                    <button type="submit" x-bind:disabled="isSubmitting" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white text-sm font-bold rounded-lg p-3 shadow-lg shadow-blue-100 transition-all flex justify-center items-center disabled:opacity-70 disabled:cursor-not-allowed">
                        <span x-show="!isSubmitting">Simpan Pengeluaran</span>
                        <span x-show="isSubmitting" x-cloak class="flex items-center gap-2">
                            <svg class="animate-spin h-4 w-4 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- MODAL EDIT PENGELUARAN --}}
    <div x-show="openEdit" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm p-4 transition-all">
        <div class="bg-white w-full max-w-lg rounded-xl shadow-2xl p-8" @click.away="openEdit = false">
            <h4 class="text-xl font-bold text-gray-900 mb-6">Edit Data Pengeluaran</h4>
            
            <form :action="'/pengeluaran/' + editData.id" method="POST" enctype="multipart/form-data" x-data="{ isSubmitting: false }" @submit="isSubmitting = true"> 
                @csrf @method('PUT')
                <div class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Tanggal</label>
                            <input type="date" name="tanggal" x-model="editData.tanggal" class="w-full text-sm p-3 bg-gray-50 border border-gray-100 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none font-bold text-gray-700" required>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Kategori</label>
                            <div class="relative">
                                <select name="kategori" x-model="editData.kategori" class="w-full text-sm p-3 pr-10 bg-gray-50 border border-gray-100 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none font-bold text-gray-700 appearance-none cursor-pointer" required>
                                    <option value="" disabled>Pilih Kategori...</option>
                                    <option value="Langganan ISP Induk">Langganan ISP Induk</option>
                                    <option value="Pembelian Perangkat (Router, Modem, Kabel)">Pembelian Perangkat (Router, Modem, Kabel)</option>
                                    <option value="Perawatan Jaringan">Perawatan Jaringan</option>
                                    <option value="Gaji Karyawan / Teknisi">Gaji Karyawan / Teknisi</option>
                                    <option value="Biaya Operasional Kantor">Biaya Operasional Kantor</option>
                                    <option value="Listrik">Listrik</option>
                                    <option value="Internet Kantor">Internet Kantor</option>
                                    <option value="Transportasi">Transportasi</option>
                                    <option value="Lainnya">Lainnya</option>
                                    <template x-if="editData.kategori && !['Langganan ISP Induk', 'Pembelian Perangkat (Router, Modem, Kabel)', 'Perawatan Jaringan', 'Gaji Karyawan / Teknisi', 'Biaya Operasional Kantor', 'Listrik', 'Internet Kantor', 'Transportasi', 'Lainnya'].includes(editData.kategori)">
                                        <option x-bind:value="editData.kategori" x-text="editData.kategori" selected></option>
                                    </template>
                                </select>
                                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-gray-400">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Total Pengeluaran (Rp)</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-500 font-bold text-sm">Rp</span>
                            <input type="number" name="jumlah" x-model="editData.jumlah" min="0" class="w-full text-sm p-3 pl-10 bg-gray-50 border border-gray-100 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none font-bold text-gray-700" required>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Update Bukti Bayar</label>
                        <input type="file" name="bukti_bayar" accept=".pdf,image/jpeg,image/png,image/jpg" class="w-full text-sm p-2 bg-gray-50 border border-gray-100 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none font-medium text-gray-700">
                        <div class="mt-1 flex items-center justify-between">
                            <p class="text-[10px] text-gray-500">*Kosongkan jika tidak ingin mengubah file lama.</p>
                            <template x-if="editData.bukti_bayar">
                                <a :href="'/' + editData.bukti_bayar" target="_blank" class="text-[10px] font-bold text-blue-600 hover:underline">Lihat File Saat Ini</a>
                            </template>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Deskripsi / Keterangan</label>
                        <textarea name="deskripsi" x-model="editData.deskripsi" rows="3" class="w-full text-sm p-3 bg-gray-50 border border-gray-100 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none font-medium"></textarea>
                    </div>
                </div>

                <div class="flex gap-3 pt-6 mt-6 border-t border-slate-100">
                    <button type="button" @click="openEdit = false" class="flex-1 text-sm font-bold text-gray-500 p-3 hover:bg-gray-100 rounded-lg transition-all">Batal</button>
                    <button type="submit" x-bind:disabled="isSubmitting" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white text-sm font-bold rounded-lg p-3 shadow-lg shadow-blue-100 transition-all flex justify-center items-center disabled:opacity-70 disabled:cursor-not-allowed">
                        <span x-show="!isSubmitting">Simpan Update</span>
                        <span x-show="isSubmitting" x-cloak class="flex items-center gap-2">
                            <svg class="animate-spin h-4 w-4 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- MODAL DELETE --}}
    <div x-show="openDelete" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm p-4 transition-all">
        <div class="bg-white w-full max-w-md rounded-2xl shadow-2xl p-8 text-center" @click.away="openDelete = false">
            <div class="w-16 h-16 bg-red-50 text-red-500 rounded-full flex items-center justify-center mx-auto mb-5">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
            </div>
            <h4 class="text-xl font-bold text-gray-900 mb-2">Hapus Pengeluaran?</h4>
            <p class="text-sm text-gray-500 leading-relaxed mb-8 px-4">Data yang dihapus tidak dapat dikembalikan lagi. Yakin ingin menghapus?</p>
            <form :action="deleteUrl" method="POST" x-data="{ isSubmitting: false }" @submit="isSubmitting = true">
                @csrf @method('DELETE')
                <div class="flex gap-3">
                    <button type="button" @click="openDelete = false" class="flex-1 text-sm font-bold text-gray-600 p-3 hover:bg-gray-100 rounded-xl transition-all">Batalkan</button>
                    <button type="submit" x-bind:disabled="isSubmitting" class="flex-1 bg-red-600 hover:bg-red-700 text-white text-sm font-bold rounded-xl p-3 shadow-lg shadow-red-100 transition-all flex justify-center items-center disabled:opacity-70 disabled:cursor-not-allowed">
                        <span x-show="!isSubmitting">Ya, Hapus</span>
                        <span x-show="isSubmitting" x-cloak class="flex items-center gap-2">
                            <svg class="animate-spin h-4 w-4 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection