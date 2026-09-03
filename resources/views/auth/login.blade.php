@extends('layouts.auth')

@section('title', 'Masuk ke KerjaLokal')

@section('content')
    <div class="mx-auto max-w-md" data-reveal>
        <div class="mb-8">
            <span class="inline-flex items-center gap-2 rounded-full bg-brand-50 px-3 py-1.5 text-xs font-bold uppercase tracking-[0.16em] text-brand-700 dark:bg-brand-950 dark:text-brand-200"><span class="h-1.5 w-1.5 rounded-full bg-coral-500"></span>Ruang kerja Anda</span>
            <h2 class="mt-5 text-3xl font-bold tracking-tight text-slate-950 dark:text-white sm:text-4xl">Selamat datang kembali.</h2>
            <p class="mt-3 text-sm leading-6 text-slate-600 dark:text-slate-300">Masuk untuk melanjutkan proses rekrutmen sebagai pencari kerja, mitra UMKM, atau admin.</p>
        </div>

        <form id="loginForm" action="{{ route('login') }}" method="POST" class="space-y-5 rounded-3xl border border-slate-200 bg-white p-5 shadow-[0_24px_70px_-40px_rgba(15,23,42,0.45)] dark:border-slate-800 dark:bg-slate-900 sm:p-7">
            @csrf
            <div>
                <label for="email" class="mb-2 block text-sm font-bold text-slate-800 dark:text-slate-100">Email atau nama pengguna</label>
                <div class="relative">
                    <span class="material-symbols-outlined pointer-events-none absolute left-3.5 top-1/2 -translate-y-1/2 text-[20px] text-slate-400">person</span>
                    <input id="email" name="email" type="text" value="{{ old('email') }}" autocomplete="username" autofocus required class="min-h-12 w-full rounded-xl border bg-slate-50 py-3 pl-11 pr-4 text-sm text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-brand-500 focus:bg-white focus:ring-4 focus:ring-brand-100 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-100 dark:placeholder:text-slate-500 dark:focus:bg-slate-900 dark:focus:ring-brand-900/60 {{ ($errors->has('email') || $errors->has('password')) ? 'border-rose-500' : 'border-slate-200' }}" placeholder="nama@email.com atau hadids">
                </div>
                @error('email')<p class="mt-1.5 flex items-center gap-1 text-xs font-semibold text-rose-600" role="alert"><span class="material-symbols-outlined text-[15px]">error</span>{{ $message }}</p>@enderror
            </div>

            <div>
                <div class="mb-2 flex items-center justify-between"><label for="password" class="text-sm font-bold text-slate-800 dark:text-slate-100">Kata sandi</label><span class="text-xs text-slate-500">Minimal 8 karakter</span></div>
                <div class="relative">
                    <span class="material-symbols-outlined pointer-events-none absolute left-3.5 top-1/2 -translate-y-1/2 text-[20px] text-slate-400">lock</span>
                    <input id="password" name="password" type="password" autocomplete="current-password" required class="min-h-12 w-full rounded-xl border bg-slate-50 py-3 pl-11 pr-12 text-sm text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-brand-500 focus:bg-white focus:ring-4 focus:ring-brand-100 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-100 dark:placeholder:text-slate-500 dark:focus:bg-slate-900 dark:focus:ring-brand-900/60 {{ $errors->has('password') ? 'border-rose-500' : 'border-slate-200' }}" placeholder="Masukkan kata sandi">
                    <button type="button" data-password-toggle="password" class="absolute right-2 top-1/2 inline-flex h-9 w-9 -translate-y-1/2 items-center justify-center rounded-lg text-slate-400 hover:bg-slate-100 hover:text-slate-700 dark:hover:bg-slate-800 dark:hover:text-white" aria-label="Tampilkan kata sandi"><span class="material-symbols-outlined text-[20px]">visibility</span></button>
                </div>
                @error('password')<p class="mt-1.5 flex items-center gap-1 text-xs font-semibold text-rose-600" role="alert"><span class="material-symbols-outlined text-[15px]">error</span>{{ $message }}</p>@enderror
            </div>

            <label class="flex cursor-pointer items-center gap-3 text-sm text-slate-600 dark:text-slate-300"><input type="checkbox" name="remember" value="1" @checked(old('remember')) class="h-4 w-4 rounded border-slate-300 text-brand-600 focus:ring-brand-500">Ingat saya di perangkat ini</label>
            <button id="loginSubmitBtn" type="submit" class="group inline-flex min-h-12 w-full items-center justify-center gap-2 rounded-xl bg-brand-700 px-5 py-3 text-sm font-bold text-white shadow-lg shadow-brand-900/15 transition hover:-translate-y-0.5 hover:bg-brand-800 focus:outline-none focus:ring-4 focus:ring-brand-200 dark:focus:ring-brand-900">Masuk ke dashboard<span class="material-symbols-outlined text-[19px] transition group-hover:translate-x-0.5">arrow_forward</span></button>
        </form>
        <p class="mt-6 text-center text-sm text-slate-600 dark:text-slate-300">Belum punya akun? <a href="{{ route('register') }}" class="font-bold text-brand-700 hover:text-brand-900 hover:underline dark:text-brand-300">Daftar gratis</a></p>
    </div>

    <x-auth-loading-overlay title="Masuk ke Dashboard..." subtitle="Memverifikasi kredensial dan menyiapkan sesi akun Anda." />
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        @if(session('account_banned'))
            const ban = @json(session('account_banned'));
            window.showAppAlert({
                title: 'Akun Ditangguhkan / Kena Ban',
                message: `<div class="space-y-2">
                    <p>Akun Anda <strong>"${ban.name || ''}"</strong> (<span class="font-mono text-brand-700 dark:text-brand-300 font-semibold">@${ban.username || ''}</span>) sedang <strong>dibekukan / terkena sanksi pemblokiran</strong> oleh Administrator.</p>
                    <div class="p-3 rounded-2xl bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-900/50 text-rose-900 dark:text-rose-200 text-xs space-y-1">
                        <div><strong>Masa Berlaku:</strong> ${ban.duration || ''}</div>
                        <div><strong>Alasan Pemblokiran:</strong> "${ban.reason || 'Pelanggaran ketentuan sistem.'}"</div>
                    </div>
                    <p class="text-[11px] text-slate-500 dark:text-slate-400">Selama masa penangguhan, Anda tidak dapat masuk atau mengakses layanan KerjaLokal. Hubungi tim administrator jika terdapat sanggahan atau kekeliruan.</p>
                </div>`,
                confirmText: 'Saya Mengerti',
                type: 'danger',
                icon: 'block'
            });
        @elseif(session('error'))
            window.showAppAlert({
                title: 'Pemberitahuan Akun',
                message: @json(session('error')),
                confirmText: 'Tutup',
                type: 'danger',
                icon: 'error'
            });
        @endif

        // Fitur "Ingat saya di perangkat ini" (Browser Session & Credential Persistence)
        const rememberedInputKey = 'kerjalokal_remembered_login';
        const emailField = document.getElementById('email');
        const rememberCheckbox = document.querySelector('input[name="remember"]');

        if (emailField && rememberCheckbox) {
            const savedLogin = localStorage.getItem(rememberedInputKey);
            if (savedLogin && !emailField.value) {
                emailField.value = savedLogin;
                rememberCheckbox.checked = true;
            }

            const form = emailField.closest('form');
            if (form) {
                form.addEventListener('submit', () => {
                    if (rememberCheckbox.checked && emailField.value.trim() !== '') {
                        localStorage.setItem(rememberedInputKey, emailField.value.trim());
                    } else {
                        localStorage.removeItem(rememberedInputKey);
                    }
                });
            }
        // Animasi Loading Masuk ke Dashboard
        const loginForm = document.getElementById('loginForm');
        const loginSubmitBtn = document.getElementById('loginSubmitBtn');
        const authLoadingOverlay = document.getElementById('authLoadingOverlay');

        if (loginForm && loginSubmitBtn) {
            loginForm.addEventListener('submit', (e) => {
                if (!loginForm.checkValidity()) {
                    return;
                }

                loginSubmitBtn.disabled = true;
                loginSubmitBtn.classList.add('opacity-80', 'cursor-wait');
                loginSubmitBtn.innerHTML = `
                    <svg class="h-5 w-5 animate-spin text-white shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span>Menghubungkan ke dashboard...</span>
                `;

                if (authLoadingOverlay) {
                    authLoadingOverlay.classList.remove('hidden');
                    authLoadingOverlay.classList.add('flex');
                }
            });

            window.addEventListener('pageshow', (event) => {
                loginSubmitBtn.disabled = false;
                loginSubmitBtn.classList.remove('opacity-80', 'cursor-wait');
                loginSubmitBtn.innerHTML = `Masuk ke dashboard<span class="material-symbols-outlined text-[19px] transition group-hover:translate-x-0.5">arrow_forward</span>`;
                if (authLoadingOverlay) {
                    authLoadingOverlay.classList.add('hidden');
                    authLoadingOverlay.classList.remove('flex');
                }
            });
        }
    });
</script>
@endpush
