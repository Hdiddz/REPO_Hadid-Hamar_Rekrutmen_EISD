<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#0f685f">
    <title>@yield('title', 'Portal KerjaLokal')</title>
    <script>
        (() => {
            const savedTheme = localStorage.getItem('theme');
            const useDarkTheme = savedTheme === 'dark' || (!savedTheme && window.matchMedia('(prefers-color-scheme: dark)').matches);
            document.documentElement.classList.toggle('dark', useDarkTheme);
        })();
    </script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,300..600,0..1,-25..0" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-[100dvh] bg-slate-100 font-sans text-slate-900 antialiased dark:bg-slate-950 dark:text-slate-100">
    <a href="#main-content" class="sr-only z-50 rounded-lg bg-brand-700 px-4 py-2 text-white focus:not-sr-only focus:fixed focus:top-3 focus:left-3">
        Lewati ke konten utama
    </a>

    <div class="flex min-h-[100dvh] flex-col">
        <header class="sticky top-0 z-40 border-b border-slate-200/80 bg-white/95 backdrop-blur-xl dark:border-slate-800 dark:bg-slate-950/90">
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

                <div class="ml-auto flex items-center gap-2">
                    @yield('portal_primary_action')

                    <button type="button" class="portal-icon-button lg:hidden" data-mobile-nav-toggle aria-controls="portal-mobile-nav" aria-expanded="false" aria-label="Buka navigasi">
                        <span class="material-symbols-outlined text-[22px]">menu</span>
                    </button>

                    <div class="relative">
                        <button type="button" class="flex min-h-11 items-center gap-2 rounded-xl px-2 py-1.5 text-left transition hover:bg-slate-100 dark:hover:bg-slate-800" data-menu-toggle="portal-user-menu" aria-expanded="false" aria-haspopup="true">
                            <span class="grid h-9 w-9 place-items-center rounded-xl bg-brand-700 text-sm font-bold text-white dark:bg-brand-500 dark:text-brand-950">
                                @yield('portal_user_initials', 'KL')
                            </span>
                            <span class="hidden max-w-40 flex-col md:flex">
                                <span class="truncate text-sm font-semibold text-slate-900 dark:text-white">@yield('portal_user_name', 'Pengguna KerjaLokal')</span>
                                <span class="truncate text-xs text-slate-500 dark:text-slate-400">@yield('portal_user_role', 'Pengguna')</span>
                            </span>
                            <span class="material-symbols-outlined hidden text-[18px] text-slate-400 md:block">expand_more</span>
                        </button>

                        <div id="portal-user-menu" data-menu class="absolute right-0 mt-2 hidden w-64 overflow-hidden rounded-2xl border border-slate-200 bg-white p-2 shadow-xl shadow-slate-900/10 dark:border-slate-700 dark:bg-slate-900">
                            <div class="border-b border-slate-100 px-3 py-2.5 dark:border-slate-800">
                                <p class="text-sm font-semibold text-slate-900 dark:text-white">@yield('portal_user_name', 'Pengguna KerjaLokal')</p>
                                <p class="mt-0.5 text-xs text-slate-500 dark:text-slate-400">@yield('portal_user_context', 'Akun aktif')</p>
                            </div>
                            <div class="grid grid-cols-2 gap-2 px-2 py-3" aria-label="Pilih tema">
                                <button type="button" data-theme-value="light" aria-pressed="false" class="flex items-center justify-center gap-1.5 rounded-xl border border-slate-200 px-2 py-2 text-xs font-medium text-slate-700 hover:bg-slate-50 dark:border-slate-700 dark:text-slate-300 dark:hover:bg-slate-800">
                                    <span class="material-symbols-outlined text-[17px]">light_mode</span> Terang
                                </button>
                                <button type="button" data-theme-value="dark" aria-pressed="false" class="flex items-center justify-center gap-1.5 rounded-xl border border-slate-200 px-2 py-2 text-xs font-medium text-slate-700 hover:bg-slate-50 dark:border-slate-700 dark:text-slate-300 dark:hover:bg-slate-800">
                                    <span class="material-symbols-outlined text-[17px]">dark_mode</span> Gelap
                                </button>
                            </div>
                            <button type="button" data-bs-toggle="modal" data-bs-target="#logoutModal" class="flex w-full items-center gap-2 rounded-xl px-3 py-2.5 text-sm font-semibold text-rose-700 transition hover:bg-rose-50 dark:text-rose-300 dark:hover:bg-rose-950/40">
                                <span class="material-symbols-outlined text-[19px]">logout</span>
                                Keluar dari akun
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <nav id="portal-mobile-nav" class="hidden border-t border-slate-200 bg-white px-4 py-3 lg:hidden dark:border-slate-800 dark:bg-slate-950" aria-label="Navigasi seluler">
                <div class="mx-auto grid max-w-7xl gap-1">
                    @yield('portal_mobile_navigation')
                </div>
            </nav>
        </header>

        <x-flash-messages />

        <section class="border-b border-slate-200/80 bg-white dark:border-slate-800 dark:bg-slate-900">
            <div class="mx-auto flex max-w-7xl flex-col gap-4 px-4 py-6 sm:px-6 lg:flex-row lg:items-end lg:justify-between lg:px-8 lg:py-7">
                <div class="max-w-3xl">
                    <div class="mb-2 flex items-center gap-2 text-sm font-medium text-brand-700 dark:text-brand-300">
                        <span class="material-symbols-outlined text-[20px]">@yield('portal_icon', 'dashboard')</span>
                        <span>@yield('portal_context', 'KerjaLokal')</span>
                    </div>
                    <h1 class="text-2xl font-bold tracking-tight text-slate-950 sm:text-3xl dark:text-white">@yield('portal_title', 'Portal KerjaLokal')</h1>
                    <p class="mt-2 max-w-2xl text-sm leading-6 text-slate-600 sm:text-base dark:text-slate-400">@yield('portal_description')</p>
                </div>
                <div class="flex flex-wrap items-center gap-2">
                    @yield('portal_actions')
                </div>
            </div>
        </section>

        <main id="main-content" class="mx-auto w-full max-w-7xl flex-1 px-4 py-6 sm:px-6 lg:px-8 lg:py-8">
            <div class="animate-page-enter">
                @yield('content')
            </div>
        </main>

        <footer class="border-t border-slate-200 bg-white dark:border-slate-800 dark:bg-slate-900">
            <div class="mx-auto flex max-w-7xl flex-col gap-1 px-4 py-5 text-xs text-slate-500 sm:flex-row sm:items-center sm:justify-between sm:px-6 lg:px-8 dark:text-slate-400">
                <span>&copy; {{ now()->year }} KerjaLokal. Portal @yield('portal_footer_name', 'operasional').</span>
                <span>SDG 8: Pekerjaan Layak dan Pertumbuhan Ekonomi</span>
            </div>
        </footer>
    </div>

    @include('components.floating-chat')
    <x-logout-modal />

    @stack('scripts')
</body>
</html>
