@extends('layouts.app')

@section('title', 'KerjaLokal | Peluang Kerja Layak untuk Ekonomi Lokal')

@section('content')
    <section class="relative overflow-hidden rounded-[2rem] bg-brand-950 text-white shadow-2xl shadow-brand-950/20" data-reveal>
        {{-- Background Cover Photo for Mobile & Tablet (< lg) --}}
        <div class="absolute inset-0 lg:hidden pointer-events-none" aria-hidden="true">
            <img src="{{ asset('images/hero-kerjalokal.jpg') }}" alt="" class="h-full w-full object-cover object-[center_30%]">
            <div class="absolute inset-0 bg-gradient-to-b from-brand-950/92 via-brand-950/80 to-brand-950/95"></div>
        </div>

        <div class="grid min-h-0 lg:min-h-[620px] lg:grid-cols-[1.04fr_0.96fr]">
            <div class="relative z-10 flex flex-col justify-center px-5 pt-8 pb-10 sm:px-10 sm:py-12 lg:px-14 lg:py-20">
                <div class="mb-5 sm:mb-7 inline-flex w-fit items-center gap-2 rounded-full border border-white/15 bg-white/10 px-3 py-1.5 text-xs font-bold uppercase tracking-[0.16em] text-brand-100 backdrop-blur">
                    <span class="h-1.5 w-1.5 rounded-full bg-coral-400"></span>
                    Ekosistem kerja lokal
                </div>
                <h1 class="max-w-3xl text-3xl font-bold leading-[1.1] tracking-[-0.03em] sm:text-5xl lg:text-7xl">
                    Kerja yang layak.<br><span class="text-brand-200">Usaha yang bertumbuh.</span>
                </h1>
                <p class="mt-4 sm:mt-6 max-w-xl text-sm leading-6 text-brand-100 sm:text-lg sm:leading-7">
                    KerjaLokal mempertemukan talenta dengan UMKM melalui lowongan yang menyebutkan upah, jam kerja, dan keterampilan secara jelas.
                </p>
                <form action="{{ route('jobs.index') }}" method="GET" class="mt-6 sm:mt-8 flex max-w-xl flex-col gap-2 rounded-2xl bg-white p-2 shadow-xl sm:flex-row">
                    <label for="home-search" class="sr-only">Cari posisi atau lokasi</label>
                    <div class="relative flex-1">
                        <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400">search</span>
                        <input id="home-search" name="q" type="search" class="min-h-12 w-full rounded-xl border-0 py-3 pl-11 pr-3 text-sm text-slate-900 outline-none ring-0" placeholder="Kasir, barista, Bandung...">
                    </div>
                    <button class="min-h-12 w-full sm:w-auto rounded-xl bg-coral-600 px-6 text-sm font-bold text-white transition hover:bg-coral-700 cursor-pointer flex items-center justify-center gap-2">
                        <span class="material-symbols-outlined text-[19px] sm:hidden">search</span>
                        <span>Cari peluang</span>
                    </button>
                </form>
                <div class="mt-6 sm:mt-8 flex flex-wrap gap-4 sm:gap-6 text-xs sm:text-sm text-brand-100">
                    <span class="flex items-center gap-1.5 sm:gap-2">
                        <span class="material-symbols-outlined text-[18px] sm:text-[19px] text-brand-300">payments</span>
                        Upah transparan
                    </span>
                    <span class="flex items-center gap-1.5 sm:gap-2">
                        <span class="material-symbols-outlined text-[18px] sm:text-[19px] text-brand-300">schedule</span>
                        Maksimal 8 jam / hari
                    </span>
                </div>
            </div>
            {{-- Dedicated Side Photo on Desktop (>= lg) --}}
            <div class="relative hidden min-h-full lg:block">
                <img src="{{ asset('images/hero-kerjalokal.jpg') }}" alt="Talenta dan tim KerjaLokal sedang berkolaborasi" class="absolute inset-0 h-full w-full object-cover object-[center_30%]">
                <div class="absolute inset-0 bg-brand-950/25"></div>
            </div>
        </div>
    </section>

    <section class="-mt-4 sm:-mt-5 relative z-10 mx-2 sm:mx-8 grid grid-cols-3 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-xl dark:border-slate-800 dark:bg-slate-900" aria-label="Ringkasan platform" data-reveal>
        @foreach ([['value' => $metrics['open_jobs'], 'label' => 'lowongan aktif'], ['value' => $metrics['employers'], 'label' => 'mitra UMKM'], ['value' => $metrics['accepted_workers'], 'label' => 'pekerja diterima']] as $metric)
            <div class="border-r border-slate-100 px-3 py-3.5 sm:px-6 sm:py-5 last:border-r-0 text-center sm:text-left dark:border-slate-800">
                <strong data-counter="{{ $metric['value'] }}" class="block text-xl sm:text-3xl font-bold tracking-tight text-brand-700 dark:text-brand-300">0</strong>
                <span class="mt-0.5 sm:mt-1 block text-[11px] sm:text-sm text-slate-500 dark:text-slate-400 truncate">{{ $metric['label'] }}</span>
            </div>
        @endforeach
    </section>

    <section class="py-12 sm:py-20" data-reveal>
        <div class="mb-6 sm:mb-9 flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="text-xs font-bold uppercase tracking-[0.18em] text-coral-600">Peluang terbaru</p>
                <h2 class="mt-1.5 sm:mt-2 text-2xl font-bold tracking-tight text-slate-950 dark:text-white sm:text-4xl">Temukan tempat untuk bertumbuh.</h2>
            </div>
            <a href="{{ route('jobs.index') }}" class="portal-button-secondary w-full sm:w-fit justify-center">
                <span>Lihat semua lowongan</span>
                <span class="material-symbols-outlined text-[18px]">arrow_forward</span>
            </a>
        </div>
        <div class="grid gap-3.5 sm:gap-4 lg:grid-cols-2">
            @forelse ($featuredJobs as $job)
                <a href="{{ route('jobs.show', $job) }}" class="group rounded-2xl border border-slate-200 bg-white p-4 sm:p-5 transition hover:-translate-y-0.5 hover:border-brand-300 hover:shadow-xl dark:border-slate-800 dark:bg-slate-900 dark:hover:border-brand-700">
                    <div class="flex items-start justify-between gap-3 sm:gap-5">
                        <div class="min-w-0 flex-1">
                            <span class="text-xs font-bold uppercase tracking-wider text-brand-700 dark:text-brand-300">{{ $job->category->name }}</span>
                            <h3 class="mt-1.5 text-base sm:text-xl font-bold text-slate-950 group-hover:text-brand-700 dark:text-white truncate">{{ $job->title }}</h3>
                            <p class="mt-0.5 text-xs sm:text-sm text-slate-500 truncate">{{ $job->employer->business_name }} · {{ $job->location }}</p>
                        </div>
                        <span class="material-symbols-outlined rounded-xl bg-brand-50 p-2 text-brand-700 transition group-hover:translate-x-1 dark:bg-brand-950 dark:text-brand-300 shrink-0 text-[20px]">arrow_outward</span>
                    </div>
                    <div class="mt-4 sm:mt-5 flex flex-wrap gap-2 text-xs font-semibold">
                        <span class="rounded-lg bg-emerald-50 px-2.5 py-1 text-emerald-700 dark:bg-emerald-950/40 dark:text-emerald-300">Rp {{ number_format($job->salary_amount, 0, ',', '.') }} / {{ $job->salary_type === 'monthly' ? 'bln' : 'hari' }}</span>
                        <span class="rounded-lg bg-slate-100 px-2.5 py-1 text-slate-600 dark:bg-slate-800 dark:text-slate-300">{{ $job->work_hours_per_day }} jam / hari</span>
                    </div>
                </a>
            @empty
                <div class="rounded-2xl border border-dashed border-slate-300 p-8 sm:p-10 text-center text-sm text-slate-500 lg:col-span-2">Belum ada lowongan aktif. Mitra UMKM dapat mulai menerbitkan lowongan.</div>
            @endforelse
        </div>
    </section>

    <section class="grid gap-6 sm:gap-8 rounded-[2rem] bg-amber-50 px-5 py-9 sm:px-10 sm:py-12 dark:bg-slate-900 lg:grid-cols-[0.8fr_1.2fr] lg:items-center" data-reveal>
        <div>
            <p class="text-xs font-bold uppercase tracking-[0.18em] text-coral-600">Alur sederhana</p>
            <h2 class="mt-2 text-2xl font-bold tracking-tight text-slate-950 dark:text-white sm:text-3xl">Dari peluang sampai keputusan.</h2>
            <p class="mt-3 text-xs sm:text-sm leading-6 text-slate-600 dark:text-slate-300">Setiap peran mendapat ruang kerja yang fokus, tanpa memutus aliran data antarproses.</p>
        </div>
        <ol class="grid gap-3 sm:grid-cols-3">
            @foreach ([['01', 'Temukan', 'Saring peluang berdasarkan kategori, lokasi, dan keterampilan.'], ['02', 'Ajukan', 'Kirim resume PDF dan catatan melalui satu formulir.'], ['03', 'Pantau', 'Lihat status seleksi yang diperbarui langsung oleh mitra.']] as $step)
                <li class="rounded-2xl bg-white p-4 sm:p-5 dark:bg-slate-950">
                    <span class="text-xs font-black text-coral-600">{{ $step[0] }}</span>
                    <h3 class="mt-3 sm:mt-5 font-bold text-sm sm:text-base text-slate-950 dark:text-white">{{ $step[1] }}</h3>
                    <p class="mt-1.5 text-xs leading-5 text-slate-500 dark:text-slate-400">{{ $step[2] }}</p>
                </li>
            @endforeach
        </ol>
    </section>

    <section class="py-14 sm:py-20 text-center" data-reveal>
        <h2 class="text-2xl font-bold tracking-tight text-slate-950 dark:text-white sm:text-3xl">Siap mengambil langkah berikutnya?</h2>
        <p class="mx-auto mt-2.5 sm:mt-3 max-w-xl text-xs sm:text-sm leading-6 text-slate-600 dark:text-slate-300 px-4">
            Cari pekerjaan yang jelas atau bantu usaha lokal menemukan anggota tim yang tepat.
        </p>
        <div class="mt-6 sm:mt-7 flex flex-col sm:flex-row justify-center gap-2.5 sm:gap-3 max-w-sm sm:max-w-none mx-auto">
            <a href="{{ route('jobs.index') }}" class="portal-button-primary w-full sm:w-auto justify-center">Cari lowongan</a>
            @guest
                <a href="{{ route('register') }}" class="portal-button-secondary w-full sm:w-auto justify-center">Daftar sebagai mitra</a>
            @endguest
        </div>
    </section>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        // 1. Animasi Typewriter pada Kolom Pencarian (Search Input Placeholder)
        const searchInput = document.getElementById('home-search');
        if (searchInput) {
            const searchSuggestions = [
                'Kasir, barista, Bandung...',
                'Staff admin, Surabaya...',
                'Desain grafis, Jakarta...',
                'Barista kedai kopi, Yogyakarta...',
                'Video editor, Bandung...',
                'Pramuniaga toko, Semarang...',
                'Content creator, Bali...'
            ];
            let sIdx = 0;
            let sChar = searchSuggestions[0].length;
            let sDeleting = false;
            let isFocused = false;

            function stepSearchType() {
                if (isFocused || searchInput.value.trim().length > 0) {
                    setTimeout(stepSearchType, 1000);
                    return;
                }

                const suggestion = searchSuggestions[sIdx];

                if (sDeleting) {
                    sChar--;
                    searchInput.setAttribute('placeholder', suggestion.substring(0, sChar));
                } else {
                    sChar++;
                    searchInput.setAttribute('placeholder', suggestion.substring(0, sChar));
                }

                let delay = sDeleting ? 30 : 65;

                if (!sDeleting && sChar === suggestion.length) {
                    delay = 2200;
                    sDeleting = true;
                } else if (sDeleting && sChar === 0) {
                    sDeleting = false;
                    sIdx = (sIdx + 1) % searchSuggestions.length;
                    delay = 400;
                }

                setTimeout(stepSearchType, delay);
            }

            searchInput.addEventListener('focus', () => { isFocused = true; });
            searchInput.addEventListener('blur', () => {
                isFocused = false;
                if (!searchInput.value.trim()) {
                    sChar = 0;
                    sDeleting = false;
                }
            });

            setTimeout(() => {
                sDeleting = true;
                stepSearchType();
            }, 2200);
        }

        // 3. Animasi Rolling / Rolling Numbers pada Statistik Metrik
        const counterEls = document.querySelectorAll('[data-counter]');
        if (counterEls.length > 0) {
            const animateCounter = (el) => {
                const target = parseInt(el.dataset.counter, 10) || 0;
                if (target === 0) {
                    el.textContent = '0';
                    return;
                }

                const duration = 1500;
                const start = performance.now();

                function step(now) {
                    const elapsed = now - start;
                    const progress = Math.min(elapsed / duration, 1);
                    // Smooth ease-out cubic
                    const ease = 1 - Math.pow(1 - progress, 3);
                    const currentVal = Math.round(ease * target);

                    el.textContent = currentVal.toLocaleString('id-ID');

                    if (progress < 1) {
                        requestAnimationFrame(step);
                    } else {
                        el.textContent = target.toLocaleString('id-ID');
                        el.classList.add('transition-transform', 'duration-200', 'scale-110');
                        setTimeout(() => el.classList.remove('scale-110'), 250);
                    }
                }

                requestAnimationFrame(step);
            };

            if ('IntersectionObserver' in window) {
                const observer = new IntersectionObserver((entries, obs) => {
                    entries.forEach(entry => {
                        if (entry.isIntersecting) {
                            obs.unobserve(entry.target);
                            animateCounter(entry.target);
                        }
                    });
                }, { threshold: 0.25 });

                counterEls.forEach(el => observer.observe(el));
            } else {
                counterEls.forEach(el => animateCounter(el));
            }
        }
    });
</script>
@endpush
