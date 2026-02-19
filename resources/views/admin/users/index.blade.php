@extends('layouts.app')

@section('title', 'Manajemen Pengguna')

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
            <h3 class="text-2xl font-bold text-gray-900 tracking-tight">Manajemen Pengguna</h3>
            <p class="text-sm text-gray-500 font-medium mt-1">Kelola data administrator dan owner sistem CSMNET</p>
        </div>
        <button @click="openAdd = true" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-lg text-sm font-bold transition-all shadow-sm flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
            Tambah User Baru
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
                    <th class="px-4 py-4">
                        <div class="flex items-center gap-1 cursor-pointer hover:text-gray-900 transition-colors">
                            Nama Lengkap
                            <svg class="w-4 h-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m8 15 4 4 4-4m0-6-4-4-4 4"/></svg>
                        </div>
                    </th>
                    <th class="px-4 py-4">Username</th>
                    <th class="px-4 py-4 text-center">Role</th>
                    <th class="px-4 py-4 text-center">Created Date</th>
                    <th class="px-4 py-4 text-center">Update Date</th>
                    <th class="px-4 py-4 text-center">Created By</th>
                    <th class="px-4 py-4 text-center">Updated By</th>
                    <th class="px-4 py-4 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($users as $user)
                <tr class="hover:bg-gray-50/50 transition-colors group">
                    <td class="px-4 py-4 text-sm font-bold text-gray-900">{{ $user->fullname }}</td>
                    <td class="px-4 py-4 text-sm text-gray-500 font-medium">{{ $user->username }}</td>
                    <td class="px-4 py-4 text-center">
                        <span class="px-3 py-1 rounded-md text-xs font-bold uppercase border
                            {{ $user->role == 'Owner' ? 'bg-purple-50 text-purple-700 border-purple-100' : ($user->role == 'Admin' ? 'bg-blue-50 text-blue-700 border-blue-100' : 'bg-gray-50 text-gray-600 border-gray-200') }}">
                            {{ $user->role }}
                        </span>
                    </td>
                    <td class="px-4 py-4 text-[11px] text-gray-500 text-center font-bold">{{ $user->created_at->format('d/m/y') }}</td>
                    <td class="px-4 py-4 text-[11px] text-gray-500 text-center font-bold">
                        {{ $user->updated_by ? $user->updated_at->format('d/m/y') : '-' }}
                    </td>
                    <td class="px-4 py-4 text-[10px] text-gray-400 font-bold uppercase text-center">{{ $user->created_by ?? 'SYSTEM' }}</td>
                    <td class="px-4 py-4 text-[10px] text-gray-400 font-bold uppercase text-center">{{ $user->updated_by ?? '-' }}</td>
                    <td class="px-4 py-4 text-right">
                        <div class="flex justify-end gap-1">
                            <button @click="openEdit = true; editData = { id: '{{ $user->id }}', fullname: '{{ $user->fullname }}', role: '{{ $user->role }}' }" 
                                    class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-all" title="Edit Data">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                            </button>
                            <button @click="openDelete = true; deleteUrl = '{{ route('users.destroy', $user->id) }}'" 
                                    class="p-2 text-red-500 hover:bg-red-50 rounded-lg transition-all" title="Hapus User">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="px-4 py-8 text-center text-sm text-gray-400 font-medium">Belum ada data pengguna.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div x-show="openAdd" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm p-4 transition-all">
        <div class="bg-white w-full max-w-md rounded-xl shadow-2xl p-8" @click.away="openAdd = false">
            <h4 class="text-lg font-bold text-gray-900 mb-1 text-center">Tambah User Baru</h4>
            <p class="text-sm text-gray-500 mb-6 text-center">Isi data akun administrator, pelanggan atau owner baru</p>
            
            <form action="{{ route('users.store') }}" method="POST" class="space-y-4" 
                  x-data="{ 
                      isSubmitting: false,
                      password: '',
                      showPassword: false,
                      get isPasswordValid() {
                          if(this.password.length === 0) return false;
                          return this.password.length >= 8 && /[a-zA-Z]/.test(this.password) && /[0-9]/.test(this.password);
                      }
                  }" 
                  @submit="isSubmitting = true">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Nama Lengkap</label>
                    <input type="text" name="fullname" placeholder="Contoh: Risky Alfarez" class="w-full text-sm p-3 bg-gray-50 border border-gray-100 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none font-medium transition-all" required>
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Username</label>
                    <input type="text" name="username" placeholder="username" class="w-full text-sm p-3 bg-gray-50 border border-gray-100 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none font-medium transition-all" required>
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Role / Hak Akses</label>
                    <select name="role" class="w-full text-sm p-3 bg-gray-50 border border-gray-100 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none font-bold text-gray-600 cursor-pointer">
                        <option value="Admin">Admin</option>
                        <option value="Owner">Owner</option>
                        <option value="Pelanggan">Pelanggan</option>
                    </select>
                </div>
                
                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Password</label>
                    <div class="relative">
                        <input :type="showPassword ? 'text' : 'password'" name="password" x-model="password" placeholder="Buat password aman" 
                               class="w-full text-sm p-3 pr-10 bg-gray-50 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none font-medium transition-all" 
                               :class="(password.length > 0 && !isPasswordValid) ? 'border-red-400 focus:ring-red-500' : 'border-gray-100'" required>
                        <button type="button" @click="showPassword = !showPassword" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600 focus:outline-none">
                            <svg x-show="!showPassword" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                            <svg x-show="showPassword" x-cloak class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.29 3.29m0 0a10.05 10.05 0 015.188-1.583c4.477 0 8.268 2.943 9.542 7a10.025 10.025 0 01-4.132 5.411m0 0l-3.29-3.29" /></svg>
                        </button>
                    </div>
                    <p class="text-[10px] mt-1.5 font-medium transition-colors duration-200" :class="(password.length > 0 && !isPasswordValid) ? 'text-red-500' : 'text-gray-400'">
                        * Minimal 8 karakter, wajib kombinasi huruf & angka
                    </p>
                </div>

                <div class="flex gap-3 pt-4">
                    <button type="button" @click="openAdd = false" class="flex-1 text-sm font-bold text-gray-500 p-3 hover:bg-gray-100 rounded-lg transition-all">Batal</button>
                    <button type="submit" x-bind:disabled="!isPasswordValid || isSubmitting" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white text-sm font-bold rounded-lg p-3 shadow-lg shadow-blue-100 transition-all flex justify-center items-center disabled:opacity-50 disabled:cursor-not-allowed">
                        <span x-show="!isSubmitting">Simpan Akun</span>
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
        <div class="bg-white w-full max-w-md rounded-xl shadow-2xl p-8" @click.away="openEdit = false">
            <h4 class="text-lg font-bold text-gray-900 mb-1 text-center">Edit Data User</h4>
            <p class="text-sm text-gray-500 mb-6 text-center">Perbarui informasi akun pengguna</p>

            <form :action="'/users/' + editData.id" method="POST" class="space-y-4" 
                  x-data="{ 
                      isSubmitting: false,
                      password: '',
                      showPassword: false,
                      get isPasswordValid() {
                          if(this.password.length === 0) return true; // Optional saat edit
                          return this.password.length >= 8 && /[a-zA-Z]/.test(this.password) && /[0-9]/.test(this.password);
                      }
                  }" 
                  @submit="isSubmitting = true">
                @csrf @method('PUT')
                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Nama Lengkap</label>
                    <input type="text" name="fullname" x-model="editData.fullname" class="w-full text-sm p-3 bg-gray-50 border border-gray-100 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none font-medium" required>
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Role / Hak Akses</label>
                    <select name="role" x-model="editData.role" class="w-full text-sm p-3 bg-gray-50 border border-gray-100 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none font-bold text-gray-600 cursor-pointer">
                        <option value="Admin">Admin</option>
                        <option value="Owner">Owner</option>
                        <option value="Pelanggan">Pelanggan</option>
                    </select>
                </div>

                <div class="pt-2">
                    <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Ganti Password (Opsional)</label>
                    <div class="relative">
                        <input :type="showPassword ? 'text' : 'password'" name="password" x-model="password" placeholder="Ketik sandi baru..." 
                               class="w-full text-sm p-3 pr-10 bg-gray-50 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all"
                               :class="(password.length > 0 && !isPasswordValid) ? 'border-red-400 focus:ring-red-500' : 'border-gray-100'">
                        <button type="button" @click="showPassword = !showPassword" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600 focus:outline-none">
                            <svg x-show="!showPassword" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                            <svg x-show="showPassword" x-cloak class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.29 3.29m0 0a10.05 10.05 0 015.188-1.583c4.477 0 8.268 2.943 9.542 7a10.025 10.025 0 01-4.132 5.411m0 0l-3.29-3.29" /></svg>
                        </button>
                    </div>
                    <p class="text-[10px] mt-1.5 font-medium transition-colors duration-200" :class="(password.length > 0 && !isPasswordValid) ? 'text-red-500' : 'text-gray-400'">
                        * Kosongkan jika tidak diganti. Jika diisi, minimal 8 karakter huruf & angka.
                    </p>
                </div>

                <div class="flex gap-3 pt-4">
                    <button type="button" @click="openEdit = false" class="flex-1 text-sm font-bold text-gray-500 p-3 hover:bg-gray-100 rounded-lg">Batal</button>
                    <button type="submit" x-bind:disabled="!isPasswordValid || isSubmitting" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white text-sm font-bold rounded-lg p-3 shadow-lg shadow-blue-100 transition-all flex justify-center items-center disabled:opacity-50 disabled:cursor-not-allowed">
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
            
            <h4 class="text-xl font-bold text-gray-900 mb-2">Hapus User Secara Permanen?</h4>
            <p class="text-sm text-gray-500 leading-relaxed mb-8 px-4">
                Tindakan ini tidak dapat dibatalkan. Semua data yang terkait dengan user ini akan hilang dari database.
            </p>
            
            <form :action="deleteUrl" method="POST" x-data="{ isSubmitting: false }" @submit="isSubmitting = true">
                @csrf @method('DELETE')
                <div class="flex gap-3">
                    <button type="button" @click="openDelete = false" class="flex-1 text-sm font-bold text-gray-600 p-3 hover:bg-gray-100 rounded-xl transition-all">
                        Batalkan
                    </button>
                    <button type="submit" x-bind:disabled="isSubmitting" class="flex-1 bg-red-600 hover:bg-red-700 text-white text-sm font-bold rounded-xl p-3 shadow-lg shadow-red-100 transition-all flex justify-center items-center disabled:opacity-70 disabled:cursor-not-allowed">
                        <span x-show="!isSubmitting">Ya, Hapus Sekarang</span>
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