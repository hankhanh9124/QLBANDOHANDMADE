<?php
// Bắt đầu session
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Cấu hình Base URL
$base_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]";
$base_url .= str_replace(basename($_SERVER['SCRIPT_NAME']), "", $_SERVER['SCRIPT_NAME']);
define('BASE_URL', $base_url);

// Include các helper và model cần thiết
require_once 'app/config/database.php';
require_once 'app/helpers/SessionHelper.php';
require_once 'app/models/ProductModel.php';

// Lấy thông tin URL để routing
$url = $_GET['url'] ?? '';
$url = rtrim($url, '/');
$url = filter_var($url, FILTER_SANITIZE_URL);
$url = explode('/', $url);

// Xác định Controller (mặc định là ProductController)
$controllerName = isset($url[0]) && $url[0] != '' ? ucfirst($url[0]) . 'Controller' : 'ProductController';

// Xác định Action (mặc định là index)
$action = isset($url[1]) && $url[1] != '' ? $url[1] : 'index';

// Kiểm tra xem controller có tồn tại không
if (!file_exists('app/controllers/' . $controllerName . '.php')) {
    die('Controller not found: ' . htmlspecialchars($controllerName));
}

require_once 'app/controllers/' . $controllerName . '.php';
$controller = new $controllerName();

// Kiểm tra xem action trong controller có tồn tại không
if (!method_exists($controller, $action)) {
    die('Action not found: ' . htmlspecialchars($action));
}

// Gọi action với các tham số còn lại trong URL
call_user_func_array([$controller, $action], array_slice($url, 2));