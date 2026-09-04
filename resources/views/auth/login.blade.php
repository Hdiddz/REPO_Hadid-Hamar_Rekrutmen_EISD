@extends('layouts.auth')

@section('title', 'Masuk ke KerjaLokal')

@section('content')
    @php
        $hasSavedUser = !empty($savedUser);
        $showQuickLogin = $hasSavedUser && !request()->has('mode') && !$errors->any();
    @endphp

    <div class="mx-auto max-w-md" data-reveal>
        @if(session('status'))
            <div id="loginStatusAlert" class="mb-4 flex items-center gap-2 rounded-2xl bg-emerald-50 p-3.5 text-xs font-semibold text-emerald-800 border border-emerald-200 dark:bg-emerald-950/40 dark:border-emerald-900/50 dark:text-emerald-300">
                <span class="material-symbols-outlined text-[18px] text-emerald-600 dark:text-emerald-400 shrink-0">check_circle</span>
                <span>{{ session('status') }}</span>
            </div>
        @endif

        @if($hasSavedUser)
            <!-- SECTION 1: QUICK LOGIN WITH SAVED PROFILE -->
            <div id="quickLoginSection" class="{{ $showQuickLogin ? '' : 'hidden' }}">
                <div class="mb-4 sm:mb-6">
                    <h2 class="text-2xl sm:text-3xl font-bold tracking-tight text-slate-950 dark:text-white">Selamat Datang Kembali</h2>
                    <p class="mt-1 text-xs sm:text-sm text-slate-500 dark:text-slate-400">Tekan profil Anda di bawah untuk langsung masuk ke dashboard.</p>
                </div>

                <div class="rounded-2xl border border-slate-200 bg-white p-5 sm:p-7 shadow-sm sm:rounded-3xl sm:shadow-[0_24px_70px_-40px_rgba(15,23,42,0.45)] dark:border-slate-800 dark:bg-slate-900 text-center">
                    <form id="quickLoginForm" action="{{ route('login.quick') }}" method="POST">
                        @csrf
                        <button type="submit" id="quickLoginSubmitBtn" class="group relative w-full rounded-2xl border-2 border-slate-100 bg-gradient-to-b from-slate-50/60 to-transparent p-5 text-center transition hover:border-brand-500 hover:bg-brand-50/30 hover:shadow-md dark:border-slate-800 dark:from-slate-800/40 dark:to-transparent dark:hover:border-brand-500/60 dark:hover:bg-brand-950/20 focus:outline-none focus:ring-4 focus:ring-brand-100 dark:focus:ring-brand-900/40 cursor-pointer" title="Klik untuk langsung masuk sebagai {{ $savedUser->name }}">
                            <!-- Profile Icon / Avatar -->
                            <div class="relative mx-auto mb-3.5 h-20 w-20 sm:h-24 sm:w-24">
                                @if($savedUser->avatar_url)
                                    <img src="{{ $savedUser->avatar_url }}" alt="{{ $savedUser->name }}" class="h-full w-full rounded-full object-cover ring-4 ring-brand-500/20 shadow-md group-hover:scale-105 group-hover:ring-brand-500/40 transition duration-300">
                                @else
                                    <div class="flex h-full w-full items-center justify-center rounded-full bg-brand-700 text-2xl sm:text-3xl font-bold text-white shadow-md ring-4 ring-brand-500/20 group-hover:scale-105 group-hover:ring-brand-500/40 transition duration-300 dark:bg-brand-600">
                                        {{ strtoupper(substr($savedUser->name, 0, 2)) }}
                                    </div>
                                @endif
                            </div>

                            <h3 class="text-base sm:text-lg font-bold text-slate-950 dark:text-white group-hover:text-brand-700 dark:group-hover:text-brand-300 transition">
                                {{ $savedUser->name }}
                            </h3>
                            <p class="mt-0.5 text-xs text-slate-500 dark:text-slate-400 font-medium">
                                {{ match($savedUser->role) {
                                    'employer' => 'Mitra UMKM',
                                    'admin' => 'Administrator',
                                    default => 'Pencari Kerja'
                                } }} · <span class="font-mono text-brand-700 dark:text-brand-400 font-semibold">@<span>{{ $savedUser->username ?: $savedUser->email }}</span></span>
                            </p>

                            <!-- Primary Action Button inside card -->
                            <div class="mt-4 inline-flex min-h-11 w-full items-center justify-center gap-2 rounded-xl bg-brand-700 px-5 py-2.5 sm:py-3 text-sm font-bold text-white shadow-md sm:shadow-lg shadow-brand-900/15 transition group-hover:bg-brand-800">
                                <span>Masuk ke dashboard</span>
                                <span class="material-symbols-outlined text-[19px] transition group-hover:translate-x-1">arrow_forward</span>
                            </div>
                        </button>
                    </form>

                    <!-- Actions underneath: Tombol ringkas biru untuk keluar akun & opsi ganti akun -->
                    <div class="mt-5 pt-4 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between gap-3 text-xs">
                        <form action="{{ route('login.forget_device') }}" method="POST" class="shrink-0">
                            @csrf
                            <button type="submit" class="inline-flex items-center gap-1.5 rounded-xl bg-blue-50 px-3 py-1.5 text-xs font-bold text-blue-600 hover:bg-blue-100 hover:text-blue-700 dark:bg-blue-950/50 dark:border dark:border-blue-900/60 dark:text-blue-400 dark:hover:bg-blue-900/60 dark:hover:text-blue-300 transition cursor-pointer" title="Keluarkan akun ini dari perangkat dan hapus kredensial cepat">
                                <span class="material-symbols-outlined text-[16px]">logout</span>
                                <span>Keluar Akun</span>
                            </button>
                        </form>

                        <button type="button" id="showManualLoginBtn" class="inline-flex items-center gap-1 font-semibold text-slate-500 hover:text-brand-700 dark:text-slate-400 dark:hover:text-brand-300 transition hover:underline cursor-pointer">
                            <span>Gunakan akun lain</span>
                            <span class="material-symbols-outlined text-[15px]">arrow_forward</span>
                        </button>
                    </div>
                </div>
                <p class="mt-4 sm:mt-6 text-center text-xs sm:text-sm text-slate-600 dark:text-slate-300">Belum punya akun? <a href="{{ route('register') }}" class="font-bold text-brand-700 hover:text-brand-900 hover:underline dark:text-brand-300">Daftar gratis</a></p>
            </div>
        @endif

        <!-- SECTION 2: MANUAL EMAIL & PASSWORD LOGIN FORM -->
        <div id="manualLoginSection" class="{{ $showQuickLogin ? 'hidden' : '' }}">
            <div class="mb-4 sm:mb-7">
                <span class="hidden sm:inline-flex items-center gap-2 rounded-full bg-brand-50 px-3 py-1.5 text-xs font-bold uppercase tracking-[0.16em] text-brand-700 dark:bg-brand-950 dark:text-brand-200"><span class="h-1.5 w-1.5 rounded-full bg-coral-500"></span>Ruang kerja Anda</span>
                <h2 class="text-2xl sm:text-3xl font-bold tracking-tight text-slate-950 dark:text-white sm:mt-4">Masuk ke Akun</h2>
                <p class="mt-1 text-xs sm:text-sm text-slate-500 dark:text-slate-400">Masuk untuk mengakses layanan KerjaLokal.</p>
            </div>

            @if($hasSavedUser)
                <div class="mb-3 flex items-center justify-between">
                    <button type="button" id="showQuickLoginBtn" class="inline-flex items-center gap-1.5 text-xs font-semibold text-brand-700 hover:text-brand-900 dark:text-brand-400 dark:hover:text-brand-300 transition hover:underline cursor-pointer">
                        <span class="material-symbols-outlined text-[16px]">arrow_back</span>
                        <span>Masuk sebagai <strong>{{ $savedUser->name }}</strong></span>
                    </button>
                    <form action="{{ route('login.forget_device') }}" method="POST">
                        @csrf
                        <button type="submit" class="inline-flex items-center gap-1 rounded-lg bg-blue-50 px-2.5 py-1 text-[11px] font-bold text-blue-600 hover:bg-blue-100 dark:bg-blue-950/50 dark:text-blue-400 transition cursor-pointer" title="Keluarkan akun tersimpan dari perangkat">
                            <span class="material-symbols-outlined text-[14px]">logout</span>
                            <span>Keluar Akun</span>
                        </button>
                    </form>
                </div>
            @endif

            <form id="loginForm" action="{{ route('login') }}" method="POST" class="space-y-4 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm sm:space-y-5 sm:rounded-3xl sm:p-7 sm:shadow-[0_24px_70px_-40px_rgba(15,23,42,0.45)] dark:border-slate-800 dark:bg-slate-900">
                @csrf
                <div>
                    <label for="email" class="mb-1.5 block text-xs sm:text-sm font-bold text-slate-800 dark:text-slate-100">Email atau nama pengguna</label>
                    <div class="relative">
                        <span class="material-symbols-outlined pointer-events-none absolute left-3.5 top-1/2 -translate-y-1/2 text-[20px] text-slate-400">person</span>
                        <input id="email" name="email" type="text" value="{{ old('email') }}" autocomplete="username" autofocus required class="min-h-11 sm:min-h-12 w-full rounded-xl border bg-slate-50 py-2.5 sm:py-3 pl-11 pr-4 text-sm text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-brand-500 focus:bg-white focus:ring-4 focus:ring-brand-100 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-100 dark:placeholder:text-slate-500 dark:focus:bg-slate-900 dark:focus:ring-brand-900/60 {{ ($errors->has('email') || $errors->has('password')) ? 'border-rose-500' : 'border-slate-200' }}" placeholder="nama@email.com">
                    </div>
                    @error('email')<p class="mt-1.5 flex items-center gap-1 text-xs font-semibold text-rose-600" role="alert"><span class="material-symbols-outlined text-[15px]">error</span>{{ $message }}</p>@enderror
                </div>

                <div>
                    <div class="mb-1.5 flex items-center justify-between">
                        <label for="password" class="text-xs sm:text-sm font-bold text-slate-800 dark:text-slate-100">Kata sandi</label>
                        <span class="hidden sm:inline text-xs text-slate-400">Minimal 8 karakter</span>
                    </div>
                    <div class="relative">
                        <span class="material-symbols-outlined pointer-events-none absolute left-3.5 top-1/2 -translate-y-1/2 text-[20px] text-slate-400">lock</span>
                        <input id="password" name="password" type="password" autocomplete="current-password" required class="min-h-11 sm:min-h-12 w-full rounded-xl border bg-slate-50 py-2.5 sm:py-3 pl-11 pr-12 text-sm text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-brand-500 focus:bg-white focus:ring-4 focus:ring-brand-100 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-100 dark:placeholder:text-slate-500 dark:focus:bg-slate-900 dark:focus:ring-brand-900/60 {{ $errors->has('password') ? 'border-rose-500' : 'border-slate-200' }}" placeholder="Masukkan kata sandi">
                        <button type="button" data-password-toggle="password" class="absolute right-2 top-1/2 inline-flex h-9 w-9 -translate-y-1/2 items-center justify-center rounded-lg text-slate-400 hover:bg-slate-100 hover:text-slate-700 dark:hover:bg-slate-800 dark:hover:text-white" aria-label="Tampilkan kata sandi"><span class="material-symbols-outlined text-[20px]">visibility</span></button>
                    </div>
                    @error('password')<p class="mt-1.5 flex items-center gap-1 text-xs font-semibold text-rose-600" role="alert"><span class="material-symbols-outlined text-[15px]">error</span>{{ $message }}</p>@enderror
                </div>

                <label class="flex cursor-pointer items-center gap-2.5 text-xs sm:text-sm text-slate-600 dark:text-slate-300">
                    <input type="checkbox" name="remember" value="1" @checked(old('remember', true)) class="h-4 w-4 rounded border-slate-300 text-brand-600 focus:ring-brand-500">
                    <span>Ingat saya di perangkat ini</span>
                </label>
                <button id="loginSubmitBtn" type="submit" class="group inline-flex min-h-11 sm:min-h-12 w-full items-center justify-center gap-2 rounded-xl bg-brand-700 px-5 py-2.5 sm:py-3 text-sm font-bold text-white shadow-md sm:shadow-lg shadow-brand-900/15 transition hover:-translate-y-0.5 hover:bg-brand-800 focus:outline-none focus:ring-4 focus:ring-brand-200 dark:focus:ring-brand-900"><span>Masuk</span><span class="material-symbols-outlined text-[19px] transition group-hover:translate-x-0.5">arrow_forward</span></button>
            </form>
            <p class="mt-4 sm:mt-6 text-center text-xs sm:text-sm text-slate-600 dark:text-slate-300">Belum punya akun? <a href="{{ route('register') }}" class="font-bold text-brand-700 hover:text-brand-900 hover:underline dark:text-brand-300">Daftar gratis</a></p>
        </div>
    </div>

    <x-auth-loading-overlay title="Sedang masuk..." />
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        @if(session('account_banned'))
            const ban = {{ Js::from(session('account_banned')) }};
            const escapeModalText = value => String(value || '')
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');
            window.showAppAlert({
                title: 'Akun Ditangguhkan / Kena Ban',
                message: `<div class="space-y-2">
                    <p>Akun Anda <strong>"${escapeModalText(ban.name)}"</strong> (<span class="font-mono text-brand-700 dark:text-brand-300 font-semibold">@${escapeModalText(ban.username)}</span>) sedang <strong>dibekukan / terkena sanksi pemblokiran</strong> oleh Administrator.</p>
                    <div class="p-3 rounded-2xl bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-900/50 text-rose-900 dark:text-rose-200 text-xs space-y-1">
                        <div><strong>Masa Berlaku:</strong> ${escapeModalText(ban.duration)}</div>
                        <div><strong>Alasan Pemblokiran:</strong> "${escapeModalText(ban.reason || 'Pelanggaran ketentuan sistem.')}"</div>
                    </div>
                    <p class="text-[11px] text-slate-500 dark:text-slate-400">Selama masa penangguhan, Anda tidak dapat masuk atau mengakses layanan KerjaLokal. Hubungi tim administrator jika terdapat sanggahan atau kekeliruan.</p>
                </div>`,
                confirmText: 'Saya Mengerti',
                type: 'danger',
                icon: 'block',
                allowHtml: true
            });
        @elseif(session('error'))
            window.showAppAlert({
                title: 'Pemberitahuan Akun',
                message: {{ Js::from(session('error')) }},
                confirmText: 'Tutup',
                type: 'danger',
                icon: 'error'
            });
        @endif

        // Auto-dismiss Flash Message ("Anda telah berhasil keluar dari akun") dengan timeout 2 detik
        const loginStatusAlert = document.getElementById('loginStatusAlert');
        if (loginStatusAlert) {
            setTimeout(() => {
                loginStatusAlert.style.transition = 'opacity 0.4s ease, transform 0.4s ease, max-height 0.4s ease, margin 0.4s ease, padding 0.4s ease';
                loginStatusAlert.style.overflow = 'hidden';
                loginStatusAlert.style.maxHeight = loginStatusAlert.offsetHeight + 'px';

                requestAnimationFrame(() => {
                    loginStatusAlert.style.opacity = '0';
                    loginStatusAlert.style.transform = 'translateY(-6px)';
                    loginStatusAlert.style.maxHeight = '0px';
                    loginStatusAlert.style.marginTop = '0px';
                    loginStatusAlert.style.marginBottom = '0px';
                    loginStatusAlert.style.paddingTop = '0px';
                    loginStatusAlert.style.paddingBottom = '0px';
                    loginStatusAlert.style.borderWidth = '0px';
                });

                setTimeout(() => {
                    loginStatusAlert.remove();
                }, 450);
            }, 2000);
        }

        // Toggle antara Tampilan Akun Tersimpan (Quick Login) dan Manual Form
        const showManualLoginBtn = document.getElementById('showManualLoginBtn');
        const showQuickLoginBtn = document.getElementById('showQuickLoginBtn');
        const quickLoginSection = document.getElementById('quickLoginSection');
        const manualLoginSection = document.getElementById('manualLoginSection');

        if (showManualLoginBtn && quickLoginSection && manualLoginSection) {
            showManualLoginBtn.addEventListener('click', () => {
                quickLoginSection.classList.add('hidden');
                manualLoginSection.classList.remove('hidden');
                const emailInput = document.getElementById('email');
                if (emailInput) {
                    emailInput.focus();
                }
            });
        }

        if (showQuickLoginBtn && quickLoginSection && manualLoginSection) {
            showQuickLoginBtn.addEventListener('click', () => {
                manualLoginSection.classList.add('hidden');
                quickLoginSection.classList.remove('hidden');
            });
        }

        // Quick Login Loading Handler
        const quickLoginForm = document.getElementById('quickLoginForm');
        const quickLoginSubmitBtn = document.getElementById('quickLoginSubmitBtn');
        const authLoadingOverlay = document.getElementById('authLoadingOverlay');

        if (quickLoginForm && quickLoginSubmitBtn) {
            quickLoginForm.addEventListener('submit', () => {
                quickLoginSubmitBtn.disabled = true;
                quickLoginSubmitBtn.classList.add('opacity-80', 'cursor-wait');
                if (authLoadingOverlay) {
                    authLoadingOverlay.classList.remove('hidden');
                    authLoadingOverlay.classList.add('flex');
                }
            });
        }

        // Fitur "Ingat saya di perangkat ini" (Browser Session & Credential Persistence)
        const rememberedInputKey = 'kerjalokal_remembered_login';
        const emailField = document.getElementById('email');
        const rememberCheckbox = document.querySelector('input[name="remember"]');

        if (emailField && rememberCheckbox) {
            const savedLogin = localStorage.getItem(rememberedInputKey);
            if (savedLogin && !emailField.value) {
                emailField.value = savedLogin;
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
        }

        // Animasi Loading Masuk ke Dashboard (Manual Form)
        const loginForm = document.getElementById('loginForm');
        const loginSubmitBtn = document.getElementById('loginSubmitBtn');

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
                if (quickLoginSubmitBtn) {
                    quickLoginSubmitBtn.disabled = false;
                    quickLoginSubmitBtn.classList.remove('opacity-80', 'cursor-wait');
                }
                if (authLoadingOverlay) {
                    authLoadingOverlay.classList.add('hidden');
                    authLoadingOverlay.classList.remove('flex');
                }
            });
        }
    });
</script>
@endpush
