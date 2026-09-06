@extends('layouts.app')

@section('title', 'Cari Lowongan | KerjaLokal')

@section('content')
    @php
        $jobseekerAppCount = auth()->check() && auth()->user()->hasRole('jobseeker')
            ? auth()->user()->jobApplications()->whereNull('jobseeker_hidden_at')->count()
            : null;
    @endphp

    <div class="mb-6 sm:mb-8 max-w-3xl" data-reveal>
        <p class="text-xs font-bold uppercase tracking-[0.18em] text-coral-600">Peluang kerja lokal</p>
        <h1 class="mt-1.5 sm:mt-2 text-2xl sm:text-4xl font-bold tracking-tight text-slate-950 dark:text-white">Cari pekerjaan yang informasinya jelas.</h1>
        <p class="mt-2 sm:mt-3 text-xs sm:text-sm leading-6 text-slate-600 dark:text-slate-300">Bandingkan peran, upah, jam kerja, lokasi, dan keterampilan sebelum mengajukan lamaran.</p>
    </div>

    {{-- Segmented Switcher: Cari Lowongan vs Riwayat Lamaran --}}
    <div class="mb-5 sm:mb-6" data-reveal>
        <div class="inline-flex w-full sm:w-auto p-1.5 rounded-2xl bg-slate-100 dark:bg-slate-800/90 border border-slate-200 dark:border-slate-700/80 shadow-inner">
            <a href="{{ route('jobs.index') }}" class="flex-1 sm:flex-initial inline-flex items-center justify-center gap-2 px-4 sm:px-6 py-2.5 rounded-xl bg-white dark:bg-slate-900 text-brand-700 dark:text-brand-300 font-bold text-xs sm:text-sm shadow-xs transition">
                <span class="material-symbols-outlined text-[18px]">search</span>
                <span>Cari Lowongan</span>
            </a>
            <a href="{{ route('applications.index') }}" class="flex-1 sm:flex-initial inline-flex items-center justify-center gap-2 px-4 sm:px-6 py-2.5 rounded-xl text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white font-bold text-xs sm:text-sm transition">
                <span class="material-symbols-outlined text-[18px]">history_edu</span>
                <span>Riwayat Lamaran</span>
                @if($jobseekerAppCount !== null && $jobseekerAppCount > 0)
                    <span class="ml-1 rounded-full bg-brand-100 text-brand-800 dark:bg-brand-950 dark:text-brand-300 px-2 py-0.5 text-[10px] font-extrabold">{{ $jobseekerAppCount }}</span>
                @endif
            </a>
        </div>
    </div>

    {{-- Search and Filter Form --}}
    <form method="GET" action="{{ route('jobs.index') }}" class="mb-6 sm:mb-7 rounded-2xl border border-slate-200 bg-white p-3.5 sm:p-4 shadow-sm dark:border-slate-800 dark:bg-slate-900" data-reveal>
        <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-[1.3fr_1fr_1fr_0.9fr_auto]">
            <div class="sm:col-span-2 lg:col-span-1">
                <div class="flex items-center justify-between mb-1.5">
                    <label for="q" class="block text-xs font-bold text-slate-600 dark:text-slate-300">Posisi atau lokasi</label>
                    <button type="button" onclick="document.getElementById('mobileFiltersPanel').classList.toggle('hidden')" class="lg:hidden inline-flex items-center gap-1 text-[11px] font-bold text-brand-700 dark:text-brand-400 hover:underline transition cursor-pointer">
                        <span class="material-symbols-outlined text-[15px]">tune</span>
                        <span>Filter Lanjutan</span>
                        @if(request()->hasAny(['category', 'skill', 'status']))
                            <span class="h-1.5 w-1.5 rounded-full bg-coral-500"></span>
                        @endif
                    </button>
                </div>
                <div class="relative">
                    <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-[19px] text-slate-400">search</span>
                    <input id="q" name="q" value="{{ request('q') }}" type="search" placeholder="Kasir, barista, Bandung..." class="min-h-11 w-full rounded-xl border border-slate-200 bg-slate-50 pl-10 pr-3 text-sm outline-none focus:border-brand-500 focus:ring-4 focus:ring-brand-100 dark:border-slate-700 dark:bg-slate-950 dark:focus:ring-brand-900">
                </div>
            </div>
            <div id="mobileFiltersPanel" class="{{ request()->hasAny(['category', 'skill', 'status']) ? 'contents' : 'hidden lg:contents' }}">
                <div>
                    <label for="category" class="mb-1.5 block text-xs font-bold text-slate-600 dark:text-slate-300">Kategori</label>
                    <select id="category" name="category" class="min-h-11 w-full rounded-xl border border-slate-200 bg-slate-50 px-3 text-sm outline-none focus:border-brand-500 dark:border-slate-700 dark:bg-slate-950">
                        <option value="">Semua kategori</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" @selected((string) request('category') === (string) $category->id)>{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="skill" class="mb-1.5 block text-xs font-bold text-slate-600 dark:text-slate-300">Keterampilan</label>
                    <select id="skill" name="skill" class="min-h-11 w-full rounded-xl border border-slate-200 bg-slate-50 px-3 text-sm outline-none focus:border-brand-500 dark:border-slate-700 dark:bg-slate-950">
                        <option value="">Semua keterampilan</option>
                        @foreach($skills as $skill)
                            <option value="{{ $skill->id }}" @selected((string) request('skill') === (string) $skill->id)>{{ $skill->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="status" class="mb-1.5 block text-xs font-bold text-slate-600 dark:text-slate-300">Status</label>
                    <select id="status" name="status" class="min-h-11 w-full rounded-xl border border-slate-200 bg-slate-50 px-3 text-sm outline-none focus:border-brand-500 dark:border-slate-700 dark:bg-slate-950">
                        <option value="" @selected(! request('status'))>Semua status</option>
                        <option value="open" @selected(request('status') === 'open')>Buka</option>
                        <option value="closed" @selected(request('status') === 'closed')>Ditutup</option>
                    </select>
                </div>
            </div>
            <div class="flex items-end gap-2 sm:col-span-2 lg:col-span-1">
                <button type="submit" class="portal-button-primary min-h-11 flex-1 lg:flex-none justify-center">
                    <span class="material-symbols-outlined text-[19px]">tune</span>
                    <span>Terapkan</span>
                </button>
                @if(request()->hasAny(['q','category','skill','status']))
                    <a href="{{ route('jobs.index') }}" class="portal-icon-button shrink-0" aria-label="Reset filter" title="Reset filter">
                        <span class="material-symbols-outlined text-[19px]">restart_alt</span>
                    </a>
                @endif
            </div>
        </div>
    </form>

    <div class="mb-4 flex items-center justify-between">
        <p class="text-xs sm:text-sm text-slate-500">
            <strong class="text-slate-900 dark:text-white font-bold">{{ $jobs->total() }}</strong> lowongan ditemukan
        </p>
    </div>
    <div class="grid gap-3.5 sm:gap-4 md:grid-cols-2 xl:grid-cols-3">
        @forelse ($jobs as $job)
            <article class="group relative flex min-h-56 sm:min-h-64 flex-col rounded-2xl border {{ $job->status === 'closed' ? 'border-slate-200/80 bg-slate-50/70 dark:border-slate-800/80 dark:bg-slate-900/60 opacity-90' : 'border-slate-200 bg-white dark:border-slate-800 dark:bg-slate-900' }} p-4 sm:p-5 transition hover:-translate-y-0.5 hover:border-brand-300 hover:shadow-xl" data-reveal>
                @if($job->cover_image)
                    <div class="relative -mx-4 -mt-4 sm:-mx-5 sm:-mt-5 mb-3.5 h-36 overflow-hidden rounded-t-2xl bg-slate-900">
                        <img src="{{ $job->cover_image_url }}" alt="{{ $job->title }}" class="h-full w-full object-cover transition duration-500 group-hover:scale-105 {{ $job->status === 'closed' ? 'grayscale-[35%]' : '' }}" loading="lazy">
                        <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent"></div>
                        @if($job->status === 'closed')
                            <div class="absolute top-2.5 left-3 flex items-center gap-1 rounded-lg bg-slate-950/80 backdrop-blur-md px-2.5 py-1 text-[11px] font-bold text-amber-300 border border-amber-500/40 shadow-xs">
                                <span class="material-symbols-outlined text-[14px]">lock</span>
                                <span>Ditutup Mitra</span>
                            </div>
                        @endif
                        @if(($job->workplace_photos_count ?? 0) > 0)
                            <div class="absolute bottom-2.5 right-3 flex items-center gap-1 rounded-lg bg-black/60 backdrop-blur-md px-2 py-0.5 text-[11px] font-semibold text-white">
                                <span class="material-symbols-outlined text-[14px]">photo_library</span>
                                <span>{{ $job->workplace_photos_count }} Foto</span>
                            </div>
                        @endif
                    </div>
                @endif
                <div class="flex items-start justify-between gap-3">
                    <div class="flex items-center gap-1.5 flex-wrap">
                        <span class="rounded-lg bg-brand-50 px-2.5 py-1 text-xs font-bold text-brand-700 dark:bg-brand-950 dark:text-brand-300">{{ $job->category->name }}</span>
                        @if($job->status === 'closed' && ! $job->cover_image)
                            <span class="rounded-lg bg-amber-50 dark:bg-amber-950/50 px-2 py-0.5 text-[11px] font-bold text-amber-800 dark:text-amber-300 border border-amber-200 dark:border-amber-900/60 inline-flex items-center gap-1">
                                <span class="material-symbols-outlined text-[13px]">lock</span>
                                Ditutup Mitra
                            </span>
                        @endif
                    </div>
                    <span class="text-xs text-slate-400">{{ $job->created_at->diffForHumans() }}</span>
                </div>
                <h2 class="mt-3 sm:mt-4 text-lg sm:text-xl font-bold leading-snug text-slate-950 dark:text-white">
                    <a href="{{ route('jobs.show', $job) }}" class="after:absolute after:inset-0 hover:text-brand-700 transition">{{ $job->title }}</a>
                </h2>
                <p class="mt-1 text-xs sm:text-sm font-medium text-slate-600 dark:text-slate-300">{{ $job->employer->business_name }}</p>
                <div class="mt-3 sm:mt-4 space-y-1.5 text-xs text-slate-500">
                    <p class="flex items-center gap-2 truncate"><span class="material-symbols-outlined text-[17px] text-brand-600 shrink-0">location_on</span>{{ $job->location }}</p>
                    <p class="flex items-center gap-2 truncate"><span class="material-symbols-outlined text-[17px] text-brand-600 shrink-0">payments</span>Rp {{ number_format($job->salary_amount, 0, ',', '.') }} / {{ $job->salary_type === 'monthly' ? 'bulan' : 'hari' }}</p>
                    <p class="flex items-center gap-2 truncate"><span class="material-symbols-outlined text-[17px] text-brand-600 shrink-0">schedule</span>{{ $job->work_hours_per_day }} jam per hari</p>
                </div>
                <div class="mt-auto flex flex-wrap items-center justify-between gap-1.5 pt-4">
                    <div class="flex flex-wrap gap-1.5">
                        @foreach($job->skills->take(3) as $skill)
                            <span class="rounded-md bg-slate-100 px-2 py-0.5 text-[11px] font-semibold text-slate-600 dark:bg-slate-800 dark:text-slate-300">{{ $skill->name }}</span>
                        @endforeach
                    </div>
                    @if($job->status === 'closed')
                        <span class="text-[11px] font-semibold text-slate-400 dark:text-slate-500">Pendaftaran ditutup</span>
                    @endif
                </div>
            </article>
        @empty
            <div class="rounded-2xl border border-dashed border-slate-300 bg-white p-8 sm:p-12 text-center md:col-span-2 xl:col-span-3 dark:border-slate-700 dark:bg-slate-900">
                <span class="material-symbols-outlined text-4xl text-slate-300">search_off</span>
                <h2 class="mt-3 font-bold text-base">Lowongan tidak ditemukan</h2>
                <p class="mt-1 text-xs sm:text-sm text-slate-500">Coba ubah kata kunci atau pilihan filter.</p>
            </div>
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
