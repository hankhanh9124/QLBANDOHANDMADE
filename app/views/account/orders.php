<?php
// app/views/account/orders.php
?>
<div class="account-wrapper">
    <!-- Sidebar -->
    <?php include 'app/views/shares/account_sidebar.php'; ?>

    <!-- Main Content -->
    <main class="account-main">
        <!-- Status Tabs -->
        <?php $currentStatus = $_GET['status'] ?? 'all'; ?>
        <nav class="order-tabs">
            <a href="<?php echo BASE_URL; ?>index.php?url=Page/orders&status=all" class="order-tab <?php echo $currentStatus === 'all' ? 'active' : ''; ?>">Tất cả</a>
            <a href="<?php echo BASE_URL; ?>index.php?url=Page/orders&status=pending" class="order-tab <?php echo $currentStatus === 'pending' ? 'active' : ''; ?>">Chờ thanh toán</a>
            <a href="<?php echo BASE_URL; ?>index.php?url=Page/orders&status=confirmed" class="order-tab <?php echo $currentStatus === 'confirmed' ? 'active' : ''; ?>">Vận chuyển</a>
            <a href="<?php echo BASE_URL; ?>index.php?url=Page/orders&status=shipping" class="order-tab <?php echo $currentStatus === 'shipping' ? 'active' : ''; ?>">Chờ giao hàng</a>
            <a href="<?php echo BASE_URL; ?>index.php?url=Page/orders&status=completed" class="order-tab <?php echo $currentStatus === 'completed' ? 'active' : ''; ?>">Hoàn thành</a>
            <a href="<?php echo BASE_URL; ?>index.php?url=Page/orders&status=cancelled" class="order-tab <?php echo $currentStatus === 'cancelled' ? 'active' : ''; ?>">Đã hủy</a>
            <a href="#" class="order-tab">Trả hàng/Hoàn tiền</a>
        </nav>

        <!-- Search Order -->
        <div class="order-search-container">
            <div class="order-search-group" style="position: relative;">
                <i class="fas fa-search" style="position:absolute; left:16px; top:50%; transform:translateY(-50%); color:#aaa; font-size:1rem; pointer-events:none;"></i>
                <input type="text" id="orderSearchInput" class="order-search-input"
                    placeholder="Bạn có thể tìm kiếm theo tên Shop, ID đơn hàng hoặc Tên sản phẩm"
                    oninput="filterOrders(this.value)"
                    style="padding-left: 44px; width: 100%; border: 1.5px solid #e0e0e0; border-radius: 8px; padding-top: 12px; padding-bottom: 12px; font-size: 0.93rem; outline: none; transition: border-color 0.2s;"
                    onfocus="this.style.borderColor='#75c794'"
                    onblur="this.style.borderColor='#e0e0e0'">
                <?php if (!empty($_GET['q'])): ?>
                    <button onclick="document.getElementById('orderSearchInput').value=''; filterOrders('');" style="position:absolute; right:14px; top:50%; transform:translateY(-50%); background:none; border:none; cursor:pointer; color:#aaa; font-size:1.1rem;">
                        <i class="fas fa-times-circle"></i>
                    </button>
                <?php endif; ?>
            </div>
        </div>

        <!-- Không tìm thấy -->
        <div id="noResultBox" style="display:none; text-align:center; padding: 40px 20px; background:#fff; border-radius:12px; margin-top:16px; border: 1px dashed #ddd;">
            <i class="fas fa-search" style="font-size:2.5rem; color:#ccc; margin-bottom:12px; display:block;"></i>
            <p style="color:#999; font-size:0.95rem; margin:0;">Không tìm thấy đơn hàng nào phù hợp với từ khóa <strong id="noResultKeyword"></strong></p>
        </div>

        <!-- Orders List -->
        <?php if (empty($orders)): ?>
            <div class="order-card text-center py-5">
                <img src="https://cdni.iconscout.com/illustration/premium/thumb/empty-cart-2130356-1800917.png" alt="No orders" style="width: 200px; margin-bottom: 20px;">
                <p class="text-muted">Chưa có đơn hàng nào.</p>
            </div>
        <?php else: ?>
            <?php foreach ($orders as $order): ?>
                <div class="order-card" data-order-id="<?php echo htmlspecialchars($order->id); ?>">
                    <div class="order-card-header">
                        <div class="shop-info">
                            <span class="shop-label">Yêu thích+</span>
                            <span class="shop-name"><?php echo htmlspecialchars($order->shop_name ?? 'GÌ CŨNG MÓC Shop'); ?></span>
                            <?php 
                            $firstItem = $order->items[0] ?? null;
                            $pImg = $firstItem ? $firstItem->image : '';
                            $finalPImg = (strpos($pImg, 'public/') === false) ?
                                ((strpos($pImg, 'uploads/') !== false) ? 'public/' . $pImg : 'public/uploads/' . $pImg) :
                                $pImg;
                            ?>
                            <button class="btn btn-sm btn-danger ml-2 open-chat-with-product" 
                                    style="background-color: #ee225b; border: none; font-size: 11px;"
                                    data-id="<?php echo $firstItem->product_id ?? ''; ?>"
                                    data-seller-id="<?php echo $firstItem->seller_id ?? 0; ?>"
                                    data-name="<?php echo htmlspecialchars($firstItem->name ?? ''); ?>"
                                    data-price="<?php echo number_format($firstItem->price ?? 0, 0, ',', '.'); ?>₫"
                                    data-image="<?php echo BASE_URL . $finalPImg; ?>">
                                <i class="fas fa-comment"></i> Chat
                            </button>
                            <a href="<?php echo BASE_URL; ?>index.php?url=Shop/profile/<?php echo $order->shop_id ?: ($order->items[0]->seller_id ?? 0); ?>" class="btn btn-sm btn-outline-secondary ml-1" style="font-size: 11px;"><i class="fas fa-store"></i> Xem Shop</a>
                        </div>
                        <div class="order-status-group">
                            <?php if ($order->status == 'completed'): ?>
                                <span class="delivery-status">
                                    <i class="fas fa-truck"></i> Giao hàng thành công
                                </span>
                                <span class="order-status-text">Hoàn thành</span>
                            <?php elseif ($order->status == 'shipping'): ?>
                                <span class="delivery-status">
                                    <i class="fas fa-truck"></i> Đang giao hàng
                                </span>
                                <span class="order-status-text" style="color: #ee225b;">Chờ giao hàng</span>
                            <?php elseif ($order->status == 'confirmed'): ?>
                                <span class="delivery-status" style="color: #26aa99;">
                                    <i class="fas fa-truck-loading"></i> Người bán đang chuẩn bị hàng
                                </span>
                                <span class="order-status-text" style="color: #26aa99;">Vận chuyển</span>
                            <?php elseif ($order->status == 'pending'): ?>
                                <span class="order-status-text" style="color: #ee4d2d; font-weight: 500; text-transform: uppercase;">Chờ thanh toán</span>
                            <?php elseif ($order->status == 'cancelled'): ?>
                                <span class="order-status-text" style="color: #ee4d2d; font-weight: 500; text-transform: uppercase;">Đã hủy</span>
                            <?php else: ?>
                                <span class="order-status-text" style="color: #f6a700;"><?php echo strtoupper($order->status); ?></span>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <div class="order-card-body">
                        <?php foreach ($order->items as $item): ?>
                            <div class="order-item">
                                <?php 
                                $pImg = $item->image;
                                $finalPImg = (strpos($pImg, 'public/') === false) ?
                                    ((strpos($pImg, 'uploads/') !== false) ? 'public/' . $pImg : 'public/uploads/' . $pImg) :
                                    $pImg;
                                ?>
                                <img src="<?php echo BASE_URL . $finalPImg; ?>" alt="Product" class="item-image" style="cursor: pointer;" onclick="window.location.href='<?php echo BASE_URL; ?>index.php?url=Product/show/<?php echo $item->product_id; ?>'">
                                <div class="item-details">
                                    <h5 class="item-name" style="cursor: pointer; transition: color 0.2s;" onmouseover="this.style.color='#ee225b'" onmouseout="this.style.color=''" onclick="window.location.href='<?php echo BASE_URL; ?>index.php?url=Product/show/<?php echo $item->product_id; ?>'"><?php echo htmlspecialchars($item->name); ?></h5>
                                    <div class="item-variant">Phân loại hàng: Handmade quà tặng</div>
                                    <div class="item-quantity">x<?php echo $item->quantity; ?></div>
                                </div>
                                <div class="item-price-group">
                                    <span class="item-old-price"><?php echo number_format($item->price * 1.2, 0, ',', '.'); ?>₫</span>
                                    <span class="item-price"><?php echo number_format($item->price, 0, ',', '.'); ?>₫</span>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    
                    <div class="order-card-footer">
                        <div class="order-total-row">
                            <span class="total-label"><?php echo ($order->status == 'pending') ? 'Tổng cộng         :' : 'Thành tiền:'; ?></span>
                            <span class="total-amount" style="<?php echo ($order->status == 'pending' || $order->status == 'cancelled') ? 'color: #ee4d2d; font-size: 24px;' : ''; ?>"><?php echo number_format($order->total, 0, ',', '.'); ?>₫</span>
                        </div>
                        <div class="order-actions" style="<?php echo ($order->status == 'cancelled') ? 'justify-content: space-between;' : ''; ?>">
                            <?php if ($order->status == 'cancelled'): ?>
                                <div class="text-muted small" style="color: rgba(0,0,0,.54);">Đã hủy bởi bạn</div>
                                <div class="d-flex" style="gap: 10px;">
                                    <button class="btn btn-danger" style="background-color: #ee4d2d; border: none; min-width: 150px; border-radius: 2px; cursor: pointer; transition: opacity 0.2s;" onmouseover="this.style.opacity='0.9'" onmouseout="this.style.opacity='1'" onclick="window.location.href='<?php echo BASE_URL; ?>index.php?url=Cart/reorder/<?php echo $order->id; ?>'">Mua Lại</button>
                                    <a href="<?php echo BASE_URL; ?>index.php?url=Page/cancelOrderDetail&id=<?php echo $order->id; ?>" class="btn btn-outline-secondary" style="min-width: 150px; background: white; color: #555; border: 1px solid rgba(0,0,0,.09); border-radius: 2px; display: flex; align-items: center; justify-content: center;">Xem Chi Tiết Hủy Đơn</a>
                                    <button class="btn-contact open-chat-with-product" 
                                            style="min-width: 150px;"
                                            data-id="<?php echo $firstItem->product_id ?? ''; ?>"
                                            data-seller-id="<?php echo $firstItem->seller_id ?? 0; ?>"
                                            data-name="<?php echo htmlspecialchars($firstItem->name ?? ''); ?>"
                                            data-price="<?php echo number_format($firstItem->price ?? 0, 0, ',', '.'); ?>₫"
                                            data-image="<?php echo BASE_URL . $finalPImg; ?>">Liên Hệ Người Bán</button>
                                </div>
                            <?php elseif ($order->status == 'pending'): ?>
                                <button class="btn btn-light text-muted" style="background: #f5f5f5; color: #999; min-width: 150px; border: 1px solid #e0e0e0; cursor: not-allowed; font-size: 16px; border-radius: 2px;" disabled>Chờ</button>
                                <button class="btn-contact open-chat-with-product" 
                                        style="min-width: 150px;"
                                        data-id="<?php echo $firstItem->product_id ?? ''; ?>"
                                        data-seller-id="<?php echo $firstItem->seller_id ?? 0; ?>"
                                        data-name="<?php echo htmlspecialchars($firstItem->name ?? ''); ?>"
                                        data-price="<?php echo number_format($firstItem->price ?? 0, 0, ',', '.'); ?>₫"
                                        data-image="<?php echo BASE_URL . $finalPImg; ?>">Liên Hệ Người Bán</button>
                                <button class="btn btn-outline-secondary btn-show-cancel-modal" style="min-width: 150px; background: white; color: #555; border: 1px solid rgba(0,0,0,.09); border-radius: 2px; height: 40px; display: flex; align-items: center; justify-content: center; font-size: 16px;" data-order-id="<?php echo $order->id; ?>">Hủy Đơn Hàng</button>
                            <?php elseif ($order->status == 'shipping' || $order->status == 'confirmed'): ?>
                                <button class="btn btn-light text-muted" style="background: #f5f5f5; color: #999; min-width: 150px; border: 1px solid #e0e0e0; cursor: not-allowed; font-size: 16px; border-radius: 2px;" disabled>Chờ</button>
                                <button class="btn-contact open-chat-with-product" 
                                        style="min-width: 150px;"
                                        data-id="<?php echo $firstItem->product_id ?? ''; ?>"
                                        data-seller-id="<?php echo $firstItem->seller_id ?? 0; ?>"
                                        data-name="<?php echo htmlspecialchars($firstItem->name ?? ''); ?>"
                                        data-price="<?php echo number_format($firstItem->price ?? 0, 0, ',', '.'); ?>₫"
                                        data-image="<?php echo BASE_URL . $finalPImg; ?>">Liên Hệ Người Bán</button>
                            <?php else: ?>
                                <button class="btn-rebuy" style="cursor: pointer;" onclick="window.location.href='<?php echo BASE_URL; ?>index.php?url=Cart/reorder/<?php echo $order->id; ?>'">Mua Lại</button>
                                <?php if ($order->status == 'completed'): ?>
                                    <button class="btn btn-outline-info ml-2" style="border-radius: 2px; font-size: 14px; padding: 8px 20px;" onclick="window.location.href='<?php echo BASE_URL; ?>index.php?url=Return/request/<?php echo $order->id; ?>'">Trả hàng/Hoàn tiền</button>
                                <?php endif; ?>
                                <button class="btn-contact open-chat-with-product" 
                                        data-id="<?php echo $firstItem->product_id ?? ''; ?>"
                                        data-seller-id="<?php echo $firstItem->seller_id ?? 0; ?>"
                                        data-name="<?php echo htmlspecialchars($firstItem->name ?? ''); ?>"
                                        data-price="<?php echo number_format($firstItem->price ?? 0, 0, ',', '.'); ?>₫"
                                        data-image="<?php echo BASE_URL . $finalPImg; ?>">Liên Hệ Người Bán</button>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </main>
</div>

<!-- Cancellation Modal -->
<div id="cancelOrderModal" class="cancel-modal">
    <div class="cancel-modal-content">
        <div class="cancel-modal-header">
            <h4>Lý Do Hủy</h4>
        </div>
        
        <div class="cancel-notice-box">
            <i class="fas fa-bell"></i>
            <div class="cancel-notice-text">
                Bạn có biết? Bạn có thể cập nhật thông tin nhận hàng cho đơn hàng (1 lần duy nhất) Nếu bạn xác nhận hủy, toàn bộ đơn hàng sẽ được hủy. Chọn lý do hủy phù hợp nhất với bạn nhé!
            </div>
        </div>

        <div class="cancel-reasons-list">
            <div class="reason-item" data-reason="Tôi muốn cập nhật địa chỉ/sđt nhận hàng.">
                <div class="reason-radio"></div>
                <div class="reason-text">Tôi muốn cập nhật địa chỉ/sđt nhận hàng.</div>
            </div>
            <div class="reason-item" data-reason="Tôi muốn thêm/thay đổi Mã giảm giá">
                <div class="reason-radio"></div>
                <div class="reason-text">Tôi muốn thêm/thay đổi Mã giảm giá</div>
            </div>
            <div class="reason-item" data-reason="Tôi muốn thay đổi sản phẩm (kích thước, màu sắc, số lượng...)">
                <div class="reason-radio"></div>
                <div class="reason-text">Tôi muốn thay đổi sản phẩm (kích thước, màu sắc, số lượng...)</div>
            </div>
            <div class="reason-item" data-reason="Thủ tục thanh toán rắc rối">
                <div class="reason-radio"></div>
                <div class="reason-text">Thủ tục thanh toán rắc rối</div>
            </div>
            <div class="reason-item" data-reason="Tôi tìm thấy chỗ mua khác tốt hơn (Rẻ hơn, uy tín hơn, giao nhanh hơn...)">
                <div class="reason-radio"></div>
                <div class="reason-text">Tôi tìm thấy chỗ mua khác tốt hơn (Rẻ hơn, uy tín hơn, giao nhanh hơn...)</div>
            </div>
            <div class="reason-item" data-reason="Tôi không có nhu cầu mua nữa">
                <div class="reason-radio"></div>
                <div class="reason-text">Tôi không có nhu cầu mua nữa</div>
            </div>
            <div class="reason-item" data-reason="Tôi không tìm thấy lý do hủy phù hợp">
                <div class="reason-radio"></div>
                <div class="reason-text">Tôi không tìm thấy lý do hủy phù hợp</div>
            </div>
        </div>

        <div class="cancel-modal-footer">
            <button class="btn-not-now">KHÔNG PHẢI BÂY GIỜ</button>
            <button id="btnConfirmCancel" class="btn-confirm-cancel" disabled>HỦY ĐƠN HÀNG</button>
        </div>
    </div>
</div>

<script>
function filterOrders(keyword) {
    const query = keyword.toLowerCase().trim();
    const cards = document.querySelectorAll('.order-card');
    let hasMatch = false;
    
    // Nếu rỗng, hiển thị lại tất cả ngoại trừ trường hợp list empty thật sự
    if (cards.length > 0 && Array.from(cards[0].classList).includes('text-center') && cards.length === 1) {
        // Đây là thông báo rỗng mặc định của server, k làm gì cả
        return;
    }

    cards.forEach(card => {
        if (!card.hasAttribute('data-order-id')) return; // Bỏ qua card empty báo lỗi
        
        // Collect strings to test
        const orderId = card.getAttribute('data-order-id') || '';
        const shopNames = Array.from(card.querySelectorAll('.shop-name')).map(el => el.innerText.toLowerCase());
        const itemNames = Array.from(card.querySelectorAll('.item-name')).map(el => el.innerText.toLowerCase());
        
        let match = false;
        if (orderId.includes(query)) match = true;
        if (shopNames.some(name => name.includes(query))) match = true;
        if (itemNames.some(name => name.includes(query))) match = true;
        
        if (match) {
            card.style.display = 'block';
            hasMatch = true;
        } else {
            card.style.display = 'none';
        }
    });

    const noResultBox = document.getElementById('noResultBox');
    const noResultKeyword = document.getElementById('noResultKeyword');
    
    if (!hasMatch && query !== '') {
        noResultKeyword.innerText = keyword;
        noResultBox.style.display = 'block';
    } else {
        noResultBox.style.display = 'none';
    }
}
</script>
