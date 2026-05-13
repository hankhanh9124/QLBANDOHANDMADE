<?php include_once 'app/views/dashboard/header.php'; ?>

<style>
    :root {
        --chat-sidebar-width: 350px;
        --msg-blue: #16e665;
        --msg-gray: #e4e6eb;
    }

    .admin-chat-container {
        display: flex;
        height: calc(100vh - 150px);
        background: #fff;
        border-radius: 15px;
        overflow: hidden;
        box-shadow: 0 10px 30px rgba(0,0,0,0.05);
    }

    /* Sidebar User List */
    .chat-sidebar {
        width: var(--chat-sidebar-width);
        border-right: 1px solid #eee;
        display: flex;
        flex-direction: column;
        background: #f8f9fa;
    }

    .chat-sidebar-header {
        padding: 20px;
        border-bottom: 1px solid #eee;
        background: #fff;
    }

    .chat-user-list {
        flex: 1;
        overflow-y: auto;
    }

    .user-item {
        display: flex;
        align-items: center;
        padding: 15px 20px;
        cursor: pointer;
        transition: all 0.2s;
        border-bottom: 1px solid #f0f0f0;
    }

    .user-item:hover {
        background: #fff;
    }

    .user-item.active {
        background: #fff;
        border-left: 4px solid var(--primary-color);
    }

    .user-avatar {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        object-fit: cover;
        margin-right: 15px;
        background: #ddd;
    }

    .user-info {
        flex: 1;
        min-width: 0;
    }

    .user-name {
        font-weight: 600;
        color: #333;
        margin-bottom: 2px;
        display: flex;
        justify-content: space-between;
    }

    .user-last-msg {
        font-size: 0.85rem;
        color: #888;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .unread-badge {
        background: var(--primary-color);
        color: #fff;
        font-size: 0.7rem;
        padding: 2px 6px;
        border-radius: 10px;
        font-weight: bold;
    }

    /* Chat Pane */
    .chat-main {
        flex: 1;
        display: flex;
        flex-direction: column;
        background: #fff;
    }

    .chat-main-header {
        padding: 15px 25px;
        border-bottom: 1px solid #eee;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .chat-messages {
        flex: 1;
        padding: 20px;
        overflow-y: auto;
        background: #fff;
        display: flex;
        flex-direction: column;
        gap: 10px;
    }

    .msg-row {
        display: flex;
        width: 100%;
    }

    .msg-row.sent { justify-content: flex-end; }
    .msg-row.received { justify-content: flex-start; }

    .msg-bubble {
        max-width: 70%;
        padding: 10px 16px;
        border-radius: 18px;
        font-size: 0.95rem;
        position: relative;
        line-height: 1.4;
    }

    .msg-received .msg-bubble {
        background: var(--msg-gray);
        color: #050505;
        border-bottom-left-radius: 4px;
    }

    .msg-sent .msg-bubble {
        background: var(--msg-blue);
        color: #fff;
        border-bottom-right-radius: 4px;
    }

    .msg-time {
        font-size: 0.7rem;
        margin-top: 4px;
        color: #888;
        display: block;
    }
    .msg-sent .msg-time { text-align: right; color: rgba(255,255,255,0.7); }

    .chat-input-area {
        padding: 20px;
        border-top: 1px solid #eee;
    }

    .chat-input-wrap {
        display: flex;
        gap: 10px;
        background: #f0f2f5;
        padding: 10px;
        border-radius: 25px;
        align-items: center;
    }

    .chat-input-wrap textarea {
        flex: 1;
        background: transparent;
        border: none;
        padding: 5px 15px;
        outline: none;
        resize: none;
        max-height: 100px;
    }

    .btn-send {
        background: var(--primary-color);
        color: #fff;
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        border: none;
        transition: 0.2s;
    }

    .btn-send:hover {
        transform: scale(1.1);
    }

    .chat-empty-state {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        height: 100%;
        color: #aaa;
    }

    .chat-empty-state i {
        font-size: 5rem;
        margin-bottom: 20px;
    }

    @media (max-width: 768px) {
        .chat-sidebar {
            width: 80px;
        }
        .user-info, .chat-sidebar-header h5 {
            display: none;
        }
    }
</style>

<div class="admin-chat-container">
    <!-- Sidebar -->
    <div class="chat-sidebar">
        <div class="chat-sidebar-header">
            <h5 class="mb-0 font-weight-bold">Tin nhắn</h5>
        </div>
        <div class="chat-user-list" id="adminUserList">
            <!-- Loaded via AJAX -->
            <div class="text-center p-4"><i class="fas fa-spinner fa-spin"></i></div>
        </div>
    </div>

    <!-- Main Chat -->
    <div class="chat-main" id="chatMain">
        <div class="chat-empty-state" id="chatEmpty">
            <i class="far fa-comments"></i>
            <h4>Chọn một hội thoại để bắt đầu</h4>
            <p>Tin nhắn từ khách hàng sẽ hiển thị ở đây</p>
        </div>

        <div class="chat-content-wrap d-none" id="chatContent" style="display: flex; flex-direction: column; height: 100%;">
            <div class="chat-main-header">
                <div class="d-flex align-items-center">
                    <img src="" class="user-avatar" id="activeAvatar">
                    <div>
                        <h6 class="mb-0 font-weight-bold" id="activeName">Khách hàng</h6>
                        <span class="small text-success">Đang trực tuyến</span>
                    </div>
                </div>
            </div>

            <div class="chat-messages" id="chatMessages">
                <!-- Messages loaded here -->
            </div>

            <div class="chat-input-area">
                <div class="chat-input-wrap">
                    <textarea id="adminChatInput" placeholder="Nhập tin nhắn..." rows="1"></textarea>
                    <button class="btn-send" id="adminSendBtn">
                        <i class="fas fa-paper-plane"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    const ADMIN_ID = <?php echo $_SESSION['user_id']; ?>;
    const BASE_URL = '<?php echo BASE_URL; ?>';
    let currentConvId = null;
    let pollTimer = null;

    $(document).ready(function() {
        loadConversations();

        // Send message
        $('#adminSendBtn').click(sendMsg);
        $('#adminChatInput').on('keydown', function(e) {
            if(e.which == 13 && !e.shiftKey) {
                e.preventDefault();
                sendMsg();
            }
        });

        // Polling
        setInterval(loadConversations, 5000);
    });

    function loadConversations() {
        $.get(BASE_URL + 'index.php?url=Chat/conversations', function(res) {
            if(res.success) {
                renderUserList(res.conversations);
            }
        });
    }

    function renderUserList(convs) {
        let html = '';
        convs.forEach(c => {
            const active = c.id == currentConvId ? 'active' : '';
            const unread = c.unread > 0 ? `<span class="unread-badge">${c.unread}</span>` : '';
            const avatar = c.display_avatar_url;
            
            html += `
                <div class="user-item ${active}" data-id="${c.id}" onclick="selectConv(${JSON.stringify(c).replace(/"/g, '&quot;')})">
                    <img src="${avatar}" class="user-avatar">
                    <div class="user-info">
                        <div class="user-name">
                            <span>${c.display_name}</span>
                            ${unread}
                        </div>
                        <div class="user-last-msg">${c.last_message || 'Bắt đầu chat...'}</div>
                    </div>
                </div>
            `;
        });
        $('#adminUserList').html(html);
    }

    function selectConv(conv) {
        currentConvId = conv.id;
        
        $('#chatEmpty').addClass('d-none');
        $('#chatContent').removeClass('d-none');
        $('#activeName').text(conv.display_name);
        $('#activeAvatar').attr('src', conv.display_avatar_url);
        
        $('.user-item').removeClass('active');
        $(`.user-item[data-id="${conv.id}"]`).addClass('active');

        loadMessages(true);
        startPolling();
    }

    function loadMessages(scroll = false) {
        if(!currentConvId) return;
        $.get(`${BASE_URL}index.php?url=Chat/history&conv_id=${currentConvId}`, function(res) {
            if(res.success) {
                renderMessages(res.messages);
                if(scroll) scrollToBottom();
            }
        });
    }

    function renderMessages(msgs) {
        let html = '';
        msgs.forEach(m => {
            const isSent = m.sender_id == ADMIN_ID;
            const time = m.created_at.substring(11, 16);
            html += `
                <div class="msg-row ${isSent ? 'sent' : 'received'}">
                    <div class="msg-bubble">
                        ${m.content}
                        <div class="msg-time">${time}</div>
                    </div>
                </div>
            `;
        });
        $('#chatMessages').html(html);
    }

    function sendMsg() {
        const text = $('#adminChatInput').val().trim();
        if(!text || !currentConvId) return;

        $.post(BASE_URL + 'index.php?url=Chat/send', {
            conversation_id: currentConvId,
            type: 'text',
            content: text
        }, function(res) {
            if(res.success) {
                $('#adminChatInput').val('');
                loadMessages(true);
            }
        });
    }

    function startPolling() {
        if(pollTimer) clearInterval(pollTimer);
        pollTimer = setInterval(() => loadMessages(), 3000);
    }

    function scrollToBottom() {
        const el = document.getElementById('chatMessages');
        el.scrollTop = el.scrollHeight;
    }
</script>

<?php include_once 'app/views/dashboard/footer.php'; ?>
