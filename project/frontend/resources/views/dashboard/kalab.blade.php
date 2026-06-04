@extends('dashboard.layout')
@section('title', 'Dashboard Kepala Laboratorium')
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
.si-orange { background: #fff7ed; color: #c2410c; }
.si-green  { background: #f0fdf4; color: #15803d; }
.si-blue   { background: #eff6ff; color: #1d4ed8; }
.stat-label  { font-size: .8125rem; color: #7b809a; margin: 0 0 .375rem; font-weight: 500; }
.stat-number { font-size: 1.75rem; font-weight: 700; color: #344767; line-height: 1; margin: 0 0 .35rem; }
.stat-sub    { font-size: .7rem; color: #adb5bd; margin: 0; }
.section-card {
    background: #fff; border-radius: 1rem; border: 1px solid #eef0f5;
    box-shadow: 0 1px 3px rgba(0,0,0,.04), 0 8px 20px -12px rgba(0,0,0,.08); overflow: hidden; margin-top: 1.5rem;
}
.section-card-header { display: flex; align-items: center; padding: 1.125rem 1.5rem; border-bottom: 1px solid #f0f2f5; }
.section-card-title { font-size: 1rem; font-weight: 700; color: #344767; margin: 0; display: flex; align-items: center; gap: .5rem; }
.section-card-title i { color: #7928ca; font-size: .8rem; }
.quicklinks { display: flex; flex-wrap: wrap; gap: 1rem; padding: 1.5rem; }
.quicklink-btn {
    display: inline-flex; align-items: center; gap: .75rem; padding: .875rem 1.25rem;
    border-radius: .75rem; text-decoration: none; background: #f8f9fa; border: 1px solid #eef0f5;
    transition: background .15s, border-color .15s, transform .15s; flex: 1; min-width: 200px;
}
.quicklink-btn:hover { background: #f0f2f5; border-color: #d2d6da; transform: translateY(-1px); }
.qi { width: 2.25rem; height: 2.25rem; border-radius: .5rem; display: flex; align-items: center; justify-content: center; font-size: .875rem; flex-shrink: 0; color: #fff; }
</style>

<div style="margin-bottom:1.5rem;">
    <h4 class="greeting-title">Halo, {{ $firstName }} 👋</h4>
    <p class="greeting-sub">Ringkasan pengadaan barang laboratorium</p>
</div>

<div class="flex flex-wrap -mx-3 mb-0">
    <div class="w-full max-w-full px-3 mb-4 sm:w-1/3 xl:mb-0">
        <div class="stat-card">
            <div class="stat-icon si-orange"><i class="fas fa-file-alt"></i></div>
            <div>
                <p class="stat-label">Draf Tersimpan</p>
                <h3 class="stat-number">{{ $p['draft'] ?? 0 }}</h3>
                <p class="stat-sub">{{ $p['total'] ?? 0 }} total draf</p>
            </div>
        </div>
    </div>
    <div class="w-full max-w-full px-3 mb-4 sm:w-1/3 xl:mb-0">
        <div class="stat-card">
            <div class="stat-icon si-green"><i class="fas fa-paper-plane"></i></div>
            <div>
                <p class="stat-label">Menunggu Review</p>
                <h3 class="stat-number">{{ $p['submitted'] ?? 0 }}</h3>
                <p class="stat-sub">Dikirim ke Kaprodi</p>
            </div>
        </div>
    </div>
    <div class="w-full max-w-full px-3 mb-4 sm:w-1/3 xl:mb-0">
        <div class="stat-card">
            <div class="stat-icon si-blue"><i class="fas fa-check-circle"></i></div>
            <div>
                <p class="stat-label">Difinalisasi Kaprodi</p>
                <h3 class="stat-number">{{ $p['approved'] ?? 0 }}</h3>
                <p class="stat-sub">{{ $p['rejected'] ?? 0 }} item ditolak</p>
            </div>
        </div>
    </div>
</div>

<div class="section-card">
    <div class="section-card-header">
        <h6 class="section-card-title"><i class="fas fa-bolt"></i> Akses Cepat</h6>
    </div>
    <div class="quicklinks">
        <a href="{{ route('kalab.procurement.create') }}" class="quicklink-btn">
            <div class="qi" style="background:linear-gradient(310deg,#7928ca,#ff007f);"><i class="fas fa-plus"></i></div>
            <div>
                <div style="font-size:.875rem;font-weight:600;color:#344767;">Buat Draf Baru</div>
                <div style="font-size:.75rem;color:#adb5bd;">Ajukan pengadaan barang</div>
            </div>
        </a>
        <a href="{{ route('kalab.procurement.index') }}" class="quicklink-btn">
            <div class="qi" style="background:linear-gradient(310deg,#2152ff,#21d4fd);"><i class="fas fa-file-invoice"></i></div>
            <div>
                <div style="font-size:.875rem;font-weight:600;color:#344767;">Semua Draf Pengadaan</div>
                <div style="font-size:.75rem;color:#adb5bd;">Kelola dan pantau status</div>
            </div>
        </a>
    </div>
</div>

@endsection
