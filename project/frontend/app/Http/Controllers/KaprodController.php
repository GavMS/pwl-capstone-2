<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;

class KaprodController extends Controller
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
    // GET /kaprodi/procurement — Daftar draf yang diajukan kalab (submitted)
    // ─────────────────────────────────────────────
    public function index()
    {
        try {
            $response = Http::withHeaders($this->authHeaders())
                ->get("{$this->apiUrl()}/api/procurement");

            if (in_array($response->status(), [401, 403])) {
                Session::forget('token');
                Session::forget('user');
                return redirect()->route('login')
                    ->withErrors(['email' => 'Sesi Anda telah berakhir. Silakan login kembali.']);
            }

            $allDrafts = $response->successful() ? ($response->json()['drafts'] ?? []) : [];
            $drafts    = array_values(array_filter($allDrafts, fn($d) => ($d['status'] ?? '') === 'submitted'));
            $error     = $response->successful() ? null : ($response->json()['message'] ?? 'Gagal mengambil data.');
        } catch (\Exception $e) {
            $drafts = [];
            $error  = 'Tidak dapat terhubung ke server backend. Pastikan server Node.js berjalan.';
        }

        return view('kaprodi.procurement.index', [
            'user'   => Session::get('user', []),
            'drafts' => $drafts,
            'error'  => $error,
        ]);
    }

    // ─────────────────────────────────────────────
    // GET /kaprodi/procurement/{id} — Detail draf + review per item
    // ─────────────────────────────────────────────
    public function show($id)
    {
        try {
            $response = Http::withHeaders($this->authHeaders())
                ->get("{$this->apiUrl()}/api/procurement/{$id}");

            if (!$response->successful()) {
                return redirect()->route('kaprodi.procurement.index')
                    ->withErrors(['api' => 'Draf pengadaan tidak ditemukan.']);
            }

            $data  = $response->json();
            $draft = $data['draft'] ?? null;
            $items = $data['items'] ?? [];
        } catch (\Exception $e) {
            return redirect()->route('kaprodi.procurement.index')
                ->withErrors(['api' => 'Tidak dapat terhubung ke server backend.']);
        }

        return view('kaprodi.procurement.show', [
            'user'  => Session::get('user', []),
            'draft' => $draft,
            'items' => $items,
        ]);
    }

    // ─────────────────────────────────────────────
    // POST /kaprodi/procurement/{id}/items/{itemId}/review
    // Setujui atau tolak satu item dalam draf
    // ─────────────────────────────────────────────
    public function updateItemStatus(Request $request, $id, $itemId)
    {
        $request->validate([
            'review_status' => 'required|in:approved,rejected,pending',
        ]);

        try {
            $response = Http::withHeaders($this->authHeaders())
                ->patch("{$this->apiUrl()}/api/procurement/{$id}/items/{$itemId}/review", [
                    'review_status' => $request->review_status,
                ]);

            if ($response->successful()) {
                return redirect()->route('kaprodi.procurement.show', $id)
                    ->with('success', 'Status item berhasil diperbarui.');
            }

            return redirect()->route('kaprodi.procurement.show', $id)
                ->withErrors(['api' => $response->json()['message'] ?? 'Gagal memperbarui status item.']);
        } catch (\Exception $e) {
            return redirect()->route('kaprodi.procurement.show', $id)
                ->withErrors(['api' => 'Tidak dapat terhubung ke server backend.']);
        }
    }

    // ─────────────────────────────────────────────
    // POST /kaprodi/procurement/{id}/finalize
    // Finalisasi draf — kunci permanen, tidak dapat diubah lagi
    // ─────────────────────────────────────────────
    public function finalize($id)
    {
        try {
            $response = Http::withHeaders($this->authHeaders())
                ->post("{$this->apiUrl()}/api/procurement/{$id}/finalize");

            if ($response->successful()) {
                return redirect()->route('kaprodi.riwayat.index')
                    ->with('success', 'Draf pengadaan berhasil difinalisasi dan diteruskan ke Staf Administrasi.');
            }

            return redirect()->route('kaprodi.procurement.show', $id)
                ->withErrors(['api' => $response->json()['message'] ?? 'Gagal memfinalisasi draf.']);
        } catch (\Exception $e) {
            return redirect()->route('kaprodi.procurement.show', $id)
                ->withErrors(['api' => 'Tidak dapat terhubung ke server backend.']);
        }
    }

    // ─────────────────────────────────────────────
    // GET /kaprodi/riwayat — Riwayat draf yang telah difinalisasi
    // ─────────────────────────────────────────────
    public function riwayat()
    {
        try {
            $response = Http::withHeaders($this->authHeaders())
                ->get("{$this->apiUrl()}/api/procurement");

            if (in_array($response->status(), [401, 403])) {
                Session::forget('token');
                Session::forget('user');
                return redirect()->route('login')
                    ->withErrors(['email' => 'Sesi Anda telah berakhir. Silakan login kembali.']);
            }

            $allDrafts = $response->successful() ? ($response->json()['drafts'] ?? []) : [];
            $drafts    = array_values(array_filter($allDrafts, fn($d) => ($d['status'] ?? '') === 'approved'));
            $error     = $response->successful() ? null : ($response->json()['message'] ?? 'Gagal mengambil data.');
        } catch (\Exception $e) {
            $drafts = [];
            $error  = 'Tidak dapat terhubung ke server backend. Pastikan server Node.js berjalan.';
        }

        return view('kaprodi.riwayat.index', [
            'user'   => Session::get('user', []),
            'drafts' => $drafts,
            'error'  => $error,
        ]);
    }
}
