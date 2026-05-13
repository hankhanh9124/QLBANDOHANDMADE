<?php
require_once 'app/config/database.php';
$db = (new Database())->getConnection();
$stmt = $db->query("SELECT * FROM banners");
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);
print_r($results);
