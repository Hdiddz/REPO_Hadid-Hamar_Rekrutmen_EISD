<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="theme-color" content="#0f685f">
    <title>@yield('title', 'Portal KerjaLokal')</title>
    <script>
        (() => {
            const savedTheme = localStorage.getItem('theme');
            const useDarkTheme = savedTheme === 'dark';
            document.documentElement.classList.toggle('dark', useDarkTheme);
        })();
    </script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,300..600,0..1,-25..0" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body class="{{ request()->routeIs('chat.*') ? 'h-[100dvh] max-h-[100dvh] overflow-hidden' : 'min-h-[100dvh]' }} bg-slate-100 font-sans text-slate-900 antialiased dark:bg-slate-950 dark:text-slate-100">
    <a href="#main-content" class="sr-only z-50 rounded-lg bg-brand-700 px-4 py-2 text-white focus:not-sr-only focus:fixed focus:top-3 focus:left-3">
        Lewati ke konten utama
    </a>

    <div class="{{ request()->routeIs('chat.*') ? 'h-[100dvh] max-h-[100dvh] flex flex-col overflow-hidden' : 'flex min-h-[100dvh] flex-col' }}">
        <header class="shrink-0 sticky top-0 z-40 border-b border-slate-200/80 bg-white/95 backdrop-blur-xl dark:border-slate-800 dark:bg-slate-950/90">
            <div class="mx-auto flex h-[72px] max-w-7xl items-center gap-4 px-4 sm:px-6 lg:px-8">
                <a href="@yield('portal_home', route('home'))" class="shrink-0 rounded-lg" aria-label="Beranda portal KerjaLokal">
                    <img src="{{ asset('logo.svg') }}" alt="KerjaLokal" class="h-9 w-auto dark:hidden">
                    <img src="{{ asset('logo-white.svg') }}" alt="KerjaLokal" class="hidden h-9 w-auto dark:block">
                </a>

                <span class="hidden h-7 w-px bg-slate-200 sm:block dark:bg-slate-800"></span>
                <span class="hidden rounded-lg bg-brand-50 px-2.5 py-1 text-xs font-semibold text-brand-800 sm:inline-flex dark:bg-brand-950 dark:text-brand-200">
                    @yield('portal_role_label', 'Portal')
                </span>

                <nav class="ml-2 hidden min-w-0 flex-1 items-center gap-1 lg:flex" aria-label="Navigasi utama">
                    @yield('portal_navigation')
                </nav>

                <div class="ml-auto flex items-center gap-1.5 sm:gap-2">
                    @yield('portal_primary_action')

                    <x-notification-bell />

                    <div class="relative hidden lg:block">
                        <button type="button" class="flex min-h-11 items-center gap-2 rounded-xl px-1.5 sm:px-2 py-1.5 text-left transition hover:bg-slate-100 dark:hover:bg-slate-800" data-menu-toggle="portal-user-menu" aria-expanded="false" aria-haspopup="true">
                            @if(auth()->user()?->avatar_url)
                                <img src="{{ auth()->user()->avatar_url }}" alt="{{ auth()->user()->name }}" class="h-9 w-9 rounded-xl object-cover ring-1 ring-slate-200 dark:ring-slate-700 shrink-0">
                            @else
                                <span class="grid h-9 w-9 place-items-center rounded-xl bg-brand-700 text-sm font-bold text-white dark:bg-brand-500 dark:text-brand-950 shrink-0">
                                    @yield('portal_user_initials', 'KL')
                                </span>
                            @endif
                            <span class="hidden max-w-40 flex-col lg:flex">
                                <span class="truncate text-sm font-semibold text-slate-900 dark:text-white">@yield('portal_user_name', 'Pengguna KerjaLokal')</span>
                                <span class="truncate text-xs text-slate-500 dark:text-slate-400">@yield('portal_user_role', 'Pengguna')</span>
                            </span>
                            <span class="material-symbols-outlined hidden text-[18px] text-slate-400 lg:block">expand_more</span>
                        </button>

                        <div id="portal-user-menu" data-menu class="absolute right-0 mt-2 hidden max-lg:!hidden w-64 overflow-hidden rounded-2xl border border-slate-200 bg-white p-2 shadow-xl shadow-slate-900/10 dark:border-slate-700 dark:bg-slate-900 z-50">
                            <div class="border-b border-slate-100 px-3 py-2.5 dark:border-slate-800">
                                <p class="text-sm font-semibold text-slate-900 dark:text-white">@yield('portal_user_name', 'Pengguna KerjaLokal')</p>
                                @if(auth()->user()?->username)
                                    <p class="text-xs font-mono font-medium text-brand-700 dark:text-brand-300">@<span>{{ auth()->user()->username }}</span></p>
                                @endif
                                <p class="mt-0.5 text-xs text-slate-500 dark:text-slate-400">@yield('portal_user_context', 'Akun aktif')</p>
                            </div>
                            <div class="py-1">
                                <a href="{{ route('profile.index') }}" class="flex items-center gap-2.5 rounded-xl px-3 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-100 dark:text-slate-200 dark:hover:bg-slate-800">
                                    <span class="material-symbols-outlined text-[19px] text-slate-500 dark:text-slate-400">account_circle</span>
                                    Profil Saya
                                </a>
                                @if(!auth()->user()?->hasRole('admin'))
                                    <a href="{{ route('reports.index') }}" class="flex items-center gap-2.5 rounded-xl px-3 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-100 dark:text-slate-200 dark:hover:bg-slate-800">
                                        <span class="material-symbols-outlined text-[19px] text-slate-500 dark:text-slate-400">flag</span>
                                        Riwayat Laporan
                                    </a>
                                @endif
                                <a href="{{ route('settings.index') }}" class="flex items-center gap-2.5 rounded-xl px-3 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-100 dark:text-slate-200 dark:hover:bg-slate-800">
                                    <span class="material-symbols-outlined text-[19px] text-slate-500 dark:text-slate-400">settings</span>
                                    Pengaturan
                                </a>
                            </div>
                            <div class="border-t border-slate-100 pt-1 dark:border-slate-800">
                                <button type="button" data-bs-toggle="modal" data-bs-target="#logoutModal" class="flex w-full items-center gap-2.5 rounded-xl px-3 py-2 text-sm font-semibold text-rose-700 transition hover:bg-rose-50 dark:text-rose-300 dark:hover:bg-rose-950/40">
                                    <span class="material-symbols-outlined text-[19px]">logout</span>
                                    Keluar dari akun
                                </button>
                            </div>
                        </div>
                    </div>

                    {{-- Hamburger Button on far right on Mobile --}}
                    <button type="button" class="portal-icon-button lg:hidden shrink-0" data-mobile-nav-toggle aria-controls="portal-mobile-nav" aria-expanded="false" aria-label="Buka navigasi">
                        <span class="material-symbols-outlined text-[24px]">menu</span>
                    </button>
                </div>
            </div>

            <nav id="portal-mobile-nav" data-mobile-nav class="hidden border-t border-slate-200 bg-white/98 backdrop-blur-xl px-4 py-4 lg:hidden dark:border-slate-800 dark:bg-slate-950/98 animate-mobile-drawer shadow-2xl" aria-label="Navigasi seluler">
                <div class="mx-auto grid max-w-7xl gap-1">
                    {{-- User Profile Card inside Mobile Drawer --}}
                    <div class="mb-3.5 flex items-center justify-between gap-2.5 rounded-2xl bg-slate-50 p-3 dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800">
                        <div class="flex items-center gap-3 min-w-0 flex-1">
                            @if(auth()->user()?->avatar_url)
                                <img src="{{ auth()->user()->avatar_url }}" alt="{{ auth()->user()->name }}" class="h-11 w-11 rounded-xl object-cover ring-2 ring-brand-500 shrink-0">
                            @else
                                <span class="grid h-11 w-11 place-items-center rounded-xl bg-brand-700 text-sm font-bold text-white dark:bg-brand-500 dark:text-brand-950 shrink-0">
                                    @yield('portal_user_initials', 'KL')
                                </span>
                            @endif
                            <div class="min-w-0 flex-1">
                                <div class="flex items-center gap-1.5">
                                    <p class="truncate text-sm font-bold text-slate-900 dark:text-white">@yield('portal_user_name', 'Pengguna KerjaLokal')</p>
                                    <span class="rounded-md bg-brand-100 px-1.5 py-0.5 text-[10px] font-bold text-brand-800 dark:bg-brand-950 dark:text-brand-300 shrink-0">
                                        @yield('portal_role_label', 'Portal')
                                    </span>
                                </div>
                                @if(auth()->user()?->username)
                                    <p class="truncate text-xs font-mono text-brand-700 dark:text-brand-400">@<span>{{ auth()->user()->username }}</span></p>
                                @endif
                                <p class="truncate text-[11px] text-slate-500 dark:text-slate-400">@yield('portal_user_context', 'Akun aktif')</p>
                            </div>
                        </div>
                        <a href="{{ route('profile.index') }}" class="inline-flex items-center gap-1 rounded-xl border border-slate-200 bg-white px-2.5 py-1.5 text-xs font-bold text-slate-700 shadow-xs hover:bg-slate-50 hover:text-brand-700 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 dark:hover:bg-slate-700 shrink-0 transition" title="Lihat Profil">
                            <span class="material-symbols-outlined text-[15px] text-brand-600 dark:text-brand-400">visibility</span>
                            <span>Lihat Profil</span>
                        </a>
                    </div>

                    @yield('portal_mobile_navigation')
                    
                    @if(!auth()->user()?->hasRole('admin'))
                        <a href="{{ route('reports.index') }}" class="flex min-h-11 items-center gap-3 rounded-xl px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-100 dark:text-slate-200 dark:hover:bg-slate-800 transition">
                            <span class="material-symbols-outlined text-[20px]">flag</span>
                            <span>Riwayat Laporan</span>
                        </a>
                    @endif
                    <a href="{{ route('settings.index') }}" class="flex min-h-11 items-center gap-3 rounded-xl px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-100 dark:text-slate-200 dark:hover:bg-slate-800 transition">
                        <span class="material-symbols-outlined text-[20px]">settings</span>
                        <span>Pengaturan Akun</span>
                    </a>
                </div>

                <div class="mt-3.5 border-t border-slate-100 pt-3 dark:border-slate-800 flex items-center justify-end">
                    <button type="button" data-bs-toggle="modal" data-bs-target="#logoutModal" class="inline-flex min-h-10 items-center gap-2 rounded-xl bg-rose-50 px-3.5 py-2 text-xs font-bold text-rose-700 hover:bg-rose-100 dark:bg-rose-950/40 dark:text-rose-300 dark:hover:bg-rose-900/50 transition">
                        <span class="material-symbols-outlined text-[18px]">logout</span>
                        <span>Keluar</span>
                    </button>
                </div>
            </nav>
        </header>

        <x-flash-messages />

        @unless(request()->routeIs('chat.*'))
            <section class="border-b border-slate-200/80 bg-white dark:border-slate-800 dark:bg-slate-900">
                <div class="mx-auto flex max-w-7xl flex-col gap-3.5 px-4 py-4 sm:px-6 lg:flex-row lg:items-center lg:justify-between lg:px-8 lg:py-6">
                    <div class="min-w-0 flex-1 lg:max-w-2xl">
                        <div class="mb-1 flex items-center gap-2 text-xs sm:text-sm font-medium text-brand-700 dark:text-brand-300">
                            <span class="material-symbols-outlined text-[18px]">@yield('portal_icon', 'dashboard')</span>
                            <span>@yield('portal_context', 'KerjaLokal')</span>
                        </div>
                        <h1 class="text-lg font-bold tracking-tight text-slate-950 sm:text-2xl lg:text-3xl dark:text-white truncate">@yield('portal_title', 'Portal KerjaLokal')</h1>
                        <p class="mt-0.5 text-xs sm:text-sm leading-relaxed text-slate-600 dark:text-slate-400 line-clamp-2">@yield('portal_description')</p>
                    </div>
                    <div class="flex flex-wrap items-center gap-1.5 sm:gap-2.5 w-full sm:w-auto shrink-0">
                        @yield('portal_actions')
                    </div>
                </div>
            </section>
        @endunless

        <main id="main-content" class="{{ request()->routeIs('chat.*') ? 'mx-auto w-full max-w-7xl flex-1 min-h-0 px-3 sm:px-6 lg:px-8 py-2 sm:py-3 flex flex-col overflow-hidden' : 'mx-auto w-full max-w-7xl flex-1 px-4 py-6 sm:px-6 lg:px-8 lg:py-8' }}">
            <div class="{{ request()->routeIs('chat.*') ? 'h-full flex flex-col flex-1 min-h-0' : 'animate-page-enter' }}">
                @yield('content')
            </div>
        </main>

        @unless(request()->routeIs('chat.*'))
            <footer class="border-t border-slate-200 bg-white dark:border-slate-800 dark:bg-slate-900">
                <div class="mx-auto flex max-w-7xl flex-col gap-1 px-4 py-5 text-xs text-slate-500 sm:flex-row sm:items-center sm:justify-between sm:px-6 lg:px-8 dark:text-slate-400">
                    <span>&copy; {{ now()->year }} KerjaLokal. Portal @yield('portal_footer_name', 'operasional').</span>
                    <span>Pekerjaan Layak dan Pertumbuhan Ekonomi</span>
                </div>
            </footer>
        @endunless
    </div>

    @include('components.floating-chat')
    <x-logout-modal />
    <x-confirm-modal />
    <x-pdf-viewer-modal />
    <x-notification-modal />

    @yield('portal_modals')
    @stack('modals')

    @stack('scripts')
</body>
</html>
