<?php
require_once 'app/config/database.php';
$db = (new Database())->getConnection();
$stmt = $db->prepare("SELECT * FROM product WHERE name LIKE '%Phương%' OR description LIKE '%Phương%'");
$stmt->execute();
print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
