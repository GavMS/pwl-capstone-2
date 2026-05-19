<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Register - Digitalisasi Aset Lab</title>
    <!-- Fonts and icons -->
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700" rel="stylesheet" />
    <!-- Font Awesome Icons -->
    <script src="https://kit.fontawesome.com/42d5adcbca.js" crossorigin="anonymous"></script>
    <!-- Nucleo Icons -->
    <link href="{{ asset('assets/css/nucleo-icons.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/css/nucleo-svg.css') }}" rel="stylesheet" />
    <!-- Main Styling -->
    <link href="{{ asset('assets/css/soft-ui-dashboard-tailwind.css?v=1.0.5') }}" rel="stylesheet" />
</head>

<body class="m-0 font-sans antialiased font-normal bg-white text-start text-base leading-default text-slate-500">
    <main class="mt-0 transition-all duration-200 ease-soft-in-out">
        <section class="min-h-screen mb-32">
            <!-- Curved Header Background -->
            <div class="relative flex items-start pt-12 pb-56 m-4 overflow-hidden bg-center bg-cover min-h-50-screen rounded-xl" style="background-image: url('{{ asset('assets/img/curved-images/curved14.jpg') }}');">
                <span class="absolute top-0 left-0 w-full h-full bg-center bg-cover bg-gradient-to-tl from-gray-900 to-slate-800 opacity-60"></span>
                <div class="container z-10">
                    <div class="flex flex-wrap justify-center -mx-3">
                        <div class="w-full max-w-full px-3 mx-auto mt-0 text-center lg:flex-0 shrink-0 lg:w-5/12">
                            <h1 class="mt-12 mb-2 text-white font-bold text-3xl">Registrasi Akun</h1>
                            <p class="text-white text-sm">
                                Daftarkan diri Anda sebagai Staf Laboratorium baru pada sistem Digitalisasi AsetLab.
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Registration Card -->
            <div class="container">
                <div class="flex flex-wrap -mx-3 -mt-48 md:-mt-56 lg:-mt-48">
                    <div class="w-full max-w-full px-3 mx-auto mt-0 md:flex-0 shrink-0 md:w-7/12 lg:w-5/12 xl:w-4/12">
                        <div class="relative z-0 flex flex-col min-w-0 break-words bg-white border-0 shadow-soft-xl rounded-2xl bg-clip-border">
                            <div class="p-6 mb-0 text-center bg-white border-b-0 rounded-t-2xl">
                                <h5 class="font-bold text-slate-700 text-lg">Buat Akun Baru</h5>
                            </div>

                            <div class="flex-auto p-6">
                                <!-- Alert Error -->
                                @if($errors->any())
                                    <div class="relative w-full p-3 mb-4 text-white border border-solid rounded-lg bg-gradient-to-tl from-red-600 to-rose-400 border-transparent text-xs">
                                        {{ $errors->first() }}
                                    </div>
                                @endif

                                <form method="POST" action="{{ route('register.post') }}">
                                    @csrf
                                    <!-- Full Name -->
                                    <label class="mb-1 ml-1 font-bold text-xs text-slate-700">Nama Lengkap</label>
                                    <div class="mb-4">
                                        <input type="text" name="name" class="text-sm focus:shadow-soft-primary-outline leading-5.6 ease-soft block w-full appearance-none rounded-lg border border-solid border-gray-300 bg-white bg-clip-padding py-2 px-3 font-normal text-gray-700 transition-all focus:border-purple-300 focus:outline-none" placeholder="Masukkan nama lengkap" value="{{ old('name') }}" required autofocus />
                                    </div>

                                    <!-- Email Address -->
                                    <label class="mb-1 ml-1 font-bold text-xs text-slate-700">Alamat Email</label>
                                    <div class="mb-4">
                                        <input type="email" name="email" class="text-sm focus:shadow-soft-primary-outline leading-5.6 ease-soft block w-full appearance-none rounded-lg border border-solid border-gray-300 bg-white bg-clip-padding py-2 px-3 font-normal text-gray-700 transition-all focus:border-purple-300 focus:outline-none" placeholder="Masukkan email" value="{{ old('email') }}" required />
                                    </div>

                                    <!-- Password -->
                                    <label class="mb-1 ml-1 font-bold text-xs text-slate-700">Password</label>
                                    <div class="mb-4">
                                        <input type="password" name="password" class="text-sm focus:shadow-soft-primary-outline leading-5.6 ease-soft block w-full appearance-none rounded-lg border border-solid border-gray-300 bg-white bg-clip-padding py-2 px-3 font-normal text-gray-700 transition-all focus:border-purple-300 focus:outline-none" placeholder="Masukkan password (min. 6 karakter)" required />
                                    </div>

                                    <!-- Confirm Password -->
                                    <label class="mb-1 ml-1 font-bold text-xs text-slate-700">Konfirmasi Password</label>
                                    <div class="mb-4">
                                        <input type="password" name="password_confirmation" class="text-sm focus:shadow-soft-primary-outline leading-5.6 ease-soft block w-full appearance-none rounded-lg border border-solid border-gray-300 bg-white bg-clip-padding py-2 px-3 font-normal text-gray-700 transition-all focus:border-purple-300 focus:outline-none" placeholder="Masukkan kembali password" required />
                                    </div>

                                    <!-- Button Register -->
                                    <div class="text-center">
                                        <button type="submit" class="inline-block w-full px-6 py-3 mt-6 mb-2 font-bold text-center text-white uppercase align-middle transition-all bg-transparent border-0 rounded-lg cursor-pointer active:opacity-85 hover:scale-102 hover:shadow-soft-xs leading-pro text-xs ease-soft-in tracking-tight-soft shadow-soft-md bg-150 bg-x-25 bg-gradient-to-tl from-purple-700 to-pink-500">
                                            Daftar Sekarang
                                        </button>
                                    </div>

                                    <p class="mt-4 mb-0 leading-normal text-sm text-center">
                                        Sudah punya akun?
                                        <a href="{{ route('login') }}" class="font-bold text-slate-700 hover:text-purple-700 transition-colors">
                                            Sign In
                                        </a>
                                    </p>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Footer -->
        <footer class="py-12">
            <div class="container">
                <div class="flex flex-wrap -mx-3">
                    <div class="w-8/12 max-w-full px-3 mx-auto mt-1 text-center flex-0">
                        <p class="mb-0 text-slate-400 text-xs">
                            Copyright © <script>document.write(new Date().getFullYear());</script> Soft by Creative Tim.
                            <span class="w-full"> Distributed by ❤️ ThemeWagon </span>
                        </p>
                    </div>
                </div>
            </div>
        </footer>
    </main>
</body>

<!-- plugin for scrollbar -->
<script src="{{ asset('assets/js/plugins/perfect-scrollbar.min.js') }}" async></script>
<!-- main script file -->
<script src="{{ asset('assets/js/soft-ui-dashboard-tailwind.js?v=1.0.5') }}" async></script>
</html>
