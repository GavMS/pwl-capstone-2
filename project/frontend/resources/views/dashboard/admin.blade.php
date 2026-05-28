@extends('dashboard.layout')
@section('title', 'Dashboard Administrator')
@section('page_title', 'Dashboard')

@section('content')
@php
    $firstName = explode(' ', $user['name'] ?? 'User')[0];
    $stats = $stats ?? [];
@endphp

<style>
/* ─── Greeting ─────────────────────────────────── */
.greeting-title {
    font-size: 1.5rem; font-weight: 700; color: #344767; margin: 0 0 .25rem;
}
.greeting-sub {
    font-size: .875rem; color: #7b809a; margin: 0;
}

/* ─── Stat Cards ───────────────────────────────── */
.stat-card {
    background: #fff;
    border-radius: 1rem;
    padding: 1.25rem 1.5rem;
    border: 1px solid #eef0f5;
    box-shadow: 0 1px 3px rgba(0,0,0,0.04), 0 8px 20px -12px rgba(0,0,0,0.08);
    display: flex;
    align-items: center;
    gap: 1rem;
    transition: transform .18s ease, box-shadow .18s ease;
    height: 100%;
}
.stat-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,.04), 0 14px 30px -12px rgba(0,0,0,.12);
}
.stat-icon {
    width: 3rem; height: 3rem;
    border-radius: .75rem;
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0;
    font-size: 1.1rem;
}
.stat-icon-users    { background: #eef2ff; color: #4338ca; }
.stat-icon-rooms    { background: #f0fdf4; color: #15803d; }
.stat-icon-assets   { background: #fff7ed; color: #c2410c; }
.stat-icon-bhp      { background: #fdf2ff; color: #7928ca; }

.stat-text { min-width: 0; flex: 1; }
.stat-label {
    font-size: .8125rem;
    color: #7b809a;
    margin: 0 0 .375rem;
    font-weight: 500;
}
.stat-number {
    font-size: 1.75rem;
    font-weight: 700;
    color: #344767;
    line-height: 1;
    margin: 0 0 .35rem;
}
.stat-sub {
    font-size: .7rem;
    color: #adb5bd;
    margin: 0;
}

/* ─── Section Card (Pengguna Terbaru) ──────────── */
.section-card {
    background: #fff;
    border-radius: 1rem;
    border: 1px solid #eef0f5;
    box-shadow: 0 1px 3px rgba(0,0,0,0.04), 0 8px 20px -12px rgba(0,0,0,0.08);
    overflow: hidden;
}
.section-card-header {
    display: flex; align-items: center; justify-content: space-between;
    padding: 1.125rem 1.5rem;
    border-bottom: 1px solid #f0f2f5;
}
.section-card-title {
    font-size: 1rem; font-weight: 700; color: #344767; margin: 0;
    display: flex; align-items: center; gap: .5rem;
}
.section-card-title i { color: #7928ca; font-size: .8rem; }
.section-card-link {
    font-size: .75rem; font-weight: 600; color: #7928ca; text-decoration: none;
    display: inline-flex; align-items: center; gap: .25rem;
}
.section-card-link:hover { color: #ff007f; }

/* ─── Recent Users Table ───────────────────────── */
.recent-table { width: 100%; border-collapse: collapse; }
.recent-table thead th {
    padding: .75rem 1.5rem;
    font-size: .65rem; font-weight: 700;
    text-transform: uppercase; letter-spacing: .05em; color: #adb5bd;
    border-bottom: 1px solid #f0f2f5; white-space: nowrap; text-align: left;
    background: #fafbfc;
}
.recent-table tbody td {
    padding: .875rem 1.5rem;
    border-bottom: 1px solid #f0f2f5; vertical-align: middle;
}
.recent-table tbody tr:last-child td { border-bottom: none; }
.recent-table tbody tr:hover { background: #fafbfc; }
.user-avatar-sm {
    width: 2.25rem; height: 2.25rem; border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    color: #fff; font-size: .75rem; font-weight: 700; flex-shrink: 0;
    background: linear-gradient(310deg, #7928ca, #ff007f);
    box-shadow: 0 4px 6px -1px rgba(121,40,202,.4);
}
.status-aktif {
    display: inline-flex; align-items: center; gap: .35rem;
    padding: .25rem .7rem; border-radius: 9999px;
    font-size: .7rem; font-weight: 600;
    background: #f0fdf4; color: #15803d;
}
.status-aktif::before {
    content: ''; width: 6px; height: 6px;
    border-radius: 50%; background: #16a34a; flex-shrink: 0;
}
</style>

{{-- Greeting --}}
<div style="margin-bottom: 1.5rem;">
    <h4 class="greeting-title">Halo, {{ $firstName }} 👋</h4>
    <p class="greeting-sub">Ringkasan sistem AsetLab hari ini</p>
</div>

{{-- Stat Cards --}}
<div class="flex flex-wrap -mx-3 mb-6">
    <div class="w-full max-w-full px-3 mb-4 sm:w-1/2 xl:w-1/4 xl:mb-0">
        <div class="stat-card">
            <div class="stat-icon stat-icon-users">
                <i class="fas fa-user-check"></i>
            </div>
            <div class="stat-text">
                <p class="stat-label">Pengguna Aktif</p>
                <h3 class="stat-number">{{ $stats['active_users'] ?? 0 }}</h3>
                <p class="stat-sub">{{ $stats['total_users'] ?? 0 }} total terdaftar</p>
            </div>
        </div>
    </div>
    <div class="w-full max-w-full px-3 mb-4 sm:w-1/2 xl:w-1/4 xl:mb-0">
        <div class="stat-card">
            <div class="stat-icon stat-icon-rooms">
                <i class="fas fa-door-open"></i>
            </div>
            <div class="stat-text">
                <p class="stat-label">Ruangan / Lab</p>
                <h3 class="stat-number">{{ $stats['room_count'] ?? 0 }}</h3>
                <p class="stat-sub">Tersebar di {{ $stats['building_count'] ?? 0 }} gedung</p>
            </div>
        </div>
    </div>
    <div class="w-full max-w-full px-3 mb-4 sm:w-1/2 xl:w-1/4 xl:mb-0">
        <div class="stat-card">
            <div class="stat-icon stat-icon-assets">
                <i class="fas fa-boxes"></i>
            </div>
            <div class="stat-text">
                <p class="stat-label">Aset Inventaris</p>
                <h3 class="stat-number">{{ $stats['asset_count'] ?? 0 }}</h3>
                <p class="stat-sub">Tercatat sistem</p>
            </div>
        </div>
    </div>
    <div class="w-full max-w-full px-3 mb-4 sm:w-1/2 xl:w-1/4 xl:mb-0">
        <div class="stat-card">
            <div class="stat-icon stat-icon-bhp">
                <i class="fas fa-flask"></i>
            </div>
            <div class="stat-text">
                <p class="stat-label">Item BHP</p>
                <h3 class="stat-number">{{ $stats['bhp_count'] ?? 0 }}</h3>
                <p class="stat-sub">{{ $stats['low_stock_count'] ?? 0 }} di bawah stok minimum</p>
            </div>
        </div>
    </div>
</div>

{{-- Pengguna Terbaru (full width) --}}
<div class="section-card">
    <div class="section-card-header">
        <h6 class="section-card-title">
            <i class="fas fa-clock"></i>
            Pengguna terbaru
        </h6>
        <a href="{{ route('admin.users.index') }}" class="section-card-link">
            Lihat semua <i class="fas fa-arrow-right" style="font-size:.65rem;"></i>
        </a>
    </div>
    <div style="overflow-x:auto;">
        <table class="recent-table">
            <thead>
                <tr>
                    <th>Nama</th>
                    <th>Role</th>
                    <th>Email</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($recentUsers ?? [] as $u)
                <tr>
                    <td>
                        <div style="display:flex; align-items:center; gap:.75rem;">
                            <div class="user-avatar-sm">{{ strtoupper(substr($u['name'] ?? 'U', 0, 2)) }}</div>
                            <span style="font-size:.875rem; font-weight:600; color:#344767;">{{ $u['name'] ?? '-' }}</span>
                        </div>
                    </td>
                    <td style="font-size:.875rem; color:#7b809a;">{{ $u['role'] ?? '-' }}</td>
                    <td style="font-size:.875rem; color:#7b809a;">{{ $u['email'] ?? '-' }}</td>
                    <td><span class="status-aktif">Aktif</span></td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" style="text-align:center; padding:2.5rem 1.5rem; font-size:.875rem; color:#adb5bd;">
                        <i class="fas fa-inbox" style="display:block; font-size:1.75rem; color:#d2d6da; margin-bottom:.5rem;"></i>
                        Tidak ada data pengguna.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection
