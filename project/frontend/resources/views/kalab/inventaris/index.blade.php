@extends('dashboard.layout')
@section('title', 'Inventaris')
@section('page_title', 'Inventaris')

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
    min-width: 150px;
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
    margin-left: auto;
}
/* ─── Table ───────────────────────────────────────── */
.inventory-table { width: 100%; border-collapse: collapse; }
.inventory-table thead th {
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
.inventory-table tbody td {
    padding: .875rem 1.25rem;
    border-bottom: 1px solid #f0f2f5;
    vertical-align: middle;
}
.inventory-table tbody tr:last-child td { border-bottom: none; }
.inventory-table tbody tr:hover { background: #fafbfc; }

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
.cond-baik { background: #e8f5e9; color: #2e7d32; }
.cond-baik .cb-dot { background: #4caf50; }
.cond-maint { background: #fff8e1; color: #f57f17; }
.cond-maint .cb-dot { background: #ffb300; }
.cond-rusak-ringan { background: #fff3e0; color: #e65100; }
.cond-rusak-ringan .cb-dot { background: #f57c00; }
.cond-rusak-berat { background: #fbe9e7; color: #c62828; }
.cond-rusak-berat .cb-dot { background: #d32f2f; }

.qr-btn {
    background: none;
    border: none;
    color: #adb5bd;
    cursor: pointer;
    transition: color .2s;
}
.qr-btn:hover { color: #344767; }
</style>

<div class="flex flex-wrap -mx-3">
    <div class="w-full px-3">

        {{-- Page Header --}}
        <div class="page-header-card">
            <h4 class="page-title">Inventaris</h4>
            <p class="page-subtitle">Daftar barang inventaris (read-only)</p>
        </div>

        {{-- Error Banner --}}
        @if(isset($error) && $error)
        <div class="mb-4 p-4 rounded-lg bg-red-50 border border-red-200 text-red-600 text-sm font-semibold">
            <i class="fas fa-exclamation-triangle mr-2"></i> {{ $error }}
        </div>
        @endif

        {{-- Table Card --}}
        <div class="table-card">
            <div class="table-card-header">
                <div class="filter-bar">
                    <div class="search-wrap">
                        <i class="fas fa-search search-icon"></i>
                        <input type="text" id="searchInput" placeholder="Cari nama, kode, atau kategori..." />
                    </div>
                    
                    @php
                        $rooms = collect($assets)->pluck('room_code')->unique()->filter()->values();
                        $conditions = collect($assets)->pluck('condition_status')->unique()->filter()->values();
                    @endphp

                    <select id="roomFilter" class="filter-select">
                        <option value="">Semua Ruangan</option>
                        @foreach($rooms as $room)
                            <option value="{{ $room }}">{{ $room }}</option>
                        @endforeach
                    </select>

                    <select id="condFilter" class="filter-select">
                        <option value="">Semua Kondisi</option>
                        @foreach($conditions as $cond)
                            <option value="{{ $cond }}">{{ $cond }}</option>
                        @endforeach
                    </select>

                    <span class="result-count" id="resultCount">{{ count($assets) }} aset</span>
                </div>
            </div>

            <div style="overflow-x: auto;">
                <table class="inventory-table" id="inventoryTable">
                    <thead>
                        <tr>
                            <th>KODE</th>
                            <th>NAMA BARANG</th>
                            <th>KATEGORI</th>
                            <th>RUANGAN</th>
                            <th>KONDISI</th>
                            <th style="text-align:center;">TAHUN</th>
                            <th style="text-align:right;">HARGA</th>
                            <th style="text-align:center;">QR</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($assets as $a)
                        <tr class="item-row" 
                            data-search="{{ strtolower($a['name'] . ' ' . $a['code'] . ' ' . $a['category']) }}"
                            data-room="{{ $a['room_code'] }}"
                            data-cond="{{ $a['condition_status'] }}">
                            
                            <td><span class="item-code">{{ $a['code'] ?? '-' }}</span></td>
                            <td><p class="item-title">{{ $a['name'] }}</p></td>
                            <td><span class="cell-text">{{ $a['category'] ?? '-' }}</span></td>
                            <td><span class="cell-text font-semibold">{{ $a['room_code'] ?? '-' }}</span></td>
                            <td>
                                @php
                                    $condStr = strtolower($a['condition_status'] ?? 'baik');
                                    $condClass = 'cond-baik';
                                    if(str_contains($condStr, 'maintenance')) $condClass = 'cond-maint';
                                    elseif(str_contains($condStr, 'ringan')) $condClass = 'cond-rusak-ringan';
                                    elseif(str_contains($condStr, 'berat')) $condClass = 'cond-rusak-berat';
                                @endphp
                                <span class="cond-badge {{ $condClass }}">
                                    <span class="cb-dot"></span> {{ $a['condition_status'] ?? 'Baik' }}
                                </span>
                            </td>
                            <td style="text-align:center;"><span class="cell-text">{{ $a['year'] ?? '-' }}</span></td>
                            <td style="text-align:right;">
                                <span class="cell-text font-semibold color-slate-700">
                                    Rp {{ number_format($a['price'] ?? 0, 0, ',', '.') }}
                                </span>
                            </td>
                            <td style="text-align:center;">
                                <button class="qr-btn" title="Lihat QR Code"><i class="fas fa-qrcode"></i></button>
                            </td>
                        </tr>
                        @empty
                        <tr id="emptyRow">
                            <td colspan="8" style="text-align:center; padding: 3rem;">
                                <i class="fas fa-box-open" style="font-size:2.5rem; color:#d2d6da; margin-bottom:1rem;"></i>
                                <p style="color:#7b809a;">Tidak ada data inventaris ditemukan.</p>
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
    const roomFilter  = document.getElementById('roomFilter');
    const condFilter  = document.getElementById('condFilter');
    const rows        = document.querySelectorAll('.item-row');
    const resultCount = document.getElementById('resultCount');

    function applyFilters() {
        const query = searchInput.value.toLowerCase();
        const room  = roomFilter.value;
        const cond  = condFilter.value;
        
        let count = 0;

        rows.forEach(row => {
            const dataSearch = row.dataset.search || '';
            const dataRoom   = row.dataset.room || '';
            const dataCond   = row.dataset.cond || '';

            const matchQ = dataSearch.includes(query);
            const matchR = room === '' || dataRoom === room;
            const matchC = cond === '' || dataCond === cond;

            if (matchQ && matchR && matchC) {
                row.style.display = '';
                count++;
            } else {
                row.style.display = 'none';
            }
        });

        resultCount.textContent = count + ' aset';
    }

    if(searchInput) searchInput.addEventListener('input', applyFilters);
    if(roomFilter) roomFilter.addEventListener('change', applyFilters);
    if(condFilter) condFilter.addEventListener('change', applyFilters);
});
</script>
@endsection
