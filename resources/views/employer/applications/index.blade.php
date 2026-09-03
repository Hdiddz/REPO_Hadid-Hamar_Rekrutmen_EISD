@extends('layouts.employer')

@section('title', 'Pelamar Masuk | KerjaLokal')
@section('portal_title', 'Pelamar masuk')
@section('portal_description', 'Tinjau data kandidat, unduh resume dari penyimpanan privat, lalu perbarui status seleksi.')
@section('portal_actions')<a href="{{ route('employer.dashboard') }}" class="portal-button-secondary"><span class="material-symbols-outlined text-[18px]">work</span>Kelola lowongan</a>@endsection

@section('content')
    <form method="GET" class="mb-6 grid gap-3 rounded-2xl border border-slate-200 bg-white p-4 sm:grid-cols-[1fr_1fr_auto] dark:border-slate-800 dark:bg-slate-900" data-reveal>
        <div><label for="job" class="portal-label">Lowongan</label><select id="job" name="job" class="portal-input"><option value="">Semua lowongan</option>@foreach($jobs as $job)<option value="{{ $job->id }}" @selected((string) request('job') === (string) $job->id)>{{ $job->title }}</option>@endforeach</select></div>
        <div><label for="status" class="portal-label">Status seleksi</label><select id="status" name="status" class="portal-input"><option value="">Semua status</option>@foreach(['pending' => 'Menunggu', 'interview' => 'Wawancara', 'accepted' => 'Diterima', 'rejected' => 'Ditolak'] as $value => $label)<option value="{{ $value }}" @selected(request('status') === $value)>{{ $label }}</option>@endforeach</select></div>
        <div class="flex items-end gap-2"><button class="portal-button-primary">Terapkan</button>@if(request()->hasAny(['job','status']))<a href="{{ route('employer.applications.index') }}" class="portal-button-secondary">Reset</a>@endif</div>
    </form>

    <div class="space-y-4">
        @forelse($applications as $application)
            <article class="rounded-2xl border border-slate-200 bg-white p-5 dark:border-slate-800 dark:bg-slate-900" data-reveal>
                <div class="grid gap-5 lg:grid-cols-[1fr_auto] lg:items-start"><div class="flex items-start gap-4"><div class="grid h-12 w-12 shrink-0 place-items-center rounded-2xl bg-brand-700 font-bold text-white">{{ str($application->user->name)->substr(0, 1)->upper() }}</div><div><p class="text-xs font-bold uppercase tracking-wider text-brand-700 dark:text-brand-300">{{ $application->job->title }}</p><h2 class="mt-1 text-lg font-bold">{{ $application->user->name }}</h2><p class="mt-1 text-sm text-slate-500">{{ $application->user->email }}{{ $application->user->phone ? ' · '.$application->user->phone : '' }}</p><div class="mt-3 flex flex-wrap gap-1.5">@foreach($application->job->skills as $skill)<span class="rounded-md bg-slate-100 px-2 py-1 text-[11px] font-semibold text-slate-600 dark:bg-slate-800 dark:text-slate-300">{{ $skill->name }}</span>@endforeach</div></div></div><div class="flex flex-wrap gap-2"><a href="{{ route('employer.applications.resume', $application) }}" class="portal-button-secondary"><span class="material-symbols-outlined text-[18px]">download</span>Unduh resume</a></div></div>
                @if($application->note)<p class="mt-5 rounded-xl bg-slate-50 p-4 text-sm leading-6 text-slate-600 dark:bg-slate-950 dark:text-slate-300"><strong>Catatan kandidat:</strong> {{ $application->note }}</p>@endif
                <form action="{{ route('employer.applications.update', $application) }}" method="POST" class="mt-5 flex flex-col gap-3 border-t border-slate-100 pt-5 sm:flex-row sm:items-end dark:border-slate-800">@csrf @method('PATCH')<div class="flex-1"><label for="status-{{ $application->id }}" class="portal-label">Status seleksi</label><select id="status-{{ $application->id }}" name="status" class="portal-input">@foreach(['pending' => 'Menunggu tinjauan', 'interview' => 'Wawancara', 'accepted' => 'Diterima', 'rejected' => 'Ditolak'] as $value => $label)<option value="{{ $value }}" @selected($application->status === $value)>{{ $label }}</option>@endforeach</select>@error('status')<p class="portal-field-error">{{ $message }}</p>@enderror</div><button class="portal-button-primary sm:mb-px"><span class="material-symbols-outlined text-[18px]">save</span>Perbarui status</button></form>
            </article>
        @empty
            <div class="rounded-2xl border border-dashed border-slate-300 bg-white p-12 text-center dark:border-slate-700 dark:bg-slate-900"><span class="material-symbols-outlined text-4xl text-slate-300">group_off</span><h2 class="mt-3 font-bold">Belum ada pelamar</h2><p class="mt-1 text-sm text-slate-500">Pelamar baru akan tampil setelah mengirim resume.</p></div>
        @endforelse
    </div>
    <div class="mt-7">{{ $applications->links() }}</div>
@endsection
