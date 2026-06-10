@extends('dashboard.layout')
@section('title', 'Detail Draf Pengadaan')
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
/* ─── Info Boxes ──────────────────────────────────── */
.info-box {
    background: #f8f9fa;
    border-radius: .75rem;
    padding: 1rem 1.25rem;
    display: flex;
    align-items: center;
    gap: 1rem;
}
.info-box-icon {
    width: 2.5rem; height: 2.5rem; border-radius: .5rem;
    background: linear-gradient(310deg, #7928ca, #ff007f);
    display: flex; align-items: center; justify-content: center;
    color: #fff; font-size: 1rem;
    flex-shrink: 0;
}
.info-box-title {
    font-size: .65rem;
    font-weight: 700;
    text-transform: uppercase;
    color: #adb5bd;
    margin: 0 0 .15rem;
}
.info-box-value {
    font-size: .875rem;
    font-weight: 700;
    color: #344767;
    margin: 0;
}
/* ─── Status Badges ───────────────────────────────── */
.status-badge {
    display: inline-flex;
    align-items: center;
    gap: .35rem;
    padding: .35rem 1rem;
    border-radius: .45rem;
    font-size: .75rem;
    font-weight: 700;
    border: 1.5px solid transparent;
}
.status-badge .sb-dot {
    width: 7px; height: 7px;
    border-radius: 50%;
    display: inline-block;
}
.status-draft     { background:#fff7ed; color:#c2410c; border-color:#fed7aa; }
.status-draft .sb-dot { background:#ea580c; }
.status-submitted { background:#e8f5e9; color:#2e7d32; border-color:#c8e6c9; }
.status-submitted .sb-dot { background:#4caf50; }
.status-approved  { background:#eff6ff; color:#1d4ed8; border-color:#bfdbfe; }
.status-approved .sb-dot { background:#2563eb; }
.status-rejected  { background:#fef2f2; color:#dc2626; border-color:#fecaca; }
.status-rejected .sb-dot { background:#ef4444; }

/* ─── Type Badges ─────────────────────────────────── */
.type-badge {
    display: inline-block;
    padding: .2rem .65rem;
    font-size: .65rem;
    font-weight: 700;
    text-transform: uppercase;
    border-radius: .3rem;
    letter-spacing: .03em;
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
    font-size: .875rem;
    color: #7b809a;
    vertical-align: middle;
}
.details-table tbody tr:last-child td { border-bottom: none; }
.details-table tbody tr:hover { background: #fafbfc; }

/* No column */
.col-no {
    text-align: center;
    font-weight: 700;
    color: #adb5bd;
    font-size: .8rem;
}
/* Item name */
.item-name {
    font-weight: 700;
    color: #344767;
    font-size: .9rem;
    margin-bottom: .15rem;
}
.item-sub {
    font-size: .72rem;
    color: #adb5bd;
}
/* Price cell */
.price-cell {
    text-align: right;
    font-weight: 600;
    color: #344767;
    white-space: nowrap;
}
.price-unit-label {
    font-size: .65rem;
    color: #adb5bd;
    font-weight: 500;
    display: block;
    text-align: right;
}
/* Total cell */
.total-cell {
    text-align: right;
    white-space: nowrap;
}
.total-amount {
    font-weight: 700;
    color: #7928ca;
    font-size: .9rem;
}
/* Qty cell */
.qty-badge {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 2.2rem;
    padding: .25rem .6rem;
    background: #f0f2f5;
    border-radius: .4rem;
    font-size: .8rem;
    font-weight: 700;
    color: #344767;
}
/* Link button */
.btn-purchase-link {
    display: inline-flex;
    align-items: center;
    gap: .4rem;
    padding: .35rem .8rem;
    font-size: .75rem;
    font-weight: 600;
    color: #2152ff;
    background: #eff6ff;
    border: 1px solid #bfdbfe;
    border-radius: .4rem;
    text-decoration: none;
    transition: background .15s, transform .1s;
    white-space: nowrap;
}
.btn-purchase-link:hover {
    background: #dbeafe;
    transform: translateY(-1px);
    color: #1d4ed8;
}
.btn-purchase-link i { font-size: .7rem; }
.no-link-text {
    font-size: .75rem;
    color: #d2d6da;
    font-style: italic;
}

/* Replace asset info */
.replace-info {
    display: flex;
    align-items: flex-start;
    gap: .5rem;
    font-size: .8rem;
}
.replace-icon {
    color: #f59e0b;
    margin-top: .1rem;
    flex-shrink: 0;
}
.replace-name { font-weight: 700; color: #344767; }
.replace-code { font-size: .7rem; color: #adb5bd; }

/* ─── Grand Total Row ─────────────────────────────── */
.grand-total-bar {
    background: linear-gradient(135deg, #f5f3ff, #fdf2f8);
    border: 1px solid #e9d5ff;
    border-radius: .75rem;
    padding: 1rem 1.5rem;
    margin-top: 1.25rem;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: .75rem;
}
.grand-total-label {
    font-size: .75rem;
    font-weight: 700;
    text-transform: uppercase;
    color: #7b809a;
    margin-bottom: .2rem;
}
.grand-total-value {
    font-size: 1.35rem;
    font-weight: 800;
    color: #7928ca;
}

/* ─── Buttons ─────────────────────────────────────── */
.btn-back {
    display: inline-flex; align-items: center; gap: .5rem;
    padding: .55rem 1.25rem; font-size: .8125rem; font-weight: 600; color: #7b809a;
    background: #f0f2f5; border: none; border-radius: .5rem; cursor: pointer;
    transition: background .15s; text-decoration: none;
}
.btn-back:hover { background: #e2e8f0; color: #7b809a; }

.btn-edit-action {
    display: inline-flex; align-items: center; gap: .5rem;
    padding: .55rem 1.25rem; font-size: .8125rem; font-weight: 600; color: #fff;
    background: linear-gradient(310deg, #2152ff, #21d4fd);
    border: none; border-radius: .5rem; cursor: pointer;
    box-shadow: 0 3px 5px -1px rgba(33,82,255,.4); text-decoration: none;
    transition: transform .15s, box-shadow .15s;
}
.btn-edit-action:hover { transform: translateY(-1px); box-shadow: 0 6px 15px -3px rgba(33,82,255,.5); color: #fff; }

/* ─── Empty Items ─────────────────────────────────── */
.empty-items {
    text-align: center;
    padding: 2.5rem 1rem;
    color: #adb5bd;
}
</style>

<div class="flex flex-wrap -mx-3">
    <div class="w-full px-3">

        {{-- ─── Card 1: Header & Ringkasan ─── --}}
        <div class="detail-card">
            <div class="flex justify-between items-start flex-wrap gap-4 mb-4">
                <div>
                    <h3 class="detail-title">{{ $draft['title'] }}</h3>
                    <p class="detail-subtitle">
                        <i class="fas fa-hashtag mr-1" style="color:#adb5bd; font-size:.75rem;"></i>
                        {{ $draft['code'] ?? 'Kode belum tersedia' }}
                        &nbsp;·&nbsp;
                        <i class="fas fa-calendar-alt mr-1" style="color:#adb5bd; font-size:.75rem;"></i>
                        Tahun Anggaran {{ $draft['year'] }}
                    </p>
                </div>
                <div>
                    @if(($draft['status'] ?? 'draft') === 'draft')
                    <span class="status-badge status-draft">
                        <span class="sb-dot"></span> Draft (Belum Diajukan)
                    </span>
                    @elseif(($draft['status'] ?? 'draft') === 'submitted')
                    <span class="status-badge status-submitted">
                        <span class="sb-dot"></span> Sudah Diajukan (Menunggu Review)
                    </span>
                    @elseif(($draft['status'] ?? 'draft') === 'approved')
                    <span class="status-badge status-approved">
                        <span class="sb-dot"></span> Disetujui Kaprodi
                    </span>
                    @else
                    <span class="status-badge status-rejected">
                        <span class="sb-dot"></span> Ditolak Kaprodi
                    </span>
                    @endif
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
                        <p class="info-box-value" style="color:#7928ca;">
                            Rp {{ number_format(collect($items)->sum(fn($i) => ($i['price'] ?? 0) * ($i['quantity'] ?? 1)), 0, ',', '.') }}
                        </p>
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
                <i class="fas fa-list-ul mr-2" style="color:#7928ca;"></i>
                Rincian Barang yang Diusulkan
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
                            <th style="width:150px;">Menggantikan</th>
                            <th style="width:160px; text-align:center;">Status & Catatan Kaprodi</th>
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
                                       target="_blank"
                                       rel="noopener noreferrer"
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

                            {{-- Status Review Kaprodi --}}
                            <td style="text-align:center;">
                                @if(($draft['status'] ?? 'draft') !== 'draft')
                                    @php $rs = $item['review_status'] ?? 'pending'; @endphp
                                    @if($rs === 'approved')
                                        <span class="status-badge status-approved" style="padding:.2rem .6rem;"><span class="sb-dot"></span> Disetujui</span>
                                    @elseif($rs === 'rejected')
                                        <span class="status-badge status-rejected" style="padding:.2rem .6rem;"><span class="sb-dot"></span> Ditolak</span>
                                    @else
                                        <span class="status-badge" style="background:#f1f5f9; color:#64748b; padding:.2rem .6rem;"><span class="sb-dot" style="background:#94a3b8;"></span> Menunggu</span>
                                    @endif
                                    @if(!empty($item['review_notes']))
                                        <p style="font-size:.7rem; color:#f59e0b; margin-top:.3rem; line-height:1.2; font-weight:600;"><i class="fas fa-comment-dots"></i> {{ $item['review_notes'] }}</p>
                                    @endif
                                @else
                                    <span style="font-size:.75rem; color:#d2d6da; font-style:italic;">—</span>
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
                        Total Keseluruhan Usulan ({{ count($items) }} item)
                    </p>
                    <p class="grand-total-value">
                        Rp {{ number_format(collect($items)->sum(fn($i) => ($i['price'] ?? 0) * ($i['quantity'] ?? 1)), 0, ',', '.') }}
                    </p>
                </div>

                @php
                    $isReviewed = in_array($draft['status'] ?? 'draft', ['approved', 'rejected']);
                    $totalApproved = collect($items)->filter(fn($i) => ($i['review_status'] ?? 'pending') !== 'rejected')->sum(fn($i) => ($i['price'] ?? 0) * ($i['quantity'] ?? 1));
                    $totalRejected = collect($items)->filter(fn($i) => ($i['review_status'] ?? 'pending') === 'rejected')->sum(fn($i) => ($i['price'] ?? 0) * ($i['quantity'] ?? 1));
                @endphp

                @if($isReviewed)
                <div style="text-align:right;">
                    @if($totalRejected > 0)
                    <p style="font-size:.7rem; color:#adb5bd; font-weight:600; text-transform:uppercase; margin-bottom:.15rem;">Ditolak Kaprodi</p>
                    <p style="font-size:.85rem; font-weight:700; color:#dc2626; margin:0 0 .35rem;">- Rp {{ number_format($totalRejected, 0, ',', '.') }}</p>
                    @endif
                    <p style="font-size:.7rem; color:#15803d; font-weight:600; text-transform:uppercase; margin-bottom:.15rem;">Total Disetujui</p>
                    <p style="font-size:1.1rem; font-weight:800; color:#15803d; margin:0;">Rp {{ number_format($totalApproved, 0, ',', '.') }}</p>
                </div>
                @else
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
                @endif
            </div>
            @endif

            <div class="section-divider"></div>

            <div class="flex justify-between items-center flex-wrap gap-3">
                <a href="{{ route('kalab.procurement.index') }}" class="btn-back">
                    <i class="fas fa-arrow-left"></i> Kembali ke Daftar
                </a>

                @if(($draft['status'] ?? 'draft') === 'draft')
                <a href="{{ route('kalab.procurement.edit', $draft['id']) }}" class="btn-edit-action">
                    <i class="fas fa-pencil-alt"></i> Edit Draf Ini
                </a>
                @endif
            </div>
        </div>

    </div>
</div>
@endsection
