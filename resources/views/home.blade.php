@extends('layouts.app')

@section('title', 'KerjaLokal | Peluang Kerja Layak untuk Ekonomi Lokal')

@section('content')
    <section class="relative overflow-hidden rounded-[2rem] bg-brand-950 text-white shadow-2xl shadow-brand-950/20" data-reveal>
        <div class="grid min-h-[620px] lg:grid-cols-[1.04fr_0.96fr]">
            <div class="relative z-10 flex flex-col justify-center px-6 py-14 sm:px-10 lg:px-14 lg:py-20">
                <div class="mb-7 inline-flex w-fit items-center gap-2 rounded-full border border-white/15 bg-white/10 px-3 py-1.5 text-xs font-bold uppercase tracking-[0.16em] text-brand-100 backdrop-blur"><span class="h-1.5 w-1.5 rounded-full bg-coral-400"></span>Ekosistem kerja lokal</div>
                <h1 class="max-w-3xl text-4xl font-bold leading-[1.05] tracking-[-0.04em] sm:text-6xl lg:text-7xl">Kerja yang layak.<br><span class="text-brand-200">Usaha yang bertumbuh.</span></h1>
                <p class="mt-6 max-w-xl text-base leading-7 text-brand-100 sm:text-lg">KerjaLokal mempertemukan talenta dengan UMKM melalui lowongan yang menyebutkan upah, jam kerja, dan keterampilan secara jelas.</p>
                <form action="{{ route('jobs.index') }}" method="GET" class="mt-8 flex max-w-xl flex-col gap-2 rounded-2xl bg-white p-2 shadow-xl sm:flex-row">
                    <label for="home-search" class="sr-only">Cari posisi atau lokasi</label>
                    <div class="relative flex-1"><span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400">search</span><input id="home-search" name="q" type="search" class="min-h-12 w-full rounded-xl border-0 py-3 pl-11 pr-3 text-sm text-slate-900 outline-none ring-0" placeholder="Kasir, barista, Bandung"></div>
                    <button class="min-h-12 rounded-xl bg-coral-600 px-6 text-sm font-bold text-white transition hover:bg-coral-700">Cari peluang</button>
                </form>
                <div class="mt-8 flex flex-wrap gap-6 text-sm text-brand-100"><span class="flex items-center gap-2"><span class="material-symbols-outlined text-[19px] text-brand-300">payments</span>Upah transparan</span><span class="flex items-center gap-2"><span class="material-symbols-outlined text-[19px] text-brand-300">schedule</span>Maksimal 8 jam per hari</span></div>
            </div>
            <div class="relative min-h-[390px] lg:min-h-full">
                <img src="{{ asset('images/hero-kerjalokal.jpg') }}" alt="Pekerja dan pemilik usaha lokal sedang berkolaborasi" class="absolute inset-0 h-full w-full object-cover">
                <div class="absolute inset-0 bg-brand-950/25"></div>
                <div class="absolute bottom-6 right-6 max-w-xs rounded-2xl border border-white/20 bg-slate-950/70 p-4 backdrop-blur-md" data-reveal>
                    <p class="text-xs font-bold uppercase tracking-widest text-brand-200">Dampak nyata</p>
                    <p class="mt-2 text-sm leading-6 text-white">Satu ruang untuk pencari kerja, mitra UMKM, dan admin menjaga proses tetap terhubung.</p>
                </div>
            </div>
        </div>
    </section>

    <section class="-mt-5 relative z-10 mx-3 grid overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-xl sm:mx-8 sm:grid-cols-3 dark:border-slate-800 dark:bg-slate-900" aria-label="Ringkasan platform" data-reveal>
        @foreach ([['value' => $metrics['open_jobs'], 'label' => 'lowongan aktif'], ['value' => $metrics['employers'], 'label' => 'mitra UMKM'], ['value' => $metrics['accepted_workers'], 'label' => 'pekerja diterima']] as $metric)
            <div class="border-b border-slate-100 px-6 py-5 last:border-0 sm:border-b-0 sm:border-r sm:last:border-r-0 dark:border-slate-800"><strong class="block text-3xl font-bold tracking-tight text-brand-700 dark:text-brand-300">{{ number_format($metric['value']) }}</strong><span class="mt-1 block text-sm text-slate-500 dark:text-slate-400">{{ $metric['label'] }}</span></div>
        @endforeach
    </section>

    <section class="py-20" data-reveal>
        <div class="mb-9 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <div><p class="text-xs font-bold uppercase tracking-[0.18em] text-coral-600">Peluang terbaru</p><h2 class="mt-2 text-3xl font-bold tracking-tight text-slate-950 dark:text-white sm:text-4xl">Temukan tempat untuk bertumbuh.</h2></div>
            <a href="{{ route('jobs.index') }}" class="portal-button-secondary w-fit">Lihat semua lowongan<span class="material-symbols-outlined text-[18px]">arrow_forward</span></a>
        </div>
        <div class="grid gap-4 lg:grid-cols-2">
            @forelse ($featuredJobs as $job)
                <a href="{{ route('jobs.show', $job) }}" class="group rounded-2xl border border-slate-200 bg-white p-5 transition hover:-translate-y-1 hover:border-brand-300 hover:shadow-xl dark:border-slate-800 dark:bg-slate-900 dark:hover:border-brand-700">
                    <div class="flex items-start justify-between gap-5"><div><span class="text-xs font-bold uppercase tracking-wider text-brand-700 dark:text-brand-300">{{ $job->category->name }}</span><h3 class="mt-2 text-xl font-bold text-slate-950 group-hover:text-brand-700 dark:text-white">{{ $job->title }}</h3><p class="mt-1 text-sm text-slate-500">{{ $job->employer->business_name }} · {{ $job->location }}</p></div><span class="material-symbols-outlined rounded-xl bg-brand-50 p-2 text-brand-700 transition group-hover:translate-x-1 dark:bg-brand-950 dark:text-brand-300">arrow_outward</span></div>
                    <div class="mt-5 flex flex-wrap gap-2 text-xs font-semibold"><span class="rounded-lg bg-emerald-50 px-2.5 py-1.5 text-emerald-700 dark:bg-emerald-950/40 dark:text-emerald-300">Rp {{ number_format($job->salary_amount, 0, ',', '.') }} / {{ $job->salary_type === 'monthly' ? 'bulan' : 'hari' }}</span><span class="rounded-lg bg-slate-100 px-2.5 py-1.5 text-slate-600 dark:bg-slate-800 dark:text-slate-300">{{ $job->work_hours_per_day }} jam / hari</span></div>
                </a>
            @empty
                <div class="rounded-2xl border border-dashed border-slate-300 p-10 text-center text-sm text-slate-500 lg:col-span-2">Belum ada lowongan aktif. Mitra UMKM dapat mulai menerbitkan lowongan.</div>
            @endforelse
        </div>
    </section>

    <section class="grid gap-8 rounded-[2rem] bg-amber-50 px-6 py-12 dark:bg-slate-900 sm:px-10 lg:grid-cols-[0.8fr_1.2fr] lg:items-center" data-reveal>
        <div><p class="text-xs font-bold uppercase tracking-[0.18em] text-coral-600">Alur sederhana</p><h2 class="mt-3 text-3xl font-bold tracking-tight text-slate-950 dark:text-white">Dari peluang sampai keputusan.</h2><p class="mt-4 text-sm leading-6 text-slate-600 dark:text-slate-300">Setiap role mendapat ruang kerja yang fokus, tanpa memutus aliran data antarproses.</p></div>
        <ol class="grid gap-3 sm:grid-cols-3">
            @foreach ([['01', 'Temukan', 'Saring peluang berdasarkan kategori, lokasi, dan keterampilan.'], ['02', 'Ajukan', 'Kirim resume PDF dan catatan melalui satu formulir.'], ['03', 'Pantau', 'Lihat status seleksi yang diperbarui langsung oleh mitra.']] as $step)
                <li class="rounded-2xl bg-white p-5 dark:bg-slate-950"><span class="text-xs font-black text-coral-600">{{ $step[0] }}</span><h3 class="mt-5 font-bold text-slate-950 dark:text-white">{{ $step[1] }}</h3><p class="mt-2 text-xs leading-5 text-slate-500 dark:text-slate-400">{{ $step[2] }}</p></li>
            @endforeach
        </ol>
    </section>

    <section class="py-20 text-center" data-reveal><h2 class="text-3xl font-bold tracking-tight text-slate-950 dark:text-white">Siap mengambil langkah berikutnya?</h2><p class="mx-auto mt-3 max-w-xl text-sm leading-6 text-slate-600 dark:text-slate-300">Cari pekerjaan yang jelas atau bantu usaha lokal menemukan anggota tim yang tepat.</p><div class="mt-7 flex flex-wrap justify-center gap-3"><a href="{{ route('jobs.index') }}" class="portal-button-primary">Cari lowongan</a>@guest<a href="{{ route('register') }}" class="portal-button-secondary">Daftar sebagai mitra</a>@endguest</div></section>
@endsection
