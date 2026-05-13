<?php
$action = 'sold_products';
include 'app/views/dashboard/header.php';
?>

<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">Lịch sử sản phẩm đã bán</h2>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <table class="table table-hover mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="border-0 px-4">Ảnh</th>
                        <th class="border-0">Sản phẩm</th>
                        <th class="border-0">Đơn hàng</th>
                        <th class="border-0">Khách hàng</th>
                        <th class="border-0">Số lượng</th>
                        <th class="border-0">Thành tiền</th>
                        <th class="border-0">Ngày hoàn thành</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($soldItems)): ?>
                        <?php foreach ($soldItems as $item): ?>
                            <tr>
                                <td class="px-4">
                                    <?php 
                                    $pImg = $item->image;
                                    $finalPImg = (strpos($pImg, 'public/') === false) ? 
                                        ((strpos($pImg, 'uploads/') !== false) ? 'public/' . $pImg : 'public/uploads/' . $pImg) : 
                                        $pImg;
                                    ?>
                                    <img src="<?php echo BASE_URL . $finalPImg; ?>" style="width: 50px; height: 50px; object-fit: cover; border-radius: 4px;" alt="p">
                                </td>
                                <td>
                                    <div class="font-weight-bold"><?php echo htmlspecialchars($item->product_name); ?></div>
                                </td>
                                <td>
                                    <a href="<?php echo BASE_URL; ?>index.php?url=Seller/orderDetail/<?php echo $item->order_id; ?>" class="text-info font-weight-bold">
                                        #<?php echo $item->order_id; ?>
                                    </a>
                                </td>
                                <td><?php echo htmlspecialchars($item->buyer_name ?? 'N/A'); ?></td>
                                <td class="text-center">x<?php echo $item->quantity; ?></td>
                                <td class="font-weight-bold text-danger">
                                    <?php echo number_format($item->price * $item->quantity, 0, ',', '.'); ?> ₫
                                </td>
                                <td class="text-muted small">
                                    <?php echo date('d/m/Y H:i', strtotime($item->sale_date)); ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
                                <i class="fas fa-history fa-3x mb-3 opacity-25"></i>
                                <p>Bạn chưa có đơn hàng nào được giao thành công.</p>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include 'app/views/dashboard/footer.php'; ?>
