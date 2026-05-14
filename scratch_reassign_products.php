<?php
require_once 'app/config/database.php';
$db = (new Database())->getConnection();
// Update all products to belong to user 5 (Nguyễn Lan Phương)
$stmt = $db->prepare("UPDATE product SET user_id = 5");
if ($stmt->execute()) {
    echo "Successfully updated " . $stmt->rowCount() . " products to user 5.\n";
} else {
    echo "Error updating products.\n";
}
