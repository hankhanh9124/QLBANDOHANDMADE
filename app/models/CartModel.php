<?php
class CartModel
{
    private $conn;
    private $table_name = "cart_items";

    public function __construct($db)
    {
        $this->conn = $db;
        $this->ensureTableExists();
    }

    private function ensureTableExists()
    {
        $query = "CREATE TABLE IF NOT EXISTS " . $this->table_name . " (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            product_id INT NOT NULL,
            variant_id INT DEFAULT 0,
            quantity INT NOT NULL DEFAULT 1,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            UNIQUE KEY user_product_variant (user_id, product_id, variant_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
        
        try {
            $this->conn->exec($query);
        } catch (PDOException $e) {
            // Error handling if needed
        }
    }

    public function getItems($userId)
    {
        $query = "SELECT ci.*, p.name as product_name, p.price as base_price, p.image as base_image, p.discount_percent, p.stock as base_stock,
                         pv.name as variant_name, pv.price as variant_price, pv.image as variant_image, pv.stock as variant_stock
                  FROM " . $this->table_name . " ci
                  JOIN product p ON ci.product_id = p.id
                  LEFT JOIN product_variants pv ON ci.variant_id = pv.id
                  WHERE ci.user_id = :user_id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $userId);
        $stmt->execute();
        
        $items = [];
        while ($row = $stmt->fetch(PDO::FETCH_OBJ)) {
            $discount = isset($row->discount_percent) ? (int)$row->discount_percent : 0;
            
            // Use variant price if exists, otherwise base price
            $price = ($row->variant_id > 0 && $row->variant_price > 0) ? $row->variant_price : $row->base_price;
            $finalPrice = ($discount > 0) ? $price * (1 - $discount/100) : $price;
            
            // Use variant image and stock if exists, otherwise base image and stock
            $image = ($row->variant_id > 0 && !empty($row->variant_image)) ? $row->variant_image : $row->base_image;
            $stock = ($row->variant_id > 0) ? $row->variant_stock : $row->base_stock;
            
            // Key the cart items by product_id and variant_id to support multiple variants of the same product
            $cartKey = $row->product_id . ($row->variant_id > 0 ? '_' . $row->variant_id : '');
            
            $items[$cartKey] = [
                'id' => $row->product_id,
                'product_id' => $row->product_id,
                'variant_id' => $row->variant_id,
                'name' => $row->product_name . ($row->variant_id > 0 ? ' - Mẫu: ' . $row->variant_name : ''),
                'price' => $finalPrice,
                'image' => $image,
                'stock' => $stock,
                'quantity' => $row->quantity
            ];
        }
        return $items;
    }

    public function addItem($userId, $productId, $variantId = 0, $quantity = 1)
    {
        $query = "INSERT INTO " . $this->table_name . " (user_id, product_id, variant_id, quantity) 
                  VALUES (:user_id, :product_id, :variant_id, :quantity)
                  ON DUPLICATE KEY UPDATE quantity = quantity + :quantity_update";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $userId);
        $stmt->bindParam(':product_id', $productId);
        $stmt->bindParam(':variant_id', $variantId);
        $stmt->bindParam(':quantity', $quantity);
        $stmt->bindParam(':quantity_update', $quantity);
        
        return $stmt->execute();
    }

    public function updateQuantity($userId, $productId, $quantity, $variantId = 0)
    {
        if ($quantity <= 0) {
            return $this->removeItem($userId, $productId, $variantId);
        }

        $query = "UPDATE " . $this->table_name . " SET quantity = :quantity 
                  WHERE user_id = :user_id AND product_id = :product_id AND variant_id = :variant_id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':quantity', $quantity);
        $stmt->bindParam(':user_id', $userId);
        $stmt->bindParam(':product_id', $productId);
        $stmt->bindParam(':variant_id', $variantId);
        
        return $stmt->execute();
    }

    public function removeItem($userId, $productId, $variantId = 0)
    {
        $query = "DELETE FROM " . $this->table_name . " WHERE user_id = :user_id AND product_id = :product_id AND variant_id = :variant_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $userId);
        $stmt->bindParam(':product_id', $productId);
        $stmt->bindParam(':variant_id', $variantId);
        return $stmt->execute();
    }

    public function clear($userId)
    {
        $query = "DELETE FROM " . $this->table_name . " WHERE user_id = :user_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $userId);
        return $stmt->execute();
    }
}
