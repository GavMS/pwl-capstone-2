@extends('dashboard.layout')
@section('title', 'Inventaris & Maintenance')
@section('page_title', 'Inventaris')

@section('content')
@php
    $apiUrl = env('NODEJS_API_URL', 'http://localhost:5000');
    $token  = session('token', '');
@endphp

<style>
/* ─── Page Styles ──────────────────────────── */
.page-header-card {
    background: #fff; border-radius: 1rem;
    box-shadow: 0 20px 27px 0 rgba(0,0,0,.05);
    padding: 1.5rem; margin-bottom: 1.5rem;
}
.page-title    { font-size: 1.25rem; font-weight: 700; color: #344767; margin: 0 0 .25rem; }
.page-subtitle { font-size: .875rem; color: #7b809a; margin: 0; }

/* ─── Stats Row ─────────────────────────────── */
.stats-row {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
    gap: 1rem;
    margin-bottom: 1.5rem;
}
.stat-card {
    background: #fff; border-radius: 1rem;
    box-shadow: 0 20px 27px 0 rgba(0,0,0,.05);
    padding: 1.25rem 1.5rem;
    display: flex; align-items: center; gap: 1rem;
    transition: transform .18s ease;
}
.stat-card:hover { transform: translateY(-2px); }
.stat-icon {
    width: 3rem; height: 3rem; border-radius: .75rem;
    display: flex; align-items: center; justify-content: center;
    font-size: 1.2rem; flex-shrink: 0;
}
.stat-icon.green  { background: linear-gradient(310deg,#17ad37,#98ec2d); color:#fff; }
.stat-icon.yellow { background: linear-gradient(310deg,#f7971e,#ffd200); color:#fff; }
.stat-icon.orange { background: linear-gradient(310deg,#f53939,#fbcf33); color:#fff; }
.stat-icon.red    { background: linear-gradient(310deg,#d32f2f,#f48fb1); color:#fff; }
.stat-icon.blue   { background: linear-gradient(310deg,#2152ff,#21d4fd); color:#fff; }
.stat-val { font-size: 1.5rem; font-weight: 800; color: #344767; line-height: 1; }
.stat-lbl { font-size: .72rem; color: #7b809a; margin-top: .2rem; }

.table-card {
    background: #fff; border-radius: 1rem;
    box-shadow: 0 20px 27px 0 rgba(0,0,0,.05);
    overflow: hidden;
}
.table-card-header {
    display: flex; align-items: center;
    justify-content: space-between;
    padding: 1rem 1.5rem; border-bottom: 1px solid #f0f2f5;
    flex-wrap: wrap; gap: .75rem;
}
.filter-bar { display: flex; align-items: center; gap: .75rem; flex-wrap: wrap; flex:1; }
.search-wrap { position: relative; flex:1; min-width:200px; }
.search-wrap .search-icon {
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
    color:#344767; outline:none; background:#fff; cursor:pointer;
    transition:border-color .2s, box-shadow .2s; min-width:140px;
}
.filter-select:focus { border-color:#7928ca; box-shadow:0 0 0 2px rgba(121,40,202,.15); }
.result-count { font-size:.75rem; color:#adb5bd; font-weight:600; white-space:nowrap; }

/* ─── Table ─────────────────────────────────── */
.inventory-table { width:100%; border-collapse:collapse; }
.inventory-table thead th {
    padding:.75rem 1.25rem; font-size:.65rem; font-weight:700;
    text-transform:uppercase; letter-spacing:.05em;
    color:#adb5bd; border-bottom:1px solid #f0f2f5;
    white-space:nowrap; background:#fff; text-align:left;
}
.inventory-table tbody td {
    padding:.875rem 1.25rem; border-bottom:1px solid #f0f2f5; vertical-align:middle;
}
.inventory-table tbody tr:last-child td { border-bottom:none; }
.inventory-table tbody tr:hover { background:#fafbfc; }

.item-title { font-size:.875rem; font-weight:600; color:#344767; margin:0; }
.item-code  { font-size:.8125rem; font-weight:600; color:#7b809a; font-family:monospace; }
.cell-text  { font-size:.875rem; color:#7b809a; }

/* ─── Condition Badges ─────────────────────── */
.cond-badge {
    display:inline-flex; align-items:center; gap:.35rem;
    padding:.3rem .85rem; border-radius:2rem;
    font-size:.7rem; font-weight:700; white-space:nowrap;
}
.cond-badge .cb-dot { width:6px;height:6px;border-radius:50%;display:inline-block; }
.cond-baik        { background:#e8f5e9; color:#2e7d32; }
.cond-baik .cb-dot        { background:#4caf50; }
.cond-maint       { background:#fff8e1; color:#f57f17; }
.cond-maint .cb-dot       { background:#ffb300; }
.cond-rusak-ringan{ background:#fff3e0; color:#e65100; }
.cond-rusak-ringan .cb-dot{ background:#f57c00; }
.cond-rusak-berat { background:#fbe9e7; color:#c62828; }
.cond-rusak-berat .cb-dot { background:#d32f2f; }
.cond-diperbaiki  { background:#e3f2fd; color:#0d47a1; }
.cond-diperbaiki .cb-dot  { background:#1976d2; }

/* ─── Action Buttons ─────────────────────────── */
.act-btn {
    display:inline-flex; align-items:center; gap:.35rem;
    padding:.35rem .75rem; border-radius:.5rem; font-size:.75rem;
    font-weight:600; cursor:pointer; border:none; transition:all .2s; white-space:nowrap;
    position:relative; z-index:10;
}
.btn-maint  { background:linear-gradient(310deg,#7928ca,#ff007f); color:#fff; }
.btn-maint:hover { opacity:.85; }
.btn-logs   { background:#e8f4ff; color:#1565c0; }
.btn-logs:hover  { background:#bbdefb; }

/* ─── Modal ──────────────────────────────────── */
.modal-backdrop {
    display:none; position:fixed; inset:0; z-index:1000;
    background:rgba(0,0,0,.45); align-items:center; justify-content:center;
}
.modal-backdrop.open { display:flex; }
.modal-box {
    background:#fff; border-radius:1.25rem;
    box-shadow:0 25px 60px rgba(0,0,0,.18);
    padding:2rem; width:100%; max-width:580px;
    max-height:90vh; overflow-y:auto;
    animation:modalIn .25s ease;
}
.modal-box-wide { max-width: 700px; }
@keyframes modalIn { from{transform:translateY(-30px);opacity:0} to{transform:translateY(0);opacity:1} }
.modal-header { display:flex; align-items:center; justify-content:space-between; margin-bottom:1.5rem; }
.modal-title  { font-size:1.1rem; font-weight:700; color:#344767; margin:0; }
.modal-close  { background:none; border:none; font-size:1.25rem; color:#adb5bd; cursor:pointer; transition:color .2s; }
.modal-close:hover { color:#344767; }

/* ─── Form Elements ─────────────────────────── */
.form-group { margin-bottom:1rem; }
.form-label { display:block; font-size:.8rem; font-weight:600; color:#344767; margin-bottom:.4rem; }
.form-label span { color:#d32f2f; }
.form-control {
    width:100%; padding:.6rem .875rem; font-size:.875rem;
    border:1px solid #d2d6da; border-radius:.5rem;
    color:#344767; outline:none;
    transition:border-color .2s, box-shadow .2s;
    box-sizing:border-box;
}
.form-control:focus { border-color:#7928ca; box-shadow:0 0 0 2px rgba(121,40,202,.15); }
.form-row    { display:grid; grid-template-columns:1fr 1fr; gap:1rem; }
.form-footer { display:flex; gap:.75rem; justify-content:flex-end; margin-top:1.5rem; }
.btn-cancel {
    padding:.6rem 1.25rem; background:#f5f6fb; border:none;
    border-radius:.5rem; font-size:.875rem; font-weight:600;
    color:#7b809a; cursor:pointer; transition:background .2s;
}
.btn-cancel:hover { background:#e9ecef; }
.btn-submit {
    padding:.6rem 1.5rem; background:linear-gradient(310deg,#7928ca,#ff007f);
    border:none; border-radius:.5rem; font-size:.875rem;
    font-weight:600; color:#fff; cursor:pointer; transition:opacity .2s;
}
.btn-submit:hover { opacity:.9; }

/* ─── BHP Section in form ─────────────────── */
.bhp-section {
    border:1px solid #f0f2f5; border-radius:.75rem;
    padding:1rem; margin-top:.5rem;
}
.bhp-section-title {
    font-size:.8rem; font-weight:700; color:#344767;
    text-transform:uppercase; letter-spacing:.04em;
    margin-bottom:.75rem;
}
.bhp-item-row {
    display:grid; grid-template-columns: 1fr 180px 36px;
    gap:.5rem; align-items:center; margin-bottom:.5rem;
}
.btn-remove-bhp {
    width:32px; height:32px; border:none; background:#fbe9e7;
    color:#c62828; border-radius:.5rem; cursor:pointer;
    display:flex; align-items:center; justify-content:center;
    transition:background .2s; flex-shrink:0;
}
.btn-remove-bhp:hover { background:#ffcdd2; }
.btn-add-bhp {
    display:inline-flex; align-items:center; gap:.4rem;
    padding:.45rem 1rem; background:#f5f6fb; border:1px dashed #d2d6da;
    border-radius:.5rem; font-size:.8rem; font-weight:600;
    color:#7b809a; cursor:pointer; transition:all .2s; margin-top:.5rem;
}
.btn-add-bhp:hover { background:#eef0fa; border-color:#7928ca; color:#7928ca; }

/* ─── Log History List ──────────────────────── */
.log-item {
    border:1px solid #f0f2f5; border-radius:.75rem;
    padding:1rem 1.25rem; margin-bottom:.75rem;
    transition:box-shadow .2s;
}
.log-item:hover { box-shadow: 0 4px 16px rgba(0,0,0,.07); }
.log-date   { font-size:.75rem; color:#adb5bd; font-weight:600; }
.log-desc   { font-size:.875rem; color:#344767; font-weight:600; margin:.25rem 0; }
.log-meta   { font-size:.75rem; color:#7b809a; }
.log-cond   { display:flex; gap:.5rem; align-items:center; flex-wrap:wrap; margin-top:.5rem; }
.cond-arrow { color:#adb5bd; font-size:.8rem; }
.log-bhp-used { margin-top:.5rem; }
.log-bhp-chip {
    display:inline-flex; align-items:center; gap:.35rem;
    background:#e8f4ff; color:#1565c0;
    padding:.2rem .65rem; border-radius:1rem; font-size:.7rem; font-weight:600;
    margin:.15rem;
}
.btn-del-log {
    background:none; border:none; color:#e57373;
    cursor:pointer; font-size:.8rem; padding:.2rem;
    transition:color .2s; float:right;
}
.btn-del-log:hover { color:#c62828; }

/* ─── Toast ─────────────────────────────────── */
#toast-container {
    position:fixed; bottom:1.5rem; right:1.5rem; z-index:9999;
    display:flex; flex-direction:column; gap:.5rem;
}
.toast {
    padding:.875rem 1.25rem; border-radius:.75rem; font-size:.875rem;
    font-weight:600; color:#fff; box-shadow:0 4px 20px rgba(0,0,0,.15);
    animation:toastIn .3s ease; min-width:260px;
}
.toast.success { background:linear-gradient(310deg,#17ad37,#98ec2d); }
.toast.error   { background:linear-gradient(310deg,#f53939,#fbcf33); }
@keyframes toastIn { from{transform:translateX(40px);opacity:0} to{transform:translateX(0);opacity:1} }
</style>

<div class="flex flex-wrap -mx-3">
<div class="w-full px-3">

    {{-- Page Header --}}
    <div class="page-header-card" style="display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:1rem;">
        <div>
            <h4 class="page-title"><i class="fas fa-tools mr-2" style="color:#7928ca;"></i>Inventaris & Maintenance</h4>
            <p class="page-subtitle">Catat log pemeliharaan aset. BHP yang digunakan akan otomatis mengurangi stok.</p>
        </div>
        <a href="{{ route('staflab.maintenance.index') }}"
           style="display:inline-flex; align-items:center; gap:.5rem; padding:.6rem 1.25rem;
                  background:linear-gradient(310deg,#2152ff,#21d4fd); color:#fff; border-radius:.5rem;
                  font-size:.875rem; font-weight:600; text-decoration:none; transition:opacity .2s;"
           onmouseover="this.style.opacity='.85'" onmouseout="this.style.opacity='1'">
            <i class="fas fa-history"></i> Lihat Riwayat
        </a>
    </div>

    {{-- Error Banner --}}
    @if(isset($error) && $error)
    <div class="mb-4 p-4 rounded-lg bg-red-50 border border-red-200 text-red-600 text-sm font-semibold">
        <i class="fas fa-exclamation-triangle mr-2"></i> {{ $error }}
    </div>
    @endif

    {{-- Stats Row --}}
    @php
        $totalAset  = count($assets);
        $asetByKond = collect($assets)->groupBy('condition_status')->map->count();
        $asetBaik   = $asetByKond['Baik'] ?? 0;
        $asetMaint  = $asetByKond['Perlu Maintenance'] ?? 0;
        $asetRR     = $asetByKond['Rusak Ringan'] ?? 0;
        $asetRB     = $asetByKond['Rusak Berat'] ?? 0;
    @endphp
    <div class="stats-row">
        <div class="stat-card">
            <div class="stat-icon blue"><i class="fas fa-boxes"></i></div>
            <div><div class="stat-val">{{ $totalAset }}</div><div class="stat-lbl">Total Aset</div></div>
        </div>
        <div class="stat-card">
            <div class="stat-icon green"><i class="fas fa-check-circle"></i></div>
            <div><div class="stat-val">{{ $asetBaik }}</div><div class="stat-lbl">Kondisi Baik</div></div>
        </div>
        <div class="stat-card">
            <div class="stat-icon yellow"><i class="fas fa-tools"></i></div>
            <div><div class="stat-val">{{ $asetMaint }}</div><div class="stat-lbl">Perlu Maintenance</div></div>
        </div>
        <div class="stat-card">
            <div class="stat-icon orange"><i class="fas fa-exclamation-triangle"></i></div>
            <div><div class="stat-val">{{ $asetRR }}</div><div class="stat-lbl">Rusak Ringan</div></div>
        </div>
        <div class="stat-card">
            <div class="stat-icon red"><i class="fas fa-times-circle"></i></div>
            <div><div class="stat-val">{{ $asetRB }}</div><div class="stat-lbl">Rusak Berat</div></div>
        </div>
    </div>

    {{-- Table Card --}}
    <div class="table-card">
        <div class="table-card-header">
            <div class="filter-bar">
                <div class="search-wrap">
                    <i class="fas fa-search search-icon"></i>
                    <input type="text" id="searchInput" placeholder="Cari nama, kode, kategori..." />
                </div>
                @php
                    $rooms = collect($assets)->pluck('room_code')->unique()->filter()->values();
                    $conditions = collect($assets)->pluck('condition_status')->unique()->filter()->values();
                @endphp
                <select id="roomFilter" class="filter-select">
                    <option value="">Semua Ruangan</option>
                    @foreach($rooms as $r) <option value="{{ $r }}">{{ $r }}</option> @endforeach
                </select>
                <select id="condFilter" class="filter-select">
                    <option value="">Semua Kondisi</option>
                    @foreach($conditions as $cond) <option value="{{ $cond }}">{{ $cond }}</option> @endforeach
                </select>
            </div>
            <span class="result-count" id="resultCount">{{ count($assets) }} aset</span>
        </div>

        <div style="overflow-x:auto;">
            <table class="inventory-table" id="inventoryTable">
                <thead>
                    <tr>
                        <th>KODE</th>
                        <th>NAMA BARANG</th>
                        <th>KATEGORI</th>
                        <th>RUANGAN</th>
                        <th>KONDISI</th>
                        <th style="text-align:center;">TAHUN</th>
                        <th style="text-align:center;">AKSI</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($assets as $a)
                    @php
                        $condStr   = strtolower($a['condition_status'] ?? 'baik');
                        $condClass = 'cond-baik';
                        if(str_contains($condStr, 'maintenance') || str_contains($condStr, 'perlu')) $condClass = 'cond-maint';
                        elseif(str_contains($condStr, 'diperbaiki')) $condClass = 'cond-diperbaiki';
                        elseif(str_contains($condStr, 'ringan'))     $condClass = 'cond-rusak-ringan';
                        elseif(str_contains($condStr, 'berat'))      $condClass = 'cond-rusak-berat';
                    @endphp
                    <tr class="item-row"
                        data-search="{{ strtolower($a['name'] . ' ' . ($a['code'] ?? '') . ' ' . ($a['category'] ?? '')) }}"
                        data-room="{{ $a['room_code'] ?? '' }}"
                        data-cond="{{ $a['condition_status'] ?? '' }}">
                        <td><span class="item-code">{{ $a['code'] ?? '-' }}</span></td>
                        <td><p class="item-title">{{ $a['name'] }}</p></td>
                        <td><span class="cell-text">{{ $a['category'] ?? '-' }}</span></td>
                        <td><span class="cell-text font-semibold">{{ $a['room_code'] ?? '-' }}</span></td>
                        <td>
                            <span class="cond-badge {{ $condClass }}">
                                <span class="cb-dot"></span> {{ $a['condition_status'] ?? 'Baik' }}
                            </span>
                        </td>
                        <td style="text-align:center;"><span class="cell-text">{{ $a['year'] ?? '-' }}</span></td>
                        <td style="text-align:center;">
                            <div style="display:flex;gap:.4rem;justify-content:center;flex-wrap:wrap;">
                                <button class="act-btn btn-maint"
                                    onclick="openMaintModal({{ $a['id'] }}, '{{ addslashes($a['name']) }}', '{{ addslashes($a['condition_status'] ?? 'Baik') }}')">
                                    <i class="fas fa-wrench"></i> Log Maintenance
                                </button>
                                <button class="act-btn btn-logs"
                                    onclick="openLogsModal({{ $a['id'] }}, '{{ addslashes($a['name']) }}')">
                                    <i class="fas fa-history"></i> Riwayat
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" style="text-align:center;padding:3rem;">
                            <i class="fas fa-box-open" style="font-size:2.5rem;color:#d2d6da;margin-bottom:1rem;display:block;"></i>
                            <p style="color:#7b809a;">Tidak ada data inventaris.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
</div>

{{-- ── Modal: Log Maintenance ───────────────────────────── --}}
<div class="modal-backdrop" id="maintModal">
    <div class="modal-box">
        <div class="modal-header">
            <h5 class="modal-title"><i class="fas fa-wrench mr-2" style="color:#7928ca;"></i>Catat Log Maintenance</h5>
            <button class="modal-close" onclick="closeModal('maintModal')">&times;</button>
        </div>
        <div style="background:#f5f6fb;border-radius:.75rem;padding:.875rem 1.25rem;margin-bottom:1.25rem;">
            <strong id="mAssetName" style="display:block;font-size:1rem;font-weight:700;color:#344767;"></strong>
            <span style="font-size:.8rem;color:#7b809a;">Kondisi sebelumnya: </span>
            <span id="mCondBefore" style="font-size:.8rem;font-weight:600;color:#344767;"></span>
        </div>
        <form id="maintForm" onsubmit="submitMaintLog(event)">
            <input type="hidden" id="mAssetId" value="">
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
                <textarea id="mDesc" class="form-control" rows="3"
                    placeholder="Contoh: Kalibrasi sensor suhu, penggantian filter, pembersihan komponen internal..." required></textarea>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Biaya Maintenance (Rp)</label>
                    <input type="number" id="mCost" class="form-control" min="0" value="0" placeholder="0">
                </div>
                <div class="form-group">
                    <label class="form-label">Catatan Tambahan</label>
                    <input type="text" id="mNotes" class="form-control" placeholder="Catatan singkat (opsional)...">
                </div>
            </div>

            {{-- BHP yang digunakan --}}
            <div class="bhp-section">
                <div class="bhp-section-title"><i class="fas fa-flask mr-1"></i> BHP yang Digunakan (Opsional)</div>
                <p style="font-size:.8rem;color:#7b809a;margin:0 0 .75rem;">
                    Stok BHP akan otomatis berkurang saat log ini disimpan.
                </p>
                <div id="bhpUsedList"></div>
                <button type="button" class="btn-add-bhp" onclick="addBhpRow()">
                    <i class="fas fa-plus"></i> Tambah BHP
                </button>
            </div>

            <div id="maintError" style="color:#c62828;font-size:.8125rem;margin-top:.75rem;display:none;"></div>
            <div class="form-footer">
                <button type="button" class="btn-cancel" onclick="closeModal('maintModal')">Batal</button>
                <button type="submit" class="btn-submit" id="maintSubmitBtn">
                    <i class="fas fa-save mr-1"></i> Simpan Log
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ── Modal: Riwayat Log ───────────────────────────────── --}}
<div class="modal-backdrop" id="logsModal">
    <div class="modal-box modal-box-wide">
        <div class="modal-header">
            <h5 class="modal-title"><i class="fas fa-history mr-2" style="color:#1565c0;"></i>Riwayat Maintenance</h5>
            <button class="modal-close" onclick="closeModal('logsModal')">&times;</button>
        </div>
        <p style="font-size:.875rem;color:#7b809a;margin-bottom:1rem;" id="logsAssetName"></p>
        <div id="logsContent">
            <div style="text-align:center;padding:2rem;color:#adb5bd;">
                <i class="fas fa-spinner fa-spin" style="font-size:2rem;"></i>
                <p>Memuat riwayat...</p>
            </div>
        </div>
    </div>
</div>

{{-- Toast Container --}}
<div id="toast-container"></div>

{{-- Pass consumables data to JS --}}
<script>
const API_URL     = '{{ $apiUrl }}';
const TOKEN       = '{{ $token }}';
const CONSUMABLES = @json($consumables);

// ── Toast ──────────────────────────────────────────
function showToast(msg, type = 'success') {
    const c = document.getElementById('toast-container');
    const t = document.createElement('div');
    t.className = `toast ${type}`;
    t.innerHTML = `<i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'} mr-2"></i>${msg}`;
    c.appendChild(t);
    setTimeout(() => { t.style.opacity='0'; t.style.transition='opacity .4s'; setTimeout(()=>t.remove(),400); }, 3500);
}

// ── Modal ──────────────────────────────────────────
function openModal(id)  { document.getElementById(id).classList.add('open'); }
function closeModal(id) { document.getElementById(id).classList.remove('open'); }
document.querySelectorAll('.modal-backdrop').forEach(m => {
    m.addEventListener('click', e => { if(e.target === m) closeModal(m.id); });
});

// ── Maintenance Modal ──────────────────────────────
function openMaintModal(assetId, name, condBefore) {
    document.getElementById('mAssetId').value     = assetId;
    document.getElementById('mAssetName').textContent = name;
    document.getElementById('mCondBefore').textContent = condBefore;
    document.getElementById('mDate').value        = new Date().toISOString().split('T')[0];
    document.getElementById('mCondAfter').value   = 'Baik';
    document.getElementById('mDesc').value        = '';
    document.getElementById('mCost').value        = 0;
    document.getElementById('mNotes').value       = '';
    document.getElementById('bhpUsedList').innerHTML = '';
    document.getElementById('maintError').style.display = 'none';
    openModal('maintModal');
}

// ── BHP Rows ───────────────────────────────────────
function addBhpRow() {
    const container = document.getElementById('bhpUsedList');
    const rowId = 'bhpRow_' + Date.now();

    const options = CONSUMABLES.map(c =>
        `<option value="${c.id}" data-unit="${c.unit ?? ''}" data-stock="${c.stock ?? 0}">
            ${c.name} (Stok: ${c.stock ?? 0} ${c.unit ?? ''})
        </option>`
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
    const qtyInput = row.querySelector('.bhp-qty');
    const unitLabel = row.querySelector('.bhp-unit-label');
    
    qtyInput.max = stock;
    qtyInput.title = `Stok tersedia: ${stock}`;
    unitLabel.textContent = unit;
}

// ── Submit Maintenance Log ─────────────────────────
async function submitMaintLog(e) {
    e.preventDefault();
    const assetId = document.getElementById('mAssetId').value;
    const errEl   = document.getElementById('maintError');
    errEl.style.display = 'none';

    // Kumpulkan BHP yang digunakan
    const bhpRows = document.querySelectorAll('#bhpUsedList .bhp-item-row');
    const consumables_used = [];
    let bhpValid = true;

    bhpRows.forEach(row => {
        const sel = row.querySelector('.bhp-select');
        const qty = row.querySelector('.bhp-qty');
        if (!sel.value) {
            errEl.textContent = 'Pilih BHP atau hapus baris BHP yang kosong.';
            errEl.style.display = 'block';
            bhpValid = false;
            return;
        }
        const qtyVal = parseInt(qty.value);
        if (isNaN(qtyVal) || qtyVal <= 0) {
            errEl.textContent = 'Jumlah BHP harus berupa angka positif.';
            errEl.style.display = 'block';
            bhpValid = false;
            return;
        }
        const maxStock = parseInt(sel.options[sel.selectedIndex]?.dataset?.stock) || 0;
        if (qtyVal > maxStock) {
            const name = sel.options[sel.selectedIndex].textContent.split('(')[0].trim();
            errEl.textContent = `Stok "${name}" tidak cukup. Tersedia: ${maxStock}.`;
            errEl.style.display = 'block';
            bhpValid = false;
            return;
        }
        consumables_used.push({ consumable_id: parseInt(sel.value), quantity_used: qtyVal });
    });

    if (!bhpValid) return;

    const body = {
        maintenance_date: document.getElementById('mDate').value,
        description:      document.getElementById('mDesc').value.trim(),
        condition_after:  document.getElementById('mCondAfter').value,
        cost:             parseFloat(document.getElementById('mCost').value) || 0,
        notes:            document.getElementById('mNotes').value.trim(),
        consumables_used,
    };

    const btn = document.getElementById('maintSubmitBtn');
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> Menyimpan...';

    try {
        const resp = await fetch(`${API_URL}/api/maintenance/asset/${assetId}`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'Authorization': `Bearer ${TOKEN}` },
            body: JSON.stringify(body)
        });
        const data = await resp.json();
        if (resp.ok) {
            showToast(data.message || 'Log maintenance berhasil disimpan.');
            closeModal('maintModal');
            setTimeout(() => location.reload(), 900);
        } else {
            errEl.textContent = data.message || 'Terjadi kesalahan.';
            errEl.style.display = 'block';
        }
    } catch {
        errEl.textContent = 'Tidak dapat terhubung ke server.';
        errEl.style.display = 'block';
    } finally {
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-save mr-1"></i> Simpan Log';
    }
}

// ── Logs History Modal ─────────────────────────────
let currentLogsAssetId = null;

async function openLogsModal(assetId, name) {
    currentLogsAssetId = assetId;
    document.getElementById('logsAssetName').textContent = `Aset: ${name}`;
    document.getElementById('logsContent').innerHTML = `
        <div style="text-align:center;padding:2rem;color:#adb5bd;">
            <i class="fas fa-spinner fa-spin" style="font-size:2rem;"></i>
            <p>Memuat riwayat...</p>
        </div>`;
    openModal('logsModal');
    await loadLogs(assetId);
}

async function loadLogs(assetId) {
    try {
        const resp = await fetch(`${API_URL}/api/maintenance/asset/${assetId}`, {
            headers: { 'Authorization': `Bearer ${TOKEN}` }
        });
        const data = await resp.json();
        renderLogs(data.logs ?? []);
    } catch {
        document.getElementById('logsContent').innerHTML =
            `<p style="color:#c62828;text-align:center;padding:1.5rem;">Gagal memuat data riwayat.</p>`;
    }
}

function renderLogs(logs) {
    const container = document.getElementById('logsContent');
    if (!logs.length) {
        container.innerHTML = `
            <div style="text-align:center;padding:2.5rem;color:#adb5bd;">
                <i class="fas fa-history" style="font-size:2.5rem;margin-bottom:1rem;display:block;"></i>
                <p>Belum ada riwayat maintenance untuk aset ini.</p>
            </div>`;
        return;
    }

    const condBadge = (cond) => {
        const map = {
            'Baik': 'cond-baik', 'Perlu Maintenance': 'cond-maint',
            'Rusak Ringan': 'cond-rusak-ringan', 'Rusak Berat': 'cond-rusak-berat',
        };
        const cls = map[cond] ?? 'cond-maint';
        return `<span class="cond-badge ${cls}"><span class="cb-dot"></span>${cond}</span>`;
    };

    container.innerHTML = logs.map(log => {
        const bhpUsed = (log.used_consumables ?? []).map(u =>
            `<span class="log-bhp-chip"><i class="fas fa-flask"></i> ${u.consumable_name} &times; ${u.quantity_used} ${u.unit ?? ''}</span>`
        ).join('');

        const bhpSection = bhpUsed ? `<div class="log-bhp-used">${bhpUsed}</div>` : '';
        const costStr = log.cost > 0
            ? `<span> • Biaya: <strong>Rp ${Number(log.cost).toLocaleString('id-ID')}</strong></span>` : '';
        const notesStr = log.notes ? `<span> • ${log.notes}</span>` : '';
        const performer = log.performed_by_name ? `Oleh: <strong>${log.performed_by_name}</strong> • ` : '';

        return `
        <div class="log-item" id="log_${log.id}">
            <button class="btn-del-log" title="Hapus log ini" onclick="deleteLog(${log.id})">
                <i class="fas fa-trash"></i>
            </button>
            <div class="log-date">${log.maintenance_date ? new Date(log.maintenance_date).toLocaleDateString('id-ID',{day:'2-digit',month:'long',year:'numeric'}) : '-'}</div>
            <div class="log-desc">${log.description}</div>
            <div class="log-meta">${performer}${costStr}${notesStr}</div>
            <div class="log-cond">
                ${condBadge(log.condition_before ?? '-')}
                <span class="cond-arrow"><i class="fas fa-arrow-right"></i></span>
                ${condBadge(log.condition_after)}
            </div>
            ${bhpSection}
        </div>`;
    }).join('');
}

async function deleteLog(logId) {
    if (!confirm('Hapus log maintenance ini? Catatan akan dihapus permanen (stok yang sudah berkurang tidak dikembalikan).')) return;
    try {
        const resp = await fetch(`${API_URL}/api/maintenance/${logId}`, {
            method: 'DELETE',
            headers: { 'Authorization': `Bearer ${TOKEN}` }
        });
        const data = await resp.json();
        if (resp.ok) {
            showToast(data.message || 'Log berhasil dihapus.');
            const el = document.getElementById(`log_${logId}`);
            if (el) el.remove();
            if (!document.querySelector('.log-item')) {
                document.getElementById('logsContent').innerHTML =
                    `<div style="text-align:center;padding:2.5rem;color:#adb5bd;">
                        <i class="fas fa-history" style="font-size:2.5rem;margin-bottom:1rem;display:block;"></i>
                        <p>Belum ada riwayat maintenance untuk aset ini.</p>
                    </div>`;
            }
        } else {
            showToast(data.message || 'Gagal menghapus log.', 'error');
        }
    } catch {
        showToast('Tidak dapat terhubung ke server.', 'error');
    }
}

// ── Filter / Search ────────────────────────────────
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    const roomFilter  = document.getElementById('roomFilter');
    const condFilter  = document.getElementById('condFilter');
    const rows        = document.querySelectorAll('.item-row');
    const resultCount = document.getElementById('resultCount');

    function applyFilters() {
        const q    = searchInput.value.toLowerCase();
        const room = roomFilter.value;
        const cond = condFilter.value;
        let count = 0;

        rows.forEach(row => {
            const mQ = (row.dataset.search || '').includes(q);
            const mR = !room || row.dataset.room === room;
            const mC = !cond || row.dataset.cond === cond;
            const show = mQ && mR && mC;
            row.style.display = show ? '' : 'none';
            if (show) count++;
        });
        resultCount.textContent = count + ' aset';
    }

    if (searchInput) searchInput.addEventListener('input',  applyFilters);
    if (roomFilter)  roomFilter.addEventListener('change', applyFilters);
    if (condFilter)  condFilter.addEventListener('change', applyFilters);
});
</script>
@endsection
