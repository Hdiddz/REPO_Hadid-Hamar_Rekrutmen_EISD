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
            <div><label for="salary_amount" class="portal-label">Nominal upah</label><input id="salary_amount" name="salary_amount" type="number" min="1" step="any" value="{{ old('salary_amount', $job ? (int) $job->salary_amount : '') }}" class="portal-input" placeholder="Contoh: 3500000" required>@error('salary_amount')<p class="portal-field-error">{{ $message }}</p>@enderror</div>
            <div><label for="work_hours_per_day" class="portal-label">Jam kerja per hari</label><input id="work_hours_per_day" name="work_hours_per_day" type="number" min="1" max="8" value="{{ old('work_hours_per_day', $job?->work_hours_per_day ?? 8) }}" class="portal-input" required>@error('work_hours_per_day')<p class="portal-field-error">{{ $message }}</p>@enderror</div>
        </div></section>

        <section class="portal-panel p-5 sm:p-7" data-reveal>
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <div>
                    <h2 class="text-lg font-bold">Keterampilan</h2>
                    <p class="mt-1 text-sm text-slate-500">Pilih keterampilan yang dibutuhkan untuk posisi ini.</p>
                </div>
                <button type="button" id="toggle-edit-skills-btn" class="inline-flex items-center gap-1.5 self-start sm:self-auto rounded-xl border border-slate-200 bg-white px-3.5 py-2 text-xs font-bold text-slate-700 shadow-sm transition hover:bg-slate-50 hover:text-slate-950 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-300 dark:hover:bg-slate-800">
                    <span class="material-symbols-outlined text-[17px]">tune</span>
                    <span id="toggle-edit-text">Kelola / Edit Keterampilan</span>
                </button>
            </div>

            <div id="skills-grid" class="mt-5 grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
                @php($selectedSkills = collect(old('skills', $job?->skills?->pluck('id')->all() ?? []))->map(fn($id) => (string) $id))
                @foreach($skills as $skill)
                    <div data-skill-item class="group flex items-center justify-between rounded-xl border border-slate-200 p-3 text-sm font-semibold transition hover:border-brand-400 dark:border-slate-700">
                        <label class="flex flex-1 cursor-pointer items-center gap-3">
                            <input type="checkbox" name="skills[]" value="{{ $skill->id }}" @checked($selectedSkills->contains((string) $skill->id)) class="h-4 w-4 rounded text-brand-600 focus:ring-brand-500">
                            <span>{{ $skill->name }}</span>
                        </label>
                        <button type="button" data-delete-skill="{{ $skill->id }}" data-skill-name="{{ $skill->name }}" class="manage-skill-action hidden shrink-0 rounded-lg p-1 text-rose-500 hover:bg-rose-50 hover:text-rose-700 dark:hover:bg-rose-950/50" title="Hapus keterampilan ini">
                            <span class="material-symbols-outlined text-[18px]">delete</span>
                        </button>
                    </div>
                @endforeach
                @if(old('new_skills'))
                    @foreach((array) old('new_skills') as $newSkill)
                        <div data-skill-item class="group flex items-center justify-between rounded-xl border border-brand-500 bg-brand-50/40 p-3 text-sm font-semibold hover:border-brand-600 dark:border-brand-600 dark:bg-brand-950/40">
                            <label class="flex flex-1 cursor-pointer items-center gap-3">
                                <input type="checkbox" name="new_skills[]" value="{{ $newSkill }}" checked class="h-4 w-4 rounded text-brand-600 focus:ring-brand-500">
                                <span>{{ $newSkill }}</span>
                                <span class="rounded-md bg-brand-100 px-1.5 py-0.5 text-[10px] font-bold uppercase text-brand-800 dark:bg-brand-900 dark:text-brand-200">Baru</span>
                            </label>
                            <button type="button" data-delete-new class="manage-skill-action hidden shrink-0 rounded-lg p-1 text-rose-500 hover:bg-rose-50 hover:text-rose-700 dark:hover:bg-rose-950/50" title="Hapus keterampilan ini">
                                <span class="material-symbols-outlined text-[18px]">delete</span>
                            </button>
                        </div>
                    @endforeach
                @endif
            </div>

            {{-- Input Tambah Keterampilan Baru (Muncul saat tombol Edit Keterampilan ditekan) --}}
            <div id="add-skill-container" class="hidden mt-6 rounded-2xl border border-dashed border-brand-300 bg-brand-50/30 p-5 dark:border-brand-900/70 dark:bg-brand-950/20">
                <label for="custom-skill-input" class="mb-2 block text-xs font-bold uppercase tracking-wider text-brand-900 dark:text-brand-300">
                    Tambah Keterampilan Kustom (Belum ada di daftar):
                </label>
                <div class="flex max-w-md gap-2">
                    <div class="relative flex-1">
                        <input type="text" id="custom-skill-input" placeholder="Contoh: Adobe Photoshop, Drone, Copywriting..." class="portal-input pl-9 bg-white dark:bg-slate-900">
                        <span class="material-symbols-outlined pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-[18px] text-slate-400">edit_note</span>
                    </div>
                    <button type="button" id="add-custom-skill-btn" class="portal-button-primary shrink-0 gap-1.5 font-bold">
                        <span class="material-symbols-outlined text-[18px]">add</span>
                        Tambah
                    </button>
                </div>
                <p class="mt-2 text-xs text-slate-500 dark:text-slate-400">Ketik nama keterampilan lalu klik Tambah atau tekan Enter untuk memasukkannya ke daftar pilihan di atas.</p>
            </div>

            @error('skills')<p class="portal-field-error">{{ $message }}</p>@enderror
            @error('skills.*')<p class="portal-field-error">{{ $message }}</p>@enderror
        </section>

        @if($isEditing)<section class="portal-panel p-5 sm:p-7"><label for="status" class="portal-label">Status lowongan</label><select id="status" name="status" class="portal-input"><option value="open" @selected(old('status', $job->status) === 'open')>Dibuka</option><option value="closed" @selected(old('status', $job->status) === 'closed')>Ditutup</option></select>@error('status')<p class="portal-field-error">{{ $message }}</p>@enderror</section>@endif
        <div class="flex justify-end gap-3"><a href="{{ route('employer.dashboard') }}" class="portal-button-secondary">Batal</a><button class="portal-button-primary"><span class="material-symbols-outlined text-[18px]">{{ $isEditing ? 'save' : 'publish' }}</span>{{ $isEditing ? 'Simpan perubahan' : 'Publikasikan lowongan' }}</button></div>
    </form>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const toggleBtn = document.getElementById('toggle-edit-skills-btn');
        const toggleText = document.getElementById('toggle-edit-text');
        const toggleIcon = toggleBtn?.querySelector('.material-symbols-outlined');
        const addContainer = document.getElementById('add-skill-container');
        const input = document.getElementById('custom-skill-input');
        const btn = document.getElementById('add-custom-skill-btn');
        const grid = document.getElementById('skills-grid');
        const csrfToken = document.querySelector('input[name="_token"]')?.value;

        let isManaging = false;

        const updateManageMode = () => {
            if (isManaging) {
                addContainer?.classList.remove('hidden');
                document.querySelectorAll('.manage-skill-action').forEach(el => el.classList.remove('hidden'));
                toggleBtn?.classList.remove('bg-white', 'text-slate-700', 'dark:bg-slate-900', 'dark:text-slate-300');
                toggleBtn?.classList.add('bg-emerald-600', 'text-white', 'border-emerald-600', 'hover:bg-emerald-700');
                if (toggleText) toggleText.textContent = 'Selesai Mengelola';
                if (toggleIcon) toggleIcon.textContent = 'check';
                input?.focus();
            } else {
                addContainer?.classList.add('hidden');
                document.querySelectorAll('.manage-skill-action').forEach(el => el.classList.add('hidden'));
                toggleBtn?.classList.remove('bg-emerald-600', 'text-white', 'border-emerald-600', 'hover:bg-emerald-700');
                toggleBtn?.classList.add('bg-white', 'text-slate-700', 'dark:bg-slate-900', 'dark:text-slate-300');
                if (toggleText) toggleText.textContent = 'Kelola / Edit Keterampilan';
                if (toggleIcon) toggleIcon.textContent = 'tune';
            }
        };

        toggleBtn?.addEventListener('click', () => {
            isManaging = !isManaging;
            updateManageMode();
        });

        // Handle delete skill
        grid?.addEventListener('click', async (e) => {
            const deleteBtn = e.target.closest('[data-delete-skill], [data-delete-new]');
            if (!deleteBtn) return;

            e.preventDefault();
            e.stopPropagation();

            const card = deleteBtn.closest('[data-skill-item]');
            const skillId = deleteBtn.dataset.deleteSkill;
            const skillName = deleteBtn.dataset.skillName || card?.querySelector('span')?.textContent?.trim() || 'keterampilan ini';

            const confirmed = await window.showAppConfirm({
                title: 'Hapus Keterampilan?',
                message: `Hapus keterampilan "${skillName}" dari pilihan sistem?`,
                confirmText: 'Ya, Hapus',
                type: 'danger',
                icon: 'delete'
            });
            if (!confirmed) return;

            if (skillId) {
                try {
                    deleteBtn.disabled = true;
                    const res = await fetch(`/mitra/keterampilan/${skillId}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json',
                        }
                    });
                    const data = await res.json();
                    if (data.success) {
                        card.style.transition = 'opacity 300ms ease, transform 300ms ease';
                        card.style.opacity = '0';
                        card.style.transform = 'scale(0.9)';
                        setTimeout(() => card.remove(), 300);
                    } else {
                        await window.showAppAlert({
                            title: 'Gagal',
                            message: data.message || 'Gagal menghapus keterampilan.',
                            type: 'danger',
                            icon: 'error'
                        });
                        deleteBtn.disabled = false;
                    }
                } catch (err) {
                    await window.showAppAlert({
                        title: 'Kesalahan',
                        message: 'Terjadi kesalahan saat menghapus.',
                        type: 'danger',
                        icon: 'error'
                    });
                    deleteBtn.disabled = false;
                }
            } else {
                // Newly added element
                card.remove();
            }
        });

        // Add new skill
        const addSkill = () => {
            const val = input.value.trim();
            if (!val) return;

            // Check if already in grid
            const items = grid.querySelectorAll('[data-skill-item]');
            for (const item of items) {
                const text = item.querySelector('span')?.textContent?.trim() || '';
                if (text.toLowerCase() === val.toLowerCase()) {
                    const cb = item.querySelector('input[type="checkbox"]');
                    if (cb) cb.checked = true;
                    input.value = '';
                    item.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                    return;
                }
            }

            // Create new checkbox item
            const newCard = document.createElement('div');
            newCard.setAttribute('data-skill-item', '');
            newCard.className = 'group flex items-center justify-between rounded-xl border border-brand-500 bg-brand-50/40 p-3 text-sm font-semibold hover:border-brand-600 dark:border-brand-600 dark:bg-brand-950/40';

            const newLabel = document.createElement('label');
            newLabel.className = 'flex flex-1 cursor-pointer items-center gap-3';

            const newCheckbox = document.createElement('input');
            newCheckbox.type = 'checkbox';
            newCheckbox.name = 'new_skills[]';
            newCheckbox.value = val;
            newCheckbox.checked = true;
            newCheckbox.className = 'h-4 w-4 rounded text-brand-600 focus:ring-brand-500';

            const span = document.createElement('span');
            span.textContent = val;

            const badge = document.createElement('span');
            badge.className = 'rounded-md bg-brand-100 px-1.5 py-0.5 text-[10px] font-bold uppercase text-brand-800 dark:bg-brand-900 dark:text-brand-200';
            badge.textContent = 'Baru';

            newLabel.appendChild(newCheckbox);
            newLabel.appendChild(span);
            newLabel.appendChild(badge);

            const deleteBtn = document.createElement('button');
            deleteBtn.type = 'button';
            deleteBtn.setAttribute('data-delete-new', '');
            deleteBtn.className = 'manage-skill-action shrink-0 rounded-lg p-1 text-rose-500 hover:bg-rose-50 hover:text-rose-700 dark:hover:bg-rose-950/50';
            deleteBtn.title = 'Hapus keterampilan ini';
            deleteBtn.innerHTML = '<span class="material-symbols-outlined text-[18px]">delete</span>';

            newCard.appendChild(newLabel);
            newCard.appendChild(deleteBtn);

            grid.appendChild(newCard);
            input.value = '';
            input.focus();
        };

        btn?.addEventListener('click', addSkill);
        input?.addEventListener('keydown', (e) => {
            if (e.key === 'Enter') {
                e.preventDefault();
                addSkill();
            }
        });
    });
</script>
@endpush
