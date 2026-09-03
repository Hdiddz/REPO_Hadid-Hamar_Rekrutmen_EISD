@extends('layouts.employer')

@php($isEditing = filled($job))
@section('title', ($isEditing ? 'Edit' : 'Pasang').' Lowongan | KerjaLokal')
@section('portal_title', $isEditing ? 'Perbarui lowongan' : 'Pasang lowongan baru')
@section('portal_description', 'Isi informasi yang dapat dipahami pencari kerja sebelum mereka mengajukan lamaran.')
@section('portal_actions')<a href="{{ route('employer.dashboard') }}" class="portal-button-secondary"><span class="material-symbols-outlined text-[18px]">arrow_back</span>Kembali</a>@endsection

@section('content')
    <form action="{{ $isEditing ? route('employer.jobs.update', $job) : route('employer.jobs.store') }}" method="POST" class="mx-auto max-w-4xl space-y-6">@csrf @if($isEditing)@method('PUT')@endif
        <section class="portal-panel p-5 sm:p-7" data-reveal><div class="mb-6"><h2 class="text-lg font-bold">Informasi utama</h2><p class="mt-1 text-sm text-slate-500">Judul, kategori, dan lokasi membantu lowongan mudah ditemukan.</p></div>
            <div class="grid gap-5 sm:grid-cols-2">
                <div class="sm:col-span-2"><label for="title" class="portal-label">Judul posisi</label><input id="title" name="title" value="{{ old('title', $job?->title) }}" class="portal-input" placeholder="Contoh: Barista dan Kasir" required>@error('title')<p class="portal-field-error">{{ $message }}</p>@enderror</div>
                <div><label for="category_id" class="portal-label">Kategori</label><select id="category_id" name="category_id" class="portal-input" required><option value="">Pilih kategori</option>@foreach($categories as $category)<option value="{{ $category->id }}" @selected((string) old('category_id', $job?->category_id) === (string) $category->id)>{{ $category->name }}</option>@endforeach</select>@error('category_id')<p class="portal-field-error">{{ $message }}</p>@enderror</div>
                <div><label for="location" class="portal-label">Lokasi kerja</label><input id="location" name="location" value="{{ old('location', $job?->location) }}" class="portal-input" placeholder="Coblong, Kota Bandung" required>@error('location')<p class="portal-field-error">{{ $message }}</p>@enderror</div>
                <div class="sm:col-span-2"><label for="description" class="portal-label">Deskripsi dan tanggung jawab</label><textarea id="description" name="description" rows="7" class="portal-input" placeholder="Jelaskan kegiatan kerja, jadwal, dan ekspektasi secara rinci." required>{{ old('description', $job?->description) }}</textarea>@error('description')<p class="portal-field-error">{{ $message }}</p>@enderror</div>
            </div>
        </section>

        <section class="portal-panel p-5 sm:p-7" data-reveal><h2 class="mb-6 text-lg font-bold">Kompensasi dan waktu</h2><div class="grid gap-5 sm:grid-cols-3">
            <div><label for="salary_type" class="portal-label">Periode upah</label><select id="salary_type" name="salary_type" class="portal-input" required><option value="monthly" @selected(old('salary_type', $job?->salary_type ?? 'monthly') === 'monthly')>Per bulan</option><option value="daily" @selected(old('salary_type', $job?->salary_type) === 'daily')>Per hari</option><option value="hourly" @selected(old('salary_type', $job?->salary_type) === 'hourly')>Per jam</option></select>@error('salary_type')<p class="portal-field-error">{{ $message }}</p>@enderror</div>
            <div><label for="salary_amount" class="portal-label">Nominal upah</label><input id="salary_amount" name="salary_amount" type="number" min="1" step="1000" value="{{ old('salary_amount', $job?->salary_amount) }}" class="portal-input" required>@error('salary_amount')<p class="portal-field-error">{{ $message }}</p>@enderror</div>
            <div><label for="work_hours_per_day" class="portal-label">Jam kerja per hari</label><input id="work_hours_per_day" name="work_hours_per_day" type="number" min="1" max="8" value="{{ old('work_hours_per_day', $job?->work_hours_per_day ?? 8) }}" class="portal-input" required>@error('work_hours_per_day')<p class="portal-field-error">{{ $message }}</p>@enderror</div>
        </div></section>

        <section class="portal-panel p-5 sm:p-7" data-reveal><h2 class="text-lg font-bold">Keterampilan</h2><p class="mt-1 text-sm text-slate-500">Pilih minimal satu keterampilan yang benar-benar dibutuhkan.</p><div class="mt-5 grid gap-3 sm:grid-cols-2 lg:grid-cols-3">@php($selectedSkills = collect(old('skills', $job?->skills?->pluck('id')->all() ?? []))->map(fn($id) => (string) $id)) @foreach($skills as $skill)<label class="flex cursor-pointer items-center gap-3 rounded-xl border border-slate-200 p-3 text-sm font-semibold hover:border-brand-400 dark:border-slate-700"><input type="checkbox" name="skills[]" value="{{ $skill->id }}" @checked($selectedSkills->contains((string) $skill->id)) class="h-4 w-4 rounded text-brand-600 focus:ring-brand-500">{{ $skill->name }}</label>@endforeach</div>@error('skills')<p class="portal-field-error">{{ $message }}</p>@enderror @error('skills.*')<p class="portal-field-error">{{ $message }}</p>@enderror</section>

        @if($isEditing)<section class="portal-panel p-5 sm:p-7"><label for="status" class="portal-label">Status lowongan</label><select id="status" name="status" class="portal-input"><option value="open" @selected(old('status', $job->status) === 'open')>Dibuka</option><option value="closed" @selected(old('status', $job->status) === 'closed')>Ditutup</option></select>@error('status')<p class="portal-field-error">{{ $message }}</p>@enderror</section>@endif
        <div class="flex justify-end gap-3"><a href="{{ route('employer.dashboard') }}" class="portal-button-secondary">Batal</a><button class="portal-button-primary"><span class="material-symbols-outlined text-[18px]">{{ $isEditing ? 'save' : 'publish' }}</span>{{ $isEditing ? 'Simpan perubahan' : 'Publikasikan lowongan' }}</button></div>
    </form>
@endsection
