<?php
require_once 'app/config/database.php';
$db = (new Database())->getConnection();
$stmt = $db->prepare("SELECT * FROM user WHERE id = 1");
$stmt->execute();
print_r($stmt->fetch(PDO::FETCH_ASSOC));
