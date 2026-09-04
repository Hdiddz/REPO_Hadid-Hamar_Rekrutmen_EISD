@props([
    'title' => 'Mohon tunggu...',
    'id' => 'authLoadingOverlay'
])

<div
    id="{{ $id }}"
    class="fixed inset-0 z-50 hidden items-center justify-center bg-white/80 px-4 backdrop-blur-sm dark:bg-slate-950/80"
    role="status"
    aria-live="polite"
    aria-busy="true"
>
    <div class="flex items-center gap-3 rounded-xl border border-slate-200 bg-white px-5 py-4 shadow-lg shadow-slate-900/10 dark:border-slate-700 dark:bg-slate-900 dark:shadow-black/20">
        <span class="material-symbols-outlined animate-spin text-[22px] text-brand-700 motion-reduce:animate-none dark:text-brand-300" aria-hidden="true">progress_activity</span>
        <span id="{{ $id }}Title" class="text-sm font-semibold text-slate-800 dark:text-slate-100">{{ $title }}</span>
    </div>
</div>
