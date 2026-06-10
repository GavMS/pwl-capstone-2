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
/* ─── Label Numbering ────────────────────────────── */
.label-input-wrap {
    display: flex; align-items: center; gap: .4rem;
}
.label-input {
    width: 130px; padding: .35rem .6rem;
    font-size: .78rem; font-weight: 600;
    border: 1.5px solid #d2d6da;
    border-radius: .4rem; color: #344767;
    outline: none; font-family: 'Courier New', monospace;
    transition: border-color .2s, box-shadow .2s;
    letter-spacing: .03em;
}
.label-input:focus {
    border-color: #2152ff;
    box-shadow: 0 0 0 2px rgba(33,82,255,.15);
}
.label-input.has-value {
    border-color: #17ad37;
    background: #f0fdf4;
    color: #15803d;
}
.btn-generate {
    display: inline-flex; align-items: center; gap: .3rem;
    padding: .3rem .6rem; font-size: .68rem; font-weight: 700;
    color: #fff;
    background: linear-gradient(310deg, #2152ff, #21d4fd);
    border: none; border-radius: .35rem; cursor: pointer;
    white-space: nowrap;
    transition: opacity .15s, transform .1s;
    flex-shrink: 0;
}
.btn-generate:hover { opacity: .88; transform: translateY(-1px); }
.label-na {
    font-size: .75rem; color: #d2d6da;
    font-style: italic;
}
/* ─── Btn Lihat QR ───────────────────────────────── */
.btn-qr {
    display: inline-flex; align-items: center; gap: .3rem;
    padding: .3rem .6rem; font-size: .68rem; font-weight: 700;
    color: #fff;
    background: linear-gradient(310deg, #7928ca, #ff0080);
    border: none; border-radius: .35rem; cursor: pointer;
    white-space: nowrap;
    transition: opacity .15s, transform .1s;
    flex-shrink: 0;
}
.btn-qr:hover { opacity: .88; transform: translateY(-1px); }
.btn-qr:disabled { opacity: .4; cursor: not-allowed; transform: none; }
/* ─── QR Modal ───────────────────────────────────── */
.qr-modal-overlay {
    display: none;
    position: fixed; inset: 0; z-index: 9999;
    background: rgba(15, 23, 42, .55);
    backdrop-filter: blur(4px);
    align-items: center; justify-content: center;
}
.qr-modal-overlay.open { display: flex; }
.qr-modal {
    background: #fff;
    border-radius: 1.25rem;
    box-shadow: 0 25px 60px rgba(0,0,0,.22);
    padding: 2rem;
    width: 340px; max-width: 95vw;
    animation: modalSlideIn .2s ease;
    text-align: center;
    position: relative;
}
@keyframes modalSlideIn {
    from { opacity: 0; transform: translateY(-18px) scale(.97); }
    to   { opacity: 1; transform: translateY(0) scale(1); }
}
.qr-modal-close {
    position: absolute; top: .9rem; right: .9rem;
    width: 28px; height: 28px;
    border: none; border-radius: .4rem;
    background: #f0f2f5; color: #7b809a;
    font-size: .85rem; cursor: pointer;
    display: flex; align-items: center; justify-content: center;
    transition: background .15s;
}
.qr-modal-close:hover { background: #e2e8f0; color: #344767; }
.qr-modal-title {
    font-size: 1rem; font-weight: 800;
    color: #344767; margin-bottom: .25rem;
}
.qr-modal-subtitle {
    font-size: .75rem; color: #adb5bd;
    margin-bottom: 1.25rem;
}
.qr-canvas-wrap {
    display: flex; justify-content: center; align-items: center;
    background: #f8f9fa;
    border-radius: .75rem;
    padding: 1.25rem;
    margin-bottom: 1rem;
}
.qr-label-code {
    display: inline-block;
    font-family: 'Courier New', monospace;
    font-size: .9rem; font-weight: 700;
    color: #344767;
    background: #f0f2f5;
    border-radius: .4rem;
    padding: .35rem .85rem;
    margin-bottom: .25rem;
    letter-spacing: .05em;
}
.qr-empty-hint {
    font-size: .8rem; color: #f59e0b; font-weight: 600;
    margin-top: .5rem;
}
/* ─── Btn Cetak Label ───────────────────────────────── */
.btn-print-label {
    display: inline-flex; align-items: center; gap: .45rem;
    padding: .5rem 1.1rem; font-size: .8rem; font-weight: 700;
    color: #fff;
    background: linear-gradient(310deg, #17ad37, #98ec2c);
    border: none; border-radius: .45rem; cursor: pointer;
    width: 100%; justify-content: center;
    margin-top: .85rem;
    transition: opacity .15s, transform .1s;
    box-shadow: 0 4px 10px -2px rgba(23,173,55,.35);
}
.btn-print-label:hover  { opacity: .88; transform: translateY(-1px); }
.btn-print-label:disabled { opacity: .4; cursor: not-allowed; transform: none; }
/* ─── Btn Cetak Semua ───────────────────────────────── */
.btn-print-all {
    display: inline-flex; align-items: center; gap: .5rem;
    padding: .55rem 1.25rem; font-size: .8125rem; font-weight: 700;
    color: #fff;
    background: linear-gradient(310deg, #7928ca, #ff0080);
    border: none; border-radius: .5rem; cursor: pointer;
    transition: opacity .15s, transform .1s, box-shadow .15s;
    box-shadow: 0 4px 12px -2px rgba(121,40,202,.4);
    white-space: nowrap;
}
.btn-print-all:hover { opacity: .88; transform: translateY(-2px); box-shadow: 0 8px 20px -4px rgba(121,40,202,.5); }
/* ─── Inventaris Sub-rows (Accordion) ────────── */
.btn-toggle-units {
    display: inline-flex; align-items: center; justify-content: center; gap: .4rem;
    padding: .4rem .8rem; font-size: .75rem; font-weight: 700;
    color: #0369a1; background: #e0f2fe;
    border: none; border-radius: .4rem; cursor: pointer;
    transition: all .2s; white-space: nowrap;
}
.btn-toggle-units:hover { background: #bae6fd; }
.btn-toggle-units.active { background: #0284c7; color: white; }
.btn-toggle-units.active i { transform: rotate(180deg); }

.units-container {
    padding: 1.5rem; background: #f8fafc;
    border-top: 1px dashed #cbd5e1;
    border-bottom: 2px solid #e2e8f0;
}
.units-grid {
    display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    gap: 1.25rem;
}
.unit-card {
    background: white; border: 1px solid #e2e8f0;
    border-radius: .75rem; padding: 1.25rem;
    display: flex; flex-direction: column; gap: 1rem;
    box-shadow: 0 4px 6px -1px rgba(0,0,0,.05);
}
.unit-card-header {
    display: flex; align-items: center; justify-content: space-between;
    border-bottom: 1px solid #f1f5f9; padding-bottom: .6rem; margin-bottom: -.25rem;
}
.unit-badge {
    display: inline-block;
    font-size: .65rem; font-weight: 800;
    color: #0369a1; background: #e0f2fe;
    padding: .15rem .5rem; border-radius: .3rem; letter-spacing: .02em;
    text-transform: uppercase;
}
.unit-form-group { display: flex; flex-direction: column; gap: .4rem; }
.unit-form-label { font-size: .7rem; font-weight: 700; color: #64748b; text-transform: uppercase; }
/* ─── University QR Upload ───────────────────── */
.univ-qr-wrap {
    display: flex; flex-direction: column;
    align-items: center; gap: .35rem;
}
.btn-upload-univ {
    display: inline-flex; align-items: center; gap: .3rem;
    padding: .28rem .65rem; font-size: .68rem; font-weight: 700;
    color: #fff;
    background: linear-gradient(310deg, #f59e0b, #fbbf24);
    border: none; border-radius: .35rem; cursor: pointer;
    white-space: nowrap;
    transition: opacity .15s, transform .1s;
}
.btn-upload-univ:hover { opacity: .88; transform: translateY(-1px); }
.univ-qr-preview-wrap {
    position: relative; display: flex;
    align-items: center; justify-content: center;
}
.univ-qr-preview {
    width: 56px; height: 56px;
    object-fit: contain;
    border: 2px solid #fbbf24;
    border-radius: .4rem;
    cursor: pointer;
    transition: transform .15s;
    display: block;
}
.univ-qr-preview:hover { transform: scale(1.08); }
.btn-univ-qr-remove {
    position: absolute; top: -6px; right: -6px;
    width: 18px; height: 18px;
    border-radius: 50%; border: none;
    background: #ef4444; color: #fff;
    font-size: .6rem; cursor: pointer;
    display: flex; align-items: center; justify-content: center;
    transition: background .15s;
    z-index: 1;
}
.btn-univ-qr-remove:hover { background: #dc2626; }
/* ─── University QR View Modal ───────────────── */
.univ-qr-view-overlay {
    display: none;
    position: fixed; inset: 0; z-index: 10000;
    background: rgba(15,23,42,.78);
    backdrop-filter: blur(6px);
    align-items: center; justify-content: center;
}
.univ-qr-view-overlay.open { display: flex; }
.univ-qr-view-modal {
    background: #fff;
    border-radius: 1.25rem;
    box-shadow: 0 25px 60px rgba(0,0,0,.3);
    padding: 1.75rem 2rem;
    max-width: 90vw; max-height: 90vh;
    position: relative;
    animation: modalSlideIn .2s ease;
    text-align: center;
}
.univ-qr-view-modal img {
    max-width: 380px; max-height: 65vh;
    border-radius: .75rem;
    object-fit: contain;
    border: 2px solid #fde68a;
}
.btn-close-univ-view {
    position: absolute; top: .9rem; right: .9rem;
    width: 28px; height: 28px;
    border: none; border-radius: .4rem;
    background: #f0f2f5; color: #7b809a;
    font-size: .85rem; cursor: pointer;
    display: flex; align-items: center; justify-content: center;
    transition: background .15s;
}
.btn-close-univ-view:hover { background: #e2e8f0; color: #344767; }
.univ-qr-view-title {
    font-size: .9rem; font-weight: 700;
    color: #344767; margin-bottom: 1rem;
}
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
                        <p class="info-box-title">Item Disetujui</p>
                        <p class="info-box-value">{{ collect($items)->filter(fn($i) => ($i['review_status'] ?? 'pending') !== 'rejected')->count() }} barang</p>
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
                            <th style="width:155px; text-align:right;">Harga Satuan</th>
                            <th style="width:75px; text-align:center;">Qty</th>
                            <th style="width:170px; text-align:right;">Total Harga</th>
                            <th style="width:165px; text-align:center;">Link Pembelian</th>
                            <th style="width:200px;">Menggantikan</th>
                            <th style="width:145px; text-align:center;">Tgl. Diterima</th>
                            <th style="width:150px; text-align:center;">Labeling</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $displayIndex = 0; @endphp
                        @forelse($items as $index => $item)
                        @if(($item['review_status'] ?? 'pending') !== 'rejected')
                        @php $displayIndex++; @endphp

                        @if(($item['item_type'] ?? '') === 'inventaris')
                        {{-- ══ INVENTARIS: Accordion Rows ══ --}}
                        @php $qty = max(1, intval($item['quantity'] ?? 1)); @endphp

                        {{-- Main Row --}}
                        <tr class="inv-main-row">
                            <td class="col-no">{{ $displayIndex }}</td>
                            <td><span class="type-badge type-inventaris">Inventaris</span></td>
                            <td>
                                <p class="item-name">{{ $item['name'] }}</p>
                                @if(!empty($item['notes']))
                                <p class="item-sub"><i class="fas fa-sticky-note mr-1"></i>{{ $item['notes'] }}</p>
                                @endif
                            </td>
                            <td>
                                <span class="price-cell">Rp {{ number_format($item['price'] ?? 0, 0, ',', '.') }}</span>
                                <span class="price-unit-label">per satuan</span>
                            </td>
                            <td style="text-align:center;"><span class="qty-badge">{{ $qty }}</span></td>
                            <td class="total-cell">
                                <span class="total-amount">Rp {{ number_format(($item['price'] ?? 0) * $qty, 0, ',', '.') }}</span>
                            </td>
                            <td style="text-align:center;">
                                @if(!empty($item['purchase_link']))
                                <a href="{{ $item['purchase_link'] }}" target="_blank" rel="noopener noreferrer" class="btn-purchase-link">
                                    <i class="fas fa-shopping-cart"></i> Lihat Toko
                                    <i class="fas fa-external-link-alt" style="font-size:.6rem;"></i>
                                </a>
                                @else
                                <span class="no-link-text">Tidak ada link</span>
                                @endif
                            </td>
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
                                <span style="font-size:.75rem;color:#d2d6da;font-style:italic;">— Tidak Ada —</span>
                                @endif
                            </td>
                            <td style="text-align:center;">
                                <input type="date" class="receive-date-input item-level-date"
                                    id="receive-date-{{ $index }}"
                                    data-index="{{ $index }}"
                                    style="padding:.35rem .6rem;font-size:.75rem;border:1px solid #d2d6da;border-radius:.375rem;color:#495057;outline:none;width:130px;"/>
                            </td>
                            <td style="text-align:center;">
                                <button type="button" class="btn-toggle-units" id="btn-toggle-{{ $index }}" onclick="toggleUnits({{ $index }})">
                                    Isi Label ({{ $qty }}) <i class="fas fa-chevron-down ml-1"></i>
                                </button>
                            </td>
                        </tr>

                        {{-- Accordion Content --}}
                        <tr class="inv-units-row" id="units-row-{{ $index }}" style="display:none;">
                            <td colspan="10" style="padding:0;">
                                <div class="units-container">
                                    <div class="units-grid">
                                        @for($unit = 0; $unit < $qty; $unit++)
                                        <div class="unit-card">
                                            <div class="unit-card-header">
                                                <span class="unit-badge">Unit {{ $unit + 1 }} / {{ $qty }}</span>
                                            </div>

                                            <div class="unit-form-group">
                                                <label class="unit-form-label">No. Label</label>
                                                <div class="label-input-wrap" style="flex-wrap:nowrap;">
                                                    <input type="text" class="label-input"
                                                        id="label-{{ $index }}-{{ $unit }}"
                                                        data-index="{{ $index }}" data-unit="{{ $unit }}"
                                                        data-year="{{ $draft['year'] }}"
                                                        data-name="{{ addslashes($item['name']) }}"
                                                        placeholder="INV-{{ $draft['year'] }}-..."
                                                        autocomplete="off" style="flex:1; width:100px;"/>
                                                    <button type="button" class="btn-generate"
                                                        id="btn-gen-{{ $index }}-{{ $unit }}"
                                                        onclick="generateLabel({{ $index }}, {{ $unit }}, {{ $draft['year'] }})"
                                                        title="Auto-generate nomor label">
                                                        <i class="fas fa-magic"></i>
                                                    </button>
                                                    <button type="button" class="btn-qr"
                                                        id="btn-qr-{{ $index }}-{{ $unit }}"
                                                        onclick="openQrModal({{ $index }}, {{ $unit }})"
                                                        title="Lihat QR Code">
                                                        <i class="fas fa-qrcode"></i> QR
                                                    </button>
                                                </div>
                                            </div>

                                            <div class="unit-form-group">
                                                <label class="unit-form-label">QR Universitas</label>
                                                <div class="univ-qr-wrap" id="univ-qr-wrap-{{ $index }}-{{ $unit }}" style="align-items:flex-start;">
                                                    <label class="btn-upload-univ" for="univ-qr-{{ $index }}-{{ $unit }}" title="Upload QR dari Universitas">
                                                        <i class="fas fa-upload"></i> Upload
                                                    </label>
                                                    <input type="file" class="univ-qr-input"
                                                        id="univ-qr-{{ $index }}-{{ $unit }}"
                                                        data-index="{{ $index }}" data-unit="{{ $unit }}"
                                                        accept="image/*" style="display:none;"
                                                        onchange="handleUnivQrUpload(this)"/>
                                                    <div class="univ-qr-preview-wrap" id="univ-qr-preview-{{ $index }}-{{ $unit }}" style="display:none; margin-top:.5rem;">
                                                        <img class="univ-qr-preview" id="univ-qr-img-{{ $index }}-{{ $unit }}" src="" alt="QR Univ"
                                                            onclick="viewUnivQr('{{ $index }}', {{ $unit }})" title="Klik untuk perbesar"/>
                                                        <button type="button" class="btn-univ-qr-remove"
                                                            onclick="removeUnivQr('{{ $index }}', {{ $unit }})" title="Hapus">
                                                            <i class="fas fa-times"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        @endfor
                                    </div>
                                </div>
                            </td>
                        </tr>

                        @else
                        {{-- ══ BHP row (no per-unit labeling) ══ --}}
                        <tr>
                            <td class="col-no">{{ $displayIndex }}</td>
                            <td><span class="type-badge type-bhp">BHP</span></td>
                            <td>
                                <p class="item-name">{{ $item['name'] }}</p>
                                @if(!empty($item['notes']))
                                <p class="item-sub"><i class="fas fa-sticky-note mr-1"></i>{{ $item['notes'] }}</p>
                                @endif
                            </td>
                            <td>
                                <span class="price-cell">Rp {{ number_format($item['price'] ?? 0, 0, ',', '.') }}</span>
                                <span class="price-unit-label">per satuan</span>
                            </td>
                            <td style="text-align:center;"><span class="qty-badge">{{ $item['quantity'] ?? 1 }}</span></td>
                            <td class="total-cell">
                                <span class="total-amount">Rp {{ number_format(($item['price'] ?? 0) * ($item['quantity'] ?? 1), 0, ',', '.') }}</span>
                            </td>
                            <td style="text-align:center;">
                                @if(!empty($item['purchase_link']))
                                <a href="{{ $item['purchase_link'] }}" target="_blank" rel="noopener noreferrer" class="btn-purchase-link">
                                    <i class="fas fa-shopping-cart"></i> Lihat Toko
                                    <i class="fas fa-external-link-alt" style="font-size:.6rem;"></i>
                                </a>
                                @else
                                <span class="no-link-text">Tidak ada link</span>
                                @endif
                            </td>
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
                                <span style="font-size:.75rem;color:#d2d6da;font-style:italic;">— Tidak Ada —</span>
                                @endif
                            </td>
                            <td style="text-align:center;">
                                <input type="date" class="receive-date-input item-level-date"
                                    id="receive-date-{{ $index }}"
                                    data-index="{{ $index }}"
                                    style="padding:.35rem .6rem;font-size:.75rem;border:1px solid #d2d6da;border-radius:.375rem;color:#495057;outline:none;width:130px;"/>
                            </td>
                            <td style="text-align:center;">
                                <span class="label-na">— Tidak dilabeli —</span>
                            </td>
                        </tr>
                        @endif

                        @endif
                        @empty
                        <tr>
                            <td colspan="11">
                                <div class="empty-items">
                                    <i class="fas fa-box-open" style="font-size:2.5rem;color:#d2d6da;display:block;margin-bottom:.75rem;"></i>
                                    <p>Belum ada rincian barang yang disetujui untuk draf ini.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Grand Total Bar --}}
            @if(count($items) > 0)
            {{-- Label Counter Info --}}
            <div id="labelCounterBar" style="
                display:flex; align-items:center; gap:.75rem;
                padding:.75rem 1.25rem; margin-bottom:.75rem;
                background:#f0fdf4; border:1px solid #bbf7d0;
                border-radius:.65rem; font-size:.8125rem;
            ">
                <i class="fas fa-tags" style="color:#15803d;"></i>
                <span style="color:#15803d; font-weight:600;">
                    Label terisi: <strong id="labelFilledCount">0</strong>
                    dari <strong>{{ collect($items)->where('item_type', 'inventaris')->sum(fn($i) => $i['quantity'] ?? 1) }}</strong> unit inventaris
                </span>
            </div>

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

                @if(collect($items)->where('item_type', 'inventaris')->count() > 0)
                <button
                    type="button"
                    class="btn-print-all"
                    onclick="printAllLabels()"
                    id="btnPrintAll"
                >
                    <i class="fas fa-print"></i>
                    Cetak Semua Label Inventaris
                    <span
                        style="
                            background:rgba(255,255,255,.25); border-radius:.3rem;
                            padding:.1rem .45rem; font-size:.7rem;
                        "
                        id="btnPrintAllCount"
                    >0</span>
                </button>
                @endif
            </div>
        </div>

    </div>
</div>

{{-- ─── QR Modal ──────────────────────────────────────────────────────── --}}
<div class="qr-modal-overlay" id="qrModalOverlay" onclick="closeQrModalOnBg(event)">
    <div class="qr-modal" role="dialog" aria-modal="true" aria-labelledby="qrModalTitle">
        <button class="qr-modal-close" onclick="closeQrModal()" title="Tutup">
            <i class="fas fa-times"></i>
        </button>
        <div class="qr-modal-title" id="qrModalTitle">QR Code Label</div>
        <div class="qr-modal-subtitle" id="qrModalItemName">Nama Barang</div>

        <div class="qr-canvas-wrap">
            <div id="qrCanvas"></div>
        </div>

        <div class="qr-label-code" id="qrLabelCode">&mdash;</div>
        <div class="qr-empty-hint" id="qrEmptyHint" style="display:none;">
            <i class="fas fa-exclamation-triangle"></i>
            Isi nomor label terlebih dahulu untuk generate QR.
        </div>

        <button
            type="button"
            class="btn-print-label"
            id="btnPrintLabel"
            onclick="printSingleLabel()"
        >
            <i class="fas fa-print"></i> Cetak Label
        </button>
    </div>
</div>

{{-- ─── University QR View Modal ──────────────────────────────────────── --}}
<div class="univ-qr-view-overlay" id="univQrViewOverlay" onclick="if(event.target===this)closeUnivQrView()">
    <div class="univ-qr-view-modal">
        <button class="btn-close-univ-view" onclick="closeUnivQrView()" title="Tutup">
            <i class="fas fa-times"></i>
        </button>
        <div class="univ-qr-view-title">
            <i class="fas fa-university mr-1" style="color:#f59e0b;"></i> QR Label Universitas
        </div>
        <img id="univQrViewImg" src="" alt="QR Universitas"/>
    </div>
</div>

{{-- CDN qrcode.js --}}
<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>

<script>
// ─── Konstanta Draft ───────────────────────────────────────────────────────
const DRAFT_ID   = '{{ $draft["id"] }}';
const DRAFT_YEAR = '{{ $draft["year"] }}';

const inventarisInputs = document.querySelectorAll('.label-input');
const receiveDateInputs = document.querySelectorAll('.receive-date-input');

// ─── Accordion Toggle ─────────────────────────────────────────────────────
function toggleUnits(index) {
    const row = document.getElementById(`units-row-${index}`);
    const btn = document.getElementById(`btn-toggle-${index}`);
    if (!row || !btn) return;
    if (row.style.display === 'none') {
        row.style.display = 'table-row';
        btn.classList.add('active');
    } else {
        row.style.display = 'none';
        btn.classList.remove('active');
    }
}

// ─── Label Numbering ───────────────────────────────────────────────────────
// Urutan berdasarkan posisi DOM (1-based) di antara semua label input

function getInputOrder(input) {
    return Array.from(inventarisInputs).indexOf(input) + 1;
}

function generateLabel(index, unit, year) {
    const input = document.getElementById(`label-${index}-${unit}`);
    if (!input) return;
    const order = getInputOrder(input);
    const num   = String(order).padStart(3, '0');
    input.value = `INV-${year}-${num}`;
    input.classList.add('has-value');
    localStorage.setItem(`label_${DRAFT_ID}_${index}_${unit}`, input.value);
    updateLabelCounter();
}

function updateLabelCounter() {
    const counter = document.getElementById('labelFilledCount');
    if (!counter) return;
    let filled = 0;
    inventarisInputs.forEach(input => { if (input.value.trim() !== '') filled++; });
    counter.textContent = filled;
    const badge = document.getElementById('btnPrintAllCount');
    if (badge) badge.textContent = filled;
}

function syncRowLabelState(index) {
    const dateInput = document.getElementById(`receive-date-${index}`);
    const hasDate = dateInput && dateInput.value !== '';
    
    const labels = document.querySelectorAll(`.label-input[data-index="${index}"]`);
    labels.forEach(labelInput => {
        const unit = labelInput.dataset.unit;
        const btnGen = document.getElementById(`btn-gen-${index}-${unit}`);
        const btnQr  = document.getElementById(`btn-qr-${index}-${unit}`);
        
        labelInput.disabled = !hasDate;
        if (btnGen) btnGen.disabled = !hasDate;
        if (btnQr)  btnQr.disabled  = !hasDate;
        
        if (!hasDate) {
            labelInput.value = '';
            labelInput.classList.remove('has-value');
            localStorage.removeItem(`label_${DRAFT_ID}_${index}_${unit}`);
            labelInput.placeholder = 'Isi Tgl. Diterima dulu';
        } else {
            labelInput.placeholder = `INV-${DRAFT_YEAR}-...`;
        }
    });
}

document.addEventListener('DOMContentLoaded', () => {
    // Load label values from localStorage
    inventarisInputs.forEach(input => {
        const { index, unit } = input.dataset;
        const saved = localStorage.getItem(`label_${DRAFT_ID}_${index}_${unit}`);
        if (saved) { input.value = saved; input.classList.add('has-value'); }
    });
    // Load receive date values from localStorage
    receiveDateInputs.forEach(input => {
        const { index } = input.dataset;
        const saved = localStorage.getItem(`receive_date_${DRAFT_ID}_${index}`);
        if (saved) input.value = saved;
    });
    // Load university QR previews from localStorage
    document.querySelectorAll('.univ-qr-input').forEach(input => {
        const { index, unit } = input.dataset;
        const saved = localStorage.getItem(`univ_qr_${DRAFT_ID}_${index}_${unit}`);
        if (saved) showUnivQrPreview(index, unit, saved);
    });
    // Sync disabled states
    receiveDateInputs.forEach(input => {
        syncRowLabelState(input.dataset.index);
    });
    updateLabelCounter();
});

inventarisInputs.forEach(input => {
    input.addEventListener('input', function() {
        const { index, unit } = this.dataset;
        const val = this.value.trim();
        if (val !== '') {
            this.classList.add('has-value');
            localStorage.setItem(`label_${DRAFT_ID}_${index}_${unit}`, val);
        } else {
            this.classList.remove('has-value');
            localStorage.removeItem(`label_${DRAFT_ID}_${index}_${unit}`);
        }
        updateLabelCounter();
    });
});

receiveDateInputs.forEach(input => {
    input.addEventListener('change', function() {
        const index = this.dataset.index;
        const val   = this.value;
        if (val) {
            localStorage.setItem(`receive_date_${DRAFT_ID}_${index}`, val);
        } else {
            localStorage.removeItem(`receive_date_${DRAFT_ID}_${index}`);
        }
        syncRowLabelState(index);
        updateLabelCounter();
    });
});

// ─── University QR Upload ───────────────────────────────────────────────────
function handleUnivQrUpload(input) {
    const { index, unit } = input.dataset;
    const file = input.files[0];
    if (!file) return;
    const reader = new FileReader();
    reader.onload = function(e) {
        const dataUrl = e.target.result;
        try {
            localStorage.setItem(`univ_qr_${DRAFT_ID}_${index}_${unit}`, dataUrl);
        } catch(ex) {
            console.warn('localStorage penuh, gambar tidak tersimpan', ex);
        }
        showUnivQrPreview(index, unit, dataUrl);
    };
    reader.readAsDataURL(file);
}

function showUnivQrPreview(index, unit, src) {
    const previewWrap = document.getElementById(`univ-qr-preview-${index}-${unit}`);
    const previewImg  = document.getElementById(`univ-qr-img-${index}-${unit}`);
    if (previewWrap && previewImg) {
        previewImg.src = src;
        previewWrap.style.display = 'flex';
    }
}

function removeUnivQr(index, unit) {
    localStorage.removeItem(`univ_qr_${DRAFT_ID}_${index}_${unit}`);
    const previewWrap = document.getElementById(`univ-qr-preview-${index}-${unit}`);
    const previewImg  = document.getElementById(`univ-qr-img-${index}-${unit}`);
    const fileInput   = document.getElementById(`univ-qr-${index}-${unit}`);
    if (previewWrap) previewWrap.style.display = 'none';
    if (previewImg)  previewImg.src = '';
    if (fileInput)   fileInput.value = '';
}

function viewUnivQr(index, unit) {
    const img = document.getElementById(`univ-qr-img-${index}-${unit}`);
    if (!img || !img.src) return;
    const overlay = document.getElementById('univQrViewOverlay');
    const viewImg = document.getElementById('univQrViewImg');
    if (overlay && viewImg) {
        viewImg.src = img.src;
        overlay.classList.add('open');
        document.body.style.overflow = 'hidden';
    }
}

function closeUnivQrView() {
    const overlay = document.getElementById('univQrViewOverlay');
    if (overlay) overlay.classList.remove('open');
    document.body.style.overflow = '';
}

// ─── QR Modal (per label) ─────────────────────────────────────────────────
let _currentPrintLabel = '';
let _currentPrintName  = '';

function openQrModal(index, unit) {
    const input    = document.getElementById(`label-${index}-${unit}`);
    const labelVal = input ? input.value.trim() : '';
    const itemName = input ? (input.dataset.name || 'Barang Inventaris') : 'Barang Inventaris';

    _currentPrintLabel = labelVal;
    _currentPrintName  = itemName;

    document.getElementById('qrModalItemName').textContent = itemName;
    document.getElementById('qrLabelCode').textContent     = labelVal || '—';

    const canvas    = document.getElementById('qrCanvas');
    const emptyHint = document.getElementById('qrEmptyHint');
    const btnPrint  = document.getElementById('btnPrintLabel');
    canvas.innerHTML = '';

    if (labelVal) {
        emptyHint.style.display = 'none';
        btnPrint.disabled       = false;
        new QRCode(canvas, {
            text:         labelVal,
            width:        180, height: 180,
            colorDark:    '#344767', colorLight: '#ffffff',
            correctLevel: QRCode.CorrectLevel.H
        });
    } else {
        emptyHint.style.display = 'block';
        btnPrint.disabled       = true;
    }
    document.getElementById('qrModalOverlay').classList.add('open');
    document.body.style.overflow = 'hidden';
}

function closeQrModal() {
    document.getElementById('qrModalOverlay').classList.remove('open');
    document.body.style.overflow = '';
}

function closeQrModalOnBg(e) {
    if (e.target === document.getElementById('qrModalOverlay')) closeQrModal();
}

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') { closeQrModal(); closeUnivQrView(); }
});

// ─── Helper: build label card HTML ──────────────────────────────────────────
function buildLabelCard(labelCode, itemName, qrSrc) {
    return `
    <div class="label-card">
        <div class="label-header">Inventaris Laboratorium</div>
        ${qrSrc ? `<img class="label-qr" src="${qrSrc}" alt="QR ${labelCode}"/>` : '<div style="width:4cm;height:4cm;background:#f0f2f5;border-radius:.3cm;margin:.4cm auto;display:flex;align-items:center;justify-content:center;font-size:8pt;color:#adb5bd;">No QR</div>'}
        <div class="label-code">${labelCode}</div>
        <div class="label-name">${itemName}</div>
        <div class="label-footer">Dicetak ${new Date().toLocaleDateString('id-ID', {day:'2-digit',month:'long',year:'numeric'})}</div>
    </div>`;
}

function getLabelPrintStyles() {
    return `
        * { margin:0; padding:0; box-sizing:border-box; }
        body { font-family: Arial, sans-serif; background:#f0f2f5; padding: 1cm; }
        .labels-grid { display: flex; flex-wrap: wrap; gap: .5cm; justify-content: flex-start; }
        .label-card {
            width: 7cm; padding: .8cm;
            border: 2px solid #344767; border-radius: .4cm;
            background: #fff; text-align: center; page-break-inside: avoid;
        }
        .label-header {
            font-size: 7pt; font-weight: 800; text-transform: uppercase;
            letter-spacing: .08em; color: #7b809a; margin-bottom: .35cm;
            border-bottom: 1px solid #e2e8f0; padding-bottom: .25cm;
        }
        .label-qr { margin: .35cm auto; display: block; width: 3.5cm; height: 3.5cm; }
        .label-code {
            font-family: 'Courier New', monospace; font-size: 11pt; font-weight: 900;
            color: #1e293b; letter-spacing: .06em; margin: .25cm 0 .15cm;
        }
        .label-name { font-size: 8pt; color: #475569; margin-bottom: .25cm; font-weight: 600; word-break: break-word; }
        .label-footer {
            font-size: 6.5pt; color: #adb5bd;
            border-top: 1px solid #e2e8f0; padding-top: .2cm; margin-top: .15cm;
        }
        @media print { body { background:#fff; padding:.5cm; } .label-card { border-color: #000; } }
    `;
}

// ─── Cetak Label Tunggal ──────────────────────────────────────────────────
function printSingleLabel() {
    if (!_currentPrintLabel) return;
    const canvas = document.getElementById('qrCanvas');
    const qrImg  = canvas ? canvas.querySelector('img') : null;
    const qrSrc  = qrImg ? qrImg.src : '';
    const html   = `<!DOCTYPE html><html lang="id"><head>
        <meta charset="UTF-8">
        <title>Label — ${_currentPrintLabel}</title>
        <style>${getLabelPrintStyles()}<\/style>
    </head><body>
        <div class="labels-grid">${buildLabelCard(_currentPrintLabel, _currentPrintName, qrSrc)}</div>
    </body></html>`;
    const pw = window.open('', '_blank', 'width=420,height=520');
    if (!pw) { alert('Izinkan popup browser untuk mencetak label.'); return; }
    pw.document.write(html); pw.document.close(); pw.focus();
    setTimeout(() => { pw.print(); }, 400);
}

// ─── Cetak Semua Label (Bulk Print) ──────────────────────────────────────
async function printAllLabels() {
    const filledInputs = Array.from(inventarisInputs).filter(inp => inp.value.trim() !== '');
    if (filledInputs.length === 0) {
        alert('Belum ada nomor label yang diisi.');
        return;
    }
    const tmpDiv = document.createElement('div');
    tmpDiv.style.cssText = 'position:fixed;left:-9999px;top:-9999px;visibility:hidden;';
    document.body.appendChild(tmpDiv);
    const cards = [];
    for (const inp of filledInputs) {
        const labelCode = inp.value.trim();
        const itemName  = inp.dataset.name || 'Barang Inventaris';
        const qrHolder  = document.createElement('div');
        tmpDiv.appendChild(qrHolder);
        await new Promise(resolve => {
            new QRCode(qrHolder, {
                text: labelCode, width: 160, height: 160,
                colorDark: '#344767', colorLight: '#ffffff',
                correctLevel: QRCode.CorrectLevel.H
            });
            setTimeout(resolve, 300);
        });
        const qrImg = qrHolder.querySelector('img');
        cards.push(buildLabelCard(labelCode, itemName, qrImg ? qrImg.src : ''));
    }
    document.body.removeChild(tmpDiv);
    const html = `<!DOCTYPE html><html lang="id"><head>
        <meta charset="UTF-8">
        <title>Cetak Semua Label Inventaris</title>
        <style>${getLabelPrintStyles()}<\/style>
    </head><body>
        <div class="labels-grid">${cards.join('')}</div>
    </body></html>`;
    const pw = window.open('', '_blank', 'width=900,height=700');
    if (!pw) { alert('Izinkan popup browser untuk mencetak label.'); return; }
    pw.document.write(html); pw.document.close(); pw.focus();
    setTimeout(() => { pw.print(); }, 600);
}
</script>
@endsection
