<!-- Modal Tanggapan Pengajuan Resign (Approve / Reject) -->
<div id="resignDecisionModal" class="fixed inset-0 z-[160] hidden bg-slate-950/70 backdrop-blur-sm p-3 sm:p-4 overflow-y-auto flex items-center justify-center" onclick="if(event.target === this) closeResignDecisionModal()">
    <div class="relative w-full max-w-lg rounded-3xl bg-white p-6 sm:p-7 shadow-2xl shadow-slate-950/20 dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 animate-modal-pop">
        
        {{-- Header --}}
        <div class="flex items-start justify-between gap-3 pb-4 border-b border-slate-100 dark:border-slate-800">
            <div class="flex items-center gap-3">
                <div id="resignDecisionIconBg" class="w-11 h-11 rounded-2xl flex items-center justify-center shrink-0">
                    <span id="resignDecisionIcon" class="material-symbols-outlined text-[24px]"></span>
                </div>
                <div>
                    <h3 id="resignDecisionTitle" class="text-base sm:text-lg font-bold text-slate-950 dark:text-white"></h3>
                    <p id="resignDecisionSubtitle" class="text-xs text-slate-500 dark:text-slate-400"></p>
                </div>
            </div>
            <button type="button" onclick="closeResignDecisionModal()" class="rounded-xl p-1.5 text-slate-400 hover:bg-slate-100 hover:text-slate-700 dark:hover:bg-slate-800 dark:hover:text-slate-200 transition cursor-pointer">
                <span class="material-symbols-outlined text-[20px]">close</span>
            </button>
        </div>

        {{-- Form --}}
        <form id="resignDecisionForm" method="POST" action="" class="mt-4 space-y-4">
            @csrf
            @method('PATCH')
            <input type="hidden" name="decision" id="resignDecisionInput" value="approved">

            {{-- Info Alert Box --}}
            <div id="resignDecisionAlert" class="rounded-2xl p-3.5 border text-xs leading-relaxed"></div>

            {{-- Message Input --}}
            <div>
                <label for="resignDecisionMessage" id="resignDecisionMessageLabel" class="portal-label text-xs uppercase tracking-wider text-slate-500 dark:text-slate-400 font-bold">
                    Pesan Tanggapan <span class="text-rose-500">*</span>
                </label>
                <textarea id="resignDecisionMessage" 
                          name="response_message" 
                          rows="4" 
                          required 
                          placeholder="Tuliskan kata-kata pesan untuk disampaikan ke kandidat..." 
                          class="portal-input mt-1 leading-relaxed text-sm"></textarea>
                <p id="resignDecisionMessageHint" class="mt-1 text-[11px] text-slate-400">Pesan ini akan otomatis terkirim ke ruang obrolan kerja sama dan notifikasi kandidat.</p>
            </div>

            {{-- Buttons --}}
            <div class="pt-4 flex items-center justify-end gap-2.5 border-t border-slate-100 dark:border-slate-800">
                <button type="button" onclick="closeResignDecisionModal()" class="inline-flex h-10 items-center justify-center rounded-xl border border-slate-200 bg-white px-4 text-xs sm:text-sm font-semibold text-slate-700 shadow-xs hover:bg-slate-50 hover:border-slate-300 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 transition cursor-pointer">
                    Batal
                </button>
                <button type="submit" id="resignDecisionSubmitBtn" class="inline-flex h-10 items-center justify-center gap-1.5 rounded-xl px-5 text-xs sm:text-sm font-semibold text-white shadow-sm active:translate-y-px transition cursor-pointer">
                    <span id="resignDecisionSubmitIcon" class="material-symbols-outlined text-[18px]"></span>
                    <span id="resignDecisionSubmitText">Konfirmasi</span>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    (function() {
        let currentOnSuccess = null;

        window.openResignDecisionModal = function(opts) {
            const {
                applicationId,
                decision = 'approved',
                candidateName = 'Kandidat',
                jobTitle = 'Posisi Pekerjaan',
                employerName = 'Mitra UMKM',
                onSuccess = null
            } = opts || {};

            currentOnSuccess = onSuccess;

            const modal = document.getElementById('resignDecisionModal');
            const form = document.getElementById('resignDecisionForm');
            const decisionInput = document.getElementById('resignDecisionInput');
            const iconBg = document.getElementById('resignDecisionIconBg');
            const icon = document.getElementById('resignDecisionIcon');
            const title = document.getElementById('resignDecisionTitle');
            const subtitle = document.getElementById('resignDecisionSubtitle');
            const alertBox = document.getElementById('resignDecisionAlert');
            const messageLabel = document.getElementById('resignDecisionMessageLabel');
            const textarea = document.getElementById('resignDecisionMessage');
            const submitBtn = document.getElementById('resignDecisionSubmitBtn');
            const submitIcon = document.getElementById('resignDecisionSubmitIcon');
            const submitText = document.getElementById('resignDecisionSubmitText');

            if (!modal || !form) return;

            form.action = `/mitra/pelamar/${applicationId}/resign-decision`;
            decisionInput.value = decision;
            subtitle.textContent = `${candidateName} · ${jobTitle}`;

            if (decision === 'approved') {
                iconBg.className = 'w-11 h-11 rounded-2xl bg-emerald-50 dark:bg-emerald-950/50 text-emerald-600 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-900/40 flex items-center justify-center shrink-0';
                icon.textContent = 'check_circle';
                title.textContent = 'Setujui Pengunduran Diri (Resign)';
                
                alertBox.className = 'rounded-2xl bg-emerald-50/80 dark:bg-emerald-950/40 p-3.5 border border-emerald-200/80 dark:border-emerald-900/60 text-xs text-emerald-900 dark:text-emerald-200 leading-relaxed';
                alertBox.innerHTML = `<strong>Pemberitahuan:</strong> Anda akan menyetujui permohonan pengunduran diri <strong>${candidateName}</strong>. Berikan kata-kata apresiasi dan pesan persetujuan yang akan dikirimkan langsung ke kandidat.`;

                messageLabel.innerHTML = 'Kata-kata Persetujuan & Ucapan Terima Kasih <span class="text-rose-500">*</span>';
                textarea.value = `Terima kasih atas dedikasi dan kerja sama yang baik selama bergabung bersama kami di ${employerName}. Kami menyetujui permohonan pengunduran diri Anda dan mendoakan kesuksesan untuk langkah karier Anda selanjutnya!`;

                submitBtn.className = 'inline-flex h-10 items-center justify-center gap-1.5 rounded-xl bg-emerald-600 px-5 text-xs sm:text-sm font-semibold text-white shadow-sm shadow-emerald-600/25 hover:bg-emerald-700 active:translate-y-px transition cursor-pointer';
                submitIcon.textContent = 'check';
                submitText.textContent = 'Konfirmasi Setujui Resign';
            } else {
                iconBg.className = 'w-11 h-11 rounded-2xl bg-rose-50 dark:bg-rose-950/50 text-rose-600 dark:text-rose-400 border border-rose-200 dark:border-rose-900/40 flex items-center justify-center shrink-0';
                icon.textContent = 'cancel';
                title.textContent = 'Tolak Pengajuan Resign';

                alertBox.className = 'rounded-2xl bg-rose-50/80 dark:bg-rose-950/40 p-3.5 border border-rose-200/80 dark:border-rose-900/60 text-xs text-rose-900 dark:text-rose-200 leading-relaxed';
                alertBox.innerHTML = `<strong>Pemberitahuan:</strong> Permohonan pengunduran diri <strong>${candidateName}</strong> akan ditolak untuk saat ini. Sampaikan alasan atau catatan agar peserta dapat mendiskusikannya kembali.`;

                messageLabel.innerHTML = 'Alasan Penolakan & Catatan untuk Kandidat <span class="text-rose-500">*</span>';
                textarea.value = `Mohon maaf, permohonan pengunduran diri belum dapat kami setujui saat ini karena masih ada proyek/tanggung jawab yang sedang berjalan. Mari kita diskusikan lebih lanjut melalui obrolan ini.`;

                submitBtn.className = 'inline-flex h-10 items-center justify-center gap-1.5 rounded-xl bg-rose-600 px-5 text-xs sm:text-sm font-semibold text-white shadow-sm shadow-rose-600/25 hover:bg-rose-700 active:translate-y-px transition cursor-pointer';
                submitIcon.textContent = 'close';
                submitText.textContent = 'Kirim Penolakan Resign';
            }

            modal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
            textarea.focus();
        };

        window.closeResignDecisionModal = function() {
            const modal = document.getElementById('resignDecisionModal');
            if (modal) {
                modal.classList.add('hidden');
                document.body.style.overflow = '';
            }
        };

        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                window.closeResignDecisionModal();
            }
        });

        document.addEventListener('DOMContentLoaded', () => {
            const form = document.getElementById('resignDecisionForm');
            if (form) {
                form.addEventListener('submit', async function(e) {
                    if (typeof currentOnSuccess === 'function') {
                        e.preventDefault();
                        const submitBtn = document.getElementById('resignDecisionSubmitBtn');
                        const originalHtml = submitBtn.innerHTML;
                        submitBtn.disabled = true;
                        submitBtn.innerHTML = '<span class="material-symbols-outlined text-[18px] animate-spin">sync</span> Memproses...';

                        try {
                            const formData = new FormData(form);
                            const res = await fetch(form.action, {
                                method: 'POST',
                                headers: {
                                    'Accept': 'application/json',
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                },
                                body: formData
                            });

                            if (res.ok) {
                                const data = await res.json();
                                window.closeResignDecisionModal();
                                currentOnSuccess(data);
                            } else {
                                const err = await res.json();
                                alert(err.message || 'Terjadi kesalahan saat memproses keputusan resign.');
                            }
                        } catch (err) {
                            console.error(err);
                            form.submit();
                        } finally {
                            submitBtn.disabled = false;
                            submitBtn.innerHTML = originalHtml;
                        }
                    }
                });
            }
        });
    })();
</script>
