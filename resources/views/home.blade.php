@extends('layouts.app')

@section('title', 'KerjaLokal | Peluang Kerja Layak untuk Ekonomi Lokal')

@section('content')
    <section class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-[0_24px_70px_-48px_rgba(15,23,42,0.35)] dark:border-slate-800 dark:bg-slate-900">
        <div class="grid lg:min-h-[500px] lg:grid-cols-[0.9fr_1.1fr]">
            <div class="flex flex-col justify-center px-6 py-10 sm:px-10 sm:py-14 lg:px-14">
                <h1 class="max-w-xl text-4xl font-bold leading-[1.08] tracking-[-0.035em] text-slate-950 dark:text-white sm:text-5xl lg:text-6xl">
                    Kerja yang jelas.<br>
                    <span class="text-brand-700 dark:text-brand-300">Usaha tumbuh.</span>
                </h1>

                <p class="mt-5 max-w-lg text-base leading-7 text-slate-600 dark:text-slate-300">
                    Temukan lowongan UMKM dengan informasi upah, jam kerja, dan lokasi yang mudah dipahami.
                </p>

                <form action="{{ route('jobs.index') }}" method="GET" class="mt-7 flex max-w-xl flex-col gap-2 sm:flex-row">
                    <label for="home-search" class="sr-only">Cari posisi atau lokasi</label>
                    <div class="relative flex-1">
                        <span class="material-symbols-outlined pointer-events-none absolute left-4 top-1/2 -translate-y-1/2 text-[21px] text-slate-400" aria-hidden="true">search</span>
                        <input
                            id="home-search"
                            name="q"
                            type="search"
                            class="min-h-12 w-full rounded-xl border border-slate-300 bg-white py-3 pl-12 pr-4 text-sm text-slate-900 outline-none transition placeholder:text-slate-500 focus:border-brand-600 focus:ring-4 focus:ring-brand-100 dark:border-slate-700 dark:bg-slate-950 dark:text-white dark:placeholder:text-slate-400 dark:focus:ring-brand-900/60"
                            placeholder="Posisi atau lokasi"
                        >
                    </div>
                    <button type="submit" class="inline-flex min-h-12 items-center justify-center gap-2 rounded-xl bg-brand-700 px-6 text-sm font-bold text-white transition hover:bg-brand-800 focus:outline-none focus:ring-4 focus:ring-brand-200 active:translate-y-px dark:bg-brand-500 dark:text-brand-950 dark:hover:bg-brand-400 dark:focus:ring-brand-900">
                        <span class="material-symbols-outlined text-[19px]" aria-hidden="true">search</span>
                        <span>Cari kerja</span>
                    </button>
                </form>
            </div>

            <div class="relative min-h-[220px] sm:min-h-[300px] lg:min-h-full">
                <img
                    src="{{ asset('images/hero-kerjalokal.jpg') }}"
                    alt="Talenta lokal sedang bekerja bersama"
                    class="absolute inset-0 h-full w-full object-cover object-[center_30%]"
                >
                <div class="absolute inset-0 bg-brand-950/10" aria-hidden="true"></div>
            </div>
        </div>
    </section>

    <section class="py-12 sm:py-16">
        <div class="grid gap-7 lg:grid-cols-[0.55fr_1.45fr] lg:gap-14">
            <div class="lg:pt-3">
                <h2 class="text-2xl font-bold tracking-tight text-slate-950 dark:text-white sm:text-3xl">Lowongan terbaru</h2>
                <p class="mt-2 text-sm leading-6 text-slate-600 dark:text-slate-300">Pilih peluang yang sesuai dengan pengalamanmu.</p>
                <a href="{{ route('jobs.index') }}" class="mt-5 inline-flex items-center gap-2 text-sm font-bold text-brand-700 transition hover:text-brand-900 dark:text-brand-300 dark:hover:text-brand-200">
                    <span>Lihat semua</span>
                    <span class="material-symbols-outlined text-[18px]" aria-hidden="true">arrow_forward</span>
                </a>
            </div>

            <div class="border-y border-slate-200 dark:border-slate-800">
                @forelse ($featuredJobs as $job)
                    <a href="{{ route('jobs.show', $job) }}" class="group grid gap-3 border-b border-slate-200 px-1 py-5 last:border-b-0 sm:grid-cols-[1fr_auto] sm:items-center sm:gap-6 dark:border-slate-800">
                        <div class="min-w-0">
                            <h3 class="truncate text-base font-bold text-slate-950 transition group-hover:text-brand-700 dark:text-white dark:group-hover:text-brand-300 sm:text-lg">{{ $job->title }}</h3>
                            <div class="mt-1 flex flex-wrap gap-x-3 gap-y-1 text-sm text-slate-500 dark:text-slate-400">
                                <span>{{ $job->employer->business_name ?: $job->employer->name }}</span>
                                <span>{{ $job->location }}</span>
                            </div>
                        </div>
                        <div class="flex items-center justify-between gap-4 sm:justify-end">
                            <span class="text-sm font-semibold text-slate-700 dark:text-slate-200">
                                Rp {{ number_format($job->salary_amount, 0, ',', '.') }} / {{ $job->salary_type === 'monthly' ? 'bulan' : 'hari' }}
                            </span>
                            <span class="material-symbols-outlined text-[20px] text-brand-700 transition group-hover:translate-x-1 dark:text-brand-300" aria-hidden="true">arrow_forward</span>
                        </div>
                    </a>
                @empty
                    <div class="py-10 text-sm text-slate-500 dark:text-slate-400">Belum ada lowongan aktif.</div>
                @endforelse
            </div>
        </div>
    </section>
@endsection
