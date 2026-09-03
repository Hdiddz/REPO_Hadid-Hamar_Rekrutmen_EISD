@php
    $flash = collect([
        'success' => ['icon' => 'check_circle', 'classes' => 'border-emerald-200 bg-emerald-50 text-emerald-900 dark:border-emerald-900 dark:bg-emerald-950/60 dark:text-emerald-100'],
        'error' => ['icon' => 'error', 'classes' => 'border-rose-200 bg-rose-50 text-rose-900 dark:border-rose-900 dark:bg-rose-950/60 dark:text-rose-100'],
        'warning' => ['icon' => 'warning', 'classes' => 'border-amber-200 bg-amber-50 text-amber-900 dark:border-amber-900 dark:bg-amber-950/60 dark:text-amber-100'],
        'status' => ['icon' => 'info', 'classes' => 'border-brand-200 bg-brand-50 text-brand-900 dark:border-brand-900 dark:bg-brand-950/60 dark:text-brand-100'],
    ])->first(fn (array $config, string $key): bool => session()->has($key));
    $flashKey = collect(['success', 'error', 'warning', 'status'])->first(fn (string $key): bool => session()->has($key));
@endphp

@if ($flash && $flashKey)
    <div data-flash-container {{ $attributes->merge(['class' => 'pointer-events-none fixed inset-x-0 top-20 z-50 flex justify-center px-4 sm:top-24']) }} role="status" aria-live="polite">
        <div data-flash-alert class="pointer-events-auto flex max-w-md items-center gap-3 rounded-2xl border px-4 py-3 text-sm font-semibold shadow-2xl backdrop-blur-md {{ $flash['classes'] }}">
            <span class="material-symbols-outlined shrink-0 text-[20px]">{{ $flash['icon'] }}</span>
            <p class="min-w-0 flex-1 leading-5">{{ session($flashKey) }}</p>
            <button type="button" data-alert-dismiss class="grid h-7 w-7 shrink-0 place-items-center rounded-lg transition hover:bg-black/10 dark:hover:bg-white/20" aria-label="Tutup pesan">
                <span class="material-symbols-outlined text-[18px]">close</span>
            </button>
        </div>
    </div>
@endif

@if ($errors->any() && ! request()->routeIs('login', 'register'))
    <div data-flash-container class="pointer-events-none fixed inset-x-0 top-20 z-50 flex justify-center px-4 sm:top-24" role="alert">
        <div data-flash-alert class="pointer-events-auto max-w-md rounded-2xl border border-rose-200 bg-rose-50/95 px-4 py-3 text-sm text-rose-900 shadow-2xl backdrop-blur-md dark:border-rose-900 dark:bg-rose-950/90 dark:text-rose-100">
            <div class="flex items-center justify-between gap-2">
                <p class="font-bold">Periksa kembali data formulir:</p>
                <button type="button" data-alert-dismiss class="grid h-6 w-6 shrink-0 place-items-center rounded-lg hover:bg-black/5 dark:hover:bg-white/10" aria-label="Tutup pesan">
                    <span class="material-symbols-outlined text-[16px]">close</span>
                </button>
            </div>
            <p class="mt-1 text-xs leading-5">{{ $errors->first() }}</p>
        </div>
    </div>
@endif
