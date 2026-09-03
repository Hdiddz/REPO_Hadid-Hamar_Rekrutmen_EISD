@extends('layouts.admin')

@section('title', 'Detail Pengguna: ' . $user->name . ' | Admin KerjaLokal')
@section('portal_title', $user->name)
@section('portal_description', 'Detail profil, kredensial, status kepatuhan, serta riwayat seluruh aktivitas lowongan atau lamaran.')

@section('portal_actions')
    <a href="{{ route('admin.users.index') }}" class="portal-action-btn">
        <span class="material-symbols-outlined text-[17px]">arrow_back</span>
        Kembali
    </a>

    @if(!$user->hasRole('admin'))
        <button type="button" onclick="openManageAccountModal()" class="portal-action-btn-primary">
            <span class="material-symbols-outlined text-[17px]">manage_accounts</span>
            Kelola Akun
        </button>
    @endif
@endsection

@section('content')
    <div class="grid gap-6 lg:grid-cols-[360px_1fr]">
        
        <!-- Left Column: User Profile Card -->
        <div class="space-y-6">
            <div class="portal-panel p-6">
                <div class="flex items-center gap-4">
                    <div class="w-14 h-14 rounded-2xl bg-brand-50 text-brand-700 dark:bg-brand-950 dark:text-brand-300 font-black text-xl flex items-center justify-center shrink-0">
                        {{ strtoupper(substr($user->name, 0, 2)) }}
                    </div>
                    <div class="min-w-0">
                        <h2 class="font-bold text-lg text-slate-900 dark:text-white truncate">{{ $user->name }}</h2>
                        <p class="text-xs font-mono font-semibold text-brand-700 dark:text-brand-300">@<span>{{ $user->username ?? '-' }}</span></p>
                        <span class="portal-badge mt-1.5 {{ match($user->role) {'jobseeker' => 'bg-emerald-50 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300', 'employer' => 'bg-blue-50 text-blue-800 dark:bg-blue-950 dark:text-blue-300', default => 'bg-purple-50 text-purple-800 dark:bg-purple-950 dark:text-purple-300'} }}">
                            {{ match($user->role) {'jobseeker' => 'Pencari kerja', 'employer' => 'Mitra UMKM', default => 'Administrator'} }}
                        </span>
                    </div>
                </div>

                <!-- Status Banner -->
                <div class="mt-6 pt-6 border-t border-slate-100 dark:border-slate-800">
                    @if($user->isBanned())
                        <div class="rounded-2xl bg-rose-50 p-4 border border-rose-200 dark:bg-rose-950/40 dark:border-rose-900/50">
                            <div class="flex items-center gap-2 text-rose-700 dark:text-rose-300 font-bold text-sm">
                                <span class="material-symbols-outlined text-lg">error</span>
                                <span>Akun Sedang Dibekukan</span>
                            </div>
                            <p class="mt-2 text-xs text-rose-800 dark:text-rose-200">
                                <strong>Masa berlaku:</strong> {{ $user->ban_status_text }}
                            </p>
                            @if($user->ban_reason)
                                <p class="mt-1 text-xs text-rose-700/90 dark:text-rose-300/90 italic">
                                    "{{ $user->ban_reason }}"
                                </p>
                            @endif
                        </div>
                    @else
                        <div class="rounded-2xl bg-emerald-50 p-4 border border-emerald-200 dark:bg-emerald-950/40 dark:border-emerald-900/50 flex items-center gap-3">
                            <span class="material-symbols-outlined text-emerald-600 text-2xl">verified_user</span>
                            <div>
                                <span class="font-bold text-sm text-emerald-900 dark:text-emerald-200 block">Status Akun Aktif</span>
                                <span class="text-xs text-emerald-700 dark:text-emerald-400">Dapat mengakses seluruh fitur sistem.</span>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Metadata List -->
                <dl class="mt-6 space-y-4 text-sm">
                    <div>
                        <dt class="text-xs text-slate-400">Username Akun</dt>
                        <dd class="mt-1 font-mono font-bold text-brand-700 dark:text-brand-300">@<span>{{ $user->username ?? '-' }}</span></dd>
                    </div>
                    <div>
                        <dt class="text-xs text-slate-400">Alamat Email</dt>
                        <dd class="mt-1 font-semibold break-all">{{ $user->email }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs text-slate-400">Nomor Telepon</dt>
                        <dd class="mt-1 font-semibold">{{ $user->phone ?: 'Belum dicantumkan' }}</dd>
                    </div>
                    @if($user->role === 'employer')
                        <div>
                            <dt class="text-xs text-slate-400">Nama Usaha / Brand</dt>
                            <dd class="mt-1 font-bold text-brand-700 dark:text-brand-300">{{ $user->business_name ?: 'Belum diisi' }}</dd>
                        </div>
                    @endif
                    <div>
                        <dt class="text-xs text-slate-400">Tanggal Terdaftar</dt>
                        <dd class="mt-1 font-semibold">{{ $user->created_at->translatedFormat('d F Y, H:i') }} WIB</dd>
                    </div>
                </dl>
            </div>
        </div>

        <!-- Right Column: Activity History -->
        <div class="space-y-6">
            
            @if($user->role === 'employer')
                <!-- Employer Activity: Jobs Created -->
                <div class="portal-panel overflow-hidden">
                    <div class="portal-panel-header">
                        <div>
                            <h3 class="font-bold text-base text-slate-900 dark:text-white">Lowongan Diterbitkan</h3>
                            <p class="text-xs text-slate-500">Total {{ $user->jobs_count }} lowongan dibuat oleh mitra ini.</p>
                        </div>
                    </div>

                    <div class="divide-y divide-slate-100 dark:divide-slate-800">
                        @forelse($user->jobs as $job)
                            <div class="p-5 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                                <div class="min-w-0">
                                    <div class="flex items-center gap-2">
                                        <span class="portal-badge bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300 text-[10px]">
                                            {{ $job->category->name }}
                                        </span>
                                        <span class="portal-badge {{ $job->status === 'open' ? 'bg-emerald-50 text-emerald-700' : 'bg-rose-50 text-rose-700' }} text-[10px]">
                                            {{ $job->isClosedByAdmin() ? 'Ditutup Admin' : ($job->status === 'open' ? 'Dibuka' : 'Ditutup') }}
                                        </span>
                                    </div>
                                    <h4 class="mt-1 font-bold text-sm text-slate-900 dark:text-white truncate">
                                        <a href="{{ route('admin.jobs.show', $job) }}" class="hover:text-brand-700">
                                            {{ $job->title }}
                                        </a>
                                    </h4>
                                    <p class="mt-0.5 text-xs text-slate-500">
                                        {{ $job->location }} · Rp {{ number_format($job->salary_amount, 0, ',', '.') }} · {{ $job->applications_count }} Pelamar
                                    </p>
                                </div>

                                <div class="flex items-center gap-2 shrink-0">
                                    <a href="{{ route('admin.jobs.show', $job) }}" class="portal-button-secondary !py-1 !px-2.5 text-xs">
                                        Detail &amp; Pelamar
                                    </a>
                                    <a href="{{ route('admin.jobs.edit', $job) }}" class="portal-button-secondary !py-1 !px-2 text-xs">
                                        <span class="material-symbols-outlined text-[16px]">edit</span>
                                    </a>
                                </div>
                            </div>
                        @empty
                            <div class="p-8 text-center text-sm text-slate-500">
                                Mitra ini belum pernah mempublikasikan lowongan pekerjaan.
                            </div>
                        @endforelse
                    </div>
                </div>

            @elseif($user->role === 'jobseeker')
                <!-- Jobseeker Activity: Applications Submitted -->
                <div class="portal-panel overflow-hidden">
                    <div class="portal-panel-header">
                        <div>
                            <h3 class="font-bold text-base text-slate-900 dark:text-white">Riwayat Lamaran Diajukan</h3>
                            <p class="text-xs text-slate-500">Total {{ $user->job_applications_count }} lamaran pekerjaan oleh pencari kerja ini.</p>
                        </div>
                    </div>

                    <div class="divide-y divide-slate-100 dark:divide-slate-800">
                        @forelse($user->jobApplications as $app)
                            <div class="p-5 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                                <div class="min-w-0">
                                    <div class="flex items-center gap-2">
                                        <span class="portal-badge {{ match($app->status) {'accepted' => 'bg-emerald-50 text-emerald-800', 'rejected' => 'bg-rose-50 text-rose-800', 'reviewed' => 'bg-blue-50 text-blue-800', default => 'bg-amber-50 text-amber-800'} }} text-[10px]">
                                            {{ match($app->status) {'accepted' => 'Diterima', 'rejected' => 'Ditolak', 'reviewed' => 'Ditinjau', default => 'Pending'} }}
                                        </span>
                                        <span class="text-xs text-slate-400">{{ $app->created_at->translatedFormat('d M Y') }}</span>
                                    </div>
                                    <h4 class="mt-1 font-bold text-sm text-slate-900 dark:text-white truncate">
                                        {{ $app->job->title }}
                                    </h4>
                                    <p class="mt-0.5 text-xs text-slate-500">
                                        Mitra: <strong>{{ $app->job->employer->business_name ?: $app->job->employer->name }}</strong>
                                    </p>
                                </div>

                                <div class="flex items-center gap-2 shrink-0">
                                    <a href="{{ route('admin.jobs.show', $app->job) }}" class="portal-button-secondary !py-1 !px-2.5 text-xs">
                                        Lihat Lowongan
                                    </a>
                                    @if($app->resume_file)
                                        <button type="button" onclick="openPdfViewer('{{ route('admin.applications.resume.preview', $app) }}', '{{ addslashes($user->name) }}', '{{ route('admin.applications.resume', $app) }}')" class="portal-button-primary !py-1 !px-2.5 text-xs gap-1 cursor-pointer">
                                            <span class="material-symbols-outlined text-[16px]">visibility</span>
                                            Lihat
                                        </button>
                                        <a href="{{ route('admin.applications.resume', $app) }}" class="portal-button-secondary !py-1 !px-2.5 text-xs">
                                            <span class="material-symbols-outlined text-[16px]">download</span>
                                            Unduh
                                        </a>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <div class="p-8 text-center text-sm text-slate-500">
                                Pengguna ini belum pernah mengajukan lamaran pekerjaan.
                            </div>
                        @endforelse
                    </div>
                </div>
            @endif

            <!-- Reports Submitted by this User -->
            @if($user->submittedReports->count() > 0)
                <div class="portal-panel overflow-hidden">
                    <div class="portal-panel-header">
                        <div>
                            <h3 class="font-bold text-base text-slate-900 dark:text-white">Laporan Pelanggaran yang Dikirim</h3>
                            <p class="text-xs text-slate-500">Pengguna ini telah melaporkan {{ $user->submittedReports->count() }} lowongan.</p>
                        </div>
                    </div>
                    <div class="divide-y divide-slate-100 dark:divide-slate-800">
                        @foreach($user->submittedReports as $report)
                            <div class="p-4 flex items-center justify-between gap-3">
                                <div>
                                    <p class="text-xs font-bold text-slate-900 dark:text-white">{{ $report->reason }}</p>
                                    <p class="text-[11px] text-slate-500">Lowongan: {{ $report->job->title }} · {{ $report->created_at->diffForHumans() }}</p>
                                </div>
                                <a href="{{ route('admin.reports.show', $report) }}" class="portal-button-secondary !py-1 !px-2 text-xs">
                                    Tinjau Laporan
                                </a>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

        </div>

    </div>
@endsection

@push('modals')
    <!-- ================= MODAL KELOLA AKUN (UNIFIED) ================= -->
    <div id="manageAccountModal" class="fixed inset-0 z-[100] hidden bg-slate-950/60 backdrop-blur-sm p-4 overflow-y-auto flex items-center justify-center" onclick="if(event.target === this) closeManageAccountModal()">
        <div class="relative w-full max-w-xl rounded-3xl bg-white p-6 sm:p-7 shadow-2xl shadow-slate-950/20 dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 animate-modal-pop">
            
            <!-- Modal Header -->
            <div class="flex items-start justify-between gap-3 pb-4 border-b border-slate-100 dark:border-slate-800">
                <div class="flex items-center gap-3.5">
                    <div class="w-11 h-11 rounded-2xl bg-brand-50 text-brand-700 dark:bg-brand-950/50 dark:text-brand-300 border border-brand-100 dark:border-brand-900/40 flex items-center justify-center shrink-0">
                        <span class="material-symbols-outlined text-[24px]">manage_accounts</span>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-slate-900 dark:text-white leading-tight">Kelola Akun: {{ $user->name }}</h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5 font-mono">@<span>{{ $user->username }}</span> &bull; {{ match($user->role) {'employer' => 'Mitra UMKM', 'jobseeker' => 'Pencari Kerja', default => 'Administrator'} }}</p>
                    </div>
                </div>
                <button type="button" onclick="closeManageAccountModal()" class="w-8 h-8 rounded-xl text-slate-400 hover:text-slate-700 hover:bg-slate-100 dark:hover:text-white dark:hover:bg-slate-800 flex items-center justify-center transition" aria-label="Tutup modal">
                    <span class="material-symbols-outlined text-[20px]">close</span>
                </button>
            </div>

            <!-- Tab Switcher -->
            <div class="mt-5 flex rounded-2xl bg-slate-100 p-1 dark:bg-slate-800/80 gap-1 text-xs font-semibold">
                <button type="button" onclick="switchManageTab('profile')" id="tabBtn-profile" class="manage-tab-btn flex-1 py-2 px-3 rounded-xl transition inline-flex items-center justify-center gap-1.5 bg-white text-slate-900 shadow-xs dark:bg-slate-900 dark:text-white">
                    <span class="material-symbols-outlined text-[16px]">badge</span>
                    Profil &amp; Username
                </button>
                <button type="button" onclick="switchManageTab('password')" id="tabBtn-password" class="manage-tab-btn flex-1 py-2 px-3 rounded-xl transition inline-flex items-center justify-center gap-1.5 text-slate-600 hover:text-slate-900 dark:text-slate-400 dark:hover:text-white">
                    <span class="material-symbols-outlined text-[16px]">key</span>
                    Kata Sandi
                </button>
                <button type="button" onclick="switchManageTab('sanction')" id="tabBtn-sanction" class="manage-tab-btn flex-1 py-2 px-3 rounded-xl transition inline-flex items-center justify-center gap-1.5 text-slate-600 hover:text-slate-900 dark:text-slate-400 dark:hover:text-white">
                    <span class="material-symbols-outlined text-[16px]">gavel</span>
                    Status &amp; Ban
                </button>
                <button type="button" onclick="switchManageTab('delete')" id="tabBtn-delete" class="manage-tab-btn flex-1 py-2 px-3 rounded-xl transition inline-flex items-center justify-center gap-1.5 text-rose-600 hover:text-rose-700 dark:text-rose-400">
                    <span class="material-symbols-outlined text-[16px]">delete_forever</span>
                    Hapus Akun
                </button>
            </div>

            <!-- Tab 1: Profil & Username -->
            <div id="tabContent-profile" class="mt-5 space-y-4">
                <form action="{{ route('admin.users.account', $user) }}" method="POST" class="space-y-4">
                    @csrf
                    @method('PUT')

                    <div>
                        <label for="manage_username" class="portal-label text-xs uppercase tracking-wider text-slate-500 dark:text-slate-400">
                            Username Akun <span class="text-rose-500">*</span>
                        </label>
                        <div class="relative flex items-center">
                            <span class="pointer-events-none absolute left-3.5 text-sm font-bold text-slate-400 dark:text-slate-500">@</span>
                            <input type="text" id="manage_username" name="username" required value="{{ old('username', $user->username) }}" placeholder="username_unik" class="w-full rounded-2xl border border-slate-200 bg-slate-50/70 pl-8 pr-3.5 py-2.5 text-sm font-medium text-slate-900 focus:bg-white focus:border-brand-600 focus:ring-4 focus:ring-brand-500/10 focus:outline-none transition dark:border-slate-700 dark:bg-slate-950/70 dark:text-slate-100 dark:focus:bg-slate-950 {{ $errors->has('username') ? 'border-rose-500' : '' }}">
                        </div>
                        @error('username')
                            <p class="mt-1 text-xs font-semibold text-rose-600">{{ $message }}</p>
                        @else
                            <p class="mt-1 text-[11px] text-slate-400">Harus unik. Hanya huruf, angka, titik (.), tanda hubung (-), dan garis bawah (_).</p>
                        @enderror
                    </div>

                    <div class="grid gap-3 sm:grid-cols-2">
                        <div>
                            <label for="manage_name" class="portal-label text-xs uppercase tracking-wider text-slate-500 dark:text-slate-400">
                                Nama Lengkap <span class="text-rose-500">*</span>
                            </label>
                            <input type="text" id="manage_name" name="name" required value="{{ old('name', $user->name) }}" class="w-full rounded-2xl border border-slate-200 bg-slate-50/70 px-3.5 py-2.5 text-sm font-medium text-slate-900 focus:bg-white focus:border-brand-600 focus:ring-4 focus:ring-brand-500/10 focus:outline-none transition dark:border-slate-700 dark:bg-slate-950/70 dark:text-slate-100 dark:focus:bg-slate-950">
                        </div>
                        <div>
                            <label for="manage_email" class="portal-label text-xs uppercase tracking-wider text-slate-500 dark:text-slate-400">
                                Alamat Email <span class="text-rose-500">*</span>
                            </label>
                            <input type="email" id="manage_email" name="email" required value="{{ old('email', $user->email) }}" class="w-full rounded-2xl border border-slate-200 bg-slate-50/70 px-3.5 py-2.5 text-sm font-medium text-slate-900 focus:bg-white focus:border-brand-600 focus:ring-4 focus:ring-brand-500/10 focus:outline-none transition dark:border-slate-700 dark:bg-slate-950/70 dark:text-slate-100 dark:focus:bg-slate-950">
                        </div>
                    </div>

                    <div class="grid gap-3 sm:grid-cols-2">
                        <div>
                            <label for="manage_phone" class="portal-label text-xs uppercase tracking-wider text-slate-500 dark:text-slate-400">
                                Nomor Telepon
                            </label>
                            <input type="text" id="manage_phone" name="phone" value="{{ old('phone', $user->phone) }}" placeholder="08..." class="w-full rounded-2xl border border-slate-200 bg-slate-50/70 px-3.5 py-2.5 text-sm font-medium text-slate-900 focus:bg-white focus:border-brand-600 focus:ring-4 focus:ring-brand-500/10 focus:outline-none transition dark:border-slate-700 dark:bg-slate-950/70 dark:text-slate-100 dark:focus:bg-slate-950">
                        </div>
                        @if($user->role === 'employer')
                            <div>
                                <label for="manage_business_name" class="portal-label text-xs uppercase tracking-wider text-slate-500 dark:text-slate-400">
                                    Nama Usaha / Brand
                                </label>
                                <input type="text" id="manage_business_name" name="business_name" value="{{ old('business_name', $user->business_name) }}" class="w-full rounded-2xl border border-slate-200 bg-slate-50/70 px-3.5 py-2.5 text-sm font-medium text-slate-900 focus:bg-white focus:border-brand-600 focus:ring-4 focus:ring-brand-500/10 focus:outline-none transition dark:border-slate-700 dark:bg-slate-950/70 dark:text-slate-100 dark:focus:bg-slate-950">
                            </div>
                        @endif
                    </div>

                    <div class="pt-4 flex items-center justify-end gap-2.5 border-t border-slate-100 dark:border-slate-800">
                        <button type="button" onclick="closeManageAccountModal()" class="inline-flex h-10 items-center justify-center rounded-xl border border-slate-200 bg-white px-4 text-xs sm:text-sm font-semibold text-slate-700 shadow-xs hover:bg-slate-50 hover:border-slate-300 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 transition cursor-pointer">
                            Tutup
                        </button>
                        <button type="submit" class="inline-flex h-10 items-center justify-center gap-1.5 rounded-xl bg-brand-700 px-5 text-xs sm:text-sm font-semibold text-white shadow-sm shadow-brand-700/25 hover:bg-brand-800 active:translate-y-px transition cursor-pointer">
                            <span class="material-symbols-outlined text-[18px]">check</span>
                            Simpan Perubahan Akun
                        </button>
                    </div>
                </form>
            </div>

            <!-- Tab 2: Kata Sandi -->
            <div id="tabContent-password" class="mt-5 space-y-4 hidden">
                <form action="{{ route('admin.users.password', $user) }}" method="POST" class="space-y-4">
                    @csrf
                    @method('PUT')
                    <div>
                        <label for="tab_new_password" class="portal-label text-xs uppercase tracking-wider text-slate-500 dark:text-slate-400">
                            Kata Sandi Baru <span class="text-rose-500">*</span>
                        </label>
                        <input type="password" id="tab_new_password" name="password" minlength="8" required placeholder="Minimal 8 karakter..." class="w-full rounded-2xl border border-slate-200 bg-slate-50/70 p-3 text-sm text-slate-900 placeholder:text-slate-400 focus:bg-white focus:border-brand-600 focus:ring-4 focus:ring-brand-500/10 focus:outline-none transition dark:border-slate-700 dark:bg-slate-950/70 dark:text-slate-100 dark:focus:bg-slate-950">
                        <p class="mt-1 text-[11px] text-slate-400">Pengguna akan menggunakan kata sandi ini untuk login berikutnya.</p>
                    </div>

                    <div class="pt-4 flex items-center justify-end gap-2.5 border-t border-slate-100 dark:border-slate-800">
                        <button type="button" onclick="closeManageAccountModal()" class="inline-flex h-10 items-center justify-center rounded-xl border border-slate-200 bg-white px-4 text-xs sm:text-sm font-semibold text-slate-700 shadow-xs hover:bg-slate-50 hover:border-slate-300 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 transition cursor-pointer">
                            Tutup
                        </button>
                        <button type="submit" class="inline-flex h-10 items-center justify-center gap-1.5 rounded-xl bg-brand-700 px-5 text-xs sm:text-sm font-semibold text-white shadow-sm shadow-brand-700/25 hover:bg-brand-800 active:translate-y-px transition cursor-pointer">
                            <span class="material-symbols-outlined text-[18px]">key</span>
                            Perbarui Kata Sandi
                        </button>
                    </div>
                </form>
            </div>

            <!-- Tab 3: Status & Sanksi Ban -->
            <div id="tabContent-sanction" class="mt-5 space-y-4 hidden">
                @if($user->isBanned())
                    <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 dark:bg-rose-950/40 dark:border-rose-900/50">
                        <div class="flex items-center gap-2 text-rose-700 dark:text-rose-300 font-bold text-sm">
                            <span class="material-symbols-outlined text-lg">block</span>
                            <span>Akun Saat Ini Sedang Dibekukan</span>
                        </div>
                        <p class="mt-2 text-xs text-rose-800 dark:text-rose-200">
                            <strong>Status Masa Berlaku:</strong> {{ $user->ban_status_text }}
                        </p>
                        @if($user->ban_reason)
                            <p class="mt-1 text-xs text-rose-700/90 dark:text-rose-300/90 italic">
                                Alasan: "{{ $user->ban_reason }}"
                            </p>
                        @endif
                    </div>

                    <form action="{{ route('admin.users.unban', $user) }}" method="POST">
                        @csrf
                        <div class="pt-4 flex items-center justify-end gap-2.5 border-t border-slate-100 dark:border-slate-800">
                            <button type="button" onclick="closeManageAccountModal()" class="inline-flex h-10 items-center justify-center rounded-xl border border-slate-200 bg-white px-4 text-xs sm:text-sm font-semibold text-slate-700 shadow-xs hover:bg-slate-50 hover:border-slate-300 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 transition cursor-pointer">
                                Tutup
                            </button>
                            <button type="submit" class="inline-flex h-10 items-center justify-center gap-1.5 rounded-xl bg-emerald-600 px-5 text-xs sm:text-sm font-semibold text-white shadow-sm shadow-emerald-600/25 hover:bg-emerald-700 active:translate-y-px transition cursor-pointer">
                                <span class="material-symbols-outlined text-[18px]">lock_open</span>
                                Buka Blokir (Unban Akun)
                            </button>
                        </div>
                    </form>
                @else
                    <form action="{{ route('admin.users.ban', $user) }}" method="POST" class="space-y-4">
                        @csrf
                        <div>
                            <label for="ban_reason" class="portal-label text-xs uppercase tracking-wider text-slate-500 dark:text-slate-400">
                                Alasan Pembekuan Akun <span class="text-rose-500">*</span>
                            </label>
                            <textarea id="ban_reason" name="ban_reason" rows="3" required placeholder="Jelaskan alasan pelanggaran aturan atau indikasi kecurangan..." class="w-full rounded-2xl border border-slate-200 bg-slate-50/70 p-3.5 text-sm text-slate-900 placeholder:text-slate-400 focus:bg-white focus:border-rose-500 focus:ring-4 focus:ring-rose-500/10 focus:outline-none transition dark:border-slate-700 dark:bg-slate-950/70 dark:text-slate-100 dark:focus:bg-slate-950"></textarea>
                        </div>

                        <div>
                            <label for="duration_type" class="portal-label text-xs uppercase tracking-wider text-slate-500 dark:text-slate-400">
                                Durasi Pembekuan <span class="text-rose-500">*</span>
                            </label>
                            <select id="duration_type" name="duration_type" required onchange="handleBanDurationChange(this.value)" class="w-full rounded-2xl border border-slate-200 bg-slate-50/70 px-3.5 py-2.5 text-sm font-medium text-slate-900 focus:bg-white focus:border-rose-500 focus:ring-4 focus:ring-rose-500/10 focus:outline-none transition dark:border-slate-700 dark:bg-slate-950/70 dark:text-slate-100 dark:focus:bg-slate-950">
                                <option value="3_days">3 Hari (Peringatan Ringan)</option>
                                <option value="7_days" selected>7 Hari (1 Minggu)</option>
                                <option value="14_days">14 Hari (2 Minggu)</option>
                                <option value="30_days">30 Hari (1 Bulan)</option>
                                <option value="custom">Kustom Jumlah Hari...</option>
                                <option value="permanent">Permanen (Selamanya)</option>
                            </select>
                        </div>

                        <div id="customDaysContainer" class="hidden">
                            <label for="custom_days" class="portal-label text-xs uppercase tracking-wider text-slate-500 dark:text-slate-400">
                                Jumlah Hari Kustom <span class="text-rose-500">*</span>
                            </label>
                            <input type="number" id="custom_days" name="custom_days" min="1" max="3650" placeholder="Contoh: 45" class="w-full rounded-2xl border border-slate-200 bg-slate-50/70 p-3 text-sm text-slate-900 focus:bg-white focus:border-rose-500 focus:ring-4 focus:ring-rose-500/10 focus:outline-none transition dark:border-slate-700 dark:bg-slate-950/70 dark:text-slate-100">
                        </div>

                        @if($user->role === 'employer')
                            <div class="p-3.5 rounded-2xl border border-slate-200/80 bg-slate-50/50 dark:border-slate-800 dark:bg-slate-950/40">
                                <label class="flex items-start gap-3 cursor-pointer select-none">
                                    <input type="checkbox" name="close_jobs" value="1" checked class="mt-0.5 h-4 w-4 rounded border-slate-300 text-rose-600 focus:ring-rose-500">
                                    <span class="text-xs leading-relaxed text-slate-700 dark:text-slate-300">
                                        <strong>Tutup seluruh lowongan aktif</strong> milik mitra ini selama masa pembekuan akun.
                                    </span>
                                </label>
                            </div>
                        @endif

                        <div class="pt-4 flex items-center justify-end gap-2.5 border-t border-slate-100 dark:border-slate-800">
                            <button type="button" onclick="closeManageAccountModal()" class="inline-flex h-10 items-center justify-center rounded-xl border border-slate-200 bg-white px-4 text-xs sm:text-sm font-semibold text-slate-700 shadow-xs hover:bg-slate-50 hover:border-slate-300 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 transition cursor-pointer">
                                Tutup
                            </button>
                            <button type="submit" class="inline-flex h-10 items-center justify-center gap-1.5 rounded-xl bg-rose-600 px-5 text-xs sm:text-sm font-semibold text-white shadow-sm shadow-rose-600/25 hover:bg-rose-700 active:translate-y-px transition cursor-pointer">
                                <span class="material-symbols-outlined text-[18px]">gavel</span>
                                Terapkan Sanksi Ban
                            </button>
                        </div>
                    </form>
                @endif
            </div>

            <!-- Tab 4: Hapus Akun Permanen -->
            <div id="tabContent-delete" class="mt-5 space-y-4 hidden">
                <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 dark:bg-rose-950/40 dark:border-rose-900/50">
                    <div class="flex items-center gap-2 text-rose-700 dark:text-rose-300 font-bold text-sm">
                        <span class="material-symbols-outlined text-lg">warning</span>
                        <span>Zona Bahaya: Hapus Permanen Akun</span>
                    </div>
                    <p class="mt-2 text-xs text-rose-800 dark:text-rose-200 leading-relaxed">
                        Tindakan ini bersifat permanen. Seluruh berkas profil, riwayat lamaran kerja, riwayat obrolan pesan, serta seluruh lowongan yang pernah dibuat oleh akun ini akan dihapus secara permanen dari basis data sistem dan tidak dapat dipulihkan.
                    </p>
                </div>

                <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="space-y-4">
                    @csrf
                    @method('DELETE')

                    <div class="p-3.5 rounded-2xl border border-slate-200/80 bg-slate-50/50 dark:border-slate-800 dark:bg-slate-950/40">
                        <p class="text-xs text-slate-600 dark:text-slate-400">
                            Konfirmasi penghapusan akun: <strong class="text-slate-900 dark:text-white">{{ $user->name }}</strong> (@<span>{{ $user->username }}</span>)
                        </p>
                    </div>

                    <div class="pt-4 flex items-center justify-end gap-2.5 border-t border-slate-100 dark:border-slate-800">
                        <button type="button" onclick="closeManageAccountModal()" class="inline-flex h-10 items-center justify-center rounded-xl border border-slate-200 bg-white px-4 text-xs sm:text-sm font-semibold text-slate-700 shadow-xs hover:bg-slate-50 hover:border-slate-300 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 transition cursor-pointer">
                            Batal
                        </button>
                        <button type="submit" class="inline-flex h-10 items-center justify-center gap-1.5 rounded-xl bg-rose-600 px-5 text-xs sm:text-sm font-semibold text-white shadow-sm shadow-rose-600/25 hover:bg-rose-700 active:translate-y-px transition cursor-pointer">
                            <span class="material-symbols-outlined text-[18px]">delete_forever</span>
                            Ya, Hapus Akun Permanen
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endpush

@push('scripts')
<script>
    function openManageAccountModal() {
        const modal = document.getElementById('manageAccountModal');
        if (modal) {
            modal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }
    }

    function closeManageAccountModal() {
        const modal = document.getElementById('manageAccountModal');
        if (modal) {
            modal.classList.add('hidden');
            document.body.style.overflow = '';
        }
    }

    function switchManageTab(tab) {
        const tabs = ['profile', 'password', 'sanction', 'delete'];
        tabs.forEach(t => {
            const content = document.getElementById(`tabContent-${t}`);
            const btn = document.getElementById(`tabBtn-${t}`);
            if (content && btn) {
                if (t === tab) {
                    content.classList.remove('hidden');
                    btn.classList.add('bg-white', 'text-slate-900', 'shadow-xs', 'dark:bg-slate-900', 'dark:text-white');
                    btn.classList.remove('text-slate-600', 'dark:text-slate-400');
                } else {
                    content.classList.add('hidden');
                    btn.classList.remove('bg-white', 'text-slate-900', 'shadow-xs', 'dark:bg-slate-900', 'dark:text-white');
                    if (t !== 'delete') {
                        btn.classList.add('text-slate-600', 'dark:text-slate-400');
                    }
                }
            }
        });
    }

    function handleBanDurationChange(val) {
        const customContainer = document.getElementById('customDaysContainer');
        const customInput = document.getElementById('custom_days');
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

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            closeManageAccountModal();
        }
    });

    document.addEventListener('DOMContentLoaded', () => {
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.get('kelola') === '1' || {{ $errors->any() ? 'true' : 'false' }}) {
            openManageAccountModal();
            @if($errors->has('password'))
                switchManageTab('password');
            @elseif($errors->has('ban_reason') || $errors->has('duration_type') || $errors->has('custom_days'))
                switchManageTab('sanction');
            @endif

            if (urlParams.has('kelola')) {
                const cleanUrl = window.location.pathname;
                window.history.replaceState({}, document.title, cleanUrl);
            }
        }
    });
</script>
@endpush
