@extends('layouts.app')

@section('title', 'Approval Pelanggan')

@section('content')
<div x-data="{ 
    openFilter: false,
    selectedIds: [], 
    allIds: {{ $pelanggans->pluck('id')->toJson() }},
    
    openConfirm: false, 
    confirmUrl: '', 
    confirmAction: '', // 'approve' atau 'reject'
    confirmText: '',
    
    openBulkConfirm: false,
    bulkActionType: '', // 'approve' atau 'reject'
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
    }
}">
    <div class="flex justify-between items-end mb-6 px-1">
        <div>
            <h3 class="text-2xl font-bold text-gray-900 tracking-tight">Approval Pelanggan</h3>
            <p class="text-sm text-gray-500 font-medium mt-1">Daftar pelanggan baru yang menunggu persetujuan pemasangan</p>
        </div>
        
        <div x-show="selectedIds.length > 0" x-cloak class="flex items-center gap-2 bg-gray-900 p-1.5 rounded-lg shadow-lg" x-transition>
            <span class="text-xs text-white font-bold px-3" x-text="selectedIds.length + ' Dipilih'"></span>
            <button @click="openBulkConfirm = true; bulkActionType = 'approve'" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-md text-xs font-bold transition-all flex items-center gap-1.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                Approve Semua
            </button>
            <button @click="openBulkConfirm = true; bulkActionType = 'reject'" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-md text-xs font-bold transition-all flex items-center gap-1.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12"/></svg>
                Tolak Semua
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

    {{-- FLOATING TOAST ERROR VALIDASI (SMOOTH ANIMATION + LIST) --}}
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
                        <p class="text-sm font-bold text-gray-800 tracking-tight mb-1">Gagal Memproses Data:</p>
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
        <form action="{{ route('approval.index') }}" method="GET" class="flex-1 flex items-center m-0 border-b md:border-b-0 border-gray-50 group" id="searchForm">
            <input type="hidden" name="start_date" value="{{ request('start_date') }}">
            <input type="hidden" name="end_date" value="{{ request('end_date') }}">
            
            <div class="pl-5 pr-2 text-gray-400 group-focus-within:text-blue-500 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            </div>
            <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari nama atau alamat pelanggan..." 
                   class="w-full py-4 bg-transparent border-none focus:ring-0 outline-none text-sm font-medium text-gray-700 placeholder-gray-400" 
                   onkeydown="if(event.key === 'Enter') this.form.submit();">
        </form>

        {{-- Kumpulan Tombol Kanan --}}
        <div class="flex items-center justify-end gap-2 p-2 px-3 shrink-0 bg-gray-50/30 md:bg-transparent">
            
            {{-- Tombol Reset Selalu Muncul --}}
            <a href="{{ route('approval.index') }}" class="text-[11px] font-black text-gray-400 hover:text-red-500 px-3 py-2 transition-colors tracking-widest uppercase">Reset</a>
            
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
                    
                    <h4 class="text-sm font-black text-gray-900 tracking-tight mb-4">Pilih Rentang Tanggal</h4>
                    
                    <form action="{{ route('approval.index') }}" method="GET">
                        <input type="hidden" name="q" value="{{ request('q') }}">
                        
                        <div class="space-y-4">
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

    {{-- TABEL DIPERBARUI --}}
    <div class="relative overflow-x-auto bg-white shadow-sm rounded-2xl border border-gray-100">
        <table class="w-full text-left text-gray-600 border-collapse">
            <thead class="text-[11px] text-gray-400 bg-gray-50/50 border-b border-gray-100 uppercase tracking-widest font-black">
                <tr>
                    <th class="px-4 py-4 w-12 text-center">
                        <input type="checkbox" :checked="isAllSelected" @change="toggleAll" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded cursor-pointer focus:ring-blue-500">
                    </th>
                    <th class="px-6 py-4">Nama Lengkap</th>
                    <th class="px-6 py-4">Paket Internet</th>
                    <th class="px-6 py-4">Alamat</th>
                    <th class="px-6 py-4 whitespace-nowrap">No WA</th>
                    <th class="px-6 py-4 text-center whitespace-nowrap">Tanggal Daftar</th>
                    <th class="px-6 py-4 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($pelanggans as $plg)
                <tr class="hover:bg-blue-50/30 transition-colors group" :class="selectedIds.includes('{{ $plg->id }}') ? 'bg-blue-50/40' : ''">
                    <td class="px-4 py-4 text-center">
                        <input type="checkbox" value="{{ $plg->id }}" x-model="selectedIds" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded cursor-pointer focus:ring-blue-500">
                    </td>
                    
                    <td class="px-6 py-4 text-sm font-bold text-gray-900 whitespace-nowrap">
                        {{ $plg->nama_pelanggan }}
                    </td>
                    
                    <td class="px-6 py-4">
                        <span class="inline-flex items-center px-2.5 py-1 rounded-md text-[10px] font-black uppercase tracking-wider bg-blue-50 text-blue-600 border border-blue-100 whitespace-nowrap">
                            {{ $plg->paket->nama_paket ?? 'Tanpa Paket' }}
                        </span>
                    </td>
                    
                    <td class="px-6 py-4 text-sm text-gray-600 font-medium truncate max-w-[200px]" title="{{ $plg->alamat }}">
                        {{ $plg->alamat }}
                    </td>
                    
                    <td class="px-6 py-4 text-sm text-gray-600 font-medium whitespace-nowrap">
                        {{ $plg->no_wa }}
                    </td>
                    
                    <td class="px-6 py-4 text-center text-sm text-gray-600 font-bold whitespace-nowrap">
                        {{ $plg->created_at->format('d/m/Y') }}
                    </td>
                    
                    <td class="px-6 py-4 text-right">
                        <div class="flex justify-end gap-2">
                            <button @click="openConfirm = true; confirmUrl = '{{ route('approval.action', $plg->id) }}'; confirmAction = 'approve'; confirmText = 'Setujui pemasangan internet untuk {{ $plg->nama_pelanggan }}?'" 
                                    class="px-2.5 py-1 bg-green-50 text-green-700 border border-green-100 hover:bg-green-500 hover:text-white rounded text-[12px] font-bold transition-all" title="Setujui">
                                Approve
                            </button>
                            <button @click="openConfirm = true; confirmUrl = '{{ route('approval.action', $plg->id) }}'; confirmAction = 'reject'; confirmText = 'Tolak pendaftaran {{ $plg->nama_pelanggan }}?'" 
                                    class="px-2.5 py-1 bg-red-50 text-red-700 border border-red-100 hover:bg-red-500 hover:text-white rounded text-[12px] font-bold transition-all" title="Tolak">
                                Reject
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-6 py-12 text-center">
                        <div class="flex flex-col items-center justify-center">
                            <svg class="w-12 h-12 text-green-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            <p class="text-sm text-gray-500 font-bold">Semua pendaftaran sudah diproses!</p>
                            <p class="text-xs text-gray-400 mt-1">Tidak ada antrean approval saat ini.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div x-show="openConfirm" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm p-4 transition-all">
        <div class="bg-white w-full max-w-md rounded-2xl shadow-2xl p-8 text-center" @click.away="openConfirm = false">
            <div class="w-16 h-16 mx-auto mb-5 rounded-full flex items-center justify-center"
                 :class="confirmAction === 'approve' ? 'bg-green-50 text-green-500' : 'bg-red-50 text-red-500'">
                <svg x-show="confirmAction === 'approve'" class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                <svg x-show="confirmAction === 'reject'" class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
            </div>

            <h4 class="text-xl font-bold text-gray-900 mb-2">Konfirmasi Tindakan</h4>
            <p class="text-sm text-gray-500 leading-relaxed mb-8 px-4" x-text="confirmText"></p>
            
            <form :action="confirmUrl" method="POST" @submit="isSubmitting = true">
                @csrf @method('PUT')
                <input type="hidden" name="action" :value="confirmAction">
                
                <div class="flex gap-3">
                    <button type="button" @click="openConfirm = false" class="flex-1 text-sm font-bold text-gray-600 p-3 hover:bg-gray-100 rounded-xl transition-all">Batalkan</button>
                    <button type="submit" :disabled="isSubmitting" 
                            :class="confirmAction === 'approve' ? 'bg-green-600 hover:bg-green-700 shadow-green-100' : 'bg-red-600 hover:bg-red-700 shadow-red-100'"
                            class="flex-1 text-white text-sm font-bold rounded-xl p-3 shadow-lg transition-all flex justify-center items-center disabled:opacity-70 disabled:cursor-not-allowed">
                        <span x-show="!isSubmitting">Ya, Lanjutkan</span>
                        <span x-show="isSubmitting" x-cloak class="flex items-center gap-2">
                            <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div x-show="openBulkConfirm" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm p-4 transition-all">
        <div class="bg-white w-full max-w-md rounded-2xl shadow-2xl p-8 text-center" @click.away="openBulkConfirm = false">
            <div class="w-16 h-16 mx-auto mb-5 rounded-full flex items-center justify-center" 
                 :class="bulkActionType === 'approve' ? 'bg-green-50 text-green-500' : 'bg-red-50 text-red-500'">
                <svg x-show="bulkActionType === 'approve'" class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                <svg x-show="bulkActionType === 'reject'" class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
            </div>
            
            <h4 class="text-xl font-bold text-gray-900 mb-2">Konfirmasi Massal</h4>
            <p class="text-sm text-gray-500 mb-6">
                Yakin ingin <strong :class="bulkActionType === 'approve' ? 'text-green-600' : 'text-red-600'" x-text="bulkActionType === 'approve' ? 'Menyetujui' : 'Menolak'"></strong> 
                <span class="text-gray-900 font-bold" x-text="selectedIds.length"></span> pendaftaran sekaligus?
            </p>

            <form action="{{ route('approval.bulk') }}" method="POST" @submit="isSubmitting = true">
                @csrf
                <input type="hidden" name="action" :value="bulkActionType">
                
                <template x-for="id in selectedIds" :key="id">
                    <input type="hidden" name="ids[]" :value="id">
                </template>
                
                <div class="flex gap-3">
                    <button type="button" @click="openBulkConfirm = false" class="flex-1 text-sm font-bold text-gray-600 p-3 hover:bg-gray-100 rounded-xl transition-all">Batalkan</button>
                    <button type="submit" :disabled="isSubmitting" 
                            :class="bulkActionType === 'approve' ? 'bg-green-600 hover:bg-green-700 shadow-green-100' : 'bg-red-600 hover:bg-red-700 shadow-red-100'"
                            class="flex-1 text-white text-sm font-bold rounded-xl p-3 shadow-lg transition-all flex justify-center items-center disabled:opacity-70 disabled:cursor-not-allowed">
                        <span x-show="!isSubmitting">Ya, Lanjutkan</span>
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