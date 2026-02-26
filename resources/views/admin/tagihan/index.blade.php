@extends('layouts.app')

@section('title', 'Tagihan Pelanggan')

@section('content')
<div x-data="{ 
    selectedIds: [], 
    allIds: {{ $pelanggans->pluck('id')->toJson() }},
    
    openConfirm: false, 
    confirmUrl: '', 
    confirmText: '',
    
    openBulkConfirm: false,
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
            <h3 class="text-2xl font-bold text-gray-900 tracking-tight">Daftar Tagihan</h3>
            <p class="text-sm text-gray-500 font-medium mt-1">Pelanggan aktif yang belum melakukan pembayaran bulan ini</p>
        </div>
        
        <div x-show="selectedIds.length > 0" x-cloak class="flex items-center gap-2 bg-gray-900 p-1.5 rounded-lg shadow-lg" x-transition>
            <span class="text-xs text-white font-bold px-3" x-text="selectedIds.length + ' Dipilih'"></span>
            <button @click="openBulkConfirm = true" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md text-xs font-bold transition-all flex items-center gap-1.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                Tandai Lunas Semua
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
                            Tgl {{ $plg->jatuh_tempo }}
                        </span>
                    </td>
                    
                    <td class="px-6 py-4 text-right">
                        <div class="flex justify-end gap-2">
                            <button @click="openConfirm = true; confirmUrl = '{{ route('tagihan.action', $plg->id) }}'; confirmText = 'Tandai tagihan {{ $plg->nama_pelanggan }} bulan ini sebagai Lunas?'" 
                                    class="px-2.5 py-1 bg-blue-50 text-blue-700 border border-blue-100 hover:bg-blue-600 hover:text-white rounded text-[11px] font-bold transition-all" title="Tandai Lunas">
                                Lunas
                            </button>

                            @php
                                $no_wa_format = preg_replace('/^0/', '62', $plg->no_wa);
                                $harga = number_format($plg->paket->harga ?? 0, 0, ',', '.');
                                $paket = $plg->paket->nama_paket ?? 'Internet';
                                $pesan = "Halo kak *{$plg->nama_pelanggan}*, ini adalah pengingat tagihan internet CSMNET untuk *{$paket}* sebesar *Rp {$harga}* yang jatuh tempo pada tanggal *{$plg->jatuh_tempo}*. Mohon segera melakukan pembayaran. Terima kasih \u{1F64F}";
                            @endphp
                            <a href="https://wa.me/{{ $no_wa_format }}?text={{ urlencode($pesan) }}" target="_blank" 
                               class="px-2.5 py-1 bg-green-50 text-green-700 border border-green-100 hover:bg-green-500 hover:text-white rounded text-[11px] font-bold transition-all flex items-center gap-1.5" title="Kirim WA Pengingat">
                                <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.405-.883-.733-1.48-1.638-1.653-1.935-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51a12.8 12.8 0 00-.57-.01c-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                                Ingatkan
                            </a>
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

    <div x-show="openConfirm" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm p-4 transition-all">
        <div class="bg-white w-full max-w-md rounded-2xl shadow-2xl p-8 text-center" @click.away="openConfirm = false">
            <div class="w-16 h-16 bg-blue-50 text-blue-500 rounded-full flex items-center justify-center mx-auto mb-5">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
            </div>
            <h4 class="text-xl font-bold text-gray-900 mb-2">Konfirmasi Tagihan Lunas</h4>
            <p class="text-sm text-gray-500 leading-relaxed mb-8 px-4" x-text="confirmText"></p>
            
            <form :action="confirmUrl" method="POST" @submit="isSubmitting = true">
                @csrf @method('PUT')
                <div class="flex gap-3">
                    <button type="button" @click="openConfirm = false" class="flex-1 text-sm font-bold text-gray-600 p-3 hover:bg-gray-100 rounded-xl transition-all">Batal</button>
                    <button type="submit" :disabled="isSubmitting" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white text-sm font-bold rounded-xl p-3 shadow-lg transition-all flex justify-center items-center disabled:opacity-70 disabled:cursor-not-allowed">
                        <span x-show="!isSubmitting">Ya, Lunas</span>
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
            <div class="w-16 h-16 bg-blue-50 text-blue-500 rounded-full flex items-center justify-center mx-auto mb-5">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
            
            <h4 class="text-xl font-bold text-gray-900 mb-2">Tandai Lunas Massal</h4>
            <p class="text-sm text-gray-500 mb-6">
                Yakin ingin menandai <span class="text-blue-600 font-bold" x-text="selectedIds.length"></span> tagihan pelanggan sebagai Lunas?
            </p>

            <form action="{{ route('tagihan.bulk') }}" method="POST" @submit="isSubmitting = true">
                @csrf
                <template x-for="id in selectedIds" :key="id">
                    <input type="hidden" name="ids[]" :value="id">
                </template>
                
                <div class="flex gap-3">
                    <button type="button" @click="openBulkConfirm = false" class="flex-1 text-sm font-bold text-gray-600 p-3 hover:bg-gray-100 rounded-xl transition-all">Batalkan</button>
                    <button type="submit" :disabled="isSubmitting" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white text-sm font-bold rounded-xl p-3 shadow-lg shadow-blue-100 transition-all flex justify-center items-center disabled:opacity-70 disabled:cursor-not-allowed">
                        <span x-show="!isSubmitting">Ya, Proses Semua</span>
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