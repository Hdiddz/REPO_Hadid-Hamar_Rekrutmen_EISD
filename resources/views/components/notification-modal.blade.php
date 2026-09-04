<!-- Global Notification Full Detail Modal Component -->
<div id="notificationDetailModal" class="fixed inset-0 z-[160] hidden bg-slate-950/60 backdrop-blur-xs p-3 sm:p-4 overflow-y-auto flex items-center justify-center" role="dialog" aria-modal="true" style="margin: 0;">
    <div class="fixed inset-0 -z-10" onclick="closeNotificationDetailModal()"></div>

    <div class="relative w-full max-w-lg rounded-3xl bg-white p-4 sm:p-7 shadow-2xl dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 animate-modal-pop overflow-hidden max-h-[92dvh] flex flex-col my-auto">
        {{-- Header --}}
        <div class="flex items-start justify-between gap-3 border-b border-slate-100 pb-3 sm:pb-4 dark:border-slate-800 min-w-0 shrink-0">
            <div class="flex items-center gap-2.5 sm:gap-3 min-w-0 flex-1">
                <div id="notifDetailIconBox" class="flex h-10 w-10 sm:h-11 sm:w-11 items-center justify-center rounded-2xl bg-brand-50 text-brand-700 dark:bg-brand-950 dark:text-brand-300 shrink-0">
                    <span id="notifDetailIcon" class="material-symbols-outlined text-xl sm:text-2xl">notifications</span>
                </div>
                <div class="min-w-0 flex-1">
                    <h3 id="notifDetailTitle" class="text-sm sm:text-base font-bold text-slate-950 dark:text-white truncate">Pemberitahuan</h3>
                    <p id="notifDetailTime" class="text-[11px] sm:text-xs text-slate-400 mt-0.5">Baru saja</p>
                </div>
            </div>
            <button type="button" onclick="closeNotificationDetailModal()" class="rounded-xl p-1.5 text-slate-400 hover:bg-slate-100 hover:text-slate-600 dark:hover:bg-slate-800 dark:hover:text-slate-300 transition cursor-pointer shrink-0" aria-label="Tutup rincian">
                <span class="material-symbols-outlined text-[20px]">close</span>
            </button>
        </div>

        {{-- Body --}}
        <div class="py-4 sm:py-5 overflow-y-auto flex-1 min-w-0">
            <div class="rounded-2xl bg-slate-50 p-3.5 sm:p-4 dark:bg-slate-950/60 border border-slate-100 dark:border-slate-800/80 min-w-0 overflow-hidden">
                <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">Isi Pesan Notifikasi</p>
                <p id="notifDetailMessage" class="text-xs sm:text-sm leading-relaxed text-slate-800 dark:text-slate-200 font-medium whitespace-pre-wrap break-words overflow-hidden">
                </p>
            </div>
            <div id="notifDetailEmployerBox" class="mt-3 flex items-center gap-2 text-xs text-slate-500 dark:text-slate-400 px-1 min-w-0">
                <span class="material-symbols-outlined text-[17px] text-brand-600 dark:text-brand-400 shrink-0">business</span>
                <span id="notifDetailEmployer" class="font-semibold text-slate-700 dark:text-slate-300 min-w-0 flex-1 truncate break-words"></span>
            </div>
        </div>

        {{-- Footer --}}
        <div class="flex flex-wrap items-center justify-end gap-2 border-t border-slate-100 pt-3 sm:pt-4 dark:border-slate-800 shrink-0">
            <button type="button" onclick="closeNotificationDetailModal()" class="portal-button-secondary py-2 px-3.5 text-xs cursor-pointer">
                Tutup
            </button>
            <a id="notifDetailActionBtn" href="#" class="portal-button-primary py-2 px-3.5 text-xs inline-flex items-center gap-1.5 cursor-pointer">
                <span>Buka Halaman Terkait</span>
                <span class="material-symbols-outlined text-[16px]">arrow_forward</span>
            </a>
        </div>
    </div>
</div>
