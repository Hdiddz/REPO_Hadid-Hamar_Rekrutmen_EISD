@extends('layouts.app')

@section('title', 'Riwayat Lamaran | KerjaLokal')

@section('content')
    <div class="mb-5 sm:mb-6" data-reveal>
        <p class="text-xs font-bold uppercase tracking-[0.18em] text-coral-600">Proses seleksi</p>
        <h1 class="mt-1 sm:mt-1.5 text-2xl sm:text-3xl font-bold tracking-tight text-slate-950 dark:text-white">Riwayat lamaran Anda.</h1>
        <p class="mt-1 text-xs sm:text-sm text-slate-500">Pantau status lamaran dan keputusan seleksi dari mitra UMKM.</p>
    </div>

    {{-- Segmented Switcher: Cari Lowongan vs Riwayat Lamaran --}}
    <div class="mb-5 sm:mb-6" data-reveal>
        <div class="inline-flex w-full sm:w-auto p-1.5 rounded-2xl bg-slate-100 dark:bg-slate-800/90 border border-slate-200 dark:border-slate-700/80 shadow-inner">
            <a href="{{ route('jobs.index') }}" class="flex-1 sm:flex-initial inline-flex items-center justify-center gap-2 px-4 sm:px-6 py-2.5 rounded-xl text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white font-bold text-xs sm:text-sm transition">
                <span class="material-symbols-outlined text-[18px]">search</span>
                <span>Cari Lowongan</span>
            </a>
            <a href="{{ route('applications.index') }}" class="flex-1 sm:flex-initial inline-flex items-center justify-center gap-2 px-4 sm:px-6 py-2.5 rounded-xl bg-white dark:bg-slate-900 text-brand-700 dark:text-brand-300 font-bold text-xs sm:text-sm shadow-xs transition">
                <span class="material-symbols-outlined text-[18px]">history_edu</span>
                <span>Riwayat Lamaran</span>
                @if(isset($counts['all']) && $counts['all'] > 0)
                    <span class="ml-1 rounded-full bg-brand-100 text-brand-800 dark:bg-brand-950 dark:text-brand-300 px-2 py-0.5 text-[10px] font-extrabold">{{ $counts['all'] }}</span>
                @endif
            </a>
        </div>
    </div>

    {{-- Filter Tabs Status Lamaran --}}
    <div class="mb-5 sm:mb-6 flex items-center gap-2 overflow-x-auto no-scrollbar pb-1.5 -mx-4 px-4 sm:mx-0 sm:px-0" data-reveal>
        <a href="{{ route('applications.index') }}" class="inline-flex items-center gap-1.5 rounded-xl px-3 sm:px-3.5 py-2 text-xs font-bold whitespace-nowrap shrink-0 transition {{ !request('status') ? 'bg-brand-700 text-white shadow-xs dark:bg-brand-500 dark:text-brand-950' : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-50 dark:bg-slate-900 dark:text-slate-300 dark:border-slate-800' }}">
            <span>Semua Lamaran</span>
            @if(isset($counts['all']))
                <span class="rounded-full bg-black/10 dark:bg-white/10 px-1.5 py-0.2 text-[10px]">{{ $counts['all'] }}</span>
            @endif
        </a>
        <a href="{{ route('applications.index', ['status' => 'pending']) }}" class="inline-flex items-center gap-1.5 rounded-xl px-3 sm:px-3.5 py-2 text-xs font-bold whitespace-nowrap shrink-0 transition {{ request('status') === 'pending' ? 'bg-amber-600 text-white shadow-xs' : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-50 dark:bg-slate-900 dark:text-slate-300 dark:border-slate-800' }}">
            <span class="material-symbols-outlined text-[16px]">hourglass_empty</span>
            <span>Menunggu Tinjauan</span>
            @if(isset($counts['pending']) && $counts['pending'] > 0)
                <span class="rounded-full bg-amber-100 text-amber-800 dark:bg-amber-950 dark:text-amber-300 px-1.5 py-0.2 text-[10px]">{{ $counts['pending'] }}</span>
            @endif
        </a>
        <a href="{{ route('applications.index', ['status' => 'interview']) }}" class="inline-flex items-center gap-1.5 rounded-xl px-3 sm:px-3.5 py-2 text-xs font-bold whitespace-nowrap shrink-0 transition {{ request('status') === 'interview' ? 'bg-indigo-600 text-white shadow-xs' : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-50 dark:bg-slate-900 dark:text-slate-300 dark:border-slate-800' }}">
            <span class="material-symbols-outlined text-[16px]">record_voice_over</span>
            <span>Tahap Wawancara</span>
            @if(isset($counts['interview']) && $counts['interview'] > 0)
                <span class="rounded-full bg-indigo-100 text-indigo-800 dark:bg-indigo-950 dark:text-indigo-300 px-1.5 py-0.2 text-[10px]">{{ $counts['interview'] }}</span>
            @endif
        </a>
        <a href="{{ route('applications.index', ['status' => 'accepted']) }}" class="inline-flex items-center gap-1.5 rounded-xl px-3 sm:px-3.5 py-2 text-xs font-bold whitespace-nowrap shrink-0 transition {{ request('status') === 'accepted' ? 'bg-emerald-600 text-white shadow-xs' : 'bg-emerald-50 text-emerald-800 border border-emerald-200 hover:bg-emerald-100 dark:bg-emerald-950/40 dark:text-emerald-300 dark:border-emerald-900/60' }}">
            <span class="material-symbols-outlined text-[16px]">how_to_reg</span>
            <span>Diterima Bekerja</span>
            @if(isset($counts['accepted']) && $counts['accepted'] > 0)
                <span class="rounded-full bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300 px-1.5 py-0.2 text-[10px]">{{ $counts['accepted'] }}</span>
            @endif
        </a>
        <a href="{{ route('applications.index', ['status' => 'rejected']) }}" class="inline-flex items-center gap-1.5 rounded-xl px-3 sm:px-3.5 py-2 text-xs font-bold whitespace-nowrap shrink-0 transition {{ request('status') === 'rejected' ? 'bg-rose-600 text-white shadow-xs' : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-50 dark:bg-slate-900 dark:text-slate-300 dark:border-slate-800' }}">
            <span class="material-symbols-outlined text-[16px]">cancel</span>
            <span>Belum Sesuai</span>
            @if(isset($counts['rejected']) && $counts['rejected'] > 0)
                <span class="rounded-full bg-rose-100 text-rose-800 dark:bg-rose-950 dark:text-rose-300 px-1.5 py-0.2 text-[10px]">{{ $counts['rejected'] }}</span>
            @endif
        </a>
        <a href="{{ route('applications.index', ['status' => 'resigned']) }}" class="inline-flex items-center gap-1.5 rounded-xl px-3 sm:px-3.5 py-2 text-xs font-bold whitespace-nowrap shrink-0 transition {{ request('status') === 'resigned' ? 'bg-slate-800 text-white shadow-xs' : 'bg-slate-100 text-slate-700 border border-slate-200 hover:bg-slate-200/80 dark:bg-slate-800 dark:text-slate-300 dark:border-slate-700' }}">
            <span class="material-symbols-outlined text-[16px]">person_cancel</span>
            <span>Telah Resign</span>
            @if(isset($counts['resigned']) && $counts['resigned'] > 0)
                <span class="rounded-full bg-slate-200 text-slate-800 dark:bg-slate-700 dark:text-slate-200 px-1.5 py-0.2 text-[10px] font-black">{{ $counts['resigned'] }}</span>
            @endif
        </a>
    </div>

    {{-- Banner Status Khusus --}}
    @if(request('status') === 'interview')
        <div class="mb-6 p-4 rounded-2xl bg-indigo-50/80 dark:bg-indigo-950/40 border border-indigo-200 dark:border-indigo-800/80 text-indigo-950 dark:text-indigo-100 flex items-center justify-between gap-3 text-xs" data-reveal>
            <div class="flex items-center gap-3">
                <span class="material-symbols-outlined text-indigo-600 dark:text-indigo-400 text-[26px]">event_available</span>
                <div>
                    <strong class="block font-bold text-sm">Undangan Sesi Wawancara Aktif</strong>
                    <span class="text-indigo-800 dark:text-indigo-300">Berikut adalah daftar lamaran di mana Anda telah diundang ke sesi wawancara oleh Mitra UMKM. Periksa jadwal, lokasi, dan konfirmasi kesiapan Anda melalui pesan mitra.</span>
                </div>
            </div>
        </div>
    @elseif(request('status') === 'accepted')
        <div class="mb-6 p-4 rounded-2xl bg-emerald-50/80 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800/80 text-emerald-950 dark:text-emerald-100 flex items-center justify-between gap-3 text-xs" data-reveal>
            <div class="flex items-center gap-3">
                <span class="material-symbols-outlined text-emerald-600 dark:text-emerald-400 text-[26px]">celebration</span>
                <div>
                    <strong class="block font-bold text-sm">Selamat! Daftar Lamaran Resmi Diterima Bekerja 🎉</strong>
                    <span class="text-emerald-800 dark:text-emerald-300">Anda telah resmi diterima dan terdaftar sebagai tenaga kerja aktif mitra UMKM. Silakan tinjau tanggal masuk kerja dan koordinasikan persiapan Anda.</span>
                </div>
            </div>
        </div>
    @elseif(request('status') === 'pending')
        <div class="mb-6 p-4 rounded-2xl bg-amber-50/80 dark:bg-amber-950/40 border border-amber-200 dark:border-amber-800/80 text-amber-950 dark:text-amber-100 flex items-center justify-between gap-3 text-xs" data-reveal>
            <div class="flex items-center gap-3">
                <span class="material-symbols-outlined text-amber-600 dark:text-amber-400 text-[26px]">hourglass_top</span>
                <div>
                    <strong class="block font-bold text-sm">Lamaran Dalam Peninjauan Mitra</strong>
                    <span class="text-amber-800 dark:text-amber-300">Lamaran Anda sedang dipelajari oleh tim rekruter Mitra UMKM. Anda akan mendapatkan notifikasi ketika terdapat pembaruan status seleksi.</span>
                </div>
            </div>
        </div>
    @elseif(request('status') === 'rejected')
        <div class="mb-6 p-4 rounded-2xl bg-rose-50/80 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-800/80 text-rose-950 dark:text-rose-100 flex items-center justify-between gap-3 text-xs" data-reveal>
            <div class="flex items-center gap-3">
                <span class="material-symbols-outlined text-rose-600 dark:text-rose-400 text-[26px]">info</span>
                <div>
                    <strong class="block font-bold text-sm">Proses Seleksi Selesai (Belum Sesuai)</strong>
                    <span class="text-rose-800 dark:text-rose-300">Daftar lamaran yang belum berhasil lolos pada periode ini. Anda dapat mempelajari catatan evaluasi mitra dan mencoba melamar ke peluang lainnya.</span>
                </div>
            </div>
        </div>
    @elseif(request('status') === 'resigned')
        <div class="mb-6 p-4 rounded-2xl bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-slate-200 flex items-center justify-between gap-3 text-xs" data-reveal>
            <div class="flex items-center gap-3">
                <span class="material-symbols-outlined text-slate-600 dark:text-slate-400 text-[26px]">person_cancel</span>
                <div>
                    <strong class="block font-bold text-sm">Riwayat Pengunduran Diri (Resign)</strong>
                    <span class="text-slate-600 dark:text-slate-400">Daftar pekerjaan yang telah diselesaikan dengan permohonan pengunduran diri yang telah resmi disetujui oleh mitra UMKM.</span>
                </div>
            </div>
        </div>
    @endif

    <div class="space-y-4">
        @forelse($applications as $application)
            @php
                $isResigned = $application->status === 'resigned' || $application->resignation_status === 'approved';
                $status = match(true) {
                    $isResigned => ['Telah Resign', 'bg-slate-100 text-slate-700 border-slate-300 dark:bg-slate-800 dark:text-slate-300 dark:border-slate-700', 'person_cancel'],
                    $application->status === 'accepted' => ['Diterima', 'bg-emerald-50 text-emerald-700 border-emerald-200 dark:bg-emerald-950/40 dark:text-emerald-300 dark:border-emerald-900', 'task_alt'],
                    $application->status === 'rejected' => ['Belum sesuai', 'bg-rose-50 text-rose-700 border-rose-200 dark:bg-rose-950/40 dark:text-rose-300 dark:border-rose-900', 'cancel'],
                    $application->status === 'interview' => ['Wawancara', 'bg-blue-50 text-blue-700 border-blue-200 dark:bg-blue-950/40 dark:text-blue-300 dark:border-blue-900', 'forum'],
                    default => ['Menunggu tinjauan', 'bg-amber-50 text-amber-700 border-amber-200 dark:bg-amber-950/40 dark:text-amber-300 dark:border-amber-900', 'hourglass_top'],
                };
            @endphp
            <article class="rounded-2xl border border-slate-200 bg-white p-4 sm:p-6 dark:border-slate-800 dark:bg-slate-900 transition-all hover:border-slate-300 dark:hover:border-slate-700 shadow-xs" data-reveal>
                {{-- Card Header: Category & Date on Left, Status Badge on Right --}}
                <div class="flex items-center justify-between gap-2">
                    <div class="flex items-center gap-1.5 sm:gap-2 text-xs text-slate-500 dark:text-slate-400 truncate">
                        <span class="font-bold uppercase tracking-wider text-brand-700 dark:text-brand-300 text-[11px] sm:text-xs">{{ $application->job->category->name }}</span>
                        <span>·</span>
                        <span class="text-[11px] sm:text-xs">Dikirim {{ $application->created_at->translatedFormat('d M Y, H:i') }}</span>
                    </div>
                    {{-- Clean Status Badge (Top Right) --}}
                    <span class="inline-flex items-center gap-1 rounded-full border px-2.5 py-0.5 text-[11px] sm:text-xs font-bold shrink-0 {{ $status[1] }}">
                        <span class="material-symbols-outlined text-[14px]">{{ $status[2] }}</span>
                        <span>{{ $status[0] }}</span>
                    </span>
                </div>

                {{-- Job Title & Employer --}}
                <div class="mt-2 sm:mt-2.5">
                    <h2 class="text-base sm:text-xl font-bold text-slate-950 dark:text-white leading-snug">
                        <a href="{{ route('jobs.show', $application->job) }}" class="hover:text-brand-700 dark:hover:text-brand-400 transition-colors">
                            {{ $application->job->title }}
                        </a>
                    </h2>
                    <p class="mt-0.5 text-xs sm:text-sm text-slate-500 font-medium">
                        {{ $application->job->employer->business_name }} · {{ $application->job->location }}
                    </p>
                </div>

                {{-- Compact Meta Chips (Single Row) --}}
                <div class="mt-3 flex flex-wrap items-center gap-2 text-xs text-slate-600 dark:text-slate-300">
                    <span class="inline-flex items-center gap-1 rounded-lg bg-slate-100 dark:bg-slate-800/80 px-2.5 py-1 text-[11px] sm:text-xs font-semibold">
                        <span class="material-symbols-outlined text-[14px] text-emerald-600 dark:text-emerald-400">payments</span>
                        <span>Rp {{ number_format($application->job->salary_amount, 0, ',', '.') }} / {{ $application->job->salary_type === 'monthly' ? 'bulan' : 'hari' }}</span>
                    </span>
                    <span class="inline-flex items-center gap-1 rounded-lg bg-slate-100 dark:bg-slate-800/80 px-2.5 py-1 text-[11px] sm:text-xs font-semibold">
                        <span class="material-symbols-outlined text-[14px] text-blue-600 dark:text-blue-400">schedule</span>
                        <span>{{ $application->job->work_hours_per_day }} jam per hari</span>
                    </span>
                    @if($application->resume_file)
                        <button type="button" data-preview-url="{{ route('applications.resume.preview', $application) }}" data-applicant-name="{{ auth()->user()->name }}" onclick="openPdfViewer(this.dataset.previewUrl, this.dataset.applicantName)" class="inline-flex items-center gap-1 rounded-lg bg-slate-100 hover:bg-slate-200 dark:bg-slate-800/80 dark:hover:bg-slate-700 px-2.5 py-1 text-[11px] sm:text-xs font-semibold text-brand-700 dark:text-brand-300 transition cursor-pointer" title="Lihat pratinjau resume PDF Anda">
                            <span class="material-symbols-outlined text-[14px]">description</span>
                            <span>Resume PDF</span>
                            <span class="material-symbols-outlined text-[12px]">visibility</span>
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
                            <a href="{{ route('chat.index', ['user' => $application->job->employer_id]) }}" class="portal-button-secondary text-xs !py-1.5 !px-3 gap-1.5" title="Buka riwayat obrolan dengan mitra">
                                <span class="material-symbols-outlined text-[15px]">chat</span>
                                <span>Riwayat Chat</span>
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
                        <div class="flex items-center gap-2 shrink-0 self-start sm:self-auto flex-wrap">
                            @if($application->resignation_status === 'pending')
                                <span class="inline-flex items-center gap-1 rounded-lg border border-amber-300 bg-amber-100/90 px-2.5 py-1 text-[11px] font-bold text-amber-800 dark:border-amber-800 dark:bg-amber-950/60 dark:text-amber-300">
                                    <span class="material-symbols-outlined text-[13px]">pending</span>
                                    Resign Sedang Ditinjau
                                </span>
                            @else
                                <a href="{{ route('jobs.show', $application->job) }}" class="inline-flex items-center gap-1 rounded-xl border border-rose-200 bg-rose-50 px-2.5 py-1.5 text-xs font-semibold text-rose-700 hover:bg-rose-100 dark:border-rose-900/60 dark:bg-rose-950/40 dark:text-rose-300 transition">
                                    <span class="material-symbols-outlined text-[14px]">exit_to_app</span>
                                    <span>Ajukan Resign</span>
                                </a>
                            @endif
                            <a href="{{ route('chat.index', ['user' => $application->job->employer_id]) }}" class="portal-button-primary !bg-emerald-700 hover:!bg-emerald-800 text-xs !py-1.5 !px-3 gap-1.5">
                                <span class="material-symbols-outlined text-[15px]">chat</span>
                                <span>Koordinasi Chat Mitra</span>
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
                        <div class="flex items-center gap-2 shrink-0 self-start sm:self-auto flex-wrap">
                            @if($application->interview_status !== 'confirmed' && $application->interview_status !== 'declined')
                                <form action="{{ route('applications.interview.response', $application) }}" method="POST" class="inline">
                                    @csrf
                                    <input type="hidden" name="action" value="confirmed">
                                    <button type="submit" class="inline-flex items-center gap-1 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white px-3 py-1.5 text-xs font-bold shadow-xs transition cursor-pointer" title="Konfirmasi kesiapan hadir Anda pada jadwal wawancara ini">
                                        <span class="material-symbols-outlined text-[15px]">check_circle</span>
                                        <span>Setujui Jadwal</span>
                                    </button>
                                </form>
                            @endif
                            <a href="{{ route('chat.index', ['user' => $application->job->employer_id]) }}" class="portal-button-primary !bg-indigo-700 hover:!bg-indigo-800 text-xs !py-1.5 !px-3 gap-1.5" title="Buka ruang obrolan untuk diskusi jadwal">
                                <span class="material-symbols-outlined text-[15px]">chat</span>
                                <span>Diskusi di Chat</span>
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
                                <a href="{{ route('jobs.show', $application->job) }}" class="portal-button-primary !bg-emerald-700 hover:!bg-emerald-800 text-xs !py-1.5 !px-3 gap-1.5 shadow-xs">
                                    <span class="material-symbols-outlined text-[15px]">replay</span>
                                    <span>Ajukan Lamaran Ulang</span>
                                </a>
                            @endif
                        </div>
                    </div>
                @endif

                {{-- Card Actions Footer --}}
                <div class="mt-3.5 pt-3 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between gap-2">
                    {{-- Primary Action: Lihat Detail Lowongan --}}
                    <a href="{{ route('jobs.show', $application->job) }}" class="portal-button-secondary !py-1.5 !px-3 text-xs font-bold gap-1.5 shadow-xs" title="Lihat detail lengkap lowongan ini">
                        <span class="material-symbols-outlined text-[16px]">visibility</span>
                        <span>Lihat Detail Lowongan</span>
                    </a>

                    {{-- Secondary Actions: Batalkan (hanya saat proses menunggu tinjauan) & Hapus Riwayat (khusus diterima, ditolak, wawancara, resign) --}}
                    <div class="flex items-center gap-1.5">
                        @if($application->status === 'pending')
                            <form id="cancelAppForm-{{ $application->id }}" action="{{ route('applications.destroy', $application) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="button" data-application-id="{{ $application->id }}" data-job-title="{{ $application->job->title }}" onclick="confirmCancelApp(this.dataset.applicationId, this.dataset.jobTitle)" class="inline-flex items-center gap-1 rounded-xl border border-rose-200 bg-rose-50/80 hover:bg-rose-100 px-2.5 py-1.5 text-xs font-bold text-rose-700 dark:border-rose-900/60 dark:bg-rose-950/40 dark:text-rose-300 dark:hover:bg-rose-900/50 transition cursor-pointer" title="Batalkan lamaran pekerjaan ini">
                                    <span class="material-symbols-outlined text-[15px]">cancel</span>
                                    <span>Batalkan</span>
                                </button>
                            </form>
                        @else
                            <form id="hideAppForm-{{ $application->id }}" action="{{ route('applications.hide', $application) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="button" data-application-id="{{ $application->id }}" data-job-title="{{ $application->job->title }}" onclick="confirmHideApp(this.dataset.applicationId, this.dataset.jobTitle)" class="inline-flex h-8 w-8 items-center justify-center rounded-xl border border-slate-200 bg-white text-slate-400 hover:border-rose-200 hover:bg-rose-50 hover:text-rose-600 dark:border-slate-800 dark:bg-slate-900 dark:text-slate-400 dark:hover:bg-rose-950/40 dark:hover:text-rose-400 transition cursor-pointer shadow-2xs" title="Hapus dari riwayat lamaran" aria-label="Hapus dari riwayat lamaran">
                                    <span class="material-symbols-outlined text-[17px]">delete_outline</span>
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </article>
        @empty
            <div class="rounded-3xl border border-dashed border-slate-300 bg-white p-14 text-center dark:border-slate-700 dark:bg-slate-900"><span class="material-symbols-outlined text-5xl text-slate-300">assignment</span><h2 class="mt-4 text-lg font-bold">Belum ada lamaran</h2><p class="mt-2 text-sm text-slate-500">Pilih lowongan yang sesuai dan kirim resume PDF Anda.</p><a href="{{ route('jobs.index') }}" class="portal-button-primary mt-6">Mulai mencari</a></div>
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
