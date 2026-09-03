@auth
@php
    $currentUser = Auth::user();
    $currentRole = $currentUser->role ?? 'jobseeker';
@endphp

<!-- ================= FLOATING CHAT WIDGET (ROLE-ADAPTIVE: PENCARI KERJA, MITRA UMKM, ADMIN) ================= -->
<div class="fixed bottom-6 right-6 z-50 flex flex-col items-end print:hidden">
    <!-- Floating Chat Pop-up Window -->
    <div id="floatingChatPopup" class="hidden mb-3 w-[350px] sm:w-[385px] max-w-[calc(100vw-2rem)] h-[520px] bg-white rounded-3xl shadow-2xl border border-slate-200 flex flex-col overflow-hidden animate-page-enter">
        
        <!-- VIEW 1: DAFTAR PERCAKAPAN (CONTACT LIST VIEW) -->
        <div id="popupChatListView" class="flex flex-col h-full">
            <!-- Header Daftar Percakapan -->
            <div class="p-3.5 bg-teal-800 text-white flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <span class="material-symbols-outlined text-lg">forum</span>
                    <div>
                        <div class="text-xs font-bold leading-tight">
                            @if($currentRole === 'employer')
                                Pesan &amp; Obrolan Pelamar
                            @elseif($currentRole === 'admin')
                                Saluran Pengawasan SDG 8
                            @else
                                Pesan &amp; Obrolan Kerja
                            @endif
                        </div>
                        <div class="text-[10px] text-teal-200">
                            @if($currentRole === 'employer')
                                Kelola seleksi &amp; wawancara kandidat
                            @elseif($currentRole === 'admin')
                                Koordinasi kepatuhan etis &amp; aduan
                            @else
                                Pilih Mitra UMKM untuk berkirim pesan
                            @endif
                        </div>
                    </div>
                </div>
                <div class="flex items-center gap-1">
                    <a href="{{ route('chat.index') }}" class="p-1 hover:bg-teal-700 rounded-lg text-teal-200 hover:text-white transition-colors" title="Buka Halaman Penuh">
                        <span class="material-symbols-outlined text-base">open_in_new</span>
                    </a>
                    <button type="button" onclick="toggleFloatingChat()" class="p-1 hover:bg-teal-700 rounded-lg text-teal-200 hover:text-white transition-colors cursor-pointer" title="Tutup Pesan">
                        <span class="material-symbols-outlined text-base">close</span>
                    </button>
                </div>
            </div>

            <!-- Role Context Badge Strip -->
            <div class="px-3.5 py-1.5 bg-teal-50/90 border-b border-teal-100 text-[10px] text-teal-900 flex items-center justify-between">
                <div class="flex items-center gap-1.5">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                    <span>Role Aktif: <strong>{{ $currentRole === 'employer' ? 'Mitra UMKM' : ($currentRole === 'admin' ? 'Super Admin SDG 8' : 'Pencari Kerja') }}</strong></span>
                </div>
                <span class="text-[9px] font-bold uppercase tracking-wider text-teal-700">100% Bebas Calo</span>
            </div>

            <!-- Conversation List Container -->
            <div class="flex-1 overflow-y-auto divide-y divide-slate-100" id="popupContactListContainer">
                @if($currentRole === 'employer')
                    <!-- EMPLOYER POV: List of Job Applicants -->
                    <!-- Applicant 1: Budi Santoso (Barista) -->
                    <div onclick="openPopupChatDetail('bs', 'Budi Santoso', 'Pelamar Barista &amp; Kasir', 'Barista &amp; Kasir')" 
                         class="p-3 bg-white hover:bg-slate-50 cursor-pointer transition-colors flex items-start gap-2.5">
                        <div class="w-9 h-9 rounded-xl bg-teal-100 text-teal-800 font-bold flex items-center justify-center text-xs shrink-0">
                            BS
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center justify-between mb-0.5">
                                <span class="font-bold text-xs text-slate-900 truncate">Budi Santoso</span>
                                <span class="text-[9px] text-teal-700 font-bold">10:15</span>
                            </div>
                            <span class="text-[10px] text-teal-800 font-semibold block truncate">Barista &amp; Kasir • 100% Match</span>
                            <p class="text-[10px] text-slate-500 truncate mt-0.5">"Siap hadir tepat waktu untuk sesi wawancara besok Pak..."</p>
                        </div>
                        <span class="w-2 h-2 rounded-full bg-rose-500 shrink-0 mt-1.5" title="Pesan baru"></span>
                    </div>

                    <!-- Applicant 2: Siti Rahma (Kasir) -->
                    <div onclick="openPopupChatDetail('sr', 'Siti Rahma', 'Pelamar Kasir Grosir', 'Kasir Grosir')" 
                         class="p-3 bg-white hover:bg-slate-50 cursor-pointer transition-colors flex items-start gap-2.5 opacity-90 hover:opacity-100">
                        <div class="w-9 h-9 rounded-xl bg-blue-100 text-blue-800 font-bold flex items-center justify-center text-xs shrink-0">
                            SR
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center justify-between mb-0.5">
                                <span class="font-bold text-xs text-slate-800 truncate">Siti Rahma</span>
                                <span class="text-[9px] text-slate-400">Kemarin</span>
                            </div>
                            <span class="text-[10px] text-blue-800 font-semibold block truncate">Kasir Grosir • Berkas Diverifikasi</span>
                            <p class="text-[10px] text-slate-500 truncate mt-0.5">"Terima kasih infonya Pak, saya bersedia shift pagi..."</p>
                        </div>
                    </div>

                    <!-- Applicant 3: Ahmad Fauzi (Kurir) -->
                    <div onclick="openPopupChatDetail('af', 'Ahmad Fauzi', 'Pelamar Kurir Paket', 'Kurir Logistik')" 
                         class="p-3 bg-white hover:bg-slate-50 cursor-pointer transition-colors flex items-start gap-2.5 opacity-90 hover:opacity-100">
                        <div class="w-9 h-9 rounded-xl bg-amber-100 text-amber-800 font-bold flex items-center justify-center text-xs shrink-0">
                            AF
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center justify-between mb-0.5">
                                <span class="font-bold text-xs text-slate-800 truncate">Ahmad Fauzi</span>
                                <span class="text-[9px] text-slate-400">2 hari lalu</span>
                            </div>
                            <span class="text-[10px] text-amber-800 font-semibold block truncate">Kurir Wilayah • SIM C Aktif</span>
                            <p class="text-[10px] text-slate-500 truncate mt-0.5">"Motor operasional siap dengan plat nomor Bandung..."</p>
                        </div>
                    </div>

                @elseif($currentRole === 'admin')
                    <!-- ADMIN POV: Coordination & Ethics Audit Contacts -->
                    <!-- Contact 1: Hendra Wijaya (Kedai Kopi Sudut Temu) -->
                    <div onclick="openPopupChatDetail('st_adm', 'Hendra Wijaya (Kedai Sudut Temu)', 'Mitra UMKM Binaan', 'Audit Slip Gaji')" 
                         class="p-3 bg-white hover:bg-slate-50 cursor-pointer transition-colors flex items-start gap-2.5">
                        <div class="w-9 h-9 rounded-xl bg-teal-100 text-teal-800 font-bold flex items-center justify-center text-xs shrink-0">
                            ST
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center justify-between mb-0.5">
                                <span class="font-bold text-xs text-slate-900 truncate">Kedai Kopi Sudut Temu</span>
                                <span class="text-[9px] text-teal-700 font-bold">11:30</span>
                            </div>
                            <span class="text-[10px] text-teal-800 font-semibold block truncate">Hendra Wijaya • Mitra Terverifikasi</span>
                            <p class="text-[10px] text-slate-500 truncate mt-0.5">"Laporan transparansi upah bulanan telah diunggah lengkap."</p>
                        </div>
                        <span class="w-2 h-2 rounded-full bg-rose-500 shrink-0 mt-1.5"></span>
                    </div>

                    <!-- Contact 2: Budi Santoso (Pelamar) -->
                    <div onclick="openPopupChatDetail('bs_adm', 'Budi Santoso (Pencari Kerja)', 'Pencari Kerja Terverifikasi', 'Verifikasi Loker')" 
                         class="p-3 bg-white hover:bg-slate-50 cursor-pointer transition-colors flex items-start gap-2.5 opacity-90 hover:opacity-100">
                        <div class="w-9 h-9 rounded-xl bg-purple-100 text-purple-800 font-bold flex items-center justify-center text-xs shrink-0">
                            BS
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center justify-between mb-0.5">
                                <span class="font-bold text-xs text-slate-800 truncate">Budi Santoso</span>
                                <span class="text-[9px] text-slate-400">Kemarin</span>
                            </div>
                            <span class="text-[10px] text-purple-800 font-semibold block truncate">Pencari Kerja • Bandung</span>
                            <p class="text-[10px] text-slate-500 truncate mt-0.5">"Terima kasih tim pengawas, loker yang saya laporkan sudah ditindak."</p>
                        </div>
                    </div>

                    <!-- Contact 3: Toko Berkah Mandiri -->
                    <div onclick="openPopupChatDetail('bm_adm', 'Bpk. Subagyo (Berkah Mandiri)', 'Mitra Ritel Sembako', 'Audit Kepatuhan')" 
                         class="p-3 bg-white hover:bg-slate-50 cursor-pointer transition-colors flex items-start gap-2.5 opacity-90 hover:opacity-100">
                        <div class="w-9 h-9 rounded-xl bg-slate-200 text-slate-700 font-bold flex items-center justify-center text-xs shrink-0">
                            BM
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center justify-between mb-0.5">
                                <span class="font-bold text-xs text-slate-800 truncate">Toko Berkah Mandiri</span>
                                <span class="text-[9px] text-slate-400">3 hari lalu</span>
                            </div>
                            <span class="text-[10px] text-slate-600 font-semibold block truncate">Bpk. Subagyo • Standar Jam Kerja</span>
                            <p class="text-[10px] text-slate-500 truncate mt-0.5">"Jadwal shift 8 jam sudah diterapkan ketat di toko."</p>
                        </div>
                    </div>

                @else
                    <!-- JOBSEEKER POV: List of UMKM Employers -->
                    <!-- UMKM 1: Kedai Kopi Sudut Temu (Active with Unread) -->
                    <div onclick="openPopupChatDetail('st', 'Kedai Kopi Sudut Temu', 'Hendra Wijaya (Owner)', 'Barista &amp; Kasir')" 
                         class="p-3 bg-white hover:bg-slate-50 cursor-pointer transition-colors flex items-start gap-2.5">
                        <div class="w-9 h-9 rounded-xl bg-teal-100 text-teal-800 font-bold flex items-center justify-center text-xs shrink-0">
                            ST
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center justify-between mb-0.5">
                                <span class="font-bold text-xs text-slate-900 truncate">Kedai Kopi Sudut Temu</span>
                                <span class="text-[9px] text-teal-700 font-bold">10:15</span>
                            </div>
                            <span class="text-[10px] text-teal-800 font-semibold block truncate">Barista &amp; Kasir • Dipatiukur</span>
                            <p class="text-[10px] text-slate-500 truncate mt-0.5">Undangan Wawancara: Besok jam 14.00 WIB</p>
                        </div>
                        <span class="w-2 h-2 rounded-full bg-rose-500 shrink-0 mt-1.5" title="Pesan baru"></span>
                    </div>

                    <!-- UMKM 2: Toko Berkah Mandiri -->
                    <div onclick="openPopupChatDetail('bm', 'Toko Berkah Mandiri', 'Bpk. Subagyo (Kepala Toko)', 'Staf Gudang')" 
                         class="p-3 bg-white hover:bg-slate-50 cursor-pointer transition-colors flex items-start gap-2.5 opacity-90 hover:opacity-100">
                        <div class="w-9 h-9 rounded-xl bg-slate-200 text-slate-700 font-bold flex items-center justify-center text-xs shrink-0">
                            BM
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center justify-between mb-0.5">
                                <span class="font-bold text-xs text-slate-800 truncate">Toko Berkah Mandiri</span>
                                <span class="text-[9px] text-slate-400">Kemarin</span>
                            </div>
                            <span class="text-[10px] text-slate-600 font-semibold block truncate">Staf Gudang • Cibadak</span>
                            <p class="text-[10px] text-slate-500 truncate mt-0.5">Berkas lamaran sudah kami verifikasi.</p>
                        </div>
                    </div>

                    <!-- UMKM 3: Sentra Distribusi Cepat -->
                    <div onclick="openPopupChatDetail('sc', 'Sentra Distribusi Cepat', 'Admin HR Logistik', 'Kurir Logistik')" 
                         class="p-3 bg-white hover:bg-slate-50 cursor-pointer transition-colors flex items-start gap-2.5 opacity-90 hover:opacity-100">
                        <div class="w-9 h-9 rounded-xl bg-slate-200 text-slate-700 font-bold flex items-center justify-center text-xs shrink-0">
                            SC
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center justify-between mb-0.5">
                                <span class="font-bold text-xs text-slate-800 truncate">Sentra Distribusi Cepat</span>
                                <span class="text-[9px] text-slate-400">2 hari lalu</span>
                            </div>
                            <span class="text-[10px] text-slate-600 font-semibold block truncate">Kurir Logistik • Kiaracondong</span>
                            <p class="text-[10px] text-slate-500 truncate mt-0.5">Apakah Anda memiliki SIM C aktif saat ini?</p>
                        </div>
                    </div>

                    <!-- UMKM 4: Warung Padang Minang Jaya (Jakarta) -->
                    <div onclick="openPopupChatDetail('mp', 'Warung Padang Minang Jaya', 'Bpk. Syahrul (Pengelola)', 'Pramusaji &amp; Kasir')" 
                         class="p-3 bg-white hover:bg-slate-50 cursor-pointer transition-colors flex items-start gap-2.5 opacity-90 hover:opacity-100">
                        <div class="w-9 h-9 rounded-xl bg-amber-100 text-amber-800 font-bold flex items-center justify-center text-xs shrink-0">
                            MJ
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center justify-between mb-0.5">
                                <span class="font-bold text-xs text-slate-800 truncate">Warung Padang Minang Jaya</span>
                                <span class="text-[9px] text-slate-400">5 jam lalu</span>
                            </div>
                            <span class="text-[10px] text-amber-800 font-semibold block truncate">Pramusaji • Tanah Abang Jakarta</span>
                            <p class="text-[10px] text-slate-500 truncate mt-0.5">Halo Budi, apakah siap mulai kerja shift pagi?</p>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <!-- VIEW 2: DETAIL PERCAKAPAN AKTIF DENGAN TOMBOL BACK -->
        <div id="popupChatDetailView" class="hidden flex-col h-full">
            <!-- Header Detail Chat dengan Tombol Back -->
            <div class="p-3 bg-teal-800 text-white flex items-center justify-between shrink-0">
                <div class="flex items-center gap-2">
                    <button type="button" onclick="showPopupChatList()" class="p-1 hover:bg-teal-700 rounded-lg text-teal-200 hover:text-white transition-colors cursor-pointer flex items-center" title="Kembali ke daftar percakapan">
                        <span class="material-symbols-outlined text-lg">arrow_back</span>
                    </button>
                    <div class="w-7 h-7 rounded-lg bg-teal-600 text-white font-bold flex items-center justify-center text-[11px] shrink-0" id="detailChatAvatar">
                        {{ $currentRole === 'employer' ? 'BS' : 'ST' }}
                    </div>
                    <div class="min-w-0">
                        <div class="text-xs font-bold leading-tight truncate max-w-[170px]" id="detailChatTitle">
                            {{ $currentRole === 'employer' ? 'Budi Santoso' : 'Kedai Kopi Sudut Temu' }}
                        </div>
                        <div class="text-[10px] text-teal-200 flex items-center gap-1" id="detailChatSubtitle">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 shrink-0"></span>
                            <span class="truncate" id="detailChatOwner">Online • {{ $currentRole === 'employer' ? 'Pelamar Barista' : 'Hendra Wijaya (Owner)' }}</span>
                        </div>
                    </div>
                </div>
                <div class="flex items-center gap-1">
                    <a href="{{ route('chat.index') }}" class="p-1 hover:bg-teal-700 rounded-lg text-teal-200 hover:text-white transition-colors" title="Buka Halaman Penuh">
                        <span class="material-symbols-outlined text-base">open_in_new</span>
                    </a>
                    <button type="button" onclick="toggleFloatingChat()" class="p-1 hover:bg-teal-700 rounded-lg text-teal-200 hover:text-white transition-colors cursor-pointer" title="Tutup Chat">
                        <span class="material-symbols-outlined text-base">close</span>
                    </button>
                </div>
            </div>

            <!-- Message Stream -->
            <div class="flex-1 p-3.5 overflow-y-auto space-y-3 bg-slate-50/50 text-xs" id="popupChatStream">
                <!-- Inbound Message -->
                <div class="flex items-start gap-2 max-w-[88%]">
                    <div class="w-6 h-6 rounded-lg bg-teal-700 text-white font-bold flex items-center justify-center text-[10px] shrink-0 mt-0.5" id="detailStreamAvatar">
                        {{ $currentRole === 'employer' ? 'BS' : 'ST' }}
                    </div>
                    <div class="bg-white border border-slate-200 p-3 rounded-2xl rounded-tl-sm shadow-2xs space-y-1">
                        <p class="text-slate-800 leading-relaxed" id="detailStreamMessage">
                            @if($currentRole === 'employer')
                                Selamat siang Pak Hendra. Terima kasih atas undangannya. Saya siap hadir wawancara kerja dan membawa CV fisik.
                            @elseif($currentRole === 'admin')
                                Selamat siang Tim Pengawas SDG 8. Transparansi nominal gaji dan absensi 8 jam kerja kami siap diaudit sewaktu-waktu.
                            @else
                                Halo Budi, berkas lamaran Anda untuk posisi Barista sudah kami review dan sangat sesuai kualifikasi.
                            @endif
                        </p>
                        <span class="text-[9px] text-slate-400 block text-right">10:15</span>
                    </div>
                </div>

                <!-- Ticket / Action Card -->
                <div class="p-2.5 bg-teal-50 border border-teal-200 rounded-2xl space-y-1.5" id="detailStreamTicket">
                    <div class="flex items-center justify-between">
                        <span class="text-[10px] font-bold text-teal-900 block" id="detailTicketTitle">
                            @if($currentRole === 'employer')
                                Jadwal Interview Kandidat
                            @elseif($currentRole === 'admin')
                                Status Kepatuhan Kerja Layak
                            @else
                                Undangan Wawancara Tatap Muka
                            @endif
                        </span>
                        <span class="material-symbols-outlined text-teal-700 text-sm">verified</span>
                    </div>
                    <div class="text-[11px] text-slate-800 font-semibold" id="detailTicketSubtitle">
                        @if($currentRole === 'employer')
                            Kamis, 14:00 WIB • Kedai Kopi Sudut Temu (Dipatiukur)
                        @elseif($currentRole === 'admin')
                            Audit Kepatuhan SDG 8: Terverifikasi Bersih
                        @else
                            Kamis, 14:00 WIB @ Kedai Dipatiukur
                        @endif
                    </div>
                    <div class="flex flex-wrap gap-1.5 pt-1" id="detailTicketButtons">
                        @if($currentRole === 'employer')
                            <button type="button" onclick="sendPopupReply('Konfirmasi jadwal interview Anda sudah kami catat di agenda kedai. Sampai jumpa besok!')" class="px-2.5 py-1 bg-teal-700 hover:bg-teal-800 text-white text-[10px] font-bold rounded-lg cursor-pointer">
                                Konfirmasi Jadwal
                            </button>
                            <button type="button" onclick="sendPopupReply('Apakah Anda membutuhkan petunjuk arah ke lokasi kedai kami?')" class="px-2.5 py-1 bg-white hover:bg-slate-100 text-slate-700 border border-slate-200 text-[10px] font-semibold rounded-lg cursor-pointer">
                                Kirim Petunjuk
                            </button>
                        @elseif($currentRole === 'admin')
                            <button type="button" onclick="sendPopupReply('Terima kasih. Hasil verifikasi kepatuhan Anda telah diperbarui ke status Terverifikasi Baik.')" class="px-2.5 py-1 bg-teal-700 hover:bg-teal-800 text-white text-[10px] font-bold rounded-lg cursor-pointer">
                                Validasi Laporan
                            </button>
                        @else
                            <button type="button" onclick="sendPopupReply('Saya konfirmasi siap hadir tepat waktu besok jam 14.00 WIB.')" class="px-2.5 py-1 bg-teal-700 hover:bg-teal-800 text-white text-[10px] font-bold rounded-lg cursor-pointer">
                                Siap Hadir
                            </button>
                            <button type="button" onclick="sendPopupReply('Apakah bisa disesuaikan ke jam 16.00 WIB?')" class="px-2.5 py-1 bg-white hover:bg-slate-100 text-slate-700 border border-slate-200 text-[10px] font-semibold rounded-lg cursor-pointer">
                                Reschedule
                            </button>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Input Bar -->
            <div class="p-2.5 border-t border-slate-200 bg-white shrink-0">
                <form onsubmit="handlePopupChatSend(event)" class="flex items-center gap-1.5">
                    <input type="text" id="popupChatInput" placeholder="{{ $currentRole === 'employer' ? 'Tulis pesan ke pelamar...' : ($currentRole === 'admin' ? 'Tulis catatan pengawasan...' : 'Tulis pesan ke UMKM...') }}" class="flex-1 py-2 px-3 bg-slate-100 focus:bg-white rounded-xl border border-transparent focus:border-teal-600 text-xs text-slate-900 outline-none transition-all">
                    <button type="submit" class="p-2 bg-teal-700 hover:bg-teal-800 active:scale-95 text-white rounded-xl cursor-pointer transition-all flex items-center justify-center">
                        <span class="material-symbols-outlined text-base">send</span>
                    </button>
                </form>
            </div>
        </div>

    </div>

    <!-- Floating FAB Button: Dinamai "Pesan" Sesuai Permintaan User -->
    <button type="button" id="floatingChatBtn" onclick="toggleFloatingChat()" class="px-4 py-3 bg-teal-800 hover:bg-teal-900 active:scale-95 text-white rounded-full shadow-2xl transition-all cursor-pointer flex items-center gap-2 relative group border border-teal-700/50">
        <span class="material-symbols-outlined text-xl">chat</span>
        <span class="text-xs font-bold tracking-tight">Pesan</span>
        <span class="w-5 h-5 bg-rose-500 text-white text-[10px] font-black rounded-full flex items-center justify-center border-2 border-teal-900 shadow-xs">1</span>
    </button>
</div>

<!-- ================= GLOBAL ROLE-ADAPTIVE CHAT JAVASCRIPT ================= -->
<script>
    // Unified Role-Based Contact Database
    const unifiedChatDB = {
        // --- EMPLOYER POV CONTACTS (Job Applicants) ---
        'bs': {
            title: 'Budi Santoso',
            owner: 'Pelamar Barista & Kasir',
            initials: 'BS',
            message: 'Selamat siang Pak Hendra, berkas CV dan sertifikat barista saya sudah diunggah. Saya siap hadir wawancara kerja.',
            hasTicket: true,
            ticketTitle: 'Jadwal Interview Kandidat',
            ticketSub: 'Kamis, 14:00 WIB • Kedai Kopi Sudut Temu'
        },
        'sr': {
            title: 'Siti Rahma',
            owner: 'Pelamar Kasir Grosir',
            initials: 'SR',
            message: 'Halo Bapak/Ibu, saya memiliki pengalaman kasir mesin POS selama 1 tahun dan siap bekerja sistem shift.',
            hasTicket: false,
            ticketTitle: 'Tahap Seleksi Berkas',
            ticketSub: 'Kesesuaian Kualifikasi: 85%'
        },
        'af': {
            title: 'Ahmad Fauzi',
            owner: 'Pelamar Kurir Paket',
            initials: 'AF',
            message: 'Selamat pagi Pak, motor saya pribadi berplat D dan saya hafal area rute pengiriman Bandung.',
            hasTicket: false,
            ticketTitle: 'Verifikasi SIM & Armada',
            ticketSub: 'SIM C Aktif s.d 2028'
        },

        // --- ADMIN POV CONTACTS (Monitoring & Compliance) ---
        'st_adm': {
            title: 'Hendra Wijaya (Kedai Sudut Temu)',
            owner: 'Mitra UMKM Binaan',
            initials: 'ST',
            message: 'Laporan kepatuhan jam kerja maksimal 8 jam dan slip gaji terbuka telah kami serahkan ke pengawas.',
            hasTicket: true,
            ticketTitle: 'Audit Kepatuhan Etis',
            ticketSub: 'Standar Upah Bersih: Lulus Verifikasi'
        },
        'bs_adm': {
            title: 'Budi Santoso (Pencari Kerja)',
            owner: 'Pencari Kerja Terverifikasi',
            initials: 'BS',
            message: 'Terima kasih tim pengawas KerjaLokal atas tindak lanjut laporan transparansi lowongan.',
            hasTicket: false,
            ticketTitle: 'Verifikasi Aduan Pelamar',
            ticketSub: 'Status: Terselesaikan'
        },
        'bm_adm': {
            title: 'Bpk. Subagyo (Berkah Mandiri)',
            owner: 'Mitra Ritel Sembako',
            initials: 'BM',
            message: 'Kami berkomitmen memenuhi seluruh pilar kerja layak dan tidak memotong upah staf.',
            hasTicket: false,
            ticketTitle: 'Audit Jam Kerja',
            ticketSub: 'Kepatuhan Istirahat: Sesuai SOP'
        },

        // --- JOBSEEKER POV CONTACTS (UMKM Partners) ---
        'st': {
            title: 'Kedai Kopi Sudut Temu',
            owner: 'Hendra Wijaya (Owner)',
            initials: 'ST',
            message: 'Halo Budi, berkas lamaran Anda untuk posisi Barista sudah kami review dan sangat sesuai kualifikasi.',
            hasTicket: true,
            ticketTitle: 'Undangan Wawancara Tatap Muka',
            ticketSub: 'Kamis, 14:00 WIB @ Kedai Dipatiukur'
        },
        'bm': {
            title: 'Toko Berkah Mandiri',
            owner: 'Bpk. Subagyo (Kepala Toko)',
            initials: 'BM',
            message: 'Selamat siang Budi. Lamaran Anda untuk Staf Gudang sudah masuk antrean interview. Bersedia hadir besok lusa?',
            hasTicket: false,
            ticketTitle: 'Tahap Tinjauan Berkas',
            ticketSub: 'Berkas Lengkap'
        },
        'sc': {
            title: 'Sentra Distribusi Cepat',
            owner: 'Admin HR Logistik',
            initials: 'SC',
            message: 'Halo Budi, untuk rute kurir paket motor, apakah kendaraan Anda memiliki plat nomor D (Bandung Raya)?',
            hasTicket: false,
            ticketTitle: 'Pemeriksaan Persyaratan',
            ticketSub: 'Syarat SIM C & Motor'
        },
        'mp': {
            title: 'Warung Padang Minang Jaya',
            owner: 'Bpk. Syahrul (Pengelola)',
            initials: 'MJ',
            message: 'Halo Budi, apakah siap mulai kerja shift pagi di cabang Tanah Abang Jakarta?',
            hasTicket: false,
            ticketTitle: 'Penawaran Posisi Pramusaji',
            ticketSub: 'Gaji Bersih Rp 3.500.000 + Makan'
        }
    };

    function showPopupChatList() {
        const listView = document.getElementById('popupChatListView');
        const detailView = document.getElementById('popupChatDetailView');
        if (listView && detailView) {
            listView.classList.remove('hidden');
            detailView.classList.add('hidden');
        }
    }

    function openPopupChatDetail(id, title, owner, role) {
        const data = unifiedChatDB[id] || {
            title: title || 'Mitra KerjaLokal',
            owner: owner || 'Online',
            initials: (title ? title.substring(0, 2).toUpperCase() : 'KL'),
            message: 'Halo, terima kasih telah menghubungi melalui sistem rekrutmen etis KerjaLokal.',
            hasTicket: false
        };

        const detailTitle = document.getElementById('detailChatTitle');
        const detailOwner = document.getElementById('detailChatOwner');
        const detailAvatar = document.getElementById('detailChatAvatar');
        const streamAvatar = document.getElementById('detailStreamAvatar');
        const streamMsg = document.getElementById('detailStreamMessage');
        const ticket = document.getElementById('detailStreamTicket');

        if (detailTitle) detailTitle.textContent = data.title;
        if (detailOwner) detailOwner.textContent = data.owner + ' • Online';
        if (detailAvatar) detailAvatar.textContent = data.initials;
        if (streamAvatar) streamAvatar.textContent = data.initials;
        if (streamMsg) streamMsg.textContent = data.message;

        if (ticket) {
            if (data.hasTicket) {
                ticket.classList.remove('hidden');
                const tTitle = document.getElementById('detailTicketTitle');
                const tSub = document.getElementById('detailTicketSubtitle');
                if (tTitle && data.ticketTitle) tTitle.textContent = data.ticketTitle;
                if (tSub && data.ticketSub) tSub.textContent = data.ticketSub;
            } else {
                ticket.classList.add('hidden');
            }
        }

        const listView = document.getElementById('popupChatListView');
        const detailView = document.getElementById('popupChatDetailView');
        if (listView && detailView) {
            listView.classList.add('hidden');
            detailView.classList.remove('hidden');
            detailView.classList.add('flex');
        }

        // Auto open popup if hidden
        const popup = document.getElementById('floatingChatPopup');
        if (popup && popup.classList.contains('hidden')) {
            popup.classList.remove('hidden');
        }
    }

    function toggleFloatingChat() {
        const popup = document.getElementById('floatingChatPopup');
        if (popup) {
            popup.classList.toggle('hidden');
            if (!popup.classList.contains('hidden')) {
                const input = document.getElementById('popupChatInput');
                if (input) input.focus();
            }
        }
    }

    function sendPopupReply(text) {
        const input = document.getElementById('popupChatInput');
        if (input) {
            input.value = text;
            input.focus();
        }
    }

    function handlePopupChatSend(e) {
        e.preventDefault();
        const input = document.getElementById('popupChatInput');
        const text = input ? input.value.trim() : '';
        if (!text) return;

        const stream = document.getElementById('popupChatStream');
        if (!stream) return;

        const userInitial = "{{ strtoupper(substr(Auth::user()->name ?? 'U', 0, 2)) }}";
        const bubble = document.createElement('div');
        bubble.className = 'flex items-start justify-end gap-2 ml-auto max-w-[85%] animate-page-enter';
        bubble.innerHTML = `
            <div class="bg-teal-700 text-white p-2.5 rounded-2xl rounded-tr-sm shadow-2xs space-y-1 text-left">
                <p class="leading-relaxed">${text.replace(/</g, "&lt;").replace(/>/g, "&gt;")}</p>
                <span class="text-[8px] text-teal-200 block text-right">Baru saja</span>
            </div>
            <div class="w-6 h-6 rounded-lg bg-slate-800 text-white font-bold flex items-center justify-center text-[10px] shrink-0 mt-0.5">
                ${userInitial}
            </div>
        `;
        stream.appendChild(bubble);
        input.value = '';
        stream.scrollTop = stream.scrollHeight;

        // Realistic Simulated Reply after 1.2 seconds
        setTimeout(() => {
            const replyBubble = document.createElement('div');
            replyBubble.className = 'flex items-start gap-2 max-w-[85%] animate-page-enter';
            replyBubble.innerHTML = `
                <div class="w-6 h-6 rounded-lg bg-teal-700 text-white font-bold flex items-center justify-center text-[10px] shrink-0 mt-0.5">
                    ✓
                </div>
                <div class="bg-white border border-slate-200 p-2.5 rounded-2xl rounded-tl-sm shadow-2xs space-y-1 text-left">
                    <p class="text-slate-800 leading-relaxed text-xs">Pesan Anda telah diterima. Kami akan menindaklanjuti segera.</p>
                    <span class="text-[8px] text-slate-400 block text-right">Baru saja</span>
                </div>
            `;
            stream.appendChild(replyBubble);
            stream.scrollTop = stream.scrollHeight;
        }, 1200);
    }
</script>
@endauth
