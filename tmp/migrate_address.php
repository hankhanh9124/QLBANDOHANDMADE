<?php
require_once 'app/config/database.php';

try {
    $database = new Database();
    $db = $database->getConnection();

    // Check if column exists first
    $check = $db->query("SHOW COLUMNS FROM addresses LIKE 'address_type'");
    if ($check->rowCount() == 0) {
        $sql = "ALTER TABLE addresses ADD COLUMN address_type VARCHAR(20) DEFAULT 'Nhà Riêng' AFTER address_line";
        $db->exec($sql);
        echo "Successfully added address_type column.\n";
    } else {
        echo "Column address_type already exists.\n";
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
