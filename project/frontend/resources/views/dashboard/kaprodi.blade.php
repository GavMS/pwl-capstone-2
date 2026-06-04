@extends('dashboard.layout')
@section('title', 'Dashboard Ketua Program Studi')
@section('page_title', 'Dashboard')

@section('content')
@php
    $firstName = explode(' ', $user['name'] ?? 'User')[0];
    $p = $procStats ?? [];
@endphp

<style>
.greeting-title { font-size: 1.5rem; font-weight: 700; color: #344767; margin: 0 0 .25rem; }
.greeting-sub   { font-size: .875rem; color: #7b809a; margin: 0; }
.stat-card {
    background: #fff; border-radius: 1rem; padding: 1.25rem 1.5rem;
    border: 1px solid #eef0f5;
    box-shadow: 0 1px 3px rgba(0,0,0,.04), 0 8px 20px -12px rgba(0,0,0,.08);
    display: flex; align-items: center; gap: 1rem;
    transition: transform .18s ease, box-shadow .18s ease; height: 100%;
}
.stat-card:hover { transform: translateY(-2px); box-shadow: 0 4px 8px rgba(0,0,0,.04), 0 14px 30px -12px rgba(0,0,0,.12); }
.stat-icon { width: 3rem; height: 3rem; border-radius: .75rem; display: flex; align-items: center; justify-content: center; flex-shrink: 0; font-size: 1.1rem; }
.si-yellow { background: #fffbeb; color: #b45309; }
.si-blue   { background: #eff6ff; color: #1d4ed8; }
.si-red    { background: #fef2f2; color: #dc2626; }
.si-gray   { background: #f8f9fa; color: #64748b; }
.stat-label  { font-size: .8125rem; color: #7b809a; margin: 0 0 .375rem; font-weight: 500; }
.stat-number { font-size: 1.75rem; font-weight: 700; color: #344767; line-height: 1; margin: 0 0 .35rem; }
.stat-sub    { font-size: .7rem; color: #adb5bd; margin: 0; }

.review-alert {
    display: flex; align-items: center; gap: .875rem;
    padding: 1rem 1.5rem;
    background: #fffbeb; border: 1px solid #fde68a; border-radius: 1rem;
    margin-top: 1.5rem; font-size: .875rem; color: #92400e;
}
.review-alert i { font-size: 1.125rem; flex-shrink: 0; }
.review-alert strong { font-weight: 700; }
.btn-review {
    display: inline-flex; align-items: center; gap: .4rem;
    padding: .5rem 1.125rem; font-size: .8125rem; font-weight: 600; color: #fff;
    background: linear-gradient(310deg, #7928ca, #ff007f);
    border: none; border-radius: .5rem; cursor: pointer; text-decoration: none;
    box-shadow: 0 4px 7px -1px rgba(0,0,0,.11); margin-left: auto; white-space: nowrap;
    transition: box-shadow .15s, transform .15s;
}
.btn-review:hover { box-shadow: 0 8px 25px -8px rgba(121,40,202,.7); transform: translateY(-1px); color: #fff; }

.section-card {
    background: #fff; border-radius: 1rem; border: 1px solid #eef0f5;
    box-shadow: 0 1px 3px rgba(0,0,0,.04), 0 8px 20px -12px rgba(0,0,0,.08); overflow: hidden; margin-top: 1.5rem;
}
.section-card-header {
    display: flex; align-items: center; justify-content: space-between;
    padding: 1.125rem 1.5rem; border-bottom: 1px solid #f0f2f5;
}
.section-card-title { font-size: 1rem; font-weight: 700; color: #344767; margin: 0; display: flex; align-items: center; gap: .5rem; }
.section-card-title i { color: #7928ca; font-size: .8rem; }
.section-card-link { font-size: .75rem; font-weight: 600; color: #7928ca; text-decoration: none; display: inline-flex; align-items: center; gap: .25rem; }
.section-card-link:hover { color: #ff007f; }
.quicklinks { display: flex; flex-wrap: wrap; gap: 1rem; padding: 1.5rem; }
.quicklink-btn {
    display: inline-flex; align-items: center; gap: .75rem;
    padding: .875rem 1.25rem; border-radius: .75rem; text-decoration: none;
    background: #f8f9fa; border: 1px solid #eef0f5;
    transition: background .15s, border-color .15s, transform .15s;
    flex: 1; min-width: 180px;
}
.quicklink-btn:hover { background: #f0f2f5; border-color: #d2d6da; transform: translateY(-1px); }
.qi { width: 2.25rem; height: 2.25rem; border-radius: .5rem; display: flex; align-items: center; justify-content: center; font-size: .875rem; flex-shrink: 0; color: #fff; }
</style>

<div style="margin-bottom:1.5rem;">
    <h4 class="greeting-title">Halo, {{ $firstName }} 👋</h4>
    <p class="greeting-sub">Pantau dan review pengajuan pengadaan dari Kepala Laboratorium</p>
</div>

<div class="flex flex-wrap -mx-3 mb-0">
    <div class="w-full max-w-full px-3 mb-4 sm:w-1/2 xl:w-1/4 xl:mb-0">
        <div class="stat-card">
            <div class="stat-icon si-yellow"><i class="fas fa-hourglass-half"></i></div>
            <div>
                <p class="stat-label">Menunggu Review</p>
                <h3 class="stat-number">{{ $p['submitted'] ?? 0 }}</h3>
                <p class="stat-sub">Perlu keputusan Anda</p>
            </div>
        </div>
    </div>
    <div class="w-full max-w-full px-3 mb-4 sm:w-1/2 xl:w-1/4 xl:mb-0">
        <div class="stat-card">
            <div class="stat-icon si-blue"><i class="fas fa-check-double"></i></div>
            <div>
                <p class="stat-label">Total Disetujui</p>
                <h3 class="stat-number">{{ $p['approved'] ?? 0 }}</h3>
                <p class="stat-sub">Diteruskan ke Staf Admin</p>
            </div>
        </div>
    </div>
    <div class="w-full max-w-full px-3 mb-4 sm:w-1/2 xl:w-1/4 xl:mb-0">
        <div class="stat-card">
            <div class="stat-icon si-red"><i class="fas fa-times-circle"></i></div>
            <div>
                <p class="stat-label">Total Ditolak</p>
                <h3 class="stat-number">{{ $p['rejected'] ?? 0 }}</h3>
                <p class="stat-sub">Dikembalikan ke Kalab</p>
            </div>
        </div>
    </div>
    <div class="w-full max-w-full px-3 mb-4 sm:w-1/2 xl:w-1/4 xl:mb-0">
        <div class="stat-card">
            <div class="stat-icon si-gray"><i class="fas fa-folder-open"></i></div>
            <div>
                <p class="stat-label">Total Pengajuan</p>
                <h3 class="stat-number">{{ $p['total'] ?? 0 }}</h3>
                <p class="stat-sub">Semua status</p>
            </div>
        </div>
    </div>
</div>

@if(($p['submitted'] ?? 0) > 0)
<div class="review-alert">
    <i class="fas fa-bell" style="color:#d97706;"></i>
    <span>Ada <strong>{{ $p['submitted'] }}</strong> draf pengadaan yang menunggu keputusan Anda.</span>
    <a href="{{ route('kaprodi.procurement.index') }}" class="btn-review">
        <i class="fas fa-tasks"></i> Review Sekarang
    </a>
</div>
@endif

<div class="section-card">
    <div class="section-card-header">
        <h6 class="section-card-title"><i class="fas fa-bolt"></i> Akses Cepat</h6>
    </div>
    <div class="quicklinks">
        <a href="{{ route('kaprodi.procurement.index') }}" class="quicklink-btn">
            <div class="qi" style="background:linear-gradient(310deg,#7928ca,#ff007f);"><i class="fas fa-tasks"></i></div>
            <div>
                <div style="font-size:.875rem;font-weight:600;color:#344767;">Review Pengadaan</div>
                <div style="font-size:.75rem;color:{{ ($p['submitted'] ?? 0) > 0 ? '#d97706' : '#adb5bd' }};">
                    {{ ($p['submitted'] ?? 0) > 0 ? ($p['submitted']).' menunggu review' : 'Tidak ada yang perlu direview' }}
                </div>
            </div>
        </a>
        <a href="{{ route('kaprodi.riwayat.index') }}" class="quicklink-btn">
            <div class="qi" style="background:linear-gradient(310deg,#2152ff,#21d4fd);"><i class="fas fa-history"></i></div>
            <div>
                <div style="font-size:.875rem;font-weight:600;color:#344767;">Riwayat Disetujui</div>
                <div style="font-size:.75rem;color:#adb5bd;">{{ $p['approved'] ?? 0 }} draf disetujui</div>
            </div>
        </a>
    </div>
</div>

@endsection
