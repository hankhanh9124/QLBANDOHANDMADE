<?php
require_once 'app/config/database.php';

$db = (new Database())->getConnection();

$sql_conversations = "CREATE TABLE IF NOT EXISTS conversations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    customer_id INT NOT NULL,
    last_message TEXT,
    last_message_type VARCHAR(20) DEFAULT 'text',
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX (customer_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";

$sql_messages = "CREATE TABLE IF NOT EXISTS chat_messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    conversation_id INT NOT NULL,
    sender_id INT NOT NULL,
    message_type ENUM('text', 'image', 'video', 'sticker', 'product', 'order') DEFAULT 'text',
    content TEXT,
    attachment_url VARCHAR(255),
    is_read TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX (conversation_id),
    INDEX (sender_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";

try {
    $db->exec($sql_conversations);
    echo "Table 'conversations' created or already exists.\n";
    $db->exec($sql_messages);
    echo "Table 'chat_messages' created or already exists.\n";
} catch (PDOException $e) {
    echo "Error creating tables: " . $e->getMessage() . "\n";
}
?>
