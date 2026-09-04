@extends('layouts.app')

@section('title', 'Riwayat Laporan Pengaduan | KerjaLokal')

@section('content')
    <div class="mb-6 sm:mb-8 flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between" data-reveal>
        <div>
            <p class="text-xs font-bold uppercase tracking-[0.18em] text-coral-600 dark:text-coral-400">Pengawasan & Keamanan</p>
            <h1 class="mt-1.5 sm:mt-2 text-2xl sm:text-3xl font-bold tracking-tight text-slate-950 dark:text-white">Riwayat Laporan Pengaduan</h1>
            <p class="mt-1.5 sm:mt-2 text-xs sm:text-sm text-slate-500 dark:text-slate-400">
                Pantau progres peninjauan dan tindakan penegakan etis oleh Tim Pengawas KerjaLokal terhadap lowongan yang Anda laporkan.
            </p>
        </div>
        @if(auth()->user()->hasRole('employer'))
            <a href="{{ route('employer.dashboard') }}" class="portal-button-primary w-full sm:w-fit justify-center gap-1.5">
                <span class="material-symbols-outlined text-[18px]">dashboard</span>
                <span>Dasbor Mitra</span>
            </a>
        @else
            <a href="{{ route('jobs.index') }}" class="portal-button-primary w-full sm:w-fit justify-center gap-1.5">
                <span class="material-symbols-outlined text-[18px]">search</span>
                <span>Cari Lowongan</span>
            </a>
        @endif
    </div>

    {{-- Filter Tabs Status Laporan --}}
    <div class="mb-5 sm:mb-6 flex items-center gap-2 overflow-x-auto no-scrollbar pb-1.5 -mx-4 px-4 sm:mx-0 sm:px-0" data-reveal>
        <a href="{{ route('reports.index') }}" class="inline-flex items-center gap-1.5 rounded-xl px-3 sm:px-3.5 py-2 text-xs font-bold whitespace-nowrap shrink-0 transition {{ !request('status') ? 'bg-brand-700 text-white shadow-xs dark:bg-brand-500 dark:text-brand-950' : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-50 dark:bg-slate-900 dark:text-slate-300 dark:border-slate-800' }}">
            <span>Semua Laporan</span>
            @if(isset($counts['all']))
                <span class="rounded-full bg-black/10 dark:bg-white/10 px-1.5 py-0.2 text-[10px]">{{ $counts['all'] }}</span>
            @endif
        </a>
        <a href="{{ route('reports.index', ['status' => 'pending']) }}" class="inline-flex items-center gap-1.5 rounded-xl px-3 sm:px-3.5 py-2 text-xs font-bold whitespace-nowrap shrink-0 transition {{ request('status') === 'pending' ? 'bg-amber-600 text-white shadow-xs' : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-50 dark:bg-slate-900 dark:text-slate-300 dark:border-slate-800' }}">
            <span class="material-symbols-outlined text-[16px]">hourglass_empty</span>
            <span>Menunggu Tinjauan</span>
            @if(isset($counts['pending']) && $counts['pending'] > 0)
                <span class="rounded-full bg-amber-100 text-amber-800 dark:bg-amber-950 dark:text-amber-300 px-1.5 py-0.2 text-[10px]">{{ $counts['pending'] }}</span>
            @endif
        </a>
        <a href="{{ route('reports.index', ['status' => 'reviewed']) }}" class="inline-flex items-center gap-1.5 rounded-xl px-3 sm:px-3.5 py-2 text-xs font-bold whitespace-nowrap shrink-0 transition {{ request('status') === 'reviewed' ? 'bg-blue-600 text-white shadow-xs' : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-50 dark:bg-slate-900 dark:text-slate-300 dark:border-slate-800' }}">
            <span class="material-symbols-outlined text-[16px]">visibility</span>
            <span>Sedang Ditinjau</span>
            @if(isset($counts['reviewed']) && $counts['reviewed'] > 0)
                <span class="rounded-full bg-blue-100 text-blue-800 dark:bg-blue-950 dark:text-blue-300 px-1.5 py-0.2 text-[10px]">{{ $counts['reviewed'] }}</span>
            @endif
        </a>
        <a href="{{ route('reports.index', ['status' => 'action_taken']) }}" class="inline-flex items-center gap-1.5 rounded-xl px-3 sm:px-3.5 py-2 text-xs font-bold whitespace-nowrap shrink-0 transition {{ request('status') === 'action_taken' ? 'bg-emerald-600 text-white shadow-xs' : 'bg-emerald-50 text-emerald-800 border border-emerald-200 hover:bg-emerald-100 dark:bg-emerald-950/40 dark:text-emerald-300 dark:border-emerald-900/60' }}">
            <span class="material-symbols-outlined text-[16px]">verified</span>
            <span>Tindakan Diambil</span>
            @if(isset($counts['action_taken']) && $counts['action_taken'] > 0)
                <span class="rounded-full bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300 px-1.5 py-0.2 text-[10px]">{{ $counts['action_taken'] }}</span>
            @endif
        </a>
        <a href="{{ route('reports.index', ['status' => 'dismissed']) }}" class="inline-flex items-center gap-1.5 rounded-xl px-3 sm:px-3.5 py-2 text-xs font-bold whitespace-nowrap shrink-0 transition {{ request('status') === 'dismissed' ? 'bg-slate-700 text-white shadow-xs' : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-50 dark:bg-slate-900 dark:text-slate-300 dark:border-slate-800' }}">
            <span class="material-symbols-outlined text-[16px]">check_circle</span>
            <span>Laporan Selesai</span>
            @if(isset($counts['dismissed']) && $counts['dismissed'] > 0)
                <span class="rounded-full bg-slate-100 text-slate-800 dark:bg-slate-800 dark:text-slate-300 px-1.5 py-0.2 text-[10px]">{{ $counts['dismissed'] }}</span>
            @endif
        </a>
    </div>

    {{-- Alert Status Bantuan --}}
    @if(request('status') === 'pending')
        <div class="mb-5 p-4 rounded-2xl bg-amber-50 dark:bg-amber-950/40 border border-amber-200 dark:border-amber-900/60 text-amber-900 dark:text-amber-200 text-xs flex items-center gap-3" data-reveal>
            <span class="material-symbols-outlined text-[24px] text-amber-600 shrink-0">hourglass_top</span>
            <div>
                <strong class="block font-bold text-sm">Menunggu Tinjauan Tim Pengawas</strong>
                <span>Laporan ini telah masuk dalam antrean investigasi tim KerjaLokal dan akan segera diperiksa kesesuaiannya dengan pedoman etis.</span>
            </div>
        </div>
    @elseif(request('status') === 'action_taken')
        <div class="mb-5 p-4 rounded-2xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-900/60 text-emerald-900 dark:text-emerald-200 text-xs flex items-center gap-3" data-reveal>
            <span class="material-symbols-outlined text-[24px] text-emerald-600 shrink-0">verified</span>
            <div>
                <strong class="block font-bold text-sm">Tindakan Resmi Telah Diterapkan</strong>
                <span>Tim Pengawas telah mengambil langkah tegas berupa penutupan lowongan atau peringatan/sanksi akun terhadap pihak terkait.</span>
            </div>
        </div>
    @endif

    <div class="space-y-4">
        @forelse($reports as $report)
            @php
                $statusConfig = match($report->status) {
                    'action_taken' => [
                        'label' => 'Tindakan Resmi Diambil',
                        'class' => 'bg-emerald-50 text-emerald-700 border-emerald-200 dark:bg-emerald-950/50 dark:text-emerald-300 dark:border-emerald-800',
                        'icon' => 'verified',
                    ],
                    'resolved' => [
                        'label' => 'Laporan Selesai Ditangani',
                        'class' => 'bg-emerald-50 text-emerald-700 border-emerald-200 dark:bg-emerald-950/50 dark:text-emerald-300 dark:border-emerald-800',
                        'icon' => 'task_alt',
                    ],
                    'reviewed' => [
                        'label' => 'Sedang Ditinjau Pengawas',
                        'class' => 'bg-blue-50 text-blue-700 border-blue-200 dark:bg-blue-950/50 dark:text-blue-300 dark:border-blue-800',
                        'icon' => 'visibility',
                    ],
                    'dismissed' => [
                        'label' => 'Laporan Ditutup',
                        'class' => 'bg-slate-100 text-slate-700 border-slate-300 dark:bg-slate-800 dark:text-slate-300 dark:border-slate-700',
                        'icon' => 'check_circle',
                    ],
                    default => [
                        'label' => 'Menunggu Tinjauan',
                        'class' => 'bg-amber-50 text-amber-700 border-amber-200 dark:bg-amber-950/50 dark:text-amber-300 dark:border-amber-800',
                        'icon' => 'hourglass_empty',
                    ],
                };
            @endphp
            <article class="rounded-2xl border border-slate-200 bg-white p-5 sm:p-6 shadow-xs dark:border-slate-800 dark:bg-slate-900 overflow-hidden" data-reveal>
                <div class="flex flex-col gap-4 md:flex-row md:items-start md:justify-between">
                    <div class="min-w-0 flex-1">
                        <div class="flex flex-wrap items-center gap-2 text-xs text-slate-400 mb-1.5">
                            <span class="font-bold uppercase tracking-wider text-brand-700 dark:text-brand-300">
                                {{ $report->job?->category?->name ?? 'Lowongan Kerja' }}
                            </span>
                            <span>·</span>
                            <span>Dilaporkan {{ $report->created_at->translatedFormat('d M Y, H:i') }} WIB</span>
                        </div>
                        <h2 class="text-lg sm:text-xl font-bold text-slate-900 dark:text-white break-words">
                            @if($report->job)
                                <a href="{{ route('jobs.show', $report->job) }}" class="hover:text-brand-700 transition-colors">
                                    {{ $report->job->title }}
                                </a>
                            @else
                                <span class="text-slate-500 italic">Lowongan ini telah dihapus atau tidak tersedia lagi</span>
                            @endif
                        </h2>
                        <p class="mt-1 text-xs sm:text-sm text-slate-500 dark:text-slate-400 break-words">
                            {{ $report->job?->employer?->business_name ?: ($report->job?->employer?->name ?? 'Mitra Terlapor') }}
                            @if($report->job?->location)
                                · {{ $report->job->location }}
                            @endif
                        </p>
                    </div>

                    <div class="flex items-center gap-2.5 shrink-0 flex-wrap">
                        <span class="inline-flex items-center gap-1.5 rounded-xl border px-3 py-1.5 text-xs font-bold {{ $statusConfig['class'] }}">
                            <span class="material-symbols-outlined text-[17px]">{{ $statusConfig['icon'] }}</span>
                            <span>{{ $statusConfig['label'] }}</span>
                        </span>
                        @if($report->job)
                            <a href="{{ route('jobs.show', ['job' => $report->job, 'return_to' => route('reports.index')]) }}" class="portal-button-secondary !py-1.5 !px-3 text-xs font-bold gap-1.5 shadow-xs" title="Lihat detail lowongan yang dilaporkan">
                                <span class="material-symbols-outlined text-[16px]">visibility</span>
                                <span>Lihat Lowongan</span>
                            </a>
                        @endif

                        <form id="hideReportForm-{{ $report->id }}" action="{{ route('reports.destroy', $report) }}" method="POST" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="button" data-report-id="{{ $report->id }}" data-job-title="{{ $report->job?->title ?? 'Laporan ini' }}" onclick="confirmHideReport(this.dataset.reportId, this.dataset.jobTitle)" class="inline-flex items-center gap-1 rounded-xl border border-slate-200 bg-white px-2.5 py-1.5 text-xs font-bold text-slate-600 shadow-xs hover:bg-rose-50 hover:border-rose-200 hover:text-rose-700 dark:border-slate-800 dark:bg-slate-900 dark:text-slate-400 dark:hover:bg-rose-950/40 dark:hover:text-rose-300 transition cursor-pointer" title="Hapus dari riwayat laporan Anda">
                                <span class="material-symbols-outlined text-[16px]">delete_outline</span>
                                <span class="hidden sm:inline">Hapus Riwayat</span>
                            </button>
                        </form>
                    </div>
                </div>

                {{-- Detail Laporan --}}
                <div class="mt-4 pt-4 border-t border-slate-100 dark:border-slate-800/80 space-y-3">
                    <div class="flex items-center gap-2 flex-wrap text-xs">
                        <span class="font-bold text-slate-500 dark:text-slate-400">Kategori Pelanggaran:</span>
                        <span class="inline-flex items-center gap-1 rounded-md bg-rose-50 px-2.5 py-1 font-bold text-rose-700 border border-rose-200 dark:bg-rose-950/50 dark:text-rose-300 dark:border-rose-900/60">
                            <span class="material-symbols-outlined text-[14px]">flag</span>
                            {{ $report->reason }}
                        </span>
                    </div>

                    <div class="rounded-xl bg-slate-50 p-3.5 sm:p-4 dark:bg-slate-950/60 border border-slate-100 dark:border-slate-800 text-xs text-slate-700 dark:text-slate-300 leading-relaxed overflow-hidden">
                        <strong class="block text-slate-900 dark:text-white font-semibold mb-1">Rincian Pengaduan yang Anda Kirimkan:</strong>
                        <p class="whitespace-pre-wrap break-words italic">"{{ $report->details }}"</p>
                    </div>

                    @if($report->admin_notes)
                        <div class="rounded-xl bg-teal-50/70 p-3.5 sm:p-4 dark:bg-teal-950/40 border border-teal-200/80 dark:border-teal-900/60 text-xs text-teal-950 dark:text-teal-200 leading-relaxed overflow-hidden">
                            <div class="flex items-center gap-2 font-bold mb-1 text-teal-900 dark:text-teal-300">
                                <span class="material-symbols-outlined text-[18px]">admin_panel_settings</span>
                                <span>Catatan & Tindak Lanjut Tim Pengawas:</span>
                            </div>
                            <p class="whitespace-pre-wrap break-words">{{ $report->admin_notes }}</p>
                        </div>
                    @endif
                </div>
            </article>
        @empty
            <div class="rounded-2xl border border-dashed border-slate-300 bg-white p-12 text-center dark:border-slate-700 dark:bg-slate-900">
                <span class="material-symbols-outlined text-4xl text-slate-300 dark:text-slate-600">verified_user</span>
                <h2 class="mt-3 font-bold text-slate-900 dark:text-white text-base">Belum Ada Riwayat Laporan</h2>
                <p class="mt-1 text-xs text-slate-500 dark:text-slate-400 max-w-md mx-auto">
                    Anda belum pernah mengirimkan laporan pengaduan lowongan kerja. KerjaLokal menjunjung tinggi lingkungan kerja yang etis, transparan, dan bebas pungutan liar.
                </p>
                <div class="mt-5">
                    @if(auth()->user()->hasRole('employer'))
                        <a href="{{ route('employer.dashboard') }}" class="portal-button-primary inline-flex items-center gap-2 text-xs">
                            <span class="material-symbols-outlined text-[16px]">arrow_back</span>
                            <span>Kembali ke Dasbor Mitra</span>
                        </a>
                    @else
                        <a href="{{ route('jobs.index') }}" class="portal-button-primary inline-flex items-center gap-2 text-xs">
                            <span class="material-symbols-outlined text-[16px]">search</span>
                            <span>Jelajahi Lowongan Pekerjaan</span>
                        </a>
                    @endif
                </div>
            </div>
        @endforelse
    </div>

    <div class="mt-7">{{ $reports->links() }}</div>
@endsection

@push('scripts')
<script>
    async function confirmHideReport(reportId, jobTitle) {
        const confirmed = await window.showAppConfirm({
            title: 'Hapus dari Riwayat Laporan?',
            message: `Apakah Anda yakin ingin menghapus laporan untuk "${jobTitle}" dari riwayat Anda?\n\nLaporan ini tidak akan ditampilkan lagi di daftar riwayat laporan Anda.`,
            confirmText: 'Ya, Hapus dari Riwayat',
            cancelText: 'Batal',
            type: 'danger',
            icon: 'delete'
        });

        if (confirmed) {
            document.getElementById(`hideReportForm-${reportId}`)?.submit();
        }
    }
</script>
@endpush
