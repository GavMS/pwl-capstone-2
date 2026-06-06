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
.biaya-col { white-space:nowrap; text-align:right; }
.asset-name { font-size:.875rem; font-weight:600; color:#344767; margin:0; white-space:nowrap; }
.asset-code { font-size:.8rem; font-weight:600; color:#adb5bd; font-family:monospace; white-space:nowrap; }
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
        <button type="button" onclick="openNewMaintModal()"
           style="display:inline-flex; align-items:center; gap:.5rem; padding:.6rem 1.25rem;
                  background:linear-gradient(310deg,#7928ca,#ff007f); color:#fff; border-radius:.5rem;
                  font-size:.875rem; font-weight:600; text-decoration:none; transition:opacity .2s; position:relative; z-index:10; border:none; cursor:pointer;"
           onmouseover="this.style.opacity='.85'" onmouseout="this.style.opacity='1'">
            <i class="fas fa-wrench"></i> Log Baru
        </button>
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
        $totalBiaya  = collect($logs)->sum(fn($l) => $l['cost'] ?? 0);
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
        <div class="stat-card">
            <div class="stat-icon teal"><i class="fas fa-coins"></i></div>
            <div>
                <div class="stat-val" style="font-size:1.1rem;">Rp {{ number_format($totalBiaya, 0, ',', '.') }}</div>
                <div class="stat-lbl">Total Biaya</div>
            </div>
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
                        <th>ASET</th>
                        <th>DESKRIPSI</th>
                        <th>KONDISI</th>
                        <th>BHP DIGUNAKAN</th>
                        <th>PETUGAS</th>
                        <th style="text-align:right;">BIAYA</th>
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
                        data-search="{{ strtolower(($log['asset_name'] ?? '') . ' ' . ($log['description'] ?? '')) }}"
                        data-cond="{{ $condAfter }}">
                        <td class="date-col">
                            <span class="cell-text font-semibold" style="color:#344767;">{{ $dateStr }}</span>
                        </td>
                        <td style="min-width:160px;">
                            <p class="asset-name">{{ $log['asset_name'] ?? '-' }}</p>
                            <span class="asset-code">{{ $log['asset_code'] ?? '' }}</span>
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
                        <td class="biaya-col">
                            <span class="cell-text">
                                {{ ($log['cost'] ?? 0) > 0 ? 'Rp ' . number_format($log['cost'], 0, ',', '.') : '—' }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" style="text-align:center;padding:3rem;">
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

{{-- ── Modal: Log Maintenance Baru ───────────────────────────── --}}
<div class="modal-backdrop" id="maintModal">
    <div class="modal-box">
        <div class="modal-header">
            <h5 class="modal-title"><i class="fas fa-wrench mr-2" style="color:#7928ca;"></i>Catat Log Maintenance</h5>
            <button class="modal-close" onclick="closeModal('maintModal')">&times;</button>
        </div>
        
        <div class="mb-4" id="maintError" style="display:none; padding:.75rem 1rem; border-radius:.5rem; background:#fee2e2; color:#b91c1c; font-size:.875rem; font-weight:600;"></div>

        <form id="maintForm" onsubmit="submitMaintLog(event)">
            <div class="form-group">
                <label class="form-label">Pilih Aset Inventaris <span>*</span></label>
                <select id="mAssetSelect" class="form-control" required onchange="updateAssetInfo(this)">
                    <option value="">-- Cari / Pilih Aset --</option>
                    @foreach($assets as $a)
                        <option value="{{ $a['id'] }}" data-cond="{{ $a['condition_status'] ?? 'Baik' }}">[{{ $a['code'] }}] {{ $a['name'] }}</option>
                    @endforeach
                </select>
            </div>
            
            <div style="background:#f5f6fb;border-radius:.75rem;padding:.875rem 1.25rem;margin-bottom:1.25rem;display:none;" id="assetInfoBox">
                <span style="font-size:.8rem;color:#7b809a;">Kondisi saat ini: </span>
                <span id="mCondBefore" style="font-size:.8rem;font-weight:600;color:#344767;"></span>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Tanggal Maintenance <span>*</span></label>
                    <input type="date" id="mDate" class="form-control" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Kondisi Setelah Maintenance <span>*</span></label>
                    <select id="mCondAfter" class="form-control" required>
                        <option value="Baik">Baik</option>
                        <option value="Perlu Maintenance">Perlu Maintenance</option>
                        <option value="Rusak Ringan">Rusak Ringan</option>
                        <option value="Rusak Berat">Rusak Berat</option>
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">Deskripsi Pekerjaan Maintenance <span>*</span></label>
                <textarea id="mDesc" class="form-control" rows="3" placeholder="Contoh: Kalibrasi sensor suhu..." required></textarea>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Biaya Maintenance (Rp)</label>
                    <input type="number" id="mCost" class="form-control" min="0" value="0" placeholder="0">
                </div>
                <div class="form-group">
                    <label class="form-label">Catatan Tambahan</label>
                    <input type="text" id="mNotes" class="form-control" placeholder="Catatan opsional...">
                </div>
            </div>

            {{-- BHP yang digunakan --}}
            <div class="bhp-section">
                <div class="bhp-section-title"><i class="fas fa-flask mr-1"></i> BHP yang Digunakan (Opsional)</div>
                <p style="font-size:.8rem;color:#7b809a;margin:0 0 .75rem;">Stok otomatis berkurang.</p>
                <div id="bhpUsedList"></div>
                <button type="button" class="btn-add-bhp" onclick="addBhpRow()">
                    <i class="fas fa-plus"></i> Tambah BHP
                </button>
            </div>

            <div class="form-footer">
                <button type="button" class="btn-cancel" onclick="closeModal('maintModal')">Batal</button>
                <button type="submit" class="btn-submit" id="btnSubmitMaint">
                    <i class="fas fa-save mr-1"></i> Simpan Log
                </button>
            </div>
        </form>
    </div>
</div>

<script>
const API_URL = '{{ $apiUrl }}';
const TOKEN   = '{{ $token }}';
const CONSUMABLES = @json($consumables);

function openNewMaintModal() {
    document.getElementById('mAssetSelect').value = '';
    document.getElementById('assetInfoBox').style.display = 'none';
    document.getElementById('mCondBefore').textContent = '';
    document.getElementById('mDate').value = new Date().toISOString().split('T')[0];
    document.getElementById('mCondAfter').value = 'Baik';
    document.getElementById('mDesc').value = '';
    document.getElementById('mCost').value = 0;
    document.getElementById('mNotes').value = '';
    document.getElementById('bhpUsedList').innerHTML = '';
    document.getElementById('maintError').style.display = 'none';
    openModal('maintModal');
}

function updateAssetInfo(sel) {
    const box = document.getElementById('assetInfoBox');
    const condLabel = document.getElementById('mCondBefore');
    if(!sel.value) {
        box.style.display = 'none';
        return;
    }
    const opt = sel.options[sel.selectedIndex];
    condLabel.textContent = opt.dataset.cond;
    box.style.display = 'block';
}

function openModal(id) { document.getElementById(id).classList.add('open'); }
function closeModal(id) { document.getElementById(id).classList.remove('open'); }

document.querySelectorAll('.modal-backdrop').forEach(m => {
    m.addEventListener('click', e => { if(e.target === m) closeModal(m.id); });
});

function addBhpRow() {
    const container = document.getElementById('bhpUsedList');
    const rowId = 'bhpRow_' + Date.now();
    const options = CONSUMABLES.map(c => 
        `<option value="${c.id}" data-unit="${c.unit ?? ''}" data-stock="${c.stock ?? 0}">${c.name} (Stok: ${c.stock ?? 0} ${c.unit ?? ''})</option>`
    ).join('');
    
    const row = document.createElement('div');
    row.className = 'bhp-item-row';
    row.id = rowId;
    row.innerHTML = `
        <select class="form-control bhp-select" onchange="updateBhpQtyMax(this)" required style="flex:2;">
            <option value="">-- Pilih BHP --</option>
            ${options}
        </select>
        <div style="display:flex; align-items:stretch; flex:1; min-width:120px;">
            <input type="number" class="form-control bhp-qty" min="1" value="1" placeholder="Jumlah" required style="border-right:0; border-top-right-radius:0; border-bottom-right-radius:0; min-width:60px; padding-left:0.5rem;">
            <span class="bhp-unit-label" style="background:#f5f6fb; padding:0 .75rem; border:1px solid #d2d6da; border-left:0; border-top-right-radius:.5rem; border-bottom-right-radius:.5rem; font-size:.8125rem; color:#7b809a; white-space:nowrap; display:flex; align-items:center;">-</span>
        </div>
        <button type="button" class="btn-remove-bhp" onclick="document.getElementById('${rowId}').remove()" style="flex:none;">
            <i class="fas fa-times"></i>
        </button>
    `;
    container.appendChild(row);
}

function updateBhpQtyMax(selectEl) {
    const opt = selectEl.options[selectEl.selectedIndex];
    const stock = parseInt(opt?.dataset?.stock) || 999;
    const unit = opt?.dataset?.unit || '-';
    const row = selectEl.closest('.bhp-item-row');
    row.querySelector('.bhp-qty').max = stock;
    row.querySelector('.bhp-unit-label').textContent = unit;
}

async function submitMaintLog(e) {
    e.preventDefault();
    const assetId = document.getElementById('mAssetSelect').value;
    const errEl   = document.getElementById('maintError');
    errEl.style.display = 'none';

    if (!assetId) {
        errEl.textContent = 'Silakan pilih aset terlebih dahulu.';
        errEl.style.display = 'block';
        return;
    }

    const bhpRows = document.querySelectorAll('#bhpUsedList .bhp-item-row');
    const consumables_used = [];
    let bhpValid = true;

    bhpRows.forEach(row => {
        const sel = row.querySelector('.bhp-select');
        const qty = row.querySelector('.bhp-qty');
        if (!sel.value) {
            errEl.textContent = 'Pilih BHP atau hapus baris BHP yang kosong.';
            bhpValid = false; return;
        }
        consumables_used.push({
            consumable_id: parseInt(sel.value),
            quantity: parseInt(qty.value)
        });
    });

    if (!bhpValid) { errEl.style.display = 'block'; return; }

    const payload = {
        asset_id: parseInt(assetId),
        maintenance_date: document.getElementById('mDate').value,
        description: document.getElementById('mDesc').value,
        condition_after: document.getElementById('mCondAfter').value,
        cost: document.getElementById('mCost').value ? parseFloat(document.getElementById('mCost').value) : 0,
        notes: document.getElementById('mNotes').value,
        consumables_used
    };

    const btn = document.getElementById('btnSubmitMaint');
    const oriText = btn.innerHTML;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> Menyimpan...';
    btn.disabled = true;

    try {
        const resp = await fetch(`${API_URL}/api/maintenance`, {
            method: 'POST',
            headers: {
                'Authorization': `Bearer ${TOKEN}`,
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(payload)
        });
        const data = await resp.json();
        if (resp.ok) {
            window.location.reload();
        } else {
            errEl.textContent = data.message || 'Gagal menyimpan log maintenance.';
            errEl.style.display = 'block';
            btn.innerHTML = oriText;
            btn.disabled = false;
        }
    } catch (err) {
        errEl.textContent = 'Terjadi kesalahan jaringan.';
        errEl.style.display = 'block';
        btn.innerHTML = oriText;
        btn.disabled = false;
    }
}
</script>
@endsection
