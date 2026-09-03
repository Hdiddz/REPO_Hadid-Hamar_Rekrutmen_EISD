<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#0f685f">
    <title>@yield('title', 'Akun KerjaLokal')</title>
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
    <main class="grid min-h-[100dvh] lg:grid-cols-[minmax(360px,0.85fr)_1.15fr]">
        <section class="relative hidden min-h-[100dvh] overflow-hidden bg-brand-950 lg:block" aria-label="Tentang KerjaLokal">
            <img src="{{ asset('images/hero-kerjalokal.jpg') }}" alt="Talenta dan tim KerjaLokal sedang berkolaborasi" class="absolute inset-0 h-full w-full object-cover object-[center_30%]">
            <div class="absolute inset-0 bg-brand-950/55"></div>
            <div class="absolute inset-x-0 bottom-0 p-10 xl:p-14" data-reveal>
                <a href="{{ route('home') }}" class="inline-flex rounded-xl bg-white/95 p-3 shadow-lg" aria-label="Kembali ke beranda KerjaLokal">
                    <img src="{{ asset('logo.svg') }}" alt="KerjaLokal" class="h-9 w-auto">
                </a>
                <h1 class="mt-7 max-w-lg text-4xl font-bold leading-tight tracking-tight text-white xl:text-5xl">Peluang kerja yang jelas untuk ekonomi lokal.</h1>
                <p class="mt-4 max-w-md text-base leading-7 text-brand-100">Upah transparan, jam kerja terukur, dan proses rekrutmen yang dapat dipantau.</p>
            </div>
        </section>

        <section class="flex min-h-[100dvh] flex-col">
            <header class="flex h-[72px] items-center justify-between px-4 sm:px-8 lg:px-10">
                <a href="{{ route('home') }}" class="rounded-lg lg:hidden">
                    <img src="{{ asset('logo.svg') }}" alt="KerjaLokal" class="h-9 w-auto dark:hidden">
                    <img src="{{ asset('logo-white.svg') }}" alt="KerjaLokal" class="hidden h-9 w-auto dark:block">
                </a>
                <a href="{{ route('home') }}" class="ml-auto inline-flex min-h-10 items-center gap-2 rounded-xl px-3 text-sm font-semibold text-slate-600 transition hover:bg-white hover:text-slate-950 dark:text-slate-300 dark:hover:bg-slate-900 dark:hover:text-white">
                    <span class="material-symbols-outlined text-[18px]">arrow_back</span>
                    Beranda
                </a>
            </header>

            <div class="flex flex-1 items-center justify-center px-4 py-8 sm:px-8 lg:px-12">
                <div class="w-full max-w-xl animate-page-enter">
                    @yield('content')
                </div>
            </div>

            <footer class="px-4 py-5 text-center text-xs text-slate-500 dark:text-slate-400">
                &copy; {{ now()->year }} KerjaLokal. Platform pekerjaan layak dan pertumbuhan ekonomi.
            </footer>
        </section>
    </main>
    <x-confirm-modal />
    @stack('scripts')
</body>
</html>
