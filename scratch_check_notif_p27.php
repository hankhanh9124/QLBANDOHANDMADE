<?php
require_once 'app/config/database.php';
$db = (new Database())->getConnection();
$stmt = $db->prepare("SELECT * FROM notifications WHERE message LIKE '%Hoa hồng đơn%' OR link LIKE '%show/27%'");
$stmt->execute();
print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
