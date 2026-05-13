<?php
require_once 'app/config/database.php';
require_once 'app/models/ProductModel.php';
require_once 'app/models/CategoryModel.php';
require_once 'app/models/UserModel.php';
require_once 'app/models/ReturnModel.php';
require_once 'app/models/OrderModel.php';
require_once 'app/models/SellerRequestModel.php';
require_once 'app/models/ShopModel.php';
require_once 'app/models/ChatModel.php';

class DashboardController
{
    private $db;
    private $productModel;
    private $categoryModel;
    private $userModel;
    private $returnModel;
    private $orderModel;
    private $sellerRequestModel;
    private $shopModel;
    private $chatModel;
    public $unreadChatCount = 0;

    public function __construct()
    {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->productModel = new ProductModel($this->db);
        $this->categoryModel = new CategoryModel($this->db);
        $this->userModel = new UserModel($this->db);
        $this->returnModel = new ReturnModel($this->db);
        $this->orderModel = new OrderModel($this->db);
        $this->sellerRequestModel = new SellerRequestModel($this->db);
        $this->shopModel = new ShopModel($this->db);
        $this->chatModel = new ChatModel($this->db);

        // Security check: Only admins can access the dashboard
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_role']) || !in_array($_SESSION['user_role'], ['admin', 'seller'])) {
            header('Location: ' . BASE_URL . 'index.php?url=Page/login');
            exit;
        }

        $this->unreadChatCount = $this->chatModel->getTotalUnread($_SESSION['user_id']);
    }

    private function requireAdmin()
    {
        if ($_SESSION['user_role'] !== 'admin') {
            $_SESSION['error_message'] = "Bạn không có quyền truy cập tính năng này.";
            header('Location: ' . BASE_URL . 'index.php?url=Dashboard');
            exit;
        }
    }

    public function index()
    {
        // Fetch stats for Admin Dashboard
        $allProducts = $this->productModel->getProducts();
        $totalProducts = is_array($allProducts) ? count($allProducts) : 0;
        
        $banners = $this->productModel->getBanners();
        $totalBanners = is_array($banners) ? count($banners) : 0;

        // Tính tổng sản phẩm đã bán
        $stmtSold = $this->db->prepare("SELECT SUM(sold) as total_sold FROM product");
        $stmtSold->execute();
        $totalSold = $stmtSold->fetch(PDO::FETCH_OBJ)->total_sold ?? 0;

        // Tính tổng đơn hàng
        $stmtOrders = $this->db->prepare("SELECT COUNT(*) as total_orders FROM orders");
        $stmtOrders->execute();
        $totalOrders = $stmtOrders->fetch(PDO::FETCH_OBJ)->total_orders ?? 0;

        // Tính tổng doanh thu
        $stmtRevenue = $this->db->prepare("SELECT SUM(total) as total_revenue FROM orders WHERE status != 'cancelled'");
        $stmtRevenue->execute();
        $totalRevenue = $stmtRevenue->fetch(PDO::FETCH_OBJ)->total_revenue ?? 0;

        // Tính số lượng yêu cầu người bán đang chờ
        $pendingSellerRequests = count($this->sellerRequestModel->getAllPending());

        include 'app/views/dashboard/index.php';
    }

    public function products()
    {
        $search = isset($_GET['search']) ? trim($_GET['search']) : '';
        $sort = isset($_GET['sort']) ? trim($_GET['sort']) : 'id';

        if ($sort === 'sold_only') {
            $products = $this->productModel->getSoldDetailsReport();
        } elseif ($search !== '') {
            $products = $this->productModel->searchProducts($search);
        } else {
            $products = $this->productModel->getProducts(null, null, $sort);
        }
        include 'app/views/dashboard/products.php';
    }
    
    public function pendingProducts()
    {
        $this->requireAdmin();
        $products = $this->productModel->getPendingProducts();
        $action = 'pending_products';
        include 'app/views/dashboard/pending_products.php';
    }

    public function approveProduct($id)
    {
        if ($this->productModel->updateProductStatus($id, 'approved')) {
            $_SESSION['success_message'] = "Sản phẩm đã được phê duyệt!";
        } else {
            $_SESSION['error_message'] = "Lỗi khi phê duyệt sản phẩm.";
        }
        header('Location: ' . BASE_URL . 'index.php?url=Dashboard/pendingProducts');
        exit;
    }

    public function rejectProduct($id)
    {
        $reason = $_POST['rejection_reason'] ?? null;
        if ($this->productModel->updateProductStatus($id, 'rejected', $reason)) {
            $_SESSION['success_message'] = "Đã từ chối sản phẩm với lý do.";
        } else {
            $_SESSION['error_message'] = "Lỗi khi từ chối sản phẩm.";
        }
        header('Location: ' . BASE_URL . 'index.php?url=Dashboard/pendingProducts');
        exit;
    }

    public function categories()
    {
        $categories = $this->categoryModel->getCategories();
        include 'app/views/dashboard/categories.php';
    }

    public function banners()
    {
        $banners = $this->productModel->getBanners();
        include 'app/views/dashboard/banners.php';
    }

    public function orders()
    {
        // Fetch orders from DB
        $query = "SELECT o.*, u.name as user_name, COALESCE(o.recipient_address, u.address) as display_address FROM orders o LEFT JOIN user u ON o.user_id = u.id ORDER BY o.created_at DESC";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        $orders = $stmt->fetchAll(PDO::FETCH_OBJ);

        include 'app/views/dashboard/orders.php';
    }

    public function users()
    {
        $this->requireAdmin();
        // Fetch users from DB
        $query = "SELECT * FROM user ORDER BY id DESC";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        $users = $stmt->fetchAll(PDO::FETCH_OBJ);

        include 'app/views/dashboard/users.php';
    }

    public function updateRole($id, $role)
    {
        // Allowed roles validation
        $allowedRoles = ['admin', 'seller', 'user'];
        if (!in_array($role, $allowedRoles)) {
            die('Invalid role');
        }

        $query = "UPDATE user SET role = :role WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':role', $role);
        $stmt->bindParam(':id', $id);

        if ($stmt->execute()) {
            header('Location: ' . BASE_URL . 'index.php?url=Dashboard/users');
        } else {
            die('Failed to update role');
        }
    }

    public function orderDetail($id)
    {
        // Fetch order basic info
        $query = "SELECT o.*, u.name as user_name, u.email, COALESCE(o.recipient_name, u.name) as display_name, COALESCE(o.recipient_phone, u.phone) as display_phone, COALESCE(o.recipient_address, u.address) as display_address 
                  FROM orders o 
                  LEFT JOIN user u ON o.user_id = u.id 
                  WHERE o.id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $order = $stmt->fetch(PDO::FETCH_OBJ);

        if (!$order) {
            die('Order not found');
        }

        // Fetch order items
        $query = "SELECT od.*, p.name as product_name, p.image 
                  FROM order_detail od 
                  JOIN product p ON od.product_id = p.id 
                  WHERE od.order_id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $items = $stmt->fetchAll(PDO::FETCH_OBJ);

        include 'app/views/dashboard/order_detail.php';
    }

    public function updateOrderStatus($id, $status)
    {
        $allowedStatus = ['pending', 'confirmed', 'shipping', 'completed', 'cancelled'];
        if (!in_array($status, $allowedStatus)) {
            die('Invalid status');
        }

        // Fetch order to check current status
        $stmtOrder = $this->db->prepare("SELECT user_id, status FROM orders WHERE id = :id");
        $stmtOrder->bindParam(':id', $id);
        $stmtOrder->execute();
        $order = $stmtOrder->fetch(PDO::FETCH_OBJ);

        if (!$order) {
            die('Order not found');
        }

        // If order is already cancelled or completed, prevent any status changes
        if ($order->status === 'cancelled') {
            $_SESSION['error_message'] = "Không thể thay đổi trạng thái của đơn hàng đã hủy.";
            header('Location: ' . BASE_URL . 'index.php?url=Dashboard/orderDetail/' . $id);
            exit;
        }

        if ($order->status === 'completed') {
            $_SESSION['error_message'] = "Không thể thay đổi trạng thái của đơn hàng đã hoàn thành.";
            header('Location: ' . BASE_URL . 'index.php?url=Dashboard/orderDetail/' . $id);
            exit;
        }

        $query = "UPDATE orders SET status = :status WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':id', $id);

        if ($stmt->execute()) {
            if ($order && $order->user_id) {
                $statusLabels = [
                    'pending' => 'Chờ xử lý',
                    'confirmed' => 'Đã xác nhận',
                    'shipping' => 'Đang giao',
                    'completed' => 'Đã giao thành công',
                    'cancelled' => 'Đã hủy'
                ];
                $statusMsg = $statusLabels[$status] ?? $status;
                $msg = "Đơn hàng #" . $id . " của bạn đã được cập nhật thành: " . $statusMsg;
                $link = 'index.php?url=Page/orders';
                
                require_once 'app/models/NotificationModel.php';
                $notificationModel = new NotificationModel($this->db);
                $notificationModel->addNotification($order->user_id, $msg, $link);
            }
            header('Location: ' . BASE_URL . 'index.php?url=Dashboard/orders');
        } else {
            die('Failed to update status');
        }
    }

    public function addCategory()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = $_POST['name'] ?? '';
            $description = $_POST['description'] ?? '';

            if (empty($name)) {
                die('Name is required');
            }

            if ($this->categoryModel->addCategory($name, $description)) {
                header('Location: ' . BASE_URL . 'index.php?url=Dashboard/categories');
            } else {
                die('Failed to add category');
            }
        }
    }

    public function updateCategory($id)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = $_POST['name'] ?? '';
            $description = $_POST['description'] ?? '';

            if (empty($name)) {
                die('Name is required');
            }

            if ($this->categoryModel->updateCategory($id, $name, $description)) {
                header('Location: ' . BASE_URL . 'index.php?url=Dashboard/categories');
            } else {
                die('Failed to update category');
            }
        }
    }

    public function deleteCategory($id)
    {
        if ($this->categoryModel->deleteCategory($id)) {
            header('Location: ' . BASE_URL . 'index.php?url=Dashboard/categories');
        } else {
            die('Failed to delete category');
        }
    }

    public function updateUser($id)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = $_POST['name'] ?? '';
            $email = $_POST['email'] ?? '';
            $phone = $_POST['phone'] ?? '';

            if (empty($name)) {
                die('Name is required');
            }

            if ($this->userModel->updateUser($id, $name, $email, $phone)) {
                header('Location: ' . BASE_URL . 'index.php?url=Dashboard/users');
            } else {
                die('Failed to update user');
            }
        }
    }

    public function deleteUser($id)
    {
        if ($this->userModel->deleteUser($id)) {
            header('Location: ' . BASE_URL . 'index.php?url=Dashboard/users');
        } else {
            die('Failed to delete user');
        }
    }

    public function addUser()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = $_POST['name'] ?? '';
            $email = $_POST['email'] ?? '';
            $phone = $_POST['phone'] ?? '';
            $password = $_POST['password'] ?? '';
            $role = $_POST['role'] ?? 'customer';

            if (empty($name) || empty($password)) {
                die('Name and Password are required');
            }

            $result = $this->userModel->adminAddUser($name, $email, $phone, $password, $role);
            if ($result === true) {
                header('Location: ' . BASE_URL . 'index.php?url=Dashboard/users');
            } else {
                die($result);
            }
        }
    }

    public function returns()
    {
        $returns = $this->returnModel->getReturnsByAdmin();
        include 'app/views/dashboard/returns.php';
    }

    public function returnDetail($id)
    {
        $return = $this->returnModel->getReturnById($id);
        $order = $this->orderModel->getOrderById($return->order_id);
        include 'app/views/dashboard/return_detail.php';
    }

    public function updateReturnStatus()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'];
            $status = $_POST['status'];
            $note = $_POST['note'] ?? '';
            
            if ($this->returnModel->updateStatus($id, $status, $note)) {
                if ($status === 'refunded') {
                    $return = $this->returnModel->getReturnById($id);
                    $this->orderModel->updateStatus($return->order_id, 'cancelled');
                }
                $_SESSION['success_message'] = "Cập nhật trạng thái trả hàng thành công.";
            } else {
                $_SESSION['error_message'] = "Có lỗi xảy ra.";
            }
            header('Location: ' . BASE_URL . 'index.php?url=Dashboard/returnDetail/' . $id);
            exit;
        }
    }
    public function checkNewReturns()
    {
        $count = $this->returnModel->getPendingReturnsCount();
        header('Content-Type: application/json');
        echo json_encode(['count' => $count]);
        exit;
    }

    public function manageSellers()
    {
        $requests = $this->sellerRequestModel->getAllPending();
        $action = 'manage_sellers';
        include 'app/views/dashboard/seller_moderation.php';
    }

    public function approveSeller($requestId)
    {
        $request = $this->sellerRequestModel->getById($requestId);
        if (!$request || $request->status !== 'pending') {
            $_SESSION['error_message'] = "Yêu cầu không hợp lệ.";
            header('Location: ' . BASE_URL . 'index.php?url=Dashboard/manageSellers');
            exit;
        }

        // 1. Update request status
        if ($this->sellerRequestModel->updateStatus($requestId, 'approved')) {
            // 2. Upgrade user role to 'seller'
            $this->userModel->updateRole($request->user_id, 'seller');

            // 3. Create shop
            $this->shopModel->create([
                'seller_id' => $request->user_id,
                'name' => $request->shop_name,
                'description' => $request->shop_description
            ]);

            // 4. Notify user
            require_once 'app/models/NotificationModel.php';
            $notif = new NotificationModel($this->db);
            $notif->create(
                $request->user_id,
                'Yêu cầu lên Người bán được phê duyệt',
                'Chúc mừng! Cửa hàng "' . $request->shop_name . '" của bạn đã được kích hoạt. Bạn có thể đăng sản phẩm ngay bây giờ.',
                'success',
                'index.php?url=Product/myProducts'
            );

            $_SESSION['success_message'] = "Đã phê duyệt người bán: " . $request->shop_name;
        }

        header('Location: ' . BASE_URL . 'index.php?url=Dashboard/manageSellers');
        exit;
    }

    public function rejectSeller()
    {
        $requestId = $_POST['request_id'] ?? null;
        $reason = $_POST['reject_reason'] ?? 'Không đủ điều kiện xét duyệt.';

        $request = $this->sellerRequestModel->getById($requestId);
        if (!$request) {
            header('Location: ' . BASE_URL . 'index.php?url=Dashboard/manageSellers');
            exit;
        }

        if ($this->sellerRequestModel->updateStatus($requestId, 'rejected', $reason)) {
            // Notify user
            require_once 'app/models/NotificationModel.php';
            $notif = new NotificationModel($this->db);
            $notif->create(
                $request->user_id,
                'Yêu cầu lên Người bán bị từ chối',
                'Rất tiếc, yêu cầu của bạn bị từ chối. Lý do: ' . $reason,
                'danger',
                'index.php?url=Seller/register'
            );

            $_SESSION['success_message'] = "Đã từ chối yêu cầu của: " . $request->shop_name;
        }

        header('Location: ' . BASE_URL . 'index.php?url=Dashboard/manageSellers');
        exit;
    }

    public function messages()
    {
        $action = 'messages';
        include 'app/views/dashboard/messages.php';
    }
}
