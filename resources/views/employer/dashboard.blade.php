@extends('layouts.employer')

@section('title', 'Dashboard Mitra | KerjaLokal')
@section('portal_title', 'Lowongan milik '.$employer->business_name)
@section('portal_description', 'Kelola informasi kerja, buka atau tutup rekrutmen, dan pantau jumlah pelamar dari satu halaman.')
@section('portal_actions')<a href="{{ route('employer.applications.index') }}" class="portal-button-secondary"><span class="material-symbols-outlined text-[18px]">group</span>Lihat pelamar</a><a href="{{ route('employer.jobs.create') }}" class="portal-button-primary"><span class="material-symbols-outlined text-[18px]">add</span>Pasang lowongan</a>@endsection

@section('content')
    <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4" data-reveal>
        @foreach ([['work', $metrics['open_jobs'], 'Lowongan aktif'], ['group', $metrics['applications'], 'Total pelamar'], ['task_alt', $metrics['accepted'], 'Diterima'], ['payments', 'Rp '.number_format($metrics['accepted_wages'], 0, ',', '.'), 'Nilai upah diterima']] as $metric)
            <div class="portal-stat-card"><span class="material-symbols-outlined text-brand-600 dark:text-brand-300">{{ $metric[0] }}</span><strong class="mt-5 block text-2xl font-bold">{{ $metric[1] }}</strong><span class="mt-1 block text-sm text-slate-500">{{ $metric[2] }}</span></div>
        @endforeach
    </section>

    <section class="mt-7 overflow-hidden rounded-2xl border border-slate-200 bg-white dark:border-slate-800 dark:bg-slate-900" data-reveal>
        <div class="flex items-center justify-between border-b border-slate-100 px-5 py-4 dark:border-slate-800"><div><h2 class="font-bold">Daftar lowongan</h2><p class="mt-0.5 text-xs text-slate-500">Data langsung dari lowongan milik akun Anda.</p></div></div>
        <div class="divide-y divide-slate-100 dark:divide-slate-800">
            @forelse($jobs as $job)
                <article class="grid gap-4 p-5 lg:grid-cols-[1fr_auto] lg:items-center">
                    <div><div class="flex flex-wrap items-center gap-2"><span class="rounded-md bg-slate-100 px-2 py-1 text-[11px] font-bold text-slate-600 dark:bg-slate-800 dark:text-slate-300">{{ $job->category->name }}</span><span class="rounded-md px-2 py-1 text-[11px] font-bold {{ $job->status === 'open' ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/40 dark:text-emerald-300' : 'bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-300' }}">{{ $job->status === 'open' ? 'Dibuka' : 'Ditutup' }}</span></div><h3 class="mt-2 text-base font-bold text-slate-950 dark:text-white">{{ $job->title }}</h3><p class="mt-1 text-xs text-slate-500">{{ $job->location }} · Rp {{ number_format($job->salary_amount, 0, ',', '.') }} / {{ $job->salary_type === 'monthly' ? 'bulan' : 'hari' }} · {{ $job->applications_count }} pelamar</p></div>
                    <div class="flex flex-wrap items-center gap-2">
                        <a href="{{ route('jobs.show', $job) }}" class="portal-icon-button" title="Lihat detail lowongan">
                            <span class="material-symbols-outlined text-[18px]">visibility</span>
                        </a>
                        <a href="{{ route('employer.jobs.edit', $job) }}" class="portal-button-secondary">Edit</a>
                        <form id="deleteEmployerJob-{{ $job->id }}" action="{{ route('employer.jobs.destroy', $job) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="button" onclick="confirmDeleteJob('{{ $job->id }}', '{{ addslashes($job->title) }}')" class="portal-icon-button text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-950/40 cursor-pointer" title="Hapus lowongan">
                                <span class="material-symbols-outlined text-[18px]">delete</span>
                            </button>
                        </form>
                    </div>
                </article>
            @empty
                <div class="p-12 text-center"><span class="material-symbols-outlined text-4xl text-slate-300">work_off</span><h3 class="mt-3 font-bold">Belum ada lowongan</h3><p class="mt-1 text-sm text-slate-500">Terbitkan peluang pertama untuk mulai menerima pelamar.</p></div>
            @endforelse
        </div>
    </section>
    <div class="mt-6">{{ $jobs->links() }}</div>
@endsection

@push('scripts')
<script>
    async function confirmDeleteJob(jobId, jobTitle) {
        const confirmed = await window.showAppConfirm({
            title: 'Hapus Lowongan Pekerjaan?',
            message: `Apakah Anda yakin ingin menghapus lowongan "${jobTitle}"?\n\nJika lowongan sudah memiliki pelamar, sistem akan otomatis mengamankannya menjadi ditutup untuk arsip riwayat.`,
            confirmText: 'Ya, Hapus',
            cancelText: 'Batal',
            type: 'danger',
            icon: 'delete'
        });

        if (confirmed) {
            document.getElementById(`deleteEmployerJob-${jobId}`)?.submit();
        }
    }
</script>
@endpush
