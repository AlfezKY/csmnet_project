<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\PelangganController;
use App\Http\Controllers\Admin\PaketController;

use App\Http\Controllers\Client\ClientController;


// 1. GUEST (Orang yang belum login)
Route::middleware('guest')->group(function () {
    Route::get('/', [LoginController::class, 'index'])->name('login');
    Route::post('/login', [LoginController::class, 'authenticate']);

    Route::get('/register', [RegisterController::class, 'index']);
    Route::post('/register', [RegisterController::class, 'store']);
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
    });

    // Khusus Pelanggan
    Route::middleware(['checkRole:Pelanggan'])->group(function () {
        Route::get('/client-portal', [ClientController::class, 'index'])->name('client-portal');
    });
});
