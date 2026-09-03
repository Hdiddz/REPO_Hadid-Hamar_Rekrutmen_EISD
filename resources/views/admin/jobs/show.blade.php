@extends('layouts.admin')

@section('title', $job->title.' | Admin KerjaLokal')
@section('portal_title', $job->title)
@section('portal_description', 'Detail lowongan, status kepatuhan etis, profil mitra penerbit, serta pantau seluruh pelamar dan status seleksinya.')

@section('portal_actions')
    <a href="{{ route('admin.jobs.index') }}" class="portal-action-btn">
        <span class="material-symbols-outlined text-[17px]">arrow_back</span>
        Kembali
    </a>

    <a href="{{ route('admin.jobs.edit', $job) }}" class="portal-action-btn">
        <span class="material-symbols-outlined text-[17px]">edit</span>
        Edit Lowongan
    </a>

    @if($job->status === 'open')
        <button type="button" onclick="openCloseJobModal({{ $job->id }}, '{{ addslashes($job->title) }}')" class="portal-action-btn-warning">
            <span class="material-symbols-outlined text-[17px]">lock</span>
            Tutup Lowongan
        </button>
    @else
        <form action="{{ route('admin.jobs.reopen', $job) }}" method="POST">
            @csrf
            <button type="submit" class="portal-action-btn-success cursor-pointer">
                <span class="material-symbols-outlined text-[17px]">lock_open</span>
                Buka Kembali
            </button>
        </form>
    @endif

    <button type="button" onclick="openDeleteJobModal()" class="portal-action-btn-danger cursor-pointer" title="Hapus Lowongan">
        <span class="material-symbols-outlined text-[17px]">delete</span>
        Hapus
    </button>
@endsection

@section('content')
    <!-- Closure Banner if Closed by Admin -->
    @if($job->isClosedByAdmin())
        <div class="mb-6 rounded-2xl bg-rose-50 p-4 border border-rose-200 dark:bg-rose-950/40 dark:border-rose-900/50 flex items-start gap-3">
            <span class="material-symbols-outlined text-rose-600 text-2xl shrink-0 mt-0.5">gavel</span>
            <div class="flex-1">
                <h3 class="font-bold text-sm text-rose-900 dark:text-rose-200">Lowongan Ini Sedang Ditutup oleh Administrator</h3>
                <p class="text-xs text-rose-800 dark:text-rose-300 mt-0.5">
                    <strong>Alasan penutupan:</strong> {{ $job->closed_reason ?: 'Peninjauan kepatuhan etis rekrutmen.' }}
                </p>
                <p class="text-xs text-rose-700 dark:text-rose-400 mt-0.5">
                    <strong>Masa berlaku penutupan:</strong> {{ $job->closed_until ? 'Hingga '.$job->closed_until->translatedFormat('d F Y, H:i').' WIB' : 'Permanen (sampai dibuka kembali)' }}
                </p>
            </div>
            <form action="{{ route('admin.jobs.reopen', $job) }}" method="POST">
                @csrf
                <button type="submit" class="portal-button-secondary !py-1 !px-2.5 text-xs !bg-white">
                    Buka Kembali
                </button>
            </form>
        </div>
    @endif

    <div class="grid gap-6 lg:grid-cols-[1fr_340px]">
        <div class="space-y-6">
            
            <!-- Job Specs Panel -->
            <section class="portal-panel p-6">
                <div class="flex flex-wrap items-center gap-2">
                    <span class="portal-badge bg-brand-50 text-brand-700 dark:bg-brand-950 dark:text-brand-300">
                        {{ $job->category->name }}
                    </span>
                    <span class="portal-badge {{ $job->status === 'open' ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/40 dark:text-emerald-300' : 'bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-300' }}">
                        {{ $job->isClosedByAdmin() ? 'Ditutup Admin' : ($job->status === 'open' ? 'Dibuka' : 'Ditutup') }}
                    </span>
                    @if($job->reports_count > 0)
                        <span class="portal-badge bg-rose-100 text-rose-800 dark:bg-rose-950 dark:text-rose-300 font-bold">
                            <span class="material-symbols-outlined text-[13px] mr-1">flag</span>
                            {{ $job->reports_count }} Laporan Masuk
                        </span>
                    @endif
                </div>

                <dl class="mt-6 grid gap-4 sm:grid-cols-3">
                    <div>
                        <dt class="text-xs text-slate-400">Lokasi Penempatan</dt>
                        <dd class="mt-1 font-bold text-slate-900 dark:text-white">{{ $job->location }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs text-slate-400">Nominal Upah</dt>
                        <dd class="mt-1 font-bold text-slate-900 dark:text-white">
                            Rp {{ number_format($job->salary_amount, 0, ',', '.') }}
                            <span class="text-xs font-normal text-slate-500">/ {{ $job->salary_type === 'monthly' ? 'bulan' : 'hari' }}</span>
                        </dd>
                    </div>
                    <div>
                        <dt class="text-xs text-slate-400">Jam Kerja per Hari</dt>
                        <dd class="mt-1 font-bold text-slate-900 dark:text-white">{{ $job->work_hours_per_day }} jam / hari</dd>
                    </div>
                </dl>

                <div class="mt-6 border-t border-slate-100 pt-6 dark:border-slate-800">
                    <h2 class="font-bold text-slate-900 dark:text-white text-sm">Deskripsi Pekerjaan</h2>
                    <p class="mt-3 whitespace-pre-line text-sm leading-7 text-slate-600 dark:text-slate-300">
                        {{ $job->description }}
                    </p>
                </div>

                <div class="mt-6 border-t border-slate-100 pt-6 dark:border-slate-800">
                    <h2 class="font-bold text-slate-900 dark:text-white text-sm mb-3">Keterampilan Terkait</h2>
                    <div class="flex flex-wrap gap-2">
                        @forelse($job->skills as $skill)
                            <span class="rounded-lg bg-slate-100 px-3 py-1.5 text-xs font-bold text-slate-700 dark:bg-slate-800 dark:text-slate-200">
                                {{ $skill->name }}
                            </span>
                        @empty
                            <span class="text-xs text-slate-400">Tidak ada keterampilan spesifik dicantumkan.</span>
                        @endforelse
                    </div>
                </div>
            </section>

            <!-- Applicants Table Section -->
            <section class="portal-panel overflow-hidden" id="pelamar">
                <div class="portal-panel-header">
                    <div>
                        <h2 class="font-bold text-slate-900 dark:text-white text-base">Pelamar Masuk</h2>
                        <p class="text-xs text-slate-500">Total {{ $job->applications_count }} pelamar terdaftar pada lowongan ini.</p>
                    </div>
                </div>

                <div class="divide-y divide-slate-100 dark:divide-slate-800">
                    @forelse($job->applications as $application)
                        <div class="p-5 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                            <div class="min-w-0">
                                <div class="flex flex-wrap items-center gap-2">
                                    <h3 class="font-bold text-sm text-slate-900 dark:text-white">
                                        <a href="{{ route('admin.users.show', $application->user) }}" class="hover:text-brand-700">
                                            {{ $application->user->name }}
                                        </a>
                                    </h3>
                                    @php
                                        $isAdminAppResigned = $application->status === 'resigned' || $application->resignation_status === 'approved';
                                    @endphp
                                    @if($isAdminAppResigned)
                                        <span class="portal-badge bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300 border border-slate-300 text-[10px] font-bold inline-flex items-center gap-1">
                                            <span class="material-symbols-outlined text-[13px]">person_cancel</span>
                                            Telah Resign
                                        </span>
                                    @elseif($application->resignation_status === 'pending')
                                        <span class="portal-badge bg-amber-100 text-amber-900 dark:bg-amber-950 dark:text-amber-200 border border-amber-300 text-[10px] font-bold inline-flex items-center gap-1">
                                            <span class="material-symbols-outlined text-[13px]">pending_actions</span>
                                            Resign Diajukan (Ditinjau)
                                        </span>
                                    @else
                                        <span class="portal-badge {{ match($application->status) {'accepted' => 'bg-emerald-50 text-emerald-800', 'interview' => 'bg-indigo-50 text-indigo-800', 'rejected' => 'bg-rose-50 text-rose-800', 'reviewed' => 'bg-blue-50 text-blue-800', default => 'bg-amber-50 text-amber-800'} }} text-[10px]">
                                            Status: {{ match($application->status) {'accepted' => 'Disetujui / Diterima', 'interview' => 'Wawancara', 'rejected' => 'Ditolak', 'reviewed' => 'Ditinjau Mitra', default => 'Belum Ditinjau (Pending)'} }}
                                        </span>
                                    @endif
                                </div>
                                <p class="mt-1 text-xs text-slate-500">
                                    {{ $application->user->email }}{{ $application->user->phone ? ' · '.$application->user->phone : '' }}
                                    · Mendaftar pada {{ $application->created_at->translatedFormat('d M Y, H:i') }}
                                </p>

                                @if($application->resignation_status === 'approved' || $application->status === 'resigned')
                                    <div class="mt-2.5 rounded-xl bg-slate-100 dark:bg-slate-800/80 p-2.5 text-xs text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-slate-700">
                                        <div class="flex items-center gap-1.5 font-bold">
                                            <span class="material-symbols-outlined text-[15px] text-slate-600 dark:text-slate-400">check_circle</span>
                                            <span>Pengunduran Diri Resmi Selesai</span>
                                            @if($application->resignation_date)
                                                <span class="font-normal text-slate-500">· Efektif: {{ $application->resignation_date->translatedFormat('d F Y') }}</span>
                                            @endif
                                            @if($application->resigned_at)
                                                <span class="font-normal text-slate-400">· Disetujui: {{ $application->resigned_at->translatedFormat('d M Y, H:i') }}</span>
                                            @endif
                                        </div>
                                        @if($application->resignation_reason)
                                            <p class="mt-1 text-[11px] text-slate-600 dark:text-slate-400 italic">"{{ $application->resignation_reason }}"</p>
                                        @endif
                                    </div>
                                @elseif($application->resignation_status === 'pending')
                                    <div class="mt-2.5 rounded-xl bg-amber-50 dark:bg-amber-950/40 p-2.5 text-xs text-amber-900 dark:text-amber-200 border border-amber-200 dark:border-amber-900">
                                        <div class="flex items-center gap-1.5 font-bold">
                                            <span class="material-symbols-outlined text-[15px] text-amber-600">pending_actions</span>
                                            <span>Pengajuan Resign Masuk (Sedang Ditinjau Mitra)</span>
                                            @if($application->resignation_date)
                                                <span class="font-normal text-amber-700">· Efektif yang diajukan: {{ $application->resignation_date->translatedFormat('d F Y') }}</span>
                                            @endif
                                        </div>
                                        @if($application->resignation_reason)
                                            <p class="mt-1 text-[11px] text-amber-800 dark:text-amber-300 italic">"{{ $application->resignation_reason }}"</p>
                                        @endif
                                    </div>
                                @endif
                            </div>

                            <div class="flex items-center gap-2 shrink-0">
                                @if($application->resume_file)
                                    <button type="button" onclick="openPdfViewer('{{ route('admin.applications.resume.preview', $application) }}', '{{ addslashes($application->user->name) }}', '{{ route('admin.applications.resume', $application) }}')" class="portal-button-primary !py-1.5 !px-3 text-xs gap-1.5 cursor-pointer shadow-xs whitespace-nowrap" title="Pratinjau CV/Resume kandidat di browser">
                                        <span class="material-symbols-outlined text-[16px]">visibility</span>
                                        <span>Lihat Resume</span>
                                    </button>
                                @else
                                    <span class="text-xs text-slate-400 italic">Tanpa lampiran CV</span>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="p-10 text-center text-sm text-slate-500">
                            <span class="material-symbols-outlined text-4xl text-slate-300 block mb-2">person_search</span>
                            Belum ada pelamar yang mendaftar pada lowongan ini.
                        </div>
                    @endforelse
                </div>
            </section>

            <!-- Reports on this Job Section -->
            @if($job->reports->count() > 0)
                <section class="portal-panel overflow-hidden">
                    <div class="portal-panel-header bg-rose-50/50 dark:bg-rose-950/20">
                        <div>
                            <h2 class="font-bold text-rose-900 dark:text-rose-200 text-base flex items-center gap-1.5">
                                <span class="material-symbols-outlined text-rose-600 text-lg">flag</span>
                                Laporan Aduan Pelamar Terkait Lowongan Ini
                            </h2>
                            <p class="text-xs text-rose-700/80 dark:text-rose-400">Tinjau laporan masuk dari pencari kerja terkait indikasi pelanggaran.</p>
                        </div>
                    </div>

                    <div class="divide-y divide-slate-100 dark:divide-slate-800">
                        @foreach($job->reports as $report)
                            <div class="p-5 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                                <div class="min-w-0">
                                    <div class="flex items-center gap-2">
                                        <span class="font-bold text-xs text-slate-900 dark:text-white">{{ $report->reason }}</span>
                                        <span class="portal-badge {{ match($report->status) {'action_taken' => 'bg-rose-50 text-rose-700', 'dismissed' => 'bg-slate-100 text-slate-600', default => 'bg-amber-50 text-amber-800'} }} text-[10px]">
                                            {{ ucfirst($report->status) }}
                                        </span>
                                    </div>
                                    <p class="mt-1 text-xs text-slate-600 dark:text-slate-300 line-clamp-2">"{{ $report->details }}"</p>
                                    <span class="text-[11px] text-slate-400 mt-1 block">
                                        Pelapor: <strong>{{ $report->reporter->name }}</strong> · {{ $report->created_at->diffForHumans() }}
                                    </span>
                                </div>
                                <a href="{{ route('admin.reports.show', $report) }}" class="portal-button-secondary !py-1 !px-3 text-xs shrink-0">
                                    Tinjau Laporan
                                </a>
                            </div>
                        @endforeach
                    </div>
                </section>
            @endif

        </div>

        <!-- Right Column: Employer Info Card -->
        <aside class="space-y-6">
            <div class="portal-panel h-fit p-6">
                <div class="grid h-12 w-12 place-items-center rounded-2xl bg-brand-50 text-brand-700 dark:bg-brand-950 dark:text-brand-300">
                    <span class="material-symbols-outlined">storefront</span>
                </div>
                <h2 class="mt-5 text-lg font-bold text-slate-900 dark:text-white">Akun Mitra Penerbit</h2>
                
                <dl class="mt-5 space-y-4 text-sm">
                    <div>
                        <dt class="text-xs text-slate-400">Nama Usaha / Bisnis</dt>
                        <dd class="mt-1 font-bold text-slate-900 dark:text-white">{{ $job->employer->business_name ?: '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs text-slate-400">Penanggung Jawab</dt>
                        <dd class="mt-1 font-semibold">{{ $job->employer->name }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs text-slate-400">Email Resmi</dt>
                        <dd class="mt-1 break-all font-semibold">{{ $job->employer->email }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs text-slate-400">Nomor Telepon</dt>
                        <dd class="mt-1 font-semibold">{{ $job->employer->phone ?: 'Belum diisi' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs text-slate-400">Status Akun Mitra</dt>
                        <dd class="mt-1">
                            @if($job->employer->isBanned())
                                <span class="rounded bg-rose-50 px-2 py-0.5 text-xs font-bold text-rose-700 border border-rose-200">
                                    Dibekukan (Ban)
                                </span>
                            @else
                                <span class="rounded bg-emerald-50 px-2 py-0.5 text-xs font-bold text-emerald-700 border border-emerald-200">
                                    Aktif
                                </span>
                            @endif
                        </dd>
                    </div>
                </dl>

                <div class="mt-6 pt-6 border-t border-slate-100 dark:border-slate-800 space-y-2">
                    <a href="{{ route('admin.users.show', $job->employer) }}" class="portal-button-secondary w-full justify-center">
                        <span class="material-symbols-outlined text-[18px]">person</span>
                        Profil Lengkap Mitra
                    </a>
                </div>
            </div>
        </aside>
    </div>
@endsection

@push('modals')
    <!-- ================= MODAL TUTUP LOWONGAN ================= -->
    <div id="closeJobModal" class="fixed inset-0 z-[100] hidden bg-slate-950/60 backdrop-blur-sm p-4 overflow-y-auto flex items-center justify-center" onclick="if(event.target === this) closeCloseJobModal()">
        <div class="relative w-full max-w-lg rounded-3xl bg-white p-6 sm:p-7 shadow-2xl shadow-slate-950/20 dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 animate-modal-pop">
            <div class="flex items-start justify-between gap-3 pb-5 border-b border-slate-100 dark:border-slate-800">
                <div class="flex items-center gap-3.5">
                    <div class="w-11 h-11 rounded-2xl bg-amber-50 dark:bg-amber-950/50 text-amber-700 dark:text-amber-400 border border-amber-200/60 dark:border-amber-900/40 flex items-center justify-center shrink-0">
                        <span class="material-symbols-outlined text-[24px]">lock</span>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-slate-900 dark:text-white leading-tight" id="closeJobModalTitle">Tutup Lowongan Pekerjaan</h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Nonaktifkan penerimaan lamaran baru untuk lowongan ini.</p>
                    </div>
                </div>
                <button type="button" onclick="closeCloseJobModal()" class="w-8 h-8 rounded-xl text-slate-400 hover:text-slate-700 hover:bg-slate-100 dark:hover:text-white dark:hover:bg-slate-800 flex items-center justify-center transition">
                    <span class="material-symbols-outlined text-[20px]">close</span>
                </button>
            </div>

            <form id="closeJobForm" method="POST" class="mt-5 space-y-4">
                @csrf
                <div>
                    <label for="close_reason" class="portal-label text-xs uppercase tracking-wider text-slate-500 dark:text-slate-400">
                        Alasan Penutupan Lowongan <span class="text-rose-500">*</span>
                    </label>
                    <textarea id="close_reason" name="close_reason" rows="3" required placeholder="Jelaskan alasan penutupan sementara atau evaluasi kepatuhan etis..." class="w-full rounded-2xl border border-slate-200 bg-slate-50/70 p-3.5 text-sm text-slate-900 placeholder:text-slate-400 focus:bg-white focus:border-amber-500 focus:ring-4 focus:ring-amber-500/10 focus:outline-none transition dark:border-slate-700 dark:bg-slate-950/70 dark:text-slate-100 dark:focus:bg-slate-950"></textarea>
                    <p class="mt-1 text-[11px] text-slate-400">Alasan ini akan tercatat dalam log audit dan tampil pada detail lowongan.</p>
                </div>

                <div>
                    <label for="job_duration_type" class="portal-label text-xs uppercase tracking-wider text-slate-500 dark:text-slate-400">
                        Durasi Penutupan <span class="text-rose-500">*</span>
                    </label>
                    <select id="job_duration_type" name="duration_type" required onchange="handleJobDurationChange(this.value)" class="w-full rounded-2xl border border-slate-200 bg-slate-50/70 px-3.5 py-2.5 text-sm font-medium text-slate-900 focus:bg-white focus:border-amber-500 focus:ring-4 focus:ring-amber-500/10 focus:outline-none transition dark:border-slate-700 dark:bg-slate-950/70 dark:text-slate-100 dark:focus:bg-slate-950">
                        <option value="7_days">7 Hari (1 Minggu)</option>
                        <option value="14_days" selected>14 Hari (2 Minggu)</option>
                        <option value="30_days">30 Hari (1 Bulan)</option>
                        <option value="custom">Kustom Jumlah Hari...</option>
                        <option value="permanent">Permanen (Sampai dibuka manual)</option>
                    </select>
                </div>

                <div id="jobCustomDaysContainer" class="hidden">
                    <label for="job_custom_days" class="portal-label text-xs uppercase tracking-wider text-slate-500 dark:text-slate-400">
                        Jumlah Hari Kustom <span class="text-rose-500">*</span>
                    </label>
                    <input type="number" id="job_custom_days" name="custom_days" min="1" max="3650" placeholder="Contoh: 21" class="w-full rounded-2xl border border-slate-200 bg-slate-50/70 p-3 text-sm text-slate-900 focus:bg-white focus:border-amber-500 focus:ring-4 focus:ring-amber-500/10 focus:outline-none transition dark:border-slate-700 dark:bg-slate-950/70 dark:text-slate-100">
                </div>

                <div class="pt-4 flex items-center justify-end gap-2.5 border-t border-slate-100 dark:border-slate-800">
                    <button type="button" onclick="closeCloseJobModal()" class="inline-flex h-10 items-center justify-center rounded-xl border border-slate-200 bg-white px-4 text-xs sm:text-sm font-semibold text-slate-700 shadow-xs hover:bg-slate-50 hover:border-slate-300 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 transition">
                        Batal
                    </button>
                    <button type="submit" class="inline-flex h-10 items-center justify-center gap-1.5 rounded-xl bg-amber-600 px-5 text-xs sm:text-sm font-semibold text-white shadow-sm shadow-amber-600/25 hover:bg-amber-700 active:translate-y-px transition">
                        <span class="material-symbols-outlined text-[18px]">lock</span>
                        Tutup Lowongan Ini
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Konfirmasi Hapus Lowongan -->
    <div id="deleteJobModal" class="fixed inset-0 z-50 hidden bg-slate-950/60 backdrop-blur-sm p-4 overflow-y-auto flex items-center justify-center" onclick="if(event.target === this) closeDeleteJobModal()">
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
                        Anda akan menghapus permanen lowongan <strong class="text-slate-900 dark:text-white">{{ $job->title }}</strong>.
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

            <form method="POST" action="{{ route('admin.jobs.destroy', $job) }}" class="mt-6 flex items-center justify-end gap-2.5">
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

    function openDeleteJobModal() {
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
