@extends('dashboard.layout')
@section('title', 'Dashboard Administrator')
@section('content')
<div class="flex flex-wrap -mx-3">
    <!-- card 1: Manajemen User -->
    <div class="w-full max-w-full px-3 mb-6 sm:w-1/2 sm:flex-none xl:mb-0 xl:w-1/4">
        <div class="relative flex flex-col min-w-0 break-words bg-white shadow-soft-xl rounded-2xl bg-clip-border hover:-translate-y-1 transition-transform duration-200">
            <div class="flex-auto p-4">
                <div class="flex flex-row items-center justify-between -mx-3">
                    <div class="px-3">
                        <h5 class="mb-0 font-bold text-slate-700 text-lg">Manajemen User</h5>
                    </div>
                    <div class="px-3 flex items-center justify-end">
                        <div class="inline-block w-12 h-12 text-center rounded-lg bg-gradient-to-tl from-purple-700 to-pink-500 shadow-soft-2xl flex items-center justify-center">
                            <i class="fas fa-users text-white text-lg"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- card 2: Manajemen Ruangan -->
    <div class="w-full max-w-full px-3 mb-6 sm:w-1/2 sm:flex-none xl:mb-0 xl:w-1/4">
        <div class="relative flex flex-col min-w-0 break-words bg-white shadow-soft-xl rounded-2xl bg-clip-border hover:-translate-y-1 transition-transform duration-200">
            <div class="flex-auto p-4">
                <div class="flex flex-row items-center justify-between -mx-3">
                    <div class="px-3">
                        <h5 class="mb-0 font-bold text-slate-700 text-lg">Manajemen Ruang</h5>
                    </div>
                    <div class="px-3 flex items-center justify-end">
                        <div class="inline-block w-12 h-12 text-center rounded-lg bg-gradient-to-tl from-purple-700 to-pink-500 shadow-soft-2xl flex items-center justify-center">
                            <i class="fas fa-building text-white text-lg"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- card 3: Laporan Sistem -->
    <div class="w-full max-w-full px-3 mb-6 sm:w-1/2 sm:flex-none xl:mb-0 xl:w-1/4">
        <div class="relative flex flex-col min-w-0 break-words bg-white shadow-soft-xl rounded-2xl bg-clip-border hover:-translate-y-1 transition-transform duration-200">
            <div class="flex-auto p-4">
                <div class="flex flex-row items-center justify-between -mx-3">
                    <div class="px-3">
                        <h5 class="mb-0 font-bold text-slate-700 text-lg">Laporan Sistem</h5>
                    </div>
                    <div class="px-3 flex items-center justify-end">
                        <div class="inline-block w-12 h-12 text-center rounded-lg bg-gradient-to-tl from-purple-700 to-pink-500 shadow-soft-2xl flex items-center justify-center">
                            <i class="fas fa-chart-bar text-white text-lg"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- card 4: Pengaturan -->
    <div class="w-full max-w-full px-3 mb-6 sm:w-1/2 sm:flex-none xl:mb-0 xl:w-1/4">
        <div class="relative flex flex-col min-w-0 break-words bg-white shadow-soft-xl rounded-2xl bg-clip-border hover:-translate-y-1 transition-transform duration-200">
            <div class="flex-auto p-4">
                <div class="flex flex-row items-center justify-between -mx-3">
                    <div class="px-3">
                        <h5 class="mb-0 font-bold text-slate-700 text-lg">Pengaturan</h5>
                    </div>
                    <div class="px-3 flex items-center justify-end">
                        <div class="inline-block w-12 h-12 text-center rounded-lg bg-gradient-to-tl from-purple-700 to-pink-500 shadow-soft-2xl flex items-center justify-center">
                            <i class="fas fa-cogs text-white text-lg"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
