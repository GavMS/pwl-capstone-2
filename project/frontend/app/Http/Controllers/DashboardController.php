<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Http;

class DashboardController extends Controller
{
    private function getUser(): array
    {
        return Session::get('user', []);
    }

    private function apiUrl(): string
    {
        return env('NODEJS_API_URL', 'http://localhost:5000');
    }

    private function authHeaders(): array
    {
        return ['Authorization' => 'Bearer ' . Session::get('token', '')];
    }

    private function fetchProcurementStats(): array
    {
        try {
            $r = Http::withHeaders($this->authHeaders())
                ->get("{$this->apiUrl()}/api/procurement/stats");
            return $r->successful() ? ($r->json()['stats'] ?? []) : [];
        } catch (\Exception $e) {
            return [];
        }
    }

    private function fetchGeneralStats(): array
    {
        try {
            $r = Http::withHeaders($this->authHeaders())
                ->get("{$this->apiUrl()}/api/users/dashboard-stats");
            return $r->successful() ? ($r->json()['stats'] ?? []) : [];
        } catch (\Exception $e) {
            return [];
        }
    }

    public function admin()
    {
        $stats       = [];
        $recentUsers = [];

        try {
            $response = Http::withHeaders($this->authHeaders())
                ->get("{$this->apiUrl()}/api/users/dashboard-stats");

            if ($response->successful()) {
                $data        = $response->json();
                $stats       = $data['stats']       ?? [];
                $recentUsers = $data['recentUsers'] ?? [];
            }
        } catch (\Exception $e) {
            // biarkan stats kosong — tampilkan 0
        }

        return view('dashboard.admin', [
            'user'        => $this->getUser(),
            'stats'       => $stats,
            'recentUsers' => $recentUsers,
        ]);
    }

    public function kalab()
    {
        return view('dashboard.kalab', [
            'user'      => $this->getUser(),
            'procStats' => $this->fetchProcurementStats(),
        ]);
    }

    public function kaprodi()
    {
        return view('dashboard.kaprodi', [
            'user'      => $this->getUser(),
            'procStats' => $this->fetchProcurementStats(),
        ]);
    }

    public function stafadmin()
    {
        return view('dashboard.stafadmin', [
            'user'      => $this->getUser(),
            'procStats' => $this->fetchProcurementStats(),
        ]);
    }

    public function staflab()
    {
        return view('dashboard.staflab', [
            'user' => $this->getUser(),
        ]);
    }
}
