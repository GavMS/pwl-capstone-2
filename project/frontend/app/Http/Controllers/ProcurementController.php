<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;

class ProcurementController extends Controller
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
    // GET /kalab/procurement — Daftar draf pengadaan
    // ─────────────────────────────────────────────
    public function index()
    {
        try {
            $response = Http::withHeaders($this->authHeaders())
                ->get("{$this->apiUrl()}/api/procurement");

            // Jika token expired
            if (in_array($response->status(), [401, 403])) {
                Session::forget('token');
                Session::forget('user');
                return redirect()->route('login')
                    ->withErrors(['email' => 'Sesi Anda telah berakhir. Silakan login kembali.']);
            }

            $drafts = $response->successful() ? ($response->json()['drafts'] ?? []) : [];
            $error = $response->successful() ? null : ($response->json()['message'] ?? 'Gagal mengambil data draf pengadaan.');
        } catch (\Exception $e) {
            $drafts = [];
            $error = 'Tidak dapat terhubung ke server backend. Pastikan server Node.js berjalan.';
        }

        return view('kalab.procurement.index', [
            'user'   => Session::get('user', []),
            'drafts' => $drafts,
            'error'  => $error,
        ]);
    }

    // ─────────────────────────────────────────────
    // GET /kalab/procurement/create — Form tambah draf
    // ─────────────────────────────────────────────
    public function create()
    {
        $assets = $this->fetchAssets();

        return view('kalab.procurement.form', [
            'user'      => Session::get('user', []),
            'assets'    => $assets,
            'editDraft' => null,
            'items'     => [],
        ]);
    }

    // ─────────────────────────────────────────────
    // POST /kalab/procurement — Simpan draf baru
    // ─────────────────────────────────────────────
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'year'  => 'required|integer|min:2020|max:2100',
            'status'=> 'required|string|in:draft,submitted,approved',
            'items' => 'required|array',
            'items.*.name' => 'required|string|max:255',
            'items.*.item_type' => 'required|string|in:inventaris,bhp',
            'items.*.price' => 'required|numeric|min:0',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.purchase_link' => 'nullable|string',
            'items.*.replaced_asset_id' => 'nullable|integer',
            'items.*.notes' => 'nullable|string',
        ]);

        try {
            $response = Http::withHeaders($this->authHeaders())
                ->post("{$this->apiUrl()}/api/procurement", $request->only('title', 'year', 'status', 'items'));

            if ($response->successful()) {
                return redirect()->route('kalab.procurement.index')
                    ->with('success', 'Draf pengadaan berhasil disimpan.');
            }

            return back()->withErrors(['api' => $response->json()['message'] ?? 'Gagal menyimpan draf pengadaan.'])->withInput();
        } catch (\Exception $e) {
            return back()->withErrors(['api' => 'Tidak dapat terhubung ke server backend.'])->withInput();
        }
    }

    // ─────────────────────────────────────────────
    // GET /kalab/procurement/{id} — Detail satu draf
    // ─────────────────────────────────────────────
    public function show($id)
    {
        try {
            $response = Http::withHeaders($this->authHeaders())
                ->get("{$this->apiUrl()}/api/procurement/{$id}");

            if (!$response->successful()) {
                return redirect()->route('kalab.procurement.index')
                    ->withErrors(['api' => 'Draf pengadaan tidak ditemukan atau gagal diambil.']);
            }

            $data = $response->json();
            $draft = $data['draft'] ?? null;
            $items = $data['items'] ?? [];
        } catch (\Exception $e) {
            return redirect()->route('kalab.procurement.index')
                ->withErrors(['api' => 'Tidak dapat terhubung ke server backend.']);
        }

        return view('kalab.procurement.show', [
            'user'  => Session::get('user', []),
            'draft' => $draft,
            'items' => $items,
        ]);
    }

    // ─────────────────────────────────────────────
    // GET /kalab/procurement/{id}/edit — Form edit draf
    // ─────────────────────────────────────────────
    public function edit($id)
    {
        $assets = $this->fetchAssets();

        try {
            $response = Http::withHeaders($this->authHeaders())
                ->get("{$this->apiUrl()}/api/procurement/{$id}");

            if (!$response->successful()) {
                return redirect()->route('kalab.procurement.index')
                    ->withErrors(['api' => 'Draf pengadaan tidak ditemukan.']);
            }

            $data = $response->json();
            $editDraft = $data['draft'] ?? null;
            $items = $data['items'] ?? [];
        } catch (\Exception $e) {
            return redirect()->route('kalab.procurement.index')
                ->withErrors(['api' => 'Tidak dapat terhubung ke server backend.']);
        }

        return view('kalab.procurement.form', [
            'user'      => Session::get('user', []),
            'assets'    => $assets,
            'editDraft' => $editDraft,
            'items'     => $items,
        ]);
    }

    // ─────────────────────────────────────────────
    // PUT /kalab/procurement/{id} — Update draf
    // ─────────────────────────────────────────────
    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'year'  => 'required|integer|min:2020|max:2100',
            'status'=> 'required|string|in:draft,submitted,approved',
            'items' => 'required|array',
            'items.*.name' => 'required|string|max:255',
            'items.*.item_type' => 'required|string|in:inventaris,bhp',
            'items.*.price' => 'required|numeric|min:0',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.purchase_link' => 'nullable|string',
            'items.*.replaced_asset_id' => 'nullable|integer',
            'items.*.notes' => 'nullable|string',
        ]);

        try {
            $response = Http::withHeaders($this->authHeaders())
                ->put("{$this->apiUrl()}/api/procurement/{$id}", $request->only('title', 'year', 'status', 'items'));

            if ($response->successful()) {
                return redirect()->route('kalab.procurement.index')
                    ->with('success', 'Draf pengadaan berhasil diperbarui.');
            }

            return back()->withErrors(['api' => $response->json()['message'] ?? 'Gagal memperbarui draf pengadaan.'])->withInput();
        } catch (\Exception $e) {
            return back()->withErrors(['api' => 'Tidak dapat terhubung ke server backend.'])->withInput();
        }
    }

    // ─────────────────────────────────────────────
    // DELETE /kalab/procurement/{id} — Hapus draf
    // ─────────────────────────────────────────────
    public function destroy($id)
    {
        try {
            $response = Http::withHeaders($this->authHeaders())
                ->delete("{$this->apiUrl()}/api/procurement/{$id}");

            if ($response->successful()) {
                return redirect()->route('kalab.procurement.index')
                    ->with('success', 'Draf pengadaan berhasil dihapus.');
            }

            return redirect()->route('kalab.procurement.index')
                ->withErrors(['api' => $response->json()['message'] ?? 'Gagal menghapus draf pengadaan.']);
        } catch (\Exception $e) {
            return redirect()->route('kalab.procurement.index')
                ->withErrors(['api' => 'Tidak dapat terhubung ke server backend.']);
        }
    }

    // ─────────────────────────────────────────────
    // Helper: Ambil daftar aset dari backend untuk dropdown penggantian
    // ─────────────────────────────────────────────
    private function fetchAssets(): array
    {
        try {
            $response = Http::withHeaders($this->authHeaders())
                ->get("{$this->apiUrl()}/api/procurement/assets/list");
            return $response->successful() ? ($response->json()['assets'] ?? []) : [];
        } catch (\Exception $e) {
            return [];
        }
    }
}
