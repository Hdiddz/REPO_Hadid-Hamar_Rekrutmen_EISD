<!-- Global In-App Confirmation & Alert Modal Component -->
<div id="appConfirmModal" class="fixed inset-0 z-[150] hidden bg-slate-950/60 backdrop-blur-xs p-4 overflow-y-auto flex items-center justify-center">
    <div class="relative w-full max-w-md rounded-3xl bg-white p-6 sm:p-7 shadow-2xl dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 animate-modal-pop">
        <div class="flex items-start gap-4">
            <div id="appConfirmIconBg" class="w-12 h-12 rounded-2xl bg-rose-50 text-rose-600 dark:bg-rose-950/50 dark:text-rose-400 border border-rose-200 dark:border-rose-900/50 flex items-center justify-center shrink-0">
                <span id="appConfirmIcon" class="material-symbols-outlined text-[24px]">delete</span>
            </div>
            <div class="flex-1 min-w-0">
                <h3 id="appConfirmTitle" class="text-base font-bold text-slate-900 dark:text-white leading-snug">
                    Konfirmasi Aksi
                </h3>
                <div id="appConfirmMessage" class="mt-1.5 text-xs text-slate-500 dark:text-slate-400 leading-relaxed whitespace-pre-line">
                    Apakah Anda yakin ingin melanjutkan tindakan ini?
                </div>
            </div>
        </div>
        <div class="mt-6 flex items-center justify-end gap-2.5">
            <button type="button" id="appConfirmCancelBtn" class="inline-flex h-9.5 items-center justify-center rounded-xl border border-slate-200 bg-white px-4 text-xs font-semibold text-slate-700 shadow-xs hover:bg-slate-50 hover:border-slate-300 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 transition cursor-pointer">
                Batal
            </button>
            <button type="button" id="appConfirmActionBtn" class="inline-flex h-9.5 items-center justify-center gap-1.5 rounded-xl bg-rose-600 px-4.5 text-xs font-semibold text-white shadow-sm shadow-rose-600/25 hover:bg-rose-700 active:translate-y-px transition cursor-pointer">
                <span id="appConfirmActionIcon" class="material-symbols-outlined text-[17px]">delete</span>
                <span id="appConfirmActionText">Lanjutkan</span>
            </button>
        </div>
    </div>
</div>

<script>
    window.showAppConfirm = function({
        title = 'Konfirmasi',
        message = 'Apakah Anda yakin ingin melanjutkan tindakan ini?',
        confirmText = 'Ya, Lanjutkan',
        cancelText = 'Batal',
        type = 'danger',
        icon = 'delete',
        allowHtml = false
    } = {}) {
        return new Promise((resolve) => {
            const modal = document.getElementById('appConfirmModal');
            if (!modal) {
                resolve(true);
                return;
            }

            const titleEl = document.getElementById('appConfirmTitle');
            const msgEl = document.getElementById('appConfirmMessage');
            const iconEl = document.getElementById('appConfirmIcon');
            const iconBg = document.getElementById('appConfirmIconBg');
            const actionBtn = document.getElementById('appConfirmActionBtn');
            const actionIcon = document.getElementById('appConfirmActionIcon');
            const actionText = document.getElementById('appConfirmActionText');
            const cancelBtn = document.getElementById('appConfirmCancelBtn');

            if (titleEl) titleEl.textContent = title;
            if (msgEl) {
                if (allowHtml) {
                    msgEl.innerHTML = message;
                } else {
                    msgEl.textContent = message;
                }
            }
            if (iconEl) iconEl.textContent = icon;
            if (actionText) actionText.textContent = confirmText;
            if (cancelBtn) {
                cancelBtn.textContent = cancelText;
                cancelBtn.style.display = '';
            }

            if (actionBtn && iconBg) {
                if (type === 'danger') {
                    actionBtn.className = 'inline-flex h-9.5 items-center justify-center gap-1.5 rounded-xl bg-rose-600 px-4.5 text-xs font-semibold text-white shadow-sm shadow-rose-600/25 hover:bg-rose-700 active:translate-y-px transition cursor-pointer';
                    iconBg.className = 'w-12 h-12 rounded-2xl bg-rose-50 text-rose-600 dark:bg-rose-950/50 dark:text-rose-400 border border-rose-200 dark:border-rose-900/50 flex items-center justify-center shrink-0';
                    if (actionIcon) actionIcon.textContent = icon || 'delete';
                } else if (type === 'warning') {
                    actionBtn.className = 'inline-flex h-9.5 items-center justify-center gap-1.5 rounded-xl bg-amber-600 px-4.5 text-xs font-semibold text-white shadow-sm shadow-amber-600/25 hover:bg-amber-700 active:translate-y-px transition cursor-pointer';
                    iconBg.className = 'w-12 h-12 rounded-2xl bg-amber-50 text-amber-600 dark:bg-amber-950/50 dark:text-amber-400 border border-amber-200 dark:border-amber-900/50 flex items-center justify-center shrink-0';
                    if (actionIcon) actionIcon.textContent = icon || 'warning';
                } else {
                    actionBtn.className = 'inline-flex h-9.5 items-center justify-center gap-1.5 rounded-xl bg-teal-700 px-4.5 text-xs font-semibold text-white shadow-sm shadow-teal-700/25 hover:bg-teal-800 active:translate-y-px transition cursor-pointer';
                    iconBg.className = 'w-12 h-12 rounded-2xl bg-teal-50 text-teal-700 dark:bg-teal-950/50 dark:text-teal-400 border border-teal-200 dark:border-teal-900/50 flex items-center justify-center shrink-0';
                    if (actionIcon) actionIcon.textContent = icon || 'check';
                }
            }

            modal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';

            function cleanup(result) {
                modal.classList.add('hidden');
                document.body.style.overflow = '';
                actionBtn?.removeEventListener('click', onConfirmClick);
                cancelBtn?.removeEventListener('click', onCancelClick);
                document.removeEventListener('keydown', onKeyDown);
                modal.removeEventListener('click', onBackdropClick);
                resolve(result);
            }

            function onConfirmClick(e) {
                e.preventDefault();
                cleanup(true);
            }

            function onCancelClick(e) {
                e.preventDefault();
                cleanup(false);
            }

            function onKeyDown(e) {
                if (e.key === 'Escape') {
                    e.preventDefault();
                    cleanup(false);
                }
            }

            function onBackdropClick(e) {
                if (e.target === modal) {
                    cleanup(false);
                }
            }

            actionBtn?.addEventListener('click', onConfirmClick);
            cancelBtn?.addEventListener('click', onCancelClick);
            document.addEventListener('keydown', onKeyDown);
            modal.addEventListener('click', onBackdropClick);
        });
    };

    window.showAppAlert = function({
        title = 'Pemberitahuan',
        message = '',
        confirmText = 'Mengerti',
        type = 'primary',
        icon = 'info',
        allowHtml = false
    } = {}) {
        return new Promise((resolve) => {
            const modal = document.getElementById('appConfirmModal');
            if (!modal) {
                resolve();
                return;
            }

            const titleEl = document.getElementById('appConfirmTitle');
            const msgEl = document.getElementById('appConfirmMessage');
            const iconEl = document.getElementById('appConfirmIcon');
            const iconBg = document.getElementById('appConfirmIconBg');
            const actionBtn = document.getElementById('appConfirmActionBtn');
            const actionIcon = document.getElementById('appConfirmActionIcon');
            const actionText = document.getElementById('appConfirmActionText');
            const cancelBtn = document.getElementById('appConfirmCancelBtn');

            if (titleEl) titleEl.textContent = title;
            if (msgEl) {
                if (allowHtml) {
                    msgEl.innerHTML = message;
                } else {
                    msgEl.textContent = message;
                }
            }
            if (iconEl) iconEl.textContent = icon;
            if (actionText) actionText.textContent = confirmText;
            if (cancelBtn) cancelBtn.style.display = 'none';

            if (actionBtn && iconBg) {
                if (type === 'danger') {
                    actionBtn.className = 'inline-flex h-9.5 items-center justify-center gap-1.5 rounded-xl bg-rose-600 px-4.5 text-xs font-semibold text-white shadow-sm shadow-rose-600/25 hover:bg-rose-700 active:translate-y-px transition cursor-pointer';
                    iconBg.className = 'w-12 h-12 rounded-2xl bg-rose-50 text-rose-600 dark:bg-rose-950/50 dark:text-rose-400 border border-rose-200 dark:border-rose-900/50 flex items-center justify-center shrink-0';
                    if (actionIcon) actionIcon.textContent = icon || 'block';
                } else if (type === 'warning') {
                    actionBtn.className = 'inline-flex h-9.5 items-center justify-center gap-1.5 rounded-xl bg-amber-600 px-4.5 text-xs font-semibold text-white shadow-sm shadow-amber-600/25 hover:bg-amber-700 active:translate-y-px transition cursor-pointer';
                    iconBg.className = 'w-12 h-12 rounded-2xl bg-amber-50 text-amber-600 dark:bg-amber-950/50 dark:text-amber-400 border border-amber-200 dark:border-amber-900/50 flex items-center justify-center shrink-0';
                    if (actionIcon) actionIcon.textContent = icon || 'person_off';
                } else {
                    actionBtn.className = 'inline-flex h-9.5 items-center justify-center gap-1.5 rounded-xl bg-teal-700 px-4.5 text-xs font-semibold text-white shadow-sm shadow-teal-700/25 hover:bg-teal-800 active:translate-y-px transition cursor-pointer';
                    iconBg.className = 'w-12 h-12 rounded-2xl bg-teal-50 text-teal-700 dark:bg-teal-950/50 dark:text-teal-400 border border-teal-200 dark:border-teal-900/50 flex items-center justify-center shrink-0';
                    if (actionIcon) actionIcon.textContent = icon || 'check';
                }
            }

            modal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';

            function cleanup() {
                modal.classList.add('hidden');
                document.body.style.overflow = '';
                if (cancelBtn) cancelBtn.style.display = '';
                actionBtn?.removeEventListener('click', onConfirmClick);
                document.removeEventListener('keydown', onKeyDown);
                modal.removeEventListener('click', onBackdropClick);
                resolve();
            }

            function onConfirmClick(e) {
                e.preventDefault();
                cleanup();
            }

            function onKeyDown(e) {
                if (e.key === 'Escape' || e.key === 'Enter') {
                    e.preventDefault();
                    cleanup();
                }
            }

            function onBackdropClick(e) {
                if (e.target === modal) cleanup();
            }

            actionBtn?.addEventListener('click', onConfirmClick);
            document.addEventListener('keydown', onKeyDown);
            modal.addEventListener('click', onBackdropClick);
        });
    };
</script>
