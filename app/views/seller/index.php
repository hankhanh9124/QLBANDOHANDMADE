<?php 
$action = 'index';
include 'app/views/dashboard/header.php'; 
?>

<div class="container-fluid">
    <h2 class="mb-4">Tổng quan cửa hàng</h2>
    
    <div class="row">
        <!-- Tổng Sản Phẩm -->
        <div class="col-lg-3 col-md-6 mb-4">
            <a href="<?php echo BASE_URL; ?>index.php?url=Product/myProducts" class="text-decoration-none">
                <div class="card card-stats bg-primary text-white h-100 shadow-sm" style="transition: all 0.3s ease; cursor: pointer; background-color: #0d6efd !important;">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h3 class="mb-0"><?php echo $totalProducts ?? 0; ?></h3>
                                <p class="mb-0">Sản phẩm</p>
                            </div>
                            <i class="fas fa-box fa-2x opacity-50"></i>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        <!-- Sản Phẩm Đã Bán -->
        <div class="col-lg-3 col-md-6 mb-4">
            <a href="<?php echo BASE_URL; ?>index.php?url=Seller/soldProducts" class="text-decoration-none">
                <div class="card card-stats bg-danger text-white h-100 shadow-sm" style="transition: all 0.3s ease; cursor: pointer; background-color: #dc3545 !important;">
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

        <!-- Tổng Đơn Hàng -->
        <div class="col-lg-3 col-md-6 mb-4">
            <a href="<?php echo BASE_URL; ?>index.php?url=Seller/orders" class="text-decoration-none">
                <div class="card card-stats bg-success text-white h-100 shadow-sm" style="transition: all 0.3s ease; cursor: pointer; background-color: #198754 !important;">
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

        <!-- Doanh Thu -->
        <div class="col-lg-3 col-md-6 mb-4">
            <a href="<?php echo BASE_URL; ?>index.php?url=Seller/orders" class="text-decoration-none">
                <div class="card card-stats text-white h-100 shadow-sm" style="transition: all 0.3s ease; cursor: pointer; background-color: #2c9faf !important;">
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
        <div class="col-md-6 mb-4">
            <div class="card border-0 shadow-sm h-100" style="border-radius: 12px;">
                <div class="card-header bg-white border-0 py-3" style="border-top-left-radius: 12px; border-top-right-radius: 12px;">
                    <h5 class="mb-0 font-weight-bold">Thao tác nhanh</h5>
                </div>
                <div class="card-body">
                    <div class="list-group list-group-flush">
                        <a href="<?php echo BASE_URL; ?>index.php?url=Product/add" class="list-group-item list-group-item-action border-0 px-0 d-flex align-items-center">
                            <i class="fas fa-plus-circle text-success mr-3"></i> Thêm sản phẩm mới
                        </a>
                        <a href="<?php echo BASE_URL; ?>index.php?url=Product/myProducts" class="list-group-item list-group-item-action border-0 px-0 d-flex align-items-center">
                            <i class="fas fa-box text-primary mr-3"></i> Quản lý kho hàng
                        </a>
                        <a href="<?php echo BASE_URL; ?>index.php?url=Seller/orders" class="list-group-item list-group-item-action border-0 px-0 d-flex align-items-center">
                            <i class="fas fa-shipping-fast text-warning mr-3"></i> Xử lý đơn hàng mới
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6 mb-4">
            <div class="card border-0 shadow-sm text-white h-100" style="background: linear-gradient(135deg, #c2255c 0%, #e64980 100%); border-radius: 12px; min-height: 200px;">
                <div class="card-body p-4 d-flex flex-column justify-content-between">
                    <div>
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="mb-0 font-weight-bold text-white-50 small text-uppercase">Ví tiền của tôi</h5>
                            <i class="fas fa-wallet fa-lg"></i>
                        </div>
                        <h3 class="font-weight-bold mb-1" style="font-size: 2.2rem;">
                            <?php echo number_format($walletBalance ?? 0, 0, ',', '.'); ?> ₫
                        </h3>
                        <p class="text-white-50 small mb-0"><i class="fas fa-info-circle mr-1"></i> Số dư khả dụng có thể rút về ngân hàng.</p>
                    </div>
                    <div class="mt-4">
                        <a href="<?php echo BASE_URL; ?>index.php?url=Seller/wallet" class="btn btn-light btn-block font-weight-bold" style="color: #c2255c; border-radius: 8px; transition: background-color 0.2s;">
                            <i class="fas fa-university mr-2"></i> Quản lý ví & Rút tiền
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'app/views/dashboard/footer.php'; ?>
