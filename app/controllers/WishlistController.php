<?php
require_once 'app/config/database.php';
require_once 'app/models/WishlistModel.php';
require_once 'app/models/ProductModel.php';
require_once 'app/models/UserModel.php';

class WishlistController
{
    private $db;
    private $wishlistModel;

    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $this->db = (new Database())->getConnection();
        $this->wishlistModel = new WishlistModel($this->db);
    }

    public function toggle()
    {
        header('Content-Type: application/json');
        
        $userId = $_SESSION['user_id'] ?? null;
        if (!$userId) {
            echo json_encode(['success' => false, 'message' => 'Vui lòng đăng nhập để thêm vào yêu thích.']);
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $productId = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;
            
            if ($productId > 0) {
                $action = $this->wishlistModel->toggleWishlist($userId, $productId);
                
                // Sync likes count in product table
                $query = "UPDATE product SET likes = (SELECT COUNT(*) FROM product_likes WHERE product_id = :product_id) WHERE id = :product_id";
                $stmt = $this->db->prepare($query);
                $stmt->bindParam(':product_id', $productId);
                $stmt->execute();

                // Get new likes count
                $query = "SELECT likes FROM product WHERE id = :id";
                $stmt = $this->db->prepare($query);
                $stmt->bindParam(':id', $productId);
                $stmt->execute();
                $newLikes = $stmt->fetch(PDO::FETCH_OBJ)->likes ?? 0;

                // Update session wishlist cache
                $_SESSION['wishlist_items'] = $this->wishlistModel->getUserWishlistIds($userId);
                
                // Get product and seller info to notify
                $productModel = new ProductModel($this->db);
                $product = $productModel->getProductById($productId);
                
                if ($action === 'added' && $product && $product->user_id != $userId) {
                    require_once 'app/models/NotificationModel.php';
                    $notificationModel = new NotificationModel($this->db);
                    $userName = $_SESSION['user_name'] ?? 'Một người dùng';
                    $notificationModel->create(
                        $product->user_id,
                        'Yêu thích sản phẩm',
                        "$userName đã thích sản phẩm: " . $product->name,
                        'wishlist',
                        'index.php?url=Product/show/' . $productId
                    );
                }

                echo json_encode([
                    'success' => true, 
                    'action' => $action, 
                    'likes' => $newLikes,
                    'message' => $action === 'added' ? 'Đã thêm vào yêu thích' : 'Đã bỏ yêu thích'
                ]);
                exit;
            }
        }
        
        echo json_encode(['success' => false, 'message' => 'Yêu cầu không hợp lệ.']);
        exit;
    }

    public function index()
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . BASE_URL . 'index.php?url=Page/login');
            exit;
        }

        $userId = $_SESSION['user_id'];
        $wishlistProducts = $this->wishlistModel->getUserWishlist($userId);
        
        // Fetch user data for sidebar
        $userModel = new UserModel($this->db);
        $user = $userModel->getUserById($userId);
        
        // Define breadcrumbs
        $breadcrumbs = [
            'Trang chủ' => BASE_URL,
            'Tài khoản' => BASE_URL . 'index.php?url=Page/profile',
            'Sản phẩm yêu thích' => '#'
        ];

        require_once 'app/views/shares/header.php';
        require_once 'app/views/account/wishlist.php';
        require_once 'app/views/shares/footer.php';
    }
}
?>
