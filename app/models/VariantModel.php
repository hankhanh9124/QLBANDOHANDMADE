<?php
class VariantModel
{
    private $conn;
    private $table_name = "product_variants";

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function getVariantsByProductId($productId)
    {
        $query = "SELECT * FROM " . $this->table_name . " WHERE product_id = :product_id ORDER BY id ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':product_id', $productId);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    public function addVariant($productId, $name, $image, $price = 0, $stock = 0)
    {
        $query = "INSERT INTO " . $this->table_name . " (product_id, name, image, price, stock) VALUES (:product_id, :name, :image, :price, :stock)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':product_id', $productId);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':image', $image);
        $stmt->bindParam(':price', $price);
        $stmt->bindParam(':stock', $stock);
        return $stmt->execute();
    }

    public function deleteVariant($id)
    {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }

    public function getVariantById($id)
    {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_OBJ);
    }

    public function updateVariant($id, $name, $image = null, $price = 0, $stock = 0)
    {
        if ($image) {
            $query = "UPDATE " . $this->table_name . " SET name = :name, image = :image, price = :price, stock = :stock WHERE id = :id";
        } else {
            $query = "UPDATE " . $this->table_name . " SET name = :name, price = :price, stock = :stock WHERE id = :id";
        }
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':price', $price);
        $stmt->bindParam(':stock', $stock);
        if ($image) {
            $stmt->bindParam(':image', $image);
        }
        return $stmt->execute();
    }
    public function updateVariantStock($id, $quantity)
    {
        $query = "UPDATE " . $this->table_name . " SET stock = GREATEST(0, stock - :quantity) WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':quantity', $quantity, PDO::PARAM_INT);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }
}
