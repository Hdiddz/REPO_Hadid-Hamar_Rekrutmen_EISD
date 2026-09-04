@extends('layouts.admin')

@section('title', 'Laporan Pelanggaran Lowongan | Admin KerjaLokal')
@section('portal_title', 'Laporan & Pengaduan Pelamar')
@section('portal_description', 'Tinjau laporan masuk dari pencari kerja terkait indikasi percaloan, penipuan, upah tidak transparan, atau pelanggaran etis.')

@section('content')
    <!-- Stat Counter Cards -->
    <div class="grid gap-4 sm:grid-cols-3 mb-6">
        <div class="portal-stat-card">
            <span class="material-symbols-outlined text-amber-600">pending_actions</span>
            <strong class="mt-4 block text-2xl font-bold">{{ $counts['pending'] }}</strong>
            <span class="text-xs text-slate-500">Laporan Menunggu Tinjauan</span>
        </div>
        <div class="portal-stat-card">
            <span class="material-symbols-outlined text-rose-600">gavel</span>
            <strong class="mt-4 block text-2xl font-bold">{{ $counts['action_taken'] }}</strong>
            <span class="text-xs text-slate-500">Telah Diberikan Tindakan</span>
        </div>
        <div class="portal-stat-card">
            <span class="material-symbols-outlined text-slate-600">check_circle</span>
            <strong class="mt-4 block text-2xl font-bold">{{ $counts['dismissed'] }}</strong>
            <span class="text-xs text-slate-500">Laporan Selesai / Ditolak</span>
        </div>
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
                    <option value="">Semua status laporan</option>
                    <option value="pending" @selected(request('status') === 'pending')>Menunggu Tinjauan (Pending)</option>
                    <option value="reviewed" @selected(request('status') === 'reviewed')>Sedang Ditinjau (Reviewed)</option>
                    <option value="action_taken" @selected(request('status') === 'action_taken')>Telah Diberikan Sanksi</option>
                    <option value="dismissed" @selected(request('status') === 'dismissed')>Ditolak / Tidak Terbukti</option>
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

    <!-- Reports Table -->
    <div class="portal-table-shell overflow-x-auto">
        <table class="portal-table">
            <thead>
                <tr>
                    <th>Lowongan Terlapor</th>
                    <th>Mitra UMKM</th>
                    <th>Pelapor</th>
                    <th>Kategori Alasan</th>
                    <th>Status Laporan</th>
                    <th>Tanggal Masuk</th>
                    <th class="text-right">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($reports as $report)
                    <tr>
                        <td>
                            <div class="min-w-0 max-w-xs">
                                <a href="{{ route('admin.jobs.show', ['job' => $report->job, 'return_to' => url()->full()]) }}" class="font-bold text-slate-900 hover:text-brand-700 dark:text-white block truncate">
                                    {{ $report->job->title }}
                                </a>
                                <span class="text-xs text-slate-500 block truncate">
                                    {{ $report->job->category->name }} · {{ $report->job->location }}
                                </span>
                            </div>
                        </td>
                        <td>
                            <div class="min-w-0">
                                <span class="font-bold text-xs text-slate-900 dark:text-white block truncate">
                                    {{ $report->job->employer->business_name ?: $report->job->employer->name }}
                                </span>
                                <span class="text-xs text-slate-500 block truncate">
                                    {{ $report->job->employer->email }}
                                </span>
                            </div>
                        </td>
                        <td>
                            <div>
                                <span class="font-bold text-xs text-slate-900 dark:text-white block">
                                    {{ $report->reporter->name }}
                                </span>
                                <span class="text-xs text-slate-500 block">
                                    {{ $report->reporter->email }}
                                </span>
                            </div>
                        </td>
                        <td>
                            <span class="font-bold text-xs text-rose-700 dark:text-rose-400 block">
                                {{ $report->reason }}
                            </span>
                            @if($report->details)
                                <span class="text-[11px] text-slate-500 block max-w-[180px] truncate" title="{{ $report->details }}">
                                    "{{ $report->details }}"
                                </span>
                            @endif
                        </td>
                        <td>
                            <span class="portal-badge {{ match($report->status) {'action_taken' => 'bg-rose-50 text-rose-800 border border-rose-200', 'dismissed' => 'bg-slate-100 text-slate-600', 'reviewed' => 'bg-blue-50 text-blue-800 border border-blue-200', default => 'bg-amber-50 text-amber-800 border border-amber-200'} }} text-[10px]">
                                {{ match($report->status) {'action_taken' => 'Ditindaklanjuti', 'dismissed' => 'Ditolak', 'reviewed' => 'Ditinjau', default => 'Menunggu Tinjauan'} }}
                            </span>
                            @if($report->action_taken)
                                <span class="block text-[10px] text-slate-400 mt-0.5">
                                    {{ $report->action_taken }}
                                </span>
                            @endif
                        </td>
                        <td>
                            <span class="text-xs text-slate-500">
                                {{ $report->created_at->diffForHumans() }}
                            </span>
                        </td>
                        <td>
                            <div class="flex items-center justify-end">
                                <a href="{{ route('admin.reports.show', $report) }}" class="portal-button-secondary !py-1 !px-3 text-xs" title="Tinjau detail laporan">
                                    <span class="material-symbols-outlined text-[16px]">policy</span>
                                    Tinjau
                                </a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="py-12 text-center text-slate-500">
                            <span class="material-symbols-outlined text-4xl text-slate-300 block mb-2">verified</span>
                            Tidak ada laporan pelanggaran yang sesuai kriteria.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-7">{{ $reports->links() }}</div>
@endsection
