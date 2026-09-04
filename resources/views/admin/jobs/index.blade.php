@extends('layouts.admin')

@section('title', 'Semua Lowongan | Admin KerjaLokal')
@section('portal_title', 'Kelola seluruh lowongan')
@section('portal_description', 'Tinjau, edit, tutup, atau buka kembali lowongan kerja dari seluruh mitra UMKM untuk menjamin standar kerja layak.')

@section('content')
    <!-- Search & Filter Bar -->
    <form method="GET" class="mb-6 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm dark:border-slate-800 dark:bg-slate-900">
        <div class="grid gap-3 sm:grid-cols-12 sm:items-end">
            <div class="sm:col-span-4">
                <label for="q" class="portal-label">Pencarian lowongan / mitra</label>
                <div class="relative">
                    <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-[18px]">search</span>
                    <input id="q" name="q" value="{{ request('q') }}" class="portal-input pl-9" placeholder="Cari judul, lokasi, atau UMKM...">
                </div>
            </div>
            <div class="sm:col-span-3">
                <label for="category_id" class="portal-label">Kategori</label>
                <select id="category_id" name="category_id" class="portal-input">
                    <option value="">Semua kategori</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" @selected(request('category_id') == $category->id)>{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="sm:col-span-3">
                <label for="status" class="portal-label">Status lowongan</label>
                <select id="status" name="status" class="portal-input">
                    <option value="">Semua status</option>
                    <option value="open" @selected(request('status') === 'open')>Dibuka (Aktif)</option>
                    <option value="closed" @selected(request('status') === 'closed')>Ditutup</option>
                    <option value="closed_by_admin" @selected(request('status') === 'closed_by_admin')>Ditutup oleh Admin</option>
                    <option value="reported" @selected(request('status') === 'reported')>Memiliki Laporan Pelamar</option>
                </select>
            </div>
            <div class="sm:col-span-2 flex items-center gap-2">
                <button type="submit" class="portal-button-primary w-full justify-center">Filter</button>
                <a href="{{ route('admin.jobs.index') }}" class="portal-button-secondary" title="Reset filter">
                    <span class="material-symbols-outlined text-[18px]">refresh</span>
                </a>
            </div>
        </div>
    </form>

    <!-- Jobs Table -->
    <div class="portal-table-shell overflow-x-auto">
        <table class="portal-table">
            <thead>
                <tr>
                    <th>Lowongan</th>
                    <th>Akun Mitra UMKM</th>
                    <th>Upah &amp; Jam Kerja</th>
                    <th>Status</th>
                    <th>Pelamar</th>
                    <th class="text-right">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($jobs as $job)
                    <tr>
                        <td>
                            <div class="min-w-0 max-w-xs sm:max-w-sm">
                                <a href="{{ route('admin.jobs.show', ['job' => $job, 'return_to' => url()->full()]) }}" class="font-bold text-slate-950 hover:text-brand-700 dark:text-white dark:hover:text-brand-400 block truncate" title="{{ $job->title }}">
                                    {{ $job->title }}
                                </a>
                                <div class="mt-0.5 flex flex-wrap items-center gap-x-2 gap-y-0.5 text-xs text-slate-500">
                                    <span class="truncate">{{ $job->category->name }} · {{ $job->location }}</span>
                                    @if($job->reports_count > 0)
                                        <span class="text-slate-300 dark:text-slate-700 hidden sm:inline">·</span>
                                        <a href="{{ route('admin.reports.index', ['q' => $job->title, 'status' => 'all']) }}" class="inline-flex items-center gap-1 text-[11px] font-medium text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 hover:underline transition shrink-0" title="Lihat {{ $job->reports_count }} laporan masuk">
                                            <span class="material-symbols-outlined text-[13px]">flag</span>
                                            <span>{{ $job->reports_count }} laporan masuk</span>
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="min-w-0">
                                <span class="font-bold text-xs text-slate-900 dark:text-white block truncate">
                                    {{ $job->employer->business_name ?: $job->employer->name }}
                                </span>
                                <span class="text-xs text-slate-500 block truncate">
                                    {{ $job->employer->name }} · {{ $job->employer->email }}
                                </span>
                                @if($job->employer->isBanned())
                                    <span class="mt-1 inline-block rounded bg-rose-50 px-1.5 py-0.5 text-[10px] font-bold text-rose-700 border border-rose-200">
                                        Mitra Dibekukan
                                    </span>
                                @endif
                            </div>
                        </td>
                        <td class="whitespace-nowrap">
                            <span class="font-bold text-xs text-slate-900 dark:text-white block">
                                Rp {{ number_format($job->salary_amount, 0, ',', '.') }}
                                <span class="text-[10px] text-slate-500 font-normal">/ {{ $job->salary_type === 'monthly' ? 'bln' : 'hari' }}</span>
                            </span>
                            <span class="text-xs text-slate-500 block">
                                {{ $job->work_hours_per_day }} jam / hari
                            </span>
                        </td>
                        <td class="whitespace-nowrap">
                            @if($job->isClosedByAdmin())
                                <div>
                                    <span class="portal-badge bg-rose-50 text-rose-700 border border-rose-200 dark:bg-rose-950/40 dark:text-rose-300 dark:border-rose-900/50">
                                        Ditutup Admin
                                    </span>
                                    @if($job->closed_until)
                                        <span class="block text-[10px] text-rose-600 mt-0.5 font-semibold">
                                            s.d {{ $job->closed_until->translatedFormat('d M Y') }}
                                        </span>
                                    @endif
                                    @if($job->closed_reason)
                                        <p class="text-[10px] text-slate-400 italic truncate max-w-[150px]" title="{{ $job->closed_reason }}">
                                            "{{ $job->closed_reason }}"
                                        </p>
                                    @endif
                                </div>
                            @elseif($job->status === 'open')
                                <span class="portal-badge bg-emerald-50 text-emerald-700 border border-emerald-200 dark:bg-emerald-950/40 dark:text-emerald-300 dark:border-emerald-900/50">
                                    Dibuka
                                </span>
                            @else
                                <span class="portal-badge bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-300">
                                    Ditutup Mitra
                                </span>
                            @endif
                        </td>
                        <td class="whitespace-nowrap">
                            <a href="{{ route('admin.jobs.show', ['job' => $job, 'return_to' => url()->full()]) }}#pelamar" class="inline-flex items-center gap-1 text-xs font-bold text-brand-700 dark:text-brand-300 hover:underline">
                                <span class="material-symbols-outlined text-[16px]">groups</span>
                                {{ $job->applications_count }}
                            </a>
                        </td>
                        <td class="whitespace-nowrap text-right">
                            <div class="flex items-center justify-end gap-1.5 whitespace-nowrap">
                                <!-- Detail Button -->
                                <a href="{{ route('admin.jobs.show', ['job' => $job, 'return_to' => url()->full()]) }}" class="portal-button-secondary !py-1 !px-2 text-xs" title="Lihat detail lowongan dan pelamar">
                                    Detail
                                </a>

                                <!-- Edit Button -->
                                <a href="{{ route('admin.jobs.edit', ['job' => $job, 'return_to' => url()->full()]) }}" class="portal-button-secondary !py-1 !px-2 text-xs" title="Edit informasi lowongan">
                                    <span class="material-symbols-outlined text-[16px]">edit</span>
                                </a>

                                <!-- Close / Reopen Button -->
                                @if($job->status === 'open')
                                    <button type="button" data-job-id="{{ $job->id }}" data-job-title="{{ $job->title }}" onclick="openCloseJobModal(this.dataset.jobId, this.dataset.jobTitle)" class="portal-button-secondary !py-1 !px-2 text-xs text-amber-700 hover:!bg-amber-50" title="Tutup lowongan ini">
                                        <span class="material-symbols-outlined text-[16px]">lock</span>
                                        Tutup
                                    </button>
                                @else
                                    <form action="{{ route('admin.jobs.reopen', $job) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="portal-button-secondary !py-1 !px-2 text-xs text-emerald-700 hover:!bg-emerald-50 cursor-pointer" title="Buka kembali lowongan">
                                            <span class="material-symbols-outlined text-[16px]">lock_open</span>
                                            Buka
                                        </button>
                                    </form>
                                @endif

                                <!-- Delete Button (Triggers Pop-up Modal) -->
                                <button type="button" data-job-title="{{ $job->title }}" data-delete-url="{{ route('admin.jobs.destroy', $job) }}" onclick="openDeleteJobModal(this.dataset.jobTitle, this.dataset.deleteUrl)" class="portal-button-secondary !py-1 !px-2 text-xs !text-rose-600 hover:!bg-rose-50 cursor-pointer" title="Hapus lowongan">
                                    <span class="material-symbols-outlined text-[16px]">delete</span>
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="py-12 text-center text-slate-500">
                            <span class="material-symbols-outlined text-4xl text-slate-300 block mb-2">work_off</span>
                            Tidak ada lowongan yang sesuai dengan filter pencarian.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-7">{{ $jobs->links() }}</div>
@endsection

@push('modals')
    <!-- Modal Tutup Lowongan Oleh Admin -->
    <div id="closeJobModal" class="fixed inset-0 z-[100] hidden bg-slate-950/60 backdrop-blur-xs p-4 overflow-y-auto flex items-center justify-center" onclick="if(event.target === this) closeCloseJobModal()">
        <div class="relative w-full max-w-lg rounded-3xl bg-white p-6 sm:p-7 shadow-2xl dark:bg-slate-900 border border-slate-200 dark:border-slate-800 animate-modal-pop">
            <div class="flex items-start justify-between gap-3 pb-4 border-b border-slate-100 dark:border-slate-800">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-2xl bg-amber-50 text-amber-600 dark:bg-amber-950/50 dark:text-amber-400 border border-amber-200 dark:border-amber-900/50 flex items-center justify-center shrink-0">
                        <span class="material-symbols-outlined text-[22px]">lock</span>
                    </div>
                    <div>
                        <h3 class="text-base sm:text-lg font-bold text-slate-900 dark:text-white" id="closeJobModalTitle">Tutup Lowongan</h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400">Pengawasan standar etika &amp; kepatuhan ketenagakerjaan</p>
                    </div>
                </div>
                <button type="button" onclick="closeCloseJobModal()" class="w-8 h-8 rounded-xl text-slate-400 hover:text-slate-700 hover:bg-slate-100 dark:hover:text-white dark:hover:bg-slate-800 flex items-center justify-center transition" aria-label="Tutup modal">
                    <span class="material-symbols-outlined text-[20px]">close</span>
                </button>
            </div>

            <form id="closeJobForm" method="POST" action="" class="mt-5 space-y-4">
                @csrf
                <div>
                    <label for="close_reason" class="portal-label text-xs uppercase tracking-wider text-slate-500 dark:text-slate-400">
                        Alasan Penutupan <span class="text-rose-500">*</span>
                    </label>
                    <textarea id="close_reason" name="close_reason" rows="3" required placeholder="Contoh: Terindikasi pelanggaran batas jam kerja harian atau standar upah tidak transparan..." class="w-full rounded-2xl border border-slate-200 bg-slate-50/70 p-3.5 text-sm text-slate-900 placeholder:text-slate-400 focus:bg-white focus:border-amber-500 focus:ring-4 focus:ring-amber-500/10 focus:outline-none transition dark:border-slate-700 dark:bg-slate-950/70 dark:text-slate-100 dark:focus:bg-slate-950"></textarea>
                </div>

                <div>
                    <label for="job_duration_type" class="portal-label text-xs uppercase tracking-wider text-slate-500 dark:text-slate-400">
                        Durasi Penutupan Lowongan <span class="text-rose-500">*</span>
                    </label>
                    <select id="job_duration_type" name="duration_type" required onchange="handleJobDurationChange(this.value)" class="w-full rounded-2xl border border-slate-200 bg-slate-50/70 px-3.5 py-2.5 text-sm font-medium text-slate-900 focus:bg-white focus:border-amber-500 focus:ring-4 focus:ring-amber-500/10 focus:outline-none transition dark:border-slate-700 dark:bg-slate-950/70 dark:text-slate-100 dark:focus:bg-slate-950">
                        <option value="7_days" selected>7 Hari (1 Minggu)</option>
                        <option value="14_days">14 Hari (2 Minggu)</option>
                        <option value="30_days">30 Hari (1 Bulan)</option>
                        <option value="custom">Kustom Jumlah Hari...</option>
                        <option value="permanent">Permanen (Sampai Diberikan Izin Kembali)</option>
                    </select>
                </div>

                <div id="jobCustomDaysContainer" class="hidden">
                    <label for="job_custom_days" class="portal-label text-xs uppercase tracking-wider text-slate-500 dark:text-slate-400">
                        Jumlah Hari Kustom <span class="text-rose-500">*</span>
                    </label>
                    <input type="number" id="job_custom_days" name="custom_days" min="1" max="365" placeholder="Contoh: 10" class="w-full rounded-2xl border border-slate-200 bg-slate-50/70 p-3 text-sm text-slate-900 focus:bg-white focus:border-amber-500 focus:ring-4 focus:ring-amber-500/10 focus:outline-none transition dark:border-slate-700 dark:bg-slate-950/70 dark:text-slate-100">
                </div>

                <div class="pt-4 flex items-center justify-end gap-2.5 border-t border-slate-100 dark:border-slate-800">
                    <button type="button" onclick="closeCloseJobModal()" class="inline-flex h-10 items-center justify-center rounded-xl border border-slate-200 bg-white px-4 text-xs sm:text-sm font-semibold text-slate-700 shadow-xs hover:bg-slate-50 hover:border-slate-300 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 transition cursor-pointer">
                        Batal
                    </button>
                    <button type="submit" class="inline-flex h-10 items-center justify-center gap-1.5 rounded-xl bg-amber-600 px-5 text-xs sm:text-sm font-semibold text-white shadow-sm shadow-amber-600/25 hover:bg-amber-700 active:translate-y-px transition cursor-pointer">
                        <span class="material-symbols-outlined text-[18px]">lock</span>
                        Tutup Lowongan Ini
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Konfirmasi Hapus Lowongan -->
    <div id="deleteJobModal" class="fixed inset-0 z-[100] hidden bg-slate-950/60 backdrop-blur-sm p-4 overflow-y-auto flex items-center justify-center" onclick="if(event.target === this) closeDeleteJobModal()">
        <div class="relative w-full max-w-md rounded-3xl bg-white p-6 sm:p-7 shadow-2xl shadow-slate-950/20 dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 animate-modal-pop">
            
            <div class="flex items-start gap-3.5">
                <div class="w-11 h-11 rounded-2xl bg-rose-50 text-rose-600 dark:bg-rose-950/50 dark:text-rose-400 border border-rose-200 dark:border-rose-900/50 flex items-center justify-center shrink-0">
                    <span class="material-symbols-outlined text-[24px]">delete_forever</span>
                </div>
                <div class="flex-1 min-w-0">
                    <h3 class="text-base sm:text-lg font-bold text-slate-900 dark:text-white leading-snug">
                        Hapus Lowongan Kerja?
                    </h3>
                    <p class="mt-1 text-xs text-slate-500 dark:text-slate-400 leading-relaxed">
                        Anda akan menghapus permanen lowongan <strong id="deleteJobTargetTitle" class="text-slate-900 dark:text-white"></strong>.
                    </p>
                </div>
                <button type="button" onclick="closeDeleteJobModal()" class="w-8 h-8 rounded-xl text-slate-400 hover:text-slate-700 hover:bg-slate-100 dark:hover:text-white dark:hover:bg-slate-800 flex items-center justify-center transition shrink-0 cursor-pointer" aria-label="Tutup">
                    <span class="material-symbols-outlined text-[20px]">close</span>
                </button>
            </div>

            <div class="mt-4 p-3.5 rounded-2xl bg-rose-50/70 border border-rose-200/70 text-rose-800 dark:bg-rose-950/30 dark:border-rose-900/40 dark:text-rose-300 text-xs flex items-start gap-2.5">
                <span class="material-symbols-outlined text-rose-600 dark:text-rose-400 text-base shrink-0 mt-0.5">warning</span>
                <span class="leading-relaxed">
                    Seluruh berkas pelamar dan riwayat lamaran yang masuk pada lowongan ini akan ikut terhapus secara permanen.
                </span>
            </div>

            <form id="deleteJobForm" method="POST" action="" class="mt-6 flex items-center justify-end gap-2.5">
                @csrf
                @method('DELETE')
                <button type="button" onclick="closeDeleteJobModal()" class="inline-flex h-10 items-center justify-center rounded-xl border border-slate-200 bg-white px-4 text-xs sm:text-sm font-semibold text-slate-700 shadow-xs hover:bg-slate-50 hover:border-slate-300 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 transition cursor-pointer">
                    Batal
                </button>
                <button type="submit" class="inline-flex h-10 items-center justify-center gap-1.5 rounded-xl bg-rose-600 px-5 text-xs sm:text-sm font-semibold text-white shadow-sm shadow-rose-600/25 hover:bg-rose-700 active:translate-y-px transition cursor-pointer">
                    <span class="material-symbols-outlined text-[18px]">delete_forever</span>
                    Ya, Hapus Lowongan
                </button>
            </form>

        </div>
    </div>
@endpush

@push('scripts')
<script>
    function openCloseJobModal(jobId, jobTitle) {
        const modal = document.getElementById('closeJobModal');
        const form = document.getElementById('closeJobForm');
        const title = document.getElementById('closeJobModalTitle');

        if (title) title.textContent = `Tutup Lowongan: ${jobTitle}`;
        if (form) form.action = `/admin/lowongan/${jobId}/tutup`;

        if (modal) {
            modal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }
    }

    function closeCloseJobModal() {
        const modal = document.getElementById('closeJobModal');
        if (modal) {
            modal.classList.add('hidden');
            document.body.style.overflow = '';
        }
    }

    function openDeleteJobModal(title, actionUrl) {
        document.getElementById('deleteJobTargetTitle').textContent = title;
        document.getElementById('deleteJobForm').action = actionUrl;
        const modal = document.getElementById('deleteJobModal');
        if (modal) {
            modal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }
    }

    function closeDeleteJobModal() {
        const modal = document.getElementById('deleteJobModal');
        if (modal) {
            modal.classList.add('hidden');
            document.body.style.overflow = '';
        }
    }

    function handleJobDurationChange(val) {
        const container = document.getElementById('jobCustomDaysContainer');
        const input = document.getElementById('job_custom_days');
        if (container) {
            if (val === 'custom') {
                container.classList.remove('hidden');
                if (input) input.required = true;
            } else {
                container.classList.add('hidden');
                if (input) input.required = false;
            }
        }
    }

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            closeCloseJobModal();
            closeDeleteJobModal();
        }
    });
</script>
@endpush
