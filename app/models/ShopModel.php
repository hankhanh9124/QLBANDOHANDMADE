<?php
class ShopModel {
    private $conn;
    private $table_name = "shops";

    public function __construct($db) {
        $this->conn = $db;
    }

    public function create($data) {
        $query = "INSERT INTO " . $this->table_name . " 
                  (seller_id, name, description, status) 
                  VALUES (:seller_id, :name, :description, 'active')";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':seller_id', $data['seller_id']);
        $stmt->bindParam(':name', $data['name']);
        $stmt->bindParam(':description', $data['description']);
        return $stmt->execute();
    }

    public function getBySellerId($seller_id) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE seller_id = :seller_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':seller_id', $seller_id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_OBJ);
    }

    public function update($id, $data) {
        $query = "UPDATE " . $this->table_name . " 
                  SET name = :name, description = :description, logo = :logo, banner = :banner 
                  WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':name', $data['name']);
        $stmt->bindParam(':description', $data['description']);
        $stmt->bindParam(':logo', $data['logo']);
        $stmt->bindParam(':banner', $data['banner']);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }

    public function getAllShops() {
        $query = "SELECT s.*, u.name as seller_name, u.email as seller_email 
                  FROM " . $this->table_name . " s
                  JOIN user u ON s.seller_id = u.id
                  ORDER BY s.created_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    public function updateShopStatus($id, $status) {
        $query = "UPDATE " . $this->table_name . " SET status = :status WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }

    public function getShopById($id) {
        $query = "SELECT s.*, u.name as seller_name, u.avatar as seller_avatar 
                  FROM " . $this->table_name . " s
                  JOIN user u ON s.seller_id = u.id
                  WHERE s.id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_OBJ);
    }

    public function getShopBySellerId($sellerId) {
        $query = "SELECT s.*, u.name as seller_name, u.avatar as seller_avatar 
                  FROM " . $this->table_name . " s
                  JOIN user u ON s.seller_id = u.id
                  WHERE s.seller_id = :seller_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':seller_id', $sellerId);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_OBJ);
    }

    public function getFollowerCount($shopId) {
        $query = "SELECT COUNT(*) as total FROM shop_followers WHERE shop_id = :shop_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':shop_id', $shopId);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_OBJ)->total ?? 0;
    }

    public function isFollowing($shopId, $userId) {
        if (!$userId) return false;
        $query = "SELECT COUNT(*) FROM shop_followers WHERE shop_id = :shop_id AND user_id = :user_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':shop_id', $shopId);
        $stmt->bindParam(':user_id', $userId);
        $stmt->execute();
        return $stmt->fetchColumn() > 0;
    }

    public function follow($shopId, $userId) {
        $query = "INSERT IGNORE INTO shop_followers (shop_id, user_id) VALUES (:shop_id, :user_id)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':shop_id', $shopId);
        $stmt->bindParam(':user_id', $userId);
        return $stmt->execute();
    }

    public function unfollow($shopId, $userId) {
        $query = "DELETE FROM shop_followers WHERE shop_id = :shop_id AND user_id = :user_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':shop_id', $shopId);
        $stmt->bindParam(':user_id', $userId);
        return $stmt->execute();
    }
}
