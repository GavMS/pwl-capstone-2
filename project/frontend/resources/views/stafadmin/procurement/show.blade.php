@extends('dashboard.layout')
@section('title', 'Detail Draf Pengadaan Disetujui')
@section('page_title', 'Detail Pengadaan')

@section('content')
<style>
/* ─── Card Styles ─────────────────────────────────── */
.detail-card {
    background: #fff;
    border-radius: 1rem;
    box-shadow: 0 20px 27px 0 rgba(0,0,0,.05);
    padding: 2rem;
    margin-bottom: 1.5rem;
}
.detail-title {
    font-size: 1.25rem;
    font-weight: 700;
    color: #344767;
    margin-bottom: .25rem;
}
.detail-subtitle {
    font-size: .875rem;
    color: #7b809a;
}
.section-divider {
    border: none;
    border-bottom: 1px solid #f0f2f5;
    margin: 1.5rem 0;
}
/* ─── Approved Banner ─────────────────────────────── */
.approved-banner {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1rem 1.25rem;
    background: linear-gradient(135deg, #eff6ff, #e0f2fe);
    border: 1px solid #bfdbfe;
    border-radius: .75rem;
    margin-bottom: 1.5rem;
}
.approved-banner-icon {
    width: 2.5rem; height: 2.5rem;
    border-radius: .5rem;
    background: linear-gradient(310deg, #2152ff, #21d4fd);
    display: flex; align-items: center; justify-content: center;
    color: #fff; font-size: 1.1rem;
    flex-shrink: 0;
}
.approved-banner-text { font-size: .875rem; color: #1d4ed8; }
.approved-banner-text strong { display: block; font-weight: 700; margin-bottom: .1rem; }
/* ─── Info Boxes ──────────────────────────────────── */
.info-box {
    background: #f8f9fa;
    border-radius: .75rem;
    padding: 1rem 1.25rem;
    display: flex; align-items: center; gap: 1rem;
}
.info-box-icon {
    width: 2.5rem; height: 2.5rem; border-radius: .5rem;
    background: linear-gradient(310deg, #2152ff, #21d4fd);
    display: flex; align-items: center; justify-content: center;
    color: #fff; font-size: 1rem; flex-shrink: 0;
}
.info-box-title {
    font-size: .65rem; font-weight: 700;
    text-transform: uppercase; color: #adb5bd;
    margin: 0 0 .15rem;
}
.info-box-value {
    font-size: .875rem; font-weight: 700;
    color: #344767; margin: 0;
}
/* ─── Status Badges ───────────────────────────────── */
.status-badge {
    display: inline-flex; align-items: center; gap: .35rem;
    padding: .35rem 1rem; border-radius: .45rem;
    font-size: .75rem; font-weight: 700;
    border: 1.5px solid transparent;
}
.status-badge .sb-dot {
    width: 7px; height: 7px;
    border-radius: 50%; display: inline-block;
}
.status-approved     { background:#eff6ff; color:#1d4ed8; border-color:#bfdbfe; }
.status-approved .sb-dot { background:#2563eb; }
/* ─── Type Badges ─────────────────────────────────── */
.type-badge {
    display: inline-block;
    padding: .2rem .65rem; font-size: .65rem; font-weight: 700;
    text-transform: uppercase; border-radius: .3rem; letter-spacing: .03em;
}
.type-inventaris { background: #e0f2fe; color: #0369a1; }
.type-bhp        { background: #fdf2f8; color: #be185d; }
/* ─── Items Table ─────────────────────────────────── */
.details-table { width: 100%; border-collapse: collapse; }
.details-table thead th {
    padding: .75rem 1rem;
    font-size: .65rem; font-weight: 700; text-transform: uppercase;
    color: #adb5bd; border-bottom: 2px solid #f0f2f5;
    text-align: left; white-space: nowrap;
    background: #fafbfc;
}
.details-table tbody td {
    padding: 1.1rem 1rem;
    border-bottom: 1px solid #f0f2f5;
    font-size: .875rem; color: #7b809a;
    vertical-align: middle;
}
.details-table tbody tr:last-child td { border-bottom: none; }
.details-table tbody tr:hover { background: #fafbfc; }
.col-no { text-align: center; font-weight: 700; color: #adb5bd; font-size: .8rem; }
.item-name { font-weight: 700; color: #344767; font-size: .9rem; margin-bottom: .15rem; }
.item-sub  { font-size: .72rem; color: #adb5bd; }
.price-cell { text-align: right; font-weight: 600; color: #344767; white-space: nowrap; }
.price-unit-label { font-size: .65rem; color: #adb5bd; font-weight: 500; display: block; text-align: right; }
.total-cell { text-align: right; white-space: nowrap; }
.total-amount { font-weight: 700; color: #2152ff; font-size: .9rem; }
.qty-badge {
    display: inline-flex; align-items: center; justify-content: center;
    min-width: 2.2rem; padding: .25rem .6rem;
    background: #f0f2f5; border-radius: .4rem;
    font-size: .8rem; font-weight: 700; color: #344767;
}
.btn-purchase-link {
    display: inline-flex; align-items: center; gap: .4rem;
    padding: .35rem .8rem; font-size: .75rem; font-weight: 600;
    color: #2152ff; background: #eff6ff;
    border: 1px solid #bfdbfe; border-radius: .4rem;
    text-decoration: none; transition: background .15s, transform .1s;
    white-space: nowrap;
}
.btn-purchase-link:hover { background: #dbeafe; transform: translateY(-1px); color: #1d4ed8; }
.btn-purchase-link i { font-size: .7rem; }
.no-link-text { font-size: .75rem; color: #d2d6da; font-style: italic; }
.replace-info { display: flex; align-items: flex-start; gap: .5rem; font-size: .8rem; }
.replace-icon { color: #f59e0b; margin-top: .1rem; flex-shrink: 0; }
.replace-name { font-weight: 700; color: #344767; }
.replace-code { font-size: .7rem; color: #adb5bd; }
/* ─── Grand Total Bar ─────────────────────────────── */
.grand-total-bar {
    background: linear-gradient(135deg, #eff6ff, #e0f2fe);
    border: 1px solid #bfdbfe;
    border-radius: .75rem;
    padding: 1rem 1.5rem;
    margin-top: 1.25rem;
    display: flex; justify-content: space-between; align-items: center;
    flex-wrap: wrap; gap: .75rem;
}
.grand-total-label {
    font-size: .75rem; font-weight: 700;
    text-transform: uppercase; color: #7b809a; margin-bottom: .2rem;
}
.grand-total-value { font-size: 1.35rem; font-weight: 800; color: #2152ff; }
/* ─── Buttons ─────────────────────────────────────── */
.btn-back {
    display: inline-flex; align-items: center; gap: .5rem;
    padding: .55rem 1.25rem; font-size: .8125rem; font-weight: 600; color: #7b809a;
    background: #f0f2f5; border: none; border-radius: .5rem; cursor: pointer;
    transition: background .15s; text-decoration: none;
}
.btn-back:hover { background: #e2e8f0; color: #7b809a; }
/* ─── Empty Items ─────────────────────────────────── */
.empty-items { text-align: center; padding: 2.5rem 1rem; color: #adb5bd; }
</style>

<div class="flex flex-wrap -mx-3">
    <div class="w-full px-3">

        {{-- ─── Approved Banner ─── --}}
        <div class="approved-banner">
            <div class="approved-banner-icon">
                <i class="fas fa-clipboard-check"></i>
            </div>
            <div class="approved-banner-text">
                <strong>Draf Pengadaan Telah Disetujui</strong>
                Draf ini telah melalui review dan mendapat persetujuan dari Ketua Program Studi. Data ini dapat digunakan sebagai acuan untuk proses penerimaan barang dan labeling inventaris.
            </div>
        </div>

        {{-- ─── Card 1: Header & Ringkasan ─── --}}
        <div class="detail-card">
            <div class="flex justify-between items-start flex-wrap gap-4 mb-4">
                <div>
                    <h3 class="detail-title">{{ $draft['title'] }}</h3>
                    <p class="detail-subtitle">
                        <i class="fas fa-calendar-alt mr-1" style="color:#adb5bd; font-size:.75rem;"></i>
                        Tahun Anggaran {{ $draft['year'] }}
                    </p>
                </div>
                <div>
                    <span class="status-badge status-approved">
                        <span class="sb-dot"></span> Telah Disetujui Kaprodi
                    </span>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4">
                <div class="info-box">
                    <div class="info-box-icon"><i class="fas fa-boxes"></i></div>
                    <div>
                        <p class="info-box-title">Jumlah Item</p>
                        <p class="info-box-value">{{ count($items) }} barang</p>
                    </div>
                </div>
                <div class="info-box">
                    <div class="info-box-icon"><i class="fas fa-wallet"></i></div>
                    <div>
                        <p class="info-box-title">Total Anggaran</p>
                        <p class="info-box-value" style="color:#2152ff;">
                            Rp {{ number_format(collect($items)->sum(fn($i) => ($i['price'] ?? 0) * ($i['quantity'] ?? 1)), 0, ',', '.') }}
                        </p>
                    </div>
                </div>
                <div class="info-box">
                    <div class="info-box-icon"><i class="fas fa-user"></i></div>
                    <div>
                        <p class="info-box-title">Dibuat Oleh</p>
                        <p class="info-box-value">{{ $draft['creator_name'] ?? 'Kepala Lab' }}</p>
                    </div>
                </div>
                <div class="info-box">
                    <div class="info-box-icon"><i class="fas fa-clock"></i></div>
                    <div>
                        <p class="info-box-title">Tanggal Dibuat</p>
                        <p class="info-box-value">
                            {{ isset($draft['created_at']) ? \Carbon\Carbon::parse($draft['created_at'])->format('d M Y') : '-' }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        {{-- ─── Card 2: Rincian Barang ─── --}}
        <div class="detail-card">
            <h4 class="detail-title text-base" style="font-size:1rem; margin-bottom:1.25rem;">
                <i class="fas fa-list-ul mr-2" style="color:#2152ff;"></i>
                Rincian Barang yang Disetujui
            </h4>

            <div style="overflow-x: auto;">
                <table class="details-table">
                    <thead>
                        <tr>
                            <th style="width:46px; text-align:center;">No</th>
                            <th style="width:100px;">Tipe</th>
                            <th style="min-width:200px;">Nama Barang</th>
                            <th style="width:160px; text-align:right;">Harga Satuan</th>
                            <th style="width:80px; text-align:center;">Qty</th>
                            <th style="width:180px; text-align:right;">Total Harga</th>
                            <th style="width:180px; text-align:center;">Link Pembelian</th>
                            <th style="width:220px;">Menggantikan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($items as $index => $item)
                        <tr>
                            {{-- No --}}
                            <td class="col-no">{{ $index + 1 }}</td>

                            {{-- Tipe --}}
                            <td>
                                @if(($item['item_type'] ?? '') === 'inventaris')
                                    <span class="type-badge type-inventaris">Inventaris</span>
                                @else
                                    <span class="type-badge type-bhp">BHP</span>
                                @endif
                            </td>

                            {{-- Nama Barang --}}
                            <td>
                                <p class="item-name">{{ $item['name'] }}</p>
                                @if(!empty($item['notes']))
                                    <p class="item-sub">
                                        <i class="fas fa-sticky-note mr-1"></i>{{ $item['notes'] }}
                                    </p>
                                @endif
                            </td>

                            {{-- Harga Satuan --}}
                            <td>
                                <span class="price-cell">
                                    Rp {{ number_format($item['price'] ?? 0, 0, ',', '.') }}
                                </span>
                                <span class="price-unit-label">per satuan</span>
                            </td>

                            {{-- Qty --}}
                            <td style="text-align:center;">
                                <span class="qty-badge">{{ $item['quantity'] ?? 1 }}</span>
                            </td>

                            {{-- Total Harga --}}
                            <td class="total-cell">
                                <span class="total-amount">
                                    Rp {{ number_format(($item['price'] ?? 0) * ($item['quantity'] ?? 1), 0, ',', '.') }}
                                </span>
                            </td>

                            {{-- Link Pembelian --}}
                            <td style="text-align:center;">
                                @if(!empty($item['purchase_link']))
                                    <a href="{{ $item['purchase_link'] }}"
                                       target="_blank" rel="noopener noreferrer"
                                       class="btn-purchase-link">
                                        <i class="fas fa-shopping-cart"></i>
                                        Lihat Toko
                                        <i class="fas fa-external-link-alt" style="font-size:.6rem;"></i>
                                    </a>
                                @else
                                    <span class="no-link-text">Tidak ada link</span>
                                @endif
                            </td>

                            {{-- Menggantikan Barang Lama --}}
                            <td>
                                @if(!empty($item['replaced_asset_id']))
                                    <div class="replace-info">
                                        <i class="fas fa-exchange-alt replace-icon"></i>
                                        <div>
                                            <div class="replace-name">{{ $item['replaced_asset_name'] ?? 'Aset Lama' }}</div>
                                            @if(!empty($item['replaced_asset_code']))
                                            <div class="replace-code">{{ $item['replaced_asset_code'] }}</div>
                                            @endif
                                        </div>
                                    </div>
                                @else
                                    <span style="font-size:.75rem; color:#d2d6da; font-style:italic;">— Tidak Ada —</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8">
                                <div class="empty-items">
                                    <i class="fas fa-box-open" style="font-size:2.5rem; color:#d2d6da; display:block; margin-bottom:.75rem;"></i>
                                    <p>Belum ada rincian barang untuk draf pengadaan ini.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Grand Total Bar --}}
            @if(count($items) > 0)
            <div class="grand-total-bar">
                <div>
                    <p class="grand-total-label">
                        <i class="fas fa-calculator mr-1"></i>
                        Total Keseluruhan Anggaran ({{ count($items) }} item)
                    </p>
                    <p class="grand-total-value">
                        Rp {{ number_format(collect($items)->sum(fn($i) => ($i['price'] ?? 0) * ($i['quantity'] ?? 1)), 0, ',', '.') }}
                    </p>
                </div>
                <div style="text-align:right;">
                    <p style="font-size:.7rem; color:#adb5bd; font-weight:600; text-transform:uppercase; margin-bottom:.25rem;">
                        Rata-rata per item
                    </p>
                    <p style="font-size:.9rem; font-weight:700; color:#344767;">
                        @php
                            $total = collect($items)->sum(fn($i) => ($i['price'] ?? 0) * ($i['quantity'] ?? 1));
                            $avg   = count($items) > 0 ? $total / count($items) : 0;
                        @endphp
                        Rp {{ number_format($avg, 0, ',', '.') }}
                    </p>
                </div>
            </div>
            @endif

            <div class="section-divider"></div>

            <div class="flex justify-between items-center flex-wrap gap-3">
                <a href="{{ route('stafadmin.procurement.index') }}" class="btn-back">
                    <i class="fas fa-arrow-left"></i> Kembali ke Daftar
                </a>
            </div>
        </div>

    </div>
</div>
@endsection
