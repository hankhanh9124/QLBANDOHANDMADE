<?php
require_once 'app/config/database.php';
$db = (new Database())->getConnection();
try {
    $db->exec("ALTER TABLE banners ADD COLUMN qr_image VARCHAR(255) DEFAULT NULL");
    echo "Successfully added qr_image column to banners table.\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
