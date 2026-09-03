@props(['white' => false, 'class' => 'h-8 w-auto'])

<div {{ $attributes->merge(['class' => 'flex items-center gap-2.5 shrink-0']) }}>
    <div class="h-9 w-9 rounded-xl {{ $white ? 'bg-white' : 'bg-teal-600' }} flex items-center justify-center p-1.5 shadow-sm shrink-0">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 40 40" class="w-full h-full" fill="none">
            <rect width="40" height="40" rx="8" fill="{{ $white ? '#0d9488' : 'transparent' }}"/>
            <path d="M12 21L17 26L27 15" stroke="{{ $white ? 'white' : 'white' }}" stroke-width="3.5" stroke-linecap="round" stroke-linejoin="round"/>
            <path d="M22 11L26 8" stroke="#5eead4" stroke-width="2.5" stroke-linecap="round"/>
        </svg>
    </div>
    <div class="flex flex-col">
        <span class="font-extrabold text-xl tracking-tight leading-none {{ $white ? 'text-white' : 'text-slate-900' }}">
            Kerja<span class="{{ $white ? 'text-teal-300' : 'text-teal-600' }}">Lokal</span>
        </span>
        <span class="text-[10px] font-medium tracking-wide mt-0.5 {{ $white ? 'text-teal-200/80' : 'text-slate-500' }}">
            Peluang Kerja Layak
        </span>
    </div>
</div>
