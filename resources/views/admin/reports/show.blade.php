@extends('layouts.admin')

@section('title', 'Tinjau Laporan Pelanggaran | Admin KerjaLokal')
@section('portal_title', 'Tinjauan Laporan Pelanggaran')
@section('portal_description', 'Analisis aduan pelamar, periksa integritas lowongan, dan ambil tindakan sanksi langsung demi standar kerja layak.')

@section('portal_actions')
    @if($report->status === 'pending')
        <form action="{{ route('admin.reports.review', $report) }}" method="POST">
            @csrf
            @method('PATCH')
            <button type="submit" class="portal-action-btn-primary !px-2.5 sm:!px-3" title="Tandai laporan sedang ditinjau">
                <span class="material-symbols-outlined text-[17px]">visibility</span>
                <span class="hidden sm:inline">Mulai Tinjauan</span>
                <span class="sm:hidden">Tinjau</span>
            </button>
        </form>
    @endif

    @if($report->status !== 'resolved' && $report->status !== 'dismissed')
        <button type="button" onclick="openResolveModal()" class="portal-action-btn-primary !bg-emerald-600 hover:!bg-emerald-700 !border-emerald-600 !px-2.5 sm:!px-3" title="Selesaikan laporan ini">
            <span class="material-symbols-outlined text-[17px]">check_circle</span>
            <span class="hidden sm:inline">Selesaikan Laporan</span>
            <span class="sm:hidden">Selesai</span>
        </button>
    @endif

    <button type="button" onclick="openDeleteModal()" class="portal-action-btn !text-rose-600 hover:!bg-rose-50 hover:!border-rose-300 dark:!text-rose-400 dark:hover:!bg-rose-950/40 !px-2.5 sm:!px-3" title="Hapus riwayat laporan ini dari panel admin">
        <span class="material-symbols-outlined text-[17px]">delete_outline</span>
        <span class="hidden sm:inline">Hapus Riwayat</span>
        <span class="sm:hidden">Hapus</span>
    </button>

    <a href="{{ $returnUrl ?? route('admin.reports.index') }}" 
       onclick="if (window.history.length > 1 && document.referrer && document.referrer.includes(window.location.host) && !document.referrer.includes(window.location.pathname)) { history.back(); return false; }" 
       class="portal-action-btn !px-2.5 sm:!px-3" 
       title="Kembali ke Daftar Laporan" 
       aria-label="Kembali ke Daftar Laporan">
        <span class="material-symbols-outlined text-[17px]">arrow_back</span>
        <span class="hidden sm:inline">Kembali ke Daftar Laporan</span>
        <span class="sm:hidden">Kembali</span>
    </a>
@endsection

@section('content')
    <!-- Status Header Banner -->
    <div class="mb-6 rounded-2xl p-4 sm:p-5 border flex flex-col sm:flex-row sm:items-center justify-between gap-4 shadow-2xs {{ match($report->status) {
        'action_taken' => 'bg-rose-50/90 border-rose-200 text-rose-900 dark:bg-rose-950/40 dark:border-rose-900/50 dark:text-rose-200',
        'resolved' => 'bg-emerald-50/90 border-emerald-200 text-emerald-950 dark:bg-emerald-950/40 dark:border-emerald-900/50 dark:text-emerald-200',
        'dismissed' => 'bg-slate-100 border-slate-200 text-slate-800 dark:bg-slate-800 dark:border-slate-700 dark:text-slate-200',
        default => 'bg-amber-50/90 border-amber-200 text-amber-950 dark:bg-amber-950/40 dark:border-amber-900/50 dark:text-amber-200'
    } }}">
        <div class="flex items-center gap-3.5 min-w-0">
            <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0 shadow-2xs {{ match($report->status) {
                'action_taken' => 'bg-rose-100 text-rose-700 dark:bg-rose-900/60 dark:text-rose-300',
                'resolved' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/60 dark:text-emerald-300',
                'dismissed' => 'bg-slate-200 text-slate-700 dark:bg-slate-700 dark:text-slate-300',
                default => 'bg-amber-100 text-amber-800 dark:bg-amber-900/60 dark:text-amber-300'
            } }}">
                <span class="material-symbols-outlined text-[22px]">
                    {{ match($report->status) {
                        'action_taken' => 'gavel',
                        'resolved' => 'check_circle',
                        'dismissed' => 'check_circle',
                        default => 'policy'
                    } }}
                </span>
            </div>
            <div class="min-w-0">
                <div class="flex items-center gap-2 flex-wrap">
                    <strong class="text-sm sm:text-base font-bold">
                        Status: {{ match($report->status) {
                            'action_taken' => 'Sanksi Telah Diberikan',
                            'resolved' => 'Laporan Telah Selesai Ditangani',
                            'dismissed' => 'Laporan Ditolak / Tidak Ditemukan Pelanggaran',
                            'reviewed' => 'Sedang Ditinjau Administrator',
                            default => 'Menunggu Tindakan'
                        } }}
                    </strong>
                    <span class="font-mono text-xs opacity-75 bg-black/5 dark:bg-white/10 px-2 py-0.5 rounded-md font-semibold">
                        #REP-{{ str_pad($report->id, 5, '0', STR_PAD_LEFT) }}
                    </span>
                </div>
                @if($report->action_taken)
                    <p class="text-xs mt-1 font-semibold opacity-90 flex items-center gap-1">
                        <span class="material-symbols-outlined text-[15px]">task_alt</span>
                        <span>Tindakan: {{ $report->action_taken }}</span>
                    </p>
                @endif
                @if($report->admin_notes)
                    <p class="text-xs mt-0.5 opacity-85 italic">"{{ $report->admin_notes }}"</p>
                @endif
            </div>
        </div>
        <span class="text-xs font-semibold opacity-80 shrink-0 flex items-center gap-1.5 self-start sm:self-center bg-black/5 dark:bg-white/10 px-3 py-1.5 rounded-xl">
            <span class="material-symbols-outlined text-[15px]">event</span>
            {{ $report->created_at->translatedFormat('d F Y, H:i') }} WIB
        </span>
    </div>

    <div class="grid gap-6 lg:grid-cols-[1.35fr_1fr]">
        
        <!-- Left Column: Report Details & Actions -->
        <div class="space-y-6">
            
            <!-- Detail Aduan Panel (Rose / Alert Palette) -->
            <div class="portal-panel p-6 sm:p-7 bg-white dark:bg-slate-900 shadow-xs relative overflow-hidden">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-2xl bg-rose-50 text-rose-600 dark:bg-rose-950/60 dark:text-rose-400 border border-rose-200/70 dark:border-rose-900/50 flex items-center justify-center shrink-0 shadow-2xs">
                        <span class="material-symbols-outlined text-[20px]">report</span>
                    </div>
                    <div>
                        <span class="inline-flex items-center gap-1.5 rounded-md bg-rose-50 px-2.5 py-0.5 text-[10px] font-bold uppercase tracking-wider text-rose-700 border border-rose-200/70 dark:bg-rose-950/50 dark:text-rose-300 dark:border-rose-900/50">
                            <span class="w-1.5 h-1.5 rounded-full bg-rose-500 animate-pulse"></span>
                            Kategori Dugaan Pelanggaran
                        </span>
                    </div>
                </div>

                <h2 class="mt-3.5 text-xl sm:text-2xl font-black tracking-tight text-slate-950 dark:text-white">
                    {{ $report->reason }}
                </h2>

                <div class="mt-4 p-4 sm:p-5 rounded-2xl bg-slate-50/90 dark:bg-slate-850/60 border border-slate-200/80 dark:border-slate-800">
                    <div class="flex items-center gap-1.5 text-xs font-bold text-slate-600 dark:text-slate-300 mb-2">
                        <span class="material-symbols-outlined text-[16px] text-rose-500">article</span>
                        <span>Kronologi &amp; Penjelasan Pelapor:</span>
                    </div>
                    <p class="text-sm leading-relaxed text-slate-800 dark:text-slate-200 whitespace-pre-line font-medium">
                        {{ $report->details ?: 'Pelapor tidak menyertakan rincian teks tambahan.' }}
                    </p>
                </div>

                <div class="mt-4 flex flex-col sm:flex-row sm:items-center justify-between gap-2 text-xs text-slate-500 pt-3.5 border-t border-slate-100 dark:border-slate-800">
                    <span class="flex items-center gap-1.5">
                        <span class="text-slate-400">ID Laporan:</span>
                        <strong class="font-mono bg-slate-100 dark:bg-slate-800 px-2 py-0.5 rounded text-slate-800 dark:text-slate-200">#REP-{{ str_pad($report->id, 5, '0', STR_PAD_LEFT) }}</strong>
                    </span>
                    <span class="flex items-center gap-1 text-slate-500">
                        <span class="material-symbols-outlined text-[15px] text-slate-400">schedule</span>
                        Dilaporkan {{ $report->created_at->diffForHumans() }}
                    </span>
                </div>
            </div>

            <!-- Action Panel for Administrator (Slate / Brand Palette) -->
            <div class="portal-panel p-6 sm:p-7 bg-white dark:bg-slate-900 shadow-xs relative overflow-hidden">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-10 h-10 rounded-2xl bg-brand-50 text-brand-700 dark:bg-brand-950/60 dark:text-brand-300 border border-brand-200/70 dark:border-brand-900/50 flex items-center justify-center shrink-0 shadow-2xs">
                        <span class="material-symbols-outlined text-[20px]">admin_panel_settings</span>
                    </div>
                    <div>
                        <span class="inline-flex items-center gap-1 rounded-md bg-brand-50 px-2.5 py-0.5 text-[10px] font-bold uppercase tracking-wider text-brand-800 border border-brand-200/70 dark:bg-brand-950/50 dark:text-brand-300">
                            Panel Keputusan Pengawasan
                        </span>
                        <h3 class="text-base sm:text-lg font-bold text-slate-950 dark:text-white mt-0.5">Eksekusi Tindakan Pengawas</h3>
                    </div>
                </div>

                <form action="{{ route('admin.reports.action', $report) }}" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label for="action_type" class="portal-label">Pilih Jenis Tindakan Sanksi <span class="text-rose-500">*</span></label>
                        <select id="action_type" name="action_type" required onchange="handleActionTypeChange(this.value)" class="portal-input">
                            <option value="">-- Pilih tindakan yang sesuai --</option>
                            <option value="close_job">1. Tutup Lowongan Ini (Nonaktifkan Rekrutmen)</option>
                            <option value="ban_employer">2. Bekukan Akun Mitra UMKM (Ban Akun &amp; Tutup Semua Lowongan)</option>
                            <option value="delete_job">3. Hapus Lowongan Permanen dari Sistem</option>
                            <option value="dismiss">4. Tolak Laporan (Tidak Ada Pelanggaran Terbukti)</option>
                            <option value="resolve">5. Selesaikan Laporan (Tandai Penanganan Tuntas)</option>
                        </select>
                    </div>

                    <!-- Close Job Options -->
                    <div id="closeJobOptions" class="hidden p-4 rounded-2xl bg-amber-50/90 dark:bg-amber-950/40 border border-amber-200 dark:border-amber-900/50 space-y-3">
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
                    <div id="banEmployerOptions" class="hidden p-4 rounded-2xl bg-rose-50/90 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-900/50 space-y-3">
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
                        <p class="text-xs text-rose-700 dark:text-rose-300 flex items-start gap-1">
                            <span class="material-symbols-outlined text-[15px] shrink-0 mt-0.5">warning</span>
                            <span>Seluruh lowongan aktif milik mitra ini akan otomatis ditutup selama masa hukuman.</span>
                        </p>
                    </div>

                    <!-- Admin Notes -->
                    <div>
                        <label for="admin_notes" class="portal-label">Catatan Hasil Investigasi Administrator</label>
                        <textarea id="admin_notes" name="admin_notes" rows="2" placeholder="Tuliskan catatan pertimbangan pengawasan..." class="portal-input"></textarea>
                    </div>

                    <div class="pt-2 flex items-center justify-end">
                        <button type="submit" class="portal-button-primary !py-2 !px-4">
                            <span class="material-symbols-outlined text-[18px]">gavel</span>
                            Eksekusi Tindakan
                        </button>
                    </div>
                </form>
            </div>

        </div>

        <!-- Right Column: Reported Job & Reported Mitra & Reporter Cards -->
        <div class="space-y-6">
            
            <!-- 1. Reported Job Info Card (Indigo / Job Palette) -->
            <div class="portal-panel p-5 sm:p-6 bg-white dark:bg-slate-900 shadow-xs relative overflow-hidden transition hover:shadow-md">
                <div class="flex items-start gap-3.5">
                    <div class="w-11 h-11 rounded-2xl bg-indigo-50 text-indigo-600 dark:bg-indigo-950/60 dark:text-indigo-400 border border-indigo-200/70 dark:border-indigo-800/50 flex items-center justify-center shrink-0 shadow-2xs">
                        <span class="material-symbols-outlined text-[22px]">work</span>
                    </div>
                    <div class="min-w-0 flex-1">
                        <div class="flex items-center gap-1.5 flex-wrap">
                            <span class="inline-flex items-center gap-1 rounded-md bg-indigo-50 px-2 py-0.5 text-[10px] font-bold text-indigo-700 border border-indigo-200/70 dark:bg-indigo-950/50 dark:text-indigo-300 dark:border-indigo-800/50">
                                <span class="w-1.5 h-1.5 rounded-full bg-indigo-500"></span>
                                Lowongan Terlapor
                            </span>
                            <span class="text-[11px] font-medium text-slate-500 dark:text-slate-400">
                                {{ $report->job->category->name }}
                            </span>
                        </div>
                        <h3 class="font-bold text-base text-slate-950 dark:text-white mt-1 group">
                            <a href="{{ route('admin.jobs.show', ['job' => $report->job, 'return_to' => url()->full()]) }}" class="hover:text-indigo-600 dark:hover:text-indigo-400 transition inline-flex items-center gap-1">
                                <span>{{ $report->job->title }}</span>
                                <span class="material-symbols-outlined text-[14px] opacity-0 group-hover:opacity-100 transition-opacity text-indigo-500">open_in_new</span>
                            </a>
                        </h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5 flex items-center gap-1">
                            <span class="material-symbols-outlined text-[14px] text-indigo-500">location_on</span>
                            <span>{{ $report->job->location }}</span>
                        </p>
                    </div>
                </div>

                @if($report->job->cover_image_url)
                    <div class="mt-3.5 relative h-24 w-full rounded-xl overflow-hidden border border-slate-200/80 dark:border-slate-800 shadow-2xs group">
                        <img src="{{ $report->job->cover_image_url }}" alt="{{ $report->job->title }}" class="h-full w-full object-cover group-hover:scale-105 transition duration-300">
                    </div>
                @endif

                <div class="mt-4 rounded-xl bg-slate-50/80 dark:bg-slate-850/50 p-3.5 border border-slate-100 dark:border-slate-800/80 space-y-2.5 text-xs">
                    <div class="flex items-center justify-between gap-2">
                        <span class="text-slate-500 dark:text-slate-400 flex items-center gap-1.5">
                            <span class="material-symbols-outlined text-[15px] text-slate-400">tune</span>
                            Status Saat Ini
                        </span>
                        <div>
                            @if($report->job->isClosedByAdmin())
                                <span class="inline-flex items-center gap-1 font-bold text-rose-600 dark:text-rose-400">
                                    <span class="material-symbols-outlined text-[13px]">gavel</span>
                                    Ditutup Admin
                                </span>
                            @elseif($report->job->status === 'open')
                                <span class="inline-flex items-center gap-1 font-bold text-emerald-600 dark:text-emerald-400">
                                    <span class="material-symbols-outlined text-[13px]">check_circle</span>
                                    Sedang Dibuka
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 font-bold text-slate-500 dark:text-slate-400">
                                    <span class="material-symbols-outlined text-[13px]">cancel</span>
                                    Ditutup Mitra
                                </span>
                            @endif
                        </div>
                    </div>
                    <div class="flex items-center justify-between gap-2">
                        <span class="text-slate-500 dark:text-slate-400 flex items-center gap-1.5">
                            <span class="material-symbols-outlined text-[15px] text-slate-400">payments</span>
                            Nominal Upah
                        </span>
                        <span class="font-bold text-slate-900 dark:text-white">
                            Rp {{ number_format($report->job->salary_amount, 0, ',', '.') }}
                            <span class="font-normal text-[11px] text-slate-500">/ {{ $report->job->salary_type === 'monthly' ? 'bln' : 'hari' }}</span>
                        </span>
                    </div>
                    <div class="flex items-center justify-between gap-2">
                        <span class="text-slate-500 dark:text-slate-400 flex items-center gap-1.5">
                            <span class="material-symbols-outlined text-[15px] text-slate-400">schedule</span>
                            Jam Kerja
                        </span>
                        <span class="font-semibold text-slate-800 dark:text-slate-200">
                            {{ $report->job->work_hours_per_day }} jam / hari
                        </span>
                    </div>
                    <div class="flex items-center justify-between gap-2">
                        <span class="text-slate-500 dark:text-slate-400 flex items-center gap-1.5">
                            <span class="material-symbols-outlined text-[15px] text-slate-400">groups</span>
                            Total Pelamar
                        </span>
                        <span class="font-semibold text-slate-800 dark:text-slate-200">
                            {{ $report->job->applications_count }} kandidat
                        </span>
                    </div>
                </div>

                <div class="mt-4 pt-3 border-t border-slate-100 dark:border-slate-800 flex items-center gap-2">
                    <a href="{{ route('admin.jobs.show', ['job' => $report->job, 'return_to' => url()->full()]) }}" class="portal-button-secondary !py-1.5 !px-3 text-xs flex-1 justify-center !text-indigo-700 hover:!bg-indigo-50 hover:!border-indigo-300 dark:!text-indigo-300 dark:hover:!bg-indigo-950/50 transition">
                        <span class="material-symbols-outlined text-[16px]">visibility</span>
                        <span>Buka Halaman Lowongan</span>
                    </a>
                    <a href="{{ route('admin.jobs.edit', ['job' => $report->job, 'return_to' => url()->full()]) }}" class="portal-button-secondary !py-1.5 !px-2.5 text-xs text-slate-600 hover:text-indigo-700 dark:text-slate-400 dark:hover:text-indigo-300 transition" title="Edit lowongan ini">
                        <span class="material-symbols-outlined text-[16px]">edit</span>
                    </a>
                </div>
            </div>

            <!-- 2. Reported Mitra Info Card (Amber / Store Palette) -->
            <div class="portal-panel p-5 sm:p-6 bg-white dark:bg-slate-900 shadow-xs relative overflow-hidden transition hover:shadow-md">
                <div class="flex items-start gap-3.5">
                    @if($report->job->employer->avatar_url)
                        <img src="{{ $report->job->employer->avatar_url }}" alt="{{ $report->job->employer->name }}" class="w-11 h-11 rounded-2xl object-cover ring-2 ring-amber-500/40 shrink-0 shadow-2xs">
                    @else
                        <div class="w-11 h-11 rounded-2xl bg-amber-50 text-amber-700 dark:bg-amber-950/60 dark:text-amber-300 border border-amber-200/70 dark:border-amber-900/50 flex items-center justify-center shrink-0 shadow-2xs">
                            <span class="material-symbols-outlined text-[22px]">storefront</span>
                        </div>
                    @endif
                    <div class="min-w-0 flex-1">
                        <span class="inline-flex items-center gap-1 rounded-md bg-amber-50 px-2 py-0.5 text-[10px] font-bold text-amber-800 border border-amber-200/70 dark:bg-amber-950/50 dark:text-amber-300 dark:border-amber-800/50">
                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                            Mitra UMKM Terlapor
                        </span>
                        <h4 class="font-bold text-base text-slate-950 dark:text-white mt-1 truncate">
                            {{ $report->job->employer->business_name ?: $report->job->employer->name }}
                        </h4>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5 truncate">
                            Penanggung Jawab: <span class="font-semibold text-slate-700 dark:text-slate-300">{{ $report->job->employer->name }}</span>
                            @if($report->job->employer->username)
                                <span class="font-mono text-amber-700 dark:text-amber-400 font-semibold">(@<span>{{ $report->job->employer->username }}</span>)</span>
                            @endif
                        </p>
                    </div>
                </div>

                <div class="mt-4 rounded-xl bg-slate-50/80 dark:bg-slate-850/50 p-3.5 border border-slate-100 dark:border-slate-800/80 space-y-2.5 text-xs">
                    <div class="flex items-center justify-between gap-2">
                        <span class="text-slate-500 dark:text-slate-400 flex items-center gap-1.5">
                            <span class="material-symbols-outlined text-[15px] text-slate-400">mail</span>
                            Email
                        </span>
                        <span class="font-semibold text-slate-800 dark:text-slate-200 break-all text-right">
                            {{ $report->job->employer->email }}
                        </span>
                    </div>
                    <div class="flex items-center justify-between gap-2">
                        <span class="text-slate-500 dark:text-slate-400 flex items-center gap-1.5">
                            <span class="material-symbols-outlined text-[15px] text-slate-400">call</span>
                            Nomor Telepon
                        </span>
                        <span class="font-semibold text-slate-800 dark:text-slate-200">
                            {{ $report->job->employer->phone ?: 'Belum diisi' }}
                        </span>
                    </div>
                    <div class="flex items-center justify-between gap-2 pt-1.5 border-t border-slate-200/50 dark:border-slate-800/60">
                        <span class="text-slate-500 dark:text-slate-400 flex items-center gap-1.5">
                            <span class="material-symbols-outlined text-[15px] text-slate-400">verified_user</span>
                            Status Akun
                        </span>
                        <div>
                            @if($report->job->employer->isBanned())
                                <span class="inline-flex items-center gap-1 rounded-md bg-rose-50 px-2 py-0.5 text-xs font-bold text-rose-700 border border-rose-200 dark:bg-rose-950/50 dark:text-rose-300 dark:border-rose-900">
                                    <span class="material-symbols-outlined text-[13px]">block</span>
                                    Dibekukan (Ban)
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 rounded-md bg-emerald-50 px-2 py-0.5 text-xs font-bold text-emerald-700 border border-emerald-200 dark:bg-emerald-950/50 dark:text-emerald-300 dark:border-emerald-900">
                                    <span class="material-symbols-outlined text-[13px]">check_circle</span>
                                    Akun Aktif
                                </span>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="mt-4 pt-3 border-t border-slate-100 dark:border-slate-800">
                    <a href="{{ route('admin.users.show', ['user' => $report->job->employer, 'return_to' => url()->full()]) }}" class="portal-button-secondary !py-1.5 !px-3 text-xs w-full justify-center !text-amber-800 hover:!bg-amber-50 hover:!border-amber-300 dark:!text-amber-300 dark:hover:!bg-amber-950/50 transition">
                        <span class="material-symbols-outlined text-[16px]">manage_accounts</span>
                        <span>Buka Profil Mitra</span>
                    </a>
                </div>
            </div>

            <!-- 3. Reporter Info Card (Teal / User Palette) -->
            <div class="portal-panel p-5 sm:p-6 bg-white dark:bg-slate-900 shadow-xs relative overflow-hidden transition hover:shadow-md">
                <div class="flex items-start gap-3.5">
                    @if($report->reporter->avatar_url)
                        <img src="{{ $report->reporter->avatar_url }}" alt="{{ $report->reporter->name }}" class="w-11 h-11 rounded-2xl object-cover ring-2 ring-teal-500/40 shrink-0 shadow-2xs">
                    @else
                        <div class="w-11 h-11 rounded-2xl bg-teal-50 text-teal-700 dark:bg-teal-950/60 dark:text-teal-300 border border-teal-200/70 dark:border-teal-900/50 flex items-center justify-center font-bold text-sm shrink-0 shadow-2xs">
                            {{ strtoupper(substr($report->reporter->name, 0, 2)) }}
                        </div>
                    @endif
                    <div class="min-w-0 flex-1">
                        <span class="inline-flex items-center gap-1 rounded-md bg-teal-50 px-2 py-0.5 text-[10px] font-bold text-teal-800 border border-teal-200/70 dark:bg-teal-950/50 dark:text-teal-300 dark:border-teal-800/50">
                            <span class="w-1.5 h-1.5 rounded-full bg-teal-500"></span>
                            Pencari Kerja / Pelapor
                        </span>
                        <h4 class="font-bold text-base text-slate-950 dark:text-white mt-1 truncate">
                            {{ $report->reporter->name }}
                        </h4>
                        @if($report->reporter->username)
                            <p class="text-xs font-mono text-teal-700 dark:text-teal-400 font-semibold mt-0.5">
                                @<span>{{ $report->reporter->username }}</span>
                            </p>
                        @endif
                    </div>
                </div>

                <div class="mt-4 rounded-xl bg-slate-50/80 dark:bg-slate-850/50 p-3.5 border border-slate-100 dark:border-slate-800/80 space-y-2.5 text-xs">
                    <div class="flex items-center justify-between gap-2">
                        <span class="text-slate-500 dark:text-slate-400 flex items-center gap-1.5">
                            <span class="material-symbols-outlined text-[15px] text-slate-400">mail</span>
                            Email
                        </span>
                        <span class="font-semibold text-slate-800 dark:text-slate-200 break-all text-right">
                            {{ $report->reporter->email }}
                        </span>
                    </div>
                    @if($report->reporter->phone)
                        <div class="flex items-center justify-between gap-2">
                            <span class="text-slate-500 dark:text-slate-400 flex items-center gap-1.5">
                                <span class="material-symbols-outlined text-[15px] text-slate-400">call</span>
                                Nomor Telepon
                            </span>
                            <span class="font-semibold text-slate-800 dark:text-slate-200">
                                {{ $report->reporter->phone }}
                            </span>
                        </div>
                    @endif
                </div>

                <div class="mt-4 pt-3 border-t border-slate-100 dark:border-slate-800">
                    <a href="{{ route('admin.users.show', ['user' => $report->reporter, 'return_to' => url()->full()]) }}" class="portal-button-secondary !py-1.5 !px-3 text-xs w-full justify-center !text-teal-800 hover:!bg-teal-50 hover:!border-teal-300 dark:!text-teal-300 dark:hover:!bg-teal-950/50 transition">
                        <span class="material-symbols-outlined text-[16px]">visibility</span>
                        <span>Buka Profil Pelapor</span>
                    </a>
                </div>
            </div>

        </div>

    </div>

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
                Laporan untuk lowongan <strong class="text-slate-900 dark:text-white">"{{ $report->job?->title ?? 'Lowongan Ini' }}"</strong> akan ditandai berstatus <strong>Selesai (Resolved)</strong> dan dikeluarkan dari daftar laporan aktif.
            </p>

            <form method="POST" action="{{ route('admin.reports.resolve', $report) }}">
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
                Apakah Anda yakin ingin menghapus riwayat laporan ini dari panel admin? Riwayat ini tidak akan ditampilkan lagi di daftar laporan admin.
            </p>

            <form method="POST" action="{{ route('admin.reports.destroy', $report) }}">
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
@endsection

@push('scripts')
<script>
    function openResolveModal() {
        const modal = document.getElementById('resolveModal');
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

    function openDeleteModal() {
        const modal = document.getElementById('deleteModal');
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
