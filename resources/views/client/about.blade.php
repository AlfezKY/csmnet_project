<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Tentang Kami - CSM.TV</title>

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
                    // --- KONFIGURASI ANIMASI CAROUSEL ---
                    animation: {
                        'scroll-right': 'scrollRight 25s linear infinite',
                    },
                    keyframes: {
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
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
    </style>
</head>
<body class="bg-white text-slate-800 antialiased selection:bg-brand-600 selection:text-white">

    <nav class="fixed w-full top-0 z-50 bg-white/95 backdrop-blur-sm border-b border-slate-100 transition-all duration-300">
        <div class="max-w-7xl mx-auto px-6 h-20 flex items-center justify-between">
            <a href="{{ url('/') }}" class="text-2xl font-extrabold tracking-tighter text-slate-900">
                CSM<span class="text-brand-600">.TV</span>
            </a>

            <div class="hidden md:flex items-center gap-10">
                <a href="{{ url('/') }}" class="text-sm font-semibold text-slate-500 hover:text-brand-600 transition-colors">Beranda</a>
                <a href="{{ url('/about') }}" class="text-sm font-semibold text-brand-600">Tentang</a>
                <a href="{{ url('/') }}#paket" class="text-sm font-semibold text-slate-500 hover:text-brand-600 transition-colors">Paket Layanan</a>
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
            <a href="{{ url('/') }}" class="block py-2 text-base font-medium text-slate-600">Beranda</a>
            <a href="{{ url('/about') }}" class="block py-2 text-base font-medium text-brand-600">Tentang</a>
            <a href="{{ url('/') }}#paket" class="block py-2 text-base font-medium text-slate-600">Paket Layanan</a>
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
    </nav>

    <main class="pt-20">
        
        <section class="py-20 lg:py-28 overflow-hidden">
            <div class="max-w-7xl mx-auto px-6">
                <div class="grid lg:grid-cols-2 gap-16 lg:gap-24 items-center">
                    
                    <div class="relative order-2 lg:order-1">
                        <div class="relative z-10 rounded-2xl overflow-hidden shadow-2xl shadow-slate-200">
                            <img src="{{ asset('assets/dummy-card.jpg') }}" alt="Tim CSM TV" class="w-full h-auto object-cover transform hover:scale-105 transition duration-700">
                        </div>
                        <div class="absolute -bottom-6 -right-6 w-2/3 h-2/3 bg-brand-50 rounded-2xl -z-10"></div>
                        <div class="absolute bottom-8 left-8 z-20 bg-white p-4 rounded-xl shadow-xl border border-slate-100 flex items-center gap-3 animate-bounce-slow">
                            <div class="w-10 h-10 rounded-full bg-green-100 flex items-center justify-center text-green-600">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </div>
                            <div>
                                <p class="text-xs text-slate-500 font-semibold uppercase">Status Jaringan</p>
                                <p class="text-sm font-bold text-slate-900">99.9% Stabil</p>
                            </div>
                        </div>
                    </div>

                    <div class="order-1 lg:order-2">
                        <span class="text-brand-600 font-bold tracking-wider uppercase text-sm mb-2 block">Tentang Kami</span>
                        <h1 class="text-4xl lg:text-5xl font-extrabold text-slate-900 leading-[1.15] mb-6">
                            Koneksi Internet Terbaik <br>
                            Untuk <span class="text-transparent bg-clip-text bg-gradient-to-r from-brand-600 to-purple-600">Gaya Hidup Digital.</span>
                        </h1>
                        <p class="text-lg text-slate-500 mb-8 leading-relaxed">
                            CSM.TV (PT. Citra Sarana Media) adalah penyedia layanan internet (ISP) yang berkomitmen menghadirkan infrastruktur fiber optic modern. Kami hadir untuk memenuhi kebutuhan digital keluarga dan bisnis Anda dengan kecepatan nyata.
                        </p>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-y-4 gap-x-8 mb-10">
                            @foreach(['Support Prioritas 24/7', 'Full Fiber Optic', 'Koneksi Stabil & Cepat', 'Harga Terjangkau', 'Pelayanan Responsif', 'Tanpa Hidden Fees'] as $item)
                            <div class="flex items-center gap-3">
                                <div class="flex-shrink-0 w-5 h-5 rounded-full bg-brand-600 flex items-center justify-center text-white">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                </div>
                                <span class="text-slate-700 font-medium text-sm lg:text-base">{{ $item }}</span>
                            </div>
                            @endforeach
                        </div>

                        <div class="flex flex-wrap gap-4">
                            <a href="https://wa.me/6281234567890" class="inline-flex items-center justify-center px-8 py-4 text-sm font-bold text-brand-600 border-2 border-brand-600 rounded-full hover:bg-brand-600 hover:text-white transition-all duration-300">
                                Hubungi Kami
                                <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="py-12 border-y border-slate-100 bg-slate-50/50 overflow-hidden">
            <div class="w-full">
                <p class="text-center text-sm font-bold text-slate-400 uppercase tracking-widest mb-8">Dipercaya Oleh Mitra Terbaik</p>
                <div class="flex gap-8 lg:gap-16 w-full">
                    <div class="flex flex-shrink-0 gap-8 lg:gap-16 items-center justify-around min-w-full animate-scroll-right">
                        <div class="gs_logo_single--inner"><img loading="lazy" decoding="async" width="140" height="52" src="https://flashnetid.com/wp-content/uploads/2025/08/ICON-PLUS.webp" alt="ICON+"></div>
                        <div class="gs_logo_single--inner"><img loading="lazy" decoding="async" width="140" height="52" src="https://flashnetid.com/wp-content/uploads/2025/08/INDOSAT.webp" alt="INDOSAT"></div>
                        <div class="gs_logo_single--inner"><img loading="lazy" decoding="async" width="140" height="52" src="https://flashnetid.com/wp-content/uploads/2025/08/LINTASARTA.webp" alt="LINTASARTA"></div>             
                        <div class="gs_logo_single--inner"><img loading="lazy" decoding="async" width="140" height="52" src="https://flashnetid.com/wp-content/uploads/2025/08/TELKOM.webp" alt="TELKOM INDONESIA"></div>
                        <div class="gs_logo_single--inner"><img loading="lazy" decoding="async" width="140" height="52" src="https://flashnetid.com/wp-content/uploads/2025/08/XL-AXIATA.webp" alt="XL AXIATA"></div>
                    </div>
                    <div class="flex flex-shrink-0 gap-8 lg:gap-16 items-center justify-around min-w-full animate-scroll-right">
                        <div class="gs_logo_single--inner"><img loading="lazy" decoding="async" width="140" height="52" src="https://flashnetid.com/wp-content/uploads/2025/08/ICON-PLUS.webp" alt="ICON+"></div>
                        <div class="gs_logo_single--inner"><img loading="lazy" decoding="async" width="140" height="52" src="https://flashnetid.com/wp-content/uploads/2025/08/INDOSAT.webp" alt="INDOSAT"></div>
                        <div class="gs_logo_single--inner"><img loading="lazy" decoding="async" width="140" height="52" src="https://flashnetid.com/wp-content/uploads/2025/08/LINTASARTA.webp" alt="LINTASARTA"></div>             
                        <div class="gs_logo_single--inner"><img loading="lazy" decoding="async" width="140" height="52" src="https://flashnetid.com/wp-content/uploads/2025/08/TELKOM.webp" alt="TELKOM INDONESIA"></div>
                        <div class="gs_logo_single--inner"><img loading="lazy" decoding="async" width="140" height="52" src="https://flashnetid.com/wp-content/uploads/2025/08/XL-AXIATA.webp" alt="XL AXIATA"></div>
                    </div>
                </div>
            </div>
        </section>

        <section class="py-20 lg:py-28">
            <div class="max-w-7xl mx-auto px-6">
                <div class="grid lg:grid-cols-2 gap-16 items-center">
                    <div>
                        <h2 class="text-3xl lg:text-4xl font-extrabold text-slate-900 mb-6">
                            Infrastruktur Modern <br>
                            Tanpa Kompromi.
                        </h2>
                        <div class="space-y-6 text-slate-600 text-lg leading-relaxed">
                            <p>Di dunia yang serba terhubung, internet bukan lagi sekadar pelengkap. Kami berinvestasi besar pada jaringan backbone dan perangkat <em>Last Mile</em> terbaru untuk memastikan Anda mendapatkan bandwidth murni.</p>
                            <p>Tim Network Operation Center (NOC) kami memantau trafik 24 jam sehari untuk memastikan pengalaman streaming 4K, gaming kompetitif, dan video conference Anda berjalan mulus.</p>
                        </div>
                        
                        <div class="mt-8 pt-8 border-t border-slate-100">
                            <div class="flex gap-12">
                                <div>
                                    <span class="block text-3xl font-extrabold text-brand-600">100%</span>
                                    <span class="text-sm text-slate-500 font-medium">Fiber Optic</span>
                                </div>
                                <div>
                                    <span class="block text-3xl font-extrabold text-brand-600">24/7</span>
                                    <span class="text-sm text-slate-500 font-medium">Monitoring</span>
                                </div>
                                <div>
                                    <span class="block text-3xl font-extrabold text-brand-600">1Gbps</span>
                                    <span class="text-sm text-slate-500 font-medium">Max Speed</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="relative">
                        <div class="absolute inset-0 bg-slate-100 rounded-2xl transform rotate-3 scale-95 origin-bottom-right -z-10"></div>
                        <img src="{{ asset('assets/dummy-card.jpg') }}" alt="Server Room" class="rounded-2xl shadow-xl w-full object-cover aspect-video lg:aspect-square">
                    </div>
                </div>
            </div>
        </section>

        <section class="py-16 bg-slate-900">
            <div class="max-w-7xl mx-auto px-6 text-center">
                <h2 class="text-3xl font-bold text-white mb-6">Siap untuk koneksi yang lebih baik?</h2>
                <p class="text-slate-400 mb-8 max-w-2xl mx-auto">Bergabunglah dengan ribuan pelanggan yang telah beralih ke layanan internet fiber optic modern kami.</p>
                <div class="flex justify-center gap-4">
                    <a href="{{ url('/') }}#paket" class="px-8 py-3 bg-white text-slate-900 font-bold rounded-full hover:bg-brand-50 transition">Lihat Paket</a>
                    <a href="https://wa.me/6281234567890" target="_blank" class="px-8 py-3 border border-slate-600 text-white font-bold rounded-full hover:bg-slate-800 transition">Konsultasi Gratis</a>
                </div>
            </div>
        </section>
    </main>

    <footer class="bg-white border-t border-slate-100 pt-16 pb-8">
        <div class="max-w-7xl mx-auto px-6">
            <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-5 gap-8 mb-12">
                <div class="col-span-2 lg:col-span-2">
                    <a href="{{ url('/') }}" class="text-2xl font-extrabold text-slate-900 tracking-tighter">CSM.TV</a>
                    <p class="mt-4 text-slate-500 text-sm leading-relaxed max-w-xs">Penyedia layanan internet fiber optic terpercaya. Kami menghubungkan Anda dengan dunia melalui infrastruktur digital terbaik.</p>
                </div>
                
                <div>
                    <h4 class="font-bold text-slate-900 mb-4">Perusahaan</h4>
                    <ul class="space-y-2 text-sm text-slate-500">
                        <li><a href="{{ url('/about') }}" class="hover:text-brand-600 transition">Tentang Kami</a></li>
                        <li><a href="#" class="hover:text-brand-600 transition">Karir</a></li>
                        <li><a href="#" class="hover:text-brand-600 transition">Blog</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-bold text-slate-900 mb-4">Layanan</h4>
                    <ul class="space-y-2 text-sm text-slate-500">
                        <li><a href="{{ url('/') }}#paket" class="hover:text-brand-600 transition">Internet Rumah</a></li>
                        <li><a href="{{ url('/') }}#paket" class="hover:text-brand-600 transition">Internet Bisnis</a></li>
                        <li><a href="#" class="hover:text-brand-600 transition">Dedicated</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-bold text-slate-900 mb-4">Bantuan</h4>
                    <ul class="space-y-2 text-sm text-slate-500">
                        <li><a href="{{ url('/') }}#faq" class="hover:text-brand-600 transition">Pusat Bantuan</a></li>
                        <li><a href="#" class="hover:text-brand-600 transition">Kontak</a></li>
                        <li><a href="#" class="hover:text-brand-600 transition">Cek Area</a></li>
                    </ul>
                </div>
            </div>
            
            <div class="border-t border-slate-100 pt-8 flex flex-col md:flex-row justify-between items-center gap-4">
                <p class="text-sm text-slate-400">&copy; {{ date('Y') }} CSM.TV. All rights reserved.</p>
                <div class="flex gap-4">
                    <a href="#" class="text-slate-400 hover:text-brand-600 transition">
                        <span class="sr-only">Instagram</span>
                        <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24"><path fill-rule="evenodd" d="M12.315 2c2.43 0 2.784.013 3.808.06 1.064.049 1.791.218 2.427.465a4.902 4.902 0 011.772 1.153 4.902 4.902 0 011.153 1.772c.247.636.416 1.363.465 2.427.048 1.067.06 1.407.06 4.123v.08c0 2.643-.012 2.987-.06 4.043-.049 1.064-.218 1.791-.465 2.427a4.902 4.902 0 01-1.153 1.772 4.902 4.902 0 01-1.772 1.153c-.636.247-1.363.416-2.427.465-1.067.048-1.407.06-4.123.06h-.08c-2.643 0-2.987-.012-4.043-.06-1.064-.049-1.791-.218-2.427-.465a4.902 4.902 0 01-1.772-1.153 4.902 4.902 0 01-1.153-1.772c-.247-.636-.416-1.363-.465-2.427-.047-1.024-.06-1.379-.06-3.808v-.63c0-2.43.013-2.784.06-3.808.049-1.064.218-1.791.465-2.427a4.902 4.902 0 011.153-1.772 4.902 4.902 0 011.772-1.153c.636-.247 1.363-.416 2.427-.465C9.673 2.013 10.03 2 12.48 2h-.165zm-2.347 5.753a6.16 6.16 0 106.16 6.16 6.161 6.161 0 00-6.16-6.16zM12 9.122a3.486 3.486 0 11-3.486 3.486A3.487 3.487 0 0112 9.122zM19.166 5.86a1.026 1.026 0 11-1.026-1.026 1.026 1.026 0 011.026 1.026z" clip-rule="evenodd" /></svg>
                    </a>
                </div>
            </div>
        </div>
    </footer>
</body>
</html>