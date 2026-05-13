<?php
class SellerRequestModel {
    private $conn;
    private $table_name = "seller_requests";

    public function __construct($db) {
        $this->conn = $db;
    }

    public function create($data) {
        $query = "INSERT INTO " . $this->table_name . " 
                  (user_id, shop_name, shop_description, product_types, identity_proof, portfolio_links, bank_account, status) 
                  VALUES (:user_id, :shop_name, :shop_description, :product_types, :identity_proof, :portfolio_links, :bank_account, 'pending')";
        
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':user_id', $data['user_id']);
        $stmt->bindParam(':shop_name', $data['shop_name']);
        $stmt->bindParam(':shop_description', $data['shop_description']);
        $stmt->bindParam(':product_types', $data['product_types']);
        $stmt->bindParam(':identity_proof', $data['identity_proof']);
        $stmt->bindParam(':portfolio_links', $data['portfolio_links']);
        $stmt->bindParam(':bank_account', $data['bank_account']);

        return $stmt->execute();
    }

    public function getAllPending() {
        $query = "SELECT r.*, u.name as user_name, u.email as user_email 
                  FROM " . $this->table_name . " r 
                  JOIN user u ON r.user_id = u.id 
                  WHERE r.status = 'pending' 
                  ORDER BY r.created_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    public function getById($id) {
        $query = "SELECT r.*, u.name as user_name, u.email as user_email, u.phone as user_phone 
                  FROM " . $this->table_name . " r 
                  JOIN user u ON r.user_id = u.id 
                  WHERE r.id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_OBJ);
    }

    public function updateStatus($id, $status, $reject_reason = null) {
        $query = "UPDATE " . $this->table_name . " 
                  SET status = :status, reject_reason = :reject_reason 
                  WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':reject_reason', $reject_reason);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }

    public function getLatestRequestByUserId($user_id) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE user_id = :user_id ORDER BY created_at DESC LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_OBJ);
    }
}
