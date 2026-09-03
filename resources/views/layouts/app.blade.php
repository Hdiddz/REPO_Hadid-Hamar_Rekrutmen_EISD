<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#0f685f">
    <title>@yield('title', 'KerjaLokal')</title>
    <script>
        (() => {
            const savedTheme = localStorage.getItem('theme');
            const dark = savedTheme === 'dark';
            document.documentElement.classList.toggle('dark', dark);
        })();
    </script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,300..600,0..1,-25..0" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-[100dvh] bg-slate-50 font-sans text-slate-900 antialiased dark:bg-slate-950 dark:text-slate-100">
    <a href="#main-content" class="sr-only z-50 rounded-lg bg-brand-700 px-4 py-2 text-white focus:not-sr-only focus:fixed focus:top-3 focus:left-3">Lewati ke konten utama</a>

    <header class="sticky top-0 z-40 border-b border-slate-200/80 bg-white/95 backdrop-blur-xl dark:border-slate-800 dark:bg-slate-950/90">
        <div class="mx-auto flex h-[72px] max-w-7xl items-center gap-5 px-4 sm:px-6 lg:px-8">
            <a href="{{ route('home') }}" class="shrink-0 rounded-lg" aria-label="Beranda KerjaLokal">
                <img src="{{ asset('logo.svg') }}" alt="KerjaLokal" class="h-9 w-auto dark:hidden">
                <img src="{{ asset('logo-white.svg') }}" alt="KerjaLokal" class="hidden h-9 w-auto dark:block">
            </a>

            <nav class="hidden flex-1 items-center gap-1 md:flex" aria-label="Navigasi utama">
                <a href="{{ route('jobs.index') }}" class="inline-flex min-h-10 items-center rounded-xl px-3.5 py-2 text-sm font-semibold transition {{ request()->routeIs('jobs.*') ? 'bg-brand-50 text-brand-800 dark:bg-brand-950 dark:text-brand-200' : 'text-slate-600 hover:bg-slate-100 dark:text-slate-300 dark:hover:bg-slate-800' }}">Cari lowongan</a>
                @auth
                    @if (auth()->user()->hasRole('jobseeker'))
                        <a href="{{ route('applications.index') }}" class="inline-flex min-h-10 items-center rounded-xl px-3.5 py-2 text-sm font-semibold transition {{ request()->routeIs('applications.*') ? 'bg-brand-50 text-brand-800 dark:bg-brand-950 dark:text-brand-200' : 'text-slate-600 hover:bg-slate-100 dark:text-slate-300 dark:hover:bg-slate-800' }}">Riwayat lamaran</a>
                    @endif
                @endauth
            </nav>

            <div class="ml-auto flex items-center gap-2">
                @guest
                    <a href="{{ route('login') }}" class="portal-button-secondary">Masuk</a>
                    <a href="{{ route('register') }}" class="portal-button-primary">Daftar</a>
                @else
                    @php
                        $dashboardRoute = match (auth()->user()->role) {
                            'admin' => 'admin.dashboard',
                            'employer' => 'employer.dashboard',
                            default => 'applications.index',
                        };
                    @endphp
                    <a href="{{ route($dashboardRoute) }}" class="portal-button-secondary hidden sm:inline-flex">
                        <span class="material-symbols-outlined text-[18px]">dashboard</span>
                        Dashboard
                    </a>
                    <div class="relative">
                        <button type="button" data-menu-toggle="main-user-menu" aria-expanded="false" aria-haspopup="true" class="flex min-h-11 items-center gap-2 rounded-xl px-2 py-1.5 transition hover:bg-slate-100 dark:hover:bg-slate-800">
                            <span class="grid h-9 w-9 place-items-center rounded-xl bg-brand-700 text-sm font-bold text-white dark:bg-brand-500 dark:text-brand-950">{{ str(auth()->user()->name)->substr(0, 1)->upper() }}</span>
                            <span class="hidden text-left sm:block">
                                <span class="block max-w-36 truncate text-sm font-semibold">{{ auth()->user()->name }}</span>
                                <span class="block text-xs text-slate-500 dark:text-slate-400">{{ match (auth()->user()->role) { 'admin' => 'Administrator', 'employer' => 'Mitra UMKM', default => 'Pencari kerja' } }}</span>
                            </span>
                            <span class="material-symbols-outlined text-[18px] text-slate-400">expand_more</span>
                        </button>
                        <div id="main-user-menu" data-menu class="absolute right-0 mt-2 hidden w-60 rounded-2xl border border-slate-200 bg-white p-2 shadow-xl dark:border-slate-700 dark:bg-slate-900">
                            @if (auth()->user()->hasRole('jobseeker'))
                                <a href="{{ route('profile.index') }}" class="flex min-h-10 items-center gap-2 rounded-xl px-3 text-sm font-medium hover:bg-slate-100 dark:hover:bg-slate-800"><span class="material-symbols-outlined text-[18px]">person</span>Profil</a>
                            @endif
                            <div class="grid grid-cols-2 gap-2 py-2" aria-label="Pilih tema">
                                <button type="button" data-theme-value="light" class="portal-button-secondary px-2" aria-pressed="false">Terang</button>
                                <button type="button" data-theme-value="dark" class="portal-button-secondary px-2" aria-pressed="false">Gelap</button>
                            </div>
                            <button type="button" data-bs-toggle="modal" data-bs-target="#logoutModal" class="flex min-h-10 w-full items-center gap-2 rounded-xl px-3 text-sm font-semibold text-rose-700 hover:bg-rose-50 dark:text-rose-300 dark:hover:bg-rose-950/40"><span class="material-symbols-outlined text-[18px]">logout</span>Keluar</button>
                        </div>
                    </div>
                @endguest
            </div>
        </div>
    </header>

    <x-flash-messages />

    <main id="main-content" class="mx-auto min-h-[calc(100dvh-145px)] w-full max-w-7xl px-4 py-7 sm:px-6 lg:px-8 lg:py-10">
        @yield('content')
    </main>

    <footer class="border-t border-slate-200 bg-white dark:border-slate-800 dark:bg-slate-900">
        <div class="mx-auto flex max-w-7xl flex-col gap-1 px-4 py-5 text-xs text-slate-500 sm:flex-row sm:justify-between sm:px-6 lg:px-8 dark:text-slate-400">
            <span>&copy; {{ now()->year }} KerjaLokal</span>
            <span>SDG 8: Pekerjaan Layak dan Pertumbuhan Ekonomi</span>
        </div>
    </footer>

    @auth
        @include('components.floating-chat')
        <x-logout-modal />
    @endauth
    @stack('scripts')
</body>
</html>
