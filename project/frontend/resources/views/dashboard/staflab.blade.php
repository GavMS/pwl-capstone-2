@extends('dashboard.layout')
@section('title', 'Dashboard Staf Laboratorium')
@section('content')
<div class="cards-grid">
    <div class="card">
        <div class="card-icon">🔧</div>
        <h3>Pengecekan Aset</h3>
        <p>Lakukan pengecekan kondisi aset laboratorium.</p>
    </div>
    <div class="card">
        <div class="card-icon">📋</div>
        <h3>Laporan Kerusakan</h3>
        <p>Laporkan kerusakan atau masalah pada peralatan lab.</p>
    </div>
    <div class="card">
        <div class="card-icon">🗂️</div>
        <h3>Inventaris Lab Saya</h3>
        <p>Lihat daftar aset yang menjadi tanggung jawab Anda.</p>
    </div>
    <div class="card">
        <div class="card-icon">🕐</div>
        <h3>Riwayat Aktivitas</h3>
        <p>Lihat riwayat aktivitas pengelolaan aset.</p>
    </div>
</div>
@endsection
