<?php
require_once 'app/config/database.php';
$db = (new Database())->getConnection();
$stmt = $db->prepare("SELECT p.id, p.name as product_name, u.name as user_name FROM product p JOIN user u ON p.user_id = u.id WHERE u.name LIKE '%Phương%'");
$stmt->execute();
print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
