@extends('dashboard.layout')
@section('title', 'Dashboard Ketua Program Studi')
@section('page_title', 'Dashboard')
@section('content')
<div class="flex flex-wrap -mx-3">
    <!-- card 1: Kebutuhan Lab Prodi -->
    <div class="w-full max-w-full px-3 mb-6 sm:w-1/2 sm:flex-none xl:mb-0 xl:w-1/4">
        <div class="relative flex flex-col min-w-0 break-words bg-white shadow-soft-xl rounded-2xl bg-clip-border hover:-translate-y-1 transition-transform duration-200">
            <div class="flex-auto p-4">
                <div class="flex flex-row items-center justify-between -mx-3">
                    <div class="px-3">
                        <h5 class="mb-0 font-bold text-slate-700 text-lg">Kebutuhan Lab</h5>
                    </div>
                    <div class="px-3 flex items-center justify-end">
                        <div class="inline-block w-12 h-12 text-center rounded-lg bg-gradient-to-tl from-purple-700 to-pink-500 shadow-soft-2xl flex items-center justify-center">
                            <i class="fas fa-graduation-cap text-white text-lg"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- card 2: Pengajuan Pengadaan -->
    <div class="w-full max-w-full px-3 mb-6 sm:w-1/2 sm:flex-none xl:mb-0 xl:w-1/4">
        <div class="relative flex flex-col min-w-0 break-words bg-white shadow-soft-xl rounded-2xl bg-clip-border hover:-translate-y-1 transition-transform duration-200">
            <div class="flex-auto p-4">
                <div class="flex flex-row items-center justify-between -mx-3">
                    <div class="px-3">
                        <h5 class="mb-0 font-bold text-slate-700 text-lg">Buat Pengajuan</h5>
                    </div>
                    <div class="px-3 flex items-center justify-end">
                        <div class="inline-block w-12 h-12 text-center rounded-lg bg-gradient-to-tl from-purple-700 to-pink-500 shadow-soft-2xl flex items-center justify-center">
                            <i class="fas fa-file-signature text-white text-lg"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- card 3: Status Pengajuan -->
    <div class="w-full max-w-full px-3 mb-6 sm:w-1/2 sm:flex-none xl:mb-0 xl:w-1/4">
        <div class="relative flex flex-col min-w-0 break-words bg-white shadow-soft-xl rounded-2xl bg-clip-border hover:-translate-y-1 transition-transform duration-200">
            <div class="flex-auto p-4">
                <div class="flex flex-row items-center justify-between -mx-3">
                    <div class="px-3">
                        <h5 class="mb-0 font-bold text-slate-700 text-lg">Status Ajuan</h5>
                    </div>
                    <div class="px-3 flex items-center justify-end">
                        <div class="inline-block w-12 h-12 text-center rounded-lg bg-gradient-to-tl from-purple-700 to-pink-500 shadow-soft-2xl flex items-center justify-center">
                            <i class="fas fa-tasks text-white text-lg"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- card 4: Inventaris Prodi -->
    <div class="w-full max-w-full px-3 mb-6 sm:w-1/2 sm:flex-none xl:mb-0 xl:w-1/4">
        <div class="relative flex flex-col min-w-0 break-words bg-white shadow-soft-xl rounded-2xl bg-clip-border hover:-translate-y-1 transition-transform duration-200">
            <div class="flex-auto p-4">
                <div class="flex flex-row items-center justify-between -mx-3">
                    <div class="px-3">
                        <h5 class="mb-0 font-bold text-slate-700 text-lg">Aset Prodi</h5>
                    </div>
                    <div class="px-3 flex items-center justify-end">
                        <div class="inline-block w-12 h-12 text-center rounded-lg bg-gradient-to-tl from-purple-700 to-pink-500 shadow-soft-2xl flex items-center justify-center">
                            <i class="fas fa-university text-white text-lg"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
