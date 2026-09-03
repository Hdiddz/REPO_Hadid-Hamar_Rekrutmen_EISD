@extends('layouts.app')

@section('title', 'Profil Saya | KerjaLokal')

@section('content')
    <div class="grid gap-6 lg:grid-cols-[340px_1fr]">
        <aside class="rounded-3xl bg-brand-950 p-6 text-white lg:self-start" data-reveal>
            <div class="relative h-20 w-20 rounded-2xl overflow-hidden ring-4 ring-white/10 shadow-lg shrink-0">
                @if($user->avatar_url)
                    <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}" class="h-full w-full object-cover">
                @else
                    <div class="grid h-full w-full place-items-center bg-white/10 text-2xl font-bold text-white">
                        {{ strtoupper(substr($user->name, 0, 2)) }}
                    </div>
                @endif
            </div>
            <h1 class="mt-5 text-2xl font-bold">{{ $user->name }}</h1>
            <p class="mt-0.5 text-sm font-mono text-brand-300">@<span>{{ $user->username }}</span></p>
            <p class="mt-1 text-sm text-brand-200">Pencari kerja</p>
            <dl class="mt-8 space-y-4 border-t border-white/10 pt-6 text-sm">
                <div><dt class="text-xs text-brand-300">Username</dt><dd class="mt-1 font-mono font-semibold">@<span>{{ $user->username }}</span></dd></div>
                <div><dt class="text-xs text-brand-300">Email</dt><dd class="mt-1 break-all font-semibold">{{ $user->email }}</dd></div>
                <div><dt class="text-xs text-brand-300">Nomor telepon</dt><dd class="mt-1 font-semibold">{{ $user->phone ?: 'Belum diisi' }}</dd></div>
                <div><dt class="text-xs text-brand-300">Bergabung</dt><dd class="mt-1 font-semibold">{{ $user->created_at->translatedFormat('d F Y') }}</dd></div>
            </dl>
            <div class="mt-6 pt-6 border-t border-white/10 flex flex-col gap-2.5">
                <a href="{{ route('settings.index') }}" class="inline-flex items-center gap-2 text-xs font-bold text-brand-200 hover:text-white transition">
                    <span class="material-symbols-outlined text-[16px]">photo_camera</span>
                    Ubah Foto Profil (1080x1080)
                </a>
                <a href="{{ route('settings.index') }}" class="inline-flex items-center gap-2 text-xs font-bold text-brand-200 hover:text-white transition">
                    <span class="material-symbols-outlined text-[16px]">tune</span>
                    Ubah Username &amp; Pengaturan
                </a>
            </div>
        </aside>
        <div class="space-y-5">
            <section class="grid gap-4 sm:grid-cols-2" data-reveal><div class="rounded-2xl border border-slate-200 bg-white p-5 dark:border-slate-800 dark:bg-slate-900"><span class="material-symbols-outlined text-brand-600">assignment</span><strong class="mt-4 block text-3xl">{{ $user->job_applications_count }}</strong><span class="text-sm text-slate-500">lamaran diajukan</span></div><div class="rounded-2xl border border-slate-200 bg-white p-5 dark:border-slate-800 dark:bg-slate-900"><span class="material-symbols-outlined text-brand-600">verified_user</span><strong class="mt-4 block text-3xl">Aktif</strong><span class="text-sm text-slate-500">status akun</span></div></section>
            <section class="rounded-2xl border border-slate-200 bg-white p-6 dark:border-slate-800 dark:bg-slate-900" data-reveal><h2 class="text-lg font-bold">Akses cepat</h2><div class="mt-5 grid gap-3 sm:grid-cols-2"><a href="{{ route('applications.index') }}" class="flex items-center justify-between rounded-xl bg-slate-50 p-4 font-bold transition hover:bg-brand-50 hover:text-brand-700 dark:bg-slate-950 dark:hover:bg-brand-950"><span class="flex items-center gap-2"><span class="material-symbols-outlined">assignment</span>Riwayat lamaran</span><span class="material-symbols-outlined text-[18px]">arrow_forward</span></a><a href="{{ route('jobs.index') }}" class="flex items-center justify-between rounded-xl bg-slate-50 p-4 font-bold transition hover:bg-brand-50 hover:text-brand-700 dark:bg-slate-950 dark:hover:bg-brand-950"><span class="flex items-center gap-2"><span class="material-symbols-outlined">search</span>Cari lowongan</span><span class="material-symbols-outlined text-[18px]">arrow_forward</span></a></div></section>
            <section class="rounded-2xl border border-amber-200 bg-amber-50 p-5 text-sm leading-6 text-amber-900 dark:border-amber-900 dark:bg-amber-950/30 dark:text-amber-200" data-reveal><strong>Privasi berkas:</strong> Resume hanya disimpan ketika Anda melamar. Berkas hanya dapat diakses oleh mitra pemilik lowongan dan administrator.</section>
        </div>
    </div>
@endsection
