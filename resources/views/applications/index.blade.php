@extends('layouts.app')

@section('title', 'Riwayat Lamaran | KerjaLokal')

@section('content')
    <div class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between" data-reveal><div><p class="text-xs font-bold uppercase tracking-[0.18em] text-coral-600">Proses seleksi</p><h1 class="mt-2 text-3xl font-bold tracking-tight text-slate-950 dark:text-white">Riwayat lamaran Anda.</h1><p class="mt-2 text-sm text-slate-500">Status diperbarui oleh mitra UMKM pada satu alur yang terhubung.</p></div><a href="{{ route('jobs.index') }}" class="portal-button-primary w-fit"><span class="material-symbols-outlined text-[18px]">search</span>Cari lowongan</a></div>

    <div class="space-y-4">
        @forelse($applications as $application)
            @php
                $status = match($application->status) {
                    'accepted' => ['Diterima', 'bg-emerald-50 text-emerald-700 border-emerald-200 dark:bg-emerald-950/40 dark:text-emerald-300 dark:border-emerald-900', 'task_alt'],
                    'rejected' => ['Belum sesuai', 'bg-rose-50 text-rose-700 border-rose-200 dark:bg-rose-950/40 dark:text-rose-300 dark:border-rose-900', 'cancel'],
                    'interview' => ['Wawancara', 'bg-blue-50 text-blue-700 border-blue-200 dark:bg-blue-950/40 dark:text-blue-300 dark:border-blue-900', 'forum'],
                    default => ['Menunggu tinjauan', 'bg-amber-50 text-amber-700 border-amber-200 dark:bg-amber-950/40 dark:text-amber-300 dark:border-amber-900', 'hourglass_top'],
                };
            @endphp
            <article class="rounded-2xl border border-slate-200 bg-white p-5 dark:border-slate-800 dark:bg-slate-900 sm:p-6" data-reveal>
                <div class="flex flex-col gap-5 md:flex-row md:items-start md:justify-between">
                    <div><div class="flex flex-wrap items-center gap-2 text-xs text-slate-400"><span class="font-bold uppercase tracking-wider text-brand-700 dark:text-brand-300">{{ $application->job->category->name }}</span><span>·</span><span>Dikirim {{ $application->created_at->translatedFormat('d M Y, H:i') }}</span></div><h2 class="mt-2 text-xl font-bold text-slate-950 dark:text-white"><a href="{{ route('jobs.show', $application->job) }}" class="hover:text-brand-700">{{ $application->job->title }}</a></h2><p class="mt-1 text-sm text-slate-500">{{ $application->job->employer->business_name }} · {{ $application->job->location }}</p></div>
                    <span class="inline-flex w-fit items-center gap-1.5 rounded-xl border px-3 py-1.5 text-xs font-bold {{ $status[1] }}"><span class="material-symbols-outlined text-[17px]">{{ $status[2] }}</span>{{ $status[0] }}</span>
                </div>
                <div class="mt-5 grid gap-3 border-t border-slate-100 pt-5 text-sm sm:grid-cols-3 dark:border-slate-800"><div><span class="block text-xs text-slate-400">Upah</span><strong class="mt-1 block">Rp {{ number_format($application->job->salary_amount, 0, ',', '.') }} / {{ $application->job->salary_type === 'monthly' ? 'bulan' : 'hari' }}</strong></div><div><span class="block text-xs text-slate-400">Jam kerja</span><strong class="mt-1 block">{{ $application->job->work_hours_per_day }} jam per hari</strong></div><div><span class="block text-xs text-slate-400">Berkas</span><strong class="mt-1 block">Resume PDF tersimpan</strong></div></div>
                @if($application->note)<p class="mt-4 rounded-xl bg-slate-50 p-3 text-xs leading-5 text-slate-600 dark:bg-slate-950 dark:text-slate-300"><strong>Catatan:</strong> {{ $application->note }}</p>@endif
            </article>
        @empty
            <div class="rounded-3xl border border-dashed border-slate-300 bg-white p-14 text-center dark:border-slate-700 dark:bg-slate-900"><span class="material-symbols-outlined text-5xl text-slate-300">assignment</span><h2 class="mt-4 text-lg font-bold">Belum ada lamaran</h2><p class="mt-2 text-sm text-slate-500">Pilih lowongan yang sesuai dan kirim resume PDF Anda.</p><a href="{{ route('jobs.index') }}" class="portal-button-primary mt-6">Mulai mencari</a></div>
        @endforelse
    </div>
    <div class="mt-8">{{ $applications->links() }}</div>
@endsection
