<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;

class StafLabController extends Controller
{
    private function apiUrl(): string
    {
        return env('NODEJS_API_URL', 'http://localhost:5000');
    }

    private function authHeaders(): array
    {
        return ['Authorization' => 'Bearer ' . Session::get('token', '')];
    }

    private function handleExpiredSession($response)
    {
        if (in_array($response->status(), [401, 403])) {
            Session::forget('token');
            Session::forget('user');
            return redirect()->route('login')
                ->withErrors(['email' => 'Sesi Anda telah berakhir. Silakan login kembali.']);
        }
        return null;
    }

    // ─────────────────────────────────────────────
    // GET /staflab/bhp — Manajemen Stok BHP
    // ─────────────────────────────────────────────
    public function bhpIndex()
    {
        try {
            $response = Http::withHeaders($this->authHeaders())
                ->get("{$this->apiUrl()}/api/consumables");

            if ($redirect = $this->handleExpiredSession($response)) return $redirect;

            $consumables = $response->successful() ? ($response->json()['consumables'] ?? []) : [];
            $error = $response->successful() ? null : ($response->json()['message'] ?? 'Gagal mengambil data BHP.');
        } catch (\Exception $e) {
            $consumables = [];
            $error = 'Tidak dapat terhubung ke server backend. Pastikan server Node.js berjalan.';
        }

        return view('staflab.bhp.index', [
            'user'        => Session::get('user', []),
            'consumables' => $consumables,
            'error'       => $error ?? null,
            'apiUrl'      => $this->apiUrl(),
            'token'       => Session::get('token', ''),
        ]);
    }

    // ─────────────────────────────────────────────
    // GET /staflab/inventaris — Manajemen Inventaris & Log Maintenance
    // ─────────────────────────────────────────────
    public function inventarisIndex()
    {
        try {
            // Ambil aset + BHP (diperlukan untuk dropdown form log maintenance)
            $assetResponse = Http::withHeaders($this->authHeaders())
                ->get("{$this->apiUrl()}/api/assets");
            if ($redirect = $this->handleExpiredSession($assetResponse)) return $redirect;

            $consumableResponse = Http::withHeaders($this->authHeaders())
                ->get("{$this->apiUrl()}/api/consumables");

            $assets      = $assetResponse->successful()      ? ($assetResponse->json()['assets'] ?? []) : [];
            $consumables = $consumableResponse->successful() ? ($consumableResponse->json()['consumables'] ?? []) : [];

            $error = $assetResponse->successful() ? null
                : ($assetResponse->json()['message'] ?? 'Gagal mengambil data inventaris.');
        } catch (\Exception $e) {
            $assets      = [];
            $consumables = [];
            $error = 'Tidak dapat terhubung ke server backend. Pastikan server Node.js berjalan.';
        }

        return view('staflab.inventaris.index', [
            'user'        => Session::get('user', []),
            'assets'      => $assets,
            'consumables' => $consumables,
            'error'       => $error ?? null,
            'apiUrl'      => $this->apiUrl(),
            'token'       => Session::get('token', ''),
        ]);
    }

    // ─────────────────────────────────────────────
    // GET /staflab/maintenance — Semua Riwayat Log Maintenance
    // ─────────────────────────────────────────────
    public function maintenanceIndex()
    {
        try {
            $response = Http::withHeaders($this->authHeaders())
                ->get("{$this->apiUrl()}/api/maintenance");
            if ($redirect = $this->handleExpiredSession($response)) return $redirect;

            $assetResponse = Http::withHeaders($this->authHeaders())->get("{$this->apiUrl()}/api/assets");
            $consumableResponse = Http::withHeaders($this->authHeaders())->get("{$this->apiUrl()}/api/consumables");

            $logs  = $response->successful() ? ($response->json()['logs'] ?? []) : [];
            $assets = $assetResponse->successful() ? ($assetResponse->json()['assets'] ?? []) : [];
            $consumables = $consumableResponse->successful() ? ($consumableResponse->json()['consumables'] ?? []) : [];
            
            $error = $response->successful() ? null : ($response->json()['message'] ?? 'Gagal mengambil log maintenance.');
        } catch (\Exception $e) {
            $logs  = [];
            $assets = [];
            $consumables = [];
            $error = 'Tidak dapat terhubung ke server backend. Pastikan server Node.js berjalan.';
        }

        return view('staflab.maintenance.index', [
            'user'  => Session::get('user', []),
            'logs'  => $logs,
            'assets'=> $assets,
            'consumables' => $consumables,
            'error' => $error ?? null,
            'apiUrl'=> $this->apiUrl(),
            'token' => Session::get('token', ''),
        ]);
    }
}
