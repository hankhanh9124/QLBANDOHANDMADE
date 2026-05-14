<?php
require_once 'app/config/database.php';
$db = (new Database())->getConnection();
$stmt = $db->prepare("SELECT id, user_id FROM product WHERE id = 27");
$stmt->execute();
print_r($stmt->fetch(PDO::FETCH_ASSOC));
