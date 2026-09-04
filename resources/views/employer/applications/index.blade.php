@extends('layouts.employer')

@section('title', 'Pelamar Masuk | KerjaLokal')
@section('portal_title', 'Pelamar masuk')
@section('portal_description', 'Tinjau data kandidat, unduh resume dari penyimpanan privat, lalu perbarui status seleksi dengan jendela konfirmasi khusus.')
@section('portal_actions')
    <a href="{{ route('employer.dashboard') }}" class="portal-button-secondary">
        <span class="material-symbols-outlined text-[18px]">work</span>
        Kelola lowongan
    </a>
@endsection

@section('content')
    {{-- Quick Status Filter - Mobile Dropdown --}}
    <div class="mb-4 sm:hidden" data-reveal>
        <label for="mobile-status-filter" class="portal-label mb-1.5 flex items-center gap-1.5">
            <span class="material-symbols-outlined text-[17px] text-brand-700 dark:text-brand-400">filter_list</span>
            <span>Filter Status Pelamar</span>
        </label>
        <div class="relative">
            <select id="mobile-status-filter" onchange="if(this.value) window.location.href=this.value" class="portal-input appearance-none pr-10 font-semibold text-slate-800 dark:text-slate-100">
                <option value="{{ route('employer.applications.index', array_filter(['job' => request('job')])) }}" @selected(!request('status'))>
                    Semua Pelamar
                </option>
                <option value="{{ route('employer.applications.index', array_filter(['job' => request('job'), 'status' => 'pending'])) }}" @selected(request('status') === 'pending')>
                    Menunggu Tinjauan
                </option>
                <option value="{{ route('employer.applications.index', array_filter(['job' => request('job'), 'status' => 'interview'])) }}" @selected(request('status') === 'interview')>
                    Tahap Wawancara
                </option>
                <option value="{{ route('employer.applications.index', array_filter(['job' => request('job'), 'status' => 'accepted'])) }}" @selected(request('status') === 'accepted')>
                    Peserta Diterima
                </option>
                <option value="{{ route('employer.applications.index', array_filter(['job' => request('job'), 'status' => 'rejected'])) }}" @selected(request('status') === 'rejected')>
                    Ditolak
                </option>
            </select>
            <span class="material-symbols-outlined pointer-events-none absolute right-3 top-1/2 -translate-y-1/2 text-[20px] text-slate-400">expand_more</span>
        </div>
    </div>

    {{-- Quick Status Filter - Desktop/Tablet Pill Tabs --}}
    <div class="mb-5 hidden sm:flex flex-wrap items-center gap-2" data-reveal>
        <a href="{{ route('employer.applications.index', array_filter(['job' => request('job')])) }}" class="inline-flex items-center gap-1.5 rounded-xl px-3.5 py-2 text-xs font-bold transition {{ !request('status') ? 'bg-brand-700 text-white shadow-xs dark:bg-brand-500 dark:text-brand-950' : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-50 dark:bg-slate-900 dark:text-slate-300 dark:border-slate-800' }}">
            <span class="material-symbols-outlined text-[16px]">groups</span>
            Semua Pelamar
        </a>
        <a href="{{ route('employer.applications.index', array_filter(['job' => request('job'), 'status' => 'pending'])) }}" class="inline-flex items-center gap-1.5 rounded-xl px-3.5 py-2 text-xs font-bold transition {{ request('status') === 'pending' ? 'bg-amber-600 text-white shadow-xs' : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-50 dark:bg-slate-900 dark:text-slate-300 dark:border-slate-800' }}">
            <span class="material-symbols-outlined text-[16px]">hourglass_empty</span>
            Menunggu Tinjauan
        </a>
        <a href="{{ route('employer.applications.index', array_filter(['job' => request('job'), 'status' => 'interview'])) }}" class="inline-flex items-center gap-1.5 rounded-xl px-3.5 py-2 text-xs font-bold transition {{ request('status') === 'interview' ? 'bg-indigo-600 text-white shadow-xs' : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-50 dark:bg-slate-900 dark:text-slate-300 dark:border-slate-800' }}">
            <span class="material-symbols-outlined text-[16px]">record_voice_over</span>
            Wawancara
        </a>
        <a href="{{ route('employer.applications.index', array_filter(['job' => request('job'), 'status' => 'accepted'])) }}" class="inline-flex items-center gap-1.5 rounded-xl px-3.5 py-2 text-xs font-bold transition {{ request('status') === 'accepted' ? 'bg-emerald-600 text-white shadow-xs' : 'bg-emerald-50 text-emerald-800 border border-emerald-200 hover:bg-emerald-100 dark:bg-emerald-950/40 dark:text-emerald-300 dark:border-emerald-900/60' }}">
            <span class="material-symbols-outlined text-[16px]">how_to_reg</span>
            Peserta Diterima
        </a>
        <a href="{{ route('employer.applications.index', array_filter(['job' => request('job'), 'status' => 'rejected'])) }}" class="inline-flex items-center gap-1.5 rounded-xl px-3.5 py-2 text-xs font-bold transition {{ request('status') === 'rejected' ? 'bg-rose-600 text-white shadow-xs' : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-50 dark:bg-slate-900 dark:text-slate-300 dark:border-slate-800' }}">
            <span class="material-symbols-outlined text-[16px]">cancel</span>
            Ditolak
        </a>
    </div>

    @if(request('status') === 'accepted')
        <div class="mb-5 p-4 rounded-2xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 text-emerald-900 dark:text-emerald-200 flex items-center justify-between gap-3 text-xs" data-reveal>
            <div class="flex items-center gap-2.5">
                <span class="material-symbols-outlined text-emerald-600 dark:text-emerald-400 text-[22px]">how_to_reg</span>
                <div>
                    <strong class="block font-bold text-sm">Daftar Peserta Resmi Diterima Bekerja</strong>
                    <span class="text-emerald-800 dark:text-emerald-300">Kandidat di bawah ini telah resmi diterima dan terdaftar sebagai peserta aktif di UMKM Anda.</span>
                </div>
            </div>
            <a href="{{ route('employer.dashboard') }}#peserta-diterima" class="shrink-0 font-bold underline hover:no-underline">
                Lihat di Dashboard
            </a>
        </div>
    @endif

    <form method="GET" class="mb-6 grid gap-3 rounded-2xl border border-slate-200 bg-white p-4 sm:grid-cols-[1fr_1fr_auto] dark:border-slate-800 dark:bg-slate-900" data-reveal>
        <div>
            <label for="job" class="portal-label">Lowongan</label>
            <select id="job" name="job" class="portal-input">
                <option value="">Semua lowongan</option>
                @foreach($jobs as $job)
                    <option value="{{ $job->id }}" @selected((string) request('job') === (string) $job->id)>{{ $job->title }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label for="status" class="portal-label">Status seleksi</label>
            <select id="status" name="status" class="portal-input">
                <option value="">Semua status</option>
                @foreach(['pending' => 'Menunggu', 'interview' => 'Wawancara', 'accepted' => 'Diterima', 'rejected' => 'Ditolak'] as $value => $label)
                    <option value="{{ $value }}" @selected(request('status') === $value)>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div class="flex items-end gap-2">
            <button class="portal-button-primary">Terapkan</button>
            @if(request()->hasAny(['job','status']))
                <a href="{{ route('employer.applications.index') }}" class="portal-button-secondary">Reset</a>
            @endif
        </div>
    </form>

    <div class="space-y-4">
        @forelse($applications as $application)
            <article class="rounded-2xl border border-slate-200 bg-white p-5 dark:border-slate-800 dark:bg-slate-900 transition hover:border-slate-300 dark:hover:border-slate-700" data-reveal>
                <div class="grid gap-5 lg:grid-cols-[1fr_auto] lg:items-start">
                    <div class="flex items-start gap-4">
                        <div class="relative h-12 w-12 shrink-0 rounded-2xl overflow-hidden bg-brand-700 flex items-center justify-center font-bold text-white shadow-xs">
                            @if($application->user->avatar_url)
                                <img src="{{ $application->user->avatar_url }}" alt="{{ $application->user->name }}" class="h-full w-full object-cover">
                            @else
                                {{ strtoupper(substr($application->user->name, 0, 2)) }}
                            @endif
                        </div>
                        <div>
                            <div class="flex flex-wrap items-center gap-2">
                                <p class="text-xs font-bold uppercase tracking-wider text-brand-700 dark:text-brand-300">{{ $application->job->title }}</p>
                                @if($application->status === 'accepted')
                                    <span class="inline-flex items-center gap-1 rounded-full bg-emerald-50 px-2.5 py-0.5 text-[11px] font-bold text-emerald-700 dark:bg-emerald-950/50 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800">
                                        <span class="material-symbols-outlined text-[13px]">how_to_reg</span>
                                        Peserta Resmi Diterima
                                    </span>
                                @elseif($application->status === 'resigned')
                                    <span class="inline-flex items-center gap-1 rounded-full bg-slate-100 px-2.5 py-0.5 text-[11px] font-bold text-slate-700 dark:bg-slate-800 dark:text-slate-300 border border-slate-300 dark:border-slate-700">
                                        <span class="material-symbols-outlined text-[13px]">person_cancel</span>
                                        Telah Resign
                                    </span>
                                @elseif($application->status === 'interview')
                                    <span class="inline-flex items-center gap-1 rounded-full bg-indigo-50 px-2 py-0.5 text-[11px] font-bold text-indigo-700 border border-indigo-200">
                                        <span class="material-symbols-outlined text-[13px]">event</span>
                                        Tahap Wawancara
                                    </span>
                                @elseif($application->status === 'rejected')
                                    <span class="inline-flex items-center gap-1 rounded-full bg-rose-50 px-2 py-0.5 text-[11px] font-bold text-rose-700 border border-rose-200">
                                        <span class="material-symbols-outlined text-[13px]">cancel</span>
                                        Belum Lolos
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 rounded-full bg-amber-50 px-2 py-0.5 text-[11px] font-bold text-amber-700 border border-amber-200">
                                        <span class="material-symbols-outlined text-[13px]">hourglass_empty</span>
                                        Menunggu Tinjauan
                                    </span>
                                @endif
                            </div>
                            <h2 class="mt-1 text-lg font-bold text-slate-900 dark:text-white flex items-center gap-2">
                                <span>{{ $application->user->name }}</span>
                                <span class="font-mono text-xs font-semibold text-slate-400">@<span>{{ $application->user->username ?? '-' }}</span></span>
                            </h2>
                            <p class="mt-1 text-sm text-slate-500">{{ $application->user->email }}{{ $application->user->phone ? ' · '.$application->user->phone : '' }}</p>
                            <div class="mt-3 flex flex-wrap gap-1.5">
                                @foreach($application->job->skills as $skill)
                                    <span class="rounded-md bg-slate-100 px-2 py-1 text-[11px] font-semibold text-slate-600 dark:bg-slate-800 dark:text-slate-300">{{ $skill->name }}</span>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    <div class="flex flex-wrap gap-2">
                        <a href="{{ route('chat.index', ['user' => $application->user_id]) }}" class="portal-button-primary !bg-teal-700 hover:!bg-teal-800 gap-1.5" title="Kirim pesan langsung">
                            <span class="material-symbols-outlined text-[18px]">chat</span>
                            Chat
                        </a>
                        <button type="button" onclick="openPdfViewer('{{ route('employer.applications.resume.preview', $application) }}', '{{ addslashes($application->user->name) }}', '{{ route('employer.applications.resume', $application) }}')" class="portal-button-primary cursor-pointer">
                            <span class="material-symbols-outlined text-[18px]">visibility</span>
                            Lihat resume
                        </button>
                        <a href="{{ route('employer.applications.resume', $application) }}" class="portal-button-secondary">
                            <span class="material-symbols-outlined text-[18px]">download</span>
                            Unduh resume
                        </a>
                    </div>
                </div>

                @if($application->note)
                    <p class="mt-5 rounded-xl bg-slate-50 p-4 text-sm leading-6 text-slate-600 dark:bg-slate-950 dark:text-slate-300">
                        <strong>Catatan kandidat:</strong> {{ $application->note }}
                    </p>
                @endif

                @if($application->resignation_status === 'pending')
                    <div class="mt-5 rounded-2xl bg-rose-50/90 dark:bg-rose-950/40 p-4 border border-rose-200 dark:border-rose-900/60 text-xs text-rose-900 dark:text-rose-200">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                            <div>
                                <div class="flex items-center gap-2 font-bold text-sm text-rose-700 dark:text-rose-400">
                                    <span class="material-symbols-outlined text-[20px]">exit_to_app</span>
                                    <span>Pengajuan Pengunduran Diri (Resign) Masuk</span>
                                </div>
                                <div class="mt-2 space-y-1 text-slate-700 dark:text-slate-300">
                                    <p>• <strong>Tanggal Efektif Resign:</strong> {{ $application->resignation_date ? $application->resignation_date->translatedFormat('l, d F Y') : '-' }}</p>
                                    <p>• <strong>Alasan Resign:</strong> {{ $application->resignation_reason }}</p>
                                    @if($application->resignation_notes)
                                        <p>• <strong>Pesan Kandidat:</strong> "{{ $application->resignation_notes }}"</p>
                                    @endif
                                </div>
                            </div>
                            <div class="flex items-center gap-2 shrink-0 self-start sm:self-center">
                                <button type="button" 
                                        onclick="openResignDecisionModal({ applicationId: '{{ $application->id }}', decision: 'approved', candidateName: '{{ addslashes($application->user->name) }}', jobTitle: '{{ addslashes($application->job->title) }}', employerName: '{{ addslashes(auth()->user()->business_name ?: auth()->user()->name) }}' })"
                                        class="inline-flex items-center gap-1.5 rounded-xl bg-emerald-600 px-3 py-2 text-xs font-bold text-white shadow-sm shadow-emerald-600/25 hover:bg-emerald-700 active:translate-y-px transition cursor-pointer">
                                    <span class="material-symbols-outlined text-[16px]">check</span>
                                    Setujui Resign
                                </button>
                                <button type="button" 
                                        onclick="openResignDecisionModal({ applicationId: '{{ $application->id }}', decision: 'rejected', candidateName: '{{ addslashes($application->user->name) }}', jobTitle: '{{ addslashes($application->job->title) }}', employerName: '{{ addslashes(auth()->user()->business_name ?: auth()->user()->name) }}' })"
                                        class="inline-flex items-center gap-1.5 rounded-xl bg-rose-600 px-3 py-2 text-xs font-bold text-white shadow-sm shadow-rose-600/25 hover:bg-rose-700 active:translate-y-px transition cursor-pointer">
                                    <span class="material-symbols-outlined text-[16px]">close</span>
                                    Tolak Pengajuan
                                </button>
                            </div>
                        </div>
                    </div>
                @elseif($application->resignation_status === 'approved')
                    <div class="mt-4 rounded-xl bg-slate-100 dark:bg-slate-800 p-3 text-xs text-slate-600 dark:text-slate-300 flex items-center gap-2">
                        <span class="material-symbols-outlined text-slate-500 text-[18px]">check_circle</span>
                        <span>Pengajuan resign kandidat telah disetujui resmi (Resigned).</span>
                    </div>
                @elseif($application->resignation_status === 'rejected')
                    <div class="mt-4 rounded-xl bg-amber-50 dark:bg-amber-950/40 border border-amber-200 dark:border-amber-900 p-3 text-xs text-amber-800 dark:text-amber-200 flex items-center gap-2">
                        <span class="material-symbols-outlined text-amber-600 text-[18px]">info</span>
                        <span>Pengajuan resign kandidat telah ditolak untuk saat ini.</span>
                    </div>
                @endif

                {{-- Status Selection Form with Modal Trigger --}}
                <div class="mt-5 flex flex-col gap-3 border-t border-slate-100 pt-5 sm:flex-row sm:items-end dark:border-slate-800">
                    <div class="flex-1">
                        <label for="status-{{ $application->id }}" class="portal-label">Status seleksi</label>
                        <select id="status-{{ $application->id }}"
                                class="portal-input application-status-select"
                                data-app-id="{{ $application->id }}"
                                data-app-url="{{ route('employer.applications.update', $application) }}"
                                data-candidate-name="{{ $application->user->name }}"
                                data-job-title="{{ $application->job->title }}"
                                data-original-status="{{ $application->status }}"
                                onchange="handleStatusChange(this)">
                            @if($application->status === 'resigned')
                                <option value="resigned" selected>Resign (Telah Mengundurkan Diri)</option>
                            @endif
                            @foreach(['pending' => 'Menunggu tinjauan', 'interview' => 'Wawancara', 'accepted' => 'Diterima', 'rejected' => 'Ditolak'] as $value => $label)
                                <option value="{{ $value }}" @selected($application->status === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <button type="button"
                            onclick="triggerStatusModalForApp('{{ $application->id }}')"
                            class="portal-button-primary sm:mb-px cursor-pointer inline-flex items-center gap-1.5">
                        <span class="material-symbols-outlined text-[18px]">tune</span>
                        Atur & Perbarui Status
                    </button>
                </div>
            </article>
        @empty
            <div class="rounded-2xl border border-dashed border-slate-300 bg-white p-12 text-center dark:border-slate-700 dark:bg-slate-900">
                <span class="material-symbols-outlined text-4xl text-slate-300">group_off</span>
                <h2 class="mt-3 font-bold">Belum ada pelamar</h2>
                <p class="mt-1 text-sm text-slate-500">Pelamar baru akan tampil setelah mengirim resume.</p>
            </div>
        @endforelse
    </div>

    <div class="mt-7">{{ $applications->links() }}</div>
@endsection

@section('portal_modals')
    <x-resign-decision-modal />

    {{-- 1. MODAL WAWANCARA (INTERVIEW) --}}
    <div id="modalStatusInterview" class="fixed inset-0 z-[160] hidden bg-slate-950/60 backdrop-blur-xs p-4 overflow-y-auto flex items-center justify-center" role="dialog" aria-modal="true">
        <div class="fixed inset-0 -z-10" onclick="closeAllStatusModals()"></div>
        <div class="relative w-full max-w-lg rounded-3xl bg-white p-6 sm:p-7 shadow-2xl dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 animate-modal-pop">
            <div class="flex items-start justify-between gap-3 border-b border-slate-100 pb-4 dark:border-slate-800">
                <div class="flex items-center gap-3">
                    <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-indigo-50 text-indigo-700 dark:bg-indigo-950 dark:text-indigo-300 shrink-0">
                        <span class="material-symbols-outlined text-2xl">event</span>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-slate-950 dark:text-white">Atur Jadwal Wawancara</h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                            Kandidat: <strong id="interviewCandidateName" class="text-slate-800 dark:text-slate-200"></strong> · <span id="interviewJobTitle"></span>
                        </p>
                    </div>
                </div>
                <button type="button" onclick="closeAllStatusModals()" class="rounded-xl p-1.5 text-slate-400 hover:bg-slate-100 hover:text-slate-600 dark:hover:bg-slate-800 dark:hover:text-slate-300 transition cursor-pointer" aria-label="Tutup">
                    <span class="material-symbols-outlined text-[20px]">close</span>
                </button>
            </div>

            <form id="formStatusInterview" method="POST" class="mt-5 space-y-4">
                @csrf
                @method('PATCH')
                <input type="hidden" name="status" value="interview">

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label for="interview_date" class="portal-label">Hari & Tanggal Wawancara <span class="text-rose-500">*</span></label>
                        <input type="date" id="interview_date" name="interview_date" min="{{ date('Y-m-d') }}" value="{{ date('Y-m-d', strtotime('+1 day')) }}" class="portal-input" required>
                    </div>
                    <div>
                        <label for="interview_time" class="portal-label">Waktu / Jam (WIB) <span class="text-rose-500">*</span></label>
                        <input type="time" id="interview_time" name="interview_time" value="10:00" class="portal-input" required>
                    </div>
                </div>

                <div>
                    <label for="interview_type" class="portal-label">Metode Pelaksanaan Wawancara</label>
                    <select id="interview_type" name="interview_type" class="portal-input">
                        <option value="Tatap Muka di Lokasi UMKM">Tatap Muka di Lokasi UMKM</option>
                        <option value="Online via Google Meet / Zoom">Online via Google Meet / Zoom</option>
                        <option value="Panggilan Telepon / WhatsApp Video">Panggilan Telepon / WhatsApp Video</option>
                    </select>
                </div>

                <div>
                    <label for="interview_location" class="portal-label">Alamat Lokasi / Tautan Pertemuan</label>
                    <input type="text" id="interview_location" name="interview_location" placeholder="Contoh: Alamat toko/kantor atau tautan meet.google.com/..." class="portal-input" value="{{ auth()->user()->business_name ? auth()->user()->business_name : '' }}">
                </div>

                <div>
                    <label for="interview_notes" class="portal-label">Pesan Konfirmasi & Catatan ke Kandidat</label>
                    <textarea id="interview_notes" name="interview_notes" rows="3" class="portal-input text-xs leading-relaxed" placeholder="Tuliskan instruksi atau pesan kesiapan..."></textarea>
                    <p class="mt-1 text-[11px] text-slate-400">Pesan ini beserta rincian jadwal akan otomatis dikirimkan ke ruang chat dan lonceng notifikasi kandidat.</p>
                </div>

                <div class="flex items-center justify-end gap-2.5 border-t border-slate-100 pt-4 dark:border-slate-800">
                    <button type="button" onclick="closeAllStatusModals()" class="portal-button-secondary py-2 px-4 text-xs cursor-pointer">
                        Batal
                    </button>
                    <button type="submit" class="portal-button-primary !bg-indigo-700 hover:!bg-indigo-800 py-2 px-4 text-xs inline-flex items-center gap-1.5 cursor-pointer">
                        <span class="material-symbols-outlined text-[16px]">send</span>
                        Kirim Undangan Wawancara
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- 2. MODAL DITERIMA (ACCEPTED) --}}
    <div id="modalStatusAccepted" class="fixed inset-0 z-[160] hidden bg-slate-950/60 backdrop-blur-xs p-4 overflow-y-auto flex items-center justify-center" role="dialog" aria-modal="true">
        <div class="fixed inset-0 -z-10" onclick="closeAllStatusModals()"></div>
        <div class="relative w-full max-w-lg rounded-3xl bg-white p-6 sm:p-7 shadow-2xl dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 animate-modal-pop">
            <div class="flex items-start justify-between gap-3 border-b border-slate-100 pb-4 dark:border-slate-800">
                <div class="flex items-center gap-3">
                    <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-emerald-50 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300 shrink-0">
                        <span class="material-symbols-outlined text-2xl">how_to_reg</span>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-slate-950 dark:text-white">Konfirmasi Penerimaan Kerja</h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                            Kandidat: <strong id="acceptedCandidateName" class="text-slate-800 dark:text-slate-200"></strong> · <span id="acceptedJobTitle"></span>
                        </p>
                    </div>
                </div>
                <button type="button" onclick="closeAllStatusModals()" class="rounded-xl p-1.5 text-slate-400 hover:bg-slate-100 hover:text-slate-600 dark:hover:bg-slate-800 dark:hover:text-slate-300 transition cursor-pointer" aria-label="Tutup">
                    <span class="material-symbols-outlined text-[20px]">close</span>
                </button>
            </div>

            <form id="formStatusAccepted" method="POST" class="mt-5 space-y-4">
                @csrf
                @method('PATCH')
                <input type="hidden" name="status" value="accepted">

                <div class="rounded-2xl bg-emerald-50/70 dark:bg-emerald-950/30 p-3.5 border border-emerald-200/70 dark:border-emerald-800/60 text-xs text-emerald-900 dark:text-emerald-200 leading-relaxed">
                    <div class="flex items-center gap-2 font-bold mb-1">
                        <span class="material-symbols-outlined text-[16px]">verified</span>
                        Peserta Resmi Tenaga Kerja Aktif
                    </div>
                    Kandidat ini akan langsung tercatat sebagai <strong>Peserta Diterima Aktif</strong> di dasbor UMKM Anda untuk mendukung pencatatan SDG 8 (Pekerjaan Layak).
                </div>

                <div>
                    <label for="start_date" class="portal-label">Tanggal Mulai Bekerja</label>
                    <input type="date" id="start_date" name="start_date" min="{{ date('Y-m-d') }}" value="{{ date('Y-m-d', strtotime('+3 days')) }}" class="portal-input">
                </div>

                <div>
                    <label for="acceptance_notes" class="portal-label">Catatan & Instruksi Hari Pertama Kerja</label>
                    <textarea id="acceptance_notes" name="acceptance_notes" rows="3" class="portal-input text-xs leading-relaxed" placeholder="Contoh: Silakan hadir pukul 08:30 WIB dengan membawa pakaian rapi dan fotokopi KTP..."></textarea>
                </div>

                <div class="flex items-center justify-end gap-2.5 border-t border-slate-100 pt-4 dark:border-slate-800">
                    <button type="button" onclick="closeAllStatusModals()" class="portal-button-secondary py-2 px-4 text-xs cursor-pointer">
                        Batal
                    </button>
                    <button type="submit" class="portal-button-primary !bg-emerald-600 hover:!bg-emerald-700 py-2 px-4 text-xs inline-flex items-center gap-1.5 cursor-pointer">
                        <span class="material-symbols-outlined text-[16px]">check_circle</span>
                        Konfirmasi & Terima Peserta Resmi
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- 3. MODAL DITOLAK (REJECTED) --}}
    <div id="modalStatusRejected" class="fixed inset-0 z-[160] hidden bg-slate-950/60 backdrop-blur-xs p-4 overflow-y-auto flex items-center justify-center" role="dialog" aria-modal="true">
        <div class="fixed inset-0 -z-10" onclick="closeAllStatusModals()"></div>
        <div class="relative w-full max-w-lg rounded-3xl bg-white p-6 sm:p-7 shadow-2xl dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 animate-modal-pop">
            <div class="flex items-start justify-between gap-3 border-b border-slate-100 pb-4 dark:border-slate-800">
                <div class="flex items-center gap-3">
                    <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-rose-50 text-rose-700 dark:bg-rose-950 dark:text-rose-300 shrink-0">
                        <span class="material-symbols-outlined text-2xl">cancel</span>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-slate-950 dark:text-white">Pembaruan Seleksi: Belum Lolos</h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                            Kandidat: <strong id="rejectedCandidateName" class="text-slate-800 dark:text-slate-200"></strong> · <span id="rejectedJobTitle"></span>
                        </p>
                    </div>
                </div>
                <button type="button" onclick="closeAllStatusModals()" class="rounded-xl p-1.5 text-slate-400 hover:bg-slate-100 hover:text-slate-600 dark:hover:bg-slate-800 dark:hover:text-slate-300 transition cursor-pointer" aria-label="Tutup">
                    <span class="material-symbols-outlined text-[20px]">close</span>
                </button>
            </div>

            <form id="formStatusRejected" method="POST" class="mt-5 space-y-4">
                @csrf
                @method('PATCH')
                <input type="hidden" name="status" value="rejected">

                <div>
                    <label for="rejection_reason" class="portal-label">Kategori Alasan Evaluasi</label>
                    <select id="rejection_reason" name="rejection_reason" class="portal-input">
                        <option value="Kualifikasi keterampilan belum sesuai dengan kebutuhan posisi saat ini">Kualifikasi keterampilan belum sesuai saat ini</option>
                        <option value="Kuota penerimaan pelamar sudah terpenuhi">Kuota penerimaan pelamar sudah terpenuhi</option>
                        <option value="Jadwal ketersediaan waktu belum cocok">Jadwal ketersediaan waktu belum cocok</option>
                        <option value="Kriteria pengalaman kerja belum mencukupi">Kriteria pengalaman kerja belum mencukupi</option>
                        <option value="Lainnya">Alasan Lainnya</option>
                    </select>
                </div>

                <div>
                    <label for="rejection_notes" class="portal-label">Catatan & Pesan Penyemangat untuk Kandidat</label>
                    <textarea id="rejection_notes" name="rejection_notes" rows="3" class="portal-input text-xs leading-relaxed" placeholder="Tuliskan pesan motivasi untuk pelamar..."></textarea>
                </div>

                <div class="flex items-center justify-end gap-2.5 border-t border-slate-100 pt-4 dark:border-slate-800">
                    <button type="button" onclick="closeAllStatusModals()" class="portal-button-secondary py-2 px-4 text-xs cursor-pointer">
                        Batal
                    </button>
                    <button type="submit" class="portal-button-primary !bg-rose-600 hover:!bg-rose-700 py-2 px-4 text-xs inline-flex items-center gap-1.5 cursor-pointer">
                        <span class="material-symbols-outlined text-[16px]">send</span>
                        Simpan & Kirim Pemberitahuan
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- 4. MODAL MENUNGGU TINJAUAN (PENDING) --}}
    <div id="modalStatusPending" class="fixed inset-0 z-[160] hidden bg-slate-950/60 backdrop-blur-xs p-4 overflow-y-auto flex items-center justify-center" role="dialog" aria-modal="true">
        <div class="fixed inset-0 -z-10" onclick="closeAllStatusModals()"></div>
        <div class="relative w-full max-w-md rounded-3xl bg-white p-6 sm:p-7 shadow-2xl dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 animate-modal-pop">
            <div class="flex items-start gap-4">
                <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-amber-50 text-amber-700 dark:bg-amber-950 dark:text-amber-300 shrink-0">
                    <span class="material-symbols-outlined text-2xl">hourglass_empty</span>
                </div>
                <div class="min-w-0 flex-1">
                    <h3 class="text-base font-bold text-slate-950 dark:text-white">Kembalikan ke Menunggu Tinjauan</h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 leading-relaxed">
                        Apakah Anda yakin ingin mengembalikan status lamaran <strong id="pendingCandidateName" class="text-slate-800 dark:text-slate-200"></strong> untuk posisi <span id="pendingJobTitle"></span> ke status <strong>Menunggu Tinjauan</strong>?
                    </p>
                </div>
            </div>

            <form id="formStatusPending" method="POST" class="mt-6 flex items-center justify-end gap-2.5">
                @csrf
                @method('PATCH')
                <input type="hidden" name="status" value="pending">

                <button type="button" onclick="closeAllStatusModals()" class="portal-button-secondary py-2 px-4 text-xs cursor-pointer">
                    Batal
                </button>
                <button type="submit" class="portal-button-primary !bg-amber-600 hover:!bg-amber-700 py-2 px-4 text-xs inline-flex items-center gap-1.5 cursor-pointer">
                    <span class="material-symbols-outlined text-[16px]">save</span>
                    Ubah Status
                </button>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    let activeSelectElement = null;

    function handleStatusChange(selectEl) {
        activeSelectElement = selectEl;
        const targetStatus = selectEl.value;
        const dataset = selectEl.dataset;
        openStatusModal(targetStatus, dataset);
    }

    function triggerStatusModalForApp(appId) {
        const selectEl = document.getElementById('status-' + appId);
        if (selectEl) {
            handleStatusChange(selectEl);
        }
    }

    function openStatusModal(status, data) {
        closeAllStatusModals(false);

        const candidate = data.candidateName || 'Kandidat';
        const jobTitle = data.jobTitle || 'Lowongan';
        const appUrl = data.appUrl;

        if (status === 'interview') {
            document.getElementById('interviewCandidateName').textContent = candidate;
            document.getElementById('interviewJobTitle').textContent = jobTitle;
            document.getElementById('formStatusInterview').action = appUrl;
            document.getElementById('interview_notes').value = `Halo ${candidate}, Anda diundang untuk mengikuti sesi wawancara untuk posisi "${jobTitle}". Mohon konfirmasi kesiapan Anda pada hari dan jam tersebut melalui ruang obrolan ini. Terima kasih!`;
            showModal('modalStatusInterview');
        } else if (status === 'accepted') {
            document.getElementById('acceptedCandidateName').textContent = candidate;
            document.getElementById('acceptedJobTitle').textContent = jobTitle;
            document.getElementById('formStatusAccepted').action = appUrl;
            document.getElementById('acceptance_notes').value = `Selamat ${candidate}! Anda resmi diterima bekerja untuk posisi "${jobTitle}". Silakan hadir tepat waktu di hari pertama dengan membawa identitas diri dan berkas pendukung.`;
            showModal('modalStatusAccepted');
        } else if (status === 'rejected') {
            document.getElementById('rejectedCandidateName').textContent = candidate;
            document.getElementById('rejectedJobTitle').textContent = jobTitle;
            document.getElementById('formStatusRejected').action = appUrl;
            document.getElementById('rejection_notes').value = `Terima kasih atas waktu dan minat Anda melamar posisi "${jobTitle}" di UMKM kami. Kami mendoakan kesuksesan untuk langkah karier Anda selanjutnya.`;
            showModal('modalStatusRejected');
        } else if (status === 'pending') {
            document.getElementById('pendingCandidateName').textContent = candidate;
            document.getElementById('pendingJobTitle').textContent = jobTitle;
            document.getElementById('formStatusPending').action = appUrl;
            showModal('modalStatusPending');
        }
    }

    function showModal(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            document.body.classList.add('overflow-hidden');
        }
    }

    function closeAllStatusModals(resetSelect = true) {
        ['modalStatusInterview', 'modalStatusAccepted', 'modalStatusRejected', 'modalStatusPending'].forEach(id => {
            const modal = document.getElementById(id);
            if (modal) {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
            }
        });
        document.body.classList.remove('overflow-hidden');

        if (resetSelect && activeSelectElement) {
            activeSelectElement.value = activeSelectElement.dataset.originalStatus;
        }
    }

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            closeAllStatusModals(true);
        }
    });
</script>
@endpush
