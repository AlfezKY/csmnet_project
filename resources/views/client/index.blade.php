<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>CSM.Net - Internet Service Provider Terpercaya</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">

    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['Inter', 'sans-serif'] },
                    colors: {
                        brand: { 50: '#eef2ff', 100: '#e0e7ff', 200: '#c7d2fe', 300: '#a5b4fc', 400: '#818cf8', 500: '#6366f1', 600: '#5A4FF3', 700: '#4338ca', 800: '#3730a3', 900: '#312e81' }
                    },
                    boxShadow: {
                        'soft': '0 4px 20px -2px rgba(0, 0, 0, 0.05)',
                        'card': '0 20px 40px -10px rgba(0,0,0,0.08)',
                        'glow': '0 0 20px rgba(90, 79, 243, 0.4)',
                    },
                    animation: {
                        'blob': 'blob 12s infinite',
                        'float': 'float 6s ease-in-out infinite',
                    },
                    keyframes: {
                        blob: {
                            '0%': { transform: 'translate(0px, 0px) scale(1)' },
                            '33%': { transform: 'translate(30px, -50px) scale(1.1)' },
                            '66%': { transform: 'translate(-20px, 20px) scale(0.9)' },
                            '100%': { transform: 'translate(0px, 0px) scale(1)' },
                        },
                        float: {
                            '0%, 100%': { transform: 'translateY(0)' },
                            '50%': { transform: 'translateY(-15px)' },
                        }
                    }
                }
            }
        }
    </script>
    <style>
        body { background-color: #F8FAFC; }
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
        .faq-content { transition: grid-template-rows 0.4s cubic-bezier(0.4, 0, 0.2, 1); }
        .faq-content[aria-expanded="false"] { grid-template-rows: 0fr; }
        .faq-content[aria-expanded="true"] { grid-template-rows: 1fr; }
        [x-cloak] { display: none !important; }
        
        .reveal-on-scroll {
            opacity: 0;
            transform: translateY(30px);
            transition: opacity 0.8s cubic-bezier(0.4, 0, 0.2, 1), transform 0.8s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .reveal-on-scroll.is-visible {
            opacity: 1;
            transform: translateY(0);
        }
        
        .delay-100 { transition-delay: 100ms; }
        .delay-200 { transition-delay: 200ms; }
        .delay-300 { transition-delay: 300ms; }
    </style>
</head>
<body class="text-slate-800 antialiased selection:bg-brand-600 selection:text-white relative overflow-x-hidden" 
      x-data="{ 
          showPendingAlert: false,
          alertTimeout: null,
          triggerAlert() {
              this.showPendingAlert = true;
              clearTimeout(this.alertTimeout);
              this.alertTimeout = setTimeout(() => { this.showPendingAlert = false }, 5000);
          }
      }">

    {{-- Background Dinamis --}}
    <div class="fixed inset-0 z-[-1] overflow-hidden pointer-events-none bg-[#F8FAFC]">
        <div class="absolute -top-[10%] -left-[10%] w-[40%] h-[40%] rounded-full bg-brand-200/50 mix-blend-multiply filter blur-[100px] animate-blob"></div>
        <div class="absolute top-[20%] -right-[10%] w-[40%] h-[40%] rounded-full bg-purple-200/50 mix-blend-multiply filter blur-[100px] animate-blob" style="animation-delay: 2s;"></div>
        <div class="absolute -bottom-[10%] left-[20%] w-[40%] h-[40%] rounded-full bg-blue-200/50 mix-blend-multiply filter blur-[100px] animate-blob" style="animation-delay: 4s;"></div>
    </div>

    @auth
        @if(Auth::user()->role === 'Pelanggan')
        <a href="{{ route('komplain.form') }}" class="fixed bottom-6 right-6 md:bottom-10 md:right-10 z-50 bg-gradient-to-r from-brand-600 to-indigo-600 text-white px-6 py-3.5 rounded-full shadow-lg hover:shadow-glow hover:-translate-y-1 transition-all duration-300 flex items-center gap-2 font-bold group">
            <svg class="w-5 h-5 group-hover:animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
            <span>Ajukan Komplain</span>
        </a>
        @endif
    @endauth

    <div x-show="showPendingAlert" x-cloak 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 translate-x-10"
         x-transition:enter-end="opacity-100 translate-x-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 translate-x-0"
         x-transition:leave-end="opacity-0 translate-x-10"
         class="fixed top-24 right-5 z-50 max-w-sm w-full bg-white text-slate-800 p-4 rounded-2xl shadow-card border-l-4 border-orange-500 flex items-start gap-3">
        <svg class="w-6 h-6 flex-shrink-0 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
        <div class="flex-1">
            <h4 class="font-bold text-sm">Menunggu Persetujuan Admin</h4>
            <p class="text-xs mt-1 text-slate-500 leading-relaxed">Pendaftaran Anda sedang ditinjau. Tim kami akan segera menghubungi Anda atau Anda dapat menghubungi WhatsApp kami.</p>
        </div>
        <button @click="showPendingAlert = false" class="text-slate-400 hover:text-slate-600 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
        </button>
    </div>

    {{-- Navbar --}}
    <nav class="fixed w-full top-0 z-50 bg-white/90 backdrop-blur-md border-b border-slate-100/50 transition-all duration-300">
        <div class="max-w-7xl mx-auto px-6 h-20 flex items-center justify-between">
            <a href="{{ url('/') }}" class="text-2xl font-black tracking-tighter text-slate-900 transition-transform hover:scale-105">
                CSM<span class="text-transparent bg-clip-text bg-gradient-to-r from-brand-600 to-indigo-500">.Net</span>
            </a>

            <div class="hidden md:flex items-center gap-10">
                <a href="{{ url('/') }}" class="text-sm font-semibold text-brand-600 relative after:content-[''] after:absolute after:-bottom-1 after:left-0 after:w-full after:h-0.5 after:bg-brand-600 after:rounded-full transition-colors">Beranda</a>
                <a href="{{ url('/about') }}" class="text-sm font-semibold text-slate-500 hover:text-brand-600 transition-colors">Tentang</a>
                <a href="#paket" class="text-sm font-semibold text-slate-500 hover:text-brand-600 transition-colors">Paket Layanan</a>
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
                        <a href="{{ route('dashboard') }}" class="px-6 py-2.5 text-sm font-semibold text-white bg-slate-900 rounded-full hover:bg-slate-800 transition shadow-lg">Dashboard Admin</a>
                    @endif
                @else
                    <a href="{{ route('login') }}" class="text-sm font-semibold text-slate-700 hover:text-brand-600 transition-colors">Login</a>
                    <a href="{{ url('/register') }}" class="px-6 py-2.5 text-sm font-semibold text-white bg-gradient-to-r from-brand-600 to-indigo-600 rounded-full hover:shadow-glow hover:-translate-y-0.5 transition-all duration-300">Daftar</a>
                @endauth
            </div>

            <button class="md:hidden p-2 text-slate-900" onclick="document.getElementById('mobile-menu').classList.toggle('hidden')">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
            </button>
        </div>

        <div id="mobile-menu" class="hidden absolute top-20 left-0 w-full bg-white border-t border-slate-100 p-6 flex flex-col gap-4 shadow-xl md:hidden">
            <a href="{{ url('/') }}" class="block py-2 text-base font-semibold text-brand-600">Beranda</a>
            <a href="{{ url('/about') }}" class="block py-2 text-base font-semibold text-slate-600">Tentang</a>
            <a href="#paket" class="block py-2 text-base font-semibold text-slate-600">Paket Layanan</a>
            <hr class="border-slate-100">
            
            @auth
                @if(Auth::user()->role === 'Pelanggan')
                    <a href="{{ route('client-portal') }}" class="block w-full py-3 text-center border border-brand-200 text-brand-600 rounded-xl font-bold bg-brand-50">Buka Client Portal</a>
                    <form action="{{ route('logout') }}" method="POST" class="w-full">
                        @csrf
                        <button type="submit" class="w-full py-3 text-center bg-red-50 text-red-600 rounded-xl font-bold">Logout</button>
                    </form>
                @else
                    <a href="{{ route('dashboard') }}" class="block w-full py-3 text-center bg-slate-900 text-white rounded-xl font-bold">Dashboard Admin</a>
                @endif
            @else
                <a href="{{ route('login') }}" class="block w-full py-3 text-center border border-slate-200 rounded-xl font-bold text-slate-700 hover:bg-slate-50">Login</a>
                <a href="{{ url('/register') }}" class="block w-full py-3 text-center bg-gradient-to-r from-brand-600 to-indigo-600 text-white rounded-xl font-bold shadow-md">Daftar Sekarang</a>
            @endauth
        </div>
    </nav>

    <main class="pt-24 lg:pt-32">
        
        {{-- Section Home --}}
<section id="home" class="py-10 lg:py-20 overflow-hidden relative">
    <div class="max-w-7xl mx-auto px-6">
        <div class="grid lg:grid-cols-12 gap-12 lg:gap-20 items-center">
            
            <div class="order-2 lg:order-1 lg:col-span-5 relative z-10 reveal-on-scroll">
                <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-white border border-slate-200 text-brand-600 text-[10px] font-bold uppercase tracking-widest mb-6 shadow-sm hover:shadow-md transition-shadow">
                    <span class="w-1.5 h-1.5 rounded-full bg-brand-600 animate-pulse"></span>
                    Promo Spesial
                </div>
                
                <h1 class="text-4xl lg:text-6xl font-black text-slate-900 leading-[1.1] mb-6 tracking-tight">
                    Paket Internet <br>
                    <span class="text-transparent bg-clip-text bg-gradient-to-r from-[#6b46ff] to-[#b05aff]">Premium & Cepat.</span>
                </h1>
                <p class="text-lg text-slate-500 mb-10 leading-relaxed font-medium">
                    Nikmati streaming tanpa buffering, gaming tanpa lag, dan download super cepat dengan infrastruktur fiber optic terbaru kami.
                </p>

                <div class="flex flex-col sm:flex-row gap-4">
                    <a href="#paket" class="inline-flex items-center justify-center px-8 py-4 text-sm font-bold text-white bg-gradient-to-r from-brand-600 to-indigo-600 rounded-full hover:shadow-glow hover:-translate-y-1 transition-all duration-300">
                        Mulai Berlangganan
                        <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                    </a>
                    <a href="https://wa.me/6281234567890" target="_blank" class="inline-flex items-center justify-center px-8 py-4 text-sm font-bold text-slate-700 bg-white border border-slate-200 rounded-full hover:bg-slate-50 hover:border-brand-300 hover:-translate-y-1 transition-all duration-300 shadow-sm">
                        WhatsApp Kami
                    </a>
                </div>
                
                <div class="mt-8 flex items-center gap-2 text-sm text-slate-500 font-medium bg-white inline-flex px-4 py-2 rounded-full border border-slate-200 shadow-[0_4px_15px_rgba(0,0,0,0.03)] cursor-default">
                    <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                    <span>Instalasi Cepat 1-3 Hari Kerja</span>
                </div>
            </div>

            <div class="relative order-1 lg:order-2 lg:col-span-7 reveal-on-scroll delay-200">
                <div class="relative group">
                    <img src="{{ asset('assets/dummy-card.jpg') }}" alt="Internet Cepat" 
                         class="relative w-full aspect-[4/3] lg:aspect-video rounded-[2.5rem] shadow-[0_32px_64px_-16px_rgba(0,0,0,0.2)] object-cover animate-float border-[6px] border-white" 
                         onerror="this.src='https://placehold.co/1200x800?text=CSM+Internet'">
                    
                    <div class="absolute -bottom-6 -left-6 md:-bottom-10 md:-left-10 bg-white px-8 py-6 rounded-3xl shadow-card border border-slate-100 flex items-center gap-5 z-20 group-hover:scale-105 transition-all duration-300">
                        <div class="w-14 h-14 rounded-2xl bg-brand-50 flex items-center justify-center text-brand-600">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                        </div>
                        <div>
                            <p class="text-xs text-slate-400 font-bold uppercase tracking-widest mb-1">Speed Test</p>
                            <p class="text-3xl font-black text-slate-900">100 Mbps</p>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>

        {{-- Section Features --}}
        <section class="py-24 relative z-10 mt-12">
            <div class="max-w-7xl mx-auto px-6 relative">
                <div class="text-center mb-16 reveal-on-scroll">
                    <h2 class="text-3xl lg:text-4xl font-black text-slate-900 mb-4 tracking-tight">Kenapa Harus CSM.Net?</h2>
                    <p class="text-slate-500 max-w-2xl mx-auto font-medium">Kami menggabungkan teknologi terbaik dengan pelayanan sepenuh hati untuk pengalaman internet terbaik Anda.</p>
                </div>

                <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-6">
                    @foreach([
                        ['icon' => 'M13 10V3L4 14h7v7l9-11h-7z', 'title' => 'Internet Stabil', 'desc' => 'Koneksi anti putus dengan teknologi fiber optic murni.', 'color' => 'text-blue-600', 'bg' => 'bg-blue-50', 'delay' => ''],
                        ['icon' => 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z', 'title' => 'Harga Terjangkau', 'desc' => 'Biaya bulanan transparan, tanpa biaya tersembunyi.', 'color' => 'text-emerald-600', 'bg' => 'bg-emerald-50', 'delay' => 'delay-100'],
                        ['icon' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z', 'title' => 'Support 24/7', 'desc' => 'Bantuan teknis siap sedia kapanpun Anda butuhkan.', 'color' => 'text-orange-600', 'bg' => 'bg-orange-50', 'delay' => 'delay-200'],
                        ['icon' => 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z', 'title' => 'Teknisi Ahli', 'desc' => 'Pemasangan rapi dan profesional oleh tim bersertifikat.', 'color' => 'text-purple-600', 'bg' => 'bg-purple-50', 'delay' => 'delay-300']
                    ] as $item)
                    <div class="bg-white p-8 rounded-2xl border border-slate-100 shadow-sm hover:shadow-card hover:-translate-y-2 transition-all duration-300 group cursor-default reveal-on-scroll {{ $item['delay'] }}">
                        <div class="w-14 h-14 {{ $item['bg'] }} rounded-xl flex items-center justify-center {{ $item['color'] }} mb-6 group-hover:scale-110 group-hover:rotate-3 transition-transform duration-300">
                            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $item['icon'] }}"/></svg>
                        </div>
                        <h3 class="text-xl font-bold text-slate-900 mb-2">{{ $item['title'] }}</h3>
                        <p class="text-slate-500 text-sm leading-relaxed">{{ $item['desc'] }}</p>
                    </div>
                    @endforeach
                </div>
            </div>
        </section>

        {{-- Section Pricing --}}
        <section id="paket" class="py-24 relative">
            <div class="max-w-7xl mx-auto px-6 relative z-10">
                <div class="text-center mb-16 reveal-on-scroll">
                    <span class="inline-block py-1.5 px-4 rounded-full bg-brand-50 text-brand-600 font-bold tracking-widest uppercase text-[10px] mb-4 border border-brand-100">Pilihan Paket</span>
                    <h2 class="text-3xl lg:text-4xl font-black text-slate-900 tracking-tight">Paket Internet Tersedia</h2>
                </div>

                @if($pakets->count() > 0)
                    @php
                        $pelanggan = Auth::check() ? \App\Models\Pelanggan::where('user_id', Auth::id())->first() : null;
                    @endphp

                    <div class="grid md:grid-cols-2 @if($pakets->count() <= 3) lg:grid-cols-3 max-w-5xl mx-auto @else lg:grid-cols-4 @endif gap-8 items-center">
                        
                        @foreach($pakets as $paket)
                            @php
                                $isFeatured = false;
                                if ($pakets->count() <= 3 && $loop->iteration == 2) $isFeatured = true;
                                if ($pakets->count() == 4 && $loop->iteration == 3) $isFeatured = true;
                                $hargaRibuan = floor($paket->harga / 1000);
                            @endphp

                            @if($isFeatured)
                                {{-- CARD PREMIUM --}}
                                <div class="bg-gradient-to-b from-slate-900 to-slate-800 rounded-[2rem] p-8 shadow-[0_20px_50px_rgba(15,23,42,0.3)] border border-slate-700 relative flex flex-col lg:scale-105 z-10 hover:-translate-y-2 transition-all duration-500 reveal-on-scroll delay-100 group">
                                    <div class="absolute inset-0 bg-gradient-to-br from-brand-600/10 to-transparent rounded-[2rem] opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
                                    <div class="absolute top-0 right-0 bg-gradient-to-r from-brand-500 to-brand-600 text-white text-[10px] font-bold px-4 py-2 rounded-bl-2xl rounded-tr-[2rem] uppercase tracking-widest shadow-lg">Terlaris</div>
                                    
                                    <div class="relative z-10">
                                        <h3 class="text-xl font-bold text-white mb-2">{{ $paket->nama_paket }}</h3>
                                        <p class="text-sm text-slate-400 mb-6 font-medium">{{ $paket->deskripsi ?? 'Internet cepat tanpa batas.' }}</p>
                                        
                                        <div class="mb-6 flex items-baseline gap-1">
                                            <span class="text-5xl font-black text-white">{{ $hargaRibuan }}rb</span>
                                            <span class="text-slate-400 font-medium">/bulan</span>
                                        </div>

                                        <div class="text-3xl font-black text-brand-400 mb-8 flex items-center gap-2">
                                            {{ filter_var($paket->kecepatan, FILTER_SANITIZE_NUMBER_INT) }} <span class="text-base font-bold text-slate-300">Mbps</span>
                                        </div>
                                        
                                        @if($paket->keypoint)
                                            <ul class="mb-10 space-y-4 text-sm text-slate-300 font-medium">
                                                @foreach(explode(',', $paket->keypoint) as $point)
                                                    <li class="flex items-start gap-3">
                                                        <div class="mt-0.5 text-brand-400">
                                                            <svg class="w-4 h-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg> 
                                                        </div>
                                                        {{ trim($point) }}
                                                    </li>
                                                @endforeach
                                            </ul>
                                        @endif
                                    </div>

                                    @auth
                                        @if(!$pelanggan || $pelanggan->status !== 'Active')
                                            <button @click="triggerAlert()" type="button" class="relative z-10 w-full py-3.5 rounded-xl bg-gradient-to-r from-brand-600 to-indigo-600 text-white font-bold hover:shadow-glow hover:scale-[1.02] transition-all duration-300 text-center mt-auto">Pilih Paket Ini</button>
                                        @else
                                            @php
                                                $pesanWa = "Halo CSM.Net, saya ingin berlangganan layanan internet.\n\n"
                                                         . "Nama: " . $pelanggan->nama_pelanggan . "\n"
                                                         . "Alamat: " . $pelanggan->alamat . "\n"
                                                         . "Paket Pilihan: " . $paket->nama_paket;
                                                $linkWa = "https://wa.me/6281234567890?text=" . urlencode($pesanWa);
                                            @endphp
                                            <a href="{{ $linkWa }}" target="_blank" rel="noopener noreferrer" class="relative z-10 w-full py-3.5 rounded-xl bg-gradient-to-r from-brand-600 to-indigo-600 text-white font-bold hover:shadow-glow hover:scale-[1.02] transition-all duration-300 text-center mt-auto">Pilih Paket Ini</a>
                                        @endif
                                    @else
                                        <a href="{{ url('/register') }}" class="relative z-10 w-full py-3.5 rounded-xl bg-gradient-to-r from-brand-600 to-indigo-600 text-white font-bold hover:shadow-glow hover:scale-[1.02] transition-all duration-300 text-center mt-auto">Pilih Paket Ini</a>
                                    @endauth
                                </div>
                            @else
                                {{-- CARD STANDAR --}}
                                <div class="bg-white rounded-[2rem] p-8 border border-slate-200 hover:border-brand-300 hover:shadow-card transition-all duration-500 flex flex-col group h-full hover:-translate-y-2 reveal-on-scroll">
                                    <h3 class="text-xl font-bold text-slate-900 mb-2">{{ $paket->nama_paket }}</h3>
                                    <p class="text-sm text-slate-500 mb-6 font-medium">{{ $paket->deskripsi ?? 'Internet cepat tanpa batas.' }}</p>
                                    
                                    <div class="mb-6 flex items-baseline gap-1">
                                        <span class="text-4xl font-black text-slate-900">{{ $hargaRibuan }}rb</span>
                                        <span class="text-slate-500 font-medium">/bulan</span>
                                    </div>

                                    <div class="text-2xl font-black text-slate-800 mb-8 flex items-center gap-2 group-hover:text-brand-600 transition-colors">
                                        {{ filter_var($paket->kecepatan, FILTER_SANITIZE_NUMBER_INT) }} <span class="text-base font-bold text-slate-500">Mbps</span>
                                    </div>
                                    
                                    @if($paket->keypoint)
                                        <ul class="mb-10 space-y-4 text-sm text-slate-600 font-medium">
                                            @foreach(explode(',', $paket->keypoint) as $point)
                                                <li class="flex items-start gap-3">
                                                    <div class="mt-0.5 text-brand-500 group-hover:scale-110 transition-transform">
                                                        <svg class="w-4 h-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg> 
                                                    </div>
                                                    {{ trim($point) }}
                                                </li>
                                            @endforeach
                                        </ul>
                                    @endif

                                    @auth
                                        @if(!$pelanggan || $pelanggan->status !== 'Active')
                                            <button @click="triggerAlert()" type="button" class="w-full py-3.5 rounded-xl bg-slate-50 text-slate-800 font-bold hover:bg-brand-50 hover:text-brand-600 hover:scale-[1.02] hover:border-brand-200 transition-all duration-300 text-center mt-auto border border-slate-200">Pilih Paket</button>
                                        @else
                                            @php
                                                $pesanWa = "Halo CSM.Net, saya ingin berlangganan layanan internet.\n\n"
                                                         . "Nama: " . $pelanggan->nama_pelanggan . "\n"
                                                         . "Alamat: " . $pelanggan->alamat . "\n"
                                                         . "Paket Pilihan: " . $paket->nama_paket;
                                                $linkWa = "https://wa.me/6281234567890?text=" . urlencode($pesanWa);
                                            @endphp
                                            <a href="{{ $linkWa }}" target="_blank" rel="noopener noreferrer" class="w-full py-3.5 rounded-xl bg-slate-50 text-slate-800 font-bold hover:bg-brand-50 hover:text-brand-600 hover:scale-[1.02] hover:border-brand-200 transition-all duration-300 text-center mt-auto border border-slate-200">Pilih Paket</a>
                                        @endif
                                    @else
                                        <a href="{{ url('/register') }}" class="w-full py-3.5 rounded-xl bg-slate-50 text-slate-800 font-bold hover:bg-brand-50 hover:text-brand-600 hover:scale-[1.02] hover:border-brand-200 transition-all duration-300 text-center mt-auto border border-slate-200">Pilih Paket</a>
                                    @endauth
                                </div>
                            @endif

                        @endforeach
                    </div>
                @else
                    <div class="text-center p-12 bg-white border border-dashed border-slate-300 rounded-3xl max-w-2xl mx-auto reveal-on-scroll">
                        <div class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4M12 20V4"/></svg>
                        </div>
                        <p class="text-slate-500 font-medium">Paket layanan sedang diperbarui. Silakan cek kembali nanti.</p>
                    </div>
                @endif

                {{-- INFO BIAYA PEMASANGAN & KABEL (Soft Marketing) --}}
                @if($pakets->count() > 0)
                <div class="mt-12 max-w-3xl mx-auto reveal-on-scroll delay-200">
                    <div class="bg-gradient-to-r from-brand-50 to-indigo-50 border border-brand-100 rounded-2xl p-6 md:p-8 relative overflow-hidden shadow-sm hover:shadow-md transition-shadow">
                        <div class="absolute top-0 right-0 -mt-4 -mr-4 w-24 h-24 bg-brand-200 rounded-full mix-blend-multiply filter blur-xl opacity-50"></div>
                        
                        <div class="relative z-10 flex flex-col sm:flex-row gap-5 items-start">
                            <div class="w-12 h-12 rounded-full bg-white flex items-center justify-center text-brand-600 flex-shrink-0 shadow-sm border border-brand-100">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            </div>
                            <div>
                                <h4 class="text-lg font-bold text-slate-900 mb-2">Satu Kali Investasi untuk Koneksi Optimal</h4>
                                <p class="text-sm text-slate-600 leading-relaxed mb-3">
                                    Untuk memastikan Anda mendapatkan kualitas sinyal fiber optic terbaik dan stabil tanpa gangguan, terdapat biaya instalasi awal sebesar <strong class="text-slate-900 font-bold border-b-2 border-brand-300">Rp 250.000</strong> (hanya dibayar satu kali saat pendaftaran).
                                </p>
                                <p class="text-xs text-slate-500 leading-relaxed bg-white/60 p-3 rounded-lg border border-brand-100/50">
                                    <span class="font-semibold text-slate-700">Fleksibilitas Lokasi:</span> Karena struktur perumahan dan jarak tiang setiap area berbeda, teknisi kami akan mencarikan jalur kabel paling aman & efisien. Jika sewaktu survei diperlukan penarikan kabel ekstra, estimasi biayanya akan kami sampaikan secara transparan <span class="italic font-medium">sebelum</span> proses pemasangan dilanjutkan.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                @endif
                {{-- END INFO BIAYA --}}

            </div>
        </section>

        {{-- Section Stats --}}
        <section class="py-20 relative z-10">
            <div class="max-w-5xl mx-auto px-6">
                <div class="bg-white rounded-[2.5rem] border border-slate-100 shadow-card p-8 lg:p-12 flex flex-col md:flex-row items-center gap-12 relative overflow-hidden group reveal-on-scroll">
                    <div class="absolute -right-20 -top-20 w-64 h-64 bg-brand-50 rounded-full mix-blend-multiply filter blur-3xl opacity-50 group-hover:scale-150 transition-transform duration-1000"></div>
                    
                    <div class="flex-1 relative z-10">
                        <h3 class="text-2xl font-black text-slate-900 mb-4">Performa Jaringan & Tim</h3>
                        <p class="text-slate-500 mb-8 font-medium leading-relaxed">Kami bangga dengan dedikasi tim kami. Skor kepuasan pelanggan dan stabilitas jaringan kami adalah bukti komitmen CSM.Net.</p>
                        
                        <div class="mb-3 flex justify-between text-sm font-bold">
                            <span class="text-slate-700">Network Uptime</span>
                            <span class="text-brand-600">99.9%</span>
                        </div>
                        <div class="w-full bg-slate-100 rounded-full h-3 mb-8 overflow-hidden">
                            <div class="bg-gradient-to-r from-brand-500 to-indigo-500 h-full rounded-full w-0 transition-all duration-1000 ease-out" id="bar-uptime"></div>
                        </div>

                        <div class="mb-3 flex justify-between text-sm font-bold">
                            <span class="text-slate-700">Customer Satisfaction (Teamwork)</span>
                            <span class="text-emerald-500">85%</span>
                        </div>
                        <div class="w-full bg-slate-100 rounded-full h-3 overflow-hidden">
                            <div class="bg-gradient-to-r from-emerald-400 to-emerald-500 h-full rounded-full w-0 transition-all duration-1000 ease-out delay-300" id="bar-csat"></div>
                        </div>
                    </div>
                    <div class="w-full md:w-1/3 text-center border-t md:border-t-0 md:border-l border-slate-100 pt-8 md:pt-0 pl-0 md:pl-12 relative z-10">
                        <div class="text-6xl font-black text-transparent bg-clip-text bg-gradient-to-b from-slate-900 to-slate-500 mb-2">24/7</div>
                        <p class="text-slate-400 font-bold uppercase tracking-widest text-xs">Support Monitoring</p>
                    </div>
                </div>
            </div>
        </section>

        {{-- Section FAQ --}}
        <section id="faq" class="py-24 relative z-10 bg-white border-t border-slate-100">
            <div class="max-w-3xl mx-auto px-6">
                
                <div class="text-center mb-16 reveal-on-scroll">
                    <span class="inline-block py-1.5 px-4 rounded-full bg-brand-50 text-brand-600 font-bold tracking-widest uppercase text-[10px] mb-4 border border-brand-100">Pusat Bantuan</span>
                    <h2 class="text-3xl lg:text-4xl font-black text-slate-900 tracking-tight">Pertanyaan Umum</h2>
                    <p class="mt-4 text-slate-500 font-medium">Jawaban cepat untuk pertanyaan yang sering diajukan.</p>
                </div>

                <div class="space-y-4 reveal-on-scroll delay-100" id="faq-container">
                    @foreach([
                        ['q' => 'Bagaimana cara mendaftar?', 'a' => 'Cukup klik tombol <a href="'.url('/register').'" class="text-brand-600 font-bold hover:underline">Daftar</a> di menu atas, atau hubungi kami via WhatsApp.'],
                        ['q' => 'Berapa lama proses pemasangan?', 'a' => 'Estimasi pemasangan adalah <strong class="text-slate-800">1-3 hari kerja</strong> setelah pembayaran administrasi dikonfirmasi.'],
                        ['q' => 'Apakah ada biaya tersembunyi?', 'a' => 'Tidak ada biaya bulanan yang disembunyikan. Harga paket adalah flat (belum termasuk PPN 11%). Untuk awal, hanya ada biaya instalasi Rp 250.000 dan penyesuaian penarikan kabel (jika ada, akan diinfokan transparan di awal).'],
                        ['q' => 'Bagaimana jika internet gangguan?', 'a' => 'Tim support kami siaga 24/7. Anda bisa melaporkan gangguan dengan menekan tombol <strong class="text-brand-600">Ajukan Komplain</strong> (khusus pelanggan) di pojok kanan bawah layar ini.']
                    ] as $faq)
                    <div class="bg-white border border-slate-200 rounded-2xl hover:border-brand-300 hover:shadow-md transition-all duration-300 group/faq">
                        <button class="w-full px-6 py-5 md:px-8 text-left flex justify-between items-center gap-4 focus:outline-none" onclick="toggleFaq(this)">
                            <span class="font-bold text-slate-800 text-[15px] group-hover/faq:text-brand-600 transition-colors">{{ $faq['q'] }}</span>
                            <div class="flex-shrink-0 w-8 h-8 rounded-full bg-slate-50 flex items-center justify-center text-slate-400 group-hover/faq:bg-brand-50 group-hover/faq:text-brand-600 transition-colors">
                                <svg class="w-5 h-5 transform transition-transform duration-300 faq-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/></svg>
                            </div>
                        </button>
                        <div class="grid grid-rows-[0fr] faq-content">
                            <div class="overflow-hidden">
                                <div class="px-6 pb-6 md:px-8 text-slate-500 font-medium leading-relaxed text-sm">
                                    <p>{!! $faq['a'] !!}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </section>
    </main>

    {{-- Footer --}}
    <footer class="bg-gradient-to-br from-[#574def] to-[#4e44d6] border-t border-white/10 pt-16 pb-8 relative z-10 overflow-hidden">
        <div class="absolute top-0 right-0 w-full h-full pointer-events-none opacity-40">
            <div class="absolute -top-32 -right-32 w-96 h-96 bg-white/20 rounded-full blur-[100px]"></div>
            <div class="absolute -bottom-32 -left-32 w-96 h-96 bg-black/10 rounded-full blur-[100px]"></div>
        </div>

        <div class="max-w-7xl mx-auto px-6 relative z-10">
            <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-5 gap-8 mb-12">
                <div class="col-span-2 lg:col-span-2">
                    <a href="{{ url('/') }}" class="text-2xl font-black text-white tracking-tighter">CSM<span class="text-white/80">.Net</span></a>
                    <p class="mt-4 text-white/80 text-sm font-medium leading-relaxed max-w-xs">
                        Ps. Melayu, Jl. Ahmad Marzuki No.100 Kec. Sambas, Kabupaten Sambas, Kalimantan Barat 79462
                    </p>
                </div>
                
                <div>
                    <h4 class="font-bold text-white mb-4">Perusahaan</h4>
                    <ul class="space-y-3 text-sm text-white/80 font-medium">
                        <li><a href="{{ url('/about') }}" class="hover:text-white hover:translate-x-1 inline-block transition-transform">Tentang Kami</a></li>
                        <li><a href="#" class="hover:text-white hover:translate-x-1 inline-block transition-transform">Karir</a></li>
                        <li><a href="#" class="hover:text-white hover:translate-x-1 inline-block transition-transform">Blog</a></li>
                    </ul>
                </div>

                <div>
                    <h4 class="font-bold text-white mb-4">Layanan</h4>
                    <ul class="space-y-3 text-sm text-white/80 font-medium">
                        <li><a href="#paket" class="hover:text-white hover:translate-x-1 inline-block transition-transform">Internet Rumah</a></li>
                        <li><a href="#paket" class="hover:text-white hover:translate-x-1 inline-block transition-transform">Internet Bisnis</a></li>
                        <li><a href="#" class="hover:text-white hover:translate-x-1 inline-block transition-transform">Dedicated</a></li>
                    </ul>
                </div>

                <div>
                    <h4 class="font-bold text-white mb-4">Bantuan</h4>
                    <ul class="space-y-3 text-sm text-white/80 font-medium">
                        <li><a href="#faq" class="hover:text-white hover:translate-x-1 inline-block transition-transform">Pusat Bantuan</a></li>
                        <li><a href="#" class="hover:text-white hover:translate-x-1 inline-block transition-transform">Kontak</a></li>
                        <li><a href="#" class="hover:text-white hover:translate-x-1 inline-block transition-transform">Cek Area</a></li>
                    </ul>
                </div>
            </div>
            
            <div class="border-t border-white/20 pt-8 flex flex-col md:flex-row justify-between items-center gap-4">
                <p class="text-sm text-white/80 font-medium">&copy; {{ date('Y') }} CSM.Net. All rights reserved.</p>
                <div class="flex gap-4">
                    <a href="#" class="text-white/80 hover:text-white hover:-translate-y-1 transition-all">
                        <span class="sr-only">Instagram</span>
                        <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24"><path fill-rule="evenodd" d="M12.315 2c2.43 0 2.784.013 3.808.06 1.064.049 1.791.218 2.427.465a4.902 4.902 0 011.772 1.153 4.902 4.902 0 011.153 1.772c.247.636.416 1.363.465 2.427.048 1.067.06 1.407.06 4.123v.08c0 2.643-.012 2.987-.06 4.043-.049 1.064-.218 1.791-.465 2.427a4.902 4.902 0 01-1.153 1.772 4.902 4.902 0 01-1.772 1.153c-.636.247-1.363.416-2.427.465-1.067.048-1.407.06-4.123.06h-.08c-2.643 0-2.987-.012-4.043-.06-1.064-.049-1.791-.218-2.427-.465a4.902 4.902 0 01-1.772-1.153 4.902 4.902 0 01-1.153-1.772c-.247-.636-.416-1.363-.465-2.427-.047-1.024-.06-1.379-.06-3.808v-.63c0-2.43.013-2.784.06-3.808.049-1.064.218-1.791.465-2.427a4.902 4.902 0 011.153-1.772 4.902 4.902 0 011.772-1.153c.636-.247 1.363-.416 2.427-.465C9.673 2.013 10.03 2 12.48 2h-.165zm-2.347 5.753a6.16 6.16 0 106.16 6.16 6.161 6.161 0 00-6.16-6.16zM12 9.122a3.486 3.486 0 11-3.486 3.486A3.487 3.487 0 0112 9.122zM19.166 5.86a1.026 1.026 0 11-1.026-1.026 1.026 1.026 0 011.026 1.026z" clip-rule="evenodd" /></svg>
                    </a>
                </div>
            </div>
        </div>
    </footer>

    <script>
        function toggleFaq(button) {
            const content = button.nextElementSibling;
            const icon = button.querySelector('.faq-icon');
            const isAlreadyOpen = content.style.gridTemplateRows === '1fr';

            document.querySelectorAll('.faq-content').forEach(el => { el.style.gridTemplateRows = '0fr'; });
            document.querySelectorAll('.faq-icon').forEach(el => { el.classList.remove('rotate-180'); });

            if (!isAlreadyOpen) {
                content.style.gridTemplateRows = '1fr';
                icon.classList.add('rotate-180');
            }
        }

        document.addEventListener('DOMContentLoaded', () => {
            const observerOptions = {
                root: null,
                rootMargin: '0px',
                threshold: 0.15
            };

            const observer = new IntersectionObserver((entries, observer) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('is-visible');
                        
                        if(entry.target.querySelector('#bar-uptime')) {
                            setTimeout(() => {
                                document.getElementById('bar-uptime').style.width = '99.9%';
                            }, 300);
                            setTimeout(() => {
                                document.getElementById('bar-csat').style.width = '85%';
                            }, 600);
                        }
                        
                        observer.unobserve(entry.target);
                    }
                });
            }, observerOptions);

            document.querySelectorAll('.reveal-on-scroll').forEach(element => {
                observer.observe(element);
            });
            
            setTimeout(() => {
                document.querySelectorAll('.reveal-on-scroll')[0]?.classList.add('is-visible');
            }, 100);
        });
    </script>
</body>
</html>