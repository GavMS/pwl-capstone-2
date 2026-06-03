<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;

class StafAdminController extends Controller
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
    // GET /stafadmin/procurement — Daftar draf yang sudah disetujui Kaprodi
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

            // Staf Admin hanya melihat draf yang berstatus 'approved' (disetujui Kaprodi)
            $drafts = array_values(array_filter($allDrafts, function ($d) {
                return ($d['status'] ?? '') === 'approved';
            }));

            $error = $response->successful() ? null : ($response->json()['message'] ?? 'Gagal mengambil data draf pengadaan.');
        } catch (\Exception $e) {
            $drafts = [];
            $error = 'Tidak dapat terhubung ke server backend. Pastikan server Node.js berjalan.';
        }

        return view('stafadmin.procurement.index', [
            'user'   => Session::get('user', []),
            'drafts' => $drafts,
            'error'  => $error,
        ]);
    }

    // ─────────────────────────────────────────────
    // GET /stafadmin/procurement/{id} — Detail draf yang disetujui
    // ─────────────────────────────────────────────
    public function show($id)
    {
        try {
            $response = Http::withHeaders($this->authHeaders())
                ->get("{$this->apiUrl()}/api/procurement/{$id}");

            if (!$response->successful()) {
                return redirect()->route('stafadmin.procurement.index')
                    ->withErrors(['api' => 'Draf pengadaan tidak ditemukan atau gagal diambil.']);
            }

            $data  = $response->json();
            $draft = $data['draft'] ?? null;
            $items = $data['items'] ?? [];

            // Pastikan hanya draf yang approved yang bisa dilihat Staf Admin
            if (($draft['status'] ?? '') !== 'approved') {
                return redirect()->route('stafadmin.procurement.index')
                    ->withErrors(['api' => 'Draf ini belum disetujui oleh Ketua Program Studi.']);
            }
        } catch (\Exception $e) {
            return redirect()->route('stafadmin.procurement.index')
                ->withErrors(['api' => 'Tidak dapat terhubung ke server backend.']);
        }

        return view('stafadmin.procurement.show', [
            'user'  => Session::get('user', []),
            'draft' => $draft,
            'items' => $items,
        ]);
    }

    // ─────────────────────────────────────────────
    // GET /stafadmin/inventaris — Daftar barang inventaris dari semua draf disetujui
    // ─────────────────────────────────────────────
    public function inventaris()
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

            // Filter hanya draf approved
            $approvedDrafts = array_values(array_filter($allDrafts, fn($d) => ($d['status'] ?? '') === 'approved'));

            // Kumpulkan semua item inventaris dari setiap draf approved
            $inventarisItems = [];
            foreach ($approvedDrafts as $draft) {
                $detailResp = Http::withHeaders($this->authHeaders())
                    ->get("{$this->apiUrl()}/api/procurement/{$draft['id']}");

                if (!$detailResp->successful()) continue;

                $items = $detailResp->json()['items'] ?? [];
                foreach ($items as $index => $item) {
                    if (($item['item_type'] ?? '') === 'inventaris') {
                        $item['draft_id']    = $draft['id'];
                        $item['draft_title'] = $draft['title'];
                        $item['draft_year']  = $draft['year'];
                        $item['original_index'] = $index;
                        $inventarisItems[]   = $item;
                    }
                }
            }

            $error = $response->successful() ? null : ($response->json()['message'] ?? 'Gagal mengambil data.');
        } catch (\Exception $e) {
            $inventarisItems = [];
            $error = 'Tidak dapat terhubung ke server backend. Pastikan server Node.js berjalan.';
        }

        return view('stafadmin.inventaris.index', [
            'user'            => Session::get('user', []),
            'inventarisItems' => $inventarisItems,
            'error'           => $error ?? null,
        ]);
    }
}
