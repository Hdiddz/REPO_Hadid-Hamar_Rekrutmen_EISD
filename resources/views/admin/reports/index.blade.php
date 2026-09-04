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
        <div class="flex items-center gap-2 overflow-x-auto no-scrollbar pb-1">
            <a href="{{ route('admin.reports.index', array_merge(request()->except('status', 'page'), ['status' => 'active'])) }}" 
               class="inline-flex items-center gap-1.5 rounded-xl px-3 py-1.5 text-xs font-bold whitespace-nowrap shrink-0 transition {{ $currentStatus === 'active' ? 'bg-brand-700 text-white shadow-xs dark:bg-brand-500 dark:text-brand-950' : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-50 dark:bg-slate-900 dark:text-slate-300 dark:border-slate-800' }}">
                <span class="material-symbols-outlined text-[16px]">inbox</span>
                <span>Laporan Aktif</span>
                @if($counts['active'] > 0)
                    <span class="rounded-full bg-black/10 dark:bg-white/10 px-1.5 py-0.2 text-[10px]">{{ $counts['active'] }}</span>
                @endif
            </a>

            <a href="{{ route('admin.reports.index', array_merge(request()->except('status', 'page'), ['status' => 'pending'])) }}" 
               class="inline-flex items-center gap-1.5 rounded-xl px-3 py-1.5 text-xs font-bold whitespace-nowrap shrink-0 transition {{ $currentStatus === 'pending' ? 'bg-amber-600 text-white shadow-xs' : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-50 dark:bg-slate-900 dark:text-slate-300 dark:border-slate-800' }}">
                <span class="material-symbols-outlined text-[16px]">pending_actions</span>
                <span>Menunggu Tinjauan</span>
                @if($counts['pending'] > 0)
                    <span class="rounded-full bg-amber-100 text-amber-800 dark:bg-amber-950 dark:text-amber-300 px-1.5 py-0.2 text-[10px]">{{ $counts['pending'] }}</span>
                @endif
            </a>

            <a href="{{ route('admin.reports.index', array_merge(request()->except('status', 'page'), ['status' => 'reviewed'])) }}" 
               class="inline-flex items-center gap-1.5 rounded-xl px-3 py-1.5 text-xs font-bold whitespace-nowrap shrink-0 transition {{ $currentStatus === 'reviewed' ? 'bg-blue-600 text-white shadow-xs' : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-50 dark:bg-slate-900 dark:text-slate-300 dark:border-slate-800' }}">
                <span class="material-symbols-outlined text-[16px]">visibility</span>
                <span>Sedang Ditinjau</span>
                @if($counts['reviewed'] > 0)
                    <span class="rounded-full bg-blue-100 text-blue-800 dark:bg-blue-950 dark:text-blue-300 px-1.5 py-0.2 text-[10px]">{{ $counts['reviewed'] }}</span>
                @endif
            </a>

            <a href="{{ route('admin.reports.index', array_merge(request()->except('status', 'page'), ['status' => 'action_taken'])) }}" 
               class="inline-flex items-center gap-1.5 rounded-xl px-3 py-1.5 text-xs font-bold whitespace-nowrap shrink-0 transition {{ $currentStatus === 'action_taken' ? 'bg-rose-600 text-white shadow-xs' : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-50 dark:bg-slate-900 dark:text-slate-300 dark:border-slate-800' }}">
                <span class="material-symbols-outlined text-[16px]">gavel</span>
                <span>Telah Ditindak</span>
                @if($counts['action_taken'] > 0)
                    <span class="rounded-full bg-rose-100 text-rose-800 dark:bg-rose-950 dark:text-rose-300 px-1.5 py-0.2 text-[10px]">{{ $counts['action_taken'] }}</span>
                @endif
            </a>

            <a href="{{ route('admin.reports.index', array_merge(request()->except('status', 'page'), ['status' => 'resolved'])) }}" 
               class="inline-flex items-center gap-1.5 rounded-xl px-3 py-1.5 text-xs font-bold whitespace-nowrap shrink-0 transition {{ $currentStatus === 'resolved' ? 'bg-emerald-600 text-white shadow-xs' : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-50 dark:bg-slate-900 dark:text-slate-300 dark:border-slate-800' }}">
                <span class="material-symbols-outlined text-[16px]">task_alt</span>
                <span>Selesai / Ditolak</span>
                @if($counts['resolved'] > 0)
                    <span class="rounded-full bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300 px-1.5 py-0.2 text-[10px]">{{ $counts['resolved'] }}</span>
                @endif
            </a>

            <a href="{{ route('admin.reports.index', array_merge(request()->except('status', 'page'), ['status' => 'all'])) }}" 
               class="inline-flex items-center gap-1.5 rounded-xl px-3 py-1.5 text-xs font-bold whitespace-nowrap shrink-0 transition {{ $currentStatus === 'all' ? 'bg-slate-800 text-white shadow-xs dark:bg-slate-200 dark:text-slate-900' : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-50 dark:bg-slate-900 dark:text-slate-300 dark:border-slate-800' }}">
                <span>Semua Status</span>
                <span class="rounded-full bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300 px-1.5 py-0.2 text-[10px]">{{ $counts['all'] }}</span>
            </a>
        </div>

        @if(($currentStatus === 'resolved' || $currentStatus === 'all') && $counts['resolved'] > 0)
            <button type="button" onclick="openClearCompletedModal()" class="portal-button-secondary !py-1.5 !px-3 text-xs text-rose-700 hover:bg-rose-50 hover:border-rose-300 dark:text-rose-400 dark:hover:bg-rose-950/40 shrink-0 self-end sm:self-auto" title="Bersihkan semua laporan yang sudah selesai dari panel admin">
                <span class="material-symbols-outlined text-[16px]">cleaning_services</span>
                <span>Bersihkan Riwayat Selesai</span>
            </button>
        @endif
    </div>

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
                        <td class="whitespace-nowrap">
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
                                <span class="block text-[10px] text-slate-400 mt-0.5">
                                    {{ $report->action_taken }}
                                </span>
                            @endif
                        </td>
                        <td class="whitespace-nowrap">
                            <span class="text-xs text-slate-500">
                                {{ $report->created_at->diffForHumans() }}
                            </span>
                        </td>
                        <td class="whitespace-nowrap text-right">
                            <div class="flex items-center justify-end gap-1.5 whitespace-nowrap">
                                <a href="{{ route('admin.reports.show', $report) }}" class="portal-button-secondary !py-1 !px-2.5 text-xs" title="Tinjau detail laporan">
                                    <span class="material-symbols-outlined text-[15px]">policy</span>
                                    <span>Tinjau</span>
                                </a>

                                @if($report->status !== 'resolved' && $report->status !== 'dismissed')
                                    <button type="button" 
                                            onclick="openResolveModal({{ $report->id }}, '{{ addslashes($report->job?->title ?? 'Lowongan') }}')" 
                                            class="inline-flex items-center gap-1 rounded-xl border border-emerald-200 bg-emerald-50 px-2.5 py-1 text-xs font-bold text-emerald-800 shadow-2xs hover:bg-emerald-100 hover:border-emerald-300 dark:bg-emerald-950/40 dark:border-emerald-900/60 dark:text-emerald-300 dark:hover:bg-emerald-900/60 transition cursor-pointer" 
                                            title="Selesaikan laporan ini dan hilangkan dari daftar laporan aktif">
                                        <span class="material-symbols-outlined text-[15px]">check_circle</span>
                                        <span class="hidden sm:inline">Selesaikan</span>
                                    </button>
                                @endif

                                <button type="button" 
                                        onclick="openDeleteModal({{ $report->id }}, '{{ addslashes($report->job?->title ?? 'Lowongan') }}', '{{ $report->status }}')" 
                                        class="inline-flex items-center justify-center w-7 h-7 rounded-xl border border-slate-200 bg-white text-slate-500 shadow-2xs hover:bg-rose-50 hover:border-rose-200 hover:text-rose-700 dark:bg-slate-900 dark:border-slate-800 dark:text-slate-400 dark:hover:bg-rose-950/40 dark:hover:text-rose-300 transition cursor-pointer" 
                                        title="Hapus riwayat laporan ini dari panel admin">
                                    <span class="material-symbols-outlined text-[16px]">delete_outline</span>
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="py-12 text-center text-slate-500">
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

    <div class="mt-7">{{ $reports->links() }}</div>

    <!-- Modal: Selesaikan Laporan -->
    <div id="resolveModal" class="fixed inset-0 z-50 hidden items-center justify-center p-4 bg-slate-950/60 backdrop-blur-xs">
        <div class="w-full max-w-md rounded-2xl bg-white p-6 shadow-2xl dark:bg-slate-900 border border-slate-200 dark:border-slate-800 animate-in fade-in zoom-in duration-150">
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
    <div id="deleteModal" class="fixed inset-0 z-50 hidden items-center justify-center p-4 bg-slate-950/60 backdrop-blur-xs">
        <div class="w-full max-w-md rounded-2xl bg-white p-6 shadow-2xl dark:bg-slate-900 border border-slate-200 dark:border-slate-800 animate-in fade-in zoom-in duration-150">
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
    <div id="clearCompletedModal" class="fixed inset-0 z-50 hidden items-center justify-center p-4 bg-slate-950/60 backdrop-blur-xs">
        <div class="w-full max-w-md rounded-2xl bg-white p-6 shadow-2xl dark:bg-slate-900 border border-slate-200 dark:border-slate-800 animate-in fade-in zoom-in duration-150">
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
@endsection

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
        }
    }

    function closeResolveModal() {
        const modal = document.getElementById('resolveModal');
        if (modal) {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
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
        }
    }

    function closeDeleteModal() {
        const modal = document.getElementById('deleteModal');
        if (modal) {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }
    }

    function openClearCompletedModal() {
        const modal = document.getElementById('clearCompletedModal');
        if (modal) {
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }
    }

    function closeClearCompletedModal() {
        const modal = document.getElementById('clearCompletedModal');
        if (modal) {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }
    }
</script>
@endpush
