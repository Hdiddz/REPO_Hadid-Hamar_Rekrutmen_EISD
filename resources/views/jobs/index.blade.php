@extends('layouts.app')

@section('title', 'Cari Lowongan | KerjaLokal')

@section('content')
    <div class="mb-8 max-w-3xl" data-reveal><p class="text-xs font-bold uppercase tracking-[0.18em] text-coral-600">Peluang kerja lokal</p><h1 class="mt-2 text-3xl font-bold tracking-tight text-slate-950 dark:text-white sm:text-4xl">Cari pekerjaan yang informasinya jelas.</h1><p class="mt-3 text-sm leading-6 text-slate-600 dark:text-slate-300">Bandingkan peran, upah, jam kerja, lokasi, dan keterampilan sebelum mengajukan lamaran.</p></div>

    <form method="GET" action="{{ route('jobs.index') }}" class="mb-7 grid gap-3 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm md:grid-cols-[1.4fr_1fr_1fr_auto] dark:border-slate-800 dark:bg-slate-900" data-reveal>
        <div><label for="q" class="mb-1.5 block text-xs font-bold text-slate-600 dark:text-slate-300">Posisi atau lokasi</label><input id="q" name="q" value="{{ request('q') }}" type="search" placeholder="Kasir, Bandung" class="min-h-11 w-full rounded-xl border border-slate-200 bg-slate-50 px-3 text-sm outline-none focus:border-brand-500 focus:ring-4 focus:ring-brand-100 dark:border-slate-700 dark:bg-slate-950 dark:focus:ring-brand-900"></div>
        <div><label for="category" class="mb-1.5 block text-xs font-bold text-slate-600 dark:text-slate-300">Kategori</label><select id="category" name="category" class="min-h-11 w-full rounded-xl border border-slate-200 bg-slate-50 px-3 text-sm outline-none focus:border-brand-500 dark:border-slate-700 dark:bg-slate-950"><option value="">Semua kategori</option>@foreach($categories as $category)<option value="{{ $category->id }}" @selected((string) request('category') === (string) $category->id)>{{ $category->name }}</option>@endforeach</select></div>
        <div><label for="skill" class="mb-1.5 block text-xs font-bold text-slate-600 dark:text-slate-300">Keterampilan</label><select id="skill" name="skill" class="min-h-11 w-full rounded-xl border border-slate-200 bg-slate-50 px-3 text-sm outline-none focus:border-brand-500 dark:border-slate-700 dark:bg-slate-950"><option value="">Semua keterampilan</option>@foreach($skills as $skill)<option value="{{ $skill->id }}" @selected((string) request('skill') === (string) $skill->id)>{{ $skill->name }}</option>@endforeach</select></div>
        <div class="flex items-end gap-2"><button class="portal-button-primary min-h-11">Terapkan</button>@if(request()->hasAny(['q','category','skill']))<a href="{{ route('jobs.index') }}" class="portal-icon-button" aria-label="Reset filter"><span class="material-symbols-outlined text-[19px]">restart_alt</span></a>@endif</div>
    </form>

    <div class="mb-4 flex items-center justify-between"><p class="text-sm text-slate-500"><strong class="text-slate-900 dark:text-white">{{ $jobs->total() }}</strong> lowongan ditemukan</p></div>
    <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
        @forelse ($jobs as $job)
            <article class="group relative flex min-h-64 flex-col rounded-2xl border border-slate-200 bg-white p-5 transition hover:-translate-y-1 hover:border-brand-300 hover:shadow-xl dark:border-slate-800 dark:bg-slate-900" data-reveal>
                <div class="flex items-start justify-between gap-3"><span class="rounded-lg bg-brand-50 px-2.5 py-1 text-xs font-bold text-brand-700 dark:bg-brand-950 dark:text-brand-300">{{ $job->category->name }}</span><span class="text-xs text-slate-400">{{ $job->created_at->diffForHumans() }}</span></div>
                <h2 class="mt-5 text-xl font-bold leading-snug text-slate-950 dark:text-white"><a href="{{ route('jobs.show', $job) }}" class="after:absolute after:inset-0">{{ $job->title }}</a></h2>
                <p class="mt-1 text-sm font-medium text-slate-600 dark:text-slate-300">{{ $job->employer->business_name }}</p>
                <div class="mt-4 space-y-2 text-xs text-slate-500"><p class="flex items-center gap-2"><span class="material-symbols-outlined text-[18px] text-brand-600">location_on</span>{{ $job->location }}</p><p class="flex items-center gap-2"><span class="material-symbols-outlined text-[18px] text-brand-600">payments</span>Rp {{ number_format($job->salary_amount, 0, ',', '.') }} / {{ $job->salary_type === 'monthly' ? 'bulan' : 'hari' }}</p><p class="flex items-center gap-2"><span class="material-symbols-outlined text-[18px] text-brand-600">schedule</span>{{ $job->work_hours_per_day }} jam per hari</p></div>
                <div class="mt-auto flex flex-wrap gap-1.5 pt-5">@foreach($job->skills->take(3) as $skill)<span class="rounded-md bg-slate-100 px-2 py-1 text-[11px] font-semibold text-slate-600 dark:bg-slate-800 dark:text-slate-300">{{ $skill->name }}</span>@endforeach</div>
            </article>
        @empty
            <div class="rounded-2xl border border-dashed border-slate-300 bg-white p-12 text-center md:col-span-2 xl:col-span-3 dark:border-slate-700 dark:bg-slate-900"><span class="material-symbols-outlined text-4xl text-slate-300">search_off</span><h2 class="mt-3 font-bold">Lowongan tidak ditemukan</h2><p class="mt-1 text-sm text-slate-500">Coba ubah kata kunci atau pilihan filter.</p></div>
        @endforelse
    </div>
    <div class="mt-8">{{ $jobs->links() }}</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const searchInput = document.getElementById('q');
        if (searchInput && !searchInput.value.trim()) {
            const suggestions = [
                'Kasir, barista, Bandung...',
                'Staff admin, Surabaya...',
                'Desain grafis, Jakarta...',
                'Barista kedai kopi, Yogyakarta...',
                'Video editor, Bandung...',
                'Pramuniaga toko, Semarang...'
            ];
            let sIdx = 0;
            let sChar = suggestions[0].length;
            let sDeleting = false;
            let isFocused = false;

            function stepSearchType() {
                if (isFocused || searchInput.value.trim().length > 0) {
                    setTimeout(stepSearchType, 1000);
                    return;
                }

                const text = suggestions[sIdx];
                if (sDeleting) {
                    sChar--;
                    searchInput.setAttribute('placeholder', text.substring(0, sChar));
                } else {
                    sChar++;
                    searchInput.setAttribute('placeholder', text.substring(0, sChar));
                }

                let delay = sDeleting ? 30 : 65;

                if (!sDeleting && sChar === text.length) {
                    delay = 2200;
                    sDeleting = true;
                } else if (sDeleting && sChar === 0) {
                    sDeleting = false;
                    sIdx = (sIdx + 1) % suggestions.length;
                    delay = 400;
                }

                setTimeout(stepSearchType, delay);
            }

            searchInput.addEventListener('focus', () => { isFocused = true; });
            searchInput.addEventListener('blur', () => {
                isFocused = false;
                if (!searchInput.value.trim()) {
                    sChar = 0;
                    sDeleting = false;
                }
            });

            setTimeout(() => {
                sDeleting = true;
                stepSearchType();
            }, 2000);
        }
    });
</script>
@endpush
