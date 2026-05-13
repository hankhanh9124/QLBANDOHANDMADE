/* public/js/chat.js — Floating Chat Widget */
(function () {
    'use strict';

    /* ── State ────────────────────────────────────────────────── */
    let currentConvId = null;
    let lastTimestamp = null;
    let pollTimer = null;
    let isOpen = false;
    let convList = [];

    /* ── Elements ─────────────────────────────────────────────── */
    const bubble = document.getElementById('wgtBubble');
    const badge = document.getElementById('wgtBadge');
    const window_ = document.getElementById('wgtWindow');
    const closeBtn = document.getElementById('wgtCloseBtn');
    const convListEl = document.getElementById('wgtConvList');
    const convEmpty = document.getElementById('wgtConvEmpty');
    const convMenu = document.getElementById('wgtConvMenu');
    const messagesEl = document.getElementById('wgtMessages');
    const textarea = document.getElementById('wgtTextarea');
    const sendBtn = document.getElementById('wgtSendBtn');
    const imgBtn = document.getElementById('wgtImgBtn');
    const fileInput = document.getElementById('wgtFileInput');
    const searchInput = document.getElementById('wgtSearchInput');
    const productCard = document.getElementById('wgtProductCard');
    const paneAvatar = document.getElementById('wgtPaneAvatar');
    const paneName = document.getElementById('wgtPaneName');
    const disclaimer = document.getElementById('wgtDisclaimer');

    /* Selector Elements */
    const selectorOverlay = document.getElementById('wgtProductSelector');
    const selectorList = document.getElementById('wgtSelectorList');
    const selectorSearch = document.getElementById('wgtSelectorSearchInput');
    const closeSelector = document.getElementById('wgtCloseSelector');
    const changeProductBtn = document.getElementById('wgtProductChangeBtn');

    if (!bubble) return; // widget not on page

    /* ── Toggle ───────────────────────────────────────────────── */
    bubble.addEventListener('click', toggleWidget);
    if (closeBtn) closeBtn.addEventListener('click', closeWidget);

    function toggleWidget() {
        if (isOpen) closeWidget(); else openWidget();
    }

    function openWidget() {
        isOpen = true;
        window_.classList.add('open');
        loadConversations();
    }

    function closeWidget() {
        isOpen = false;
        window_.classList.remove('open');
        stopPolling();
    }

    /* ── Load conversation list ───────────────────────────────── */
    function loadConversations() {
        fetch(BASE_URL + 'index.php?url=Chat/conversations')
            .then(r => r.json())
            .then(data => {
                if (!data.success) return;
                convList = data.conversations || [];
                renderConvList(convList);

                // Auto-select first
                if (convList.length > 0 && !currentConvId) {
                    selectConv(convList[0]);
                }

                // Overall unread badge on bubble
                const totalUnread = convList.reduce((s, c) => s + (c.unread || 0), 0);
                updateBubbleBadge(totalUnread);
            })
            .catch(() => { });
    }

    function renderConvList(list) {
        if (!convListEl) return;
        if (list.length === 0) {
            convListEl.innerHTML = '';
            convEmpty && convEmpty.removeAttribute('style');
            return;
        }
        convEmpty && (convEmpty.style.display = 'none');

        const html = list.map(c => {
            const preview = c.last_message
                ? escHtml(c.last_message.substring(0, 28))
                : 'Bắt đầu cuộc trò chuyện...';
            const unread = c.unread > 0
                ? `<span class="wgt-conv-badge">${c.unread}</span>`
                : '';
            const active = c.id == currentConvId ? 'active' : '';
            const pinned = c.is_pinned == 1 ? '<i class="fas fa-thumbtack wgt-pin-icon"></i>' : '';
            const muted = c.is_muted == 1 ? 'muted' : '';

            const displayName = IS_ADMIN ? c.customer_name : SHOP_NAME;
            const displayAvatar = IS_ADMIN ? c.customer_avatar_url : SHOP_AVATAR;

            return `<div class="wgt-conv-item ${active} ${muted}" data-id="${c.id}"
                         data-name="${escAttr(displayName)}"
                         data-pinned="${c.is_pinned}"
                         data-muted="${c.is_muted}">
                        <img src="${escAttr(displayAvatar)}" alt="">
                        <div class="wgt-conv-info">
                            <div class="wgt-conv-name">
                                ${escHtml(displayName)}
                                ${pinned}
                            </div>
                            <div class="wgt-conv-preview">${preview}</div>
                        </div>
                        ${unread}
                        <div class="wgt-conv-chevron" data-id="${c.id}">
                            <i class="fas fa-chevron-down"></i>
                        </div>
                    </div>`;
        }).join('');

        convListEl.innerHTML = html;

        convListEl.querySelectorAll('.wgt-conv-item').forEach(el => {
            el.addEventListener('click', (e) => {
                // If clicked on chevron, don't select conversation
                if (e.target.closest('.wgt-conv-chevron')) {
                    e.stopPropagation();
                    const chevron = e.target.closest('.wgt-conv-chevron');
                    openConvMenu(chevron, el.dataset.id);
                    return;
                }

                const id = parseInt(el.dataset.id);
                const conv = convList.find(c => c.id == id);
                if (conv) selectConv(conv);
            });
        });
    }

    /* ── Conversation Menu Logic ─────────────────────────────── */
    function openConvMenu(anchor, convId) {
        if (!convMenu) return;

        const rect = anchor.getBoundingClientRect();
        convMenu.style.top = (rect.bottom + 5) + 'px';
        convMenu.style.left = (rect.right - 190) + 'px';
        convMenu.style.display = 'block';
        convMenu.dataset.activeId = convId;
    }

    // Close menu when clicking outside
    document.addEventListener('click', (e) => {
        if (convMenu && !e.target.closest('.wgt-conv-chevron') && !e.target.closest('.wgt-conv-menu')) {
            convMenu.style.display = 'none';
        }
    });

    // Handle menu actions
    if (convMenu) {
        convMenu.querySelectorAll('.wgt-conv-menu-item').forEach(item => {
            item.addEventListener('click', () => {
                const action = item.dataset.action;
                const convId = convMenu.dataset.activeId;
                const convItem = convListEl.querySelector(`.wgt-conv-item[data-id="${convId}"]`);

                if (!convId || !convItem) return;

                let value = 0;
                if (action === 'pin') value = convItem.dataset.pinned == '1' ? 0 : 1;
                if (action === 'mute') value = convItem.dataset.muted == '1' ? 0 : 1;

                if (action === 'delete') {
                    if (!confirm('Bạn có chắc muốn xóa cuộc trò chuyện này?')) return;
                }

                const formData = new FormData();
                formData.append('conv_id', convId);
                formData.append('action', action);
                formData.append('value', value);

                fetch(`${BASE_URL}index.php?url=Chat/manage`, {
                    method: 'POST',
                    body: formData
                })
                    .then(res => res.json())
                    .then(res => {
                        if (res.success) {
                            convMenu.style.display = 'none';

                            // Nếu đang mở chính hội thoại vừa xóa, hãy xóa nội dung trên màn hình
                            if (currentConvId == convId) {
                                currentConvId = null;
                                paneName.textContent = 'Chọn hội thoại';
                                paneAvatar.src = '';
                                if (messagesEl) messagesEl.innerHTML = '';
                                if (textarea) textarea.value = '';
                            }

                            loadConversations();
                        } else {
                            alert(res.message || 'Thao tác thất bại');
                        }
                    })
                    .catch(err => {
                        console.error('Delete error:', err);
                        alert('Đã xảy ra lỗi khi kết nối máy chủ');
                    });
            });
        });
    }

    /* ── Select conversation ──────────────────────────────────── */
    function selectConv(conv) {
        currentConvId = conv.id;
        lastTimestamp = null;

        // Update active state
        convListEl.querySelectorAll('.wgt-conv-item').forEach(el => {
            el.classList.toggle('active', parseInt(el.dataset.id) === conv.id);
        });

        // Update pane header
        paneAvatar.src = conv.display_avatar_url || (IS_ADMIN ? conv.customer_avatar_url : SHOP_AVATAR);
        paneName.textContent = conv.display_name || (IS_ADMIN ? conv.customer_name : SHOP_NAME);

        // Load messages
        loadMessages(true);
        startPolling();
    }

    /* ── Load / poll messages ─────────────────────────────────── */
    function loadMessages(fullReload = false) {
        if (!currentConvId) return;

        let url = BASE_URL + 'index.php?url=Chat/history&conv_id=' + currentConvId;
        if (!fullReload && lastTimestamp) {
            url += '&since=' + encodeURIComponent(lastTimestamp);
        }

        fetch(url)
            .then(r => r.json())
            .then(data => {
                if (!data.success) return;
                lastTimestamp = data.server_time;

                if (fullReload) {
                    renderMessages(data.messages);
                } else if (data.messages && data.messages.length > 0) {
                    appendMessages(data.messages);
                }
            })
            .catch(() => { });
    }

    function renderMessages(messages) {
        // Keep disclaimer and product card
        const keeps = [];
        if (productCard) keeps.push(productCard.outerHTML.replace('style="display:none"', 'style="display:none"'));
        const disclaimerEl = document.getElementById('wgtDisclaimer');
        if (disclaimerEl) keeps.push(disclaimerEl.outerHTML);

        // Remove old dynamic messages
        const oldMsgs = messagesEl.querySelectorAll('.wgt-msg-row, .wgt-date-sep');
        oldMsgs.forEach(el => el.remove());

        const fragment = buildMessagesHTML(messages);
        messagesEl.insertAdjacentHTML('beforeend', fragment);
        scrollToBottom();
    }

    function appendMessages(messages) {
        const fragment = buildMessagesHTML(messages);
        messagesEl.insertAdjacentHTML('beforeend', fragment);
        scrollToBottom();
    }

    function buildMessagesHTML(messages) {
        let html = '';
        let lastDate = '';

        messages.forEach(msg => {
            const ts = new Date(msg.created_at.replace(' ', 'T'));
            const dateKey = ts.toLocaleDateString('vi-VN');

            if (dateKey !== lastDate) {
                const today = new Date().toLocaleDateString('vi-VN');
                const yesterday = new Date(Date.now() - 86400000).toLocaleDateString('vi-VN');
                const label = dateKey === today ? 'Hôm nay' : (dateKey === yesterday ? 'Hôm qua' : dateKey);
                html += `<div class="wgt-date-sep">${label}</div>`;
                lastDate = dateKey;
            }

            const isSent = msg.sender_id == USER_ID;
            const rowClass = isSent ? 'sent' : 'received';
            const timeStr = ts.toLocaleTimeString('vi-VN', { hour: '2-digit', minute: '2-digit' });
            const avatarUrl = msg.sender_avatar
                ? BASE_URL + 'public/uploads/avatars/' + msg.sender_avatar
                : `https://ui-avatars.com/api/?name=${encodeURIComponent(msg.sender_name || 'U')}&background=2563eb&color=fff&size=60`;

            let content = '';
            if (msg.message_type === 'image') {
                content = `<img src="${BASE_URL}${escAttr(msg.attachment_url)}" onclick="window.open(this.src)" alt="Ảnh">`;
            } else if (msg.message_type === 'video') {
                content = `<video src="${BASE_URL}${escAttr(msg.attachment_url)}" controls style="max-width:150px"></video>`;
            } else if (msg.content && msg.content.startsWith('[P]:')) {
                // Render product card bubble
                try {
                    const data = JSON.parse(msg.content.substring(4));
                    content = `
                        <a href="${BASE_URL}index.php?url=Product/show/${data.id}" class="msg-product-card">
                            <span class="msg-product-header">Sản phẩm</span>
                            <div class="msg-product-body">
                                <img src="${escAttr(data.image)}" alt="">
                                <div class="msg-product-info">
                                    <div class="msg-product-name">${escHtml(data.name)}</div>
                                    <div class="msg-product-price-row">
                                        <span class="msg-product-price">${formatPrice(data.price)}</span>
                                        ${data.old_price ? `<span class="msg-product-old-price">${formatPrice(data.old_price)}</span>` : ''}
                                    </div>
                                </div>
                            </div>
                        </a>`;
                } catch (e) {
                    content = escHtml(msg.content).replace(/\n/g, '<br>');
                }
            } else {
                content = escHtml(msg.content || '').replace(/\n/g, '<br>');
            }

            html += `
                <div class="wgt-msg-row ${rowClass}">
                    ${!isSent ? `<img class="avatar" src="${escAttr(avatarUrl)}" alt="">` : ''}
                    <div class="wgt-msg-body">
                        <div class="wgt-bubble ${msg.content && msg.content.startsWith('[P]:') ? 'p-0' : ''}">${content}</div>
                        <div class="wgt-msg-time">${timeStr}${isSent && msg.is_read ? ' <i class="fas fa-check-double" style="color:#2563eb;font-size:9px"></i>' : ''}</div>
                    </div>
                </div>`;

        });

        return html;
    }

    function scrollToBottom() {
        messagesEl.scrollTop = messagesEl.scrollHeight;
    }

    /* ── Polling ──────────────────────────────────────────────── */
    function startPolling() {
        stopPolling();
        pollTimer = setInterval(() => loadMessages(false), 3000);
    }

    function stopPolling() {
        if (pollTimer) { clearInterval(pollTimer); pollTimer = null; }
    }

    /* ── Send message ─────────────────────────────────────────── */
    if (textarea) {
        textarea.addEventListener('input', () => {
            sendBtn && (sendBtn.classList.toggle('ready', textarea.value.trim().length > 0));
            // Auto-resize
            textarea.style.height = 'auto';
            textarea.style.height = Math.min(textarea.scrollHeight, 70) + 'px';
        });
        textarea.addEventListener('keydown', e => {
            if (e.key === 'Enter' && !e.shiftKey) { e.preventDefault(); sendText(); }
        });
    }

    if (sendBtn) sendBtn.addEventListener('click', sendText);

    function sendText() {
        const content = textarea.value.trim();
        if (!content) return;
        sendMessage(content, 'text');
        textarea.value = '';
        textarea.style.height = 'auto';
        if (sendBtn) sendBtn.classList.remove('ready');
    }

    function sendMessage(content, type = 'text') {
        if (!content || !currentConvId) return;

        const fd = new FormData();
        fd.append('conversation_id', currentConvId);
        fd.append('type', type);
        fd.append('content', content);

        fetch(BASE_URL + 'index.php?url=Chat/send', { method: 'POST', body: fd })
            .then(r => r.json())
            .then(d => { if (d.success) loadMessages(false); })
            .catch(() => { });
    }

    /* ── File upload ──────────────────────────────────────────── */
    if (imgBtn) imgBtn.addEventListener('click', () => fileInput && fileInput.click());

    if (fileInput) {
        fileInput.addEventListener('change', function () {
            const file = this.files[0];
            if (!file || !currentConvId) return;
            const type = file.type.startsWith('image/') ? 'image' : 'video';
            const fd = new FormData();
            fd.append('conversation_id', currentConvId);
            fd.append('type', type);
            fd.append('attachment', file);
            this.value = '';
            fetch(BASE_URL + 'index.php?url=Chat/send', { method: 'POST', body: fd })
                .then(r => r.json())
                .then(d => { if (d.success) loadMessages(false); })
                .catch(() => { });
        });
    }

    /* ── Search conversations ─────────────────────────────────── */
    if (searchInput) {
        searchInput.addEventListener('input', function () {
            const q = this.value.trim().toLowerCase();
            const filtered = q ? convList.filter(c => c.customer_name.toLowerCase().includes(q)) : convList;
            renderConvList(filtered);
        });
    }

    /* ── Global: open chat from product page ──────────────────── */
    document.addEventListener('click', function (e) {
        const btn = e.target.closest('.open-chat-with-product');
        if (!btn) return;

        const id = btn.dataset.id;
        const name = btn.dataset.name;
        const price = btn.dataset.price;
        const oldPrice = btn.dataset.oldPrice || '';
        const image = btn.dataset.image;
        const sellerId = btn.dataset.sellerId || 0;

        // 1. Open widget
        if (!isOpen) openWidget();

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
            });

        // 3. Set product context (for sending)
        const p = { id, name, price, old_price: oldPrice, image };
        setProductContext(p);
    });

    /* ── Set Product Context Helper ───────────────────────────── */
    function setProductContext(p) {
        if (!p) return;

        // Update Bottom Card
        if (productCard) {
            document.getElementById('wgtProductImg').src = p.image;
            document.getElementById('wgtProductName').textContent = p.name;
            document.getElementById('wgtProductPrice').innerHTML = `
                ${formatPrice(p.price)}
                ${p.old_price ? `<span class="msg-product-old-price" style="margin-left:5px">${formatPrice(p.old_price)}</span>` : ''}
            `;
            productCard.style.display = 'block';

            const closeBtn = document.getElementById('wgtCloseContext');
            if (closeBtn) closeBtn.onclick = () => productCard.style.display = 'none';
            if (changeProductBtn) changeProductBtn.onclick = openProductSelector;
        }

        // Update Top Card (Sticky)
        const topCard = document.getElementById('wgtTopCard');
        if (topCard) {
            document.getElementById('wgtTopImg').src = p.image;
            document.getElementById('wgtTopName').textContent = p.name;
            document.getElementById('wgtTopPrice').innerHTML = `
                <span style="color:#ee4d2d;font-weight:500">${formatPrice(p.price)}</span> 
                ${p.old_price ? `<span class="msg-product-old-price" style="margin-left:5px;font-size:0.9em">${formatPrice(p.old_price)}</span>` : ''}
            `;
            const buyBtn = document.getElementById('wgtTopBuyBtn');
            if (buyBtn) buyBtn.href = BASE_URL + 'index.php?url=Product/show/' + p.id;
            topCard.style.display = 'flex';
        }
    }

    /* ── Product Selector Logic ───────────────────────────────── */
    if (closeSelector) closeSelector.onclick = () => selectorOverlay.style.display = 'none';

    function openProductSelector() {
        selectorOverlay.style.display = 'flex';
        fetchProducts('');
    }

    function fetchProducts(q = '') {
        selectorList.innerHTML = '<div class="p-3 text-center text-muted">Đang tải sản phẩm...</div>';
        fetch(BASE_URL + 'index.php?url=Chat/productList&q=' + encodeURIComponent(q))
            .then(r => r.json())
            .then(data => {
                if (data.success) renderSelectorList(data.products);
            }).catch(() => {
                selectorList.innerHTML = '<div class="p-3 text-center text-danger">Lỗi khi tải dữ liệu</div>';
            });
    }

    function renderSelectorList(products) {
        if (!products.length) {
            selectorList.innerHTML = '<div class="p-4 text-center text-muted">Không tìm thấy sản phẩm nào</div>';
            return;
        }
        selectorList.innerHTML = products.map(p => `
            <div class="wgt-selector-item" data-id="${p.id}" data-name="${escAttr(p.name)}" data-price="${p.price}" data-old-price="${p.old_price || ''}" data-image="${p.image}">
                <img src="${p.image}" alt="">
                <div class="wgt-item-info">
                    <div class="wgt-item-name">${escHtml(p.name)}</div>
                    <div class="wgt-item-price-row">
                        <span class="wgt-item-price">${formatPrice(p.price)}</span>
                        ${p.old_price ? `<span class="wgt-item-old-price">${formatPrice(p.old_price)}</span>` : ''}
                    </div>
                </div>
                <div class="wgt-item-actions">
                    <button class="wgt-btn-send-item wgt-select-btn">Gửi</button>
                </div>
            </div>
        `).join('');

        // Selection handler
        selectorList.querySelectorAll('.wgt-select-btn').forEach(btn => {
            btn.onclick = function () {
                const item = this.closest('.wgt-selector-item');
                const p = {
                    id: item.dataset.id,
                    name: item.dataset.name,
                    price: item.dataset.price,
                    old_price: item.dataset.oldPrice,
                    image: item.dataset.image
                };
                // 1. Send message
                sendMessage('[P]:' + JSON.stringify(p));
                // 2. Update context (Top & Bottom)
                setProductContext(p);
                // 3. Close selector
                selectorOverlay.style.display = 'none';
            };
        });
    }

    let searchTimeout = null;
    if (selectorSearch) {
        selectorSearch.oninput = function () {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => fetchProducts(this.value.trim()), 300);
        };
    }

    /* ── Badge ────────────────────────────────────────────────── */
    function updateBubbleBadge(n) {
        if (!badge) return;
        badge.textContent = n;
        badge.classList.toggle('show', n > 0);
    }

    /* ── Helpers ──────────────────────────────────────────────── */
    function formatPrice(p) {
        if (!p) return '';
        // Underline the 'đ' or '₫' symbol
        return String(p).replace(/(₫|đ)/g, '<span class="currency">$1</span>');
    }

    function escHtml(s) {
        return String(s || '')
            .replace(/&/g, '&amp;').replace(/</g, '&lt;')
            .replace(/>/g, '&gt;').replace(/"/g, '&quot;');
    }
    function escAttr(s) { return escHtml(s || ''); }

    /* ── Poll unread every 30s even when closed ───────────────── */
    setInterval(() => {
        if (!USER_ID) return;
        fetch(BASE_URL + 'index.php?url=Chat/conversations')
            .then(r => r.json())
            .then(data => {
                if (!data.success) return;
                convList = data.conversations || [];
                const total = convList.reduce((s, c) => s + (c.unread || 0), 0);
                updateBubbleBadge(total);
            }).catch(() => { });
    }, 30000);

})();
