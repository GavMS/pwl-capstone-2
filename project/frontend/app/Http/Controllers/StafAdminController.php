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
    // POST /stafadmin/inventaris/{id}/label — Simpan label + QR (file) + tanggal terima
    // ─────────────────────────────────────────────
    public function saveLabel(Request $request, $id)
    {
        $request->validate([
            'label_number'  => 'nullable|string|max:255',
            'received_date' => 'nullable|date',
            'qr_data_url'   => 'nullable|string',
        ]);

        try {
            // Simpan gambar QR (PNG base64 dari browser) ke storage publik
            $qrPath = null;
            $dataUrl = $request->input('qr_data_url');
            if ($dataUrl && str_starts_with($dataUrl, 'data:image')) {
                $parts = explode(',', $dataUrl, 2);
                if (count($parts) === 2) {
                    $binary = base64_decode($parts[1]);
                    if ($binary !== false) {
                        $filename = "qr/asset-{$id}.png";
                        Storage::disk('public')->put($filename, $binary);
                        $qrPath = Storage::url($filename); // /storage/qr/asset-{id}.png
                    }
                }
            }

            $payload = [
                'label_number'  => $request->input('label_number'),
                'received_date' => $request->input('received_date'),
            ];
            if ($qrPath !== null) {
                $payload['qr_path'] = $qrPath;
            }

            $response = Http::withHeaders($this->authHeaders())
                ->patch("{$this->apiUrl()}/api/assets/{$id}", $payload);

            if ($response->successful()) {
                return redirect()->route('stafadmin.inventaris.index')
                    ->with('success', 'Data inventaris berhasil disimpan.');
            }

            return redirect()->route('stafadmin.inventaris.index')
                ->withErrors(['api' => $response->json()['message'] ?? 'Gagal menyimpan data inventaris.']);
        } catch (\Exception $e) {
            return redirect()->route('stafadmin.inventaris.index')
                ->withErrors(['api' => 'Tidak dapat terhubung ke server backend.']);
        }
    }
}
