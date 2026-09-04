@extends('layouts.auth')

@section('title', 'Daftar KerjaLokal')

@section('content')
    <div class="mx-auto max-w-xl" data-reveal>
        <div class="mb-4 sm:mb-6">
            <span class="hidden sm:inline-flex items-center gap-2 rounded-full bg-brand-50 px-3 py-1.5 text-xs font-bold uppercase tracking-[0.16em] text-brand-700 dark:bg-brand-950 dark:text-brand-200"><span class="h-1.5 w-1.5 rounded-full bg-coral-500"></span>Mulai di KerjaLokal</span>
            <h2 class="text-2xl sm:text-3xl font-bold tracking-tight text-slate-950 dark:text-white sm:mt-4">Daftar Akun Baru</h2>
            <p class="mt-1 text-xs sm:text-sm text-slate-500 dark:text-slate-400">Pilih peran dan lengkapi data akun Anda.</p>
        </div>

        <form id="registerForm" action="{{ route('register') }}" method="POST" class="space-y-3.5 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm sm:space-y-5 sm:rounded-3xl sm:p-7 sm:shadow-[0_24px_70px_-40px_rgba(15,23,42,0.45)] dark:border-slate-800 dark:bg-slate-900">
            @csrf
            <fieldset>
                <legend class="mb-1.5 text-xs sm:text-sm font-bold text-slate-800 dark:text-slate-100">Daftar sebagai</legend>
                <input type="hidden" name="role" value="{{ old('role', 'jobseeker') }}" data-role-input>
                <div class="grid grid-cols-2 gap-2 rounded-2xl bg-slate-100 p-1.5 dark:bg-slate-950">
                    <button type="button" data-role-option="jobseeker" class="auth-role-option flex min-h-11 sm:min-h-12 items-center justify-center gap-2 rounded-xl px-3 text-xs sm:text-sm font-bold text-slate-600 transition hover:text-brand-700 dark:text-slate-300"><span class="material-symbols-outlined text-[19px]">person</span>Pencari kerja</button>
                    <button type="button" data-role-option="employer" class="auth-role-option flex min-h-11 sm:min-h-12 items-center justify-center gap-2 rounded-xl px-3 text-xs sm:text-sm font-bold text-slate-600 transition hover:text-brand-700 dark:text-slate-300"><span class="material-symbols-outlined text-[19px]">storefront</span>Mitra UMKM</button>
                </div>
                @error('role')<p class="mt-1.5 text-xs font-semibold text-rose-600" role="alert">{{ $message }}</p>@enderror
            </fieldset>

            <div class="grid gap-3.5 sm:gap-5 sm:grid-cols-2">
                <div>
                    <label for="name" class="mb-1.5 block text-xs sm:text-sm font-bold text-slate-800 dark:text-slate-100">Nama lengkap</label>
                    <input id="name" name="name" type="text" value="{{ old('name') }}" autocomplete="name" required placeholder="Budi Santoso" class="min-h-11 sm:min-h-12 w-full rounded-xl border bg-slate-50 px-4 py-2.5 sm:py-3 text-sm text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-brand-500 focus:bg-white focus:ring-4 focus:ring-brand-100 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-100 dark:placeholder:text-slate-500 dark:focus:bg-slate-900 dark:focus:ring-brand-900/60 {{ $errors->has('name') ? 'border-rose-500' : 'border-slate-200' }}">
                    @error('name')<p class="mt-1.5 text-xs font-semibold text-rose-600" role="alert">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="phone" class="mb-1.5 block text-xs sm:text-sm font-bold text-slate-800 dark:text-slate-100">Nomor telepon</label>
                    <input id="phone" name="phone" type="tel" value="{{ old('phone') }}" autocomplete="tel" placeholder="081234567890" maxlength="13" class="min-h-11 sm:min-h-12 w-full rounded-xl border bg-slate-50 px-4 py-2.5 sm:py-3 text-sm text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-brand-500 focus:bg-white focus:ring-4 focus:ring-brand-100 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-100 dark:placeholder:text-slate-500 dark:focus:bg-slate-900 dark:focus:ring-brand-900/60 {{ $errors->has('phone') ? 'border-rose-500' : 'border-slate-200' }}">
                    @error('phone')<p class="mt-1.5 text-xs font-semibold text-rose-600" role="alert">{{ $message }}</p>@enderror
                </div>
            </div>

            <div data-business-field class="hidden">
                <label for="business_name" class="mb-1.5 block text-xs sm:text-sm font-bold text-slate-800 dark:text-slate-100">Nama usaha</label>
                <input id="business_name" name="business_name" type="text" value="{{ old('business_name') }}" autocomplete="organization" placeholder="Kedai Kopi Sudut Temu" class="min-h-11 sm:min-h-12 w-full rounded-xl border bg-slate-50 px-4 py-2.5 sm:py-3 text-sm text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-brand-500 focus:bg-white focus:ring-4 focus:ring-brand-100 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-100 dark:placeholder:text-slate-500 dark:focus:bg-slate-900 dark:focus:ring-brand-900/60 {{ $errors->has('business_name') ? 'border-rose-500' : 'border-slate-200' }}">
                @error('business_name')<p class="mt-1.5 text-xs font-semibold text-rose-600" role="alert">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="email" class="mb-1.5 block text-xs sm:text-sm font-bold text-slate-800 dark:text-slate-100">Alamat email</label>
                <input id="email" name="email" type="email" value="{{ old('email') }}" autocomplete="email" required placeholder="nama@email.com" class="min-h-11 sm:min-h-12 w-full rounded-xl border bg-slate-50 px-4 py-2.5 sm:py-3 text-sm text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-brand-500 focus:bg-white focus:ring-4 focus:ring-brand-100 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-100 dark:placeholder:text-slate-500 dark:focus:bg-slate-900 dark:focus:ring-brand-900/60 {{ $errors->has('email') ? 'border-rose-500' : 'border-slate-200' }}">
                @error('email')<p class="mt-1.5 text-xs font-semibold text-rose-600" role="alert">{{ $message }}</p>@enderror
            </div>

            <div class="grid gap-3.5 sm:gap-5 sm:grid-cols-2">
                <div>
                    <label for="password" class="mb-1.5 block text-xs sm:text-sm font-bold text-slate-800 dark:text-slate-100">Kata sandi</label>
                    <div class="relative"><input id="password" name="password" type="password" autocomplete="new-password" required placeholder="Minimal 8 karakter" class="min-h-11 sm:min-h-12 w-full rounded-xl border bg-slate-50 px-4 py-2.5 sm:py-3 pr-11 text-sm text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-brand-500 focus:bg-white focus:ring-4 focus:ring-brand-100 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-100 dark:placeholder:text-slate-500 dark:focus:bg-slate-900 dark:focus:ring-brand-900/60 {{ $errors->has('password') ? 'border-rose-500' : 'border-slate-200' }}"><button type="button" data-password-toggle="password" class="absolute right-2 top-1/2 inline-flex h-9 w-9 -translate-y-1/2 items-center justify-center rounded-lg text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800" aria-label="Tampilkan kata sandi"><span class="material-symbols-outlined text-[20px]">visibility</span></button></div>
                    @error('password')<p class="mt-1.5 text-xs font-semibold text-rose-600" role="alert">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="password_confirmation" class="mb-1.5 block text-xs sm:text-sm font-bold text-slate-800 dark:text-slate-100">Konfirmasi sandi</label>
                    <div class="relative"><input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" required placeholder="Ulangi kata sandi" class="min-h-11 sm:min-h-12 w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 sm:py-3 pr-11 text-sm text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-brand-500 focus:bg-white focus:ring-4 focus:ring-brand-100 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-100 dark:placeholder:text-slate-500 dark:focus:bg-slate-900 dark:focus:ring-brand-900/60"><button type="button" data-password-toggle="password_confirmation" class="absolute right-2 top-1/2 inline-flex h-9 w-9 -translate-y-1/2 items-center justify-center rounded-lg text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800" aria-label="Tampilkan konfirmasi kata sandi"><span class="material-symbols-outlined text-[20px]">visibility</span></button></div>
                </div>
            </div>
            <p class="text-[11px] sm:text-xs leading-relaxed text-slate-500 dark:text-slate-400">Minimal 8 karakter kombinasi huruf & angka. Pendaftaran gratis.</p>
            <button id="registerSubmitBtn" type="submit" class="group inline-flex min-h-11 sm:min-h-12 w-full items-center justify-center gap-2 rounded-xl bg-brand-700 px-5 py-2.5 sm:py-3 text-sm font-bold text-white shadow-md sm:shadow-lg shadow-brand-900/15 transition hover:-translate-y-0.5 hover:bg-brand-800 focus:outline-none focus:ring-4 focus:ring-brand-200 dark:focus:ring-brand-900"><span>Daftar Sekarang</span><span class="material-symbols-outlined text-[19px] transition group-hover:translate-x-0.5">arrow_forward</span></button>
        </form>
        <p class="mt-4 sm:mt-6 text-center text-xs sm:text-sm text-slate-600 dark:text-slate-300">Sudah punya akun? <a href="{{ route('login') }}" class="font-bold text-brand-700 hover:text-brand-900 hover:underline dark:text-brand-300">Masuk</a></p>
    </div>

    <x-auth-loading-overlay id="registerLoadingOverlay" title="Menyiapkan akun..." />
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const registerForm = document.getElementById('registerForm');
        const registerSubmitBtn = document.getElementById('registerSubmitBtn');
        const registerLoadingOverlay = document.getElementById('registerLoadingOverlay');

        if (registerForm && registerSubmitBtn) {
            registerForm.addEventListener('submit', () => {
                if (!registerForm.checkValidity()) {
                    return;
                }

                registerSubmitBtn.disabled = true;
                registerSubmitBtn.classList.add('opacity-80', 'cursor-wait');
                registerSubmitBtn.innerHTML = `
                    <svg class="h-5 w-5 animate-spin text-white shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span>Mendaftarkan akun...</span>
                `;

                if (registerLoadingOverlay) {
                    registerLoadingOverlay.classList.remove('hidden');
                    registerLoadingOverlay.classList.add('flex');
                }
            });

            window.addEventListener('pageshow', (event) => {
                registerSubmitBtn.disabled = false;
                registerSubmitBtn.classList.remove('opacity-80', 'cursor-wait');
                registerSubmitBtn.innerHTML = `Buat akun<span class="material-symbols-outlined text-[19px] transition group-hover:translate-x-0.5">arrow_forward</span>`;
                if (registerLoadingOverlay) {
                    registerLoadingOverlay.classList.add('hidden');
                    registerLoadingOverlay.classList.remove('flex');
                }
            });
        }
    });
</script>
@endpush
