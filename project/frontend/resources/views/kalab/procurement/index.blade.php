@extends('dashboard.layout')
@section('title', 'Draf Pengadaan Barang')

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
/* ─── Search ──────────────────────────────────────── */
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
    width: 220px;
    transition: border-color .2s, box-shadow .2s;
}
.search-wrap input:focus {
    border-color: #7928ca;
    box-shadow: 0 0 0 2px rgba(121,40,202,.15);
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

/* ─── Action Buttons ──────────────────────────────── */
.action-group { display: flex; align-items: center; gap: .4rem; }
.btn-view, .btn-edit, .btn-delete {
    display: inline-flex; align-items: center; gap: .3rem;
    padding: .35rem .85rem;
    font-size: .75rem; font-weight: 600;
    color: #fff; border: none; border-radius: .45rem;
    cursor: pointer; text-decoration: none;
    transition: transform .15s, box-shadow .15s;
}
.btn-view   { background: linear-gradient(310deg, #17ad37, #98ec2c); box-shadow: 0 3px 5px -1px rgba(23,173,55,.4); }
.btn-edit   { background: linear-gradient(310deg, #2152ff, #21d4fd); box-shadow: 0 3px 5px -1px rgba(33,82,255,.4); }
.btn-delete { background: linear-gradient(310deg, #ea0606, #ff667c); box-shadow: 0 3px 5px -1px rgba(234,6,6,.4); }
.btn-view:hover, .btn-edit:hover, .btn-delete:hover { transform: translateY(-1px); box-shadow: 0 5px 10px -2px rgba(0,0,0,.3); color:#fff; }

.table-footer {
    padding: .75rem 1.5rem;
    border-top: 1px solid #f0f2f5;
    font-size: .8rem; color: #adb5bd;
}
</style>

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
                <div class="search-wrap">
                    <i class="fas fa-search search-icon"></i>
                    <input type="text" id="searchInput" placeholder="Cari draf pengadaan..." />
                </div>
            </div>

            {{-- Table --}}
            <div style="overflow-x: auto;">
                <table class="drafts-table" id="draftsTable">
                    <thead>
                        <tr>
                            <th style="width:50px; text-align:center;">No</th>
                            <th>Judul Usulan</th>
                            <th style="text-align:center;">Tahun Anggaran</th>
                            <th style="text-align:center;">Jumlah Item</th>
                            <th style="text-align:right;">Total Anggaran</th>
                            <th style="text-align:center;">Status</th>
                            <th style="text-align:center;">Pembuat</th>
                            <th style="text-align:center; width:220px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="draftsTableBody">
                        @forelse($drafts as $index => $d)
                        <tr class="draft-row">
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
                                <span class="cell-text" style="font-weight: 600; color:#344767;">
                                    Rp {{ number_format($d['total_price'], 0, ',', '.') }}
                                </span>
                            </td>
                            <td style="text-align:center;">
                                @if(($d['status'] ?? 'draft') === 'draft')
                                <span class="status-badge status-draft">
                                    <span class="sb-dot"></span> Draft
                                </span>
                                @else
                                <span class="status-badge status-submitted">
                                    <span class="sb-dot"></span> Diajukan
                                </span>
                                @endif
                            </td>
                            <td style="text-align:center;">
                                <span class="cell-text">{{ $d['creator_name'] ?? 'Kepala Lab' }}</span>
                            </td>
                            <td style="text-align:center;">
                                <div class="action-group" style="justify-content:center;">
                                    <a href="{{ route('kalab.procurement.show', $d['id']) }}" class="btn-view">
                                        <i class="fas fa-eye"></i> Detail
                                    </a>
                                    
                                    @if(($d['status'] ?? 'draft') === 'draft')
                                    <a href="{{ route('kalab.procurement.edit', $d['id']) }}" class="btn-edit">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>
                                    <form action="{{ route('kalab.procurement.destroy', $d['id']) }}" method="POST" style="margin:0;" onsubmit="return confirm('Apakah Anda yakin ingin menghapus draf ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-delete">
                                            <i class="fas fa-trash-alt"></i> Hapus
                                        </button>
                                    </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8">
                                <div class="empty-state">
                                    <i class="fas fa-file-invoice" style="font-size:2.5rem; color:#d2d6da; display:block; margin-bottom:1rem;"></i>
                                    <p>Belum ada draf pengadaan barang.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Footer --}}
            <div class="table-footer">
                Total: <strong style="color:#344767;">{{ count($drafts) }}</strong> draf pengadaan
            </div>
        </div>
    </div>

</div>

<script>
// ── Search ───────────────────────────────────────────
document.getElementById('searchInput').addEventListener('input', function () {
    const q = this.value.toLowerCase();
    document.querySelectorAll('#draftsTableBody .draft-row').forEach(row => {
        const title = (row.querySelector('.draft-title-text')?.textContent ?? '').toLowerCase();
        row.style.display = title.includes(q) ? '' : 'none';
    });
});
</script>
@endsection
