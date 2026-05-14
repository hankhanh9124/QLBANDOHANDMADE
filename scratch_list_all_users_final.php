<?php
require_once 'app/config/database.php';
$db = (new Database())->getConnection();
$stmt = $db->prepare("SELECT id, name, role FROM user");
$stmt->execute();
print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
