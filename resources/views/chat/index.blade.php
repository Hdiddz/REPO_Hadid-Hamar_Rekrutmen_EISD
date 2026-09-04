@extends(match(Auth::user()->role ?? 'jobseeker') {
    'admin' => 'layouts.admin',
    'employer' => 'layouts.employer',
    default => 'layouts.app',
})

@php
    $currentRole = Auth::user()->role ?? 'jobseeker';
    $userName = Auth::user()->name ?? 'Pengguna';
    $userInitials = strtoupper(substr($userName, 0, 2));
@endphp

@section('title', 'Pesan & Saluran Komunikasi | KerjaLokal')

@section('portal_icon', 'forum')
@section('portal_context', match($currentRole) {
    'admin' => 'Pusat tata kelola sistem',
    'employer' => 'Portal operasional mitra',
    default => 'KerjaLokal',
})
@section('portal_title', 'Pesan & Obrolan Real-time')
@section('portal_description', 'Komunikasi langsung dan transparan antara Mitra UMKM, Pencari Kerja, dan Administrator.')

@push('styles')
<style>
    html, body {
        height: 100% !important;
        max-height: 100dvh !important;
        overflow: hidden !important;
        overscroll-behavior: none !important;
    }
</style>
@endpush

@section('content')
<div class="w-full h-full flex-1 min-h-0 flex flex-col overflow-hidden">
    
    <!-- Compact Top Bar Header for All Roles -->
    <div class="mb-2 sm:mb-2.5 flex items-center justify-between gap-3 shrink-0">
        <div class="flex items-center gap-2 sm:gap-2.5 min-w-0">
            <h1 class="text-lg sm:text-xl font-bold text-slate-950 dark:text-white tracking-tight truncate">
                Pesan &amp; Obrolan Kerja
            </h1>
            <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-teal-50 text-teal-800 dark:bg-teal-950 dark:text-teal-300 border border-teal-200/70 dark:border-teal-900 shrink-0">
                @if($currentRole === 'employer')
                    Mitra UMKM
                @elseif($currentRole === 'admin')
                    Administrator
                @else
                    Pencari Kerja
                @endif
            </span>
            <p class="hidden md:block text-xs text-slate-500 dark:text-slate-400 truncate">
                · Koordinasi langsung dan transparan terkait proses kerja
            </p>
        </div>
        <div class="flex items-center gap-2 shrink-0">
            <button type="button" onclick="exitFullscreenChat()" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 text-xs font-bold border border-slate-200 dark:border-slate-700 transition cursor-pointer shadow-2xs" title="Keluar dari Halaman Penuh">
                <span class="material-symbols-outlined text-[17px]">close_fullscreen</span>
                <span class="hidden sm:inline">Keluar dari Halaman Penuh</span>
                <span class="sm:hidden">Keluar</span>
            </button>
        </div>
    </div>

    <!-- Fixed Mode Split Chat Container -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl sm:rounded-3xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden flex flex-col lg:flex-row flex-1 min-h-0 w-full">
        
        <!-- Left Pane: Conversation List -->
        <div id="chatLeftPane" class="w-full lg:w-80 xl:w-96 border-b lg:border-b-0 lg:border-r border-slate-200 dark:border-slate-800 flex flex-col h-full bg-slate-50/50 dark:bg-slate-950/40 shrink-0 overflow-hidden">
            <!-- Search bar -->
            <div class="p-3 sm:p-3.5 border-b border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 shrink-0">
                <div class="flex items-center bg-slate-100 dark:bg-slate-800 rounded-xl px-3 py-2 text-xs focus-within:bg-white dark:focus-within:bg-slate-950 focus-within:ring-2 focus-within:ring-teal-600/20 focus-within:border-teal-600 border border-transparent transition-all">
                    <span class="material-symbols-outlined text-slate-400 text-base mr-2">search</span>
                    <input type="text" id="chatSearchInput" oninput="filterConversations()" placeholder="Cari kontak obrolan..." class="w-full bg-transparent border-none outline-none text-slate-900 dark:text-white placeholder:text-slate-400 p-0 text-base sm:text-xs">
                </div>
            </div>

            <!-- Chat Thread Items (Dynamic) -->
            <div class="flex-1 overflow-y-auto divide-y divide-slate-100 dark:divide-slate-800/60" id="chatThreadList">
                <div class="p-8 text-center flex flex-col items-center justify-center text-slate-400 my-auto h-full">
                    <span class="material-symbols-outlined text-4xl mb-2 text-slate-300">chat_bubble_outline</span>
                    <p class="text-xs font-bold text-slate-700 dark:text-slate-300">Belum ada obrolan</p>
                    <p class="text-[11px] text-slate-400 mt-1 max-w-[200px]">Daftar kontak percakapan Anda akan muncul di sini.</p>
                </div>
            </div>
        </div>

        <!-- Right Pane: Active Chat Conversation -->
        <div id="chatRightPane" class="hidden lg:flex flex-1 flex-col h-full bg-white dark:bg-slate-900 min-w-0 overflow-hidden">
            
            <!-- Chat Window Header -->
            <div class="p-3 sm:p-4 border-b border-slate-200 dark:border-slate-800 flex items-center justify-between bg-white dark:bg-slate-900 shrink-0" id="activeChatHeader">
                <div class="flex items-center gap-2 sm:gap-3 min-w-0 flex-1">
                    <button type="button" onclick="showChatContactList()" class="lg:hidden p-1.5 -ml-1 text-slate-500 hover:text-slate-800 dark:text-slate-400 dark:hover:text-white rounded-xl hover:bg-slate-100 dark:hover:bg-slate-850 transition cursor-pointer shrink-0" aria-label="Kembali ke kontak">
                        <span class="material-symbols-outlined text-[22px]">arrow_back</span>
                    </button>
                    <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-2xl bg-teal-700 text-white font-bold flex items-center justify-center text-xs sm:text-sm shrink-0" id="activeChatAvatar">
                        --
                    </div>
                    <div class="min-w-0 flex-1">
                        <div class="flex items-center gap-1.5 sm:gap-2 min-w-0">
                            <span class="font-bold text-xs sm:text-sm text-slate-900 dark:text-white truncate" id="activeChatName">Pilih percakapan</span>
                            <span class="px-1.5 sm:px-2 py-0.5 rounded bg-teal-50 dark:bg-teal-950 text-teal-800 dark:text-teal-300 text-[9px] sm:text-[10px] font-bold border border-teal-200 dark:border-teal-800 hidden shrink-0" id="activeChatRoleBadge"></span>
                        </div>
                        <p class="text-[10px] sm:text-[11px] text-slate-500 dark:text-slate-400 flex items-center gap-1.5 truncate" id="activeChatStatus">
                            <span class="w-1.5 h-1.5 rounded-full bg-slate-300 shrink-0"></span>
                            <span class="truncate">Tidak ada obrolan aktif</span>
                        </p>
                    </div>
                </div>

                <div class="flex items-center gap-1.5 sm:gap-2 shrink-0">
                    <!-- View Profile Button -->
                    <button type="button" id="chatViewProfileBtn" onclick="openChatProfileModal()" class="hidden inline-flex items-center gap-1.5 px-2.5 sm:px-3 py-1.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 hover:bg-slate-100 dark:bg-slate-800 dark:hover:bg-slate-750 text-slate-700 dark:text-slate-200 text-xs font-semibold shadow-2xs transition cursor-pointer" title="Lihat Profil Lengkap">
                        <span class="material-symbols-outlined text-[17px] text-teal-600 dark:text-teal-400">badge</span>
                        <span class="hidden sm:inline">Lihat Profil</span>
                    </button>

                    <!-- Hamburger / Kebab Menu for Chat Actions -->
                    <div class="relative" id="chatActionMenuContainer">
                        <button type="button" id="chatActionMenuBtn" onclick="toggleChatActionMenu(event)" class="hidden p-2 text-slate-500 hover:text-slate-800 dark:text-slate-400 dark:hover:text-white rounded-xl hover:bg-slate-100 dark:hover:bg-slate-800 transition cursor-pointer" title="Opsi Percakapan" aria-label="Opsi Percakapan">
                            <span class="material-symbols-outlined text-[20px]">more_vert</span>
                        </button>
                        <!-- Dropdown Options -->
                        <div id="chatActionMenuDropdown" class="hidden absolute right-0 mt-1.5 w-48 sm:w-52 bg-white dark:bg-slate-850 rounded-2xl shadow-xl border border-slate-200/80 dark:border-slate-700/80 py-1.5 z-30 animate-page-enter">
                            <button type="button" id="chatMenuProfileOption" onclick="openChatProfileModal(); toggleChatActionMenu();" class="w-full text-left px-3.5 py-2.5 text-xs text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800 flex items-center gap-2.5 transition cursor-pointer">
                                <span class="material-symbols-outlined text-[17px] text-teal-600 dark:text-teal-400">badge</span>
                                <span>Lihat Profil Lawan Bicara</span>
                            </button>
                            <div class="my-1 border-t border-slate-100 dark:border-slate-700/60"></div>
                            <button type="button" onclick="confirmClearChat(); toggleChatActionMenu();" class="w-full text-left px-3.5 py-2.5 text-xs text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800 flex items-center gap-2.5 transition cursor-pointer">
                                <span class="material-symbols-outlined text-[17px] text-slate-400">delete_sweep</span>
                                <span>Bersihkan Obrolan</span>
                            </button>
                            <div class="my-1 border-t border-slate-100 dark:border-slate-700/60"></div>
                            <button type="button" onclick="confirmRemovePerson(); toggleChatActionMenu();" class="w-full text-left px-3.5 py-2.5 text-xs text-rose-600 dark:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-950/40 flex items-center gap-2.5 transition cursor-pointer">
                                <span class="material-symbols-outlined text-[17px] text-rose-500">person_remove</span>
                                <span>Hapus Kontak Percakapan</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            @if(Auth::user()?->role === 'employer')
                <!-- Pending Resignation Banner for Employer -->
                <div id="chatResignationBanner" class="hidden border-b border-rose-200 dark:border-rose-900/60 bg-rose-50/95 dark:bg-rose-950/50 p-3 sm:p-3.5 px-4 flex-col sm:flex-row sm:items-center justify-between gap-3 text-xs shrink-0 animate-page-enter">
                    <div class="flex items-start sm:items-center gap-2.5 min-w-0">
                        <div class="w-8 h-8 rounded-xl bg-rose-100 text-rose-700 dark:bg-rose-900/60 dark:text-rose-300 flex items-center justify-center shrink-0">
                            <span class="material-symbols-outlined text-[18px]">exit_to_app</span>
                        </div>
                        <div class="min-w-0">
                            <div class="flex items-center gap-2 flex-wrap">
                                <strong class="font-bold text-slate-900 dark:text-white" id="chatResignJobTitle">Pengajuan Resign Masuk</strong>
                                <span class="rounded-md bg-rose-200/80 dark:bg-rose-900/80 px-1.5 py-0.2 text-[10px] font-bold text-rose-800 dark:text-rose-200">Perlu Tanggapan</span>
                            </div>
                            <p class="text-[11px] text-slate-600 dark:text-slate-300 mt-0.5" id="chatResignDetails">
                                Efektif: - · Alasan: -
                            </p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2 shrink-0 self-end sm:self-center">
                        <button type="button" id="chatResignApproveBtn" class="inline-flex items-center gap-1 rounded-xl bg-emerald-600 px-3 py-1.5 text-xs font-bold text-white shadow-sm hover:bg-emerald-700 active:translate-y-px transition cursor-pointer">
                            <span class="material-symbols-outlined text-[15px]">check</span>
                            Setujui Resign
                        </button>
                        <button type="button" id="chatResignRejectBtn" class="inline-flex items-center gap-1 rounded-xl bg-rose-600 px-3 py-1.5 text-xs font-bold text-white shadow-sm hover:bg-rose-700 active:translate-y-px transition cursor-pointer">
                            <span class="material-symbols-outlined text-[15px]">close</span>
                            Tolak
                        </button>
                    </div>
                </div>
            @endif

            <!-- Active Interview Invitation Banner (For Both Applicant & Employer) -->
            <div id="chatInterviewBanner" class="hidden border-b border-indigo-200 dark:border-indigo-900/60 bg-indigo-50/95 dark:bg-indigo-950/60 p-3 sm:p-3.5 px-4 flex-col sm:flex-row sm:items-center justify-between gap-3 text-xs shrink-0 animate-page-enter">
                <div class="flex items-start sm:items-center gap-2.5 min-w-0">
                    <div class="w-8 h-8 rounded-xl bg-indigo-100 text-indigo-700 dark:bg-indigo-900/60 dark:text-indigo-300 flex items-center justify-center shrink-0 mt-0.5 sm:mt-0">
                        <span class="material-symbols-outlined text-[18px]">event_available</span>
                    </div>
                    <div class="min-w-0">
                        <div class="flex items-center gap-2 flex-wrap">
                            <strong class="font-bold text-slate-900 dark:text-white" id="chatInterviewJobTitle">Undangan Wawancara Kerja</strong>
                            <span id="chatInterviewStatusBadge" class="rounded-full px-2 py-0.2 text-[10px] font-bold border">Status</span>
                        </div>
                        <div class="text-[11px] text-slate-600 dark:text-slate-300 mt-0.5 flex flex-wrap items-center gap-x-2.5 gap-y-0.5" id="chatInterviewDetails">
                            <!-- Dynamic Schedule Info -->
                        </div>
                        <p class="text-[10px] text-slate-500 dark:text-slate-400 italic line-clamp-1 mt-0.5" id="chatInterviewNotes"></p>
                    </div>
                </div>
                <div class="flex items-center gap-1.5 shrink-0 self-end sm:self-center flex-wrap" id="chatInterviewActionContainer">
                    <!-- Action buttons dynamically inserted by JS -->
                </div>
            </div>

            <!-- Messages Scroll Area (Fixed & Independent Scroll) -->
            <div class="flex-1 p-4 sm:p-6 overflow-y-auto space-y-3.5 bg-slate-50/40 dark:bg-slate-950/30" id="messageContainer">
                <div class="h-full flex flex-col items-center justify-center text-slate-400 my-auto text-center p-8">
                    <span class="material-symbols-outlined text-5xl text-slate-300 mb-3">forum</span>
                    <p class="font-bold text-slate-700 dark:text-slate-300 text-sm">Pilih obrolan di sebelah kiri</p>
                    <p class="text-xs text-slate-400 mt-1 max-w-xs">Anda dapat saling berkirim pesan secara real-time antar sesama pengguna.</p>
                </div>
            </div>

            <!-- Reply Preview Strip -->
            <div id="replyPreviewBar" class="hidden px-4 py-2 bg-teal-50/90 dark:bg-slate-800/90 border-t border-teal-100 dark:border-slate-700/80 flex items-center justify-between gap-3 text-xs animate-page-enter">
                <div class="flex items-center gap-2.5 min-w-0 border-l-4 border-teal-600 pl-2.5">
                    <span class="material-symbols-outlined text-teal-700 dark:text-teal-400 text-base shrink-0">reply</span>
                    <div class="min-w-0">
                        <p class="font-bold text-teal-900 dark:text-teal-200 text-[11px] truncate" id="replyPreviewSender">Membalas</p>
                        <p class="text-slate-600 dark:text-slate-400 text-[11px] truncate max-w-md" id="replyPreviewSnippet">Teks pesan...</p>
                    </div>
                </div>
                <button type="button" onclick="cancelReply()" class="p-1 hover:bg-slate-200/70 dark:hover:bg-slate-700 rounded-lg text-slate-400 hover:text-slate-700 dark:hover:text-slate-200 transition-colors cursor-pointer" title="Batal membalas">
                    <span class="material-symbols-outlined text-[17px]">close</span>
                </button>
            </div>

            <!-- Chat Input Strip (Fixed at Bottom) -->
            <div class="p-3 sm:p-4 border-t border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 shrink-0">
                <form id="mainChatForm" onsubmit="handleSendMessage(event)" class="flex items-center gap-2">
                    <input type="text" id="chatInput" placeholder="Ketik pesan..." class="flex-1 py-2.5 px-4 bg-slate-100 dark:bg-slate-800 focus:bg-white dark:focus:bg-slate-950 rounded-2xl border border-transparent focus:border-teal-600 focus:ring-2 focus:ring-teal-600/15 text-base sm:text-xs text-slate-900 dark:text-white outline-none transition-all" autocomplete="off" disabled required>
                    <button type="submit" id="chatSendBtn" class="px-5 py-2.5 bg-teal-700 hover:bg-teal-800 disabled:opacity-40 disabled:cursor-not-allowed active:scale-95 text-white font-bold text-xs rounded-2xl shadow-xs transition-all flex items-center gap-1.5 cursor-pointer" disabled>
                        <span>Kirim</span>
                        <span class="material-symbols-outlined text-sm">send</span>
                    </button>
                </form>
            </div>

        </div>

    </div>
</div>

@if(Auth::user()?->role === 'employer')
    <x-resign-decision-modal />
@endif

<!-- In-Chat Profile Viewer Modal -->
<div id="chatProfileModal" class="fixed inset-0 z-[150] hidden bg-slate-950/60 backdrop-blur-xs p-3 sm:p-6 overflow-y-auto flex items-center justify-center" role="dialog" aria-modal="true">
    <div class="relative w-full max-w-xl bg-white dark:bg-slate-900 rounded-3xl shadow-2xl border border-slate-200 dark:border-slate-800 p-5 sm:p-6 my-auto overflow-hidden animate-page-enter">
        <button type="button" onclick="closeChatProfileModal()" class="absolute top-4 right-4 p-2 text-slate-400 hover:text-slate-700 dark:hover:text-slate-200 rounded-xl hover:bg-slate-100 dark:hover:bg-slate-800 transition cursor-pointer" title="Tutup">
            <span class="material-symbols-outlined text-[20px]">close</span>
        </button>

        <div id="chatProfileModalContent">
            <!-- Dynamically populated by openChatProfileModal() -->
        </div>
    </div>
</div>

<!-- Modal Respon Wawancara (Approval, Diskusi, Tolak) -->
<div id="chatInterviewActionModal" class="fixed inset-0 z-[160] hidden bg-slate-950/60 backdrop-blur-xs p-4 overflow-y-auto flex items-center justify-center" role="dialog" aria-modal="true">
    <div class="relative w-full max-w-md bg-white dark:bg-slate-900 rounded-3xl shadow-2xl border border-slate-200 dark:border-slate-800 p-6 my-auto animate-page-enter">
        <div class="flex items-center gap-3 min-w-0">
            <div class="w-10 h-10 rounded-2xl flex items-center justify-center shrink-0" id="interviewModalIconContainer">
                <span class="material-symbols-outlined text-[22px]" id="interviewModalIcon">event_available</span>
            </div>
            <div class="min-w-0 flex-1">
                <h3 class="text-base font-bold text-slate-900 dark:text-white truncate" id="interviewModalHeading">Konfirmasi Wawancara</h3>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5 break-words" id="interviewModalDesc">Tanggapi jadwal wawancara yang diajukan mitra.</p>
            </div>
        </div>

        <form id="chatInterviewResponseForm" onsubmit="submitInterviewResponse(event)" class="mt-5 space-y-4">
            <input type="hidden" id="interviewModalAction" name="action" value="">
            <div>
                <label for="interviewModalNote" class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1.5" id="interviewModalNoteLabel">
                    Pesan / Catatan Tambahan (Opsional)
                </label>
                <textarea id="interviewModalNote" name="note" rows="3" class="w-full text-xs rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/80 p-3 text-slate-900 dark:text-white focus:ring-2 focus:ring-teal-600/20 focus:border-teal-600 outline-none transition" placeholder="Tuliskan alasan atau catatan Anda..."></textarea>
            </div>
            <div class="flex items-center justify-end gap-2 pt-2">
                <button type="button" onclick="closeInterviewActionModal()" class="portal-button-secondary text-xs !py-2 !px-3.5">
                    Batal
                </button>
                <button type="submit" id="interviewModalSubmitBtn" class="portal-button-primary text-xs !py-2 !px-4 gap-1.5">
                    <span class="material-symbols-outlined text-[16px]">send</span>
                    <span id="interviewModalSubmitText">Kirim Tanggapan</span>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    let activeUserId = {{ $activeUserId ? (int) $activeUserId : 'null' }};
    let activeUserName = '';
    let allConversations = [];
    let pollMessagesTimer = null;
    let pollConversationsTimer = null;
    let currentReply = null;
    let currentPendingResignation = null;
    let currentInterviewInvitation = null;
    let currentProfile = null;
    let currentOtherUser = null;
    let activeInterviewApplicationId = null;
    const isEmployer = {{ Auth::user()?->role === 'employer' ? 'true' : 'false' }};
    const employerName = {{ Js::from(Auth::user()?->business_name ?: Auth::user()?->name) }};
    const currentUserInitials = {{ Js::from($userInitials) }};
    const csrfToken = {{ Js::from(csrf_token()) }};

    function exitFullscreenChat() {
        if (window.history.length > 1) {
            window.history.back();
        } else {
            window.location.href = "{{ match($currentRole) { 'employer' => route('employer.dashboard'), 'admin' => route('admin.dashboard'), default => route('jobs.index') } }}";
        }
    }

    function toggleChatActionMenu(event) {
        if (event) {
            event.stopPropagation();
        }
        const dropdown = document.getElementById('chatActionMenuDropdown');
        if (dropdown) {
            dropdown.classList.toggle('hidden');
        }
    }

    document.addEventListener('click', (event) => {
        const container = document.getElementById('chatActionMenuContainer');
        const dropdown = document.getElementById('chatActionMenuDropdown');
        if (dropdown && !dropdown.classList.contains('hidden')) {
            if (!container || !container.contains(event.target)) {
                dropdown.classList.add('hidden');
            }
        }
    });

    async function loadConversations() {
        try {
            const url = activeUserId ? `/chat/conversations?with=${activeUserId}` : '/chat/conversations';
            const res = await fetch(url, { headers: { 'Accept': 'application/json' } });
            if (!res.ok) return;
            allConversations = await res.json();
            renderConversations(allConversations);

            if (activeUserId && !pollMessagesTimer) {
                const target = allConversations.find(c => c.id === activeUserId);
                if (target) {
                    selectConversation(target.id, target.name, target.role_label, target.initials);
                } else {
                    selectConversation(activeUserId, 'Kontak Obrolan', 'Kontak', 'KL');
                }
            }
        } catch (e) {
            console.error(e);
        }
    }

    function renderConversations(conversations) {
        const list = document.getElementById('chatThreadList');
        if (!list) return;

        if (!conversations || conversations.length === 0) {
            list.innerHTML = `
                <div class="p-8 text-center flex flex-col items-center justify-center text-slate-400 my-auto h-full">
                    <span class="material-symbols-outlined text-4xl mb-2 text-slate-300">chat_bubble_outline</span>
                    <p class="text-xs font-bold text-slate-700 dark:text-slate-300">Belum ada obrolan</p>
                    <p class="text-[11px] text-slate-400 mt-1 max-w-[200px]">Daftar kontak percakapan Anda akan muncul di sini.</p>
                </div>
            `;
            return;
        }

        list.innerHTML = conversations.map(user => {
            const isActive = user.id === activeUserId;
            return `
                <div data-conversation-id="${user.id}"
                     class="group relative p-3.5 sm:p-4 cursor-pointer transition-all flex items-start gap-3 ${isActive ? 'bg-white dark:bg-slate-900 border-l-4 border-teal-600' : 'bg-transparent hover:bg-white dark:hover:bg-slate-850'}">
                    <div class="w-10 h-10 rounded-2xl bg-teal-100 text-teal-800 dark:bg-teal-950 dark:text-teal-200 font-bold flex items-center justify-center shrink-0 text-xs">
                        ${escapeHtml(user.initials)}
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center justify-between gap-1 mb-0.5">
                            <span class="font-bold text-xs text-slate-900 dark:text-white truncate">${escapeHtml(user.name)}</span>
                            <span class="text-[10px] text-teal-700 dark:text-teal-400 font-semibold shrink-0">${user.last_time || ''}</span>
                        </div>
                        <span class="inline-block text-[10px] font-bold text-teal-800 dark:text-teal-300 bg-teal-50 dark:bg-teal-950/70 px-1.5 py-0.5 rounded mb-1">
                            ${escapeHtml(user.role_label)}
                        </span>
                        <p class="text-xs text-slate-500 dark:text-slate-400 truncate">
                            ${user.last_message ? escapeHtml(user.last_message) : '<em>Mulai obrolan...</em>'}
                        </p>
                    </div>
                    <div class="flex items-center gap-1 shrink-0">
                        ${user.unread_count > 0 ? '<span class="w-2 h-2 rounded-full bg-rose-500 shrink-0 mt-2"></span>' : ''}
                        <button type="button" data-remove-conversation-id="${user.id}" class="opacity-0 group-hover:opacity-100 p-1 text-slate-400 hover:text-rose-600 rounded-lg transition-opacity cursor-pointer" title="Hapus kontak ini dari daftar">
                            <span class="material-symbols-outlined text-[17px]">person_remove</span>
                        </button>
                    </div>
                </div>
            `;
        }).join('');

        list.querySelectorAll('[data-conversation-id]').forEach(item => {
            item.addEventListener('click', () => {
                const user = conversations.find(conversation => conversation.id === Number(item.dataset.conversationId));
                if (user) {
                    selectConversation(user.id, user.name, user.role_label, user.initials);
                }
            });
        });

        list.querySelectorAll('[data-remove-conversation-id]').forEach(button => {
            button.addEventListener('click', event => {
                event.stopPropagation();
                const user = conversations.find(conversation => conversation.id === Number(button.dataset.removeConversationId));
                if (user) {
                    removePersonConversation(user.id, user.name);
                }
            });
        });
    }

    function filterConversations() {
        const q = (document.getElementById('chatSearchInput')?.value || '').toLowerCase();
        if (!q) {
            renderConversations(allConversations);
            return;
        }
        const filtered = allConversations.filter(c => c.name.toLowerCase().includes(q) || (c.role_label && c.role_label.toLowerCase().includes(q)));
        renderConversations(filtered);
    }

    function showChatContactList() {
        if (pollMessagesTimer) {
            clearInterval(pollMessagesTimer);
            pollMessagesTimer = null;
        }
        activeUserId = null;
        activeUserName = '';
        cancelReply();
        if (window.history.replaceState) {
            window.history.replaceState({}, '', '{{ route("chat.index") }}');
        }
        const leftPane = document.getElementById('chatLeftPane');
        const rightPane = document.getElementById('chatRightPane');
        if (leftPane) leftPane.classList.remove('hidden');
        if (rightPane) {
            rightPane.classList.add('hidden');
            rightPane.classList.remove('flex');
        }
    }

    async function selectConversation(userId, name, roleLabel, initials) {
        activeUserId = Number(userId);
        activeUserName = name;
        cancelReply();

        if (window.innerWidth < 1024) {
            const leftPane = document.getElementById('chatLeftPane');
            const rightPane = document.getElementById('chatRightPane');
            if (leftPane) leftPane.classList.add('hidden');
            if (rightPane) {
                rightPane.classList.remove('hidden');
                rightPane.classList.add('flex');
            }
        }

        const rightPane = document.getElementById('chatRightPane');
        if (rightPane) {
            rightPane.classList.remove('animate-chat-slide-right');
            void rightPane.offsetWidth;
            rightPane.classList.add('animate-chat-slide-right');
        }

        const avatar = document.getElementById('activeChatAvatar');
        const nameEl = document.getElementById('activeChatName');
        const badge = document.getElementById('activeChatRoleBadge');
        const status = document.getElementById('activeChatStatus');
        const input = document.getElementById('chatInput');
        const sendBtn = document.getElementById('chatSendBtn');
        const actionMenuBtn = document.getElementById('chatActionMenuBtn');
        const actionMenuDropdown = document.getElementById('chatActionMenuDropdown');

        if (avatar) avatar.textContent = initials || (name ? name.substring(0, 2).toUpperCase() : 'KL');
        if (nameEl) nameEl.textContent = name;
        if (badge) {
            badge.textContent = roleLabel;
            badge.classList.remove('hidden');
        }
        if (status) {
            status.innerHTML = `
                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                <span>Online</span>
            `;
        }

        if (actionMenuDropdown) actionMenuDropdown.classList.add('hidden');
        if (actionMenuBtn) actionMenuBtn.classList.remove('hidden');

        if (input) {
            input.disabled = false;
            input.placeholder = `Tulis pesan ke ${name}...`;
            if (window.innerWidth >= 1024) {
                input.focus();
            }
        }
        if (sendBtn) sendBtn.disabled = false;

        renderConversations(allConversations);

        const container = document.getElementById('messageContainer');
        if (container) container.innerHTML = '';

        await fetchMessages();

        if (pollMessagesTimer) clearInterval(pollMessagesTimer);
        pollMessagesTimer = setInterval(fetchMessages, 1500);
    }

    function renderMessageHTML(msg, otherUser) {
        if (msg.is_deleted) {
            if (msg.is_me) {
                return `
                    <div data-msg-id="${msg.id}" data-msg-deleted="true" class="relative flex items-start justify-end gap-1.5 sm:gap-2 ml-auto max-w-xl">
                        <div class="bg-slate-100 dark:bg-slate-800/90 border border-slate-200/80 dark:border-slate-700/60 text-slate-500 dark:text-slate-400 rounded-2xl rounded-tr-sm px-3.5 py-2.5 shadow-2xs text-xs space-y-1 text-left min-w-[90px] italic flex items-center gap-1.5">
                            <span class="material-symbols-outlined text-[15px] opacity-60 not-italic">block</span>
                            <span>Pesan ini telah dihapus</span>
                            <span class="text-[9px] text-slate-400 not-italic ml-2">${msg.time}</span>
                        </div>
                        <div class="w-8 h-8 rounded-xl bg-slate-800 text-white font-bold flex items-center justify-center text-xs shrink-0 mt-0.5 opacity-60">
                            ${escapeHtml(currentUserInitials)}
                        </div>
                    </div>
                `;
            } else {
                return `
                    <div data-msg-id="${msg.id}" data-msg-deleted="true" class="relative flex items-start gap-1.5 sm:gap-2 max-w-xl">
                        <div class="w-8 h-8 rounded-xl bg-teal-700 text-white font-bold flex items-center justify-center text-xs shrink-0 mt-0.5 opacity-60">
                            ${escapeHtml(otherUser?.initials || 'KL')}
                        </div>
                        <div class="bg-slate-100 dark:bg-slate-800/90 border border-slate-200/80 dark:border-slate-700/60 text-slate-500 dark:text-slate-400 rounded-2xl rounded-tl-sm px-3.5 py-2.5 shadow-2xs text-xs space-y-1 text-left min-w-[90px] italic flex items-center gap-1.5">
                            <span class="material-symbols-outlined text-[15px] opacity-60 not-italic">block</span>
                            <span>Pesan ini telah dihapus</span>
                            <span class="text-[9px] text-slate-400 not-italic ml-2">${msg.time}</span>
                        </div>
                    </div>
                `;
            }
        }

        let replyHtml = '';
        if (msg.reply_to) {
            if (msg.is_me) {
                replyHtml = `
                    <div class="mb-1.5 p-2 rounded-xl bg-black/15 dark:bg-black/30 border-l-3 border-teal-300 text-[11px] leading-snug">
                        <div class="font-bold text-teal-200 text-[10px] flex items-center gap-1">
                            <span class="material-symbols-outlined text-[13px]">reply</span>
                            <span>${escapeHtml(msg.reply_to.sender_name)}</span>
                        </div>
                        <p class="text-teal-50 truncate text-[10px] mt-0.5 opacity-90">${escapeHtml(msg.reply_to.message)}</p>
                    </div>
                `;
            } else {
                replyHtml = `
                    <div class="mb-1.5 p-2 rounded-xl bg-slate-100 dark:bg-slate-700/60 border-l-3 border-teal-600 text-[11px] leading-snug">
                        <div class="font-bold text-teal-800 dark:text-teal-400 text-[10px] flex items-center gap-1">
                            <span class="material-symbols-outlined text-[13px]">reply</span>
                            <span>${escapeHtml(msg.reply_to.sender_name)}</span>
                        </div>
                        <p class="text-slate-600 dark:text-slate-300 truncate text-[10px] mt-0.5">${escapeHtml(msg.reply_to.message)}</p>
                    </div>
                `;
            }
        }

        let resignActionBtns = '';
        if (!msg.is_me && isEmployer && currentPendingResignation && msg.message.includes('[Pengajuan Pengunduran Diri (Resign)]')) {
            resignActionBtns = `
                <div class="mt-2.5 pt-2 border-t border-slate-200 dark:border-slate-700 flex items-center gap-1.5 flex-wrap">
                    <button type="button" onclick="handleResignInChat('approved')" class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white text-[11px] font-bold shadow-xs transition cursor-pointer">
                        <span class="material-symbols-outlined text-[14px]">check</span>
                        Setujui Resign
                    </button>
                    <button type="button" onclick="handleResignInChat('rejected')" class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg bg-rose-600 hover:bg-rose-700 text-white text-[11px] font-bold shadow-xs transition cursor-pointer">
                        <span class="material-symbols-outlined text-[14px]">close</span>
                        Tolak
                    </button>
                </div>
            `;
        }

        if (msg.is_me) {
            return `
                <div data-msg-id="${msg.id}" class="group relative flex items-start justify-end gap-1.5 sm:gap-2 ml-auto max-w-xl">
                    <div class="opacity-100 sm:opacity-0 sm:group-hover:opacity-100 transition-opacity flex items-center gap-0.5 self-center shrink-0">
                        <button type="button" data-chat-action="reply" title="Balas pesan ini" class="p-1.5 text-slate-400 hover:text-teal-700 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-lg cursor-pointer">
                            <span class="material-symbols-outlined text-[16px]">reply</span>
                        </button>
                        <button type="button" data-chat-action="delete" title="Hapus pesan ini" class="p-1.5 text-slate-400 hover:text-rose-600 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-lg cursor-pointer">
                            <span class="material-symbols-outlined text-[16px]">delete</span>
                        </button>
                    </div>
                    <div class="bg-teal-700 text-white rounded-2xl rounded-tr-sm p-3 sm:p-3.5 shadow-2xs text-xs space-y-1 text-left min-w-[90px]">
                        ${replyHtml}
                        <p class="leading-relaxed whitespace-pre-line">${escapeHtml(msg.message)}</p>
                        <span class="text-[9px] text-teal-200 block text-right">${msg.time}</span>
                    </div>
                    <div class="w-8 h-8 rounded-xl bg-slate-800 text-white font-bold flex items-center justify-center text-xs shrink-0 mt-0.5">
                        ${escapeHtml(currentUserInitials)}
                    </div>
                </div>
            `;
        } else {
            return `
                <div data-msg-id="${msg.id}" class="group relative flex items-start gap-1.5 sm:gap-2 max-w-xl">
                    <div class="w-8 h-8 rounded-xl bg-teal-700 text-white font-bold flex items-center justify-center text-xs shrink-0 mt-0.5">
                        ${escapeHtml(otherUser?.initials || 'KL')}
                    </div>
                    <div class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl rounded-tl-sm p-3 sm:p-3.5 shadow-2xs text-xs space-y-1 text-left min-w-[90px]">
                        ${replyHtml}
                        <p class="text-slate-800 dark:text-slate-100 leading-relaxed whitespace-pre-line">${escapeHtml(msg.message)}</p>
                        ${resignActionBtns}
                        <span class="text-[9px] text-slate-400 block text-right">${msg.time}</span>
                    </div>
                    <div class="opacity-100 sm:opacity-0 sm:group-hover:opacity-100 transition-opacity flex items-center gap-0.5 self-center shrink-0">
                        <button type="button" data-chat-action="reply" title="Balas pesan ini" class="p-1.5 text-slate-400 hover:text-teal-700 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-lg cursor-pointer">
                            <span class="material-symbols-outlined text-[16px]">reply</span>
                        </button>
                    </div>
                </div>
            `;
        }
    }

    function bindMessageActions(element, message, otherUser) {
        if (message.is_deleted) return;
        element.querySelector('[data-chat-action="reply"]')?.addEventListener('click', () => {
            const senderName = message.is_me ? 'Anda' : otherUser.name;
            startReply(message.id, senderName, message.message);
        });
        element.querySelector('[data-chat-action="delete"]')?.addEventListener('click', () => {
            deleteMessage(message.id);
        });
    }

    window.handleResignInChat = function(decision) {
        if (!currentPendingResignation || !currentOtherUser) return;
        window.openResignDecisionModal({
            applicationId: currentPendingResignation.application_id,
            decision: decision,
            candidateName: currentOtherUser.name,
            jobTitle: currentPendingResignation.job_title,
            employerName: employerName,
            onSuccess: (res) => {
                fetchMessages();
            }
        });
    };

    async function fetchMessages() {
        if (!activeUserId) return;
        const container = document.getElementById('messageContainer');
        if (!container) return;

        try {
            const res = await fetch(`/chat/messages/${activeUserId}`, {
                headers: { 'Accept': 'application/json' }
            });
            if (!res.ok) return;
            const data = await res.json();

            currentPendingResignation = data.pending_resignation || null;
            currentInterviewInvitation = data.interview_invitation || null;
            currentProfile = data.profile || null;
            currentOtherUser = data.user || null;

            // Handle Contact Details Header Update if loaded dynamically
            if (data.user) {
                const nameEl = document.getElementById('activeChatName');
                const badge = document.getElementById('activeChatRoleBadge');
                const avatar = document.getElementById('activeChatAvatar');
                const input = document.getElementById('chatInput');
                const displayName = data.user.business_name || data.user.name;

                if (nameEl && (!activeUserName || activeUserName === 'Kontak Obrolan')) {
                    activeUserName = displayName;
                    nameEl.textContent = displayName;
                    if (input) input.placeholder = `Tulis pesan ke ${displayName}...`;
                }
                if (badge && data.user.role_label) {
                    badge.textContent = data.user.role_label;
                    badge.classList.remove('hidden');
                }
                if (avatar && (avatar.textContent === '--' || avatar.textContent === 'KL')) {
                    avatar.textContent = data.user.initials || displayName.substring(0, 2).toUpperCase();
                }

                if (!allConversations.some(c => c.id === data.user.id)) {
                    allConversations.unshift({
                        id: data.user.id,
                        name: data.user.name,
                        business_name: data.user.business_name,
                        role: data.user.role,
                        role_label: data.user.role_label,
                        initials: data.user.initials,
                        last_message: null,
                        last_time: null,
                        unread_count: 0
                    });
                    renderConversations(allConversations);
                }
            }

            // Handle Profile Button in Chat Header
            const profileBtn = document.getElementById('chatViewProfileBtn');
            if (profileBtn) {
                if (currentProfile) {
                    profileBtn.classList.remove('hidden');
                } else {
                    profileBtn.classList.add('hidden');
                }
            }

            // Handle Resignation Banner
            const resignBanner = document.getElementById('chatResignationBanner');
            if (resignBanner) {
                if (data.pending_resignation) {
                    resignBanner.classList.remove('hidden');
                    resignBanner.classList.add('flex');
                    const jobTitleEl = document.getElementById('chatResignJobTitle');
                    if (jobTitleEl) jobTitleEl.textContent = `Pengajuan Resign: ${data.pending_resignation.job_title}`;
                    const detailsEl = document.getElementById('chatResignDetails');
                    if (detailsEl) {
                        detailsEl.textContent = `Efektif: ${data.pending_resignation.resignation_date} · Alasan: ${data.pending_resignation.resignation_reason}` + (data.pending_resignation.resignation_notes ? ` ("${data.pending_resignation.resignation_notes}")` : '');
                    }

                    const approveBtn = document.getElementById('chatResignApproveBtn');
                    if (approveBtn) {
                        approveBtn.onclick = () => handleResignInChat('approved');
                    }
                    const rejectBtn = document.getElementById('chatResignRejectBtn');
                    if (rejectBtn) {
                        rejectBtn.onclick = () => handleResignInChat('rejected');
                    }
                } else {
                    resignBanner.classList.add('hidden');
                    resignBanner.classList.remove('flex');
                }
            }

            // Handle Interview Banner
            const interviewBanner = document.getElementById('chatInterviewBanner');
            if (interviewBanner) {
                if (data.interview_invitation) {
                    interviewBanner.classList.remove('hidden');
                    interviewBanner.classList.add('flex');
                    renderInterviewBanner(data.interview_invitation);
                } else {
                    interviewBanner.classList.add('hidden');
                    interviewBanner.classList.remove('flex');
                }
            }

            const existingElements = Array.from(container.querySelectorAll('[data-msg-id]'));
            const existingIds = new Set(existingElements.map(el => parseInt(el.dataset.msgId)));
            const incomingIds = new Set(data.messages.map(m => m.id));

            // Remove deleted messages
            existingElements.forEach(el => {
                const id = parseInt(el.dataset.msgId);
                if (!incomingIds.has(id)) {
                    el.remove();
                }
            });

            if (data.messages.length === 0) {
                container.innerHTML = `
                    <div data-chat-placeholder class="h-full flex flex-col items-center justify-center text-slate-400 my-auto text-center p-8">
                        <span class="material-symbols-outlined text-4xl text-slate-300 mb-2">waving_hand</span>
                        <p class="font-bold text-slate-700 dark:text-slate-300 text-xs">Belum ada riwayat pesan</p>
                        <p class="text-[11px] text-slate-400 mt-1 max-w-xs">Kirim pesan pertama Anda untuk memulai percakapan.</p>
                    </div>
                `;
                return;
            }

            const isPlaceholder = container.querySelector('[data-chat-placeholder]');
            const isNearBottom = (container.scrollHeight - container.scrollTop - container.clientHeight) < 80;

            if (isPlaceholder || existingIds.size === 0) {
                container.innerHTML = '';
                data.messages.forEach(msg => {
                    const temp = document.createElement('div');
                    temp.innerHTML = renderMessageHTML(msg, data.user).trim();
                    const element = temp.firstElementChild;
                    bindMessageActions(element, msg, data.user);
                    container.appendChild(element);
                });
                container.scrollTop = container.scrollHeight;
            } else {
                let hasNew = false;
                let hasMyNew = false;
                data.messages.forEach(msg => {
                    if (existingIds.has(msg.id)) {
                        const existingEl = container.querySelector(`[data-msg-id="${msg.id}"]`);
                        if (existingEl && msg.is_deleted && !existingEl.dataset.msgDeleted) {
                            existingEl.dataset.msgDeleted = 'true';
                            if (msg.is_me) {
                                existingEl.className = 'relative flex items-start justify-end gap-1.5 sm:gap-2 ml-auto max-w-xl';
                                existingEl.innerHTML = `
                                    <div class="bg-slate-100 dark:bg-slate-800/90 border border-slate-200/80 dark:border-slate-700/60 text-slate-500 dark:text-slate-400 rounded-2xl rounded-tr-sm px-3.5 py-2.5 shadow-2xs text-xs space-y-1 text-left min-w-[90px] italic flex items-center gap-1.5">
                                        <span class="material-symbols-outlined text-[15px] opacity-60 not-italic">block</span>
                                        <span>Pesan ini telah dihapus</span>
                                        <span class="text-[9px] text-slate-400 not-italic ml-2">${msg.time}</span>
                                    </div>
                                    <div class="w-8 h-8 rounded-xl bg-slate-800 text-white font-bold flex items-center justify-center text-xs shrink-0 mt-0.5 opacity-60">
                                        ${escapeHtml(currentUserInitials)}
                                    </div>
                                `;
                            } else {
                                existingEl.className = 'relative flex items-start gap-1.5 sm:gap-2 max-w-xl';
                                existingEl.innerHTML = `
                                    <div class="w-8 h-8 rounded-xl bg-teal-700 text-white font-bold flex items-center justify-center text-xs shrink-0 mt-0.5 opacity-60">
                                        ${escapeHtml(data.user.initials)}
                                    </div>
                                    <div class="bg-slate-100 dark:bg-slate-800/90 border border-slate-200/80 dark:border-slate-700/60 text-slate-500 dark:text-slate-400 rounded-2xl rounded-tl-sm px-3.5 py-2.5 shadow-2xs text-xs space-y-1 text-left min-w-[90px] italic flex items-center gap-1.5">
                                        <span class="material-symbols-outlined text-[15px] opacity-60 not-italic">block</span>
                                        <span>Pesan ini telah dihapus</span>
                                        <span class="text-[9px] text-slate-400 not-italic ml-2">${msg.time}</span>
                                    </div>
                                `;
                            }
                        }
                    } else {
                        const temp = document.createElement('div');
                        temp.innerHTML = renderMessageHTML(msg, data.user).trim();
                        const el = temp.firstElementChild;
                        bindMessageActions(el, msg, data.user);
                        el.classList.add('animate-msg-popup');
                        container.appendChild(el);
                        hasNew = true;
                        if (msg.is_me) {
                            hasMyNew = true;
                        }
                    }
                });

                if (hasNew && (isNearBottom || hasMyNew)) {
                    container.scrollTo({ top: container.scrollHeight, behavior: 'smooth' });
                }
            }
        } catch (e) {
            console.error(e);
        }
    }

    function startReply(msgId, senderName, messageText) {
        currentReply = { id: msgId, name: senderName, text: messageText };
        const bar = document.getElementById('replyPreviewBar');
        const senderEl = document.getElementById('replyPreviewSender');
        const snippetEl = document.getElementById('replyPreviewSnippet');
        const input = document.getElementById('chatInput');

        if (senderEl) senderEl.textContent = `Membalas ke: ${senderName}`;
        if (snippetEl) snippetEl.textContent = messageText.length > 80 ? messageText.substring(0, 80) + '...' : messageText;
        if (bar) bar.classList.remove('hidden');
        if (input) input.focus();
    }

    function cancelReply() {
        currentReply = null;
        const bar = document.getElementById('replyPreviewBar');
        if (bar) bar.classList.add('hidden');
    }

    async function handleSendMessage(e) {
        e.preventDefault();
        const input = document.getElementById('chatInput');
        const text = (input?.value || '').trim();
        if (!text || !activeUserId) return;

        const replyId = currentReply ? currentReply.id : null;
        input.value = '';
        cancelReply();

        try {
            const res = await fetch(`/chat/messages/${activeUserId}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({
                    message: text,
                    reply_to_id: replyId
                })
            });
            if (res.ok) {
                await fetchMessages();
                loadConversations();
            }
        } catch (e) {
            console.error(e);
        }
    }

    async function deleteMessage(msgId) {
        const confirmed = await window.showAppConfirm({
            title: 'Hapus Pesan Obrolan?',
            message: 'Pesan akan dihapus dari obrolan ini.',
            confirmText: 'Ya, Hapus',
            type: 'danger',
            icon: 'delete'
        });
        if (!confirmed) return;

        try {
            const res = await fetch(`/chat/messages/${msgId}`, {
                method: 'DELETE',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                }
            });
            if (res.ok) {
                const el = document.querySelector(`[data-msg-id="${msgId}"]`);
                if (el) {
                    const timeSpan = el.querySelector('span.text-\\[9px\\]')?.textContent || '';
                    el.className = 'relative flex items-start justify-end gap-1.5 sm:gap-2 ml-auto max-w-xl';
                    el.dataset.msgDeleted = 'true';
                    el.innerHTML = `
                        <div class="bg-slate-100 dark:bg-slate-800/90 border border-slate-200/80 dark:border-slate-700/60 text-slate-500 dark:text-slate-400 rounded-2xl rounded-tr-sm px-3.5 py-2.5 shadow-2xs text-xs space-y-1 text-left min-w-[90px] italic flex items-center gap-1.5">
                            <span class="material-symbols-outlined text-[15px] opacity-60 not-italic">block</span>
                            <span>Pesan ini telah dihapus</span>
                            <span class="text-[9px] text-slate-400 not-italic ml-2">${timeSpan}</span>
                        </div>
                        <div class="w-8 h-8 rounded-xl bg-slate-800 text-white font-bold flex items-center justify-center text-xs shrink-0 mt-0.5 opacity-60">
                            ${escapeHtml(currentUserInitials)}
                        </div>
                    `;
                }
                loadConversations();
            }
        } catch (e) {
            console.error(e);
        }
    }

    async function confirmClearChat() {
        if (!activeUserId) return;
        const confirmed = await window.showAppConfirm({
            title: 'Bersihkan Seluruh Obrolan?',
            message: 'Seluruh riwayat pesan obrolan dengan pengguna ini akan dihapus secara permanen.',
            confirmText: 'Ya, Bersihkan',
            type: 'danger',
            icon: 'delete_sweep'
        });
        if (!confirmed) return;

        try {
            const res = await fetch(`/chat/clear/${activeUserId}`, {
                method: 'DELETE',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                }
            });
            if (res.ok) {
                const container = document.getElementById('messageContainer');
                if (container) {
                    container.innerHTML = `
                        <div class="h-full flex flex-col items-center justify-center text-slate-400 my-auto text-center p-8">
                            <span class="material-symbols-outlined text-4xl text-slate-300 mb-2">delete_sweep</span>
                            <p class="font-bold text-slate-700 dark:text-slate-300 text-xs">Riwayat obrolan telah dibersihkan</p>
                            <p class="text-[11px] text-slate-400 mt-1 max-w-xs">Mulai percakapan baru dengan mengirim pesan di bawah.</p>
                        </div>
                    `;
                }
                cancelReply();
                loadConversations();
            }
        } catch (e) {
            console.error(e);
        }
    }

    async function confirmRemovePerson() {
        if (!activeUserId) return;
        await removePersonConversation(activeUserId, activeUserName || 'pengguna ini');
    }

    async function removePersonConversation(userId, userName) {
        const confirmed = await window.showAppConfirm({
            title: 'Hapus Kontak Obrolan?',
            message: `Hapus ${userName} dari daftar kontak Anda? Riwayat percakapan akan dibersihkan.`,
            confirmText: 'Ya, Hapus Kontak',
            type: 'danger',
            icon: 'person_remove'
        });
        if (!confirmed) return;

        try {
            const res = await fetch(`/chat/conversations/${userId}`, {
                method: 'DELETE',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                }
            });
            if (res.ok) {
                if (activeUserId === userId) {
                    activeUserId = null;
                    activeUserName = '';
                    if (pollMessagesTimer) clearInterval(pollMessagesTimer);
                    pollMessagesTimer = null;
                    cancelReply();

                    // Reset right pane
                    document.getElementById('activeChatName').textContent = 'Pilih percakapan';
                    document.getElementById('activeChatAvatar').textContent = '--';
                    document.getElementById('activeChatRoleBadge').classList.add('hidden');
                    document.getElementById('activeChatStatus').innerHTML = `
                        <span class="w-1.5 h-1.5 rounded-full bg-slate-300"></span>
                        <span>Tidak ada obrolan aktif</span>
                    `;
                    const actionBtn = document.getElementById('chatActionMenuBtn');
                    if (actionBtn) actionBtn.classList.add('hidden');
                    const actionDropdown = document.getElementById('chatActionMenuDropdown');
                    if (actionDropdown) actionDropdown.classList.add('hidden');
                    document.getElementById('chatInput').disabled = true;
                    document.getElementById('chatInput').placeholder = 'Ketik pesan...';
                    document.getElementById('chatSendBtn').disabled = true;

                    const profileBtn = document.getElementById('chatViewProfileBtn');
                    if (profileBtn) profileBtn.classList.add('hidden');
                    const interviewBanner = document.getElementById('chatInterviewBanner');
                    if (interviewBanner) {
                        interviewBanner.classList.add('hidden');
                        interviewBanner.classList.remove('flex');
                    }
                    currentInterviewInvitation = null;
                    currentProfile = null;

                    document.getElementById('messageContainer').innerHTML = `
                        <div class="h-full flex flex-col items-center justify-center text-slate-400 my-auto text-center p-8">
                            <span class="material-symbols-outlined text-5xl text-slate-300 mb-3">forum</span>
                            <p class="font-bold text-slate-700 dark:text-slate-300 text-sm">Pilih obrolan di sebelah kiri</p>
                            <p class="text-xs text-slate-400 mt-1 max-w-xs">Anda dapat saling berkirim pesan secara real-time antar sesama pengguna.</p>
                        </div>
                    `;
                }
                loadConversations();
            }
        } catch (e) {
            console.error(e);
        }
    }

    function renderInterviewBanner(invitation) {
        const titleEl = document.getElementById('chatInterviewJobTitle');
        if (titleEl) {
            titleEl.textContent = `Wawancara: ${invitation.job_title || 'Posisi Lowongan'}`;
        }

        const badgeEl = document.getElementById('chatInterviewStatusBadge');
        if (badgeEl) {
            let badgeText = 'Menunggu Konfirmasi';
            let badgeClass = 'border-amber-300 bg-amber-100 text-amber-800 dark:border-amber-800 dark:bg-amber-950/60 dark:text-amber-300';
            let badgeIcon = 'schedule';

            if (invitation.interview_status === 'confirmed') {
                badgeText = 'Jadwal Disetujui Pelamar';
                badgeClass = 'border-emerald-300 bg-emerald-100 text-emerald-800 dark:border-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-300';
                badgeIcon = 'check_circle';
            } else if (invitation.interview_status === 'reschedule_requested') {
                badgeText = 'Pelamar Ajukan Diskusi Jadwal';
                badgeClass = 'border-sky-300 bg-sky-100 text-sky-800 dark:border-sky-800 dark:bg-sky-950/60 dark:text-sky-300';
                badgeIcon = 'edit_calendar';
            } else if (invitation.interview_status === 'declined') {
                badgeText = 'Wawancara Ditolak Pelamar';
                badgeClass = 'border-rose-300 bg-rose-100 text-rose-800 dark:border-rose-800 dark:bg-rose-950/60 dark:text-rose-300';
                badgeIcon = 'cancel';
            }

            badgeEl.className = `rounded-full px-2.5 py-0.5 text-[10px] font-bold border flex items-center gap-1 ${badgeClass}`;
            badgeEl.innerHTML = `<span class="material-symbols-outlined text-[12px]">${badgeIcon}</span> <span>${badgeText}</span>`;
        }

        const detailsEl = document.getElementById('chatInterviewDetails');
        if (detailsEl) {
            let detailsHtml = '';
            if (invitation.interview_date) {
                detailsHtml += `<span>Hari & Tanggal: <strong class="text-slate-800 dark:text-slate-100">${escapeHtml(invitation.interview_date)}</strong></span>`;
            }
            if (invitation.interview_time) {
                detailsHtml += `<span>·</span><span>Pukul: <strong class="text-slate-800 dark:text-slate-100">${escapeHtml(invitation.interview_time)}</strong></span>`;
            }
            if (invitation.interview_type) {
                detailsHtml += `<span>·</span><span>Metode: <strong class="text-slate-800 dark:text-slate-100">${escapeHtml(invitation.interview_type)}</strong></span>`;
            }
            if (invitation.interview_location) {
                detailsHtml += `<span>·</span><span>Lokasi/Tautan: <strong class="text-slate-800 dark:text-slate-100">${escapeHtml(invitation.interview_location)}</strong></span>`;
            }
            detailsEl.innerHTML = detailsHtml;
        }

        const notesEl = document.getElementById('chatInterviewNotes');
        if (notesEl) {
            if (invitation.interview_notes) {
                notesEl.textContent = `Catatan mitra: "${invitation.interview_notes}"`;
                notesEl.classList.remove('hidden');
            } else {
                notesEl.classList.add('hidden');
            }
        }

        const actionContainer = document.getElementById('chatInterviewActionContainer');
        if (actionContainer) {
            actionContainer.innerHTML = '';
            if (invitation.can_respond) {
                if (invitation.interview_status === 'pending' || invitation.interview_status === 'reschedule_requested') {
                    actionContainer.innerHTML = `
                        <button type="button" onclick="confirmInterviewDirect(${invitation.application_id})" class="inline-flex items-center gap-1 rounded-xl bg-emerald-600 px-3 py-1.5 text-xs font-bold text-white shadow-xs hover:bg-emerald-700 active:translate-y-px transition cursor-pointer">
                            <span class="material-symbols-outlined text-[15px]">check_circle</span>
                            <span>Setujui Jadwal</span>
                        </button>
                        <button type="button" onclick="openInterviewModal(${invitation.application_id}, 'reschedule_requested')" class="inline-flex items-center gap-1 rounded-xl bg-sky-600 px-3 py-1.5 text-xs font-bold text-white shadow-xs hover:bg-sky-700 active:translate-y-px transition cursor-pointer">
                            <span class="material-symbols-outlined text-[15px]">edit_calendar</span>
                            <span>Ajukan Diskusi</span>
                        </button>
                        <button type="button" onclick="openInterviewModal(${invitation.application_id}, 'declined')" class="inline-flex items-center gap-1 rounded-xl border border-rose-300 dark:border-rose-800 bg-rose-50 dark:bg-rose-950/40 px-2.5 py-1.5 text-xs font-bold text-rose-700 dark:text-rose-300 hover:bg-rose-100 transition cursor-pointer">
                            <span class="material-symbols-outlined text-[15px]">close</span>
                            <span>Tolak</span>
                        </button>
                    `;
                } else if (invitation.interview_status === 'confirmed') {
                    actionContainer.innerHTML = `
                        <span class="text-[11px] font-semibold text-emerald-700 dark:text-emerald-400 flex items-center gap-1">
                            <span class="material-symbols-outlined text-[15px]">verified</span>
                            <span>Telah Disetujui</span>
                        </span>
                        <button type="button" onclick="openInterviewModal(${invitation.application_id}, 'reschedule_requested')" class="text-[11px] font-semibold text-slate-600 hover:text-teal-700 dark:text-slate-300 underline cursor-pointer ml-1">
                            Ingin ajukan diskusi lain?
                        </button>
                    `;
                } else if (invitation.interview_status === 'declined') {
                    actionContainer.innerHTML = `
                        <span class="text-[11px] font-semibold text-rose-700 dark:text-rose-400 flex items-center gap-1">
                            <span class="material-symbols-outlined text-[15px]">cancel</span>
                            <span>Wawancara Ditolak</span>
                        </span>
                    `;
                }
            } else {
                if (invitation.interview_status === 'confirmed') {
                    actionContainer.innerHTML = `
                        <span class="inline-flex items-center gap-1 text-[11px] font-bold text-emerald-700 dark:text-emerald-400">
                            <span class="material-symbols-outlined text-[16px]">check_circle</span>
                            <span>Pelamar bersedia hadir</span>
                        </span>
                    `;
                } else if (invitation.interview_status === 'reschedule_requested') {
                    actionContainer.innerHTML = `
                        <span class="inline-flex items-center gap-1 text-[11px] font-bold text-sky-700 dark:text-sky-400">
                            <span class="material-symbols-outlined text-[16px]">schedule</span>
                            <span>Pelamar mengajak diskusi jadwal</span>
                        </span>
                    `;
                } else if (invitation.interview_status === 'declined') {
                    actionContainer.innerHTML = `
                        <span class="inline-flex items-center gap-1 text-[11px] font-bold text-rose-700 dark:text-rose-400">
                            <span class="material-symbols-outlined text-[16px]">cancel</span>
                            <span>Pelamar menolak wawancara</span>
                        </span>
                    `;
                } else {
                    actionContainer.innerHTML = `
                        <span class="text-[11px] text-slate-500 italic">Menunggu konfirmasi kehadiran pelamar...</span>
                    `;
                }
            }
        }
    }

    function openChatProfileModal() {
        if (!currentProfile) return;
        const modal = document.getElementById('chatProfileModal');
        const content = document.getElementById('chatProfileModalContent');
        if (!modal || !content) return;

        let html = '';
        if (currentProfile.type === 'jobseeker') {
            const avatarHtml = currentProfile.avatar_url
                ? `<img src="${currentProfile.avatar_url}" alt="${escapeHtml(currentProfile.name)}" class="w-14 h-14 rounded-2xl object-cover border-2 border-teal-500 shrink-0">`
                : `<div class="w-14 h-14 rounded-2xl bg-teal-700 text-white font-bold flex items-center justify-center text-lg shrink-0">${escapeHtml(currentProfile.initials || '--')}</div>`;

            let appsHtml = '';
            if (currentProfile.applications && currentProfile.applications.length > 0) {
                appsHtml = currentProfile.applications.map(app => {
                    const statusBadgeColors = {
                        pending: 'bg-amber-100 text-amber-800 border-amber-300 dark:bg-amber-950/60 dark:text-amber-300 dark:border-amber-800',
                        interview: 'bg-indigo-100 text-indigo-800 border-indigo-300 dark:bg-indigo-950/60 dark:text-indigo-300 dark:border-indigo-800',
                        accepted: 'bg-emerald-100 text-emerald-800 border-emerald-300 dark:bg-emerald-950/60 dark:text-emerald-300 dark:border-emerald-800',
                        rejected: 'bg-rose-100 text-rose-800 border-rose-300 dark:bg-rose-950/60 dark:text-rose-300 dark:border-rose-800',
                        resigned: 'bg-slate-100 text-slate-800 border-slate-300 dark:bg-slate-800 dark:text-slate-300 dark:border-slate-700',
                    }[app.status] || 'bg-slate-100 text-slate-700 border-slate-300';

                    let interviewSnippet = '';
                    if (app.status === 'interview') {
                        interviewSnippet = `
                            <div class="mt-2 p-2 rounded-xl bg-indigo-50/70 dark:bg-indigo-950/40 border border-indigo-200/70 dark:border-indigo-900/50 text-[11px] text-indigo-900 dark:text-indigo-300">
                                <span class="font-bold flex items-center gap-1 mb-0.5"><span class="material-symbols-outlined text-[14px]">event</span> Wawancara: ${escapeHtml(app.interview_date || '')} ${escapeHtml(app.interview_time || '')}</span>
                                <span>Status Respon: <strong>${escapeHtml(app.interview_status_label)}</strong></span>
                            </div>
                        `;
                    }

                    let resumeActions = '';
                    if (app.has_resume) {
                        resumeActions = `
                            <div class="mt-2.5 pt-2 border-t border-slate-100 dark:border-slate-800 flex items-center gap-2 flex-wrap">
                                ${app.resume_preview_url ? `
                                    <a href="${app.resume_preview_url}" target="_blank" class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-teal-600 hover:bg-teal-700 text-white font-semibold text-[11px] shadow-2xs transition">
                                        <span class="material-symbols-outlined text-[14px]">visibility</span>
                                        <span>Buka CV / Resume</span>
                                    </a>
                                ` : ''}
                                ${app.resume_download_url ? `
                                    <a href="${app.resume_download_url}" class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg border border-slate-200 dark:border-slate-700 bg-slate-50 hover:bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 text-[11px] font-semibold transition">
                                        <span class="material-symbols-outlined text-[14px]">download</span>
                                        <span>Unduh CV</span>
                                    </a>
                                ` : ''}
                            </div>
                        `;
                    }

                    return `
                        <div class="p-3.5 rounded-2xl border border-slate-200 dark:border-slate-800 bg-slate-50/60 dark:bg-slate-800/40 space-y-1">
                            <div class="flex items-start justify-between gap-2">
                                <h5 class="font-bold text-xs text-slate-900 dark:text-white">${escapeHtml(app.job_title)}</h5>
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold border ${statusBadgeColors}">${escapeHtml(app.status_label)}</span>
                            </div>
                            <p class="text-[10px] text-slate-400">Diajukan pada ${escapeHtml(app.applied_at)}</p>
                            ${app.note ? `<p class="text-[11px] text-slate-600 dark:text-slate-300 italic mt-1">"${escapeHtml(app.note)}"</p>` : ''}
                            ${interviewSnippet}
                            ${resumeActions}
                        </div>
                    `;
                }).join('');
            } else {
                appsHtml = `<p class="text-xs text-slate-400 italic">Belum ada lamaran terdaftar pada usaha Anda.</p>`;
            }

            html = `
                <div class="space-y-4">
                    <div class="flex items-start gap-3.5">
                        ${avatarHtml}
                        <div class="min-w-0 flex-1">
                            <div class="flex items-center gap-2 flex-wrap">
                                <h4 class="font-bold text-base text-slate-900 dark:text-white">${escapeHtml(currentProfile.name)}</h4>
                                <span class="px-2 py-0.5 rounded-md bg-teal-50 dark:bg-teal-950/60 border border-teal-200 dark:border-teal-800 text-teal-800 dark:text-teal-300 text-[10px] font-bold">Pencari Kerja</span>
                            </div>
                            ${currentProfile.username ? `<p class="text-xs font-mono text-slate-400 mt-0.5">${escapeHtml(currentProfile.username)}</p>` : ''}
                            <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-1 flex items-center gap-1">
                                <span class="material-symbols-outlined text-[14px]">calendar_month</span>
                                <span>Bergabung sejak ${escapeHtml(currentProfile.member_since)}</span>
                            </p>
                        </div>
                    </div>

                    <!-- Contact Details Card -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 p-3 rounded-2xl bg-slate-50 dark:bg-slate-800/60 border border-slate-100 dark:border-slate-800 text-xs">
                        <div class="flex items-center gap-2 min-w-0">
                            <span class="material-symbols-outlined text-slate-400 text-base shrink-0">mail</span>
                            <a href="mailto:${escapeHtml(currentProfile.email)}" class="text-slate-700 dark:text-slate-300 hover:text-teal-700 truncate">${escapeHtml(currentProfile.email)}</a>
                        </div>
                        <div class="flex items-center gap-2 min-w-0">
                            <span class="material-symbols-outlined text-slate-400 text-base shrink-0">phone</span>
                            <a href="tel:${escapeHtml(currentProfile.phone)}" class="text-slate-700 dark:text-slate-300 hover:text-teal-700 truncate">${escapeHtml(currentProfile.phone)}</a>
                        </div>
                    </div>

                    <!-- Application History for this Employer -->
                    <div class="space-y-2 pt-1">
                        <h5 class="text-xs font-bold text-slate-800 dark:text-slate-200 flex items-center gap-1.5">
                            <span class="material-symbols-outlined text-teal-600 text-base">assignment</span>
                            <span>Riwayat Lamaran untuk Usaha Anda</span>
                        </h5>
                        <div class="space-y-2.5 max-h-60 overflow-y-auto pr-1">
                            ${appsHtml}
                        </div>
                    </div>
                </div>
            `;
        } else if (currentProfile.type === 'employer') {
            const avatarHtml = currentProfile.avatar_url
                ? `<img src="${currentProfile.avatar_url}" alt="${escapeHtml(currentProfile.name)}" class="w-14 h-14 rounded-2xl object-cover border-2 border-teal-500 shrink-0">`
                : `<div class="w-14 h-14 rounded-2xl bg-teal-800 text-white font-bold flex items-center justify-center text-lg shrink-0">${escapeHtml(currentProfile.initials || '--')}</div>`;

            let jobsHtml = '';
            if (currentProfile.open_jobs && currentProfile.open_jobs.length > 0) {
                jobsHtml = currentProfile.open_jobs.map(job => `
                    <div class="p-3 rounded-2xl border border-slate-200 dark:border-slate-800 bg-slate-50/60 dark:bg-slate-800/40 flex items-center justify-between gap-3 min-w-0">
                        <div class="min-w-0 flex-1">
                            <h6 class="font-bold text-xs text-slate-900 dark:text-white truncate">${escapeHtml(job.title)}</h6>
                            <div class="text-[10px] text-slate-500 dark:text-slate-400 mt-0.5 flex items-center gap-2 flex-wrap">
                                <span>${escapeHtml(job.location)}</span>
                                <span>·</span>
                                <span class="font-semibold text-teal-700 dark:text-teal-400">${escapeHtml(job.salary_formatted)}</span>
                            </div>
                        </div>
                        <a href="${job.url}" target="_blank" class="portal-button-secondary !py-1 !px-2.5 text-[11px] font-bold gap-1 shrink-0">
                            <span>Detail</span>
                            <span class="material-symbols-outlined text-[13px]">arrow_forward</span>
                        </a>
                    </div>
                `).join('');
            } else {
                jobsHtml = `<p class="text-xs text-slate-400 italic">Saat ini belum ada lowongan aktif yang sedang dibuka.</p>`;
            }

            let myAppsHtml = '';
            if (currentProfile.my_applications && currentProfile.my_applications.length > 0) {
                myAppsHtml = `
                    <div class="pt-2 border-t border-slate-100 dark:border-slate-800">
                        <h5 class="text-xs font-bold text-slate-800 dark:text-slate-200 mb-2 flex items-center gap-1.5">
                            <span class="material-symbols-outlined text-teal-600 text-base">task_alt</span>
                            <span>Lamaran Anda pada Mitra Ini</span>
                        </h5>
                        <div class="space-y-1.5">
                            ${currentProfile.my_applications.map(a => `
                                <div class="flex items-center justify-between text-xs p-2 rounded-xl bg-slate-50 dark:bg-slate-800/40 border border-slate-100 dark:border-slate-800 min-w-0 gap-2">
                                    <span class="font-medium text-slate-800 dark:text-slate-200 truncate min-w-0 flex-1 mr-2">${escapeHtml(a.job_title)}</span>
                                    <span class="font-bold text-teal-700 dark:text-teal-400 shrink-0">${escapeHtml(a.status_label)}</span>
                                </div>
                            `).join('')}
                        </div>
                    </div>
                `;
            }

            html = `
                <div class="space-y-4">
                    <div class="flex items-start gap-3.5">
                        ${avatarHtml}
                        <div class="min-w-0 flex-1">
                            <div class="flex items-center gap-2 flex-wrap">
                                <h4 class="font-bold text-base text-slate-900 dark:text-white">${escapeHtml(currentProfile.name)}</h4>
                                <span class="px-2 py-0.5 rounded-md bg-teal-50 dark:bg-teal-950/60 border border-teal-200 dark:border-teal-800 text-teal-800 dark:text-teal-300 text-[10px] font-bold">Mitra UMKM</span>
                            </div>
                            <p class="text-xs text-slate-600 dark:text-slate-400 mt-0.5">Pemilik/Kontak: <strong class="text-slate-800 dark:text-slate-200">${escapeHtml(currentProfile.owner_name)}</strong></p>
                            <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-1 flex items-center gap-1">
                                <span class="material-symbols-outlined text-[14px]">store</span>
                                <span>Mitra sejak ${escapeHtml(currentProfile.member_since)}</span>
                            </p>
                        </div>
                    </div>

                    <!-- Contact Details -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 p-3 rounded-2xl bg-slate-50 dark:bg-slate-800/60 border border-slate-100 dark:border-slate-800 text-xs">
                        <div class="flex items-center gap-2 min-w-0">
                            <span class="material-symbols-outlined text-slate-400 text-base shrink-0">mail</span>
                            <a href="mailto:${escapeHtml(currentProfile.email)}" class="text-slate-700 dark:text-slate-300 hover:text-teal-700 truncate">${escapeHtml(currentProfile.email)}</a>
                        </div>
                        <div class="flex items-center gap-2 min-w-0">
                            <span class="material-symbols-outlined text-slate-400 text-base shrink-0">phone</span>
                            <a href="tel:${escapeHtml(currentProfile.phone)}" class="text-slate-700 dark:text-slate-300 hover:text-teal-700 truncate">${escapeHtml(currentProfile.phone)}</a>
                        </div>
                    </div>

                    <!-- Open Jobs Section -->
                    <div class="space-y-2 pt-1">
                        <h5 class="text-xs font-bold text-slate-800 dark:text-slate-200 flex items-center gap-1.5">
                            <span class="material-symbols-outlined text-teal-600 text-base">work</span>
                            <span>Lowongan Terbuka Saat Ini</span>
                        </h5>
                        <div class="space-y-2 max-h-52 overflow-y-auto pr-1">
                            ${jobsHtml}
                        </div>
                    </div>

                    ${myAppsHtml}
                </div>
            `;
        } else {
            html = `
                <div class="space-y-3">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 rounded-2xl bg-slate-800 text-white font-bold flex items-center justify-center text-base shrink-0">${escapeHtml(currentProfile.initials || 'AD')}</div>
                        <div>
                            <h4 class="font-bold text-sm text-slate-900 dark:text-white">${escapeHtml(currentProfile.name)}</h4>
                            <p class="text-xs text-slate-500">${escapeHtml(currentProfile.role_label || 'Super Administrator')}</p>
                        </div>
                    </div>
                    <div class="p-3 rounded-xl bg-slate-50 dark:bg-slate-800/50 text-xs space-y-1">
                        <p class="text-slate-600 dark:text-slate-400">Email: <span class="font-medium text-slate-800 dark:text-slate-200">${escapeHtml(currentProfile.email)}</span></p>
                    </div>
                </div>
            `;
        }

        content.innerHTML = html;
        modal.classList.remove('hidden');
    }

    function closeChatProfileModal() {
        const modal = document.getElementById('chatProfileModal');
        if (modal) modal.classList.add('hidden');
    }

    async function confirmInterviewDirect(applicationId) {
        const confirmed = await window.showAppConfirm({
            title: 'Setujui Jadwal Wawancara?',
            message: 'Apakah Anda yakin bersedia menghadiri sesi wawancara sesuai hari dan jam yang dijadwalkan oleh Mitra UMKM?\n\nPemberitahuan resmi dan pesan konfirmasi otomatis akan langsung dikirimkan ke Mitra.',
            confirmText: 'Ya, Saya Bersedia Hadir',
            cancelText: 'Kembali',
            type: 'primary',
            icon: 'event_available'
        });
        if (!confirmed) return;

        try {
            const res = await fetch(`/riwayat-lamaran/${applicationId}/interview-response`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({ action: 'confirmed' })
            });
            const data = await res.json();
            if (res.ok && data.success) {
                fetchMessages();
                loadConversations();
            }
        } catch (e) {
            console.error(e);
        }
    }

    function openInterviewModal(applicationId, action) {
        activeInterviewApplicationId = applicationId;
        const modal = document.getElementById('chatInterviewActionModal');
        const actionInput = document.getElementById('interviewModalAction');
        const headingEl = document.getElementById('interviewModalHeading');
        const descEl = document.getElementById('interviewModalDesc');
        const noteLabel = document.getElementById('interviewModalNoteLabel');
        const noteInput = document.getElementById('interviewModalNote');
        const submitBtn = document.getElementById('interviewModalSubmitBtn');
        const submitText = document.getElementById('interviewModalSubmitText');
        const iconContainer = document.getElementById('interviewModalIconContainer');
        const iconEl = document.getElementById('interviewModalIcon');

        if (!modal || !actionInput) return;
        actionInput.value = action;
        if (noteInput) noteInput.value = '';

        if (action === 'reschedule_requested') {
            iconContainer.className = 'w-10 h-10 rounded-2xl bg-sky-100 dark:bg-sky-900/60 text-sky-700 dark:text-sky-300 flex items-center justify-center shrink-0';
            iconEl.textContent = 'edit_calendar';
            headingEl.textContent = 'Ajukan Diskusi Jadwal Wawancara';
            descEl.textContent = 'Sampaikan usulan alternatif hari atau jam yang lebih sesuai agar dapat dikoordinasikan dengan mitra.';
            noteLabel.textContent = 'Usulan Jadwal Baru / Alasan Penyesuaian *';
            if (noteInput) {
                noteInput.placeholder = 'Contoh: Bisakah wawancara dijadwalkan pada hari Jumat pukul 14:00 WIB karena ada agenda lain...';
                noteInput.required = true;
            }
            submitBtn.className = 'inline-flex items-center gap-1.5 rounded-xl bg-sky-600 hover:bg-sky-700 text-white font-bold text-xs px-4 py-2 shadow-xs transition cursor-pointer';
            submitText.textContent = 'Kirim Pengajuan Diskusi';
        } else if (action === 'declined') {
            iconContainer.className = 'w-10 h-10 rounded-2xl bg-rose-100 dark:bg-rose-900/60 text-rose-700 dark:text-rose-300 flex items-center justify-center shrink-0';
            iconEl.textContent = 'event_busy';
            headingEl.textContent = 'Tolak Undangan Wawancara';
            descEl.textContent = 'Menyatakan bahwa Anda tidak dapat menghadiri sesi wawancara untuk posisi ini.';
            noteLabel.textContent = 'Alasan Penolakan (Opsional)';
            if (noteInput) {
                noteInput.placeholder = 'Contoh: Mohon maaf, saya telah menerima penawaran pekerjaan lain...';
                noteInput.required = false;
            }
            submitBtn.className = 'inline-flex items-center gap-1.5 rounded-xl bg-rose-600 hover:bg-rose-700 text-white font-bold text-xs px-4 py-2 shadow-xs transition cursor-pointer';
            submitText.textContent = 'Tolak Wawancara';
        }

        modal.classList.remove('hidden');
    }

    function closeInterviewActionModal() {
        const modal = document.getElementById('chatInterviewActionModal');
        if (modal) modal.classList.add('hidden');
    }

    async function submitInterviewResponse(event) {
        event.preventDefault();
        if (!activeInterviewApplicationId) return;

        const action = document.getElementById('interviewModalAction')?.value;
        const note = document.getElementById('interviewModalNote')?.value || '';
        const submitBtn = document.getElementById('interviewModalSubmitBtn');

        if (submitBtn) {
            submitBtn.disabled = true;
            submitBtn.classList.add('opacity-50');
        }

        try {
            const res = await fetch(`/riwayat-lamaran/${activeInterviewApplicationId}/interview-response`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({ action: action, note: note })
            });
            const data = await res.json();
            if (res.ok && data.success) {
                closeInterviewActionModal();
                fetchMessages();
                loadConversations();
            }
        } catch (e) {
            console.error(e);
        } finally {
            if (submitBtn) {
                submitBtn.disabled = false;
                submitBtn.classList.remove('opacity-50');
            }
        }
    }

    function escapeHtml(str) {
        if (!str) return '';
        return String(str)
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#039;");
    }

    document.addEventListener('DOMContentLoaded', () => {
        loadConversations();
        pollConversationsTimer = setInterval(loadConversations, 4000);
    });
</script>
@endpush
