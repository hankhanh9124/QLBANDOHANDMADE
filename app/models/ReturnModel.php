<?php
class ReturnModel {
    private $db;

    public function __construct($db) {
        $this->db = $db;
        $this->autoMigrate();
    }

    private function autoMigrate() {
        try {
            // 1. Returns Table
            $sqlReturns = "CREATE TABLE IF NOT EXISTS returns (
                id INT AUTO_INCREMENT PRIMARY KEY,
                order_id INT NOT NULL,
                user_id INT NOT NULL,
                reason VARCHAR(255) NOT NULL,
                description TEXT,
                amount DECIMAL(10,2) NOT NULL,
                status ENUM('pending', 'reviewing', 'approved', 'rejected', 'refunded') DEFAULT 'pending',
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            )";
            $this->db->exec($sqlReturns);

            // 2. Return Media Table
            $sqlMedia = "CREATE TABLE IF NOT EXISTS return_media (
                id INT AUTO_INCREMENT PRIMARY KEY,
                return_id INT NOT NULL,
                file_path VARCHAR(255) NOT NULL,
                file_type ENUM('image', 'video') NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )";
            $this->db->exec($sqlMedia);

            // 3. Return History Table
            $sqlHistory = "CREATE TABLE IF NOT EXISTS return_history (
                id INT AUTO_INCREMENT PRIMARY KEY,
                return_id INT NOT NULL,
                status VARCHAR(50) NOT NULL,
                note TEXT,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )";
            $this->db->exec($sqlHistory);
            
        } catch (PDOException $e) {
            // Ignore migration errors
        }
    }

    public function createRequest($orderId, $userId, $reason, $description, $amount) {
        $query = "INSERT INTO returns (order_id, user_id, reason, description, amount, status) 
                  VALUES (:order_id, :user_id, :reason, :description, :amount, 'pending')";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':order_id', $orderId);
        $stmt->bindParam(':user_id', $userId);
        $stmt->bindParam(':reason', $reason);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':amount', $amount);
        
        if ($stmt->execute()) {
            $returnId = $this->db->lastInsertId();
            $this->addHistory($returnId, 'pending', 'Yêu cầu trả hàng đã được gửi.');
            return $returnId;
        }
        return false;
    }

    public function addMedia($returnId, $filePath, $fileType) {
        $query = "INSERT INTO return_media (return_id, file_path, file_type) VALUES (:return_id, :file_path, :file_type)";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':return_id', $returnId);
        $stmt->bindParam(':file_path', $filePath);
        $stmt->bindParam(':file_type', $fileType);
        return $stmt->execute();
    }

    public function addHistory($returnId, $status, $note = '') {
        $query = "INSERT INTO return_history (return_id, status, note) VALUES (:return_id, :status, :note)";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':return_id', $returnId);
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':note', $note);
        return $stmt->execute();
    }

    public function getPendingReturnsCount() {
        $query = "SELECT COUNT(*) as count FROM returns WHERE status = 'pending'";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_OBJ)->count;
    }

    public function getReturnById($id) {
        $query = "SELECT r.*, o.id as order_number, u.name as user_name FROM returns r 
                  JOIN orders o ON r.order_id = o.id 
                  JOIN user u ON r.user_id = u.id
                  WHERE r.id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $return = $stmt->fetch(PDO::FETCH_OBJ);
        
        if ($return) {
            $return->media = $this->getMediaByReturnId($id);
            $return->history = $this->getHistoryByReturnId($id);
        }
        return $return;
    }

    public function getMediaByReturnId($returnId) {
        $query = "SELECT * FROM return_media WHERE return_id = :return_id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':return_id', $returnId);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    public function getHistoryByReturnId($returnId) {
        $query = "SELECT * FROM return_history WHERE return_id = :return_id ORDER BY created_at DESC";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':return_id', $returnId);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    public function updateStatus($id, $status, $note = '') {
        $query = "UPDATE returns SET status = :status WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':id', $id);
        
        if ($stmt->execute()) {
            $this->addHistory($id, $status, $note);
            return true;
        }
        return false;
    }

    public function getReturnsByUser($userId) {
        $query = "SELECT r.*, o.id as order_number FROM returns r 
                  JOIN orders o ON r.order_id = o.id 
                  WHERE r.user_id = :user_id ORDER BY r.created_at DESC";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':user_id', $userId);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }
    
    public function getReturnByOrderId($orderId) {
        $query = "SELECT * FROM returns WHERE order_id = :order_id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':order_id', $orderId);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_OBJ);
    }

    public function getReturnsByAdmin() {
        $query = "SELECT r.*, o.id as order_number, u.name as user_name FROM returns r 
                  JOIN orders o ON r.order_id = o.id 
                  JOIN user u ON r.user_id = u.id
                  ORDER BY r.created_at DESC";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }
}
?>
