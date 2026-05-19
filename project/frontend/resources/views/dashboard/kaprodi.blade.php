@extends('dashboard.layout')
@section('title', 'Dashboard Ketua Program Studi')
@section('content')
<div class="cards-grid">
    <div class="card">
        <div class="card-icon">🎓</div>
        <h3>Kebutuhan Lab Prodi</h3>
        <p>Ajukan kebutuhan peralatan untuk program studi.</p>
    </div>
    <div class="card">
        <div class="card-icon">📄</div>
        <h3>Pengajuan Pengadaan</h3>
        <p>Buat dan kirimkan draf pengajuan pengadaan aset.</p>
    </div>
    <div class="card">
        <div class="card-icon">📊</div>
        <h3>Status Pengajuan</h3>
        <p>Pantau status persetujuan pengajuan yang telah dikirim.</p>
    </div>
    <div class="card">
        <div class="card-icon">🏫</div>
        <h3>Inventaris Prodi</h3>
        <p>Lihat daftar aset yang dimiliki program studi.</p>
    </div>
</div>
@endsection
