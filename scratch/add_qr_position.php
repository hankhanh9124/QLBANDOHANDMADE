<?php
require_once 'app/config/database.php';
$db = (new Database())->getConnection();
try {
    $db->exec("ALTER TABLE banners ADD COLUMN qr_position VARCHAR(20) DEFAULT 'bottom-center'");
    echo "Successfully added qr_position column to banners table.\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
