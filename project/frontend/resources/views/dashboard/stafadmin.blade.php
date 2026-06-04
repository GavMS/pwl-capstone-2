@extends('dashboard.layout')
@section('title', 'Dashboard Staf Administrasi')
@section('page_title', 'Dashboard')

@section('content')
@php
    $firstName = explode(' ', $user['name'] ?? 'User')[0];
    $p = $procStats ?? [];
@endphp

<style>
.greeting-title { font-size: 1.5rem; font-weight: 700; color: #344767; margin: 0 0 .25rem; }
.greeting-sub   { font-size: .875rem; color: #7b809a; margin: 0; }
.stat-row { display: flex; flex-wrap: wrap; gap: 1rem; margin-bottom: 1.5rem; }
.stat-card {
    background: #fff; border-radius: 1rem; padding: 1.25rem 1.5rem;
    border: 1px solid #eef0f5;
    box-shadow: 0 1px 3px rgba(0,0,0,.04), 0 8px 20px -12px rgba(0,0,0,.08);
    display: flex; align-items: center; gap: 1rem;
    transition: transform .18s ease, box-shadow .18s ease;
    flex: 1; min-width: 200px;
}
.stat-card:hover { transform: translateY(-2px); box-shadow: 0 4px 8px rgba(0,0,0,.04), 0 14px 30px -12px rgba(0,0,0,.12); }
.stat-icon { width: 3rem; height: 3rem; border-radius: .75rem; display: flex; align-items: center; justify-content: center; flex-shrink: 0; font-size: 1.1rem; }
.si-blue   { background: #eff6ff; color: #1d4ed8; }
.si-green  { background: #f0fdf4; color: #15803d; }
.si-gray   { background: #f8f9fa; color: #64748b; }
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
    transition: background .15s, border-color .15s, transform .15s; flex: 1; min-width: 180px;
}
.quicklink-btn:hover { background: #f0f2f5; border-color: #d2d6da; transform: translateY(-1px); }
.qi { width: 2.25rem; height: 2.25rem; border-radius: .5rem; display: flex; align-items: center; justify-content: center; font-size: .875rem; flex-shrink: 0; color: #fff; }
</style>

<div style="margin-bottom:1.5rem;">
    <h4 class="greeting-title">Halo, {{ $firstName }} 👋</h4>
    <p class="greeting-sub">Kelola penerimaan dan labeling barang dari pengadaan yang disetujui</p>
</div>

<div class="stat-row">
    <div class="stat-card">
        <div class="stat-icon si-blue"><i class="fas fa-clipboard-check"></i></div>
        <div>
            <p class="stat-label">Pengadaan Disetujui</p>
            <h3 class="stat-number">{{ $p['approved'] ?? 0 }}</h3>
            <p class="stat-sub">Siap diproses & dilabeling</p>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon si-green"><i class="fas fa-folder-open"></i></div>
        <div>
            <p class="stat-label">Total Pengajuan</p>
            <h3 class="stat-number">{{ $p['total'] ?? 0 }}</h3>
            <p class="stat-sub">{{ $p['submitted'] ?? 0 }} menunggu review Kaprodi</p>
        </div>
    </div>
</div>

<div class="section-card">
    <div class="section-card-header">
        <h6 class="section-card-title"><i class="fas fa-bolt"></i> Akses Cepat</h6>
    </div>
    <div class="quicklinks">
        <a href="{{ route('stafadmin.procurement.index') }}" class="quicklink-btn">
            <div class="qi" style="background:linear-gradient(310deg,#2152ff,#21d4fd);"><i class="fas fa-clipboard-check"></i></div>
            <div>
                <div style="font-size:.875rem;font-weight:600;color:#344767;">Draf Disetujui</div>
                <div style="font-size:.75rem;color:#adb5bd;">{{ $p['approved'] ?? 0 }} siap diproses</div>
            </div>
        </a>
        <a href="{{ route('stafadmin.inventaris.index') }}" class="quicklink-btn">
            <div class="qi" style="background:linear-gradient(310deg,#17ad37,#98ec2c);"><i class="fas fa-qrcode"></i></div>
            <div>
                <div style="font-size:.875rem;font-weight:600;color:#344767;">Labeling Inventaris</div>
                <div style="font-size:.75rem;color:#adb5bd;">Cetak & tempel label QR</div>
            </div>
        </a>
    </div>
</div>

@endsection
