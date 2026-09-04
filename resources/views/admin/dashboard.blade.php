@extends('layouts.admin')

@section('title', 'Dashboard Admin | KerjaLokal')
@section('portal_title', 'Ringkasan sistem rekrutmen')
@section('portal_description', 'Pantau aktivitas lowongan, partisipasi mitra, laporan kepatuhan, dan hasil seleksi dalam ekosistem KerjaLokal.')
@section('portal_actions')
    <a href="{{ route('admin.reports.index') }}" class="portal-action-btn !px-2.5 sm:!px-3" title="Laporan Pelamar">
        <span class="material-symbols-outlined text-[17px]">flag</span>
        <span>Laporan<span class="hidden sm:inline"> Pelamar</span> ({{ $metrics['pending_reports'] }})</span>
    </a>
    <a href="{{ route('admin.jobs.index') }}" class="portal-action-btn-primary !px-2.5 sm:!px-3" title="Tinjau Lowongan">
        <span class="material-symbols-outlined text-[17px]">visibility</span>
        <span>Tinjau<span class="hidden sm:inline"> Lowongan</span></span>
    </a>
@endsection

@section('content')
    <section class="grid gap-3 sm:gap-4 grid-cols-2 lg:grid-cols-3 xl:grid-cols-5" data-reveal>
        @foreach ([
            ['work', $metrics['open_jobs'], 'Lowongan aktif'],
            ['task_alt', $metrics['accepted_workers'], 'Pekerja diterima'],
            ['storefront', $metrics['employers'], 'Mitra UMKM'],
            ['person', $metrics['jobseekers'], 'Pencari kerja'],
            ['flag', $metrics['pending_reports'], 'Laporan aduan pending'],
        ] as $metric)
            <div class="portal-stat-card">
                <span class="material-symbols-outlined {{ $metric[0] === 'flag' ? 'text-rose-600 dark:text-rose-400' : 'text-brand-600 dark:text-brand-300' }}">{{ $metric[0] }}</span>
                <strong class="mt-4 block text-2xl font-bold">{{ $metric[1] }}</strong>
                <span class="mt-1 block text-xs text-slate-500">{{ $metric[2] }}</span>
            </div>
        @endforeach
    </section>

    <!-- Recent Reports Notification Banner if pending > 0 -->
    @if($metrics['pending_reports'] > 0)
        <div class="mt-6 rounded-2xl bg-rose-50 p-4 border border-rose-200 dark:bg-rose-950/40 dark:border-rose-900/50 flex flex-col sm:flex-row sm:items-center justify-between gap-3" data-reveal>
            <div class="flex items-center gap-3">
                <span class="material-symbols-outlined text-rose-600 text-2xl">priority_high</span>
                <div>
                    <h3 class="font-bold text-sm text-rose-900 dark:text-rose-200">Perhatian Pengawasan: {{ $metrics['pending_reports'] }} Laporan Masuk Menunggu Tinjauan</h3>
                    <p class="text-xs text-rose-700 dark:text-rose-300 mt-0.5">Terdapat indikasi aduan percaloan, upah tidak transparan, atau jam kerja eksploitatif yang perlu ditindaklanjuti.</p>
                </div>
            </div>
            <a href="{{ route('admin.reports.index', ['status' => 'pending']) }}" class="portal-button-primary !bg-rose-600 hover:!bg-rose-700 shrink-0 text-xs">
                Tinjau Semua Laporan
            </a>
        </div>
    @endif

    <div class="mt-7 grid gap-6 xl:grid-cols-[1.2fr_0.8fr_0.8fr]">
        <!-- Recent Jobs -->
        <section class="portal-panel overflow-hidden" data-reveal>
            <div class="portal-panel-header bg-indigo-50/70 dark:bg-indigo-950/30 border-b border-indigo-100 dark:border-indigo-900/40">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-indigo-100 text-indigo-700 dark:bg-indigo-900/60 dark:text-indigo-300 flex items-center justify-center shrink-0">
                        <span class="material-symbols-outlined text-[19px]">work</span>
                    </div>
                    <div>
                        <h2 class="font-bold text-slate-900 dark:text-white text-base">Lowongan terbaru</h2>
                        <p class="text-xs text-indigo-950/60 dark:text-indigo-300/70">Termasuk detail akun mitra penerbit.</p>
                    </div>
                </div>
                <a href="{{ route('admin.jobs.index') }}" class="text-xs sm:text-sm font-bold text-indigo-700 hover:text-indigo-800 dark:text-indigo-400 hover:underline inline-flex items-center gap-1 shrink-0">
                    <span>Lihat semua</span>
                    <span class="material-symbols-outlined text-[16px]">chevron_right</span>
                </a>
            </div>
            <div class="divide-y divide-slate-100 dark:divide-slate-800">
                @forelse($recentJobs as $job)
                    <a href="{{ route('admin.jobs.show', ['job' => $job, 'return_to' => url()->full()]) }}" class="flex items-center justify-between gap-4 p-4 transition hover:bg-slate-50 dark:hover:bg-slate-800/40">
                        <div class="min-w-0">
                            <p class="text-xs font-bold uppercase tracking-wider text-brand-700 dark:text-brand-300">{{ $job->category->name }}</p>
                            <h3 class="mt-1 font-bold text-sm truncate">{{ $job->title }}</h3>
                            <p class="mt-1 text-xs text-slate-500 truncate">{{ $job->employer->business_name }} · {{ $job->applications_count }} pelamar</p>
                        </div>
                        <span class="material-symbols-outlined text-slate-400 shrink-0">chevron_right</span>
                    </a>
                @empty
                    <div class="p-8 text-center text-sm text-slate-500">Belum ada lowongan.</div>
                @endforelse
            </div>
        </section>

        <!-- Recent Reports -->
        <section class="portal-panel overflow-hidden" data-reveal>
            <div class="portal-panel-header bg-rose-50/70 dark:bg-rose-950/30 border-b border-rose-100 dark:border-rose-900/40">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-rose-100 text-rose-700 dark:bg-rose-900/60 dark:text-rose-300 flex items-center justify-center shrink-0">
                        <span class="material-symbols-outlined text-[19px]">flag</span>
                    </div>
                    <div>
                        <h2 class="font-bold text-rose-950 dark:text-rose-100 text-base">Aduan &amp; Laporan</h2>
                        <p class="text-xs text-rose-950/60 dark:text-rose-300/70">Laporan aduan pelamar.</p>
                    </div>
                </div>
                <a href="{{ route('admin.reports.index') }}" class="text-xs sm:text-sm font-bold text-rose-600 hover:text-rose-700 dark:text-rose-400 hover:underline inline-flex items-center gap-1 shrink-0">
                    <span>Semua</span>
                    <span class="material-symbols-outlined text-[16px]">chevron_right</span>
                </a>
            </div>
            <div class="divide-y divide-slate-100 dark:divide-slate-800">
                @forelse($recentReports as $report)
                    <a href="{{ route('admin.reports.show', ['report' => $report, 'return_to' => url()->full()]) }}" class="block p-4 transition hover:bg-slate-50 dark:hover:bg-slate-800/40">
                        <div class="flex items-center justify-between gap-2">
                            <span class="font-bold text-xs text-rose-700 dark:text-rose-300 truncate">{{ $report->reason }}</span>
                            <span class="text-[10px] text-slate-400 shrink-0">{{ $report->created_at->diffForHumans() }}</span>
                        </div>
                        <p class="text-xs text-slate-600 dark:text-slate-300 mt-1 truncate">Lowongan: {{ $report->job->title }}</p>
                        <p class="text-[11px] text-slate-400 mt-0.5">Pelapor: {{ $report->reporter->name }}</p>
                    </a>
                @empty
                    <div class="p-8 text-center text-sm text-slate-500">Belum ada laporan masuk.</div>
                @endforelse
            </div>
        </section>

        <!-- Recent Applications -->
        <section class="portal-panel overflow-hidden" data-reveal>
            <div class="portal-panel-header bg-emerald-50/70 dark:bg-emerald-950/30 border-b border-emerald-100 dark:border-emerald-900/40">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-emerald-100 text-emerald-700 dark:bg-emerald-900/60 dark:text-emerald-300 flex items-center justify-center shrink-0">
                        <span class="material-symbols-outlined text-[19px]">assignment_turned_in</span>
                    </div>
                    <div>
                        <h2 class="font-bold text-slate-900 dark:text-white text-base">Lamaran terbaru</h2>
                        <p class="text-xs text-emerald-950/60 dark:text-emerald-300/70">Aktivitas kandidat terbaru.</p>
                    </div>
                </div>
            </div>
            <div class="divide-y divide-slate-100 dark:divide-slate-800">
                @forelse($recentApplications as $application)
                    <div class="p-4">
                        <div class="flex items-center justify-between gap-3">
                            <div class="min-w-0">
                                <p class="text-sm font-bold truncate">{{ $application->user->name }}</p>
                                <p class="mt-0.5 text-xs text-slate-500 truncate">{{ $application->job->title }}</p>
                            </div>
                            <span class="rounded-lg bg-slate-100 px-2 py-1 text-[11px] font-bold dark:bg-slate-800 shrink-0">{{ ucfirst($application->status) }}</span>
                        </div>
                    </div>
                @empty
                    <div class="p-8 text-center text-sm text-slate-500">Belum ada lamaran.</div>
                @endforelse
            </div>
        </section>
    </div>
@endsection
