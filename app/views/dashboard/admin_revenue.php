<?php
// app/views/dashboard/admin_revenue.php
include 'app/views/dashboard/header.php';
?>
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="h3 font-weight-bold text-dark mb-0">Doanh thu hoa hồng hệ thống</h2>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb bg-transparent mb-0 p-0">
                <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>index.php?url=Dashboard" class="text-muted">Admin</a></li>
                <li class="breadcrumb-item active text-primary" aria-current="page">Doanh thu hoa hồng</li>
            </ol>
        </nav>
    </div>

    <!-- Stats Grid -->
    <div class="row mb-4">
        <!-- Total Platform Commission (10%) -->
        <div class="col-lg-6 col-md-6 mb-4">
            <div class="card border-0 text-white shadow" style="background: linear-gradient(135deg, #c2255c 0%, #e64980 100%); border-radius: 16px;">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-white-50 small text-uppercase font-weight-bold mb-1">Tổng hoa hồng thu được (10%)</p>
                            <h3 class="font-weight-bold mb-0" style="font-size: 2.2rem;">
                                <?php echo number_format($totals->total_revenue ?? 0, 0, ',', '.'); ?> <span style="font-size: 1.2rem;">₫</span>
                            </h3>
                        </div>
                        <div class="bg-white-20 p-3 rounded-circle d-flex align-items-center justify-content-center" style="width: 60px; height: 60px; background: rgba(255, 255, 255, 0.2);">
                            <i class="fas fa-percent fa-lg text-white"></i>
                        </div>
                    </div>
                    <p class="text-white-50 small mt-4 mb-0"><i class="fas fa-check-circle mr-1"></i> Số dư thực tế được giữ lại trên nền tảng làm phí quản lý.</p>
                </div>
            </div>
        </div>

        <!-- Total Gross volume -->
        <div class="col-lg-6 col-md-6 mb-4">
            <div class="card border-0 text-white shadow" style="background: linear-gradient(135deg, #12b886 0%, #20c997 100%); border-radius: 16px;">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-white-50 small text-uppercase font-weight-bold mb-1">Tổng giá trị giao dịch đối soát</p>
                            <h3 class="font-weight-bold mb-0" style="font-size: 2.2rem;">
                                <?php echo number_format($totals->total_gross ?? 0, 0, ',', '.'); ?> <span style="font-size: 1.2rem;">₫</span>
                            </h3>
                        </div>
                        <div class="bg-white-20 p-3 rounded-circle d-flex align-items-center justify-content-center" style="width: 60px; height: 60px; background: rgba(255, 255, 255, 0.2);">
                            <i class="fas fa-shopping-bag fa-lg text-white"></i>
                        </div>
                    </div>
                    <p class="text-white-50 small mt-4 mb-0"><i class="fas fa-calculator mr-1"></i> Tổng số tiền đơn hàng (90% đã được phân bổ cho các Seller tương ứng).</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Revenue Ledgers Card -->
    <div class="card border-0 shadow-sm mb-4" style="border-radius: 16px;">
        <div class="card-header bg-white border-0 py-3 px-4 d-flex justify-content-between align-items-center" style="border-top-left-radius: 16px; border-top-right-radius: 16px;">
            <h5 class="mb-0 font-weight-bold text-dark"><i class="fas fa-receipt text-primary mr-2"></i> Lịch sử đối soát hoa hồng</h5>
        </div>
        <div class="card-body p-0">
            <?php if (empty($revenues)): ?>
                <div class="text-center py-5">
                    <img src="https://cdn-icons-png.flaticon.com/512/11329/11329061.png" alt="Empty" style="width: 120px; opacity: 0.7;">
                    <p class="text-muted mt-3">Chưa có giao dịch hoa hồng nào được ghi nhận trên hệ thống.</p>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light text-uppercase small text-muted font-weight-bold border-0">
                            <tr>
                                <th class="border-0 px-4">Mã GD đối soát</th>
                                <th class="border-0">Thời gian</th>
                                <th class="border-0">Đơn hàng</th>
                                <th class="border-0">Người bán</th>
                                <th class="border-0">Giá trị đơn hàng</th>
                                <th class="border-0 text-right">Phí dịch vụ (10%)</th>
                                <th class="border-0 text-right px-4">Seller nhận (90%)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($revenues as $rev): ?>
                                <tr style="transition: background-color 0.2s;">
                                    <td class="px-4 font-weight-bold text-dark align-middle">
                                        <code><?php echo $rev->transaction_code; ?></code>
                                    </td>
                                    <td class="align-middle text-secondary small">
                                        <?php echo date('H:i d/m/Y', strtotime($rev->created_at)); ?>
                                    </td>
                                    <td class="align-middle text-dark font-weight-bold">
                                        #<?php echo $rev->order_id; ?>
                                    </td>
                                    <td class="align-middle text-secondary small font-weight-bold">
                                        <?php echo htmlspecialchars($rev->seller_name ?? 'Không rõ'); ?>
                                    </td>
                                    <td class="align-middle text-secondary font-weight-bold">
                                        <?php echo number_format($rev->gross_amount, 0, ',', '.'); ?> ₫
                                    </td>
                                    <td class="align-middle text-right font-weight-bold text-primary" style="font-size: 1.05rem;">
                                        +<?php echo number_format($rev->admin_fee, 0, ',', '.'); ?> ₫
                                    </td>
                                    <td class="align-middle text-right font-weight-bold text-success px-4" style="font-size: 1.05rem;">
                                        <?php echo number_format($rev->seller_receive, 0, ',', '.'); ?> ₫
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<style>
    .bg-white-20 {
        background-color: rgba(255, 255, 255, 0.15) !important;
    }
    .table th {
        font-weight: 700;
        border-top: none !important;
    }
    .table td {
        border-top: 1px solid #f1f3f5 !important;
    }
</style>

<?php
include 'app/views/dashboard/footer.php';
?>
