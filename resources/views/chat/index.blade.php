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
            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-emerald-50 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300 text-[11px] font-bold rounded-xl border border-emerald-200 dark:border-emerald-800">
                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                <span class="hidden sm:inline">Saluran Komunikasi Aktif</span>
            </span>
        </div>
    </div>

    <!-- Fixed Mode Split Chat Container -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl sm:rounded-3xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden flex flex-col lg:flex-row flex-1 min-h-0 w-full">
        
        <!-- Left Pane: Conversation List -->
        <div class="w-full lg:w-80 xl:w-96 border-b lg:border-b-0 lg:border-r border-slate-200 dark:border-slate-800 flex flex-col h-full bg-slate-50/50 dark:bg-slate-950/40 shrink-0 overflow-hidden">
            <!-- Search bar -->
            <div class="p-3.5 border-b border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 shrink-0">
                <div class="flex items-center bg-slate-100 dark:bg-slate-800 rounded-xl px-3 py-2 text-xs focus-within:bg-white dark:focus-within:bg-slate-950 focus-within:ring-2 focus-within:ring-teal-600/20 focus-within:border-teal-600 border border-transparent transition-all">
                    <span class="material-symbols-outlined text-slate-400 text-base mr-2">search</span>
                    <input type="text" id="chatSearchInput" oninput="filterConversations()" placeholder="Cari kontak obrolan..." class="w-full bg-transparent border-none outline-none text-slate-900 dark:text-white placeholder:text-slate-400 p-0 text-xs">
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
        <div class="flex-1 flex flex-col h-full bg-white dark:bg-slate-900 min-w-0 overflow-hidden" id="chatRightPane">
            
            <!-- Chat Window Header -->
            <div class="p-3.5 sm:p-4 border-b border-slate-200 dark:border-slate-800 flex items-center justify-between bg-white dark:bg-slate-900 shrink-0" id="activeChatHeader">
                <div class="flex items-center gap-3 min-w-0">
                    <div class="w-10 h-10 rounded-2xl bg-teal-700 text-white font-bold flex items-center justify-center text-sm shrink-0" id="activeChatAvatar">
                        --
                    </div>
                    <div class="min-w-0">
                        <div class="flex items-center gap-2">
                            <span class="font-bold text-sm text-slate-900 dark:text-white truncate" id="activeChatName">Pilih percakapan</span>
                            <span class="px-2 py-0.5 rounded bg-teal-50 dark:bg-teal-950 text-teal-800 dark:text-teal-300 text-[10px] font-bold border border-teal-200 dark:border-teal-800 hidden" id="activeChatRoleBadge"></span>
                        </div>
                        <p class="text-[11px] text-slate-500 dark:text-slate-400 flex items-center gap-1.5" id="activeChatStatus">
                            <span class="w-1.5 h-1.5 rounded-full bg-slate-300"></span>
                            <span>Tidak ada obrolan aktif</span>
                        </p>
                    </div>
                </div>

                <!-- Action Buttons: Remove Contact & Clear Chat -->
                <div class="flex items-center gap-1.5 sm:gap-2">
                    <button type="button" id="removeContactBtn" onclick="confirmRemovePerson()" class="hidden portal-button-secondary !py-1.5 !px-2.5 sm:!px-3 text-xs text-rose-600 hover:text-rose-700 hover:bg-rose-50 border-rose-200 dark:border-rose-900/50 dark:hover:bg-rose-950/40 cursor-pointer" title="Hapus kontak dari daftar obrolan">
                        <span class="material-symbols-outlined text-[16px]">person_remove</span>
                        <span class="hidden md:inline">Hapus Kontak</span>
                    </button>
                    <button type="button" id="clearChatBtn" onclick="confirmClearChat()" class="hidden portal-button-secondary !py-1.5 !px-2.5 sm:!px-3 text-xs text-slate-600 hover:text-rose-700 hover:bg-rose-50 border-slate-200 dark:border-slate-800 dark:hover:bg-rose-950/40 cursor-pointer" title="Bersihkan seluruh riwayat pesan obrolan">
                        <span class="material-symbols-outlined text-[16px]">delete_sweep</span>
                        <span class="hidden md:inline">Bersihkan Obrolan</span>
                    </button>
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
                    <input type="text" id="chatInput" placeholder="Ketik pesan..." class="flex-1 py-2.5 px-4 bg-slate-100 dark:bg-slate-800 focus:bg-white dark:focus:bg-slate-950 rounded-2xl border border-transparent focus:border-teal-600 focus:ring-2 focus:ring-teal-600/15 text-xs text-slate-900 dark:text-white outline-none transition-all" autocomplete="off" disabled required>
                    <button type="submit" id="chatSendBtn" class="px-5 py-2.5 bg-teal-700 hover:bg-teal-800 disabled:opacity-40 disabled:cursor-not-allowed active:scale-95 text-white font-bold text-xs rounded-2xl shadow-xs transition-all flex items-center gap-1.5 cursor-pointer" disabled>
                        <span>Kirim</span>
                        <span class="material-symbols-outlined text-sm">send</span>
                    </button>
                </form>
            </div>

        </div>

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
    const currentUserInitials = "{{ $userInitials }}";
    const csrfToken = "{{ csrf_token() }}";

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
                <div onclick="selectConversation(${user.id}, '${escapeHtml(user.name)}', '${escapeHtml(user.role_label)}', '${escapeHtml(user.initials)}')" 
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
                        <button type="button" onclick="event.stopPropagation(); removePersonConversation(${user.id}, '${escapeHtml(user.name)}')" class="opacity-0 group-hover:opacity-100 p-1 text-slate-400 hover:text-rose-600 rounded-lg transition-opacity cursor-pointer" title="Hapus kontak ini dari daftar">
                            <span class="material-symbols-outlined text-[17px]">person_remove</span>
                        </button>
                    </div>
                </div>
            `;
        }).join('');
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

    async function selectConversation(userId, name, roleLabel, initials) {
        activeUserId = userId;
        activeUserName = name;
        cancelReply();

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
        const clearBtn = document.getElementById('clearChatBtn');
        const removeBtn = document.getElementById('removeContactBtn');

        if (avatar) avatar.textContent = initials || name.substring(0, 2).toUpperCase();
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

        if (clearBtn) clearBtn.classList.remove('hidden');
        if (removeBtn) removeBtn.classList.remove('hidden');

        if (input) {
            input.disabled = false;
            input.placeholder = `Tulis pesan ke ${name}...`;
            input.focus();
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

        const senderName = msg.is_me ? 'Anda' : otherUser.name;
        const safeSnippet = escapeJs(msg.message);

        if (msg.is_me) {
            return `
                <div data-msg-id="${msg.id}" class="group relative flex items-start justify-end gap-1.5 sm:gap-2 ml-auto max-w-xl">
                    <div class="opacity-0 group-hover:opacity-100 transition-opacity flex items-center gap-0.5 self-center shrink-0">
                        <button type="button" onclick="startReply(${msg.id}, '${escapeHtml(senderName)}', '${safeSnippet}')" title="Balas pesan ini" class="p-1.5 text-slate-400 hover:text-teal-700 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-lg cursor-pointer">
                            <span class="material-symbols-outlined text-[16px]">reply</span>
                        </button>
                        <button type="button" onclick="deleteMessage(${msg.id})" title="Hapus pesan ini" class="p-1.5 text-slate-400 hover:text-rose-600 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-lg cursor-pointer">
                            <span class="material-symbols-outlined text-[16px]">delete</span>
                        </button>
                    </div>
                    <div class="bg-teal-700 text-white rounded-2xl rounded-tr-sm p-3 sm:p-3.5 shadow-2xs text-xs space-y-1 text-left min-w-[90px]">
                        ${replyHtml}
                        <p class="leading-relaxed whitespace-pre-line">${escapeHtml(msg.message)}</p>
                        <span class="text-[9px] text-teal-200 block text-right">${msg.time}</span>
                    </div>
                    <div class="w-8 h-8 rounded-xl bg-slate-800 text-white font-bold flex items-center justify-center text-xs shrink-0 mt-0.5">
                        ${currentUserInitials}
                    </div>
                </div>
            `;
        } else {
            return `
                <div data-msg-id="${msg.id}" class="group relative flex items-start gap-1.5 sm:gap-2 max-w-xl">
                    <div class="w-8 h-8 rounded-xl bg-teal-700 text-white font-bold flex items-center justify-center text-xs shrink-0 mt-0.5">
                        ${escapeHtml(otherUser.initials)}
                    </div>
                    <div class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl rounded-tl-sm p-3 sm:p-3.5 shadow-2xs text-xs space-y-1 text-left min-w-[90px]">
                        ${replyHtml}
                        <p class="text-slate-800 dark:text-slate-100 leading-relaxed whitespace-pre-line">${escapeHtml(msg.message)}</p>
                        <span class="text-[9px] text-slate-400 block text-right">${msg.time}</span>
                    </div>
                    <div class="opacity-0 group-hover:opacity-100 transition-opacity flex items-center gap-0.5 self-center shrink-0">
                        <button type="button" onclick="startReply(${msg.id}, '${escapeHtml(senderName)}', '${safeSnippet}')" title="Balas pesan ini" class="p-1.5 text-slate-400 hover:text-teal-700 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-lg cursor-pointer">
                            <span class="material-symbols-outlined text-[16px]">reply</span>
                        </button>
                        <button type="button" onclick="deleteMessage(${msg.id})" title="Hapus pesan ini" class="p-1.5 text-slate-400 hover:text-rose-600 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-lg cursor-pointer">
                            <span class="material-symbols-outlined text-[16px]">delete</span>
                        </button>
                    </div>
                </div>
            `;
        }
    }

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
                    <div class="h-full flex flex-col items-center justify-center text-slate-400 my-auto text-center p-8">
                        <span class="material-symbols-outlined text-4xl text-slate-300 mb-2">waving_hand</span>
                        <p class="font-bold text-slate-700 dark:text-slate-300 text-xs">Belum ada riwayat pesan</p>
                        <p class="text-[11px] text-slate-400 mt-1 max-w-xs">Kirim pesan pertama Anda untuk memulai percakapan.</p>
                    </div>
                `;
                return;
            }

            const isPlaceholder = container.querySelector('.text-slate-400');
            if (isPlaceholder || existingIds.size === 0) {
                container.innerHTML = '';
                data.messages.forEach(msg => {
                    const temp = document.createElement('div');
                    temp.innerHTML = renderMessageHTML(msg, data.user).trim();
                    container.appendChild(temp.firstElementChild);
                });
                container.scrollTop = container.scrollHeight;
            } else {
                let hasNew = false;
                data.messages.forEach(msg => {
                    if (!existingIds.has(msg.id)) {
                        const temp = document.createElement('div');
                        temp.innerHTML = renderMessageHTML(msg, data.user).trim();
                        const el = temp.firstElementChild;
                        el.classList.add('animate-msg-popup');
                        container.appendChild(el);
                        hasNew = true;
                    }
                });

                if (hasNew) {
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
        if (!activeUserId) return;

        const input = document.getElementById('chatInput');
        const text = input ? input.value.trim() : '';
        if (!text) return;

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
            message: 'Pesan yang telah dihapus tidak dapat dipulihkan kembali.',
            confirmText: 'Ya, Hapus Pesan',
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
                if (el) el.remove();
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
                    document.getElementById('clearChatBtn').classList.add('hidden');
                    document.getElementById('removeContactBtn').classList.add('hidden');
                    document.getElementById('chatInput').disabled = true;
                    document.getElementById('chatInput').placeholder = 'Ketik pesan...';
                    document.getElementById('chatSendBtn').disabled = true;

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

    function escapeHtml(str) {
        if (!str) return '';
        return String(str)
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#039;");
    }

    function escapeJs(str) {
        if (!str) return '';
        return String(str)
            .replace(/\\/g, '\\\\')
            .replace(/'/g, "\\'")
            .replace(/"/g, '\\"')
            .replace(/\n/g, ' ')
            .replace(/\r/g, '');
    }

    document.addEventListener('DOMContentLoaded', () => {
        loadConversations();
        pollConversationsTimer = setInterval(loadConversations, 4000);
    });
</script>
@endpush
