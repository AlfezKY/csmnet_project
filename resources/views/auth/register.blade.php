<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pendaftaran Pemasangan - CSM.TV</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="bg-slate-50 min-h-screen flex items-center justify-center p-4 py-10">

    {{-- FLOATING TOAST ERROR VALIDASI (SMOOTH ANIMATION) --}}
    @if ($errors->any())
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
                    <p class="text-sm font-bold text-gray-800 tracking-tight mb-1">Pendaftaran Gagal:</p>
                    <ul class="list-disc list-inside text-[11px] font-medium text-gray-500">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
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

    <div class="bg-white p-8 md:p-10 rounded-2xl shadow-xl w-full max-w-2xl border-t-4 border-indigo-600">
        
        <div class="text-center mb-8">
            <h2 class="text-3xl font-extrabold text-slate-900 tracking-tight">Daftar CSM<span class="text-indigo-600">.TV</span></h2>
            <p class="text-sm text-slate-500 mt-2">Isi data diri untuk pemasangan internet rumah Anda.</p>
        </div>
        
        <form action="{{ url('/register') }}" method="post" class="space-y-5">
            @csrf
            
            <div class="mb-4 pb-2 border-b border-slate-100">
                <h5 class="text-sm font-extrabold text-slate-800 uppercase tracking-wider">Informasi Pelanggan</h5>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Nama Lengkap / Instansi</label>
                    <input type="text" name="fullname" value="{{ old('fullname') }}" class="w-full text-sm p-3 bg-slate-50 border border-slate-200 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none font-medium transition-all @error('fullname') border-red-500 @enderror" required>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">No. WhatsApp Aktif</label>
                    <input type="text" name="no_wa" value="{{ old('no_wa') }}" placeholder="Contoh: 08123456789" class="w-full text-sm p-3 bg-slate-50 border border-slate-200 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none font-medium transition-all @error('no_wa') border-red-500 @enderror" required>
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Alamat Pemasangan Lengkap</label>
                <textarea name="alamat" rows="3" placeholder="Nama Jalan, RT/RW, Patokan Rumah..." class="w-full text-sm p-3 bg-slate-50 border border-slate-200 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none font-medium transition-all @error('alamat') border-red-500 @enderror" required>{{ old('alamat') }}</textarea>
            </div>

            <div class="mt-8 mb-4 pb-2 border-b border-slate-100">
                <h5 class="text-sm font-extrabold text-slate-800 uppercase tracking-wider">Informasi Akun Login</h5>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Username Login</label>
                    <input type="text" name="username" value="{{ old('username') }}" placeholder="Min. 4 karakter" class="w-full text-sm p-3 bg-slate-50 border border-slate-200 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none font-medium transition-all @error('username') border-red-500 @enderror" required>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Email Aktif (Opsional)</label>
                    <input type="email" name="email" value="{{ old('email') }}" placeholder="email@contoh.com" class="w-full text-sm p-3 bg-slate-50 border border-slate-200 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none font-medium transition-all @error('email') border-red-500 @enderror">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5" x-data="{ showPass: false, showConf: false }">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Password Baru</label>
                    <div class="relative">
                        <input :type="showPass ? 'text' : 'password'" name="password" placeholder="Buat password" class="w-full text-sm p-3 pr-10 bg-slate-50 border border-slate-200 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none font-medium transition-all @error('password') border-red-500 @enderror" required>
                        <button type="button" @click="showPass = !showPass" class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600">
                            <svg x-show="!showPass" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                            <svg x-show="showPass" x-cloak class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.29 3.29m0 0a10.05 10.05 0 015.188-1.583c4.477 0 8.268 2.943 9.542 7a10.025 10.025 0 01-4.132 5.411m0 0l-3.29-3.29" /></svg>
                        </button>
                    </div>
                    <p class="text-[10px] text-slate-500 mt-1.5">* Min. 8 Karakter dan harus mengandung angka.</p>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Ulangi Password</label>
                    <div class="relative">
                        <input :type="showConf ? 'text' : 'password'" name="password_confirmation" placeholder="Ulangi password" class="w-full text-sm p-3 pr-10 bg-slate-50 border border-slate-200 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none font-medium transition-all" required>
                        <button type="button" @click="showConf = !showConf" class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600">
                            <svg x-show="!showConf" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                            <svg x-show="showConf" x-cloak class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.29 3.29m0 0a10.05 10.05 0 015.188-1.583c4.477 0 8.268 2.943 9.542 7a10.025 10.025 0 01-4.132 5.411m0 0l-3.29-3.29" /></svg>
                        </button>
                    </div>
                </div>
            </div>

            <button type="submit" class="w-full mt-4 bg-indigo-600 text-white font-bold py-3.5 rounded-xl hover:bg-indigo-700 transition duration-300 tracking-wider shadow-lg shadow-indigo-600/30">AJUKAN PEMASANGAN BARU</button>
            
            <p class="text-center text-sm text-slate-500 mt-6">
                Sudah menjadi pelanggan? <a href="{{ url('/login') }}" class="text-indigo-600 font-bold hover:underline">Masuk Client Portal</a>
            </p>
        </form>
    </div>
</body>
</html>