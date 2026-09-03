@extends('layouts.auth')

@section('title', 'Daftar KerjaLokal')

@section('content')
    <div class="mx-auto max-w-xl" data-reveal>
        <div class="mb-6">
            <span class="inline-flex items-center gap-2 rounded-full bg-brand-50 px-3 py-1.5 text-xs font-bold uppercase tracking-[0.16em] text-brand-700 dark:bg-brand-950 dark:text-brand-200"><span class="h-1.5 w-1.5 rounded-full bg-coral-500"></span>Mulai di KerjaLokal</span>
            <h2 class="mt-4 text-3xl font-bold tracking-tight text-slate-950 dark:text-white sm:text-4xl">Buat akun sesuai peran.</h2>
            <p class="mt-2 text-sm leading-6 text-slate-600 dark:text-slate-300">Pilih pencari kerja untuk melamar atau mitra UMKM untuk membuka lowongan.</p>
        </div>

        <form action="{{ route('register') }}" method="POST" class="space-y-5 rounded-3xl border border-slate-200 bg-white p-5 shadow-[0_24px_70px_-40px_rgba(15,23,42,0.45)] dark:border-slate-800 dark:bg-slate-900 sm:p-7">
            @csrf
            <fieldset>
                <legend class="mb-2 text-sm font-bold text-slate-800 dark:text-slate-100">Daftar sebagai</legend>
                <input type="hidden" name="role" value="{{ old('role', 'jobseeker') }}" data-role-input>
                <div class="grid grid-cols-2 gap-2 rounded-2xl bg-slate-100 p-1.5 dark:bg-slate-950">
                    <button type="button" data-role-option="jobseeker" class="auth-role-option flex min-h-12 items-center justify-center gap-2 rounded-xl px-3 text-sm font-bold text-slate-600 transition hover:text-brand-700 dark:text-slate-300"><span class="material-symbols-outlined text-[20px]">person</span>Pencari kerja</button>
                    <button type="button" data-role-option="employer" class="auth-role-option flex min-h-12 items-center justify-center gap-2 rounded-xl px-3 text-sm font-bold text-slate-600 transition hover:text-brand-700 dark:text-slate-300"><span class="material-symbols-outlined text-[20px]">storefront</span>Mitra UMKM</button>
                </div>
                @error('role')<p class="mt-1.5 text-xs font-semibold text-rose-600" role="alert">{{ $message }}</p>@enderror
            </fieldset>

            <div class="grid gap-5 sm:grid-cols-2">
                <div>
                    <label for="name" class="mb-2 block text-sm font-bold text-slate-800 dark:text-slate-100">Nama lengkap</label>
                    <input id="name" name="name" type="text" value="{{ old('name') }}" autocomplete="name" required placeholder="Budi Santoso" class="min-h-12 w-full rounded-xl border bg-slate-50 px-4 py-3 text-sm outline-none transition focus:border-brand-500 focus:bg-white focus:ring-4 focus:ring-brand-100 dark:bg-slate-950 dark:focus:ring-brand-900/60 {{ $errors->has('name') ? 'border-rose-500' : 'border-slate-200 dark:border-slate-700' }}">
                    @error('name')<p class="mt-1.5 text-xs font-semibold text-rose-600" role="alert">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="phone" class="mb-2 block text-sm font-bold text-slate-800 dark:text-slate-100">Nomor telepon</label>
                    <input id="phone" name="phone" type="tel" value="{{ old('phone') }}" autocomplete="tel" placeholder="081234567890" class="min-h-12 w-full rounded-xl border bg-slate-50 px-4 py-3 text-sm outline-none transition focus:border-brand-500 focus:bg-white focus:ring-4 focus:ring-brand-100 dark:bg-slate-950 dark:focus:ring-brand-900/60 {{ $errors->has('phone') ? 'border-rose-500' : 'border-slate-200 dark:border-slate-700' }}">
                    @error('phone')<p class="mt-1.5 text-xs font-semibold text-rose-600" role="alert">{{ $message }}</p>@enderror
                </div>
            </div>

            <div data-business-field class="hidden">
                <label for="business_name" class="mb-2 block text-sm font-bold text-slate-800 dark:text-slate-100">Nama usaha</label>
                <input id="business_name" name="business_name" type="text" value="{{ old('business_name') }}" autocomplete="organization" placeholder="Kedai Kopi Sudut Temu" class="min-h-12 w-full rounded-xl border bg-slate-50 px-4 py-3 text-sm outline-none transition focus:border-brand-500 focus:bg-white focus:ring-4 focus:ring-brand-100 dark:bg-slate-950 dark:focus:ring-brand-900/60 {{ $errors->has('business_name') ? 'border-rose-500' : 'border-slate-200 dark:border-slate-700' }}">
                @error('business_name')<p class="mt-1.5 text-xs font-semibold text-rose-600" role="alert">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="email" class="mb-2 block text-sm font-bold text-slate-800 dark:text-slate-100">Alamat email</label>
                <input id="email" name="email" type="email" value="{{ old('email') }}" autocomplete="email" required placeholder="nama@email.com" class="min-h-12 w-full rounded-xl border bg-slate-50 px-4 py-3 text-sm outline-none transition focus:border-brand-500 focus:bg-white focus:ring-4 focus:ring-brand-100 dark:bg-slate-950 dark:focus:ring-brand-900/60 {{ $errors->has('email') ? 'border-rose-500' : 'border-slate-200 dark:border-slate-700' }}">
                @error('email')<p class="mt-1.5 text-xs font-semibold text-rose-600" role="alert">{{ $message }}</p>@enderror
            </div>

            <div class="grid gap-5 sm:grid-cols-2">
                <div>
                    <label for="password" class="mb-2 block text-sm font-bold text-slate-800 dark:text-slate-100">Kata sandi</label>
                    <div class="relative"><input id="password" name="password" type="password" autocomplete="new-password" required placeholder="Minimal 8 karakter" class="min-h-12 w-full rounded-xl border bg-slate-50 px-4 py-3 pr-11 text-sm outline-none transition focus:border-brand-500 focus:bg-white focus:ring-4 focus:ring-brand-100 dark:bg-slate-950 dark:focus:ring-brand-900/60 {{ $errors->has('password') ? 'border-rose-500' : 'border-slate-200 dark:border-slate-700' }}"><button type="button" data-password-toggle="password" class="absolute right-2 top-1/2 inline-flex h-9 w-9 -translate-y-1/2 items-center justify-center rounded-lg text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800" aria-label="Tampilkan kata sandi"><span class="material-symbols-outlined text-[20px]">visibility</span></button></div>
                    @error('password')<p class="mt-1.5 text-xs font-semibold text-rose-600" role="alert">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="password_confirmation" class="mb-2 block text-sm font-bold text-slate-800 dark:text-slate-100">Konfirmasi sandi</label>
                    <div class="relative"><input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" required placeholder="Ulangi kata sandi" class="min-h-12 w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 pr-11 text-sm outline-none transition focus:border-brand-500 focus:bg-white focus:ring-4 focus:ring-brand-100 dark:border-slate-700 dark:bg-slate-950 dark:focus:ring-brand-900/60"><button type="button" data-password-toggle="password_confirmation" class="absolute right-2 top-1/2 inline-flex h-9 w-9 -translate-y-1/2 items-center justify-center rounded-lg text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800" aria-label="Tampilkan konfirmasi kata sandi"><span class="material-symbols-outlined text-[20px]">visibility</span></button></div>
                </div>
            </div>
            <p class="text-xs leading-5 text-slate-500">Gunakan minimal 8 karakter yang memuat huruf dan angka. Pendaftaran tidak dipungut biaya.</p>
            <button type="submit" class="group inline-flex min-h-12 w-full items-center justify-center gap-2 rounded-xl bg-brand-700 px-5 py-3 text-sm font-bold text-white shadow-lg shadow-brand-900/15 transition hover:-translate-y-0.5 hover:bg-brand-800 focus:outline-none focus:ring-4 focus:ring-brand-200 dark:focus:ring-brand-900">Buat akun<span class="material-symbols-outlined text-[19px] transition group-hover:translate-x-0.5">arrow_forward</span></button>
        </form>
        <p class="mt-6 text-center text-sm text-slate-600 dark:text-slate-300">Sudah punya akun? <a href="{{ route('login') }}" class="font-bold text-brand-700 hover:text-brand-900 hover:underline dark:text-brand-300">Masuk</a></p>
    </div>
@endsection
