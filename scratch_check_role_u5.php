<?php
require_once 'app/config/database.php';
$db = (new Database())->getConnection();
$stmt = $db->prepare("SELECT role FROM user WHERE id = 5");
$stmt->execute();
echo $stmt->fetchColumn();
