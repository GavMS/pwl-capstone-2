@extends('dashboard.layout')
@section('title', 'Draf Pengadaan Disetujui')
@section('page_title', 'Draf Disetujui')

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
/* ─── Info Banner ─────────────────────────────────── */
.info-banner {
    display: flex;
    align-items: center;
    gap: .75rem;
    padding: .875rem 1.25rem;
    background: #eff6ff;
    border: 1px solid #bfdbfe;
    border-radius: .75rem;
    margin-bottom: 1.25rem;
    font-size: .8125rem;
    color: #1d4ed8;
    font-weight: 600;
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
.search-wrap { position: relative; }
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
    color: #344767; outline: none;
    width: 240px;
    transition: border-color .2s, box-shadow .2s;
}
.search-wrap input:focus {
    border-color: #2152ff;
    box-shadow: 0 0 0 2px rgba(33,82,255,.15);
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
    font-size: .65rem; font-weight: 700;
    text-transform: uppercase; letter-spacing: .05em;
    color: #adb5bd; border-bottom: 1px solid #f0f2f5;
    white-space: nowrap; background: #fff;
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

/* ─── Status Badge ────────────────────────────────── */
.status-badge {
    display: inline-flex; align-items: center; gap: .35rem;
    padding: .3rem .85rem; border-radius: .45rem;
    font-size: .7rem; font-weight: 700;
    white-space: nowrap; border: 1.5px solid transparent;
}
.status-badge .sb-dot {
    width: 6px; height: 6px;
    border-radius: 50%; display: inline-block;
}
.status-approved { background:#eff6ff; color:#1d4ed8; border-color:#bfdbfe; }
.status-approved .sb-dot { background:#2563eb; }

/* ─── Icon Action Button ──────────────────────────── */
.action-group { display: flex; align-items: center; gap: .5rem; justify-content: center; }
.btn-icon {
    display: inline-flex; align-items: center; justify-content: center;
    width: 34px; height: 34px;
    border: none; border-radius: .5rem; cursor: pointer;
    font-size: .85rem; text-decoration: none;
    transition: transform .15s, box-shadow .15s, opacity .15s;
    position: relative;
}
.btn-icon:hover { transform: translateY(-2px); opacity: .9; }
.btn-icon-detail {
    background: linear-gradient(310deg, #17ad37, #98ec2c);
    color: #fff;
    box-shadow: 0 3px 5px -1px rgba(23,173,55,.35);
}
.btn-icon-detail:hover { box-shadow: 0 6px 15px -3px rgba(23,173,55,.5); color: #fff; }

/* Tooltip */
.btn-icon[data-tooltip]::after {
    content: attr(data-tooltip);
    position: absolute; bottom: calc(100% + 6px); left: 50%;
    transform: translateX(-50%);
    background: #344767; color: #fff;
    font-size: .65rem; font-weight: 600;
    padding: .25rem .55rem; border-radius: .35rem;
    white-space: nowrap; opacity: 0;
    pointer-events: none; transition: opacity .15s; z-index: 999;
}
.btn-icon[data-tooltip]:hover::after { opacity: 1; }

.table-footer {
    padding: .75rem 1.5rem; border-top: 1px solid #f0f2f5;
    font-size: .8rem; color: #adb5bd;
    display: flex; justify-content: space-between; align-items: center;
}
/* ─── Empty State ─────────────────────────────────── */
.empty-state {
    padding: 3rem 1rem; text-align: center;
    color: #adb5bd; font-size: .875rem;
}
</style>

<div class="flex flex-wrap -mx-3">

    {{-- ─── Header ────────────────────────────────── --}}
    <div class="w-full px-3 mb-4">
        <div class="page-header-card">
            <div>
                <h4 class="page-title">
                    <i class="fas fa-clipboard-check mr-2" style="color:#2563eb;"></i>
                    Draf Pengadaan Disetujui
                </h4>
                <p class="page-subtitle">Daftar draf pengadaan barang yang telah disetujui oleh Ketua Program Studi dan siap untuk diproses.</p>
            </div>
        </div>
    </div>

    {{-- ─── Info Banner ──────────────────────────── --}}
    <div class="w-full px-3 mb-2">
        <div class="info-banner">
            <i class="fas fa-info-circle" style="font-size:1rem; flex-shrink:0;"></i>
            <span>Sebagai Staf Administrasi, Anda dapat melihat draf pengadaan yang telah disetujui Kaprodi. Gunakan data ini untuk melakukan proses penerimaan dan labeling barang.</span>
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
                <h6 class="table-card-title">Daftar Draf yang Telah Disetujui</h6>
                <div class="filter-bar">
                    <div class="search-wrap">
                        <i class="fas fa-search search-icon"></i>
                        <input type="text" id="searchInput" placeholder="Cari judul draf..." />
                    </div>
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
                            <th style="text-align:center;">Jumlah Item</th>
                            <th style="text-align:right;">Anggaran Disetujui</th>
                            <th style="text-align:center;">Status</th>
                            <th style="text-align:center; width:80px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="draftsTableBody">
                        @forelse($drafts as $index => $d)
                        <tr class="draft-row" data-title="{{ strtolower($d['title']) }}">
                            <td style="text-align:center;">
                                <span class="cell-text" style="font-weight:600;">{{ $index + 1 }}</span>
                            </td>
                            <td>
                                <p class="draft-title">{{ $d['title'] }}</p>
                            </td>
                            <td style="text-align:center;">
                                <span class="cell-text">{{ $d['year'] }}</span>
                            </td>
                            <td style="text-align:center;">
                                <span class="cell-text" style="font-weight:600;">{{ $d['item_count'] }} item</span>
                            </td>
                            <td style="text-align:right;">
                                <span class="cell-text" style="font-weight:600; color:#15803d;">
                                    Rp {{ number_format($d['approved_total_price'] ?? $d['total_price'], 0, ',', '.') }}
                                </span>
                            </td>
                            <td style="text-align:center;">
                                <span class="status-badge status-approved">
                                    <span class="sb-dot"></span> Disetujui
                                </span>
                            </td>
                            <td>
                                <div class="action-group">
                                    <a href="{{ route('stafadmin.procurement.show', $d['id']) }}"
                                       class="btn-icon btn-icon-detail"
                                       data-tooltip="Lihat Detail">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr id="emptyRow">
                            <td colspan="7">
                                <div class="empty-state">
                                    <i class="fas fa-clipboard-list" style="font-size:2.5rem; color:#d2d6da; display:block; margin-bottom:1rem;"></i>
                                    <p style="font-weight:600; color:#7b809a; margin-bottom:.25rem;">Belum ada draf yang disetujui.</p>
                                    <p style="font-size:.8rem;">Draf akan muncul di sini setelah disetujui oleh Ketua Program Studi.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Footer --}}
            <div class="table-footer">
                <span>Total: <strong style="color:#344767;" id="footerCount">{{ count($drafts) }}</strong> draf disetujui</span>
            </div>
        </div>
    </div>

</div>

<script>
const searchInput  = document.getElementById('searchInput');
const resultCount  = document.getElementById('resultCount');
const footerCount  = document.getElementById('footerCount');

function applyFilter() {
    const q    = searchInput.value.toLowerCase().trim();
    const rows = document.querySelectorAll('#draftsTableBody .draft-row');
    let visible = 0;

    rows.forEach(row => {
        const title  = row.dataset.title || '';
        const matchQ = !q || title.includes(q);
        row.style.display = matchQ ? '' : 'none';
        if (matchQ) visible++;
    });

    // Pesan "tidak ditemukan"
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
</script>
@endsection
