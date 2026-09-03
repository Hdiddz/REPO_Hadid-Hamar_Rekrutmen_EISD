@extends('layouts.admin')

@section('title', 'Tinjau Laporan Pelanggaran | Admin KerjaLokal')
@section('portal_title', 'Tinjauan Laporan Pelanggaran')
@section('portal_description', 'Analisis aduan pelamar, periksa integritas lowongan, dan ambil tindakan sanksi langsung demi standar kerja layak.')

@section('portal_actions')
    <a href="{{ route('admin.reports.index') }}" class="portal-action-btn">
        <span class="material-symbols-outlined text-[17px]">arrow_back</span>
        Kembali ke Daftar Laporan
    </a>
@endsection

@section('content')
    <!-- Status Header Banner -->
    <div class="mb-6 rounded-2xl p-4 border flex items-center justify-between gap-4 {{ match($report->status) {'action_taken' => 'bg-rose-50 border-rose-200 text-rose-900 dark:bg-rose-950/40 dark:border-rose-900/50 dark:text-rose-200', 'dismissed' => 'bg-slate-100 border-slate-200 text-slate-800 dark:bg-slate-800 dark:border-slate-700 dark:text-slate-200', default => 'bg-amber-50 border-amber-200 text-amber-900 dark:bg-amber-950/40 dark:border-amber-900/50 dark:text-amber-200'} }}">
        <div class="flex items-center gap-3">
            <span class="material-symbols-outlined text-2xl">
                {{ match($report->status) {'action_taken' => 'gavel', 'dismissed' => 'check_circle', default => 'policy'} }}
            </span>
            <div>
                <strong class="text-sm block">Status Laporan: {{ match($report->status) {'action_taken' => 'Sanksi Telah Diberikan', 'dismissed' => 'Laporan Ditolak / Tidak Ditemukan Pelanggaran', 'reviewed' => 'Sedang Ditinjau Administrator', default => 'Menunggu Tindakan'} }}</strong>
                @if($report->action_taken)
                    <span class="text-xs block opacity-90">Tindakan: {{ $report->action_taken }}</span>
                @endif
                @if($report->admin_notes)
                    <span class="text-xs block opacity-80 italic">Catatan: "{{ $report->admin_notes }}"</span>
                @endif
            </div>
        </div>
        <span class="text-xs font-semibold opacity-75 shrink-0">{{ $report->created_at->translatedFormat('d F Y, H:i') }} WIB</span>
    </div>

    <div class="grid gap-6 lg:grid-cols-[1.4fr_1fr]">
        
        <!-- Left Column: Report Details & Actions -->
        <div class="space-y-6">
            
            <!-- Detail Aduan Panel -->
            <div class="portal-panel p-6 sm:p-7">
                <div class="flex items-center gap-2 text-rose-600 dark:text-rose-400 mb-2">
                    <span class="material-symbols-outlined text-xl">report</span>
                    <span class="text-xs font-bold uppercase tracking-wider">Kategori Dugaan Pelanggaran</span>
                </div>
                <h2 class="text-xl font-black text-slate-900 dark:text-white">{{ $report->reason }}</h2>

                <div class="mt-4 p-4 rounded-2xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700">
                    <span class="text-xs text-slate-400 font-semibold block mb-1">Kronologi &amp; Penjelasan Pelapor:</span>
                    <p class="text-sm leading-relaxed text-slate-800 dark:text-slate-200 whitespace-pre-line">
                        {{ $report->details ?: 'Pelapor tidak menyertakan rincian teks tambahan.' }}
                    </p>
                </div>

                <div class="mt-4 flex items-center justify-between text-xs text-slate-500 pt-3 border-t border-slate-100 dark:border-slate-800">
                    <span>ID Laporan: <strong>#REP-{{ str_pad($report->id, 5, '0', STR_PAD_LEFT) }}</strong></span>
                    <span>Dilaporkan pada {{ $report->created_at->diffForHumans() }}</span>
                </div>
            </div>

            <!-- Action Panel for Administrator -->
            <div class="portal-panel p-6 sm:p-7">
                <div class="flex items-center gap-2 text-brand-700 dark:text-brand-400 mb-2">
                    <span class="material-symbols-outlined text-xl">admin_panel_settings</span>
                    <span class="text-xs font-bold uppercase tracking-wider">Tindakan Pengawas Administrator</span>
                </div>
                <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-4">Eksekusi Keputusan Tindak Lanjut</h3>

                <form action="{{ route('admin.reports.action', $report) }}" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label for="action_type" class="portal-label">Pilih Jenis Tindakan <span class="text-rose-500">*</span></label>
                        <select id="action_type" name="action_type" required onchange="handleActionTypeChange(this.value)" class="portal-input">
                            <option value="">-- Pilih tindakan yang sesuai --</option>
                            <option value="close_job">1. Tutup Lowongan Ini (Nonaktifkan Rekrutmen)</option>
                            <option value="ban_employer">2. Bekukan Akun Mitra UMKM (Ban Akun &amp; Tutup Semua Lowongan)</option>
                            <option value="delete_job">3. Hapus Lowongan Permanen dari Sistem</option>
                            <option value="dismiss">4. Tolak Laporan (Tidak Ada Pelanggaran Terbukti)</option>
                        </select>
                    </div>

                    <!-- Close Job Options -->
                    <div id="closeJobOptions" class="hidden p-4 rounded-2xl bg-amber-50 dark:bg-amber-950/40 border border-amber-200 dark:border-amber-900/50 space-y-3">
                        <label for="close_duration" class="portal-label !text-amber-900 dark:!text-amber-200">Durasi Penutupan Lowongan <span class="text-rose-500">*</span></label>
                        <select id="close_duration" name="close_duration" onchange="handleCloseDurationChange(this.value)" class="portal-input">
                            <option value="7_days">7 Hari (1 Minggu)</option>
                            <option value="14_days" selected>14 Hari (2 Minggu)</option>
                            <option value="30_days">30 Hari (1 Bulan)</option>
                            <option value="custom">Kustom Jumlah Hari...</option>
                            <option value="permanent">Permanen</option>
                        </select>
                        <div id="closeCustomDaysContainer" class="hidden">
                            <input type="number" id="close_custom_days" name="close_custom_days" min="1" placeholder="Jumlah hari penutupan..." class="portal-input">
                        </div>
                    </div>

                    <!-- Ban Employer Options -->
                    <div id="banEmployerOptions" class="hidden p-4 rounded-2xl bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-900/50 space-y-3">
                        <label for="ban_duration" class="portal-label !text-rose-900 dark:!text-rose-200">Durasi Pemblokiran Akun Mitra <span class="text-rose-500">*</span></label>
                        <select id="ban_duration" name="ban_duration" onchange="handleBanDurationChange(this.value)" class="portal-input">
                            <option value="3_days">3 Hari</option>
                            <option value="7_days" selected>7 Hari (1 Minggu)</option>
                            <option value="14_days">14 Hari (2 Minggu)</option>
                            <option value="30_days">30 Hari (1 Bulan)</option>
                            <option value="custom">Kustom Jumlah Hari...</option>
                            <option value="permanent">Permanen (Selamanya)</option>
                        </select>
                        <div id="banCustomDaysContainer" class="hidden">
                            <input type="number" id="ban_custom_days" name="ban_custom_days" min="1" placeholder="Jumlah hari pemblokiran..." class="portal-input">
                        </div>
                        <p class="text-xs text-rose-700 dark:text-rose-300">
                            <em>Perhatian:</em> Seluruh lowongan aktif milik mitra ini akan otomatis ditutup selama masa hukuman.
                        </p>
                    </div>

                    <!-- Admin Notes -->
                    <div>
                        <label for="admin_notes" class="portal-label">Catatan Hasil Investigasi Administrator</label>
                        <textarea id="admin_notes" name="admin_notes" rows="2" placeholder="Tuliskan catatan pertimbangan pengawasan..." class="portal-input"></textarea>
                    </div>

                    <div class="pt-2 flex items-center justify-end">
                        <button type="submit" class="portal-button-primary">
                            <span class="material-symbols-outlined text-[18px]">gavel</span>
                            Eksekusi Tindakan
                        </button>
                    </div>
                </form>
            </div>

        </div>

        <!-- Right Column: Reported Job & Reporter Information -->
        <div class="space-y-6">
            
            <!-- Reported Job Info Card -->
            <div class="portal-panel p-6">
                <span class="portal-badge bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300 text-[10px] mb-2 block w-fit">
                    Lowongan Terlapor
                </span>
                <h3 class="font-bold text-base text-slate-900 dark:text-white">
                    <a href="{{ route('admin.jobs.show', $report->job) }}" class="hover:text-brand-700">
                        {{ $report->job->title }}
                    </a>
                </h3>
                <span class="text-xs text-slate-500 block mt-0.5">
                    {{ $report->job->category->name }} · {{ $report->job->location }}
                </span>

                <dl class="mt-4 space-y-2.5 text-xs border-t border-slate-100 dark:border-slate-800 pt-3">
                    <div class="flex justify-between">
                        <dt class="text-slate-400">Status Saat Ini:</dt>
                        <dd class="font-bold {{ $report->job->status === 'open' ? 'text-emerald-600' : 'text-rose-600' }}">
                            {{ $report->job->isClosedByAdmin() ? 'Ditutup Admin' : ($report->job->status === 'open' ? 'Dibuka' : 'Ditutup') }}
                        </dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-slate-400">Nominal Upah:</dt>
                        <dd class="font-semibold">Rp {{ number_format($report->job->salary_amount, 0, ',', '.') }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-slate-400">Jam Kerja:</dt>
                        <dd class="font-semibold">{{ $report->job->work_hours_per_day }} jam / hari</dd>
                    </div>
                </dl>

                <div class="mt-4 pt-3 border-t border-slate-100 dark:border-slate-800 flex items-center gap-2">
                    <a href="{{ route('admin.jobs.show', $report->job) }}" class="portal-button-secondary !py-1 !px-2.5 text-xs w-full justify-center">
                        Buka Halaman Lowongan
                    </a>
                    <a href="{{ route('admin.jobs.edit', $report->job) }}" class="portal-button-secondary !py-1 !px-2 text-xs" title="Edit lowongan">
                        <span class="material-symbols-outlined text-[16px]">edit</span>
                    </a>
                </div>
            </div>

            <!-- Reported Mitra Info Card -->
            <div class="portal-panel p-6">
                <span class="portal-badge bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300 text-[10px] mb-2 block w-fit">
                    Mitra UMKM Terlapor
                </span>
                <h4 class="font-bold text-base text-slate-900 dark:text-white">
                    {{ $report->job->employer->business_name ?: $report->job->employer->name }}
                </h4>
                <p class="text-xs text-slate-500 mt-0.5">Penanggung Jawab: {{ $report->job->employer->name }}</p>

                <dl class="mt-4 space-y-2 text-xs border-t border-slate-100 dark:border-slate-800 pt-3">
                    <div class="flex justify-between">
                        <dt class="text-slate-400">Email:</dt>
                        <dd class="font-semibold">{{ $report->job->employer->email }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-slate-400">Nomor Telepon:</dt>
                        <dd class="font-semibold">{{ $report->job->employer->phone ?: '-' }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-slate-400">Status Akun:</dt>
                        <dd class="font-bold {{ $report->job->employer->isBanned() ? 'text-rose-600' : 'text-emerald-600' }}">
                            {{ $report->job->employer->isBanned() ? 'Dibekukan (Ban)' : 'Aktif' }}
                        </dd>
                    </div>
                </dl>

                <div class="mt-4 pt-3 border-t border-slate-100 dark:border-slate-800">
                    <a href="{{ route('admin.users.show', $report->job->employer) }}" class="portal-button-secondary !py-1 !px-2.5 text-xs w-full justify-center">
                        Buka Profil Mitra
                    </a>
                </div>
            </div>

            <!-- Reporter Info Card -->
            <div class="portal-panel p-6">
                <span class="portal-badge bg-emerald-50 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300 text-[10px] mb-2 block w-fit">
                    Pencari Kerja / Pelapor
                </span>
                <h4 class="font-bold text-sm text-slate-900 dark:text-white">
                    {{ $report->reporter->name }}
                </h4>
                <p class="text-xs text-slate-500 mt-0.5">{{ $report->reporter->email }}</p>
                @if($report->reporter->phone)
                    <p class="text-xs text-slate-400 mt-0.5">{{ $report->reporter->phone }}</p>
                @endif
                <div class="mt-3 pt-3 border-t border-slate-100 dark:border-slate-800">
                    <a href="{{ route('admin.users.show', $report->reporter) }}" class="portal-button-secondary !py-1 !px-2.5 text-xs w-full justify-center">
                        Profil Pelapor
                    </a>
                </div>
            </div>

        </div>

    </div>
@endsection

@push('scripts')
<script>
    function handleActionTypeChange(val) {
        const closeOpts = document.getElementById('closeJobOptions');
        const banOpts = document.getElementById('banEmployerOptions');

        if (closeOpts) closeOpts.classList.add('hidden');
        if (banOpts) banOpts.classList.add('hidden');

        if (val === 'close_job' && closeOpts) {
            closeOpts.classList.remove('hidden');
        } else if (val === 'ban_employer' && banOpts) {
            banOpts.classList.remove('hidden');
        }
    }

    function handleCloseDurationChange(val) {
        const customContainer = document.getElementById('closeCustomDaysContainer');
        const customInput = document.getElementById('close_custom_days');
        if (customContainer) {
            if (val === 'custom') {
                customContainer.classList.remove('hidden');
                if (customInput) customInput.required = true;
            } else {
                customContainer.classList.add('hidden');
                if (customInput) customInput.required = false;
            }
        }
    }

    function handleBanDurationChange(val) {
        const customContainer = document.getElementById('banCustomDaysContainer');
        const customInput = document.getElementById('ban_custom_days');
        if (customContainer) {
            if (val === 'custom') {
                customContainer.classList.remove('hidden');
                if (customInput) customInput.required = true;
            } else {
                customContainer.classList.add('hidden');
                if (customInput) customInput.required = false;
            }
        }
    }
</script>
@endpush
