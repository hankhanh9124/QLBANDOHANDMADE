<?php
require_once 'app/config/database.php';
$db = (new Database())->getConnection();
$stmt = $db->prepare("SELECT p.id, p.name as product_name, u.name as user_name, s.name as shop_name 
                      FROM product p 
                      LEFT JOIN user u ON p.user_id = u.id 
                      LEFT JOIN shops s ON p.user_id = s.seller_id 
                      WHERE p.name LIKE '%Hoa hồng đơn%'");
$stmt->execute();
print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
