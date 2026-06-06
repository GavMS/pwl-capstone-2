@extends('dashboard.layout')
@section('title', 'Manajemen BHP')
@section('page_title', 'BHP')

@section('content')
@php
    $apiUrl = env('NODEJS_API_URL', 'http://localhost:5000');
    $token  = session('token', '');
@endphp

<style>
/* ─── Page Styles ─────────────────────────────────── */
.page-header-card {
    background: #fff;
    border-radius: 1rem;
    box-shadow: 0 20px 27px 0 rgba(0,0,0,.05);
    padding: 1.5rem;
    margin-bottom: 1.5rem;
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 1rem;
}
.page-title  { font-size: 1.25rem; font-weight: 700; color: #344767; margin: 0 0 .25rem; }
.page-subtitle { font-size: .875rem; color: #7b809a; margin: 0; }

/* ─── Stats row ──────────────────────────────────── */
.stats-row {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
    gap: 1rem;
    margin-bottom: 1.5rem;
}
.stat-card {
    background: #fff;
    border-radius: 1rem;
    box-shadow: 0 20px 27px 0 rgba(0,0,0,.05);
    padding: 1.25rem 1.5rem;
    display: flex;
    align-items: center;
    gap: 1rem;
}
.stat-icon {
    width: 3rem; height: 3rem;
    border-radius: .75rem;
    display: flex; align-items: center; justify-content: center;
    font-size: 1.2rem;
    flex-shrink: 0;
}
.stat-icon.purple  { background: linear-gradient(310deg,#7928ca,#ff007f); color:#fff; }
.stat-icon.green   { background: linear-gradient(310deg,#17ad37,#98ec2d); color:#fff; }
.stat-icon.orange  { background: linear-gradient(310deg,#f53939,#fbcf33); color:#fff; }
.stat-icon.blue    { background: linear-gradient(310deg,#2152ff,#21d4fd); color:#fff; }
.stat-val { font-size: 1.5rem; font-weight: 800; color: #344767; line-height: 1; }
.stat-lbl { font-size: .75rem; color: #7b809a; margin-top: .2rem; }

/* ─── Alert Warning ───────────────────────────────── */
.alert-warning {
    display: flex; align-items: center; gap: .75rem;
    padding: 1rem 1.25rem;
    background: #fff8e1; border: 1px solid #ffecb3;
    border-radius: .75rem; margin-bottom: 1.5rem;
    font-size: .875rem; color: #b97c00;
}
.alert-warning i { color: #f57f17; font-size: 1.1rem; }

/* ─── Table Card ──────────────────────────────────── */
.table-card {
    background: #fff; border-radius: 1rem;
    box-shadow: 0 20px 27px 0 rgba(0,0,0,.05);
    overflow: hidden;
}
.table-card-header {
    display: flex; align-items: center;
    justify-content: space-between;
    padding: 1rem 1.5rem;
    border-bottom: 1px solid #f0f2f5;
    flex-wrap: wrap; gap: .75rem;
}
.filter-bar { display: flex; align-items: center; gap: .75rem; flex-wrap: wrap; flex:1; }
.search-wrap { position: relative; flex: 1; min-width: 200px; }
.search-wrap .search-icon {
    position: absolute; left: .75rem; top: 50%;
    transform: translateY(-50%); color: #adb5bd; font-size: .75rem; pointer-events: none;
}
.search-wrap input {
    padding: .5rem .875rem .5rem 2.2rem; font-size: .8125rem;
    border: 1px solid #d2d6da; border-radius: .5rem;
    color: #344767; outline: none; width: 100%;
    transition: border-color .2s, box-shadow .2s;
}
.search-wrap input:focus { border-color: #7928ca; box-shadow: 0 0 0 2px rgba(121,40,202,.15); }

.filter-select {
    padding: .5rem .875rem; font-size: .8125rem;
    border: 1px solid #d2d6da; border-radius: .5rem;
    color: #344767; outline: none; background: #fff;
    cursor: pointer; transition: border-color .2s, box-shadow .2s; min-width: 140px;
}
.filter-select:focus { border-color: #7928ca; box-shadow: 0 0 0 2px rgba(121,40,202,.15); }
.result-count { font-size: .75rem; color: #adb5bd; font-weight: 600; white-space: nowrap; }

/* ─── Table ───────────────────────────────────────── */
.bhp-table { width: 100%; border-collapse: collapse; }
.bhp-table thead th {
    padding: .75rem 1.25rem; font-size: .65rem; font-weight: 700;
    text-transform: uppercase; letter-spacing: .05em;
    color: #adb5bd; border-bottom: 1px solid #f0f2f5;
    white-space: nowrap; background: #fff; text-align: left;
}
.bhp-table tbody td { padding: .875rem 1.25rem; border-bottom: 1px solid #f0f2f5; vertical-align: middle; }
.bhp-table tbody tr:last-child td { border-bottom: none; }
.bhp-table tbody tr:hover { background: #fafbfc; }

.item-title { font-size: .875rem; font-weight: 600; color: #344767; margin: 0; }
.item-code  { font-size: .8125rem; font-weight: 600; color: #7b809a; font-family: monospace; }
.cell-text  { font-size: .875rem; color: #7b809a; }

.cond-badge {
    display: inline-flex; align-items: center; gap: .35rem;
    padding: .3rem .85rem; border-radius: 2rem;
    font-size: .7rem; font-weight: 700; white-space: nowrap;
}
.cond-badge .cb-dot { width: 6px; height: 6px; border-radius: 50%; display: inline-block; }
.cond-aman   { background: #e8f5e9; color: #2e7d32; }
.cond-aman .cb-dot   { background: #4caf50; }
.cond-kritis { background: #fbe9e7; color: #c62828; }
.cond-kritis .cb-dot { background: #d32f2f; }

/* ─── Action Buttons ───────────────────────────────── */
.act-btn {
    display: inline-flex; align-items: center; gap: .35rem;
    padding: .35rem .75rem; border-radius: .5rem; font-size: .75rem;
    font-weight: 600; cursor: pointer; border: none; transition: all .2s;
    white-space: nowrap;
}
.btn-edit  { background: #e8f4ff; color: #1565c0; }
.btn-edit:hover  { background: #bbdefb; }
.btn-stock { background: #e8f5e9; color: #2e7d32; }
.btn-stock:hover { background: #c8e6c9; }
.btn-del   { background: #fbe9e7; color: #c62828; }
.btn-del:hover   { background: #ffcdd2; }
.btn-primary {
    display: inline-flex; align-items: center; gap: .5rem;
    padding: .6rem 1.25rem; background: linear-gradient(310deg,#7928ca,#ff007f);
    color: #fff; border: none; border-radius: .5rem; font-size: .875rem;
    font-weight: 600; cursor: pointer; transition: opacity .2s;
}
.btn-primary:hover { opacity: .9; }

/* ─── Modal ───────────────────────────────────────── */
.modal-backdrop {
    display: none; position: fixed; inset: 0; z-index: 1000;
    background: rgba(0,0,0,.45); align-items: center; justify-content: center;
}
.modal-backdrop.open { display: flex; }
.modal-box {
    background: #fff; border-radius: 1.25rem;
    box-shadow: 0 25px 60px rgba(0,0,0,.18);
    padding: 2rem; width: 100%; max-width: 540px;
    max-height: 90vh; overflow-y: auto;
    animation: modalIn .25s ease;
}
@keyframes modalIn { from { transform: translateY(-30px); opacity: 0; } to { transform: translateY(0); opacity: 1; } }
.modal-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.5rem; }
.modal-title  { font-size: 1.1rem; font-weight: 700; color: #344767; }
.modal-close  {
    background: none; border: none; font-size: 1.25rem;
    color: #adb5bd; cursor: pointer; transition: color .2s;
    padding: 0; line-height: 1;
}
.modal-close:hover { color: #344767; }

/* ─── Form Elements ───────────────────────────────── */
.form-group { margin-bottom: 1rem; }
.form-label { display: block; font-size: .8rem; font-weight: 600; color: #344767; margin-bottom: .4rem; }
.form-label span { color: #d32f2f; }
.form-control {
    width: 100%; padding: .6rem .875rem; font-size: .875rem;
    border: 1px solid #d2d6da; border-radius: .5rem;
    color: #344767; outline: none;
    transition: border-color .2s, box-shadow .2s;
    box-sizing: border-box;
}
.form-control:focus { border-color: #7928ca; box-shadow: 0 0 0 2px rgba(121,40,202,.15); }
.form-row   { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; }
.form-footer { display: flex; gap: .75rem; justify-content: flex-end; margin-top: 1.5rem; }
.btn-cancel {
    padding: .6rem 1.25rem; background: #f5f6fb; border: none;
    border-radius: .5rem; font-size: .875rem; font-weight: 600;
    color: #7b809a; cursor: pointer; transition: background .2s;
}
.btn-cancel:hover { background: #e9ecef; }
.btn-submit {
    padding: .6rem 1.5rem; background: linear-gradient(310deg,#7928ca,#ff007f);
    border: none; border-radius: .5rem; font-size: .875rem;
    font-weight: 600; color: #fff; cursor: pointer; transition: opacity .2s;
}
.btn-submit:hover { opacity: .9; }

/* ─── Adjust Stock modal ──────────────────────────── */
.adjust-info {
    background: #f5f6fb; border-radius: .75rem;
    padding: .875rem 1.25rem; margin-bottom: 1.25rem;
    font-size: .875rem; color: #344767;
}
.adjust-info strong { display: block; font-size: 1rem; font-weight: 700; margin-bottom: .2rem; }

/* ─── Toast ───────────────────────────────────────── */
#toast-container {
    position: fixed; bottom: 1.5rem; right: 1.5rem; z-index: 9999;
    display: flex; flex-direction: column; gap: .5rem;
}
.toast {
    padding: .875rem 1.25rem; border-radius: .75rem; font-size: .875rem;
    font-weight: 600; color: #fff; box-shadow: 0 4px 20px rgba(0,0,0,.15);
    animation: toastIn .3s ease; min-width: 260px;
}
.toast.success { background: linear-gradient(310deg,#17ad37,#98ec2d); }
.toast.error   { background: linear-gradient(310deg,#f53939,#fbcf33); }
@keyframes toastIn { from { transform: translateX(40px); opacity: 0; } to { transform: translateX(0); opacity: 1; } }
</style>

<div class="flex flex-wrap -mx-3">
    <div class="w-full px-3">

        {{-- Page Header --}}
        <div class="page-header-card">
            <div>
                <h4 class="page-title"><i class="fas fa-flask mr-2" style="color:#7928ca;"></i>Manajemen BHP</h4>
                <p class="page-subtitle">Kelola stok Bahan Habis Pakai (BHP) laboratorium — tambah, edit, sesuaikan stok, atau hapus item.</p>
            </div>
            <button class="btn-primary" onclick="openAddModal()">
                <i class="fas fa-plus"></i> Tambah BHP
            </button>
        </div>

        {{-- Error Banner --}}
        @if(isset($error) && $error)
        <div class="mb-4 p-4 rounded-lg bg-red-50 border border-red-200 text-red-600 text-sm font-semibold">
            <i class="fas fa-exclamation-triangle mr-2"></i> {{ $error }}
        </div>
        @endif

        {{-- Stats Row --}}
        @php
            $totalItems   = count($consumables);
            $criticalItems = collect($consumables)->filter(fn($c) => ($c['stock'] ?? 0) <= ($c['min_stock'] ?? 0));
            $amanItems    = $totalItems - $criticalItems->count();
            $totalValue   = collect($consumables)->sum(fn($c) => ($c['stock'] ?? 0) * ($c['price'] ?? 0));
        @endphp
        <div class="stats-row">
            <div class="stat-card">
                <div class="stat-icon purple"><i class="fas fa-flask"></i></div>
                <div><div class="stat-val">{{ $totalItems }}</div><div class="stat-lbl">Total Item BHP</div></div>
            </div>
            <div class="stat-card">
                <div class="stat-icon green"><i class="fas fa-check-circle"></i></div>
                <div><div class="stat-val">{{ $amanItems }}</div><div class="stat-lbl">Stok Aman</div></div>
            </div>
            <div class="stat-card">
                <div class="stat-icon orange"><i class="fas fa-exclamation-triangle"></i></div>
                <div><div class="stat-val">{{ $criticalItems->count() }}</div><div class="stat-lbl">Stok Kritis</div></div>
            </div>
            <div class="stat-card">
                <div class="stat-icon blue"><i class="fas fa-coins"></i></div>
                <div>
                    <div class="stat-val" style="font-size:1.1rem;">Rp {{ number_format($totalValue, 0, ',', '.') }}</div>
                    <div class="stat-lbl">Total Nilai Stok</div>
                </div>
            </div>
        </div>

        {{-- Critical Warning --}}
        @if($criticalItems->count() > 0)
        <div class="alert-warning">
            <i class="fas fa-exclamation-triangle"></i>
            <div>
                <strong>{{ $criticalItems->count() }} item di bawah stok minimum!</strong>
                {{ $criticalItems->pluck('name')->join(', ') }}.
            </div>
        </div>
        @endif

        {{-- Table Card --}}
        <div class="table-card">
            <div class="table-card-header">
                <div class="filter-bar">
                    <div class="search-wrap">
                        <i class="fas fa-search search-icon"></i>
                        <input type="text" id="searchInput" placeholder="Cari nama, kode, atau kategori BHP..." />
                    </div>
                    @php $categories = collect($consumables)->pluck('category')->unique()->filter()->values(); @endphp
                    <select id="catFilter" class="filter-select">
                        <option value="">Semua Kategori</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat }}">{{ $cat }}</option>
                        @endforeach
                    </select>
                    <select id="statusFilter" class="filter-select">
                        <option value="">Semua Status</option>
                        <option value="aman">Aman</option>
                        <option value="kritis">Kritis</option>
                    </select>
                </div>
                <span class="result-count" id="resultCount">{{ count($consumables) }} item</span>
            </div>

            <div style="overflow-x: auto;">
                <table class="bhp-table" id="bhpTable">
                    <thead>
                        <tr>
                            <th>KODE</th>
                            <th>NAMA BHP</th>
                            <th>KATEGORI</th>
                            <th>LOKASI</th>
                            <th>SATUAN</th>
                            <th style="text-align:center;">STOK</th>
                            <th style="text-align:center;">MIN</th>
                            <th>STATUS</th>
                            <th style="text-align:right;">HARGA</th>
                            <th style="text-align:center;">AKSI</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($consumables as $c)
                        @php $isCritical = ($c['stock'] ?? 0) <= ($c['min_stock'] ?? 0); @endphp
                        <tr class="item-row"
                            data-search="{{ strtolower($c['name'] . ' ' . ($c['code'] ?? '') . ' ' . ($c['category'] ?? '')) }}"
                            data-cat="{{ $c['category'] ?? '' }}"
                            data-status="{{ $isCritical ? 'kritis' : 'aman' }}">

                            <td><span class="item-code">{{ $c['code'] ?? '-' }}</span></td>
                            <td>
                                <p class="item-title">{{ $c['name'] }}</p>
                                @if($c['description'] ?? '')
                                <p style="font-size:.75rem;color:#adb5bd;margin:0;">{{ Str::limit($c['description'], 50) }}</p>
                                @endif
                            </td>
                            <td><span class="cell-text">{{ $c['category'] ?? '-' }}</span></td>
                            <td><span class="cell-text font-semibold">{{ $c['location'] ?? '-' }}</span></td>
                            <td><span class="cell-text">{{ $c['unit'] ?? '-' }}</span></td>
                            <td style="text-align:center;">
                                <span class="cell-text font-bold {{ $isCritical ? 'text-red-600' : 'text-slate-800' }}" style="font-size:.95rem;">
                                    {{ $c['stock'] ?? 0 }}
                                </span>
                            </td>
                            <td style="text-align:center;"><span class="cell-text">{{ $c['min_stock'] ?? 0 }}</span></td>
                            <td>
                                @if($isCritical)
                                <span class="cond-badge cond-kritis"><span class="cb-dot"></span> Kritis</span>
                                @else
                                <span class="cond-badge cond-aman"><span class="cb-dot"></span> Aman</span>
                                @endif
                            </td>
                            <td style="text-align:right;">
                                <span class="cell-text">Rp {{ number_format($c['price'] ?? 0, 0, ',', '.') }}</span>
                            </td>
                            <td style="text-align:center;">
                                <div style="display:flex;gap:.4rem;justify-content:center;flex-wrap:wrap;">
                                    <button class="act-btn btn-stock" title="Sesuaikan Stok"
                                        onclick="openStockModal({{ $c['id'] }}, '{{ addslashes($c['name']) }}', {{ $c['stock'] ?? 0 }}, '{{ addslashes($c['unit'] ?? '') }}')">
                                        <i class="fas fa-boxes"></i> Stok
                                    </button>
                                    <button class="act-btn btn-edit" title="Edit BHP"
                                        onclick="openEditModal({{ json_encode($c) }})">
                                        <i class="fas fa-pen"></i> Edit
                                    </button>
                                    <button class="act-btn btn-del" title="Hapus BHP"
                                        onclick="deleteBhp({{ $c['id'] }}, '{{ addslashes($c['name']) }}')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr id="emptyRow">
                            <td colspan="9" style="text-align:center; padding: 3rem;">
                                <i class="fas fa-flask" style="font-size:2.5rem; color:#d2d6da; margin-bottom:1rem; display:block;"></i>
                                <p style="color:#7b809a;">Tidak ada data BHP. Klik "Tambah BHP" untuk menambahkan.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- ── Modal: Tambah / Edit BHP ─────────────────────────── --}}
<div class="modal-backdrop" id="bhpModal">
    <div class="modal-box">
        <div class="modal-header">
            <h5 class="modal-title" id="modalTitle">Tambah BHP Baru</h5>
            <button class="modal-close" onclick="closeModal('bhpModal')">&times;</button>
        </div>
        <form id="bhpForm" onsubmit="submitBhpForm(event)">
            <input type="hidden" id="bhpId" value="">

            {{-- Nama + Kode --}}
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Nama BHP <span>*</span></label>
                    <input type="text" id="fName" class="form-control"
                        placeholder="Contoh: Sarung Tangan Nitril M" required
                        oninput="suggestCode()">
                </div>
                <div class="form-group">
                    <label class="form-label">Kode BHP</label>
                    <div style="display:flex; align-items:stretch;">
                        <input type="text" id="fCodePrefix" class="form-control" value="BHP/XXX/" readonly tabindex="-1"
                            style="width:90px; border-top-right-radius:0; border-bottom-right-radius:0; border-right:0; background-color:#f8f9fa; color:#7b809a; font-weight:600; padding-left:.5rem; padding-right:.5rem; text-align:center;">
                        <input type="text" id="fCodeNum" class="form-control" placeholder="001" style="flex:1; border-radius:0; border-right:0;">
                        <button type="button" id="genCodeBtn" onclick="generateCode()"
                            style="padding:0 .75rem; background:#f5f6fb; border:1px solid #d2d6da; border-top-right-radius:.5rem; border-bottom-right-radius:.5rem;
                                   font-size:.75rem; font-weight:600; color:#7928ca; cursor:pointer; transition:all .2s;"
                            title="Auto-generate nomor urut">
                            <i class="fas fa-magic"></i>
                        </button>
                    </div>
                </div>
            </div>

            {{-- Kategori + Satuan (dropdown) --}}
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Kategori</label>
                    <input type="text" id="fCategory" list="catList" class="form-control" placeholder="Pilih atau ketik Lainnya (isi manual)..." onchange="suggestCode()">
                    <datalist id="catList">
                        <option value="APD">APD (Alat Pelindung Diri)</option>
                        <option value="Reagen">Reagen & Bahan Kimia</option>
                        <option value="Gelas">Alat Gelas (Glassware)</option>
                        <option value="Plastik">Plastik & Consumable Sekali Pakai</option>
                        <option value="Filter">Filter & Membran</option>
                        <option value="Media">Media Kultur & Agar</option>
                        <option value="Kultur">Peralatan Kultur Jaringan</option>
                        <option value="Kimia">Bahan Kimia Umum</option>
                        <option value="Kabel">Kabel & Jaringan</option>
                        <option value="Hardware">Hardware Komputer</option>
                    </datalist>
                </div>
                <div class="form-group">
                    <label class="form-label">Satuan (Unit)</label>
                    <input type="text" id="fUnit" list="unitList" class="form-control" placeholder="Pilih atau ketik Lainnya (isi manual)...">
                    <datalist id="unitList">
                        <option value="box (100 pcs)">
                        <option value="box (50 pcs)">
                        <option value="box (20 pcs)">
                        <option value="pcs">
                        <option value="liter">
                        <option value="mL">
                        <option value="gram">
                        <option value="kg">
                        <option value="lusin">
                        <option value="pak">
                        <option value="rak (96 pcs)">
                        <option value="bag (500 pcs)">
                        <option value="pack (100 pcs)">
                        <option value="botol">
                        <option value="tube">
                        <option value="ampul">
                        <option value="roll (305m)">
                    </datalist>
                </div>
            </div>

            {{-- Stok + Stok Minimum --}}
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Stok Saat Ini <span>*</span></label>
                    <input type="number" id="fStock" class="form-control" min="0" value="0" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Stok Minimum</label>
                    <input type="number" id="fMinStock" class="form-control" min="0" value="0">
                </div>
            </div>

            {{-- Harga + Lokasi (dropdown dari rooms) --}}
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Harga Satuan (Rp)</label>
                    <input type="number" id="fPrice" class="form-control" min="0" value="0">
                </div>
                <div class="form-group">
                    <label class="form-label">Lokasi Penyimpanan</label>
                    <select id="fLocation" class="form-control">
                        <option value="">-- Pilih Ruangan --</option>
                        <option value="__loading__" disabled>⏳ Memuat ruangan...</option>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Deskripsi / Catatan</label>
                <textarea id="fDescription" class="form-control" rows="2"
                    placeholder="Keterangan tambahan (opsional)..."></textarea>
            </div>

            <div id="formError" style="color:#c62828; font-size:.8125rem; margin-bottom:.5rem; display:none;"></div>
            <div class="form-footer">
                <button type="button" class="btn-cancel" onclick="closeModal('bhpModal')">Batal</button>
                <button type="submit" class="btn-submit" id="submitBtn">
                    <i class="fas fa-save mr-1"></i> Simpan
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ── Modal: Sesuaikan Stok ────────────────────────────── --}}
<div class="modal-backdrop" id="stockModal">
    <div class="modal-box" style="max-width:440px;">
        <div class="modal-header">
            <h5 class="modal-title">Sesuaikan Stok</h5>
            <button class="modal-close" onclick="closeModal('stockModal')">&times;</button>
        </div>
        <div class="adjust-info" id="stockInfo">
            <strong id="stockItemName">-</strong>
            Stok saat ini: <strong id="stockCurrent">0</strong> <span id="stockUnit"></span>
        </div>
        <div class="form-group">
            <label class="form-label">Jenis Penyesuaian <span>*</span></label>
            <select id="adjType" class="form-control" onchange="toggleAdjType()">
                <option value="add">Tambah Stok (Pengadaan / Koreksi Tambah)</option>
                <option value="sub">Kurangi Stok (Pemakaian / Koreksi Kurang)</option>
                <option value="set">Set Stok Langsung (Opname)</option>
            </select>
        </div>
        <div class="form-group">
            <label class="form-label" id="adjLabel">Jumlah yang Ditambah <span>*</span></label>
            <input type="number" id="adjQty" class="form-control" min="1" value="1" placeholder="Masukkan jumlah...">
        </div>
        <div id="adjPreview" style="font-size:.875rem; color:#344767; padding:.75rem 1rem; background:#f5f6fb; border-radius:.5rem; margin-bottom:.75rem;">
            Stok baru: <strong id="newStockPreview">-</strong>
        </div>
        <div id="stockError" style="color:#c62828; font-size:.8125rem; margin-bottom:.5rem; display:none;"></div>
        <div class="form-footer">
            <button type="button" class="btn-cancel" onclick="closeModal('stockModal')">Batal</button>
            <button type="button" class="btn-submit" onclick="submitStockAdjust()">
                <i class="fas fa-check mr-1"></i> Konfirmasi
            </button>
        </div>
        <input type="hidden" id="adjItemId" value="">
        <input type="hidden" id="adjCurrentStock" value="0">
    </div>
</div>

{{-- Toast Container --}}
<div id="toast-container"></div>

<script>
const API_URL = '{{ $apiUrl }}';
const TOKEN   = '{{ $token }}';

// ── Toast ──────────────────────────────────────────
function showToast(msg, type = 'success') {
    const c = document.getElementById('toast-container');
    const t = document.createElement('div');
    t.className = `toast ${type}`;
    t.innerHTML = `<i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'} mr-2"></i>${msg}`;
    c.appendChild(t);
    setTimeout(() => { t.style.opacity = '0'; t.style.transition = 'opacity .4s'; setTimeout(() => t.remove(), 400); }, 3500);
}

// ── Modal open/close ───────────────────────────────
function openModal(id) { document.getElementById(id).classList.add('open'); }
function closeModal(id) { document.getElementById(id).classList.remove('open'); }
document.querySelectorAll('.modal-backdrop').forEach(m => {
    m.addEventListener('click', e => { if (e.target === m) closeModal(m.id); });
});

// ── Load Meta (rooms untuk dropdown lokasi) ──────────────────
async function loadMeta() {
    try {
        const resp = await fetch(`${API_URL}/api/consumables/meta`, {
            headers: { 'Authorization': `Bearer ${TOKEN}` }
        });
        if (!resp.ok) return;
        const data = await resp.json();

        const sel = document.getElementById('fLocation');
        sel.innerHTML = '<option value="">-- Pilih Ruangan --</option>';

        // Tambahkan room sebagai opsi
        if (data.rooms && data.rooms.length > 0) {
            data.rooms.forEach(r => {
                const opt = document.createElement('option');
                opt.value = r.name;
                opt.textContent = `${r.code} — ${r.name}`;
                sel.appendChild(opt);
            });
        } else {
            sel.innerHTML += '<option value="" disabled>Belum ada ruangan terdaftar</option>';
        }
    } catch(e) {
        console.error('Gagal memuat ruangan:', e);
        const sel = document.getElementById('fLocation');
        sel.innerHTML = '<option value="">-- Gagal memuat ruangan --</option>';
    }
}

// ── Auto-generate Kode BHP ─────────────────────────
const CAT_PREFIX = {
    'APD': 'BHP/APD/', 'Reagen': 'BHP/REA/', 'Gelas': 'BHP/GLS/', 
    'Plastik': 'BHP/PLT/', 'Filter': 'BHP/FLT/', 'Media': 'BHP/MED/', 
    'Kultur': 'BHP/KLT/', 'Kimia': 'BHP/KIM/', 'Kabel': 'BHP/KBL/', 
    'Hardware': 'BHP/HW/'
};

function getCategoryPrefix(cat) {
    // Cari prefix persis
    for (const key in CAT_PREFIX) {
        if (cat.includes(key)) return CAT_PREFIX[key];
    }
    // Jika tidak ada, ambil 3 huruf pertama
    const abbr = (cat || 'XXX').toUpperCase().replace(/[^A-Z]/g, '').substring(0, 3).padEnd(3, 'X');
    return `BHP/${abbr}/`;
}

function suggestCode() {
    const catVal = document.getElementById('fCategory').value;
    if (!catVal) return;
    
    // Ganti prefix otomatis sesuai kategori
    const prefix = getCategoryPrefix(catVal);
    document.getElementById('fCodePrefix').value = prefix;
}

function generateCode() {
    // Ambil nomor urut dari consumables yang ada
    const existingItems = document.querySelectorAll('.item-row');
    const num = String(existingItems.length + 1).padStart(3, '0');
    
    const numEl = document.getElementById('fCodeNum');
    numEl.value = num;
    
    // Highlight field sebentar
    numEl.style.borderColor = '#7928ca';
    numEl.style.boxShadow = '0 0 0 2px rgba(121,40,202,.25)';
    setTimeout(() => { numEl.style.borderColor = ''; numEl.style.boxShadow = ''; }, 1200);
}

// ── Add BHP ────────────────────────────────────────
function openAddModal() {
    document.getElementById('modalTitle').textContent = 'Tambah BHP Baru';
    document.getElementById('bhpId').value = '';
    document.getElementById('bhpForm').reset();
    document.getElementById('fStock').value = 0;
    document.getElementById('fMinStock').value = 0;
    document.getElementById('fPrice').value = 0;
    document.getElementById('formError').style.display = 'none';
    openModal('bhpModal');
}

// ── Edit BHP ───────────────────────────────────────
function openEditModal(item) {
    document.getElementById('modalTitle').textContent = 'Edit Data BHP';
    document.getElementById('bhpId').value = item.id;
    document.getElementById('fName').value = item.name ?? '';
    
    // Parse kode menjadi prefix dan angka
    const code = item.code ?? '';
    const lastSlash = code.lastIndexOf('/');
    if (lastSlash !== -1 && lastSlash < code.length - 1) {
        const prefix = code.substring(0, lastSlash + 1);
        const num = code.substring(lastSlash + 1);
        
        document.getElementById('fCodePrefix').value = prefix;
        document.getElementById('fCodeNum').value = num;
    } else {
        document.getElementById('fCodePrefix').value = 'BHP/XXX/';
        document.getElementById('fCodeNum').value = code; // fallback
    }

    document.getElementById('fStock').value    = item.stock ?? 0;
    document.getElementById('fMinStock').value = item.min_stock ?? 0;
    document.getElementById('fPrice').value    = item.price ?? 0;
    document.getElementById('fDescription').value = item.description ?? '';
    document.getElementById('fCategory').value = item.category ?? '';
    document.getElementById('fUnit').value = item.unit ?? '';
    // Set lokasi — pastikan option ada, jika belum buat sementara
    const locSel = document.getElementById('fLocation');
    const locVal = item.location ?? '';
    if (locVal) {
        if (![...locSel.options].some(o => o.value === locVal)) {
            const opt = document.createElement('option');
            opt.value = locVal;
            opt.textContent = `${locVal}`;
            locSel.appendChild(opt);
        }
        locSel.value = locVal;
    } else {
        locSel.value = '';
    }

    document.getElementById('formError').style.display = 'none';
    openModal('bhpModal');
}

// ── Submit form (Add/Edit) ─────────────────────────
async function submitBhpForm(e) {
    e.preventDefault();
    const id = document.getElementById('bhpId').value;

    const prefix = document.getElementById('fCodePrefix').value;
    const num = document.getElementById('fCodeNum').value.trim();
    const fullCode = num ? `${prefix}${num}` : '';

    const body = {
        name:        document.getElementById('fName').value.trim(),
        code:        fullCode,
        category:    document.getElementById('fCategory').value.trim(),
        unit:        document.getElementById('fUnit').value.trim(),
        stock:       parseInt(document.getElementById('fStock').value) || 0,
        min_stock:   parseInt(document.getElementById('fMinStock').value) || 0,
        price:       parseFloat(document.getElementById('fPrice').value) || 0,
        location:    document.getElementById('fLocation').value.trim(),
        description: document.getElementById('fDescription').value.trim(),
    };

    const errEl = document.getElementById('formError');
    errEl.style.display = 'none';
    document.getElementById('submitBtn').disabled = true;
    document.getElementById('submitBtn').innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> Menyimpan...';

    try {
        const method = id ? 'PUT' : 'POST';
        const url    = id ? `${API_URL}/api/consumables/${id}` : `${API_URL}/api/consumables`;
        const resp = await fetch(url, {
            method,
            headers: { 'Content-Type': 'application/json', 'Authorization': `Bearer ${TOKEN}` },
            body: JSON.stringify(body)
        });
        const data = await resp.json();
        if (resp.ok) {
            showToast(data.message || 'BHP berhasil disimpan.');
            closeModal('bhpModal');
            setTimeout(() => location.reload(), 900);
        } else {
            errEl.textContent = data.message || 'Terjadi kesalahan.';
            errEl.style.display = 'block';
        }
    } catch (err) {
        errEl.textContent = 'Tidak dapat terhubung ke server.';
        errEl.style.display = 'block';
    } finally {
        document.getElementById('submitBtn').disabled = false;
        document.getElementById('submitBtn').innerHTML = '<i class="fas fa-save mr-1"></i> Simpan';
    }
}

// ── Delete BHP ─────────────────────────────────────
async function deleteBhp(id, name) {
    if (!confirm(`Hapus BHP "${name}"? Tindakan ini tidak dapat dibatalkan.`)) return;

    try {
        const resp = await fetch(`${API_URL}/api/consumables/${id}`, {
            method: 'DELETE',
            headers: { 'Authorization': `Bearer ${TOKEN}` }
        });
        const data = await resp.json();
        if (resp.ok) {
            showToast(data.message || 'BHP berhasil dihapus.');
            setTimeout(() => location.reload(), 900);
        } else {
            showToast(data.message || 'Gagal menghapus BHP.', 'error');
        }
    } catch {
        showToast('Tidak dapat terhubung ke server.', 'error');
    }
}

// ── Stock Adjustment Modal ─────────────────────────
function openStockModal(id, name, currentStock, unit) {
    document.getElementById('adjItemId').value    = id;
    document.getElementById('adjCurrentStock').value = currentStock;
    document.getElementById('stockItemName').textContent = name;
    document.getElementById('stockCurrent').textContent  = currentStock;
    document.getElementById('stockUnit').textContent     = unit;
    document.getElementById('adjType').value  = 'add';
    document.getElementById('adjQty').value   = 1;
    document.getElementById('adjQty').min     = 1;
    document.getElementById('stockError').style.display = 'none';
    document.getElementById('adjLabel').textContent = 'Jumlah yang Ditambah *';
    updatePreview();
    openModal('stockModal');
}

function toggleAdjType() {
    const type = document.getElementById('adjType').value;
    const labels = { add: 'Jumlah yang Ditambah *', sub: 'Jumlah yang Dikurangi *', set: 'Stok Baru (nilai pasti) *' };
    document.getElementById('adjLabel').textContent = labels[type] || 'Jumlah *';
    if (type === 'set') {
        document.getElementById('adjQty').min = 0;
        document.getElementById('adjQty').value = parseInt(document.getElementById('adjCurrentStock').value) || 0;
    } else {
        document.getElementById('adjQty').min = 1;
        document.getElementById('adjQty').value = 1;
    }
    updatePreview();
}

function updatePreview() {
    const type    = document.getElementById('adjType').value;
    const current = parseInt(document.getElementById('adjCurrentStock').value) || 0;
    const qty     = parseInt(document.getElementById('adjQty').value) || 0;
    let newVal;
    if (type === 'add') newVal = current + qty;
    else if (type === 'sub') newVal = current - qty;
    else newVal = qty;

    const preview = document.getElementById('newStockPreview');
    preview.textContent = newVal;
    preview.style.color = newVal < 0 ? '#d32f2f' : '#17ad37';
}

document.addEventListener('DOMContentLoaded', () => {
    const adjQty = document.getElementById('adjQty');
    if (adjQty) adjQty.addEventListener('input', updatePreview);
});

async function submitStockAdjust() {
    const id      = document.getElementById('adjItemId').value;
    const type    = document.getElementById('adjType').value;
    const current = parseInt(document.getElementById('adjCurrentStock').value) || 0;
    const qty     = parseInt(document.getElementById('adjQty').value) || 0;

    let adjustment;
    if (type === 'add') adjustment = qty;
    else if (type === 'sub') adjustment = -qty;
    else adjustment = qty - current; // set: adjustment = target - current

    const errEl = document.getElementById('stockError');
    errEl.style.display = 'none';

    if (adjustment === 0 && type !== 'set') {
        errEl.textContent = 'Jumlah penyesuaian tidak boleh 0.';
        errEl.style.display = 'block';
        return;
    }
    if (current + adjustment < 0) {
        errEl.textContent = `Stok tidak mencukupi. Stok saat ini: ${current}, pengurangan: ${Math.abs(adjustment)}.`;
        errEl.style.display = 'block';
        return;
    }

    try {
        const resp = await fetch(`${API_URL}/api/consumables/${id}/adjust-stock`, {
            method: 'PATCH',
            headers: { 'Content-Type': 'application/json', 'Authorization': `Bearer ${TOKEN}` },
            body: JSON.stringify({ adjustment })
        });
        const data = await resp.json();
        if (resp.ok) {
            showToast(data.message || 'Stok berhasil disesuaikan.');
            closeModal('stockModal');
            setTimeout(() => location.reload(), 900);
        } else {
            errEl.textContent = data.message || 'Gagal menyesuaikan stok.';
            errEl.style.display = 'block';
        }
    } catch {
        errEl.textContent = 'Tidak dapat terhubung ke server.';
        errEl.style.display = 'block';
    }
}

// ── Filter / Search ────────────────────────────────
document.addEventListener('DOMContentLoaded', function() {
    // Muat data rooms untuk dropdown lokasi
    loadMeta();

    const searchInput  = document.getElementById('searchInput');
    const catFilter    = document.getElementById('catFilter');
    const statusFilter = document.getElementById('statusFilter');
    const rows         = document.querySelectorAll('.item-row');
    const resultCount  = document.getElementById('resultCount');

    function applyFilters() {
        const q      = searchInput.value.toLowerCase();
        const cat    = catFilter.value;
        const status = statusFilter.value;
        let count = 0;

        rows.forEach(row => {
            const mQ = (row.dataset.search || '').includes(q);
            const mC = !cat    || row.dataset.cat    === cat;
            const mS = !status || row.dataset.status === status;
            const show = mQ && mC && mS;
            row.style.display = show ? '' : 'none';
            if (show) count++;
        });
        resultCount.textContent = count + ' item';
    }

    if (searchInput)  searchInput.addEventListener('input',   applyFilters);
    if (catFilter)    catFilter.addEventListener('change',    applyFilters);
    if (statusFilter) statusFilter.addEventListener('change', applyFilters);
});
</script>
@endsection
