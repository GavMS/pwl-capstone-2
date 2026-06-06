@extends('dashboard.layout')
@section('title', 'Dashboard Staf Laboratorium')
@section('page_title', 'Dashboard')

@section('content')
@php
    $firstName = explode(' ', $user['name'] ?? 'User')[0];
    $s = $labStats ?? [];
@endphp

<style>
/* ─── Greeting ───────────────────────────────── */
.greeting-title { font-size: 1.5rem; font-weight: 700; color: #344767; margin: 0 0 .25rem; }
.greeting-sub   { font-size: .875rem; color: #7b809a; margin: 0; }

/* ─── Stats Row ──────────────────────────────── */
.stats-row {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
    gap: 1rem;
    margin-bottom: 1.5rem;
}
.stat-card {
    background: #fff;
    border-radius: 1rem;
    box-shadow: 0 20px 27px 0 rgba(0,0,0,.05);
    padding: 1.25rem 1.5rem;
    display: flex; align-items: center; gap: 1rem;
    transition: transform .18s ease, box-shadow .18s ease;
}
.stat-card:hover { transform: translateY(-2px); box-shadow: 0 8px 30px rgba(0,0,0,.1); }
.stat-icon {
    width: 3rem; height: 3rem; border-radius: .75rem;
    display: flex; align-items: center; justify-content: center;
    font-size: 1.2rem; flex-shrink: 0;
}
.stat-icon.purple { background: linear-gradient(310deg,#7928ca,#ff007f); color:#fff; }
.stat-icon.green  { background: linear-gradient(310deg,#17ad37,#98ec2d); color:#fff; }
.stat-icon.orange { background: linear-gradient(310deg,#f53939,#fbcf33); color:#fff; }
.stat-icon.blue   { background: linear-gradient(310deg,#2152ff,#21d4fd); color:#fff; }
.stat-icon.teal   { background: linear-gradient(310deg,#0093e9,#80d0c7); color:#fff; }
.stat-val { font-size: 1.5rem; font-weight: 800; color: #344767; line-height: 1; }
.stat-lbl { font-size: .72rem; color: #7b809a; margin-top: .2rem; }
.stat-sub { font-size: .7rem; color: #adb5bd; margin-top: .1rem; }

/* ─── Two-column grid ────────────────────────── */
.dash-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1.25rem;
    margin-bottom: 1.5rem;
}
@media (max-width: 768px) { .dash-grid { grid-template-columns: 1fr; } }

/* ─── Panel card ─────────────────────────────── */
.panel-card {
    background: #fff; border-radius: 1rem;
    box-shadow: 0 20px 27px 0 rgba(0,0,0,.05);
    overflow: hidden;
}
.panel-header {
    display: flex; align-items: center; justify-content: space-between;
    padding: 1rem 1.5rem; border-bottom: 1px solid #f0f2f5;
}
.panel-title {
    font-size: .9rem; font-weight: 700; color: #344767; margin: 0;
    display: flex; align-items: center; gap: .5rem;
}
.panel-title i { color: #7928ca; }
.panel-link {
    font-size: .75rem; font-weight: 600; color: #7928ca;
    text-decoration: none; display: inline-flex; align-items: center; gap: .25rem;
}
.panel-link:hover { color: #ff007f; }
.panel-body { padding: 1rem 1.5rem; }

/* ─── BHP Kritis table ───────────────────────── */
.kritis-table { width: 100%; border-collapse: collapse; }
.kritis-table thead th {
    padding: .6rem 1rem; font-size: .65rem; font-weight: 700;
    text-transform: uppercase; letter-spacing: .05em;
    color: #adb5bd; border-bottom: 1px solid #f0f2f5; text-align: left;
}
.kritis-table tbody td { padding: .75rem 1rem; border-bottom: 1px solid #f0f2f5; vertical-align: middle; }
.kritis-table tbody tr:last-child td { border-bottom: none; }
.kritis-table tbody tr:hover { background: #fafbfc; }
.item-name  { font-size: .875rem; font-weight: 600; color: #344767; }
.item-code  { font-size: .75rem; color: #adb5bd; font-family: monospace; }
.stock-kritis { font-size: .875rem; font-weight: 700; color: #c62828; }
.stock-min    { font-size: .75rem; color: #7b809a; }

/* ─── Kondisi bar ────────────────────────────── */
.kondisi-list { list-style: none; padding: 0; margin: 0; }
.kondisi-list li {
    display: flex; align-items: center; justify-content: space-between;
    padding: .6rem 0; border-bottom: 1px solid #f0f2f5;
    font-size: .875rem;
}
.kondisi-list li:last-child { border-bottom: none; }
.kondisi-label { display: flex; align-items: center; gap: .5rem; color: #344767; font-weight: 500; }
.kondisi-dot { width: 10px; height: 10px; border-radius: 50%; flex-shrink: 0; }
.dot-baik   { background: #4caf50; }
.dot-maint  { background: #ffb300; }
.dot-ringan { background: #f57c00; }
.dot-berat  { background: #d32f2f; }
.kondisi-count {
    font-weight: 700; font-size: .95rem; color: #344767;
    background: #f5f6fb; padding: .2rem .7rem; border-radius: .5rem;
}

/* ─── Recent Maintenance list ────────────────── */
.log-mini {
    border-bottom: 1px solid #f0f2f5; padding: .75rem 0;
    display: flex; gap: .75rem; align-items: flex-start;
}
.log-mini:last-child { border-bottom: none; }
.log-mini-icon {
    width: 2rem; height: 2rem; border-radius: .5rem;
    background: linear-gradient(310deg,#7928ca,#ff007f);
    display: flex; align-items: center; justify-content: center;
    color: #fff; font-size: .7rem; flex-shrink: 0;
}
.log-mini-body { flex: 1; min-width: 0; }
.log-mini-asset { font-size: .8rem; font-weight: 700; color: #344767; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.log-mini-desc  { font-size: .75rem; color: #7b809a; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.log-mini-date  { font-size: .7rem; color: #adb5bd; white-space: nowrap; }

/* ─── Alert kritis ───────────────────────────── */
.alert-kritis {
    display: flex; align-items: center; gap: .75rem;
    padding: .875rem 1.25rem; background: #fbe9e7;
    border: 1px solid #ffcdd2; border-radius: .75rem;
    margin-bottom: 1.5rem; font-size: .875rem; color: #c62828;
}
.alert-kritis i { font-size: 1.1rem; flex-shrink: 0; }

/* ─── Quick Access ───────────────────────────── */
.quick-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
    gap: 1.25rem;
    margin-top: 1.5rem;
}
.quick-card {
    background: #fff; border-radius: 1.25rem;
    border: 1px solid #eef0f5;
    box-shadow: 0 1px 3px rgba(0,0,0,.04), 0 8px 20px -12px rgba(0,0,0,.08);
    padding: 1.5rem 1.25rem;
    display: flex; flex-direction: column; gap: .875rem;
    transition: box-shadow .25s ease, transform .25s ease;
    text-decoration: none; color: inherit;
}
.quick-card:hover { box-shadow: 0 8px 30px rgba(121,40,202,.12); transform: translateY(-3px); }
.qc-icon {
    width: 3.25rem; height: 3.25rem; border-radius: 1rem;
    display: flex; align-items: center; justify-content: center; font-size: 1.3rem;
}
.qc-icon.purple { background: linear-gradient(310deg,#7928ca,#ff007f); color:#fff; }
.qc-icon.green  { background: linear-gradient(310deg,#17ad37,#98ec2d); color:#fff; }
.qc-icon.blue   { background: linear-gradient(310deg,#2152ff,#21d4fd); color:#fff; }
.qc-title { font-size: .9375rem; font-weight: 700; color: #344767; margin: 0; }
.qc-desc  { font-size: .8rem; color: #7b809a; margin: 0; line-height: 1.5; }
.qc-link  { display: inline-flex; align-items: center; gap: .35rem; font-size: .8rem; font-weight: 700; color: #7928ca; }
.qc-link i { font-size: .7rem; transition: transform .2s; }
.quick-card:hover .qc-link i { transform: translateX(3px); }

.section-title-row { display: flex; align-items: center; justify-content: space-between; margin-bottom: 1rem; }
.section-title-row h5 { font-size: 1rem; font-weight: 700; color: #344767; margin: 0; }
.section-title-row span { font-size: .8rem; color: #7b809a; }
</style>

{{-- Greeting --}}
<div style="margin-bottom:1.5rem;">
    <h4 class="greeting-title">Halo, {{ $firstName }} 👋</h4>
    <p class="greeting-sub">Ringkasan kondisi lab hari ini — Staf Laboratorium</p>
</div>

{{-- Alert stok kritis --}}
@if(($s['critical_bhp'] ?? 0) > 0)
<div class="alert-kritis">
    <i class="fas fa-exclamation-triangle"></i>
    <div>
        <strong>{{ $s['critical_bhp'] }} item BHP di bawah stok minimum!</strong>
        Segera lakukan pengisian stok untuk mencegah kekurangan saat dibutuhkan.
    </div>
    <a href="{{ route('staflab.bhp.index') }}" style="margin-left:auto; font-size:.8rem; font-weight:700; color:#c62828; text-decoration:none; white-space:nowrap;">
        Lihat BHP <i class="fas fa-arrow-right" style="font-size:.65rem;"></i>
    </a>
</div>
@endif

{{-- Stats Row --}}
<div class="stats-row">
    <div class="stat-card">
        <div class="stat-icon purple"><i class="fas fa-flask"></i></div>
        <div>
            <div class="stat-val">{{ $s['total_bhp'] ?? 0 }}</div>
            <div class="stat-lbl">Total Item BHP</div>
            <div class="stat-sub">Rp {{ number_format($s['total_nilai_bhp'] ?? 0, 0, ',', '.') }} nilai stok</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon orange"><i class="fas fa-exclamation-triangle"></i></div>
        <div>
            <div class="stat-val">{{ $s['critical_bhp'] ?? 0 }}</div>
            <div class="stat-lbl">Stok Kritis</div>
            <div class="stat-sub">Di bawah stok minimum</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon green"><i class="fas fa-boxes"></i></div>
        <div>
            <div class="stat-val">{{ $s['total_aset'] ?? 0 }}</div>
            <div class="stat-lbl">Total Aset Inventaris</div>
            <div class="stat-sub">{{ $s['aset_baik'] ?? 0 }} kondisi baik</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon blue"><i class="fas fa-wrench"></i></div>
        <div>
            <div class="stat-val">{{ $s['logs_bulan_ini'] ?? 0 }}</div>
            <div class="stat-lbl">Maintenance Bulan Ini</div>
            <div class="stat-sub">{{ $s['total_logs'] ?? 0 }} total log</div>
        </div>
    </div>
</div>

{{-- Dashboard 2-column grid --}}
<div class="dash-grid">

    {{-- Panel: BHP Kritis --}}
    <div class="panel-card">
        <div class="panel-header">
            <h6 class="panel-title"><i class="fas fa-exclamation-circle"></i> BHP Stok Kritis</h6>
            <a href="{{ route('staflab.bhp.index') }}" class="panel-link">
                Lihat semua <i class="fas fa-arrow-right" style="font-size:.65rem;"></i>
            </a>
        </div>
        @php $critItems = $s['critical_items'] ?? []; @endphp
        @if(count($critItems) > 0)
        <div style="overflow-x:auto;">
            <table class="kritis-table">
                <thead>
                    <tr>
                        <th>NAMA BHP</th>
                        <th style="text-align:center;">STOK</th>
                        <th style="text-align:center;">MIN</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach(array_slice($critItems, 0, 6) as $c)
                    <tr>
                        <td>
                            <div class="item-name">{{ $c['name'] }}</div>
                            <div class="item-code">{{ $c['code'] ?? '' }}</div>
                        </td>
                        <td style="text-align:center;">
                            <span class="stock-kritis">{{ $c['stock'] ?? 0 }}</span>
                        </td>
                        <td style="text-align:center;">
                            <span class="stock-min">{{ $c['min_stock'] ?? 0 }}</span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="panel-body" style="text-align:center; color:#adb5bd; padding:2rem 1.5rem;">
            <i class="fas fa-check-circle" style="font-size:2rem; color:#4caf50; display:block; margin-bottom:.5rem;"></i>
            <p style="margin:0; font-size:.875rem;">Semua stok BHP dalam batas aman.</p>
        </div>
        @endif
    </div>

    {{-- Panel: Kondisi Inventaris --}}
    <div class="panel-card">
        <div class="panel-header">
            <h6 class="panel-title"><i class="fas fa-boxes"></i> Kondisi Inventaris</h6>
            <a href="{{ route('staflab.inventaris.index') }}" class="panel-link">
                Kelola <i class="fas fa-arrow-right" style="font-size:.65rem;"></i>
            </a>
        </div>
        <div class="panel-body">
            @php
                $totalAset = $s['total_aset'] ?? 0;
                $asetBaik  = $s['aset_baik'] ?? 0;
                $asetMaint = $s['aset_maint'] ?? 0;
                $asetRR    = $s['aset_rusak_ringan'] ?? 0;
                $asetRB    = $s['aset_rusak_berat'] ?? 0;
                $pctBaik   = $totalAset > 0 ? round($asetBaik / $totalAset * 100) : 0;
            @endphp

            {{-- Progress bar visual --}}
            <div style="margin-bottom:1.25rem;">
                <div style="display:flex; justify-content:space-between; font-size:.75rem; color:#7b809a; margin-bottom:.5rem;">
                    <span>Kondisi keseluruhan</span>
                    <span style="font-weight:700; color:{{ $pctBaik >= 70 ? '#2e7d32' : ($pctBaik >= 40 ? '#f57f17' : '#c62828') }};">{{ $pctBaik }}% Baik</span>
                </div>
                <div style="height:8px; background:#f0f2f5; border-radius:1rem; overflow:hidden;">
                    <div style="height:100%; border-radius:1rem; background:linear-gradient(90deg,#17ad37,#98ec2d); width:{{ $pctBaik }}%; transition:width .5s;"></div>
                </div>
            </div>

            <ul class="kondisi-list">
                <li>
                    <span class="kondisi-label">
                        <span class="kondisi-dot dot-baik"></span> Baik
                    </span>
                    <span class="kondisi-count">{{ $asetBaik }}</span>
                </li>
                <li>
                    <span class="kondisi-label">
                        <span class="kondisi-dot dot-maint"></span> Perlu Maintenance
                    </span>
                    <span class="kondisi-count">{{ $asetMaint }}</span>
                </li>
                <li>
                    <span class="kondisi-label">
                        <span class="kondisi-dot dot-ringan"></span> Rusak Ringan
                    </span>
                    <span class="kondisi-count">{{ $asetRR }}</span>
                </li>
                <li>
                    <span class="kondisi-label">
                        <span class="kondisi-dot dot-berat"></span> Rusak Berat
                    </span>
                    <span class="kondisi-count">{{ $asetRB }}</span>
                </li>
            </ul>
        </div>
    </div>

    {{-- Panel: Log Maintenance Terbaru --}}
    <div class="panel-card" style="grid-column: 1 / -1;">
        <div class="panel-header">
            <h6 class="panel-title"><i class="fas fa-history"></i> Log Maintenance Terbaru</h6>
            <div style="display:flex; align-items:center; gap:1rem;">
                <span style="font-size:.75rem; color:#7b809a;">Biaya bulan ini: <strong style="color:#344767;">Rp {{ number_format($s['biaya_bulan_ini'] ?? 0, 0, ',', '.') }}</strong></span>
                <a href="{{ route('staflab.maintenance.index') }}" class="panel-link">
                    Lihat semua <i class="fas fa-arrow-right" style="font-size:.65rem;"></i>
                </a>
            </div>
        </div>
        <div class="panel-body">
            @php $recentLogs = $s['recent_logs'] ?? []; @endphp
            @if(count($recentLogs) > 0)
            @foreach($recentLogs as $log)
            @php
                $dateStr = isset($log['maintenance_date'])
                    ? \Carbon\Carbon::parse($log['maintenance_date'])->translatedFormat('d M Y')
                    : '-';
                $condAfter = $log['condition_after'] ?? '-';
                $condMap = ['Baik'=>'#4caf50','Perlu Maintenance'=>'#ffb300','Rusak Ringan'=>'#f57c00','Rusak Berat'=>'#d32f2f'];
                $condColor = $condMap[$condAfter] ?? '#adb5bd';
            @endphp
            <div class="log-mini">
                <div class="log-mini-icon"><i class="fas fa-wrench"></i></div>
                <div class="log-mini-body">
                    <div class="log-mini-asset">{{ $log['asset_name'] ?? '-' }} <span style="font-size:.7rem; font-family:monospace; color:#adb5bd;">{{ $log['asset_code'] ?? '' }}</span></div>
                    <div class="log-mini-desc">{{ $log['description'] ?? '-' }}</div>
                </div>
                <div style="text-align:right; flex-shrink:0;">
                    <div class="log-mini-date">{{ $dateStr }}</div>
                    <span style="font-size:.68rem; font-weight:700; color:{{ $condColor }};">● {{ $condAfter }}</span>
                </div>
            </div>
            @endforeach
            @else
            <div style="text-align:center; color:#adb5bd; padding:1.5rem 0;">
                <i class="fas fa-history" style="font-size:2rem; display:block; margin-bottom:.5rem;"></i>
                <p style="margin:0; font-size:.875rem;">Belum ada log maintenance.</p>
            </div>
            @endif
        </div>
    </div>

</div>

{{-- Divider --}}
<div class="section-title-row" style="margin-top:.5rem;">
    <h5><i class="fas fa-th-large mr-2" style="color:#7928ca;"></i>Akses Cepat</h5>
    <span>Menu yang tersedia untuk Staf Lab</span>
</div>

{{-- Quick Access Grid --}}
<div class="quick-grid">
    <a href="{{ route('staflab.bhp.index') }}" class="quick-card">
        <div class="qc-icon purple"><i class="fas fa-flask"></i></div>
        <div>
            <p class="qc-title">Manajemen BHP</p>
            <p class="qc-desc">Tambah, edit, dan sesuaikan stok Bahan Habis Pakai. Pantau item yang sudah kritis.</p>
            <span class="qc-link">Buka halaman <i class="fas fa-arrow-right"></i></span>
        </div>
    </a>
    <a href="{{ route('staflab.inventaris.index') }}" class="quick-card">
        <div class="qc-icon green"><i class="fas fa-tools"></i></div>
        <div>
            <p class="qc-title">Inventaris & Maintenance</p>
            <p class="qc-desc">Catat log pemeliharaan aset. BHP yang digunakan otomatis mengurangi stok real-time.</p>
            <span class="qc-link">Buka halaman <i class="fas fa-arrow-right"></i></span>
        </div>
    </a>
    <a href="{{ route('staflab.maintenance.index') }}" class="quick-card">
        <div class="qc-icon blue"><i class="fas fa-history"></i></div>
        <div>
            <p class="qc-title">Riwayat Maintenance</p>
            <p class="qc-desc">Lihat seluruh catatan log pemeliharaan dari semua aset, lengkap dengan BHP yang digunakan.</p>
            <span class="qc-link">Buka halaman <i class="fas fa-arrow-right"></i></span>
        </div>
    </a>
</div>

@endsection
