<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     * Checks if the logged-in user's role matches the required role.
     */
    public function handle(Request $request, Closure $next, string $role): mixed
    {
        // 1. Cek apakah user sudah login (ada token di session)
        if (!Session::has('token')) {
            return redirect()->route('login')->withErrors(['email' => 'Silakan login terlebih dahulu.']);
        }

        // 2. Ambil data user dari session
        $user = Session::get('user');

        // 3. Cocokkan role user dengan role yang diizinkan untuk route ini
        if (!$user || $user['role'] !== $role) {
            // Jika role tidak cocok, redirect ke dashboard mereka sendiri
            return redirect()->route('dashboard.' . $this->getRouteKey($user['role'] ?? ''))
                ->withErrors(['auth' => 'Anda tidak memiliki akses ke halaman tersebut.']);
        }

        return $next($request);
    }

    /**
     * Convert role name to route key
     */
    private function getRouteKey(string $role): string
    {
        return match($role) {
            'Administrator'        => 'admin',
            'Kepala Laboratorium'  => 'kalab',
            'Ketua Program Studi'  => 'kaprodi',
            'Staf Administrasi'    => 'stafadmin',
            'Staf Laboratorium'    => 'staflab',
            default                => 'login',
        };
    }
}
