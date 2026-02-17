<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - CSMNET</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 h-screen flex items-center justify-center p-4">
    <div class="bg-white p-8 rounded-lg shadow-md w-full max-w-md border-t-4 border-green-600">
        <h2 class="text-2xl font-bold mb-6 text-center text-green-600 uppercase tracking-wider">BUAT AKUN BARU</h2>
        
        <form action="/register" method="post">
            @csrf
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700">Nama Lengkap</label>
                <input type="text" name="fullname" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 p-2 border @error('fullname') border-red-500 @enderror" value="{{ old('fullname') }}" required>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700">Daftar Sebagai (Role)</label>
                <select name="role" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 p-2 border @error('role') border-red-500 @enderror" required>
                    <option value="" disabled selected>Pilih Role...</option>
                    <option value="Admin" {{ old('role') == 'Admin' ? 'selected' : '' }}>Admin</option>
                    <option value="Owner" {{ old('role') == 'Owner' ? 'selected' : '' }}>Owner</option>
                    <option value="Pelanggan" {{ old('role') == 'Pelanggan' ? 'selected' : '' }}>Pelanggan</option>
                </select>
                @error('role')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700">Username</label>
                <input type="text" name="username" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 p-2 border @error('username') border-red-500 @enderror" value="{{ old('username') }}" required>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700">Email (Opsional)</label>
                <input type="email" name="email" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 p-2 border" value="{{ old('email') }}">
            </div>

            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700">Password</label>
                <input type="password" name="password" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 p-2 border @error('password') border-red-500 @enderror" required>
            </div>

            <button type="submit" class="w-full bg-green-600 text-white font-bold py-2 rounded-md hover:bg-green-700 transition duration-200 uppercase tracking-widest shadow-lg">DAFTAR SEKARANG</button>
            
            <p class="text-center text-sm text-gray-600 mt-4">
                Sudah punya akun? <a href="/login" class="text-green-600 font-bold hover:underline">Login di sini</a>
            </p>
        </form>
    </div>
</body>
</html>