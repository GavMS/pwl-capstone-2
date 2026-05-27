<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;

class UserManagementController extends Controller
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
    // GET /admin/users — Daftar semua user
    // ─────────────────────────────────────────────
    public function index()
    {
        try {
            $response = Http::withHeaders($this->authHeaders())
                ->get("{$this->apiUrl()}/api/users");

            // Token expired / tidak valid → paksa login ulang
            if (in_array($response->status(), [401, 403])) {
                Session::forget('token');
                Session::forget('user');
                return redirect()->route('login')
                    ->withErrors(['email' => 'Sesi Anda telah berakhir. Silakan login kembali.']);
            }

            $users = $response->successful() ? ($response->json()['users'] ?? []) : [];
            $error = $response->successful() ? null : ($response->json()['message'] ?? 'Gagal mengambil data user.');
        } catch (\Exception $e) {
            $users = [];
            $error = 'Tidak dapat terhubung ke server. Pastikan backend berjalan.';
        }

        return view('admin.users.index', [
            'user'  => Session::get('user', []),
            'users' => $users,
            'error' => $error,
        ]);
    }

    // ─────────────────────────────────────────────
    // GET /admin/users/create — Form tambah user
    // ─────────────────────────────────────────────
    public function create()
    {
        $roles = $this->fetchRoles();

        return view('admin.users.form', [
            'user'      => Session::get('user', []),
            'roles'     => $roles,
            'editUser'  => null,
            'deps'      => [],
        ]);
    }

    // ─────────────────────────────────────────────
    // POST /admin/users — Simpan user baru
    // ─────────────────────────────────────────────
    public function store(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|max:255',
            'password' => 'required|string|min:6',
            'role_id'  => 'required|integer',
        ]);

        try {
            $response = Http::withHeaders($this->authHeaders())
                ->post("{$this->apiUrl()}/api/users", $request->only('name', 'email', 'password', 'role_id'));

            if ($response->successful()) {
                return redirect()->route('admin.users.index')
                    ->with('success', 'User berhasil ditambahkan.');
            }

            return back()->withErrors(['api' => $response->json()['message'] ?? 'Gagal menambahkan user.'])->withInput();
        } catch (\Exception $e) {
            return back()->withErrors(['api' => 'Tidak dapat terhubung ke server.'])->withInput();
        }
    }

    // ─────────────────────────────────────────────
    // GET /admin/users/{id}/edit — Form edit user (Smart Edit)
    // ─────────────────────────────────────────────
    public function edit(string $id)
    {
        $roles = $this->fetchRoles();

        try {
            $response = Http::withHeaders($this->authHeaders())
                ->get("{$this->apiUrl()}/api/users/{$id}");

            if (!$response->successful()) {
                return redirect()->route('admin.users.index')
                    ->withErrors(['api' => 'User tidak ditemukan.']);
            }

            $data     = $response->json();
            $editUser = $data['user'] ?? null;
            $deps     = $data['dependencies'] ?? [];
        } catch (\Exception $e) {
            return redirect()->route('admin.users.index')
                ->withErrors(['api' => 'Tidak dapat terhubung ke server.']);
        }

        return view('admin.users.form', [
            'user'     => Session::get('user', []),
            'roles'    => $roles,
            'editUser' => $editUser,
            'deps'     => $deps,
        ]);
    }

    // ─────────────────────────────────────────────
    // PUT /admin/users/{id} — Update user (Smart Edit)
    // ─────────────────────────────────────────────
    public function update(Request $request, string $id)
    {
        $request->validate([
            'name'    => 'required|string|max:255',
            'email'   => 'required|email|max:255',
            'role_id' => 'required|integer',
            'password' => 'nullable|string|min:6',
        ]);

        try {
            $payload = $request->only('name', 'email', 'role_id');
            if ($request->filled('password')) {
                $payload['password'] = $request->password;
            }

            $response = Http::withHeaders($this->authHeaders())
                ->put("{$this->apiUrl()}/api/users/{$id}", $payload);

            if ($response->successful()) {
                return redirect()->route('admin.users.index')
                    ->with('success', 'User berhasil diperbarui.');
            }

            return back()->withErrors(['api' => $response->json()['message'] ?? 'Gagal memperbarui user.'])->withInput();
        } catch (\Exception $e) {
            return back()->withErrors(['api' => 'Tidak dapat terhubung ke server.'])->withInput();
        }
    }

    // ─────────────────────────────────────────────
    // GET /admin/users/{id}/check-delete — AJAX: cek sebelum hapus (Smart Delete)
    // ─────────────────────────────────────────────
    public function checkDelete(string $id)
    {
        try {
            $response = Http::withHeaders($this->authHeaders())
                ->get("{$this->apiUrl()}/api/users/{$id}/check-delete");

            return response()->json($response->json(), $response->status());
        } catch (\Exception $e) {
            return response()->json(['message' => 'Tidak dapat terhubung ke server.'], 500);
        }
    }

    // ─────────────────────────────────────────────
    // DELETE /admin/users/{id} — Hapus user (Smart Delete)
    // ─────────────────────────────────────────────
    public function destroy(string $id)
    {
        try {
            $response = Http::withHeaders($this->authHeaders())
                ->delete("{$this->apiUrl()}/api/users/{$id}");

            if ($response->successful()) {
                return redirect()->route('admin.users.index')
                    ->with('success', 'User berhasil dihapus.');
            }

            $msg = $response->json()['message'] ?? 'Gagal menghapus user.';
            return redirect()->route('admin.users.index')->withErrors(['api' => $msg]);
        } catch (\Exception $e) {
            return redirect()->route('admin.users.index')
                ->withErrors(['api' => 'Tidak dapat terhubung ke server.']);
        }
    }

    // ─────────────────────────────────────────────
    // Helper: Ambil daftar roles dari API
    // ─────────────────────────────────────────────
    private function fetchRoles(): array
    {
        try {
            $response = Http::withHeaders($this->authHeaders())
                ->get("{$this->apiUrl()}/api/users/roles");
            return $response->successful() ? ($response->json()['roles'] ?? []) : [];
        } catch (\Exception $e) {
            return [];
        }
    }
}
