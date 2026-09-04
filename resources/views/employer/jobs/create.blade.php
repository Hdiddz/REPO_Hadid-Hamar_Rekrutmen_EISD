@extends('layouts.employer')

@php($isEditing = filled($job))
@section('title', ($isEditing ? 'Edit' : 'Pasang').' Lowongan | KerjaLokal')
@section('portal_title', $isEditing ? 'Perbarui lowongan' : 'Pasang lowongan baru')
@section('portal_description', 'Isi informasi yang dapat dipahami pencari kerja sebelum mereka mengajukan lamaran.')
@section('portal_actions')<a href="{{ $isEditing ? route('jobs.show', $job) : route('employer.dashboard') }}" class="portal-button-secondary"><span class="material-symbols-outlined text-[18px]">arrow_back</span>Kembali</a>@endsection

@section('content')
    <form action="{{ $isEditing ? route('employer.jobs.update', $job) : route('employer.jobs.store') }}" method="POST" enctype="multipart/form-data" class="mx-auto max-w-4xl space-y-6">@csrf @if($isEditing)@method('PUT')@endif
        <section class="portal-panel p-5 sm:p-7" data-reveal><div class="mb-6"><h2 class="text-lg font-bold">Informasi utama</h2><p class="mt-1 text-sm text-slate-500">Judul, kategori, dan lokasi membantu lowongan mudah ditemukan.</p></div>
            <div class="grid gap-5 sm:grid-cols-2">
                <div class="sm:col-span-2"><label for="title" class="portal-label">Judul posisi</label><input id="title" name="title" value="{{ old('title', $job?->title) }}" class="portal-input" placeholder="Contoh: Barista dan Kasir" required>@error('title')<p class="portal-field-error">{{ $message }}</p>@enderror</div>
                <div><label for="category_id" class="portal-label">Kategori</label><select id="category_id" name="category_id" class="portal-input" required><option value="">Pilih kategori</option>@foreach($categories as $category)<option value="{{ $category->id }}" @selected((string) old('category_id', $job?->category_id) === (string) $category->id)>{{ $category->name }}</option>@endforeach</select>@error('category_id')<p class="portal-field-error">{{ $message }}</p>@enderror</div>
                <div><label for="location" class="portal-label">Lokasi kerja</label><input id="location" name="location" value="{{ old('location', $job?->location) }}" class="portal-input" placeholder="Coblong, Kota Bandung" required>@error('location')<p class="portal-field-error">{{ $message }}</p>@enderror</div>
                <div class="sm:col-span-2"><label for="description" class="portal-label">Deskripsi dan tanggung jawab</label><textarea id="description" name="description" rows="7" class="portal-input" placeholder="Jelaskan kegiatan kerja, jadwal, dan ekspektasi secara rinci." required>{{ old('description', $job?->description) }}</textarea>@error('description')<p class="portal-field-error">{{ $message }}</p>@enderror</div>
            </div>
        </section>

        {{-- Panel Foto Cover & Foto Lingkungan Kerja --}}
        <section class="portal-panel p-5 sm:p-7" data-reveal>
            <div class="mb-6">
                <div class="flex items-center gap-2.5">
                    <span class="grid h-9 w-9 place-items-center rounded-xl bg-brand-50 text-brand-700 dark:bg-brand-950 dark:text-brand-300">
                        <span class="material-symbols-outlined text-[20px]">add_photo_alternate</span>
                    </span>
                    <div>
                        <h2 class="text-lg font-bold text-slate-900 dark:text-white">Foto Lowongan &amp; Lingkungan Kerja</h2>
                        <p class="text-xs sm:text-sm text-slate-500">Tampilkan foto menarik agar calon pelamar mengenal tempat dan suasana kerja Anda.</p>
                    </div>
                </div>
            </div>

            <div class="grid gap-6">
                {{-- 1. Upload Cover Lowongan --}}
                <div class="space-y-3">
                    <label for="cover_image" class="portal-label block">
                        <span>Foto Sampul (Cover Lowongan)</span>
                        <span class="text-xs font-normal text-slate-400 block mt-0.5">Gambar utama yang tampil sebagai banner lowongan (format JPG, PNG, WEBP maks. 5MB, disarankan lanskap 16:9).</span>
                    </label>

                    @if($isEditing && $job?->cover_image)
                        <div id="current-cover-container" class="relative overflow-hidden rounded-2xl border border-slate-200 bg-slate-50 dark:border-slate-800 dark:bg-slate-900 p-3 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                            <div class="flex items-center gap-3">
                                <img src="{{ $job->cover_image_url }}" alt="{{ $job->title }}" class="h-16 w-24 rounded-xl object-cover ring-1 ring-slate-200 dark:ring-slate-700 shrink-0">
                                <div>
                                    <p class="text-xs font-bold text-slate-900 dark:text-white">Foto Cover Aktif</p>
                                    <span class="text-[11px] text-slate-500">Tersimpan di sistem. Unggah berkas baru di bawah jika ingin menggantinya.</span>
                                </div>
                            </div>
                            <label class="inline-flex items-center gap-2 text-xs font-bold text-rose-600 dark:text-rose-400 cursor-pointer select-none">
                                <input type="checkbox" name="remove_cover_image" value="1" id="remove_cover_image" class="rounded text-rose-600 focus:ring-rose-500">
                                <span>Hapus foto cover ini</span>
                            </label>
                        </div>
                    @endif

                    <div class="relative">
                        <input type="file" id="cover_image" name="cover_image" accept="image/jpeg,image/png,image/webp,image/jpg" class="portal-input file:mr-4 file:py-1 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-brand-50 file:text-brand-700 hover:file:bg-brand-100 dark:file:bg-brand-950 dark:file:text-brand-300">
                    </div>

                    {{-- Live Preview for Cover --}}
                    <div id="cover-preview-wrapper" class="hidden mt-3 space-y-2">
                        <div class="flex items-center justify-between">
                            <p class="text-xs font-bold text-slate-700 dark:text-slate-300 flex items-center gap-1.5">
                                <span class="material-symbols-outlined text-[16px] text-brand-600 dark:text-brand-400">check_circle</span>
                                <span>Foto Sampul Baru yang Disesuaikan (16:9):</span>
                            </p>
                            <button type="button" onclick="openCropCoverModal()" class="text-xs font-semibold text-brand-600 hover:text-brand-700 dark:text-brand-400 inline-flex items-center gap-1 cursor-pointer">
                                <span class="material-symbols-outlined text-[15px]">tune</span>
                                <span>Sesuaikan Ulang Cover</span>
                            </button>
                        </div>
                        <div class="relative overflow-hidden rounded-2xl border border-slate-200 bg-slate-100 dark:border-slate-800 dark:bg-slate-900 aspect-video max-h-64 shadow-xs">
                            <img id="cover-preview-img" src="#" alt="Pratinjau Cover" class="h-full w-full object-cover">
                        </div>
                    </div>

                    @error('cover_image')<p class="portal-field-error">{{ $message }}</p>@enderror
                </div>

                {{-- 2. Upload Foto Lingkungan Kerja (Slide Photos) --}}
                <div class="space-y-3 pt-4 border-t border-slate-100 dark:border-slate-800/80">
                    <label for="workplace_photos" class="portal-label block">
                        <span>Foto Lingkungan Kerja (Slide / Suasana Tempat Kerja)</span>
                        <span class="text-xs font-normal text-slate-400 block mt-0.5">Unggah hingga 6 foto tempat, tim, meja kerja, atau fasilitas UMKM Anda (tampil interaktif sebagai galeri slide).</span>
                    </label>

                    @if($isEditing && $job?->workplacePhotos && $job->workplacePhotos->isNotEmpty())
                        <div class="space-y-2">
                            <p class="text-xs font-bold text-slate-700 dark:text-slate-300">Foto Lingkungan Kerja Saat Ini ({{ $job->workplacePhotos->count() }}):</p>
                            <div class="grid grid-cols-2 gap-3 sm:grid-cols-3 md:grid-cols-4">
                                @foreach($job->workplacePhotos as $photo)
                                    <div class="group relative overflow-hidden rounded-2xl border border-slate-200 bg-slate-50 dark:border-slate-800 dark:bg-slate-900 p-1.5">
                                        <div class="relative aspect-video overflow-hidden rounded-xl bg-slate-200">
                                            <img src="{{ $photo->photo_url }}" alt="Suasana kerja" class="h-full w-full object-cover">
                                        </div>
                                        <label class="mt-2 flex items-center justify-between px-1 text-[11px] font-semibold text-rose-600 dark:text-rose-400 cursor-pointer">
                                            <span>Hapus foto</span>
                                            <input type="checkbox" name="delete_workplace_photo_ids[]" value="{{ $photo->id }}" class="rounded text-rose-600 focus:ring-rose-500">
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                            <p class="text-[11px] text-slate-400">Centang kotak &quot;Hapus foto&quot; pada foto yang ingin dihapus saat menyimpan perubahan.</p>
                        </div>
                    @endif

                    <div class="relative">
                        <input type="file" id="workplace_photos" name="workplace_photos[]" accept="image/jpeg,image/png,image/webp,image/jpg" multiple class="portal-input file:mr-4 file:py-1 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-brand-50 file:text-brand-700 hover:file:bg-brand-100 dark:file:bg-brand-950 dark:file:text-brand-300">
                    </div>

                    {{-- Live Previews for Multiple Workplace Photos --}}
                    <div id="workplace-previews-wrapper" class="hidden mt-3 space-y-2.5">
                        <div class="flex items-center justify-between">
                            <p class="text-xs font-bold text-slate-700 dark:text-slate-300 flex items-center gap-1.5">
                                <span class="material-symbols-outlined text-[16px] text-brand-600 dark:text-brand-400">collections</span>
                                <span>Foto yang Baru Disesuaikan (<span id="workplace-previews-count">0</span>):</span>
                            </p>
                            <button type="button" onclick="openCropWorkplaceModal()" class="text-xs font-semibold text-brand-600 hover:text-brand-700 dark:text-brand-400 inline-flex items-center gap-1 cursor-pointer">
                                <span class="material-symbols-outlined text-[15px]">tune</span>
                                <span>Sesuaikan Ulang Semua</span>
                            </button>
                        </div>
                        <div id="workplace-previews-grid" class="grid grid-cols-2 gap-3 sm:grid-cols-3 md:grid-cols-4"></div>
                    </div>

                    @error('workplace_photos')<p class="portal-field-error">{{ $message }}</p>@enderror
                    @error('workplace_photos.*')<p class="portal-field-error">{{ $message }}</p>@enderror
                </div>
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
                    <span id="toggle-edit-text">Tambah Keterampilan</span>
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

        <div class="flex items-center justify-end gap-3 pt-2">
            <a href="{{ $isEditing ? route('jobs.show', $job) : route('employer.dashboard') }}" class="portal-button-secondary">Batal</a>
            <button type="submit" class="portal-button-primary">
                <span class="material-symbols-outlined text-[18px]">{{ $isEditing ? 'save' : 'publish' }}</span>
                <span>{{ $isEditing ? 'Simpan Perubahan' : 'Publikasikan Lowongan' }}</span>
            </button>
        </div>
    </form>
@endsection

@push('modals')
    {{-- Modal Pratinjau, Cropping & Resizing Foto Sampul (Cover Lowongan 16:9) --}}
    <div id="cropCoverModal" class="fixed inset-0 z-[100] hidden items-center justify-center bg-slate-950/75 p-3 sm:p-4 backdrop-blur-md transition-all duration-200" role="dialog" aria-modal="true" aria-labelledby="cropCoverTitle">
        <div class="relative flex flex-col w-full max-w-xl max-h-[92vh] overflow-hidden rounded-3xl border border-slate-200/80 bg-white shadow-2xl dark:border-slate-800 dark:bg-slate-900 animate-page-enter">
            <!-- Modal Header -->
            <div class="flex items-center justify-between border-b border-slate-100 px-5 sm:px-6 py-4 dark:border-slate-800">
                <div class="flex items-center gap-3">
                    <span class="grid h-10 w-10 place-items-center rounded-2xl bg-brand-50 text-brand-700 dark:bg-brand-950 dark:text-brand-300">
                        <span class="material-symbols-outlined text-[22px]">panorama</span>
                    </span>
                    <div>
                        <h3 id="cropCoverTitle" class="text-base font-bold text-slate-950 dark:text-white">Sesuaikan &amp; Potong Foto Sampul</h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400">Atur posisi dan perbesaran banner lowongan (Rasio Lanskap 16:9).</p>
                    </div>
                </div>
                <button type="button" onclick="closeCropCoverModal()" class="grid h-8 w-8 place-items-center rounded-xl text-slate-400 hover:bg-slate-100 hover:text-slate-700 dark:hover:bg-slate-800 dark:hover:text-white transition cursor-pointer" aria-label="Tutup Modal">
                    <span class="material-symbols-outlined text-[20px]">close</span>
                </button>
            </div>

            <!-- Modal Body (Canvas Stage & Controls) -->
            <div class="flex-1 overflow-y-auto p-4 sm:p-6 space-y-4">
                <div class="flex flex-col items-center justify-center">
                    <!-- 16:9 Canvas Stage Container -->
                    <div class="relative flex flex-col items-center">
                        <div id="coverCropStageContainer" class="relative overflow-hidden rounded-2xl border-2 border-brand-500 bg-slate-950 shadow-inner select-none touch-none cursor-grab active:cursor-grabbing transition-all duration-200" style="width: 384px; height: 216px;">
                            <canvas id="coverCropCanvas" width="384" height="216" class="block h-full w-full"></canvas>

                            <!-- Rule of Thirds Grid Overlay -->
                            <div class="pointer-events-none absolute inset-0 grid grid-cols-3 grid-rows-3 opacity-25">
                                <div class="border-r border-b border-white"></div>
                                <div class="border-r border-b border-white"></div>
                                <div class="border-b border-white"></div>
                                <div class="border-r border-b border-white"></div>
                                <div class="border-r border-b border-white"></div>
                                <div class="border-b border-white"></div>
                                <div class="border-r border-white"></div>
                                <div class="border-r border-white"></div>
                                <div></div>
                            </div>
                        </div>

                        <span class="mt-2 text-[11px] text-slate-400 flex items-center gap-1 font-medium">
                            <span class="material-symbols-outlined text-[15px]">pan_tool</span>
                            Klik &amp; geser gambar untuk mengatur posisi sampul
                        </span>
                    </div>

                    <!-- Stage Controls (Zoom, Rotate, Reset) -->
                    <div class="w-full max-w-md mt-4 space-y-3">
                        <div class="space-y-1">
                            <div class="flex items-center justify-between text-xs text-slate-600 dark:text-slate-400 font-semibold">
                                <span>Perbesaran (Zoom)</span>
                                <span id="coverZoomValueText" class="font-mono text-[11px] text-slate-500">1.0x</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <button type="button" onclick="adjustCoverZoom(-0.15)" class="h-8 w-8 rounded-xl bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 flex items-center justify-center text-slate-700 dark:text-slate-200 cursor-pointer transition" title="Perkecil">
                                    <span class="material-symbols-outlined text-[17px]">remove</span>
                                </button>
                                <input type="range" id="coverCropZoomRange" min="1" max="3" step="0.02" value="1" oninput="setCoverCropZoom(this.value)" class="flex-1 accent-brand-600 cursor-pointer h-1.5 bg-slate-200 rounded-lg dark:bg-slate-700">
                                <button type="button" onclick="adjustCoverZoom(0.15)" class="h-8 w-8 rounded-xl bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 flex items-center justify-center text-slate-700 dark:text-slate-200 cursor-pointer transition" title="Perbesar">
                                    <span class="material-symbols-outlined text-[17px]">add</span>
                                </button>
                            </div>
                        </div>

                        <div class="flex items-center gap-2 pt-1">
                            <button type="button" onclick="rotateCoverPhoto()" class="flex-1 portal-button-secondary !py-2 !px-3 text-xs font-semibold gap-1.5 justify-center cursor-pointer">
                                <span class="material-symbols-outlined text-[16px]">rotate_right</span>
                                <span>Putar 90°</span>
                            </button>
                            <button type="button" onclick="resetCoverTransform()" class="flex-1 portal-button-secondary !py-2 !px-3 text-xs font-semibold gap-1.5 justify-center cursor-pointer">
                                <span class="material-symbols-outlined text-[16px]">restart_alt</span>
                                <span>Reset Posisi</span>
                            </button>
                        </div>

                        <div class="rounded-2xl bg-brand-50/70 p-3 dark:bg-brand-950/30 border border-brand-200/60 dark:border-brand-900/40 w-full text-[11px] text-brand-900 dark:text-brand-200 leading-tight">
                            <span class="font-bold flex items-center gap-1 mb-1 text-brand-800 dark:text-brand-300">
                                <span class="material-symbols-outlined text-[15px]">verified</span>
                                Standar Banner 16:9 (1600 × 900 px)
                            </span>
                            Foto otomatis dikompresi jernih dan dioptimasi sehingga ringan dan cepat dimuat.
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="flex flex-wrap items-center justify-between gap-3 border-t border-slate-100 px-5 sm:px-6 py-4 dark:border-slate-800 bg-slate-50/70 dark:bg-slate-950/50">
                <button type="button" onclick="document.getElementById('cover_image').click()" class="portal-button-secondary !py-2 !px-3.5 text-xs font-semibold gap-1 cursor-pointer">
                    <span class="material-symbols-outlined text-[16px]">add_photo_alternate</span>
                    <span>Pilih Berkas Lain</span>
                </button>
                <div class="flex items-center gap-2">
                    <button type="button" onclick="closeCropCoverModal()" class="portal-button-secondary !py-2 !px-4 text-xs font-semibold cursor-pointer">
                        Batal
                    </button>
                    <button type="button" id="applyCoverCropBtn" onclick="applyAndSaveCoverPhoto()" class="portal-button-primary !py-2 !px-5 text-xs font-bold gap-1.5 cursor-pointer">
                        <span class="material-symbols-outlined text-[17px]">check_circle</span>
                        <span id="applyCoverCropBtnText">Terapkan &amp; Simpan Cover</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal Pratinjau, Cropping & Resizing Foto Lingkungan Kerja --}}
    <div id="cropWorkplaceModal" class="fixed inset-0 z-[100] hidden items-center justify-center bg-slate-950/75 p-3 sm:p-4 backdrop-blur-md transition-all duration-200" role="dialog" aria-modal="true" aria-labelledby="cropWorkplaceTitle">
        <div class="relative flex flex-col w-full max-w-2xl max-h-[92vh] overflow-hidden rounded-3xl border border-slate-200/80 bg-white shadow-2xl dark:border-slate-800 dark:bg-slate-900 animate-page-enter">
            <!-- Modal Header -->
            <div class="flex items-center justify-between border-b border-slate-100 px-5 sm:px-6 py-4 dark:border-slate-800">
                <div class="flex items-center gap-3">
                    <span class="grid h-10 w-10 place-items-center rounded-2xl bg-brand-50 text-brand-700 dark:bg-brand-950 dark:text-brand-300">
                        <span class="material-symbols-outlined text-[22px]">photo_size_select_large</span>
                    </span>
                    <div>
                        <h3 id="cropWorkplaceTitle" class="text-base font-bold text-slate-950 dark:text-white">Sesuaikan &amp; Potong Foto Lingkungan</h3>
                        <p id="cropWorkplaceSubtitle" class="text-xs text-slate-500 dark:text-slate-400">Atur posisi dan komposisi foto suasana kerja Anda.</p>
                    </div>
                </div>
                <button type="button" onclick="closeCropWorkplaceModal()" class="grid h-8 w-8 place-items-center rounded-xl text-slate-400 hover:bg-slate-100 hover:text-slate-700 dark:hover:bg-slate-800 dark:hover:text-white transition cursor-pointer" aria-label="Tutup Modal">
                    <span class="material-symbols-outlined text-[20px]">close</span>
                </button>
            </div>

            <!-- Aspect Ratio Selector & Detection Banner -->
            <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-100 bg-slate-50/80 px-5 sm:px-6 py-2.5 dark:border-slate-800 dark:bg-slate-950/40">
                <div class="flex items-center gap-2">
                    <span class="text-xs font-semibold text-slate-600 dark:text-slate-400">Rasio Aspek:</span>
                    <span id="cropRatioIndicatorBadge" class="inline-flex items-center gap-1 rounded-full px-2.5 py-0.5 text-[11px] font-bold bg-brand-100 text-brand-800 dark:bg-brand-950 dark:text-brand-300">
                        <span class="material-symbols-outlined text-[14px]">auto_fix_high</span>
                        <span id="cropRatioIndicatorText">Otomatis</span>
                    </span>
                </div>
                <div class="inline-flex items-center rounded-xl bg-slate-200/80 p-1 dark:bg-slate-800" role="tablist">
                    <button type="button" onclick="setWorkplaceRatio('4/5')" data-ratio-btn="4/5" class="rounded-lg px-2.5 py-1 text-xs font-bold transition">4:5 (Standar)</button>
                    <button type="button" onclick="setWorkplaceRatio('16/9')" data-ratio-btn="16/9" class="rounded-lg px-2.5 py-1 text-xs font-bold transition">16:9 (Lanskap)</button>
                    <button type="button" onclick="setWorkplaceRatio('1/1')" data-ratio-btn="1/1" class="rounded-lg px-2.5 py-1 text-xs font-bold transition">1:1 (Persegi)</button>
                </div>
            </div>

            <!-- Multi-Photo Stepper / Thumbnails (if > 1 photo) -->
            <div id="cropPhotoStepperBar" class="hidden items-center justify-between border-b border-slate-100 bg-white px-5 sm:px-6 py-2 dark:border-slate-800 dark:bg-slate-900">
                <button type="button" onclick="navigateCropPhoto(-1)" id="prevCropPhotoBtn" class="inline-flex items-center gap-1 rounded-xl px-2.5 py-1 text-xs font-bold text-slate-700 hover:bg-slate-100 dark:text-slate-300 dark:hover:bg-slate-800 disabled:opacity-30 disabled:pointer-events-none cursor-pointer transition">
                    <span class="material-symbols-outlined text-[18px]">chevron_left</span>
                    <span>Sebelumnya</span>
                </button>
                <div id="cropPhotoStepperPills" class="flex items-center gap-1.5 overflow-x-auto max-w-[280px] sm:max-w-sm px-1 py-0.5 scrollbar-thin"></div>
                <button type="button" onclick="navigateCropPhoto(1)" id="nextCropPhotoBtn" class="inline-flex items-center gap-1 rounded-xl px-2.5 py-1 text-xs font-bold text-slate-700 hover:bg-slate-100 dark:text-slate-300 dark:hover:bg-slate-800 disabled:opacity-30 disabled:pointer-events-none cursor-pointer transition">
                    <span>Selanjutnya</span>
                    <span class="material-symbols-outlined text-[18px]">chevron_right</span>
                </button>
            </div>

            <!-- Modal Body (Canvas Stage & Controls) -->
            <div class="flex-1 overflow-y-auto p-4 sm:p-6 space-y-4">
                <div class="flex flex-col items-center justify-center">
                    <!-- Dynamic Canvas Container -->
                    <div class="relative flex flex-col items-center">
                        <div id="workplaceCropStageContainer" class="relative overflow-hidden rounded-2xl border-2 border-brand-500 bg-slate-950 shadow-inner select-none touch-none cursor-grab active:cursor-grabbing transition-all duration-200">
                            <canvas id="workplaceCropCanvas" class="block"></canvas>

                            <!-- Rule of Thirds Grid Overlay -->
                            <div class="pointer-events-none absolute inset-0 grid grid-cols-3 grid-rows-3 opacity-25">
                                <div class="border-r border-b border-white"></div>
                                <div class="border-r border-b border-white"></div>
                                <div class="border-b border-white"></div>
                                <div class="border-r border-b border-white"></div>
                                <div class="border-r border-b border-white"></div>
                                <div class="border-b border-white"></div>
                                <div class="border-r border-white"></div>
                                <div class="border-r border-white"></div>
                                <div></div>
                            </div>
                        </div>

                        <span class="mt-2 text-[11px] text-slate-400 flex items-center gap-1 font-medium">
                            <span class="material-symbols-outlined text-[15px]">pan_tool</span>
                            Klik &amp; geser foto untuk mengubah posisi
                        </span>
                    </div>

                    <!-- Stage Controls (Zoom, Rotate, Reset) -->
                    <div class="w-full max-w-md mt-4 space-y-3">
                        <div class="space-y-1">
                            <div class="flex items-center justify-between text-xs text-slate-600 dark:text-slate-400 font-semibold">
                                <span>Perbesaran (Zoom)</span>
                                <span id="cropZoomValueText" class="font-mono text-[11px] text-slate-500">1.0x</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <button type="button" onclick="adjustWorkplaceZoom(-0.15)" class="h-8 w-8 rounded-xl bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 flex items-center justify-center text-slate-700 dark:text-slate-200 cursor-pointer transition" title="Perkecil">
                                    <span class="material-symbols-outlined text-[17px]">remove</span>
                                </button>
                                <input type="range" id="workplaceCropZoomRange" min="1" max="3" step="0.02" value="1" oninput="setWorkplaceCropZoom(this.value)" class="flex-1 accent-brand-600 cursor-pointer h-1.5 bg-slate-200 rounded-lg dark:bg-slate-700">
                                <button type="button" onclick="adjustWorkplaceZoom(0.15)" class="h-8 w-8 rounded-xl bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 flex items-center justify-center text-slate-700 dark:text-slate-200 cursor-pointer transition" title="Perbesar">
                                    <span class="material-symbols-outlined text-[17px]">add</span>
                                </button>
                            </div>
                        </div>

                        <div class="flex items-center gap-2 pt-1">
                            <button type="button" onclick="rotateWorkplacePhoto()" class="flex-1 portal-button-secondary !py-2 !px-3 text-xs font-semibold gap-1.5 justify-center cursor-pointer">
                                <span class="material-symbols-outlined text-[16px]">rotate_right</span>
                                <span>Putar 90°</span>
                            </button>
                            <button type="button" onclick="resetWorkplaceTransform()" class="flex-1 portal-button-secondary !py-2 !px-3 text-xs font-semibold gap-1.5 justify-center cursor-pointer">
                                <span class="material-symbols-outlined text-[16px]">restart_alt</span>
                                <span>Reset Posisi</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="flex flex-wrap items-center justify-between gap-3 border-t border-slate-100 px-5 sm:px-6 py-4 dark:border-slate-800 bg-slate-50/70 dark:bg-slate-950/50">
                <button type="button" onclick="document.getElementById('workplace_photos').click()" class="portal-button-secondary !py-2 !px-3.5 text-xs font-semibold gap-1 cursor-pointer">
                    <span class="material-symbols-outlined text-[16px]">add_photo_alternate</span>
                    <span>Pilih Berkas Lain</span>
                </button>
                <div class="flex items-center gap-2">
                    <button type="button" onclick="closeCropWorkplaceModal()" class="portal-button-secondary !py-2 !px-4 text-xs font-semibold cursor-pointer">
                        Batal
                    </button>
                    <button type="button" id="applyWorkplaceCropBtn" onclick="applyAndSaveWorkplacePhotos()" class="portal-button-primary !py-2 !px-5 text-xs font-bold gap-1.5 cursor-pointer">
                        <span class="material-symbols-outlined text-[17px]">check_circle</span>
                        <span id="applyWorkplaceCropBtnText">Terapkan &amp; Simpan Foto</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
@endpush

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
                if (toggleText) toggleText.textContent = 'Tambah Keterampilan';
                if (toggleIcon) toggleIcon.textContent = 'tune';
            }
        };

        toggleBtn?.addEventListener('click', () => {
            isManaging = !isManaging;
            updateManageMode();
        });

        // Hapus hanya keterampilan baru yang belum tersimpan.
        grid?.addEventListener('click', async (e) => {
            const deleteBtn = e.target.closest('[data-delete-new]');
            if (!deleteBtn) return;

            e.preventDefault();
            e.stopPropagation();

            const card = deleteBtn.closest('[data-skill-item]');
            const skillName = card?.querySelector('span')?.textContent?.trim() || 'keterampilan ini';

            const confirmed = await window.showAppConfirm({
                title: 'Hapus Keterampilan?',
                message: `Hapus keterampilan baru "${skillName}" dari formulir ini?`,
                confirmText: 'Ya, Hapus',
                type: 'danger',
                icon: 'delete'
            });
            if (!confirmed) return;

            card?.remove();
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

        // ==========================================
        // Cover Photo Interactive Crop & Compression (16:9)
        // ==========================================
        const coverInput = document.getElementById('cover_image');
        const coverWrapper = document.getElementById('cover-preview-wrapper');
        const coverImg = document.getElementById('cover-preview-img');
        const removeCoverCheckbox = document.getElementById('remove_cover_image');

        let coverSourceImg = null;
        let coverOriginalFile = null;
        let coverZoom = 1.0;
        let coverRotation = 0;
        let coverCropX = 0;
        let coverCropY = 0;
        let isCoverDragging = false;
        let coverDragStartX = 0;
        let coverDragStartY = 0;
        let coverEventsBound = false;

        function getCoverStageDims() {
            const isMobile = window.innerWidth < 640;
            return isMobile
                ? { stageW: 288, stageH: 162, exportW: 1600, exportH: 900 }
                : { stageW: 384, stageH: 216, exportW: 1600, exportH: 900 };
        }

        function handleCoverFileSelected(file) {
            if (!file) return;

            if (!['image/jpeg', 'image/png', 'image/webp', 'image/jpg'].includes(file.type)) {
                if (window.showAppAlert) {
                    window.showAppAlert({
                        title: 'Format Berkas',
                        message: 'Silakan pilih gambar sampul dengan format JPG, JPEG, PNG, atau WEBP.',
                        type: 'warning'
                    });
                }
                if (coverInput) coverInput.value = '';
                return;
            }

            coverOriginalFile = file;

            const reader = new FileReader();
            reader.onload = function(e) {
                const img = new Image();
                img.onload = function() {
                    coverSourceImg = img;
                    coverZoom = 1.0;
                    coverRotation = 0;
                    coverCropX = 0;
                    coverCropY = 0;
                    openCropCoverModal();
                };
                img.src = e.target.result;
            };
            reader.readAsDataURL(file);
        }

        function openCropCoverModal() {
            if (!coverSourceImg) return;
            const modal = document.getElementById('cropCoverModal');
            if (!modal) return;

            if (modal.parentElement !== document.body) {
                document.body.appendChild(modal);
            }

            const zoomRange = document.getElementById('coverCropZoomRange');
            const zoomText = document.getElementById('coverZoomValueText');
            if (zoomRange) zoomRange.value = coverZoom.toFixed(2);
            if (zoomText) zoomText.textContent = `${coverZoom.toFixed(2)}x`;

            modal.classList.remove('hidden');
            modal.classList.add('flex');
            document.body.style.overflow = 'hidden';

            initCoverCanvasEvents();
            renderCoverCrop();
        }
        window.openCropCoverModal = openCropCoverModal;

        function closeCropCoverModal() {
            const modal = document.getElementById('cropCoverModal');
            if (modal) {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
            }
            document.body.style.overflow = '';
            // If no preview is set, clear input
            if (!coverImg || !coverImg.src || coverImg.src.endsWith('#') || coverImg.src === window.location.href) {
                if (coverInput) coverInput.value = '';
            }
        }
        window.closeCropCoverModal = closeCropCoverModal;

        function getCoverBaseFitScale(stageW, stageH) {
            if (!coverSourceImg) return 1;
            const isRotated = (coverRotation % 180 !== 0);
            const imgW = isRotated ? coverSourceImg.height : coverSourceImg.width;
            const imgH = isRotated ? coverSourceImg.width : coverSourceImg.height;
            return Math.max(stageW / imgW, stageH / imgH);
        }

        function clampCoverOffsets(stageW, stageH) {
            if (!coverSourceImg) return;
            const isRotated = (coverRotation % 180 !== 0);
            const imgW = isRotated ? coverSourceImg.height : coverSourceImg.width;
            const imgH = isRotated ? coverSourceImg.width : coverSourceImg.height;

            const currentW = imgW * getCoverBaseFitScale(stageW, stageH) * coverZoom;
            const currentH = imgH * getCoverBaseFitScale(stageW, stageH) * coverZoom;

            const maxOffsetX = Math.max(0, (currentW - stageW) / 2);
            const maxOffsetY = Math.max(0, (currentH - stageH) / 2);

            coverCropX = Math.max(-maxOffsetX, Math.min(maxOffsetX, coverCropX));
            coverCropY = Math.max(-maxOffsetY, Math.min(maxOffsetY, coverCropY));
        }

        function renderCoverCrop() {
            if (!coverSourceImg) return;
            const { stageW, stageH } = getCoverStageDims();
            const container = document.getElementById('coverCropStageContainer');
            const canvas = document.getElementById('coverCropCanvas');
            if (!container || !canvas) return;

            container.style.width = `${stageW}px`;
            container.style.height = `${stageH}px`;
            canvas.width = stageW;
            canvas.height = stageH;

            const ctx = canvas.getContext('2d');
            ctx.clearRect(0, 0, stageW, stageH);

            clampCoverOffsets(stageW, stageH);

            const scale = getCoverBaseFitScale(stageW, stageH) * coverZoom;
            const drawW = coverSourceImg.width * scale;
            const drawH = coverSourceImg.height * scale;

            ctx.save();
            ctx.translate(stageW / 2, stageH / 2);
            ctx.translate(coverCropX, coverCropY);
            ctx.rotate((coverRotation * Math.PI) / 180);
            ctx.drawImage(coverSourceImg, -drawW / 2, -drawH / 2, drawW, drawH);
            ctx.restore();
        }

        function setCoverCropZoom(val) {
            coverZoom = parseFloat(val);
            const zoomText = document.getElementById('coverZoomValueText');
            if (zoomText) zoomText.textContent = `${coverZoom.toFixed(2)}x`;
            renderCoverCrop();
        }
        window.setCoverCropZoom = setCoverCropZoom;

        function adjustCoverZoom(delta) {
            coverZoom = Math.max(1.0, Math.min(3.0, coverZoom + delta));
            const zoomRange = document.getElementById('coverCropZoomRange');
            if (zoomRange) zoomRange.value = coverZoom.toFixed(2);
            const zoomText = document.getElementById('coverZoomValueText');
            if (zoomText) zoomText.textContent = `${coverZoom.toFixed(2)}x`;
            renderCoverCrop();
        }
        window.adjustCoverZoom = adjustCoverZoom;

        function rotateCoverPhoto() {
            coverRotation = (coverRotation + 90) % 360;
            coverCropX = 0;
            coverCropY = 0;
            renderCoverCrop();
        }
        window.rotateCoverPhoto = rotateCoverPhoto;

        function resetCoverTransform() {
            coverZoom = 1.0;
            coverRotation = 0;
            coverCropX = 0;
            coverCropY = 0;
            const zoomRange = document.getElementById('coverCropZoomRange');
            const zoomText = document.getElementById('coverZoomValueText');
            if (zoomRange) zoomRange.value = '1.00';
            if (zoomText) zoomText.textContent = '1.00x';
            renderCoverCrop();
        }
        window.resetCoverTransform = resetCoverTransform;

        function initCoverCanvasEvents() {
            if (coverEventsBound) return;
            coverEventsBound = true;

            const container = document.getElementById('coverCropStageContainer');
            if (!container) return;

            container.addEventListener('pointerdown', e => {
                isCoverDragging = true;
                coverDragStartX = e.clientX - coverCropX;
                coverDragStartY = e.clientY - coverCropY;
                container.setPointerCapture(e.pointerId);
            });

            container.addEventListener('pointermove', e => {
                if (!isCoverDragging) return;
                coverCropX = e.clientX - coverDragStartX;
                coverCropY = e.clientY - coverDragStartY;
                renderCoverCrop();
            });

            const stopDrag = e => {
                if (isCoverDragging) {
                    isCoverDragging = false;
                    try { container.releasePointerCapture(e.pointerId); } catch (_) {}
                }
            };

            container.addEventListener('pointerup', stopDrag);
            container.addEventListener('pointercancel', stopDrag);

            container.addEventListener('wheel', e => {
                e.preventDefault();
                const delta = e.deltaY < 0 ? 0.08 : -0.08;
                adjustCoverZoom(delta);
            }, { passive: false });
        }

        async function applyAndSaveCoverPhoto() {
            if (!coverSourceImg) return;

            const applyBtn = document.getElementById('applyCoverCropBtn');
            const applyBtnText = document.getElementById('applyCoverCropBtnText');
            if (applyBtn) applyBtn.disabled = true;
            if (applyBtnText) applyBtnText.textContent = 'Menyimpan Cover...';

            const { stageW, stageH, exportW, exportH } = getCoverStageDims();
            const ratioMultiplier = exportW / stageW;

            const exportCanvas = document.createElement('canvas');
            exportCanvas.width = exportW;
            exportCanvas.height = exportH;
            const eCtx = exportCanvas.getContext('2d');
            eCtx.imageSmoothingEnabled = true;
            eCtx.imageSmoothingQuality = 'high';

            clampCoverOffsets(stageW, stageH);

            const scale = getCoverBaseFitScale(stageW, stageH) * coverZoom * ratioMultiplier;
            const drawW = coverSourceImg.width * scale;
            const drawH = coverSourceImg.height * scale;

            eCtx.save();
            eCtx.translate(exportW / 2, exportH / 2);
            eCtx.translate(coverCropX * ratioMultiplier, coverCropY * ratioMultiplier);
            eCtx.rotate((coverRotation * Math.PI) / 180);
            eCtx.drawImage(coverSourceImg, -drawW / 2, -drawH / 2, drawW, drawH);
            eCtx.restore();

            const blob = await new Promise(resolve => exportCanvas.toBlob(resolve, 'image/jpeg', 0.90));
            if (blob) {
                const baseName = (coverOriginalFile?.name || 'cover').replace(/\.[^/.]+$/, '');
                const cleanName = `${baseName}_cover_16x9.jpg`;
                const file = new File([blob], cleanName, { type: 'image/jpeg' });

                const dt = new DataTransfer();
                dt.items.add(file);
                if (coverInput) coverInput.files = dt.files;

                if (coverImg) {
                    coverImg.src = URL.createObjectURL(blob);
                }
                if (coverWrapper) {
                    coverWrapper.classList.remove('hidden');
                }
                if (removeCoverCheckbox) {
                    removeCoverCheckbox.checked = false;
                }
            }

            if (applyBtn) applyBtn.disabled = false;
            if (applyBtnText) applyBtnText.textContent = 'Terapkan & Simpan Cover';

            closeCropCoverModal();
        }
        window.applyAndSaveCoverPhoto = applyAndSaveCoverPhoto;

        coverInput?.addEventListener('change', function() {
            const file = this.files?.[0];
            if (file) {
                handleCoverFileSelected(file);
            }
        });

        // ==========================================
        // Workplace Photos Interactive Crop & Multi-File System
        // ==========================================
        const workplaceInput = document.getElementById('workplace_photos');
        const workplaceWrapper = document.getElementById('workplace-previews-wrapper');
        const workplaceGrid = document.getElementById('workplace-previews-grid');
        const workplaceCountEl = document.getElementById('workplace-previews-count');

        let cropWorkplaceItems = [];
        let currentWorkplaceIndex = 0;
        let activeWorkplaceRatio = '4/5';
        let isWorkplaceDragging = false;
        let workplaceDragStartX = 0;
        let workplaceDragStartY = 0;
        let workplaceEventsBound = false;

        function getStageDims(ratioKey) {
            const isMobile = window.innerWidth < 640;
            if (ratioKey === '16/9') {
                return isMobile
                    ? { stageW: 288, stageH: 162, exportW: 1600, exportH: 900 }
                    : { stageW: 384, stageH: 216, exportW: 1600, exportH: 900 };
            }
            if (ratioKey === '1/1') {
                return isMobile
                    ? { stageW: 260, stageH: 260, exportW: 1080, exportH: 1080 }
                    : { stageW: 320, stageH: 320, exportW: 1080, exportH: 1080 };
            }
            // Default 4:5
            return isMobile
                ? { stageW: 260, stageH: 325, exportW: 1080, exportH: 1350 }
                : { stageW: 320, stageH: 400, exportW: 1080, exportH: 1350 };
        }

        function detectSmartRatio(items) {
            if (!items || items.length === 0) {
                return { ratio: '4/5', isUniform: true, label: 'Standar 4:5' };
            }

            function classify(r) {
                if (r >= 1.60 && r <= 1.95) return '16/9';
                if (r >= 0.72 && r <= 0.88) return '4/5';
                if (r >= 0.92 && r <= 1.08) return '1/1';
                return 'other';
            }

            if (items.length === 1) {
                const cat = classify(items[0].origRatio);
                if (cat === '16/9') return { ratio: '16/9', isUniform: true, label: 'Seragam 16:9' };
                if (cat === '1/1') return { ratio: '1/1', isUniform: true, label: 'Seragam 1:1' };
                if (cat === '4/5') return { ratio: '4/5', isUniform: true, label: 'Seragam 4:5' };
                return { ratio: '4/5', isUniform: false, label: 'Terkunci 4:5 (Rasio Asli Unik)' };
            }

            const firstCat = classify(items[0].origRatio);
            const allSameCategory = items.every(i => classify(i.origRatio) === firstCat && firstCat !== 'other');
            if (allSameCategory) {
                return {
                    ratio: firstCat,
                    isUniform: true,
                    label: `Seragam ${firstCat === '16/9' ? '16:9' : (firstCat === '1/1' ? '1:1' : '4:5')}`
                };
            }

            const ratios = items.map(i => i.origRatio);
            const minR = Math.min(...ratios);
            const maxR = Math.max(...ratios);
            if (maxR - minR < 0.10) {
                const avg = (minR + maxR) / 2;
                if (Math.abs(avg - (16 / 9)) < 0.15) return { ratio: '16/9', isUniform: true, label: 'Seragam 16:9' };
                if (Math.abs(avg - 1.0) < 0.12) return { ratio: '1/1', isUniform: true, label: 'Seragam 1:1' };
                if (Math.abs(avg - 0.8) < 0.10) return { ratio: '4/5', isUniform: true, label: 'Seragam 4:5' };
            }

            // Mixed / Diverse aspect ratios -> lock to 4:5 strictly
            return { ratio: '4/5', isUniform: false, label: 'Terkunci 4:5 (Rasio Foto Beragam)' };
        }

        async function handleWorkplaceFilesSelected(fileList) {
            const rawFiles = Array.from(fileList || []);
            if (rawFiles.length === 0) return;

            let files = rawFiles;
            if (files.length > 6) {
                if (window.showAppAlert) {
                    await window.showAppAlert({
                        title: 'Maksimal 6 Foto',
                        message: 'Maksimal 6 foto lingkungan kerja yang dapat diunggah. Hanya 6 foto pertama yang akan diproses.',
                        type: 'warning'
                    });
                }
                files = files.slice(0, 6);
            }

            // Validate format
            const validFiles = files.filter(f => ['image/jpeg', 'image/png', 'image/webp', 'image/jpg'].includes(f.type));
            if (validFiles.length !== files.length) {
                if (window.showAppAlert) {
                    await window.showAppAlert({
                        title: 'Format Berkas',
                        message: 'Beberapa berkas dilewati karena bukan gambar JPG, JPEG, PNG, atau WEBP.',
                        type: 'warning'
                    });
                }
            }
            if (validFiles.length === 0) return;

            // Load into image objects
            const loadPromises = validFiles.map(file => {
                return new Promise(resolve => {
                    const reader = new FileReader();
                    reader.onload = e => {
                        const img = new Image();
                        img.onload = () => {
                            const w = img.naturalWidth || img.width;
                            const h = img.naturalHeight || img.height;
                            resolve({
                                file,
                                img,
                                dataUrl: e.target.result,
                                name: file.name,
                                origRatio: w / h,
                                zoom: 1.0,
                                rotation: 0,
                                cropX: 0,
                                cropY: 0,
                                croppedBlob: null,
                                croppedUrl: null
                            });
                        };
                        img.src = e.target.result;
                    };
                    reader.readAsDataURL(file);
                });
            });

            cropWorkplaceItems = await Promise.all(loadPromises);

            // Detect smart ratio
            const detection = detectSmartRatio(cropWorkplaceItems);
            activeWorkplaceRatio = detection.ratio;

            const badgeText = document.getElementById('cropRatioIndicatorText');
            const badge = document.getElementById('cropRatioIndicatorBadge');
            if (badgeText) badgeText.textContent = detection.label;
            if (badge) {
                if (detection.isUniform) {
                    badge.className = 'inline-flex items-center gap-1 rounded-full px-2.5 py-0.5 text-[11px] font-bold bg-brand-100 text-brand-800 dark:bg-brand-950 dark:text-brand-300';
                } else {
                    badge.className = 'inline-flex items-center gap-1 rounded-full px-2.5 py-0.5 text-[11px] font-bold bg-amber-100 text-amber-800 dark:bg-amber-950 dark:text-amber-300';
                }
            }

            currentWorkplaceIndex = 0;
            openCropWorkplaceModal();
        }

        function openCropWorkplaceModal(startIndex = 0) {
            if (cropWorkplaceItems.length === 0) return;
            const modal = document.getElementById('cropWorkplaceModal');
            if (!modal) return;

            // Teleport modal directly to document.body so it is never trapped inside any container or transform
            if (modal.parentElement !== document.body) {
                document.body.appendChild(modal);
            }

            currentWorkplaceIndex = Math.max(0, Math.min(cropWorkplaceItems.length - 1, startIndex));
            updateRatioButtonStyles();
            updateStepperUI();
            updateActivePhotoUI();

            modal.classList.remove('hidden');
            modal.classList.add('flex');
            document.body.style.overflow = 'hidden';

            initWorkplaceCanvasEvents();
            renderWorkplaceCrop();
        }
        window.openCropWorkplaceModal = openCropWorkplaceModal;

        function closeCropWorkplaceModal() {
            const modal = document.getElementById('cropWorkplaceModal');
            if (modal) {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
            }
            document.body.style.overflow = '';
            // If nothing applied yet and no existing items, clear input
            if (!cropWorkplaceItems.some(i => i.croppedBlob)) {
                if (workplaceInput) workplaceInput.value = '';
            }
        }
        window.closeCropWorkplaceModal = closeCropWorkplaceModal;

        function setWorkplaceRatio(ratioKey) {
            activeWorkplaceRatio = ratioKey;
            updateRatioButtonStyles();

            // Reset offsets for current item to center properly
            const item = cropWorkplaceItems[currentWorkplaceIndex];
            if (item) {
                item.cropX = 0;
                item.cropY = 0;
            }

            renderWorkplaceCrop();
        }
        window.setWorkplaceRatio = setWorkplaceRatio;

        function updateRatioButtonStyles() {
            document.querySelectorAll('[data-ratio-btn]').forEach(btn => {
                const isSelected = btn.dataset.ratioBtn === activeWorkplaceRatio;
                if (isSelected) {
                    btn.className = 'rounded-lg px-2.5 py-1 text-xs font-bold transition bg-white text-slate-900 shadow-sm dark:bg-slate-700 dark:text-white';
                } else {
                    btn.className = 'rounded-lg px-2.5 py-1 text-xs font-bold transition text-slate-600 hover:text-slate-900 dark:text-slate-400 dark:hover:text-white';
                }
            });
        }

        function updateStepperUI() {
            const bar = document.getElementById('cropPhotoStepperBar');
            const pillsContainer = document.getElementById('cropPhotoStepperPills');
            const prevBtn = document.getElementById('prevCropPhotoBtn');
            const nextBtn = document.getElementById('nextCropPhotoBtn');

            if (!bar || !pillsContainer) return;

            if (cropWorkplaceItems.length <= 1) {
                bar.classList.add('hidden');
                bar.classList.remove('flex');
                return;
            }

            bar.classList.remove('hidden');
            bar.classList.add('flex');

            if (prevBtn) prevBtn.disabled = currentWorkplaceIndex === 0;
            if (nextBtn) nextBtn.disabled = currentWorkplaceIndex === cropWorkplaceItems.length - 1;

            pillsContainer.innerHTML = '';
            cropWorkplaceItems.forEach((item, idx) => {
                const pill = document.createElement('button');
                pill.type = 'button';
                const isActive = idx === currentWorkplaceIndex;
                pill.className = `flex items-center gap-1 px-2.5 py-1 rounded-xl text-xs font-bold transition cursor-pointer shrink-0 ${
                    isActive
                        ? 'bg-brand-600 text-white shadow-xs'
                        : 'bg-slate-100 text-slate-600 hover:bg-slate-200 dark:bg-slate-800 dark:text-slate-300 dark:hover:bg-slate-700'
                }`;
                pill.innerHTML = `<span>Foto ${idx + 1}</span>`;
                pill.onclick = () => {
                    currentWorkplaceIndex = idx;
                    updateStepperUI();
                    updateActivePhotoUI();
                    renderWorkplaceCrop();
                };
                pillsContainer.appendChild(pill);
            });
        }

        function updateActivePhotoUI() {
            const item = cropWorkplaceItems[currentWorkplaceIndex];
            if (!item) return;

            const subtitle = document.getElementById('cropWorkplaceSubtitle');
            if (subtitle) {
                subtitle.textContent = `Foto ${currentWorkplaceIndex + 1} dari ${cropWorkplaceItems.length}: ${item.name}`;
            }

            const zoomRange = document.getElementById('workplaceCropZoomRange');
            const zoomText = document.getElementById('cropZoomValueText');
            if (zoomRange) zoomRange.value = item.zoom.toFixed(2);
            if (zoomText) zoomText.textContent = `${item.zoom.toFixed(2)}x`;

            const applyBtnText = document.getElementById('applyWorkplaceCropBtnText');
            if (applyBtnText) {
                applyBtnText.textContent = `Terapkan & Simpan (${cropWorkplaceItems.length}) Foto`;
            }
        }

        function navigateCropPhoto(delta) {
            const newIndex = currentWorkplaceIndex + delta;
            if (newIndex >= 0 && newIndex < cropWorkplaceItems.length) {
                currentWorkplaceIndex = newIndex;
                updateStepperUI();
                updateActivePhotoUI();
                renderWorkplaceCrop();
            }
        }
        window.navigateCropPhoto = navigateCropPhoto;

        function getBaseFitScale(item, stageW, stageH) {
            if (!item || !item.img) return 1;
            const isRotated = (item.rotation % 180 !== 0);
            const imgW = isRotated ? item.img.height : item.img.width;
            const imgH = isRotated ? item.img.width : item.img.height;
            return Math.max(stageW / imgW, stageH / imgH);
        }

        function clampOffsets(item, stageW, stageH) {
            if (!item || !item.img) return;
            const isRotated = (item.rotation % 180 !== 0);
            const imgW = isRotated ? item.img.height : item.img.width;
            const imgH = isRotated ? item.img.width : item.img.height;

            const currentW = imgW * getBaseFitScale(item, stageW, stageH) * item.zoom;
            const currentH = imgH * getBaseFitScale(item, stageW, stageH) * item.zoom;

            const maxOffsetX = Math.max(0, (currentW - stageW) / 2);
            const maxOffsetY = Math.max(0, (currentH - stageH) / 2);

            item.cropX = Math.max(-maxOffsetX, Math.min(maxOffsetX, item.cropX));
            item.cropY = Math.max(-maxOffsetY, Math.min(maxOffsetY, item.cropY));
        }

        function renderWorkplaceCrop() {
            const item = cropWorkplaceItems[currentWorkplaceIndex];
            if (!item || !item.img) return;

            const { stageW, stageH } = getStageDims(activeWorkplaceRatio);
            const container = document.getElementById('workplaceCropStageContainer');
            const canvas = document.getElementById('workplaceCropCanvas');
            if (!container || !canvas) return;

            container.style.width = `${stageW}px`;
            container.style.height = `${stageH}px`;
            canvas.width = stageW;
            canvas.height = stageH;

            const ctx = canvas.getContext('2d');
            ctx.clearRect(0, 0, stageW, stageH);

            clampOffsets(item, stageW, stageH);

            const scale = getBaseFitScale(item, stageW, stageH) * item.zoom;
            const drawW = item.img.width * scale;
            const drawH = item.img.height * scale;

            ctx.save();
            ctx.translate(stageW / 2, stageH / 2);
            ctx.translate(item.cropX, item.cropY);
            ctx.rotate((item.rotation * Math.PI) / 180);
            ctx.drawImage(item.img, -drawW / 2, -drawH / 2, drawW, drawH);
            ctx.restore();
        }

        function setWorkplaceCropZoom(val) {
            const item = cropWorkplaceItems[currentWorkplaceIndex];
            if (!item) return;
            item.zoom = parseFloat(val);
            const zoomText = document.getElementById('cropZoomValueText');
            if (zoomText) zoomText.textContent = `${item.zoom.toFixed(2)}x`;
            renderWorkplaceCrop();
        }
        window.setWorkplaceCropZoom = setWorkplaceCropZoom;

        function adjustWorkplaceZoom(delta) {
            const item = cropWorkplaceItems[currentWorkplaceIndex];
            if (!item) return;
            item.zoom = Math.max(1.0, Math.min(3.0, item.zoom + delta));
            const zoomRange = document.getElementById('workplaceCropZoomRange');
            if (zoomRange) zoomRange.value = item.zoom.toFixed(2);
            const zoomText = document.getElementById('cropZoomValueText');
            if (zoomText) zoomText.textContent = `${item.zoom.toFixed(2)}x`;
            renderWorkplaceCrop();
        }
        window.adjustWorkplaceZoom = adjustWorkplaceZoom;

        function rotateWorkplacePhoto() {
            const item = cropWorkplaceItems[currentWorkplaceIndex];
            if (!item) return;
            item.rotation = (item.rotation + 90) % 360;
            item.cropX = 0;
            item.cropY = 0;
            renderWorkplaceCrop();
        }
        window.rotateWorkplacePhoto = rotateWorkplacePhoto;

        function resetWorkplaceTransform() {
            const item = cropWorkplaceItems[currentWorkplaceIndex];
            if (!item) return;
            item.zoom = 1.0;
            item.rotation = 0;
            item.cropX = 0;
            item.cropY = 0;
            updateActivePhotoUI();
            renderWorkplaceCrop();
        }
        window.resetWorkplaceTransform = resetWorkplaceTransform;

        function initWorkplaceCanvasEvents() {
            if (workplaceEventsBound) return;
            workplaceEventsBound = true;

            const container = document.getElementById('workplaceCropStageContainer');
            if (!container) return;

            container.addEventListener('pointerdown', e => {
                const item = cropWorkplaceItems[currentWorkplaceIndex];
                if (!item) return;
                isWorkplaceDragging = true;
                workplaceDragStartX = e.clientX - item.cropX;
                workplaceDragStartY = e.clientY - item.cropY;
                container.setPointerCapture(e.pointerId);
            });

            container.addEventListener('pointermove', e => {
                if (!isWorkplaceDragging) return;
                const item = cropWorkplaceItems[currentWorkplaceIndex];
                if (!item) return;
                item.cropX = e.clientX - workplaceDragStartX;
                item.cropY = e.clientY - workplaceDragStartY;
                renderWorkplaceCrop();
            });

            const stopDrag = e => {
                if (isWorkplaceDragging) {
                    isWorkplaceDragging = false;
                    try { container.releasePointerCapture(e.pointerId); } catch (_) {}
                }
            };

            container.addEventListener('pointerup', stopDrag);
            container.addEventListener('pointercancel', stopDrag);

            container.addEventListener('wheel', e => {
                e.preventDefault();
                const delta = e.deltaY < 0 ? 0.08 : -0.08;
                adjustWorkplaceZoom(delta);
            }, { passive: false });
        }

        async function applyAndSaveWorkplacePhotos() {
            if (cropWorkplaceItems.length === 0) return;

            const applyBtn = document.getElementById('applyWorkplaceCropBtn');
            const applyBtnText = document.getElementById('applyWorkplaceCropBtnText');
            if (applyBtn) applyBtn.disabled = true;
            if (applyBtnText) applyBtnText.textContent = 'Memproses foto...';

            const { stageW, stageH, exportW, exportH } = getStageDims(activeWorkplaceRatio);
            const ratioMultiplier = exportW / stageW;

            const processedFiles = [];

            for (let i = 0; i < cropWorkplaceItems.length; i++) {
                const item = cropWorkplaceItems[i];
                const exportCanvas = document.createElement('canvas');
                exportCanvas.width = exportW;
                exportCanvas.height = exportH;
                const eCtx = exportCanvas.getContext('2d');
                eCtx.imageSmoothingEnabled = true;
                eCtx.imageSmoothingQuality = 'high';

                clampOffsets(item, stageW, stageH);

                const scale = getBaseFitScale(item, stageW, stageH) * item.zoom * ratioMultiplier;
                const drawW = item.img.width * scale;
                const drawH = item.img.height * scale;

                eCtx.save();
                eCtx.translate(exportW / 2, exportH / 2);
                eCtx.translate(item.cropX * ratioMultiplier, item.cropY * ratioMultiplier);
                eCtx.rotate((item.rotation * Math.PI) / 180);
                eCtx.drawImage(item.img, -drawW / 2, -drawH / 2, drawW, drawH);
                eCtx.restore();

                const blob = await new Promise(resolve => exportCanvas.toBlob(resolve, 'image/jpeg', 0.90));
                if (blob) {
                    item.croppedBlob = blob;
                    if (item.croppedUrl) URL.revokeObjectURL(item.croppedUrl);
                    item.croppedUrl = URL.createObjectURL(blob);

                    const cleanName = (item.name.replace(/\.[^/.]+$/, '') || `workplace_photo_${i + 1}`) + '.jpg';
                    const file = new File([blob], cleanName, { type: 'image/jpeg' });
                    processedFiles.push(file);
                }
            }

            // Sync with DataTransfer to workplaceInput
            const dt = new DataTransfer();
            processedFiles.forEach(f => dt.items.add(f));
            if (workplaceInput) {
                workplaceInput.files = dt.files;
            }

            renderWorkplacePreviewsGrid();

            if (applyBtn) applyBtn.disabled = false;
            if (applyBtnText) applyBtnText.textContent = 'Terapkan & Simpan Foto';

            closeCropWorkplaceModal();
        }
        window.applyAndSaveWorkplacePhotos = applyAndSaveWorkplacePhotos;

        function renderWorkplacePreviewsGrid() {
            if (!workplaceGrid || !workplaceWrapper) return;
            workplaceGrid.innerHTML = '';

            const validItems = cropWorkplaceItems.filter(i => i.croppedBlob);
            if (validItems.length === 0) {
                workplaceWrapper.classList.add('hidden');
                if (workplaceCountEl) workplaceCountEl.textContent = '0';
                return;
            }

            workplaceWrapper.classList.remove('hidden');
            if (workplaceCountEl) workplaceCountEl.textContent = validItems.length;

            const aspectClass = activeWorkplaceRatio === '16/9'
                ? 'aspect-video'
                : (activeWorkplaceRatio === '1/1' ? 'aspect-square' : 'aspect-[4/5]');

            cropWorkplaceItems.forEach((item, index) => {
                if (!item.croppedBlob) return;

                const card = document.createElement('div');
                card.className = 'group relative overflow-hidden rounded-2xl border border-slate-200 bg-slate-50 dark:border-slate-800 dark:bg-slate-900 p-2 shadow-xs transition hover:border-brand-400';
                card.innerHTML = `
                    <div class="relative ${aspectClass} overflow-hidden rounded-xl bg-slate-950">
                        <img src="${item.croppedUrl}" alt="Foto ${index + 1}" class="h-full w-full object-cover">
                        <span class="absolute top-1.5 left-1.5 rounded-lg bg-black/60 backdrop-blur-md px-1.5 py-0.5 text-[10px] font-bold text-white">#${index + 1}</span>
                    </div>
                    <p class="mt-1.5 truncate text-[11px] font-semibold text-slate-700 dark:text-slate-300" title="${item.name}">${item.name}</p>
                    <div class="mt-2 flex items-center justify-between gap-1 border-t border-slate-100 pt-1.5 dark:border-slate-800">
                        <button type="button" class="inline-flex items-center gap-0.5 text-[11px] font-semibold text-brand-600 hover:text-brand-700 dark:text-brand-400 cursor-pointer" onclick="openCropWorkplaceModal(${index})">
                            <span class="material-symbols-outlined text-[14px]">tune</span>
                            <span>Sesuaikan</span>
                        </button>
                        <button type="button" class="inline-flex items-center gap-0.5 text-[11px] font-semibold text-rose-600 hover:text-rose-700 dark:text-rose-400 cursor-pointer" onclick="removeWorkplacePhotoItem(${index})">
                            <span class="material-symbols-outlined text-[14px]">delete</span>
                            <span>Hapus</span>
                        </button>
                    </div>
                `;
                workplaceGrid.appendChild(card);
            });
        }

        function removeWorkplacePhotoItem(index) {
            if (index < 0 || index >= cropWorkplaceItems.length) return;
            const removed = cropWorkplaceItems.splice(index, 1)[0];
            if (removed && removed.croppedUrl) {
                URL.revokeObjectURL(removed.croppedUrl);
            }

            // Rebuild DataTransfer
            const dt = new DataTransfer();
            cropWorkplaceItems.forEach((item, idx) => {
                if (item.croppedBlob) {
                    const cleanName = (item.name.replace(/\.[^/.]+$/, '') || `workplace_photo_${idx + 1}`) + '.jpg';
                    const file = new File([item.croppedBlob], cleanName, { type: 'image/jpeg' });
                    dt.items.add(file);
                }
            });

            if (workplaceInput) {
                workplaceInput.files = dt.files;
            }

            renderWorkplacePreviewsGrid();
        }
        window.removeWorkplacePhotoItem = removeWorkplacePhotoItem;

        workplaceInput?.addEventListener('change', function(e) {
            handleWorkplaceFilesSelected(this.files);
        });
    });
</script>
@endpush
