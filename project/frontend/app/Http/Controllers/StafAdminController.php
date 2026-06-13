<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;

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
    // GET /stafadmin/inventaris — Aset hasil pengadaan untuk labeling
    // ─────────────────────────────────────────────
    public function inventaris()
    {
        try {
            $response = Http::withHeaders($this->authHeaders())
                ->get("{$this->apiUrl()}/api/assets/procurement");

            if (in_array($response->status(), [401, 403])) {
                Session::forget('token');
                Session::forget('user');
                return redirect()->route('login')
                    ->withErrors(['email' => 'Sesi Anda telah berakhir. Silakan login kembali.']);
            }

            $assets = $response->successful() ? ($response->json()['assets'] ?? []) : [];
            $error  = $response->successful() ? null : ($response->json()['message'] ?? 'Gagal mengambil data inventaris.');
        } catch (\Exception $e) {
            $assets = [];
            $error  = 'Tidak dapat terhubung ke server backend. Pastikan server Node.js berjalan.';
        }

        return view('stafadmin.inventaris.index', [
            'user'   => Session::get('user', []),
            'assets' => $assets,
            'error'  => $error ?? null,
        ]);
    }

    // ─────────────────────────────────────────────
    // POST /stafadmin/inventaris/bulk-label — Simpan multiple label sekaligus
    // ─────────────────────────────────────────────
    public function bulkSaveLabels(Request $request)
    {
        $request->validate([
            'labels'             => 'required|array',
            'qr_data_urls'       => 'nullable|array',
            'univ_qr_data_urls'  => 'nullable|array',
        ]);

        $labels = $request->input('labels', []);
        $qrUrls = $request->input('qr_data_urls', []);
        $univUrls = $request->input('univ_qr_data_urls', []);

        try {
            $hasError = false;
            $errorMessage = '';

            foreach ($labels as $id => $label_number) {
                // Simpan QR sistem
                $qrPath = null;
                $dataUrl = $qrUrls[$id] ?? null;
                if ($dataUrl && str_starts_with($dataUrl, 'data:image')) {
                    $parts = explode(',', $dataUrl, 2);
                    if (count($parts) === 2) {
                        $binary = base64_decode($parts[1]);
                        if ($binary !== false) {
                            $filename = "qr/asset-{$id}.png";
                            Storage::disk('public')->put($filename, $binary);
                            $qrPath = Storage::url($filename);
                        }
                    }
                }

                // Simpan QR universitas
                $univQrPath = null;
                $univDataUrl = $univUrls[$id] ?? null;
                if ($univDataUrl && str_starts_with($univDataUrl, 'data:image')) {
                    $parts = explode(',', $univDataUrl, 2);
                    if (count($parts) === 2) {
                        $binary = base64_decode($parts[1]);
                        if ($binary !== false) {
                            $filename = "qr/univ-asset-{$id}.png";
                            Storage::disk('public')->put($filename, $binary);
                            $univQrPath = Storage::url($filename);
                        }
                    }
                }

                $payload = [
                    'label_number' => $label_number,
                ];
                if ($qrPath !== null) {
                    $payload['qr_path'] = $qrPath;
                }
                if ($univQrPath !== null) {
                    $payload['univ_qr_path'] = $univQrPath;
                }

                $response = Http::withHeaders($this->authHeaders())
                    ->patch("{$this->apiUrl()}/api/assets/{$id}", $payload);

                if (!$response->successful()) {
                    $hasError = true;
                    $errorMessage = $response->json()['message'] ?? 'Gagal menyimpan data inventaris.';
                    break;
                }
            }

            if ($hasError) {
                return redirect()->route('stafadmin.inventaris.index')
                    ->withErrors(['api' => $errorMessage]);
            }

            return redirect()->route('stafadmin.inventaris.index')
                ->with('success', 'Data inventaris berhasil disimpan secara massal.');
        } catch (\Exception $e) {
            return redirect()->route('stafadmin.inventaris.index')
                ->withErrors(['api' => 'Tidak dapat terhubung ke server backend.']);
        }
    }

    // ─────────────────────────────────────────────
    // POST /stafadmin/inventaris/procurement-item/{sourceId}/received
    // Simpan tanggal diterima ke SEMUA unit dengan source_item_id yang sama
    // ─────────────────────────────────────────────
    public function setReceivedDate(Request $request, $sourceId)
    {
        $request->validate([
            'received_date' => 'required|date',
        ]);

        try {
            $response = Http::withHeaders($this->authHeaders())
                ->patch("{$this->apiUrl()}/api/assets/by-source/{$sourceId}/received", [
                    'received_date' => $request->input('received_date'),
                ]);

            if ($response->successful()) {
                return redirect()->route('stafadmin.inventaris.index')
                    ->with('success', 'Tanggal diterima berhasil disimpan untuk semua unit barang.');
            }

            return redirect()->route('stafadmin.inventaris.index')
                ->withErrors(['api' => $response->json()['message'] ?? 'Gagal menyimpan tanggal diterima.']);
        } catch (\Exception $e) {
            return redirect()->route('stafadmin.inventaris.index')
                ->withErrors(['api' => 'Tidak dapat terhubung ke server backend.']);
        }
    }
}
