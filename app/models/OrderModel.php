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

        // Auto-migrate: add commission columns to orders and order_detail
        try {
            $this->conn->exec("ALTER TABLE orders ADD COLUMN commission_settled TINYINT(1) NOT NULL DEFAULT 0 AFTER shipping_fee");
        } catch (PDOException $e) { /* ignore if already exists */ }

        try {
            $this->conn->exec("ALTER TABLE order_detail ADD COLUMN commission_percent DECIMAL(5,2) NOT NULL DEFAULT 10.00 AFTER price");
            $this->conn->exec("ALTER TABLE order_detail ADD COLUMN admin_fee DECIMAL(12,2) NOT NULL DEFAULT 0.00 AFTER commission_percent");
            $this->conn->exec("ALTER TABLE order_detail ADD COLUMN seller_receive DECIMAL(12,2) NOT NULL DEFAULT 0.00 AFTER admin_fee");
            $this->conn->exec("ALTER TABLE order_detail ADD COLUMN commission_settled TINYINT(1) NOT NULL DEFAULT 0 AFTER seller_receive");
        } catch (PDOException $e) { /* ignore if already exists */ }

        // Auto-migrate: create seller_wallets table
        try {
            $this->conn->exec("CREATE TABLE IF NOT EXISTS `seller_wallets` (
                `id`               INT NOT NULL AUTO_INCREMENT,
                `seller_id`        INT NOT NULL,
                `balance`          DECIMAL(15,2) NOT NULL DEFAULT 0.00,
                `total_earned`     DECIMAL(15,2) NOT NULL DEFAULT 0.00,
                `total_withdrawn`  DECIMAL(15,2) NOT NULL DEFAULT 0.00,
                `created_at`       DATETIME DEFAULT CURRENT_TIMESTAMP,
                `updated_at`       DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                PRIMARY KEY (`id`),
                UNIQUE KEY `uk_seller_wallet` (`seller_id`),
                CONSTRAINT `fk_wallet_seller` FOREIGN KEY (`seller_id`) REFERENCES `user` (`id`) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
        } catch (PDOException $e) { /* ignore */ }

        // Auto-migrate: create wallet_transactions table
        try {
            $this->conn->exec("CREATE TABLE IF NOT EXISTS `wallet_transactions` (
                `id`                 INT NOT NULL AUTO_INCREMENT,
                `transaction_code`   VARCHAR(50) NOT NULL,
                `wallet_id`          INT NOT NULL,
                `seller_id`          INT NOT NULL,
                `order_id`           INT DEFAULT NULL,
                `order_detail_id`    INT DEFAULT NULL,
                `type`               ENUM('commission','withdrawal','refund','adjustment') NOT NULL,
                `gross_amount`       DECIMAL(12,2) NOT NULL DEFAULT 0.00,
                `commission_percent` DECIMAL(5,2) NOT NULL DEFAULT 10.00,
                `admin_fee`          DECIMAL(12,2) NOT NULL DEFAULT 0.00,
                `amount`             DECIMAL(12,2) NOT NULL,
                `balance_before`     DECIMAL(12,2) NOT NULL DEFAULT 0.00,
                `balance_after`      DECIMAL(12,2) NOT NULL DEFAULT 0.00,
                `note`               TEXT DEFAULT NULL,
                `status`             ENUM('pending','completed','failed','refunded') NOT NULL DEFAULT 'completed',
                `created_at`         DATETIME DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY (`id`),
                UNIQUE KEY `uk_txn_code` (`transaction_code`),
                CONSTRAINT `fk_txn_wallet`  FOREIGN KEY (`wallet_id`)  REFERENCES `seller_wallets` (`id`) ON DELETE CASCADE,
                CONSTRAINT `fk_txn_seller`  FOREIGN KEY (`seller_id`)  REFERENCES `user` (`id`) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
        } catch (PDOException $e) { /* ignore */ }

        // Auto-migrate: create admin_revenue table
        try {
            $this->conn->exec("CREATE TABLE IF NOT EXISTS `admin_revenue` (
                `id`                 INT NOT NULL AUTO_INCREMENT,
                `transaction_code`   VARCHAR(50) NOT NULL,
                `order_id`           INT NOT NULL,
                `order_detail_id`    INT NOT NULL,
                `seller_id`          INT NOT NULL,
                `gross_amount`       DECIMAL(12,2) NOT NULL,
                `commission_percent` DECIMAL(5,2) NOT NULL DEFAULT 10.00,
                `admin_fee`          DECIMAL(12,2) NOT NULL,
                `seller_receive`     DECIMAL(12,2) NOT NULL,
                `status`             ENUM('pending','settled','refunded') NOT NULL DEFAULT 'settled',
                `note`               TEXT DEFAULT NULL,
                `created_at`         DATETIME DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY (`id`),
                UNIQUE KEY `uk_rev_txn_code` (`transaction_code`),
                CONSTRAINT `fk_rev_order`  FOREIGN KEY (`order_id`)  REFERENCES `orders` (`id`) ON DELETE CASCADE,
                CONSTRAINT `fk_rev_seller` FOREIGN KEY (`seller_id`) REFERENCES `user` (`id`) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
        } catch (PDOException $e) { /* ignore */ }

        // Auto-migrate: create withdrawal_requests table
        try {
            $this->conn->exec("CREATE TABLE IF NOT EXISTS `withdrawal_requests` (
                `id`           INT NOT NULL AUTO_INCREMENT,
                `request_code` VARCHAR(50) NOT NULL,
                `seller_id`    INT NOT NULL,
                `wallet_id`    INT NOT NULL,
                `amount`       DECIMAL(12,2) NOT NULL,
                `bank_name`    VARCHAR(100) DEFAULT NULL,
                `bank_account` VARCHAR(50) DEFAULT NULL,
                `bank_owner`   VARCHAR(100) DEFAULT NULL,
                `status`       ENUM('pending','approved','rejected','processing','completed') NOT NULL DEFAULT 'pending',
                `admin_note`   TEXT DEFAULT NULL,
                `processed_at` DATETIME DEFAULT NULL,
                `created_at`   DATETIME DEFAULT CURRENT_TIMESTAMP,
                `updated_at`   DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                PRIMARY KEY (`id`),
                UNIQUE KEY `uk_wdr_code` (`request_code`),
                CONSTRAINT `fk_wdr_seller` FOREIGN KEY (`seller_id`) REFERENCES `user` (`id`) ON DELETE CASCADE,
                CONSTRAINT `fk_wdr_wallet` FOREIGN KEY (`wallet_id`) REFERENCES `seller_wallets` (`id`) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
        } catch (PDOException $e) { /* ignore */ }
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
                          pv.name as variant_name, s.id as shop_id, s.name as shop_name
                  FROM orders o
                  LEFT JOIN order_detail od ON o.id = od.order_id
                  LEFT JOIN product p ON od.product_id = p.id
                  LEFT JOIN product_variants pv ON od.variant_id = pv.id
                  LEFT JOIN shops s ON p.user_id = s.seller_id
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
                    'shop_id' => $row->shop_id,
                    'shop_name' => $row->shop_name,
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
                         u.name as user_name, s.id as shop_id, s.name as shop_name
                  FROM orders o
                  LEFT JOIN order_detail od ON o.id = od.order_id
                  LEFT JOIN product p ON od.product_id = p.id
                  LEFT JOIN user u ON o.user_id = u.id
                  LEFT JOIN user u_s ON p.user_id = u_s.id
                  LEFT JOIN shops s ON p.user_id = s.seller_id
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
            'shop_id' => $results[0]->shop_id,
            'shop_name' => $results[0]->shop_name,
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

    public function processOrderCommission($orderId) {
        try {
            // Start transaction if not already in one
            $startedTransaction = false;
            if (!$this->conn->inTransaction()) {
                $this->conn->beginTransaction();
                $startedTransaction = true;
            }

            // 1. Fetch order details with seller information
            $queryItems = "SELECT od.*, p.user_id as seller_id 
                           FROM order_detail od 
                           JOIN product p ON od.product_id = p.id 
                           WHERE od.order_id = :order_id AND od.commission_settled = 0";
            $stmtItems = $this->conn->prepare($queryItems);
            $stmtItems->bindParam(':order_id', $orderId);
            $stmtItems->execute();
            $items = $stmtItems->fetchAll(PDO::FETCH_OBJ);

            if (empty($items)) {
                if ($startedTransaction) {
                    $this->conn->commit();
                }
                return true;
            }

            $commissionPercent = 10.00;

            foreach ($items as $item) {
                $itemTotal = $item->price * $item->quantity;
                $adminFee = round($itemTotal * $commissionPercent / 100, 2);
                $sellerReceive = $itemTotal - $adminFee;

                // Update order_detail
                $stmtUpdateDetail = $this->conn->prepare("UPDATE order_detail SET commission_percent = :cp, admin_fee = :af, seller_receive = :sr, commission_settled = 1 WHERE id = :id");
                $stmtUpdateDetail->execute([
                    ':cp' => $commissionPercent,
                    ':af' => $adminFee,
                    ':sr' => $sellerReceive,
                    ':id' => $item->id
                ]);

                // Ensure seller has a wallet
                $stmtWallet = $this->conn->prepare("SELECT id, balance FROM seller_wallets WHERE seller_id = :seller_id");
                $stmtWallet->bindParam(':seller_id', $item->seller_id);
                $stmtWallet->execute();
                $wallet = $stmtWallet->fetch(PDO::FETCH_OBJ);

                if (!$wallet) {
                    $stmtCreateWallet = $this->conn->prepare("INSERT INTO seller_wallets (seller_id, balance, total_earned) VALUES (:seller_id, 0, 0)");
                    $stmtCreateWallet->bindParam(':seller_id', $item->seller_id);
                    $stmtCreateWallet->execute();
                    $walletId = $this->conn->lastInsertId();
                    $balanceBefore = 0.00;
                } else {
                    $walletId = $wallet->id;
                    $balanceBefore = $wallet->balance;
                }

                $balanceAfter = $balanceBefore + $sellerReceive;

                // Update wallet balance
                $stmtUpdateWallet = $this->conn->prepare("UPDATE seller_wallets SET balance = balance + :amount, total_earned = total_earned + :amount WHERE id = :id");
                $stmtUpdateWallet->execute([
                    ':amount' => $sellerReceive,
                    ':id' => $walletId
                ]);

                // Record transaction
                $txnCode = 'TXN-' . date('Ymd') . '-' . sprintf("%05d", rand(1, 99999)) . '-' . $item->id;
                $stmtInsertTxn = $this->conn->prepare("INSERT INTO wallet_transactions 
                    (transaction_code, wallet_id, seller_id, order_id, order_detail_id, type, gross_amount, commission_percent, admin_fee, amount, balance_before, balance_after, note, status) 
                    VALUES 
                    (:code, :wallet_id, :seller_id, :order_id, :order_detail_id, 'commission', :gross, :percent, :admin_fee, :amount, :before, :after, :note, 'completed')");
                $stmtInsertTxn->execute([
                    ':code' => $txnCode,
                    ':wallet_id' => $walletId,
                    ':seller_id' => $item->seller_id,
                    ':order_id' => $orderId,
                    ':order_detail_id' => $item->id,
                    ':gross' => $itemTotal,
                    ':percent' => $commissionPercent,
                    ':admin_fee' => $adminFee,
                    ':amount' => $sellerReceive,
                    ':before' => $balanceBefore,
                    ':after' => $balanceAfter,
                    ':note' => 'Nhận tiền từ sản phẩm #' . $item->product_id . ' (đơn hàng #' . $orderId . ')'
                ]);

                // Record admin revenue
                $revCode = 'REV-' . date('Ymd') . '-' . sprintf("%05d", rand(1, 99999)) . '-' . $item->id;
                $stmtInsertRev = $this->conn->prepare("INSERT INTO admin_revenue 
                    (transaction_code, order_id, order_detail_id, seller_id, gross_amount, commission_percent, admin_fee, seller_receive, status, note) 
                    VALUES 
                    (:code, :order_id, :order_detail_id, :seller_id, :gross, :percent, :admin_fee, :seller_receive, 'settled', :note)");
                $stmtInsertRev->execute([
                    ':code' => $revCode,
                    ':order_id' => $orderId,
                    ':order_detail_id' => $item->id,
                    ':seller_id' => $item->seller_id,
                    ':gross' => $itemTotal,
                    ':percent' => $commissionPercent,
                    ':admin_fee' => $adminFee,
                    ':seller_receive' => $sellerReceive,
                    ':note' => 'Phí hoa hồng 10% từ sản phẩm #' . $item->product_id . ' (đơn hàng #' . $orderId . ')'
                ]);
            }

            // 3. Mark the whole order as commission settled
            $stmtUpdateOrder = $this->conn->prepare("UPDATE orders SET commission_settled = 1 WHERE id = :id");
            $stmtUpdateOrder->bindParam(':id', $orderId);
            $stmtUpdateOrder->execute();

            if ($startedTransaction) {
                $this->conn->commit();
            }
            return true;
        } catch (Exception $e) {
            if (isset($startedTransaction) && $startedTransaction) {
                $this->conn->rollBack();
            }
            error_log("Error in processOrderCommission: " . $e->getMessage());
            return false;
        }
    }
}
