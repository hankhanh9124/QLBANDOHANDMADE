<?php
require_once 'app/config/database.php';
$db = (new Database())->getConnection();
$stmt = $db->query('SHOW TABLES');
while($row = $stmt->fetch(PDO::FETCH_NUM)) {
    echo $row[0] . PHP_EOL;
}
