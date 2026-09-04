@extends('layouts.admin')

@section('title', 'Kelola Pengguna | Admin KerjaLokal')
@section('portal_title', 'Kelola pengguna & kredensial')
@section('portal_description', 'Pantau aktivitas akun, kelola kredensial password, tindak pemblokiran (ban), serta kepatuhan etis pengguna.')

@section('content')
    <!-- Search & Filter Card -->
    <form method="GET" class="mb-6 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm dark:border-slate-800 dark:bg-slate-900">
        <div class="grid gap-3 sm:grid-cols-12 sm:items-end">
            <div class="sm:col-span-5">
                <label for="q" class="portal-label">Pencarian akun</label>
                <div class="relative">
                    <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-[18px]">search</span>
                    <input type="text" id="q" name="q" value="{{ request('q') }}" placeholder="Cari nama, email, usaha, atau nomor telepon..." class="portal-input pl-9">
                </div>
            </div>
            <div class="sm:col-span-3">
                <label for="role" class="portal-label">Filter role</label>
                <select id="role" name="role" class="portal-input">
                    <option value="">Semua role</option>
                    <option value="jobseeker" @selected(request('role') === 'jobseeker')>Pencari kerja</option>
                    <option value="employer" @selected(request('role') === 'employer')>Mitra UMKM</option>
                    <option value="admin" @selected(request('role') === 'admin')>Administrator</option>
                </select>
            </div>
            <div class="sm:col-span-2">
                <label for="status" class="portal-label">Status akun</label>
                <select id="status" name="status" class="portal-input">
                    <option value="">Semua status</option>
                    <option value="active" @selected(request('status') === 'active')>Aktif</option>
                    <option value="banned" @selected(request('status') === 'banned')>Dibekukan (Ban)</option>
                </select>
            </div>
            <div class="sm:col-span-2 flex items-center gap-2">
                <button type="submit" class="portal-button-primary w-full justify-center">Terapkan</button>
                <a href="{{ route('admin.users.index') }}" class="portal-button-secondary" title="Reset filter">
                    <span class="material-symbols-outlined text-[18px]">refresh</span>
                </a>
            </div>
        </div>
    </form>

    <!-- Users Table -->
    <div class="portal-table-shell overflow-x-auto">
        <table class="portal-table">
            <thead>
                <tr>
                    <th>Pengguna</th>
                    <th>Role</th>
                    <th>Status Akun</th>
                    <th>Usaha / Organisasi</th>
                    <th>Aktivitas</th>
                    <th>Terdaftar</th>
                    <th class="text-right">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                    <tr>
                        <td>
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-200 font-bold flex items-center justify-center text-xs shrink-0">
                                    {{ strtoupper(substr($user->name, 0, 2)) }}
                                </div>
                                <div class="min-w-0">
                                    <a href="{{ route('admin.users.show', $user) }}" class="font-bold text-slate-900 hover:text-brand-700 dark:text-white dark:hover:text-brand-400 truncate block">
                                        {{ $user->name }}
                                    </a>
                                    <span class="text-xs text-brand-700 dark:text-brand-300 font-mono font-medium block">@<span>{{ $user->username }}</span></span>
                                    <span class="text-xs text-slate-500 block truncate">{{ $user->email }}{{ $user->phone ? ' · '.$user->phone : '' }}</span>
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="portal-badge {{ match($user->role) {'jobseeker' => 'bg-emerald-50 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300', 'employer' => 'bg-blue-50 text-blue-800 dark:bg-blue-950 dark:text-blue-300', default => 'bg-purple-50 text-purple-800 dark:bg-purple-950 dark:text-purple-300'} }}">
                                {{ match($user->role) {'jobseeker' => 'Pencari kerja', 'employer' => 'Mitra UMKM', default => 'Admin'} }}
                            </span>
                        </td>
                        <td>
                            @if($user->isBanned())
                                <div>
                                    <span class="inline-flex items-center gap-1 rounded-md bg-rose-50 px-2 py-0.5 text-xs font-bold text-rose-700 border border-rose-200 dark:bg-rose-950/40 dark:text-rose-300 dark:border-rose-900/50">
                                        <span class="material-symbols-outlined text-[14px]">block</span>
                                        {{ $user->ban_status_text }}
                                    </span>
                                    @if($user->ban_reason)
                                        <p class="mt-1 text-[11px] text-slate-500 italic max-w-xs truncate" title="{{ $user->ban_reason }}">
                                            "{{ $user->ban_reason }}"
                                        </p>
                                    @endif
                                </div>
                            @else
                                <span class="inline-flex items-center gap-1 rounded-md bg-emerald-50 px-2 py-0.5 text-xs font-bold text-emerald-700 border border-emerald-200 dark:bg-emerald-950/40 dark:text-emerald-300 dark:border-emerald-900/50">
                                    <span class="material-symbols-outlined text-[14px]">check_circle</span>
                                    Aktif
                                </span>
                            @endif
                        </td>
                        <td>
                            <span class="text-xs {{ $user->business_name ? 'font-semibold text-slate-800 dark:text-slate-200' : 'text-slate-400' }}">
                                {{ $user->business_name ?: '-' }}
                            </span>
                        </td>
                        <td>
                            <div class="text-xs text-slate-600 dark:text-slate-400 space-y-0.5">
                                @if($user->role === 'employer')
                                    <span><strong>{{ $user->jobs_count }}</strong> lowongan dibuat</span>
                                @elseif($user->role === 'jobseeker')
                                    <span><strong>{{ $user->job_applications_count }}</strong> lamaran diajukan</span>
                                @else
                                    <span>Pengawas Sistem</span>
                                @endif
                            </div>
                        </td>
                        <td>
                            <span class="text-xs text-slate-500">{{ $user->created_at->translatedFormat('d M Y') }}</span>
                        </td>
                        <td>
                            <div class="flex items-center justify-end gap-1.5">
                                <!-- Detail Button -->
                                <a href="{{ route('admin.users.show', ['user' => $user, 'return_to' => url()->full()]) }}" class="portal-button-secondary !py-1 !px-2.5 text-xs" title="Lihat detail aktivitas pengguna">
                                    <span class="material-symbols-outlined text-[16px]">visibility</span>
                                    Detail
                                </a>

                                @if(!$user->hasRole('admin'))
                                    <!-- Kelola Akun Button -->
                                    <a href="{{ route('admin.users.show', ['user' => $user, 'return_to' => url()->full(), 'kelola' => 1]) }}" class="portal-button-secondary !py-1 !px-2.5 text-xs !text-brand-700 hover:!bg-brand-50 dark:!text-brand-300" title="Kelola Akun (Ganti Sandi, Username, Ban)">
                                        <span class="material-symbols-outlined text-[16px]">manage_accounts</span>
                                        Kelola Akun
                                    </a>

                                    <!-- Delete Button (Trigger Pop-up Modal) -->
                                    <button type="button" onclick="openDeleteUserModal('{{ addslashes($user->name) }}', '{{ addslashes($user->email) }}', '{{ route('admin.users.destroy', $user) }}')" class="portal-button-secondary !py-1 !px-2 text-xs !text-rose-600 hover:!bg-rose-50 cursor-pointer" title="Hapus pengguna">
                                        <span class="material-symbols-outlined text-[16px]">delete</span>
                                    </button>
                                @else
                                    <span class="text-xs text-slate-400 font-semibold px-2 py-1">Admin</span>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="py-12 text-center text-slate-500">
                            <span class="material-symbols-outlined text-4xl text-slate-300 block mb-2">person_search</span>
                            Tidak ada data pengguna yang sesuai dengan filter.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-7">{{ $users->links() }}</div>
@endsection

@push('modals')
    <!-- Modal Konfirmasi Hapus Akun Pengguna -->
    <div id="deleteUserModal" class="fixed inset-0 z-[100] hidden bg-slate-950/60 backdrop-blur-sm p-4 overflow-y-auto flex items-center justify-center" onclick="if(event.target === this) closeDeleteUserModal()">
        <div class="relative w-full max-w-md rounded-3xl bg-white p-6 sm:p-7 shadow-2xl shadow-slate-950/20 dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 animate-modal-pop">
            
            <div class="flex items-start gap-3.5">
                <div class="w-11 h-11 rounded-2xl bg-rose-50 text-rose-600 dark:bg-rose-950/50 dark:text-rose-400 border border-rose-200 dark:border-rose-900/50 flex items-center justify-center shrink-0">
                    <span class="material-symbols-outlined text-[24px]">person_remove</span>
                </div>
                <div class="flex-1 min-w-0">
                    <h3 class="text-base sm:text-lg font-bold text-slate-900 dark:text-white leading-snug">
                        Hapus Akun Pengguna?
                    </h3>
                    <p class="mt-1 text-xs text-slate-500 dark:text-slate-400 leading-relaxed">
                        Anda akan menghapus permanen akun <strong id="deleteTargetUserName" class="text-slate-900 dark:text-white"></strong> (<span id="deleteTargetUserEmail" class="font-mono text-[11px]"></span>).
                    </p>
                </div>
                <button type="button" onclick="closeDeleteUserModal()" class="w-8 h-8 rounded-xl text-slate-400 hover:text-slate-700 hover:bg-slate-100 dark:hover:text-white dark:hover:bg-slate-800 flex items-center justify-center transition shrink-0 cursor-pointer" aria-label="Tutup">
                    <span class="material-symbols-outlined text-[20px]">close</span>
                </button>
            </div>

            <div class="mt-4 p-3.5 rounded-2xl bg-rose-50/70 border border-rose-200/70 text-rose-800 dark:bg-rose-950/30 dark:border-rose-900/40 dark:text-rose-300 text-xs flex items-start gap-2.5">
                <span class="material-symbols-outlined text-rose-600 dark:text-rose-400 text-base shrink-0 mt-0.5">warning</span>
                <span class="leading-relaxed">
                    Tindakan ini permanen dan tidak dapat dibatalkan. Seluruh riwayat lamaran, pesan obrolan, dan lowongan kerja terkait akun ini akan dihapus dari sistem.
                </span>
            </div>

            <form id="deleteUserForm" method="POST" action="" class="mt-6 flex items-center justify-end gap-2.5">
                @csrf
                @method('DELETE')
                <button type="button" onclick="closeDeleteUserModal()" class="inline-flex h-10 items-center justify-center rounded-xl border border-slate-200 bg-white px-4 text-xs sm:text-sm font-semibold text-slate-700 shadow-xs hover:bg-slate-50 hover:border-slate-300 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 transition cursor-pointer">
                    Batal
                </button>
                <button type="submit" class="inline-flex h-10 items-center justify-center gap-1.5 rounded-xl bg-rose-600 px-5 text-xs sm:text-sm font-semibold text-white shadow-sm shadow-rose-600/25 hover:bg-rose-700 active:translate-y-px transition cursor-pointer">
                    <span class="material-symbols-outlined text-[18px]">delete_forever</span>
                    Ya, Hapus Akun
                </button>
            </form>

        </div>
    </div>
@endpush

@push('scripts')
<script>
    function openDeleteUserModal(name, email, actionUrl) {
        document.getElementById('deleteTargetUserName').textContent = name;
        document.getElementById('deleteTargetUserEmail').textContent = email;
        document.getElementById('deleteUserForm').action = actionUrl;
        const modal = document.getElementById('deleteUserModal');
        if (modal) {
            modal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }
    }

    function closeDeleteUserModal() {
        const modal = document.getElementById('deleteUserModal');
        if (modal) {
            modal.classList.add('hidden');
            document.body.style.overflow = '';
        }
    }

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeDeleteUserModal();
        }
    });
</script>
@endpush
