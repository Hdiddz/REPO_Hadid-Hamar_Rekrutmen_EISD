@extends('layouts.app')

@section('title', 'Riwayat Lamaran | KerjaLokal')

@section('content')
    <div class="mb-5 sm:mb-6" data-reveal>
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div class="min-w-0">
                <p class="text-[11px] sm:text-xs font-bold uppercase tracking-[0.18em] text-coral-600">Proses seleksi</p>
                <h1 class="mt-0.5 text-xl sm:text-3xl font-bold tracking-tight text-slate-950 dark:text-white">Riwayat lamaran Anda.</h1>
                <p class="mt-1 text-xs sm:text-sm text-slate-500 dark:text-slate-400">Pantau status lamaran dan keputusan seleksi dari mitra UMKM.</p>
            </div>
            <div class="shrink-0">
                <a href="{{ route('jobs.index') }}" class="portal-button-primary !min-h-0 !py-2 !px-3.5 sm:!py-2.5 sm:!px-4 text-xs sm:text-sm inline-flex items-center gap-1.5 shadow-xs w-fit">
                    <span class="material-symbols-outlined text-[17px] sm:text-[18px]">travel_explore</span>
                    <span>Cari lowongan</span>
                </a>
            </div>
        </div>
    </div>

    {{-- Filter Status Lamaran (Teks ringkas & modern, scrollbar disembunyikan sepenuhnya) --}}
    <div class="mb-4 sm:mb-6 flex items-center gap-3.5 sm:gap-5 overflow-x-auto [scrollbar-width:none] [-ms-overflow-style:none] [&::-webkit-scrollbar]:hidden no-scrollbar pb-1 text-xs sm:text-sm font-medium -mx-4 px-4 sm:mx-0 sm:px-0 border-b border-slate-200 dark:border-slate-800" style="scrollbar-width: none; -ms-overflow-style: none;" data-reveal>
        <a href="{{ route('applications.index') }}" class="whitespace-nowrap pb-2 transition {{ !request('status') ? 'font-bold text-blue-600 dark:text-blue-400 border-b-2 border-blue-600 dark:border-blue-400' : 'text-slate-500 hover:text-blue-600 dark:text-slate-400 dark:hover:text-blue-400' }}">
            <span class="sm:hidden">Semua</span><span class="hidden sm:inline">Semua Lamaran</span>
            <span class="text-[11px] opacity-75">({{ $counts['all'] ?? 0 }})</span>
        </a>
        <a href="{{ route('applications.index', ['status' => 'pending']) }}" class="whitespace-nowrap pb-2 transition {{ request('status') === 'pending' ? 'font-bold text-blue-600 dark:text-blue-400 border-b-2 border-blue-600 dark:border-blue-400' : 'text-slate-500 hover:text-blue-600 dark:text-slate-400 dark:hover:text-blue-400' }}">
            <span class="sm:hidden">Menunggu</span><span class="hidden sm:inline">Menunggu Tinjauan</span>
            <span class="text-[11px] opacity-75">({{ $counts['pending'] ?? 0 }})</span>
        </a>
        <a href="{{ route('applications.index', ['status' => 'interview']) }}" class="whitespace-nowrap pb-2 transition {{ request('status') === 'interview' ? 'font-bold text-blue-600 dark:text-blue-400 border-b-2 border-blue-600 dark:border-blue-400' : 'text-slate-500 hover:text-blue-600 dark:text-slate-400 dark:hover:text-blue-400' }}">
            <span class="sm:hidden">Wawancara</span><span class="hidden sm:inline">Tahap Wawancara</span>
            <span class="text-[11px] opacity-75">({{ $counts['interview'] ?? 0 }})</span>
        </a>
        <a href="{{ route('applications.index', ['status' => 'accepted']) }}" class="whitespace-nowrap pb-2 transition {{ request('status') === 'accepted' ? 'font-bold text-blue-600 dark:text-blue-400 border-b-2 border-blue-600 dark:border-blue-400' : 'text-slate-500 hover:text-blue-600 dark:text-slate-400 dark:hover:text-blue-400' }}">
            <span>Diterima</span>
            <span class="text-[11px] opacity-75">({{ $counts['accepted'] ?? 0 }})</span>
        </a>
        <a href="{{ route('applications.index', ['status' => 'rejected']) }}" class="whitespace-nowrap pb-2 transition {{ request('status') === 'rejected' ? 'font-bold text-blue-600 dark:text-blue-400 border-b-2 border-blue-600 dark:border-blue-400' : 'text-slate-500 hover:text-blue-600 dark:text-slate-400 dark:hover:text-blue-400' }}">
            <span>Belum Sesuai</span>
            <span class="text-[11px] opacity-75">({{ $counts['rejected'] ?? 0 }})</span>
        </a>
        <a href="{{ route('applications.index', ['status' => 'resigned']) }}" class="whitespace-nowrap pb-2 transition {{ request('status') === 'resigned' ? 'font-bold text-blue-600 dark:text-blue-400 border-b-2 border-blue-600 dark:border-blue-400' : 'text-slate-500 hover:text-blue-600 dark:text-slate-400 dark:hover:text-blue-400' }}">
            <span class="sm:hidden">Resign</span><span class="hidden sm:inline">Telah Resign</span>
            <span class="text-[11px] opacity-75">({{ $counts['resigned'] ?? 0 }})</span>
        </a>
    </div>

    {{-- Banner Status Khusus --}}
    @if(request('status') === 'interview')
        <div class="mb-5 p-3.5 sm:p-4 rounded-2xl bg-indigo-50/80 dark:bg-indigo-950/40 border border-indigo-200 dark:border-indigo-800/80 text-indigo-950 dark:text-indigo-100 flex items-start gap-3 text-xs" data-reveal>
            <span class="material-symbols-outlined text-indigo-600 dark:text-indigo-400 text-[22px] sm:text-[26px] shrink-0 mt-0.5">event_available</span>
            <div>
                <strong class="block font-bold text-sm">Undangan Sesi Wawancara Aktif</strong>
                <span class="text-indigo-800 dark:text-indigo-300">Berikut adalah daftar lamaran di mana Anda telah diundang ke sesi wawancara oleh Mitra UMKM. Periksa jadwal, lokasi, dan konfirmasi kesiapan Anda melalui pesan mitra.</span>
            </div>
        </div>
    @elseif(request('status') === 'accepted')
        <div class="mb-5 p-3.5 sm:p-4 rounded-2xl bg-emerald-50/80 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800/80 text-emerald-950 dark:text-emerald-100 flex items-start gap-3 text-xs" data-reveal>
            <span class="material-symbols-outlined text-emerald-600 dark:text-emerald-400 text-[22px] sm:text-[26px] shrink-0 mt-0.5">celebration</span>
            <div>
                <strong class="block font-bold text-sm">Selamat! Daftar Lamaran Resmi Diterima Bekerja 🎉</strong>
                <span class="text-emerald-800 dark:text-emerald-300">Anda telah resmi diterima dan terdaftar sebagai tenaga kerja aktif mitra UMKM. Silakan tinjau tanggal masuk kerja dan koordinasikan persiapan Anda.</span>
            </div>
        </div>
    @elseif(request('status') === 'pending')
        <div class="mb-5 p-3.5 sm:p-4 rounded-2xl bg-amber-50/80 dark:bg-amber-950/40 border border-amber-200 dark:border-amber-800/80 text-amber-950 dark:text-amber-100 flex items-start gap-3 text-xs" data-reveal>
            <span class="material-symbols-outlined text-amber-600 dark:text-amber-400 text-[22px] sm:text-[26px] shrink-0 mt-0.5">hourglass_top</span>
            <div>
                <strong class="block font-bold text-sm">Lamaran Dalam Peninjauan Mitra</strong>
                <span class="text-amber-800 dark:text-amber-300">Lamaran Anda sedang dipelajari oleh tim rekruter Mitra UMKM. Anda akan mendapatkan notifikasi ketika terdapat pembaruan status seleksi.</span>
            </div>
        </div>
    @elseif(request('status') === 'rejected')
        <div class="mb-5 p-3.5 sm:p-4 rounded-2xl bg-rose-50/80 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-800/80 text-rose-950 dark:text-rose-100 flex items-start gap-3 text-xs" data-reveal>
            <span class="material-symbols-outlined text-rose-600 dark:text-rose-400 text-[22px] sm:text-[26px] shrink-0 mt-0.5">info</span>
            <div>
                <strong class="block font-bold text-sm">Proses Seleksi Selesai (Belum Sesuai)</strong>
                <span class="text-rose-800 dark:text-rose-300">Daftar lamaran yang belum berhasil lolos pada periode ini. Anda dapat mempelajari catatan evaluasi mitra dan mencoba melamar ke peluang lainnya.</span>
            </div>
        </div>
    @elseif(request('status') === 'resigned')
        <div class="mb-5 p-3.5 sm:p-4 rounded-2xl bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-slate-200 flex items-start gap-3 text-xs" data-reveal>
            <span class="material-symbols-outlined text-slate-600 dark:text-slate-400 text-[22px] sm:text-[26px] shrink-0 mt-0.5">person_cancel</span>
            <div>
                <strong class="block font-bold text-sm">Riwayat Pengunduran Diri (Resign)</strong>
                <span class="text-slate-600 dark:text-slate-400">Daftar pekerjaan yang telah diselesaikan dengan permohonan pengunduran diri yang telah resmi disetujui oleh mitra UMKM.</span>
            </div>
        </div>
    @endif

    <div class="space-y-4">
        @forelse($applications as $application)
            @php
                $isResigned = $application->status === 'resigned' || $application->resignation_status === 'approved';
                $statusLabel = match(true) {
                    $isResigned => 'Telah Resign',
                    $application->status === 'accepted' => 'Diterima Bekerja',
                    $application->status === 'rejected' => 'Belum Sesuai',
                    $application->status === 'interview' => 'Tahap Wawancara',
                    default => 'Menunggu Tinjauan',
                };
                $statusColor = match(true) {
                    $isResigned => 'text-slate-500 dark:text-slate-400',
                    $application->status === 'accepted' => 'text-emerald-600 dark:text-emerald-400',
                    $application->status === 'rejected' => 'text-rose-600 dark:text-rose-400',
                    $application->status === 'interview' => 'text-blue-600 dark:text-blue-400',
                    default => 'text-blue-600 dark:text-blue-400',
                };
            @endphp
            <article class="rounded-2xl border border-slate-200 bg-white p-4 sm:p-5 dark:border-slate-800 dark:bg-slate-900 transition-all hover:border-slate-300 dark:hover:border-slate-700 shadow-xs" data-reveal>
                {{-- Card Header: Category on Left, Concise Status on Right (Tidak bertabrakan di mobile) --}}
                <div class="flex items-center justify-between gap-3">
                    <span class="font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500 text-[11px] sm:text-xs truncate">
                        {{ $application->job->category->name }}
                    </span>
                    {{-- Status: Teks ringkas warna biru / status bersih tanpa border pill --}}
                    <span class="text-xs sm:text-sm font-bold {{ $statusColor }} shrink-0 text-right">
                        {{ $statusLabel }}
                    </span>
                </div>

                {{-- Job Title & Employer --}}
                <div class="mt-1.5">
                    <h2 class="text-base sm:text-lg font-bold text-slate-950 dark:text-white leading-snug">
                        <a href="{{ route('jobs.show', $application->job) }}" class="hover:text-blue-600 dark:hover:text-blue-400 transition-colors">
                            {{ $application->job->title }}
                        </a>
                    </h2>
                    <p class="mt-0.5 text-xs sm:text-sm text-slate-500 font-medium">
                        {{ $application->job->employer->business_name }} · {{ $application->job->location }}
                    </p>
                </div>

                {{-- Compact Meta Details (Clean text: Gaji, Jam, Tanggal Kirim, Resume) --}}
                <div class="mt-3 flex flex-wrap items-center gap-x-2.5 gap-y-1 text-xs text-slate-500 dark:text-slate-400">
                    <span class="font-medium text-slate-700 dark:text-slate-300">Rp {{ number_format($application->job->salary_amount, 0, ',', '.') }} / {{ $application->job->salary_type === 'monthly' ? 'bulan' : 'hari' }}</span>
                    <span class="text-slate-300 dark:text-slate-700" aria-hidden="true">&bull;</span>
                    <span>{{ $application->job->work_hours_per_day }} jam/hari</span>
                    <span class="text-slate-300 dark:text-slate-700" aria-hidden="true">&bull;</span>
                    <span>Dikirim {{ $application->created_at->translatedFormat('d M Y') }}</span>
                    @if($application->resume_file)
                        <span class="text-slate-300 dark:text-slate-700" aria-hidden="true">&bull;</span>
                        <button type="button" data-preview-url="{{ route('applications.resume.preview', $application) }}" data-applicant-name="{{ auth()->user()->name }}" onclick="openPdfViewer(this.dataset.previewUrl, this.dataset.applicantName)" class="font-semibold text-blue-600 hover:text-blue-700 hover:underline dark:text-blue-400 dark:hover:text-blue-300 inline-flex items-center gap-1 cursor-pointer" title="Lihat pratinjau resume PDF Anda">
                            <span>Resume PDF</span>
                            <span class="material-symbols-outlined text-[13px]">visibility</span>
                        </button>
                    @endif
                </div>

                @if($application->note)
                    <p class="mt-3 rounded-xl bg-slate-50 dark:bg-slate-950/60 p-2.5 text-xs text-slate-600 dark:text-slate-300 border border-slate-100 dark:border-slate-800">
                        <strong class="font-bold text-slate-700 dark:text-slate-200">Catatan lamaran Anda:</strong> {{ $application->note }}
                    </p>
                @endif

                {{-- Status Specific Context Box --}}
                @if($isResigned)
                    <div class="mt-3 rounded-xl bg-slate-50 dark:bg-slate-800/50 p-3 border border-slate-200 dark:border-slate-700 text-xs flex flex-col sm:flex-row sm:items-center justify-between gap-2.5">
                        <div class="flex items-start gap-2.5 text-slate-700 dark:text-slate-300">
                            <span class="material-symbols-outlined text-slate-500 text-[22px] shrink-0 mt-0.5">person_cancel</span>
                            <div>
                                <strong class="font-bold text-sm block text-slate-900 dark:text-white">Pengunduran Diri (Resign) Resmi Selesai</strong>
                                <span class="text-slate-600 dark:text-slate-400 text-[11px]">
                                    @if($application->resignation_date)
                                        Tanggal Efektif: <strong class="font-semibold">{{ $application->resignation_date->translatedFormat('l, d F Y') }}</strong> ·
                                    @endif
                                    Disetujui oleh {{ $application->job->employer->business_name }}
                                </span>
                            </div>
                        </div>
                        <div class="flex items-center gap-2 shrink-0 self-start sm:self-auto">
                            <a href="{{ route('chat.index', ['user' => $application->job->employer_id]) }}" class="font-semibold text-blue-600 hover:text-blue-700 hover:underline dark:text-blue-400 inline-flex items-center gap-1 text-xs" title="Buka riwayat obrolan dengan mitra">
                                <span>Riwayat Chat</span>
                                <span class="material-symbols-outlined text-[14px]">chat</span>
                            </a>
                        </div>
                    </div>
                @elseif($application->status === 'accepted')
                    <div class="mt-3 rounded-xl bg-emerald-50 dark:bg-emerald-950/30 p-3.5 border border-emerald-200 dark:border-emerald-800/60 text-xs flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                        <div class="flex items-start gap-2.5 text-emerald-950 dark:text-emerald-100">
                            <span class="material-symbols-outlined text-emerald-600 dark:text-emerald-400 text-[24px] shrink-0 mt-0.5">celebration</span>
                            <div>
                                <strong class="font-extrabold text-sm block">Selamat! Anda Resmi Diterima Bekerja 🎉</strong>
                                <div class="mt-0.5 flex flex-wrap items-center gap-x-2 text-[11px] text-emerald-800 dark:text-emerald-300">
                                    @if($application->start_date)
                                        <span>Tanggal Mulai Kerja: <strong class="font-bold text-emerald-950 dark:text-emerald-100">{{ $application->start_date->translatedFormat('l, d F Y') }}</strong></span>
                                        <span>·</span>
                                    @endif
                                    <span>Peserta Aktif Bekerja</span>
                                </div>
                                @if($application->acceptance_notes)
                                    <p class="mt-1 text-[11px] text-emerald-700 dark:text-emerald-300 italic">"{{ $application->acceptance_notes }}"</p>
                                @endif
                            </div>
                        </div>
                        <div class="flex items-center gap-3 shrink-0 self-start sm:self-auto flex-wrap">
                            @if($application->resignation_status === 'pending')
                                <span class="text-xs font-semibold text-amber-700 dark:text-amber-400 inline-flex items-center gap-1">
                                    <span class="material-symbols-outlined text-[13px]">pending</span>
                                    <span>Resign Sedang Ditinjau</span>
                                </span>
                            @else
                                <a href="{{ route('jobs.show', $application->job) }}" class="text-xs font-medium text-rose-600 hover:underline dark:text-rose-400 inline-flex items-center gap-1">
                                    <span>Ajukan Resign</span>
                                </a>
                            @endif
                            <a href="{{ route('chat.index', ['user' => $application->job->employer_id]) }}" class="text-xs font-semibold text-blue-600 hover:underline dark:text-blue-400 inline-flex items-center gap-1">
                                <span>Koordinasi Chat</span>
                                <span class="material-symbols-outlined text-[14px]">chat</span>
                            </a>
                        </div>
                    </div>
                @elseif($application->status === 'interview')
                    <div class="mt-3 rounded-xl bg-indigo-50/90 dark:bg-indigo-950/30 p-3.5 border border-indigo-200 dark:border-indigo-800/60 text-xs flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                        <div class="flex items-start gap-2.5 text-indigo-950 dark:text-indigo-100 min-w-0">
                            <span class="material-symbols-outlined text-indigo-600 dark:text-indigo-400 text-[24px] shrink-0 mt-0.5">event</span>
                            <div class="min-w-0">
                                <div class="flex items-center gap-1.5 flex-wrap">
                                    <strong class="font-extrabold text-sm block">Jadwal Sesi Wawancara 📅</strong>
                                    @if($application->interview_status === 'confirmed')
                                        <span class="inline-flex items-center gap-0.5 rounded-full bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300 px-2 py-0.2 text-[10px] font-bold">Jadwal Disetujui</span>
                                    @elseif($application->interview_status === 'reschedule_requested')
                                        <span class="inline-flex items-center gap-0.5 rounded-full bg-sky-100 text-sky-800 dark:bg-sky-950 dark:text-sky-300 px-2 py-0.2 text-[10px] font-bold">Diskusi Jadwal</span>
                                    @endif
                                </div>
                                <div class="mt-0.5 flex flex-wrap items-center gap-x-2 text-[11px] text-indigo-800 dark:text-indigo-300">
                                    @if($application->interview_date)
                                        <span>Hari & Tanggal: <strong class="font-bold">{{ $application->interview_date->translatedFormat('l, d F Y') }}</strong></span>
                                    @endif
                                    @if($application->interview_time)
                                        <span>· Pukul <strong class="font-bold">{{ $application->interview_time }} WIB</strong></span>
                                    @endif
                                    @if($application->interview_type)
                                        <span>· Metode: <strong class="font-bold">{{ $application->interview_type }}</strong></span>
                                    @endif
                                </div>
                                @if($application->interview_location)
                                    <p class="mt-0.5 text-[11px] text-indigo-700 dark:text-indigo-300 truncate">
                                        <strong>Lokasi / Tautan:</strong> {{ $application->interview_location }}
                                    </p>
                                @endif
                                @if($application->interview_notes)
                                    <p class="mt-0.5 text-[11px] text-indigo-700 dark:text-indigo-300 italic">"{{ $application->interview_notes }}"</p>
                                @endif
                            </div>
                        </div>
                        <div class="flex items-center gap-3 shrink-0 self-start sm:self-auto flex-wrap">
                            @if($application->interview_status !== 'confirmed' && $application->interview_status !== 'declined')
                                <form action="{{ route('applications.interview.response', $application) }}" method="POST" class="inline">
                                    @csrf
                                    <input type="hidden" name="action" value="confirmed">
                                    <button type="submit" class="text-xs font-semibold text-emerald-600 hover:underline dark:text-emerald-400 inline-flex items-center gap-1 cursor-pointer" title="Konfirmasi kesiapan hadir Anda pada jadwal wawancara ini">
                                        <span class="material-symbols-outlined text-[15px]">check_circle</span>
                                        <span>Setujui Jadwal</span>
                                    </button>
                                </form>
                            @endif
                            <a href="{{ route('chat.index', ['user' => $application->job->employer_id]) }}" class="text-xs font-semibold text-blue-600 hover:underline dark:text-blue-400 inline-flex items-center gap-1" title="Buka ruang obrolan untuk diskusi jadwal">
                                <span>Diskusi di Chat</span>
                                <span class="material-symbols-outlined text-[14px]">chat</span>
                            </a>
                        </div>
                    </div>
                @elseif($application->status === 'rejected')
                    <div class="mt-3 rounded-xl bg-rose-50/90 dark:bg-rose-950/30 p-3.5 border border-rose-200 dark:border-rose-800/60 text-xs flex flex-col sm:flex-row sm:items-start justify-between gap-3">
                        <div class="min-w-0 flex-1">
                            <strong class="text-rose-950 dark:text-white font-bold block text-sm">Pemberitahuan Seleksi Belum Berhasil</strong>
                            <p class="mt-0.5 text-rose-800 dark:text-rose-300 text-[11px]">
                                Mohon maaf, saat ini kualifikasi Anda belum sesuai dengan kebutuhan posisi ini.
                            </p>
                            @if($application->rejection_reason)
                                <p class="mt-1 text-[11px] text-rose-700 dark:text-rose-300">
                                    <strong>Keterangan mitra:</strong> {{ $application->rejection_reason }}
                                </p>
                            @endif
                            @if($application->rejection_notes)
                                <p class="mt-0.5 text-[11px] text-rose-700 dark:text-rose-300 italic">"{{ $application->rejection_notes }}"</p>
                            @endif

                            @if($application->canBeReappliedTomorrow())
                                <div class="mt-2 inline-flex items-center gap-1 rounded-lg border border-amber-300 bg-amber-100/90 px-2.5 py-0.5 text-[11px] font-bold text-amber-900 dark:border-amber-800 dark:bg-amber-950/70 dark:text-amber-300">
                                    <span class="material-symbols-outlined text-[13px]">schedule</span>
                                    <span>Dapat melamar kembali besok ({{ $application->reapplyAvailableAt()->translatedFormat('d M Y') }})</span>
                                </div>
                            @elseif($application->canBeReapplied() && $application->job->status === 'open')
                                <div class="mt-2 inline-flex items-center gap-1 rounded-lg border border-emerald-300 bg-emerald-100/90 px-2.5 py-0.5 text-[11px] font-bold text-emerald-900 dark:border-emerald-800 dark:bg-emerald-950/70 dark:text-emerald-300">
                                    <span class="material-symbols-outlined text-[13px]">published_with_changes</span>
                                    <span>Kesempatan terbuka: Anda sudah dapat mengajukan lamaran ulang!</span>
                                </div>
                            @endif
                        </div>
                        <div class="flex items-center gap-2 shrink-0 self-start sm:self-auto flex-wrap">
                            @if($application->canBeReapplied() && $application->job->status === 'open')
                                <a href="{{ route('jobs.show', $application->job) }}" class="text-xs font-semibold text-blue-600 hover:underline dark:text-blue-400 inline-flex items-center gap-1">
                                    <span>Ajukan Lamaran Ulang</span>
                                    <span class="material-symbols-outlined text-[14px]">replay</span>
                                </a>
                            @endif
                        </div>
                    </div>
                @endif

                {{-- Card Actions Footer (Teks ringkas) --}}
                <div class="mt-3.5 pt-3 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between gap-3 text-xs sm:text-sm">
                    {{-- Primary Action: Teks ringkas warna biru --}}
                    <a href="{{ route('jobs.show', $application->job) }}" class="font-semibold text-blue-600 hover:text-blue-700 hover:underline dark:text-blue-400 dark:hover:text-blue-300 inline-flex items-center gap-1" title="Lihat detail lengkap lowongan ini">
                        <span>Lihat Detail Lowongan</span>
                        <span class="material-symbols-outlined text-[15px]">arrow_forward</span>
                    </a>

                    {{-- Secondary Actions: Batalkan / Hapus Riwayat (Teks ringkas) --}}
                    <div class="flex items-center gap-3">
                        @if($application->status === 'pending')
                            <form id="cancelAppForm-{{ $application->id }}" action="{{ route('applications.destroy', $application) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="button" data-application-id="{{ $application->id }}" data-job-title="{{ $application->job->title }}" onclick="confirmCancelApp(this.dataset.applicationId, this.dataset.jobTitle)" class="font-medium text-rose-600 hover:text-rose-700 hover:underline dark:text-rose-400 inline-flex items-center gap-1 cursor-pointer text-xs" title="Batalkan lamaran pekerjaan ini">
                                    <span>Batalkan</span>
                                </button>
                            </form>
                        @else
                            <form id="hideAppForm-{{ $application->id }}" action="{{ route('applications.hide', $application) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="button" data-application-id="{{ $application->id }}" data-job-title="{{ $application->job->title }}" onclick="confirmHideApp(this.dataset.applicationId, this.dataset.jobTitle)" class="font-medium text-slate-400 hover:text-rose-600 hover:underline dark:text-slate-500 dark:hover:text-rose-400 inline-flex items-center gap-1 cursor-pointer text-xs" title="Hapus dari riwayat lamaran">
                                    <span>Hapus riwayat</span>
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </article>
        @empty
            <div class="rounded-3xl border border-dashed border-slate-300 bg-white p-14 text-center dark:border-slate-700 dark:bg-slate-900">
                <span class="material-symbols-outlined text-5xl text-slate-300">assignment</span>
                <h2 class="mt-4 text-lg font-bold text-slate-900 dark:text-white">Belum ada lamaran</h2>
                <p class="mt-2 text-sm text-slate-500">Pilih lowongan yang sesuai dan kirim resume PDF Anda.</p>
                <a href="{{ route('jobs.index') }}" class="inline-flex items-center gap-1 mt-4 text-xs sm:text-sm font-semibold text-blue-600 hover:text-blue-700 hover:underline dark:text-blue-400">
                    <span>Mulai mencari lowongan</span>
                    <span class="material-symbols-outlined text-[15px]">arrow_forward</span>
                </a>
            </div>
        @endforelse
    </div>
    <div class="mt-8">{{ $applications->links() }}</div>
@endsection

@push('scripts')
<script>
    async function confirmCancelApp(applicationId, jobTitle) {
        const confirmed = await window.showAppConfirm({
            title: 'Batalkan Lamaran Pekerjaan?',
            message: `Apakah Anda yakin ingin membatalkan lamaran untuk posisi "${jobTitle}"?\n\nBerkas resume Anda akan ditarik dari daftar peninjauan mitra UMKM dan lamaran ini akan dihapus dari riwayat Anda.`,
            confirmText: 'Ya, Batalkan Lamaran',
            cancelText: 'Kembali',
            type: 'danger',
            icon: 'cancel'
        });

        if (confirmed) {
            document.getElementById(`cancelAppForm-${applicationId}`)?.submit();
        }
    }

    async function confirmHideApp(applicationId, jobTitle) {
        const confirmed = await window.showAppConfirm({
            title: 'Hapus dari Riwayat Lamaran?',
            message: `Apakah Anda yakin ingin menghapus riwayat lamaran untuk posisi "${jobTitle}"?\n\nLamaran ini tidak akan ditampilkan lagi di daftar riwayat lamaran Anda.`,
            confirmText: 'Ya, Hapus dari Riwayat',
            cancelText: 'Kembali',
            type: 'danger',
            icon: 'delete'
        });

        if (confirmed) {
            document.getElementById(`hideAppForm-${applicationId}`)?.submit();
        }
    }
</script>
@endpush
