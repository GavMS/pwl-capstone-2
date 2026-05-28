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
    border-bottom: 1px solid #f0f2f5;
    margin: 1.5rem 0;
}
/* ─── Grid Cards ──────────────────────────────────── */
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
/* ─── Badges ──────────────────────────────────────── */
.status-badge {
    display: inline-flex;
    align-items: center;
    gap: .35rem;
    padding: .3rem .85rem;
    border-radius: .45rem;
    font-size: .7rem;
    font-weight: 700;
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

.type-badge {
    display: inline-block;
    padding: .2rem .6rem;
    font-size: .65rem;
    font-weight: 700;
    text-transform: uppercase;
    border-radius: .25rem;
}
.type-inventaris { background: #e0f2fe; color: #0369a1; }
.type-bhp { background: #fdf2f8; color: #be185d; }

/* ─── Table ───────────────────────────────────────── */
.details-table { width: 100%; border-collapse: collapse; }
.details-table thead th {
    padding: .75rem 1rem;
    font-size: .65rem; font-weight: 700; text-transform: uppercase;
    color: #adb5bd; border-bottom: 1px solid #f0f2f5;
    text-align: left;
}
.details-table tbody td {
    padding: 1rem;
    border-bottom: 1px solid #f0f2f5;
    font-size: .875rem;
    color: #7b809a;
}
.details-table tbody tr:last-child td { border-bottom: none; }
.details-table tbody tr:hover { background: #fafbfc; }

/* ─── Buttons ─────────────────────────────────────── */
.btn-back {
    display: inline-flex; align-items: center; gap: .5rem;
    padding: .5rem 1.25rem; font-size: .8125rem; font-weight: 600; color: #7b809a;
    background: #f0f2f5; border: none; border-radius: .5rem; cursor: pointer;
    transition: background .15s; text-decoration: none;
}
.btn-back:hover { background: #e2e8f0; color: #7b809a; }

.btn-edit-action {
    display: inline-flex; align-items: center; gap: .5rem;
    padding: .5rem 1.25rem; font-size: .8125rem; font-weight: 600; color: #fff;
    background: linear-gradient(310deg, #2152ff, #21d4fd); border: none; border-radius: .5rem; cursor: pointer;
    box-shadow: 0 3px 5px -1px rgba(33,82,255,.4); text-decoration: none;
    transition: transform .15s, box-shadow .15s;
}
.btn-edit-action:hover { transform: translateY(-1px); box-shadow: 0 6px 15px -3px rgba(33,82,255,.5); color: #fff; }

.btn-link {
    display: inline-flex; align-items: center; gap: .35rem;
    padding: .3rem .6rem; font-size: .75rem; font-weight: 600; color: #2152ff;
    background: #eff6ff; border: 1px solid #bfdbfe; border-radius: .35rem;
    text-decoration: none; transition: background .15s;
}
.btn-link:hover { background: #dbeafe; }
</style>

<div class="flex flex-wrap -mx-3">
    <div class="w-full px-3">
        
        {{-- Card 1: Header & Ringkasan Draf --}}
        <div class="detail-card">
            <div class="flex justify-between items-center flex-wrap gap-4">
                <div>
                    <h3 class="detail-title">{{ $draft['title'] }}</h3>
                    <p class="detail-subtitle">Informasi detail mengenai usulan anggaran dan daftar pengadaan barang.</p>
                </div>
                <div>
                    @if(($draft['status'] ?? 'draft') === 'draft')
                    <span class="status-badge status-draft">
                        <span class="sb-dot"></span> Draft (Belum Diajukan)
                    </span>
                    @else
                    <span class="status-badge status-submitted">
                        <span class="sb-dot"></span> Sudah Diajukan (Terkunci)
                    </span>
                    @endif
                </div>
            </div>

            <div class="section-divider"></div>

            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4">
                <div class="info-box">
                    <div class="info-box-icon"><i class="fas fa-calendar-alt"></i></div>
                    <div>
                        <p class="info-box-title">Tahun Anggaran</p>
                        <p class="info-box-value">{{ $draft['year'] }}</p>
                    </div>
                </div>

                <div class="info-box">
                    <div class="info-box-icon"><i class="fas fa-wallet"></i></div>
                    <div>
                        <p class="info-box-title">Total Anggaran</p>
                        <p class="info-box-value" style="color: #7928ca;">
                            Rp {{ number_format($draft['total_price'] ?? collect($items)->sum(fn($i) => $i['price'] * $i['quantity']), 0, ',', '.') }}
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
                        <p class="info-box-title">Tanggal Pembuatan</p>
                        <p class="info-box-value">
                            {{ isset($draft['created_at']) ? \Carbon\Carbon::parse($draft['created_at'])->format('d M Y, H:i') : '-' }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Card 2: Rincian Barang --}}
        <div class="detail-card">
            <h4 class="detail-title text-base mb-4">
                <i class="fas fa-boxes text-purple-700 mr-2"></i>
                Rincian Barang yang Diusulkan
            </h4>

            <div style="overflow-x: auto;">
                <table class="details-table">
                    <thead>
                        <tr>
                            <th style="width: 50px; text-align: center;">No</th>
                            <th style="width: 120px;">Tipe</th>
                            <th>Nama Barang</th>
                            <th style="text-align: right; width: 160px;">Harga Satuan</th>
                            <th style="text-align: center; width: 80px;">Jumlah</th>
                            <th style="text-align: right; width: 180px;">Total Harga</th>
                            <th style="width: 220px;">Menggantikan Barang Lama</th>
                            <th>Catatan</th>
                            <th style="width: 140px; text-align: center;">Link</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($items as $index => $item)
                        <tr>
                            <td style="text-align: center; font-weight: 600;">{{ $index + 1 }}</td>
                            <td>
                                @if($item['item_type'] === 'inventaris')
                                    <span class="type-badge type-inventaris">Inventaris</span>
                                @else
                                    <span class="type-badge type-bhp">BHP</span>
                                @endif
                            </td>
                            <td style="font-weight: 600; color: #344767;">{{ $item['name'] }}</td>
                            <td style="text-align: right;">
                                Rp {{ number_format($item['price'], 0, ',', '.') }}
                            </td>
                            <td style="text-align: center; font-weight: 600;">{{ $item['quantity'] }}</td>
                            <td style="text-align: right; font-weight: 600; color: #344767;">
                                Rp {{ number_format($item['price'] * $item['quantity'], 0, ',', '.') }}
                            </td>
                            <td>
                                @if($item['replaced_asset_id'])
                                    <div class="flex items-center gap-1.5 text-slate-700" style="font-size: .8rem;">
                                        <i class="fas fa-exchange-alt text-amber-500"></i>
                                        <span>
                                            <strong>{{ $item['replaced_asset_name'] }}</strong><br>
                                            <small class="text-slate-400">Code: {{ $item['replaced_asset_code'] ?? '-' }}</small>
                                        </span>
                                    </div>
                                @else
                                    <span class="text-slate-400 text-xs">-- Tidak Ada --</span>
                                @endif
                            </td>
                            <td>
                                <span class="text-xs">{{ $item['notes'] ?? '-' }}</span>
                            </td>
                            <td style="text-align: center;">
                                @if($item['purchase_link'])
                                    <a href="{{ $item['purchase_link'] }}" target="_blank" class="btn-link">
                                        <i class="fas fa-shopping-cart text-xs"></i> Link Beli
                                    </a>
                                @else
                                    <span class="text-slate-400 text-xs">-</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" style="text-align: center;" class="py-4 text-slate-400">
                                Belum ada rincian barang untuk draf pengadaan ini.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="section-divider"></div>

            <div class="flex justify-between items-center">
                <a href="{{ route('kalab.procurement.index') }}" class="btn-back">
                    <i class="fas fa-arrow-left"></i> Kembali ke Daftar
                </a>

                @if(($draft['status'] ?? 'draft') === 'draft')
                <a href="{{ route('kalab.procurement.edit', $draft['id']) }}" class="btn-edit-action">
                    <i class="fas fa-edit"></i> Edit Draf Ini
                </a>
                @endif
            </div>

        </div>

    </div>
</div>
@endsection
