<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Client Portal - CSM.TV</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['Inter', 'sans-serif'] },
                    colors: { brand: { 50: '#eef2ff', 100: '#e0e7ff', 500: '#6366f1', 600: '#4f46e5', 700: '#4338ca', 900: '#312e81' } }
                }
            }
        }
    </script>
    <style>[x-cloak] { display: none !important; }</style>
</head>
<body class="bg-slate-50 text-slate-800 antialiased flex flex-col min-h-screen">

    {{-- NAVBAR PUTIH SIMPLE --}}
    <nav class="fixed w-full top-0 z-50 bg-white/95 backdrop-blur-sm border-b border-slate-100 transition-all duration-300">
        <div class="max-w-7xl mx-auto px-6 h-20 flex items-center justify-between">
            <a href="{{ url('/') }}" class="text-2xl font-extrabold tracking-tighter text-slate-900">
                CSM<span class="text-brand-600">.TV</span>
            </a>

            <div class="hidden md:flex items-center gap-10">
                <a href="{{ url('/') }}" class="text-sm font-semibold text-slate-500 hover:text-brand-600 transition-colors">Beranda</a>
                <a href="{{ route('komplain.form') }}" class="text-sm font-semibold text-slate-500 hover:text-brand-600 transition-colors">Lapor Gangguan</a>
            </div>

            <div class="hidden md:flex items-center gap-4">
                <form action="{{ route('logout') }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="px-5 py-2 text-sm font-bold text-red-600 bg-red-50 hover:bg-red-100 rounded-full transition-colors">Logout</button>
                </form>
            </div>

            <button class="md:hidden p-2 text-slate-900" onclick="document.getElementById('mobile-menu').classList.toggle('hidden')">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
            </button>
        </div>

        <div id="mobile-menu" class="hidden absolute top-20 left-0 w-full bg-white border-b border-slate-100 p-6 flex flex-col gap-4 shadow-xl md:hidden">
            <a href="{{ url('/') }}" class="block py-2 text-base font-medium text-slate-600">Beranda</a>
            <a href="{{ route('komplain.form') }}" class="block py-2 text-base font-medium text-slate-600">Lapor Gangguan</a>
            <hr class="border-slate-100">
            <form action="{{ route('logout') }}" method="POST" class="w-full">
                @csrf
                <button type="submit" class="w-full py-3 text-center bg-red-50 text-red-600 rounded-lg font-bold">Logout</button>
            </form>
        </div>
    </nav>

    <main class="flex-1 pt-28 pb-16">
        <div class="max-w-7xl mx-auto px-4 md:px-6">
            
            <div class="mb-8">
                <h1 class="text-3xl md:text-4xl font-extrabold text-slate-900 tracking-tight">Client Portal</h1>
                <p class="text-sm text-slate-500 mt-2 font-medium">Selamat datang, kelola informasi akun dan pantau tagihan layanan Anda disini.</p>
            </div>

            {{-- FLOATING TOAST ANIMATION (SOLID, SHADOW & INTERACTIVE) --}}
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
                            }, 20); // 4 detik
                        },
                        pauseTimer() {
                            clearInterval(this.interval);
                        },
                        init() {
                            // Delay dikit biar animasi masuknya keliatan pas halaman baru load
                            setTimeout(() => {
                                this.show = true;
                                this.startTimer();
                            }, 100);
                        }
                     }" 
                     x-show="show" 
                     @mouseenter="pauseTimer()"
                     @mouseleave="startTimer()"
                     x-transition:enter="transition ease-out duration-500"
                     x-transition:enter-start="opacity-0 translate-x-10 scale-95"
                     x-transition:enter-end="opacity-100 translate-x-0 scale-100"
                     x-transition:leave="transition ease-in duration-300"
                     x-transition:leave-start="opacity-100 translate-x-0 scale-100"
                     x-transition:leave-end="opacity-0 translate-x-10 scale-95"
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

                    {{-- Progress Bar Indicator --}}
                    <div class="w-full h-1 bg-gray-50">
                        <div class="h-full bg-emerald-500 transition-all duration-75 ease-linear" :style="`width: ${progress}%`"></div>
                    </div>
                </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                
                {{-- KIRI: FORM EDIT PROFIL --}}
                <div class="lg:col-span-2">
                    <div class="bg-white p-6 md:p-8 rounded-3xl shadow-sm border border-slate-100">
                        <h2 class="text-xl font-bold text-slate-900 mb-6 flex items-center gap-2">
                            <svg class="w-5 h-5 text-brand-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                            Informasi Pribadi
                        </h2>

                        <form action="{{ route('client-portal.update') }}" method="POST" class="space-y-5" x-data="{ isSubmitting: false }" @submit="isSubmitting = true">
                            @csrf
                            @method('PUT')
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                <div>
                                    <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">Username Login <span class="text-red-500">*</span></label>
                                    <input type="text" value="{{ $user->username }}" class="w-full p-3.5 bg-slate-100 border border-slate-200 rounded-xl text-slate-500 font-bold cursor-not-allowed text-sm" readonly>
                                    <p class="text-[10px] text-slate-400 mt-1">Username tidak dapat diubah.</p>
                                </div>
                                <div>
                                    <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">Nama Lengkap <span class="text-red-500">*</span></label>
                                    <input type="text" name="fullname" value="{{ $pelanggan->nama_pelanggan ?? $user->fullname }}" class="w-full p-3.5 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-brand-500 outline-none font-bold text-slate-800 text-sm transition-all" required>
                                </div>
                                <div>
                                    <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">Nomor WhatsApp</label>
                                    <input type="text" name="no_wa" value="{{ $pelanggan->no_wa ?? '' }}" class="w-full p-3.5 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-brand-500 outline-none font-bold text-slate-800 text-sm transition-all" placeholder="Contoh: 08123456789">
                                </div>
                                <div>
                                    <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">Email Akses</label>
                                    <input type="email" name="email" value="{{ $user->email }}" class="w-full p-3.5 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-brand-500 outline-none font-bold text-slate-800 text-sm transition-all">
                                </div>
                            </div>

                            <div>
                                <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">Alamat Pemasangan</label>
                                <textarea name="alamat" rows="3" class="w-full p-3.5 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-brand-500 outline-none font-medium text-slate-800 text-sm transition-all">{{ $pelanggan->alamat ?? '' }}</textarea>
                            </div>

                            <div class="pt-4 border-t border-slate-100">
                                <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">Ubah Password Login (Opsional)</label>
                                <input type="password" name="password" placeholder="Kosongkan jika tidak ingin mengubah password" class="w-full p-3.5 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-brand-500 outline-none font-medium text-slate-800 text-sm transition-all">
                            </div>

                            <div class="pt-2">
                                <button type="submit" x-bind:disabled="isSubmitting" class="w-full md:w-auto px-8 py-3.5 bg-brand-600 text-white font-bold rounded-xl hover:bg-brand-700 transition duration-300 shadow-lg shadow-brand-600/30 flex justify-center items-center disabled:opacity-70 disabled:cursor-not-allowed">
                                    <span x-show="!isSubmitting">Simpan Perubahan</span>
                                    <span x-show="isSubmitting" x-cloak class="flex items-center gap-2">
                                        <svg class="animate-spin h-5 w-5 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                        Menyimpan...
                                    </span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                {{-- KANAN: PAKET & TRANSAKSI --}}
                <div class="space-y-8">
                    
                    {{-- Card Paket & Tagihan --}}
                    <div class="bg-slate-900 p-6 md:p-8 rounded-3xl shadow-xl relative overflow-hidden">
                        <div class="absolute -right-6 -top-6 w-32 h-32 bg-brand-500 rounded-full blur-3xl opacity-20"></div>
                        
                        <h3 class="text-sm font-bold text-slate-400 uppercase tracking-widest mb-6">Layanan Aktif Anda</h3>
                        
                        @if($pelanggan && $pelanggan->paket)
                            <div class="mb-6">
                                <h4 class="text-2xl font-black text-white mb-1">{{ $pelanggan->paket->nama_paket }}</h4>
                                <p class="text-brand-400 font-bold text-lg mb-1">{{ $pelanggan->paket->kecepatan }}</p>
                                <p class="text-slate-300 font-medium text-sm">Rp {{ number_format($pelanggan->paket->harga, 0, ',', '.') }} <span class="text-slate-500 text-xs">/ bulan</span></p>
                            </div>

                            <div class="bg-white/10 rounded-2xl p-4 border border-white/10 backdrop-blur-sm mb-4">
                                <div class="flex justify-between items-center mb-2">
                                    <span class="text-xs font-medium text-slate-300">Status Tagihan</span>
                                    @if($pelanggan->status_pembayaran == 'Lunas')
                                        <span class="text-[10px] font-black px-2 py-1 bg-emerald-500/20 text-emerald-400 rounded-md">LUNAS</span>
                                    @else
                                        <span class="text-[10px] font-black px-2 py-1 bg-red-500/20 text-red-400 rounded-md">BELUM LUNAS</span>
                                    @endif
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-xs font-medium text-slate-300">Jatuh Tempo</span>
                                    <span class="text-sm font-bold text-white">{{ $pelanggan->jatuh_tempo ? \Carbon\Carbon::parse($pelanggan->jatuh_tempo)->translatedFormat('d M Y') : '-' }}</span>
                                </div>
                            </div>
                            
                            <p class="text-[10px] text-slate-400 leading-relaxed text-center mt-4">Hubungi admin melalui WhatsApp untuk melakukan *Upgrade* atau perubahan paket berlangganan.</p>
                        @else
                            <div class="py-8 text-center">
                                <div class="w-12 h-12 bg-white/10 rounded-full flex items-center justify-center text-slate-400 mx-auto mb-3">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                                </div>
                                <p class="text-sm font-medium text-slate-300">Akun belum memiliki paket aktif.<br>Menunggu aktivasi Admin.</p>
                            </div>
                        @endif
                    </div>

                    {{-- Card Riwayat Transaksi --}}
                    <div class="bg-white p-6 md:p-8 rounded-3xl shadow-sm border border-slate-100">
                        <div class="flex items-center justify-between mb-6">
                            <h2 class="text-lg font-bold text-slate-900">Riwayat Pembayaran</h2>
                            <span class="text-xs font-bold text-slate-400 bg-slate-100 px-2 py-1 rounded-lg">10 Terakhir</span>
                        </div>

                        <div class="space-y-3">
                            @forelse($transaksis as $trx)
                                <div class="flex items-center justify-between p-3.5 bg-slate-50 rounded-2xl border border-slate-100 hover:border-brand-200 transition-colors">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-xl bg-emerald-100 text-emerald-600 flex flex-shrink-0 items-center justify-center">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                                        </div>
                                        <div>
                                            <p class="text-xs font-bold text-slate-800 mb-0.5">Pembayaran Internet</p>
                                            <p class="text-[10px] font-medium text-slate-500">{{ \Carbon\Carbon::parse($trx->tanggal)->translatedFormat('d M Y') }}</p>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-sm font-black text-slate-900">Rp {{ number_format($trx->jumlah, 0, ',', '.') }}</p>
                                        <p class="text-[9px] font-bold text-emerald-600 uppercase tracking-widest mt-0.5">Berhasil</p>
                                    </div>
                                </div>
                            @empty
                                <div class="text-center py-8 border-2 border-dashed border-slate-200 rounded-2xl bg-slate-50/50">
                                    <p class="text-xs font-bold text-slate-400">Belum ada riwayat transaksi.</p>
                                </div>
                            @endforelse
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </main>

    <footer class="bg-white border-t border-slate-100 py-6 mt-auto">
        <div class="max-w-7xl mx-auto px-6 text-center">
            <p class="text-sm text-slate-400 font-medium">&copy; {{ date('Y') }} CSM.TV. All rights reserved.</p>
        </div>
    </footer>
</body>
</html>