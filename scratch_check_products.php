<?php
require_once 'app/config/database.php';
$db = (new Database())->getConnection();
$stmt = $db->prepare("SELECT id, name FROM product WHERE user_id = 5");
$stmt->execute();
print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
