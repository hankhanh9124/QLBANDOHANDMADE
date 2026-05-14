<?php
require_once 'app/config/database.php';
$db = (new Database())->getConnection();
$stmt = $db->prepare("SELECT p.id, p.name, p.user_id, u.name as user_name FROM product p LEFT JOIN user u ON p.user_id = u.id");
$stmt->execute();
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);
foreach ($results as $row) {
    if (strpos($row['user_name'], 'Phương') !== false) {
        print_r($row);
    }
}
echo "Total products: " . count($results) . "\n";
