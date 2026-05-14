<?php
require_once 'app/config/database.php';
$db = (new Database())->getConnection();
$stmt = $db->prepare("SELECT id, name, username FROM user WHERE name LIKE '%Nguyễn Lan Phương%'");
$stmt->execute();
print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
