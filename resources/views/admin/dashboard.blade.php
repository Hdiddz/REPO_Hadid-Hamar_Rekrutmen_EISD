@extends('layouts.admin')

@section('title', 'Dashboard Admin | KerjaLokal')
@section('portal_title', 'Ringkasan sistem rekrutmen')
@section('portal_description', 'Pantau aktivitas lowongan, partisipasi mitra, dan hasil seleksi dalam ekosistem KerjaLokal.')
@section('portal_actions')<a href="{{ route('admin.jobs.index') }}" class="portal-button-primary"><span class="material-symbols-outlined text-[18px]">visibility</span>Tinjau lowongan</a>@endsection

@section('content')
    <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-5" data-reveal>
        @foreach ([['work', $metrics['open_jobs'], 'Lowongan aktif'], ['task_alt', $metrics['accepted_workers'], 'Pekerja diterima'], ['storefront', $metrics['employers'], 'Mitra UMKM'], ['person', $metrics['jobseekers'], 'Pencari kerja'], ['payments', 'Rp '.number_format($metrics['wage_circulation'], 0, ',', '.'), 'Nilai upah']] as $metric)
            <div class="portal-stat-card"><span class="material-symbols-outlined text-brand-600 dark:text-brand-300">{{ $metric[0] }}</span><strong class="mt-5 block text-2xl font-bold">{{ $metric[1] }}</strong><span class="mt-1 block text-xs text-slate-500">{{ $metric[2] }}</span></div>
        @endforeach
    </section>

    <div class="mt-7 grid gap-6 xl:grid-cols-[1.4fr_0.8fr]">
        <section class="portal-panel overflow-hidden" data-reveal><div class="portal-panel-header"><div><h2 class="font-bold">Lowongan terbaru</h2><p class="text-xs text-slate-500">Termasuk detail akun mitra penerbit.</p></div><a href="{{ route('admin.jobs.index') }}" class="text-sm font-bold text-brand-700 hover:underline dark:text-brand-300">Lihat semua</a></div><div class="divide-y divide-slate-100 dark:divide-slate-800">@forelse($recentJobs as $job)<a href="{{ route('admin.jobs.show', $job) }}" class="flex items-center justify-between gap-4 p-5 transition hover:bg-slate-50 dark:hover:bg-slate-800/40"><div><p class="text-xs font-bold uppercase tracking-wider text-brand-700 dark:text-brand-300">{{ $job->category->name }}</p><h3 class="mt-1 font-bold">{{ $job->title }}</h3><p class="mt-1 text-xs text-slate-500">{{ $job->employer->business_name }} · {{ $job->employer->email }} · {{ $job->applications_count }} pelamar</p></div><span class="material-symbols-outlined text-slate-400">chevron_right</span></a>@empty<div class="p-8 text-center text-sm text-slate-500">Belum ada lowongan.</div>@endforelse</div></section>
        <section class="portal-panel overflow-hidden" data-reveal><div class="portal-panel-header"><div><h2 class="font-bold">Lamaran terbaru</h2><p class="text-xs text-slate-500">Aktivitas kandidat terbaru.</p></div></div><div class="divide-y divide-slate-100 dark:divide-slate-800">@forelse($recentApplications as $application)<div class="p-4"><div class="flex items-center justify-between gap-3"><div><p class="text-sm font-bold">{{ $application->user->name }}</p><p class="mt-0.5 text-xs text-slate-500">{{ $application->job->title }}</p></div><span class="rounded-lg bg-slate-100 px-2 py-1 text-[11px] font-bold dark:bg-slate-800">{{ ucfirst($application->status) }}</span></div></div>@empty<div class="p-8 text-center text-sm text-slate-500">Belum ada lamaran.</div>@endforelse</div></section>
    </div>
@endsection
