@extends('dashboard.layout')
@section('title', 'Dashboard Staf Administrasi')
@section('content')
<div class="cards-grid">
    <div class="card">
        <div class="card-icon">📝</div>
        <h3>Dokumen Pengadaan</h3>
        <p>Kelola dokumen dan berkas pengadaan aset.</p>
    </div>
    <div class="card">
        <div class="card-icon">📦</div>
        <h3>Penerimaan Barang</h3>
        <p>Catat dan verifikasi penerimaan barang baru.</p>
    </div>
    <div class="card">
        <div class="card-icon">📑</div>
        <h3>Arsip Dokumen</h3>
        <p>Akses dan kelola arsip dokumen administrasi.</p>
    </div>
    <div class="card">
        <div class="card-icon">📬</div>
        <h3>Notifikasi</h3>
        <p>Lihat notifikasi tugas dan pengingat administrasi.</p>
    </div>
</div>
@endsection
