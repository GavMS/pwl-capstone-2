<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>@yield('title', 'Dashboard') - AsetLab</title>
    <!-- Fonts and icons -->
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700" rel="stylesheet" />
    <!-- Font Awesome Icons -->
    <script src="https://kit.fontawesome.com/42d5adcbca.js" crossorigin="anonymous"></script>
    <!-- Nucleo Icons -->
    <link href="{{ asset('assets/css/nucleo-icons.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/css/nucleo-svg.css') }}" rel="stylesheet" />
    <!-- Popper -->
    <script src="https://unpkg.com/@popperjs/core@2"></script>
    <!-- Main Styling -->
    <link href="{{ asset('assets/css/soft-ui-dashboard-tailwind.css?v=1.0.5') }}" rel="stylesheet" />
    
    <style>
        /* --- Original active states (kept) --- */
        .active-nav-item {
            background-color: #ffffff;
            box-shadow: 0 6px 18px -8px rgba(20, 20, 43, 0.10);
            border-radius: 0.5rem;
            font-weight: 600;
            color: #344767 !important;
        }
        .active-icon {
            background-image: linear-gradient(310deg, #7928ca, #ff007f) !important;
        }

        /* --- Subtle dashboard refinements (visual polish only) --- */

        /* Page background: very light, soft tone */
        body {
            background-color: #f7f8fc;
        }

        /* Sidebar: refined shadow + spacing */
        aside {
            box-shadow: 0 8px 24px -16px rgba(15, 23, 42, 0.08);
        }
        aside a.block.px-8.py-6 {
            padding-top: 1.25rem;
            padding-bottom: 1.25rem;
        }

        /* Section header in sidebar: calmer */
        aside h6.uppercase {
            color: #94a3b8;
            opacity: 1 !important;
            font-size: 0.6875rem;
            letter-spacing: 0.06em;
            margin-bottom: 0.25rem;
        }

        /* Sidebar nav items: softer hover for inactive items */
        aside ul li a {
            border-radius: 0.5rem;
            transition: background-color .15s ease, color .15s ease;
        }
        aside ul li a:not(.active-nav-item):hover {
            background-color: #f5f6fb;
            color: #344767 !important;
        }

        /* Sidebar icon containers: lighter shadow */
        aside ul li a > div.shadow-soft-md {
            box-shadow: 0 2px 6px -2px rgba(20, 20, 43, 0.08) !important;
        }

        /* Sidebar footer user card: keep gradient but soften the white logout button */
        aside .bg-gradient-to-tl.from-purple-700 button[type="submit"] {
            letter-spacing: 0.04em;
            border-radius: 0.5rem !important;
            box-shadow: 0 4px 10px -4px rgba(0, 0, 0, 0.15) !important;
            transition: transform .15s ease, box-shadow .15s ease;
        }
        aside .bg-gradient-to-tl.from-purple-700 button[type="submit"]:hover {
            transform: translateY(-1px);
        }

        /* Top navbar: comfortable spacing, refined search */
        main > nav {
            padding-top: 0.5rem;
            padding-bottom: 0.5rem;
        }
        main > nav ol li a {
            transition: color .15s ease;
        }
        main > nav h6 {
            color: #344767;
            letter-spacing: -0.005em;
            margin-top: 0.125rem;
        }

        /* Search input refinements */
        main > nav .relative.flex.flex-wrap.items-stretch {
            border-color: #e2e8f0 !important;
            border-radius: 0.625rem !important;
            transition: border-color .15s ease, box-shadow .15s ease;
        }
        main > nav .relative.flex.flex-wrap.items-stretch:focus-within {
            border-color: #c4b5fd !important;
            box-shadow: 0 0 0 4px rgba(139, 92, 246, 0.10);
        }
        main > nav input[type="text"]::placeholder {
            color: #94a3b8;
        }

        /* User greeting + role badge: better breathing room */
        main > nav ul li.flex.items-center.pl-4 span.role-badge {
            margin-left: 0.25rem;
            padding: 0.3rem 0.65rem;
            font-weight: 600;
            letter-spacing: 0.02em;
            box-shadow: 0 4px 10px -4px rgba(124, 58, 237, 0.35) !important;
        }

        /* Content cards: softer shadow, gentler hover */
        main .relative.flex.flex-col.bg-white.shadow-soft-xl.rounded-2xl {
            box-shadow:
                0 1px 2px rgba(16, 24, 40, 0.04),
                0 8px 24px -16px rgba(16, 24, 40, 0.10) !important;
            border: 1px solid #eef0f5;
            transition: transform .18s ease, box-shadow .18s ease !important;
        }
        main .relative.flex.flex-col.bg-white.shadow-soft-xl.rounded-2xl:hover {
            transform: translateY(-2px);
            box-shadow:
                0 1px 2px rgba(16, 24, 40, 0.04),
                0 14px 30px -16px rgba(16, 24, 40, 0.14) !important;
        }

        /* Card titles: tighter tracking, consistent size */
        main .relative.flex.flex-col.bg-white h5.text-lg {
            color: #344767;
            letter-spacing: -0.005em;
            font-weight: 700;
        }

        /* Card icon container: gentler shadow */
        main .relative.flex.flex-col.bg-white .shadow-soft-2xl {
            box-shadow: 0 6px 16px -8px rgba(121, 40, 202, 0.45) !important;
        }

        /* Footer text: lower contrast */
        footer .text-slate-500 {
            color: #94a3b8 !important;
        }

        /* --- Extra card polish (applies to all role dashboards) --- */

        /* Tighter, consistent card body */
        main .relative.flex.flex-col.bg-white.shadow-soft-xl.rounded-2xl > .flex-auto.p-4 {
            padding: 1.125rem 1.25rem;
        }

        /* Consistent card minimum height so the grid feels rhythmic */
        main .relative.flex.flex-col.bg-white.shadow-soft-xl.rounded-2xl {
            min-height: 92px;
        }

        /* Card icon tile: slightly smaller + cleaner rounding */
        main .relative.flex.flex-col.bg-white .inline-block.w-12.h-12.rounded-lg {
            width: 2.5rem !important;
            height: 2.5rem !important;
            border-radius: 0.625rem !important;
        }
        main .relative.flex.flex-col.bg-white .inline-block.w-12.h-12.rounded-lg i {
            font-size: 0.95rem !important;
        }

        /* Card title: more refined size + tracking */
        main .relative.flex.flex-col.bg-white h5.text-lg {
            font-size: 1rem;
            line-height: 1.35;
        }

        /* Consistent vertical spacing between card rows on wide screens */
        main .flex.flex-wrap.-mx-3 > [class*="w-1/4"],
        main .flex.flex-wrap.-mx-3 > [class*="sm:w-1/2"] {
            margin-bottom: 1.25rem;
        }

        /* Remove the inline hover translate flicker — keep just the CSS-driven lift */
        main .relative.flex.flex-col.bg-white.shadow-soft-xl.rounded-2xl.hover\:-translate-y-1:hover {
            --tw-translate-y: 0;
        }
    </style>
</head>

<body class="m-0 font-sans text-base antialiased font-normal leading-default bg-gray-50 text-slate-500">
    <!-- sidenav -->
    <aside class="max-w-62.5 ease-nav-brand z-990 fixed inset-y-0 my-4 ml-4 block w-full -translate-x-full flex-wrap items-center justify-between overflow-y-auto rounded-2xl border-0 bg-white p-0 antialiased shadow-none transition-transform duration-200 xl:left-0 xl:translate-x-0 xl:bg-transparent">
        <div class="h-19.5">
            <i class="absolute top-0 right-0 hidden p-4 opacity-50 cursor-pointer fas fa-times text-slate-400 xl:hidden" sidenav-close></i>
            <a class="block px-8 py-6 m-0 text-sm whitespace-nowrap text-slate-700" href="#">
                <span class="ml-1 font-bold text-lg text-slate-800 bg-gradient-to-tl from-purple-700 to-pink-500 bg-clip-text text-transparent">AsetLab Capstone</span>
            </a>
        </div>

        <hr class="h-px mt-0 bg-transparent bg-gradient-to-r from-transparent via-black/40 to-transparent" />

        <div class="items-center block w-auto max-h-screen overflow-auto h-sidenav grow basis-full">
            <ul class="flex flex-col pl-0 mb-0">
                <li class="mt-0.5 w-full">
                    <a class="py-2.7 text-sm ease-nav-brand my-0 mx-4 flex items-center whitespace-nowrap px-4 transition-colors active-nav-item" href="#">
                        <div class="shadow-soft-2xl mr-2 flex h-8 w-8 items-center justify-center rounded-lg bg-white bg-center stroke-0 text-center xl:p-2.5 active-icon">
                            <i class="fas fa-home text-white text-xs"></i>
                        </div>
                        <span class="ml-1 duration-300 opacity-100 pointer-events-none ease-soft">Dashboard</span>
                    </a>
                </li>

                <!-- Section: Menu Utama -->
                <li class="w-full mt-4">
                    <h6 class="pl-6 ml-2 text-xs font-bold leading-tight uppercase opacity-60">Menu Aset Lab</h6>
                </li>

                @if(isset($user['role']) && $user['role'] === 'Administrator')
                <li class="mt-0.5 w-full">
                    <a class="py-2.7 text-sm ease-nav-brand my-0 mx-4 flex items-center whitespace-nowrap px-4 transition-colors text-slate-600 hover:text-slate-900 {{ request()->routeIs('admin.users.*') ? 'active-nav-item' : '' }}"
                       href="{{ route('admin.users.index') }}">
                        <div class="shadow-soft-md mr-2 flex h-8 w-8 items-center justify-center rounded-lg bg-white bg-center stroke-0 text-center xl:p-2.5 {{ request()->routeIs('admin.users.*') ? 'active-icon' : '' }}">
                            <i class="fas fa-users {{ request()->routeIs('admin.users.*') ? 'text-white' : 'text-slate-700' }} text-xs"></i>
                        </div>
                        <span class="ml-1 duration-300 opacity-100 pointer-events-none ease-soft">Manajemen User</span>
                    </a>
                </li>
                @endif

                @if(isset($user['role']) && $user['role'] === 'Administrator')
                <li class="mt-0.5 w-full">
                    <a class="py-2.7 text-sm ease-nav-brand my-0 mx-4 flex items-center whitespace-nowrap px-4 transition-colors text-slate-600 hover:text-slate-900 {{ request()->routeIs('admin.rooms.*') ? 'active-nav-item' : '' }}"
                       href="{{ route('admin.rooms.index') }}">
                        <div class="shadow-soft-md mr-2 flex h-8 w-8 items-center justify-center rounded-lg bg-white bg-center stroke-0 text-center xl:p-2.5 {{ request()->routeIs('admin.rooms.*') ? 'active-icon' : '' }}">
                            <i class="fas fa-door-open {{ request()->routeIs('admin.rooms.*') ? 'text-white' : 'text-slate-700' }} text-xs"></i>
                        </div>
                        <span class="ml-1 duration-300 opacity-100 pointer-events-none ease-soft">Manajemen Ruang</span>
                    </a>
                </li>
                @endif

                @if(isset($user['role']) && $user['role'] === 'Administrator')
                <li class="mt-0.5 w-full">
                    <a class="py-2.7 text-sm ease-nav-brand my-0 mx-4 flex items-center whitespace-nowrap px-4 transition-colors text-slate-600 hover:text-slate-900" href="#">
                        <div class="shadow-soft-md mr-2 flex h-8 w-8 items-center justify-center rounded-lg bg-white bg-center stroke-0 text-center xl:p-2.5">
                            <i class="fas fa-boxes text-slate-700 text-xs"></i>
                        </div>
                        <span class="ml-1 duration-300 opacity-100 pointer-events-none ease-soft">Manajemen Aset</span>
                    </a>
                </li>
                @endif

                @if(isset($user['role']) && in_array($user['role'], ['Administrator', 'Kepala Laboratorium', 'Ketua Program Studi']))
                <li class="mt-0.5 w-full">
                    <a class="py-2.7 text-sm ease-nav-brand my-0 mx-4 flex items-center whitespace-nowrap px-4 transition-colors text-slate-600 hover:text-slate-900" href="#">
                        <div class="shadow-soft-md mr-2 flex h-8 w-8 items-center justify-center rounded-lg bg-white bg-center stroke-0 text-center xl:p-2.5">
                            <i class="fas fa-chart-line text-slate-700 text-xs"></i>
                        </div>
                        <span class="ml-1 duration-300 opacity-100 pointer-events-none ease-soft">Laporan & Statistik</span>
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
            </ul>
        </div>

        <!-- Sidebar Footer Info User -->
        <div class="mx-4 my-4">
            <div class="relative flex flex-col min-w-0 break-words rounded-2xl border-0 border-solid bg-gradient-to-tl from-purple-700 to-pink-500 bg-clip-border shadow-none p-4 text-white">
                <div class="flex-auto text-left text-white">
                    <div class="flex items-center justify-center w-8 h-8 mb-3 text-center bg-white bg-center rounded-lg icon shadow-soft-2xl">
                        <i class="fas fa-user-circle text-purple-700 text-lg"></i>
                    </div>
                    <div>
                        <h6 class="mb-0 text-white font-bold">{{ $user['name'] ?? 'User' }}</h6>
                        <p class="mt-0 mb-3 text-xs font-semibold leading-tight text-white/80">
                            {{ $user['role'] ?? 'Guest' }}
                        </p>
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit" class="inline-block w-full px-8 py-2 mb-0 text-xs font-bold text-center text-red-600 uppercase transition-all ease-in bg-white border-0 border-white rounded-lg shadow-soft-md bg-150 leading-pro hover:shadow-soft-2xl hover:scale-102">
                                Logout
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </aside>
    <!-- end sidenav -->

    <!-- main content -->
    <main class="ease-soft-in-out xl:ml-68.5 relative h-full max-h-screen rounded-xl transition-all duration-200">
        <!-- Navbar -->
        <nav class="relative flex flex-wrap items-center justify-between px-0 py-2 mx-6 transition-all shadow-none duration-250 ease-soft-in rounded-2xl lg:flex-nowrap lg:justify-start" navbar-main navbar-scroll="true">
            <div class="flex items-center justify-between w-full px-4 py-1 mx-auto flex-wrap-inherit">
                <nav>
                    <!-- breadcrumb -->
                    <ol class="flex flex-wrap pt-1 mr-12 bg-transparent rounded-lg sm:mr-16">
                        <li class="text-sm leading-normal">
                            <a class="opacity-50 text-slate-700" href="#">Pages</a>
                        </li>
                        <li class="text-sm pl-2 capitalize leading-normal text-slate-700 before:float-left before:pr-2 before:text-gray-600 before:content-['/']" aria-current="page">
                            Dashboard
                        </li>
                    </ol>
                    <h6 class="mb-0 font-bold capitalize">Dashboard ({{ $user['role'] ?? 'User' }})</h6>
                </nav>

                <div class="flex items-center mt-2 grow sm:mt-0 sm:mr-6 md:mr-0 lg:flex lg:basis-auto">
                    <div class="flex items-center md:ml-auto md:pr-4">
                        <div class="relative flex flex-wrap items-stretch w-full transition-all rounded-lg ease-soft bg-white border border-gray-300">
                            <span class="text-sm ease-soft leading-5.6 absolute z-50 -ml-px flex h-full items-center whitespace-nowrap rounded-lg rounded-tr-none rounded-br-none border border-r-0 border-transparent bg-transparent py-2 px-2.5 text-center font-normal text-slate-500 transition-all">
                                <i class="fas fa-search"></i>
                            </span>
                            <input type="text" class="pl-8.75 text-sm focus:shadow-soft-primary-outline ease-soft w-1/100 leading-5.6 relative -ml-px block min-w-0 flex-auto rounded-lg border border-solid border-transparent bg-transparent bg-clip-padding py-2 pr-3 text-gray-700 transition-all placeholder:text-gray-500 focus:outline-none" placeholder="Cari aset/ruang..." />
                        </div>
                    </div>
                    <ul class="flex flex-row justify-end pl-0 mb-0 list-none md-max:w-full">
                        <li class="flex items-center pl-4">
                            <span class="text-sm font-semibold text-slate-700 mr-2">Halo, {{ $user['name'] ?? 'User' }}</span>
                            <span class="role-badge bg-gradient-to-tl from-purple-700 to-pink-500 text-white text-xs px-2.5 py-1 rounded-full font-semibold shadow-soft-xl">
                                {{ $user['role'] ?? 'User' }}
                            </span>
                        </li>
                        <li class="flex items-center pl-4 xl:hidden">
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


            <!-- Page Content -->
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

<!-- plugin for charts -->
<script src="{{ asset('assets/js/plugins/chartjs.min.js') }}" async></script>
<!-- plugin for scrollbar -->
<script src="{{ asset('assets/js/plugins/perfect-scrollbar.min.js') }}" async></script>
<!-- github button -->
<script async defer src="https://buttons.github.io/buttons.js"></script>
<!-- main script file -->
<script src="{{ asset('assets/js/soft-ui-dashboard-tailwind.js?v=1.0.5') }}" async></script>
</html>
