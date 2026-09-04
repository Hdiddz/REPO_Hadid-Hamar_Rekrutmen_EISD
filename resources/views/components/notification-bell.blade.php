@php
    $unreadCount = auth()->check() ? auth()->user()->unreadNotifications()->count() : 0;
@endphp

<style>
    @keyframes bellRing {
        0% { transform: rotate(0) scale(1); }
        15% { transform: rotate(18deg) scale(1.15); }
        30% { transform: rotate(-18deg) scale(1.15); }
        45% { transform: rotate(12deg) scale(1.1); }
        60% { transform: rotate(-12deg) scale(1.1); }
        75% { transform: rotate(6deg) scale(1.04); }
        85% { transform: rotate(-6deg) scale(1.02); }
        100% { transform: rotate(0) scale(1); }
    }
    .animate-bell-ring {
        animation: bellRing 0.65s cubic-bezier(0.36, 0.07, 0.19, 0.97) both;
        transform-origin: top center;
    }
    @keyframes notifDropdownPop {
        0% {
            opacity: 0;
            transform: scale(0.94) translateY(-6px);
        }
        100% {
            opacity: 1;
            transform: scale(1) translateY(0);
        }
    }
    .animate-dropdown-pop {
        animation: notifDropdownPop 0.22s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        transform-origin: top right;
    }
</style>

<div class="relative shrink-0" id="notificationDropdownContainer">
    {{-- Bell Button --}}
    <button type="button"
            data-menu-toggle="notification-dropdown-menu"
            id="notificationBellBtn"
            aria-expanded="false"
            aria-haspopup="true"
            class="relative inline-flex h-10 w-10 items-center justify-center rounded-xl text-slate-600 hover:bg-slate-100 hover:text-slate-900 active:scale-90 dark:text-slate-300 dark:hover:bg-slate-800 dark:hover:text-white transition-all duration-150 cursor-pointer select-none"
            aria-label="Lihat notifikasi">
        <span id="notificationBellIcon" class="material-symbols-outlined text-[22px] select-none pointer-events-none transition-transform">notifications</span>
        <span id="notificationBadge" class="{{ $unreadCount > 0 ? '' : 'hidden' }} absolute top-1.5 right-1.5 flex h-4 min-w-4 items-center justify-center rounded-full bg-rose-600 px-1 text-[10px] font-black text-white shadow-xs animate-pulse pointer-events-none">
            {{ $unreadCount > 9 ? '9+' : $unreadCount }}
        </span>
    </button>

    {{-- Dropdown Panel --}}
    <div id="notification-dropdown-menu" data-menu onclick="event.stopPropagation()" class="fixed inset-x-3 top-[68px] sm:absolute sm:inset-auto sm:right-0 sm:top-full sm:mt-2 hidden w-auto sm:w-96 max-w-[calc(100vw-1.5rem)] overflow-hidden rounded-3xl border border-slate-200/90 bg-white/95 shadow-2xl backdrop-blur-xl dark:border-slate-800 dark:bg-slate-900/95 z-50">
        {{-- Header --}}
        <div class="flex items-center justify-between border-b border-slate-100 px-4 py-3 dark:border-slate-800">
            <div class="flex items-center gap-2">
                <span class="grid h-7 w-7 place-items-center rounded-lg bg-brand-50 text-brand-700 dark:bg-brand-950 dark:text-brand-300">
                    <span class="material-symbols-outlined text-[17px]">notifications</span>
                </span>
                <span class="text-sm font-bold text-slate-900 dark:text-white">Notifikasi</span>
                <span id="notificationHeaderBadge" class="{{ $unreadCount > 0 ? '' : 'hidden' }} rounded-full bg-rose-100 dark:bg-rose-950/60 px-2 py-0.5 text-[10px] font-bold text-rose-700 dark:text-rose-300">
                    {{ $unreadCount }} baru
                </span>
            </div>
            <button type="button" onclick="markAllNotificationsRead(event)" class="text-[11px] font-semibold text-brand-700 hover:text-brand-800 hover:underline dark:text-brand-300 cursor-pointer">
                Tandai semua dibaca
            </button>
        </div>

        {{-- Notifications List Stream --}}
        <div id="notificationListStream" class="max-h-[calc(100dvh-12rem)] sm:max-h-[380px] overflow-y-auto divide-y divide-slate-100 dark:divide-slate-800/60">
            <div class="p-8 text-center text-xs text-slate-400">
                <span class="material-symbols-outlined text-3xl mb-1 text-slate-300 block animate-spin">sync</span>
                Memuat notifikasi...
            </div>
        </div>
    </div>
</div>

<script>
    (function() {
        let isNotificationLoaded = false;
        const notificationCache = new Map();

        async function fetchNotifications() {
            try {
                const res = await fetch('{{ route('notifications.index') }}', {
                    headers: { 'Accept': 'application/json' }
                });
                if (!res.ok) return;
                const data = await res.json();

                updateNotificationBadge(data.unread_count);
                renderNotificationList(data.notifications);
                isNotificationLoaded = true;
            } catch (e) {
                console.error(e);
            }
        }

        function updateNotificationBadge(count) {
            const badge = document.getElementById('notificationBadge');
            const headerBadge = document.getElementById('notificationHeaderBadge');

            if (badge) {
                if (count > 0) {
                    badge.textContent = count > 9 ? '9+' : count;
                    badge.classList.remove('hidden');
                } else {
                    badge.classList.add('hidden');
                }
            }

            if (headerBadge) {
                if (count > 0) {
                    headerBadge.textContent = `${count} baru`;
                    headerBadge.classList.remove('hidden');
                } else {
                    headerBadge.classList.add('hidden');
                }
            }
        }

        function renderNotificationList(items) {
            const stream = document.getElementById('notificationListStream');
            if (!stream) return;

            notificationCache.clear();

            if (!items || items.length === 0) {
                stream.innerHTML = `
                    <div class="p-8 text-center text-slate-400 dark:text-slate-500">
                        <span class="material-symbols-outlined text-4xl text-slate-300 dark:text-slate-600 mb-2 block">notifications_off</span>
                        <p class="text-xs font-bold text-slate-700 dark:text-slate-300">Belum Ada Notifikasi</p>
                        <p class="text-[11px] text-slate-400 mt-1 max-w-[220px] mx-auto leading-relaxed">Pembaruan seleksi lamaran dan aktivitas akun Anda akan muncul di sini.</p>
                    </div>
                `;
                return;
            }

            stream.innerHTML = items.map(item => {
                notificationCache.set(item.id, item);

                const d = item.data || {};
                const isUnread = !item.is_read;

                let iconColor = 'text-brand-700 bg-brand-50 dark:bg-brand-950 dark:text-brand-300';
                if (d.status === 'accepted') {
                    iconColor = 'text-emerald-700 bg-emerald-50 dark:bg-emerald-950 dark:text-emerald-300';
                } else if (d.status === 'interview') {
                    iconColor = 'text-indigo-700 bg-indigo-50 dark:bg-indigo-950 dark:text-indigo-300';
                } else if (d.status === 'rejected') {
                    iconColor = 'text-rose-700 bg-rose-50 dark:bg-rose-950 dark:text-rose-300';
                } else if (d.status === 'reviewed') {
                    iconColor = 'text-amber-700 bg-amber-50 dark:bg-amber-950 dark:text-amber-300';
                } else if (d.status === 'application_submitted') {
                    iconColor = 'text-teal-700 bg-teal-50 dark:bg-teal-950 dark:text-teal-300';
                } else if (d.status === 'new_applicant') {
                    iconColor = 'text-blue-700 bg-blue-50 dark:bg-blue-950 dark:text-blue-300';
                } else if (d.status === 'report_pending' || d.status === 'report_employer_action') {
                    iconColor = 'text-rose-700 bg-rose-50 dark:bg-rose-950 dark:text-rose-300';
                } else if (d.status === 'report_reviewed') {
                    iconColor = 'text-amber-700 bg-amber-50 dark:bg-amber-950 dark:text-amber-300';
                } else if (d.status === 'report_action_taken') {
                    iconColor = 'text-emerald-700 bg-emerald-50 dark:bg-emerald-950 dark:text-emerald-300';
                } else if (d.status === 'report_dismissed') {
                    iconColor = 'text-slate-700 bg-slate-100 dark:bg-slate-800 dark:text-slate-300';
                }

                return `
                    <div id="notif-item-${item.id}" onclick="openNotificationDetail('${item.id}')" class="group relative flex items-start gap-3 p-3.5 transition hover:bg-slate-50 dark:hover:bg-slate-800/60 cursor-pointer ${isUnread ? 'bg-brand-50/25 dark:bg-brand-950/20' : ''}">
                        <div class="h-9 w-9 rounded-2xl flex items-center justify-center shrink-0 ${iconColor}">
                            <span class="material-symbols-outlined text-[19px]">${d.icon || 'notifications'}</span>
                        </div>
                        <div class="flex-1 min-w-0 pr-1">
                            <div class="flex items-center justify-between gap-1.5">
                                <h4 class="text-xs font-bold text-slate-900 dark:text-white truncate ${isUnread ? 'font-extrabold text-brand-900 dark:text-brand-100' : ''}">
                                    ${escapeHtml(d.title || 'Pemberitahuan')}
                                </h4>
                                <div class="flex items-center gap-1.5 shrink-0">
                                    <span class="text-[10px] text-slate-400">${item.created_at}</span>
                                    <button type="button"
                                            onclick="deleteNotification(event, '${item.id}')"
                                            class="flex h-5 w-5 items-center justify-center rounded-md text-slate-400 hover:bg-rose-50 hover:text-rose-600 dark:hover:bg-rose-950/60 dark:hover:text-rose-400 transition cursor-pointer"
                                            title="Hapus notifikasi (-)"
                                            aria-label="Hapus notifikasi">
                                        <span class="material-symbols-outlined text-[15px]">remove</span>
                                    </button>
                                </div>
                            </div>
                            <p class="mt-0.5 text-xs text-slate-600 dark:text-slate-300 leading-relaxed line-clamp-2">
                                ${escapeHtml(d.message || '')}
                            </p>
                            <div class="mt-1.5 flex items-center justify-between gap-2 min-w-0">
                                ${d.employer_name ? `<span class="inline-block text-[10px] font-semibold text-brand-700 dark:text-brand-300 truncate min-w-0 flex-1">${escapeHtml(d.employer_name)}</span>` : '<span class="flex-1"></span>'}
                                <span class="text-[10px] font-bold text-brand-600 hover:text-brand-700 dark:text-brand-400 inline-flex items-center gap-0.5 shrink-0">
                                    Lihat rincian <span class="material-symbols-outlined text-[13px]">chevron_right</span>
                                </span>
                            </div>
                        </div>
                        ${isUnread ? '<span class="h-2 w-2 rounded-full bg-brand-600 shrink-0 mt-1.5"></span>' : ''}
                    </div>
                `;
            }).join('');
        }

        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text || '';
            return div.innerHTML;
        }

        window.openNotificationDetail = async function(id) {
            const item = notificationCache.get(id);
            if (!item) return;

            const d = item.data || {};

            // 1. Isi konten modal
            document.getElementById('notifDetailTitle').textContent = d.title || 'Pemberitahuan';
            document.getElementById('notifDetailMessage').textContent = d.message || '';
            document.getElementById('notifDetailTime').textContent = item.created_at || 'Baru saja';
            document.getElementById('notifDetailIcon').textContent = d.icon || 'notifications';

            const empBox = document.getElementById('notifDetailEmployerBox');
            const empText = document.getElementById('notifDetailEmployer');
            if (d.employer_name) {
                empText.textContent = d.employer_name;
                empBox.classList.remove('hidden');
            } else {
                empBox.classList.add('hidden');
            }

            const iconBox = document.getElementById('notifDetailIconBox');
            iconBox.className = 'flex h-11 w-11 items-center justify-center rounded-2xl shrink-0';
            if (d.status === 'accepted') {
                iconBox.classList.add('text-emerald-700', 'bg-emerald-50', 'dark:bg-emerald-950', 'dark:text-emerald-300');
            } else if (d.status === 'interview') {
                iconBox.classList.add('text-indigo-700', 'bg-indigo-50', 'dark:bg-indigo-950', 'dark:text-indigo-300');
            } else if (d.status === 'rejected') {
                iconBox.classList.add('text-rose-700', 'bg-rose-50', 'dark:bg-rose-950', 'dark:text-rose-300');
            } else if (d.status === 'application_submitted') {
                iconBox.classList.add('text-teal-700', 'bg-teal-50', 'dark:bg-teal-950', 'dark:text-teal-300');
            } else if (d.status === 'new_applicant') {
                iconBox.classList.add('text-blue-700', 'bg-blue-50', 'dark:bg-blue-950', 'dark:text-blue-300');
            } else if (d.status === 'report_pending' || d.status === 'report_employer_action') {
                iconBox.classList.add('text-rose-700', 'bg-rose-50', 'dark:bg-rose-950', 'dark:text-rose-300');
            } else if (d.status === 'report_reviewed') {
                iconBox.classList.add('text-amber-700', 'bg-amber-50', 'dark:bg-amber-950', 'dark:text-amber-300');
            } else if (d.status === 'report_action_taken') {
                iconBox.classList.add('text-emerald-700', 'bg-emerald-50', 'dark:bg-emerald-950', 'dark:text-emerald-300');
            } else if (d.status === 'report_dismissed') {
                iconBox.classList.add('text-slate-700', 'bg-slate-100', 'dark:bg-slate-800', 'dark:text-slate-300');
            } else {
                iconBox.classList.add('text-brand-700', 'bg-brand-50', 'dark:bg-brand-950', 'dark:text-brand-300');
            }

            const actionBtn = document.getElementById('notifDetailActionBtn');
            if (d.url) {
                actionBtn.href = d.url;
                actionBtn.classList.remove('hidden');
            } else {
                actionBtn.classList.add('hidden');
            }

            // 2. Tutup dropdown notifikasi & tampilkan modal dialog di tengah layar
            const dropdown = document.getElementById('notification-dropdown-menu');
            if (dropdown) {
                dropdown.classList.add('hidden');
            }

            const modal = document.getElementById('notificationDetailModal');
            if (modal) {
                modal.classList.remove('hidden');
                modal.classList.add('flex');
                document.body.classList.add('overflow-hidden');
            }

            // 3. Tandai notifikasi telah dibaca secara diam-diam (AJAX)
            try {
                await fetch(`/notifikasi/${id}/baca`, {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                });
                item.is_read = true;
                item.read_at = new Date().toISOString();
                fetchNotifications();
            } catch (_) {}
        };

        window.closeNotificationDetailModal = function() {
            const modal = document.getElementById('notificationDetailModal');
            if (modal) {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
                document.body.classList.remove('overflow-hidden');
            }
        };

        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                window.closeNotificationDetailModal();
            }
        });

        window.markAllNotificationsRead = async function(event) {
            if (event) {
                event.stopPropagation();
                event.preventDefault();
            }

            try {
                const res = await fetch('{{ route('notifications.readAll') }}', {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                });
                if (res.ok) {
                    fetchNotifications();
                }
            } catch (e) {
                console.error(e);
            }
        };

        window.deleteNotification = async function(event, id) {
            if (event) {
                event.stopPropagation();
                event.preventDefault();
            }

            const itemEl = document.getElementById('notif-item-' + id);
            if (itemEl) {
                itemEl.style.transition = 'opacity 200ms ease, transform 200ms ease';
                itemEl.style.opacity = '0';
                itemEl.style.transform = 'translateX(20px)';
            }

            try {
                const res = await fetch(`/notifikasi/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                });

                if (res.ok) {
                    const data = await res.json();
                    updateNotificationBadge(data.unread_count);
                    notificationCache.delete(id);

                    setTimeout(() => {
                        fetchNotifications();
                    }, 220);
                }
            } catch (e) {
                console.error(e);
                fetchNotifications();
            }
        };

        document.addEventListener('DOMContentLoaded', () => {
            fetchNotifications();
            // Polling interval setiap 8 detik untuk notifikasi baru
            setInterval(fetchNotifications, 8000);

            const bellBtn = document.getElementById('notificationBellBtn');
            const bellIcon = document.getElementById('notificationBellIcon');
            const dropdownMenu = document.getElementById('notification-dropdown-menu');

            if (bellBtn) {
                bellBtn.addEventListener('click', () => {
                    if (bellIcon) {
                        bellIcon.classList.remove('animate-bell-ring');
                        void bellIcon.offsetWidth; // trigger reflow
                        bellIcon.classList.add('animate-bell-ring');
                    }

                    setTimeout(() => {
                        if (dropdownMenu && !dropdownMenu.classList.contains('hidden')) {
                            dropdownMenu.classList.remove('animate-dropdown-pop');
                            void dropdownMenu.offsetWidth; // trigger reflow
                            dropdownMenu.classList.add('animate-dropdown-pop');
                        }
                    }, 10);
                });
            }
        });
    })();
</script>
