@extends(match($user->role ?? 'jobseeker') {
    'admin' => 'layouts.admin',
    'employer' => 'layouts.employer',
    default => 'layouts.app',
})

@section('title', 'Pengaturan & Preferensi | KerjaLokal')

@section('portal_icon', 'tune')
@section('portal_context', match($user->role ?? 'jobseeker') {
    'admin' => 'Pusat tata kelola sistem',
    'employer' => 'Portal operasional mitra',
    default => 'KerjaLokal',
})
@section('portal_title', 'Pengaturan & Preferensi')
@section('portal_description', 'Atur preferensi tema visual aplikasi serta kelola kredensial dan informasi akun Anda.')

@section('portal_actions')
    <a href="{{ match($user->role ?? 'jobseeker') { 'employer' => route('employer.dashboard'), 'admin' => route('admin.dashboard'), default => route('jobs.index') } }}" class="portal-button-secondary">
        <span class="material-symbols-outlined text-[18px]">arrow_back</span>
        Kembali ke Dashboard
    </a>
@endsection

@section('content')
    <div class="mx-auto max-w-3xl space-y-8" data-reveal>
        {{-- Header Halaman (khusus saat dibuka dengan shell publik / pencari kerja) --}}
        @if(!in_array($user->role ?? 'jobseeker', ['admin', 'employer']))
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <a href="{{ route('jobs.index') }}" class="inline-flex items-center gap-1.5 text-xs font-bold text-slate-500 hover:text-brand-700 dark:text-slate-400 dark:hover:text-brand-300">
                        <span class="material-symbols-outlined text-[16px]">arrow_back</span>
                        Kembali ke Katalog Lowongan
                    </a>
                    <h1 class="mt-2 text-2xl font-bold tracking-tight text-slate-950 sm:text-3xl dark:text-white">Pengaturan & Preferensi</h1>
                    <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Atur preferensi tampilan visual aplikasi dan tinjau profil akun Anda.</p>
                </div>
                <div class="inline-flex items-center gap-2 rounded-xl bg-brand-50 px-3.5 py-2 text-xs font-bold uppercase tracking-wider text-brand-700 dark:bg-brand-950 dark:text-brand-300">
                    <span class="material-symbols-outlined text-[18px]">verified</span>
                    Pencari Kerja
                </div>
            </div>
        @endif

        {{-- Bagian 1: Preferensi Tema Tampilan --}}
        <section class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900 sm:p-8">
            <div class="mb-6">
                <div class="flex items-center gap-2.5">
                    <span class="grid h-8 w-8 place-items-center rounded-lg bg-brand-50 text-brand-700 dark:bg-brand-950 dark:text-brand-300">
                        <span class="material-symbols-outlined text-[20px]">palette</span>
                    </span>
                    <h2 class="text-lg font-bold text-slate-950 dark:text-white">Tema Tampilan (Mode Layar)</h2>
                </div>
                <p class="mt-2 text-sm text-slate-500 dark:text-slate-400">Pilih mode tampilan yang nyaman untuk mata Anda. Pengaturan ini tersimpan otomatis di perangkat Anda.</p>
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                {{-- Opsi Mode Terang --}}
                <button type="button" data-theme-value="light" data-theme-card="light" class="group relative flex flex-col justify-between rounded-2xl border-2 p-5 text-left transition hover:border-brand-500 focus:outline-none">
                    <div class="flex items-start justify-between">
                        <span class="grid h-12 w-12 place-items-center rounded-xl bg-amber-50 text-amber-600 transition group-hover:scale-105 dark:bg-amber-950/60 dark:text-amber-400">
                            <span class="material-symbols-outlined text-[28px]">light_mode</span>
                        </span>
                        <span data-theme-check class="material-symbols-outlined hidden text-[22px] text-brand-600 dark:text-brand-400">check_circle</span>
                    </div>
                    <div class="mt-5">
                        <p class="font-bold text-slate-900 dark:text-white">Mode Terang (Bawaan)</p>
                        <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">Latar putih bersih dengan kontras tinggi untuk kenyamanan baca di siang hari.</p>
                    </div>
                </button>

                {{-- Opsi Mode Gelap --}}
                <button type="button" data-theme-value="dark" data-theme-card="dark" class="group relative flex flex-col justify-between rounded-2xl border-2 p-5 text-left transition hover:border-brand-500 focus:outline-none">
                    <div class="flex items-start justify-between">
                        <span class="grid h-12 w-12 place-items-center rounded-xl bg-slate-100 text-slate-700 transition group-hover:scale-105 dark:bg-slate-800 dark:text-slate-200">
                            <span class="material-symbols-outlined text-[28px]">dark_mode</span>
                        </span>
                        <span data-theme-check class="material-symbols-outlined hidden text-[22px] text-brand-600 dark:text-brand-400">check_circle</span>
                    </div>
                    <div class="mt-5">
                        <p class="font-bold text-slate-900 dark:text-white">Mode Gelap</p>
                        <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">Latar gelap redup yang nyaman untuk mengurangi ketegangan mata di ruangan minim cahaya.</p>
                    </div>
                </button>
            </div>
        </section>

        {{-- Bagian 2: Informasi Akun --}}
        <section class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900 sm:p-8">
            <div class="mb-6 flex items-center gap-2.5">
                <span class="grid h-8 w-8 place-items-center rounded-lg bg-brand-50 text-brand-700 dark:bg-brand-950 dark:text-brand-300">
                    <span class="material-symbols-outlined text-[20px]">badge</span>
                </span>
                <h2 class="text-lg font-bold text-slate-950 dark:text-white">Informasi Akun</h2>
            </div>

            <dl class="grid gap-4 sm:grid-cols-2">
                <div class="rounded-2xl bg-slate-50 p-4 dark:bg-slate-950">
                    <dt class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Nama Lengkap</dt>
                    <dd class="mt-1 text-base font-semibold text-slate-900 dark:text-white">{{ $user->name }}</dd>
                </div>

                <div class="rounded-2xl bg-slate-50 p-4 dark:bg-slate-950">
                    <dt class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Username Akun</dt>
                    <dd class="mt-1 text-base font-semibold text-brand-700 dark:text-brand-300 font-mono">@<span>{{ $user->username ?? '-' }}</span></dd>
                </div>

                <div class="rounded-2xl bg-slate-50 p-4 dark:bg-slate-950">
                    <dt class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Alamat Email</dt>
                    <dd class="mt-1 text-base font-semibold text-slate-900 dark:text-white">{{ $user->email }}</dd>
                </div>

                @if ($user->business_name)
                    <div class="rounded-2xl bg-slate-50 p-4 dark:bg-slate-950">
                        <dt class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Nama Usaha / Usaha Mitra</dt>
                        <dd class="mt-1 text-base font-semibold text-slate-900 dark:text-white">{{ $user->business_name }}</dd>
                    </div>
                @endif

                <div class="rounded-2xl bg-slate-50 p-4 dark:bg-slate-950">
                    <dt class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Nomor Telepon</dt>
                    <dd class="mt-1 text-base font-semibold text-slate-900 dark:text-white">{{ $user->phone ?: 'Belum diisi' }}</dd>
                </div>

                <div class="rounded-2xl bg-slate-50 p-4 dark:bg-slate-950">
                    <dt class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Tanggal Terdaftar</dt>
                    <dd class="mt-1 text-base font-semibold text-slate-900 dark:text-white">{{ $user->created_at->translatedFormat('d F Y') }}</dd>
                </div>

                <div class="rounded-2xl bg-slate-50 p-4 dark:bg-slate-950">
                    <dt class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Status Akun</dt>
                    <dd class="mt-1 flex items-center gap-1.5 text-base font-semibold text-emerald-600 dark:text-emerald-400">
                        <span class="h-2 w-2 rounded-full bg-emerald-500"></span>
                        Aktif & Terverifikasi
                    </dd>
                </div>
            </dl>
        </section>

        {{-- Bagian: Foto Profil Akun (1080x1080) --}}
        <section class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900 sm:p-8">
            <div class="mb-6 flex items-center justify-between gap-4">
                <div class="flex items-center gap-2.5">
                    <span class="grid h-8 w-8 place-items-center rounded-lg bg-brand-50 text-brand-700 dark:bg-brand-950 dark:text-brand-300">
                        <span class="material-symbols-outlined text-[20px]">account_circle</span>
                    </span>
                    <div>
                        <h2 class="text-lg font-bold text-slate-950 dark:text-white">Foto Profil Akun</h2>
                        <p class="mt-0.5 text-xs text-slate-500 dark:text-slate-400">Rasio 1:1, resolusi optimal 1080 x 1080 piksel (JPG, PNG, atau WEBP).</p>
                    </div>
                </div>
                <span class="hidden sm:inline-flex items-center gap-1 rounded-xl bg-teal-50 px-2.5 py-1 text-xs font-bold text-teal-800 dark:bg-teal-950 dark:text-teal-300 border border-teal-200/60 dark:border-teal-800">
                    <span class="material-symbols-outlined text-[15px]">aspect_ratio</span>
                    1080 × 1080 px
                </span>
            </div>

            <div class="flex flex-col sm:flex-row items-start sm:items-center gap-6">
                <!-- Avatar Preview -->
                <div class="relative shrink-0">
                    <div class="h-28 w-28 rounded-2xl overflow-hidden border-2 border-slate-200 dark:border-slate-700 bg-slate-100 dark:bg-slate-800 shadow-inner flex items-center justify-center">
                        @if($user->avatar_url)
                            <img id="avatarPreviewImg" src="{{ $user->avatar_url }}" alt="{{ $user->name }}" class="h-full w-full object-cover">
                        @else
                            <div id="avatarInitialsPlaceholder" class="grid h-full w-full place-items-center bg-brand-700 text-3xl font-bold text-white dark:bg-brand-600">
                                {{ strtoupper(substr($user->name, 0, 2)) }}
                            </div>
                            <img id="avatarPreviewImg" src="" alt="{{ $user->name }}" class="hidden h-full w-full object-cover">
                        @endif
                    </div>
                    <button type="button" onclick="document.getElementById('avatarFileInput').click()" class="absolute -bottom-2 -right-2 grid h-8 w-8 place-items-center rounded-xl bg-brand-700 text-white shadow-md hover:bg-brand-800 transition cursor-pointer" title="Pilih foto baru">
                        <span class="material-symbols-outlined text-[18px]">photo_camera</span>
                    </button>
                </div>

                <!-- Action & Upload Controls -->
                <div class="flex-1 space-y-3">
                    <form id="avatarUploadForm" action="{{ route('settings.avatar.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input type="file" id="avatarFileInput" accept="image/jpeg,image/png,image/webp" class="hidden" onchange="handleAvatarSelected(event)">
                        <input type="file" id="avatarCroppedInput" name="avatar" class="hidden">
                    </form>

                    <div class="flex flex-wrap items-center gap-2.5">
                        <button type="button" onclick="document.getElementById('avatarFileInput').click()" class="portal-button-primary !py-2 !px-4 text-xs font-bold gap-1.5 cursor-pointer">
                            <span class="material-symbols-outlined text-[17px]">upload</span>
                            Pilih Foto (1080x1080)
                        </button>

                        <button type="button" id="saveAvatarBtn" onclick="submitProcessedAvatar()" class="hidden portal-button-primary !bg-emerald-600 hover:!bg-emerald-700 !py-2 !px-4 text-xs font-bold gap-1.5 cursor-pointer">
                            <span class="material-symbols-outlined text-[17px]">check_circle</span>
                            Simpan Foto Profil
                        </button>

                        @if($user->avatar)
                            <form id="deleteAvatarForm" action="{{ route('settings.avatar.destroy') }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="button" onclick="confirmDeleteAvatar()" class="portal-button-secondary !py-2 !px-3 text-xs font-bold text-rose-600 hover:text-rose-700 hover:bg-rose-50 border-rose-200 dark:border-rose-900/50 dark:hover:bg-rose-950/40 cursor-pointer">
                                    <span class="material-symbols-outlined text-[17px]">delete</span>
                                    Hapus Foto
                                </button>
                            </form>
                        @endif
                    </div>

                    <div id="avatarStatusNotice" class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed">
                        Pilih foto berformat JPG, PNG, atau WEBP. Sistem otomatis memotong area tengah (*center crop*) dan menyesuaikan rasio menjadi <strong>1080 × 1080 piksel</strong> untuk kejernihan maksimal.
                    </div>
                </div>
            </div>
        </section>

        {{-- Bagian: Ganti Username --}}
        <section class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900 sm:p-8">
            <div class="mb-6 flex items-center gap-2.5">
                <span class="grid h-8 w-8 place-items-center rounded-lg bg-brand-50 text-brand-700 dark:bg-brand-950 dark:text-brand-300">
                    <span class="material-symbols-outlined text-[20px]">alternate_email</span>
                </span>
                <div>
                    <h2 class="text-lg font-bold text-slate-950 dark:text-white">Ganti Username</h2>
                    <p class="mt-0.5 text-xs text-slate-500 dark:text-slate-400">Username bersifat unik untuk identitas profil Anda dan dapat digunakan untuk masuk (login).</p>
                </div>
            </div>

            <form action="{{ route('settings.username.update') }}" method="POST" class="max-w-xl space-y-4">
                @csrf
                @method('PUT')

                <div>
                    <label for="username" class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-slate-600 dark:text-slate-300">Username Baru</label>
                    <div class="relative flex items-center">
                        <span class="pointer-events-none absolute left-3.5 text-sm font-bold text-slate-400 dark:text-slate-500">@</span>
                        <input id="username" name="username" type="text" required value="{{ old('username', $user->username) }}" placeholder="masukkan_username_baru" class="min-h-11 w-full rounded-xl border bg-slate-50 pl-8 pr-3.5 py-2.5 text-sm font-medium text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-brand-500 focus:bg-white focus:ring-4 focus:ring-brand-100 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-100 dark:placeholder:text-slate-500 dark:focus:bg-slate-900 dark:focus:ring-brand-900/60 {{ $errors->has('username') ? 'border-rose-500' : 'border-slate-200' }}">
                    </div>
                    @error('username')
                        <p class="mt-1.5 text-xs font-semibold text-rose-600" role="alert">{{ $message }}</p>
                    @else
                        <p class="mt-1.5 text-xs text-slate-400">Minimal 3 karakter. Hanya huruf, angka, titik (.), tanda hubung (-), dan garis bawah (_). Bebas spasi.</p>
                    @enderror
                </div>

                <div class="pt-2">
                    <button type="submit" class="inline-flex items-center justify-center gap-2 rounded-xl bg-brand-700 px-5 py-2.5 text-sm font-bold text-white shadow-sm transition hover:bg-brand-800 focus:outline-none focus:ring-4 focus:ring-brand-200 dark:bg-brand-600 dark:hover:bg-brand-500">
                        <span class="material-symbols-outlined text-[18px]">check</span>
                        Simpan Username Baru
                    </button>
                </div>
            </form>
        </section>

        {{-- Bagian: Ganti Kata Sandi --}}
        <section class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900 sm:p-8">
            <div class="mb-6 flex items-center gap-2.5">
                <span class="grid h-8 w-8 place-items-center rounded-lg bg-brand-50 text-brand-700 dark:bg-brand-950 dark:text-brand-300">
                    <span class="material-symbols-outlined text-[20px]">lock_reset</span>
                </span>
                <div>
                    <h2 class="text-lg font-bold text-slate-950 dark:text-white">Ganti Kata Sandi</h2>
                    <p class="mt-0.5 text-xs text-slate-500 dark:text-slate-400">Pastikan akun Anda menggunakan kata sandi yang aman dan tidak digunakan di situs lain.</p>
                </div>
            </div>

            <form action="{{ route('settings.password.update') }}" method="POST" class="max-w-xl space-y-4">
                @csrf
                @method('PUT')

                <div>
                    <label for="current_password" class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-slate-600 dark:text-slate-300">Kata Sandi Saat Ini</label>
                    <div class="relative">
                        <input id="current_password" name="current_password" type="password" required autocomplete="current-password" placeholder="Masukkan kata sandi saat ini" class="min-h-11 w-full rounded-xl border bg-slate-50 px-3.5 py-2.5 pr-11 text-sm text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-brand-500 focus:bg-white focus:ring-4 focus:ring-brand-100 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-100 dark:placeholder:text-slate-500 dark:focus:bg-slate-900 dark:focus:ring-brand-900/60 {{ $errors->has('current_password') ? 'border-rose-500' : 'border-slate-200' }}">
                        <button type="button" data-password-toggle="current_password" class="absolute right-2 top-1/2 inline-flex h-8 w-8 -translate-y-1/2 items-center justify-center rounded-lg text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800" aria-label="Tampilkan kata sandi"><span class="material-symbols-outlined text-[18px]">visibility</span></button>
                    </div>
                    @error('current_password')<p class="mt-1 text-xs font-semibold text-rose-600" role="alert">{{ $message }}</p>@enderror
                </div>

                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label for="new_password" class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-slate-600 dark:text-slate-300">Kata Sandi Baru</label>
                        <div class="relative">
                            <input id="new_password" name="password" type="password" required autocomplete="new-password" placeholder="Minimal 8 karakter" class="min-h-11 w-full rounded-xl border bg-slate-50 px-3.5 py-2.5 pr-11 text-sm text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-brand-500 focus:bg-white focus:ring-4 focus:ring-brand-100 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-100 dark:placeholder:text-slate-500 dark:focus:bg-slate-900 dark:focus:ring-brand-900/60 {{ $errors->has('password') ? 'border-rose-500' : 'border-slate-200' }}">
                            <button type="button" data-password-toggle="new_password" class="absolute right-2 top-1/2 inline-flex h-8 w-8 -translate-y-1/2 items-center justify-center rounded-lg text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800" aria-label="Tampilkan kata sandi"><span class="material-symbols-outlined text-[18px]">visibility</span></button>
                        </div>
                        @error('password')<p class="mt-1 text-xs font-semibold text-rose-600" role="alert">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label for="password_confirmation" class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-slate-600 dark:text-slate-300">Ulangi Kata Sandi</label>
                        <div class="relative">
                            <input id="password_confirmation" name="password_confirmation" type="password" required autocomplete="new-password" placeholder="Ulangi kata sandi baru" class="min-h-11 w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2.5 pr-11 text-sm text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-brand-500 focus:bg-white focus:ring-4 focus:ring-brand-100 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-100 dark:placeholder:text-slate-500 dark:focus:bg-slate-900 dark:focus:ring-brand-900/60">
                            <button type="button" data-password-toggle="password_confirmation" class="absolute right-2 top-1/2 inline-flex h-8 w-8 -translate-y-1/2 items-center justify-center rounded-lg text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800" aria-label="Tampilkan kata sandi"><span class="material-symbols-outlined text-[18px]">visibility</span></button>
                        </div>
                    </div>
                </div>

                <div class="pt-2">
                    <button type="submit" class="inline-flex items-center justify-center gap-2 rounded-xl bg-brand-700 px-5 py-2.5 text-sm font-bold text-white shadow-sm transition hover:bg-brand-800 focus:outline-none focus:ring-4 focus:ring-brand-200 dark:bg-brand-600 dark:hover:bg-brand-500">
                        <span class="material-symbols-outlined text-[18px]">key</span>
                        Perbarui Kata Sandi
                    </button>
                </div>
            </form>
        </section>

        {{-- Bagian 3: Keluar Akun --}}
        <section class="flex flex-col gap-4 rounded-3xl border border-rose-200 bg-rose-50/50 p-6 sm:flex-row sm:items-center sm:justify-between dark:border-rose-900/50 dark:bg-rose-950/20">
            <div>
                <h3 class="font-bold text-rose-900 dark:text-rose-200">Keluar dari Sesi Kerja</h3>
                <p class="text-xs text-rose-700 dark:text-rose-300">Tutup sesi login Anda di perangkat ini dengan aman.</p>
            </div>
            <button type="button" data-bs-toggle="modal" data-bs-target="#logoutModal" class="inline-flex items-center justify-center gap-2 rounded-xl bg-rose-600 px-4 py-2.5 text-sm font-bold text-white shadow-sm transition hover:bg-rose-700 focus:outline-none focus:ring-4 focus:ring-rose-200 dark:bg-rose-700 dark:hover:bg-rose-600">
                <span class="material-symbols-outlined text-[18px]">logout</span>
                Keluar dari Akun
            </button>
        </section>
    </div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const updateThemeSelectionCards = () => {
            const isDark = document.documentElement.classList.contains('dark');
            document.querySelectorAll('[data-theme-card]').forEach(card => {
                const isLightCard = card.dataset.themeCard === 'light';
                const isActive = isLightCard ? !isDark : isDark;
                card.classList.toggle('border-brand-600', isActive);
                card.classList.toggle('bg-brand-50/50', isActive && isLightCard);
                card.classList.toggle('dark:bg-brand-950/50', isActive && !isLightCard);
                card.classList.toggle('border-slate-200', !isActive);
                card.classList.toggle('dark:border-slate-800', !isActive);
                card.classList.toggle('bg-white', !isActive && isLightCard);
                card.classList.toggle('dark:bg-slate-900', !isActive);
                const check = card.querySelector('[data-theme-check]');
                if (check) {
                    check.classList.toggle('hidden', !isActive);
                }
            });
        };

        updateThemeSelectionCards();

        document.querySelectorAll('[data-theme-value]').forEach(button => {
            button.addEventListener('click', () => {
                setTimeout(updateThemeSelectionCards, 30);
            });
        });
    });

    let processedAvatarBlob = null;

    function handleAvatarSelected(event) {
        const file = event.target.files?.[0];
        if (!file) return;

        if (!['image/jpeg', 'image/png', 'image/webp'].includes(file.type)) {
            window.showAppAlert({
                title: 'Format Berkas Tidak Sesuai',
                message: 'Silakan pilih gambar dengan format JPG, JPEG, PNG, atau WEBP.',
                type: 'warning'
            });
            event.target.value = '';
            return;
        }

        const reader = new FileReader();
        reader.onload = function(e) {
            const img = new Image();
            img.onload = function() {
                const targetSize = 1080;
                const canvas = document.createElement('canvas');
                canvas.width = targetSize;
                canvas.height = targetSize;
                const ctx = canvas.getContext('2d');

                const minDim = Math.min(img.width, img.height);
                const sx = (img.width - minDim) / 2;
                const sy = (img.height - minDim) / 2;

                ctx.imageSmoothingEnabled = true;
                ctx.imageSmoothingQuality = 'high';
                ctx.drawImage(img, sx, sy, minDim, minDim, 0, 0, targetSize, targetSize);

                canvas.toBlob(function(blob) {
                    processedAvatarBlob = blob;

                    const previewImg = document.getElementById('avatarPreviewImg');
                    const initials = document.getElementById('avatarInitialsPlaceholder');
                    if (previewImg) {
                        previewImg.src = URL.createObjectURL(blob);
                        previewImg.classList.remove('hidden');
                    }
                    if (initials) initials.classList.add('hidden');

                    const saveBtn = document.getElementById('saveAvatarBtn');
                    if (saveBtn) saveBtn.classList.remove('hidden');

                    const notice = document.getElementById('avatarStatusNotice');
                    if (notice) {
                        notice.innerHTML = `<span class="text-emerald-600 dark:text-emerald-400 font-bold flex items-center gap-1"><span class="material-symbols-outlined text-[16px]">check_circle</span> Foto siap diunggah dalam resolusi presisi 1080 × 1080 piksel. Klik "Simpan Foto Profil" untuk menerapkan.</span>`;
                    }
                }, 'image/jpeg', 0.92);
            };
            img.src = e.target.result;
        };
        reader.readAsDataURL(file);
    }

    function submitProcessedAvatar() {
        if (!processedAvatarBlob) return;

        const fileInput = document.getElementById('avatarCroppedInput');
        const file = new File([processedAvatarBlob], 'profile_1080x1080.jpg', { type: 'image/jpeg' });

        const dataTransfer = new DataTransfer();
        dataTransfer.items.add(file);
        fileInput.files = dataTransfer.files;

        document.getElementById('avatarUploadForm').submit();
    }

    async function confirmDeleteAvatar() {
        const confirmed = await window.showAppConfirm({
            title: 'Hapus Foto Profil?',
            message: 'Apakah Anda yakin ingin menghapus foto profil? Tampilan akun akan kembali menggunakan inisial nama Anda.',
            confirmText: 'Ya, Hapus Foto',
            cancelText: 'Batal',
            type: 'danger',
            icon: 'delete'
        });

        if (confirmed) {
            document.getElementById('deleteAvatarForm')?.submit();
        }
    }
</script>
@endpush
