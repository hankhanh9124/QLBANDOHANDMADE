<?php
// app/views/account/cancel_order_detail.php
if (!isset($order) || !$order) {
    echo "<div class='container my-5'><div class='alert alert-danger'>Không tìm thấy thông tin đơn hàng.</div></div>";
    return;
}
?>

<div class="cancel-detail-container">
    <!-- Back Header -->
    <div class="cancel-detail-header">
        <a href="<?php echo BASE_URL; ?>index.php?url=Page/orders&status=cancelled" class="back-link">
            <i class="fas fa-chevron-left"></i> TRỞ LẠI
        </a>
        <div class="cancel-request-time">
            Yêu cầu vào: <?php echo date('H:i d-m-Y', strtotime($order->created_at)); ?>
        </div>
    </div>

    <!-- Status Banner -->
    <div class="cancel-status-banner">
        <h3 class="status-title">Đã hủy đơn hàng</h3>
        <p class="status-time">vào <?php echo date('H:i d-m-Y', strtotime($order->created_at)); ?></p> 
    </div>

    <!-- Shop Info Block -->
    <div class="cancel-shop-block">
        <div class="shop-left">
            <span class="shop-badge">Mall</span>
            <span class="shop-name"><?php echo htmlspecialchars($order->shop_name ?? 'GÌ CŨNG MÓC Official Store'); ?></span>
        </div>
        <div class="shop-right">
            <a href="<?php echo BASE_URL; ?>index.php?url=Shop/profile/<?php echo $order->shop_id ?: ($order->items[0]->seller_id ?? 0); ?>" class="btn-view-shop" style="text-decoration: none; color: #555; display: inline-block;"><i class="fas fa-store"></i> Xem Shop</a>
        </div>
    </div>

    <!-- Product List -->
    <div class="cancel-products-list">
        <?php foreach ($order->items as $item): ?>
            <div class="cancel-product-item">
                <div class="prod-img">
                    <?php 
                    $pImg = $item->image;
                    $finalPImg = (strpos($pImg, 'public/') === false) ?
                        ((strpos($pImg, 'uploads/') !== false) ? 'public/' . $pImg : 'public/uploads/' . $pImg) :
                        $pImg;
                    ?>
                    <img src="<?php echo BASE_URL . $finalPImg; ?>" alt="Product">
                </div>
                <div class="prod-info">
                    <h5 class="prod-name"><?php echo htmlspecialchars($item->name); ?></h5>
                    <div class="prod-variant">Handmade quà tặng</div>
                    <div class="prod-qty">x<?php echo $item->quantity; ?></div>
                </div>
                <div class="prod-price-group">
                    <span class="prod-old-price"><?php echo number_format($item->price * 1.2, 0, ',', '.'); ?>₫</span>
                    <span class="prod-new-price"><?php echo number_format($item->price, 0, ',', '.'); ?>₫</span>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- Meta Info Table -->
    <div class="cancel-meta-info">
        <div class="meta-row">
            <div class="meta-label">Yêu cầu bởi</div>
            <div class="meta-value">Người mua</div>
        </div>
        <div class="meta-row">
            <div class="meta-label">Phương thức thanh toán</div>
            <div class="meta-value">COD</div>
        </div>
        <div class="meta-row">
            <div class="meta-label">Mã đơn hàng</div>
            <div class="meta-value">
                <span class="order-id-highlight"><?php echo strtoupper(str_replace('-', '', substr($order->id ?? '6K3FUF6EN100426', 0, 15))); ?></span>
                <i class="far fa-copy copy-icon"></i>
            </div>
        </div>
    </div>

    <!-- Reason Section -->
    <div class="cancel-reason-section">
        <div class="reason-label">Lý do:</div>
        <div class="reason-content"><?php echo htmlspecialchars($order->cancel_reason ?? 'Tôi không có nhu cầu mua nữa'); ?></div>
    </div>
</div>
