/* public/js/chat_v2.js — Modern Premium Chat Logic */
(function () {
    'use strict';

    /* ── State ────────────────────────────────────────────────── */
    let currentConvId = window.CURRENT_CONV_ID || null;
    let lastTimestamp = null;
    let pollTimer = null;
    let isOpen = false;
    let convList = [];
    let isStartingNewChat = false; // Flag to prevent auto-select during specific chat start

    /* ── Elements ─────────────────────────────────────────────── */
    const isFullPage = document.querySelector('.chat-page-wrapper') !== null;
    
    // Widget Specific
    const bubble = document.getElementById('wgtBubble');
    const badge = document.getElementById('wgtBadge');
    const widgetWindow = document.getElementById('wgtWindow');
    const widgetCloseBtn = document.getElementById('wgtCloseBtn');
    
    // Common Elements
    const convListEl = document.getElementById(isFullPage ? 'sidebar-conv-list' : 'wgtConvList');
    const convEmpty = document.getElementById(isFullPage ? 'chat-pane-empty' : 'wgtConvEmpty');
    const activePane = document.getElementById(isFullPage ? 'chat-pane-active' : 'wgtChatPane');
    const messagesEl = document.getElementById(isFullPage ? 'chat-messages-body' : 'wgtMessages');
    const textarea = document.getElementById(isFullPage ? 'full-chat-input' : 'wgtTextarea');
    const sendBtn = document.getElementById(isFullPage ? 'full-chat-send' : 'wgtSendBtn');
    const fileInput = document.getElementById(isFullPage ? 'full-chat-file' : 'wgtFileInput');
    const searchInput = document.getElementById(isFullPage ? 'sidebar-search' : 'wgtSearchInput');
    const paneAvatar = document.getElementById(isFullPage ? 'active-conv-avatar' : 'wgtPaneAvatar');
    const paneName = document.getElementById(isFullPage ? 'active-conv-name' : 'wgtPaneName');
    
    // Mobile Back Button
    const mobileBackBtn = document.getElementById('mobile-back-btn');
    const sidebar = document.getElementById('full-chat-sidebar');

    /* ── Initialization ──────────────────────────────────────── */
    if (isFullPage) {
        loadConversations();
        if (currentConvId) {
            // Find conv in list and select it after loading
            setTimeout(() => {
                const conv = convList.find(c => c.id == currentConvId);
                if (conv) selectConv(conv);
            }, 500);
        }
    } else {
        const minimizeBtn = document.getElementById('wgtMinimizeBtn');
        if (bubble) bubble.addEventListener('click', toggleWidget);
        if (widgetCloseBtn) widgetCloseBtn.addEventListener('click', closeWidget);
        if (minimizeBtn) minimizeBtn.addEventListener('click', closeWidget);
    }

    if (mobileBackBtn) {
        mobileBackBtn.onclick = () => {
            if (sidebar) sidebar.classList.remove('hidden');
        };
    }

    /* ── Actions ─────────────────────────────────────────────── */
    window.chatAction = {
        selectById: function(id) {
            const conv = convList.find(c => c.id == id);
            if (conv) selectConv(conv);
        },
        open: function() {
            openWidget();
        },
        close: function() {
            closeWidget();
        }
    };

    // Sidebar Back to Top
    const socialBackToTop = document.getElementById('socialBackToTop');
    if (socialBackToTop) {
        socialBackToTop.onclick = () => window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    /* ── Toggle Widget ────────────────────────────────────────── */
    function toggleWidget() {
        if (isOpen) closeWidget(); else openWidget();
    }

    function openWidget() {
        isOpen = true;
        if (widgetWindow) widgetWindow.classList.add('open');
        loadConversations();
    }

    function closeWidget() {
        isOpen = false;
        if (widgetWindow) widgetWindow.classList.remove('open');
        stopPolling();
    }

    /* ── Conversations ────────────────────────────────────────── */
    function loadConversations() {
        fetch(BASE_URL + 'index.php?url=Chat/conversations')
            .then(r => r.json())
            .then(data => {
                if (!data.success) return;
                convList = data.conversations || [];
                renderConvList(convList);
                
                const totalUnread = convList.reduce((s, c) => s + (c.unread || 0), 0);
                updateBubbleBadge(totalUnread);

                // Auto-select first conversation for Widget (Customer)
                if (!isFullPage && convList.length > 0 && !currentConvId && !isStartingNewChat) {
                    selectConv(convList[0]);
                }
                isStartingNewChat = false; // Reset after first load
            })
            .catch(err => console.error('Load conv error:', err));
    }

    function renderConvList(list) {
        if (!convListEl) return;
        
        if (list.length === 0) {
            convListEl.innerHTML = '<div class="text-center p-5 text-muted">Chưa có hội thoại nào</div>';
            return;
        }

        const html = list.map(c => {
            const preview = c.last_message ? escHtml(c.last_message.substring(0, 35)) + (c.last_message.length > 35 ? '...' : '') : 'Bắt đầu trò chuyện...';
            const unread = c.unread > 0 ? `<span class="chat-unread-badge">${c.unread}</span>` : '';
            const active = c.id == currentConvId ? 'active' : '';
            const avatar = c.display_avatar_url || `https://ui-avatars.com/api/?name=${encodeURIComponent(c.display_name)}&background=random`;
            const time = c.last_message_at ? c.last_message_at.substring(11, 16) : '';

            return `
                <div class="chat-conv-item ${active}" data-id="${c.id}" onclick="window.chatAction.selectById(${c.id})">
                    <div class="chat-conv-avatar">
                        <img src="${escAttr(avatar)}" alt="">
                        <span class="chat-status-dot"></span>
                    </div>
                    <div class="chat-conv-info">
                        <div class="chat-conv-name">${escHtml(c.display_name)}</div>
                        <div class="chat-conv-preview">${preview}</div>
                    </div>
                    <div class="chat-conv-meta">
                        <div class="chat-conv-time">${time}</div>
                        ${unread}
                    </div>
                </div>`;
        }).join('');

        convListEl.innerHTML = html;
    }

    /* ── Selection ────────────────────────────────────────────── */
    function selectConv(conv) {
        if (!conv) return;
        currentConvId = conv.id;
        lastTimestamp = null;

        // UI Updates
        if (convEmpty) convEmpty.style.display = 'none';
        if (activePane) activePane.style.display = 'flex';
        
        // Mobile Sidebar handling
        if (isFullPage && window.innerWidth <= 768) {
            if (sidebar) sidebar.classList.add('hidden');
        }

        // Highlight selected
        document.querySelectorAll('.chat-conv-item').forEach(el => {
            el.classList.toggle('active', el.dataset.id == conv.id);
        });

        // Header
        if (paneAvatar) paneAvatar.src = conv.display_avatar_url || `https://ui-avatars.com/api/?name=${encodeURIComponent(conv.display_name)}&background=random`;
        if (paneName) paneName.textContent = conv.display_name;

        // Load Messages
        loadMessages(true);
        startPolling();
    }

    /* ── Messages ─────────────────────────────────────────────── */
    function loadMessages(fullReload = false) {
        if (!currentConvId) return;

        let url = `${BASE_URL}index.php?url=Chat/history&conv_id=${currentConvId}`;
        if (!fullReload && lastTimestamp) {
            url += `&since=${encodeURIComponent(lastTimestamp)}`;
        }

        fetch(url)
            .then(r => r.json())
            .then(data => {
                if (!data.success) return;

                if (data.messages && data.messages.length > 0) {
                    lastTimestamp = data.messages[data.messages.length - 1].created_at;
                }

                if (fullReload) {
                    messagesEl.innerHTML = buildMessagesHTML(data.messages);
                    scrollToBottom();
                } else if (data.messages && data.messages.length > 0) {
                    const html = buildMessagesHTML(data.messages, lastTimestamp);
                    messagesEl.insertAdjacentHTML('beforeend', html);
                    scrollToBottom();
                }
            })
            .catch(err => console.error('Load msg error:', err));
    }

    function buildMessagesHTML(messages, lastMsgTs = null) {
        let html = '';
        let lastDate = '';
        
        messages.forEach(msg => {
            const ts = new Date(msg.created_at.replace(' ', 'T'));
            const dateKey = ts.toLocaleDateString('vi-VN');
            
            if (dateKey !== lastDate) {
                const today = new Date().toLocaleDateString('vi-VN');
                const label = dateKey === today ? 'Hôm nay' : dateKey;
                html += `<div class="wgt-date-sep"><span>${label}</span></div>`;
                lastDate = dateKey;
            }

            const isSent = msg.sender_id == USER_ID;
            const time = `${String(ts.getHours()).padStart(2, '0')}:${String(ts.getMinutes()).padStart(2, '0')}`;
            const avatar = msg.sender_avatar 
                ? `${BASE_URL}public/uploads/avatars/${msg.sender_avatar}` 
                : `https://ui-avatars.com/api/?name=${encodeURIComponent(msg.sender_name)}&background=random`;

            let content = '';
            if (msg.message_type === 'image') {
                content = `<img src="${BASE_URL}${msg.attachment_url}" onclick="window.open(this.src)" alt="Image">`;
            } else if (msg.message_type === 'product') {
                try {
                    const p = JSON.parse(msg.content);
                    content = `
                        <a href="${BASE_URL}index.php?url=Product/show/${p.id}" class="msg-product-card">
                            <div class="msg-product-body">
                                <img src="${fixImageUrl(p.image)}" alt="">
                                <div class="msg-product-info">
                                    <div class="msg-product-name">${escHtml(p.name)}</div>
                                    <div class="msg-product-price">${p.price}</div>
                                </div>
                            </div>
                        </a>`;
                } catch(e) { content = escHtml(msg.content); }
            } else {
                content = escHtml(msg.content || '').replace(/\n/g, '<br>');
            }

            html += `
                <div class="wgt-msg-row ${isSent ? 'sent' : 'received'}" data-id="${msg.id}">
                    ${!isSent ? `<img src="${avatar}" class="avatar" alt="">` : ''}
                    <div class="wgt-msg-body">
                        <div class="wgt-bubble type-${msg.message_type}">${content}</div>
                        <div class="wgt-msg-time">${time}</div>
                    </div>
                </div>`;
        });
        return html;
    }

    function scrollToBottom() {
        if (messagesEl) {
            messagesEl.scrollTo({ top: messagesEl.scrollHeight, behavior: 'smooth' });
        }
    }

    /* ── Actions ─────────────────────────────────────────────── */
    function sendText() {
        const text = textarea.value.trim();
        if (!text || !currentConvId) return;

        const fd = new FormData();
        fd.append('conversation_id', currentConvId);
        fd.append('type', 'text');
        fd.append('content', text);

        fetch(`${BASE_URL}index.php?url=Chat/send`, { method: 'POST', body: fd })
            .then(r => r.json())
            .then(d => {
                if (d.success) {
                    textarea.value = '';
                    loadMessages();
                }
            });
    }

    if (sendBtn) sendBtn.onclick = sendText;
    if (textarea) {
        textarea.onkeydown = e => { if (e.key === 'Enter' && !e.shiftKey) { e.preventDefault(); sendText(); } };
    }

    if (fileInput) {
        fileInput.onchange = function() {
            const file = this.files[0];
            if (!file || !currentConvId) return;
            const fd = new FormData();
            fd.append('conversation_id', currentConvId);
            fd.append('type', 'image');
            fd.append('attachment', file);
            fetch(`${BASE_URL}index.php?url=Chat/send`, { method: 'POST', body: fd })
                .then(r => r.json())
                .then(d => { if (d.success) loadMessages(); });
            this.value = '';
        };
    }

    /* ── Polling ──────────────────────────────────────────────── */
    function startPolling() {
        stopPolling();
        pollTimer = setInterval(() => loadMessages(), 3000);
    }

    function stopPolling() {
        if (pollTimer) clearInterval(pollTimer);
    }

    /* ── Helpers ──────────────────────────────────────────────── */
    function escHtml(s) { return String(s||'').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;'); }
    function escAttr(s) { return escHtml(s); }
    function fixImageUrl(url) {
        if (!url) return '';
        if (url.startsWith('http')) return url;
        return BASE_URL + (url.startsWith('/') ? url.substring(1) : url);
    }
    function updateBubbleBadge(n) {
        if (badge) {
            badge.textContent = n;
            badge.classList.toggle('show', n > 0);
        }
    }

    /* ── Global: open chat from product/order page ───────────── */
    document.addEventListener('click', function (e) {
        const btn = e.target.closest('.open-chat-with-product');
        if (!btn) return;

        const sellerId = btn.dataset.sellerId || 0;
        isStartingNewChat = true; 

        // 1. Open widget
        if (!isOpen) openWidget();
        
        // Clear current state to avoid sending to wrong person during load
        currentConvId = null; 
        if (messagesEl) messagesEl.innerHTML = '<div class="text-center p-4"><i class="fas fa-spinner fa-spin"></i> Đang kết nối với người bán...</div>';

        // 2. Load or create conversation with this seller
        fetch(`${BASE_URL}index.php?url=Chat/history&seller_id=${sellerId}`)
            .then(r => r.json())
            .then(data => {
                if (data.success && data.conversation_id) {
                    const existing = convList.find(c => c.id == data.conversation_id);
                    if (existing) {
                        selectConv(existing);
                    } else if (data.conversation) {
                        // Add to list then select
                        convList.unshift(data.conversation);
                        renderConvList(convList);
                        selectConv(data.conversation);
                    }
                }
            })
            .catch(err => console.error('Start chat error:', err));
    });

})();
