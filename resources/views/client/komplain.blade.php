<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Ajukan Komplain - CSM.TV</title>

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
                    colors: {
                        brand: { 50: '#eef2ff', 100: '#e0e7ff', 600: '#4f46e5', 700: '#4338ca', 900: '#312e81' }
                    }
                }
            }
        }
    </script>
    <style>[x-cloak] { display: none !important; }</style>
</head>
<body class="bg-slate-50 text-slate-800 antialiased selection:bg-brand-600 selection:text-white flex flex-col min-h-screen">

    <nav class="fixed w-full top-0 z-50 bg-white/95 backdrop-blur-sm border-b border-slate-100 transition-all duration-300">
        <div class="max-w-7xl mx-auto px-6 h-20 flex items-center justify-between">
            <a href="{{ url('/') }}" class="text-2xl font-extrabold tracking-tighter text-slate-900">
                CSM<span class="text-brand-600">.TV</span>
            </a>

            <div class="hidden md:flex items-center gap-10">
                <a href="{{ url('/') }}" class="text-sm font-semibold text-slate-500 hover:text-brand-600 transition-colors">Beranda</a>
                <a href="{{ url('/about') }}" class="text-sm font-semibold text-slate-500 hover:text-brand-600 transition-colors">Tentang</a>
                <a href="{{ url('/#paket') }}" class="text-sm font-semibold text-slate-500 hover:text-brand-600 transition-colors">Paket Layanan</a>
            </div>

            <div class="hidden md:flex items-center gap-4">
                @auth
                    @if(Auth::user()->role === 'Pelanggan')
                        <a href="{{ route('client-portal') }}" class="text-sm font-bold text-slate-700 hover:text-brand-600 transition-colors mr-2 flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            Client Portal
                        </a>
                        <form action="{{ route('logout') }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="px-5 py-2 text-sm font-bold text-red-600 bg-red-50 hover:bg-red-100 rounded-full transition-colors">Logout</button>
                        </form>
                    @else
                        <a href="{{ route('dashboard') }}" class="px-6 py-2.5 text-sm font-semibold text-white bg-slate-900 rounded-full hover:bg-slate-800 transition">Dashboard Admin</a>
                    @endif
                @else
                    <a href="{{ route('login') }}" class="text-sm font-semibold text-slate-900 hover:text-brand-600">Login</a>
                    <a href="{{ url('/register') }}" class="px-6 py-2.5 text-sm font-semibold text-white bg-brand-600 rounded-full hover:bg-brand-700 transition shadow-lg shadow-brand-600/20">Daftar</a>
                @endauth
            </div>

            <button class="md:hidden p-2 text-slate-900" onclick="document.getElementById('mobile-menu').classList.toggle('hidden')">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
            </button>
        </div>

        <div id="mobile-menu" class="hidden absolute top-20 left-0 w-full bg-white border-b border-slate-100 p-6 flex flex-col gap-4 shadow-xl md:hidden">
            <a href="{{ url('/') }}" class="block py-2 text-base font-medium text-slate-600">Beranda</a>
            <a href="{{ url('/about') }}" class="block py-2 text-base font-medium text-slate-600">Tentang</a>
            <a href="{{ url('/#paket') }}" class="block py-2 text-base font-medium text-slate-600">Paket Layanan</a>
            <hr class="border-slate-100">
            @auth
                @if(Auth::user()->role === 'Pelanggan')
                    <a href="{{ route('client-portal') }}" class="block w-full py-3 text-center border border-brand-200 text-brand-600 rounded-lg font-bold">Buka Client Portal</a>
                    <form action="{{ route('logout') }}" method="POST" class="w-full">
                        @csrf
                        <button type="submit" class="w-full py-3 text-center bg-red-50 text-red-600 rounded-lg font-bold">Logout</button>
                    </form>
                @else
                    <a href="{{ route('dashboard') }}" class="block w-full py-3 text-center bg-slate-900 text-white rounded-lg font-bold">Dashboard Admin</a>
                @endif
            @else
                <a href="{{ route('login') }}" class="block w-full py-3 text-center border border-slate-200 rounded-lg font-bold text-slate-700">Login</a>
                <a href="{{ url('/register') }}" class="block w-full py-3 text-center bg-brand-600 text-white rounded-lg font-bold">Daftar Sekarang</a>
            @endauth
        </div>
    </nav>

    <main class="flex-1 flex items-center justify-center pt-28 pb-12">
        <div class="w-full max-w-2xl mx-auto px-6">
            <div class="bg-white p-8 md:p-10 rounded-3xl shadow-xl shadow-slate-200 border-t-4 border-brand-600 text-center border border-slate-100">
                
                <div class="w-14 h-14 bg-brand-50 text-brand-600 rounded-full flex items-center justify-center mx-auto mb-5">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                </div>
                
                <h2 class="text-2xl font-extrabold text-slate-900 mb-2">Lapor Gangguan Jaringan</h2>
                <p class="text-sm text-slate-500 mb-8 leading-relaxed px-4">Internet putus atau lambat? Silakan isi form di bawah ini agar tim teknis NOC kami dapat segera melakukan pengecekan.</p>

                @if(session('success'))
                    <div class="mb-6 p-4 bg-green-50 text-green-700 font-bold rounded-lg border border-green-100 text-sm text-left flex items-start gap-3">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        {{ session('success') }}
                    </div>
                @endif
                
                @if(session('error') || $errors->any())
                    <div class="mb-6 p-4 bg-red-50 text-red-700 font-bold rounded-lg border border-red-100 text-sm text-left flex items-start gap-3">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                        {{ session('error') ?? 'Mohon periksa kembali isian Anda.' }}
                    </div>
                @endif

                @auth
                    @php
                        $pelangganData = \App\Models\Pelanggan::where('user_id', auth()->id())->first();
                    @endphp
                    
                    @if(auth()->user()->role == 'Pelanggan' && $pelangganData)
                        <form action="{{ route('komplain.store') }}" method="POST" class="space-y-5 text-left" x-data="{ isSubmitting: false }" @submit="isSubmitting = true">
                            @csrf
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1">Nama Pelanggan</label>
                                    <input type="text" value="{{ $pelangganData->nama_pelanggan }}" class="w-full p-3.5 bg-slate-100 border border-slate-200 rounded-xl text-slate-600 font-bold cursor-not-allowed text-sm" readonly>
                                </div>
                                <div>
                                    <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1">Nomor WhatsApp</label>
                                    <input type="text" value="{{ $pelangganData->no_wa }}" class="w-full p-3.5 bg-slate-100 border border-slate-200 rounded-xl text-slate-600 font-bold cursor-not-allowed text-sm" readonly>
                                </div>
                            </div>
                            
                            <div>
                                <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1">Detail Keluhan</label>
                                <textarea name="keluhan" rows="4" placeholder="Contoh: Lampu LOS di modem kedap-kedip merah sejak pagi tadi..." class="w-full p-3.5 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-brand-500 outline-none font-medium text-sm transition-all" required></textarea>
                            </div>

                            <button type="submit" x-bind:disabled="isSubmitting" class="w-full bg-brand-600 text-white font-bold py-3.5 rounded-xl hover:bg-brand-700 transition duration-300 shadow-lg shadow-brand-600/30 flex justify-center items-center disabled:opacity-70 disabled:cursor-not-allowed mt-2">
                                <span x-show="!isSubmitting">Kirim Laporan Gangguan</span>
                                <span x-show="isSubmitting" x-cloak class="flex items-center gap-2">
                                    <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                    Memproses...
                                </span>
                            </button>
                        </form>
                    @else
                        <div class="p-6 bg-orange-50 text-orange-700 rounded-xl border border-orange-100 font-medium text-center">
                            Akun Anda saat ini belum tertaut dengan data Pelanggan di sistem kami.
                        </div>
                    @endif
                @endauth
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