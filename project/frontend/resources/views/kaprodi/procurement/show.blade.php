@extends('dashboard.layout')
@section('title', 'Review Detail Pengadaan')
@section('page_title', 'Detail Pengadaan')

@section('content')
@php
    $status    = $draft['status'] ?? 'draft';
    $isLocked  = $status !== 'submitted';
    $approved  = collect($items)->filter(fn($i) => ($i['review_status'] ?? 'pending') === 'approved')->count();
    $rejected  = collect($items)->filter(fn($i) => ($i['review_status'] ?? 'pending') === 'rejected')->count();
    $pending   = collect($items)->filter(fn($i) => ($i['review_status'] ?? 'pending') === 'pending')->count();
    $totalItems = count($items);
    $totalAll      = collect($items)->sum(fn($i) => ($i['price'] ?? 0) * ($i['quantity'] ?? 1));
    $totalApproved = collect($items)->filter(fn($i) => ($i['review_status'] ?? 'pending') !== 'rejected')->sum(fn($i) => ($i['price'] ?? 0) * ($i['quantity'] ?? 1));
    $totalRejected = collect($items)->filter(fn($i) => ($i['review_status'] ?? 'pending') === 'rejected')->sum(fn($i) => ($i['price'] ?? 0) * ($i['quantity'] ?? 1));
@endphp

<style>
/* ─── Back btn & page ─────────────────────────────── */
.back-btn { display:inline-flex;align-items:center;justify-content:center;width:2.25rem;height:2.25rem;background:#f0f2f5;border-radius:.5rem;color:#7b809a;text-decoration:none;transition:background .15s,color .15s;flex-shrink:0; }
.back-btn:hover { background:#e2e8f0;color:#344767; }
.page-main-title { font-size:1.25rem;font-weight:700;color:#344767;margin:0 0 .2rem; }
.page-main-sub   { font-size:.875rem;color:#7b809a;margin:0; }

/* ─── Alerts ────────────────────────────────────────── */
.alert-success { display:flex;align-items:center;gap:.75rem;padding:.875rem 1.25rem;background:#f0fdf4;border:1px solid #bbf7d0;border-radius:.75rem;margin-bottom:1rem;font-size:.875rem;font-weight:600;color:#15803d; }
.alert-error   { display:flex;align-items:center;gap:.75rem;padding:.875rem 1.25rem;background:#fef2f2;border:1px solid #fecaca;border-radius:.75rem;margin-bottom:1rem;font-size:.875rem;font-weight:600;color:#dc2626; }

/* ─── Cards ─────────────────────────────────────────── */
.detail-card { background:#fff;border-radius:1rem;border:1px solid #eef0f5;box-shadow:0 1px 3px rgba(0,0,0,.04),0 8px 20px -12px rgba(0,0,0,.08);overflow:hidden;margin-bottom:1.25rem; }
.detail-card-bar { height:4px;background:linear-gradient(90deg,#7928ca,#ff007f); }
.detail-body { padding:1.75rem; }
.section-label { font-size:.65rem;font-weight:700;text-transform:uppercase;letter-spacing:.09em;color:#adb5bd;margin:0 0 1rem;display:flex;align-items:center;gap:.5rem; }
.section-label::after { content:'';flex:1;height:1px;background:#f0f2f5; }
.form-divider { border:none;border-top:1px solid #f0f2f5;margin:1.5rem 0; }

/* ─── Info boxes ─────────────────────────────────────── */
.info-grid { display:grid;grid-template-columns:repeat(4,1fr);gap:1rem;margin-bottom:1.5rem; }
@media(max-width:768px){ .info-grid { grid-template-columns:repeat(2,1fr); } }
.info-box { background:#f8f9fa;border-radius:.75rem;padding:1rem;display:flex;align-items:center;gap:.875rem; }
.info-icon { width:2.5rem;height:2.5rem;border-radius:.5rem;background:linear-gradient(310deg,#7928ca,#ff007f);display:flex;align-items:center;justify-content:center;color:#fff;font-size:1rem;flex-shrink:0; }
.info-label { font-size:.65rem;font-weight:700;text-transform:uppercase;color:#adb5bd;margin:0 0 .15rem; }
.info-value { font-size:.875rem;font-weight:700;color:#344767;margin:0; }

/* ─── Draft status badges ───────────────────────────── */
.draft-status { display:inline-flex;align-items:center;gap:.35rem;padding:.35rem 1rem;border-radius:9999px;font-size:.75rem;font-weight:700; }
.ds-dot { width:7px;height:7px;border-radius:50%;display:inline-block; }
.ds-submitted { background:#f0fdf4;color:#15803d; } .ds-submitted .ds-dot { background:#16a34a; }
.ds-approved  { background:#eff6ff;color:#1d4ed8; } .ds-approved .ds-dot  { background:#2563eb; }
.ds-rejected  { background:#fef2f2;color:#dc2626; } .ds-rejected .ds-dot  { background:#ef4444; }
.ds-draft     { background:#fff7ed;color:#c2410c; } .ds-draft .ds-dot     { background:#ea580c; }

/* ─── Review progress bar ───────────────────────────── */
.review-progress { background:#f8f9fa;border-radius:.75rem;padding:1rem 1.25rem;margin-bottom:1.25rem;display:flex;align-items:center;gap:1.5rem;flex-wrap:wrap; }
.prog-item { display:flex;align-items:center;gap:.5rem;font-size:.875rem;font-weight:600; }
.prog-dot { width:10px;height:10px;border-radius:50%;flex-shrink:0; }
.prog-approved .prog-dot { background:#16a34a; }
.prog-approved { color:#15803d; }
.prog-rejected .prog-dot { background:#ef4444; }
.prog-rejected { color:#dc2626; }
.prog-pending .prog-dot  { background:#94a3b8; }
.prog-pending  { color:#64748b; }

/* ─── Items table ───────────────────────────────────── */
.users-table { width:100%;border-collapse:collapse; }
.users-table thead th { padding:.75rem 1rem;font-size:.65rem;font-weight:700;text-transform:uppercase;color:#adb5bd;border-bottom:2px solid #f0f2f5;text-align:left;white-space:nowrap;background:#fafbfc; }
.users-table tbody td { padding:.875rem 1rem;border-bottom:1px solid #f0f2f5;font-size:.875rem;color:#7b809a;vertical-align:middle; }
.users-table tbody tr:last-child td { border-bottom:none; }
.users-table tbody tr:hover { background:#fafbfc; }

/* ─── Item review status badges ─────────────────────── */
.item-status { display:inline-flex;align-items:center;gap:.3rem;padding:.25rem .7rem;border-radius:9999px;font-size:.7rem;font-weight:600;white-space:nowrap; }
.is-dot { width:6px;height:6px;border-radius:50%;flex-shrink:0; }
.is-approved { background:#f0fdf4;color:#15803d; } .is-approved .is-dot { background:#16a34a; }
.is-rejected { background:#fef2f2;color:#dc2626; } .is-rejected .is-dot { background:#ef4444; }
.is-pending  { background:#f1f5f9;color:#64748b; } .is-pending .is-dot  { background:#94a3b8; }

/* ─── Type badges ───────────────────────────────────── */
.type-badge { display:inline-block;padding:.2rem .65rem;font-size:.65rem;font-weight:700;text-transform:uppercase;border-radius:.3rem;letter-spacing:.03em; }
.type-inventaris { background:#e0f2fe;color:#0369a1; }
.type-bhp        { background:#fdf2f8;color:#be185d; }
.qty-badge { display:inline-flex;align-items:center;justify-content:center;min-width:2rem;padding:.25rem .5rem;background:#f0f2f5;border-radius:.4rem;font-size:.8rem;font-weight:700;color:#344767; }

/* ─── Review action buttons ─────────────────────────── */
.review-actions { display:flex;align-items:center;gap:.375rem; }
.btn-item { display:inline-flex;align-items:center;gap:.25rem;padding:.3rem .7rem;font-size:.72rem;font-weight:600;border:none;border-radius:.4rem;cursor:pointer;white-space:nowrap;transition:opacity .15s,transform .1s; }
.btn-item:hover { opacity:.85;transform:translateY(-1px); }
.btn-item-approve { background:#f0fdf4;color:#15803d;border:1px solid #bbf7d0; }
.btn-item-reject  { background:#fef2f2;color:#dc2626;border:1px solid #fecaca; }
.btn-item-reset   { background:#f1f5f9;color:#64748b;border:1px solid #e2e8f0; }

/* ─── Grand total bar ───────────────────────────────── */
.total-bar { background:linear-gradient(135deg,#f5f3ff,#fdf2f8);border:1px solid #e9d5ff;border-radius:.75rem;padding:1rem 1.5rem;margin-top:1.25rem;display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:.75rem; }

/* ─── Finalize section ──────────────────────────────── */
.finalize-section { background:#fff;border-radius:1rem;border:1px solid #eef0f5;box-shadow:0 1px 3px rgba(0,0,0,.04),0 8px 20px -12px rgba(0,0,0,.08);padding:1.5rem;margin-bottom:1.25rem; }
.finalize-title { font-size:1rem;font-weight:700;color:#344767;margin:0 0 .35rem; }
.finalize-sub   { font-size:.875rem;color:#7b809a;margin:0 0 1.25rem; }
.warn-pending { display:flex;align-items:flex-start;gap:.75rem;padding:.875rem 1rem;background:#fffbeb;border:1px solid #fde68a;border-radius:.65rem;margin-bottom:1rem;font-size:.8125rem;color:#92400e; }
.warn-pending i { flex-shrink:0;margin-top:.1rem;color:#d97706; }
.btn-finalize {
    display:inline-flex;align-items:center;gap:.5rem;
    padding:.625rem 1.75rem;font-size:.875rem;font-weight:700;color:#fff;
    background:linear-gradient(310deg,#2152ff,#21d4fd);border:none;border-radius:.65rem;
    cursor:pointer;box-shadow:0 4px 7px -1px rgba(33,82,255,.4);
    transition:box-shadow .15s,transform .15s;
}
.btn-finalize:hover { box-shadow:0 8px 25px -8px rgba(33,82,255,.6);transform:translateY(-1px); }
.btn-back-form { display:inline-flex;align-items:center;gap:.5rem;padding:.625rem 1.5rem;font-size:.8125rem;font-weight:600;color:#7b809a;background:#f0f2f5;border:none;border-radius:.65rem;cursor:pointer;text-decoration:none;transition:background .15s; }
.btn-back-form:hover { background:#e2e8f0;color:#344767; }
.locked-banner { display:flex;align-items:center;gap:.875rem;padding:1rem 1.25rem;background:#eff6ff;border:1px solid #bfdbfe;border-radius:.75rem;margin-bottom:1.25rem;font-size:.875rem;color:#1d4ed8;font-weight:600; }
</style>

<div class="flex flex-wrap -mx-3">
<div class="w-full px-3">

    {{-- Page Header --}}
    <div style="background:#fff;border-radius:1rem;box-shadow:0 20px 27px 0 rgba(0,0,0,.05);padding:1.25rem 1.5rem;margin-bottom:1.25rem;">
        <div style="display:flex;align-items:center;gap:1rem;">
            <a href="{{ route('kaprodi.procurement.index') }}" class="back-btn">
                <i class="fas fa-arrow-left" style="font-size:.75rem;"></i>
            </a>
            <div>
                <h4 class="page-main-title">
                    <i class="fas fa-gavel" style="color:#7928ca;font-size:1rem;margin-right:.4rem;"></i>
                    Review Detail Pengadaan
                </h4>
                <p class="page-main-sub">Setujui atau tolak setiap item, lalu finalisasi draf untuk menguncinya secara permanen.</p>
            </div>
        </div>
    </div>

    {{-- Alerts --}}
    @if(session('success'))
    <div class="alert-success"><i class="fas fa-check-circle"></i>{{ session('success') }}</div>
    @endif
    @if($errors->any())
    <div class="alert-error"><i class="fas fa-exclamation-circle"></i>{{ $errors->first() }}</div>
    @endif

    {{-- Locked banner (jika sudah difinalisasi) --}}
    @if($isLocked)
    <div class="locked-banner">
        <i class="fas fa-lock" style="font-size:1.1rem;flex-shrink:0;"></i>
        <span>Draf ini sudah <strong>difinalisasi</strong> dan tidak dapat diubah lagi. Item yang ditolak tidak akan diproses oleh Staf Administrasi.</span>
    </div>
    @endif

    {{-- ─── Info Header ─── --}}
    <div class="detail-card">
        <div class="detail-card-bar"></div>
        <div class="detail-body">

            <div style="display:flex;justify-content:space-between;align-items:flex-start;flex-wrap:wrap;gap:1rem;margin-bottom:1.25rem;">
                <div>
                    <h3 style="font-size:1.25rem;font-weight:700;color:#344767;margin:0 0 .25rem;">{{ $draft['title'] }}</h3>
                    <p style="font-size:.875rem;color:#7b809a;margin:0;">
                        <i class="fas fa-calendar-alt" style="color:#adb5bd;font-size:.75rem;"></i>
                        Tahun Anggaran {{ $draft['year'] }}
                    </p>
                </div>
                @if($status === 'submitted')
                    <span class="draft-status ds-submitted"><span class="ds-dot"></span> Menunggu Review</span>
                @elseif($status === 'approved')
                    <span class="draft-status ds-approved"><span class="ds-dot"></span> Telah Difinalisasi</span>
                @else
                    <span class="draft-status ds-draft"><span class="ds-dot"></span> {{ ucfirst($status) }}</span>
                @endif
            </div>

            <div class="info-grid">
                <div class="info-box">
                    <div class="info-icon"><i class="fas fa-boxes"></i></div>
                    <div><p class="info-label">Total Item</p><p class="info-value">{{ $totalItems }} barang</p></div>
                </div>
                <div class="info-box">
                    <div class="info-icon"><i class="fas fa-wallet"></i></div>
                    <div>
                        <p class="info-label">Total Anggaran</p>
                        <p class="info-value" style="color:#7928ca;">
                            Rp {{ number_format(collect($items)->sum(fn($i) => ($i['price'] ?? 0) * ($i['quantity'] ?? 1)), 0, ',', '.') }}
                        </p>
                    </div>
                </div>
                <div class="info-box">
                    <div class="info-icon"><i class="fas fa-clock"></i></div>
                    <div>
                        <p class="info-label">Tanggal Diajukan</p>
                        <p class="info-value">{{ isset($draft['created_at']) ? \Carbon\Carbon::parse($draft['created_at'])->format('d M Y') : '-' }}</p>
                    </div>
                </div>
            </div>

            {{-- Review progress --}}
            @if($totalItems > 0)
            <div class="review-progress">
                <span style="font-size:.8125rem;font-weight:700;color:#344767;">Progres Review:</span>
                <span class="prog-item prog-approved"><span class="prog-dot"></span> {{ $approved }} Disetujui</span>
                <span class="prog-item prog-rejected"><span class="prog-dot"></span> {{ $rejected }} Ditolak</span>
                <span class="prog-item prog-pending"><span class="prog-dot"></span> {{ $pending }} Belum Diputuskan</span>
            </div>
            @endif

            {{-- ─── Items Table ─── --}}
            <p class="section-label"><i class="fas fa-list-ul"></i> Rincian Barang — Klik Setujui/Tolak pada setiap item</p>

            <div style="overflow-x:auto;">
                <table class="users-table">
                    <thead>
                        <tr>
                            <th style="width:42px;text-align:center;">No</th>
                            <th style="width:90px;">Tipe</th>
                            <th>Nama Barang</th>
                            <th style="width:140px;text-align:right;">Harga Satuan</th>
                            <th style="width:65px;text-align:center;">Qty</th>
                            <th style="width:150px;text-align:right;">Total</th>
                            <th style="width:130px;text-align:center;">Status Review</th>
                            @if(!$isLocked)
                            <th style="width:180px;text-align:center;">Keputusan</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($items as $idx => $item)
                        @php
                            $rs = $item['review_status'] ?? 'pending';
                        @endphp
                        <tr style="{{ $rs === 'rejected' ? 'opacity:.55;' : '' }}">
                            <td style="text-align:center;font-weight:700;color:#adb5bd;font-size:.8rem;">{{ $idx + 1 }}</td>
                            <td>
                                @if(($item['item_type'] ?? '') === 'inventaris')
                                    <span class="type-badge type-inventaris">Inventaris</span>
                                @else
                                    <span class="type-badge type-bhp">BHP</span>
                                @endif
                            </td>
                            <td>
                                <div style="font-weight:600;color:#344767;">{{ $item['name'] }}</div>
                                @if(!empty($item['notes'])) <div style="font-size:.72rem;color:#adb5bd;"><i class="fas fa-sticky-note mr-1"></i>{{ $item['notes'] }}</div> @endif
                                @if(!empty($item['replaced_asset_id']))
                                <div style="font-size:.72rem;color:#f59e0b;"><i class="fas fa-exchange-alt mr-1"></i>Menggantikan: {{ $item['replaced_asset_name'] ?? 'Aset Lama' }}</div>
                                @endif
                            </td>
                            <td style="text-align:right;font-weight:600;color:#344767;">Rp {{ number_format($item['price'] ?? 0, 0, ',', '.') }}</td>
                            <td style="text-align:center;"><span class="qty-badge">{{ $item['quantity'] ?? 1 }}</span></td>
                            <td style="text-align:right;font-weight:700;color:#7928ca;">Rp {{ number_format(($item['price'] ?? 0) * ($item['quantity'] ?? 1), 0, ',', '.') }}</td>
                            <td style="text-align:center;">
                                @if($rs === 'approved')
                                    <span class="item-status is-approved"><span class="is-dot"></span> Disetujui</span>
                                @elseif($rs === 'rejected')
                                    <span class="item-status is-rejected"><span class="is-dot"></span> Ditolak</span>
                                @else
                                    <span class="item-status is-pending"><span class="is-dot"></span> Belum Diputuskan</span>
                                @endif
                            </td>
                            @if(!$isLocked)
                            <td>
                                <div class="review-actions" style="justify-content:center;">
                                    @if($rs !== 'approved')
                                    <form action="{{ route('kaprodi.procurement.item.review', [$draft['id'], $item['id']]) }}" method="POST" style="margin:0;">
                                        @csrf
                                        <input type="hidden" name="review_status" value="approved">
                                        <button type="submit" class="btn-item btn-item-approve" title="Setujui item ini">
                                            <i class="fas fa-check"></i> Setujui
                                        </button>
                                    </form>
                                    @endif
                                    @if($rs !== 'rejected')
                                    <form action="{{ route('kaprodi.procurement.item.review', [$draft['id'], $item['id']]) }}" method="POST" style="margin:0;">
                                        @csrf
                                        <input type="hidden" name="review_status" value="rejected">
                                        <button type="submit" class="btn-item btn-item-reject" title="Tolak item ini">
                                            <i class="fas fa-times"></i> Tolak
                                        </button>
                                    </form>
                                    @endif
                                    @if($rs !== 'pending')
                                    <form action="{{ route('kaprodi.procurement.item.review', [$draft['id'], $item['id']]) }}" method="POST" style="margin:0;">
                                        @csrf
                                        <input type="hidden" name="review_status" value="pending">
                                        <button type="submit" class="btn-item btn-item-reset" title="Reset ke belum diputuskan">
                                            <i class="fas fa-undo"></i>
                                        </button>
                                    </form>
                                    @endif
                                </div>
                            </td>
                            @endif
                        </tr>
                        @empty
                        <tr>
                            <td colspan="{{ $isLocked ? 7 : 8 }}" style="text-align:center;padding:2.5rem;color:#adb5bd;">
                                <i class="fas fa-box-open" style="font-size:2rem;display:block;margin-bottom:.75rem;color:#d2d6da;"></i>
                                Belum ada rincian barang.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($totalItems > 0)
            <div class="total-bar">
                <div>
                    <p style="font-size:.7rem;color:#7b809a;font-weight:700;text-transform:uppercase;margin-bottom:.2rem;">Total Seluruh Anggaran</p>
                    <p style="font-size:1.35rem;font-weight:800;color:#7928ca;margin:0;">
                        Rp {{ number_format($totalAll, 0, ',', '.') }}
                    </p>
                </div>
                <div style="text-align:right;">
                    @if($totalRejected > 0)
                    <p style="font-size:.7rem;color:#adb5bd;font-weight:600;text-transform:uppercase;margin-bottom:.15rem;">Ditolak</p>
                    <p style="font-size:.85rem;font-weight:700;color:#dc2626;margin:0 0 .5rem;">- Rp {{ number_format($totalRejected, 0, ',', '.') }}</p>
                    @endif
                    <p style="font-size:.7rem;color:#adb5bd;font-weight:600;text-transform:uppercase;margin-bottom:.25rem;">Anggaran Disetujui</p>
                    <p style="font-size:1.15rem;font-weight:800;color:#15803d;margin:0;">
                        Rp {{ number_format($totalApproved, 0, ',', '.') }}
                    </p>
                </div>
            </div>
            @endif

        </div>
    </div>

    {{-- ─── Finalize Section (hanya jika belum difinalisasi) ─── --}}
    @if(!$isLocked)
    <div class="finalize-section">
        <p class="finalize-title"><i class="fas fa-flag-checkered mr-2" style="color:#2152ff;"></i>Finalisasi Draf</p>
        <p class="finalize-sub">
            Setelah finalisasi, draf ini akan dikunci secara permanen dan diteruskan ke Staf Administrasi.
            Item yang masih "Belum Diputuskan" akan otomatis dianggap disetujui.
        </p>

        @if($pending > 0)
        <div class="warn-pending">
            <i class="fas fa-exclamation-triangle"></i>
            <span><strong>{{ $pending }} item belum diputuskan.</strong> Item tersebut akan otomatis disetujui saat Anda melakukan finalisasi.</span>
        </div>
        @endif

        @if($approved === 0 && $pending === 0)
        <div class="alert-error" style="margin-bottom:1rem;">
            <i class="fas fa-exclamation-circle"></i>
            Semua item ditolak. Tidak ada barang yang akan diproses. Pastikan minimal 1 item disetujui sebelum finalisasi.
        </div>
        @endif

        <div style="display:flex;align-items:center;gap:.75rem;flex-wrap:wrap;">
            <a href="{{ route('kaprodi.procurement.index') }}" class="btn-back-form">
                <i class="fas fa-arrow-left"></i> Kembali ke Daftar
            </a>
            <form action="{{ route('kaprodi.procurement.finalize', $draft['id']) }}" method="POST" style="margin:0;" id="finalizeForm">
                @csrf
                <button type="button" class="btn-finalize" id="btnFinalize">
                    <i class="fas fa-lock"></i> Finalisasi Draf
                </button>
            </form>
        </div>
    </div>
    @else
    <div style="margin-bottom:1.25rem;">
        <a href="{{ route('kaprodi.riwayat.index') }}" class="btn-back-form">
            <i class="fas fa-arrow-left"></i> Kembali ke Riwayat
        </a>
    </div>
    @endif

</div>
</div>

@section('scripts')
<script>
document.getElementById('btnFinalize')?.addEventListener('click', function () {
    Swal.fire({
        title: 'Finalisasi Draf?',
        html: `
            <div style="text-align:left; line-height:1.7;">
                <p style="margin:0 0 .5rem; font-weight:600; color:#7928ca;">Perhatian sebelum melanjutkan:</p>
                <ul style="padding-left:1.25rem; margin:0;">
                    <li style="margin-bottom:.25rem; color:#7b809a;">Draf akan <strong>dikunci permanen</strong></li>
                    <li style="margin-bottom:.25rem; color:#7b809a;">Item belum diputuskan <strong>({{ $pending }})</strong> otomatis disetujui</li>
                    <li style="color:#7b809a;">Tindakan ini <strong>tidak dapat dibatalkan</strong></li>
                </ul>
            </div>`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: '<i class="fas fa-lock"></i>&nbsp; Ya, Finalisasi!',
        cancelButtonText: '<i class="fas fa-times"></i>&nbsp; Batal',
        buttonsStyling: false,
        reverseButtons: false,
        focusCancel: true,
        customClass: {
            popup: 'swal-custom-popup',
            title: 'swal-custom-title',
            htmlContainer: 'swal-custom-html',
            confirmButton: 'swal-btn-confirm',
            cancelButton: 'swal-btn-cancel',
        }
    }).then(function (result) {
        if (result.isConfirmed) {
            document.getElementById('finalizeForm').submit();
        }
    });
});
</script>
@endsection
@endsection
