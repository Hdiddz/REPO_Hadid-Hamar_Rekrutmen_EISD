@props([
    'title' => 'Menghubungkan ke Dashboard...',
    'subtitle' => 'Mohon tunggu sebentar, kami sedang menyiapkan sesi akun Anda.',
    'id' => 'authLoadingOverlay'
])

<div id="{{ $id }}" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-950/60 backdrop-blur-md transition-opacity duration-300">
    <div class="relative w-full max-w-sm mx-4 overflow-hidden rounded-3xl border border-slate-200/80 bg-white/95 p-7 sm:p-8 text-center shadow-2xl backdrop-blur-xl dark:border-slate-800 dark:bg-slate-900/95">
        <!-- Ambient background glow -->
        <div class="absolute -top-12 -left-12 h-36 w-36 rounded-full bg-brand-500/15 blur-2xl pointer-events-none"></div>
        <div class="absolute -bottom-12 -right-12 h-36 w-36 rounded-full bg-emerald-500/15 blur-2xl pointer-events-none"></div>

        <!-- Animated Spinner with Logo / Icon -->
        <div class="relative mx-auto mb-6 flex h-20 w-20 items-center justify-center">
            <!-- Pulsing outer ring -->
            <div class="absolute inset-0 rounded-3xl bg-brand-500/20 dark:bg-brand-400/25 animate-ping opacity-60"></div>
            <!-- Outer ring track -->
            <div class="absolute inset-0 rounded-3xl border-3 border-slate-100 dark:border-slate-800"></div>
            <!-- Spinning active gradient ring -->
            <div class="absolute inset-0 rounded-3xl border-3 border-brand-600 border-t-transparent animate-spin"></div>
            <!-- Centered icon -->
            <div class="relative flex h-11 w-11 items-center justify-center rounded-2xl bg-brand-700 text-white shadow-md shadow-brand-900/30">
                <span class="material-symbols-outlined text-[24px] animate-pulse">sync</span>
            </div>
        </div>

        <!-- Title & Subtitle -->
        <h3 id="{{ $id }}Title" class="text-lg font-bold text-slate-900 dark:text-white">
            {{ $title }}
        </h3>
        <p id="{{ $id }}Subtitle" class="mt-2 text-xs leading-relaxed text-slate-500 dark:text-slate-400">
            {{ $subtitle }}
        </p>

        <!-- Modern animated progress track -->
        <div class="mt-6 h-1.5 w-full overflow-hidden rounded-full bg-slate-100 dark:bg-slate-800">
            <div class="h-full w-2/3 rounded-full bg-linear-to-r from-teal-500 via-brand-600 to-emerald-400 animate-pulse-progress"></div>
        </div>

        <!-- Micro status footer -->
        <div class="mt-4 flex items-center justify-center gap-1.5 text-[11px] font-medium text-slate-400 dark:text-slate-500">
            <span class="inline-block h-1.5 w-1.5 rounded-full bg-emerald-500 animate-ping"></span>
            <span>Memproses data akun...</span>
        </div>
    </div>
</div>

<style>
@keyframes pulseProgress {
    0% { transform: translateX(-100%); }
    50% { transform: translateX(50%); }
    100% { transform: translateX(200%); }
}
.animate-pulse-progress {
    animation: pulseProgress 1.6s ease-in-out infinite;
}
</style>
