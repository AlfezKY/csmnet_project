<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        // 1. Cek apakah user sudah login
        if (!$request->user()) {
            return redirect('/');
        }

        // 2. Cek apakah role user ada di daftar yang diizinkan (Admin/Owner)
        if (in_array($request->user()->role, $roles)) {
            return $next($request);
        }

        // 3. Kalau Pelanggan coba-coba buka Dashboard, balikin ke portalnya
        if ($request->user()->role === 'Pelanggan') {
            return redirect('/client-portal');
        }

        // 4. Sebaliknya, kalau Admin/Owner nyasar ke portal pelanggan, balikin ke Dashboard
        return redirect('/dashboard');
    }
}
