<?php
class WishlistModel
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function isFavorited($userId, $productId)
    {
        $query = "SELECT COUNT(*) as count FROM product_likes WHERE user_id = :user_id AND product_id = :product_id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':user_id', $userId);
        $stmt->bindParam(':product_id', $productId);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_OBJ);
        return $result->count > 0;
    }

    public function addWishlist($userId, $productId)
    {
        if ($this->isFavorited($userId, $productId)) {
            return true; // Already favorited
        }
        
        $query = "INSERT INTO product_likes (user_id, product_id, created_at) VALUES (:user_id, :product_id, NOW())";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':user_id', $userId);
        $stmt->bindParam(':product_id', $productId);
        return $stmt->execute();
    }

    public function removeWishlist($userId, $productId)
    {
        $query = "DELETE FROM product_likes WHERE user_id = :user_id AND product_id = :product_id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':user_id', $userId);
        $stmt->bindParam(':product_id', $productId);
        return $stmt->execute();
    }

    public function toggleWishlist($userId, $productId)
    {
        if ($this->isFavorited($userId, $productId)) {
            $this->removeWishlist($userId, $productId);
            return 'removed';
        } else {
            $this->addWishlist($userId, $productId);
            return 'added';
        }
    }

    public function getUserWishlist($userId)
    {
        $query = "SELECT p.id, p.name, p.price, p.image, p.sold, p.stock, p.rating, p.discount_percent,
                         c.name as category_name, w.created_at
                  FROM product_likes w 
                  JOIN product p ON w.product_id = p.id 
                  LEFT JOIN category c ON p.category_id = c.id
                  WHERE w.user_id = :user_id 
                  ORDER BY w.created_at DESC";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':user_id', $userId);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }
    
    public function getUserWishlistIds($userId)
    {
        $query = "SELECT product_id FROM product_likes WHERE user_id = :user_id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':user_id', $userId);
        $stmt->execute();
        $results = $stmt->fetchAll(PDO::FETCH_COLUMN);
        return $results;
    }
}
?>
