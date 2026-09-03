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
    @stack('styles')
</head>
<body class="{{ request()->routeIs('chat.*') ? 'h-[100dvh] max-h-[100dvh] overflow-hidden flex flex-col' : 'min-h-[100dvh]' }} bg-slate-50 font-sans text-slate-900 antialiased dark:bg-slate-950 dark:text-slate-100">
    <a href="#main-content" class="sr-only z-50 rounded-lg bg-brand-700 px-4 py-2 text-white focus:not-sr-only focus:fixed focus:top-3 focus:left-3">Lewati ke konten utama</a>

    <header class="shrink-0 sticky top-0 z-40 border-b border-slate-200/80 bg-white/95 backdrop-blur-xl dark:border-slate-800 dark:bg-slate-950/90">
        <div class="mx-auto flex h-[72px] max-w-7xl items-center gap-5 px-4 sm:px-6 lg:px-8">
            <a href="{{ route('home') }}" class="shrink-0 rounded-lg" aria-label="Beranda KerjaLokal">
                <img src="{{ asset('logo.svg') }}" alt="KerjaLokal" class="h-9 w-auto dark:hidden">
                <img src="{{ asset('logo-white.svg') }}" alt="KerjaLokal" class="hidden h-9 w-auto dark:block">
            </a>

            @auth
                @if (auth()->user()->hasRole('employer'))
                    <span class="hidden h-7 w-px bg-slate-200 sm:block dark:bg-slate-800"></span>
                    <span class="hidden rounded-lg bg-brand-50 px-2.5 py-1 text-xs font-semibold text-brand-800 sm:inline-flex dark:bg-brand-950 dark:text-brand-200">
                        Mitra UMKM
                    </span>
                @elseif (auth()->user()->hasRole('admin'))
                    <span class="hidden h-7 w-px bg-slate-200 sm:block dark:bg-slate-800"></span>
                    <span class="hidden rounded-lg bg-brand-50 px-2.5 py-1 text-xs font-semibold text-brand-800 sm:inline-flex dark:bg-brand-950 dark:text-brand-200">
                        Administrator
                    </span>
                @endif
            @endauth

            <nav class="hidden flex-1 items-center gap-1 md:flex" aria-label="Navigasi utama">
                @guest
                    <a href="{{ route('jobs.index') }}" class="inline-flex min-h-10 items-center gap-2 rounded-xl px-3.5 py-2 text-sm font-semibold transition {{ request()->routeIs('jobs.*') ? 'bg-brand-700 text-white dark:bg-brand-500 dark:text-brand-950' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-950 dark:text-slate-300 dark:hover:bg-slate-800 dark:hover:text-white' }}">
                        <span class="material-symbols-outlined text-[19px]">search</span>
                        Cari lowongan
                    </a>
                @else
                    @if (auth()->user()->hasRole('employer'))
                        <a href="{{ route('employer.dashboard') }}" class="inline-flex min-h-10 items-center gap-2 rounded-xl px-3.5 py-2 text-sm font-semibold transition {{ request()->routeIs('employer.dashboard') ? 'bg-brand-700 text-white dark:bg-brand-500 dark:text-brand-950' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-950 dark:text-slate-300 dark:hover:bg-slate-800 dark:hover:text-white' }}">
                            <span class="material-symbols-outlined text-[19px]">work</span>
                            Lowongan Saya
                        </a>
                        <a href="{{ route('employer.applications.index') }}" class="inline-flex min-h-10 items-center gap-2 rounded-xl px-3.5 py-2 text-sm font-semibold transition {{ request()->routeIs('employer.applications.*') ? 'bg-brand-700 text-white dark:bg-brand-500 dark:text-brand-950' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-950 dark:text-slate-300 dark:hover:bg-slate-800 dark:hover:text-white' }}">
                            <span class="material-symbols-outlined text-[19px]">group</span>
                            Pelamar
                        </a>
                        <a href="{{ route('jobs.index') }}" class="inline-flex min-h-10 items-center gap-2 rounded-xl px-3.5 py-2 text-sm font-semibold transition {{ request()->routeIs('jobs.*') ? 'bg-brand-700 text-white dark:bg-brand-500 dark:text-brand-950' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-950 dark:text-slate-300 dark:hover:bg-slate-800 dark:hover:text-white' }}">
                            <span class="material-symbols-outlined text-[19px]">travel_explore</span>
                            Lihat lowongan
                        </a>
                    @elseif (auth()->user()->hasRole('admin'))
                        <a href="{{ route('admin.dashboard') }}" class="inline-flex min-h-10 items-center gap-2 rounded-xl px-3.5 py-2 text-sm font-semibold transition {{ request()->routeIs('admin.dashboard') ? 'bg-brand-700 text-white dark:bg-brand-500 dark:text-brand-950' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-950 dark:text-slate-300 dark:hover:bg-slate-800 dark:hover:text-white' }}">
                            <span class="material-symbols-outlined text-[19px]">dashboard</span>
                            Dashboard
                        </a>
                        <a href="{{ route('admin.jobs.index') }}" class="inline-flex min-h-10 items-center gap-2 rounded-xl px-3.5 py-2 text-sm font-semibold transition {{ request()->routeIs('admin.jobs.*') ? 'bg-brand-700 text-white dark:bg-brand-500 dark:text-brand-950' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-950 dark:text-slate-300 dark:hover:bg-slate-800 dark:hover:text-white' }}">
                            <span class="material-symbols-outlined text-[19px]">work</span>
                            Lowongan
                        </a>
                        @php
                            $appPendingReports = \App\Models\JobReport::where('status', 'pending')->count();
                        @endphp
                        <a href="{{ route('admin.reports.index') }}" class="inline-flex min-h-10 items-center gap-2 rounded-xl px-3.5 py-2 text-sm font-semibold transition {{ request()->routeIs('admin.reports.*') ? 'bg-brand-700 text-white dark:bg-brand-500 dark:text-brand-950' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-950 dark:text-slate-300 dark:hover:bg-slate-800 dark:hover:text-white' }}">
                            <span class="material-symbols-outlined text-[19px]">flag</span>
                            <span>Laporan</span>
                            @if($appPendingReports > 0)
                                <span class="ml-1 rounded-full bg-rose-500 px-1.5 py-0.5 text-[10px] font-extrabold text-white">{{ $appPendingReports }}</span>
                            @endif
                        </a>
                        <a href="{{ route('admin.master') }}" class="inline-flex min-h-10 items-center gap-2 rounded-xl px-3.5 py-2 text-sm font-semibold transition {{ request()->routeIs('admin.master') ? 'bg-brand-700 text-white dark:bg-brand-500 dark:text-brand-950' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-950 dark:text-slate-300 dark:hover:bg-slate-800 dark:hover:text-white' }}">
                            <span class="material-symbols-outlined text-[19px]">database</span>
                            Master data
                        </a>
                        <a href="{{ route('admin.users.index') }}" class="inline-flex min-h-10 items-center gap-2 rounded-xl px-3.5 py-2 text-sm font-semibold transition {{ request()->routeIs('admin.users.*') ? 'bg-brand-700 text-white dark:bg-brand-500 dark:text-brand-950' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-950 dark:text-slate-300 dark:hover:bg-slate-800 dark:hover:text-white' }}">
                            <span class="material-symbols-outlined text-[19px]">group</span>
                            Pengguna
                        </a>
                    @else
                        <a href="{{ route('jobs.index') }}" class="inline-flex min-h-10 items-center gap-2 rounded-xl px-3.5 py-2 text-sm font-semibold transition {{ request()->routeIs('jobs.*') ? 'bg-brand-700 text-white dark:bg-brand-500 dark:text-brand-950' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-950 dark:text-slate-300 dark:hover:bg-slate-800 dark:hover:text-white' }}">
                            <span class="material-symbols-outlined text-[19px]">search</span>
                            Cari lowongan
                        </a>
                        <a href="{{ route('applications.index') }}" class="inline-flex min-h-10 items-center gap-2 rounded-xl px-3.5 py-2 text-sm font-semibold transition {{ request()->routeIs('applications.*') ? 'bg-brand-700 text-white dark:bg-brand-500 dark:text-brand-950' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-950 dark:text-slate-300 dark:hover:bg-slate-800 dark:hover:text-white' }}">
                            <span class="material-symbols-outlined text-[19px]">history_edu</span>
                            Riwayat lamaran
                        </a>
                    @endif
                @endguest
            </nav>

            <div class="ml-auto flex items-center gap-2">
                @guest
                    <a href="{{ route('login') }}" class="portal-button-secondary">Masuk</a>
                    <a href="{{ route('register') }}" class="portal-button-primary">Daftar</a>
                @else
                    @php
                        $userParts = preg_split('/\s+/', trim(auth()->user()->name));
                        $userInitials = strtoupper(substr($userParts[0] ?? 'U', 0, 1) . substr(end($userParts) ?: 'U', 0, 1));
                    @endphp

                    @if (auth()->user()->hasRole('employer'))
                        <a href="{{ route('employer.jobs.create') }}" class="portal-button-primary hidden sm:inline-flex">
                            <span class="material-symbols-outlined text-[19px]">add</span>
                            Pasang lowongan
                        </a>
                    @elseif (auth()->user()->hasRole('jobseeker'))
                        <a href="{{ route('jobs.index') }}" class="portal-button-primary hidden sm:inline-flex gap-1.5">
                            <span class="material-symbols-outlined text-[19px]">search</span>
                            Cari lowongan
                        </a>
                    @endif

                    <div class="relative">
                        <button type="button" data-menu-toggle="main-user-menu" aria-expanded="false" aria-haspopup="true" class="flex min-h-11 items-center gap-2 rounded-xl px-2 py-1.5 transition hover:bg-slate-100 dark:hover:bg-slate-800">
                            @if(auth()->user()->avatar_url)
                                <img src="{{ auth()->user()->avatar_url }}" alt="{{ auth()->user()->name }}" class="h-9 w-9 rounded-xl object-cover ring-1 ring-slate-200 dark:ring-slate-700 shrink-0">
                            @else
                                <span class="grid h-9 w-9 place-items-center rounded-xl bg-brand-700 text-sm font-bold text-white dark:bg-brand-500 dark:text-brand-950 shrink-0">{{ $userInitials }}</span>
                            @endif
                            <span class="hidden text-left sm:block">
                                <span class="block max-w-36 truncate text-sm font-semibold">{{ auth()->user()->name }}</span>
                                <span class="block text-xs text-slate-500 dark:text-slate-400">{{ match (auth()->user()->role) { 'admin' => 'Administrator', 'employer' => 'Mitra UMKM', default => 'Pencari kerja' } }}</span>
                            </span>
                            <span class="material-symbols-outlined text-[18px] text-slate-400">expand_more</span>
                        </button>
                        <div id="main-user-menu" data-menu class="absolute right-0 mt-2 hidden w-64 overflow-hidden rounded-2xl border border-slate-200 bg-white p-2 shadow-xl dark:border-slate-700 dark:bg-slate-900">
                            <div class="border-b border-slate-100 px-3 py-2.5 dark:border-slate-800">
                                <p class="text-sm font-semibold text-slate-900 dark:text-white">{{ auth()->user()->name }}</p>
                                <p class="text-xs font-mono font-medium text-brand-700 dark:text-brand-300">@<span>{{ auth()->user()->username }}</span></p>
                                <p class="mt-0.5 text-xs text-slate-500 dark:text-slate-400">{{ auth()->user()->business_name ?: match (auth()->user()->role) { 'admin' => 'Administrator', 'employer' => 'Mitra UMKM', default => 'Pencari kerja' } }}</p>
                            </div>
                            <div class="py-1">
                                @if (auth()->user()->hasRole('jobseeker'))
                                    <a href="{{ route('jobs.index') }}" class="flex items-center gap-2.5 rounded-xl px-3 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-100 dark:text-slate-200 dark:hover:bg-slate-800"><span class="material-symbols-outlined text-[19px] text-slate-500 dark:text-slate-400">search</span>Cari Lowongan</a>
                                    <a href="{{ route('applications.index') }}" class="flex items-center gap-2.5 rounded-xl px-3 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-100 dark:text-slate-200 dark:hover:bg-slate-800"><span class="material-symbols-outlined text-[19px] text-slate-500 dark:text-slate-400">history_edu</span>Riwayat Lamaran</a>
                                    <a href="{{ route('profile.index') }}" class="flex items-center gap-2.5 rounded-xl px-3 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-100 dark:text-slate-200 dark:hover:bg-slate-800"><span class="material-symbols-outlined text-[19px] text-slate-500 dark:text-slate-400">person</span>Profil</a>
                                @endif
                                <a href="{{ route('settings.index') }}" class="flex items-center gap-2.5 rounded-xl px-3 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-100 dark:text-slate-200 dark:hover:bg-slate-800"><span class="material-symbols-outlined text-[19px] text-slate-500 dark:text-slate-400">settings</span>Pengaturan</a>
                            </div>
                            <div class="border-t border-slate-100 pt-1 dark:border-slate-800">
                                <button type="button" data-bs-toggle="modal" data-bs-target="#logoutModal" class="flex w-full items-center gap-2.5 rounded-xl px-3 py-2 text-sm font-semibold text-rose-700 transition hover:bg-rose-50 dark:text-rose-300 dark:hover:bg-rose-950/40"><span class="material-symbols-outlined text-[19px]">logout</span>Keluar dari akun</button>
                            </div>
                        </div>
                    </div>
                @endguest
            </div>
        </div>
    </header>

    <x-flash-messages />

    <main id="main-content" class="{{ request()->routeIs('chat.*') ? 'mx-auto flex-1 min-h-0 w-full max-w-7xl px-3 sm:px-6 lg:px-8 py-2 sm:py-3 flex flex-col overflow-hidden' : 'mx-auto min-h-[calc(100dvh-145px)] w-full max-w-7xl px-4 py-7 sm:px-6 lg:px-8 lg:py-10' }}">
        @yield('content')
    </main>

    @unless(request()->routeIs('chat.*'))
        <footer class="border-t border-slate-200 bg-white dark:border-slate-800 dark:bg-slate-900">
            <div class="mx-auto flex max-w-7xl flex-col gap-1 px-4 py-5 text-xs text-slate-500 sm:flex-row sm:justify-between sm:px-6 lg:px-8 dark:text-slate-400">
                <span>&copy; {{ now()->year }} KerjaLokal</span>
                <span>Pekerjaan Layak dan Pertumbuhan Ekonomi</span>
            </div>
        </footer>
    @endunless

    @auth
        @include('components.floating-chat')
        <x-logout-modal />
    @endauth
    <x-confirm-modal />
    <x-pdf-viewer-modal />

    @yield('modals')
    @stack('modals')

    @stack('scripts')
</body>
</html>
