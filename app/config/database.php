<?php
class Database {
private $host = "localhost";
private $db_name = "handmade_shop";
private $username = "root";
private $password = "";
public $conn;
    public function getConnection() {
        $this->conn = null;
        try {
            $this->conn = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->db_name . ";charset=utf8mb4", $this->username, $this->password);
            $this->conn->exec("set names utf8mb4");
            
            // Auto-migration for character set (utf8mb4) to support emojis and 4-byte characters
            try {
                $stmt = $this->conn->query("SELECT TABLE_COLLATION AS table_collation FROM information_schema.tables WHERE table_schema = '" . $this->db_name . "' AND table_name = 'product'");
                if ($stmt) {
                    $row = $stmt->fetch(PDO::FETCH_ASSOC);
                    $collation = (string)($row['table_collation'] ?? ($row['TABLE_COLLATION'] ?? ''));
                    if ($row && strpos($collation, 'utf8mb4') === false) {
                        $this->conn->exec("ALTER DATABASE `" . $this->db_name . "` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
                        
                        $tablesStmt = $this->conn->query("SHOW TABLES");
                        if ($tablesStmt) {
                            $tables = $tablesStmt->fetchAll(PDO::FETCH_COLUMN);
                            foreach ($tables as $table) {
                                $this->conn->exec("ALTER TABLE `" . $table . "` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
                            }
                        }
                    }
                }
            } catch(PDOException $e) {
                // Ignore migration errors if any table doesn't exist yet or permissions are restricted
            }

            // Auto-migration to add related_images column if it does not exist
            try {
                $checkColumn = $this->conn->query("SHOW COLUMNS FROM `product` LIKE 'related_images'");
                if ($checkColumn && $checkColumn->rowCount() == 0) {
                    $this->conn->exec("ALTER TABLE `product` ADD COLUMN `related_images` TEXT NULL");
                }
            } catch(PDOException $e) {
                // Ignore database migration errors
            }
        } catch(PDOException $exception) {
            error_log("Connection error: " . $exception->getMessage());
        }
        return $this->conn;
    }
}