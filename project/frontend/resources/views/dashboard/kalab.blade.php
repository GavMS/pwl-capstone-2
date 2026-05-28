@extends('dashboard.layout')
@section('title', 'Dashboard Kepala Laboratorium')
@section('page_title', 'Dashboard')
@section('content')
<div class="flex flex-wrap -mx-3">
    <!-- card 1: Inventaris Aset Lab -->
    <div class="w-full max-w-full px-3 mb-6 sm:w-1/2 sm:flex-none xl:mb-0 xl:w-1/4">
        <div class="relative flex flex-col min-w-0 break-words bg-white shadow-soft-xl rounded-2xl bg-clip-border hover:-translate-y-1 transition-transform duration-200">
            <div class="flex-auto p-4">
                <div class="flex flex-row items-center justify-between -mx-3">
                    <div class="px-3">
                        <h5 class="mb-0 font-bold text-slate-700 text-lg">Inventaris Aset Lab</h5>
                    </div>
                    <div class="px-3 flex items-center justify-end">
                        <div class="inline-block w-12 h-12 text-center rounded-lg bg-gradient-to-tl from-purple-700 to-pink-500 shadow-soft-2xl flex items-center justify-center">
                            <i class="fas fa-flask text-white text-lg"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- card 2: Persetujuan Pengadaan -->
    <div class="w-full max-w-full px-3 mb-6 sm:w-1/2 sm:flex-none xl:mb-0 xl:w-1/4">
        <div class="relative flex flex-col min-w-0 break-words bg-white shadow-soft-xl rounded-2xl bg-clip-border hover:-translate-y-1 transition-transform duration-200">
            <div class="flex-auto p-4">
                <div class="flex flex-row items-center justify-between -mx-3">
                    <div class="px-3">
                        <h5 class="mb-0 font-bold text-slate-700 text-lg">Pengadaan Barang</h5>
                    </div>
                    <div class="px-3 flex items-center justify-end">
                        <div class="inline-block w-12 h-12 text-center rounded-lg bg-gradient-to-tl from-purple-700 to-pink-500 shadow-soft-2xl flex items-center justify-center">
                            <i class="fas fa-check-double text-white text-lg"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- card 3: Laporan Kondisi Aset -->
    <div class="w-full max-w-full px-3 mb-6 sm:w-1/2 sm:flex-none xl:mb-0 xl:w-1/4">
        <div class="relative flex flex-col min-w-0 break-words bg-white shadow-soft-xl rounded-2xl bg-clip-border hover:-translate-y-1 transition-transform duration-200">
            <div class="flex-auto p-4">
                <div class="flex flex-row items-center justify-between -mx-3">
                    <div class="px-3">
                        <h5 class="mb-0 font-bold text-slate-700 text-lg">Kondisi & Status</h5>
                    </div>
                    <div class="px-3 flex items-center justify-end">
                        <div class="inline-block w-12 h-12 text-center rounded-lg bg-gradient-to-tl from-purple-700 to-pink-500 shadow-soft-2xl flex items-center justify-center">
                            <i class="fas fa-chart-line text-white text-lg"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- card 4: Jadwal Pemeliharaan -->
    <div class="w-full max-w-full px-3 mb-6 sm:w-1/2 sm:flex-none xl:mb-0 xl:w-1/4">
        <div class="relative flex flex-col min-w-0 break-words bg-white shadow-soft-xl rounded-2xl bg-clip-border hover:-translate-y-1 transition-transform duration-200">
            <div class="flex-auto p-4">
                <div class="flex flex-row items-center justify-between -mx-3">
                    <div class="px-3">
                        <h5 class="mb-0 font-bold text-slate-700 text-lg">Pemeliharaan</h5>
                    </div>
                    <div class="px-3 flex items-center justify-end">
                        <div class="inline-block w-12 h-12 text-center rounded-lg bg-gradient-to-tl from-purple-700 to-pink-500 shadow-soft-2xl flex items-center justify-center">
                            <i class="fas fa-calendar-alt text-white text-lg"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
