<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;

class RoomManagementController extends Controller
{
    private function apiUrl(): string
    {
        return env('NODEJS_API_URL', 'http://localhost:5000');
    }

    private function authHeaders(): array
    {
        return ['Authorization' => 'Bearer ' . Session::get('token', '')];
    }

    private function handleExpiredToken()
    {
        Session::forget('token');
        Session::forget('user');
        return redirect()->route('login')
            ->withErrors(['email' => 'Sesi Anda telah berakhir. Silakan login kembali.']);
    }

    // ─────────────────────────────────────────────
    // GET /admin/rooms — Daftar semua ruangan
    // ─────────────────────────────────────────────
    public function index()
    {
        try {
            $response = Http::withHeaders($this->authHeaders())
                ->get("{$this->apiUrl()}/api/rooms");

            if (in_array($response->status(), [401, 403])) {
                return $this->handleExpiredToken();
            }

            $rooms = $response->successful() ? ($response->json()['rooms'] ?? []) : [];
            $error = $response->successful() ? null : ($response->json()['message'] ?? 'Gagal mengambil data ruangan.');
        } catch (\Exception $e) {
            $rooms = [];
            $error = 'Tidak dapat terhubung ke server. Pastikan backend berjalan.';
        }

        return view('admin.rooms.index', [
            'user'  => Session::get('user', []),
            'rooms' => $rooms,
            'error' => $error,
        ]);
    }

    // ─────────────────────────────────────────────
    // GET /admin/rooms/create — Form tambah ruangan
    // ─────────────────────────────────────────────
    public function create()
    {
        return view('admin.rooms.form', [
            'user'     => Session::get('user', []),
            'editRoom' => null,
            'deps'     => [],
        ]);
    }

    // ─────────────────────────────────────────────
    // POST /admin/rooms — Simpan ruangan baru
    // ─────────────────────────────────────────────
    public function store(Request $request)
    {
        $request->validate([
            'name'        => 'required|string|max:255',
            'code'        => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        try {
            $response = Http::withHeaders($this->authHeaders())
                ->post("{$this->apiUrl()}/api/rooms", $request->only('name', 'code', 'description'));

            if ($response->successful()) {
                return redirect()->route('admin.rooms.index')
                    ->with('success', 'Ruangan berhasil ditambahkan.');
            }

            return back()->withErrors(['api' => $response->json()['message'] ?? 'Gagal menambahkan ruangan.'])->withInput();
        } catch (\Exception $e) {
            return back()->withErrors(['api' => 'Tidak dapat terhubung ke server.'])->withInput();
        }
    }

    // ─────────────────────────────────────────────
    // GET /admin/rooms/{id}/edit — Form edit (Smart Edit)
    // ─────────────────────────────────────────────
    public function edit(string $id)
    {
        try {
            $response = Http::withHeaders($this->authHeaders())
                ->get("{$this->apiUrl()}/api/rooms/{$id}/check-edit");

            if (!$response->successful()) {
                return redirect()->route('admin.rooms.index')
                    ->withErrors(['api' => 'Ruangan tidak ditemukan.']);
            }

            $data     = $response->json();
            $editRoom = $data['room'] ?? null;
            $deps     = $data['dependencies'] ?? [];
        } catch (\Exception $e) {
            return redirect()->route('admin.rooms.index')
                ->withErrors(['api' => 'Tidak dapat terhubung ke server.']);
        }

        return view('admin.rooms.form', [
            'user'     => Session::get('user', []),
            'editRoom' => $editRoom,
            'deps'     => $deps,
        ]);
    }

    // ─────────────────────────────────────────────
    // PUT /admin/rooms/{id} — Update ruangan
    // ─────────────────────────────────────────────
    public function update(Request $request, string $id)
    {
        $request->validate([
            'name'        => 'required|string|max:255',
            'code'        => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        try {
            $response = Http::withHeaders($this->authHeaders())
                ->put("{$this->apiUrl()}/api/rooms/{$id}", $request->only('name', 'code', 'description'));

            if ($response->successful()) {
                return redirect()->route('admin.rooms.index')
                    ->with('success', 'Ruangan berhasil diperbarui.');
            }

            return back()->withErrors(['api' => $response->json()['message'] ?? 'Gagal memperbarui ruangan.'])->withInput();
        } catch (\Exception $e) {
            return back()->withErrors(['api' => 'Tidak dapat terhubung ke server.'])->withInput();
        }
    }

    // ─────────────────────────────────────────────
    // GET /admin/rooms/{id}/check-delete — AJAX Smart Delete
    // ─────────────────────────────────────────────
    public function checkDelete(string $id)
    {
        try {
            $response = Http::withHeaders($this->authHeaders())
                ->get("{$this->apiUrl()}/api/rooms/{$id}/check-delete");

            return response()->json($response->json(), $response->status());
        } catch (\Exception $e) {
            return response()->json(['message' => 'Tidak dapat terhubung ke server.'], 500);
        }
    }

    // ─────────────────────────────────────────────
    // DELETE /admin/rooms/{id} — Hapus ruangan (Smart Delete)
    // ─────────────────────────────────────────────
    public function destroy(string $id)
    {
        try {
            $response = Http::withHeaders($this->authHeaders())
                ->delete("{$this->apiUrl()}/api/rooms/{$id}");

            if ($response->successful()) {
                return redirect()->route('admin.rooms.index')
                    ->with('success', 'Ruangan berhasil dihapus.');
            }

            $msg = $response->json()['message'] ?? 'Gagal menghapus ruangan.';
            return redirect()->route('admin.rooms.index')->withErrors(['api' => $msg]);
        } catch (\Exception $e) {
            return redirect()->route('admin.rooms.index')
                ->withErrors(['api' => 'Tidak dapat terhubung ke server.']);
        }
    }
}
