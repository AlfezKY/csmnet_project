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
                    // Durasinya dilambatkan sedikit (25) agar sempat dibaca
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

    {{-- TABEL DATA PENGGUNA (DIPERBARUI) --}}
    <div class="relative overflow-x-auto bg-white shadow-sm rounded-2xl border border-gray-100">
        <table class="w-full text-left text-gray-600 border-collapse">
            <thead class="text-[11px] text-gray-400 bg-gray-50/50 border-b border-gray-100 uppercase tracking-widest font-black">
                <tr>
                    <th class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center gap-1 cursor-pointer hover:text-gray-900 transition-colors">
                            Nama Lengkap
                            <svg class="w-4 h-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m8 15 4 4 4-4m0-6-4-4-4 4"/></svg>
                        </div>
                    </th>
                    <th class="px-6 py-4 whitespace-nowrap">Username</th>
                    <th class="px-6 py-4 text-center whitespace-nowrap">Role</th>
                    <th class="px-6 py-4 text-center whitespace-nowrap">Dibuat Pada</th>
                    <th class="px-6 py-4 text-center whitespace-nowrap">Update Terakhir</th>
                    <th class="px-6 py-4 text-right whitespace-nowrap">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($users as $user)
                <tr class="hover:bg-blue-50/30 transition-colors group">
                    <td class="px-6 py-4 text-sm font-bold text-gray-900 whitespace-nowrap">{{ $user->fullname }}</td>
                    <td class="px-6 py-4 text-sm text-gray-600 font-bold whitespace-nowrap">{{ $user->username }}</td>
                    <td class="px-6 py-4 text-center whitespace-nowrap">
                        <span class="inline-flex items-center px-2.5 py-1 rounded-md text-[10px] font-black uppercase tracking-wider border
                            {{ $user->role == 'Owner' ? 'bg-purple-50 text-purple-700 border-purple-100' : ($user->role == 'Admin' ? 'bg-blue-50 text-blue-700 border-blue-100' : 'bg-emerald-50 text-emerald-700 border-emerald-100') }}">
                            {{ $user->role }}
                        </span>
                    </td>
                    
                    <td class="px-6 py-4 text-center whitespace-nowrap">
                        <div class="flex flex-col items-center justify-center">
                            <span class="text-sm font-bold text-gray-600">{{ $user->created_at->format('d/m/Y') }}</span>
                            <span class="text-[10px] text-gray-400 font-black uppercase tracking-widest mt-0.5">{{ $user->created_by ?? 'SYSTEM' }}</span>
                        </div>
                    </td>

                    <td class="px-6 py-4 text-center whitespace-nowrap">
                        <div class="flex flex-col items-center justify-center">
                            <span class="text-sm font-bold text-gray-600">{{ $user->updated_at ? $user->updated_at->format('d/m/Y') : '-' }}</span>
                            <span class="text-[10px] text-gray-400 font-black uppercase tracking-widest mt-0.5">{{ $user->updated_by ?? '-' }}</span>
                        </div>
                    </td>

                    <td class="px-6 py-4 text-right whitespace-nowrap">
                        <div class="flex justify-end gap-2">
                            <button @click="openEdit = true; editData = { id: '{{ $user->id }}', fullname: '{{ $user->fullname }}', role: '{{ $user->role }}' }" 
                                    class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-all" title="Edit Data">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                            </button>
                            <button @click="openDelete = true; deleteUrl = '{{ route('users.destroy', $user->id) }}'" 
                                    class="p-2 text-red-500 hover:bg-red-50 rounded-lg transition-all" title="Hapus User">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-12 text-center">
                        <div class="flex flex-col items-center justify-center text-gray-400">
                            <div class="w-12 h-12 bg-gray-50/50 text-gray-300 rounded-full flex items-center justify-center mb-3">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </div>
                            <span class="text-sm font-bold text-gray-500">Belum ada data pengguna.</span>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- MODAL TAMBAH PENGGUNA (DIPERBARUI ROUNDED-2XL) --}}
    <div x-show="openAdd" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm p-4 transition-all">
        <div class="bg-white w-full max-w-xl rounded-2xl shadow-2xl p-8" @click.away="openAdd = false">
            <h4 class="text-xl font-bold text-gray-900 mb-1">Tambah User Baru</h4>
            <p class="text-sm font-medium text-gray-500 mb-6">Isi data akun administrator, pelanggan atau owner baru</p>
            
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
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-[10px] font-black text-gray-700 uppercase tracking-widest mb-1.5 ml-1">Nama Lengkap</label>
                        <input type="text" name="fullname" placeholder="Contoh: Risky Alfarez" class="w-full text-sm p-3 bg-gray-50 border border-gray-100 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none font-bold text-gray-800 transition-all" required>
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-gray-700 uppercase tracking-widest mb-1.5 ml-1">Username</label>
                        <input type="text" name="username" placeholder="username" class="w-full text-sm p-3 bg-gray-50 border border-gray-100 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none font-bold text-gray-800 transition-all" required>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 items-start">
                    <div>
                        <label class="block text-[10px] font-black text-gray-700 uppercase tracking-widest mb-1.5 ml-1">Role / Hak Akses</label>
                        <select name="role" class="w-full text-sm p-3 bg-gray-50 border border-gray-100 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none font-bold text-gray-800 cursor-pointer transition-all">
                            <option value="Admin">Admin</option>
                            <option value="Owner">Owner</option>
                            <option value="Pelanggan">Pelanggan</option>
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-[10px] font-black text-gray-700 uppercase tracking-widest mb-1.5 ml-1">Password</label>
                        <div class="relative">
                            <input :type="showPassword ? 'text' : 'password'" name="password" x-model="password" placeholder="Buat password aman" 
                                   class="w-full text-sm p-3 pr-10 bg-gray-50 border rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none font-bold text-gray-800 transition-all" 
                                   :class="(password.length > 0 && !isPasswordValid) ? 'border-red-400 focus:ring-red-500' : 'border-gray-100'" required>
                            <button type="button" @click="showPassword = !showPassword" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600 focus:outline-none">
                                <svg x-show="!showPassword" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                                <svg x-show="showPassword" x-cloak class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.29 3.29m0 0a10.05 10.05 0 015.188-1.583c4.477 0 8.268 2.943 9.542 7a10.025 10.025 0 01-4.132 5.411m0 0l-3.29-3.29" /></svg>
                            </button>
                        </div>
                        <p class="text-[10px] font-medium mt-1.5 ml-1 transition-colors duration-200" :class="(password.length > 0 && !isPasswordValid) ? 'text-red-500' : 'text-gray-400'">
                            * Min. 8 karakter, wajib huruf & angka
                        </p>
                    </div>
                </div>

                <div class="flex gap-3 pt-6 mt-6 border-t border-gray-100">
                    <button type="button" @click="openAdd = false" class="flex-1 text-sm font-bold text-gray-500 p-3 hover:bg-gray-100 rounded-xl transition-all">Batal</button>
                    <button type="submit" x-bind:disabled="!isPasswordValid || isSubmitting" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white text-sm font-bold rounded-xl p-3 shadow-lg shadow-blue-100 transition-all flex justify-center items-center disabled:opacity-70 disabled:cursor-not-allowed">
                        <span x-show="!isSubmitting">Simpan Akun</span>
                        <span x-show="isSubmitting" x-cloak class="flex items-center gap-2">
                            <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- MODAL EDIT PENGGUNA (DIPERBARUI ROUNDED-2XL) --}}
    <div x-show="openEdit" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm p-4 transition-all">
        <div class="bg-white w-full max-w-xl rounded-2xl shadow-2xl p-8" @click.away="openEdit = false">
            <h4 class="text-xl font-bold text-gray-900 mb-1">Edit Data User</h4>
            <p class="text-sm font-medium text-gray-500 mb-6">Perbarui informasi akun pengguna</p>

            <form :action="'/users/' + editData.id" method="POST" class="space-y-4" 
                  x-data="{ 
                      isSubmitting: false,
                      password: '',
                      showPassword: false,
                      get isPasswordValid() {
                          if(this.password.length === 0) return true;
                          return this.password.length >= 8 && /[a-zA-Z]/.test(this.password) && /[0-9]/.test(this.password);
                      }
                  }" 
                  @submit="isSubmitting = true">
                @csrf @method('PUT')
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-[10px] font-black text-gray-700 uppercase tracking-widest mb-1.5 ml-1">Nama Lengkap</label>
                        <input type="text" name="fullname" x-model="editData.fullname" class="w-full text-sm p-3 bg-gray-50 border border-gray-100 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none font-bold text-gray-800 transition-all" required>
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-gray-700 uppercase tracking-widest mb-1.5 ml-1">Role / Hak Akses</label>
                        <select name="role" x-model="editData.role" class="w-full text-sm p-3 bg-gray-50 border border-gray-100 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none font-bold text-gray-800 cursor-pointer transition-all">
                            <option value="Admin">Admin</option>
                            <option value="Owner">Owner</option>
                            <option value="Pelanggan">Pelanggan</option>
                        </select>
                    </div>
                </div>

                <div class="pt-2">
                    <label class="block text-[10px] font-black text-gray-700 uppercase tracking-widest mb-1.5 ml-1">Ganti Password (Opsional)</label>
                    <div class="relative">
                        <input :type="showPassword ? 'text' : 'password'" name="password" x-model="password" placeholder="Ketik sandi baru jika ingin mengubah..." 
                               class="w-full text-sm p-3 pr-10 bg-gray-50 border rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none font-bold text-gray-800 transition-all"
                               :class="(password.length > 0 && !isPasswordValid) ? 'border-red-400 focus:ring-red-500' : 'border-gray-100'">
                        <button type="button" @click="showPassword = !showPassword" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600 focus:outline-none">
                            <svg x-show="!showPassword" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                            <svg x-show="showPassword" x-cloak class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.29 3.29m0 0a10.05 10.05 0 015.188-1.583c4.477 0 8.268 2.943 9.542 7a10.025 10.025 0 01-4.132 5.411m0 0l-3.29-3.29" /></svg>
                        </button>
                    </div>
                    <p class="text-[10px] font-medium mt-1.5 ml-1 transition-colors duration-200" :class="(password.length > 0 && !isPasswordValid) ? 'text-red-500' : 'text-gray-400'">
                        * Kosongkan jika tidak diganti. Jika diisi, minimal 8 karakter huruf & angka.
                    </p>
                </div>

                <div class="flex gap-3 pt-6 mt-6 border-t border-gray-100">
                    <button type="button" @click="openEdit = false" class="flex-1 text-sm font-bold text-gray-500 p-3 hover:bg-gray-100 rounded-xl transition-all">Batal</button>
                    <button type="submit" x-bind:disabled="!isPasswordValid || isSubmitting" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white text-sm font-bold rounded-xl p-3 shadow-lg shadow-blue-100 transition-all flex justify-center items-center disabled:opacity-70 disabled:cursor-not-allowed">
                        <span x-show="!isSubmitting">Update Perubahan</span>
                        <span x-show="isSubmitting" x-cloak class="flex items-center gap-2">
                            <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
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
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
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
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection