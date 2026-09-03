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
@section('portal_user_role', 'Pemilik usaha')
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
    <a href="{{ route('employer.dashboard') }}" class="inline-flex min-h-10 items-center gap-2 rounded-xl px-3.5 py-2 text-sm font-semibold transition {{ request()->routeIs('employer.dashboard') ? 'bg-brand-700 text-white dark:bg-brand-500 dark:text-brand-950' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-950 dark:text-slate-300 dark:hover:bg-slate-800 dark:hover:text-white' }}">
        <span class="material-symbols-outlined text-[19px]">work</span>
        Lowongan
    </a>
    <a href="{{ route('employer.applications.index') }}" class="inline-flex min-h-10 items-center gap-2 rounded-xl px-3.5 py-2 text-sm font-semibold transition {{ request()->routeIs('employer.applications.*') ? 'bg-brand-700 text-white dark:bg-brand-500 dark:text-brand-950' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-950 dark:text-slate-300 dark:hover:bg-slate-800 dark:hover:text-white' }}">
        <span class="material-symbols-outlined text-[19px]">group</span>
        Pelamar
    </a>
@endsection

@section('portal_mobile_navigation')
    <a href="{{ route('employer.dashboard') }}" class="flex min-h-11 items-center gap-3 rounded-xl px-3 py-2 text-sm font-semibold {{ request()->routeIs('employer.dashboard') ? 'bg-brand-50 text-brand-800 dark:bg-brand-950 dark:text-brand-200' : 'text-slate-700 dark:text-slate-200' }}"><span class="material-symbols-outlined">work</span>Kelola lowongan</a>
    <a href="{{ route('employer.applications.index') }}" class="flex min-h-11 items-center gap-3 rounded-xl px-3 py-2 text-sm font-semibold {{ request()->routeIs('employer.applications.*') ? 'bg-brand-50 text-brand-800 dark:bg-brand-950 dark:text-brand-200' : 'text-slate-700 dark:text-slate-200' }}"><span class="material-symbols-outlined">group</span>Pelamar masuk</a>
    <a href="{{ route('employer.jobs.create') }}" class="flex min-h-11 items-center gap-3 rounded-xl bg-brand-700 px-3 py-2 text-sm font-semibold text-white dark:bg-brand-500 dark:text-brand-950"><span class="material-symbols-outlined">add</span>Pasang lowongan</a>
@endsection
