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
use App\Http\Controllers\Admin\PengeluaranController;
use App\Http\Controllers\Admin\LaporanController;
use App\Http\Controllers\Client\ClientController;

// --- 1. PUBLIC ROUTES ---
Route::get('/', function () {
    $pakets = Paket::where('is_show', true)->take(4)->get();
    return view('client.index', compact('pakets'));
})->name('home');

Route::get('/about', function () {
    return view('client.about');
})->name('about');

// --- 2. GUEST ROUTES ---
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'index'])->name('login');
    Route::post('/login', [LoginController::class, 'authenticate']);

    Route::get('/register', [RegisterController::class, 'index']);
    Route::post('/register', [RegisterController::class, 'store']);
});

// --- 3. AUTH ROUTES ---
Route::middleware('auth')->group(function () {

    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    // ==========================================
    // MENU BERSAMA (ADMIN & OWNER)
    // ==========================================
    Route::middleware(['checkRole:Admin,Owner'])->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::resource('transaksi', TransaksiController::class);
        Route::resource('pengeluaran', PengeluaranController::class)->except(['create', 'show', 'edit']);
    });

    // ==========================================
    // KHUSUS ADMIN
    // ==========================================
    Route::middleware(['checkRole:Admin'])->group(function () {
        Route::resource('users', UserController::class);
        Route::resource('pelanggan', PelangganController::class);
        Route::resource('paket', PaketController::class);

        Route::post('/komplain/admin-store', [App\Http\Controllers\Admin\KomplainController::class, 'storeAdmin'])->name('komplain.storeAdmin');
        Route::resource('komplain', App\Http\Controllers\Admin\KomplainController::class)->except(['create', 'show', 'store']);

        Route::get('/approval', [ApprovalController::class, 'index'])->name('approval.index');
        Route::put('/approval/{id}', [ApprovalController::class, 'action'])->name('approval.action');
        Route::post('/approval/bulk', [ApprovalController::class, 'bulkAction'])->name('approval.bulk');

        Route::get('/tagihan', [TagihanController::class, 'index'])->name('tagihan.index');
        Route::put('/tagihan/{id}/lunas', [TagihanController::class, 'action'])->name('tagihan.action');
        Route::post('/tagihan/bulk-lunas', [TagihanController::class, 'bulkAction'])->name('tagihan.bulk');
        Route::post('/tagihan/{id}/ingatkan', [TagihanController::class, 'ingatkan'])->name('tagihan.ingatkan');
    });

    // ==========================================
    // KHUSUS OWNER
    // ==========================================

    Route::middleware(['checkRole:Owner'])->group(function () {
        Route::get('/laporan', [LaporanController::class, 'index'])->name('laporan.index');

        // Tambahkan baris ini brok:
        Route::get('/cashflow', [App\Http\Controllers\Admin\CashflowController::class, 'index'])->name('cashflow.index');
    });

    // ==========================================
    // KHUSUS PELANGGAN
    // ==========================================
    Route::middleware(['checkRole:Pelanggan'])->group(function () {
        Route::get('/client-portal', [ClientController::class, 'index'])->name('client-portal');
        Route::get('/lapor-gangguan', function () {
            return view('client.komplain');
        })->name('komplain.form');
        Route::post('/komplain/kirim', [App\Http\Controllers\Admin\KomplainController::class, 'store'])->name('komplain.store');
    });
});
