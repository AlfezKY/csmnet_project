<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Tentang Kami - CSM.TV</title>

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
                        // Warna brand utama yang sama dengan index
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
                        'scroll-right': 'scrollRight 25s linear infinite',
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
                        },
                        scrollRight: {
                            '0%': { transform: 'translateX(-100%)' },
                            '100%': { transform: 'translateX(0)' },
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
        [x-cloak] { display: none !important; }
        
        /* Animasi Scroll Reveal */
        .reveal-on-scroll {
            opacity: 0;
            transform: translateY(30px);
            transition: opacity 0.8s cubic-bezier(0.4, 0, 0.2, 1), transform 0.8s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .reveal-on-scroll.is-visible {
            opacity: 1;
            transform: translateY(0);
        }
        
        /* Delay untuk elemen berurutan */
        .delay-100 { transition-delay: 100ms; }
        .delay-200 { transition-delay: 200ms; }
        .delay-300 { transition-delay: 300ms; }
    </style>
</head>
<body class="text-slate-800 antialiased selection:bg-[#6b46ff] selection:text-white relative overflow-x-hidden">

    {{-- Background Dinamis (Blobs) --}}
    <div class="fixed inset-0 z-[-1] overflow-hidden pointer-events-none bg-[#F8FAFC]">
        <div class="absolute -top-[10%] -left-[10%] w-[40%] h-[40%] rounded-full bg-brand-200/50 mix-blend-multiply filter blur-[100px] animate-blob"></div>
        <div class="absolute top-[20%] -right-[10%] w-[40%] h-[40%] rounded-full bg-purple-200/50 mix-blend-multiply filter blur-[100px] animate-blob" style="animation-delay: 2s;"></div>
        <div class="absolute -bottom-[10%] left-[20%] w-[40%] h-[40%] rounded-full bg-blue-200/50 mix-blend-multiply filter blur-[100px] animate-blob" style="animation-delay: 4s;"></div>
    </div>

    {{-- Navbar Clean Solid --}}
    <nav class="fixed w-full top-0 z-50 bg-white/90 backdrop-blur-md border-b border-slate-100/50 transition-all duration-300">
        <div class="max-w-7xl mx-auto px-6 h-20 flex items-center justify-between">
            <a href="{{ url('/') }}" class="text-2xl font-black tracking-tighter text-slate-900 transition-transform hover:scale-105">
                CSM<span class="text-transparent bg-clip-text bg-gradient-to-r from-[#6b46ff] to-[#b05aff]">.TV</span>
            </a>

            <div class="hidden md:flex items-center gap-10">
                <a href="{{ url('/') }}" class="text-sm font-semibold text-slate-500 hover:text-[#6b46ff] transition-colors">Beranda</a>
                <a href="{{ url('/about') }}" class="text-sm font-semibold text-[#6b46ff] relative after:content-[''] after:absolute after:-bottom-1 after:left-0 after:w-full after:h-0.5 after:bg-[#6b46ff] after:rounded-full transition-colors">Tentang</a>
                <a href="{{ url('/') }}#paket" class="text-sm font-semibold text-slate-500 hover:text-[#6b46ff] transition-colors">Paket Layanan</a>
            </div>

            <div class="hidden md:flex items-center gap-4">
                @auth
                    @if(Auth::user()->role === 'Pelanggan')
                        <span class="text-sm font-bold text-slate-700 mr-2 flex items-center gap-2">
                            <svg class="w-5 h-5 text-brand-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            Halo, {{ explode(' ', Auth::user()->fullname)[0] }}
                        </span>
                        <form action="{{ route('logout') }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="px-5 py-2 text-sm font-bold text-red-600 bg-red-50 hover:bg-red-100 rounded-full transition-colors">Logout</button>
                        </form>
                    @else
                        <a href="{{ route('dashboard') }}" class="px-6 py-2.5 text-sm font-semibold text-white bg-slate-900 rounded-full hover:bg-slate-800 transition shadow-lg">Dashboard Admin</a>
                    @endif
                @else
                    <a href="{{ route('login') }}" class="text-sm font-semibold text-slate-700 hover:text-[#6b46ff] transition-colors">Login</a>
                    <a href="{{ url('/register') }}" class="px-6 py-2.5 text-sm font-semibold text-white bg-gradient-to-r from-[#6b46ff] to-[#b05aff] rounded-full hover:shadow-[0_0_15px_rgba(107,70,255,0.4)] hover:-translate-y-0.5 transition-all duration-300">Daftar</a>
                @endauth
            </div>

            <button class="md:hidden p-2 text-slate-900" onclick="document.getElementById('mobile-menu').classList.toggle('hidden')">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
            </button>
        </div>

        <div id="mobile-menu" class="hidden absolute top-20 left-0 w-full bg-white border-b border-slate-100 p-6 flex flex-col gap-4 shadow-xl md:hidden">
            <a href="{{ url('/') }}" class="block py-2 text-base font-semibold text-slate-600">Beranda</a>
            <a href="{{ url('/about') }}" class="block py-2 text-base font-semibold text-[#6b46ff]">Tentang</a>
            <a href="{{ url('/') }}#paket" class="block py-2 text-base font-semibold text-slate-600">Paket Layanan</a>
            <hr class="border-slate-100">
            
            @auth
                @if(Auth::user()->role === 'Pelanggan')
                    <p class="text-center text-sm text-slate-500 font-medium">Masuk sebagai {{ Auth::user()->fullname }}</p>
                    <form action="{{ route('logout') }}" method="POST" class="w-full">
                        @csrf
                        <button type="submit" class="w-full py-3 text-center bg-red-50 text-red-600 rounded-xl font-bold">Logout</button>
                    </form>
                @else
                    <a href="{{ route('dashboard') }}" class="block w-full py-3 text-center bg-slate-900 text-white rounded-xl font-bold">Dashboard Admin</a>
                @endif
            @else
                <a href="{{ route('login') }}" class="block w-full py-3 text-center border border-slate-200 rounded-xl font-bold text-slate-700 hover:bg-slate-50">Login</a>
                <a href="{{ url('/register') }}" class="block w-full py-3 text-center bg-gradient-to-r from-[#6b46ff] to-[#b05aff] text-white rounded-xl font-bold shadow-md">Daftar Sekarang</a>
            @endauth
        </div>
    </nav>

    <main class="pt-24 lg:pt-32">
        
        {{-- Section Hero About --}}
        <section class="py-10 lg:py-16 overflow-hidden relative">
            <div class="max-w-7xl mx-auto px-6">
                <div class="grid lg:grid-cols-2 gap-16 lg:gap-24 items-center">
                    
                    <div class="relative order-2 lg:order-1 reveal-on-scroll delay-200">
                        <div class="relative z-10 rounded-[2rem] overflow-hidden shadow-[0_20px_50px_-12px_rgba(0,0,0,0.2)] border-4 border-white animate-float">
                            <img src="{{ asset('assets/dummy-card.jpg') }}" alt="Tim CSM TV" class="w-full h-auto object-cover" onerror="this.src='https://placehold.co/800x600?text=CSM+Team'">
                        </div>
                        <div class="absolute bottom-8 left-8 z-20 bg-white px-6 py-5 rounded-2xl shadow-card border border-slate-100 flex items-center gap-4 group hover:scale-105 hover:shadow-glow transition-all duration-300">
                            <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-emerald-50 to-emerald-100 flex items-center justify-center text-emerald-600 group-hover:rotate-12 transition-transform">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </div>
                            <div>
                                <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest mb-0.5">Status Jaringan</p>
                                <p class="text-2xl font-black text-transparent bg-clip-text bg-gradient-to-r from-slate-900 to-slate-700">99.9% Stabil</p>
                            </div>
                        </div>
                    </div>

                    <div class="order-1 lg:order-2 reveal-on-scroll">
                        <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-white border border-slate-200 text-brand-600 text-[10px] font-bold uppercase tracking-widest mb-6 shadow-sm">
                            <span class="w-1.5 h-1.5 rounded-full bg-brand-600"></span>
                            Tentang Kami
                        </div>
                        <h1 class="text-4xl lg:text-[3.5rem] font-black text-slate-900 leading-[1.1] mb-6 tracking-tight">
                            Koneksi Internet Terbaik <br>
                            Untuk <span class="text-transparent bg-clip-text bg-gradient-to-r from-[#6b46ff] to-[#b05aff]">Gaya Hidup Digital.</span>
                        </h1>
                        <p class="text-lg text-slate-500 mb-8 leading-relaxed font-medium">
                            CSM.TV (PT. Citra Sarana Media) adalah penyedia layanan internet (ISP) yang berkomitmen menghadirkan infrastruktur fiber optic modern. Kami hadir untuk memenuhi kebutuhan digital keluarga dan bisnis Anda dengan kecepatan nyata.
                        </p>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-y-5 gap-x-8 mb-10">
                            @foreach(['Support Prioritas 24/7', 'Full Fiber Optic', 'Koneksi Stabil & Cepat', 'Harga Terjangkau', 'Pelayanan Responsif', 'Tanpa Hidden Fees'] as $item)
                            <div class="flex items-center gap-3">
                                <div class="flex-shrink-0 w-6 h-6 rounded-full bg-brand-50 flex items-center justify-center text-brand-600 border border-brand-100">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                </div>
                                <span class="text-slate-700 font-bold text-sm">{{ $item }}</span>
                            </div>
                            @endforeach
                        </div>

                        <div class="flex flex-wrap gap-4">
                            <a href="https://wa.me/6281234567890" target="_blank" class="inline-flex items-center justify-center px-8 py-3.5 text-sm font-bold text-white bg-gradient-to-r from-brand-600 to-indigo-600 rounded-full hover:shadow-glow hover:-translate-y-1 transition-all duration-300">
                                Hubungi Kami
                                <svg class="w-4 h-4 ml-2 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        {{-- Section Partners Logo --}}
        <section class="py-12 border-y border-slate-100 bg-white/50 backdrop-blur-sm overflow-hidden reveal-on-scroll delay-100 mt-10">
            <div class="w-full">
                <p class="text-center text-xs font-bold text-slate-400 uppercase tracking-widest mb-8">Dipercaya Oleh Mitra Terbaik</p>
                <div class="flex gap-8 lg:gap-16 w-full opacity-60 hover:opacity-100 transition-opacity duration-500 cursor-default">
                    <div class="flex flex-shrink-0 gap-8 lg:gap-16 items-center justify-around min-w-full animate-scroll-right">
                        <div class="gs_logo_single--inner"><img loading="lazy" decoding="async" width="140" height="52" src="https://flashnetid.com/wp-content/uploads/2025/08/ICON-PLUS.webp" alt="ICON+" class="grayscale hover:grayscale-0 transition-all duration-300"></div>
                        <div class="gs_logo_single--inner"><img loading="lazy" decoding="async" width="140" height="52" src="https://flashnetid.com/wp-content/uploads/2025/08/INDOSAT.webp" alt="INDOSAT" class="grayscale hover:grayscale-0 transition-all duration-300"></div>
                        <div class="gs_logo_single--inner"><img loading="lazy" decoding="async" width="140" height="52" src="https://flashnetid.com/wp-content/uploads/2025/08/LINTASARTA.webp" alt="LINTASARTA" class="grayscale hover:grayscale-0 transition-all duration-300"></div>             
                        <div class="gs_logo_single--inner"><img loading="lazy" decoding="async" width="140" height="52" src="https://flashnetid.com/wp-content/uploads/2025/08/TELKOM.webp" alt="TELKOM INDONESIA" class="grayscale hover:grayscale-0 transition-all duration-300"></div>
                        <div class="gs_logo_single--inner"><img loading="lazy" decoding="async" width="140" height="52" src="https://flashnetid.com/wp-content/uploads/2025/08/XL-AXIATA.webp" alt="XL AXIATA" class="grayscale hover:grayscale-0 transition-all duration-300"></div>
                    </div>
                    <div class="flex flex-shrink-0 gap-8 lg:gap-16 items-center justify-around min-w-full animate-scroll-right">
                        <div class="gs_logo_single--inner"><img loading="lazy" decoding="async" width="140" height="52" src="https://flashnetid.com/wp-content/uploads/2025/08/ICON-PLUS.webp" alt="ICON+" class="grayscale hover:grayscale-0 transition-all duration-300"></div>
                        <div class="gs_logo_single--inner"><img loading="lazy" decoding="async" width="140" height="52" src="https://flashnetid.com/wp-content/uploads/2025/08/INDOSAT.webp" alt="INDOSAT" class="grayscale hover:grayscale-0 transition-all duration-300"></div>
                        <div class="gs_logo_single--inner"><img loading="lazy" decoding="async" width="140" height="52" src="https://flashnetid.com/wp-content/uploads/2025/08/LINTASARTA.webp" alt="LINTASARTA" class="grayscale hover:grayscale-0 transition-all duration-300"></div>             
                        <div class="gs_logo_single--inner"><img loading="lazy" decoding="async" width="140" height="52" src="https://flashnetid.com/wp-content/uploads/2025/08/TELKOM.webp" alt="TELKOM INDONESIA" class="grayscale hover:grayscale-0 transition-all duration-300"></div>
                        <div class="gs_logo_single--inner"><img loading="lazy" decoding="async" width="140" height="52" src="https://flashnetid.com/wp-content/uploads/2025/08/XL-AXIATA.webp" alt="XL AXIATA" class="grayscale hover:grayscale-0 transition-all duration-300"></div>
                    </div>
                </div>
            </div>
        </section>

        {{-- Section Infrastruktur --}}
        <section class="py-24 relative z-10">
            <div class="max-w-7xl mx-auto px-6">
                <div class="grid lg:grid-cols-2 gap-16 items-center">
                    <div class="reveal-on-scroll">
                        <h2 class="text-3xl lg:text-4xl font-black text-slate-900 mb-6 tracking-tight">
                            Infrastruktur Modern <br>
                            <span class="text-transparent bg-clip-text bg-gradient-to-r from-[#6b46ff] to-[#b05aff]">Tanpa Kompromi.</span>
                        </h2>
                        <div class="space-y-6 text-slate-500 font-medium leading-relaxed">
                            <p>Di dunia yang serba terhubung, internet bukan lagi sekadar pelengkap. Kami berinvestasi besar pada jaringan backbone dan perangkat <em class="font-bold text-slate-700">Last Mile</em> terbaru untuk memastikan Anda mendapatkan bandwidth murni.</p>
                            <p>Tim Network Operation Center (NOC) kami memantau trafik 24 jam sehari untuk memastikan pengalaman streaming 4K, gaming kompetitif, dan video conference Anda berjalan mulus tanpa hambatan.</p>
                        </div>
                        
                        <div class="mt-10 pt-8 border-t border-slate-200">
                            <div class="flex gap-10">
                                <div>
                                    <span class="block text-4xl font-black text-slate-900 mb-1">100<span class="text-brand-600">%</span></span>
                                    <span class="text-xs font-bold text-slate-400 uppercase tracking-widest">Fiber Optic</span>
                                </div>
                                <div>
                                    <span class="block text-4xl font-black text-slate-900 mb-1">24<span class="text-brand-600">/</span>7</span>
                                    <span class="text-xs font-bold text-slate-400 uppercase tracking-widest">Monitoring</span>
                                </div>
                                <div>
                                    <span class="block text-4xl font-black text-slate-900 mb-1">1<span class="text-brand-600">Gbps</span></span>
                                    <span class="text-xs font-bold text-slate-400 uppercase tracking-widest">Max Speed</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="relative reveal-on-scroll delay-200">
                        <div class="absolute inset-0 bg-brand-50 rounded-[2rem] transform rotate-3 scale-95 origin-bottom-right -z-10 transition-transform hover:rotate-6 duration-500"></div>
                        <img src="{{ asset('assets/dummy-card.jpg') }}" alt="Server Room" class="rounded-[2rem] shadow-card w-full object-cover aspect-video lg:aspect-square border border-slate-100" onerror="this.src='https://placehold.co/800x800?text=Infrastruktur+CSM'">
                    </div>
                </div>
            </div>
        </section>

        {{-- Section CTA --}}
        <section class="py-20 relative z-10">
            <div class="max-w-5xl mx-auto px-6">
                <div class="bg-white rounded-[2.5rem] border border-slate-100 shadow-card p-12 text-center relative overflow-hidden group reveal-on-scroll">
                    <div class="absolute -right-20 -top-20 w-64 h-64 bg-[#eef2ff] rounded-full mix-blend-multiply filter blur-3xl opacity-50 group-hover:scale-150 transition-transform duration-1000"></div>
                    <div class="absolute -left-20 -bottom-20 w-64 h-64 bg-brand-50 rounded-full mix-blend-multiply filter blur-3xl opacity-50 group-hover:scale-150 transition-transform duration-1000 delay-300"></div>
                    
                    <div class="relative z-10">
                        <h2 class="text-3xl lg:text-4xl font-black text-slate-900 mb-4 tracking-tight">Siap untuk koneksi yang lebih baik?</h2>
                        <p class="text-slate-500 font-medium mb-8 max-w-2xl mx-auto">Bergabunglah dengan ribuan pelanggan yang telah beralih ke layanan internet fiber optic modern kami.</p>
                        
                        <div class="flex flex-col sm:flex-row justify-center gap-4">
                            <a href="{{ url('/') }}#paket" class="inline-flex items-center justify-center px-8 py-3.5 text-sm font-bold text-white bg-gradient-to-r from-[#6b46ff] to-[#b05aff] rounded-full hover:shadow-[0_0_20px_rgba(107,70,255,0.4)] hover:-translate-y-1 transition-all duration-300">
                                Lihat Pilihan Paket
                            </a>
                            <a href="https://wa.me/6281234567890" target="_blank" class="inline-flex items-center justify-center px-8 py-3.5 text-sm font-bold text-slate-700 bg-white border border-slate-200 rounded-full hover:bg-slate-50 hover:border-brand-300 hover:-translate-y-1 transition-all duration-300 shadow-sm">
                                Konsultasi Gratis
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </section>

    </main>

    {{-- FOOTER UPDATED: Base #574def dengan gradien halus --}}
    <footer class="bg-gradient-to-br from-[#574def] to-[#4e44d6] border-t border-white/10 pt-16 pb-8 relative z-10 overflow-hidden">
        {{-- Efek cahaya/glow transparan di latar belakang footer --}}
        <div class="absolute top-0 right-0 w-full h-full pointer-events-none opacity-40">
            <div class="absolute -top-32 -right-32 w-96 h-96 bg-white/20 rounded-full blur-[100px]"></div>
            <div class="absolute -bottom-32 -left-32 w-96 h-96 bg-black/10 rounded-full blur-[100px]"></div>
        </div>

        <div class="max-w-7xl mx-auto px-6 relative z-10">
            <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-5 gap-8 mb-12">
                <div class="col-span-2 lg:col-span-2">
                    <a href="{{ url('/') }}" class="text-2xl font-black text-white tracking-tighter">CSM<span class="text-white/80">.TV</span></a>
                    <p class="mt-4 text-white/80 text-sm font-medium leading-relaxed max-w-xs">
                        Penyedia layanan internet fiber optic terpercaya. Kami menghubungkan Anda dengan dunia melalui infrastruktur digital terbaik.
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
                        <li><a href="{{ url('/') }}#paket" class="hover:text-white hover:translate-x-1 inline-block transition-transform">Internet Rumah</a></li>
                        <li><a href="{{ url('/') }}#paket" class="hover:text-white hover:translate-x-1 inline-block transition-transform">Internet Bisnis</a></li>
                        <li><a href="#" class="hover:text-white hover:translate-x-1 inline-block transition-transform">Dedicated</a></li>
                    </ul>
                </div>

                <div>
                    <h4 class="font-bold text-white mb-4">Bantuan</h4>
                    <ul class="space-y-3 text-sm text-white/80 font-medium">
                        <li><a href="{{ url('/') }}#faq" class="hover:text-white hover:translate-x-1 inline-block transition-transform">Pusat Bantuan</a></li>
                        <li><a href="#" class="hover:text-white hover:translate-x-1 inline-block transition-transform">Kontak</a></li>
                        <li><a href="#" class="hover:text-white hover:translate-x-1 inline-block transition-transform">Cek Area</a></li>
                    </ul>
                </div>
            </div>
            
            <div class="border-t border-white/20 pt-8 flex flex-col md:flex-row justify-between items-center gap-4">
                <p class="text-sm text-white/80 font-medium">&copy; {{ date('Y') }} CSM.TV. All rights reserved.</p>
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
        // Scroll Reveal Animation Script
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