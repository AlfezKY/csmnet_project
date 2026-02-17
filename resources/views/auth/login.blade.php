<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - CSMNET</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 h-screen flex items-center justify-center">
    <div class="bg-white p-8 rounded-lg shadow-md w-96 border-t-4 border-blue-600">
        <h2 class="text-2xl font-bold mb-6 text-center text-blue-600 uppercase tracking-wider">CSMNET LOGIN</h2>
        
        @if(session()->has('loginError'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-2 rounded mb-4 text-sm text-center">
                {{ session('loginError') }}
            </div>
        @endif

        <form action="/login" method="post">
            @csrf
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700">Username</label>
                <input type="text" name="username" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-2 border" required autofocus>
            </div>
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700">Password</label>
                <input type="password" name="password" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-2 border" required>
            </div>
            <button type="submit" class="w-full bg-blue-600 text-white font-bold py-2 rounded-md hover:bg-blue-700 transition duration-300 shadow-lg">MASUK</button>
        <p class="text-center text-sm text-gray-600 mt-4">Belum punya akun? <a href="/register" class="text-blue-600 font-bold hover:underline">Daftar</a></p>
        </form>
    </div>
</body>
</html>