<?php
require_once 'app/config/database.php';
require_once 'app/models/SellerRequestModel.php';
require_once 'app/models/UserModel.php';
require_once 'app/models/ShopModel.php';
require_once 'app/models/NotificationModel.php';

class AdminController {
    private $db;
    private $sellerRequestModel;
    private $userModel;
    private $shopModel;
    private $notificationModel;

    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        
        // Basic Admin Auth
        if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
            header('Location: ' . BASE_URL . 'index.php?url=Page/login');
            exit;
        }

        $database = new Database();
        $this->db = $database->getConnection();
        $this->sellerRequestModel = new SellerRequestModel($this->db);
        $this->userModel = new UserModel($this->db);
        $this->shopModel = new ShopModel($this->db);
        $this->notificationModel = new NotificationModel($this->db);
    }

    public function manageSellers() {
        $requests = $this->sellerRequestModel->getAllPending();
        require_once 'app/views/shares/header.php';
        require_once 'app/views/admin/seller_moderation.php';
        require_once 'app/views/shares/footer.php';
    }

    public function approveSeller($id) {
        $request = $this->sellerRequestModel->getById($id);
        if ($request && $request->status === 'pending') {
            // 1. Update request status
            $this->sellerRequestModel->updateStatus($id, 'approved');
            
            // 2. Update user role to seller
            $query = "UPDATE user SET role = 'seller' WHERE id = :user_id";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':user_id', $request->user_id);
            $stmt->execute();

            // 3. Create shop
            $this->shopModel->create([
                'seller_id' => $request->user_id,
                'name' => $request->shop_name,
                'description' => $request->shop_description
            ]);

            // 4. Notify user
            $this->notificationModel->create(
                $request->user_id,
                'Yêu cầu đăng ký Seller thành công!',
                'Chúc mừng! Shop "' . $request->shop_name . '" của bạn đã được kích hoạt. Hãy bắt đầu đăng sản phẩm ngay.',
                'seller_request',
                'index.php?url=Product/myProducts'
            );

            $_SESSION['success_message'] = "Đã phê duyệt người bán: " . $request->shop_name;
        }
        header('Location: ' . BASE_URL . 'index.php?url=Admin/manageSellers');
        exit;
    }

    public function rejectSeller() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['request_id'];
            $reason = $_POST['reason'];
            
            $request = $this->sellerRequestModel->getById($id);
            if ($request) {
                $this->sellerRequestModel->updateStatus($id, 'rejected', $reason);
                
                // Notify user
                $this->notificationModel->create(
                    $request->user_id,
                    'Yêu cầu đăng ký Seller bị từ chối',
                    'Rất tiếc, yêu cầu của bạn bị từ chối với lý do: ' . $reason,
                    'seller_request',
                    'index.php?url=Seller/register'
                );
                
                $_SESSION['success_message'] = "Đã từ chối yêu cầu của " . $request->shop_name;
            }
        }
        header('Location: ' . BASE_URL . 'index.php?url=Admin/manageSellers');
        exit;
    }
}
