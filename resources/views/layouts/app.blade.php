<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'CSMNET')</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Instrument Sans', 'sans-serif'],
                    },
                },
            },
        }
    </script>

    <style>
        [x-cloak] { display: none !important; }
        svg { display: inline-block; vertical-align: middle; }
    </style>
</head>
<body class="bg-gray-50 text-gray-900 font-sans antialiased">

    <div class="flex h-screen overflow-hidden">
        <aside class="hidden md:flex flex-col w-72 bg-white border-r border-gray-200 h-full overflow-y-auto">
            <div class="p-8 border-b border-gray-50">
                <div class="flex items-center gap-3">
                    <span class="text-2xl font-bold text-blue-600 tracking-tight">CSMNET</span>
                </div>
            </div>

            <nav class="flex-1 px-4 py-6">
                <div class="mb-8">
                    <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold transition-all {{ request()->routeIs('dashboard') ? 'bg-blue-50 text-blue-600 shadow-sm' : 'text-gray-500 hover:bg-gray-50' }}">
                        <svg class="w-5 h-5 {{ request()->routeIs('dashboard') ? 'text-blue-600' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                        <span>Dashboard</span>
                    </a>
                </div>

                @if(auth()->user()->role == 'Admin' || auth()->user()->role == 'Owner')
                <div class="mb-8">
                    <p class="px-4 mb-3 text-[10px] font-bold text-gray-400 uppercase tracking-widest">PELANGGAN</p>
                    <a href="{{ route('pelanggan.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium {{ request()->routeIs('pelanggan.*') ? 'bg-blue-50 text-blue-600 shadow-sm' : 'text-gray-500 hover:bg-gray-50' }} transition-all mb-1">
                        <svg class="w-5 h-5 {{ request()->routeIs('pelanggan.*') ? 'text-blue-600' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                        </svg>
                        <span>Data Pelanggan</span>
                    </a>
                    <a href="#" class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium text-gray-500 hover:bg-gray-50 transition-all mb-1">
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <span>Approval Pelanggan</span>
                    </a>
                    <a href="#" class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium text-gray-500 hover:bg-gray-50 transition-all mb-1">
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        <span>Tagihan Pelanggan</span>
                    </a>
                    <a href="#" class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium text-gray-500 hover:bg-gray-50 transition-all">
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
                        <span>Transaksi Pelanggan</span>
                    </a>
                </div>

                <div class="mb-8">
                    <p class="px-4 mb-3 text-[10px] font-bold text-gray-400 uppercase tracking-widest">MASTER</p>
                    <a href="{{ route('paket.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium {{ request()->routeIs('paket.*') ? 'bg-blue-50 text-blue-600 shadow-sm' : 'text-gray-500' }} hover:bg-gray-50 transition-all mb-1">
                        <svg class="w-5 h-5 {{ request()->routeIs('paket.*') ? 'text-blue-600' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                        <span>Paket</span>
                    </a>
                    <a href="{{ route('users.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium {{ request()->routeIs('users.*') ? 'bg-blue-50 text-blue-600' : 'text-gray-500' }} hover:bg-gray-50 transition-all">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                        <span>Manajemen Pengguna</span>
                    </a>
                </div>
                @endif

                @if(auth()->user()->role == 'Owner')
                <div class="mb-8">
                    <p class="px-4 mb-3 text-[10px] font-bold text-gray-400 uppercase tracking-widest">OWNER MENU</p>
                    <a href="#" class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium text-gray-500 hover:bg-gray-50 transition-all">
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4V7M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                        <span>Laporan</span>
                    </a>
                </div>
                @endif
            </nav>
        </aside>

        <main class="flex-1 flex flex-col min-w-0 bg-gray-50 overflow-hidden">
            <header class="h-16 bg-white border-b border-gray-200 flex items-center justify-between px-8 z-10">
                <div>
                    <h1 class="text-sm font-bold text-gray-900">Dashboard</h1>
                    <p class="text-[10px] text-gray-500 font-medium">Panel administrasi</p>
                </div>

                <div class="flex items-center gap-4">
                    <div class="flex flex-col items-end mr-2">
                        <span class="text-xs font-bold text-gray-900">{{ auth()->user()->fullname }}</span>
                        <span class="text-[10px] text-blue-600 font-bold uppercase tracking-tighter">{{ auth()->user()->role }}</span>
                    </div>
                    
                    <div class="relative" id="user-menu-container">
                        <button id="user-menu-button" type="button" class="w-8 h-8 rounded-full bg-indigo-600 flex items-center justify-center text-white font-bold text-xs shadow-sm transition-transform active:scale-95 focus:outline-none">
                            {{ substr(auth()->user()->fullname, 0, 1) }}
                        </button>
                        
                        <div id="user-menu-dropdown" class="hidden absolute right-0 mt-2 w-48 bg-white rounded-xl shadow-lg border border-gray-100 py-2 z-50">
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit" class="w-full text-left px-4 py-2 text-xs text-red-600 font-bold hover:bg-red-50 transition-colors">
                                    Logout
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </header>

            <div class="flex-1 overflow-y-auto p-8">
                @yield('content')
            </div>
        </main>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const userMenuBtn = document.getElementById('user-menu-button');
            const userMenuDropdown = document.getElementById('user-menu-dropdown');
            const userMenuContainer = document.getElementById('user-menu-container');

            // Toggle buka/tutup
            userMenuBtn.addEventListener('click', function(e) {
                e.stopPropagation();
                userMenuDropdown.classList.toggle('hidden');
            });

            // Tutup jika klik di luar area profil
            document.addEventListener('click', function(e) {
                if (!userMenuContainer.contains(e.target)) {
                    userMenuDropdown.classList.add('hidden');
                }
            });

            // Tutup jika tekan tombol ESC
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    userMenuDropdown.classList.add('hidden');
                }
            });
        });
    </script>
</body>
</html>