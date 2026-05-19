@extends('dashboard.layout')
@section('title', 'Dashboard Kepala Laboratorium')
@section('content')
<div class="cards-grid">
    <div class="card">
        <div class="card-icon">🔬</div>
        <h3>Inventaris Aset Lab</h3>
        <p>Kelola dan pantau seluruh aset laboratorium.</p>
    </div>
    <div class="card">
        <div class="card-icon">📋</div>
        <h3>Persetujuan Pengadaan</h3>
        <p>Tinjau dan setujui pengajuan pengadaan barang.</p>
    </div>
    <div class="card">
        <div class="card-icon">📈</div>
        <h3>Laporan Kondisi Aset</h3>
        <p>Lihat laporan kondisi dan status aset lab.</p>
    </div>
    <div class="card">
        <div class="card-icon">🗓️</div>
        <h3>Jadwal Pemeliharaan</h3>
        <p>Pantau jadwal perawatan berkala peralatan lab.</p>
    </div>
</div>
@endsection
