@extends('layouts.portal')

@section('portal_home', route('admin.dashboard'))
@section('portal_role_label', 'Administrator')
@section('portal_user_initials', 'HF')
@section('portal_user_name', auth()->user()?->name ?? 'Hafiz')
@section('portal_user_role', 'Administrator')
@section('portal_user_context', 'Pengawasan dan tata kelola sistem')
@section('portal_context', 'Pusat tata kelola sistem')
@section('portal_icon', 'shield_person')
@section('portal_footer_name', 'administrator')

@php
    $pendingReportsCount = \App\Models\JobReport::where('status', 'pending')->count();
@endphp

@section('portal_navigation')
    <a href="{{ route('admin.dashboard') }}" class="inline-flex min-h-10 items-center gap-2 rounded-xl px-3.5 py-2 text-sm font-semibold whitespace-nowrap shrink-0 transition {{ request()->routeIs('admin.dashboard') ? 'bg-brand-700 text-white dark:bg-brand-500 dark:text-brand-950' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-950 dark:text-slate-300 dark:hover:bg-slate-800 dark:hover:text-white' }}">
        <span class="material-symbols-outlined text-[19px]">dashboard</span>
        Dashboard
    </a>
    <a href="{{ route('admin.jobs.index') }}" class="inline-flex min-h-10 items-center gap-2 rounded-xl px-3.5 py-2 text-sm font-semibold whitespace-nowrap shrink-0 transition {{ request()->routeIs('admin.jobs.*') ? 'bg-brand-700 text-white dark:bg-brand-500 dark:text-brand-950' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-950 dark:text-slate-300 dark:hover:bg-slate-800 dark:hover:text-white' }}">
        <span class="material-symbols-outlined text-[19px]">work</span>
        Lowongan
    </a>
    <a href="{{ route('admin.reports.index') }}" class="inline-flex min-h-10 items-center gap-2 rounded-xl px-3.5 py-2 text-sm font-semibold whitespace-nowrap shrink-0 transition {{ request()->routeIs('admin.reports.*') ? 'bg-brand-700 text-white dark:bg-brand-500 dark:text-brand-950' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-950 dark:text-slate-300 dark:hover:bg-slate-800 dark:hover:text-white' }}">
        <span class="material-symbols-outlined text-[19px]">flag</span>
        <span>Laporan</span>
        @if($pendingReportsCount > 0)
            <span class="ml-1 rounded-full bg-rose-500 px-1.5 py-0.5 text-[10px] font-extrabold text-white">{{ $pendingReportsCount }}</span>
        @endif
    </a>
    <a href="{{ route('admin.users.index') }}" class="inline-flex min-h-10 items-center gap-2 rounded-xl px-3.5 py-2 text-sm font-semibold whitespace-nowrap shrink-0 transition {{ request()->routeIs('admin.users.*') ? 'bg-brand-700 text-white dark:bg-brand-500 dark:text-brand-950' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-950 dark:text-slate-300 dark:hover:bg-slate-800 dark:hover:text-white' }}">
        <span class="material-symbols-outlined text-[19px]">group</span>
        Pengguna
    </a>
@endsection

@section('portal_mobile_navigation')
    <a href="{{ route('admin.dashboard') }}" class="flex min-h-11 items-center gap-3 rounded-xl px-3 py-2 text-sm font-semibold {{ request()->routeIs('admin.dashboard') ? 'bg-brand-50 text-brand-800 dark:bg-brand-950 dark:text-brand-200' : 'text-slate-700 dark:text-slate-200' }}"><span class="material-symbols-outlined">dashboard</span>Dashboard</a>
    <a href="{{ route('admin.jobs.index') }}" class="flex min-h-11 items-center gap-3 rounded-xl px-3 py-2 text-sm font-semibold {{ request()->routeIs('admin.jobs.*') ? 'bg-brand-50 text-brand-800 dark:bg-brand-950 dark:text-brand-200' : 'text-slate-700 dark:text-slate-200' }}"><span class="material-symbols-outlined">work</span>Semua lowongan</a>
    <a href="{{ route('admin.reports.index') }}" class="flex min-h-11 items-center justify-between rounded-xl px-3 py-2 text-sm font-semibold {{ request()->routeIs('admin.reports.*') ? 'bg-brand-50 text-brand-800 dark:bg-brand-950 dark:text-brand-200' : 'text-slate-700 dark:text-slate-200' }}">
        <span class="flex items-center gap-3"><span class="material-symbols-outlined">flag</span>Laporan</span>
        @if($pendingReportsCount > 0)
            <span class="rounded-full bg-rose-500 px-2 py-0.5 text-xs font-bold text-white">{{ $pendingReportsCount }}</span>
        @endif
    </a>
    <a href="{{ route('admin.users.index') }}" class="flex min-h-11 items-center gap-3 rounded-xl px-3 py-2 text-sm font-semibold {{ request()->routeIs('admin.users.*') ? 'bg-brand-50 text-brand-800 dark:bg-brand-950 dark:text-brand-200' : 'text-slate-700 dark:text-slate-200' }}"><span class="material-symbols-outlined">group</span>Pengguna</a>
@endsection
