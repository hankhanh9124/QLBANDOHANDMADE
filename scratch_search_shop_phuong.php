<?php
require_once 'app/config/database.php';
$db = (new Database())->getConnection();
$stmt = $db->prepare("SELECT s.id, s.name as shop_name, u.name as seller_name FROM shops s JOIN user u ON s.seller_id = u.id WHERE u.name LIKE '%Phương%'");
$stmt->execute();
print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
