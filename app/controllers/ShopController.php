<?php
require_once 'app/config/database.php';
require_once 'app/models/ShopModel.php';
require_once 'app/models/ProductModel.php';
require_once 'app/models/UserModel.php';
require_once 'app/models/ReviewModel.php';

class ShopController {
    private $db;
    private $shopModel;
    private $productModel;
    private $userModel;
    private $reviewModel;

    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        $database = new Database();
        $this->db = $database->getConnection();
        $this->shopModel = new ShopModel($this->db);
        $this->productModel = new ProductModel($this->db);
        $this->userModel = new UserModel($this->db);
        $this->reviewModel = new ReviewModel($this->db);
    }

    public function profile($id) {
        // Try to get by Shop ID first
        $shop = $this->shopModel->getShopById($id);
        
        // Fallback: try to get by Seller ID (for admins or direct user links)
        if (!$shop) {
            $shop = $this->shopModel->getShopBySellerId($id);
        }

        // If still not found, check if this ID belongs to an admin and create a shop on the fly
        if (!$shop) {
            $user = $this->userModel->getUserById($id);
            if ($user && $user->role === 'admin') {
                $this->shopModel->create([
                    'seller_id' => $user->id,
                    'name' => $user->name . ' Shop',
                    'description' => 'Cửa hàng chính thức của ban quản trị.'
                ]);
                $shop = $this->shopModel->getShopBySellerId($user->id);
            }
        }

        if (!$shop) {
            die("Shop không tồn tại.");
        }

        $id = $shop->id; // Use the actual shop ID for subsequent queries
        $products = $this->productModel->getProductsByShop($id);
        $followerCount = $this->shopModel->getFollowerCount($id);
        
        $isFollowing = false;
        if (isset($_SESSION['user_id'])) {
            $isFollowing = $this->shopModel->isFollowing($id, $_SESSION['user_id']);
        }

        // Get reviews for shop products
        $shopReviews = $this->reviewModel->getReviewsByShopId($id);

        include 'app/views/shares/header.php';
        include 'app/views/shop/profile.php';
        include 'app/views/shares/footer.php';
    }

    public function follow($id) {
        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['success' => false, 'message' => 'Vui lòng đăng nhập để theo dõi shop.']);
            return;
        }

        $userId = $_SESSION['user_id'];
        $isFollowing = $this->shopModel->isFollowing($id, $userId);

        if ($isFollowing) {
            $this->shopModel->unfollow($id, $userId);
            $action = 'unfollowed';
        } else {
            $this->shopModel->follow($id, $userId);
            $action = 'followed';
        }

        $newCount = $this->shopModel->getFollowerCount($id);
        echo json_encode([
            'success' => true, 
            'action' => $action, 
            'followerCount' => $newCount
        ]);
    }
}
