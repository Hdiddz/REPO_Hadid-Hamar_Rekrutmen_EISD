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
    <div {{ $attributes->merge(['class' => 'mx-auto w-full max-w-7xl px-4 pt-4 sm:px-6 lg:px-8']) }} role="status" aria-live="polite">
        <div data-flash-alert class="flex items-start gap-3 rounded-xl border px-4 py-3 text-sm shadow-sm {{ $flash['classes'] }}">
            <span class="material-symbols-outlined mt-0.5 text-[20px]">{{ $flash['icon'] }}</span>
            <p class="min-w-0 flex-1 leading-6">{{ session($flashKey) }}</p>
            <button type="button" data-alert-dismiss class="grid h-8 w-8 shrink-0 place-items-center rounded-lg transition hover:bg-black/5 dark:hover:bg-white/10" aria-label="Tutup pesan">
                <span class="material-symbols-outlined text-[18px]">close</span>
            </button>
        </div>
    </div>
@endif

@if ($errors->any() && ! request()->routeIs('login', 'register'))
    <div class="mx-auto w-full max-w-7xl px-4 pt-4 sm:px-6 lg:px-8" role="alert">
        <div class="rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-900 dark:border-rose-900 dark:bg-rose-950/60 dark:text-rose-100">
            <p class="font-semibold">Periksa kembali data formulir.</p>
            <p class="mt-1">{{ $errors->first() }}</p>
        </div>
    </div>
@endif
