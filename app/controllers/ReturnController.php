<?php
require_once 'app/config/database.php';
require_once 'app/models/ReturnModel.php';
require_once 'app/models/OrderModel.php';
require_once 'app/models/NotificationModel.php';

class ReturnController {
    private $db;
    private $returnModel;
    private $orderModel;

    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . BASE_URL . 'index.php?url=Page/login');
            exit;
        }
        $this->db = (new Database())->getConnection();
        $this->returnModel = new ReturnModel($this->db);
        $this->orderModel = new OrderModel($this->db);
    }

    public function request($orderId) {
        $order = $this->orderModel->getOrderById($orderId);
        if (!$order || $order->user_id != $_SESSION['user_id'] || $order->status != 'completed') {
            $_SESSION['error_message'] = "Không thể yêu cầu trả hàng cho đơn hàng này.";
            header('Location: ' . BASE_URL . 'index.php?url=Page/orders');
            exit;
        }

        // Check if already has a return request
        $existing = $this->returnModel->getReturnByOrderId($orderId);
        if ($existing) {
            header('Location: ' . BASE_URL . 'index.php?url=Return/detail/' . $existing->id);
            exit;
        }

        require_once 'app/views/shares/header.php';
        require_once 'app/views/account/return_request.php';
        require_once 'app/views/shares/footer.php';
    }

    public function submit() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $orderId = $_POST['order_id'];
            $reason = $_POST['reason'];
            $description = $_POST['description'];
            $amount = $_POST['amount'];
            $userId = $_SESSION['user_id'];

            $returnId = $this->returnModel->createRequest($orderId, $userId, $reason, $description, $amount);

            if ($returnId) {
                // Handle File Uploads
                if (!empty($_FILES['media']['name'][0])) {
                    $targetDir = "public/uploads/returns/";
                    if (!is_dir($targetDir)) mkdir($targetDir, 0777, true);

                    foreach ($_FILES['media']['name'] as $key => $name) {
                        $tmpName = $_FILES['media']['tmp_name'][$key];
                        $type = $_FILES['media']['type'][$key];
                        $extension = pathinfo($name, PATHINFO_EXTENSION);
                        $fileName = "return_" . $returnId . "_" . $key . "_" . time() . "." . $extension;
                        $targetPath = $targetDir . $fileName;

                        if (move_uploaded_file($tmpName, $targetPath)) {
                            $fileType = (strpos($type, 'video') !== false) ? 'video' : 'image';
                            $this->returnModel->addMedia($returnId, $targetPath, $fileType);
                        }
                    }
                }

                // Notify Admin
                $notifModel = new NotificationModel($this->db);
                $stmt = $this->db->query("SELECT id FROM user WHERE role = 'admin'");
                $admins = $stmt->fetchAll(PDO::FETCH_OBJ);
                foreach ($admins as $admin) {
                    $msg = "Yêu cầu trả hàng mới cho đơn hàng #" . $orderId;
                    $link = "index.php?url=Dashboard/returnDetail/" . $returnId;
                    $notifModel->addNotification($admin->id, $msg, $link);
                }

                $_SESSION['success_message'] = "Yêu cầu trả hàng của bạn đã được gửi thành công.";
                header('Location: ' . BASE_URL . 'index.php?url=Return/detail/' . $returnId);
                exit;
            }
        }
        header('Location: ' . BASE_URL . 'index.php?url=Page/orders');
        exit;
    }

    public function detail($id) {
        $return = $this->returnModel->getReturnById($id);
        if (!$return || ($return->user_id != $_SESSION['user_id'] && $_SESSION['user_role'] != 'admin')) {
            die("Unauthorized");
        }

        $order = $this->orderModel->getOrderById($return->order_id);

        require_once 'app/views/shares/header.php';
        require_once 'app/views/account/return_detail.php';
        require_once 'app/views/shares/footer.php';
    }
}
?>
