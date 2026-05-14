<?php
require_once 'app/config/database.php';
$db = (new Database())->getConnection();
$stmt = $db->prepare("SELECT p.id, p.name, p.user_id, u.name as user_name, p.created_at FROM product p LEFT JOIN user u ON p.user_id = u.id ORDER BY p.id DESC LIMIT 5");
$stmt->execute();
print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
