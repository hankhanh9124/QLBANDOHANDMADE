<?php
require_once 'app/config/database.php';
require_once 'app/models/NotificationModel.php';

class NotificationController {
    private $db;
    private $notificationModel;

    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        
        if (!isset($_SESSION['user_id'])) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit;
        }

        $database = new Database();
        $this->db = $database->getConnection();
        $this->notificationModel = new NotificationModel($this->db);
    }

    public function getUnread() {
        $userId = $_SESSION['user_id'];
        $count = $this->notificationModel->getUnreadCount($userId);
        $notifications = $this->notificationModel->getByUserId($userId, 10);
        
        // Format dates for display
        foreach ($notifications as $n) {
            $n->created_at = $this->timeAgo($n->created_at);
        }

        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'count' => $count,
            'notifications' => $notifications
        ]);
    }

    public function markRead() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'];
            $this->notificationModel->markAsRead($id);
            header('Content-Type: application/json');
            echo json_encode(['success' => true]);
        }
    }

    public function markAllRead() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $userId = $_SESSION['user_id'];
            $this->notificationModel->markAllAsRead($userId);
            header('Content-Type: application/json');
            echo json_encode(['success' => true]);
        }
    }

    private function timeAgo($timestamp) {
        $time = strtotime($timestamp);
        $diff = time() - $time;
        
        if ($diff < 60) return 'Vừa xong';
        if ($diff < 3600) return floor($diff / 60) . ' phút trước';
        if ($diff < 86400) return floor($diff / 3600) . ' giờ trước';
        return date('d/m/Y', $time);
    }
}
