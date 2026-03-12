<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use Illuminate\Session\TokenMismatchException; // WAJIB DI-IMPORT
use Illuminate\Http\Request;                   // WAJIB DI-IMPORT
use Illuminate\Support\Facades\Auth;           // WAJIB DI-IMPORT

class Handler extends ExceptionHandler
{
    /**
     * A list of exception types with their corresponding custom log levels.
     *
     * @var array<class-string<\Throwable>, \Psr\Log\LogLevel::*>
     */
    protected $levels = [
        //
    ];

    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<\Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });

        // --- TAMBAHAN KODE NANGKEP 419 PAGE EXPIRED ---
        $this->renderable(function (TokenMismatchException $e, Request $request) {
            // 1. Paksa logout dan bersihkan sisa sesi
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            // 2. Tendang ke halaman Beranda bawa pesan error
            return redirect('/')->with('error', 'Sesi Anda telah berakhir karena terlalu lama tidak ada aktivitas. Silakan masuk kembali.');
        });
    }
}
