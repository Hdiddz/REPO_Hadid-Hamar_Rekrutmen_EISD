@extends('layouts.portal')

@php
    $portalUserName = auth()->user()?->name ?? 'Hendra Wijaya';
    $portalNameParts = preg_split('/\s+/', trim($portalUserName));
    $portalInitials = strtoupper(substr($portalNameParts[0] ?? 'H', 0, 1).substr(end($portalNameParts) ?: 'W', 0, 1));
@endphp

@section('portal_home', route('employer.dashboard'))
@section('portal_role_label', 'Mitra UMKM')
@section('portal_user_initials', $portalInitials)
@section('portal_user_name', $portalUserName)
@section('portal_user_role', 'Mitra UMKM')
@section('portal_user_context', auth()->user()?->business_name ?? 'Mitra UMKM KerjaLokal')
@section('portal_context', 'Ruang kerja mitra UMKM')
@section('portal_icon', 'storefront')
@section('portal_footer_name', 'mitra UMKM')

@section('portal_primary_action')
    <a href="{{ route('employer.jobs.create') }}" class="portal-button-primary hidden sm:inline-flex">
        <span class="material-symbols-outlined text-[19px]">add</span>
        Pasang lowongan
    </a>
@endsection

@section('portal_navigation')
    <a href="{{ route('employer.dashboard') }}" class="inline-flex min-h-10 items-center gap-2 rounded-xl px-3.5 py-2 text-sm font-semibold whitespace-nowrap shrink-0 transition {{ request()->routeIs('employer.dashboard') ? 'bg-brand-700 text-white dark:bg-brand-500 dark:text-brand-950' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-950 dark:text-slate-300 dark:hover:bg-slate-800 dark:hover:text-white' }}">
        <span class="material-symbols-outlined text-[19px]">work</span>
        Lowongan Saya
    </a>

    <div class="relative shrink-0">
        <button type="button" class="inline-flex min-h-10 items-center gap-1.5 rounded-xl px-3.5 py-2 text-sm font-semibold whitespace-nowrap transition cursor-pointer {{ request()->routeIs('employer.applications.*') ? 'bg-brand-700 text-white dark:bg-brand-500 dark:text-brand-950' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-950 dark:text-slate-300 dark:hover:bg-slate-800 dark:hover:text-white' }}" data-menu-toggle="portal-employer-applicants-menu" aria-expanded="false" aria-haspopup="true">
            <span class="material-symbols-outlined text-[19px]">group</span>
            <span>Pelamar</span>
            <span class="material-symbols-outlined text-[17px] opacity-70">expand_more</span>
        </button>

        <div id="portal-employer-applicants-menu" data-menu class="absolute left-0 mt-2 hidden w-60 overflow-hidden rounded-2xl border border-slate-200 bg-white p-1.5 shadow-xl shadow-slate-900/10 dark:border-slate-800 dark:bg-slate-900 z-50">
            <a href="{{ route('employer.applications.index') }}" class="flex items-center gap-2.5 rounded-xl px-3 py-2.5 text-sm font-semibold transition {{ request()->routeIs('employer.applications.*') && !request('status') ? 'bg-brand-50 text-brand-800 dark:bg-brand-950/60 dark:text-brand-200' : 'text-slate-700 hover:bg-slate-100 dark:text-slate-200 dark:hover:bg-slate-800' }}">
                <span class="material-symbols-outlined text-[19px] text-slate-500 dark:text-slate-400">groups</span>
                <div>
                    <span class="block text-sm font-bold">Semua Pelamar</span>
                    <span class="block text-[11px] font-normal text-slate-500 dark:text-slate-400">Daftar seluruh lamaran masuk</span>
                </div>
            </a>
            <a href="{{ route('employer.applications.index', ['status' => 'interview']) }}" class="flex items-center gap-2.5 rounded-xl px-3 py-2.5 text-sm font-semibold transition {{ request('status') === 'interview' ? 'bg-indigo-50 text-indigo-800 dark:bg-indigo-950/60 dark:text-indigo-200' : 'text-slate-700 hover:bg-slate-100 dark:text-slate-200 dark:hover:bg-slate-800' }}">
                <span class="material-symbols-outlined text-[19px] text-indigo-600 dark:text-indigo-400">record_voice_over</span>
                <div>
                    <span class="block text-sm font-bold">Tahap Wawancara</span>
                    <span class="block text-[11px] font-normal text-slate-500 dark:text-slate-400">Jadwal & konfirmasi wawancara</span>
                </div>
            </a>
            <a href="{{ route('employer.applications.index', ['status' => 'accepted']) }}" class="flex items-center gap-2.5 rounded-xl px-3 py-2.5 text-sm font-semibold transition {{ request('status') === 'accepted' ? 'bg-emerald-50 text-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-200' : 'text-slate-700 hover:bg-slate-100 dark:text-slate-200 dark:hover:bg-slate-800' }}">
                <span class="material-symbols-outlined text-[19px] text-emerald-600 dark:text-emerald-400">how_to_reg</span>
                <div>
                    <span class="block text-sm font-bold">Peserta Diterima</span>
                    <span class="block text-[11px] font-normal text-slate-500 dark:text-slate-400">Kandidat yang telah lolos seleksi</span>
                </div>
            </a>
        </div>
    </div>

    <a href="{{ route('jobs.index') }}" class="inline-flex min-h-10 items-center gap-2 rounded-xl px-3.5 py-2 text-sm font-semibold whitespace-nowrap shrink-0 transition {{ request()->routeIs('jobs.*') ? 'bg-brand-700 text-white dark:bg-brand-500 dark:text-brand-950' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-950 dark:text-slate-300 dark:hover:bg-slate-800 dark:hover:text-white' }}">
        <span class="material-symbols-outlined text-[19px]">travel_explore</span>
        Lihat lowongan
    </a>
@endsection

@section('portal_mobile_navigation')
    <a href="{{ route('employer.dashboard') }}" class="flex min-h-11 items-center gap-3 rounded-xl px-3 py-2 text-sm font-semibold {{ request()->routeIs('employer.dashboard') ? 'bg-brand-50 text-brand-800 dark:bg-brand-950 dark:text-brand-200' : 'text-slate-700 dark:text-slate-200' }}"><span class="material-symbols-outlined">work</span>Kelola lowongan saya</a>
    <a href="{{ route('employer.applications.index') }}" class="flex min-h-11 items-center gap-3 rounded-xl px-3 py-2 text-sm font-semibold {{ request()->routeIs('employer.applications.*') && !request('status') ? 'bg-brand-50 text-brand-800 dark:bg-brand-950 dark:text-brand-200' : 'text-slate-700 dark:text-slate-200' }}"><span class="material-symbols-outlined">groups</span>Semua pelamar masuk</a>
    <a href="{{ route('employer.applications.index', ['status' => 'interview']) }}" class="flex min-h-11 items-center gap-3 rounded-xl px-3 py-2 text-sm font-semibold {{ request('status') === 'interview' ? 'bg-indigo-50 text-indigo-800 dark:bg-indigo-950 dark:text-indigo-200' : 'text-slate-700 dark:text-slate-200' }}"><span class="material-symbols-outlined text-indigo-600 dark:text-indigo-400">record_voice_over</span>Tahap wawancara</a>
    <a href="{{ route('employer.applications.index', ['status' => 'accepted']) }}" class="flex min-h-11 items-center gap-3 rounded-xl px-3 py-2 text-sm font-semibold {{ request('status') === 'accepted' ? 'bg-emerald-50 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-200' : 'text-slate-700 dark:text-slate-200' }}"><span class="material-symbols-outlined text-emerald-600 dark:text-emerald-400">how_to_reg</span>Peserta yang diterima</a>
    <a href="{{ route('jobs.index') }}" class="flex min-h-11 items-center gap-3 rounded-xl px-3 py-2 text-sm font-semibold {{ request()->routeIs('jobs.*') ? 'bg-brand-50 text-brand-800 dark:bg-brand-950 dark:text-brand-200' : 'text-slate-700 dark:text-slate-200' }}"><span class="material-symbols-outlined">travel_explore</span>Lihat lowongan</a>
    <a href="{{ route('employer.jobs.create') }}" class="flex min-h-11 items-center gap-3 rounded-xl bg-brand-700 px-3 py-2 text-sm font-semibold text-white dark:bg-brand-500 dark:text-brand-950"><span class="material-symbols-outlined">add</span>Pasang lowongan</a>
@endsection
