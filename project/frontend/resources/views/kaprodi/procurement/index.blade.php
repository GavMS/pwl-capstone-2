@extends('dashboard.layout')
@section('title', 'Review Pengadaan')
@section('page_title', 'Review Pengadaan')

@section('content')
<style>
.btn-add { display:inline-flex;align-items:center;gap:.5rem;padding:.6rem 1.25rem;font-size:.8125rem;font-weight:600;color:#fff;background:linear-gradient(310deg,#7928ca,#ff007f);border:none;border-radius:.5rem;box-shadow:0 4px 7px -1px rgba(0,0,0,.11),0 2px 4px -1px rgba(0,0,0,.07);cursor:pointer;text-decoration:none;white-space:nowrap;transition:box-shadow .15s,transform .15s; }
.btn-add:hover { box-shadow:0 8px 25px -8px rgba(121,40,202,.7);transform:translateY(-1px);color:#fff; }

.alert-success { display:flex;align-items:center;gap:.75rem;padding:.875rem 1.25rem;background:#f0fdf4;border:1px solid #bbf7d0;border-radius:.75rem;margin-bottom:1rem;font-size:.875rem;font-weight:600;color:#15803d; }
.alert-error   { display:flex;align-items:center;gap:.75rem;padding:.875rem 1.25rem;background:#fef2f2;border:1px solid #fecaca;border-radius:.75rem;margin-bottom:1rem;font-size:.875rem;font-weight:600;color:#dc2626; }

.info-banner { display:flex;align-items:center;gap:.75rem;padding:.875rem 1.25rem;background:#fdf2f8;border:1px solid #f9a8d4;border-radius:.75rem;margin-bottom:1.25rem;font-size:.8125rem;color:#be185d;font-weight:600; }

.filter-row { display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:.75rem;margin-bottom:1rem; }
.filter-left { display:flex;align-items:center;gap:.625rem;flex-wrap:wrap; }
.search-wrap { position:relative; }
.search-wrap .search-icon { position:absolute;left:.875rem;top:50%;transform:translateY(-50%);color:#94a3b8;font-size:.8125rem;pointer-events:none;line-height:1; }
.search-wrap input { padding:.55rem .875rem .55rem 2.35rem;font-size:.8125rem;border:1px solid #d2d6da;border-radius:.5rem;color:#344767;outline:none;width:260px;background:#fff;transition:border-color .2s,box-shadow .2s; }
.search-wrap input:focus { border-color:#7928ca;box-shadow:0 0 0 2px rgba(121,40,202,.15); }
.count-label { font-size:.8125rem;color:#adb5bd;white-space:nowrap; }

.table-card { background:#fff;border-radius:1rem;border:1px solid #eef0f5;box-shadow:0 1px 3px rgba(0,0,0,.04),0 8px 20px -12px rgba(0,0,0,.08);overflow:hidden; }
.users-table { width:100%;border-collapse:collapse; }
.users-table thead th { padding:.75rem 1.25rem;font-size:.65rem;font-weight:700;text-transform:uppercase;letter-spacing:.05em;color:#adb5bd;border-bottom:1px solid #f0f2f5;white-space:nowrap;background:#fff;text-align:left; }
.users-table tbody td { padding:.875rem 1.25rem;border-bottom:1px solid #f0f2f5;vertical-align:middle; }
.users-table tbody tr:last-child td { border-bottom:none; }
.users-table tbody tr:hover { background:#fafbfc; }

.cell-title { font-size:.875rem;font-weight:600;color:#344767;margin:0; }
.cell-text  { font-size:.875rem;color:#7b809a; }

.status-badge { display:inline-flex;align-items:center;gap:.3rem;padding:.25rem .7rem;border-radius:9999px;font-size:.7rem;font-weight:600; }
.status-dot   { width:6px;height:6px;border-radius:50%;flex-shrink:0; }
.sb-submitted { background:#f0fdf4;color:#15803d; }
.sb-submitted .status-dot { background:#16a34a; }

.action-group { display:flex;align-items:center;gap:.375rem;justify-content:center; }
.btn-icon { display:inline-flex;align-items:center;justify-content:center;width:2rem;height:2rem;border-radius:.4rem;border:1px solid #e2e8f0;background:#fff;cursor:pointer;text-decoration:none;transition:background .15s,border-color .15s,transform .15s;color:#64748b; }
.btn-icon:hover { transform:translateY(-1px); }
.btn-icon-view:hover { background:#f0fdf4;border-color:#bbf7d0;color:#15803d; }
.btn-icon i { font-size:.7rem; }

.empty-state { text-align:center;padding:3rem 1.5rem; }
.empty-state i { font-size:2.5rem;color:#d2d6da;display:block;margin-bottom:1rem; }
.empty-state p { font-size:.875rem;color:#adb5bd;margin:0; }
</style>

{{-- Page Header --}}
<div style="display:flex;align-items:flex-start;justify-content:space-between;flex-wrap:wrap;gap:.75rem;margin-bottom:1.25rem;">
    <div>
        <h4 style="font-size:1.25rem;font-weight:700;color:#344767;margin:0 0 .25rem;">
            <i class="fas fa-tasks mr-2" style="color:#be185d;"></i>Review Pengadaan Masuk
        </h4>
        <p style="font-size:.875rem;color:#7b809a;margin:0;">Draf yang diajukan Kepala Laboratorium dan menunggu keputusan Anda</p>
    </div>
</div>

@if(session('success'))
<div class="alert-success"><i class="fas fa-check-circle"></i>{{ session('success') }}</div>
@endif
@if($errors->any() || isset($error))
<div class="alert-error"><i class="fas fa-exclamation-circle"></i>{{ $errors->any() ? $errors->first() : $error }}</div>
@endif

<div class="info-banner">
    <i class="fas fa-info-circle" style="font-size:1rem;flex-shrink:0;"></i>
    <span>Sebagai Ketua Program Studi, Anda berwenang menyetujui atau menolak draf pengadaan dari Kepala Laboratorium.</span>
</div>

<div class="filter-row">
    <div class="filter-left">
        <div class="search-wrap">
            <i class="fas fa-search search-icon"></i>
            <input type="text" id="searchInput" placeholder="Cari judul draf..." />
        </div>
    </div>
    <span class="count-label" id="countLabel">{{ count($drafts) }} dari {{ count($drafts) }} draf</span>
</div>

<div class="table-card">
    <div style="overflow-x:auto;">
        <table class="users-table" id="draftsTable">
            <thead>
                <tr>
                    <th>Judul Usulan</th>
                    <th style="text-align:center;">Tahun</th>
                    <th style="text-align:center;">Item</th>
                    <th style="text-align:right;">Total Anggaran</th>
                    <th style="text-align:center;">Status</th>
                    <th style="text-align:center;width:80px;">Aksi</th>
                </tr>
            </thead>
            <tbody id="draftsTableBody">
                @forelse($drafts as $d)
                <tr class="draft-row" data-title="{{ strtolower($d['title']) }}">
                    <td><p class="cell-title draft-title-text">{{ $d['title'] }}</p></td>
                    <td style="text-align:center;"><span class="cell-text">{{ $d['year'] }}</span></td>
                    <td style="text-align:center;"><span class="cell-text" style="font-weight:600;">{{ $d['item_count'] }} item</span></td>
                    <td style="text-align:right;"><span class="cell-text" style="font-weight:600;color:#344767;">Rp {{ number_format($d['total_price'],0,',','.') }}</span></td>
                    <td style="text-align:center;">
                        <span class="status-badge sb-submitted">
                            <span class="status-dot"></span> Menunggu Review
                        </span>
                    </td>
                    <td>
                        <div class="action-group">
                            <a href="{{ route('kaprodi.procurement.show', $d['id']) }}" class="btn-icon btn-icon-view" title="Detail & Review">
                                <i class="fas fa-search"></i>
                            </a>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7">
                        <div class="empty-state">
                            <i class="fas fa-clipboard-check"></i>
                            <p>Tidak ada draf yang menunggu review saat ini.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<script>
const total = {{ count($drafts) }};
document.getElementById('searchInput').addEventListener('input', function() {
    const q = this.value.toLowerCase();
    let visible = 0;
    document.querySelectorAll('#draftsTableBody .draft-row').forEach(row => {
        const match = !q || (row.dataset.title || '').includes(q);
        row.style.display = match ? '' : 'none';
        if (match) visible++;
    });
    document.getElementById('countLabel').textContent = visible + ' dari ' + total + ' draf';
});
</script>
@endsection
