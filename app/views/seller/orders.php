<?php
$action = 'orders';
include 'app/views/dashboard/header.php';
?>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Quản lý đơn hàng (Shop)</h2>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <table class="table table-hover mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="border-0 px-4">ID</th>
                        <th class="border-0">Khách hàng</th>
                        <th class="border-0">Tổng tiền</th>
                        <th class="border-0">Ngày đặt</th>
                        <th class="border-0">Trạng thái</th>
                        <th class="border-0 px-4 text-right">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($orders)): ?>
                        <?php foreach ($orders as $order): ?>
                            <tr>
                                <td class="px-4">#<?php echo $order->id; ?></td>
                                <td><?php echo htmlspecialchars($order->buyer_name ?? 'Khách vãng lai'); ?></td>
                                <td class="font-weight-bold text-danger"><?php echo number_format($order->total, 0, ',', '.'); ?> ₫</td>
                                <td><?php echo date('d/m/Y H:i', strtotime($order->created_at)); ?></td>
                                <td>
                                    <?php
                                    $statusClass = 'badge-secondary';
                                    $statusText = 'Chờ thanh toán';
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
                                            $statusText = 'Hủy đơn';
                                            break;
                                    }
                                    ?>
                                    <span class="badge <?php echo $statusClass; ?> px-3 py-2"><?php echo $statusText; ?></span>
                                </td>
                                <td class="px-4 text-right">
                                    <div class="d-flex justify-content-end align-items-center">
                                        <a href="<?php echo BASE_URL; ?>index.php?url=Seller/orderDetail/<?php echo $order->id; ?>" class="btn btn-sm btn-info mr-2" style="background-color: #17a2b8; border: none; padding: 5px 10px;" title="Xem chi tiết">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <button class="btn btn-sm btn-success mr-2 open-chat-with-customer" data-customer-id="<?php echo $order->user_id; ?>" style="background-color: #28a745; border: none; padding: 5px 10px;" title="Chat với khách hàng">
                                            <i class="fas fa-comment"></i>
                                        </button>
                                         <?php if ($order->status !== 'completed' && $order->status !== 'cancelled'): ?>
                                            <div class="btn-group">
                                                <button type="button" class="btn btn-sm btn-dark dropdown-toggle" data-toggle="dropdown" style="background-color: #495057; border: none; padding: 5px 15px;">
                                                    Cập nhật
                                                </button>
                                                <div class="dropdown-menu dropdown-menu-right">
                                                    <a class="dropdown-item" href="<?php echo BASE_URL; ?>index.php?url=Seller/updateOrderStatus/<?php echo $order->id; ?>/confirmed">Đang xử lý</a>
                                                    <a class="dropdown-item" href="<?php echo BASE_URL; ?>index.php?url=Seller/updateOrderStatus/<?php echo $order->id; ?>/shipping">Đang giao</a>
                                                    <a class="dropdown-item" href="<?php echo BASE_URL; ?>index.php?url=Seller/updateOrderStatus/<?php echo $order->id; ?>/completed">Đã giao</a>
                                                    <div class="dropdown-divider"></div>
                                                    <a class="dropdown-item text-danger" href="<?php echo BASE_URL; ?>index.php?url=Seller/updateOrderStatus/<?php echo $order->id; ?>/cancelled" onclick="return confirm('Hủy đơn hàng này?')">Hủy đơn</a>
                                                </div>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                <i class="fas fa-shopping-bag fa-3x mb-3 opacity-25"></i>
                                <p>Chưa có đơn hàng nào cho sản phẩm của bạn.</p>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include 'app/views/dashboard/footer.php'; ?>
