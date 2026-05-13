<?php
require_once 'app/config/database.php';
$db = (new Database())->getConnection();
$stmt = $db->query("DESCRIBE banners");
while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    echo $row['Field'] . " - " . $row['Type'] . "\n";
}
