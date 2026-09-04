@extends('layouts.app')

@section('title', 'KerjaLokal | Peluang Kerja Layak untuk Ekonomi Lokal')

@section('content')
    <section class="overflow-hidden rounded-3xl border border-slate-200/90 bg-white shadow-[0_20px_60px_-30px_rgba(15,23,42,0.2)] dark:border-slate-800 dark:bg-slate-900">
        <div class="grid lg:min-h-[500px] lg:grid-cols-[1.05fr_0.95fr]">
            <div class="flex flex-col justify-center px-5 py-8 sm:px-10 sm:py-14 lg:px-12">
                <div class="mb-4 inline-flex w-fit items-center gap-2 rounded-full border border-brand-200/80 bg-brand-50/90 px-3 py-1 text-[11px] font-bold uppercase tracking-[0.14em] text-brand-800 shadow-xs dark:border-brand-900/60 dark:bg-brand-950/60 dark:text-brand-300">
                    <span class="relative flex h-2 w-2">
                        <span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-brand-500 opacity-75"></span>
                        <span class="relative inline-flex h-2 w-2 rounded-full bg-brand-600 dark:bg-brand-400"></span>
                    </span>
                    <span>Peluang Dari Usaha Lokal</span>
                </div>

                <h1 class="max-w-xl text-3xl font-extrabold leading-[1.12] tracking-tight text-slate-950 dark:text-white sm:text-5xl lg:text-6xl">
                    Kerja yang jelas.<br>
                    <span class="text-brand-700 dark:text-brand-400">Usaha tumbuh.</span>
                </h1>

                <p class="mt-3.5 max-w-lg text-sm sm:text-base leading-relaxed text-slate-600 dark:text-slate-300">
                    Temukan lowongan UMKM dengan informasi upah, jam kerja, dan lokasi yang mudah dipahami sejak awal.
                </p>

                <div class="mt-6 max-w-xl">
                    {{-- Unified Modern Search Bar --}}
                    <form action="{{ route('jobs.index') }}" method="GET" class="group relative flex items-center rounded-2xl border border-slate-200 bg-slate-50/70 p-1.5 shadow-sm transition-all focus-within:border-brand-600 focus-within:bg-white focus-within:ring-4 focus-within:ring-brand-100 dark:border-slate-700 dark:bg-slate-950 dark:focus-within:border-brand-500 dark:focus-within:ring-brand-900/40">
                        <label for="home-search" class="sr-only">Cari posisi atau lokasi</label>
                        <span class="material-symbols-outlined ml-2.5 text-[20px] text-slate-400 transition group-focus-within:text-brand-600 dark:text-slate-500 dark:group-focus-within:text-brand-400" aria-hidden="true">search</span>
                        <input
                            id="home-search"
                            name="q"
                            type="search"
                            class="min-h-10 w-full bg-transparent px-2.5 py-1.5 text-sm text-slate-900 outline-none placeholder:text-slate-400 dark:text-white dark:placeholder:text-slate-500"
                            placeholder="Posisi pekerjaan atau lokasi..."
                        >
                        <button type="submit" class="inline-flex h-10 items-center justify-center gap-1.5 rounded-xl bg-brand-700 px-4 text-xs font-bold text-white shadow-xs transition hover:bg-brand-800 active:scale-95 shrink-0 dark:bg-brand-500 dark:text-brand-950 dark:hover:bg-brand-400">
                            <span class="material-symbols-outlined text-[17px]" aria-hidden="true">search</span>
                            <span>Cari</span>
                        </button>
                    </form>

                    @guest
                        <div class="mt-3 flex flex-wrap items-center gap-x-2.5 gap-y-1 text-xs sm:text-sm text-slate-500 dark:text-slate-400">
                            <span>Sudah punya akun? <a href="{{ route('login') }}" class="font-semibold text-blue-600 hover:text-blue-700 hover:underline dark:text-blue-400 dark:hover:text-blue-300">Login</a></span>
                            <span class="text-slate-300 dark:text-slate-600" aria-hidden="true">&bull;</span>
                            <span>Belum punya akun? <a href="{{ route('register') }}" class="font-semibold text-blue-600 hover:text-blue-700 hover:underline dark:text-blue-400 dark:hover:text-blue-300">Register</a></span>
                        </div>
                    @endguest
                </div>
            </div>

            <div class="relative p-3 sm:p-5 lg:p-0">
                <div class="relative min-h-[250px] sm:min-h-[320px] lg:min-h-full overflow-hidden rounded-2xl lg:rounded-none lg:rounded-r-3xl">
                    <img
                        src="{{ asset('images/hero-kerjalokal.jpg') }}"
                        alt="Talenta lokal sedang bekerja bersama"
                        class="absolute inset-0 h-full w-full object-cover object-[center_45%] sm:object-[center_38%] lg:object-[center_30%]"
                    >
                    <div class="absolute inset-0 bg-gradient-to-t from-slate-950/85 via-slate-950/25 to-transparent sm:from-slate-950/50 sm:via-transparent" aria-hidden="true"></div>

                    <div class="absolute inset-x-3 bottom-3 sm:inset-x-auto sm:bottom-6 sm:left-6 flex items-center gap-3 rounded-2xl border border-white/20 bg-slate-950/60 p-3 text-white shadow-xl backdrop-blur-md dark:border-slate-700/60 dark:bg-slate-950/80">
                        <span class="grid h-10 w-10 shrink-0 place-items-center rounded-xl border border-brand-400/30 bg-brand-500/25 text-brand-300 dark:bg-brand-400/20 dark:text-brand-300">
                            <span class="material-symbols-outlined text-[20px]" aria-hidden="true">verified</span>
                        </span>
                        <div class="min-w-0 pr-1">
                            <p class="text-xs sm:text-sm font-bold text-white leading-tight">Informasi Transparan Sejak Awal</p>
                            <p class="mt-0.5 text-[11px] text-slate-200 dark:text-slate-300 leading-tight">Upah dan jam kerja tercantum jelas & terverifikasi</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="py-14 sm:py-20" aria-labelledby="latest-jobs-title">
        <div class="mb-7 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="text-sm font-bold text-brand-700 dark:text-brand-300">Peluang terbaru</p>
                <h2 id="latest-jobs-title" class="mt-1 text-3xl font-bold tracking-tight text-slate-950 dark:text-white sm:text-4xl">Temukan kerja di sekitarmu</h2>
                <p class="mt-2 text-sm leading-6 text-slate-600 dark:text-slate-300">Lowongan dari UMKM dengan detail yang bisa dilihat sebelum melamar.</p>
            </div>
            <a href="{{ route('jobs.index') }}" class="inline-flex min-h-11 w-fit items-center gap-2 rounded-xl border border-slate-300 bg-white px-4 text-sm font-bold text-slate-800 transition hover:border-brand-600 hover:text-brand-700 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100 dark:hover:border-brand-400 dark:hover:text-brand-300">
                <span>Lihat semua lowongan</span>
                <span class="material-symbols-outlined text-[18px]" aria-hidden="true">arrow_forward</span>
            </a>
        </div>

        @if ($featuredJobs->isNotEmpty())
            <div class="grid overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-[0_24px_60px_-48px_rgba(15,23,42,0.5)] lg:grid-cols-[1.05fr_0.95fr] dark:border-slate-800 dark:bg-slate-900">
                
                <!-- Left Column: Rotating Featured Job Cover (3s Auto Rotation) -->
                <div id="featuredJobsCover" 
                     class="group/cover relative flex min-h-[400px] sm:min-h-[464px] flex-col justify-between overflow-hidden bg-brand-950 p-6 text-white sm:p-9"
                     onmouseenter="featuredCarouselPause()" 
                     onmouseleave="featuredCarouselResume()">
                    
                    <!-- Decorative background patterns -->
                    <div class="absolute -right-16 -top-24 h-72 w-72 rounded-full border-[52px] border-brand-700/25 pointer-events-none" aria-hidden="true"></div>
                    <div class="absolute -bottom-24 -left-16 h-64 w-64 rounded-full bg-brand-700/20 blur-3xl pointer-events-none" aria-hidden="true"></div>
                    <div class="absolute top-1/3 -right-12 h-44 w-44 rounded-full bg-teal-500/10 blur-2xl pointer-events-none" aria-hidden="true"></div>

                    <!-- Slide Items -->
                    @foreach ($featuredJobs as $index => $job)
                        <a href="{{ route('jobs.show', $job) }}"
                           data-featured-slide="{{ $index }}"
                           data-job-id="{{ $job->id }}"
                           class="featured-cover-slide absolute inset-0 flex flex-col justify-between p-6 sm:p-9 text-white transition-opacity duration-700 ease-in-out {{ $loop->first ? 'opacity-100 pointer-events-auto z-10' : 'opacity-0 pointer-events-none z-0' }}"
                           aria-label="Lowongan unggulan: {{ $job->title }} di {{ $job->location }}">

                            @if($job->cover_image)
                                <!-- Background Cover Image with Darkened Overlay -->
                                <div class="absolute inset-0 z-0 overflow-hidden pointer-events-none">
                                    <img src="{{ $job->cover_image_url }}" alt="" class="h-full w-full object-cover object-center transition-transform duration-1000 ease-out group-hover/cover:scale-105" loading="lazy" aria-hidden="true">
                                    <div class="absolute inset-0 bg-brand-950/80 bg-gradient-to-t from-brand-950 via-brand-950/75 to-brand-950/65"></div>
                                </div>
                            @endif

                            <!-- Header: Category -->
                            <div class="relative flex items-center gap-2 z-10">
                                <span class="inline-flex items-center gap-2 rounded-full border border-white/15 bg-white/10 px-3 py-1.5 text-xs font-bold text-brand-50 backdrop-blur-xs">
                                    <span class="material-symbols-outlined text-[16px]" aria-hidden="true">{{ match ($job->category->slug) { 'kuliner-kedai-kopi' => 'local_cafe', 'kreatif-dan-media' => 'movie_edit', 'ritel-toko' => 'storefront', 'logistik-gudang' => 'inventory_2', 'administrasi-keuangan' => 'contract', default => 'work' } }}</span>
                                    {{ $job->category->name }}
                                </span>
                            </div>

                            <!-- Body: Title and Employer -->
                            <div class="relative my-auto py-5 z-10">
                                <h3 class="max-w-md text-2xl sm:text-3xl lg:text-4xl font-bold leading-tight tracking-tight text-white group-hover/cover:text-brand-200 transition-colors">
                                    {{ $job->title }}
                                </h3>
                                <p class="mt-3 text-sm sm:text-base font-semibold text-brand-100 flex items-center gap-2">
                                    <span class="material-symbols-outlined text-[18px] text-brand-300">storefront</span>
                                    <span>{{ $job->employer->business_name ?: $job->employer->name }}</span>
                                </p>
                            </div>

                            <!-- Footer: Location, Salary, CTA -->
                            <div class="relative z-10">
                                <div class="grid gap-5 sm:grid-cols-2 pt-4 border-t border-white/10">
                                    <div>
                                        <p class="text-[11px] uppercase tracking-[0.14em] text-brand-200 font-medium">Lokasi</p>
                                        <p class="mt-1 font-bold text-white flex items-center gap-1 text-sm sm:text-base">
                                            <span class="material-symbols-outlined text-[16px] text-brand-300">location_on</span>
                                            <span>{{ $job->location }}</span>
                                        </p>
                                    </div>
                                    <div>
                                        <p class="text-[11px] uppercase tracking-[0.14em] text-brand-200 font-medium">Upah</p>
                                        <p class="mt-1 font-bold text-white text-sm sm:text-base">
                                            Rp {{ number_format($job->salary_amount, 0, ',', '.') }}
                                            <span class="text-xs font-normal text-brand-200">/ {{ $job->salary_type === 'monthly' ? 'bulan' : ($job->salary_type === 'daily' ? 'hari' : 'jam') }}</span>
                                        </p>
                                    </div>
                                </div>

                                <div class="mt-6 flex items-center justify-between">
                                    <span class="inline-flex items-center gap-2 text-sm font-bold text-white group-hover/cover:text-brand-200 transition-colors">
                                        Lihat detail
                                        <span class="material-symbols-outlined text-[19px] transition-transform duration-200 group-hover/cover:translate-x-1.5" aria-hidden="true">arrow_forward</span>
                                    </span>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>

                <!-- Right Column: Other Featured Jobs List -->
                <div class="divide-y divide-slate-200 dark:divide-slate-800">
                    @foreach ($featuredJobs->skip(1) as $job)
                        <a href="{{ route('jobs.show', $job) }}" 
                           data-featured-right-job="{{ $job->id }}"
                           onmouseenter="featuredCarouselHoverJob({{ $job->id }})"
                           onmouseleave="featuredCarouselResume()"
                           class="featured-right-item group flex min-h-[116px] items-center gap-4 px-5 py-5 transition-all duration-300 hover:bg-brand-50/70 sm:px-7 dark:hover:bg-brand-950/30">
                            <span class="grid h-11 w-11 shrink-0 place-items-center rounded-xl bg-brand-50 text-brand-700 transition group-hover:bg-brand-700 group-hover:text-white dark:bg-brand-950 dark:text-brand-300 dark:group-hover:bg-brand-500 dark:group-hover:text-brand-950">
                                <span class="material-symbols-outlined text-[21px]" aria-hidden="true">{{ match ($job->category->slug) { 'kuliner-kedai-kopi' => 'local_cafe', 'kreatif-dan-media' => 'movie_edit', 'ritel-toko' => 'storefront', 'logistik-gudang' => 'inventory_2', 'administrasi-keuangan' => 'contract', default => 'handyman' } }}</span>
                            </span>
                            <div class="min-w-0 flex-1">
                                <h3 class="truncate text-base font-bold text-slate-950 transition group-hover:text-brand-700 dark:text-white dark:group-hover:text-brand-300">{{ $job->title }}</h3>
                                <p class="mt-1 truncate text-sm text-slate-500 dark:text-slate-400">{{ $job->employer->business_name ?: $job->employer->name }} · {{ $job->location }}</p>
                                <p class="mt-1 text-sm font-semibold text-slate-700 dark:text-slate-200">Rp {{ number_format($job->salary_amount, 0, ',', '.') }} / {{ $job->salary_type === 'monthly' ? 'bulan' : ($job->salary_type === 'daily' ? 'hari' : 'jam') }}</p>
                            </div>
                            <span class="material-symbols-outlined shrink-0 text-[20px] text-brand-700 transition group-hover:translate-x-1 dark:text-brand-300" aria-hidden="true">arrow_forward</span>
                        </a>
                    @endforeach
                </div>

            </div>
        @else
            <div class="rounded-3xl border border-dashed border-slate-300 bg-white px-6 py-14 text-center dark:border-slate-700 dark:bg-slate-900">
                <span class="material-symbols-outlined text-4xl text-slate-400" aria-hidden="true">work_off</span>
                <p class="mt-3 font-bold text-slate-900 dark:text-white">Belum ada lowongan aktif</p>
                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Silakan kembali lagi untuk melihat peluang terbaru.</p>
            </div>
        @endif
    </section>

    <section class="overflow-hidden rounded-3xl bg-brand-950 text-white" aria-labelledby="about-title">
        <div class="grid lg:grid-cols-[0.9fr_1.1fr]">
            <div class="flex flex-col justify-center px-6 py-10 sm:px-10 sm:py-14 lg:px-14">
                <p class="text-sm font-bold text-brand-200">Tentang KerjaLokal</p>
                <h2 id="about-title" class="mt-2 max-w-lg text-3xl font-bold leading-tight tracking-tight sm:text-4xl">Peluang lokal dengan informasi yang jelas.</h2>
                <p class="mt-4 max-w-lg leading-7 text-brand-100">KerjaLokal mempertemukan pencari kerja dengan UMKM yang membutuhkan orang tepat untuk berkembang bersama.</p>
            </div>

            <div class="divide-y divide-white/10 border-t border-white/10 px-6 sm:px-10 lg:border-l lg:border-t-0 lg:px-12">
                <div class="flex gap-4 py-7">
                    <span class="material-symbols-outlined mt-0.5 text-brand-300" aria-hidden="true">payments</span>
                    <div>
                        <h3 class="font-bold">Upah tercantum</h3>
                        <p class="mt-1 text-sm leading-6 text-brand-100">Nominal dan periode pembayaran terlihat sejak awal.</p>
                    </div>
                </div>
                <div class="flex gap-4 py-7">
                    <span class="material-symbols-outlined mt-0.5 text-brand-300" aria-hidden="true">schedule</span>
                    <div>
                        <h3 class="font-bold">Jam kerja wajar</h3>
                        <p class="mt-1 text-sm leading-6 text-brand-100">Lowongan dibatasi maksimal delapan jam per hari.</p>
                    </div>
                </div>
                <div class="flex gap-4 py-7">
                    <span class="material-symbols-outlined mt-0.5 text-brand-300" aria-hidden="true">fact_check</span>
                    <div>
                        <h3 class="font-bold">Proses mudah dipantau</h3>
                        <p class="mt-1 text-sm leading-6 text-brand-100">Perkembangan lamaran dapat dilihat langsung dari akun pencari kerja.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="py-14 sm:py-20">
        <div class="flex flex-col gap-6 border-y border-slate-200 py-9 sm:flex-row sm:items-center sm:justify-between dark:border-slate-800">
            <div>
                <p class="text-sm font-bold text-brand-700 dark:text-brand-300">Untuk pemilik usaha</p>
                <h2 class="mt-1 text-2xl font-bold tracking-tight text-slate-950 dark:text-white sm:text-3xl">Temukan kandidat untuk UMKM-mu.</h2>
                <p class="mt-2 text-sm leading-6 text-slate-600 dark:text-slate-300">Daftar sebagai mitra, pasang lowongan, lalu kelola pelamar dalam satu tempat.</p>
            </div>
            <a href="{{ route('register') }}" class="inline-flex min-h-12 w-fit shrink-0 items-center justify-center gap-2 rounded-xl bg-brand-700 px-5 text-sm font-bold text-white transition hover:bg-brand-800 focus:outline-none focus:ring-4 focus:ring-brand-200 active:translate-y-px dark:bg-brand-500 dark:text-brand-950 dark:hover:bg-brand-400 dark:focus:ring-brand-900">
                Daftar sebagai mitra
                <span class="material-symbols-outlined text-[19px]" aria-hidden="true">arrow_forward</span>
            </a>
        </div>
    </section>
@endsection

@push('scripts')
<script>
    (function () {
        const slides = document.querySelectorAll('.featured-cover-slide');
        const rightItems = document.querySelectorAll('.featured-right-item');
        const total = slides.length;
        if (total <= 1) return;

        let currentIndex = 0;
        let timer = null;
        let isPaused = false;
        const ROTATION_INTERVAL = 3000; // 3 detik per lowongan

        function showSlide(index) {
            if (index < 0) index = total - 1;
            if (index >= total) index = 0;
            currentIndex = index;

            slides.forEach((slide, i) => {
                if (i === currentIndex) {
                    slide.classList.remove('opacity-0', 'pointer-events-none', 'z-0');
                    slide.classList.add('opacity-100', 'pointer-events-auto', 'z-10');
                } else {
                    slide.classList.remove('opacity-100', 'pointer-events-auto', 'z-10');
                    slide.classList.add('opacity-0', 'pointer-events-none', 'z-0');
                }
            });

            const currentJobId = slides[currentIndex]?.dataset.jobId;
            rightItems.forEach(item => {
                if (item.dataset.featuredRightJob === currentJobId) {
                    item.classList.add('bg-brand-50/90', 'dark:bg-brand-950/60', 'border-l-4', 'border-brand-600', 'dark:border-brand-400');
                } else {
                    item.classList.remove('bg-brand-50/90', 'dark:bg-brand-950/60', 'border-l-4', 'border-brand-600', 'dark:border-brand-400');
                }
            });
        }

        function startRotation() {
            stopRotation();
            timer = setInterval(() => {
                if (!isPaused) {
                    showSlide(currentIndex + 1);
                }
            }, ROTATION_INTERVAL);
        }

        function stopRotation() {
            if (timer) {
                clearInterval(timer);
                timer = null;
            }
        }

        window.featuredCarouselPause = function () {
            isPaused = true;
            stopRotation();
        };

        window.featuredCarouselResume = function () {
            isPaused = false;
            startRotation();
        };

        window.featuredCarouselHoverJob = function (jobId) {
            const targetIndex = Array.from(slides).findIndex(s => s.dataset.jobId == jobId);
            if (targetIndex !== -1) {
                isPaused = true;
                stopRotation();
                showSlide(targetIndex);
            }
        };

        // Start on ready
        showSlide(0);
        startRotation();
    })();
</script>
@endpush
