<?php
$action = 'orders';
include 'app/views/dashboard/header.php';
?>

<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div class="d-flex align-items-center">
            <a href="<?php echo BASE_URL; ?>index.php?url=Dashboard/orders" class="btn btn-outline-secondary mr-3 rounded-circle" style="width: 40px; height: 40px; padding: 7px 0;">
                <i class="fas fa-arrow-left"></i>
            </a>
            <h2 class="mb-0">Chi tiết đơn hàng #<?php echo $order->id; ?></h2>
        </div>
        <div>
            <span class="text-muted mr-2">Ngày đặt: <?php echo date('d/m/Y H:i', strtotime($order->created_at)); ?></span>
            <?php
            $statusClass = 'badge-secondary';
            $statusText = 'Chờ xử lý';
            switch ($order->status) {
                case 'confirmed':
                    $statusClass = 'badge-info';
                    $statusText = 'Đang xử lý';
                    break;
                case 'shipping':
                    $statusClass = 'badge-primary';
                    $statusText = 'Đang giao';
                    break;
                case 'completed':
                    $statusClass = 'badge-success';
                    $statusText = 'Đã giao';
                    break;
                case 'cancelled':
                    $statusClass = 'badge-danger';
                    $statusText = 'Đã hủy';
                    break;
            }
            ?>
            <span class="badge <?php echo $statusClass; ?> px-3 py-2"><?php echo $statusText; ?></span>
        </div>
    </div>

    <div class="row">
        <!-- Order Items -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0">Sản phẩm trong đơn hàng</h5>
                </div>
                <div class="card-body p-0">
                    <table class="table mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="border-0 px-4">Ảnh</th>
                                <th class="border-0">Sản phẩm</th>
                                <th class="border-0">Giá</th>
                                <th class="border-0">Số lượng</th>
                                <th class="border-0 text-right px-4">Thành tiền</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($items as $item): ?>
                                <tr>
                                    <td class="px-4">
                                        <img src="<?php echo BASE_URL; ?>public/uploads/<?php echo $item->image; ?>" style="width: 50px; height: 50px; object-fit: cover; border-radius: 4px;" alt="p">
                                    </td>
                                    <td>
                                        <div class="font-weight-bold"><?php echo htmlspecialchars($item->product_name); ?></div>
                                    </td>
                                    <td><?php echo number_format($item->price, 0, ',', '.'); ?> ₫</td>
                                    <td>x<?php echo $item->quantity; ?></td>
                                    <td class="text-right px-4 font-weight-bold">
                                        <?php echo number_format($item->price * $item->quantity, 0, ',', '.'); ?> ₫
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot class="bg-light">
                            <tr>
                                <td colspan="4" class="text-right font-weight-bold px-4 py-3">TỔNG CỘNG:</td>
                                <td class="text-right px-4 py-3 font-weight-bold text-danger h5 mb-0">
                                    <?php echo number_format($order->total, 0, ',', '.'); ?> ₫
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        <!-- Customer Info -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0">Thông tin khách hàng</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="bg-info text-white rounded-circle d-flex align-items-center justify-content-center mr-3" style="width: 45px; height: 45px;">
                            <i class="fas fa-user fa-lg"></i>
                        </div>
                        <div>
                            <div class="font-weight-bold"><?php echo htmlspecialchars($order->display_name ?? 'Khách vãng lai'); ?></div>
                            <div class="small text-muted">ID: #<?php echo $order->user_id; ?></div>
                        </div>
                    </div>
                    <hr>
                    <div class="mb-3">
                        <div class="small text-muted mb-1 text-uppercase font-weight-bold">Email</div>
                        <div><?php echo htmlspecialchars($order->email ?? 'N/A'); ?></div>
                    </div>
                    <div class="mb-3">
                        <div class="small text-muted mb-1 text-uppercase font-weight-bold">Số điện thoại</div>
                        <div><?php echo htmlspecialchars($order->display_phone ?? 'N/A'); ?></div>
                    </div>
                    <div class="mb-0">
                        <div class="small text-muted mb-1 text-uppercase font-weight-bold">Địa chỉ nhận hàng</div>
                        <div><?php echo htmlspecialchars($order->display_address ?? 'Chưa cung cấp'); ?></div>
                    </div>
                </div>
            </div>

            <!-- Action Summary -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0">Thao tác nhanh</h5>
                </div>
                <div class="card-body">
                    <div class="btn-group w-100">
                        <?php if ($order->status === 'cancelled'): ?>
                            <button type="button" class="btn btn-danger btn-block disabled" disabled>
                                Đơn hàng đã hủy
                            </button>
                        <?php elseif ($order->status === 'completed'): ?>
                            <button type="button" class="btn btn-success btn-block disabled" disabled>
                                Đơn hàng đã hoàn thành
                            </button>
                        <?php else: ?>
                            <button type="button" class="btn btn-secondary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                Cập nhật trạng thái
                            </button>
                            <div class="dropdown-menu dropdown-menu-right w-100">
                                <a class="dropdown-item" href="<?php echo BASE_URL; ?>index.php?url=Dashboard/updateOrderStatus/<?php echo $order->id; ?>/confirmed">Đang xử lý</a>
                                <a class="dropdown-item" href="<?php echo BASE_URL; ?>index.php?url=Dashboard/updateOrderStatus/<?php echo $order->id; ?>/shipping">Đang giao</a>
                                <a class="dropdown-item" href="<?php echo BASE_URL; ?>index.php?url=Dashboard/updateOrderStatus/<?php echo $order->id; ?>/completed">Đã giao</a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item text-danger" href="<?php echo BASE_URL; ?>index.php?url=Dashboard/updateOrderStatus/<?php echo $order->id; ?>/cancelled" onclick="return confirm('Xác nhận hủy đơn hàng này?')">Hủy đơn</a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'app/views/dashboard/footer.php'; ?>