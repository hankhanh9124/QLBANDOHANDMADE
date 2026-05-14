<?php
require_once 'app/config/database.php';
$db = (new Database())->getConnection();
$stmt = $db->prepare("SELECT * FROM shops WHERE seller_id = 3");
$stmt->execute();
print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
