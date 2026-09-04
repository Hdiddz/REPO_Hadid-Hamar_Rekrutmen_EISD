@extends('layouts.employer')

@section('title', 'Dashboard Mitra | KerjaLokal')
@section('portal_title', 'Lowongan milik '.$employer->business_name)
@section('portal_description', 'Kelola informasi kerja, buka atau tutup rekrutmen, dan pantau jumlah pelamar dari satu halaman.')
@section('portal_actions')<a href="{{ route('employer.applications.index') }}" class="portal-button-secondary"><span class="material-symbols-outlined text-[18px]">group</span>Lihat pelamar</a><a href="{{ route('employer.jobs.create') }}" class="portal-button-primary"><span class="material-symbols-outlined text-[18px]">add</span>Pasang lowongan</a>@endsection

@section('content')
    <section class="grid gap-3 sm:gap-4 grid-cols-2 xl:grid-cols-4" data-reveal>
        @foreach ([
            ['work', $metrics['open_jobs'], 'Lowongan aktif', 'Lowongan yang sedang dibuka untuk pelamar'],
            ['group', $metrics['applications'], 'Total pelamar', 'Seluruh berkas lamaran yang masuk'],
            ['task_alt', $metrics['accepted'], 'Pekerja diterima', 'Kandidat yang telah lolos dan diterima bekerja'],
            ['payments', 'Rp '.number_format($metrics['accepted_wages'], 0, ',', '.'), 'Alokasi upah pekerja', 'Total komitmen upah per periode untuk seluruh pekerja yang telah diterima']
        ] as $metric)
            <div class="portal-stat-card" title="{{ $metric[3] }}">
                <span class="material-symbols-outlined text-brand-600 dark:text-brand-300">{{ $metric[0] }}</span>
                <strong class="mt-5 block text-2xl font-bold">{{ $metric[1] }}</strong>
                <span class="mt-1 block text-sm text-slate-500 dark:text-slate-400">{{ $metric[2] }}</span>
            </div>
        @endforeach
    </section>

    <section class="mt-7 overflow-hidden rounded-2xl border border-slate-200 bg-white dark:border-slate-800 dark:bg-slate-900" data-reveal>
        <div class="flex items-center justify-between border-b border-indigo-100 bg-indigo-50/70 px-5 py-4 dark:border-indigo-900/40 dark:bg-indigo-950/30">
            <div class="flex items-center gap-3">
                <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-indigo-100 text-indigo-700 dark:bg-indigo-900/60 dark:text-indigo-300 shrink-0">
                    <span class="material-symbols-outlined text-[19px]">work</span>
                </div>
                <div>
                    <h2 class="font-bold text-slate-900 dark:text-white text-base">Daftar lowongan</h2>
                    <p class="text-xs text-indigo-950/60 dark:text-indigo-300/70">Data langsung dari lowongan milik akun Anda.</p>
                </div>
            </div>
        </div>
        <div class="divide-y divide-slate-100 dark:divide-slate-800">
            @forelse($jobs as $job)
                <article class="grid gap-4 p-5 lg:grid-cols-[1fr_auto] lg:items-center">
                    <div>
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="rounded-md bg-slate-100 px-2 py-1 text-[11px] font-bold text-slate-600 dark:bg-slate-800 dark:text-slate-300">{{ $job->category->name }}</span>
                            @if($job->isClosedByAdmin())
                                <span class="inline-flex items-center gap-1 rounded-md bg-rose-50 border border-rose-200 px-2 py-1 text-[11px] font-bold text-rose-700 dark:bg-rose-950/40 dark:border-rose-900/60 dark:text-rose-300">
                                    <span class="material-symbols-outlined text-[13px]">gavel</span>
                                    Ditutup Pengawas
                                </span>
                            @else
                                <span class="rounded-md px-2 py-1 text-[11px] font-bold {{ $job->status === 'open' ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/40 dark:text-emerald-300' : 'bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-300' }}">{{ $job->status === 'open' ? 'Dibuka' : 'Ditutup' }}</span>
                            @endif
                        </div>
                        <h3 class="mt-2 text-base font-bold text-slate-950 dark:text-white">{{ $job->title }}</h3>
                        <p class="mt-1 text-xs text-slate-500">{{ $job->location }} · Rp {{ number_format($job->salary_amount, 0, ',', '.') }} / {{ $job->salary_type === 'monthly' ? 'bulan' : 'hari' }} · {{ $job->applications_count }} pelamar</p>
                        @if($job->isClosedByAdmin() && $job->closed_reason)
                            <p class="mt-1.5 text-xs text-rose-600 dark:text-rose-400 font-medium line-clamp-1" title="{{ $job->closed_reason }}">
                                <span class="font-bold">Catatan Pengawas:</span> {{ $job->closed_reason }}
                            </p>
                        @endif
                    </div>
                    <div class="flex flex-wrap items-center gap-2">
                        <a href="{{ route('jobs.show', $job) }}" class="portal-icon-button" title="Lihat detail lowongan">
                            <span class="material-symbols-outlined text-[18px]">visibility</span>
                        </a>
                        <a href="{{ route('employer.jobs.edit', $job) }}" class="portal-button-secondary">Edit</a>
                        <form id="deleteEmployerJob-{{ $job->id }}" action="{{ route('employer.jobs.destroy', $job) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="button" data-job-id="{{ $job->id }}" data-job-title="{{ $job->title }}" onclick="confirmDeleteJob(this.dataset.jobId, this.dataset.jobTitle)" class="portal-icon-button text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-950/40 cursor-pointer" title="Hapus lowongan">
                                <span class="material-symbols-outlined text-[18px]">delete</span>
                            </button>
                        </form>
                    </div>
                </article>
            @empty
                <div class="p-12 text-center"><span class="material-symbols-outlined text-4xl text-slate-300">work_off</span><h3 class="mt-3 font-bold">Belum ada lowongan</h3><p class="mt-1 text-sm text-slate-500">Terbitkan peluang pertama untuk mulai menerima pelamar.</p></div>
            @endforelse
        </div>
    </section>
    <div class="mt-6">{{ $jobs->links() }}</div>

    {{-- Bagian: Daftar Peserta / Tenaga Kerja yang Diterima --}}
    <section class="mt-8 overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900" data-reveal>
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between border-b border-emerald-100 px-6 py-4 dark:border-emerald-900/40 bg-emerald-50/70 dark:bg-emerald-950/30">
            <div class="flex items-center gap-3">
                <div class="grid h-10 w-10 place-items-center rounded-2xl bg-emerald-50 text-emerald-600 dark:bg-emerald-950/50 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-900/50 shrink-0">
                    <span class="material-symbols-outlined text-[22px]">how_to_reg</span>
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <h2 class="text-base sm:text-lg font-bold text-slate-950 dark:text-white">Peserta &amp; Tenaga Kerja Diterima</h2>
                        <span class="rounded-lg bg-emerald-100 px-2 py-0.5 text-xs font-extrabold text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300">
                            {{ $acceptedWorkers->count() }} Orang
                        </span>
                    </div>
                    <p class="mt-0.5 text-xs text-slate-500 dark:text-slate-400">Pencari kerja yang telah lolos seleksi dan resmi bergabung di UMKM Anda.</p>
                </div>
            </div>
            <a href="{{ route('employer.applications.index', ['status' => 'accepted']) }}" class="portal-button-secondary !py-1.5 !px-3 text-xs font-semibold gap-1.5 self-start sm:self-auto">
                <span class="material-symbols-outlined text-[16px]">groups</span>
                Semua Data Pelamar
            </a>
        </div>

        <div class="divide-y divide-slate-100 dark:divide-slate-800">
            @forelse($acceptedWorkers as $worker)
                <div class="p-5 sm:p-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4 transition hover:bg-slate-50/70 dark:hover:bg-slate-950/40">
                    <div class="flex items-start gap-4 min-w-0">
                        <!-- Avatar -->
                        <button type="button"
                                onclick="openApplicantProfileModal({{ Js::from([
                                    'name' => $worker->user->name,
                                    'username' => $worker->user->username ? '@'.$worker->user->username : null,
                                    'email' => $worker->user->email,
                                    'phone' => $worker->user->phone,
                                    'avatar_url' => $worker->user->avatar_url,
                                    'initials' => strtoupper(substr($worker->user->name, 0, 2)),
                                    'registered_at' => $worker->user->created_at ? $worker->user->created_at->locale('id')->translatedFormat('d F Y').' ('.$worker->user->created_at->locale('id')->diffForHumans().')' : 'Terdaftar di platform',
                                    'job_title' => $worker->job->title,
                                    'status' => $worker->status,
                                    'status_label' => match($worker->status) {
                                        'accepted' => 'Diterima Bekerja',
                                        'resigned' => 'Telah Resign',
                                        default => 'Kandidat Diterima'
                                    },
                                    'applied_at' => $worker->created_at ? $worker->created_at->locale('id')->translatedFormat('d F Y') : '-',
                                    'chat_url' => route('chat.index', ['user' => $worker->user_id]),
                                ]) }})"
                                class="relative h-12 w-12 rounded-2xl overflow-hidden ring-2 ring-emerald-500/20 bg-slate-100 dark:bg-slate-800 shrink-0 flex items-center justify-center hover:ring-4 hover:ring-emerald-500/40 hover:scale-105 transition cursor-pointer group"
                                title="Klik untuk melihat detail profil pekerja"
                                aria-label="Lihat profil {{ $worker->user->name }}">
                            @if($worker->user->avatar_url)
                                <img src="{{ $worker->user->avatar_url }}" alt="{{ $worker->user->name }}" class="h-full w-full object-cover group-hover:scale-105 transition-transform">
                            @else
                                <span class="text-base font-bold text-slate-700 dark:text-slate-200">
                                    {{ strtoupper(substr($worker->user->name, 0, 2)) }}
                                </span>
                            @endif
                        </button>

                        <!-- Worker Info -->
                        <div class="min-w-0">
                            <div class="flex flex-wrap items-center gap-2">
                                <h3 class="text-base font-bold text-slate-900 dark:text-white truncate">
                                    {{ $worker->user->name }}
                                </h3>
                                <span class="font-mono text-xs text-brand-700 dark:text-brand-300 font-semibold">
                                    @<span>{{ $worker->user->username ?? '-' }}</span>
                                </span>
                                @if($worker->resignation_status === 'pending')
                                    <span class="inline-flex items-center gap-1 rounded-full bg-rose-100 dark:bg-rose-950/60 px-2.5 py-0.5 text-[11px] font-bold text-rose-700 dark:text-rose-300 border border-rose-200 dark:border-rose-900 animate-pulse">
                                        <span class="material-symbols-outlined text-[13px]">exit_to_app</span>
                                        Pengajuan Resign Masuk
                                    </span>
                                @elseif($worker->resignation_status === 'approved')
                                    <span class="inline-flex items-center gap-1 rounded-full bg-slate-100 dark:bg-slate-800 px-2 py-0.5 text-[11px] font-bold text-slate-600 dark:text-slate-400 border border-slate-200 dark:border-slate-700">
                                        <span class="material-symbols-outlined text-[13px]">check</span>
                                        Resign Disetujui
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 rounded-full bg-emerald-50 px-2 py-0.5 text-[11px] font-bold text-emerald-700 dark:bg-emerald-950/50 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800">
                                        <span class="material-symbols-outlined text-[13px]">check_circle</span>
                                        Diterima Bekerja
                                    </span>
                                @endif
                            </div>

                            <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">
                                Posisi: <strong class="text-slate-700 dark:text-slate-200">{{ $worker->job->title }}</strong> · {{ $worker->job->location }}
                            </p>

                            <div class="mt-2 flex flex-wrap items-center gap-x-4 gap-y-1 text-xs text-slate-500 dark:text-slate-400">
                                <span class="flex items-center gap-1">
                                    <span class="material-symbols-outlined text-[15px] text-slate-400">payments</span>
                                    Rp {{ number_format($worker->job->salary_amount, 0, ',', '.') }} / {{ $worker->job->salary_type === 'monthly' ? 'bulan' : 'hari' }}
                                </span>
                                @if($worker->user->phone)
                                    <a href="tel:{{ $worker->user->phone }}" class="flex items-center gap-1 hover:text-brand-600 transition">
                                        <span class="material-symbols-outlined text-[15px] text-slate-400">call</span>
                                        {{ $worker->user->phone }}
                                    </a>
                                @endif
                                <a href="mailto:{{ $worker->user->email }}" class="flex items-center gap-1 hover:text-brand-600 transition">
                                    <span class="material-symbols-outlined text-[15px] text-slate-400">mail</span>
                                    {{ $worker->user->email }}
                                </a>
                                <span class="flex items-center gap-1 text-[11px] text-slate-400">
                                    <span class="material-symbols-outlined text-[15px]">event_available</span>
                                    Bergabung: {{ $worker->updated_at->translatedFormat('d M Y') }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="flex items-center gap-2 self-end sm:self-center shrink-0 flex-wrap">
                        @if($worker->resignation_status === 'pending')
                            <button type="button" 
                                    data-application-id="{{ $worker->id }}" data-candidate-name="{{ $worker->user->name }}" data-job-title="{{ $worker->job->title }}" data-employer-name="{{ auth()->user()->business_name ?: auth()->user()->name }}" onclick="openResignDecisionModal({ applicationId: this.dataset.applicationId, decision: 'approved', candidateName: this.dataset.candidateName, jobTitle: this.dataset.jobTitle, employerName: this.dataset.employerName })"
                                    class="inline-flex items-center gap-1 rounded-xl bg-emerald-600 px-3 py-2 text-xs font-bold text-white shadow-sm hover:bg-emerald-700 transition cursor-pointer" title="Setujui permohonan resign">
                                <span class="material-symbols-outlined text-[16px]">check</span>
                                Setujui Resign
                            </button>
                            <button type="button" 
                                    data-application-id="{{ $worker->id }}" data-candidate-name="{{ $worker->user->name }}" data-job-title="{{ $worker->job->title }}" data-employer-name="{{ auth()->user()->business_name ?: auth()->user()->name }}" onclick="openResignDecisionModal({ applicationId: this.dataset.applicationId, decision: 'rejected', candidateName: this.dataset.candidateName, jobTitle: this.dataset.jobTitle, employerName: this.dataset.employerName })"
                                    class="inline-flex items-center gap-1 rounded-xl bg-rose-600 px-3 py-2 text-xs font-bold text-white shadow-sm hover:bg-rose-700 transition cursor-pointer" title="Tolak permohonan resign">
                                <span class="material-symbols-outlined text-[16px]">close</span>
                                Tolak
                            </button>
                        @endif
                        <a href="{{ route('chat.index', ['user' => $worker->user_id]) }}" class="portal-button-primary !py-2 !px-3 text-xs font-bold gap-1.5" title="Kirim pesan langsung ke peserta">
                            <span class="material-symbols-outlined text-[17px]">chat</span>
                            Chat Peserta
                        </a>
                        <button type="button" data-preview-url="{{ route('employer.applications.resume.preview', $worker) }}" data-applicant-name="{{ $worker->user->name }}" data-download-url="{{ route('employer.applications.resume', $worker) }}" onclick="openPdfViewer(this.dataset.previewUrl, this.dataset.applicantName, this.dataset.downloadUrl)" class="portal-button-secondary !py-2 !px-3 text-xs font-semibold gap-1.5 cursor-pointer" title="Lihat resume PDF">
                            <span class="material-symbols-outlined text-[17px]">visibility</span>
                            Resume
                        </button>
                    </div>
                </div>
            @empty
                <div class="p-10 text-center">
                    <span class="material-symbols-outlined text-4xl text-slate-300 dark:text-slate-600 block mb-2">person_search</span>
                    <h3 class="font-bold text-sm text-slate-700 dark:text-slate-300">Belum ada peserta yang diterima</h3>
                    <p class="mt-1 text-xs text-slate-500 dark:text-slate-400 max-w-md mx-auto">
                        Ketika Anda menerima pelamar pada menu <a href="{{ route('employer.applications.index') }}" class="text-brand-600 font-semibold underline">Pelamar Masuk</a>, data peserta akan otomatis tercatat dan tampil di daftar ini.
                    </p>
                </div>
            @endforelse
        </div>
    </section>
@endsection

@section('portal_modals')
    <x-resign-decision-modal />
@endsection

@push('scripts')
<script>
    async function confirmDeleteJob(jobId, jobTitle) {
        const confirmed = await window.showAppConfirm({
            title: 'Hapus Lowongan Pekerjaan?',
            message: `Apakah Anda yakin ingin menghapus lowongan "${jobTitle}"?\n\nJika lowongan sudah memiliki pelamar, sistem akan otomatis mengamankannya menjadi ditutup untuk arsip riwayat.`,
            confirmText: 'Ya, Hapus',
            cancelText: 'Batal',
            type: 'danger',
            icon: 'delete'
        });

        if (confirmed) {
            document.getElementById(`deleteEmployerJob-${jobId}`)?.submit();
        }
    }
</script>
@endpush
