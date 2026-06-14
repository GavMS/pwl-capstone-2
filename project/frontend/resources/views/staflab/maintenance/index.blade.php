@extends('dashboard.layout')
@section('title', 'Riwayat Maintenance')
@section('page_title', 'Riwayat Maintenance')

@section('content')
<style>
.page-header-card {
    background:#fff; border-radius:1rem;
    box-shadow:0 20px 27px 0 rgba(0,0,0,.05);
    padding:1.5rem; margin-bottom:1.5rem;
}
.page-title    { font-size:1.25rem; font-weight:700; color:#344767; margin:0 0 .25rem; }
.page-subtitle { font-size:.875rem; color:#7b809a; margin:0; }

/* ─── Stats row ──────────────────────────── */
.stats-row {
    display:grid; grid-template-columns:repeat(auto-fit,minmax(150px,1fr));
    gap:1rem; margin-bottom:1.5rem;
}
.stat-card {
    background:#fff; border-radius:1rem;
    box-shadow:0 20px 27px 0 rgba(0,0,0,.05);
    padding:1.25rem 1.5rem; display:flex; align-items:center; gap:1rem;
}
.stat-icon {
    width:3rem; height:3rem; border-radius:.75rem;
    display:flex; align-items:center; justify-content:center;
    font-size:1.2rem; flex-shrink:0;
}
.stat-icon.purple { background:linear-gradient(310deg,#7928ca,#ff007f); color:#fff; }
.stat-icon.blue   { background:linear-gradient(310deg,#2152ff,#21d4fd); color:#fff; }
.stat-icon.green  { background:linear-gradient(310deg,#17ad37,#98ec2d); color:#fff; }
.stat-val { font-size:1.5rem; font-weight:800; color:#344767; line-height:1; }
.stat-lbl { font-size:.75rem; color:#7b809a; margin-top:.2rem; }

.stat-icon.orange { background: linear-gradient(310deg,#f53939,#fbcf33); color:#fff; }
.stat-icon.teal   { background: linear-gradient(310deg,#0093e9,#80d0c7); color:#fff; }
.stat-sub { font-size:.7rem; color:#adb5bd; margin-top:.1rem; }

/* ─── Table Card ─────────────────────────── */
.table-card {
    background:#fff; border-radius:1rem;
    box-shadow:0 20px 27px 0 rgba(0,0,0,.05);
    overflow:hidden;
}
.table-card-header {
    display:flex; align-items:center; justify-content:space-between;
    padding:1rem 1.5rem; border-bottom:1px solid #f0f2f5;
    flex-wrap:wrap; gap:.75rem;
}
.filter-bar { display:flex; align-items:center; gap:.75rem; flex-wrap:wrap; flex:1; }
.search-wrap { position:relative; flex:1; min-width:200px; }
.search-wrap .si {
    position:absolute; left:.75rem; top:50%;
    transform:translateY(-50%); color:#adb5bd; font-size:.75rem; pointer-events:none;
}
.search-wrap input {
    padding:.5rem .875rem .5rem 2.2rem; font-size:.8125rem;
    border:1px solid #d2d6da; border-radius:.5rem;
    color:#344767; outline:none; width:100%;
    transition:border-color .2s, box-shadow .2s;
}
.search-wrap input:focus { border-color:#7928ca; box-shadow:0 0 0 2px rgba(121,40,202,.15); }
.filter-select {
    padding:.5rem .875rem; font-size:.8125rem;
    border:1px solid #d2d6da; border-radius:.5rem;
    color:#344767; outline:none; background:#fff; cursor:pointer; min-width:140px;
}
.result-count { font-size:.75rem; color:#adb5bd; font-weight:600; white-space:nowrap; }

/* ─── Table ──────────────────────────────── */
.log-table { width:100%; border-collapse:collapse; }
.log-table thead th {
    padding:.75rem 1.25rem; font-size:.65rem; font-weight:700;
    text-transform:uppercase; letter-spacing:.05em;
    color:#adb5bd; border-bottom:1px solid #f0f2f5;
    white-space:nowrap; background:#fff; text-align:left;
}
.log-table tbody td {
    padding:.875rem 1.25rem; border-bottom:1px solid #f0f2f5; vertical-align:middle;
}
.log-table tbody tr:last-child td { border-bottom:none; }
.log-table tbody tr:hover { background:#fafbfc; }

.item-title { font-size:.875rem; font-weight:600; color:#344767; margin:0; }
.item-code  { font-size:.8125rem; font-weight:600; color:#7b809a; font-family:monospace; }
.cell-text  { font-size:.875rem; color:#7b809a; }

/* ─── Condition Badges ───────────────────── */
.cond-badge {
    display:inline-flex; align-items:center; gap:.35rem;
    padding:.3rem .85rem; border-radius:2rem;
    font-size:.7rem; font-weight:700; white-space:nowrap;
}
.cond-badge .cb-dot { width:6px;height:6px;border-radius:50%;display:inline-block; }
.cond-baik            { background:#e8f5e9; color:#2e7d32; }
.cond-baik .cb-dot            { background:#4caf50; }
.cond-maint           { background:#fff8e1; color:#f57f17; }
.cond-maint .cb-dot           { background:#ffb300; }
.cond-rusak-ringan    { background:#fff3e0; color:#e65100; }
.cond-rusak-ringan .cb-dot    { background:#f57c00; }
.cond-rusak-berat     { background:#fbe9e7; color:#c62828; }
.cond-rusak-berat .cb-dot     { background:#d32f2f; }

/* ─── BHP Chips ──────────────────────────── */
.bhp-chip {
    display:inline-flex; align-items:center; gap:.3rem;
    background:#e8f4ff; color:#1565c0;
    padding:.15rem .55rem; border-radius:1rem; font-size:.7rem; font-weight:600; margin:.1rem;
}

.desc-cell { max-width:250px; }
.desc-text {
    font-size:.875rem; color:#344767; font-weight:600;
    white-space:nowrap; overflow:hidden; text-overflow:ellipsis; max-width:240px;
    display:block;
}
.desc-note { font-size:.75rem; color:#adb5bd; margin-top:.2rem; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; max-width:240px; display:block; }
.cond-arrow { color:#adb5bd; margin:0 .2rem; font-size:.6rem; }
.cond-col { white-space:nowrap; }
.date-col { white-space:nowrap; }
.petugas-col { white-space:nowrap; }
.asset-name { font-size:.875rem; font-weight:600; color:#344767; margin:0; white-space:nowrap; }
.asset-code { font-size:.8rem; font-weight:600; color:#adb5bd; font-family:monospace; white-space:nowrap; }
.asset-meta { display:flex; align-items:center; gap:.4rem; flex-wrap:wrap; margin-top:.2rem; }
.label-pill {
    display:inline-flex; align-items:center; gap:.3rem;
    padding:.12rem .5rem; border-radius:.4rem;
    background:#ede9fe; color:#6d28d9;
    font-size:.7rem; font-weight:700; font-family:monospace; white-space:nowrap;
}
.label-pill i { font-size:.6rem; }
.unit-hint { font-size:.68rem; color:#cbd5e1; font-style:italic; white-space:nowrap; }
.asset-cat { font-size:.7rem; color:#adb5bd; }
.bhp-cell { min-width:160px; }

/* ─── Modal ──────────────────────────────────── */
.modal-backdrop { display:none; position:fixed; inset:0; z-index:1000; background:rgba(0,0,0,.45); align-items:center; justify-content:center; }
.modal-backdrop.open { display:flex; }
.modal-box { background:#fff; border-radius:1.25rem; box-shadow:0 25px 60px rgba(0,0,0,.18); padding:2rem; width:100%; max-width:580px; max-height:90vh; overflow-y:auto; animation:modalIn .25s ease; }
@keyframes modalIn { from{transform:translateY(-30px);opacity:0} to{transform:translateY(0);opacity:1} }
.modal-header { display:flex; align-items:center; justify-content:space-between; margin-bottom:1.5rem; }
.modal-title  { font-size:1.1rem; font-weight:700; color:#344767; margin:0; }
.modal-close  { background:none; border:none; font-size:1.25rem; color:#adb5bd; cursor:pointer; }
.modal-close:hover { color:#344767; }

/* ─── Form Elements ─────────────────────────── */
.form-group { margin-bottom:1rem; }
.form-label { display:block; font-size:.8rem; font-weight:600; color:#344767; margin-bottom:.4rem; }
.form-label span { color:#d32f2f; }
.form-control { width:100%; padding:.6rem .875rem; font-size:.875rem; border:1px solid #d2d6da; border-radius:.5rem; color:#344767; outline:none; box-sizing:border-box; }
.form-control:focus { border-color:#7928ca; box-shadow:0 0 0 2px rgba(121,40,202,.15); }
.form-row    { display:grid; grid-template-columns:1fr 1fr; gap:1rem; }
.form-footer { display:flex; gap:.75rem; justify-content:flex-end; margin-top:1.5rem; }
.btn-cancel { padding:.6rem 1.25rem; background:#f5f6fb; border:none; border-radius:.5rem; font-size:.875rem; font-weight:600; color:#7b809a; cursor:pointer; }
.btn-submit { padding:.6rem 1.5rem; background:linear-gradient(310deg,#7928ca,#ff007f); border:none; border-radius:.5rem; font-size:.875rem; font-weight:600; color:#fff; cursor:pointer; }
.bhp-section { border:1px solid #f0f2f5; border-radius:.75rem; padding:1rem; margin-top:.5rem; }
.bhp-section-title { font-size:.8rem; font-weight:700; color:#344767; text-transform:uppercase; margin-bottom:.75rem; }
.bhp-item-row { display:grid; grid-template-columns: 1fr 180px 36px; gap:.5rem; align-items:center; margin-bottom:.5rem; }
.btn-remove-bhp { width:32px; height:32px; border:none; background:#fbe9e7; color:#c62828; border-radius:.5rem; cursor:pointer; display:flex; align-items:center; justify-content:center; flex-shrink:0; }
.btn-add-bhp { display:inline-flex; align-items:center; gap:.4rem; padding:.45rem 1rem; background:#f5f6fb; border:1px dashed #d2d6da; border-radius:.5rem; font-size:.8rem; font-weight:600; color:#7b809a; cursor:pointer; margin-top:.5rem; }
</style>

<div class="flex flex-wrap -mx-3">
<div class="w-full px-3">

    {{-- Page Header --}}
    <div class="page-header-card" style="display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:1rem;">
        <div>
            <h4 class="page-title"><i class="fas fa-history mr-2" style="color:#7928ca;"></i>Riwayat Maintenance</h4>
            <p class="page-subtitle">Seluruh catatan log pemeliharaan inventaris — lintas aset, terurut dari terbaru.</p>
        </div>
    </div>

    {{-- Error Banner --}}
    @if(isset($error) && $error)
    <div class="mb-4 p-4 rounded-lg bg-red-50 border border-red-200 text-red-600 text-sm font-semibold">
        <i class="fas fa-exclamation-triangle mr-2"></i> {{ $error }}
    </div>
    @endif

    {{-- Stats --}}
    @php
        $totalLogs   = count($logs);
        $logsWithBhp = collect($logs)->filter(fn($l) => count($l['used_consumables'] ?? []) > 0)->count();
        $uniqueAssets = collect($logs)->pluck('asset_id')->unique()->count();
    @endphp
    <div class="stats-row">
        <div class="stat-card">
            <div class="stat-icon purple"><i class="fas fa-history"></i></div>
            <div><div class="stat-val">{{ $totalLogs }}</div><div class="stat-lbl">Total Log</div></div>
        </div>
        <div class="stat-card">
            <div class="stat-icon blue"><i class="fas fa-box-open"></i></div>
            <div><div class="stat-val">{{ $uniqueAssets }}</div><div class="stat-lbl">Aset Dimaintenance</div></div>
        </div>
        <div class="stat-card">
            <div class="stat-icon green"><i class="fas fa-flask"></i></div>
            <div><div class="stat-val">{{ $logsWithBhp }}</div><div class="stat-lbl">Log Gunakan BHP</div></div>
        </div>
    </div>


    {{-- Table --}}
    <div class="table-card">
        <div class="table-card-header">
            <div class="filter-bar">
                <div class="search-wrap">
                    <i class="fas fa-search si"></i>
                    <input type="text" id="searchInput" placeholder="Cari nama aset, deskripsi..." />
                </div>
                @php
                    $condAfterValues = collect($logs)->pluck('condition_after')->unique()->filter()->values();
                @endphp
                <select id="condFilter" class="filter-select">
                    <option value="">Semua Kondisi Akhir</option>
                    @foreach($condAfterValues as $c)
                        <option value="{{ $c }}">{{ $c }}</option>
                    @endforeach
                </select>
            </div>
            <span class="result-count" id="resultCount">{{ $totalLogs }} log</span>
        </div>

        <div style="overflow-x:auto;">
            <table class="log-table" id="logTable">
                <thead>
                    <tr>
                        <th>TANGGAL</th>
                        <th>BARANG (UNIT)</th>
                        <th>DESKRIPSI</th>
                        <th>KONDISI</th>
                        <th>BHP DIGUNAKAN</th>
                        <th>PETUGAS</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logs as $log)
                    @php
                        $condBefore = $log['condition_before'] ?? '-';
                        $condAfter  = $log['condition_after']  ?? '-';

                        $condMap = ['Baik' => 'cond-baik', 'Perlu Maintenance' => 'cond-maint', 'Rusak Ringan' => 'cond-rusak-ringan', 'Rusak Berat' => 'cond-rusak-berat'];
                        $classBefore = $condMap[$condBefore] ?? 'cond-maint';
                        $classAfter  = $condMap[$condAfter]  ?? 'cond-maint';

                        $usedBhp = $log['used_consumables'] ?? [];
                        $dateStr = $log['maintenance_date'] ? \Carbon\Carbon::parse($log['maintenance_date'])->translatedFormat('d M Y') : '-';
                    @endphp
                    <tr class="item-row"
                        data-search="{{ strtolower(($log['asset_name'] ?? '') . ' ' . ($log['asset_code'] ?? '') . ' ' . ($log['asset_label'] ?? '') . ' ' . ($log['description'] ?? '')) }}"
                        data-cond="{{ $condAfter }}">
                        <td class="date-col">
                            <span class="cell-text font-semibold" style="color:#344767;">{{ $dateStr }}</span>
                        </td>
                        <td style="min-width:200px;">
                            <p class="asset-name">{{ $log['asset_name'] ?? '-' }}</p>
                            <div class="asset-meta">
                                @if(!empty($log['asset_code']))
                                    <span class="asset-code">{{ $log['asset_code'] }}</span>
                                @endif
                                @if(!empty($log['asset_label']))
                                    <span class="label-pill"><i class="fas fa-tag"></i> {{ $log['asset_label'] }}</span>
                                @else
                                    <span class="unit-hint">Unit belum dilabeli</span>
                                @endif
                            </div>
                            @if(!empty($log['asset_category']))
                                <span class="asset-cat">{{ $log['asset_category'] }}</span>
                            @endif
                        </td>
                        <td class="desc-cell">
                            <span class="desc-text" title="{{ $log['description'] ?? '' }}">{{ $log['description'] ?? '-' }}</span>
                            @if($log['notes'] ?? '')
                            <span class="desc-note" title="{{ $log['notes'] }}">{{ $log['notes'] }}</span>
                            @endif
                        </td>
                        <td class="cond-col">
                            <div style="display:inline-flex;align-items:center;gap:.3rem;flex-wrap:nowrap;">
                                <span class="cond-badge {{ $classBefore }}">
                                    <span class="cb-dot"></span> {{ $condBefore }}
                                </span>
                                <i class="fas fa-arrow-right cond-arrow"></i>
                                <span class="cond-badge {{ $classAfter }}">
                                    <span class="cb-dot"></span> {{ $condAfter }}
                                </span>
                            </div>
                        </td>
                        <td class="bhp-cell">
                            @if(count($usedBhp) > 0)
                                @foreach($usedBhp as $u)
                                <span class="bhp-chip">
                                    <i class="fas fa-flask" style="font-size:.65rem;"></i>
                                    {{ $u['consumable_name'] ?? '-' }} &times; {{ $u['quantity_used'] }} {{ $u['unit'] ?? '' }}
                                </span>
                                @endforeach
                            @else
                                <span class="cell-text">—</span>
                            @endif
                        </td>
                        <td class="petugas-col">
                            <span class="cell-text">{{ $log['performed_by_name'] ?? '-' }}</span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" style="text-align:center;padding:3rem;">
                            <i class="fas fa-history" style="font-size:2.5rem;color:#d2d6da;margin-bottom:1rem;display:block;"></i>
                            <p style="color:#7b809a;">Belum ada riwayat maintenance. Mulai catat dari halaman Inventaris & Maintenance.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.getElementById('searchInput');
    const condFilter  = document.getElementById('condFilter');
    const rows        = document.querySelectorAll('.item-row');
    const resultCount = document.getElementById('resultCount');

    function applyFilters() {
        const q    = searchInput.value.toLowerCase();
        const cond = condFilter.value;
        let count = 0;

        rows.forEach(row => {
            const mQ = (row.dataset.search || '').includes(q);
            const mC = !cond || row.dataset.cond === cond;
            const show = mQ && mC;
            row.style.display = show ? '' : 'none';
            if (show) count++;
        });
        resultCount.textContent = count + ' log';
    }

    if (searchInput) searchInput.addEventListener('input',  applyFilters);
    if (condFilter)  condFilter.addEventListener('change', applyFilters);
});
</script>

@endsection
