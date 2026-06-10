<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>@yield('title', 'Dashboard') - AsetLab</title>
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700" rel="stylesheet" />
    {{-- SweetAlert2 --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" />
    {{-- Font Awesome 6 — pakai cdnjs (lebih reliable dibanding kit) --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" referrerpolicy="no-referrer" />
    <link href="{{ asset('assets/css/nucleo-icons.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/css/nucleo-svg.css') }}" rel="stylesheet" />
    <script src="https://unpkg.com/@popperjs/core@2"></script>
    <link href="{{ asset('assets/css/soft-ui-dashboard-tailwind.css?v=1.0.5') }}" rel="stylesheet" />

    <style>
        body { background-color: #f7f8fc; }

        /* ── Sidebar shell ───────────────────────────────────────────────── */
        aside {{{-- Sidebar footer: user card --}}
            background: #fff;
            border-right: 1px solid #f0f2f5;
            box-shadow: 0 20px 27px rgba(0, 0, 0, 0.05);
        }

        /* Active nav item */
        .active-nav-item {
            background-color: #fdf2ff !important;
            border-radius: 0.5rem;
            font-weight: 600;
            color: #344767 !important;
        }
        .active-icon {
            background-image: linear-gradient(310deg, #7928ca, #ff007f) !important;
        }

        /* Nav links */
        aside ul li a {
            border-radius: 0.5rem;
            transition: background-color .15s ease, color .15s ease;
        }
        aside ul li a:not(.active-nav-item):hover {
            background-color: #f5f6fb;
            color: #344767 !important;
        }

        /* Nav icon tile */
        .nav-icon {
            background-color: #f5f6fb;
            border-radius: 0.5rem;
            box-shadow: 0 1px 2px rgba(20, 20, 43, 0.04);
            transition: background-color .15s ease;
            flex-shrink: 0;
            width: 2rem;
            height: 2rem;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        aside ul li a:hover .nav-icon:not(.active-icon) {
            background-color: #eef0fa;
        }
        .nav-icon.active-icon {
            box-shadow: 0 4px 12px -2px rgba(121, 40, 202, 0.35);
        }

        /* Topbar */
        main > nav h6 {
            color: #344767;
            letter-spacing: -0.005em;
            margin-top: 0.125rem;
        }

        /* Content cards */
        main .relative.flex.flex-col.bg-white.shadow-soft-xl.rounded-2xl {
            box-shadow: 0 1px 2px rgba(16, 24, 40, 0.04), 0 8px 24px -16px rgba(16, 24, 40, 0.10) !important;
            border: 1px solid #eef0f5;
        }

        footer .text-slate-500 { color: #94a3b8 !important; }

        /* ── SweetAlert2 Customization (Soft, Rounded & Uniform) ────────────── */
        .swal-custom-popup {
            font-family: 'Open Sans', sans-serif !important;
            border-radius: 1.5rem !important; /* Ujung lebih tumpul */
            padding: 2rem !important;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1) !important;
            border: none !important;
        }
        .swal-custom-title {
            font-weight: 700 !important;
            color: #344767 !important;
            font-size: 1.35rem !important;
        }
        .swal-custom-html {
            color: #7b809a !important;
            font-size: 0.95rem !important;
        }
        .swal-btn-confirm {
            background: linear-gradient(310deg, #7928ca, #ff007f) !important;
            color: #fff !important;
            border: none !important;
            border-radius: 0.75rem !important; /* Tombol tumpul */
            font-weight: 600 !important;
            padding: 0.75rem 1.75rem !important;
            margin: 0 0.5rem !important;
            cursor: pointer;
            box-shadow: 0 4px 7px -1px rgba(121, 40, 202, 0.4) !important;
            transition: all 0.2s ease;
        }
        .swal-btn-confirm:hover {
            opacity: 0.9;
            transform: translateY(-1px);
        }
        .swal-btn-danger {
            background: linear-gradient(310deg, #f53939, #fbcf33) !important;
            box-shadow: 0 4px 7px -1px rgba(245, 57, 57, 0.4) !important;
        }
        .swal-btn-cancel {
            background: #f0f2f5 !important;
            color: #7b809a !important;
            border: none !important;
            border-radius: 0.75rem !important; /* Tombol tumpul */
            font-weight: 600 !important;
            padding: 0.75rem 1.75rem !important;
            margin: 0 0.5rem !important;
            cursor: pointer;
            transition: all 0.2s ease;
        }
        .swal-btn-cancel:hover {
            background: #e2e8f0 !important;
            color: #344767 !important;
        }

    </style>
</head>

<body class="m-0 font-sans text-base antialiased font-normal leading-default bg-gray-50 text-slate-500">
    <!-- sidenav -->
    <aside class="max-w-62.5 ease-nav-brand z-990 fixed inset-y-0 my-4 ml-4 flex flex-col w-full -translate-x-full rounded-2xl border-0 bg-white p-0 antialiased transition-transform duration-200 xl:left-0 xl:translate-x-0">

        {{-- Logo --}}
        <div class="h-19.5">
            <i class="absolute top-0 right-0 hidden p-4 opacity-50 cursor-pointer fas fa-times text-slate-400 xl:hidden" sidenav-close></i>
            <a class="block px-8 py-5 m-0 whitespace-nowrap" href="#">
                <br>
                <span class="font-bold text-xl text-slate-800 leading-tight block">AsetLab</span>
            </a>
        </div>

        <hr class="h-px mt-0 bg-transparent bg-gradient-to-r from-transparent via-black/40 to-transparent" />

        {{-- Nav items --}}
        <div class="flex-1 overflow-y-auto w-full">
            <ul class="flex flex-col pl-0 mb-0">

                {{-- Dashboard --}}
                @php
                $dashboardUrl = match($user['role'] ?? '') {
                    'Administrator'       => route('dashboard.admin'),
                    'Kepala Laboratorium' => route('dashboard.kalab'),
                    'Ketua Program Studi' => route('dashboard.kaprodi'),
                    'Staf Administrasi'   => route('dashboard.stafadmin'),
                    'Staf Laboratorium'   => route('dashboard.staflab'),
                    default               => '#',
                };
                $dashboardActive = request()->is('dashboard/*');
                @endphp
                <li class="mt-0.5 w-full">
                    <a class="py-2.7 text-sm ease-nav-brand my-0 mx-4 flex items-center whitespace-nowrap px-4 transition-colors {{ $dashboardActive ? 'active-nav-item' : 'text-slate-600 hover:text-slate-900' }}"
                       href="{{ $dashboardUrl }}">
                        <div class="nav-icon mr-2 flex h-8 w-8 items-center justify-center rounded-lg {{ $dashboardActive ? 'active-icon' : '' }}">
                            <i class="fas fa-home {{ $dashboardActive ? 'text-white' : 'text-slate-700' }} text-sm"></i>
                        </div>
                        <span class="ml-1 duration-300 opacity-100 pointer-events-none ease-soft">Dashboard</span>
                    </a>
                </li>

                {{-- Section header --}}
                <li class="w-full mt-4">
                    <h6 class="pl-6 ml-2 text-xs font-bold leading-tight uppercase" style="color:#94a3b8; letter-spacing:.06em;">Menu Utama</h6>
                </li>

                @if(isset($user['role']) && $user['role'] === 'Administrator')
                <li class="mt-0.5 w-full">
                    <a class="py-2.7 text-sm ease-nav-brand my-0 mx-4 flex items-center whitespace-nowrap px-4 transition-colors {{ request()->routeIs('admin.users.*') ? 'active-nav-item' : 'text-slate-600 hover:text-slate-900' }}"
                       href="{{ route('admin.users.index') }}">
                        <div class="nav-icon mr-2 flex h-8 w-8 items-center justify-center rounded-lg {{ request()->routeIs('admin.users.*') ? 'active-icon' : '' }}">
                            <i class="fas fa-users {{ request()->routeIs('admin.users.*') ? 'text-white' : 'text-slate-700' }} text-sm"></i>
                        </div>
                        <span class="ml-1 duration-300 opacity-100 pointer-events-none ease-soft">Manajemen Pengguna</span>
                    </a>
                </li>
                @endif

                @if(isset($user['role']) && $user['role'] === 'Administrator')
                <li class="mt-0.5 w-full">
                    <a class="py-2.7 text-sm ease-nav-brand my-0 mx-4 flex items-center whitespace-nowrap px-4 transition-colors {{ request()->routeIs('admin.rooms.*') ? 'active-nav-item' : 'text-slate-600 hover:text-slate-900' }}"
                       href="{{ route('admin.rooms.index') }}">
                        <div class="nav-icon mr-2 flex h-8 w-8 items-center justify-center rounded-lg {{ request()->routeIs('admin.rooms.*') ? 'active-icon' : '' }}">
                            <i class="fas fa-door-open {{ request()->routeIs('admin.rooms.*') ? 'text-white' : 'text-slate-700' }} text-sm"></i>
                        </div>
                        <span class="ml-1 duration-300 opacity-100 pointer-events-none ease-soft">Manajemen Ruangan</span>
                    </a>
                </li>
                @endif

                @if(isset($user['role']) && $user['role'] === 'Kepala Laboratorium')
                <li class="mt-0.5 w-full">
                    <a class="py-2.7 text-sm ease-nav-brand my-0 mx-4 flex items-center whitespace-nowrap px-4 transition-colors text-slate-600 hover:text-slate-900 {{ request()->routeIs('kalab.procurement.*') ? 'active-nav-item' : '' }}"
                       href="{{ route('kalab.procurement.index') }}">
                        <div class="shadow-soft-md mr-2 flex h-8 w-8 items-center justify-center rounded-lg bg-white bg-center stroke-0 text-center xl:p-2.5 {{ request()->routeIs('kalab.procurement.*') ? 'active-icon' : '' }}">
                            <i class="fas fa-file-invoice {{ request()->routeIs('kalab.procurement.*') ? 'text-white' : 'text-slate-700' }} text-xs"></i>
                        </div>
                        <span class="ml-1 duration-300 opacity-100 pointer-events-none ease-soft">Draf Pengadaan</span>
                    </a>
                </li>
                @endif

                @if(isset($user['role']) && $user['role'] === 'Ketua Program Studi')
                <li class="mt-0.5 w-full">
                    <a class="py-2.7 text-sm ease-nav-brand my-0 mx-4 flex items-center whitespace-nowrap px-4 transition-colors {{ request()->routeIs('kaprodi.procurement.*') ? 'active-nav-item' : 'text-slate-600 hover:text-slate-900' }}"
                       href="{{ route('kaprodi.procurement.index') }}">
                        <div class="shadow-soft-md mr-2 flex h-8 w-8 items-center justify-center rounded-lg bg-white bg-center stroke-0 text-center xl:p-2.5 {{ request()->routeIs('kaprodi.procurement.*') ? 'active-icon' : '' }}">
                            <i class="fas fa-tasks {{ request()->routeIs('kaprodi.procurement.*') ? 'text-white' : 'text-slate-700' }} text-xs"></i>
                        </div>
                        <span class="ml-1 duration-300 opacity-100 pointer-events-none ease-soft">Review Pengadaan</span>
                    </a>
                </li>
                <li class="mt-0.5 w-full">
                    <a class="py-2.7 text-sm ease-nav-brand my-0 mx-4 flex items-center whitespace-nowrap px-4 transition-colors {{ request()->routeIs('kaprodi.riwayat.*') ? 'active-nav-item' : 'text-slate-600 hover:text-slate-900' }}"
                       href="{{ route('kaprodi.riwayat.index') }}">
                        <div class="shadow-soft-md mr-2 flex h-8 w-8 items-center justify-center rounded-lg bg-white bg-center stroke-0 text-center xl:p-2.5 {{ request()->routeIs('kaprodi.riwayat.*') ? 'active-icon' : '' }}">
                            <i class="fas fa-history {{ request()->routeIs('kaprodi.riwayat.*') ? 'text-white' : 'text-slate-700' }} text-xs"></i>
                        </div>
                        <span class="ml-1 duration-300 opacity-100 pointer-events-none ease-soft">Riwayat Disetujui</span>
                    </a>
                </li>
                @endif

                @if(isset($user['role']) && $user['role'] === 'Staf Administrasi')
                <li class="mt-0.5 w-full">
                    <a class="py-2.7 text-sm ease-nav-brand my-0 mx-4 flex items-center whitespace-nowrap px-4 transition-colors text-slate-600 hover:text-slate-900 {{ request()->routeIs('stafadmin.procurement.*') ? 'active-nav-item' : '' }}"
                       href="{{ route('stafadmin.procurement.index') }}">
                        <div class="shadow-soft-md mr-2 flex h-8 w-8 items-center justify-center rounded-lg bg-white bg-center stroke-0 text-center xl:p-2.5 {{ request()->routeIs('stafadmin.procurement.*') ? 'active-icon' : '' }}">
                            <i class="fas fa-clipboard-check {{ request()->routeIs('stafadmin.procurement.*') ? 'text-white' : 'text-slate-700' }} text-xs"></i>
                        </div>
                        <span class="ml-1 duration-300 opacity-100 pointer-events-none ease-soft">Draf Disetujui</span>
                    </a>
                </li>
                <li class="mt-0.5 w-full">
                    <a class="py-2.7 text-sm ease-nav-brand my-0 mx-4 flex items-center whitespace-nowrap px-4 transition-colors text-slate-600 hover:text-slate-900 {{ request()->routeIs('stafadmin.inventaris.*') ? 'active-nav-item' : '' }}"
                       href="{{ route('stafadmin.inventaris.index') }}">
                        <div class="shadow-soft-md mr-2 flex h-8 w-8 items-center justify-center rounded-lg bg-white bg-center stroke-0 text-center xl:p-2.5 {{ request()->routeIs('stafadmin.inventaris.*') ? 'active-icon' : '' }}">
                            <i class="fas fa-qrcode {{ request()->routeIs('stafadmin.inventaris.*') ? 'text-white' : 'text-slate-700' }} text-xs"></i>
                        </div>
                        <span class="ml-1 duration-300 opacity-100 pointer-events-none ease-soft">Labeling Inventaris</span>
                    </a>
                </li>
                @endif
                
                @if(isset($user['role']) && $user['role'] === 'Staf Laboratorium')
                <li class="mt-0.5 w-full">
                    <a class="py-2.7 text-sm ease-nav-brand my-0 mx-4 flex items-center whitespace-nowrap px-4 transition-colors {{ request()->routeIs('staflab.bhp.*') ? 'active-nav-item' : 'text-slate-600 hover:text-slate-900' }}"
                       href="{{ route('staflab.bhp.index') }}">
                        <div class="nav-icon mr-2 flex h-8 w-8 items-center justify-center rounded-lg {{ request()->routeIs('staflab.bhp.*') ? 'active-icon' : '' }}">
                            <i class="fas fa-flask {{ request()->routeIs('staflab.bhp.*') ? 'text-white' : 'text-slate-700' }} text-sm"></i>
                        </div>
                        <span class="ml-1 duration-300 opacity-100 pointer-events-none ease-soft">Manajemen BHP</span>
                    </a>
                </li>
                <li class="mt-0.5 w-full">
                    <a class="py-2.7 text-sm ease-nav-brand my-0 mx-4 flex items-center whitespace-nowrap px-4 transition-colors {{ request()->routeIs('staflab.inventaris.*') ? 'active-nav-item' : 'text-slate-600 hover:text-slate-900' }}"
                       href="{{ route('staflab.inventaris.index') }}">
                        <div class="nav-icon mr-2 flex h-8 w-8 items-center justify-center rounded-lg {{ request()->routeIs('staflab.inventaris.*') ? 'active-icon' : '' }}">
                            <i class="fas fa-tools {{ request()->routeIs('staflab.inventaris.*') ? 'text-white' : 'text-slate-700' }} text-sm"></i>
                        </div>
                        <span class="ml-1 duration-300 opacity-100 pointer-events-none ease-soft">Inventaris & Maintenance</span>
                    </a>
                </li>
                <li class="mt-0.5 w-full">
                    <a class="py-2.7 text-sm ease-nav-brand my-0 mx-4 flex items-center whitespace-nowrap px-4 transition-colors {{ request()->routeIs('staflab.maintenance.*') ? 'active-nav-item' : 'text-slate-600 hover:text-slate-900' }}"
                       href="{{ route('staflab.maintenance.index') }}">
                        <div class="nav-icon mr-2 flex h-8 w-8 items-center justify-center rounded-lg {{ request()->routeIs('staflab.maintenance.*') ? 'active-icon' : '' }}">
                            <i class="fas fa-history {{ request()->routeIs('staflab.maintenance.*') ? 'text-white' : 'text-slate-700' }} text-sm"></i>
                        </div>
                        <span class="ml-1 duration-300 opacity-100 pointer-events-none ease-soft">Riwayat Maintenance</span>
                    </a>
                </li>
                @endif
                
            </ul>
        </div>

        {{-- Sidebar footer: user card --}}
        <div class="mx-4 my-4 px-3 py-3 rounded-2xl border border-slate-100 bg-white" style="box-shadow:0 1px 4px rgba(0,0,0,0.05);">
            <div class="flex items-center justify-between gap-2">
                <div class="flex items-center gap-2.5 flex-1 min-w-0">
                    <div class="flex-shrink-0 w-9 h-9 rounded-full flex items-center justify-center text-white text-xs font-bold" style="background:linear-gradient(310deg,#7928ca,#ff007f);">
                        {{ strtoupper(substr($user['name'] ?? 'U', 0, 2)) }}
                    </div>
                    <div class="flex-1 min-w-0" style="margin-left: 12px;">
                        <p class="text-sm font-semibold text-slate-700 mb-0 leading-tight truncate">{{ $user['name'] ?? 'User' }}</p>
                        <p class="text-xs text-slate-400 mb-0 truncate">{{ $user['role'] ?? 'Guest' }}</p>
                    </div>
                </div>
                <form action="{{ route('logout') }}" method="POST" class="flex-shrink-0">
                    @csrf
                    <button type="submit" title="Logout"
                        style="background:none;border:none;cursor:pointer;color:#94a3b8;padding:4px;transition:color .15s;"
                        onmouseover="this.style.color='#ef4444'" onmouseout="this.style.color='#94a3b8'">
                        <i class="fas fa-sign-out-alt text-sm"></i>
                    </button>
                </form>
            </div>
        </div>
    </aside>
    <!-- end sidenav -->

    <!-- main content -->
    <main class="ease-soft-in-out xl:ml-68.5 relative h-full max-h-screen rounded-xl transition-all duration-200">
        <!-- Navbar -->
        <nav class="relative flex flex-wrap items-center justify-between px-0 py-2 mx-6 transition-all shadow-none duration-250 ease-soft-in rounded-2xl lg:flex-nowrap lg:justify-start" navbar-main navbar-scroll="true">
            <div class="flex items-center justify-between w-full px-4 py-1 mx-auto flex-wrap-inherit">

                <div class="flex items-center mt-2 grow sm:mt-0 lg:flex lg:basis-auto justify-end">
                    <ul class="flex flex-row justify-end pl-0 mb-0 list-none">
                        {{-- Mobile sidenav trigger --}}
                        <li class="flex items-center xl:hidden">
                            <a href="javascript:;" class="block p-0 text-sm transition-all ease-nav-brand text-slate-500" sidenav-trigger>
                                <div class="w-4.5 overflow-hidden">
                                    <i class="ease-soft mb-0.75 relative block h-0.5 rounded-sm bg-slate-500 transition-all"></i>
                                    <i class="ease-soft mb-0.75 relative block h-0.5 rounded-sm bg-slate-500 transition-all"></i>
                                    <i class="ease-soft relative block h-0.5 rounded-sm bg-slate-500 transition-all"></i>
                                </div>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
        <!-- end Navbar -->

        <!-- Container -->
        <div class="w-full px-6 py-6 mx-auto">

            @yield('content')

            <!-- Footer -->
            <footer class="pt-12">
                <div class="w-full px-6 mx-auto">
                    <div class="flex flex-wrap items-center -mx-3 lg:justify-between">
                        <div class="w-full max-w-full px-3 mt-0 mb-6 shrink-0 lg:mb-0 lg:w-1/2 lg:flex-none">
                            <div class="text-sm leading-normal text-center text-slate-500 lg:text-left">
                                © {{ date('Y') }}
                                <a href="#" class="font-semibold text-slate-700">AsetLab</a>
                                — Digitalisasi Aset Lab.
                            </div>
                        </div>
                    </div>
                </div>
            </footer>
        </div>
    </main>
</body>

<script src="{{ asset('assets/js/plugins/chartjs.min.js') }}" async></script>
<script src="{{ asset('assets/js/plugins/perfect-scrollbar.min.js') }}" async></script>
<script async defer src="https://buttons.github.io/buttons.js"></script>
<script src="{{ asset('assets/js/soft-ui-dashboard-tailwind.js?v=1.0.5') }}" async></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@yield('scripts')
</html>
