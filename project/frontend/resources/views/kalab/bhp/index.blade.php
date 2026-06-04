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
    border-color: #7928ca;
    box-shadow: 0 0 0 2px rgba(121,40,202,.15);
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
</style>

<div class="flex flex-wrap -mx-3">
    <div class="w-full px-3">

        {{-- Page Header --}}
        <div class="page-header-card">
            <h4 class="page-title"><i class="fas fa-flask mr-2" style="color:#be185d;"></i>BHP / Bahan Habis Pakai</h4>
            <p class="page-subtitle">Daftar seluruh bahan habis pakai laboratorium beserta informasi stok. Data bersifat read-only.</p>
        </div>

        {{-- Error Banner --}}
        @if(isset($error) && $error)
        <div class="mb-4 p-4 rounded-lg bg-red-50 border border-red-200 text-red-600 text-sm font-semibold">
            <i class="fas fa-exclamation-triangle mr-2"></i> {{ $error }}
        </div>
        @endif

        {{-- Minimum Stock Warning Banner --}}
        @php
            $criticalItems = collect($consumables)->filter(function($c) {
                return ($c['stock'] ?? 0) < ($c['min_stock'] ?? 0);
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
                                    $isCritical = ($c['stock'] ?? 0) < ($c['min_stock'] ?? 0);
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
                        </tr>
                        @empty
                        <tr id="emptyRow">
                            <td colspan="9" style="text-align:center; padding: 3rem;">
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

<script>
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
</script>
@endsection
