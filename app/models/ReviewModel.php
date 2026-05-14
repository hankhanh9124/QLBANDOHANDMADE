<?php
class ReviewModel
{
    private $conn;
    private $table_name = "product_reviews";

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function addReview($product_id, $user_id, $rating, $comment)
    {
        $query = "INSERT INTO " . $this->table_name . " (product_id, user_id, rating, comment) VALUES (:product_id, :user_id, :rating, :comment)";
        $stmt = $this->conn->prepare($query);

        $comment = htmlspecialchars(strip_tags($comment));

        $stmt->bindParam(':product_id', $product_id);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->bindParam(':rating', $rating);
        $stmt->bindParam(':comment', $comment);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function getReviewsByProductId($product_id)
    {
        $query = "SELECT r.*, u.name as user_name 
                  FROM " . $this->table_name . " r 
                  LEFT JOIN user u ON r.user_id = u.id 
                  WHERE r.product_id = :product_id 
                  ORDER BY r.created_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':product_id', $product_id);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    public function hasUserReviewed($product_id, $user_id)
    {
        $query = "SELECT id FROM " . $this->table_name . " WHERE product_id = :product_id AND user_id = :user_id LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':product_id', $product_id);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();

        return $stmt->rowCount() > 0;
    }

    public function getReviewsByShopId($shopId)
    {
        $query = "SELECT r.*, u.name as user_name, p.name as product_name, p.image as product_image
                  FROM " . $this->table_name . " r 
                  LEFT JOIN user u ON r.user_id = u.id 
                  JOIN product p ON r.product_id = p.id
                  JOIN shops s ON p.user_id = s.seller_id
                  WHERE s.id = :shop_id 
                  ORDER BY r.created_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':shop_id', $shopId);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }
}
?>
