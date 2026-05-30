<!-- app/views/dashboard/header.php -->
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - GÌ CŨNG MÓC</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css">
    <style>
        :root {
            --sidebar-width: 270px;
            --primary-color: #c2255c;
            --secondary-color: #75c794;
        }
        body {
            background-color: #f4f7f6;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            font-size: 16px;
            overflow-x: hidden;
            width: 100%;
        }
        #sidebar {
            width: var(--sidebar-width);
            height: 100vh;
            position: fixed;
            left: 0;
            top: 0;
            background: #2c3e50;
            color: #fff;
            transition: all 0.3s;
            z-index: 1000;
        }
        #sidebar .sidebar-header {
            padding: 20px;
            background: #1a252f;
            text-align: center;
        }
        #sidebar .sidebar-header h3 {
            font-size: 24px;
        }
        #sidebar ul.components {
            padding: 20px 0;
        }
        #sidebar ul li {
            padding: 12px 20px;
            font-size: 16px;
            display: block;
        }
        #sidebar ul li a {
            color: #bdc3c7;
            text-decoration: none;
            display: block;
            transition: 0.3s;
        }
        #sidebar ul li a:hover {
            color: #fff;
            background: #34495e;
            border-radius: 4px;
        }
        #sidebar ul li.active > a {
            color: #fff;
            background: var(--primary-color);
            border-radius: 4px;
        }
        #content {
            width: calc(100% - var(--sidebar-width));
            padding: 25px;
            min-height: 100vh;
            transition: all 0.3s;
            margin-left: var(--sidebar-width);
            max-width: 100%;
        }
        .card-stats {
            border-radius: 10px;
            border: none;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
            transition: transform 0.3s;
        }
        .card-stats h3 {
            font-size: 28px;
        }
        .card-stats p {
            font-size: 16px;
        }
        .card-stats:hover {
            transform: translateY(-5px);
        }

        @media (max-width: 991.98px) {
            #sidebar {
                margin-left: calc(-1 * var(--sidebar-width));
            }
            #sidebar.active {
                margin-left: 0;
            }
            #content {
                width: 100%;
                margin-left: 0;
            }
        }

        /* Toast Notification Styles */
        #return-notification-toast {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 9999;
            min-width: 300px;
            display: none;
            animation: slideInRight 0.5s ease-out;
        }
        @keyframes slideInRight {
            from { transform: translateX(100%); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }
        .toast-new-return {
            background: #fff;
            border-left: 5px solid var(--primary-color);
            box-shadow: 0 10px 25px rgba(0,0,0,0.15);
            padding: 20px;
            border-radius: 8px;
            display: flex;
            align-items: center;
        }
        .toast-icon {
            font-size: 24px;
            color: var(--primary-color);
            margin-right: 15px;
        }
        .toast-content h6 {
            margin: 0 0 5px 0;
            font-weight: 700;
            color: #333;
        }
        .toast-content p {
            margin: 0;
            font-size: 14px;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <!-- Toast Notification for New Returns -->
        <div id="return-notification-toast">
            <div class="toast-new-return">
                <div class="toast-icon"><i class="fas fa-bell animate__animated animate__swing animate__infinite"></i></div>
                <div class="toast-content">
                    <h6>Yêu cầu mới!</h6>
                    <p>Có <span id="new-return-count">0</span> yêu cầu trả hàng mới cần xử lý.</p>
                    <a href="<?php echo BASE_URL; ?>index.php?url=Dashboard/returns" class="btn btn-sm btn-link p-0 mt-2 font-weight-bold" style="color: var(--primary-color)">Xem ngay <i class="fas fa-arrow-right ml-1"></i></a>
                </div>
                <button type="button" class="close ml-3" onclick="document.getElementById('return-notification-toast').style.display='none'">
                    <span>&times;</span>
                </button>
            </div>
        </div>

        <!-- Sidebar -->
        <nav id="sidebar">
            <div class="sidebar-header">
                <h3><?php echo (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin') ? 'Admin Panel' : 'Seller Panel'; ?></h3>
                <p style="font-size: 0.8em; color: var(--secondary-color);">GÌ CŨNG MÓC</p>
            </div>

            <ul class="list-unstyled components">
                <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin'): ?>
                    <!-- Admin Menu -->
                    <li class="<?php echo $action == 'index' ? 'active' : ''; ?>">
                        <a href="<?php echo BASE_URL; ?>index.php?url=Dashboard"><i class="fas fa-home mr-2"></i> Tổng quan</a>
                    </li>
                    <li class="<?php echo ($action == 'products' && (!isset($_GET['sort']) || $_GET['sort'] != 'sold_only')) ? 'active' : ''; ?>">
                        <a href="<?php echo BASE_URL; ?>index.php?url=Dashboard/products"><i class="fas fa-box mr-2"></i> Quản lý sản phẩm</a>
                    </li>
                    <li class="<?php echo $action == 'pending_products' ? 'active' : ''; ?>">
                        <a href="<?php echo BASE_URL; ?>index.php?url=Dashboard/pendingProducts"><i class="fas fa-hourglass-half mr-2"></i> Duyệt sản phẩm</a>
                    </li>
                    <li class="<?php echo (isset($_GET['sort']) && $_GET['sort'] == 'sold_only') ? 'active' : ''; ?>">
                        <a href="<?php echo BASE_URL; ?>index.php?url=Dashboard/products&sort=sold_only"><i class="fas fa-check-circle mr-2"></i> Sản phẩm đã bán</a>
                    </li>
                    <li class="<?php echo $action == 'categories' ? 'active' : ''; ?>">
                        <a href="<?php echo BASE_URL; ?>index.php?url=Dashboard/categories"><i class="fas fa-tags mr-2"></i> Quản lý danh mục</a>
                    </li>
                    <li class="<?php echo $action == 'banners' ? 'active' : ''; ?>">
                        <a href="<?php echo BASE_URL; ?>index.php?url=Dashboard/banners"><i class="fas fa-image mr-2"></i> Quản lý Banner</a>
                    </li>
                    <li class="<?php echo $action == 'orders' ? 'active' : ''; ?>">
                        <a href="<?php echo BASE_URL; ?>index.php?url=Dashboard/orders"><i class="fas fa-shopping-bag mr-2"></i> Quản lý đơn hàng</a>
                    </li>
                    <li class="<?php echo $action == 'users' ? 'active' : ''; ?>">
                        <a href="<?php echo BASE_URL; ?>index.php?url=Dashboard/users"><i class="fas fa-users mr-2"></i> Quản lý người dùng</a>
                    </li>
                    <li class="<?php echo $action == 'manage_sellers' ? 'active' : ''; ?>">
                        <a href="<?php echo BASE_URL; ?>index.php?url=Dashboard/manageSellers" class="d-flex align-items-center">
                            <span><i class="fas fa-user-check mr-2"></i> Duyệt người bán</span>
                            <?php if (isset($this->pendingSellerRequestsCount) && $this->pendingSellerRequestsCount > 0): ?>
                                <span class="badge badge-danger ml-auto"><?php echo $this->pendingSellerRequestsCount; ?></span>
                            <?php endif; ?>
                        </a>
                    </li>
                    <li class="<?php echo $action == 'shop_updates' ? 'active' : ''; ?>">
                        <a href="<?php echo BASE_URL; ?>index.php?url=Dashboard/shopUpdates"><i class="fas fa-store-slash mr-2"></i> Duyệt thông tin Shop</a>
                    </li>
                    <li class="<?php echo $action == 'manage_shops' ? 'active' : ''; ?>">
                        <a href="<?php echo BASE_URL; ?>index.php?url=Dashboard/manageShops"><i class="fas fa-store mr-2"></i> Quản lý Shop</a>
                    </li>
                    <li class="<?php echo $action == 'returns' ? 'active' : ''; ?>">
                        <a href="<?php echo BASE_URL; ?>index.php?url=Dashboard/returns"><i class="fas fa-undo mr-2"></i> Trả hàng / Hoàn tiền</a>
                    </li>
                    <li class="<?php echo $action == 'admin_revenue' ? 'active' : ''; ?>">
                        <a href="<?php echo BASE_URL; ?>index.php?url=Dashboard/adminRevenue"><i class="fas fa-wallet mr-2"></i> Doanh thu hoa hồng</a>
                    </li>
                    <!-- Rút tiền tự động đã được kích hoạt, ẩn/xóa menu Yêu cầu rút tiền thủ công -->

                <?php elseif (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'seller'): ?>
                    <!-- Seller Menu -->
                    <li class="<?php echo $action == 'index' ? 'active' : ''; ?>">
                        <a href="<?php echo BASE_URL; ?>index.php?url=Seller"><i class="fas fa-chart-line mr-2"></i> Tổng quan</a>
                    </li>
                    <li class="<?php echo $action == 'settings' ? 'active' : ''; ?>">
                        <a href="<?php echo BASE_URL; ?>index.php?url=Seller/settings"><i class="fas fa-store mr-2"></i> Thông tin Shop</a>
                    </li>
                    <li class="<?php echo $action == 'add_product' ? 'active' : ''; ?>">
                        <a href="<?php echo BASE_URL; ?>index.php?url=Product/add"><i class="fas fa-plus-circle mr-2"></i> Đăng sản phẩm</a>
                    </li>
                    <li class="<?php echo $action == 'my_products' ? 'active' : ''; ?>">
                        <a href="<?php echo BASE_URL; ?>index.php?url=Product/myProducts"><i class="fas fa-box mr-2"></i> Sản phẩm của tôi</a>
                    </li>
                    <li class="<?php echo $action == 'pending_products' ? 'active' : ''; ?>">
                        <a href="<?php echo BASE_URL; ?>index.php?url=Seller/pendingProducts"><i class="fas fa-hourglass-half mr-2"></i> Sản phẩm chờ duyệt</a>
                    </li>
                    <li class="<?php echo $action == 'sold_products' ? 'active' : ''; ?>">
                        <a href="<?php echo BASE_URL; ?>index.php?url=Seller/soldProducts"><i class="fas fa-check-circle mr-2"></i> Sản phẩm đã bán</a>
                    </li>
                    <li class="<?php echo $action == 'categories' ? 'active' : ''; ?>">
                        <a href="<?php echo BASE_URL; ?>index.php?url=Seller/categories"><i class="fas fa-tags mr-2"></i> Quản lý danh mục</a>
                    </li>
                    <li class="<?php echo $action == 'orders' ? 'active' : ''; ?>">
                        <a href="<?php echo BASE_URL; ?>index.php?url=Seller/orders"><i class="fas fa-shopping-bag mr-2"></i> Quản lý đơn hàng</a>
                    </li>
                    <li class="<?php echo $action == 'seller_wallet' ? 'active' : ''; ?>">
                        <a href="<?php echo BASE_URL; ?>index.php?url=Seller/wallet"><i class="fas fa-wallet mr-2"></i> Ví của tôi</a>
                    </li>

                <?php endif; ?>

                <li>
                    <a href="<?php echo BASE_URL; ?>index.php?url=Product"><i class="fas fa-arrow-left mr-2"></i> Quay lại Shop</a>
                </li>
            </ul>
        </nav>

        <!-- Page Content -->
        <div id="content">
            <nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom mb-4">
                <div class="container-fluid">
                    <span class="navbar-brand mb-0 h1"><?php echo (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin') ? 'Admin Panel' : 'Seller Panel'; ?></span>
                    <div class="ml-auto">
                        <span class="text-muted small mr-3">Xin chào, <?php echo htmlspecialchars($_SESSION['user_name'] ?? 'Người dùng'); ?></span>
                    </div>
                </div>
            </nav>
