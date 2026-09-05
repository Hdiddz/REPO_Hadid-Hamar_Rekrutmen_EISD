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

    @if($job->status === 'open')
        <button type="button" onclick="openComplianceActionModal()" class="portal-action-btn-warning !px-2.5 sm:!px-3 cursor-pointer" title="Tindak Kepatuhan atau Tutup Lowongan">
            <span class="material-symbols-outlined text-[17px]">gavel</span>
            <span>Tindak<span class="hidden sm:inline"> Kepatuhan</span></span>
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
    <!-- Warning Banner if Job has Admin Compliance Warning -->
    @if($job->hasAdminWarning())
        <div class="mb-6 rounded-2xl bg-amber-50 p-4 border border-amber-200 dark:bg-amber-950/40 dark:border-amber-900/50 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
            <div class="flex items-start gap-3">
                <span class="material-symbols-outlined text-amber-600 dark:text-amber-400 text-2xl shrink-0 mt-0.5">warning</span>
                <div>
                    <div class="flex items-center gap-2 flex-wrap">
                        <h3 class="font-bold text-sm text-amber-900 dark:text-amber-200">Catatan Peringatan Kepatuhan Aktif</h3>
                        <span class="portal-badge bg-amber-100 text-amber-800 dark:bg-amber-900/60 dark:text-amber-300 text-[11px]">
                            {{ $job->admin_warning_category_label }}
                        </span>
                    </div>
                    <p class="text-xs text-amber-800 dark:text-amber-300 mt-1">
                        <strong>Catatan Admin:</strong> "{{ $job->admin_warning_message }}"
                    </p>
                    <p class="text-[11px] text-amber-700 dark:text-amber-400 mt-0.5">
                        Diterbitkan pada {{ $job->admin_warned_at?->translatedFormat('d F Y, H:i') }} WIB &bull; Telah dinotifikasikan dan dikirimkan via obrolan resmi ke Mitra UMKM.
                    </p>
                </div>
            </div>
            <div class="flex items-center gap-2 shrink-0 self-end sm:self-center">
                <button type="button" onclick="openComplianceActionModal('warning_only')" class="portal-button-secondary !py-1.5 !px-3 text-xs !bg-white dark:!bg-slate-900 cursor-pointer">
                    Ubah Peringatan
                </button>
                <form action="{{ route('admin.jobs.dismissWarning', $job) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin mencabut catatan peringatan kepatuhan untuk lowongan ini?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="inline-flex items-center gap-1 py-1.5 px-3 text-xs font-semibold rounded-xl bg-emerald-600 text-white hover:bg-emerald-700 transition cursor-pointer">
                        <span class="material-symbols-outlined text-[15px]">check_circle</span>
                        <span>Cabut Peringatan</span>
                    </button>
                </form>
            </div>
        </div>
    @endif

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
                        <a href="{{ route('admin.reports.index', ['q' => $job->title, 'status' => 'all']) }}" class="inline-flex items-center gap-1 text-xs font-semibold text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 hover:underline transition" title="Lihat laporan masuk">
                            <span class="material-symbols-outlined text-[15px]">flag</span>
                            <span>{{ $job->reports_count }} laporan masuk</span>
                        </a>
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
    <!-- ================= MODAL TINDAK KEPATUHAN & PENGAWASAN LOWONGAN ================= -->
    <div id="complianceActionModal" class="fixed inset-0 z-[100] hidden bg-slate-950/60 backdrop-blur-sm p-4 overflow-y-auto flex items-center justify-center" onclick="if(event.target === this) closeComplianceActionModal()">
        <div class="relative w-full max-w-xl rounded-3xl bg-white p-6 sm:p-7 shadow-2xl shadow-slate-950/20 dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 animate-modal-pop">
            
            {{-- Modal Header --}}
            <div class="flex items-start gap-3.5 pb-4 border-b border-slate-100 dark:border-slate-800">
                <div class="w-11 h-11 rounded-2xl bg-amber-50 text-amber-600 dark:bg-amber-950/50 dark:text-amber-400 border border-amber-200 dark:border-amber-900/50 flex items-center justify-center shrink-0">
                    <span class="material-symbols-outlined text-[24px]">gavel</span>
                </div>
                <div class="flex-1 min-w-0">
                    <h3 class="text-base sm:text-lg font-bold text-slate-900 dark:text-white leading-snug">
                        Tindak Kepatuhan Lowongan
                    </h3>
                    <p class="mt-0.5 text-xs text-slate-500 dark:text-slate-400 leading-relaxed truncate">
                        {{ $job->title }} &bull; <span class="font-medium text-slate-700 dark:text-slate-300">{{ $job->employer->business_name ?: $job->employer->name }}</span>
                    </p>
                </div>
                <button type="button" onclick="closeComplianceActionModal()" class="w-8 h-8 rounded-xl text-slate-400 hover:text-slate-700 hover:bg-slate-100 dark:hover:text-white dark:hover:bg-slate-800 flex items-center justify-center transition shrink-0 cursor-pointer" aria-label="Tutup">
                    <span class="material-symbols-outlined text-[20px]">close</span>
                </button>
            </div>

            {{-- Mode Selector Cards --}}
            <div class="mt-4">
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-2">
                    Pilih Tindakan Pengawasan:
                </label>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-2">
                    <label id="modeCardWarning" class="relative flex flex-col p-3 rounded-2xl border cursor-pointer transition select-none bg-amber-50/70 border-amber-500 dark:bg-amber-950/40 dark:border-amber-600">
                        <input type="radio" name="compliance_action_type" value="warning_only" checked onchange="handleComplianceActionTypeChange(this.value)" class="sr-only">
                        <div class="flex items-center gap-1.5 text-xs font-bold text-slate-800 dark:text-slate-200">
                            <span class="material-symbols-outlined text-[16px] text-amber-600">warning</span>
                            <span>Peringatan</span>
                        </div>
                        <span class="text-[11px] text-slate-500 dark:text-slate-400 mt-1 leading-snug">
                            Tegur mitra untuk revisi. Lowongan tetap buka.
                        </span>
                    </label>

                    <label id="modeCardWarningClose" class="relative flex flex-col p-3 rounded-2xl border cursor-pointer transition select-none bg-slate-50/70 dark:bg-slate-850 border-slate-200 dark:border-slate-700">
                        <input type="radio" name="compliance_action_type" value="warning_and_close" onchange="handleComplianceActionTypeChange(this.value)" class="sr-only">
                        <div class="flex items-center gap-1.5 text-xs font-bold text-slate-800 dark:text-slate-200">
                            <span class="material-symbols-outlined text-[16px] text-amber-600">lock_clock</span>
                            <span>Peringatan &amp; Tutup</span>
                        </div>
                        <span class="text-[11px] text-slate-500 dark:text-slate-400 mt-1 leading-snug">
                            Tegur mitra dan nonaktifkan lowongan sementara.
                        </span>
                    </label>

                    <label id="modeCardClose" class="relative flex flex-col p-3 rounded-2xl border cursor-pointer transition select-none bg-slate-50/70 dark:bg-slate-850 border-slate-200 dark:border-slate-700">
                        <input type="radio" name="compliance_action_type" value="close_only" onchange="handleComplianceActionTypeChange(this.value)" class="sr-only">
                        <div class="flex items-center gap-1.5 text-xs font-bold text-slate-800 dark:text-slate-200">
                            <span class="material-symbols-outlined text-[16px] text-slate-600">lock</span>
                            <span>Tutup Saja</span>
                        </div>
                        <span class="text-[11px] text-slate-500 dark:text-slate-400 mt-1 leading-snug">
                            Tutup lowongan dengan alasan umum penutupan.
                        </span>
                    </label>
                </div>
            </div>

            {{-- Dynamic Form --}}
            <form id="complianceActionForm" method="POST" action="{{ route('admin.jobs.warn', $job) }}" class="mt-4 space-y-4">
                @csrf
                <input type="hidden" id="action_also_close_job" name="also_close_job" value="0">

                {{-- Group A: Warning Fields (shown for warning_only and warning_and_close) --}}
                <div id="complianceWarningFields" class="space-y-4">
                    <div>
                        <label for="action_warning_category" class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                            Kategori Pelanggaran Kepatuhan <span class="text-rose-500">*</span>
                        </label>
                        <select id="action_warning_category" name="warning_category" required class="w-full rounded-2xl border border-slate-200 bg-slate-50/70 px-3.5 py-2.5 text-xs font-medium text-slate-900 focus:bg-white focus:border-amber-500 focus:ring-4 focus:ring-amber-500/10 focus:outline-none transition dark:border-slate-700 dark:bg-slate-950/70 dark:text-slate-100 dark:focus:bg-slate-950">
                            <option value="salary_not_standard" {{ old('warning_category', $job->admin_warning_category) === 'salary_not_standard' ? 'selected' : '' }}>Upah Tidak Sesuai / Di Bawah Standar Kelayakan</option>
                            <option value="excessive_hours" {{ old('warning_category', $job->admin_warning_category) === 'excessive_hours' ? 'selected' : '' }}>Jam Kerja Melebihi Batas Etis (>8 Jam/Hari)</option>
                            <option value="misleading_info" {{ old('warning_category', $job->admin_warning_category) === 'misleading_info' ? 'selected' : '' }}>Informasi Lowongan Tidak Jelas / Menyesatkan</option>
                            <option value="unethical_conditions" {{ old('warning_category', $job->admin_warning_category) === 'unethical_conditions' ? 'selected' : '' }}>Persyaratan Kerja Tidak Wajar / Diskriminatif</option>
                            <option value="other" {{ old('warning_category', $job->admin_warning_category) === 'other' ? 'selected' : '' }}>Lainnya / Pelanggaran Standar Kepatuhan Lainnya</option>
                        </select>
                    </div>

                    <div>
                        <label for="action_warning_message" class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                            Catatan Peringatan &amp; Saran Perbaikan <span class="text-rose-500">*</span>
                        </label>
                        <textarea id="action_warning_message" name="warning_message" rows="3" required minlength="5" maxlength="2000" placeholder="Tuliskan bagian mana yang tidak sesuai standar dan arahan perbaikan untuk Mitra UMKM..." class="w-full rounded-2xl border border-slate-200 bg-slate-50/70 p-3.5 text-xs text-slate-900 placeholder:text-slate-400 focus:bg-white focus:border-amber-500 focus:ring-4 focus:ring-amber-500/10 focus:outline-none transition dark:border-slate-700 dark:bg-slate-950/70 dark:text-slate-100 dark:focus:bg-slate-950 leading-relaxed">{{ old('warning_message', $job->admin_warning_message) }}</textarea>
                        <p class="text-[11px] text-slate-400 mt-1">Pesan ini otomatis dikirimkan ke obrolan resmi dan notifikasi akun Mitra UMKM.</p>
                    </div>
                </div>

                {{-- Group B: Close Reason (shown ONLY for close_only) --}}
                <div id="complianceCloseReasonField" class="hidden space-y-1.5">
                    <label for="action_close_reason" class="block text-xs font-bold text-slate-700 dark:text-slate-300">
                        Alasan Penutupan Lowongan <span class="text-rose-500">*</span>
                    </label>
                    <textarea id="action_close_reason" name="close_reason" rows="3" maxlength="1000" placeholder="Jelaskan alasan penutupan lowongan kerja ini..." class="w-full rounded-2xl border border-slate-200 bg-slate-50/70 p-3.5 text-xs text-slate-900 placeholder:text-slate-400 focus:bg-white focus:border-amber-500 focus:ring-4 focus:ring-amber-500/10 focus:outline-none transition dark:border-slate-700 dark:bg-slate-950/70 dark:text-slate-100 dark:focus:bg-slate-950 leading-relaxed"></textarea>
                    <p class="text-[11px] text-slate-400">Alasan ini akan tercatat dalam log audit dan dikirimkan ke mitra.</p>
                </div>

                {{-- Group C: Duration Fields (shown for warning_and_close and close_only) --}}
                <div id="complianceDurationFields" class="hidden space-y-3 pt-3 border-t border-slate-100 dark:border-slate-800">
                    <div>
                        <label for="action_duration_type" class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                            Durasi Penutupan Lowongan <span class="text-rose-500">*</span>
                        </label>
                        <select id="action_duration_type" name="duration_type" onchange="handleActionDurationChange(this.value)" class="w-full rounded-2xl border border-slate-200 bg-slate-50/70 px-3.5 py-2.5 text-xs font-medium text-slate-900 focus:bg-white focus:border-amber-500 focus:ring-4 focus:ring-amber-500/10 focus:outline-none transition dark:border-slate-700 dark:bg-slate-950/70 dark:text-slate-100 dark:focus:bg-slate-950">
                            <option value="7_days">7 Hari (1 Minggu)</option>
                            <option value="14_days" selected>14 Hari (2 Minggu)</option>
                            <option value="30_days">30 Hari (1 Bulan)</option>
                            <option value="custom">Kustom Jumlah Hari...</option>
                            <option value="permanent">Permanen (Sampai dibuka manual)</option>
                        </select>
                    </div>

                    <div id="actionCustomDaysContainer" class="hidden">
                        <label for="action_custom_days" class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">
                            Jumlah Hari Kustom <span class="text-rose-500">*</span>
                        </label>
                        <input type="number" id="action_custom_days" name="custom_days" min="1" max="3650" placeholder="Contoh: 21" class="w-full rounded-2xl border border-slate-200 bg-slate-50/70 p-2.5 text-xs text-slate-900 focus:bg-white focus:border-amber-500 focus:ring-4 focus:ring-amber-500/10 focus:outline-none transition dark:border-slate-700 dark:bg-slate-950/70 dark:text-slate-100">
                    </div>
                </div>

                {{-- Modal Footer Buttons --}}
                <div class="pt-4 flex items-center justify-end gap-2.5 border-t border-slate-100 dark:border-slate-800">
                    <button type="button" onclick="closeComplianceActionModal()" class="inline-flex h-10 items-center justify-center rounded-xl border border-slate-200 bg-white px-4 text-xs sm:text-sm font-semibold text-slate-700 shadow-xs hover:bg-slate-50 hover:border-slate-300 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 transition cursor-pointer">
                        Batal
                    </button>
                    <button type="submit" id="complianceActionSubmitBtn" class="inline-flex h-10 items-center justify-center gap-1.5 rounded-xl bg-amber-600 px-5 text-xs sm:text-sm font-semibold text-white shadow-sm shadow-amber-600/25 hover:bg-amber-700 active:translate-y-px transition cursor-pointer">
                        <span class="material-symbols-outlined text-[18px]">send</span>
                        <span id="complianceActionSubmitBtnText">Kirimkan Peringatan</span>
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

    const complianceWarnUrl = "{{ route('admin.jobs.warn', $job) }}";
    const complianceCloseUrl = "{{ route('admin.jobs.close', $job) }}";

    function openComplianceActionModal(initialMode = 'warning_only') {
        const modal = document.getElementById('complianceActionModal');
        if (modal) {
            const radio = document.querySelector(`input[name="compliance_action_type"][value="${initialMode}"]`);
            if (radio) {
                radio.checked = true;
            }
            handleComplianceActionTypeChange(initialMode);
            modal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }
    }

    function closeComplianceActionModal() {
        const modal = document.getElementById('complianceActionModal');
        if (modal) {
            modal.classList.add('hidden');
            document.body.style.overflow = '';
        }
    }

    function handleComplianceActionTypeChange(mode) {
        const form = document.getElementById('complianceActionForm');
        const warningFields = document.getElementById('complianceWarningFields');
        const closeReasonField = document.getElementById('complianceCloseReasonField');
        const durationFields = document.getElementById('complianceDurationFields');
        const alsoCloseInput = document.getElementById('action_also_close_job');
        const submitBtnText = document.getElementById('complianceActionSubmitBtnText');
        const warningCategory = document.getElementById('action_warning_category');
        const warningMessage = document.getElementById('action_warning_message');
        const closeReason = document.getElementById('action_close_reason');

        const cardWarning = document.getElementById('modeCardWarning');
        const cardWarningClose = document.getElementById('modeCardWarningClose');
        const cardClose = document.getElementById('modeCardClose');

        const activeClasses = ['bg-amber-50/70', 'border-amber-500', 'dark:bg-amber-950/40', 'dark:border-amber-600'];
        const inactiveClasses = ['bg-slate-50/70', 'border-slate-200', 'dark:bg-slate-850', 'dark:border-slate-700'];

        [cardWarning, cardWarningClose, cardClose].forEach(card => {
            if (card) {
                card.classList.remove(...activeClasses);
                card.classList.add(...inactiveClasses);
            }
        });

        if (mode === 'warning_only') {
            if (cardWarning) {
                cardWarning.classList.remove(...inactiveClasses);
                cardWarning.classList.add(...activeClasses);
            }
            form.action = complianceWarnUrl;
            alsoCloseInput.value = '0';
            warningFields.classList.remove('hidden');
            closeReasonField.classList.add('hidden');
            durationFields.classList.add('hidden');
            submitBtnText.textContent = 'Kirimkan Peringatan';
            warningCategory.required = true;
            warningMessage.required = true;
            closeReason.required = false;
        } else if (mode === 'warning_and_close') {
            if (cardWarningClose) {
                cardWarningClose.classList.remove(...inactiveClasses);
                cardWarningClose.classList.add(...activeClasses);
            }
            form.action = complianceWarnUrl;
            alsoCloseInput.value = '1';
            warningFields.classList.remove('hidden');
            closeReasonField.classList.add('hidden');
            durationFields.classList.remove('hidden');
            submitBtnText.textContent = 'Kirim Peringatan & Tutup';
            warningCategory.required = true;
            warningMessage.required = true;
            closeReason.required = false;
        } else if (mode === 'close_only') {
            if (cardClose) {
                cardClose.classList.remove(...inactiveClasses);
                cardClose.classList.add(...activeClasses);
            }
            form.action = complianceCloseUrl;
            alsoCloseInput.value = '0';
            warningFields.classList.add('hidden');
            closeReasonField.classList.remove('hidden');
            durationFields.classList.remove('hidden');
            submitBtnText.textContent = 'Tutup Lowongan Ini';
            warningCategory.required = false;
            warningMessage.required = false;
            closeReason.required = true;
        }
    }

    function handleActionDurationChange(val) {
        const container = document.getElementById('actionCustomDaysContainer');
        const input = document.getElementById('action_custom_days');
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

    // Backwards-compatible aliases
    function openWarnJobModal() {
        openComplianceActionModal('warning_only');
    }
    function closeWarnJobModal() {
        closeComplianceActionModal();
    }
    function openCloseJobModal(jobId, jobTitle) {
        openComplianceActionModal('close_only');
    }
    function closeCloseJobModal() {
        closeComplianceActionModal();
    }

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            closeComplianceActionModal();
            closeDeleteJobModal();
            closeApplicantProfileModal();
        }
    });
</script>
@endpush
