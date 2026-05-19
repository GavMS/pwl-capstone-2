@extends('dashboard.layout')
@section('title', 'Dashboard Administrator')
@section('content')
<div class="cards-grid">
    <div class="card">
        <div class="card-icon">👥</div>
        <h3>Manajemen User</h3>
        <p>Kelola akun pengguna dan hak akses semua role.</p>
    </div>
    <div class="card">
        <div class="card-icon">🏛️</div>
        <h3>Manajemen Ruangan</h3>
        <p>Kelola data ruangan laboratorium.</p>
    </div>
    <div class="card">
        <div class="card-icon">📊</div>
        <h3>Laporan Sistem</h3>
        <p>Lihat seluruh laporan aktivitas sistem.</p>
    </div>
    <div class="card">
        <div class="card-icon">⚙️</div>
        <h3>Pengaturan</h3>
        <p>Konfigurasi sistem secara keseluruhan.</p>
    </div>
</div>
@endsection
