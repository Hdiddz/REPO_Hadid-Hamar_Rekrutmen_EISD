@extends('layouts.app')

@section('title', $job->title.' | KerjaLokal')

@section('content')
    <a href="{{ route('jobs.index') }}" class="mb-5 inline-flex items-center gap-1 text-sm font-bold text-brand-700 hover:underline dark:text-brand-300"><span class="material-symbols-outlined text-[18px]">arrow_back</span>Kembali ke daftar lowongan</a>
    <div class="grid gap-6 lg:grid-cols-[minmax(0,1fr)_380px]">
        <div class="space-y-5">
            <section class="rounded-3xl bg-brand-950 p-6 text-white sm:p-9" data-reveal>
                <div class="flex flex-wrap items-center gap-2">
                    <span class="rounded-lg bg-white/10 px-2.5 py-1 text-xs font-bold text-brand-100">{{ $job->category->name }}</span>
                    <span class="rounded-lg bg-emerald-400/15 px-2.5 py-1 text-xs font-bold text-emerald-200">{{ $job->status === 'open' ? 'Masih dibuka' : 'Ditutup' }}</span>
                    <span class="inline-flex items-center gap-1.5 rounded-lg bg-white/10 px-2.5 py-1 text-xs font-semibold text-brand-100" title="{{ $job->updated_at->translatedFormat('d F Y, H:i:s') }} WIB">
                        <span class="material-symbols-outlined text-[15px] text-emerald-300">schedule</span>
                        Diperbarui <span data-relative-time="{{ $job->updated_at->toISOString() }}">{{ $job->updated_at->diffForHumans() }}</span>
                    </span>
                    @auth
                        @if(auth()->user()->id === $job->employer_id)
                            <a href="{{ route('employer.jobs.edit', $job) }}" class="ml-auto inline-flex items-center gap-1.5 rounded-xl bg-white/20 px-3 py-1.5 text-xs font-bold text-white transition hover:bg-white/30">
                                <span class="material-symbols-outlined text-[16px]">edit</span>
                                Edit Lowongan
                            </a>
                        @endif
                    @endauth
                </div>
                <h1 class="mt-6 text-3xl font-bold tracking-tight sm:text-4xl">{{ $job->title }}</h1><p class="mt-2 text-base text-brand-100">{{ $job->employer->business_name }}</p>
                <dl class="mt-8 grid gap-3 sm:grid-cols-3"><div class="rounded-2xl bg-white/10 p-4"><dt class="text-xs text-brand-200">Lokasi</dt><dd class="mt-1 text-sm font-bold">{{ $job->location }}</dd></div><div class="rounded-2xl bg-white/10 p-4"><dt class="text-xs text-brand-200">Upah</dt><dd class="mt-1 text-sm font-bold">Rp {{ number_format($job->salary_amount, 0, ',', '.') }} / {{ $job->salary_type === 'monthly' ? 'bulan' : 'hari' }}</dd></div><div class="rounded-2xl bg-white/10 p-4"><dt class="text-xs text-brand-200">Jam kerja</dt><dd class="mt-1 text-sm font-bold">{{ $job->work_hours_per_day }} jam / hari</dd></div></dl>
            </section>

            <section class="rounded-2xl border border-slate-200 bg-white p-6 dark:border-slate-800 dark:bg-slate-900" data-reveal><h2 class="text-lg font-bold text-slate-950 dark:text-white">Deskripsi pekerjaan</h2><div class="mt-4 whitespace-pre-line text-sm leading-7 text-slate-600 dark:text-slate-300">{{ $job->description }}</div></section>
            <section class="rounded-2xl border border-slate-200 bg-white p-6 dark:border-slate-800 dark:bg-slate-900" data-reveal><h2 class="text-lg font-bold text-slate-950 dark:text-white">Keterampilan yang dicari</h2><div class="mt-4 flex flex-wrap gap-2">@foreach($job->skills as $skill)<span class="rounded-lg bg-brand-50 px-3 py-1.5 text-xs font-bold text-brand-700 dark:bg-brand-950 dark:text-brand-300">{{ $skill->name }}</span>@endforeach</div></section>
            <section class="rounded-2xl border border-slate-200 bg-white p-6 dark:border-slate-800 dark:bg-slate-900" data-reveal><h2 class="text-lg font-bold text-slate-950 dark:text-white">Tentang mitra</h2><p class="mt-3 text-sm font-bold">{{ $job->employer->business_name }}</p><p class="mt-1 text-sm text-slate-500">Penanggung jawab: {{ $job->employer->name }}</p>@auth @if(auth()->user()->hasRole('jobseeker'))<a href="{{ route('chat.index', ['user' => $job->employer_id]) }}" class="portal-button-secondary mt-5 w-fit"><span class="material-symbols-outlined text-[18px]">chat</span>Pesan mitra</a>@endif @endauth</section>
        </div>

        <aside class="lg:sticky lg:top-24 lg:self-start">
            <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-xl shadow-slate-900/5 dark:border-slate-800 dark:bg-slate-900 sm:p-6" data-reveal>
                @guest
                    <div class="grid h-12 w-12 place-items-center rounded-2xl bg-brand-50 text-brand-700 dark:bg-brand-950 dark:text-brand-300"><span class="material-symbols-outlined">login</span></div><h2 class="mt-5 text-xl font-bold">Masuk untuk melamar</h2><p class="mt-2 text-sm leading-6 text-slate-500">Buat akun pencari kerja untuk mengirim resume dan memantau status seleksi.</p><a href="{{ route('login') }}" class="portal-button-primary mt-6 w-full">Masuk</a><a href="{{ route('register') }}" class="portal-button-secondary mt-2 w-full">Daftar</a>
                @elseif(auth()->user()->hasRole('jobseeker'))
                    @if($hasApplied)
                        <div class="grid h-12 w-12 place-items-center rounded-2xl bg-emerald-50 text-emerald-700 dark:bg-emerald-950/40 dark:text-emerald-300">
                            <span class="material-symbols-outlined">task_alt</span>
                        </div>
                        <h2 class="mt-4 text-xl font-bold text-slate-950 dark:text-white">Lamaran Sudah Terkirim</h2>
                        
                        @php
                            $appStatus = match($application?->status) {
                                'accepted' => ['Diterima', 'bg-emerald-50 text-emerald-700 border-emerald-200 dark:bg-emerald-950/40 dark:text-emerald-300 dark:border-emerald-900', 'task_alt'],
                                'rejected' => ['Belum sesuai', 'bg-rose-50 text-rose-700 border-rose-200 dark:bg-rose-950/40 dark:text-rose-300 dark:border-rose-900', 'cancel'],
                                'interview' => ['Tahap Wawancara', 'bg-blue-50 text-blue-700 border-blue-200 dark:bg-blue-950/40 dark:text-blue-300 dark:border-blue-900', 'forum'],
                                default => ['Menunggu Tinjauan', 'bg-amber-50 text-amber-700 border-amber-200 dark:bg-amber-950/40 dark:text-amber-300 dark:border-amber-900', 'hourglass_top'],
                            };
                        @endphp
                        
                        <div class="mt-3 p-3.5 rounded-2xl border bg-slate-50 dark:bg-slate-950/60 border-slate-200/80 dark:border-slate-800 space-y-2">
                            <div class="flex items-center justify-between">
                                <span class="text-xs text-slate-500">Status Seleksi:</span>
                                <span class="inline-flex items-center gap-1 rounded-lg border px-2.5 py-0.5 text-xs font-bold {{ $appStatus[1] }}">
                                    <span class="material-symbols-outlined text-[15px]">{{ $appStatus[2] }}</span>
                                    {{ $appStatus[0] }}
                                </span>
                            </div>
                            @if($application?->created_at)
                                <div class="flex items-center justify-between text-xs text-slate-500">
                                    <span>Terkirim pada:</span>
                                    <span class="font-semibold text-slate-700 dark:text-slate-300">{{ $application->created_at->translatedFormat('d M Y, H:i') }} WIB</span>
                                </div>
                            @endif
                        </div>

                        <p class="mt-3 text-xs leading-relaxed text-slate-500">
                            Anda tetap dapat memantau rincian tugas, upah, dan kriteria lowongan ini kapan saja selama proses seleksi berlangsung.
                        </p>

                        <div class="mt-5 space-y-2">
                            <a href="{{ route('applications.index') }}" class="portal-button-primary w-full justify-center gap-2">
                                <span class="material-symbols-outlined text-[18px]">history_edu</span>
                                Cek Riwayat Lamaran
                            </a>
                            <a href="{{ route('chat.index', ['user' => $job->employer_id]) }}" class="portal-button-secondary w-full justify-center gap-2">
                                <span class="material-symbols-outlined text-[18px]">chat</span>
                                Hubungi Mitra Terkait
                            </a>
                            @if($application)
                                <form id="cancelApplicationForm-{{ $application->id }}" action="{{ route('applications.destroy', $application) }}" method="POST" class="pt-1">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" onclick="confirmCancelJobApplication('{{ $application->id }}', '{{ addslashes($job->title) }}')" class="flex w-full items-center justify-center gap-1.5 rounded-xl border border-rose-200 bg-rose-50/70 py-2.5 px-3 text-xs font-bold text-rose-700 hover:bg-rose-100 hover:border-rose-300 dark:border-rose-900/60 dark:bg-rose-950/40 dark:text-rose-300 dark:hover:bg-rose-900/50 transition cursor-pointer">
                                        <span class="material-symbols-outlined text-[17px]">cancel</span>
                                        Batalkan Lamaran
                                    </button>
                                </form>
                            @endif
                        </div>
                    @elseif($job->status === 'open')
                        <h2 class="text-xl font-bold">Ajukan lamaran</h2><p class="mt-1 text-sm text-slate-500">Resume disimpan privat dan hanya dapat diunduh mitra pemilik lowongan serta admin.</p>
                        <form action="{{ route('applications.store', $job) }}" method="POST" enctype="multipart/form-data" class="mt-6 space-y-5">@csrf
                            <div><label for="resume" class="mb-2 block text-sm font-bold">Resume PDF</label><input id="resume" name="resume" type="file" accept="application/pdf,.pdf" required class="block w-full rounded-xl border border-slate-200 bg-slate-50 text-xs file:mr-3 file:border-0 file:bg-brand-700 file:px-3 file:py-3 file:font-bold file:text-white dark:border-slate-700 dark:bg-slate-950"><p class="mt-1.5 text-xs text-slate-400">Maksimal 2 MB.</p>@error('resume')<p class="mt-1.5 text-xs font-semibold text-rose-600" role="alert">{{ $message }}</p>@enderror</div>
                            <div><label for="note" class="mb-2 block text-sm font-bold">Catatan singkat</label><textarea id="note" name="note" rows="4" class="w-full rounded-xl border border-slate-200 bg-slate-50 p-3 text-sm outline-none focus:border-brand-500 focus:ring-4 focus:ring-brand-100 dark:border-slate-700 dark:bg-slate-950 dark:focus:ring-brand-900" placeholder="Ceritakan pengalaman yang relevan">{{ old('note') }}</textarea>@error('note')<p class="mt-1.5 text-xs font-semibold text-rose-600" role="alert">{{ $message }}</p>@enderror</div>
                            <button class="portal-button-primary w-full"><span class="material-symbols-outlined text-[18px]">send</span>Kirim lamaran</button>
                        </form>
                    @else
                        <div class="grid h-12 w-12 place-items-center rounded-2xl bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-300">
                            <span class="material-symbols-outlined text-[24px]">lock</span>
                        </div>
                        <h2 class="mt-4 text-xl font-bold">Lowongan Ditutup</h2>
                        <p class="mt-2 text-sm leading-6 text-slate-500 dark:text-slate-400">
                            @if($job->isClosedByAdmin())
                                Lowongan ini dinonaktifkan oleh Administrator. {{ $job->closed_reason ? 'Keterangan: "'.$job->closed_reason.'"' : '' }}
                            @else
                                Mitra saat ini tidak lagi menerima lamaran baru untuk posisi pekerjaan ini.
                            @endif
                        </p>
                    @endif
                @else
                    @if(auth()->user()->id === $job->employer_id)
                        <div class="grid h-12 w-12 place-items-center rounded-2xl bg-brand-50 text-brand-700 dark:bg-brand-950 dark:text-brand-300">
                            <span class="material-symbols-outlined text-[24px]">storefront</span>
                        </div>
                        <h2 class="mt-4 text-xl font-bold text-slate-950 dark:text-white">Lowongan Milik Anda</h2>
                        <p class="mt-1.5 text-sm leading-6 text-slate-500 dark:text-slate-400">Anda adalah pemilik lowongan ini. Anda dapat memperbarui informasi, meninjau pelamar, atau mengubah status rekrutmen.</p>

                        <div class="mt-4 p-3.5 rounded-2xl border bg-slate-50 dark:bg-slate-950/60 border-slate-200/80 dark:border-slate-800 space-y-2 text-xs">
                            <div class="flex items-center justify-between">
                                <span class="text-slate-500">Status Rekrutmen:</span>
                                @if($job->status === 'open')
                                    <span class="inline-flex items-center gap-1 font-bold text-emerald-700 dark:text-emerald-300 bg-emerald-50 dark:bg-emerald-950/40 px-2.5 py-0.5 rounded-lg border border-emerald-200 dark:border-emerald-900">
                                        <span class="material-symbols-outlined text-[15px]">check_circle</span>
                                        Sedang Dibuka
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 font-bold text-slate-700 dark:text-slate-300 bg-slate-100 dark:bg-slate-800 px-2.5 py-0.5 rounded-lg border border-slate-200 dark:border-slate-700">
                                        <span class="material-symbols-outlined text-[15px]">lock</span>
                                        Sedang Ditutup
                                    </span>
                                @endif
                            </div>
                            <div class="flex items-center justify-between text-slate-500">
                                <span>Total Pelamar:</span>
                                <strong class="text-slate-800 dark:text-slate-200 font-semibold">{{ $job->applications_count }} orang</strong>
                            </div>
                        </div>

                        <div class="mt-5 space-y-2.5">
                            <a href="{{ route('employer.jobs.edit', $job) }}" class="portal-button-primary w-full justify-center gap-2">
                                <span class="material-symbols-outlined text-[19px]">edit_note</span>
                                Edit Lowongan Ini
                            </a>
                            <a href="{{ route('employer.applications.index', ['job' => $job->id]) }}" class="portal-button-secondary w-full justify-center gap-2">
                                <span class="material-symbols-outlined text-[19px]">group</span>
                                Tinjau Pelamar Masuk ({{ $job->applications_count }})
                            </a>

                            @if($job->status === 'open')
                                <form id="closeEmployerJobForm" action="{{ route('employer.jobs.status', $job) }}" method="POST" class="pt-1">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="status" value="closed">
                                    <button type="button" onclick="confirmCloseEmployerJob('{{ addslashes($job->title) }}')" class="flex w-full items-center justify-center gap-1.5 rounded-xl border border-amber-300 bg-amber-50/80 py-2.5 px-3 text-xs font-bold text-amber-800 hover:bg-amber-100 hover:border-amber-400 dark:border-amber-800/60 dark:bg-amber-950/40 dark:text-amber-300 dark:hover:bg-amber-900/50 transition cursor-pointer">
                                        <span class="material-symbols-outlined text-[17px]">lock</span>
                                        Tutup Lowongan Ini
                                    </button>
                                </form>
                            @else
                                @if(! $job->isClosedByAdmin())
                                    <form id="openEmployerJobForm" action="{{ route('employer.jobs.status', $job) }}" method="POST" class="pt-1">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="status" value="open">
                                        <button type="button" onclick="confirmOpenEmployerJob('{{ addslashes($job->title) }}')" class="flex w-full items-center justify-center gap-1.5 rounded-xl border border-emerald-300 bg-emerald-50/80 py-2.5 px-3 text-xs font-bold text-emerald-800 hover:bg-emerald-100 hover:border-emerald-400 dark:border-emerald-800/60 dark:bg-emerald-950/40 dark:text-emerald-300 dark:hover:bg-emerald-900/50 transition cursor-pointer">
                                            <span class="material-symbols-outlined text-[17px]">lock_open</span>
                                            Buka Kembali Lowongan Ini
                                        </button>
                                    </form>
                                @else
                                    <div class="mt-2 rounded-xl bg-rose-50 p-2.5 text-center text-xs font-semibold text-rose-700 dark:bg-rose-950/40 dark:text-rose-300">
                                        Lowongan dinonaktifkan oleh Administrator
                                    </div>
                                @endif
                            @endif
                        </div>
                    @else
                        <h2 class="text-xl font-bold text-slate-950 dark:text-white">Tampilan informasi</h2>
                        <p class="mt-2 text-sm leading-6 text-slate-500 dark:text-slate-400">Akun {{ auth()->user()->hasRole('admin') ? 'administrator' : 'mitra lain' }} dapat melihat detail lowongan tanpa mengajukan lamaran.</p>
                    @endif
                @endguest

                {{-- Status Pembaruan Real-Time --}}
                <div class="mt-6 border-t border-slate-100 pt-4 dark:border-slate-800">
                    <div class="flex items-center gap-2 text-xs font-semibold text-slate-600 dark:text-slate-300">
                        <span class="relative flex h-2 w-2">
                            <span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-emerald-400 opacity-75"></span>
                            <span class="relative inline-flex h-2 w-2 rounded-full bg-emerald-500"></span>
                        </span>
                        <span>Status Pembaruan Mitra:</span>
                    </div>
                    <p class="mt-1.5 text-sm font-bold text-slate-900 dark:text-white">
                        Diperbarui <span data-relative-time="{{ $job->updated_at->toISOString() }}">{{ $job->updated_at->diffForHumans() }}</span>
                    </p>
                    <p class="mt-0.5 text-xs text-slate-400 dark:text-slate-500">
                        {{ $job->updated_at->translatedFormat('d F Y, H:i') }} WIB
                    </p>
                </div>

                {{-- Laporkan Lowongan Button --}}
                @auth
                    @if(auth()->user()->id !== $job->employer_id && !auth()->user()->hasRole('admin'))
                        <div class="mt-4 border-t border-slate-100 pt-3 dark:border-slate-800">
                            <button type="button" onclick="openReportModal()" class="inline-flex items-center gap-1.5 text-xs font-semibold text-slate-500 transition hover:text-rose-600 dark:text-slate-400 dark:hover:text-rose-400">
                                <span class="material-symbols-outlined text-[16px]">flag</span>
                                Laporkan Lowongan Ini
                            </button>
                        </div>
                    @endif
                @else
                    <div class="mt-4 border-t border-slate-100 pt-3 dark:border-slate-800">
                        <a href="{{ route('login') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-slate-400 transition hover:text-rose-600">
                            <span class="material-symbols-outlined text-[16px]">flag</span>
                            Masuk untuk melaporkan indikasi pelanggaran
                        </a>
                    </div>
                @endauth
            </div>
        </aside>
    </div>
@endsection

@push('modals')
    <!-- Modal Laporkan Lowongan -->
    <!-- ================= MODAL LAPORKAN LOWONGAN ================= -->
    @auth
        <div id="reportJobModal" class="fixed inset-0 z-[100] hidden bg-slate-950/60 backdrop-blur-sm p-4 overflow-y-auto flex items-center justify-center" onclick="if(event.target === this) closeReportModal()">
            <div class="relative w-full max-w-lg rounded-3xl bg-white p-6 sm:p-7 shadow-2xl shadow-slate-950/20 dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 animate-modal-pop">
                <div class="flex items-start justify-between gap-3 pb-5 border-b border-slate-100 dark:border-slate-800">
                    <div class="flex items-center gap-3.5">
                        <div class="w-11 h-11 rounded-2xl bg-rose-50 dark:bg-rose-950/50 text-rose-600 dark:text-rose-400 border border-rose-100 dark:border-rose-900/40 flex items-center justify-center shrink-0">
                            <span class="material-symbols-outlined text-[24px]">flag</span>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-slate-900 dark:text-white leading-tight">Laporkan Lowongan</h3>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Aduan ditinjau secara independen oleh Tim Pengawas KerjaLokal.</p>
                        </div>
                    </div>
                    <button type="button" onclick="closeReportModal()" class="w-8 h-8 rounded-xl text-slate-400 hover:text-slate-700 hover:bg-slate-100 dark:hover:text-white dark:hover:bg-slate-800 flex items-center justify-center transition">
                        <span class="material-symbols-outlined text-[20px]">close</span>
                    </button>
                </div>

                <form action="{{ route('jobs.report', $job) }}" method="POST" class="mt-5 space-y-4">
                    @csrf
                    <div>
                        <label for="report_reason" class="portal-label text-xs uppercase tracking-wider text-slate-500 dark:text-slate-400">
                            Kategori Dugaan Pelanggaran <span class="text-rose-500">*</span>
                        </label>
                        <select id="report_reason" name="reason" required class="w-full rounded-2xl border border-slate-200 bg-slate-50/70 px-3.5 py-2.5 text-sm font-medium text-slate-900 focus:bg-white focus:border-rose-500 focus:ring-4 focus:ring-rose-500/10 focus:outline-none transition dark:border-slate-700 dark:bg-slate-950/70 dark:text-slate-100 dark:focus:bg-slate-950">
                            <option value="">-- Pilih alasan pengaduan --</option>
                            <option value="Indikasi Percaloan / Pungutan Biaya Administrasi">Indikasi Calo / Pungutan Liar Biaya Masuk</option>
                            <option value="Upah Tidak Transparan / Di Bawah Standar">Upah Tidak Transparan / Di Bawah Standar Layak</option>
                            <option value="Jam Kerja Melebihi Batas Etis (>8 Jam/Hari)">Jam Kerja Melebihi Batas Etis Tanpa Lembur</option>
                            <option value="Identitas Usaha Palsu / Dugaan Penipuan">Identitas Usaha Palsu / Dugaan Penipuan</option>
                            <option value="Diskriminasi atau Pelanggaran Etika Lainnya">Diskriminasi SARA atau Pelanggaran Etika Lainnya</option>
                        </select>
                    </div>

                    <div>
                        <label for="report_details" class="portal-label text-xs uppercase tracking-wider text-slate-500 dark:text-slate-400">
                            Kronologi &amp; Rincian Aduan <span class="text-rose-500">*</span>
                        </label>
                        <textarea id="report_details" name="details" rows="4" required minlength="10" placeholder="Ceritakan bukti atau indikasi yang Anda temukan (minimal 10 karakter)..." class="w-full rounded-2xl border border-slate-200 bg-slate-50/70 p-3.5 text-sm text-slate-900 placeholder:text-slate-400 focus:bg-white focus:border-rose-500 focus:ring-4 focus:ring-rose-500/10 focus:outline-none transition dark:border-slate-700 dark:bg-slate-950/70 dark:text-slate-100 dark:focus:bg-slate-950"></textarea>
                        <p class="mt-1 text-[11px] text-slate-400">Laporan Anda akan ditinjau secara rahasia demi perlindungan pelamar kerja.</p>
                    </div>

                    <div class="pt-4 flex items-center justify-end gap-2.5 border-t border-slate-100 dark:border-slate-800">
                        <button type="button" onclick="closeReportModal()" class="inline-flex h-10 items-center justify-center rounded-xl border border-slate-200 bg-white px-4 text-xs sm:text-sm font-semibold text-slate-700 shadow-xs hover:bg-slate-50 hover:border-slate-300 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 transition">
                            Batal
                        </button>
                        <button type="submit" class="inline-flex h-10 items-center justify-center gap-1.5 rounded-xl bg-rose-600 px-5 text-xs sm:text-sm font-semibold text-white shadow-sm shadow-rose-600/25 hover:bg-rose-700 active:translate-y-px transition">
                            <span class="material-symbols-outlined text-[18px]">send</span>
                            Kirim Laporan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endauth
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const updateRelativeTimes = () => {
            document.querySelectorAll('[data-relative-time]').forEach(el => {
                const dateStr = el.dataset.relativeTime;
                if (!dateStr) return;
                const past = new Date(dateStr);
                const now = new Date();
                const diffSec = Math.floor((now - past) / 1000);

                if (diffSec < 5) {
                    el.textContent = 'baru saja';
                } else if (diffSec < 60) {
                    el.textContent = `${diffSec} detik yang lalu`;
                } else if (diffSec < 3600) {
                    const m = Math.floor(diffSec / 60);
                    el.textContent = `${m} menit yang lalu`;
                } else if (diffSec < 86400) {
                    const h = Math.floor(diffSec / 3600);
                    el.textContent = `${h} jam yang lalu`;
                } else if (diffSec < 2592000) {
                    const d = Math.floor(diffSec / 86400);
                    el.textContent = `${d} hari yang lalu`;
                }
            });
        };

        updateRelativeTimes();
        setInterval(updateRelativeTimes, 5000);
    });

    function openReportModal() {
        const modal = document.getElementById('reportJobModal');
        if (modal) {
            modal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }
    }

    function closeReportModal() {
        const modal = document.getElementById('reportJobModal');
        if (modal) {
            modal.classList.add('hidden');
            document.body.style.overflow = '';
        }
    }

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            closeReportModal();
        }
    });

    async function confirmCancelJobApplication(applicationId, jobTitle) {
        const confirmed = await window.showAppConfirm({
            title: 'Batalkan Lamaran Pekerjaan?',
            message: `Apakah Anda yakin ingin membatalkan lamaran untuk posisi "${jobTitle}"?\n\nBerkas resume Anda akan ditarik dari daftar peninjauan mitra UMKM dan lamaran ini akan dihapus dari riwayat Anda.`,
            confirmText: 'Ya, Batalkan Lamaran',
            cancelText: 'Kembali',
            type: 'danger',
            icon: 'cancel'
        });

        if (confirmed) {
            document.getElementById(`cancelApplicationForm-${applicationId}`)?.submit();
        }
    }

    async function confirmCloseEmployerJob(jobTitle) {
        const confirmed = await window.showAppConfirm({
            title: 'Tutup Lowongan Pekerjaan?',
            message: `Apakah Anda yakin ingin menutup rekrutmen untuk lowongan "${jobTitle}"?\n\nSetelah ditutup, lowongan ini tidak akan menerima pelamar baru dan disembunyikan dari daftar pencarian aktif.`,
            confirmText: 'Ya, Tutup Lowongan',
            cancelText: 'Batal',
            type: 'warning',
            icon: 'lock'
        });

        if (confirmed) {
            document.getElementById('closeEmployerJobForm')?.submit();
        }
    }

    async function confirmOpenEmployerJob(jobTitle) {
        const confirmed = await window.showAppConfirm({
            title: 'Buka Kembali Lowongan?',
            message: `Apakah Anda yakin ingin membuka kembali rekrutmen untuk lowongan "${jobTitle}"?\n\nPencari kerja akan dapat melihat kembali lowongan ini dan mengirimkan lamaran.`,
            confirmText: 'Ya, Buka Lowongan',
            cancelText: 'Batal',
            type: 'primary',
            icon: 'lock_open'
        });

        if (confirmed) {
            document.getElementById('openEmployerJobForm')?.submit();
        }
    }
</script>
@endpush
