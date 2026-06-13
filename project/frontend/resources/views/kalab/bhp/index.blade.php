@extends('dashboard.layout')
@section('title', 'Bahan Habis Pakai')
@section('page_title', 'BHP')

@section('content')
<style>
/* ─── Page Styles ─────────────────────────────────── */
.page-header-card {
    background: #fff;
    border-radius: 1rem;
    box-shadow: 0 20px 27px 0 rgba(0,0,0,.05);
    padding: 1.5rem;
    margin-bottom: 1.5rem;
}
.page-title {
    font-size: 1.25rem;
    font-weight: 700;
    color: #344767;
    margin: 0 0 .25rem;
}
.page-subtitle {
    font-size: .875rem;
    color: #7b809a;
    margin: 0;
}
/* ─── Alerts ──────────────────────────────────────── */
.alert-success-box {
    display: flex; align-items: center; gap: .75rem;
    padding: .875rem 1.25rem;
    background: #f0fdf4; border: 1px solid #bbf7d0;
    border-radius: .75rem; margin-bottom: 1rem;
    font-size: .875rem; font-weight: 600; color: #15803d;
}
.alert-error-box {
    display: flex; align-items: center; gap: .75rem;
    padding: .875rem 1.25rem;
    background: #fef2f2; border: 1px solid #fecaca;
    border-radius: .75rem; margin-bottom: 1rem;
    font-size: .875rem; font-weight: 600; color: #dc2626;
}
/* ─── Warning Banner ──────────────────────────────── */
.alert-warning {
    display: flex;
    align-items: center;
    gap: .75rem;
    padding: 1rem 1.25rem;
    background: #fff8e1;
    border: 1px solid #ffecb3;
    border-radius: .75rem;
    margin-bottom: 1.5rem;
    font-size: .875rem;
    color: #b97c00;
}
.alert-warning i { color: #f57f17; font-size: 1.1rem; }
/* ─── Table Card ──────────────────────────────────── */
.table-card {
    background: #fff;
    border-radius: 1rem;
    box-shadow: 0 20px 27px 0 rgba(0,0,0,.05);
    overflow: hidden;
}
.table-card-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 1rem 1.5rem;
    border-bottom: 1px solid #f0f2f5;
    flex-wrap: wrap;
    gap: .75rem;
}
/* ─── Filters ─────────────────────────────────────── */
.filter-bar {
    display: flex;
    align-items: center;
    gap: .75rem;
    flex-wrap: wrap;
    width: 100%;
}
.search-wrap {
    position: relative;
    flex: 1;
    min-width: 200px;
}
.search-wrap .search-icon {
    position: absolute;
    left: .75rem; top: 50%;
    transform: translateY(-50%);
    color: #adb5bd; font-size: .75rem;
    pointer-events: none;
}
.search-wrap input {
    padding: .5rem .875rem .5rem 2.2rem;
    font-size: .8125rem;
    border: 1px solid #d2d6da;
    border-radius: .5rem;
    color: #344767;
    outline: none;
    width: 100%;
    transition: border-color .2s, box-shadow .2s;
}
.search-wrap input:focus {
    border-color: #be185d;
    box-shadow: 0 0 0 2px rgba(190,24,93,.15);
}
.result-count {
    font-size: .75rem;
    color: #adb5bd;
    font-weight: 600;
    white-space: nowrap;
    margin-left: auto;
}
/* ─── Table ───────────────────────────────────────── */
.bhp-table { width: 100%; border-collapse: collapse; }
.bhp-table thead th {
    padding: .75rem 1.25rem;
    font-size: .65rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .05em;
    color: #adb5bd;
    border-bottom: 1px solid #f0f2f5;
    white-space: nowrap;
    background: #fff;
    text-align: left;
}
.bhp-table tbody td {
    padding: .875rem 1.25rem;
    border-bottom: 1px solid #f0f2f5;
    vertical-align: middle;
}
.bhp-table tbody tr:last-child td { border-bottom: none; }
.bhp-table tbody tr:hover { background: #fafbfc; }

.item-title { font-size: .875rem; font-weight: 600; color: #344767; margin: 0; }
.item-code { font-size: .8125rem; font-weight: 600; color: #7b809a; font-family: monospace; }
.cell-text { font-size: .875rem; color: #7b809a; }

/* ─── Status Badges ───────────────────────────────── */
.cond-badge {
    display: inline-flex;
    align-items: center;
    gap: .35rem;
    padding: .3rem .85rem;
    border-radius: 2rem;
    font-size: .7rem;
    font-weight: 700;
    white-space: nowrap;
}
.cond-badge .cb-dot {
    width: 6px; height: 6px;
    border-radius: 50%;
    display: inline-block;
}
.cond-aman { background: #e8f5e9; color: #2e7d32; }
.cond-aman .cb-dot { background: #4caf50; }
.cond-kritis { background: #fbe9e7; color: #c62828; }
.cond-kritis .cb-dot { background: #d32f2f; }

/* ─── Action Button ───────────────────────────────── */
.btn-edit-bhp {
    display: inline-flex; align-items: center; justify-content: center;
    width: 34px; height: 34px;
    border: none; border-radius: .5rem; cursor: pointer;
    font-size: .85rem; text-decoration: none;
    transition: transform .15s, box-shadow .15s, opacity .15s;
    background: linear-gradient(310deg, #be185d, #f472b6);
    color: #fff;
    box-shadow: 0 3px 5px -1px rgba(190,24,93,.35);
}
.btn-edit-bhp:hover { transform: translateY(-2px); opacity: .9; box-shadow: 0 6px 15px -3px rgba(190,24,93,.5); }

/* ─── Modal ───────────────────────────────────────── */
.modal-overlay {
    display: none;
    position: fixed; inset: 0; z-index: 9999;
    background: rgba(15, 23, 42, .55);
    backdrop-filter: blur(4px);
    align-items: center; justify-content: center;
}
.modal-overlay.open { display: flex; }
.modal-box {
    background: #fff;
    border-radius: 1.25rem;
    box-shadow: 0 25px 60px rgba(0,0,0,.22);
    padding: 2rem;
    width: 440px; max-width: 95vw;
    animation: modalIn .2s ease;
    position: relative;
}
@keyframes modalIn {
    from { opacity: 0; transform: translateY(-18px) scale(.97); }
    to   { opacity: 1; transform: translateY(0) scale(1); }
}
.modal-header {
    display: flex; align-items: center; gap: .75rem;
    margin-bottom: 1.5rem;
}
.modal-icon {
    width: 2.5rem; height: 2.5rem; border-radius: .6rem;
    background: linear-gradient(310deg, #be185d, #f472b6);
    display: flex; align-items: center; justify-content: center;
    color: #fff; font-size: 1rem; flex-shrink: 0;
}
.modal-title { font-size: 1rem; font-weight: 800; color: #344767; margin: 0; }
.modal-subtitle { font-size: .8125rem; color: #7b809a; margin: .15rem 0 0; }
.modal-close {
    position: absolute; top: 1rem; right: 1rem;
    width: 28px; height: 28px;
    border: none; border-radius: .4rem;
    background: #f0f2f5; color: #7b809a;
    font-size: .85rem; cursor: pointer;
    display: flex; align-items: center; justify-content: center;
    transition: background .15s;
}
.modal-close:hover { background: #e2e8f0; color: #344767; }
.form-group { margin-bottom: 1.25rem; }
.form-label {
    display: block; font-size: .75rem; font-weight: 700;
    text-transform: uppercase; color: #7b809a;
    margin-bottom: .4rem;
}
.form-control {
    width: 100%; padding: .6rem .875rem;
    font-size: .875rem; border: 1.5px solid #d2d6da;
    border-radius: .5rem; color: #344767; outline: none;
    transition: border-color .2s, box-shadow .2s;
}
.form-control:focus {
    border-color: #be185d;
    box-shadow: 0 0 0 3px rgba(190,24,93,.12);
}
.form-hint { font-size: .72rem; color: #adb5bd; margin-top: .3rem; }
.modal-footer {
    display: flex; align-items: center; justify-content: flex-end;
    gap: .75rem; margin-top: 1.5rem;
    padding-top: 1.25rem; border-top: 1px solid #f0f2f5;
}
.btn-cancel {
    padding: .5rem 1.1rem; font-size: .8125rem; font-weight: 600;
    color: #7b809a; background: #f0f2f5;
    border: none; border-radius: .5rem; cursor: pointer;
    transition: background .15s;
}
.btn-cancel:hover { background: #e2e8f0; }
.btn-submit-modal {
    padding: .5rem 1.25rem; font-size: .8125rem; font-weight: 700;
    color: #fff;
    background: linear-gradient(310deg, #be185d, #f472b6);
    border: none; border-radius: .5rem; cursor: pointer;
    box-shadow: 0 4px 10px -2px rgba(190,24,93,.4);
    transition: opacity .15s, transform .1s;
}
.btn-submit-modal:hover { opacity: .88; transform: translateY(-1px); }
</style>

<div class="flex flex-wrap -mx-3">
    <div class="w-full px-3">

        {{-- Page Header --}}
        <div class="page-header-card">
            <h4 class="page-title"><i class="fas fa-flask mr-2" style="color:#be185d;"></i>BHP / Bahan Habis Pakai</h4>
            <p class="page-subtitle">Daftar seluruh bahan habis pakai laboratorium. Kepala Lab dapat mengatur lokasi penyimpanan dan stok minimum tiap item.</p>
        </div>

        {{-- Alerts --}}
        @if(session('success'))
        <div class="alert-success-box">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
        </div>
        @endif
        @if($errors->any())
        <div class="alert-error-box">
            <i class="fas fa-exclamation-circle"></i> {{ $errors->first() }}
        </div>
        @endif

        {{-- Error Banner --}}
        @if(isset($error) && $error)
        <div class="mb-4 p-4 rounded-lg bg-red-50 border border-red-200 text-red-600 text-sm font-semibold">
            <i class="fas fa-exclamation-triangle mr-2"></i> {{ $error }}
        </div>
        @endif

        {{-- Minimum Stock Warning Banner --}}
        @php
            $criticalItems = collect($consumables)->filter(function($c) {
                return ($c['stock'] ?? 0) <= ($c['min_stock'] ?? 0);
            });
        @endphp

        @if($criticalItems->count() > 0)
        <div class="alert-warning">
            <i class="fas fa-exclamation-triangle"></i>
            <div>
                <strong>{{ $criticalItems->count() }} item</strong> di bawah stok minimum:
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
                        <input type="text" id="searchInput" placeholder="Cari nama atau kategori BHP..." />
                    </div>
                    <span class="result-count" id="resultCount">{{ count($consumables) }} item</span>
                </div>
            </div>

            <div style="overflow-x: auto;">
                <table class="bhp-table" id="bhpTable">
                    <thead>
                        <tr>
                            <th>KODE</th>
                            <th>NAMA</th>
                            <th>KATEGORI</th>
                            <th>SATUAN</th>
                            <th style="text-align:center;">STOK</th>
                            <th style="text-align:center;">MIN</th>
                            <th>STATUS</th>
                            <th>LOKASI</th>
                            <th style="text-align:right;">HARGA</th>
                            <th style="text-align:center; width:70px;">AKSI</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($consumables as $c)
                        <tr class="item-row" data-search="{{ strtolower($c['name'] . ' ' . $c['code'] . ' ' . $c['category']) }}">
                            <td><span class="item-code">{{ $c['code'] ?? '-' }}</span></td>
                            <td><p class="item-title">{{ $c['name'] }}</p></td>
                            <td><span class="cell-text">{{ $c['category'] ?? '-' }}</span></td>
                            <td><span class="cell-text">{{ $c['unit'] ?? '-' }}</span></td>
                            <td style="text-align:center;">
                                <span class="cell-text font-bold text-slate-800" style="font-size: .95rem;">
                                    {{ $c['stock'] ?? 0 }}
                                </span>
                            </td>
                            <td style="text-align:center;"><span class="cell-text">{{ $c['min_stock'] ?? 0 }}</span></td>
                            <td>
                                @php
                                    $isCritical = ($c['stock'] ?? 0) <= ($c['min_stock'] ?? 0);
                                @endphp
                                @if($isCritical)
                                <span class="cond-badge cond-kritis">
                                    <span class="cb-dot"></span> Kritis
                                </span>
                                @else
                                <span class="cond-badge cond-aman">
                                    <span class="cb-dot"></span> Aman
                                </span>
                                @endif
                            </td>
                            <td><span class="cell-text font-semibold">{{ $c['location'] ?? ($c['room_code'] ?? '-') }}</span></td>
                            <td style="text-align:right;">
                                <span class="cell-text">
                                    Rp {{ number_format($c['price'] ?? 0, 0, ',', '.') }}
                                </span>
                            </td>
                            <td style="text-align:center;">
                                <button type="button" class="btn-edit-bhp"
                                    title="Edit Lokasi & Stok Minimum"
                                    onclick="openEditModal(
                                        '{{ $c['id'] }}',
                                        '{{ addslashes($c['name']) }}',
                                        '{{ addslashes($c['location'] ?? ($c['room_code'] ?? '')) }}',
                                        '{{ $c['min_stock'] ?? 0 }}'
                                    )">
                                    <i class="fas fa-pen"></i>
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr id="emptyRow">
                            <td colspan="10" style="text-align:center; padding: 3rem;">
                                <i class="fas fa-flask" style="font-size:2.5rem; color:#d2d6da; margin-bottom:1rem;"></i>
                                <p style="color:#7b809a;">Tidak ada data BHP ditemukan.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- ─── Modal Edit BHP ──────────────────────────────────────── --}}
<div class="modal-overlay" id="editModalOverlay" onclick="if(event.target===this) closeModal()">
    <div class="modal-box" role="dialog" aria-modal="true" aria-labelledby="modalTitle">
        <button class="modal-close" onclick="closeModal()" title="Tutup"><i class="fas fa-times"></i></button>

        <div class="modal-header">
            <div class="modal-icon"><i class="fas fa-map-marker-alt"></i></div>
            <div>
                <p class="modal-title" id="modalTitle">Edit Lokasi & Stok Minimum</p>
                <p class="modal-subtitle" id="modalItemName">—</p>
            </div>
        </div>

        <form id="editBhpForm" method="POST" action="">
            @csrf
            @method('PATCH')

            <div class="form-group">
                <label class="form-label" for="inputLocation">
                    <i class="fas fa-map-marker-alt mr-1"></i> Lokasi Penyimpanan
                </label>
                <input type="text" id="inputLocation" name="location" class="form-control"
                    placeholder="Contoh: Lemari A, Rak 2, Lab Kimia..." autocomplete="off">
                <p class="form-hint">Tuliskan lokasi penyimpanan BHP di laboratorium.</p>
            </div>

            <div class="form-group">
                <label class="form-label" for="inputMinStock">
                    <i class="fas fa-exclamation-triangle mr-1"></i> Stok Minimum
                </label>
                <input type="number" id="inputMinStock" name="min_stock" class="form-control"
                    min="0" placeholder="Contoh: 10">
                <p class="form-hint">Sistem akan memberi peringatan jika stok di bawah nilai ini.</p>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn-cancel" onclick="closeModal()">Batal</button>
                <button type="submit" class="btn-submit-modal">
                    <i class="fas fa-save mr-1"></i> Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>

<script>
// ─── Search Filter ────────────────────────────────────────────
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    const rows        = document.querySelectorAll('.item-row');
    const resultCount = document.getElementById('resultCount');

    function applyFilters() {
        const query = searchInput.value.toLowerCase();
        let count = 0;

        rows.forEach(row => {
            const dataSearch = row.dataset.search || '';

            if (dataSearch.includes(query)) {
                row.style.display = '';
                count++;
            } else {
                row.style.display = 'none';
            }
        });

        resultCount.textContent = count + ' item';
    }

    if(searchInput) searchInput.addEventListener('input', applyFilters);
});

// ─── Modal Logic ──────────────────────────────────────────────
function openEditModal(id, name, location, minStock) {
    document.getElementById('modalItemName').textContent = name;
    document.getElementById('inputLocation').value       = location;
    document.getElementById('inputMinStock').value       = minStock;

    // Set form action ke route yang benar
    document.getElementById('editBhpForm').action = '/kalab/bhp/' + id;

    document.getElementById('editModalOverlay').classList.add('open');
    document.body.style.overflow = 'hidden';

    // Focus ke field pertama
    setTimeout(() => document.getElementById('inputLocation').focus(), 100);
}

function closeModal() {
    document.getElementById('editModalOverlay').classList.remove('open');
    document.body.style.overflow = '';
}

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') closeModal();
});
</script>
@endsection
