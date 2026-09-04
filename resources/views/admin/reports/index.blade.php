@extends('layouts.admin')

@section('title', 'Laporan Pelanggaran Lowongan | Admin KerjaLokal')
@section('portal_title', 'Laporan & Pengaduan Pelamar')
@section('portal_description', 'Tinjau laporan masuk dari pencari kerja terkait indikasi percaloan, penipuan, upah tidak transparan, atau pelanggaran etis.')

@section('content')
    <!-- Stat Counter Cards -->
    <div class="grid gap-4 sm:grid-cols-3 mb-6">
        <a href="{{ route('admin.reports.index', ['status' => 'pending']) }}" class="portal-stat-card group hover:border-amber-400 transition {{ $currentStatus === 'pending' ? 'ring-2 ring-amber-500/40 border-amber-300 dark:border-amber-700' : '' }}">
            <div class="flex items-center justify-between">
                <span class="material-symbols-outlined text-amber-600 text-2xl group-hover:scale-110 transition-transform">pending_actions</span>
                <span class="text-[11px] font-bold text-amber-700 dark:text-amber-400 bg-amber-50 dark:bg-amber-950/60 px-2 py-0.5 rounded-md">Pending</span>
            </div>
            <strong class="mt-3 block text-2xl font-bold">{{ $counts['pending'] }}</strong>
            <span class="text-xs text-slate-500">Laporan Menunggu Tinjauan</span>
        </a>

        <a href="{{ route('admin.reports.index', ['status' => 'action_taken']) }}" class="portal-stat-card group hover:border-rose-400 transition {{ $currentStatus === 'action_taken' ? 'ring-2 ring-rose-500/40 border-rose-300 dark:border-rose-700' : '' }}">
            <div class="flex items-center justify-between">
                <span class="material-symbols-outlined text-rose-600 text-2xl group-hover:scale-110 transition-transform">gavel</span>
                <span class="text-[11px] font-bold text-rose-700 dark:text-rose-400 bg-rose-50 dark:bg-rose-950/60 px-2 py-0.5 rounded-md">Ditindak</span>
            </div>
            <strong class="mt-3 block text-2xl font-bold">{{ $counts['action_taken'] }}</strong>
            <span class="text-xs text-slate-500">Telah Diberikan Tindakan</span>
        </a>

        <a href="{{ route('admin.reports.index', ['status' => 'resolved']) }}" class="portal-stat-card group hover:border-emerald-400 transition {{ $currentStatus === 'resolved' ? 'ring-2 ring-emerald-500/40 border-emerald-300 dark:border-emerald-700' : '' }}">
            <div class="flex items-center justify-between">
                <span class="material-symbols-outlined text-emerald-600 text-2xl group-hover:scale-110 transition-transform">check_circle</span>
                <span class="text-[11px] font-bold text-emerald-700 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-950/60 px-2 py-0.5 rounded-md">Tuntas</span>
            </div>
            <strong class="mt-3 block text-2xl font-bold">{{ $counts['resolved'] }}</strong>
            <span class="text-xs text-slate-500">Laporan Selesai / Ditolak</span>
        </a>
    </div>

    <!-- Filter Form -->
    <form method="GET" class="mb-6 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm dark:border-slate-800 dark:bg-slate-900">
        <div class="grid gap-3 sm:grid-cols-12 sm:items-end">
            <div class="sm:col-span-6">
                <label for="q" class="portal-label">Pencarian laporan</label>
                <div class="relative">
                    <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-[18px]">search</span>
                    <input id="q" name="q" value="{{ request('q') }}" class="portal-input pl-9" placeholder="Cari isi laporan, judul lowongan, atau nama pelapor...">
                </div>
            </div>
            <div class="sm:col-span-4">
                <label for="status" class="portal-label">Status laporan</label>
                <select id="status" name="status" class="portal-input">
                    <option value="active" @selected($currentStatus === 'active')>Laporan Aktif (Belum Selesai)</option>
                    <option value="pending" @selected($currentStatus === 'pending')>Menunggu Tinjauan (Pending)</option>
                    <option value="reviewed" @selected($currentStatus === 'reviewed')>Sedang Ditinjau (Reviewed)</option>
                    <option value="action_taken" @selected($currentStatus === 'action_taken')>Telah Diberikan Sanksi</option>
                    <option value="resolved" @selected($currentStatus === 'resolved')>Selesai / Ditolak</option>
                    <option value="all" @selected($currentStatus === 'all')>Semua Status Laporan</option>
                </select>
            </div>
            <div class="sm:col-span-2 flex items-center gap-2">
                <button type="submit" class="portal-button-primary w-full justify-center">Filter</button>
                <a href="{{ route('admin.reports.index') }}" class="portal-button-secondary" title="Reset filter">
                    <span class="material-symbols-outlined text-[18px]">refresh</span>
                </a>
            </div>
        </div>
    </form>

    <!-- Quick Status Tabs & Action Toolbar -->
    <div class="mb-4 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div class="flex items-center gap-1.5 overflow-x-auto no-scrollbar pb-1">
            <a href="{{ route('admin.reports.index', array_merge(request()->except('status', 'page'), ['status' => 'active'])) }}" 
               class="inline-flex items-center gap-1.5 rounded-xl px-3 py-1.5 text-xs font-bold whitespace-nowrap shrink-0 transition {{ $currentStatus === 'active' ? 'bg-blue-50 text-blue-700 border border-blue-200 dark:bg-blue-950/60 dark:text-blue-300 dark:border-blue-900 shadow-2xs' : 'bg-white text-slate-600 border border-slate-200/80 hover:text-blue-600 hover:bg-blue-50/50 dark:bg-slate-900 dark:text-slate-300 dark:border-slate-800' }}">
                <span class="material-symbols-outlined text-[16px]">inbox</span>
                <span>Laporan Aktif</span>
                @if($counts['active'] > 0)
                    <span class="rounded-full bg-blue-100 text-blue-800 dark:bg-blue-900/60 dark:text-blue-200 px-1.5 py-0.2 text-[10px]">{{ $counts['active'] }}</span>
                @endif
            </a>

            <a href="{{ route('admin.reports.index', array_merge(request()->except('status', 'page'), ['status' => 'pending'])) }}" 
               class="inline-flex items-center gap-1.5 rounded-xl px-3 py-1.5 text-xs font-bold whitespace-nowrap shrink-0 transition {{ $currentStatus === 'pending' ? 'bg-amber-50 text-amber-700 border border-amber-200 dark:bg-amber-950/60 dark:text-amber-300 dark:border-amber-900 shadow-2xs' : 'bg-white text-slate-600 border border-slate-200/80 hover:text-blue-600 hover:bg-blue-50/50 dark:bg-slate-900 dark:text-slate-300 dark:border-slate-800' }}">
                <span class="material-symbols-outlined text-[16px]">pending_actions</span>
                <span>Menunggu Tinjauan</span>
                @if($counts['pending'] > 0)
                    <span class="rounded-full bg-amber-100 text-amber-800 dark:bg-amber-950 dark:text-amber-300 px-1.5 py-0.2 text-[10px]">{{ $counts['pending'] }}</span>
                @endif
            </a>

            <a href="{{ route('admin.reports.index', array_merge(request()->except('status', 'page'), ['status' => 'reviewed'])) }}" 
               class="inline-flex items-center gap-1.5 rounded-xl px-3 py-1.5 text-xs font-bold whitespace-nowrap shrink-0 transition {{ $currentStatus === 'reviewed' ? 'bg-indigo-50 text-indigo-700 border border-indigo-200 dark:bg-indigo-950/60 dark:text-indigo-300 dark:border-indigo-900 shadow-2xs' : 'bg-white text-slate-600 border border-slate-200/80 hover:text-blue-600 hover:bg-blue-50/50 dark:bg-slate-900 dark:text-slate-300 dark:border-slate-800' }}">
                <span class="material-symbols-outlined text-[16px]">visibility</span>
                <span>Sedang Ditinjau</span>
                @if($counts['reviewed'] > 0)
                    <span class="rounded-full bg-indigo-100 text-indigo-800 dark:bg-indigo-950 dark:text-indigo-300 px-1.5 py-0.2 text-[10px]">{{ $counts['reviewed'] }}</span>
                @endif
            </a>

            <a href="{{ route('admin.reports.index', array_merge(request()->except('status', 'page'), ['status' => 'action_taken'])) }}" 
               class="inline-flex items-center gap-1.5 rounded-xl px-3 py-1.5 text-xs font-bold whitespace-nowrap shrink-0 transition {{ $currentStatus === 'action_taken' ? 'bg-rose-50 text-rose-700 border border-rose-200 dark:bg-rose-950/60 dark:text-rose-300 dark:border-rose-900 shadow-2xs' : 'bg-white text-slate-600 border border-slate-200/80 hover:text-blue-600 hover:bg-blue-50/50 dark:bg-slate-900 dark:text-slate-300 dark:border-slate-800' }}">
                <span class="material-symbols-outlined text-[16px]">gavel</span>
                <span>Telah Ditindak</span>
                @if($counts['action_taken'] > 0)
                    <span class="rounded-full bg-rose-100 text-rose-800 dark:bg-rose-950 dark:text-rose-300 px-1.5 py-0.2 text-[10px]">{{ $counts['action_taken'] }}</span>
                @endif
            </a>

            <a href="{{ route('admin.reports.index', array_merge(request()->except('status', 'page'), ['status' => 'resolved'])) }}" 
               class="inline-flex items-center gap-1.5 rounded-xl px-3 py-1.5 text-xs font-bold whitespace-nowrap shrink-0 transition {{ $currentStatus === 'resolved' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200 dark:bg-emerald-950/60 dark:text-emerald-300 dark:border-emerald-900 shadow-2xs' : 'bg-white text-slate-600 border border-slate-200/80 hover:text-blue-600 hover:bg-blue-50/50 dark:bg-slate-900 dark:text-slate-300 dark:border-slate-800' }}">
                <span class="material-symbols-outlined text-[16px]">task_alt</span>
                <span>Selesai / Ditolak</span>
                @if($counts['resolved'] > 0)
                    <span class="rounded-full bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300 px-1.5 py-0.2 text-[10px]">{{ $counts['resolved'] }}</span>
                @endif
            </a>

            <a href="{{ route('admin.reports.index', array_merge(request()->except('status', 'page'), ['status' => 'all'])) }}" 
               class="inline-flex items-center gap-1.5 rounded-xl px-3 py-1.5 text-xs font-bold whitespace-nowrap shrink-0 transition {{ $currentStatus === 'all' ? 'bg-slate-100 text-slate-900 border border-slate-300 dark:bg-slate-800 dark:text-white dark:border-slate-700 shadow-2xs' : 'bg-white text-slate-600 border border-slate-200/80 hover:text-blue-600 hover:bg-blue-50/50 dark:bg-slate-900 dark:text-slate-300 dark:border-slate-800' }}">
                <span>Semua Status</span>
                <span class="rounded-full bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300 px-1.5 py-0.2 text-[10px]">{{ $counts['all'] }}</span>
            </a>
        </div>

        @if(($currentStatus === 'resolved' || $currentStatus === 'all') && $counts['resolved'] > 0)
            <button type="button" onclick="openClearCompletedModal()" class="portal-button-secondary !py-1.5 !px-3 text-xs text-rose-700 hover:bg-rose-50 hover:border-rose-300 dark:text-rose-400 dark:hover:bg-rose-950/40 shrink-0 self-end sm:self-auto cursor-pointer" title="Bersihkan semua laporan yang sudah selesai dari panel admin">
                <span class="material-symbols-outlined text-[16px]">cleaning_services</span>
                <span>Bersihkan Riwayat Selesai</span>
            </button>
        @endif
    </div>

    <!-- Reports Table for Desktop & Tablet (No Horizontal Scrolling) -->
    <div class="hidden md:block portal-table-shell overflow-hidden">
        <table class="portal-table w-full table-fixed">
            <thead>
                <tr>
                    <th class="w-4/12">Lowongan &amp; Mitra UMKM</th>
                    <th class="w-4/12">Pengaduan &amp; Pelapor</th>
                    <th class="w-2/12">Status Laporan</th>
                    <th class="w-2/12 text-right">Aksi Navigasi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($reports as $report)
                    <tr class="hover:bg-slate-50/60 dark:hover:bg-slate-850/40 transition">
                        <!-- 1. Lowongan & Mitra UMKM -->
                        <td class="align-top py-3.5">
                            <div class="min-w-0 pr-2">
                                <a href="{{ route('admin.jobs.show', ['job' => $report->job, 'return_to' => url()->full()]) }}" class="font-bold text-sm text-slate-900 hover:text-blue-600 dark:text-white dark:hover:text-blue-400 transition block truncate" title="{{ $report->job->title }}">
                                    {{ $report->job->title }}
                                </a>
                                <div class="text-xs text-slate-500 dark:text-slate-400 mt-0.5 truncate">
                                    <span>{{ $report->job->category->name }}</span>
                                    <span class="mx-1 text-slate-300 dark:text-slate-600">·</span>
                                    <span>{{ $report->job->location }}</span>
                                </div>
                                <div class="text-xs mt-1 text-slate-500 dark:text-slate-400 flex items-center gap-1 truncate">
                                    <span class="text-slate-400 dark:text-slate-500 shrink-0">Mitra:</span>
                                    <a href="{{ route('admin.users.show', ['user' => $report->job->employer, 'return_to' => url()->full()]) }}" class="font-semibold text-blue-600 hover:text-blue-800 dark:text-blue-400 hover:underline truncate" title="Buka profil mitra {{ $report->job->employer->business_name ?: $report->job->employer->name }}">
                                        {{ $report->job->employer->business_name ?: $report->job->employer->name }}
                                    </a>
                                </div>
                            </div>
                        </td>

                        <!-- 2. Pengaduan & Pelapor -->
                        <td class="align-top py-3.5">
                            <div class="min-w-0 pr-2">
                                <span class="font-bold text-xs text-rose-700 dark:text-rose-400 block truncate" title="{{ $report->reason }}">
                                    {{ $report->reason }}
                                </span>
                                @if($report->details)
                                    <p class="text-[11px] text-slate-600 dark:text-slate-400 mt-0.5 line-clamp-1 italic" title="{{ $report->details }}">
                                        "{{ $report->details }}"
                                    </p>
                                @endif
                                <div class="text-xs mt-1 text-slate-500 dark:text-slate-400 flex items-center gap-1 truncate">
                                    <span class="text-slate-400 dark:text-slate-500 shrink-0">Pelapor:</span>
                                    <a href="{{ route('admin.users.show', ['user' => $report->reporter, 'return_to' => url()->full()]) }}" class="font-semibold text-blue-600 hover:text-blue-800 dark:text-blue-400 hover:underline truncate" title="Buka profil pelapor {{ $report->reporter->name }}">
                                        {{ $report->reporter->name }}
                                    </a>
                                    <span class="text-slate-300 dark:text-slate-600 shrink-0">·</span>
                                    <span class="text-[11px] text-slate-400 shrink-0">{{ $report->created_at->diffForHumans() }}</span>
                                </div>
                            </div>
                        </td>

                        <!-- 3. Status Laporan -->
                        <td class="align-top py-3.5">
                            <div class="min-w-0">
                                <span class="portal-badge {{ match($report->status) {
                                    'action_taken' => 'bg-rose-50 text-rose-800 border border-rose-200 dark:bg-rose-950/50 dark:text-rose-300 dark:border-rose-900',
                                    'resolved' => 'bg-emerald-50 text-emerald-800 border border-emerald-200 dark:bg-emerald-950/50 dark:text-emerald-300 dark:border-emerald-900',
                                    'dismissed' => 'bg-slate-100 text-slate-600 border border-slate-200 dark:bg-slate-800 dark:text-slate-300 dark:border-slate-700',
                                    'reviewed' => 'bg-blue-50 text-blue-800 border border-blue-200 dark:bg-blue-950/50 dark:text-blue-300 dark:border-blue-900',
                                    default => 'bg-amber-50 text-amber-800 border border-amber-200 dark:bg-amber-950/50 dark:text-amber-300 dark:border-amber-900'
                                } }} text-[10px]">
                                    {{ match($report->status) {
                                        'action_taken' => 'Ditindaklanjuti',
                                        'resolved' => 'Selesai',
                                        'dismissed' => 'Ditolak',
                                        'reviewed' => 'Ditinjau',
                                        default => 'Menunggu Tinjauan'
                                    } }}
                                </span>
                                @if($report->action_taken)
                                    <span class="block text-[10px] text-slate-500 dark:text-slate-400 mt-1 line-clamp-1" title="{{ $report->action_taken }}">
                                        {{ $report->action_taken }}
                                    </span>
                                @endif
                            </div>
                        </td>

                        <!-- 4. Aksi Navigasi (Langsung di layar, tanpa perlu geser ke kanan) -->
                        <td class="align-top py-3.5 text-right">
                            <div class="flex items-center justify-end gap-1.5">
                                <!-- Teks Ringkas Biru untuk Navigasi Tinjau -->
                                <a href="{{ route('admin.reports.show', $report) }}" class="inline-flex items-center gap-1 text-xs font-bold text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 hover:underline px-1 py-1 transition" title="Tinjau rincian laporan">
                                    <span class="material-symbols-outlined text-[16px]">visibility</span>
                                    <span>Tinjau</span>
                                </a>

                                <!-- Tombol Selesaikan (Pill Hijau Ringkas) -->
                                @if($report->status !== 'resolved' && $report->status !== 'dismissed')
                                    <button type="button" 
                                            onclick="openResolveModal({{ $report->id }}, '{{ addslashes($report->job?->title ?? 'Lowongan') }}')" 
                                            class="inline-flex items-center gap-1 rounded-lg bg-emerald-50 px-2 py-1 text-xs font-bold text-emerald-700 hover:bg-emerald-100 border border-emerald-200/80 dark:bg-emerald-950/40 dark:border-emerald-900/60 dark:text-emerald-300 dark:hover:bg-emerald-900/60 transition cursor-pointer" 
                                            title="Selesaikan laporan ini">
                                        <span class="material-symbols-outlined text-[14px]">task_alt</span>
                                        <span>Selesai</span>
                                    </button>
                                @endif

                                <!-- Tombol Hapus Riwayat -->
                                <button type="button" 
                                        onclick="openDeleteModal({{ $report->id }}, '{{ addslashes($report->job?->title ?? 'Lowongan') }}', '{{ $report->status }}')" 
                                        class="inline-flex items-center justify-center w-7 h-7 rounded-lg border border-slate-200 bg-white text-slate-400 shadow-2xs hover:bg-rose-50 hover:border-rose-200 hover:text-rose-700 dark:bg-slate-900 dark:border-slate-800 dark:text-slate-400 dark:hover:bg-rose-950/40 dark:hover:text-rose-300 transition cursor-pointer" 
                                        title="Hapus riwayat laporan ini dari panel admin">
                                    <span class="material-symbols-outlined text-[15px]">delete_outline</span>
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="py-12 text-center text-slate-500">
                            <span class="material-symbols-outlined text-4xl text-slate-300 block mb-2">verified</span>
                            @if($currentStatus === 'active')
                                Semua laporan pengaduan telah selesai ditangani. Tidak ada laporan aktif yang memerlukan tindakan.
                            @else
                                Tidak ada riwayat laporan pengaduan yang sesuai kriteria.
                            @endif
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Mobile Cards View (Bebas Geser Kanan) -->
    <div class="block md:hidden space-y-3">
        @forelse($reports as $report)
            <div class="rounded-2xl border border-slate-200/80 bg-white p-4 shadow-2xs dark:border-slate-800 dark:bg-slate-900">
                <!-- Top Status & Timestamp -->
                <div class="flex items-center justify-between gap-2 pb-2.5 border-b border-slate-100 dark:border-slate-800">
                    <span class="portal-badge {{ match($report->status) {
                        'action_taken' => 'bg-rose-50 text-rose-800 border border-rose-200 dark:bg-rose-950/50 dark:text-rose-300 dark:border-rose-900',
                        'resolved' => 'bg-emerald-50 text-emerald-800 border border-emerald-200 dark:bg-emerald-950/50 dark:text-emerald-300 dark:border-emerald-900',
                        'dismissed' => 'bg-slate-100 text-slate-600 border border-slate-200 dark:bg-slate-800 dark:text-slate-300 dark:border-slate-700',
                        'reviewed' => 'bg-blue-50 text-blue-800 border border-blue-200 dark:bg-blue-950/50 dark:text-blue-300 dark:border-blue-900',
                        default => 'bg-amber-50 text-amber-800 border border-amber-200 dark:bg-amber-950/50 dark:text-amber-300 dark:border-amber-900'
                    } }} text-[10px]">
                        {{ match($report->status) {
                            'action_taken' => 'Ditindaklanjuti',
                            'resolved' => 'Selesai',
                            'dismissed' => 'Ditolak',
                            'reviewed' => 'Ditinjau',
                            default => 'Menunggu Tinjauan'
                        } }}
                    </span>
                    <span class="text-[11px] text-slate-400">{{ $report->created_at->diffForHumans() }}</span>
                </div>

                <!-- Job Title & Meta -->
                <div class="mt-2.5">
                    <a href="{{ route('admin.jobs.show', ['job' => $report->job, 'return_to' => url()->full()]) }}" class="font-bold text-sm text-slate-900 hover:text-blue-600 dark:text-white dark:hover:text-blue-400 block transition">
                        {{ $report->job->title }}
                    </a>
                    <p class="text-xs text-slate-500 mt-0.5">
                        {{ $report->job->category->name }} · {{ $report->job->location }}
                    </p>
                    <div class="text-xs mt-1 text-slate-500 flex items-center gap-1">
                        <span class="text-slate-400">Mitra:</span>
                        <a href="{{ route('admin.users.show', ['user' => $report->job->employer, 'return_to' => url()->full()]) }}" class="font-semibold text-blue-600 hover:text-blue-800 dark:text-blue-400 hover:underline">
                            {{ $report->job->employer->business_name ?: $report->job->employer->name }}
                        </a>
                    </div>
                </div>

                <!-- Reason & Reporter -->
                <div class="mt-2.5 pt-2.5 border-t border-slate-100 dark:border-slate-800">
                    <span class="font-bold text-xs text-rose-700 dark:text-rose-400 block">
                        {{ $report->reason }}
                    </span>
                    @if($report->details)
                        <p class="text-xs text-slate-600 dark:text-slate-400 mt-0.5 line-clamp-2 italic">
                            "{{ $report->details }}"
                        </p>
                    @endif
                    <div class="text-xs mt-1 text-slate-500 flex items-center gap-1">
                        <span class="text-slate-400">Pelapor:</span>
                        <a href="{{ route('admin.users.show', ['user' => $report->reporter, 'return_to' => url()->full()]) }}" class="font-semibold text-blue-600 hover:text-blue-800 dark:text-blue-400 hover:underline">
                            {{ $report->reporter->name }}
                        </a>
                    </div>
                    @if($report->action_taken)
                        <div class="text-[11px] text-slate-400 mt-1">
                            Tindakan: {{ $report->action_taken }}
                        </div>
                    @endif
                </div>

                <!-- Action Toolbar (Right on the card, no scroll needed) -->
                <div class="mt-3.5 pt-2.5 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between gap-2">
                    <a href="{{ route('admin.reports.show', $report) }}" class="inline-flex items-center gap-1 text-xs font-bold text-blue-600 hover:text-blue-800 dark:text-blue-400 hover:underline py-1">
                        <span class="material-symbols-outlined text-[16px]">visibility</span>
                        <span>Tinjau Laporan</span>
                    </a>

                    <div class="flex items-center gap-1.5">
                        @if($report->status !== 'resolved' && $report->status !== 'dismissed')
                            <button type="button" 
                                    onclick="openResolveModal({{ $report->id }}, '{{ addslashes($report->job?->title ?? 'Lowongan') }}')" 
                                    class="inline-flex items-center gap-1 rounded-lg bg-emerald-50 px-2.5 py-1 text-xs font-bold text-emerald-700 hover:bg-emerald-100 border border-emerald-200/80 dark:bg-emerald-950/40 dark:border-emerald-900/60 dark:text-emerald-300 transition cursor-pointer">
                                <span class="material-symbols-outlined text-[14px]">task_alt</span>
                                <span>Selesaikan</span>
                            </button>
                        @endif

                        <button type="button" 
                                onclick="openDeleteModal({{ $report->id }}, '{{ addslashes($report->job?->title ?? 'Lowongan') }}', '{{ $report->status }}')" 
                                class="inline-flex items-center justify-center w-7 h-7 rounded-lg border border-slate-200 bg-white text-slate-400 hover:bg-rose-50 hover:border-rose-200 hover:text-rose-700 dark:bg-slate-900 dark:border-slate-800 dark:text-slate-400 dark:hover:text-rose-300 transition cursor-pointer" 
                                title="Hapus riwayat laporan">
                            <span class="material-symbols-outlined text-[15px]">delete_outline</span>
                        </button>
                    </div>
                </div>
            </div>
        @empty
            <div class="rounded-2xl border border-slate-200 bg-white p-8 text-center text-slate-500 dark:border-slate-800 dark:bg-slate-900">
                <span class="material-symbols-outlined text-4xl text-slate-300 block mb-2">verified</span>
                @if($currentStatus === 'active')
                    Semua laporan pengaduan telah selesai ditangani.
                @else
                    Tidak ada riwayat laporan pengaduan.
                @endif
            </div>
        @endforelse
    </div>

    <div class="mt-7">{{ $reports->links() }}</div>
@endsection

@push('modals')
    <!-- Modal: Selesaikan Laporan -->
    <div id="resolveModal" class="fixed inset-0 z-[100] hidden items-center justify-center p-4 bg-slate-950/60 backdrop-blur-xs overflow-y-auto" onclick="if(event.target === this) closeResolveModal()">
        <div class="relative w-full max-w-md rounded-2xl bg-white p-6 shadow-2xl dark:bg-slate-900 border border-slate-200 dark:border-slate-800 animate-in fade-in zoom-in duration-150">
            <div class="flex items-center gap-3 text-emerald-600 mb-3">
                <div class="w-10 h-10 rounded-xl bg-emerald-50 dark:bg-emerald-950/60 flex items-center justify-center border border-emerald-200 dark:border-emerald-800">
                    <span class="material-symbols-outlined text-2xl">check_circle</span>
                </div>
                <div>
                    <h3 class="font-bold text-base text-slate-950 dark:text-white">Selesaikan Laporan</h3>
                    <p class="text-xs text-slate-500">Tandai pengaduan ini telah tuntas ditangani</p>
                </div>
            </div>

            <p class="text-xs text-slate-600 dark:text-slate-300 leading-relaxed mb-4">
                Laporan untuk lowongan <strong id="resolveJobTitle" class="text-slate-900 dark:text-white"></strong> akan ditandai berstatus <strong>Selesai (Resolved)</strong> dan otomatis dihilangkan dari daftar laporan aktif.
            </p>

            <form id="resolveForm" method="POST" action="">
                @csrf
                @method('PATCH')
                
                <div class="mb-4">
                    <label for="resolve_admin_notes" class="portal-label">Catatan Penyelesaian (Opsional)</label>
                    <textarea id="resolve_admin_notes" name="admin_notes" rows="2" placeholder="Tuliskan keterangan penutupan untuk pelapor..." class="portal-input text-xs"></textarea>
                </div>

                <div class="flex items-center justify-end gap-2 pt-2 border-t border-slate-100 dark:border-slate-800">
                    <button type="button" onclick="closeResolveModal()" class="portal-button-secondary !py-2 !px-4 text-xs">Batal</button>
                    <button type="submit" class="portal-button-primary !py-2 !px-4 text-xs !bg-emerald-600 hover:!bg-emerald-700 !border-emerald-600">
                        <span class="material-symbols-outlined text-[16px]">task_alt</span>
                        <span>Ya, Selesaikan Laporan</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal: Hapus Riwayat Laporan -->
    <div id="deleteModal" class="fixed inset-0 z-[100] hidden items-center justify-center p-4 bg-slate-950/60 backdrop-blur-xs overflow-y-auto" onclick="if(event.target === this) closeDeleteModal()">
        <div class="relative w-full max-w-md rounded-2xl bg-white p-6 shadow-2xl dark:bg-slate-900 border border-slate-200 dark:border-slate-800 animate-in fade-in zoom-in duration-150">
            <div class="flex items-center gap-3 text-rose-600 mb-3">
                <div class="w-10 h-10 rounded-xl bg-rose-50 dark:bg-rose-950/60 flex items-center justify-center border border-rose-200 dark:border-rose-800">
                    <span class="material-symbols-outlined text-2xl">delete</span>
                </div>
                <div>
                    <h3 class="font-bold text-base text-slate-950 dark:text-white">Hapus Riwayat Laporan</h3>
                    <p class="text-xs text-slate-500">Hapus pengaduan dari tampilan panel admin</p>
                </div>
            </div>

            <p class="text-xs text-slate-600 dark:text-slate-300 leading-relaxed mb-4">
                Apakah Anda yakin ingin menghapus riwayat laporan untuk lowongan <strong id="deleteJobTitle" class="text-slate-900 dark:text-white"></strong> dari panel admin? Riwayat ini tidak akan ditampilkan lagi di daftar laporan admin.
            </p>

            <form id="deleteForm" method="POST" action="">
                @csrf
                @method('DELETE')

                <div class="flex items-center justify-end gap-2 pt-2 border-t border-slate-100 dark:border-slate-800">
                    <button type="button" onclick="closeDeleteModal()" class="portal-button-secondary !py-2 !px-4 text-xs">Batal</button>
                    <button type="submit" class="portal-button-primary !py-2 !px-4 text-xs !bg-rose-600 hover:!bg-rose-700 !border-rose-600">
                        <span class="material-symbols-outlined text-[16px]">delete</span>
                        <span>Ya, Hapus Riwayat</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal: Bersihkan Semua Riwayat Selesai -->
    <div id="clearCompletedModal" class="fixed inset-0 z-[100] hidden items-center justify-center p-4 bg-slate-950/60 backdrop-blur-xs overflow-y-auto" onclick="if(event.target === this) closeClearCompletedModal()">
        <div class="relative w-full max-w-md rounded-2xl bg-white p-6 shadow-2xl dark:bg-slate-900 border border-slate-200 dark:border-slate-800 animate-in fade-in zoom-in duration-150">
            <div class="flex items-center gap-3 text-amber-600 mb-3">
                <div class="w-10 h-10 rounded-xl bg-amber-50 dark:bg-amber-950/60 flex items-center justify-center border border-amber-200 dark:border-amber-800">
                    <span class="material-symbols-outlined text-2xl">cleaning_services</span>
                </div>
                <div>
                    <h3 class="font-bold text-base text-slate-950 dark:text-white">Bersihkan Riwayat Selesai</h3>
                    <p class="text-xs text-slate-500">Pembersihan massal riwayat laporan tuntas</p>
                </div>
            </div>

            <p class="text-xs text-slate-600 dark:text-slate-300 leading-relaxed mb-4">
                Tindakan ini akan menghapus seluruh riwayat pengaduan yang sudah berstatus <strong>Selesai</strong> atau <strong>Ditolak</strong> (sebanyak {{ $counts['resolved'] }} laporan) dari panel admin.
            </p>

            <form method="POST" action="{{ route('admin.reports.clearCompleted') }}">
                @csrf

                <div class="flex items-center justify-end gap-2 pt-2 border-t border-slate-100 dark:border-slate-800">
                    <button type="button" onclick="closeClearCompletedModal()" class="portal-button-secondary !py-2 !px-4 text-xs">Batal</button>
                    <button type="submit" class="portal-button-primary !py-2 !px-4 text-xs !bg-amber-600 hover:!bg-amber-700 !border-amber-600">
                        <span class="material-symbols-outlined text-[16px]">cleaning_services</span>
                        <span>Ya, Bersihkan Riwayat</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
@endpush

@push('scripts')
<script>
    function openResolveModal(reportId, jobTitle) {
        const modal = document.getElementById('resolveModal');
        const form = document.getElementById('resolveForm');
        const titleEl = document.getElementById('resolveJobTitle');

        if (titleEl) titleEl.textContent = `"${jobTitle}"`;
        if (form) form.action = `/admin/laporan/${reportId}/selesaikan`;
        if (modal) {
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            document.body.classList.add('overflow-hidden');
        }
    }

    function closeResolveModal() {
        const modal = document.getElementById('resolveModal');
        if (modal) {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            document.body.classList.remove('overflow-hidden');
        }
    }

    function openDeleteModal(reportId, jobTitle, status) {
        const modal = document.getElementById('deleteModal');
        const form = document.getElementById('deleteForm');
        const titleEl = document.getElementById('deleteJobTitle');

        if (titleEl) titleEl.textContent = `"${jobTitle}"`;
        if (form) form.action = `/admin/laporan/${reportId}`;
        if (modal) {
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            document.body.classList.add('overflow-hidden');
        }
    }

    function closeDeleteModal() {
        const modal = document.getElementById('deleteModal');
        if (modal) {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            document.body.classList.remove('overflow-hidden');
        }
    }

    function openClearCompletedModal() {
        const modal = document.getElementById('clearCompletedModal');
        if (modal) {
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            document.body.classList.add('overflow-hidden');
        }
    }

    function closeClearCompletedModal() {
        const modal = document.getElementById('clearCompletedModal');
        if (modal) {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            document.body.classList.remove('overflow-hidden');
        }
    }

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            closeResolveModal();
            closeDeleteModal();
            closeClearCompletedModal();
        }
    });
</script>
@endpush
