<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>CSM.TV - Internet Service Provider Terpercaya</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                    },
                    colors: {
                        brand: {
                            50: '#eef2ff',
                            100: '#e0e7ff',
                            600: '#4f46e5', // Indigo utama
                            700: '#4338ca',
                            900: '#312e81',
                        }
                    },
                    boxShadow: {
                        'soft': '0 4px 6px -1px rgba(0, 0, 0, 0.02), 0 2px 4px -1px rgba(0, 0, 0, 0.02)',
                        'glow': '0 0 15px rgba(79, 70, 229, 0.3)'
                    }
                }
            }
        }
    </script>

    <style>
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
        
        /* Accordion Animation */
        .faq-content {
            transition: grid-template-rows 0.3s ease-out;
        }
        .faq-content[aria-expanded="false"] { grid-template-rows: 0fr; }
        .faq-content[aria-expanded="true"] { grid-template-rows: 1fr; }
    </style>
</head>
<body class="bg-white text-slate-800 antialiased selection:bg-brand-600 selection:text-white">

    <nav class="fixed w-full top-0 z-50 bg-white/95 backdrop-blur-sm border-b border-slate-100 transition-all duration-300">
        <div class="max-w-7xl mx-auto px-6 h-20 flex items-center justify-between">
            {{-- Ganti route('home') ke url('/') jika route home belum didefinisikan --}}
            <a href="{{ url('/') }}" class="text-2xl font-extrabold tracking-tighter text-slate-900">
                CSM<span class="text-brand-600">.TV</span>
            </a>

            <div class="hidden md:flex items-center gap-10">
                <a href="{{ url('/') }}" class="text-sm font-semibold text-brand-600">Beranda</a>
                {{-- Gunakan # jika route about belum ada --}}
                <a href="{{ url('/about') }}" class="text-sm font-semibold text-slate-500 hover:text-brand-600 transition-colors">Tentang</a>
                <a href="#paket" class="text-sm font-semibold text-slate-500 hover:text-brand-600 transition-colors">Paket Layanan</a>
            </div>

            <div class="hidden md:flex items-center gap-4">
                @auth
                    @if(Auth::user()->role === 'Pelanggan')
                        <span class="text-sm font-semibold text-slate-700 mr-2">Halo, {{ explode(' ', Auth::user()->fullname)[0] }}</span>
                        <form action="{{ route('logout') }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="px-6 py-2.5 text-sm font-semibold text-white bg-red-600 rounded-full hover:bg-red-700 transition shadow-lg shadow-red-600/20">Logout</button>
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
            <a href="{{ url('/') }}" class="block py-2 text-base font-medium text-brand-600">Beranda</a>
            <a href="#about" class="block py-2 text-base font-medium text-slate-600">Tentang</a>
            <a href="{{ url('/about') }}" class="block py-2 text-base font-medium text-slate-600">Paket Layanan</a>
            <hr class="border-slate-100">
            
            @auth
                @if(Auth::user()->role === 'Pelanggan')
                    <p class="text-center text-sm text-slate-500 font-medium">Masuk sebagai {{ Auth::user()->fullname }}</p>
                    <form action="{{ route('logout') }}" method="POST" class="w-full">
                        @csrf
                        <button type="submit" class="w-full py-3 text-center bg-red-600 text-white rounded-lg font-bold">Logout</button>
                    </form>
                @else
                    <a href="{{ route('dashboard') }}" class="block w-full py-3 text-center bg-slate-900 text-white rounded-lg font-bold">Dashboard Admin</a>
                @endif
            @else
                <a href="{{ route('login') }}" class="block w-full py-3 text-center border border-slate-200 rounded-lg font-bold text-slate-700">Login</a>
                <a href="{{ url('/register') }}" class="block w-full py-3 text-center bg-brand-600 text-white rounded-lg font-bold">Daftar Sekarang</a>
            @endauth
        </div>

            <button class="md:hidden p-2 text-slate-900" onclick="document.getElementById('mobile-menu').classList.toggle('hidden')">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
            </button>
        </div>

        <div id="mobile-menu" class="hidden absolute top-20 left-0 w-full bg-white border-b border-slate-100 p-6 flex flex-col gap-4 shadow-xl md:hidden">
            <a href="{{ url('/') }}" class="block py-2 text-base font-medium text-brand-600">Beranda</a>
            <a href="{{ url('/about') }}" class="block py-2 text-base font-medium text-slate-600">Tentang</a>
            <a href="#paket" class="block py-2 text-base font-medium text-slate-600">Paket Layanan</a>
            <hr class="border-slate-100">
            @guest
                <a href="{{ route('login') }}" class="block w-full py-3 text-center border border-slate-200 rounded-lg font-bold text-slate-700">Masuk</a>
                <a href="{{ url('/register') }}" class="block w-full py-3 text-center bg-brand-600 text-white rounded-lg font-bold">Daftar Sekarang</a>
            @endguest
        </div>
    </nav>

    <main class="pt-20">
        
        <section id="home" class="py-20 lg:py-32 overflow-hidden">
            <div class="max-w-7xl mx-auto px-6">
                <div class="grid lg:grid-cols-2 gap-16 lg:gap-24 items-center">
                    
                    <div class="order-2 lg:order-1">
                        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-brand-50 border border-brand-100 text-brand-700 text-xs font-bold uppercase tracking-wider mb-6">
                            <span class="w-2 h-2 rounded-full bg-brand-600 animate-pulse"></span>
                            Promo Spesial
                        </div>
                        <h1 class="text-4xl lg:text-6xl font-extrabold text-slate-900 leading-[1.1] mb-6 tracking-tight">
                            Paket Internet <br>
                            <span class="text-transparent bg-clip-text bg-gradient-to-r from-brand-600 to-purple-600">Premium & Cepat.</span>
                        </h1>
                        <p class="text-lg text-slate-500 mb-8 leading-relaxed max-w-lg">
                            Nikmati streaming tanpa buffering, gaming tanpa lag, dan download super cepat dengan infrastruktur fiber optic terbaru kami.
                        </p>

                        <div class="flex flex-col sm:flex-row gap-4">
                            <a href="#paket" class="inline-flex items-center justify-center px-8 py-4 text-sm font-bold text-white bg-brand-600 rounded-full hover:bg-brand-700 transition shadow-xl shadow-brand-200">
                                Mulai Berlangganan
                                <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                            </a>
                            <a href="https://wa.me/6281234567890" target="_blank" class="inline-flex items-center justify-center px-8 py-4 text-sm font-bold text-slate-700 border border-slate-200 rounded-full hover:border-brand-600 hover:text-brand-600 transition bg-white">
                                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/></svg>
                                WhatsApp Kami
                            </a>
                        </div>
                        
                        <div class="mt-8 flex items-center gap-2 text-sm text-slate-500">
                            <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            <span>Instalasi Cepat 1-3 Hari Kerja</span>
                        </div>
                    </div>

                    <div class="relative order-1 lg:order-2">
                         <div class="absolute -inset-4 bg-gradient-to-tr from-brand-100 to-purple-50 rounded-full blur-3xl opacity-60 -z-10"></div>
                        {{-- Pastikan gambar ini ada di folder public/assets --}}
                        <img src="{{ asset('assets/dummy-card.jpg') }}" alt="Internet Cepat" class="relative w-full rounded-2xl shadow-2xl shadow-slate-200 transform hover:-translate-y-2 transition duration-500" onerror="this.src='https://placehold.co/600x400?text=CSM+Internet'">
                        
                        <div class="absolute -bottom-6 -left-6 bg-white p-5 rounded-xl shadow-xl border border-slate-100 flex items-center gap-4 animate-bounce-slow max-w-xs">
                            <div class="w-12 h-12 rounded-full bg-brand-50 flex items-center justify-center text-brand-600">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                            </div>
                            <div>
                                <p class="text-xs text-slate-400 font-bold uppercase tracking-wider">Speed Test</p>
                                <p class="text-xl text-slate-900">100 Mbps</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        {{-- Section Features --}}
        <section class="py-20 bg-slate-50 border-y border-slate-100">
            <div class="max-w-7xl mx-auto px-6">
                <div class="text-center mb-16">
                    <h2 class="text-3xl lg:text-4xl font-bold text-slate-900 mb-4">Kenapa Harus CSM.TV?</h2>
                    <p class="text-slate-500 max-w-2xl mx-auto">Kami menggabungkan teknologi terbaik dengan pelayanan sepenuh hati untuk pengalaman internet terbaik Anda.</p>
                </div>

                <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-8">
                    @foreach([
                        ['icon' => 'M13 10V3L4 14h7v7l9-11h-7z', 'title' => 'Internet Stabil', 'desc' => 'Koneksi anti putus dengan teknologi fiber optic murni.'],
                        ['icon' => 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z', 'title' => 'Harga Terjangkau', 'desc' => 'Biaya bulanan transparan, tanpa biaya tersembunyi.'],
                        ['icon' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z', 'title' => 'Support 24/7', 'desc' => 'Bantuan teknis siap sedia kapanpun Anda butuhkan.'],
                        ['icon' => 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z', 'title' => 'Teknisi Ahli', 'desc' => 'Pemasangan rapi dan profesional oleh tim bersertifikat.']
                    ] as $item)
                    <div class="bg-white p-8 rounded-2xl border border-slate-100 shadow-sm hover:shadow-lg hover:border-brand-200 transition duration-300 group">
                        <div class="w-12 h-12 bg-brand-50 rounded-xl flex items-center justify-center text-brand-600 mb-6 group-hover:scale-110 transition-transform">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $item['icon'] }}"/></svg>
                        </div>
                        <h3 class="text-xl font-bold text-slate-900 mb-2">{{ $item['title'] }}</h3>
                        <p class="text-slate-500 text-sm leading-relaxed">{{ $item['desc'] }}</p>
                    </div>
                    @endforeach
                </div>
            </div>
        </section>

        {{-- Section Pricing --}}
        <section id="paket" class="py-24">
            <div class="max-w-7xl mx-auto px-6">
                <div class="text-center mb-16">
                    <span class="text-brand-600 font-bold tracking-wider uppercase text-sm mb-2 block">Pilihan Paket</span>
                    <h2 class="text-3xl lg:text-5xl font-extrabold text-slate-900">Paket Internet Rumah</h2>
                </div>

                @if($pakets->count() > 0)
                    <div class="grid md:grid-cols-2 @if($pakets->count() <= 3) lg:grid-cols-3 max-w-5xl mx-auto @else lg:grid-cols-4 @endif gap-6">
                        
                        @foreach($pakets as $paket)
                            @php
                                // Logika untuk nentuin mana paket yang di-Highlight (Warna Gelap)
                                $isFeatured = false;
                                if ($pakets->count() <= 3 && $loop->iteration == 2) $isFeatured = true;
                                if ($pakets->count() == 4 && $loop->iteration == 3) $isFeatured = true;

                                // Format harga dari 150000 jadi 150
                                $hargaRibuan = floor($paket->harga / 1000);
                            @endphp

                            @if($isFeatured)
                                {{-- CARD PREMIUM (GELAP) --}}
                                <div class="bg-slate-900 rounded-3xl p-6 border border-slate-800 shadow-2xl relative flex flex-col transform lg:-translate-y-4">
                                    <div class="absolute top-0 right-0 bg-brand-600 text-white text-xs font-bold px-3 py-1 rounded-bl-xl rounded-tr-2xl uppercase tracking-wider">Terlaris</div>
                                    <h3 class="text-lg font-bold text-white mb-1">{{ $paket->nama_paket }}</h3>
                                    <p class="text-sm text-slate-400 mb-6">{{ $paket->deskripsi ?? 'Internet cepat tanpa batas.' }}</p>
                                    
                                    <div class="mb-6">
                                        <span class="text-4xl font-extrabold text-white">{{ $hargaRibuan }}rb</span>
                                        <span class="text-slate-400">/bulan</span>
                                    </div>
                                    <div class="text-3xl font-black text-brand-400 mb-6 flex items-center gap-2 text-slate-100">
                                        {{ filter_var($paket->kecepatan, FILTER_SANITIZE_NUMBER_INT) }} <span class="text-sm font-medium text-slate-400">Mbps</span>
                                    </div>
                                    
                                    @if($paket->keypoint)
                                        <ul class="mb-8 space-y-3 text-sm text-slate-300">
                                            @foreach(explode(',', $paket->keypoint) as $point)
                                                <li class="flex items-center gap-2">
                                                    <svg class="w-4 h-4 text-brand-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg> 
                                                    {{ trim($point) }}
                                                </li>
                                            @endforeach
                                        </ul>
                                    @endif

                                    <a href="{{ url('/register') }}" class="w-full py-3 rounded-xl bg-brand-600 text-white font-bold hover:bg-brand-700 transition text-center shadow-lg shadow-brand-900/50 mt-auto">Pilih Paket Ini</a>
                                </div>
                            @else
                                {{-- CARD STANDAR (PUTIH) --}}
                                <div class="bg-white rounded-3xl p-6 border border-slate-200 hover:border-brand-300 hover:shadow-xl transition-all duration-300 flex flex-col">
                                    <h3 class="text-lg font-bold text-slate-900 mb-1">{{ $paket->nama_paket }}</h3>
                                    <p class="text-sm text-slate-500 mb-6">{{ $paket->deskripsi ?? 'Internet cepat tanpa batas.' }}</p>
                                    
                                    <div class="mb-6">
                                        <span class="text-4xl font-extrabold text-slate-900">{{ $hargaRibuan }}rb</span>
                                        <span class="text-slate-500">/bulan</span>
                                    </div>
                                    <div class="text-2xl font-black text-brand-600 mb-6 flex items-center gap-2">
                                        {{ filter_var($paket->kecepatan, FILTER_SANITIZE_NUMBER_INT) }} <span class="text-sm font-medium text-slate-500">Mbps</span>
                                    </div>
                                    
                                    @if($paket->keypoint)
                                        <ul class="mb-8 space-y-3 text-sm text-slate-600">
                                            @foreach(explode(',', $paket->keypoint) as $point)
                                                <li class="flex items-center gap-2">
                                                    <svg class="w-4 h-4 text-brand-600 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg> 
                                                    {{ trim($point) }}
                                                </li>
                                            @endforeach
                                        </ul>
                                    @endif

                                    <a href="{{ url('/register') }}" class="w-full py-3 rounded-xl border-2 border-slate-200 text-slate-700 font-bold hover:border-brand-600 hover:text-brand-600 transition text-center mt-auto">Pilih Paket</a>
                                </div>
                            @endif

                        @endforeach
                    </div>
                @else
                    {{-- Kalau admin belum nambahin paket yang is_show = 1 --}}
                    <div class="text-center p-12 bg-slate-50 border border-dashed border-slate-200 rounded-3xl">
                        <p class="text-slate-500 font-medium">Paket layanan sedang diperbarui. Silakan cek kembali nanti.</p>
                    </div>
                @endif
            </div>
        </section>

        {{-- Section Stats --}}
        <section class="py-20 bg-slate-50 border-t border-slate-100">
            <div class="max-w-5xl mx-auto px-6">
                <div class="bg-white rounded-3xl shadow-xl p-8 lg:p-12 flex flex-col md:flex-row items-center gap-12">
                    <div class="flex-1">
                        <h3 class="text-2xl font-bold text-slate-900 mb-4">Performa Jaringan & Tim</h3>
                        <p class="text-slate-500 mb-6 leading-relaxed">Kami bangga dengan dedikasi tim kami. Skor kepuasan pelanggan dan stabilitas jaringan kami adalah bukti komitmen CSM.TV.</p>
                        
                        <div class="mb-2 flex justify-between text-sm font-bold">
                            <span class="text-slate-700">Network Uptime</span>
                            <span class="text-brand-600">99.9%</span>
                        </div>
                        <div class="w-full bg-slate-100 rounded-full h-3 overflow-hidden mb-6">
                            <div class="bg-brand-600 h-full rounded-full" style="width: 99.9%"></div>
                        </div>

                        <div class="mb-2 flex justify-between text-sm font-bold">
                            <span class="text-slate-700">Customer Satisfaction (Teamwork)</span>
                            <span class="text-brand-600">85%</span>
                        </div>
                        <div class="w-full bg-slate-100 rounded-full h-3 overflow-hidden">
                            <div class="bg-brand-600 h-full rounded-full" style="width: 85%"></div>
                        </div>
                    </div>
                    <div class="w-full md:w-1/3 text-center border-l border-slate-100 pl-0 md:pl-12">
                        <div class="text-5xl font-black text-slate-900 mb-2">24/7</div>
                        <p class="text-slate-500 font-medium">Support Monitoring</p>
                    </div>
                </div>
            </div>
        </section>

        {{-- Section FAQ --}}
        <section id="faq" class="py-24 bg-white">
            <div class="max-w-3xl mx-auto px-6">
                
                <div class="text-center mb-16">
                    <span class="text-brand-600 font-bold tracking-wider uppercase text-sm mb-2 block">Pusat Bantuan</span>
                    <h2 class="text-3xl lg:text-4xl font-extrabold text-slate-900">Pertanyaan Umum</h2>
                    <p class="mt-4 text-slate-500">Jawaban cepat untuk pertanyaan yang sering diajukan.</p>
                </div>

                <div class="space-y-4" id="faq-container">
                    @foreach([
                        ['q' => 'Bagaimana cara mendaftar?', 'a' => 'Cukup klik tombol <a href="'.url('/register').'" class="text-brand-600 font-semibold hover:underline">Daftar</a> di menu atas, atau hubungi kami via WhatsApp.'],
                        ['q' => 'Berapa lama proses pemasangan?', 'a' => 'Estimasi pemasangan adalah <strong>1-3 hari kerja</strong> setelah pembayaran administrasi dikonfirmasi.'],
                        ['q' => 'Apakah ada biaya tersembunyi?', 'a' => 'Tidak ada. Harga yang tertera adalah harga bulanan flat (belum termasuk PPN 11%).'],
                        ['q' => 'Bagaimana jika internet gangguan?', 'a' => 'Tim support kami siaga 24/7. Anda bisa melaporkan gangguan melalui WhatsApp Customer Service atau Dashboard Pelanggan.']
                    ] as $faq)
                    <div class="group border border-slate-100 rounded-3xl bg-slate-50 hover:bg-white hover:shadow-lg hover:shadow-slate-200/50 transition-all duration-300">
                        <button class="w-full px-6 py-5 md:px-8 text-left flex justify-between items-start md:items-center gap-4 focus:outline-none" onclick="toggleFaq(this)">
                            <span class="font-bold text-slate-800 text-lg group-hover:text-brand-600 transition-colors">{{ $faq['q'] }}</span>
                            <div class="flex-shrink-0 w-8 h-8 rounded-full bg-white border border-slate-200 flex items-center justify-center text-slate-400 group-hover:border-brand-200 group-hover:text-brand-600 transition-all duration-300">
                                <svg class="w-5 h-5 transform transition-transform duration-300 faq-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                            </div>
                        </button>
                        <div class="grid grid-rows-[0fr] transition-[grid-template-rows] duration-300 ease-out faq-content">
                            <div class="overflow-hidden">
                                <div class="px-6 pb-6 md:px-8 text-slate-600 leading-relaxed">
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

    <footer class="bg-white border-t border-slate-100 pt-16 pb-8">
        <div class="max-w-7xl mx-auto px-6">
            <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-5 gap-8 mb-12">
                <div class="col-span-2 lg:col-span-2">
                    <a href="{{ url('/') }}" class="text-2xl font-extrabold text-slate-900 tracking-tighter">CSM.TV</a>
                    <p class="mt-4 text-slate-500 text-sm leading-relaxed max-w-xs">
                        Penyedia layanan internet fiber optic terpercaya. Kami menghubungkan Anda dengan dunia melalui infrastruktur digital terbaik.
                    </p>
                </div>
                
                <div>
                    <h4 class="font-bold text-slate-900 mb-4">Perusahaan</h4>
                    <ul class="space-y-2 text-sm text-slate-500">
                        <li><a href="#about" class="hover:text-brand-600 transition">Tentang Kami</a></li>
                        <li><a href="#" class="hover:text-brand-600 transition">Karir</a></li>
                        <li><a href="#" class="hover:text-brand-600 transition">Blog</a></li>
                    </ul>
                </div>

                <div>
                    <h4 class="font-bold text-slate-900 mb-4">Layanan</h4>
                    <ul class="space-y-2 text-sm text-slate-500">
                        <li><a href="#paket" class="hover:text-brand-600 transition">Internet Rumah</a></li>
                        <li><a href="#paket" class="hover:text-brand-600 transition">Internet Bisnis</a></li>
                        <li><a href="#" class="hover:text-brand-600 transition">Dedicated</a></li>
                    </ul>
                </div>

                <div>
                    <h4 class="font-bold text-slate-900 mb-4">Bantuan</h4>
                    <ul class="space-y-2 text-sm text-slate-500">
                        <li><a href="#faq" class="hover:text-brand-600 transition">Pusat Bantuan</a></li>
                        <li><a href="#" class="hover:text-brand-600 transition">Kontak</a></li>
                        <li><a href="#" class="hover:text-brand-600 transition">Cek Area</a></li>
                    </ul>
                </div>
            </div>
            
            <div class="border-t border-slate-100 pt-8 flex flex-col md:flex-row justify-between items-center gap-4">
                <p class="text-sm text-slate-400">&copy; {{ date('Y') }} CSM.TV. All rights reserved.</p>
                <div class="flex gap-4">
                    {{-- Social Icons --}}
                    <a href="#" class="text-slate-400 hover:text-brand-600 transition">
                        <span class="sr-only">Instagram</span>
                        <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24"><path fill-rule="evenodd" d="M12.315 2c2.43 0 2.784.013 3.808.06 1.064.049 1.791.218 2.427.465a4.902 4.902 0 011.772 1.153 4.902 4.902 0 011.153 1.772c.247.636.416 1.363.465 2.427.048 1.067.06 1.407.06 4.123v.08c0 2.643-.012 2.987-.06 4.043-.049 1.064-.218 1.791-.465 2.427a4.902 4.902 0 01-1.153 1.772 4.902 4.902 0 01-1.772 1.153c-.636.247-1.363.416-2.427.465-1.067.048-1.407.06-4.123.06h-.08c-2.643 0-2.987-.012-4.043-.06-1.064-.049-1.791-.218-2.427-.465a4.902 4.902 0 01-1.772-1.153 4.902 4.902 0 01-1.153-1.772c-.247-.636-.416-1.363-.465-2.427-.047-1.024-.06-1.379-.06-3.808v-.63c0-2.43.013-2.784.06-3.808.049-1.064.218-1.791.465-2.427a4.902 4.902 0 011.153-1.772 4.902 4.902 0 011.772-1.153c.636-.247 1.363-.416 2.427-.465C9.673 2.013 10.03 2 12.48 2h-.165zm-2.347 5.753a6.16 6.16 0 106.16 6.16 6.161 6.161 0 00-6.16-6.16zM12 9.122a3.486 3.486 0 11-3.486 3.486A3.487 3.487 0 0112 9.122zM19.166 5.86a1.026 1.026 0 11-1.026-1.026 1.026 1.026 0 011.026 1.026z" clip-rule="evenodd" /></svg>
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

            // 1. Tutup SEMUA item lain terlebih dahulu
            document.querySelectorAll('.faq-content').forEach(el => {
                el.style.gridTemplateRows = '0fr';
            });
            document.querySelectorAll('.faq-icon').forEach(el => {
                el.classList.remove('rotate-180');
            });

            // 2. Buka yang diklik jika belum terbuka
            if (!isAlreadyOpen) {
                content.style.gridTemplateRows = '1fr';
                icon.classList.add('rotate-180');
            }
        }
    </script>
</body>
</html>