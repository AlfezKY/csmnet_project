<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Tentang Kami - CSM.Net</title>

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
<body class="text-slate-800 antialiased selection:bg-[#6b46ff] selection:text-white relative overflow-x-hidden">

    {{-- Background Dinamis --}}
    <div class="fixed inset-0 z-[-1] overflow-hidden pointer-events-none bg-[#F8FAFC]">
        <div class="absolute -top-[10%] -left-[10%] w-[40%] h-[40%] rounded-full bg-brand-200/50 mix-blend-multiply filter blur-[100px] animate-blob"></div>
        <div class="absolute top-[20%] -right-[10%] w-[40%] h-[40%] rounded-full bg-purple-200/50 mix-blend-multiply filter blur-[100px] animate-blob" style="animation-delay: 2s;"></div>
        <div class="absolute -bottom-[10%] left-[20%] w-[40%] h-[40%] rounded-full bg-blue-200/50 mix-blend-multiply filter blur-[100px] animate-blob" style="animation-delay: 4s;"></div>
    </div>

    {{-- Navbar --}}
    <nav class="fixed w-full top-0 z-50 bg-white/90 backdrop-blur-md border-b border-slate-100/50 transition-all duration-300">
        <div class="max-w-7xl mx-auto px-6 h-20 flex items-center justify-between">
            <a href="{{ url('/') }}" class="text-2xl font-black tracking-tighter text-slate-900 transition-transform hover:scale-105">
                CSM<span class="text-transparent bg-clip-text bg-gradient-to-r from-[#6b46ff] to-[#b05aff]">.Net</span>
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
    </nav>

    {{-- Main Container --}}
    <main class="pt-24 lg:pt-32 pb-10">
        
        {{-- SECTION HERO BARU (STYLE OVERLAPPING IMAGES) --}}
        <section class="relative pb-16 pt-4 lg:pt-8 overflow-hidden">
            <div class="max-w-7xl mx-auto px-6">
                <div class="grid lg:grid-cols-2 gap-12 lg:gap-8 items-center">
                    
                    {{-- Kolom Teks (Kiri) --}}
                    <div class="order-2 lg:order-1 reveal-on-scroll">
                        <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-brand-50 border border-brand-100 text-brand-600 text-xs font-bold uppercase tracking-widest mb-6 shadow-sm">
                            <span class="w-2 h-2 rounded-full bg-brand-600 animate-pulse"></span>
                            Mengenal CSM.Net
                        </div>
                        
                        <h1 class="text-4xl lg:text-[3.5rem] font-black text-slate-900 leading-[1.1] mb-6 tracking-tight">
                            Lebih dari sekadar <br>
                            <span class="text-transparent bg-clip-text bg-gradient-to-r from-[#6b46ff] to-[#b05aff]">Koneksi Internet.</span>
                        </h1>
                        
                        <p class="text-lg text-slate-500 font-medium leading-relaxed max-w-lg mb-8">
                           Sebagai evolusi CSM TV, CSM Net kini hadir khusus memenuhi kebutuhan digital Anda melalui infrastruktur Fiber Optic yang stabil. Kami menjamin pengalaman internetan tanpa drama buffering untuk kerja, streaming, hingga gaming dengan dukungan penuh 24/7. Saatnya beralih ke layanan yang lebih fokus dan bisa Anda andalkan setiap saat.
                        </p>

                        {{-- Stats di Bawah Teks --}}
                        <div class="grid grid-cols-3 gap-6 pt-8 border-t border-slate-200/60 max-w-lg">
                            <div>
                                <span class="block text-3xl lg:text-4xl font-black text-slate-900 mb-1">5<span class="text-brand-600">+</span></span>
                                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Tahun Melayani</span>
                            </div>
                            <div>
                                <span class="block text-3xl lg:text-4xl font-black text-slate-900 mb-1">100<span class="text-brand-600">%</span></span>
                                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Fiber Optic</span>
                            </div>
                            <div>
                                <span class="block text-3xl lg:text-4xl font-black text-slate-900 mb-1">24<span class="text-brand-600">/</span>7</span>
                                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">NOC Support</span>
                            </div>
                        </div>
                    </div>

                    {{-- Kolom Foto (Kanan - Style Aesthetic Overlap) --}}
                    <div class="order-1 lg:order-2 relative w-full max-w-lg mx-auto lg:max-w-none lg:w-full h-[400px] sm:h-[500px] lg:h-[550px] flex items-center justify-center lg:justify-end reveal-on-scroll delay-200">
                        
                        {{-- Background Blurs --}}
                        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-64 h-64 bg-brand-300 rounded-full mix-blend-multiply filter blur-[80px] opacity-60 animate-blob"></div>
                        <div class="absolute top-1/3 right-1/4 w-64 h-64 bg-indigo-300 rounded-full mix-blend-multiply filter blur-[80px] opacity-60 animate-blob" style="animation-delay: 2s;"></div>

                        {{-- Foto Utama (Gede, di atas-kanan) --}}
                        <div class="absolute right-0 lg:right-4 top-0 w-[95%] md:w-[90%] h-[95%] rounded-[2rem] overflow-hidden shadow-2xl border-[6px] border-white z-10 group">
                            <img src="{{ asset('assets/dummy-card1.jpg') }}" class="w-full h-full object-cover " alt="Tim CSM" onerror="this.src='https://placehold.co/600x800?text=Tim+CSM'">
                            <div class="absolute inset-0 bg-brand-900/10 "></div>
                        </div>

                        
                        {{-- Floating Badge Status --}}
                        <div class="absolute top-8 left-4 lg:-left-6 z-30 bg-white/95 backdrop-blur-md px-6 py-4 rounded-2xl shadow-xl border border-slate-100 flex items-center gap-4 animate-float">
                            <div class="w-12 h-12 rounded-full bg-brand-50 flex items-center justify-center text-brand-600">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                            </div>
                            <div>
                                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-0.5">SLA</p>
                                <p class="text-xl font-black text-slate-900">99,5 %</p>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </section>

       {{-- Partner Logos (Minimalis, Centered, Modern & Vibrant Spotlight) --}}
        <section class="py-20 relative bg-white overflow-hidden reveal-on-scroll">
            
            {{-- Efek Glow/Cahaya Ngejreng di Latar Belakang (Soft tapi Vibrant) --}}
            <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-full max-w-3xl h-64 bg-gradient-to-r from-brand-300/20 via-purple-400/20 to-brand-300/20 blur-[80px] rounded-full pointer-events-none"></div>

            <div class="max-w-7xl mx-auto px-6 relative z-10 text-center">
                
                {{-- Judul Minimalis dengan Teks Gradasi --}}
                <div class="inline-flex items-center justify-center gap-4 mb-16">
                    <span class="w-12 h-[2px] bg-gradient-to-r from-transparent to-brand-500 rounded-full"></span>
                    <p class="text-sm md:text-base font-black bg-clip-text text-transparent bg-gradient-to-r from-brand-600 to-purple-600 uppercase tracking-[0.2em]">
                        Mitra Infrastruktur Terbaik
                    </p>
                    <span class="w-12 h-[2px] bg-gradient-to-l from-transparent to-brand-500 rounded-full"></span>
                </div>
                
                {{-- Container Logo Terpusat --}}
                <div class="flex flex-col sm:flex-row justify-center items-center gap-16 md:gap-32">
                    
                    {{-- Logo Indosat --}}
                    <div class="group relative flex justify-center items-center cursor-pointer">
                        {{-- Efek cahaya tambahan saat di-hover --}}
                        <div class="absolute inset-0 bg-brand-500/15 rounded-full blur-2xl scale-0 group-hover:scale-150 transition-transform duration-500"></div>
                        
                        <img width="220" height="80" 
                             class="relative z-10 object-contain drop-shadow-sm group-hover:scale-110 group-hover:-translate-y-2 group-hover:drop-shadow-xl transition-all duration-300 ease-out" 
                             src="{{ asset('assets/indosat.png') }}" 
                             alt="INDOSAT">
                    </div>

                    {{-- Logo Lintasarta --}}
                    <div class="group relative flex justify-center items-center cursor-pointer">
                        {{-- Efek cahaya tambahan saat di-hover --}}
                        <div class="absolute inset-0 bg-brand-500/15 rounded-full blur-2xl scale-0 group-hover:scale-150 transition-transform duration-500"></div>
                        
                        <img width="220" height="80" 
                             class="relative z-10 object-contain drop-shadow-sm group-hover:scale-110 group-hover:-translate-y-2 group-hover:drop-shadow-xl transition-all duration-300 ease-out" 
                             src="{{ asset('assets/lintasarta.png') }}" 
                             alt="LINTASARTA">
                    </div>
                    
                </div>
            </div>
        </section>

        {{-- Visi Misi Section (Desain Sleek) --}}
        <section class="py-20 relative z-10 bg-white">
            <div class="max-w-6xl mx-auto px-6">
                <div class="grid md:grid-cols-2 gap-10 lg:gap-12">
                    {{-- Visi --}}
                    <div class="bg-slate-50 p-10 lg:p-14 rounded-[3rem] border border-slate-100 shadow-sm hover:shadow-card hover:-translate-y-2 transition-all duration-300 reveal-on-scroll relative overflow-hidden group">
                        <div class="absolute top-0 right-0 w-40 h-40 bg-brand-200 rounded-full mix-blend-multiply filter blur-3xl opacity-30 group-hover:scale-150 transition-transform duration-700"></div>
                        
                        <div class="w-16 h-16 bg-gradient-to-br from-brand-500 to-brand-600 rounded-2xl flex items-center justify-center text-white mb-8 shadow-lg shadow-brand-200 relative z-10 group-hover:rotate-6 transition-transform">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                        </div>
                        <h2 class="text-3xl font-black text-slate-900 mb-6 relative z-10">Visi Kami</h2>
                        <p class="text-slate-600 leading-relaxed font-medium text-lg relative z-10">
                            Menjadi penyedia layanan internet dan hiburan digital terdepan yang mendobrak batas, mendorong produktivitas, dan menghadirkan konektivitas ngebut tanpa kompromi untuk masyarakat luas.
                        </p>
                    </div>

                    {{-- Misi --}}
                    <div class="bg-gradient-to-br from-brand-600 to-indigo-700 p-10 lg:p-14 rounded-[3rem] border border-brand-500 shadow-glow hover:-translate-y-2 transition-all duration-300 reveal-on-scroll delay-100 relative overflow-hidden group">
                        <div class="absolute top-0 right-0 w-40 h-40 bg-white rounded-full mix-blend-overlay filter blur-3xl opacity-20 group-hover:scale-150 transition-transform duration-700"></div>

                        <div class="w-16 h-16 bg-white/10 backdrop-blur-sm border border-white/20 rounded-2xl flex items-center justify-center text-white mb-8 relative z-10 group-hover:-rotate-6 transition-transform">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                        </div>
                        <h2 class="text-3xl font-black text-white mb-6 relative z-10">Misi Kami</h2>
                        <ul class="space-y-5 text-white/90 font-medium text-lg leading-relaxed relative z-10">
                            <li class="flex items-start gap-4">
                                <div class="mt-2 w-2.5 h-2.5 rounded-full bg-brand-300 flex-shrink-0 shadow-[0_0_10px_rgba(165,180,252,0.8)]"></div>
                                <span>Membangun infrastruktur <strong>Fiber Optic murni</strong> yang tangguh di setiap sudut jangkauan.</span>
                            </li>
                            <li class="flex items-start gap-4">
                                <div class="mt-2 w-2.5 h-2.5 rounded-full bg-brand-300 flex-shrink-0 shadow-[0_0_10px_rgba(165,180,252,0.8)]"></div>
                                <span>Memberikan dedikasi support teknis yang responsif, nyata, dan anti ribet.</span>
                            </li>
                            <li class="flex items-start gap-4">
                                <div class="mt-2 w-2.5 h-2.5 rounded-full bg-brand-300 flex-shrink-0 shadow-[0_0_10px_rgba(165,180,252,0.8)]"></div>
                                <span>Menyediakan harga transparan, wajar, tanpa jebakan *hidden fee*.</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </section>

        {{-- Core Values (Nilai Perusahaan) --}}
        <section class="py-24 relative z-10 bg-[#F8FAFC]">
            <div class="max-w-7xl mx-auto px-6">
                <div class="text-center mb-16 reveal-on-scroll">
                    <h2 class="text-3xl lg:text-4xl font-black text-slate-900 tracking-tight mb-4">Nilai yang Kami Pegang</h2>
                    <p class="text-slate-500 font-medium text-lg">Prinsip kerja yang membuat kami berbeda dan tetap dipercaya.</p>
                </div>

                <div class="grid md:grid-cols-3 gap-8">
                    @foreach([
                        ['icon' => 'M13 10V3L4 14h7v7l9-11h-7z', 'title' => 'Cepat & Handal', 'desc' => 'Kami percaya waktu Anda berharga. Koneksi lelet bukan budaya kami.'],
                        ['icon' => 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z', 'title' => 'Integritas Tinggi', 'desc' => 'Apa yang Anda bayar, itu yang Anda dapatkan. Transparan di setiap layanan.'],
                        ['icon' => 'M14 10h4.764a2 2 0 011.789 2.894l-3.5 7A2 2 0 0115.263 21h-4.017c-.163 0-.326-.02-.485-.06L7 20m7-10V5a2 2 0 00-2-2h-.095c-.5 0-.905.405-.905.905 0 .714-.211 1.412-.608 2.006L7 11v9m7-10h-2M7 20H5a2 2 0 01-2-2v-6a2 2 0 012-2h2.5', 'title' => 'Fokus Pelanggan', 'desc' => 'Bukan sekadar jualan putus, kepuasan jangka panjang Anda adalah target utama kami.']
                    ] as $key => $val)
                    <div class="bg-white p-8 rounded-[2rem] border border-slate-100 shadow-sm hover:shadow-card hover:-translate-y-1 transition-all duration-300 text-center group reveal-on-scroll delay-[{{ $key * 100 }}ms]">
                        <div class="w-20 h-20 mx-auto bg-brand-50 rounded-full flex items-center justify-center text-brand-600 mb-6 group-hover:scale-110 group-hover:bg-brand-600 group-hover:text-white transition-all duration-300">
                            <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $val['icon'] }}"/></svg>
                        </div>
                        <h3 class="text-xl font-bold text-slate-900 mb-3">{{ $val['title'] }}</h3>
                        <p class="text-slate-500 leading-relaxed font-medium text-sm">{{ $val['desc'] }}</p>
                    </div>
                    @endforeach
                </div>
            </div>
        </section>

        {{-- Timeline Perjalanan Section --}}
        <section class="py-24 relative z-10 bg-white">
            <div class="max-w-4xl mx-auto px-6">
                <div class="text-center mb-20 reveal-on-scroll">
                    <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-slate-50 border border-slate-200 text-slate-600 text-xs font-bold uppercase tracking-widest mb-4 shadow-sm">Jejak Langkah</div>
                    <h2 class="text-3xl lg:text-4xl font-black text-slate-900 tracking-tight">Perjalanan CSM.Net</h2>
                </div>

                <div class="space-y-12 relative before:absolute before:inset-0 before:ml-5 md:before:mx-auto md:before:translate-x-0 before:h-full before:w-1 before:bg-gradient-to-b before:from-brand-100 before:via-brand-300 before:to-brand-100">
                    
                    {{-- Item 1 --}}
                    <div class="relative flex items-center justify-between md:justify-normal md:odd:flex-row-reverse group reveal-on-scroll">
                        <div class="flex items-center justify-center w-12 h-12 rounded-full bg-white border-4 border-brand-100 group-hover:border-brand-500 group-hover:scale-110 shadow-sm absolute left-0 md:left-1/2 md:-translate-x-1/2 transition-all duration-300 z-10">
                            <div class="w-4 h-4 bg-brand-600 rounded-full group-hover:animate-ping"></div>
                        </div>
                        <div class="w-[calc(100%-4rem)] md:w-[calc(50%-3rem)] ml-14 md:ml-0 p-8 bg-slate-50 rounded-[2rem] border border-slate-100 shadow-sm group-hover:shadow-card group-hover:-translate-y-1 transition-all duration-300">
                            <span class="inline-block px-3 py-1 bg-brand-100 text-brand-600 text-xs font-bold rounded-full mb-4">Awal Mula</span>
                            <h3 class="text-2xl font-bold text-slate-900 mb-3">Langkah Pertama</h3>
                            <p class="text-slate-500 leading-relaxed font-medium">Berawal dari inisiatif sederhana untuk menyediakan koneksi lokal yang lebih stabil di area sekitar, CSM.Net menarik kabel perdananya.</p>
                        </div>
                    </div>

                    {{-- Item 2 --}}
                    <div class="relative flex items-center justify-between md:justify-normal md:odd:flex-row-reverse group reveal-on-scroll">
                        <div class="flex items-center justify-center w-12 h-12 rounded-full bg-white border-4 border-brand-100 group-hover:border-brand-500 group-hover:scale-110 shadow-sm absolute left-0 md:left-1/2 md:-translate-x-1/2 transition-all duration-300 z-10">
                            <div class="w-4 h-4 bg-brand-600 rounded-full group-hover:animate-ping"></div>
                        </div>
                        <div class="w-[calc(100%-4rem)] md:w-[calc(50%-3rem)] ml-14 md:ml-0 p-8 bg-slate-50 rounded-[2rem] border border-slate-100 shadow-sm group-hover:shadow-card group-hover:-translate-y-1 transition-all duration-300">
                            <span class="inline-block px-3 py-1 bg-brand-100 text-brand-600 text-xs font-bold rounded-full mb-4">Evolusi Teknologi</span>
                            <h3 class="text-2xl font-bold text-slate-900 mb-3">Migrasi Full Fiber Optic</h3>
                            <p class="text-slate-500 leading-relaxed font-medium">Meninggalkan teknologi lawas, kami merombak total infrastruktur menjadi *Fiber to the Home* (FTTH) demi menjamin kecepatan dan kestabilan anti-*lag*.</p>
                        </div>
                    </div>

                    {{-- Item 3 --}}
                    <div class="relative flex items-center justify-between md:justify-normal md:odd:flex-row-reverse group reveal-on-scroll">
                        <div class="flex items-center justify-center w-12 h-12 rounded-full bg-white border-4 border-brand-100 group-hover:border-brand-500 group-hover:scale-110 shadow-sm absolute left-0 md:left-1/2 md:-translate-x-1/2 transition-all duration-300 z-10">
                            <div class="w-4 h-4 bg-brand-600 rounded-full group-hover:animate-ping"></div>
                        </div>
                        <div class="w-[calc(100%-4rem)] md:w-[calc(50%-3rem)] ml-14 md:ml-0 p-8 bg-gradient-to-br from-[#6b46ff] to-[#b05aff] rounded-[2rem] border border-brand-500 shadow-glow group-hover:-translate-y-1 transition-all duration-300 text-white">
                            <span class="inline-block px-3 py-1 bg-white/20 text-white text-xs font-bold rounded-full mb-4 backdrop-blur-sm">Masa Depan</span>
                            <h3 class="text-2xl font-bold mb-3">Terus Berkembang</h3>
                            <p class="text-white/80 leading-relaxed font-medium">Kini melayani ribuan pelanggan, mengekspansi jaringan, dan terus berkomitmen memberikan inovasi pelayanan paling asik untuk Anda.</p>
                        </div>
                    </div>

                </div>
            </div>
        </section>

        {{-- CTA Section Bersih --}}
        <section class="py-24 relative z-10">
            <div class="max-w-5xl mx-auto px-6">
                <div class="bg-gradient-to-br from-slate-900 to-slate-800 rounded-[3rem] border border-slate-700 shadow-2xl p-12 lg:p-16 text-center relative overflow-hidden group reveal-on-scroll">
                    <div class="absolute -right-20 -top-20 w-80 h-80 bg-brand-500/20 rounded-full mix-blend-screen filter blur-3xl group-hover:scale-125 transition-transform duration-1000"></div>
                    <div class="absolute -left-20 -bottom-20 w-80 h-80 bg-indigo-500/20 rounded-full mix-blend-screen filter blur-3xl group-hover:scale-125 transition-transform duration-1000 delay-300"></div>
                    
                    <div class="relative z-10">
                        <h2 class="text-3xl lg:text-5xl font-black text-white mb-6 tracking-tight">Mulai Perjalanan Digitalmu Sekarang</h2>
                        <p class="text-slate-300 font-medium mb-10 text-lg max-w-2xl mx-auto">Tinggalkan koneksi lambat dan mulailah nikmati internet sesungguhnya bersama CSM.Net.</p>
                        
                        <div class="flex flex-col sm:flex-row justify-center gap-5">
                            <a href="{{ url('/') }}#paket" class="inline-flex items-center justify-center px-10 py-4 text-sm font-bold text-slate-900 bg-white rounded-full hover:shadow-[0_0_25px_rgba(255,255,255,0.3)] hover:-translate-y-1 transition-all duration-300">
                                Cek Paket Berlangganan
                            </a>
                            <a href="https://wa.me/6281234567890" target="_blank" class="inline-flex items-center justify-center px-10 py-4 text-sm font-bold text-white bg-white/10 backdrop-blur-md border border-white/20 rounded-full hover:bg-white/20 hover:-translate-y-1 transition-all duration-300">
                                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/></svg>
                                Tanya Admin via WhatsApp
                            </a>
                        </div>
                    </div>
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
                        Ps. Melayu, 
                        Jl. Ahmad Marzuki No.100  Kec. Sambas, Kabupaten Sambas, Kalimantan Barat 79462
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
                <p class="text-sm text-white/80 font-medium">&copy; {{ date('Y') }} CSM.Net. All rights reserved.</p>
                <div class="flex gap-4">
                    <a href="#" class="text-white/80 hover:text-white hover:-translate-y-1 transition-all">
                        <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24"><path fill-rule="evenodd" d="M12.315 2c2.43 0 2.784.013 3.808.06 1.064.049 1.791.218 2.427.465a4.902 4.902 0 011.772 1.153 4.902 4.902 0 011.153 1.772c.247.636.416 1.363.465 2.427.048 1.067.06 1.407.06 4.123v.08c0 2.643-.012 2.987-.06 4.043-.049 1.064-.218 1.791-.465 2.427a4.902 4.902 0 01-1.153 1.772 4.902 4.902 0 01-1.772 1.153c-.636.247-1.363.416-2.427.465-1.067.048-1.407.06-4.123.06h-.08c-2.643 0-2.987-.012-4.043-.06-1.064-.049-1.791-.218-2.427-.465a4.902 4.902 0 01-1.772-1.153 4.902 4.902 0 01-1.153-1.772c-.247-.636-.416-1.363-.465-2.427-.047-1.024-.06-1.379-.06-3.808v-.63c0-2.43.013-2.784.06-3.808.049-1.064.218-1.791.465-2.427a4.902 4.902 0 011.153-1.772 4.902 4.902 0 011.772-1.153c.636-.247 1.363-.416 2.427-.465C9.673 2.013 10.03 2 12.48 2h-.165zm-2.347 5.753a6.16 6.16 0 106.16 6.16 6.161 6.161 0 00-6.16-6.16zM12 9.122a3.486 3.486 0 11-3.486 3.486A3.487 3.487 0 0112 9.122zM19.166 5.86a1.026 1.026 0 11-1.026-1.026 1.026 1.026 0 011.026 1.026z" clip-rule="evenodd" /></svg>
                    </a>
                </div>
            </div>
        </div>
    </footer>

    <script>
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
                const firstElement = document.querySelectorAll('.reveal-on-scroll')[0];
                if(firstElement) firstElement.classList.add('is-visible');
            }, 100);
        });
    </script>
</body>
</html>