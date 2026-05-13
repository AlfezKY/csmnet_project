<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'CSMNET')</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,500,600,700" rel="stylesheet" />
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Plus Jakarta Sans', 'sans-serif'],
                    },
                },
            },
        }
    </script>

    <style>
        [x-cloak] { display: none !important; }
        .sidebar-transition { transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1); }
        
        aside::-webkit-scrollbar { width: 4px; }
        aside::-webkit-scrollbar-track { background: transparent; }
        aside::-webkit-scrollbar-thumb { background: #e5e7eb; border-radius: 10px; }
    </style>
</head>
<body class="bg-[#f8fafc] text-slate-900 font-sans antialiased">

    <div x-data="{ sidebarOpen: true }" class="flex h-screen overflow-hidden">
        
        <aside 
            :class="sidebarOpen ? 'w-72' : 'w-20'" 
            class="sidebar-transition hidden md:flex flex-col bg-white border-r border-slate-200 h-full overflow-y-auto z-20 shadow-sm relative">
            
            <div class="h-20 flex items-center border-b border-slate-50 mb-2 overflow-hidden flex-shrink-0 transition-all duration-300" :class="sidebarOpen ? 'px-8 justify-start' : 'justify-center'">
                <span x-show="sidebarOpen" class="text-2xl font-bold tracking-tight text-slate-800 whitespace-nowrap">
                    CSM<span class="text-blue-600">NET</span>
                </span>
                <span x-show="!sidebarOpen" x-cloak class="text-2xl font-bold tracking-tight text-blue-600">
                    CS
                </span>
            </div>

            <nav class="flex-1 px-4 space-y-8 py-4">
                
                @if(auth()->user()->role == 'Owner')
                
                {{-- GRUP: RANGKUMAN --}}
                <div>
                    <div class="flex items-center px-3 mb-4">
                        <div x-show="!sidebarOpen" class="w-full h-[1px] bg-slate-100"></div>
                        <p x-show="sidebarOpen" class="text-[11px] font-bold text-slate-400 uppercase tracking-[0.15em] whitespace-nowrap">Rangkuman</p>
                    </div>
                    <div class="space-y-1.5">
                        <a href="{{ route('dashboard') }}" title="Dashboard" class="group flex items-center gap-3 rounded-xl py-3 px-3.5 text-sm font-semibold sidebar-transition {{ request()->routeIs('dashboard') ? 'bg-blue-600 text-white shadow-md shadow-blue-100' : 'text-slate-500 hover:bg-slate-50 hover:text-blue-600' }}">
                            <svg class="w-5 h-5 flex-shrink-0 {{ request()->routeIs('dashboard') ? 'text-white' : 'text-slate-400 group-hover:text-blue-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                            <span x-show="sidebarOpen" class="whitespace-nowrap">Dashboard</span>
                        </a>
                        <a href="{{ route('laporan.index') }}" title="Laporan" class="group flex items-center gap-3 rounded-xl py-3 px-3.5 text-sm font-semibold sidebar-transition {{ request()->routeIs('laporan.*') ? 'bg-blue-600 text-white shadow-md shadow-blue-100' : 'text-slate-500 hover:bg-slate-50 hover:text-blue-600' }}">
                            <svg class="w-5 h-5 flex-shrink-0 {{ request()->routeIs('laporan.*') ? 'text-white' : 'text-slate-400 group-hover:text-blue-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4V7M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                            <span x-show="sidebarOpen" class="whitespace-nowrap">Laporan</span>
                        </a>
                    </div>
                </div>

                {{-- GRUP: KEUANGAN --}}
                <div>
                    <div class="flex items-center px-3 mb-4">
                        <div x-show="!sidebarOpen" class="w-full h-[1px] bg-slate-100"></div>
                        <p x-show="sidebarOpen" class="text-[11px] font-bold text-slate-400 uppercase tracking-[0.15em] whitespace-nowrap">Keuangan</p>
                    </div>
                    <div class="space-y-1.5">
                        <a href="{{ route('transaksi.index') }}" title="Catatan Pemasukkan" class="group flex items-center gap-3 rounded-xl py-3 px-3.5 text-sm font-semibold sidebar-transition {{ request()->routeIs('transaksi.*') ? 'bg-blue-600 text-white shadow-md shadow-blue-100' : 'text-slate-500 hover:bg-slate-50 hover:text-blue-600' }}">
                            <svg class="w-5 h-5 flex-shrink-0 {{ request()->routeIs('transaksi.*') ? 'text-white' : 'text-slate-400 group-hover:text-blue-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
                            <span x-show="sidebarOpen" class="whitespace-nowrap">Catatan Pemasukkan</span>
                        </a>
                        <a href="{{ route('pengeluaran.index') }}" title="Catatan Pengeluaran" class="group flex items-center gap-3 rounded-xl py-3 px-3.5 text-sm font-semibold sidebar-transition {{ request()->routeIs('pengeluaran.*') ? 'bg-blue-600 text-white shadow-md shadow-blue-100' : 'text-slate-500 hover:bg-slate-50 hover:text-blue-600' }}">
                            <svg class="w-5 h-5 flex-shrink-0 {{ request()->routeIs('pengeluaran.*') ? 'text-white' : 'text-slate-400 group-hover:text-blue-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            <span x-show="sidebarOpen" class="whitespace-nowrap">Catatan Pengeluaran</span>
                        </a>
                        <a href="{{ route('cashflow.index') }}" title="Cashflow" class="group flex items-center gap-3 rounded-xl py-3 px-3.5 text-sm font-semibold sidebar-transition {{ request()->routeIs('cashflow.*') ? 'bg-blue-600 text-white shadow-md shadow-blue-100' : 'text-slate-500 hover:bg-slate-50 hover:text-blue-600' }}">
                            <svg class="w-5 h-5 flex-shrink-0 {{ request()->routeIs('cashflow.*') ? 'text-white' : 'text-slate-400 group-hover:text-blue-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                            <span x-show="sidebarOpen" class="whitespace-nowrap">Cashflow</span>
                        </a>
                    </div>
                </div>

                <div>
                    <div class="flex items-center px-3 mb-4">
                        <div x-show="!sidebarOpen" class="w-full h-[1px] bg-slate-100"></div>
                        <p x-show="sidebarOpen" class="text-[11px] font-bold text-slate-400 uppercase tracking-[0.15em] whitespace-nowrap">MASTER</p>
                    </div>
                    <div class="space-y-1.5">
                        <a href="{{ route('users.index') }}" title="Manajemen Akun" class="group flex items-center gap-3 rounded-xl py-3 px-3.5 text-sm font-semibold sidebar-transition {{ request()->routeIs('users.*') ? 'bg-blue-600 text-white shadow-md shadow-blue-100' : 'text-slate-500 hover:bg-slate-50 hover:text-blue-600' }}">
                            <svg class="w-5 h-5 flex-shrink-0 {{ request()->routeIs('users.*') ? 'text-white' : 'text-slate-400 group-hover:text-blue-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                            <span x-show="sidebarOpen" class="whitespace-nowrap">Manajemen Akun</span>
                        </a>
                    </div>
                </div>
                @elseif(auth()->user()->role == 'Admin')

                <div>
                    <a href="{{ route('dashboard') }}" title="Dashboard" class="group flex items-center gap-3 rounded-xl py-3 px-3.5 text-sm font-semibold sidebar-transition {{ request()->routeIs('dashboard') ? 'bg-blue-600 text-white shadow-md shadow-blue-100' : 'text-slate-500 hover:bg-slate-50 hover:text-blue-600' }}">
                        <svg class="w-5 h-5 flex-shrink-0 {{ request()->routeIs('dashboard') ? 'text-white' : 'text-slate-400 group-hover:text-blue-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                        <span x-show="sidebarOpen" class="whitespace-nowrap">Dashboard</span>
                    </a>
                </div>

                {{-- GRUP: PELANGGAN --}}
                <div>
                    <div class="flex items-center px-3 mb-4">
                        <div x-show="!sidebarOpen" class="w-full h-[1px] bg-slate-100"></div>
                        <p x-show="sidebarOpen" class="text-[11px] font-bold text-slate-400 uppercase tracking-[0.15em] whitespace-nowrap">Pelanggan</p>
                    </div>
                    <div class="space-y-1.5">
                        <a href="{{ route('pelanggan.index') }}" title="Data Pelanggan" class="group flex items-center gap-3 rounded-xl py-3 px-3.5 text-sm font-semibold sidebar-transition {{ request()->routeIs('pelanggan.*') ? 'bg-blue-600 text-white shadow-md shadow-blue-100' : 'text-slate-500 hover:bg-slate-50 hover:text-blue-600' }}">
                            <svg class="w-5 h-5 flex-shrink-0 {{ request()->routeIs('pelanggan.*') ? 'text-white' : 'text-slate-400 group-hover:text-blue-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                            <span x-show="sidebarOpen" class="whitespace-nowrap">Data Pelanggan</span>
                        </a>
                        
                        <a href="{{ route('approval.index') }}" title="Approval Pelanggan" class="relative group flex items-center gap-3 rounded-xl py-3 px-3.5 text-sm font-semibold sidebar-transition {{ request()->routeIs('approval.*') ? 'bg-blue-600 text-white shadow-md shadow-blue-100' : 'text-slate-500 hover:bg-slate-50 hover:text-blue-600' }}">
                            <svg class="w-5 h-5 flex-shrink-0 {{ request()->routeIs('approval.*') ? 'text-white' : 'text-slate-400 group-hover:text-blue-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            <span x-show="sidebarOpen" class="whitespace-nowrap">Approval Pelanggan</span>
                            @php $pendingCount = \App\Models\Pelanggan::where('status', 'Pending')->count(); @endphp
                            @if($pendingCount > 0)
                                <span x-show="sidebarOpen" class="ml-auto bg-red-100 text-red-600 text-[10px] font-bold px-2 py-0.5 rounded-full">{{ $pendingCount }}</span>
                                <span x-show="!sidebarOpen" class="absolute top-2 right-2 w-2 h-2 bg-red-500 rounded-full border-2 border-white"></span>
                            @endif
                        </a>
                        
                        <a href="{{ route('tagihan.index') }}" title="Tagihan Pelanggan" class="relative group flex items-center gap-3 rounded-xl py-3 px-3.5 text-sm font-semibold sidebar-transition {{ request()->routeIs('tagihan.*') ? 'bg-blue-600 text-white shadow-md shadow-blue-100' : 'text-slate-500 hover:bg-slate-50 hover:text-blue-600' }}">
                            <svg class="w-5 h-5 flex-shrink-0 {{ request()->routeIs('tagihan.*') ? 'text-white' : 'text-slate-400 group-hover:text-blue-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                            <span x-show="sidebarOpen" class="whitespace-nowrap">Tagihan Pelanggan</span>
                            @php $tagihanCount = \App\Models\Pelanggan::where('status', 'Active')->where('status_pembayaran', 'Belum Lunas')->count(); @endphp
                            @if($tagihanCount > 0)
                                <span x-show="sidebarOpen" class="ml-auto bg-red-100 text-red-600 text-[10px] font-bold px-2 py-0.5 rounded-full">{{ $tagihanCount }}</span>
                                <span x-show="!sidebarOpen" class="absolute top-2 right-2 w-2 h-2 bg-red-500 rounded-full border-2 border-white"></span>
                            @endif
                        </a>
                        
                        <a href="{{ route('transaksi.index') }}" title="Transaksi Pelanggan" class="group flex items-center gap-3 rounded-xl py-3 px-3.5 text-sm font-semibold sidebar-transition {{ request()->routeIs('transaksi.*') ? 'bg-blue-600 text-white shadow-md shadow-blue-100' : 'text-slate-500 hover:bg-slate-50 hover:text-blue-600' }}">
                            <svg class="w-5 h-5 flex-shrink-0 {{ request()->routeIs('transaksi.*') ? 'text-white' : 'text-slate-400 group-hover:text-blue-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                            <span x-show="sidebarOpen" class="whitespace-nowrap">Transaksi Pelanggan</span>
                        </a>
                        
                        <a href="{{ route('komplain.index') }}" title="Komplain Pelanggan" class="relative group flex items-center gap-3 rounded-xl py-3 px-3.5 text-sm font-semibold sidebar-transition {{ request()->routeIs('komplain.*') ? 'bg-blue-600 text-white shadow-md shadow-blue-100' : 'text-slate-500 hover:bg-slate-50 hover:text-blue-600' }}">
                            <svg class="w-5 h-5 flex-shrink-0 {{ request()->routeIs('komplain.*') ? 'text-white' : 'text-slate-400 group-hover:text-blue-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M21 12c0 4.418-4.03 8-9 8a9.77 9.77 0 01-4-.8L3 20l1.8-3.6A7.5 7.5 0 013 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                            <span x-show="sidebarOpen" class="whitespace-nowrap">Komplain Pelanggan</span>
                            @php $komplainCount = \App\Models\Komplain::whereIn('status', ['Not Yet', 'In Progress'])->count(); @endphp
                            @if($komplainCount > 0)
                                <span x-show="sidebarOpen" class="ml-auto bg-red-100 text-red-600 text-[10px] font-bold px-2 py-0.5 rounded-full">{{ $komplainCount }}</span>
                                <span x-show="!sidebarOpen" class="absolute top-2 right-2 w-2 h-2 bg-red-500 rounded-full border-2 border-white"></span>
                            @endif
                        </a>
                    </div>
                </div>

                {{-- GRUP: MENU LAIN --}}
                <div>
                    <div class="flex items-center px-3 mb-4">
                        <div x-show="!sidebarOpen" class="w-full h-[1px] bg-slate-100"></div>
                        <p x-show="sidebarOpen" class="text-[11px] font-bold text-slate-400 uppercase tracking-[0.15em] whitespace-nowrap">Menu Lain</p>
                    </div>
                    <div class="space-y-1.5">
                        <a href="{{ route('pengeluaran.index') }}" title="Pengeluaran" class="group flex items-center gap-3 rounded-xl py-3 px-3.5 text-sm font-semibold sidebar-transition {{ request()->routeIs('pengeluaran.*') ? 'bg-blue-600 text-white shadow-md shadow-blue-100' : 'text-slate-500 hover:bg-slate-50 hover:text-blue-600' }}">
                            <svg class="w-5 h-5 flex-shrink-0 {{ request()->routeIs('pengeluaran.*') ? 'text-white' : 'text-slate-400 group-hover:text-blue-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            <span x-show="sidebarOpen" class="whitespace-nowrap">Pengeluaran</span>
                        </a>
                    </div>
                </div>
            
                {{-- GRUP: MASTER --}}
                <div>
                    <div class="flex items-center px-3 mb-4">
                        <div x-show="!sidebarOpen" class="w-full h-[1px] bg-slate-100"></div>
                        <p x-show="sidebarOpen" class="text-[11px] font-bold text-slate-400 uppercase tracking-[0.15em] whitespace-nowrap">Master</p>
                    </div>
                    <div class="space-y-1.5">
                        <a href="{{ route('paket.index') }}" title="Paket Internet" class="group flex items-center gap-3 rounded-xl py-3 px-3.5 text-sm font-semibold sidebar-transition {{ request()->routeIs('paket.*') ? 'bg-blue-600 text-white shadow-md shadow-blue-100' : 'text-slate-500 hover:bg-slate-50 hover:text-blue-600' }}">
                            <svg class="w-5 h-5 flex-shrink-0 {{ request()->routeIs('paket.*') ? 'text-white' : 'text-slate-400 group-hover:text-blue-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                            <span x-show="sidebarOpen" class="whitespace-nowrap">Paket Internet</span>
                        </a>
                    </div>
                </div>
                @endif
            </nav>
        </aside>

        <main class="flex-1 flex flex-col min-w-0 bg-[#f8fafc] overflow-hidden">
            
            <header class="h-20 bg-white/80 backdrop-blur-md border-b border-slate-200 flex items-center justify-between px-8 z-10 sticky top-0">
                
                <div class="flex items-center gap-6">
                    <button @click="sidebarOpen = !sidebarOpen" class="relative group w-10 h-10 flex flex-col items-center justify-center gap-1.5 focus:outline-none bg-slate-50 rounded-xl hover:bg-slate-100 transition-all">
                        <div class="w-5 h-0.5 bg-slate-600 rounded-full sidebar-transition origin-center" :class="sidebarOpen ? '' : 'rotate-45 translate-y-2'"></div>
                        <div class="w-5 h-0.5 bg-slate-600 rounded-full sidebar-transition" :class="sidebarOpen ? '' : 'opacity-0'"></div>
                        <div class="w-5 h-0.5 bg-slate-600 rounded-full sidebar-transition origin-center" :class="sidebarOpen ? '' : '-rotate-45 -translate-y-2'"></div>
                    </button>

                    <div>
                        <h1 class="text-lg font-bold text-slate-800 tracking-tight">Dashboard</h1>
                        <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest">Panel administrasi</p>
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open" class="flex items-center gap-2 p-1.5 pr-4 rounded-2xl hover:bg-slate-50 transition-all focus:outline-none">
                            <div class="flex flex-col items-end mr-1 hidden sm:flex">
                                <span class="text-xs font-bold text-slate-800">{{ auth()->user()->fullname }}</span>
                                <span class="text-[10px] text-blue-600 font-bold uppercase tracking-tighter">{{ auth()->user()->role }}</span>
                            </div>
                            <div class="w-9 h-9 rounded-xl bg-slate-800 flex items-center justify-center text-white shadow-lg shadow-slate-200">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                            </div>
                            <svg class="w-4 h-4 text-slate-400 transition-transform duration-300" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </button>

                        <div x-show="open" @click.outside="open = false" x-transition.origin.top.right 
                            class="absolute right-0 mt-3 w-56 bg-white rounded-2xl shadow-xl shadow-slate-200/50 border border-slate-100 py-2 z-50">
                            <div class="px-4 py-3 border-b border-slate-50 mb-1 sm:hidden">
                                <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">Akun Saya</p>
                                <p class="text-sm font-bold text-slate-700 truncate">{{ auth()->user()->fullname }}</p>
                            </div>

                            <button type="button" @click="open = false; $dispatch('open-profile-modal')" class="w-full flex items-center gap-3 px-4 py-3 text-sm text-slate-600 font-bold hover:bg-slate-50 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                Edit Profil
                            </button>
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit" class="w-full flex items-center gap-3 px-4 py-3 text-sm text-red-500 font-bold hover:bg-red-50 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                                    Logout
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </header>

            <div class="flex-1 overflow-y-auto p-8 lg:p-10">
                @yield('content')
            </div>
        </main>
    </div>

    @stack('scripts')

    {{-- MODAL EDIT PROFIL GLOBAL (Bisa dipanggil dari mana saja) --}}
    <div x-data="{ 
            openProfile: false,
            password: '',
            showPassword: false,
            get isPasswordValid() {
                if(this.password.length === 0) return true;
                return this.password.length >= 8 && /[a-zA-Z]/.test(this.password) && /[0-9]/.test(this.password);
            }
         }"
         @open-profile-modal.window="openProfile = true"
         x-show="openProfile" 
         x-cloak 
         class="fixed inset-0 z-[999] flex items-center justify-center bg-black/60 backdrop-blur-sm p-4 transition-all">
        
        <div class="bg-white w-full max-w-xl rounded-2xl shadow-2xl p-8" @click.away="openProfile = false"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 scale-90"
             x-transition:enter-end="opacity-100 scale-100">
            
            <h4 class="text-xl font-bold text-slate-900 mb-1">Edit Profil Saya</h4>
            <p class="text-sm font-medium text-slate-500 mb-6">Perbarui informasi nama dan password akun Anda.</p>

            <form action="{{ url('/users/' . auth()->user()->id) }}" method="POST" class="space-y-4" x-data="{ isSubmitting: false }" @submit="isSubmitting = true">
                @csrf @method('PUT')
                
                <div>
                    <label class="block text-[10px] font-black text-slate-700 uppercase tracking-widest mb-1.5 ml-1">Nama Lengkap</label>
                    <input type="text" name="fullname" value="{{ auth()->user()->fullname }}" class="w-full text-sm p-3 bg-slate-50 border border-slate-100 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none font-bold text-slate-800 transition-all" required>
                </div>
                
                <div class="pt-2">
                    <label class="block text-[10px] font-black text-slate-700 uppercase tracking-widest mb-1.5 ml-1">Ganti Password (Opsional)</label>
                    <div class="relative">
                        <input :type="showPassword ? 'text' : 'password'" name="password" x-model="password" placeholder="Ketik sandi baru jika ingin mengubah..." 
                               class="w-full text-sm p-3 pr-10 bg-slate-50 border rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none font-bold text-slate-800 transition-all"
                               :class="(password.length > 0 && !isPasswordValid) ? 'border-red-400 focus:ring-red-500' : 'border-slate-100'">
                        <button type="button" @click="showPassword = !showPassword" class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600 focus:outline-none">
                            <svg x-show="!showPassword" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                            <svg x-show="showPassword" x-cloak class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.29 3.29m0 0a10.05 10.05 0 015.188-1.583c4.477 0 8.268 2.943 9.542 7a10.025 10.025 0 01-4.132 5.411m0 0l-3.29-3.29" /></svg>
                        </button>
                    </div>
                    <p class="text-[10px] font-medium mt-1.5 ml-1 transition-colors duration-200" :class="(password.length > 0 && !isPasswordValid) ? 'text-red-500' : 'text-slate-400'">
                        * Kosongkan jika tidak diganti. Jika diisi, minimal 8 karakter (kombinasi huruf & angka).
                    </p>
                </div>

                <div class="flex gap-3 pt-6 mt-6 border-t border-slate-100">
                    <button type="button" @click="openProfile = false" class="flex-1 text-sm font-bold text-slate-500 p-3 hover:bg-slate-100 rounded-xl transition-all">Batal</button>
                    <button type="submit" x-bind:disabled="!isPasswordValid || isSubmitting" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white text-sm font-bold rounded-xl p-3 shadow-lg shadow-blue-100 transition-all flex justify-center items-center disabled:opacity-70 disabled:cursor-not-allowed">
                        <span x-show="!isSubmitting">Simpan Perubahan</span>
                        <span x-show="isSubmitting" x-cloak class="flex items-center gap-2">
                            <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    @stack('scripts')
</body>
</html>

</body>
</html>