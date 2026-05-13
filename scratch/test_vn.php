<?php
require_once 'app/config/database.php';
$database = new Database();
$db = $database->getConnection();
$stmt = $db->query("SELECT name FROM product LIMIT 1");
$row = $stmt->fetch(PDO::FETCH_ASSOC);
echo "Tên sản phẩm: " . $row['name'] . "\n";
