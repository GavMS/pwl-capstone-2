@extends('dashboard.layout')
@section('title', 'Draf Pengadaan Barang')
@section('page_title', 'Draf Pengadaan')

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
/* ─── Action Button ──────────────────────────── */
.btn-add-draft {
    display: inline-flex;
    align-items: center;
    gap: .5rem;
    padding: .6rem 1.25rem;
    font-size: .8125rem;
    font-weight: 600;
    color: #fff;
    background: linear-gradient(310deg, #7928ca, #ff007f);
    border: none;
    border-radius: .5rem;
    box-shadow: 0 4px 7px -1px rgba(0,0,0,.11), 0 2px 4px -1px rgba(0,0,0,.07);
    cursor: pointer;
    text-decoration: none;
    white-space: nowrap;
    transition: box-shadow .15s ease, transform .15s ease;
}
.btn-add-draft:hover {
    box-shadow: 0 8px 25px -8px rgba(121,40,202,.7);
    transform: translateY(-1px);
    color: #fff;
}
/* ─── Alerts ──────────────────────────────────────── */
.alert-success {
    display: flex; align-items: center; gap: .75rem;
    padding: .875rem 1.25rem;
    background: #f0fdf4; border: 1px solid #bbf7d0;
    border-radius: .75rem; margin-bottom: 1rem;
    font-size: .875rem; font-weight: 600; color: #15803d;
}
.alert-error {
    display: flex; align-items: center; gap: .75rem;
    padding: .875rem 1.25rem;
    background: #fef2f2; border: 1px solid #fecaca;
    border-radius: .75rem; margin-bottom: 1rem;
    font-size: .875rem; font-weight: 600; color: #dc2626;
}
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
.table-card-title {
    font-size: 1rem;
    font-weight: 700;
    color: #344767;
    margin: 0;
}
/* ─── Filters ─────────────────────────────────────── */
.filter-bar {
    display: flex;
    align-items: center;
    gap: .75rem;
    flex-wrap: wrap;
}
.search-wrap {
    position: relative;
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
    width: 240px;
    transition: border-color .2s, box-shadow .2s;
}
.search-wrap input:focus {
    border-color: #7928ca;
    box-shadow: 0 0 0 2px rgba(121,40,202,.15);
}
.filter-select {
    padding: .5rem .875rem;
    font-size: .8125rem;
    border: 1px solid #d2d6da;
    border-radius: .5rem;
    color: #344767;
    outline: none;
    background: #fff;
    cursor: pointer;
    transition: border-color .2s, box-shadow .2s;
}
.filter-select:focus {
    border-color: #7928ca;
    box-shadow: 0 0 0 2px rgba(121,40,202,.15);
}
.result-count {
    font-size: .75rem;
    color: #adb5bd;
    font-weight: 600;
    white-space: nowrap;
}
/* ─── Table ───────────────────────────────────────── */
.drafts-table { width: 100%; border-collapse: collapse; }
.drafts-table thead th {
    padding: .75rem 1.25rem;
    font-size: .65rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .05em;
    color: #adb5bd;
    border-bottom: 1px solid #f0f2f5;
    white-space: nowrap;
    background: #fff;
}
.drafts-table tbody td {
    padding: .875rem 1.25rem;
    border-bottom: 1px solid #f0f2f5;
    vertical-align: middle;
}
.drafts-table tbody tr:last-child td { border-bottom: none; }
.drafts-table tbody tr:hover { background: #fafbfc; }

.draft-title { font-size: .875rem; font-weight: 600; color: #344767; margin: 0; }
.cell-text { font-size: .875rem; color: #7b809a; }
.cell-date { font-size: .8rem; color: #adb5bd; white-space: nowrap; }

/* ─── Status Badges ───────────────────────────────── */
.status-badge {
    display: inline-flex;
    align-items: center;
    gap: .35rem;
    padding: .3rem .85rem;
    border-radius: .45rem;
    font-size: .7rem;
    font-weight: 700;
    white-space: nowrap;
    border: 1.5px solid transparent;
}
.status-badge .sb-dot {
    width: 6px; height: 6px;
    border-radius: 50%;
    display: inline-block;
}
.status-draft { background:#fff7ed; color:#c2410c; border-color:#fed7aa; }
.status-draft .sb-dot { background:#ea580c; }
.status-submitted { background:#e8f5e9; color:#2e7d32; border-color:#c8e6c9; }
.status-submitted .sb-dot { background:#4caf50; }
.status-approved { background:#eff6ff; color:#1d4ed8; border-color:#bfdbfe; }
.status-approved .sb-dot { background:#2563eb; }
.status-rejected { background:#fef2f2; color:#dc2626; border-color:#fecaca; }
.status-rejected .sb-dot { background:#ef4444; }

/* ─── Icon Action Buttons ─────────────────────────── */
.action-group { display: flex; align-items: center; gap: .5rem; justify-content: center; }

.btn-icon {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 34px; height: 34px;
    border: none;
    border-radius: .5rem;
    cursor: pointer;
    font-size: .85rem;
    text-decoration: none;
    transition: transform .15s, box-shadow .15s, opacity .15s;
    position: relative;
}
.btn-icon:hover { transform: translateY(-2px); opacity: .9; }
.btn-icon:active { transform: translateY(0); }

.btn-icon-detail {
    background: linear-gradient(310deg, #17ad37, #98ec2c);
    color: #fff;
    box-shadow: 0 3px 5px -1px rgba(23,173,55,.35);
}
.btn-icon-detail:hover { box-shadow: 0 6px 15px -3px rgba(23,173,55,.5); color: #fff; }

.btn-icon-edit {
    background: linear-gradient(310deg, #2152ff, #21d4fd);
    color: #fff;
    box-shadow: 0 3px 5px -1px rgba(33,82,255,.35);
}
.btn-icon-edit:hover { box-shadow: 0 6px 15px -3px rgba(33,82,255,.5); color: #fff; }

.btn-icon-submit {
    background: linear-gradient(310deg, #7928ca, #ff007f);
    color: #fff;
    box-shadow: 0 3px 5px -1px rgba(121,40,202,.35);
}
.btn-icon-submit:hover { box-shadow: 0 6px 15px -3px rgba(121,40,202,.5); color: #fff; }

.btn-icon-delete {
    background: linear-gradient(310deg, #ea0606, #ff667c);
    color: #fff;
    box-shadow: 0 3px 5px -1px rgba(234,6,6,.35);
}
.btn-icon-delete:hover { box-shadow: 0 6px 15px -3px rgba(234,6,6,.5); color: #fff; }

/* Tooltip */
.btn-icon[data-tooltip] {
    position: relative;
}
.btn-icon[data-tooltip]::after {
    content: attr(data-tooltip);
    position: absolute;
    bottom: calc(100% + 6px);
    left: 50%;
    transform: translateX(-50%);
    background: #344767;
    color: #fff;
    font-size: .65rem;
    font-weight: 600;
    padding: .25rem .55rem;
    border-radius: .35rem;
    white-space: nowrap;
    opacity: 0;
    pointer-events: none;
    transition: opacity .15s;
    z-index: 999;
}
.btn-icon[data-tooltip]:hover::after {
    opacity: 1;
}

.table-footer {
    padding: .75rem 1.5rem;
    border-top: 1px solid #f0f2f5;
    font-size: .8rem; color: #adb5bd;
    display: flex; justify-content: space-between; align-items: center;
}

/* ─── Empty State ─────────────────────────────────── */
.empty-state {
    padding: 3rem 1rem;
    text-align: center;
    color: #adb5bd;
    font-size: .875rem;
}
</style>

{{-- ─── Hidden Forms for SweetAlert ─── --}}
<form id="actionForm" method="POST" style="display: none;">
    @csrf
    <input type="hidden" name="_method" id="actionMethod" value="POST">
</form>

<div class="flex flex-wrap -mx-3">

    {{-- ─── Header ────────────────────────────────── --}}
    <div class="w-full px-3 mb-4">
        <div class="page-header-card">
            <div style="display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:.75rem;">
                <div>
                    <h4 class="page-title">Draf Pengadaan Barang</h4>
                    <p class="page-subtitle">Kelola usulan barang inventaris dan BHP tahunan untuk laboratorium.</p>
                </div>
                <a href="{{ route('kalab.procurement.create') }}" class="btn-add-draft">
                    <i class="fas fa-plus"></i>
                    Buat Draf Baru
                </a>
            </div>
        </div>
    </div>

    {{-- ─── Alert Success ──────────────────────────── --}}
    @if(session('success'))
    <div class="w-full px-3 mb-3">
        <div class="alert-success">
            <i class="fas fa-check-circle"></i>
            {{ session('success') }}
        </div>
    </div>
    @endif

    {{-- ─── Alert Error ────────────────────────────── --}}
    @if($errors->any() || isset($error))
    <div class="w-full px-3 mb-3">
        <div class="alert-error">
            <i class="fas fa-exclamation-circle"></i>
            {{ $errors->any() ? $errors->first() : $error }}
        </div>
    </div>
    @endif

    {{-- ─── Table Card ─────────────────────────────── --}}
    <div class="w-full px-3">
        <div class="table-card">

            {{-- Card Header --}}
            <div class="table-card-header">
                <h6 class="table-card-title">Daftar Usulan Draf</h6>
                <div class="filter-bar">
                    <div class="search-wrap">
                        <i class="fas fa-search search-icon"></i>
                        <input type="text" id="searchInput" placeholder="Cari judul atau kode draf..." />
                    </div>
                    <select id="statusFilter" class="filter-select">
                        <option value="">Semua Status</option>
                        <option value="draft">Draft</option>
                        <option value="submitted">Diajukan</option>
                        <option value="approved">Disetujui</option>
                        <option value="rejected">Ditolak</option>
                    </select>
                    <span class="result-count" id="resultCount">{{ count($drafts) }} draf</span>
                </div>
            </div>

            {{-- Table --}}
            <div style="overflow-x: auto;">
                <table class="drafts-table" id="draftsTable">
                    <thead>
                        <tr>
                            <th style="width:50px; text-align:center;">No</th>
                            <th>Judul Usulan</th>
                            <th style="text-align:center;">Tahun</th>
                            <th style="text-align:center;">Item</th>
                            <th style="text-align:right;">Total Anggaran</th>
                            <th style="text-align:center;">Status</th>
                            <th style="text-align:center; width:120px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="draftsTableBody">
                        @forelse($drafts as $index => $d)
                        <tr class="draft-row" data-title="{{ strtolower($d['title']) }}" data-status="{{ $d['status'] ?? 'draft' }}">
                            <td style="text-align:center;">
                                <span class="cell-text" style="font-weight:600;">{{ $index + 1 }}</span>
                            </td>
                            <td>
                                <p class="draft-title draft-title-text">{{ $d['title'] }}</p>
                            </td>
                            <td style="text-align:center;">
                                <span class="cell-text">{{ $d['year'] }}</span>
                            </td>
                            <td style="text-align:center;">
                                <span class="cell-text" style="font-weight: 600;">{{ $d['item_count'] }} item</span>
                            </td>
                            <td style="text-align:right;">
                                @if(in_array($d['status'] ?? 'draft', ['approved', 'rejected']))
                                <span class="cell-text" style="font-weight: 600; color:#15803d;" title="Anggaran Disetujui">
                                    Rp {{ number_format($d['approved_total_price'] ?? $d['total_price'], 0, ',', '.') }}
                                </span>
                                <br><span style="font-size: .65rem; color:#adb5bd; text-transform:uppercase; font-weight:700;">Disetujui</span>
                                @else
                                <span class="cell-text" style="font-weight: 600; color:#344767;" title="Anggaran Diusulkan">
                                    Rp {{ number_format($d['total_price'], 0, ',', '.') }}
                                </span>
                                <br><span style="font-size: .65rem; color:#adb5bd; text-transform:uppercase; font-weight:700;">Diusulkan</span>
                                @endif
                            </td>
                            <td style="text-align:center;">
                                @if(($d['status'] ?? 'draft') === 'draft')
                                <span class="status-badge status-draft">
                                    <span class="sb-dot"></span> Draft
                                </span>
                                @elseif(($d['status'] ?? 'draft') === 'submitted')
                                <span class="status-badge status-submitted">
                                    <span class="sb-dot"></span> Diajukan
                                </span>
                                @elseif(($d['status'] ?? '') === 'approved')
                                <span class="status-badge status-approved">
                                    <span class="sb-dot"></span> Disetujui
                                </span>
                                @else
                                <span class="status-badge status-rejected">
                                    <span class="sb-dot"></span> Ditolak
                                </span>
                                @endif
                            </td>
                            <td>
                                <div class="action-group">
                                    {{-- Tombol Detail --}}
                                    <a href="{{ route('kalab.procurement.show', $d['id']) }}"
                                       class="btn-icon btn-icon-detail"
                                       data-tooltip="Detail">
                                        <i class="fas fa-eye"></i>
                                    </a>

                                    @if(($d['status'] ?? 'draft') === 'draft')
                                    {{-- Tombol Ajukan --}}
                                    <button type="button"
                                            class="btn-icon btn-icon-submit"
                                            data-tooltip="Ajukan"
                                            onclick="confirmSubmit('{{ addslashes($d['title']) }}', '{{ route('kalab.procurement.submit', $d['id']) }}')">
                                        <i class="fas fa-paper-plane"></i>
                                    </button>

                                    {{-- Tombol Edit --}}
                                    <a href="{{ route('kalab.procurement.edit', $d['id']) }}"
                                       class="btn-icon btn-icon-edit"
                                       data-tooltip="Edit">
                                        <i class="fas fa-pencil-alt"></i>
                                    </a>

                                    {{-- Tombol Hapus --}}
                                    <button type="button"
                                            class="btn-icon btn-icon-delete"
                                            data-tooltip="Hapus"
                                            onclick="confirmDelete('{{ addslashes($d['title']) }}', '{{ route('kalab.procurement.destroy', $d['id']) }}')">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr id="emptyRow">
                            <td colspan="8">
                                <div class="empty-state">
                                    <i class="fas fa-file-invoice" style="font-size:2.5rem; color:#d2d6da; display:block; margin-bottom:1rem;"></i>
                                    <p>Belum ada draf pengadaan barang.</p>
                                    <a href="{{ route('kalab.procurement.create') }}" class="btn-add-draft" style="margin-top:.75rem; font-size:.75rem; padding:.45rem 1rem;">
                                        <i class="fas fa-plus"></i> Buat Draf Pertama
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Footer --}}
            <div class="table-footer">
                <span>Total: <strong style="color:#344767;" id="footerCount">{{ count($drafts) }}</strong> draf pengadaan</span>
            </div>
        </div>
    </div>

</div>

<script>
// ── Search & Filter ───────────────────────────────────────────
const searchInput   = document.getElementById('searchInput');
const statusFilter  = document.getElementById('statusFilter');
const resultCount   = document.getElementById('resultCount');
const footerCount   = document.getElementById('footerCount');

function applyFilter() {
    const q      = searchInput.value.toLowerCase().trim();
    const status = statusFilter.value;
    const rows   = document.querySelectorAll('#draftsTableBody .draft-row');
    let visible  = 0;

    rows.forEach(row => {
        const title     = row.dataset.title || '';
        const rowStatus = row.dataset.status || '';
        const matchQ    = !q || title.includes(q);
        const matchS    = !status || rowStatus === status;
        const show      = matchQ && matchS;
        row.style.display = show ? '' : 'none';
        if (show) visible++;
    });

    // Tampilkan pesan "tidak ditemukan" jika 0 hasil
    let noResultRow = document.getElementById('noResultRow');
    if (visible === 0 && rows.length > 0) {
        if (!noResultRow) {
            noResultRow = document.createElement('tr');
            noResultRow.id = 'noResultRow';
            noResultRow.innerHTML = `<td colspan="8">
                <div class="empty-state">
                    <i class="fas fa-search" style="font-size:2rem; color:#d2d6da; display:block; margin-bottom:.75rem;"></i>
                    <p>Tidak ada draf yang cocok dengan pencarian Anda.</p>
                </div>
            </td>`;
            document.getElementById('draftsTableBody').appendChild(noResultRow);
        }
        noResultRow.style.display = '';
    } else if (noResultRow) {
        noResultRow.style.display = 'none';
    }

    resultCount.textContent = visible + ' draf';
    footerCount.textContent = visible;
}

searchInput.addEventListener('input', applyFilter);
statusFilter.addEventListener('change', applyFilter);

// ── SweetAlert Actions ─────────────────────────────────────────
function submitAction(url, method) {
    const form = document.getElementById('actionForm');
    form.action = url;
    document.getElementById('actionMethod').value = method;
    form.submit();
}

function confirmSubmit(title, url) {
    Swal.fire({
        title: 'Ajukan Draf?',
        html: `Anda akan mengajukan draf:<br><strong style="color:#344767;">${title}</strong><br><br><span style="font-size:0.85rem; color:#7b809a;">Setelah diajukan, draf ini akan dikunci dan dikirim ke Kaprodi untuk di-review.</span>`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: '<i class="fas fa-paper-plane mr-1"></i> Ya, Ajukan',
        cancelButtonText: '<i class="fas fa-times mr-1"></i> Batal',
        customClass: {
            popup: 'swal-custom-popup',
            confirmButton: 'swal-btn-confirm',
            cancelButton: 'swal-btn-cancel'
        },
        buttonsStyling: false
    }).then((result) => {
        if (result.isConfirmed) {
            submitAction(url, 'PATCH');
        }
    });
}

function confirmDelete(title, url) {
    Swal.fire({
        title: 'Hapus Draf?',
        html: `Anda akan menghapus draf:<br><strong style="color:#344767;">${title}</strong><br><br><span style="font-size:0.85rem; color:#ea0606;">Tindakan ini tidak dapat dibatalkan.</span>`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: '<i class="fas fa-trash-alt mr-1"></i> Ya, Hapus',
        cancelButtonText: '<i class="fas fa-times mr-1"></i> Batal',
        customClass: {
            popup: 'swal-custom-popup',
            confirmButton: 'swal-btn-danger',
            cancelButton: 'swal-btn-cancel'
        },
        buttonsStyling: false
    }).then((result) => {
        if (result.isConfirmed) {
            submitAction(url, 'DELETE');
        }
    });
}
</script>
@endsection
