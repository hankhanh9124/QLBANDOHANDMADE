<?php
// app/views/account/bank.php
?>
<div class="account-wrapper">
    <!-- Sidebar -->
    <?php include 'app/views/shares/account_sidebar.php'; ?>

    <!-- Main Content -->
    <main class="account-main">
        <div class="profile-card">
            <div class="profile-card-header d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="profile-title">Thẻ Ngân Hàng/Tài Khoản</h2>
                    <p class="profile-subtitle">Quản lý phương thức thanh toán của bạn</p>
                </div>
                <button class="btn-add-bank"><i class="fas fa-plus mr-1"></i> Thêm tài khoản ngân hàng</button>
            </div>
            
            <div class="bank-card-list">
                <?php if (empty($user->bank_account)): ?>
                    <div class="empty-bank-state">
                        <i class="fas fa-university fa-3x mb-3" style="color: #ddd;"></i>
                        <p>Bạn chưa có tài khoản ngân hàng nào.</p>
                    </div>
                <?php else: ?>
                    <div class="bank-item-card">
                        <div class="bank-item-info">
                            <div class="bank-name-tag"><?php echo htmlspecialchars($user->bank_name ?? 'Ngân hàng'); ?></div>
                            <div class="bank-account-num">**** **** **** <?php echo substr($user->bank_account, -4); ?></div>
                            <div class="bank-user-name"><?php echo htmlspecialchars($user->name); ?></div>
                        </div>
                        <div class="bank-item-actions">
                            <button class="btn-delete-bank">Xóa</button>
                        </div>
                    </div>
                <?php endif; ?>
            </div>

            <div class="security-notice mt-5">
                <h5 style="font-size: 14px; margin-bottom: 10px; color: #333;">Lưu ý bảo mật</h5>
                <ul style="font-size: 13px; color: #888; padding-left: 20px;">
                    <li>GÌ CŨNG MÓC không bao giờ yêu cầu mã PIN hoặc mật khẩu ngân hàng của bạn.</li>
                    <li>Sử dụng xác thực 2 lớp để bảo vệ tài khoản của bạn tốt hơn.</li>
                </ul>
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
.empty-bank-state {
    text-align: center;
    padding: 100px 0;
    color: #999;
}
.bank-item-card {
    border: 1px solid #efefef;
    border-radius: 4px;
    padding: 20px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    background: linear-gradient(135deg, #fdfbfb 0%, #ebedee 100%);
    max-width: 500px;
}
.bank-name-tag {
    font-weight: 600;
    color: #c2255c;
    margin-bottom: 15px;
    text-transform: uppercase;
}
.bank-account-num {
    font-size: 18px;
    font-family: monospace;
    letter-spacing: 2px;
    color: #333;
    margin-bottom: 10px;
}
.bank-user-name {
    font-size: 13px;
    color: #777;
    text-transform: uppercase;
}
.btn-delete-bank {
    background: none;
    border: none;
    color: #ee225b;
    font-size: 14px;
    cursor: pointer;
}
</style>
