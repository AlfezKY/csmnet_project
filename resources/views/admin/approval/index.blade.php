@extends('layouts.app')

@section('title', 'Approval Pelanggan')

@section('content')
<div x-data="{ 
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

    @if(session('success'))
        <div class="mb-6 p-4 bg-green-50 border border-green-100 text-green-700 text-sm font-bold rounded-lg flex items-center gap-3">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            {{ session('success') }}
        </div>
    @endif

    <div class="relative overflow-x-auto bg-white shadow-sm rounded-lg border border-gray-200">
        <table class="w-full text-left text-gray-600 border-collapse">
            <thead class="text-xs text-gray-400 bg-gray-50/50 border-b border-gray-200 uppercase tracking-widest font-bold">
                <tr>
                    <th class="px-4 py-4 w-12 text-center">
                        <input type="checkbox" :checked="isAllSelected" @change="toggleAll" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded cursor-pointer focus:ring-blue-500">
                    </th>
                    <th class="px-6 py-4">Nama Lengkap</th>
                    <th class="px-6 py-4">Paket Internet</th>
                    <th class="px-6 py-4">Alamat</th>
                    <th class="px-6 py-4">No WA</th>
                    <th class="px-6 py-4 text-center">Tanggal Daftar</th>
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
                        <span class="text-sm font-bold text-blue-600">
                            {{ $plg->paket->nama_paket ?? 'Tanpa Paket' }}
                        </span>
                    </td>
                    
                    <td class="px-6 py-4 text-sm text-gray-500 font-medium truncate max-w-[150px]" title="{{ $plg->alamat }}">{{ $plg->alamat }}</td>
                    <td class="px-6 py-4 text-sm text-gray-500 font-medium">{{ $plg->no_wa }}</td>
                    <td class="px-6 py-4 text-center">
                        <span class="text-[12px] text-gray-500 font-bold">{{ $plg->created_at->format('d/m/y') }}</span>
                    </td>
                    <td class="px-6 py-4 text-right">
                        <div class="flex justify-end gap-2">
                            <button @click="openConfirm = true; confirmUrl = '{{ route('approval.action', $plg->id) }}'; confirmAction = 'approve'; confirmText = 'Setujui pemasangan internet untuk {{ $plg->nama_pelanggan }}?'" 
                                    class="px-2.5 py-1 bg-green-50 text-green-700 border border-green-100 hover:bg-green-500 hover:text-white rounded text-[11px] font-bold transition-all" title="Setujui">
                                Approve
                            </button>
                            <button @click="openConfirm = true; confirmUrl = '{{ route('approval.action', $plg->id) }}'; confirmAction = 'reject'; confirmText = 'Tolak pendaftaran {{ $plg->nama_pelanggan }}?'" 
                                    class="px-2.5 py-1 bg-red-50 text-red-700 border border-red-100 hover:bg-red-500 hover:text-white rounded text-[11px] font-bold transition-all" title="Tolak">
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