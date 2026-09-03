@extends('layouts.app')

@section('title', $job->title.' | KerjaLokal')

@section('content')
    <a href="{{ route('jobs.index') }}" class="mb-5 inline-flex items-center gap-1 text-sm font-bold text-brand-700 hover:underline dark:text-brand-300"><span class="material-symbols-outlined text-[18px]">arrow_back</span>Kembali ke daftar lowongan</a>
    <div class="grid gap-6 lg:grid-cols-[minmax(0,1fr)_380px]">
        <div class="space-y-5">
            <section class="rounded-3xl bg-brand-950 p-6 text-white sm:p-9" data-reveal>
                <div class="flex flex-wrap items-center gap-2"><span class="rounded-lg bg-white/10 px-2.5 py-1 text-xs font-bold text-brand-100">{{ $job->category->name }}</span><span class="rounded-lg bg-emerald-400/15 px-2.5 py-1 text-xs font-bold text-emerald-200">{{ $job->status === 'open' ? 'Masih dibuka' : 'Ditutup' }}</span></div>
                <h1 class="mt-6 text-3xl font-bold tracking-tight sm:text-4xl">{{ $job->title }}</h1><p class="mt-2 text-base text-brand-100">{{ $job->employer->business_name }}</p>
                <dl class="mt-8 grid gap-3 sm:grid-cols-3"><div class="rounded-2xl bg-white/10 p-4"><dt class="text-xs text-brand-200">Lokasi</dt><dd class="mt-1 text-sm font-bold">{{ $job->location }}</dd></div><div class="rounded-2xl bg-white/10 p-4"><dt class="text-xs text-brand-200">Upah</dt><dd class="mt-1 text-sm font-bold">Rp {{ number_format($job->salary_amount, 0, ',', '.') }} / {{ $job->salary_type === 'monthly' ? 'bulan' : 'hari' }}</dd></div><div class="rounded-2xl bg-white/10 p-4"><dt class="text-xs text-brand-200">Jam kerja</dt><dd class="mt-1 text-sm font-bold">{{ $job->work_hours_per_day }} jam / hari</dd></div></dl>
            </section>

            <section class="rounded-2xl border border-slate-200 bg-white p-6 dark:border-slate-800 dark:bg-slate-900" data-reveal><h2 class="text-lg font-bold text-slate-950 dark:text-white">Deskripsi pekerjaan</h2><div class="mt-4 whitespace-pre-line text-sm leading-7 text-slate-600 dark:text-slate-300">{{ $job->description }}</div></section>
            <section class="rounded-2xl border border-slate-200 bg-white p-6 dark:border-slate-800 dark:bg-slate-900" data-reveal><h2 class="text-lg font-bold text-slate-950 dark:text-white">Keterampilan yang dicari</h2><div class="mt-4 flex flex-wrap gap-2">@foreach($job->skills as $skill)<span class="rounded-lg bg-brand-50 px-3 py-1.5 text-xs font-bold text-brand-700 dark:bg-brand-950 dark:text-brand-300">{{ $skill->name }}</span>@endforeach</div></section>
            <section class="rounded-2xl border border-slate-200 bg-white p-6 dark:border-slate-800 dark:bg-slate-900" data-reveal><h2 class="text-lg font-bold text-slate-950 dark:text-white">Tentang mitra</h2><p class="mt-3 text-sm font-bold">{{ $job->employer->business_name }}</p><p class="mt-1 text-sm text-slate-500">Penanggung jawab: {{ $job->employer->name }}</p>@auth @if(auth()->user()->hasRole('jobseeker'))<a href="{{ route('chat.index') }}" class="portal-button-secondary mt-5 w-fit"><span class="material-symbols-outlined text-[18px]">chat</span>Pesan mitra</a>@endif @endauth</section>
        </div>

        <aside class="lg:sticky lg:top-24 lg:self-start">
            <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-xl shadow-slate-900/5 dark:border-slate-800 dark:bg-slate-900 sm:p-6" data-reveal>
                @guest
                    <div class="grid h-12 w-12 place-items-center rounded-2xl bg-brand-50 text-brand-700 dark:bg-brand-950 dark:text-brand-300"><span class="material-symbols-outlined">login</span></div><h2 class="mt-5 text-xl font-bold">Masuk untuk melamar</h2><p class="mt-2 text-sm leading-6 text-slate-500">Buat akun pencari kerja untuk mengirim resume dan memantau status seleksi.</p><a href="{{ route('login') }}" class="portal-button-primary mt-6 w-full">Masuk</a><a href="{{ route('register') }}" class="portal-button-secondary mt-2 w-full">Daftar</a>
                @elseif(auth()->user()->hasRole('jobseeker'))
                    @if($hasApplied)
                        <div class="grid h-12 w-12 place-items-center rounded-2xl bg-emerald-50 text-emerald-700 dark:bg-emerald-950/40 dark:text-emerald-300"><span class="material-symbols-outlined">task_alt</span></div><h2 class="mt-5 text-xl font-bold">Lamaran sudah dikirim</h2><p class="mt-2 text-sm leading-6 text-slate-500">Status terbaru dapat dilihat pada riwayat lamaran Anda.</p><a href="{{ route('applications.index') }}" class="portal-button-primary mt-6 w-full">Lihat riwayat</a>
                    @elseif($job->status === 'open')
                        <h2 class="text-xl font-bold">Ajukan lamaran</h2><p class="mt-1 text-sm text-slate-500">Resume disimpan privat dan hanya dapat diunduh mitra pemilik lowongan serta admin.</p>
                        <form action="{{ route('applications.store', $job) }}" method="POST" enctype="multipart/form-data" class="mt-6 space-y-5">@csrf
                            <div><label for="resume" class="mb-2 block text-sm font-bold">Resume PDF</label><input id="resume" name="resume" type="file" accept="application/pdf,.pdf" required class="block w-full rounded-xl border border-slate-200 bg-slate-50 text-xs file:mr-3 file:border-0 file:bg-brand-700 file:px-3 file:py-3 file:font-bold file:text-white dark:border-slate-700 dark:bg-slate-950"><p class="mt-1.5 text-xs text-slate-400">Maksimal 2 MB.</p>@error('resume')<p class="mt-1.5 text-xs font-semibold text-rose-600" role="alert">{{ $message }}</p>@enderror</div>
                            <div><label for="note" class="mb-2 block text-sm font-bold">Catatan singkat</label><textarea id="note" name="note" rows="4" class="w-full rounded-xl border border-slate-200 bg-slate-50 p-3 text-sm outline-none focus:border-brand-500 focus:ring-4 focus:ring-brand-100 dark:border-slate-700 dark:bg-slate-950 dark:focus:ring-brand-900" placeholder="Ceritakan pengalaman yang relevan">{{ old('note') }}</textarea>@error('note')<p class="mt-1.5 text-xs font-semibold text-rose-600" role="alert">{{ $message }}</p>@enderror</div>
                            <button class="portal-button-primary w-full"><span class="material-symbols-outlined text-[18px]">send</span>Kirim lamaran</button>
                        </form>
                    @else
                        <h2 class="text-xl font-bold">Lowongan telah ditutup</h2><p class="mt-2 text-sm leading-6 text-slate-500">Mitra tidak lagi menerima lamaran untuk posisi ini.</p>
                    @endif
                @else
                    <h2 class="text-xl font-bold">Tampilan informasi</h2><p class="mt-2 text-sm leading-6 text-slate-500">Akun {{ auth()->user()->hasRole('admin') ? 'admin' : 'mitra' }} dapat melihat detail lowongan tanpa mengajukan lamaran.</p>
                @endguest
            </div>
        </aside>
    </div>
@endsection
