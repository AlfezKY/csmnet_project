@extends('layouts.app')

@section('title', 'Data Paket Internet')

@section('content')
<div x-data="{ 
    openAdd: {{ $errors->any() ? 'true' : 'false' }}, 
    openEdit: false, 
    openDelete: false, 
    editData: {}, 
    deleteUrl: '' 
}">
    <div class="flex justify-between items-end mb-6 px-1">
        <div>
            <h3 class="text-2xl font-bold text-gray-900 tracking-tight">Master Paket</h3>
            <p class="text-sm text-gray-500 font-medium mt-1">Kelola daftar layanan paket internet dan harganya</p>
        </div>
        <button @click="openAdd = true" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-lg text-sm font-bold transition-all shadow-sm flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
            Tambah Paket
        </button>
    </div>

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
                    <th class="px-6 py-4">Nama Paket</th>
                    <th class="px-6 py-4">Kecepatan</th>
                    <th class="px-6 py-4">Harga / Bulan</th>
                    <th class="px-6 py-4 text-center">Status</th>
                    <th class="px-6 py-4 text-center">Update Terakhir</th>
                    <th class="px-6 py-4 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($pakets as $paket)
                <tr class="hover:bg-gray-50/50 transition-colors group">
                    <td class="px-6 py-4">
                        <span class="text-sm font-bold text-gray-900">{{ $paket->nama_paket }}</span>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-500 font-bold">{{ $paket->kecepatan }} Mbps</td>
                    <td class="px-6 py-4 text-sm text-blue-600 font-bold">Rp {{ number_format($paket->harga, 0, ',', '.') }}</td>
                    <td class="px-6 py-4 text-center">
                        <span class="px-3 py-1 rounded-md text-xs font-bold uppercase border 
                            {{ $paket->status == 'Active' ? 'bg-green-50 text-green-700 border-green-100' : ($paket->status == 'Pending' ? 'bg-orange-50 text-orange-600 border-orange-100' : 'bg-gray-50 text-gray-600 border-gray-200') }}">
                            {{ $paket->status }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-center">
                        <div class="flex flex-col">
                            <span class="text-[11px] text-gray-500 font-bold">{{ $paket->updated_at ? $paket->updated_at->format('d M Y') : $paket->created_at->format('d M Y') }}</span>
                            <span class="text-[10px] text-gray-400 uppercase font-medium">{{ $paket->updated_by ?? $paket->created_by }}</span>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-right">
                        <div class="flex justify-end gap-2">
                            <button @click="openEdit = true; editData = {{ json_encode($paket) }}" class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-all" title="Edit Data">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                            </button>
                            <button @click="openDelete = true; deleteUrl = '{{ route('paket.destroy', $paket->id) }}'" class="p-2 text-red-500 hover:bg-red-50 rounded-lg transition-all" title="Hapus Paket">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-8 text-center text-sm text-gray-400 font-medium">Belum ada data paket internet.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div x-show="openAdd" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm p-4 transition-all">
        <div class="bg-white w-full max-w-xl rounded-xl shadow-2xl p-8 max-h-[90vh] overflow-y-auto" @click.away="openAdd = false">
            <h4 class="text-xl font-bold text-gray-900 mb-1">Tambah Paket Baru</h4>
            <p class="text-sm text-gray-500 mb-6">Buat paket internet baru yang akan ditawarkan ke pelanggan</p>
            
            <form action="{{ route('paket.store') }}" method="POST" class="space-y-4" x-data="{ isSubmitting: false }" @submit="isSubmitting = true"> 
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Nama Paket</label>
                        <input type="text" name="nama_paket" placeholder="Cth: Paket Hemat 10Mbps" class="w-full text-sm p-3 bg-gray-50 border border-gray-100 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none font-medium" required>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Kecepatan</label>
                        <input type="text" name="kecepatan" placeholder="Cth: 10 Mbps" class="w-full text-sm p-3 bg-gray-50 border border-gray-100 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none font-medium" required>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Harga (Rp) / Bulan</label>
                        <input type="number" name="harga" placeholder="Cth: 150000" class="w-full text-sm p-3 bg-gray-50 border border-gray-100 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none font-medium" required>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Status Penjualan</label>
                        <select name="status" class="w-full text-sm p-3 bg-gray-50 border border-gray-100 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none font-bold text-gray-600" required>
                            <option value="Active">Active</option>
                            <option value="Non Active">Non Active</option>
                            <option value="Pending">Pending (Draft)</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Deskripsi Paket (Opsional)</label>
                    <textarea name="deskripsi" rows="2" placeholder="Tuliskan info singkat mengenai paket ini..." class="w-full text-sm p-3 bg-gray-50 border border-gray-100 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none font-medium"></textarea>
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Keypoint / Kelebihan (Opsional)</label>
                    <textarea name="keypoint" rows="2" placeholder="Cth: Pemasangan Gratis, Modem Dipinjamkan" class="w-full text-sm p-3 bg-gray-50 border border-gray-100 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none font-medium"></textarea>
                </div>

                <div class="flex gap-3 pt-4 border-t border-gray-100">
                    <button type="button" @click="openAdd = false" class="flex-1 text-sm font-bold text-gray-500 p-3 hover:bg-gray-100 rounded-lg transition-all">Batal</button>
                    <button type="submit" x-bind:disabled="isSubmitting" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white text-sm font-bold rounded-lg p-3 shadow-lg shadow-blue-100 transition-all flex justify-center items-center disabled:opacity-70 disabled:cursor-not-allowed">
                        <span x-show="!isSubmitting">Simpan Paket</span>
                        <span x-show="isSubmitting" x-cloak class="flex items-center gap-2">
                            <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                            Menyimpan...
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div x-show="openEdit" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm p-4 transition-all">
        <div class="bg-white w-full max-w-xl rounded-xl shadow-2xl p-8 max-h-[90vh] overflow-y-auto" @click.away="openEdit = false">
            <h4 class="text-xl font-bold text-gray-900 mb-1">Edit Paket Internet</h4>
            <p class="text-sm text-gray-500 mb-6">Perbarui detail harga atau informasi paket</p>
            
            <form :action="'/paket/' + editData.id" method="POST" class="space-y-4" x-data="{ isSubmitting: false }" @submit="isSubmitting = true"> 
                @csrf @method('PUT')
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Nama Paket</label>
                        <input type="text" name="nama_paket" x-model="editData.nama_paket" class="w-full text-sm p-3 bg-gray-50 border border-gray-100 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none font-medium" required>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Kecepatan</label>
                        <input type="text" name="kecepatan" x-model="editData.kecepatan" class="w-full text-sm p-3 bg-gray-50 border border-gray-100 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none font-medium" required>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Harga (Rp) / Bulan</label>
                        <input type="number" name="harga" x-model="editData.harga" class="w-full text-sm p-3 bg-gray-50 border border-gray-100 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none font-medium" required>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Status Penjualan</label>
                        <select name="status" x-model="editData.status" class="w-full text-sm p-3 bg-gray-50 border border-gray-100 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none font-bold text-gray-600" required>
                            <option value="Active">Active</option>
                            <option value="Non Active">Non Active</option>
                            <option value="Pending">Pending (Draft)</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Deskripsi Paket (Opsional)</label>
                    <textarea name="deskripsi" x-model="editData.deskripsi" rows="2" class="w-full text-sm p-3 bg-gray-50 border border-gray-100 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none font-medium"></textarea>
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Keypoint / Kelebihan (Opsional)</label>
                    <textarea name="keypoint" x-model="editData.keypoint" rows="2" class="w-full text-sm p-3 bg-gray-50 border border-gray-100 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none font-medium"></textarea>
                </div>

                <div class="flex gap-3 pt-4 border-t border-gray-100">
                    <button type="button" @click="openEdit = false" class="flex-1 text-sm font-bold text-gray-500 p-3 hover:bg-gray-100 rounded-lg transition-all">Batal</button>
                    <button type="submit" x-bind:disabled="isSubmitting" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white text-sm font-bold rounded-lg p-3 shadow-lg shadow-blue-100 transition-all flex justify-center items-center disabled:opacity-70 disabled:cursor-not-allowed">
                        <span x-show="!isSubmitting">Update Perubahan</span>
                        <span x-show="isSubmitting" x-cloak class="flex items-center gap-2">
                            <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                            Memproses...
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div x-show="openDelete" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm p-4 transition-all">
        <div class="bg-white w-full max-w-md rounded-2xl shadow-2xl p-8 text-center" @click.away="openDelete = false">
            <div class="w-16 h-16 bg-red-50 text-red-500 rounded-full flex items-center justify-center mx-auto mb-5">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
            </div>
            <h4 class="text-xl font-bold text-gray-900 mb-2">Hapus Paket Internet?</h4>
            <p class="text-sm text-gray-500 leading-relaxed mb-8 px-4">
                Pastikan paket ini tidak sedang digunakan oleh pelanggan manapun. Yakin ingin menghapus?
            </p>
            <form :action="deleteUrl" method="POST" x-data="{ isSubmitting: false }" @submit="isSubmitting = true">
                @csrf @method('DELETE')
                <div class="flex gap-3">
                    <button type="button" @click="openDelete = false" class="flex-1 text-sm font-bold text-gray-600 p-3 hover:bg-gray-100 rounded-xl transition-all">Batalkan</button>
                    <button type="submit" x-bind:disabled="isSubmitting" class="flex-1 bg-red-600 hover:bg-red-700 text-white text-sm font-bold rounded-xl p-3 shadow-lg shadow-red-100 transition-all flex justify-center items-center disabled:opacity-70 disabled:cursor-not-allowed">
                        <span x-show="!isSubmitting">Ya, Hapus</span>
                        <span x-show="isSubmitting" x-cloak class="flex items-center gap-2">
                            <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                            Menghapus...
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection