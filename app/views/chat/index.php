<?php
// app/views/chat/index.php
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tin nhắn - GÌ CŨNG MÓC SHOP</title>
    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- FontAwesome CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <!-- Custom CSS -->
    <link href="<?php echo BASE_URL; ?>public/css/chat.css?v=<?php echo time(); ?>" rel="stylesheet">
    <style>
        body {
            background-color: #f1f5f9;
            height: 100vh;
            margin: 0;
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }

        .chat-main-container {
            flex: 1;
            max-width: 1400px;
            width: 100%;
            margin: 0 auto;
            padding: 15px;
            display: flex;
            flex-direction: column;
            height: 100vh;
        }

        @media (max-width: 768px) {
            .chat-main-container {
                padding: 0;
            }
        }
    </style>
</head>

<body>
    <div class="chat-main-container">
        <div class="chat-page-wrapper">
            <!-- Sidebar -->
            <div class="chat-page-sidebar" id="full-chat-sidebar">
                <div class="chat-page-sidebar-head">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <h2 class="mb-0">Chat</h2>
                        <a href="<?php echo BASE_URL; ?>" class="btn btn-light btn-sm rounded-circle shadow-sm" title="Quay lại shop">
                            <i class="fas fa-home"></i>
                        </a>
                    </div>
                    <div class="chat-search-box">
                        <i class="fas fa-search"></i>
                        <input type="text" id="sidebar-search" placeholder="Tìm kiếm hội thoại...">
                    </div>
                </div>
                <div class="chat-conv-list" id="sidebar-conv-list">
                    <!-- Conversations will be loaded here via chat_v2.js -->
                    <div class="text-center p-5">
                        <div class="spinner-border text-primary spinner-border-sm" role="status"></div>
                    </div>
                </div>
            </div>

            <!-- Main Pane -->
            <div class="chat-page-pane">
                <!-- Mobile Loading Placeholder -->
                <div id="chat-pane-empty" class="chat-empty-pane">
                    <div class="text-center">
                        <i class="fas fa-comments mb-4" style="font-size: 64px; color: #e2e8f0;"></i>
                        <h3 class="font-weight-bold">Chào mừng đến với Chat</h3>
                        <p class="text-muted">Chọn một hội thoại để bắt đầu nhắn tin.</p>
                    </div>
                </div>

                <div id="chat-pane-active" style="display: none; flex-direction: column; height: 100%;">
                    <!-- Pane header -->
                    <div class="chat-pane-header-bar">
                        <div class="chat-pane-user-info">
                            <button class="btn-back-sidebar" id="mobile-back-btn">
                                <i class="fas fa-chevron-left"></i>
                            </button>
                            <div class="chat-pane-avatar">
                                <img id="active-conv-avatar" src="" alt="">
                            </div>
                            <div class="chat-pane-details">
                                <h3 id="active-conv-name">Đang tải...</h3>
                                <div class="chat-pane-status">Đang hoạt động</div>
                            </div>
                        </div>
                        <div class="chat-pane-actions">
                            <i class="fas fa-phone-alt" title="Bắt đầu gọi thoại"></i>
                            <i class="fas fa-video" title="Bắt đầu gọi video"></i>
                            <i class="fas fa-info-circle" title="Thông tin hội thoại"></i>
                        </div>
                    </div>

                    <!-- Messages Area -->
                    <div class="chat-messages-area" id="chat-messages-body">
                        <!-- Messages loaded here -->
                    </div>

                    <!-- Input area -->
                    <div class="chat-input-bar">
                        <div class="chat-input-container">
                            <div class="chat-input-actions">
                                <i class="fas fa-plus-circle" title="Thêm"></i>
                                <i class="fas fa-image" title="Gửi ảnh" onclick="document.getElementById('full-chat-file').click()"></i>
                                <i class="fas fa-box-open" title="Gửi sản phẩm"></i>
                            </div>
                            <div class="chat-input-field">
                                <textarea id="full-chat-input" placeholder="Nhập tin nhắn..." rows="1"></textarea>
                            </div>
                            <input type="file" id="full-chat-file" style="display: none;" accept="image/*">
                            <button class="btn-send-msg" id="full-chat-send">
                                <i class="fas fa-paper-plane"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script>
        window.BASE_URL = '<?php echo BASE_URL; ?>';
        window.USER_ID = <?php echo $_SESSION['user_id'] ?? 0; ?>;
        window.USER_ROLE = '<?php echo $_SESSION['user_role'] ?? ''; ?>';
        window.CURRENT_CONV_ID = <?php echo $convId ?? 'null'; ?>;
    </script>
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="<?php echo BASE_URL; ?>public/js/chat_v2.js?v=<?php echo time(); ?>"></script>
</body>

</html>
