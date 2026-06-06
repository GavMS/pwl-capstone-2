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

    // ─────────────────────────────────────────────
    // Fetch stats khusus untuk dashboard Staf Lab
    // ─────────────────────────────────────────────
    private function fetchStafLabStats(): array
    {
        $headers = $this->authHeaders();
        $api     = $this->apiUrl();

        // Ambil semua BHP
        $consumables = [];
        try {
            $r = Http::withHeaders($headers)->get("{$api}/api/consumables");
            if ($r->successful()) {
                $consumables = $r->json()['consumables'] ?? [];
            }
        } catch (\Exception $e) {}

        // Ambil semua aset
        $assets = [];
        try {
            $r = Http::withHeaders($headers)->get("{$api}/api/assets");
            if ($r->successful()) {
                $assets = $r->json()['assets'] ?? [];
            }
        } catch (\Exception $e) {}

        // Ambil semua log maintenance
        $logs = [];
        try {
            $r = Http::withHeaders($headers)->get("{$api}/api/maintenance");
            if ($r->successful()) {
                $logs = $r->json()['logs'] ?? [];
            }
        } catch (\Exception $e) {}

        // ── Kalkulasi BHP stats
        $totalBhp    = count($consumables);
        $criticalBhp = collect($consumables)->filter(fn($c) => ($c['stock'] ?? 0) <= ($c['min_stock'] ?? 0));
        $totalNilai  = collect($consumables)->sum(fn($c) => ($c['stock'] ?? 0) * ($c['price'] ?? 0));

        // ── Kalkulasi Aset stats (breakdown kondisi)
        $totalAset  = count($assets);
        $asetByKond = collect($assets)->groupBy('condition_status')->map->count();

        // ── Kalkulasi Maintenance bulan ini
        $bulanIni     = now()->format('Y-m');
        $logsThisMonth = collect($logs)->filter(function($l) use ($bulanIni) {
            return isset($l['maintenance_date']) && str_starts_with($l['maintenance_date'], $bulanIni);
        });
        $totalBiayaBulanIni = $logsThisMonth->sum(fn($l) => $l['cost'] ?? 0);

        return [
            // BHP
            'total_bhp'       => $totalBhp,
            'critical_bhp'    => $criticalBhp->count(),
            'total_nilai_bhp' => $totalNilai,
            'critical_items'  => $criticalBhp->values()->toArray(),

            // Aset
            'total_aset'     => $totalAset,
            'aset_baik'      => $asetByKond['Baik'] ?? 0,
            'aset_maint'     => $asetByKond['Perlu Maintenance'] ?? 0,
            'aset_rusak_ringan' => $asetByKond['Rusak Ringan'] ?? 0,
            'aset_rusak_berat'  => $asetByKond['Rusak Berat'] ?? 0,

            // Maintenance
            'total_logs'           => count($logs),
            'logs_bulan_ini'       => $logsThisMonth->count(),
            'biaya_bulan_ini'      => $totalBiayaBulanIni,
            'recent_logs'          => collect($logs)->take(5)->values()->toArray(),
        ];
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
        $labStats = $this->fetchStafLabStats();

        return view('dashboard.staflab', [
            'user'      => $this->getUser(),
            'labStats'  => $labStats,
        ]);
    }
}
