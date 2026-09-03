@extends('layouts.auth')

@section('title', 'Masuk ke KerjaLokal')

@section('content')
    <div class="mx-auto max-w-md" data-reveal>
        <div class="mb-8">
            <span class="inline-flex items-center gap-2 rounded-full bg-brand-50 px-3 py-1.5 text-xs font-bold uppercase tracking-[0.16em] text-brand-700 dark:bg-brand-950 dark:text-brand-200"><span class="h-1.5 w-1.5 rounded-full bg-coral-500"></span>Ruang kerja Anda</span>
            <h2 class="mt-5 text-3xl font-bold tracking-tight text-slate-950 dark:text-white sm:text-4xl">Selamat datang kembali.</h2>
            <p class="mt-3 text-sm leading-6 text-slate-600 dark:text-slate-300">Masuk untuk melanjutkan proses rekrutmen sebagai pencari kerja, mitra UMKM, atau admin.</p>
        </div>

        <form action="{{ route('login') }}" method="POST" class="space-y-5 rounded-3xl border border-slate-200 bg-white p-5 shadow-[0_24px_70px_-40px_rgba(15,23,42,0.45)] dark:border-slate-800 dark:bg-slate-900 sm:p-7">
            @csrf
            <div>
                <label for="email" class="mb-2 block text-sm font-bold text-slate-800 dark:text-slate-100">Alamat email</label>
                <div class="relative">
                    <span class="material-symbols-outlined pointer-events-none absolute left-3.5 top-1/2 -translate-y-1/2 text-[20px] text-slate-400">mail</span>
                    <input id="email" name="email" type="email" value="{{ old('email') }}" autocomplete="email" autofocus required class="min-h-12 w-full rounded-xl border bg-slate-50 py-3 pl-11 pr-4 text-sm outline-none transition placeholder:text-slate-400 focus:border-brand-500 focus:bg-white focus:ring-4 focus:ring-brand-100 dark:bg-slate-950 dark:focus:bg-slate-950 dark:focus:ring-brand-900/60 {{ $errors->has('email') ? 'border-rose-500' : 'border-slate-200 dark:border-slate-700' }}" placeholder="nama@email.com">
                </div>
                @error('email')<p class="mt-1.5 flex items-center gap-1 text-xs font-semibold text-rose-600" role="alert"><span class="material-symbols-outlined text-[15px]">error</span>{{ $message }}</p>@enderror
            </div>

            <div>
                <div class="mb-2 flex items-center justify-between"><label for="password" class="text-sm font-bold text-slate-800 dark:text-slate-100">Kata sandi</label><span class="text-xs text-slate-500">Minimal 8 karakter</span></div>
                <div class="relative">
                    <span class="material-symbols-outlined pointer-events-none absolute left-3.5 top-1/2 -translate-y-1/2 text-[20px] text-slate-400">lock</span>
                    <input id="password" name="password" type="password" autocomplete="current-password" required class="min-h-12 w-full rounded-xl border bg-slate-50 py-3 pl-11 pr-12 text-sm outline-none transition placeholder:text-slate-400 focus:border-brand-500 focus:bg-white focus:ring-4 focus:ring-brand-100 dark:bg-slate-950 dark:focus:bg-slate-950 dark:focus:ring-brand-900/60 {{ $errors->has('password') ? 'border-rose-500' : 'border-slate-200 dark:border-slate-700' }}" placeholder="Masukkan kata sandi">
                    <button type="button" data-password-toggle="password" class="absolute right-2 top-1/2 inline-flex h-9 w-9 -translate-y-1/2 items-center justify-center rounded-lg text-slate-400 hover:bg-slate-100 hover:text-slate-700 dark:hover:bg-slate-800 dark:hover:text-white" aria-label="Tampilkan kata sandi"><span class="material-symbols-outlined text-[20px]">visibility</span></button>
                </div>
                @error('password')<p class="mt-1.5 flex items-center gap-1 text-xs font-semibold text-rose-600" role="alert"><span class="material-symbols-outlined text-[15px]">error</span>{{ $message }}</p>@enderror
            </div>

            <label class="flex cursor-pointer items-center gap-3 text-sm text-slate-600 dark:text-slate-300"><input type="checkbox" name="remember" value="1" @checked(old('remember')) class="h-4 w-4 rounded border-slate-300 text-brand-600 focus:ring-brand-500">Ingat saya di perangkat ini</label>
            <button type="submit" class="group inline-flex min-h-12 w-full items-center justify-center gap-2 rounded-xl bg-brand-700 px-5 py-3 text-sm font-bold text-white shadow-lg shadow-brand-900/15 transition hover:-translate-y-0.5 hover:bg-brand-800 focus:outline-none focus:ring-4 focus:ring-brand-200 dark:focus:ring-brand-900">Masuk ke dashboard<span class="material-symbols-outlined text-[19px] transition group-hover:translate-x-0.5">arrow_forward</span></button>
        </form>
        <p class="mt-6 text-center text-sm text-slate-600 dark:text-slate-300">Belum punya akun? <a href="{{ route('register') }}" class="font-bold text-brand-700 hover:text-brand-900 hover:underline dark:text-brand-300">Daftar gratis</a></p>
    </div>
@endsection
