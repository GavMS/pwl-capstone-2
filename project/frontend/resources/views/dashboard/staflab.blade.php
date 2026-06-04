@extends('dashboard.layout')
@section('title', 'Dashboard Staf Laboratorium')
@section('page_title', 'Dashboard')

@section('content')
@php $firstName = explode(' ', $user['name'] ?? 'User')[0]; @endphp

<style>
.greeting-title { font-size: 1.5rem; font-weight: 700; color: #344767; margin: 0 0 .25rem; }
.greeting-sub   { font-size: .875rem; color: #7b809a; margin: 0; }
.info-card {
    background: #fff; border-radius: 1rem; border: 1px solid #eef0f5;
    box-shadow: 0 1px 3px rgba(0,0,0,.04), 0 8px 20px -12px rgba(0,0,0,.08);
    padding: 3rem 2rem; text-align: center; margin-top: 1.5rem;
}
</style>

<div style="margin-bottom:1.5rem;">
    <h4 class="greeting-title">Halo, {{ $firstName }} 👋</h4>
    <p class="greeting-sub">Selamat datang di sistem AsetLab</p>
</div>

<div class="info-card">
    <i class="fas fa-tools" style="font-size:3rem; color:#d2d6da; display:block; margin-bottom:1rem;"></i>
    <h5 style="font-size:1.1rem; font-weight:700; color:#344767; margin-bottom:.5rem;">Staf Laboratorium</h5>
    <p style="font-size:.875rem; color:#7b809a; max-width:420px; margin:0 auto;">
        Gunakan sidebar untuk mengakses fitur yang tersedia sesuai kewenangan Anda.
        Hubungi administrator jika membutuhkan akses tambahan.
    </p>
</div>

@endsection
