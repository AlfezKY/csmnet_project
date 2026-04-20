<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - CSM.TV</title>
    
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
                    }
                }
            }
        }
    </script>

    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <style>
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="bg-slate-50 min-h-screen flex items-center justify-center p-4">

    {{-- FLOATING TOAST ERROR (SMOOTH ANIMATION) --}}
    @if(session()->has('loginError'))
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
             x-transition:enter-start="opacity-0 translate-y-[-20px] scale-90"
             x-transition:enter-end="opacity-100 translate-y-0 scale-100"
             x-transition:leave="transition-all transform ease-in duration-300"
             x-transition:leave-start="opacity-100 translate-y-0 scale-100"
             x-transition:leave-end="opacity-0 translate-y-[-20px] scale-90"
             x-cloak
             class="fixed top-5 right-5 z-50 max-w-sm w-auto min-w-[280px] bg-white border border-gray-100 shadow-[0_8px_30px_rgb(0,0,0,0.08)] shadow-red-500/10 rounded-xl overflow-hidden flex flex-col cursor-default">
            
            <div class="px-4 py-3 flex items-start gap-3">
                <div class="w-8 h-8 rounded-full bg-red-100 text-red-600 flex flex-shrink-0 items-center justify-center mt-0.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                </div>
                <div class="flex-1">
                    <p class="text-sm font-bold text-gray-800 tracking-tight mb-1">Gagal Masuk</p>
                    <p class="text-[11px] font-medium text-gray-500">{{ session('loginError') }}</p>
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

    <div class="bg-white p-8 md:p-10 rounded-2xl shadow-xl w-full max-w-md border-t-4 border-indigo-600">
        <div class="text-center mb-8">
            <h2 class="text-3xl font-extrabold text-slate-900 tracking-tight">Masuk CSM<span class="text-indigo-600">.TV</span></h2>
            <p class="text-sm text-slate-500 mt-2">Silakan login untuk mengakses Area Client Portal.</p>
        </div>

        <form action="/login" method="post" class="space-y-5">
            @csrf
            
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Username Login</label>
                <input type="text" name="username" class="w-full text-sm p-3 bg-slate-50 border border-slate-200 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none font-medium transition-all" required autofocus>
            </div>
            
            <div x-data="{ showPass: false }">
                <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Password</label>
                <div class="relative">
                    <input :type="showPass ? 'text' : 'password'" name="password" class="w-full text-sm p-3 pr-10 bg-slate-50 border border-slate-200 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none font-medium transition-all" required>
                    <button type="button" @click="showPass = !showPass" class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600 transition-colors">
                        <svg x-show="!showPass" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                        <svg x-show="showPass" x-cloak class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.29 3.29m0 0a10.05 10.05 0 015.188-1.583c4.477 0 8.268 2.943 9.542 7a10.025 10.025 0 01-4.132 5.411m0 0l-3.29-3.29" /></svg>
                    </button>
                </div>
            </div>

            <button type="submit" class="w-full mt-4 bg-indigo-600 text-white font-bold py-3.5 rounded-xl hover:bg-indigo-700 transition duration-300 tracking-wider shadow-lg shadow-indigo-600/30">MASUK</button>
        
            <p class="text-center text-sm text-slate-500 mt-6">
                Belum punya akun? <a href="/register" class="text-indigo-600 font-bold hover:underline">Daftar Pemasangan Baru</a>
            </p>
        </form>
    </div>
</body>
</html>