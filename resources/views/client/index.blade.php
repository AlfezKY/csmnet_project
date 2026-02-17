<!DOCTYPE html>
<html>
<head>
    <title>Portal Pelanggan - CSMNET</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-10">
    <div class="bg-white p-8 rounded shadow-md border-t-4 border-green-600">
        <h1 class="text-2xl font-bold">Selamat Datang, {{ Auth::user()->fullname }}!</h1>
        <p class="text-gray-600 mt-2">Ini adalah halaman khusus <span class="font-semibold text-green-600">Pelanggan</span>.</p>
        
        <div class="mt-6 p-4 bg-green-50 rounded border border-green-200">
            <p class="text-green-800 font-medium font-mono">Status: Login Client Portal BERHASIL!</p>
        </div>

        <form action="/logout" method="POST" class="mt-10">
            @csrf
            <button type="submit" class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600">Logout</button>
        </form>
    </div>
</body>
</html>