<div class="modal fade" id="logoutModal" tabindex="-1" aria-labelledby="logoutModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="p-6">
                <div class="grid h-11 w-11 place-items-center rounded-xl bg-rose-50 text-rose-700 dark:bg-rose-950/60 dark:text-rose-300">
                    <span class="material-symbols-outlined">logout</span>
                </div>
                <h2 id="logoutModalLabel" class="mt-4 text-lg font-bold text-slate-950 dark:text-white">Keluar dari akun?</h2>
                <p class="mt-2 text-sm leading-6 text-slate-600 dark:text-slate-400">Sesi aktif akan diakhiri. Anda perlu masuk kembali untuk membuka fitur sesuai role.</p>
                <div class="mt-6 flex justify-end gap-2">
                    <button type="button" class="portal-button-secondary" data-bs-dismiss="modal">Batal</button>
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="inline-flex min-h-10 items-center justify-center rounded-xl bg-rose-700 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-rose-800 active:translate-y-px">Ya, keluar</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
