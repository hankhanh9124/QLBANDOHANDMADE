<?php
// app/views/shares/chat_widget.php
$isLoggedIn = isset($_SESSION['user_id']);
$myUserId   = $_SESSION['user_id'] ?? 0;
$isAdmin    = isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
$shopName   = 'GÌ CŨNG MÓC SHOP';
$shopAvatar = BASE_URL . 'public/images/logolen.jpg';
?>
<?php if ($isLoggedIn): ?>

    <link rel="stylesheet" href="<?php echo BASE_URL; ?>public/css/chat.css?v=<?php echo time(); ?>">

    <!-- ══ WIDGET WRAPPER ══ -->
    <div class="chat-widget-wrap" id="chatWidget">

        <!-- Bubble button (Standard Position) -->
        <div class="wgt-floating-btn" id="wgtBubble" title="Chat với shop">
            <i class="fab fa-facebook-messenger fa-lg"></i>
            <span class="wgt-bubble-badge" id="wgtBadge">0</span>
        </div>

        <!-- Window -->
        <div class="wgt-window" id="wgtWindow">

            <!-- ── Header ── -->
            <div class="wgt-pane-header">
                <div class="wgt-header-left">
                    <img src="<?php echo $shopAvatar; ?>" alt="Shop" id="wgtPaneAvatar">
                    <div class="wgt-header-info">
                        <h4 id="wgtPaneName"><?php echo $shopName; ?></h4>
                        <span>Đang hoạt động</span>
                    </div>
                </div>
                <div class="wgt-header-controls">
                    <i class="fas fa-minus" id="wgtMinimizeBtn" title="Thu nhỏ"></i>
                    <i class="fas fa-times" id="wgtCloseBtn" title="Đóng"></i>
                </div>
            </div>

            <!-- Conversations List (Hidden by default in widget, but needed for JS logic) -->
            <div id="wgtConvList" style="display:none"></div>

            <!-- Empty State -->
            <div id="wgtConvEmpty" style="display:none; padding: 40px 20px; text-align: center; color: #999;">
                <i class="fas fa-comments fa-3x mb-3"></i>
                <p>Bắt đầu trò chuyện với chúng tôi!</p>
            </div>

            <!-- Messages Area (Active Pane) -->
            <div id="wgtChatPane" style="display: flex; flex-direction: column; flex: 1; overflow: hidden;">
                <div class="chat-messages-area" id="wgtMessages" style="flex: 1; overflow-y: auto;">
                    <!-- Messages will be loaded here -->
                    <div class="text-center p-5 text-muted">
                        <i class="fas fa-spinner fa-spin mr-2"></i> Đang tải tin nhắn...
                    </div>
                </div>
            </div>

            <!-- Input area -->
            <div class="chat-input-bar">
                <div class="chat-input-container">
                    <div class="chat-input-actions">
                        <i class="fas fa-image" title="Gửi ảnh" onclick="document.getElementById('wgtFileInput').click()"></i>
                        <i class="fas fa-box-open" id="wgtProductBtn" title="Sản phẩm"></i>
                    </div>
                    <div class="chat-input-field">
                        <textarea id="wgtTextarea" placeholder="Nhập tin nhắn..." rows="1"></textarea>
                    </div>
                    <input type="file" id="wgtFileInput" style="display: none;" accept="image/*,video/*">
                    <button class="btn-send-msg" id="wgtSendBtn">
                        <i class="fas fa-paper-plane"></i>
                    </button>
                </div>
            </div>

        </div><!-- /.wgt-window -->

        <!-- Product Selector Modal -->
        <div class="wgt-selector-overlay" id="wgtProductSelector" style="display:none">
            <div class="wgt-selector-modal">
                <div class="wgt-selector-header">
                    <span>Chọn sản phẩm</span>
                    <i class="fas fa-times" onclick="document.getElementById('wgtProductSelector').style.display='none'"></i>
                </div>
                <div class="wgt-selector-search">
                    <div class="chat-search-box">
                        <i class="fas fa-search"></i>
                        <input type="text" id="wgtSelectorSearchInput" placeholder="Tìm tên sản phẩm">
                    </div>
                </div>
                <div class="wgt-selector-list" id="wgtSelectorList">
                    <!-- Products loaded here -->
                </div>
            </div>
        </div>
    </div><!-- /.chat-widget-wrap -->

    <script>
        const USER_ID = <?php echo $myUserId; ?>;
        const BASE_URL = '<?php echo BASE_URL; ?>';
    </script>
    <script src="<?php echo BASE_URL; ?>public/js/chat_v2.js?v=<?php echo time(); ?>"></script>

<?php endif; ?>