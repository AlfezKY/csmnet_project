<?php

use Illuminate\Support\Facades\Route;
use App\Models\Paket;

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\PelangganController;
use App\Http\Controllers\Admin\PaketController;
use App\Http\Controllers\Admin\TransaksiController;
use App\Http\Controllers\Admin\ApprovalController;
use App\Http\Controllers\Admin\TagihanController;

use App\Http\Controllers\Client\ClientController;


// 1. GUEST (Orang yang belum login)
Route::middleware('guest')->group(function () {

    // --- ROUTE LANDING PAGE ---
    Route::get('/', function () {
        $pakets = Paket::where('is_show', true)->take(4)->get();
        return view('client.index', compact('pakets'));
    })->name('home');

    // --- ROUTE ABOUT (TENTANG KAMI) ---
    Route::get('/about', function () {
        return view('client.about');
    })->name('about');


    // --- 2. GUEST (Khusus orang yang BELUM login) ---
    Route::middleware('guest')->group(function () {
        Route::get('/login', [LoginController::class, 'index'])->name('login');
        Route::post('/login', [LoginController::class, 'authenticate']);

        Route::get('/register', [RegisterController::class, 'index']);
        Route::post('/register', [RegisterController::class, 'store']);
    });
});

// 2. AUTH (Harus login dulu)
Route::middleware('auth')->group(function () {

    // ROUTE LOGOUT (WAJIB ADA INI BIAR GAK 404)
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    // Khusus Admin & Owner
    Route::middleware(['checkRole:Admin,Owner'])->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::resource('users', UserController::class);
        Route::resource('pelanggan', PelangganController::class);
        Route::resource('paket', PaketController::class);
        Route::resource('transaksi', TransaksiController::class);

        Route::get('/approval', [ApprovalController::class, 'index'])->name('approval.index');
        Route::put('/approval/{id}', [ApprovalController::class, 'action'])->name('approval.action');
        Route::post('/approval/bulk', [ApprovalController::class, 'bulkAction'])->name('approval.bulk');

        Route::get('/tagihan', [TagihanController::class, 'index'])->name('tagihan.index');
        Route::put('/tagihan/{id}/lunas', [TagihanController::class, 'action'])->name('tagihan.action');
        Route::post('/tagihan/bulk-lunas', [TagihanController::class, 'bulkAction'])->name('tagihan.bulk');
    });

    // Khusus Pelanggan
    Route::middleware(['checkRole:Pelanggan'])->group(function () {
        Route::get('/client-portal', [ClientController::class, 'index'])->name('client-portal');
    });
});
