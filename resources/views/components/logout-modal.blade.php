<div class="modal fade" id="logoutModal" tabindex="-1" aria-labelledby="logoutModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered max-w-md">
        <div class="modal-content overflow-hidden rounded-3xl border border-slate-200/80 bg-white shadow-2xl shadow-slate-950/20 dark:border-slate-800 dark:bg-slate-900">
            <div class="p-6 sm:p-7">
                <div class="flex items-center gap-3.5 pb-4 border-b border-slate-100 dark:border-slate-800">
                    <div class="w-11 h-11 rounded-2xl bg-rose-50 text-rose-600 dark:bg-rose-950/50 dark:text-rose-400 border border-rose-100 dark:border-rose-900/40 flex items-center justify-center shrink-0">
                        <span class="material-symbols-outlined text-[24px]">logout</span>
                    </div>
                    <div>
                        <h2 id="logoutModalLabel" class="text-lg font-bold text-slate-900 dark:text-white leading-tight">Keluar dari akun?</h2>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Konfirmasi pengakhiran sesi aktif Anda.</p>
                    </div>
                </div>
                <p class="mt-4 text-sm leading-relaxed text-slate-600 dark:text-slate-400">Sesi aktif akan diakhiri. Anda perlu masuk kembali dengan kredensial akun untuk mengakses fitur portal.</p>
                <div class="mt-6 flex items-center justify-end gap-2.5 pt-4 border-t border-slate-100 dark:border-slate-800">
                    <button type="button" class="inline-flex h-10 items-center justify-center rounded-xl border border-slate-200 bg-white px-4 text-xs sm:text-sm font-semibold text-slate-700 shadow-xs hover:bg-slate-50 hover:border-slate-300 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 transition" data-bs-dismiss="modal">Batal</button>
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="inline-flex h-10 items-center justify-center gap-1.5 rounded-xl bg-rose-600 px-5 text-xs sm:text-sm font-semibold text-white shadow-sm shadow-rose-600/25 hover:bg-rose-700 active:translate-y-px transition">
                            <span class="material-symbols-outlined text-[18px]">logout</span>
                            Ya, Keluar
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
