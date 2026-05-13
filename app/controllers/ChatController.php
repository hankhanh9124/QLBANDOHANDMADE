<?php
require_once 'app/config/database.php';
require_once 'app/models/ChatModel.php';
require_once 'app/models/CartModel.php';
require_once 'app/models/ProductModel.php';
require_once 'app/models/OrderModel.php';

class ChatController
{
    private $chatModel;
    private $db;

    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) session_start();
        $this->db        = (new Database())->getConnection();
        $this->chatModel = new ChatModel($this->db);
    }

    // ── Helpers ──────────────────────────────────────────────────

    private function userId()
    {
        return $_SESSION['user_id']   ?? null;
    }
    private function isAdmin()
    {
        return ($_SESSION['user_role'] ?? '') === 'admin';
    }
    private function requireLogin()
    {
        if (!$this->userId()) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Chưa đăng nhập']);
            exit;
        }
    }
    private function redirectLogin()
    {
        if (!$this->userId()) {
            header('Location: ' . BASE_URL . 'index.php?url=Page/login');
            exit;
        }
    }

    // ── Full-page chat ────────────────────────────────────────────

    public function index($convId = null)
    {
        $this->redirectLogin();
        $userId  = $this->userId();
        $isAdmin = $this->isAdmin();

        $conversations = $this->chatModel->getConversations($userId, $isAdmin);

        $currentConv = null;
        $messages    = [];

        if ($convId) {
            $currentConv = $this->chatModel->getConversationById((int)$convId);
        } elseif (!$isAdmin) {
            // Customer: tự tạo / lấy conv của mình
            $currentConv = $this->chatModel->getOrCreateConversation($userId);
        } elseif (!empty($conversations)) {
            $currentConv = $conversations[0];
        }

        if ($currentConv) {
            $messages = $this->chatModel->getMessages($currentConv->id);
            $this->chatModel->markAsRead($currentConv->id, $userId);
        }

        require_once 'app/views/chat/index.php';
    }

    // ── AJAX: Danh sách hội thoại ─────────────────────────────────

    public function conversations()
    {
        $this->requireLogin();
        header('Content-Type: application/json');
        $userId  = $this->userId();
        $isAdmin = $this->isAdmin();
        $convs   = $this->chatModel->getConversations($userId, $isAdmin);

        // Thêm unread và thông tin hiển thị cho mỗi conv
        foreach ($convs as $c) {
            $c->unread = $this->chatModel->getUnreadCount($c->id, $userId);
            
            if ($userId == $c->customer_id) {
                // Tôi là người mua -> hiện Shop/Seller
                if ($c->seller_id == 0 || (isset($c->seller_name) && $c->seller_name == 'Admin')) {
                    $c->display_name = "GÌ CŨNG MÓC SHOP";
                    $c->display_avatar_url = BASE_URL . 'public/images/logolen.jpg';
                } else {
                    $c->display_name = !empty($c->shop_name) ? $c->shop_name : 'GÌ CŨNG MÓC SHOP';
                    if (!empty($c->shop_logo)) {
                        $c->display_avatar_url = BASE_URL . $c->shop_logo;
                    } elseif (!empty($c->seller_avatar)) {
                        $c->display_avatar_url = BASE_URL . 'public/uploads/avatars/' . $c->seller_avatar;
                    } else {
                        $c->display_avatar_url = 'https://ui-avatars.com/api/?name=' . urlencode($c->display_name) . '&background=2563eb&color=fff';
                    }
                }
            } else {
                // Tôi là người bán -> hiện khách hàng
                $c->display_name = $c->customer_name;
                if ($c->customer_avatar) {
                    $c->display_avatar_url = BASE_URL . 'public/uploads/avatars/' . $c->customer_avatar;
                } else {
                    $c->display_avatar_url = 'https://ui-avatars.com/api/?name=' . urlencode($c->customer_name) . '&background=2563eb&color=fff';
                }
            }
        }

        // Customer: nếu chưa có conv, tự tạo
        if (!$isAdmin && empty($convs)) {
            $conv  = $this->chatModel->getOrCreateConversation($userId);
            $conv->unread = 0;
            $conv->display_name = "GÌ CŨNG MÓC SHOP";
            $conv->display_avatar_url = BASE_URL . 'public/images/logolen.jpg';
            $convs = [$conv];
        }

        echo json_encode(['success' => true, 'conversations' => $convs]);
    }

    // ── AJAX: Lịch sử tin nhắn ────────────────────────────────────

    public function history()
    {
        $this->requireLogin();
        header('Content-Type: application/json');
        $userId = $this->userId();

        $convId     = isset($_GET['conv_id'])    ? (int)$_GET['conv_id']    : null;
        $sellerId   = isset($_GET['seller_id'])  ? (int)$_GET['seller_id']  : 0;
        $customerId = isset($_GET['customer_id'])? (int)$_GET['customer_id']: null;
        $since      = $_GET['since'] ?? null;

        // Customer → lấy conv với seller cụ thể (seller_id > 0 mới là hợp lệ)
        if (!$convId && !$this->isAdmin() && $sellerId > 0) {
            $conv   = $this->chatModel->getOrCreateConversation($userId, $sellerId);
            $convId = $conv ? $conv->id : null;
        }

        // Customer → không truyền seller_id thì lấy conv mặc định (với admin/shop)
        if (!$convId && !$this->isAdmin() && $sellerId === 0 && !$customerId) {
            $conv   = $this->chatModel->getOrCreateConversation($userId, 0);
            $convId = $conv ? $conv->id : null;
        }

        // Admin/Seller: lấy conv với customer cụ thể
        if (!$convId && $customerId !== null) {
            $conv   = $this->chatModel->getOrCreateConversation($customerId, $userId);
            $convId = $conv ? $conv->id : null;
        }

        if (!$convId) {
            echo json_encode(['success' => false, 'message' => 'Không tìm thấy hội thoại']);
            return;
        }

        if ($since) {
            $messages = $this->chatModel->getNewMessages($convId, $since);
        } else {
            $messages = $this->chatModel->getMessages($convId);
        }

        $this->chatModel->markAsRead($convId, $userId);

        $conv = $this->chatModel->getConversationById($convId);
        if ($conv) {
            if ($userId == $conv->customer_id) {
                // Tôi là khách hàng → hiện thông tin người bán
                if ($conv->seller_id == 0) {
                    $conv->display_name       = 'GÌ CŨNG MÓC SHOP';
                    $conv->display_avatar_url = BASE_URL . 'public/images/logolen.jpg';
                } else {
                    $conv->display_name = !empty($conv->shop_name) ? $conv->shop_name : 'GÌ CŨNG MÓC SHOP';
                    if (!empty($conv->shop_logo)) {
                        $conv->display_avatar_url = BASE_URL . $conv->shop_logo;
                    } elseif (!empty($conv->seller_avatar)) {
                        $conv->display_avatar_url = BASE_URL . 'public/uploads/avatars/' . $conv->seller_avatar;
                    } else {
                        $conv->display_avatar_url = 'https://ui-avatars.com/api/?name=' . urlencode($conv->display_name) . '&background=2563eb&color=fff';
                    }
                }
            } else {
                // Tôi là người bán / admin → hiện thông tin khách hàng
                $conv->display_name = $conv->customer_name ?? 'Khách hàng';
                $conv->display_avatar_url = !empty($conv->customer_avatar)
                    ? BASE_URL . 'public/uploads/avatars/' . $conv->customer_avatar
                    : 'https://ui-avatars.com/api/?name=' . urlencode($conv->display_name) . '&background=2563eb&color=fff';
            }
        }


        echo json_encode([
            'success'         => true,
            'conversation_id' => $convId,
            'conversation'    => $conv,
            'messages'        => $messages,
            'server_time'     => date('Y-m-d H:i:s'),
        ]);
    }

    // ── AJAX: Gửi tin nhắn ───────────────────────────────────────

    public function send()
    {
        $this->requireLogin();
        header('Content-Type: application/json');
        $userId = $this->userId();

        $convId  = (int)($_POST['conversation_id'] ?? 0);
        $type    = $_POST['type']    ?? 'text';
        $content = trim($_POST['content'] ?? '');
        $url     = null;

        if (isset($_FILES['attachment']) && $_FILES['attachment']['error'] === 0) {
            $url = $this->uploadFile($_FILES['attachment']);
        }

        if ($type === 'sticker') {
            $url = $content;
            $content = '';
        }

        if (!$convId || ($content === '' && !$url)) {
            echo json_encode(['success' => false, 'message' => 'Dữ liệu không hợp lệ']);
            return;
        }

        $msgId = $this->chatModel->sendMessage($convId, $userId, $type, $content, $url);
        if ($msgId) {
            echo json_encode(['success' => true, 'message_id' => $msgId]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Lỗi khi lưu tin nhắn']);
        }
    }

    // ── AJAX: Đánh dấu đã đọc ────────────────────────────────────

    public function markRead()
    {
        $this->requireLogin();
        header('Content-Type: application/json');
        $userId = $this->userId();
        $convId = (int)($_POST['conv_id'] ?? $_GET['conv_id'] ?? 0);
        if ($convId) {
            $this->chatModel->markAsRead($convId, $userId);
        }
        echo json_encode(['success' => true]);
    }

    // ── AJAX: Quản lý hội thoại (Ghim, Tắt thông báo, Xóa...) ──────

    public function manage()
    {
        $this->requireLogin();
        header('Content-Type: application/json');
        $userId = $this->userId();
        $convId = (int)($_POST['conv_id'] ?? 0);
        $action = $_POST['action']  ?? '';
        $value  = (int)($_POST['value'] ?? 0);

        if (!$convId || !$action) {
            echo json_encode(['success' => false, 'message' => 'Dữ liệu thiếu']);
            return;
        }

        $success = false;
        switch ($action) {
            case 'pin':
                $success = $this->chatModel->updateStatus($convId, 'is_pinned', $value);
                break;
            case 'mute':
                $success = $this->chatModel->updateStatus($convId, 'is_muted', $value);
                break;
            case 'unread':
                $success = $this->chatModel->markUnreadManual($convId, $userId);
                break;
            case 'delete':
                $success = $this->chatModel->softDelete($convId);
                break;
        }

        echo json_encode(['success' => $success]);
    }

    // ── AJAX: Danh sách sản phẩm để chọn ──────────────────────────

    public function productList()
    {
        $this->requireLogin();
        header('Content-Type: application/json');

        $userId   = $this->userId();
        $sellerId = isset($_GET['seller_id']) ? (int)$_GET['seller_id'] : 0;
        $keyword  = $_GET['q'] ?? '';

        $cartModel    = new CartModel($this->db);
        $productModel = new ProductModel($this->db);

        $results = [];

        // Lấy sản phẩm của Seller này
        if ($sellerId > 0) {
            $shopProducts = $productModel->getProductsBySeller($sellerId);
            foreach ($shopProducts as $p) {
                if (empty($keyword) || mb_stripos($p->name, $keyword) !== false) {
                    $discount = isset($p->discount_percent) ? (int)$p->discount_percent : 0;
                    $price = $p->price;
                    $oldPrice = '';
                    if ($discount > 0) {
                        $oldPrice = number_format($price, 0, ',', '.') . '₫';
                        $price = $price * (1 - $discount / 100);
                    }

                    $results[] = [
                        'id'    => $p->id,
                        'name'  => $p->name,
                        'price' => number_format($price, 0, ',', '.') . '₫',
                        'old_price' => $oldPrice,
                        'image' => (strpos($p->image, 'http') === 0) ? $p->image : BASE_URL . (strpos($p->image, 'public/') === 0 ? '' : 'public/uploads/') . $p->image
                    ];
                }
            }
        } else {
            // Fallback nếu không có seller_id (lấy từ giỏ hàng)
            $cartItems = $cartModel->getItems($userId);
            foreach ($cartItems as $item) {
                if (empty($keyword) || mb_stripos($item['name'], $keyword) !== false) {
                    $results[] = [
                        'id'    => $item['id'],
                        'name'  => $item['name'],
                        'price' => number_format($item['price'], 0, ',', '.') . '₫',
                        'image' => (strpos($item['image'], 'http') === 0) ? $item['image'] : BASE_URL . (strpos($item['image'], 'public/') === 0 ? '' : 'public/uploads/') . $item['image']
                    ];
                }
            }
        }

        // Nếu giỏ hàng trống hoặc đang tìm kiếm, lấy thêm từ shop
        if (count($results) < 5 || !empty($keyword)) {
            $shopProducts = $productModel->getProducts();
            foreach ($shopProducts as $p) {
                // Tránh trùng lặp với giỏ hàng
                if (isset($cartItems[$p->id])) continue;

                if (empty($keyword) || mb_stripos($p->name, $keyword) !== false) {
                    $discount = isset($p->discount_percent) ? (int)$p->discount_percent : 0;
                    $price = $p->price;
                    $oldPrice = '';
                    if ($discount > 0) {
                        $oldPrice = number_format($price, 0, ',', '.') . '₫';
                        $price = $price * (1 - $discount / 100);
                    }

                    $results[] = [
                        'id'    => $p->id,
                        'name'  => $p->name,
                        'price' => number_format($price, 0, ',', '.') . '₫',
                        'old_price' => $oldPrice,
                        'image' => (strpos($p->image, 'http') === 0) ? $p->image : BASE_URL . 'public/uploads/' . $p->image
                    ];
                }
                if (count($results) >= 20) break; // Giới hạn 20 sản phẩm
            }
        }

        echo json_encode(['success' => true, 'products' => $results]);
    }

    // ── AJAX: Danh sách đơn hàng để chọn ──────────────────────────

    public function orderList()
    {
        $this->requireLogin();
        header('Content-Type: application/json');

        $userId = $this->userId();
        $orderModel = new OrderModel($this->db);
        $orders = $orderModel->getOrdersByUserId($userId);

        foreach ($orders as $o) {
            $o->date_str = date('d/m/Y H:i', strtotime($o->created_at));
            $o->status_label = $this->getStatusLabel($o->status);
            foreach ($o->items as $item) {
                $item->price_str = number_format($item->price, 0, ',', '.') . ' đ';
                $item->image_url = (strpos($item->image, 'http') === 0) ? $item->image : BASE_URL . 'public/uploads/' . $item->image;
            }
            $o->total_str = number_format($o->total, 0, ',', '.') . ' đ';
        }

        echo json_encode(['success' => true, 'orders' => $orders]);
    }

    private function getStatusLabel($status)
    {
        $labels = [
            'pending'   => 'Chờ xác nhận',
            'processing' => 'Đang xử lý',
            'shipped'   => 'Đang giao',
            'completed' => 'Đã hoàn thành',
            'cancelled' => 'Đã hủy',
            'refunded'  => 'Đã hoàn tiền'
        ];
        return $labels[$status] ?? $status;
    }

    // ── Upload ────────────────────────────────────────────────────

    private function uploadFile($file)
    {
        $dir = 'public/uploads/chat/';
        if (!is_dir($dir)) mkdir($dir, 0777, true);
        $ext  = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $name = 'chat_' . time() . '_' . uniqid() . '.' . $ext;
        $dest = $dir . $name;
        if (move_uploaded_file($file['tmp_name'], $dest)) {
            return $dest;
        }
        return null;
    }
}
