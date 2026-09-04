<!-- Global Applicant Profile Detail Modal for Employer / Mitra UMKM -->
<div id="applicantProfileModal" class="fixed inset-0 z-[160] hidden bg-slate-950/60 backdrop-blur-xs p-3 sm:p-4 overflow-y-auto flex items-center justify-center" role="dialog" aria-modal="true" style="margin: 0;">
    <div class="fixed inset-0 -z-10" onclick="closeApplicantProfileModal()"></div>

    <div class="relative w-full max-w-lg rounded-3xl bg-white p-5 sm:p-7 shadow-2xl dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 animate-modal-pop overflow-hidden max-h-[92dvh] flex flex-col my-auto">
        {{-- Header --}}
        <div class="flex items-start justify-between gap-3 border-b border-slate-100 pb-4 dark:border-slate-800 min-w-0 shrink-0">
            <div class="flex items-center gap-3 sm:gap-4 min-w-0 flex-1">
                <div id="applicantModalAvatarWrapper" class="relative h-13 w-13 sm:h-14 sm:w-14 rounded-2xl overflow-hidden bg-brand-700 text-white font-bold text-base sm:text-lg flex items-center justify-center shadow-xs shrink-0 ring-2 ring-brand-500/20">
                    <img id="applicantModalAvatarImg" src="" alt="" class="h-full w-full object-cover hidden">
                    <span id="applicantModalAvatarInitials">--</span>
                </div>
                <div class="min-w-0 flex-1">
                    <div class="flex items-center gap-2 min-w-0 flex-wrap">
                        <h3 id="applicantModalName" class="text-base sm:text-lg font-bold text-slate-950 dark:text-white truncate">
                            Nama Pelamar
                        </h3>
                        <span class="inline-flex items-center gap-1 rounded-full bg-teal-50 dark:bg-teal-950/60 text-teal-700 dark:text-teal-300 border border-teal-200 dark:border-teal-800/80 px-2 py-0.5 text-[10px] font-bold shrink-0">
                            <span class="material-symbols-outlined text-[13px]">verified</span>
                            <span>Pencari Kerja</span>
                        </span>
                    </div>
                    <p id="applicantModalUsername" class="text-xs font-mono font-medium text-slate-500 dark:text-slate-400 mt-0.5 truncate">
                        @username
                    </p>
                </div>
            </div>
            <button type="button" onclick="closeApplicantProfileModal()" class="rounded-xl p-1.5 text-slate-400 hover:bg-slate-100 hover:text-slate-600 dark:hover:bg-slate-800 dark:hover:text-slate-300 transition cursor-pointer shrink-0" aria-label="Tutup detail profil">
                <span class="material-symbols-outlined text-[20px]">close</span>
            </button>
        </div>

        {{-- Body Content --}}
        <div class="py-4 space-y-4 overflow-y-auto flex-1 min-w-0 pr-1">
            
            {{-- Section 1: Detail Registrasi & Akun Platform --}}
            <div class="rounded-2xl bg-slate-50 dark:bg-slate-850 p-4 border border-slate-100 dark:border-slate-800">
                <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-2.5 flex items-center gap-1.5">
                    <span class="material-symbols-outlined text-[15px] text-brand-600 dark:text-brand-400">badge</span>
                    <span>Informasi Akun Platform</span>
                </p>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs">
                    <div class="flex items-start gap-2.5">
                        <div class="w-8 h-8 rounded-xl bg-brand-50 dark:bg-brand-950 text-brand-700 dark:text-brand-300 flex items-center justify-center shrink-0 mt-0.5">
                            <span class="material-symbols-outlined text-[18px]">calendar_month</span>
                        </div>
                        <div class="min-w-0">
                            <span class="text-slate-400 block text-[11px]">Terdaftar Sejak</span>
                            <span id="applicantModalRegisteredAt" class="font-semibold text-slate-900 dark:text-slate-100 leading-snug">
                                -
                            </span>
                        </div>
                    </div>
                    <div class="flex items-start gap-2.5">
                        <div class="w-8 h-8 rounded-xl bg-emerald-50 dark:bg-emerald-950 text-emerald-700 dark:text-emerald-300 flex items-center justify-center shrink-0 mt-0.5">
                            <span class="material-symbols-outlined text-[18px]">verified_user</span>
                        </div>
                        <div class="min-w-0">
                            <span class="text-slate-400 block text-[11px]">Status Akun</span>
                            <span class="font-semibold text-emerald-700 dark:text-emerald-400 leading-snug">
                                Aktif & Terverifikasi
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Section 2: Kontak Pelamar --}}
            <div class="rounded-2xl bg-slate-50 dark:bg-slate-850 p-4 border border-slate-100 dark:border-slate-800">
                <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-2.5 flex items-center gap-1.5">
                    <span class="material-symbols-outlined text-[15px] text-brand-600 dark:text-brand-400">contact_phone</span>
                    <span>Kontak Pelamar</span>
                </p>
                <div class="space-y-2.5 text-xs">
                    {{-- Email --}}
                    <div class="flex items-center justify-between gap-2">
                        <div class="flex items-center gap-2.5 min-w-0">
                            <span class="material-symbols-outlined text-[18px] text-slate-400 shrink-0">mail</span>
                            <span id="applicantModalEmail" class="text-slate-800 dark:text-slate-200 font-medium truncate">
                                -
                            </span>
                        </div>
                        <a id="applicantModalMailLink" href="#" class="text-brand-600 hover:text-brand-700 dark:text-brand-400 font-semibold text-[11px] shrink-0 inline-flex items-center gap-0.5 hover:underline">
                            <span>Kirim Email</span>
                            <span class="material-symbols-outlined text-[13px]">arrow_forward</span>
                        </a>
                    </div>
                    {{-- Phone / WA --}}
                    <div class="flex items-center justify-between gap-2 pt-1 border-t border-slate-200/60 dark:border-slate-800">
                        <div class="flex items-center gap-2.5 min-w-0">
                            <span class="material-symbols-outlined text-[18px] text-slate-400 shrink-0">call</span>
                            <span id="applicantModalPhone" class="text-slate-800 dark:text-slate-200 font-medium truncate">
                                -
                            </span>
                        </div>
                        <a id="applicantModalWaLink" href="#" target="_blank" rel="noopener noreferrer" class="hidden text-emerald-600 hover:text-emerald-700 dark:text-emerald-400 font-semibold text-[11px] shrink-0 items-center gap-0.5 hover:underline">
                            <span>Hubungi WA</span>
                            <span class="material-symbols-outlined text-[13px]">open_in_new</span>
                        </a>
                    </div>
                </div>
            </div>

            {{-- Section 3: Detail Lamaran / Pekerjaan (Kondisional jika relevan) --}}
            <div id="applicantModalJobSection" class="hidden rounded-2xl bg-slate-50 dark:bg-slate-850 p-4 border border-slate-100 dark:border-slate-800">
                <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-2.5 flex items-center gap-1.5">
                    <span class="material-symbols-outlined text-[15px] text-brand-600 dark:text-brand-400">work</span>
                    <span>Rincian Rekrutmen & Pekerjaan</span>
                </p>
                <div class="space-y-2 text-xs">
                    <div class="flex items-center justify-between gap-2">
                        <span class="text-slate-400 text-[11px]">Posisi:</span>
                        <span id="applicantModalJobTitle" class="font-bold text-slate-900 dark:text-slate-100 text-right truncate">
                            -
                        </span>
                    </div>
                    <div class="flex items-center justify-between gap-2">
                        <span class="text-slate-400 text-[11px]">Status Seleksi:</span>
                        <span id="applicantModalStatusBadge" class="inline-flex items-center gap-1 rounded-full px-2 py-0.5 text-[11px] font-bold border">
                            -
                        </span>
                    </div>
                    <div id="applicantModalAppliedAtRow" class="flex items-center justify-between gap-2">
                        <span class="text-slate-400 text-[11px]">Tanggal Melamar:</span>
                        <span id="applicantModalAppliedAt" class="font-medium text-slate-700 dark:text-slate-300 text-right">
                            -
                        </span>
                    </div>
                    <div id="applicantModalResumeRow" class="hidden pt-2 border-t border-slate-200/60 dark:border-slate-800 flex items-center justify-between gap-2">
                        <span class="text-slate-400 text-[11px]">Resume:</span>
                        <div class="flex items-center gap-2">
                            <button type="button" id="applicantModalResumePreviewBtn" class="text-brand-600 hover:text-brand-700 dark:text-brand-400 font-semibold text-[11px] inline-flex items-center gap-1 hover:underline cursor-pointer">
                                <span class="material-symbols-outlined text-[14px]">visibility</span>
                                <span>Lihat Resume</span>
                            </button>
                            <a id="applicantModalResumeDownloadLink" href="#" class="text-slate-500 hover:text-slate-700 dark:text-slate-400 font-semibold text-[11px] inline-flex items-center gap-1 hover:underline">
                                <span class="material-symbols-outlined text-[14px]">download</span>
                                <span>Unduh</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        {{-- Footer Buttons --}}
        <div class="flex items-center justify-end gap-2.5 border-t border-slate-100 pt-4 dark:border-slate-800 shrink-0">
            <button type="button" onclick="closeApplicantProfileModal()" class="portal-button-secondary py-2 px-4 text-xs cursor-pointer">
                Tutup
            </button>
            <a id="applicantModalChatBtn" href="#" class="portal-button-primary !bg-teal-700 hover:!bg-teal-800 py-2 px-4 text-xs inline-flex items-center gap-1.5 cursor-pointer">
                <span class="material-symbols-outlined text-[16px]">chat</span>
                <span>Kirim Pesan</span>
            </a>
        </div>
    </div>
</div>

<script>
    window.openApplicantProfileModal = function(data) {
        if (!data) return;

        const modal = document.getElementById('applicantProfileModal');
        if (!modal) return;

        // 1. Avatar & Initials
        const avatarImg = document.getElementById('applicantModalAvatarImg');
        const avatarInitials = document.getElementById('applicantModalAvatarInitials');
        if (data.avatar_url) {
            avatarImg.src = data.avatar_url;
            avatarImg.alt = data.name || 'Pelamar';
            avatarImg.classList.remove('hidden');
            avatarInitials.classList.add('hidden');
        } else {
            avatarImg.classList.add('hidden');
            avatarImg.src = '';
            avatarInitials.textContent = data.initials || (data.name ? data.name.substring(0, 2).toUpperCase() : 'PK');
            avatarInitials.classList.remove('hidden');
        }

        // 2. Name & Username
        const nameEl = document.getElementById('applicantModalName');
        const usernameEl = document.getElementById('applicantModalUsername');
        if (nameEl) nameEl.textContent = data.name || 'Pencari Kerja';
        if (usernameEl) {
            const rawUsername = data.username ? (data.username.startsWith('@') ? data.username : '@' + data.username) : '@-';
            usernameEl.textContent = rawUsername;
        }

        // 3. Registered At (Terdaftar Sejak)
        const registeredAtEl = document.getElementById('applicantModalRegisteredAt');
        if (registeredAtEl) {
            registeredAtEl.textContent = data.registered_at || data.member_since || 'Terdaftar di platform';
        }

        // 4. Contact: Email & Phone
        const emailEl = document.getElementById('applicantModalEmail');
        const mailLink = document.getElementById('applicantModalMailLink');
        if (emailEl) emailEl.textContent = data.email || '-';
        if (mailLink) {
            if (data.email) {
                mailLink.href = 'mailto:' + data.email;
                mailLink.classList.remove('hidden');
            } else {
                mailLink.classList.add('hidden');
            }
        }

        const phoneEl = document.getElementById('applicantModalPhone');
        const waLink = document.getElementById('applicantModalWaLink');
        if (phoneEl) phoneEl.textContent = data.phone && data.phone !== '-' ? data.phone : 'Belum ditambahkan';
        if (waLink) {
            if (data.phone && data.phone !== '-') {
                let cleanPhone = data.phone.replace(/\D/g, '');
                if (cleanPhone.startsWith('0')) {
                    cleanPhone = '62' + cleanPhone.substring(1);
                }
                waLink.href = 'https://wa.me/' + cleanPhone;
                waLink.classList.remove('hidden');
                waLink.classList.add('inline-flex');
            } else {
                waLink.classList.add('hidden');
                waLink.classList.remove('inline-flex');
            }
        }

        // 5. Job & Application Section (optional)
        const jobSection = document.getElementById('applicantModalJobSection');
        const jobTitleEl = document.getElementById('applicantModalJobTitle');
        const statusBadge = document.getElementById('applicantModalStatusBadge');
        const appliedAtRow = document.getElementById('applicantModalAppliedAtRow');
        const appliedAtEl = document.getElementById('applicantModalAppliedAt');
        const resumeRow = document.getElementById('applicantModalResumeRow');
        const resumePreviewBtn = document.getElementById('applicantModalResumePreviewBtn');
        const resumeDownloadLink = document.getElementById('applicantModalResumeDownloadLink');

        if (data.job_title) {
            if (jobSection) jobSection.classList.remove('hidden');
            if (jobTitleEl) jobTitleEl.textContent = data.job_title;

            if (statusBadge) {
                const status = data.status || 'pending';
                let badgeLabel = data.status_label || 'Menunggu Tinjauan';
                let badgeClass = 'bg-amber-50 text-amber-800 border-amber-200 dark:bg-amber-950/60 dark:text-amber-300 dark:border-amber-800';
                let badgeIcon = 'hourglass_empty';

                if (status === 'accepted') {
                    badgeClass = 'bg-emerald-50 text-emerald-800 border-emerald-200 dark:bg-emerald-950/60 dark:text-emerald-300 dark:border-emerald-800';
                    badgeIcon = 'how_to_reg';
                } else if (status === 'interview') {
                    badgeClass = 'bg-indigo-50 text-indigo-800 border-indigo-200 dark:bg-indigo-950/60 dark:text-indigo-300 dark:border-indigo-800';
                    badgeIcon = 'event';
                } else if (status === 'rejected') {
                    badgeClass = 'bg-rose-50 text-rose-800 border-rose-200 dark:bg-rose-950/60 dark:text-rose-300 dark:border-rose-800';
                    badgeIcon = 'cancel';
                } else if (status === 'resigned') {
                    badgeClass = 'bg-slate-100 text-slate-800 border-slate-300 dark:bg-slate-800 dark:text-slate-300 dark:border-slate-700';
                    badgeIcon = 'person_cancel';
                }

                statusBadge.className = `inline-flex items-center gap-1 rounded-full px-2.5 py-0.5 text-[11px] font-bold border ${badgeClass}`;
                statusBadge.innerHTML = `<span class="material-symbols-outlined text-[13px]">${badgeIcon}</span><span>${badgeLabel}</span>`;
            }

            if (data.applied_at && appliedAtRow && appliedAtEl) {
                appliedAtEl.textContent = data.applied_at;
                appliedAtRow.classList.remove('hidden');
            } else if (appliedAtRow) {
                appliedAtRow.classList.add('hidden');
            }

            if (data.resume_preview_url || data.resume_url) {
                if (resumeRow) resumeRow.classList.remove('hidden');
                if (resumePreviewBtn && data.resume_preview_url) {
                    resumePreviewBtn.onclick = function() {
                        closeApplicantProfileModal();
                        if (typeof window.openPdfViewer === 'function') {
                            window.openPdfViewer(data.resume_preview_url, data.name, data.resume_url);
                        }
                    };
                    resumePreviewBtn.classList.remove('hidden');
                } else if (resumePreviewBtn) {
                    resumePreviewBtn.classList.add('hidden');
                }
                if (resumeDownloadLink && data.resume_url) {
                    resumeDownloadLink.href = data.resume_url;
                    resumeDownloadLink.classList.remove('hidden');
                } else if (resumeDownloadLink) {
                    resumeDownloadLink.classList.add('hidden');
                }
            } else if (resumeRow) {
                resumeRow.classList.add('hidden');
            }
        } else {
            if (jobSection) jobSection.classList.add('hidden');
        }

        // 6. Chat Action Button
        const chatBtn = document.getElementById('applicantModalChatBtn');
        if (chatBtn) {
            if (data.chat_url) {
                chatBtn.href = data.chat_url;
                chatBtn.classList.remove('hidden');
            } else {
                chatBtn.classList.add('hidden');
            }
        }

        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    };

    window.closeApplicantProfileModal = function() {
        const modal = document.getElementById('applicantProfileModal');
        if (modal) {
            modal.classList.add('hidden');
            document.body.style.overflow = '';
        }
    };

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            const modal = document.getElementById('applicantProfileModal');
            if (modal && !modal.classList.contains('hidden')) {
                closeApplicantProfileModal();
            }
        }
    });
</script>
