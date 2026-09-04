@extends('layouts.admin')

@section('title', $job->title.' | Admin KerjaLokal')
@section('portal_title', $job->title)
@section('portal_description', 'Detail lowongan, status kepatuhan etis, profil mitra penerbit, serta pantau seluruh pelamar dan status seleksinya.')

@section('portal_actions')
    <a href="{{ $returnUrl ?? route('admin.jobs.index') }}" 
       onclick="if (window.history.length > 1 && document.referrer && document.referrer.includes(window.location.host) && !document.referrer.includes(window.location.pathname)) { history.back(); return false; }" 
       class="portal-action-btn !px-2.5 sm:!px-3" 
       title="Kembali ke halaman sebelumnya" 
       aria-label="Kembali">
        <span class="material-symbols-outlined text-[17px]">arrow_back</span>
        <span class="hidden sm:inline">Kembali</span>
    </a>

    <a href="{{ route('admin.jobs.edit', ['job' => $job, 'return_to' => url()->full()]) }}" class="portal-action-btn !px-2.5 sm:!px-3" title="Edit Lowongan">
        <span class="material-symbols-outlined text-[17px]">edit</span>
        <span>Edit<span class="hidden sm:inline"> Lowongan</span></span>
    </a>

    @if($job->status === 'open')
        <button type="button" data-job-id="{{ $job->id }}" data-job-title="{{ $job->title }}" onclick="openCloseJobModal(this.dataset.jobId, this.dataset.jobTitle)" class="portal-action-btn-warning !px-2.5 sm:!px-3" title="Tutup Lowongan">
            <span class="material-symbols-outlined text-[17px]">lock</span>
            <span>Tutup<span class="hidden sm:inline"> Lowongan</span></span>
        </button>
    @else
        <form action="{{ route('admin.jobs.reopen', $job) }}" method="POST">
            @csrf
            <button type="submit" class="portal-action-btn-success cursor-pointer !px-2.5 sm:!px-3" title="Buka Kembali Lowongan">
                <span class="material-symbols-outlined text-[17px]">lock_open</span>
                <span>Buka<span class="hidden sm:inline"> Kembali</span></span>
            </button>
        </form>
    @endif

    <button type="button" onclick="openDeleteJobModal()" class="portal-action-btn-danger cursor-pointer !px-2.5 sm:!px-3" title="Hapus Lowongan" aria-label="Hapus Lowongan">
        <span class="material-symbols-outlined text-[17px]">delete</span>
        <span class="hidden sm:inline">Hapus</span>
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

                @if($job->cover_image || $job->workplacePhotos->isNotEmpty())
                    <div class="mt-6 border-t border-slate-100 pt-6 dark:border-slate-800">
                        <h2 class="font-bold text-slate-900 dark:text-white text-sm mb-3 flex items-center gap-2">
                            <span class="material-symbols-outlined text-[18px] text-brand-600">photo_library</span>
                            Foto Lowongan &amp; Lingkungan Kerja
                        </h2>
                        
                        @if($job->cover_image)
                            <div class="mb-4">
                                <span class="text-xs font-semibold text-slate-400 block mb-1.5">Foto Sampul Utama (Cover)</span>
                                <div class="relative max-w-md aspect-video rounded-xl overflow-hidden border border-slate-200 dark:border-slate-800 bg-slate-900">
                                    <img src="{{ $job->cover_image_url }}" alt="Cover {{ $job->title }}" class="h-full w-full object-cover">
                                </div>
                            </div>
                        @endif

                        @if($job->workplacePhotos->isNotEmpty())
                            <div>
                                <span class="text-xs font-semibold text-slate-400 block mb-1.5">Foto Lingkungan Kerja ({{ $job->workplacePhotos->count() }} foto)</span>
                                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3">
                                    @foreach($job->workplacePhotos as $idx => $photo)
                                        <div class="group relative aspect-video rounded-xl overflow-hidden border border-slate-200 dark:border-slate-800 bg-slate-900">
                                            <img src="{{ $photo->photo_url }}" alt="Foto #{{ $idx + 1 }}" class="h-full w-full object-cover group-hover:scale-105 transition">
                                            @if($photo->caption)
                                                <div class="absolute bottom-0 inset-x-0 bg-black/70 p-1.5 text-[10px] text-white truncate" title="{{ $photo->caption }}">
                                                    {{ $photo->caption }}
                                                </div>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                @endif

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
                <div class="portal-panel-header bg-indigo-50/70 dark:bg-indigo-950/30 border-b border-indigo-100 dark:border-indigo-900/40">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl bg-indigo-100 text-indigo-700 dark:bg-indigo-900/60 dark:text-indigo-300 flex items-center justify-center shrink-0">
                            <span class="material-symbols-outlined text-[19px]">groups</span>
                        </div>
                        <div>
                            <h2 class="font-bold text-slate-900 dark:text-white text-base">Pelamar Masuk</h2>
                            <p class="text-xs text-indigo-950/60 dark:text-indigo-300/70">Total {{ $job->applications_count }} pelamar terdaftar pada lowongan ini.</p>
                        </div>
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

                            <div class="flex items-center gap-2 shrink-0 flex-wrap">
                                <button type="button"
                                        onclick="openApplicantProfileModal({{ json_encode([
                                            'id' => $application->user->id,
                                            'name' => $application->user->name,
                                            'username' => $application->user->username ?? '-',
                                            'email' => $application->user->email,
                                            'phone' => $application->user->phone ?? 'Belum dicantumkan',
                                            'avatar_url' => $application->user->avatar ? asset('storage/'.$application->user->avatar) : null,
                                            'avatar_initials' => strtoupper(substr($application->user->name, 0, 2)),
                                            'is_banned' => $application->user->isBanned(),
                                            'ban_status' => $application->user->ban_status_text,
                                            'ban_reason' => $application->user->ban_reason,
                                            'registered_at' => $application->user->created_at->translatedFormat('d F Y, H:i') . ' WIB',
                                            'applied_at' => $application->created_at->translatedFormat('d M Y, H:i') . ' WIB',
                                            'status_label' => match($application->status) {'accepted' => 'Disetujui / Diterima', 'interview' => 'Wawancara', 'rejected' => 'Ditolak', 'reviewed' => 'Ditinjau Mitra', 'resigned' => 'Telah Resign', default => 'Belum Ditinjau (Pending)'},
                                            'status_color' => match($application->status) {'accepted' => 'emerald', 'interview' => 'indigo', 'rejected' => 'rose', 'reviewed' => 'blue', 'resigned' => 'slate', default => 'amber'},
                                            'note' => $application->note,
                                            'profile_url' => route('admin.users.show', ['user' => $application->user, 'return_to' => request()->fullUrl()]),
                                            'resume_preview_url' => $application->resume_file ? route('admin.applications.resume.preview', $application) : null,
                                            'resume_download_url' => $application->resume_file ? route('admin.applications.resume', $application) : null,
                                        ]) }})"
                                        class="portal-button-secondary !py-1.5 !px-3 text-xs gap-1.5 shadow-xs whitespace-nowrap cursor-pointer inline-flex items-center"
                                        title="Lihat profil pencari kerja ini">
                                    <span class="material-symbols-outlined text-[16px]">person</span>
                                    <span>Lihat Profil</span>
                                </button>

                                @if($application->resume_file)
                                    <button type="button" data-preview-url="{{ route('admin.applications.resume.preview', $application) }}" data-applicant-name="{{ $application->user->name }}" data-download-url="{{ route('admin.applications.resume', $application) }}" onclick="openPdfViewer(this.dataset.previewUrl, this.dataset.applicantName, this.dataset.downloadUrl)" class="portal-button-primary !py-1.5 !px-3 text-xs gap-1.5 cursor-pointer shadow-xs whitespace-nowrap" title="Pratinjau CV/Resume kandidat di browser">
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
                    <div class="portal-panel-header bg-rose-50/70 dark:bg-rose-950/30 border-b border-rose-100 dark:border-rose-900/40">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-xl bg-rose-100 text-rose-700 dark:bg-rose-900/60 dark:text-rose-300 flex items-center justify-center shrink-0">
                                <span class="material-symbols-outlined text-[19px]">flag</span>
                            </div>
                            <div>
                                <h2 class="font-bold text-rose-950 dark:text-rose-100 text-base">
                                    Laporan Aduan Pelamar Terkait Lowongan Ini
                                </h2>
                                <p class="text-xs text-rose-950/60 dark:text-rose-300/70">Tinjau laporan masuk dari pencari kerja terkait indikasi pelanggaran.</p>
                            </div>
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

    <!-- ================= MODAL PROFIL PENCARI KERJA ================= -->
    <div id="applicantProfileModal" class="fixed inset-0 z-[110] hidden bg-slate-950/60 backdrop-blur-xs p-3 sm:p-4 overflow-y-auto flex items-center justify-center" onclick="if(event.target === this) closeApplicantProfileModal()">
        <div class="relative w-full max-w-lg rounded-3xl bg-white p-5 sm:p-7 shadow-2xl dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 animate-modal-pop overflow-hidden max-h-[92dvh] flex flex-col my-auto">
            {{-- Modal Header --}}
            <div class="flex items-start justify-between gap-3 border-b border-slate-100 pb-4 dark:border-slate-800 shrink-0">
                <div class="flex items-center gap-3 min-w-0 flex-1">
                    <div id="modalApplicantAvatarBox" class="w-12 h-12 rounded-2xl bg-brand-50 text-brand-700 dark:bg-brand-950 dark:text-brand-300 font-bold text-lg flex items-center justify-center shrink-0 border border-brand-200/60 dark:border-brand-900/40 overflow-hidden">
                        <span id="modalApplicantInitials">--</span>
                    </div>
                    <div class="min-w-0 flex-1">
                        <div class="flex items-center gap-2 flex-wrap">
                            <h3 id="modalApplicantName" class="text-base font-bold text-slate-900 dark:text-white truncate">Nama Pelamar</h3>
                            <span class="portal-badge bg-emerald-50 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300 text-[10px]">Pencari Kerja</span>
                        </div>
                        <p id="modalApplicantUsername" class="text-xs font-mono font-semibold text-brand-700 dark:text-brand-300 mt-0.5">@username</p>
                    </div>
                </div>
                <button type="button" onclick="closeApplicantProfileModal()" class="w-8 h-8 rounded-xl text-slate-400 hover:text-slate-700 hover:bg-slate-100 dark:hover:text-white dark:hover:bg-slate-800 flex items-center justify-center transition shrink-0 cursor-pointer" aria-label="Tutup">
                    <span class="material-symbols-outlined text-[20px]">close</span>
                </button>
            </div>

            {{-- Modal Body --}}
            <div class="py-4 overflow-y-auto flex-1 space-y-4">
                {{-- Ban Status Alert if Banned --}}
                <div id="modalApplicantBanBox" class="hidden rounded-2xl bg-rose-50 p-3.5 border border-rose-200 dark:bg-rose-950/40 dark:border-rose-900/50">
                    <div class="flex items-center gap-2 text-rose-700 dark:text-rose-300 font-bold text-xs">
                        <span class="material-symbols-outlined text-[16px]">block</span>
                        <span>Akun Sedang Dibekukan (Ban)</span>
                    </div>
                    <p id="modalApplicantBanReason" class="text-[11px] text-rose-800 dark:text-rose-200 mt-1 italic"></p>
                </div>

                {{-- Status Seleksi Lowongan Ini --}}
                <div class="rounded-2xl bg-slate-50 dark:bg-slate-850/60 p-3.5 border border-slate-200/80 dark:border-slate-800 space-y-2">
                    <div class="flex items-center justify-between gap-2">
                        <span class="text-xs text-slate-500 dark:text-slate-400">Status Seleksi di Lowongan Ini:</span>
                        <span id="modalApplicantStatusBadge" class="portal-badge text-[11px] font-bold">Status</span>
                    </div>
                    <div class="flex items-center justify-between gap-2 text-xs">
                        <span class="text-slate-500 dark:text-slate-400">Tanggal Melamar:</span>
                        <span id="modalApplicantAppliedAt" class="font-semibold text-slate-800 dark:text-slate-200"></span>
                    </div>
                    <div id="modalApplicantNoteBox" class="hidden pt-2 border-t border-slate-200/60 dark:border-slate-700/60">
                        <span class="text-[11px] font-bold text-slate-500 block mb-1">Catatan Pengantar Lamaran:</span>
                        <p id="modalApplicantNote" class="text-xs text-slate-700 dark:text-slate-300 italic bg-white dark:bg-slate-900 p-2.5 rounded-xl border border-slate-200/60 dark:border-slate-800 leading-relaxed"></p>
                    </div>
                </div>

                {{-- Data Profil & Kontak --}}
                <div class="rounded-2xl border border-slate-100 dark:border-slate-800 p-3.5 space-y-3 text-xs">
                    <div class="flex items-center justify-between gap-2">
                        <span class="text-slate-500 dark:text-slate-400 flex items-center gap-1.5">
                            <span class="material-symbols-outlined text-[15px] text-slate-400">mail</span>
                            Email
                        </span>
                        <span id="modalApplicantEmail" class="font-semibold text-slate-800 dark:text-slate-200 break-all text-right"></span>
                    </div>
                    <div class="flex items-center justify-between gap-2">
                        <span class="text-slate-500 dark:text-slate-400 flex items-center gap-1.5">
                            <span class="material-symbols-outlined text-[15px] text-slate-400">call</span>
                            No. Telepon / WhatsApp
                        </span>
                        <span id="modalApplicantPhone" class="font-semibold text-slate-800 dark:text-slate-200"></span>
                    </div>
                    <div class="flex items-center justify-between gap-2">
                        <span class="text-slate-500 dark:text-slate-400 flex items-center gap-1.5">
                            <span class="material-symbols-outlined text-[15px] text-slate-400">calendar_today</span>
                            Terdaftar Sejak
                        </span>
                        <span id="modalApplicantRegisteredAt" class="font-semibold text-slate-800 dark:text-slate-200"></span>
                    </div>
                </div>
            </div>

            {{-- Modal Footer Actions --}}
            <div class="flex flex-wrap items-center justify-between gap-2 border-t border-slate-100 pt-4 dark:border-slate-800 shrink-0">
                <div class="flex items-center gap-2">
                    <div id="modalApplicantResumeBtnBox" class="hidden">
                        <button type="button" id="modalApplicantResumeBtn" class="portal-button-secondary !py-2 !px-3 text-xs gap-1.5 cursor-pointer">
                            <span class="material-symbols-outlined text-[16px]">visibility</span>
                            <span>Resume PDF</span>
                        </button>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <button type="button" onclick="closeApplicantProfileModal()" class="portal-button-secondary !py-2 !px-3 text-xs cursor-pointer">
                        Tutup
                    </button>
                    <a id="modalApplicantFullProfileLink" href="#" class="portal-button-primary !py-2 !px-3.5 text-xs gap-1.5 inline-flex items-center cursor-pointer" title="Buka detail lengkap profil & kelola akun pengguna">
                        <span class="material-symbols-outlined text-[16px]">open_in_new</span>
                        <span>Buka Halaman Akun Penuh</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
@endpush

@push('scripts')
<script>
    function openApplicantProfileModal(data) {
        document.getElementById('modalApplicantName').textContent = data.name || 'Nama Pelamar';
        document.getElementById('modalApplicantUsername').textContent = `@${data.username}`;
        document.getElementById('modalApplicantEmail').textContent = data.email || '-';
        document.getElementById('modalApplicantPhone').textContent = data.phone || 'Belum dicantumkan';
        document.getElementById('modalApplicantRegisteredAt').textContent = data.registered_at || '-';
        document.getElementById('modalApplicantAppliedAt').textContent = data.applied_at || '-';

        // Avatar
        const avatarBox = document.getElementById('modalApplicantAvatarBox');
        if (data.avatar_url) {
            avatarBox.innerHTML = `<img src="${data.avatar_url}" alt="${data.name}" class="w-full h-full object-cover">`;
        } else {
            avatarBox.innerHTML = `<span>${data.avatar_initials || '--'}</span>`;
        }

        // Ban Status
        const banBox = document.getElementById('modalApplicantBanBox');
        const banReason = document.getElementById('modalApplicantBanReason');
        if (data.is_banned) {
            banBox.classList.remove('hidden');
            banReason.textContent = data.ban_reason ? `Alasan: "${data.ban_reason}"` : `Status: ${data.ban_status}`;
        } else {
            banBox.classList.add('hidden');
        }

        // Application Status Badge
        const statusBadge = document.getElementById('modalApplicantStatusBadge');
        const colorClasses = {
            emerald: 'bg-emerald-50 text-emerald-800 border-emerald-200 dark:bg-emerald-950/60 dark:text-emerald-300 dark:border-emerald-800',
            indigo: 'bg-indigo-50 text-indigo-800 border-indigo-200 dark:bg-indigo-950/60 dark:text-indigo-300 dark:border-indigo-800',
            rose: 'bg-rose-50 text-rose-800 border-rose-200 dark:bg-rose-950/60 dark:text-rose-300 dark:border-rose-800',
            blue: 'bg-blue-50 text-blue-800 border-blue-200 dark:bg-blue-950/60 dark:text-blue-300 dark:border-blue-800',
            slate: 'bg-slate-100 text-slate-800 border-slate-300 dark:bg-slate-800 dark:text-slate-300 dark:border-slate-700',
            amber: 'bg-amber-50 text-amber-800 border-amber-200 dark:bg-amber-950/60 dark:text-amber-300 dark:border-amber-800',
        };
        statusBadge.className = `portal-badge border text-[11px] font-bold ${colorClasses[data.status_color] || colorClasses.amber}`;
        statusBadge.textContent = data.status_label || 'Pending';

        // Note
        const noteBox = document.getElementById('modalApplicantNoteBox');
        const noteEl = document.getElementById('modalApplicantNote');
        if (data.note) {
            noteBox.classList.remove('hidden');
            noteEl.textContent = data.note;
        } else {
            noteBox.classList.add('hidden');
        }

        // Resume PDF Button
        const resumeBox = document.getElementById('modalApplicantResumeBtnBox');
        const resumeBtn = document.getElementById('modalApplicantResumeBtn');
        if (data.resume_preview_url) {
            resumeBox.classList.remove('hidden');
            resumeBtn.onclick = () => {
                closeApplicantProfileModal();
                openPdfViewer(data.resume_preview_url, data.name, data.resume_download_url);
            };
        } else {
            resumeBox.classList.add('hidden');
        }

        // Full Profile Link
        document.getElementById('modalApplicantFullProfileLink').href = data.profile_url;

        // Open Modal
        const modal = document.getElementById('applicantProfileModal');
        if (modal) {
            modal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }
    }

    function closeApplicantProfileModal() {
        const modal = document.getElementById('applicantProfileModal');
        if (modal) {
            modal.classList.add('hidden');
            document.body.style.overflow = '';
        }
    }

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
            closeApplicantProfileModal();
        }
    });
</script>
@endpush
