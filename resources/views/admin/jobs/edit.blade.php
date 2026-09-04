@extends('layouts.admin')

@section('title', 'Edit Lowongan: ' . $job->title . ' | Admin KerjaLokal')
@section('portal_title', 'Edit Lowongan Pekerjaan')
@section('portal_description', 'Perbarui parameter lowongan mitra demi kesesuaian informasi standar kerja layak.')

@section('portal_actions')
    <a href="{{ $returnUrl ?? route('admin.jobs.show', $job) }}" 
       onclick="if (window.history.length > 1 && document.referrer && document.referrer.includes(window.location.host) && !document.referrer.includes(window.location.pathname)) { history.back(); return false; }"
       class="portal-action-btn !px-2.5 sm:!px-3" 
       title="Batal dan Kembali">
        <span class="material-symbols-outlined text-[17px]">arrow_back</span>
        <span>Batal<span class="hidden sm:inline"> &amp; Kembali</span></span>
    </a>
@endsection

@section('content')
    <div class="max-w-4xl mx-auto">
        <form action="{{ route('admin.jobs.update', $job) }}" method="POST" class="portal-panel p-6 sm:p-8 space-y-6">
            @csrf
            @method('PUT')

            <!-- Header Info -->
            <div class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div>
                    <span class="text-xs text-slate-400 block">Mitra Pemilik Lowongan</span>
                    <strong class="text-sm font-bold text-slate-900 dark:text-white">{{ $job->employer->business_name ?: $job->employer->name }}</strong>
                    <span class="text-xs text-slate-500 block">{{ $job->employer->email }}</span>
                </div>
                <div>
                    <a href="{{ route('admin.users.show', $job->employer) }}" class="portal-button-secondary !py-1 !px-2.5 text-xs">
                        Lihat Profil Mitra
                    </a>
                </div>
            </div>

            <!-- Title & Category -->
            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label for="title" class="portal-label">Judul Lowongan <span class="text-rose-500">*</span></label>
                    <input type="text" id="title" name="title" value="{{ old('title', $job->title) }}" required class="portal-input">
                    @error('title')
                        <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="category_id" class="portal-label">Kategori Sektor <span class="text-rose-500">*</span></label>
                    <select id="category_id" name="category_id" required class="portal-input">
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" @selected(old('category_id', $job->category_id) == $category->id)>{{ $category->name }}</option>
                        @endforeach
                    </select>
                    @error('category_id')
                        <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Location & Work Hours -->
            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label for="location" class="portal-label">Wilayah / Lokasi Penempatan <span class="text-rose-500">*</span></label>
                    <input type="text" id="location" name="location" value="{{ old('location', $job->location) }}" required class="portal-input">
                    @error('location')
                        <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="work_hours_per_day" class="portal-label">Jam Kerja per Hari (Maksimal 8 Jam Etis) <span class="text-rose-500">*</span></label>
                    <input type="number" id="work_hours_per_day" name="work_hours_per_day" min="1" max="24" value="{{ old('work_hours_per_day', $job->work_hours_per_day) }}" required class="portal-input">
                    @error('work_hours_per_day')
                        <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Salary Details (Hanya Pantau / Kewenangan Mitra UMKM) -->
            <div class="rounded-2xl border border-slate-200 bg-slate-50/70 p-4 dark:border-slate-800 dark:bg-slate-900/60">
                <div class="flex items-center justify-between gap-3 mb-1.5">
                    <span class="portal-label !mb-0 text-xs text-slate-500">Pemantauan Nilai Upah yang Ditawarkan Mitra</span>
                    <span class="portal-badge bg-emerald-50 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300 text-[10px]">
                        Hanya Pantau (Kewenangan Mitra)
                    </span>
                </div>
                <div class="flex items-center gap-3 text-base font-bold text-slate-900 dark:text-white">
                    <span class="material-symbols-outlined text-emerald-600 text-[20px]">payments</span>
                    <span>Rp {{ number_format($job->salary_amount, 0, ',', '.') }}</span>
                    <span class="text-xs text-slate-500 font-normal">/ {{ match($job->salary_type) {'monthly' => 'Bulan', 'daily' => 'Hari', 'hourly' => 'Jam', default => 'Bulan'} }}</span>
                </div>
                <p class="mt-2 text-[11px] text-slate-400">
                    Nilai dan skema upah ditetapkan langsung oleh Mitra UMKM pemilik lowongan. Administrator memantau kesesuaian nilai ini secara transparan tanpa kewenangan mengubah nominal kesepakatan mitra.
                </p>
            </div>

            <!-- Status -->
            <div>
                <label for="status" class="portal-label">Status Lowongan <span class="text-rose-500">*</span></label>
                <select id="status" name="status" required class="portal-input">
                    <option value="open" @selected(old('status', $job->status) === 'open')>Dibuka (Aktif menerima pelamar)</option>
                    <option value="closed" @selected(old('status', $job->status) === 'closed')>Ditutup</option>
                </select>
                @error('status')
                    <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Description -->
            <div>
                <label for="description" class="portal-label">Deskripsi &amp; Tanggung Jawab Pekerjaan <span class="text-rose-500">*</span></label>
                <textarea id="description" name="description" rows="5" required class="portal-input">{{ old('description', $job->description) }}</textarea>
                @error('description')
                    <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Skills -->
            <div>
                <label class="portal-label mb-2 block">Keterampilan Terkait</label>
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-2.5 max-h-52 overflow-y-auto p-3 rounded-2xl border border-slate-200 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-900/50">
                    @php
                        $selectedSkills = old('skills', $job->skills->pluck('id')->all());
                    @endphp
                    @foreach($skills as $skill)
                        <label class="flex items-center gap-2 text-xs text-slate-700 dark:text-slate-300 cursor-pointer p-1 rounded hover:bg-slate-100 dark:hover:bg-slate-800">
                            <input type="checkbox" name="skills[]" value="{{ $skill->id }}" @checked(in_array($skill->id, $selectedSkills)) class="rounded border-slate-300 text-brand-600 focus:ring-brand-500">
                            <span class="truncate">{{ $skill->name }}</span>
                        </label>
                    @endforeach
                </div>
            </div>

            <!-- Submit Button -->
            <div class="pt-4 flex items-center justify-end gap-3 border-t border-slate-100 dark:border-slate-800">
                <a href="{{ $returnUrl ?? route('admin.jobs.show', $job) }}" class="portal-button-secondary">Batal</a>
                <button type="submit" class="portal-button-primary">
                    <span class="material-symbols-outlined text-[18px]">save</span>
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
@endsection
