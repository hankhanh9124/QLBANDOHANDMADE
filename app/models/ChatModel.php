<?php

class ChatModel {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
        $this->autoMigrate();
    }

    private function autoMigrate() {
        try {
            // 1. Conversations Table
            $sqlConv = "CREATE TABLE IF NOT EXISTS conversations (
                id INT AUTO_INCREMENT PRIMARY KEY,
                customer_id INT NOT NULL,
                seller_id INT NOT NULL DEFAULT 0,
                last_message TEXT,
                last_message_type ENUM('text', 'image', 'video', 'sticker', 'product', 'order') DEFAULT 'text',
                last_message_at DATETIME,
                is_pinned TINYINT(1) DEFAULT 0,
                is_muted TINYINT(1) DEFAULT 0,
                unread_admin INT DEFAULT 0,
                unread_customer INT DEFAULT 0,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                deleted_at TIMESTAMP NULL DEFAULT NULL
            )";
            $this->conn->exec($sqlConv);

            // Migration: Update ENUMs if table already exists
            try {
                $this->conn->exec("ALTER TABLE conversations MODIFY COLUMN last_message_type ENUM('text', 'image', 'video', 'sticker', 'product', 'order') DEFAULT 'text'");
            } catch (Exception $e) {}

            // Migration: Add seller_id if not exists
            try {
                $this->conn->exec("ALTER TABLE conversations ADD COLUMN seller_id INT NOT NULL DEFAULT 0 AFTER customer_id");
            } catch (Exception $e) {}

            // 2. Chat Messages Table
            $sqlMsgs = "CREATE TABLE IF NOT EXISTS chat_messages (
                id INT AUTO_INCREMENT PRIMARY KEY,
                conversation_id INT NOT NULL,
                sender_id INT NOT NULL,
                message_type ENUM('text', 'image', 'video', 'sticker', 'product', 'order') DEFAULT 'text',
                content TEXT,
                attachment_url VARCHAR(255),
                is_read TINYINT(1) DEFAULT 0,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )";
            $this->conn->exec($sqlMsgs);

            // Migration: Update ENUMs for chat_messages
            try {
                $this->conn->exec("ALTER TABLE chat_messages MODIFY COLUMN message_type ENUM('text', 'image', 'video', 'sticker', 'product', 'order') DEFAULT 'text'");
            } catch (Exception $e) {}

        } catch (PDOException $e) {
            // Ignore migration errors
        }
    }

    // ─── Conversations ──────────────────────────────────────────

    /**
     * Lấy danh sách hội thoại.
     * Admin → tất cả; Customer → chỉ của mình.
     */
    public function getConversations($userId, $isAdmin = false) {
        if ($isAdmin) {
            // Admin thấy tất cả
            $where = "c.deleted_at IS NULL";
        } else {
            // User thấy hội thoại mình là customer HOẶC mình là seller
            $where = "(c.customer_id = :uid OR c.seller_id = :uid) AND c.deleted_at IS NULL";
        }
        
        $query = "SELECT c.*, 
                         u_c.name AS customer_name, u_c.avatar AS customer_avatar,
                         u_s.name AS seller_name, u_s.avatar AS seller_avatar
                  FROM conversations c
                  JOIN user u_c ON c.customer_id = u_c.id
                  LEFT JOIN user u_s ON c.seller_id = u_s.id
                  WHERE $where
                  ORDER BY c.is_pinned DESC, c.updated_at DESC";
        
        $stmt = $this->conn->prepare($query);
        if (!$isAdmin) {
            $stmt->bindParam(':uid', $userId, PDO::PARAM_INT);
        }
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    /**
     * Lấy (hoặc tự tạo) conversation giữa customer và seller.
     */
    public function getOrCreateConversation($customerId, $sellerId = 0) {
        // Tìm hội thoại hiện có giữa 2 người này
        $stmt = $this->conn->prepare(
            "SELECT c.*, u_c.name AS customer_name, u_c.avatar AS customer_avatar,
                         u_s.name AS seller_name, u_s.avatar AS seller_avatar
             FROM conversations c
             JOIN user u_c ON c.customer_id = u_c.id
             LEFT JOIN user u_s ON c.seller_id = u_s.id
             WHERE c.customer_id = :cid AND c.seller_id = :sid LIMIT 1"
        );
        $stmt->bindParam(':cid', $customerId, PDO::PARAM_INT);
        $stmt->bindParam(':sid', $sellerId,   PDO::PARAM_INT);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $conv = $stmt->fetch(PDO::FETCH_OBJ);
            if ($conv->deleted_at !== null) {
                $this->conn->prepare("UPDATE conversations SET deleted_at = NULL WHERE id = ?")->execute([$conv->id]);
            }
            return $conv;
        }

        // Tự tạo mới
        $ins = $this->conn->prepare(
            "INSERT INTO conversations (customer_id, seller_id) VALUES (:cid, :sid)"
        );
        $ins->bindParam(':cid', $customerId, PDO::PARAM_INT);
        $ins->bindParam(':sid', $sellerId,   PDO::PARAM_INT);
        if ($ins->execute()) {
            $id = $this->conn->lastInsertId();
            return $this->getConversationById($id);
        }
        return false;
    }

    /**
     * Lấy conversation theo ID.
     */
    public function getConversationById($convId) {
        $stmt = $this->conn->prepare(
            "SELECT c.*, u_c.name AS customer_name, u_c.avatar AS customer_avatar,
                         u_s.name AS seller_name, u_s.avatar AS seller_avatar
             FROM conversations c
             JOIN user u_c ON c.customer_id = u_c.id
             LEFT JOIN user u_s ON c.seller_id = u_s.id
             WHERE c.id = :id LIMIT 1"
        );
        $stmt->bindParam(':id', $convId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_OBJ);
    }

    // ─── Messages ────────────────────────────────────────────────

    /**
     * Lấy tất cả tin nhắn của một hội thoại.
     */
    public function getMessages($conversationId) {
        $stmt = $this->conn->prepare(
            "SELECT m.*, u.name AS sender_name, u.avatar AS sender_avatar
             FROM chat_messages m
             JOIN user u ON m.sender_id = u.id
             WHERE m.conversation_id = :cid
             ORDER BY m.created_at ASC"
        );
        $stmt->bindParam(':cid', $conversationId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    /**
     * Lấy tin nhắn mới hơn một timestamp (dùng cho polling).
     */
    public function getNewMessages($conversationId, $afterTimestamp) {
        $stmt = $this->conn->prepare(
            "SELECT m.*, u.name AS sender_name, u.avatar AS sender_avatar
             FROM chat_messages m
             JOIN user u ON m.sender_id = u.id
             WHERE m.conversation_id = :cid AND m.created_at > :ts
             ORDER BY m.created_at ASC"
        );
        $stmt->bindParam(':cid', $conversationId, PDO::PARAM_INT);
        $stmt->bindParam(':ts', $afterTimestamp);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    /**
     * Gửi một tin nhắn.
     */
    public function sendMessage($conversationId, $senderId, $type, $content, $attachmentUrl = null) {
        $stmt = $this->conn->prepare(
            "INSERT INTO chat_messages (conversation_id, sender_id, message_type, content, attachment_url)
             VALUES (:cid, :sid, :type, :content, :url)"
        );
        $stmt->bindParam(':cid',     $conversationId, PDO::PARAM_INT);
        $stmt->bindParam(':sid',     $senderId,       PDO::PARAM_INT);
        $stmt->bindParam(':type',    $type);
        $stmt->bindParam(':content', $content);
        $stmt->bindParam(':url',     $attachmentUrl);

        if ($stmt->execute()) {
            $lastId = $this->conn->lastInsertId();
            // Cập nhật last_message trên conversation
            $preview = $type === 'text' ? mb_substr($content, 0, 100) : "[$type]";
            $upd = $this->conn->prepare(
                "UPDATE conversations
                 SET last_message = :msg, last_message_type = :type,
                     last_message_at = NOW(), updated_at = NOW()
                 WHERE id = :cid"
            );
            $upd->bindParam(':msg',  $preview);
            $upd->bindParam(':type', $type);
            $upd->bindParam(':cid',  $conversationId, PDO::PARAM_INT);
            $upd->execute();
            return $lastId;
        }
        return false;
    }

    /**
     * Đánh dấu tin nhắn đã đọc (đánh dấu những tin từ phía đối phương).
     */
    public function markAsRead($conversationId, $userId) {
        $stmt = $this->conn->prepare(
            "UPDATE chat_messages SET is_read = 1
             WHERE conversation_id = :cid AND sender_id != :uid AND is_read = 0"
        );
        $stmt->bindParam(':cid', $conversationId, PDO::PARAM_INT);
        $stmt->bindParam(':uid', $userId,         PDO::PARAM_INT);
        $stmt->execute();

        // Reset unread counter
        $isAdmin = isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
        $col = $isAdmin ? 'unread_admin' : 'unread_customer';
        $this->conn->exec("UPDATE conversations SET `$col` = 0 WHERE id = $conversationId");
    }

    /**
     * Đếm tin chưa đọc.
     */
    public function getUnreadCount($conversationId, $userId) {
        $stmt = $this->conn->prepare(
            "SELECT COUNT(*) FROM chat_messages
             WHERE conversation_id = :cid AND sender_id != :uid AND is_read = 0"
        );
        $stmt->bindParam(':cid', $conversationId, PDO::PARAM_INT);
        $stmt->bindParam(':uid', $userId,         PDO::PARAM_INT);
        $stmt->execute();
        return (int)$stmt->fetchColumn();
    }

    /**
     * Tổng tin chưa đọc của user (tất cả hội thoại).
     */
    public function getTotalUnread($userId) {
        $stmt = $this->conn->prepare(
            "SELECT COUNT(*) FROM chat_messages m
             JOIN conversations c ON m.conversation_id = c.id
             WHERE c.customer_id = :uid AND m.sender_id != :uid2 AND m.is_read = 0"
        );
        $stmt->bindParam(':uid',  $userId, PDO::PARAM_INT);
        $stmt->bindParam(':uid2', $userId, PDO::PARAM_INT);
        $stmt->execute();
        return (int)$stmt->fetchColumn();
    }

    /**
     * Cập nhật trạng thái (Ghim, Tắt thông báo).
     */
    public function updateStatus($convId, $field, $value) {
        $allowed = ['is_pinned', 'is_muted'];
        if (!in_array($field, $allowed)) return false;
        
        $stmt = $this->conn->prepare("UPDATE conversations SET `$field` = :val WHERE id = :id");
        $stmt->bindParam(':val', $value, PDO::PARAM_INT);
        $stmt->bindParam(':id',  $convId, PDO::PARAM_INT);
        return $stmt->execute();
    }

    /**
     * Xóa vĩnh viễn hội thoại và tất cả tin nhắn.
     */
    public function softDelete($convId) {
        $this->conn->beginTransaction();
        try {
            // 1. Xóa tất cả tin nhắn
            $stmt1 = $this->conn->prepare("DELETE FROM chat_messages WHERE conversation_id = :id");
            $stmt1->bindParam(':id', $convId, PDO::PARAM_INT);
            $stmt1->execute();

            // 2. Xóa cuộc hội thoại
            $stmt2 = $this->conn->prepare("DELETE FROM conversations WHERE id = :id");
            $stmt2->bindParam(':id', $convId, PDO::PARAM_INT);
            $stmt2->execute();

            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            $this->conn->rollBack();
            return false;
        }
    }

    /**
     * Đánh dấu chưa đọc thủ công.
     */
    public function markUnreadManual($convId, $userId) {
        $isAdmin = isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
        $col = $isAdmin ? 'unread_admin' : 'unread_customer';
        // Tăng unread lên ít nhất 1 để hiện badge
        $stmt = $this->conn->prepare("UPDATE conversations SET `$col` = IF(`$col` > 0, `$col`, 1) WHERE id = :id");
        $stmt->bindParam(':id', $convId, PDO::PARAM_INT);
        return $stmt->execute();
    }
}
