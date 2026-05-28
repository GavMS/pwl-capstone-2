<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;

class InventoryController extends Controller
{
    private function apiUrl(): string
    {
        return env('NODEJS_API_URL', 'http://localhost:5000');
    }

    private function authHeaders(): array
    {
        return ['Authorization' => 'Bearer ' . Session::get('token', '')];
    }

    // ─────────────────────────────────────────────
    // GET /kalab/inventaris — Daftar inventaris
    // ─────────────────────────────────────────────
    public function assets()
    {
        try {
            $response = Http::withHeaders($this->authHeaders())
                ->get("{$this->apiUrl()}/api/assets");

            // Jika token expired
            if (in_array($response->status(), [401, 403])) {
                Session::forget('token');
                Session::forget('user');
                return redirect()->route('login')
                    ->withErrors(['email' => 'Sesi Anda telah berakhir. Silakan login kembali.']);
            }

            $assets = $response->successful() ? ($response->json()['assets'] ?? []) : [];
            $error = $response->successful() ? null : ($response->json()['message'] ?? 'Gagal mengambil data inventaris.');
        } catch (\Exception $e) {
            $assets = [];
            $error = 'Tidak dapat terhubung ke server backend. Pastikan server Node.js berjalan.';
        }

        return view('kalab.inventaris.index', [
            'user'   => Session::get('user', []),
            'assets' => $assets,
            'error'  => $error,
        ]);
    }

    // ─────────────────────────────────────────────
    // GET /kalab/bhp — Daftar bahan habis pakai (BHP)
    // ─────────────────────────────────────────────
    public function consumables()
    {
        try {
            $response = Http::withHeaders($this->authHeaders())
                ->get("{$this->apiUrl()}/api/consumables");

            // Jika token expired
            if (in_array($response->status(), [401, 403])) {
                Session::forget('token');
                Session::forget('user');
                return redirect()->route('login')
                    ->withErrors(['email' => 'Sesi Anda telah berakhir. Silakan login kembali.']);
            }

            $consumables = $response->successful() ? ($response->json()['consumables'] ?? []) : [];
            $error = $response->successful() ? null : ($response->json()['message'] ?? 'Gagal mengambil data BHP.');
        } catch (\Exception $e) {
            $consumables = [];
            $error = 'Tidak dapat terhubung ke server backend. Pastikan server Node.js berjalan.';
        }

        return view('kalab.bhp.index', [
            'user'        => Session::get('user', []),
            'consumables' => $consumables,
            'error'       => $error,
        ]);
    }
}
