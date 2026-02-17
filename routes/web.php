<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ClientController;

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
    });

    // Khusus Pelanggan
    Route::middleware(['checkRole:Pelanggan'])->group(function () {
        Route::get('/client-portal', [ClientController::class, 'index'])->name('client-portal');
    });
});
