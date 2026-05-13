<?php
// app/views/account/address.php
?>
<div class="account-wrapper">
    <!-- Sidebar -->
    <?php include 'app/views/shares/account_sidebar.php'; ?>

    <!-- Main Content -->
    <main class="account-main">
        <div class="profile-card">
            <div class="profile-card-header d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="profile-title">Địa Chỉ Của Tôi</h2>
                    <p class="profile-subtitle">Quản lý các địa chỉ nhận hàng của bạn</p>
                </div>
                <button class="btn-add-bank"><i class="fas fa-plus mr-1"></i> Thêm địa chỉ mới</button>
            </div>
            
            <div class="address-list">
                <?php if (empty($user->address)): ?>
                    <div class="empty-bank-state">
                        <i class="fas fa-map-marker-alt fa-3x mb-3" style="color: #ddd;"></i>
                        <p>Bạn chưa có địa chỉ nào.</p>
                    </div>
                <?php else: ?>
                    <div class="address-item-card">
                        <div class="address-main-info">
                            <div class="address-header-row">
                                <span class="receiver-name"><?php echo htmlspecialchars($user->name); ?></span>
                                <span class="divider">|</span>
                                <span class="receiver-phone"><?php echo htmlspecialchars($user->phone ?? 'Chưa có SĐT'); ?></span>
                            </div>
                            <div class="address-detail-row">
                                <?php echo htmlspecialchars($user->address); ?>
                            </div>
                            <div class="address-tag">Mặc định</div>
                        </div>
                        <div class="address-actions">
                            <a href="#" class="address-action-link">Cập nhật</a>
                            <a href="#" class="address-action-link">Xóa</a>
                            <button class="btn-set-default" disabled>Thiết lập mặc định</button>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </main>
</div>

<style>
.profile-card {
    background: #fff;
    padding: 30px;
    border-radius: 4px;
    box-shadow: 0 1px 2px rgba(0,0,0,.1);
}
.profile-card-header {
    border-bottom: 1px solid #efefef;
    padding-bottom: 20px;
    margin-bottom: 30px;
}
.profile-title {
    font-size: 18px;
    font-weight: 500;
    color: #333;
    margin-bottom: 5px;
}
.profile-subtitle {
    font-size: 14px;
    color: #555;
    margin: 0;
}
.btn-add-bank {
    background: #ee225b;
    color: #fff;
    border: none;
    padding: 10px 20px;
    border-radius: 2px;
    font-size: 14px;
    cursor: pointer;
}
.address-item-card {
    border-bottom: 1px solid #efefef;
    padding: 25px 0;
    display: flex;
    justify-content: space-between;
}
.receiver-name {
    font-weight: 600;
    font-size: 16px;
    color: #333;
}
.divider {
    margin: 0 10px;
    color: #ddd;
}
.receiver-phone {
    color: #777;
    font-size: 14px;
}
.address-header-row {
    margin-bottom: 10px;
}
.address-detail-row {
    font-size: 14px;
    color: #555;
    margin-bottom: 12px;
    line-height: 1.4;
}
.address-tag {
    display: inline-block;
    border: 1px solid #ee225b;
    color: #ee225b;
    font-size: 12px;
    padding: 2px 5px;
    border-radius: 2px;
}
.address-actions {
    text-align: right;
    display: flex;
    flex-direction: column;
    gap: 10px;
    align-items: flex-end;
}
.address-action-link {
    font-size: 14px;
    color: #05a;
    text-decoration: none !important;
}
.btn-set-default {
    background: #fff;
    border: 1px solid #efefef;
    padding: 5px 10px;
    font-size: 14px;
    color: #999;
    border-radius: 2px;
    cursor: not-allowed;
}
</style>
