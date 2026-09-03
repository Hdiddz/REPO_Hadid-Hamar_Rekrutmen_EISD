<!-- Global In-App PDF Viewer Modal Component -->
<div id="appPdfModal" class="fixed inset-0 z-[160] hidden bg-slate-950/75 backdrop-blur-sm p-2 sm:p-4 md:p-6 overflow-y-auto flex items-center justify-center">
    <div class="relative flex flex-col w-full max-w-5xl h-[92vh] max-h-[900px] rounded-3xl bg-white shadow-2xl dark:bg-slate-900 border border-slate-200/90 dark:border-slate-800 overflow-hidden animate-modal-pop">
        
        <!-- Header -->
        <div class="flex items-center justify-between px-5 py-3.5 border-b border-slate-100 dark:border-slate-800 bg-slate-50/80 dark:bg-slate-900/80 shrink-0">
            <div class="flex items-center gap-3 min-w-0">
                <div class="w-10 h-10 rounded-xl bg-rose-50 text-rose-600 dark:bg-rose-950/50 dark:text-rose-400 border border-rose-200 dark:border-rose-900/50 flex items-center justify-center shrink-0">
                    <span class="material-symbols-outlined text-[22px]">picture_as_pdf</span>
                </div>
                <div class="min-w-0">
                    <h3 id="appPdfTitle" class="text-sm font-bold text-slate-900 dark:text-white truncate">
                        Pratinjau Resume PDF
                    </h3>
                    <p class="text-[11px] text-slate-500 dark:text-slate-400 truncate">
                        Ditampilkan langsung melalui penampil PDF dokumen browser
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-2 shrink-0">
                <a id="appPdfOpenNewTab" href="#" target="_blank" rel="noopener noreferrer" class="hidden sm:inline-flex items-center gap-1.5 rounded-xl border border-slate-200 bg-white px-3 py-1.5 text-xs font-semibold text-slate-700 shadow-xs hover:bg-slate-50 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 transition" title="Buka berkas di jendela/tab peramban penuh">
                    <span class="material-symbols-outlined text-[16px]">open_in_new</span>
                    <span>Tab baru</span>
                </a>
                <a id="appPdfDownload" href="#" class="inline-flex items-center gap-1.5 rounded-xl bg-teal-700 px-3 py-1.5 text-xs font-semibold text-white shadow-sm hover:bg-teal-800 transition" title="Unduh berkas PDF ini ke perangkat">
                    <span class="material-symbols-outlined text-[16px]">download</span>
                    <span>Unduh PDF</span>
                </a>
                <button type="button" onclick="closePdfViewer()" class="inline-flex h-8 w-8 items-center justify-center rounded-xl text-slate-400 hover:bg-slate-200/60 hover:text-slate-700 dark:hover:bg-slate-800 dark:hover:text-white transition cursor-pointer" title="Tutup pratinjau (Esc)">
                    <span class="material-symbols-outlined text-[20px]">close</span>
                </button>
            </div>
        </div>

        <!-- Body / PDF Viewer Frame -->
        <div class="relative flex-1 bg-slate-100 dark:bg-slate-950 p-2 sm:p-3 overflow-hidden">
            <div id="appPdfLoading" class="absolute inset-0 flex flex-col items-center justify-center gap-3 bg-slate-100/90 dark:bg-slate-950/90 z-10">
                <span class="inline-block h-8 w-8 animate-spin rounded-full border-3 border-solid border-teal-600 border-r-transparent"></span>
                <p class="text-xs font-medium text-slate-600 dark:text-slate-400">Memuat pratinjau berkas PDF...</p>
            </div>

            <iframe id="appPdfFrame" src="about:blank" class="w-full h-full rounded-2xl border border-slate-200 dark:border-slate-800 bg-white shadow-inner" title="Penampil Dokumen PDF"></iframe>
        </div>
    </div>
</div>

<script>
    window.openPdfViewer = function(previewUrl, candidateName = 'Kandidat', downloadUrl = null) {
        const modal = document.getElementById('appPdfModal');
        const frame = document.getElementById('appPdfFrame');
        const titleEl = document.getElementById('appPdfTitle');
        const newTabEl = document.getElementById('appPdfOpenNewTab');
        const downloadEl = document.getElementById('appPdfDownload');
        const loadingEl = document.getElementById('appPdfLoading');

        if (!modal || !frame) return;

        if (titleEl) {
            titleEl.textContent = `Resume PDF: ${candidateName}`;
        }

        if (newTabEl) {
            newTabEl.href = previewUrl;
        }

        if (downloadEl) {
            downloadEl.href = downloadUrl || previewUrl;
        }

        if (loadingEl) {
            loadingEl.style.display = 'flex';
        }

        frame.onload = function() {
            if (loadingEl) loadingEl.style.display = 'none';
        };

        // Pasang src preview inline
        frame.src = previewUrl;

        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    };

    window.closePdfViewer = function() {
        const modal = document.getElementById('appPdfModal');
        const frame = document.getElementById('appPdfFrame');
        if (!modal) return;

        modal.classList.add('hidden');
        document.body.style.overflow = '';
        if (frame) {
            frame.src = 'about:blank';
        }
    };

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            const modal = document.getElementById('appPdfModal');
            if (modal && !modal.classList.contains('hidden')) {
                window.closePdfViewer();
            }
        }
    });

    document.addEventListener('DOMContentLoaded', () => {
        const modal = document.getElementById('appPdfModal');
        if (modal) {
            modal.addEventListener('click', (e) => {
                if (e.target === modal) {
                    window.closePdfViewer();
                }
            });
        }
    });
</script>
