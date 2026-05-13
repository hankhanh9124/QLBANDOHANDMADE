<?php 
$action = 'index';
include 'app/views/dashboard/header.php'; 
?>

<div class="container-fluid">
    <h2 class="mb-4">Tổng quan hệ thống</h2>
    
    <div class="row">
        <div class="col-lg col-md-4 col-sm-6 mb-4">
            <a href="<?php echo BASE_URL; ?>index.php?url=Dashboard/products" class="text-decoration-none">
                <div class="card card-stats bg-primary text-white h-100 shadow-sm" style="transition: all 0.3s ease; cursor: pointer;">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h3 class="mb-0"><?php echo isset($totalProducts) ? $totalProducts : 0; ?></h3>
                                <p class="mb-0">Sản phẩm</p>
                            </div>
                            <i class="fas fa-box fa-2x opacity-50"></i>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-lg col-md-4 col-sm-6 mb-4">
            <a href="<?php echo BASE_URL; ?>index.php?url=Dashboard/products&sort=sold_only" class="text-decoration-none">
                <div class="card card-stats bg-danger text-white h-100 shadow-sm" style="transition: all 0.3s ease; cursor: pointer;">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h3 class="mb-0"><?php echo number_format($totalSold ?? 0); ?></h3>
                                <p class="mb-0">Sản phẩm đã bán</p>
                            </div>
                            <i class="fas fa-cart-arrow-down fa-2x opacity-50"></i>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-lg col-md-4 col-sm-6 mb-4">
            <a href="<?php echo BASE_URL; ?>index.php?url=Dashboard/orders" class="text-decoration-none">
                <div class="card card-stats bg-success text-white h-100 shadow-sm" style="transition: all 0.3s ease; cursor: pointer;">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h3 class="mb-0"><?php echo $totalOrders ?? 0; ?></h3>
                                <p class="mb-0">Tổng đơn hàng</p>
                            </div>
                            <i class="fas fa-shopping-cart fa-2x opacity-50"></i>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-lg col-md-6 col-sm-6 mb-4">
            <a href="<?php echo BASE_URL; ?>index.php?url=Dashboard/orders" class="text-decoration-none">
                <div class="card card-stats bg-info text-white h-100 shadow-sm" style="transition: all 0.3s ease; cursor: pointer;">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h3 class="mb-0"><?php echo number_format($totalRevenue ?? 0, 0, ',', '.'); ?> ₫</h3>
                                <p class="mb-0">Doanh thu</p>
                            </div>
                            <i class="fas fa-dollar-sign fa-2x opacity-50"></i>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-lg col-md-6 col-sm-6 mb-4">
            <a href="<?php echo BASE_URL; ?>index.php?url=Dashboard/banners" class="text-decoration-none">
                <div class="card card-stats bg-warning text-white h-100 shadow-sm" style="transition: all 0.3s ease; cursor: pointer;">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h3 class="mb-0"><?php echo $totalBanners ?? 0; ?></h3>
                                <p class="mb-0">Banners hoạt động</p>
                            </div>
                            <i class="fas fa-image fa-2x opacity-50"></i>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-lg col-md-6 col-sm-6 mb-4">
            <a href="<?php echo BASE_URL; ?>index.php?url=Dashboard/manageSellers" class="text-decoration-none">
                <div class="card card-stats bg-dark text-white h-100 shadow-sm" style="transition: all 0.3s ease; cursor: pointer;">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h3 class="mb-0"><?php echo $pendingSellerRequests ?? 0; ?></h3>
                                <p class="mb-0">Duyệt người bán</p>
                            </div>
                            <i class="fas fa-user-check fa-2x opacity-50"></i>
                        </div>
                    </div>
                </div>
            </a>
        </div>
    </div>

    <style>
        .card-stats:hover {
            transform: translateY(-5px) scale(1.02);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1) !important;
            filter: brightness(1.1);
        }
    </style>

    <!-- Quick Actions -->
    <div class="row mt-4">
        <div class="col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="mb-0 font-weight-bold">Lối tắt quản lý</h5>
                </div>
                <div class="card-body">
                    <div class="list-group list-group-flush">
                        <a href="<?php echo BASE_URL; ?>index.php?url=Product/add" class="list-group-item list-group-item-action border-0 px-0 d-flex align-items-center">
                            <i class="fas fa-plus-circle text-success mr-3"></i> Thêm sản phẩm mới
                        </a>
                        <a href="<?php echo BASE_URL; ?>index.php?url=Dashboard/banners" class="list-group-item list-group-item-action border-0 px-0 d-flex align-items-center">
                            <i class="fas fa-images text-primary mr-3"></i> Thay đổi banner trang chủ
                        </a>
                        <a href="<?php echo BASE_URL; ?>index.php?url=Dashboard/manageSellers" class="list-group-item list-group-item-action border-0 px-0 d-flex align-items-center">
                            <i class="fas fa-user-check text-dark mr-3"></i> Duyệt người bán mới
                        </a>
                        <a href="#" class="list-group-item list-group-item-action border-0 px-0 d-flex align-items-center">
                            <i class="fas fa-user-cog text-info mr-3"></i> Cài đặt tài khoản
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'app/views/dashboard/footer.php'; ?>
