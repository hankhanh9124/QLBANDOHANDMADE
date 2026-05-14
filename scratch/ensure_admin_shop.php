<?php
require_once 'app/config/database.php';
$db = (new Database())->getConnection();

// Check if admin (role = 'admin') has a shop
$sql = "SELECT u.id, u.name, s.id as shop_id FROM user u LEFT JOIN shops s ON u.id = s.seller_id WHERE u.role = 'admin'";
$stmt = $db->prepare($sql);
$stmt->execute();
$admins = $stmt->fetchAll(PDO::FETCH_OBJ);

foreach ($admins as $admin) {
    if (!$admin->shop_id) {
        echo "Admin '{$admin->name}' (ID: {$admin->id}) has no shop. Creating one...\n";
        $insert = "INSERT INTO shops (seller_id, name, description, status) VALUES (:seller_id, :name, :description, 'active')";
        $iStmt = $db->prepare($insert);
        $shopName = $admin->name . " Shop";
        $desc = "Cửa hàng chính thức của ban quản trị.";
        $iStmt->bindParam(':seller_id', $admin->id);
        $iStmt->bindParam(':name', $shopName);
        $iStmt->bindParam(':description', $desc);
        $iStmt->execute();
        echo "Created shop for Admin ID {$admin->id}.\n";
    } else {
        echo "Admin '{$admin->name}' already has shop ID: {$admin->shop_id}.\n";
    }
}
