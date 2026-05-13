<?php
class OrderModel {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
        // Auto-migrate: add variant_id to order_detail if it doesn't exist
        try {
            $this->conn->exec("ALTER TABLE order_detail ADD COLUMN variant_id INT DEFAULT 0 AFTER product_id");
        } catch (PDOException $e) { /* ignore if already exists */ }

        // Auto-migrate: add columns to orders table
        try {
            $this->conn->exec("ALTER TABLE orders ADD COLUMN payment_method VARCHAR(50) DEFAULT 'cod' AFTER status");
            $this->conn->exec("ALTER TABLE orders ADD COLUMN shipping_fee DECIMAL(10,2) DEFAULT 0 AFTER payment_method");
            $this->conn->exec("ALTER TABLE orders ADD COLUMN recipient_name VARCHAR(255) AFTER shipping_fee");
            $this->conn->exec("ALTER TABLE orders ADD COLUMN recipient_phone VARCHAR(20) AFTER recipient_name");
            $this->conn->exec("ALTER TABLE orders ADD COLUMN recipient_address TEXT AFTER recipient_phone");
            $this->conn->exec("ALTER TABLE orders ADD COLUMN note TEXT AFTER recipient_address");
        } catch (PDOException $e) { /* ignore if already exists */ }
    }

    public function createOrder($userId, $total, $data = []) {
        $status = ($data['payment_method'] === 'cod') ? 'confirmed' : 'pending';
        
        $query = "INSERT INTO orders (user_id, total, status, payment_method, shipping_fee, recipient_name, recipient_phone, recipient_address, note, created_at) 
                  VALUES (:user_id, :total, :status, :payment_method, :shipping_fee, :recipient_name, :recipient_phone, :recipient_address, :note, CURRENT_TIMESTAMP)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $userId);
        $stmt->bindParam(':total', $total);
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':payment_method', $data['payment_method']);
        $stmt->bindParam(':shipping_fee', $data['shipping_fee']);
        $stmt->bindParam(':recipient_name', $data['recipient_name']);
        $stmt->bindParam(':recipient_phone', $data['recipient_phone']);
        $stmt->bindParam(':recipient_address', $data['recipient_address']);
        $stmt->bindParam(':note', $data['note']);
        
        if ($stmt->execute()) {
            return $this->conn->lastInsertId();
        }
        return false;
    }

    public function addOrderDetail($orderId, $productId, $quantity, $price, $variantId = 0) {
        $query = "INSERT INTO order_detail (order_id, product_id, variant_id, quantity, price) VALUES (:order_id, :product_id, :variant_id, :quantity, :price)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':order_id', $orderId);
        $stmt->bindParam(':product_id', $productId);
        $stmt->bindParam(':variant_id', $variantId);
        $stmt->bindParam(':quantity', $quantity);
        $stmt->bindParam(':price', $price);
        return $stmt->execute();
    }

    // Nếu người dùng chưa có tài khoản, ta có thể tạo nhanh
    public function createQuickUser($name, $email, $phone, $address, $password) {
        $query = "SELECT id FROM user WHERE email = :email";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        if ($stmt->rowCount() > 0) {
            return $stmt->fetch(PDO::FETCH_OBJ)->id;
        }

        $query = "INSERT INTO user (name, email, phone, address, password, role) VALUES (:name, :email, :phone, :address, :password, 'customer')";
        $stmt = $this->conn->prepare($query);
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':phone', $phone);
        $stmt->bindParam(':address', $address);
        $stmt->bindParam(':password', $hashedPassword);
        if ($stmt->execute()) {
            return $this->conn->lastInsertId();
        }
        return false;
    }

    public function getOrdersByUserId($userId, $status = null) {
        $query = "SELECT o.*, 
                         od.product_id, od.variant_id, od.quantity, od.price as item_price,
                          p.name as product_name, p.image as product_image, p.user_id as product_user_id,
                          pv.name as variant_name
                  FROM orders o
                  LEFT JOIN order_detail od ON o.id = od.order_id
                  LEFT JOIN product p ON od.product_id = p.id
                  LEFT JOIN product_variants pv ON od.variant_id = pv.id
                  WHERE o.user_id = :user_id";
        
        if ($status) {
            $query .= " AND o.status = :status";
        }
        
        $query .= " ORDER BY o.created_at DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $userId);
        if ($status) {
            $stmt->bindParam(':status', $status);
        }
        $stmt->execute();
        
        $results = $stmt->fetchAll(PDO::FETCH_OBJ);
        
        // Group items by order
        $orders = [];
        foreach ($results as $row) {
            $orderId = $row->id;
            if (!isset($orders[$orderId])) {
                $orders[$orderId] = (object) [
                    'id' => $row->id,
                    'user_id' => $row->user_id,
                    'total' => $row->total,
                    'status' => $row->status,
                    'payment_method' => $row->payment_method,
                    'shipping_fee' => $row->shipping_fee,
                    'created_at' => $row->created_at,
                    'items' => []
                ];
            }
            if ($row->product_id) {
                $orders[$orderId]->items[] = (object) [
                    'product_id' => $row->product_id,
                    'variant_id' => $row->variant_id,
                    'name' => $row->product_name,
                    'variant_name' => $row->variant_name,
                    'image' => $row->product_image,
                    'seller_id' => $row->product_user_id,
                    'quantity' => $row->quantity,
                    'price' => $row->item_price
                ];
            }
        }
        return array_values($orders);
    }

    public function cancelOrderWithReason($orderId, $reason) {
        $query = "UPDATE orders SET status = 'cancelled', cancel_reason = :reason WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':reason', $reason);
        $stmt->bindParam(':id', $orderId);
        return $stmt->execute();
    }

    public function getOrderById($orderId) {
        $query = "SELECT o.*, 
                         od.product_id, od.variant_id, od.quantity, od.price as item_price,
                         p.name as product_name, p.image as product_image, p.user_id as product_user_id, u_s.role as seller_role,
                         u.name as user_name
                  FROM orders o
                  LEFT JOIN order_detail od ON o.id = od.order_id
                  LEFT JOIN product p ON od.product_id = p.id
                  LEFT JOIN user u ON o.user_id = u.id
                  LEFT JOIN user u_s ON p.user_id = u_s.id
                  WHERE o.id = :id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $orderId);
        $stmt->execute();
        
        $results = $stmt->fetchAll(PDO::FETCH_OBJ);
        
        if (empty($results)) return null;

        $order = (object) [
            'id' => $results[0]->id,
            'user_id' => $results[0]->user_id,
            'user_name' => $results[0]->user_name,
            'total' => $results[0]->total,
            'status' => $results[0]->status,
            'payment_method' => $results[0]->payment_method,
            'shipping_fee' => $results[0]->shipping_fee,
            'created_at' => $results[0]->created_at,
            'cancel_reason' => $results[0]->cancel_reason,
            'items' => []
        ];

        foreach ($results as $row) {
            if ($row->product_id) {
                $order->items[] = (object) [
                    'product_id' => $row->product_id,
                    'variant_id' => $row->variant_id,
                    'name' => $row->product_name,
                    'image' => $row->product_image,
                    'seller_id' => $row->product_user_id,
                    'seller_role' => $row->seller_role,
                    'quantity' => $row->quantity,
                    'price' => $row->item_price
                ];
            }
        }
        return $order;
    }

    public function getOrdersBySellerId($sellerId) {
        $query = "SELECT DISTINCT o.*, u.name as buyer_name
                  FROM orders o
                  JOIN order_detail od ON o.id = od.order_id
                  JOIN product p ON od.product_id = p.id
                  LEFT JOIN user u ON o.user_id = u.id
                  WHERE p.user_id = :seller_id
                  ORDER BY o.created_at DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':seller_id', $sellerId);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    public function updateStatus($id, $status) {
        $query = "UPDATE orders SET status = :status WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }
}
