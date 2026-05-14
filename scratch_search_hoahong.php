<?php
require_once 'app/config/database.php';
$db = (new Database())->getConnection();
$stmt = $db->prepare("SELECT p.id, p.name as product_name, u.name as user_name, p.status FROM product p JOIN user u ON p.user_id = u.id WHERE p.name LIKE '%Hoa hồng%'");
$stmt->execute();
print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
