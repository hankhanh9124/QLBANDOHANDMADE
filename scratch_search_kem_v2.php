<?php
require_once 'app/config/database.php';
$db = (new Database())->getConnection();
$stmt = $db->prepare("SELECT p.id, p.name, p.user_id, u.name as user_name FROM product p LEFT JOIN user u ON p.user_id = u.id WHERE p.name LIKE '%Kẽm%'");
$stmt->execute();
print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
