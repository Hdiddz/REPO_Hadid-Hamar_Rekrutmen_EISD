@auth
@if(!request()->routeIs('chat.*'))
@php
    $currentUser = Auth::user();
    $currentRole = $currentUser->role ?? 'jobseeker';
@endphp

<!-- ================= FLOATING CHAT WIDGET ================= -->
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
                                Saluran Pengawasan Administrator
                            @else
                                Pesan &amp; Obrolan Kerja
                            @endif
                        </div>
                        <div class="text-[10px] text-teal-200">
                            @if($currentRole === 'employer')
                                Komunikasi dengan kandidat pelamar
                            @elseif($currentRole === 'admin')
                                Koordinasi kepatuhan etis &amp; aduan
                            @else
                                Berkirim pesan dengan Mitra UMKM
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
                    <span>Role: <strong>{{ $currentRole === 'employer' ? 'Mitra UMKM' : ($currentRole === 'admin' ? 'Super Admin' : 'Pencari Kerja') }}</strong></span>
                </div>
                <span class="text-[9px] text-teal-600 font-semibold">Real-time</span>
            </div>

            <!-- Conversation List Container -->
            <div class="flex-1 overflow-y-auto divide-y divide-slate-100" id="popupContactListContainer">
                <div id="popupEmptyState" class="p-8 text-center flex flex-col items-center justify-center text-slate-400 my-auto h-full">
                    <span class="material-symbols-outlined text-4xl mb-2 text-slate-300">chat_bubble_outline</span>
                    <p class="text-xs font-bold text-slate-600">Belum ada percakapan</p>
                    <p class="text-[10px] text-slate-400 mt-0.5">Kontak yang terhubung akan tampil di sini.</p>
                </div>
            </div>
        </div>

        <!-- VIEW 2: DETAIL PERCAKAPAN (ACTIVE CHAT VIEW) -->
        <div id="popupChatDetailView" class="hidden flex-col h-full">
            <!-- Header Detail Percakapan -->
            <div class="p-3 bg-teal-800 text-white flex items-center justify-between shadow-xs">
                <div class="flex items-center gap-2 min-w-0">
                    <button type="button" onclick="showPopupChatList()" class="p-1 hover:bg-teal-700 rounded-lg text-teal-200 hover:text-white transition-colors cursor-pointer" title="Kembali ke Daftar Kontak">
                        <span class="material-symbols-outlined text-base">arrow_back</span>
                    </button>
                    <div class="w-7 h-7 rounded-lg bg-teal-600 text-white font-bold flex items-center justify-center text-xs shrink-0" id="detailChatAvatar">
                        --
                    </div>
                    <div class="min-w-0">
                        <div class="text-xs font-bold leading-tight truncate max-w-[150px]" id="detailChatTitle">
                            Pengguna
                        </div>
                        <div class="text-[10px] text-teal-200 flex items-center gap-1" id="detailChatSubtitle">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 shrink-0"></span>
                            <span class="truncate" id="detailChatOwner">Online</span>
                        </div>
                    </div>
                </div>
                <div class="flex items-center gap-1">
                    <button type="button" onclick="confirmRemovePopupPerson()" class="p-1 hover:bg-teal-700 rounded-lg text-teal-200 hover:text-rose-200 transition-colors cursor-pointer" title="Hapus kontak percakapan">
                        <span class="material-symbols-outlined text-base">person_remove</span>
                    </button>
                    <button type="button" onclick="confirmClearPopupChat()" class="p-1 hover:bg-teal-700 rounded-lg text-teal-200 hover:text-rose-200 transition-colors cursor-pointer" title="Bersihkan seluruh obrolan (Clear Chat)">
                        <span class="material-symbols-outlined text-base">delete_sweep</span>
                    </button>
                    <a href="{{ route('chat.index') }}" class="p-1 hover:bg-teal-700 rounded-lg text-teal-200 hover:text-white transition-colors" title="Buka Halaman Penuh">
                        <span class="material-symbols-outlined text-base">open_in_new</span>
                    </a>
                    <button type="button" onclick="toggleFloatingChat()" class="p-1 hover:bg-teal-700 rounded-lg text-teal-200 hover:text-white transition-colors cursor-pointer" title="Tutup Chat">
                        <span class="material-symbols-outlined text-base">close</span>
                    </button>
                </div>
            </div>

            <!-- Message Stream with Slide-Up Animation -->
            <div class="flex-1 p-3.5 overflow-y-auto space-y-3 bg-slate-50/50 text-xs" id="popupChatStream">
                <!-- Messages will load dynamically here -->
            </div>

            <!-- Popup Reply Preview Bar -->
            <div id="popupReplyPreviewBar" class="hidden px-3 py-1.5 bg-teal-50 border-t border-teal-100 flex items-center justify-between gap-2 text-xs animate-page-enter">
                <div class="flex items-center gap-1.5 min-w-0 border-l-3 border-teal-600 pl-2">
                    <span class="material-symbols-outlined text-teal-700 text-sm shrink-0">reply</span>
                    <div class="min-w-0">
                        <p class="font-bold text-teal-900 text-[10px] truncate" id="popupReplySender">Membalas</p>
                        <p class="text-slate-600 text-[10px] truncate max-w-[200px]" id="popupReplySnippet">Teks...</p>
                    </div>
                </div>
                <button type="button" onclick="cancelPopupReply()" class="p-0.5 hover:bg-slate-200 rounded text-slate-400 hover:text-slate-700 transition-colors cursor-pointer" title="Batal membalas">
                    <span class="material-symbols-outlined text-[15px]">close</span>
                </button>
            </div>

            <!-- Input Bar -->
            <div class="p-2.5 border-t border-slate-200 bg-white shrink-0">
                <form id="popupChatSendForm" onsubmit="handlePopupChatSend(event)" class="flex items-center gap-1.5">
                    <input type="text" id="popupChatInput" placeholder="Ketik pesan..." class="flex-1 py-2 px-3 bg-slate-100 focus:bg-white rounded-xl border border-transparent focus:border-teal-600 text-xs text-slate-900 outline-none transition-all" autocomplete="off" required>
                    <button type="submit" class="p-2 bg-teal-700 hover:bg-teal-800 active:scale-95 text-white rounded-xl cursor-pointer transition-all flex items-center justify-center">
                        <span class="material-symbols-outlined text-base">send</span>
                    </button>
                </form>
            </div>
        </div>

    </div>

    <!-- Floating FAB Button: Dinamai "Pesan" -->
    <button type="button" id="floatingChatBtn" onclick="toggleFloatingChat()" class="px-4 py-3 bg-teal-800 hover:bg-teal-900 active:scale-95 text-white rounded-full shadow-2xl transition-all cursor-pointer flex items-center gap-2 relative group border border-teal-700/50">
        <span class="material-symbols-outlined text-xl">chat</span>
        <span class="text-xs font-bold tracking-tight">Pesan</span>
        <span id="floatingChatBadge" class="hidden min-w-[20px] h-5 px-1 bg-rose-500 text-white text-[10px] font-black rounded-full flex items-center justify-center border-2 border-teal-900 shadow-xs">0</span>
    </button>
</div>

<!-- ================= GLOBAL REAL-TIME CHAT SCRIPT ================= -->
<script>
    let activeChatUserId = null;
    let activeChatUserName = '';
    let chatPollInterval = null;
    let listPollInterval = null;
    let popupReply = null;
    const currentUserId = {{ Auth::id() }};
    const currentUserInitials = "{{ strtoupper(substr(Auth::user()->name ?? 'U', 0, 2)) }}";
    const csrfToken = "{{ csrf_token() }}";

    function showPopupChatList() {
        activeChatUserId = null;
        activeChatUserName = '';
        cancelPopupReply();

        if (chatPollInterval) {
            clearInterval(chatPollInterval);
            chatPollInterval = null;
        }

        const listView = document.getElementById('popupChatListView');
        const detailView = document.getElementById('popupChatDetailView');
        if (listView && detailView) {
            detailView.classList.add('hidden');
            detailView.classList.remove('flex', 'animate-chat-slide-right');

            listView.classList.remove('hidden');
            listView.classList.add('animate-chat-slide-left');
            setTimeout(() => listView.classList.remove('animate-chat-slide-left'), 300);
        }
        loadPopupConversations();
    }

    async function loadPopupConversations() {
        const container = document.getElementById('popupContactListContainer');
        const badge = document.getElementById('floatingChatBadge');
        if (!container) return;

        try {
            const res = await fetch('/chat/conversations', {
                headers: { 'Accept': 'application/json' }
            });
            if (!res.ok) return;
            const users = await res.json();

            const totalUnread = users.reduce((acc, u) => acc + (u.unread_count || 0), 0);
            if (badge) {
                if (totalUnread > 0) {
                    badge.textContent = totalUnread > 99 ? '99+' : totalUnread;
                    badge.classList.remove('hidden');
                } else {
                    badge.classList.add('hidden');
                }
            }

            if (users.length === 0) {
                container.innerHTML = `
                    <div class="p-8 text-center flex flex-col items-center justify-center text-slate-400 my-auto h-full">
                        <span class="material-symbols-outlined text-4xl mb-2 text-slate-300">chat_bubble_outline</span>
                        <p class="text-xs font-bold text-slate-600">Belum ada percakapan</p>
                        <p class="text-[10px] text-slate-400 mt-0.5">Kontak yang terhubung akan tampil di sini.</p>
                    </div>
                `;
                return;
            }

            container.innerHTML = users.map(user => `
                <div onclick="openPopupChatDetail(${user.id}, '${escapeHtml(user.name)}', '${escapeHtml(user.role_label)}')" class="group p-3 hover:bg-teal-50/60 cursor-pointer transition-colors flex items-start gap-2.5">
                    <div class="w-8 h-8 rounded-lg bg-teal-700 text-white font-bold flex items-center justify-center text-xs shrink-0 mt-0.5">
                        ${escapeHtml(user.initials)}
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center justify-between">
                            <span class="font-bold text-xs text-slate-900 truncate">${escapeHtml(user.name)}</span>
                            <span class="text-[9px] text-teal-700 font-semibold">${user.last_time || ''}</span>
                        </div>
                        <span class="text-[10px] text-teal-800 font-semibold block truncate">${escapeHtml(user.role_label)}</span>
                        <p class="text-[10px] text-slate-500 truncate mt-0.5">${user.last_message ? escapeHtml(user.last_message) : '<em>Mulai obrolan...</em>'}</p>
                    </div>
                    <div class="flex items-center gap-1 shrink-0">
                        ${user.unread_count > 0 ? '<span class="w-2 h-2 rounded-full bg-rose-500 shrink-0 mt-1.5"></span>' : ''}
                        <button type="button" onclick="event.stopPropagation(); removePopupConversationById(${user.id}, '${escapeHtml(user.name)}')" class="opacity-0 group-hover:opacity-100 p-1 text-slate-400 hover:text-rose-600 rounded-lg transition-opacity cursor-pointer" title="Hapus kontak ini">
                            <span class="material-symbols-outlined text-[15px]">person_remove</span>
                        </button>
                    </div>
                </div>
            `).join('');
        } catch (e) {
            console.error(e);
        }
    }

    async function openPopupChatDetail(userId, name, roleLabel) {
        activeChatUserId = userId;
        activeChatUserName = name;
        cancelPopupReply();

        const detailTitle = document.getElementById('detailChatTitle');
        const detailOwner = document.getElementById('detailChatOwner');
        const detailAvatar = document.getElementById('detailChatAvatar');

        if (detailTitle) detailTitle.textContent = name;
        if (detailOwner) detailOwner.textContent = (roleLabel ? roleLabel + ' • Online' : 'Online');
        if (detailAvatar) detailAvatar.textContent = name.substring(0, 2).toUpperCase();

        const listView = document.getElementById('popupChatListView');
        const detailView = document.getElementById('popupChatDetailView');
        if (listView && detailView) {
            listView.classList.add('hidden');
            listView.classList.remove('animate-chat-slide-left');

            detailView.classList.remove('hidden');
            detailView.classList.add('flex', 'animate-chat-slide-right');
            setTimeout(() => detailView.classList.remove('animate-chat-slide-right'), 300);
        }

        const popup = document.getElementById('floatingChatPopup');
        if (popup && popup.classList.contains('hidden')) {
            popup.classList.remove('hidden');
        }

        const stream = document.getElementById('popupChatStream');
        if (stream) stream.innerHTML = '';

        await fetchChatMessages();

        if (chatPollInterval) clearInterval(chatPollInterval);
        chatPollInterval = setInterval(fetchChatMessages, 1500);

        const input = document.getElementById('popupChatInput');
        if (input) input.focus();
    }

    function renderPopupMessageHTML(msg, otherUser) {
        let replyHtml = '';
        if (msg.reply_to) {
            if (msg.is_me) {
                replyHtml = `
                    <div class="mb-1 p-1.5 rounded-lg bg-black/15 border-l-2 border-teal-300 text-[10px] leading-snug">
                        <div class="font-bold text-teal-200 text-[9px] flex items-center gap-1">
                            <span class="material-symbols-outlined text-[11px]">reply</span>
                            <span>${escapeHtml(msg.reply_to.sender_name)}</span>
                        </div>
                        <p class="text-teal-50 truncate text-[9px] mt-0.5 opacity-90">${escapeHtml(msg.reply_to.message)}</p>
                    </div>
                `;
            } else {
                replyHtml = `
                    <div class="mb-1 p-1.5 rounded-lg bg-slate-100 border-l-2 border-teal-600 text-[10px] leading-snug">
                        <div class="font-bold text-teal-800 text-[9px] flex items-center gap-1">
                            <span class="material-symbols-outlined text-[11px]">reply</span>
                            <span>${escapeHtml(msg.reply_to.sender_name)}</span>
                        </div>
                        <p class="text-slate-600 truncate text-[9px] mt-0.5">${escapeHtml(msg.reply_to.message)}</p>
                    </div>
                `;
            }
        }

        const senderName = msg.is_me ? 'Anda' : otherUser.name;
        const safeSnippet = escapeJs(msg.message);

        if (msg.is_me) {
            return `
                <div data-msg-id="${msg.id}" class="group relative flex items-start justify-end gap-1 ml-auto max-w-[90%]">
                    <div class="opacity-0 group-hover:opacity-100 transition-opacity flex items-center gap-0.5 self-center shrink-0">
                        <button type="button" onclick="startPopupReply(${msg.id}, '${escapeHtml(senderName)}', '${safeSnippet}')" title="Balas pesan ini" class="p-1 text-slate-400 hover:text-teal-700 hover:bg-slate-100 rounded cursor-pointer">
                            <span class="material-symbols-outlined text-[15px]">reply</span>
                        </button>
                        <button type="button" onclick="deletePopupMessage(${msg.id})" title="Hapus pesan ini" class="p-1 text-slate-400 hover:text-rose-600 hover:bg-slate-100 rounded cursor-pointer">
                            <span class="material-symbols-outlined text-[15px]">delete</span>
                        </button>
                    </div>
                    <div class="bg-teal-700 text-white p-2.5 rounded-2xl rounded-tr-sm shadow-2xs space-y-0.5 text-left min-w-[70px]">
                        ${replyHtml}
                        <p class="leading-relaxed whitespace-pre-line text-xs">${escapeHtml(msg.message)}</p>
                        <span class="text-[8px] text-teal-200 block text-right">${msg.time}</span>
                    </div>
                    <div class="w-6 h-6 rounded-lg bg-slate-800 text-white font-bold flex items-center justify-center text-[10px] shrink-0 mt-0.5">
                        ${currentUserInitials}
                    </div>
                </div>
            `;
        } else {
            return `
                <div data-msg-id="${msg.id}" class="group relative flex items-start gap-1 max-w-[90%]">
                    <div class="w-6 h-6 rounded-lg bg-teal-700 text-white font-bold flex items-center justify-center text-[10px] shrink-0 mt-0.5">
                        ${escapeHtml(otherUser.initials)}
                    </div>
                    <div class="bg-white border border-slate-200 p-2.5 rounded-2xl rounded-tl-sm shadow-2xs space-y-0.5 text-left min-w-[70px]">
                        ${replyHtml}
                        <p class="text-slate-800 leading-relaxed whitespace-pre-line text-xs">${escapeHtml(msg.message)}</p>
                        <span class="text-[8px] text-slate-400 block text-right">${msg.time}</span>
                    </div>
                    <div class="opacity-0 group-hover:opacity-100 transition-opacity flex items-center gap-0.5 self-center shrink-0">
                        <button type="button" onclick="startPopupReply(${msg.id}, '${escapeHtml(senderName)}', '${safeSnippet}')" title="Balas pesan ini" class="p-1 text-slate-400 hover:text-teal-700 hover:bg-slate-100 rounded cursor-pointer">
                            <span class="material-symbols-outlined text-[15px]">reply</span>
                        </button>
                        <button type="button" onclick="deletePopupMessage(${msg.id})" title="Hapus pesan dari pengguna ini" class="p-1 text-slate-400 hover:text-rose-600 hover:bg-slate-100 rounded cursor-pointer">
                            <span class="material-symbols-outlined text-[15px]">delete</span>
                        </button>
                    </div>
                </div>
            `;
        }
    }

    async function fetchChatMessages() {
        if (!activeChatUserId) return;
        const stream = document.getElementById('popupChatStream');
        if (!stream) return;

        try {
            const res = await fetch(`/chat/messages/${activeChatUserId}`, {
                headers: { 'Accept': 'application/json' }
            });
            if (!res.ok) return;
            const data = await res.json();

            const existingElements = Array.from(stream.querySelectorAll('[data-msg-id]'));
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
                stream.innerHTML = `
                    <div class="p-6 text-center text-xs text-slate-400">
                        Belum ada riwayat pesan. Kirim pesan pertama untuk memulai obrolan.
                    </div>
                `;
                return;
            }

            const isPlaceholder = stream.querySelector('.text-slate-400');
            if (isPlaceholder || existingIds.size === 0) {
                stream.innerHTML = '';
                data.messages.forEach(msg => {
                    const temp = document.createElement('div');
                    temp.innerHTML = renderPopupMessageHTML(msg, data.user).trim();
                    stream.appendChild(temp.firstElementChild);
                });
                stream.scrollTop = stream.scrollHeight;
            } else {
                let hasNew = false;
                data.messages.forEach(msg => {
                    if (!existingIds.has(msg.id)) {
                        const temp = document.createElement('div');
                        temp.innerHTML = renderPopupMessageHTML(msg, data.user).trim();
                        const el = temp.firstElementChild;
                        el.classList.add('animate-msg-popup');
                        stream.appendChild(el);
                        hasNew = true;
                    }
                });

                if (hasNew) {
                    stream.scrollTo({ top: stream.scrollHeight, behavior: 'smooth' });
                }
            }
        } catch (e) {
            console.error(e);
        }
    }

    function startPopupReply(msgId, senderName, messageText) {
        popupReply = { id: msgId, name: senderName, text: messageText };
        const bar = document.getElementById('popupReplyPreviewBar');
        const senderEl = document.getElementById('popupReplySender');
        const snippetEl = document.getElementById('popupReplySnippet');
        const input = document.getElementById('popupChatInput');

        if (senderEl) senderEl.textContent = `Membalas ke: ${senderName}`;
        if (snippetEl) snippetEl.textContent = messageText.length > 50 ? messageText.substring(0, 50) + '...' : messageText;
        if (bar) bar.classList.remove('hidden');
        if (input) input.focus();
    }

    function cancelPopupReply() {
        popupReply = null;
        const bar = document.getElementById('popupReplyPreviewBar');
        if (bar) bar.classList.add('hidden');
    }

    async function handlePopupChatSend(e) {
        e.preventDefault();
        if (!activeChatUserId) return;

        const input = document.getElementById('popupChatInput');
        const text = input ? input.value.trim() : '';
        if (!text) return;

        const replyId = popupReply ? popupReply.id : null;
        input.value = '';
        cancelPopupReply();

        try {
            const res = await fetch(`/chat/messages/${activeChatUserId}`, {
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
                await fetchChatMessages();
                loadPopupConversations();
            }
        } catch (e) {
            console.error(e);
        }
    }

    async function deletePopupMessage(msgId) {
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
                loadPopupConversations();
            }
        } catch (e) {
            console.error(e);
        }
    }

    async function confirmClearPopupChat() {
        if (!activeChatUserId) return;
        const confirmed = await window.showAppConfirm({
            title: 'Bersihkan Seluruh Obrolan?',
            message: 'Seluruh riwayat pesan obrolan dengan pengguna ini akan dihapus secara permanen.',
            confirmText: 'Ya, Bersihkan',
            type: 'danger',
            icon: 'delete_sweep'
        });
        if (!confirmed) return;

        try {
            const res = await fetch(`/chat/clear/${activeChatUserId}`, {
                method: 'DELETE',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                }
            });
            if (res.ok) {
                const stream = document.getElementById('popupChatStream');
                if (stream) {
                    stream.innerHTML = '<div class="p-6 text-center text-xs text-slate-400">Riwayat obrolan telah dibersihkan.</div>';
                }
                cancelPopupReply();
                loadPopupConversations();
            }
        } catch (e) {
            console.error(e);
        }
    }

    async function confirmRemovePopupPerson() {
        if (!activeChatUserId) return;
        await removePopupConversationById(activeChatUserId, activeChatUserName || 'pengguna ini');
    }

    async function removePopupConversationById(userId, userName) {
        const confirmed = await window.showAppConfirm({
            title: 'Hapus Kontak Obrolan?',
            message: `Hapus ${userName} dari daftar kontak obrolan Anda? Riwayat pesan akan dibersihkan.`,
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
                if (activeChatUserId === userId) {
                    showPopupChatList();
                } else {
                    loadPopupConversations();
                }
            }
        } catch (e) {
            console.error(e);
        }
    }

    function toggleFloatingChat() {
        const popup = document.getElementById('floatingChatPopup');
        if (popup) {
            popup.classList.toggle('hidden');
            if (!popup.classList.contains('hidden')) {
                if (!activeChatUserId) {
                    loadPopupConversations();
                } else {
                    fetchChatMessages();
                }
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
        loadPopupConversations();
        listPollInterval = setInterval(loadPopupConversations, 5000);
    });
</script>
@endif
@endauth
