<?php
try {
    $db = new PDO("mysql:host=127.0.0.1;dbname=handmade_shop", "root", "");
    echo "Raw PDO connection succeeded!\n";
    $stmt = $db->prepare("SELECT COUNT(*) as total FROM product WHERE user_id = 1");
    $stmt->execute();
    var_dump($stmt->fetch(PDO::FETCH_OBJ));
} catch (PDOException $e) {
    echo "Raw PDO Exception: " . $e->getMessage() . "\n";
}
