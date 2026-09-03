@extends('layouts.app')

@section('title', 'Riwayat Lamaran | KerjaLokal')

@section('content')
    <div class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between" data-reveal><div><p class="text-xs font-bold uppercase tracking-[0.18em] text-coral-600">Proses seleksi</p><h1 class="mt-2 text-3xl font-bold tracking-tight text-slate-950 dark:text-white">Riwayat lamaran Anda.</h1><p class="mt-2 text-sm text-slate-500">Status diperbarui oleh mitra UMKM pada satu alur yang terhubung.</p></div><a href="{{ route('jobs.index') }}" class="portal-button-primary w-fit"><span class="material-symbols-outlined text-[18px]">search</span>Cari lowongan</a></div>

    {{-- Filter Tabs Status Lamaran --}}
    <div class="mb-6 flex items-center gap-2 overflow-x-auto pb-1" data-reveal>
        <a href="{{ route('applications.index') }}" class="inline-flex items-center gap-1.5 rounded-xl px-3.5 py-2 text-xs font-bold whitespace-nowrap transition {{ !request('status') ? 'bg-brand-700 text-white shadow-xs dark:bg-brand-500 dark:text-brand-950' : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-50 dark:bg-slate-900 dark:text-slate-300 dark:border-slate-800' }}">
            <span>Semua Lamaran</span>
            @if(isset($counts['all']))
                <span class="rounded-full bg-black/10 dark:bg-white/10 px-1.5 py-0.2 text-[10px]">{{ $counts['all'] }}</span>
            @endif
        </a>
        <a href="{{ route('applications.index', ['status' => 'pending']) }}" class="inline-flex items-center gap-1.5 rounded-xl px-3.5 py-2 text-xs font-bold whitespace-nowrap transition {{ request('status') === 'pending' ? 'bg-amber-600 text-white shadow-xs' : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-50 dark:bg-slate-900 dark:text-slate-300 dark:border-slate-800' }}">
            <span class="material-symbols-outlined text-[16px]">hourglass_empty</span>
            <span>Menunggu Tinjauan</span>
            @if(isset($counts['pending']) && $counts['pending'] > 0)
                <span class="rounded-full bg-amber-100 text-amber-800 dark:bg-amber-950 dark:text-amber-300 px-1.5 py-0.2 text-[10px]">{{ $counts['pending'] }}</span>
            @endif
        </a>
        <a href="{{ route('applications.index', ['status' => 'interview']) }}" class="inline-flex items-center gap-1.5 rounded-xl px-3.5 py-2 text-xs font-bold whitespace-nowrap transition {{ request('status') === 'interview' ? 'bg-indigo-600 text-white shadow-xs' : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-50 dark:bg-slate-900 dark:text-slate-300 dark:border-slate-800' }}">
            <span class="material-symbols-outlined text-[16px]">record_voice_over</span>
            <span>Tahap Wawancara</span>
            @if(isset($counts['interview']) && $counts['interview'] > 0)
                <span class="rounded-full bg-indigo-100 text-indigo-800 dark:bg-indigo-950 dark:text-indigo-300 px-1.5 py-0.2 text-[10px]">{{ $counts['interview'] }}</span>
            @endif
        </a>
        <a href="{{ route('applications.index', ['status' => 'accepted']) }}" class="inline-flex items-center gap-1.5 rounded-xl px-3.5 py-2 text-xs font-bold whitespace-nowrap transition {{ request('status') === 'accepted' ? 'bg-emerald-600 text-white shadow-xs' : 'bg-emerald-50 text-emerald-800 border border-emerald-200 hover:bg-emerald-100 dark:bg-emerald-950/40 dark:text-emerald-300 dark:border-emerald-900/60' }}">
            <span class="material-symbols-outlined text-[16px]">how_to_reg</span>
            <span>Diterima Bekerja</span>
            @if(isset($counts['accepted']) && $counts['accepted'] > 0)
                <span class="rounded-full bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300 px-1.5 py-0.2 text-[10px]">{{ $counts['accepted'] }}</span>
            @endif
        </a>
        <a href="{{ route('applications.index', ['status' => 'rejected']) }}" class="inline-flex items-center gap-1.5 rounded-xl px-3.5 py-2 text-xs font-bold whitespace-nowrap transition {{ request('status') === 'rejected' ? 'bg-rose-600 text-white shadow-xs' : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-50 dark:bg-slate-900 dark:text-slate-300 dark:border-slate-800' }}">
            <span class="material-symbols-outlined text-[16px]">cancel</span>
            <span>Belum Sesuai</span>
            @if(isset($counts['rejected']) && $counts['rejected'] > 0)
                <span class="rounded-full bg-rose-100 text-rose-800 dark:bg-rose-950 dark:text-rose-300 px-1.5 py-0.2 text-[10px]">{{ $counts['rejected'] }}</span>
            @endif
        </a>
        <a href="{{ route('applications.index', ['status' => 'resigned']) }}" class="inline-flex items-center gap-1.5 rounded-xl px-3.5 py-2 text-xs font-bold whitespace-nowrap transition {{ request('status') === 'resigned' ? 'bg-slate-800 text-white shadow-xs' : 'bg-slate-100 text-slate-700 border border-slate-200 hover:bg-slate-200/80 dark:bg-slate-800 dark:text-slate-300 dark:border-slate-700' }}">
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
            <article class="rounded-2xl border border-slate-200 bg-white p-5 dark:border-slate-800 dark:bg-slate-900 sm:p-6" data-reveal>
                <div class="flex flex-col gap-4 md:flex-row md:items-start md:justify-between">
                    <div>
                        <div class="flex flex-wrap items-center gap-2 text-xs text-slate-400">
                            <span class="font-bold uppercase tracking-wider text-brand-700 dark:text-brand-300">{{ $application->job->category->name }}</span>
                            <span>·</span>
                            <span>Dikirim {{ $application->created_at->translatedFormat('d M Y, H:i') }}</span>
                        </div>
                        <h2 class="mt-2 text-xl font-bold text-slate-950 dark:text-white">
                            <a href="{{ route('jobs.show', $application->job) }}" class="hover:text-brand-700 transition-colors">
                                {{ $application->job->title }}
                            </a>
                        </h2>
                        <p class="mt-1 text-sm text-slate-500">{{ $application->job->employer->business_name }} · {{ $application->job->location }}</p>
                    </div>
                    <div class="flex flex-wrap items-center gap-2.5 shrink-0">
                        <span class="inline-flex w-fit items-center gap-1.5 rounded-xl border px-3 py-1.5 text-xs font-bold {{ $status[1] }}">
                            <span class="material-symbols-outlined text-[17px]">{{ $status[2] }}</span>
                            {{ $status[0] }}
                        </span>
                        <a href="{{ route('jobs.show', $application->job) }}" class="portal-button-secondary !py-1.5 !px-3 text-xs font-bold gap-1.5 shadow-xs" title="Lihat detail lengkap lowongan ini">
                            <span class="material-symbols-outlined text-[16px]">visibility</span>
                            Lihat Detail Lowongan
                        </a>
                        @if($application->status !== 'accepted' && $application->status !== 'resigned')
                            <form id="cancelAppForm-{{ $application->id }}" action="{{ route('applications.destroy', $application) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="button" onclick="confirmCancelApp('{{ $application->id }}', '{{ addslashes($application->job->title) }}')" class="inline-flex items-center gap-1 rounded-xl border border-rose-200 bg-rose-50/80 px-2.5 py-1.5 text-xs font-bold text-rose-700 shadow-xs hover:bg-rose-100 hover:border-rose-300 dark:border-rose-900/60 dark:bg-rose-950/40 dark:text-rose-300 dark:hover:bg-rose-900/50 transition cursor-pointer" title="Batalkan lamaran ini">
                                    <span class="material-symbols-outlined text-[16px]">cancel</span>
                                    Batalkan
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
                <div class="mt-5 grid gap-3 border-t border-slate-100 pt-5 text-sm sm:grid-cols-3 dark:border-slate-800">
                    <div>
                        <span class="block text-xs text-slate-400">Upah</span>
                        <strong class="mt-1 block">Rp {{ number_format($application->job->salary_amount, 0, ',', '.') }} / {{ $application->job->salary_type === 'monthly' ? 'bulan' : 'hari' }}</strong>
                    </div>
                    <div>
                        <span class="block text-xs text-slate-400">Jam kerja</span>
                        <strong class="mt-1 block">{{ $application->job->work_hours_per_day }} jam per hari</strong>
                    </div>
                    <div>
                        <span class="block text-xs text-slate-400">Berkas</span>
                        <div class="mt-1 flex items-center gap-2">
                            <strong class="block">Resume PDF</strong>
                            @if($application->resume_file)
                                <button type="button" onclick="openPdfViewer('{{ route('applications.resume.preview', $application) }}', '{{ addslashes(auth()->user()->name) }}')" class="inline-flex items-center gap-1 text-xs font-semibold text-brand-700 hover:text-brand-800 hover:underline cursor-pointer">
                                    <span class="material-symbols-outlined text-[15px]">visibility</span>
                                    Lihat PDF
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
                @if($application->note)
                    <p class="mt-4 rounded-xl bg-slate-50 p-3 text-xs leading-5 text-slate-600 dark:bg-slate-950 dark:text-slate-300">
                        <strong>Catatan lamaran Anda:</strong> {{ $application->note }}
                    </p>
                @endif

                @if($isResigned)
                    <div class="mt-4 rounded-2xl bg-slate-50 dark:bg-slate-800/60 p-4 border border-slate-200 dark:border-slate-700 flex flex-col sm:flex-row sm:items-center justify-between gap-3 text-xs">
                        <div class="flex items-start gap-3 text-slate-700 dark:text-slate-300">
                            <span class="material-symbols-outlined text-slate-500 dark:text-slate-400 text-[24px] shrink-0 mt-0.5">person_cancel</span>
                            <div>
                                <strong class="font-extrabold text-sm block text-slate-900 dark:text-white">Pengunduran Diri (Resign) Resmi Selesai</strong>
                                <div class="mt-1 flex flex-wrap items-center gap-x-3 gap-y-1 text-slate-600 dark:text-slate-400">
                                    @if($application->resignation_date)
                                        <span>Tanggal Efektif: <strong class="font-bold text-slate-800 dark:text-slate-200">{{ $application->resignation_date->translatedFormat('l, d F Y') }}</strong></span>
                                        <span>·</span>
                                    @endif
                                    <span>Status: <strong class="font-bold text-slate-800 dark:text-slate-200">Resmi Mengundurkan Diri</strong></span>
                                </div>
                                @if($application->resignation_reason)
                                    <p class="mt-1 text-[11px] text-slate-500 dark:text-slate-400"><strong>Alasan pengajuan:</strong> {{ $application->resignation_reason }}</p>
                                @endif
                                <p class="mt-1 text-[11px] text-slate-500 dark:text-slate-400">
                                    Permohonan pengunduran diri Anda telah disetujui oleh <strong>{{ $application->job->employer->business_name }}</strong>{{ $application->resigned_at ? ' pada ' . $application->resigned_at->translatedFormat('d F Y, H:i') : '' }}. Masa kerja sama untuk posisi ini telah berakhir secara resmi.
                                </p>
                            </div>
                        </div>
                        <div class="flex items-center gap-2 shrink-0 flex-wrap">
                            <span class="inline-flex items-center gap-1 rounded-xl border border-slate-300 bg-slate-100 px-2.5 py-1.5 text-[11px] font-bold text-slate-700 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-300">
                                <span class="material-symbols-outlined text-[15px]">check_circle</span>
                                Resign Disetujui
                            </span>
                            <a href="{{ route('chat.index', ['user' => $application->job->employer_id]) }}" class="portal-button-secondary text-xs !py-2 !px-3 gap-1.5" title="Buka riwayat obrolan dengan mitra">
                                <span class="material-symbols-outlined text-[16px]">chat</span>
                                Riwayat Chat
                            </a>
                        </div>
                    </div>
                @elseif($application->status === 'accepted')
                    <div class="mt-4 rounded-2xl bg-emerald-50 dark:bg-emerald-950/40 p-4 border border-emerald-200 dark:border-emerald-800/80 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                        <div class="flex items-start gap-3 text-xs text-emerald-900 dark:text-emerald-200">
                            <span class="material-symbols-outlined text-emerald-600 dark:text-emerald-400 text-[22px] shrink-0 mt-0.5">celebration</span>
                            <div>
                                <strong class="font-extrabold text-sm block text-emerald-950 dark:text-white">Selamat! Anda Resmi Diterima Bekerja 🎉</strong>
                                <div class="mt-1 flex flex-wrap items-center gap-x-3 gap-y-1 text-emerald-800 dark:text-emerald-300">
                                    @if($application->start_date)
                                        <span>Tanggal Mulai Kerja: <strong class="font-bold text-emerald-950 dark:text-emerald-100">{{ $application->start_date->translatedFormat('l, d F Y') }}</strong></span>
                                        <span>·</span>
                                    @endif
                                    <span>Status: <strong class="font-bold text-emerald-950 dark:text-emerald-100">Peserta Aktif Bekerja</strong></span>
                                </div>
                                @if($application->acceptance_notes)
                                    <p class="mt-1.5 text-[11px] text-emerald-700 dark:text-emerald-300 italic">"{{ $application->acceptance_notes }}"</p>
                                @endif
                            </div>
                        </div>
                        <div class="flex items-center gap-2 shrink-0 flex-wrap">
                            @if($application->resignation_status === 'pending')
                                <span class="inline-flex items-center gap-1 rounded-xl border border-amber-300 bg-amber-100/80 px-2.5 py-1.5 text-[11px] font-bold text-amber-800 dark:border-amber-800 dark:bg-amber-950/60 dark:text-amber-300">
                                    <span class="material-symbols-outlined text-[15px]">pending_actions</span>
                                    Resign Sedang Ditinjau
                                </span>
                            @else
                                <a href="{{ route('jobs.show', $application->job) }}" class="inline-flex items-center gap-1 rounded-xl border border-rose-200 bg-rose-50 px-2.5 py-1.5 text-xs font-semibold text-rose-700 hover:bg-rose-100 dark:border-rose-900/60 dark:bg-rose-950/40 dark:text-rose-300 dark:hover:bg-rose-900/50 transition">
                                    <span class="material-symbols-outlined text-[15px]">exit_to_app</span>
                                    Ajukan Resign
                                </a>
                            @endif
                            <a href="{{ route('chat.index', ['user' => $application->job->employer_id]) }}" class="portal-button-primary !bg-emerald-700 hover:!bg-emerald-800 text-xs !py-2 !px-3.5 gap-1.5">
                                <span class="material-symbols-outlined text-[16px]">chat</span>
                                Koordinasi Chat Mitra
                            </a>
                        </div>
                    </div>
                @elseif($application->status === 'interview')
                    <div class="mt-4 rounded-2xl bg-indigo-50 dark:bg-indigo-950/40 p-4 border border-indigo-200 dark:border-indigo-800/80 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                        <div class="flex items-start gap-3 text-xs text-indigo-900 dark:text-indigo-200">
                            <span class="material-symbols-outlined text-indigo-600 dark:text-indigo-400 text-[22px] shrink-0 mt-0.5">event</span>
                            <div>
                                <strong class="font-extrabold text-sm block text-indigo-950 dark:text-white">Jadwal Sesi Wawancara 📅</strong>
                                <div class="mt-1 flex flex-wrap items-center gap-x-3 gap-y-1 text-indigo-800 dark:text-indigo-300">
                                    @if($application->interview_date)
                                        <span>Hari & Tanggal: <strong class="font-bold text-indigo-950 dark:text-indigo-100">{{ $application->interview_date->translatedFormat('l, d F Y') }}</strong></span>
                                    @endif
                                    @if($application->interview_time)
                                        <span>·</span>
                                        <span>Pukul: <strong class="font-bold text-indigo-950 dark:text-indigo-100">{{ $application->interview_time }} WIB</strong></span>
                                    @endif
                                    @if($application->interview_type)
                                        <span>·</span>
                                        <span>Metode: <strong class="font-bold text-indigo-950 dark:text-indigo-100">{{ $application->interview_type }}</strong></span>
                                    @endif
                                </div>
                                @if($application->interview_location)
                                    <p class="mt-1 text-[11px] text-indigo-700 dark:text-indigo-300">
                                        <strong>Lokasi / Tautan:</strong> {{ $application->interview_location }}
                                    </p>
                                @endif
                                @if($application->interview_notes)
                                    <p class="mt-1 text-[11px] text-indigo-700 dark:text-indigo-300 italic">"{{ $application->interview_notes }}"</p>
                                @endif
                            </div>
                        </div>
                        <a href="{{ route('chat.index', ['user' => $application->job->employer_id]) }}" class="portal-button-primary !bg-indigo-700 hover:!bg-indigo-800 text-xs !py-2 !px-3.5 shrink-0 gap-1.5">
                            <span class="material-symbols-outlined text-[16px]">chat</span>
                            Konfirmasi Jadwal
                        </a>
                    </div>
                @elseif($application->status === 'rejected')
                    <div class="mt-4 rounded-2xl bg-rose-50 dark:bg-rose-950/40 p-4 border border-rose-200 dark:border-rose-800/80 flex flex-col sm:flex-row sm:items-start justify-between gap-3">
                        <div class="flex items-start gap-3 text-xs text-rose-900 dark:text-rose-200">
                            <span class="material-symbols-outlined text-rose-600 dark:text-rose-400 text-[22px] shrink-0 mt-0.5">info</span>
                            <div>
                                <strong class="font-extrabold text-sm block text-rose-950 dark:text-white">Pemberitahuan Seleksi Belum Berhasil</strong>
                                <p class="mt-1 text-rose-800 dark:text-rose-300">
                                    Mohon maaf, saat ini kualifikasi Anda belum sesuai dengan kebutuhan posisi ini. Jangan berkecil hati dan terus kembangkan kemampuan Anda.
                                </p>
                                @if($application->rejection_reason)
                                    <p class="mt-1 text-[11px] text-rose-700 dark:text-rose-300">
                                        <strong>Keterangan mitra:</strong> {{ $application->rejection_reason }}
                                    </p>
                                @endif
                                @if($application->rejection_notes)
                                    <p class="mt-1 text-[11px] text-rose-700 dark:text-rose-300 italic">"{{ $application->rejection_notes }}"</p>
                                @endif
                            </div>
                        </div>
                        <a href="{{ route('jobs.index') }}" class="portal-button-primary !bg-rose-700 hover:!bg-rose-800 text-xs !py-2 !px-3.5 shrink-0 gap-1.5">
                            <span class="material-symbols-outlined text-[16px]">search</span>
                            Cari Lowongan Lain
                        </a>
                    </div>
                @endif

                <div class="mt-4 flex flex-wrap items-center justify-between gap-3 border-t border-slate-100 pt-3 dark:border-slate-800">
                    <span class="text-xs text-slate-400 flex items-center gap-1">
                        <span class="material-symbols-outlined text-[15px]">info</span>
                        Status saat ini: <strong class="text-slate-600 dark:text-slate-300 font-semibold">{{ $status[0] }}</strong>
                        @if($isResigned)
                            <span class="text-slate-600 dark:text-slate-400 font-medium">(Telah resmi mengundurkan diri)</span>
                        @elseif($application->status === 'accepted')
                            <span class="text-emerald-600 dark:text-emerald-400 font-medium">(Resmi diterima & terdaftar)</span>
                        @elseif($application->status === 'interview')
                            <span class="text-indigo-600 dark:text-indigo-400 font-medium">(Tahap seleksi wawancara)</span>
                        @elseif($application->status === 'rejected')
                            <span class="text-rose-600 dark:text-rose-400 font-medium">(Proses seleksi selesai)</span>
                        @else
                            <span>(dalam peninjauan oleh mitra)</span>
                        @endif
                    </span>
                    <a href="{{ route('jobs.show', $application->job) }}" class="inline-flex items-center gap-1 text-xs font-bold text-brand-700 hover:text-brand-800 hover:underline dark:text-brand-400">
                        <span>Buka Detail Lengkap Lowongan</span>
                        <span class="material-symbols-outlined text-[15px]">arrow_forward</span>
                    </a>
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
</script>
@endpush
