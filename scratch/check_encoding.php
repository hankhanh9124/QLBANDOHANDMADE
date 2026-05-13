<?php
$content = file_get_contents('product.sql');
$encoding = mb_detect_encoding($content, 'UTF-8, ISO-8859-1, Windows-1252, Windows-1258', true);
echo "Detected Encoding: " . $encoding . "\n";
// Kiểm tra vài ký tự tiếng Việt
if (strpos($content, 'Thành phố') !== false) {
    echo "Chuỗi 'Thành phố' tìm thấy (UTF-8).\n";
} else {
    echo "Chuỗi 'Thành phố' KHÔNG tìm thấy.\n";
}
