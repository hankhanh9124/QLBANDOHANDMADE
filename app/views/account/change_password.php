<?php
// app/views/account/change_password.php
?>
<div class="account-wrapper">
    <!-- Sidebar -->
    <?php include 'app/views/shares/account_sidebar.php'; ?>

    <!-- Main Content -->
    <main class="account-main">
        <div class="profile-card" style="max-width: 800px;">
            <div class="profile-card-header">
                <h2 class="profile-title">Đổi Mật Khẩu</h2>
                <p class="profile-subtitle">Để bảo mật tài khoản, vui lòng không chia sẻ mật khẩu cho người khác</p>
            </div>
            
            <div class="profile-card-body">
                <form action="<?php echo BASE_URL; ?>index.php?url=Page/processChangePassword" method="POST" class="profile-form">
                    <div class="form-group-row">
                        <label>Mật Khẩu Hiện Tại</label>
                        <input type="password" name="current_password" required class="form-control-minimal">
                    </div>
                    <div class="form-group-row">
                        <label>Mật Khẩu Mới</label>
                        <input type="password" name="new_password" required class="form-control-minimal">
                    </div>
                    <div class="form-group-row">
                        <label>Xác Nhận Mật Khẩu</label>
                        <input type="password" name="confirm_password" required class="form-control-minimal">
                    </div>
                    
                    <div class="form-group-row" style="margin-top: 30px;">
                        <label></label>
                        <div style="flex: 1;">
                            <button type="submit" class="btn-save-profile">Xác nhận</button>
                            <a href="#" class="btn-forgot-pass ml-3" style="font-size: 13px; color: #05a;">Quên mật khẩu?</a>
                        </div>
                    </div>
                </form>
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
.form-group-row {
    display: flex;
    align-items: center;
    margin-bottom: 25px;
}
.form-group-row label {
    width: 200px;
    text-align: right;
    margin-right: 20px;
    color: rgba(85,85,85,.8);
    font-size: 14px;
}
.form-control-minimal {
    border: 1px solid rgba(0,0,0,.14);
    padding: 10px;
    font-size: 14px;
    border-radius: 2px;
    flex: 1;
    max-width: 400px;
    outline: none;
}
.btn-save-profile {
    background: #ee225b;
    color: #fff;
    border: none;
    padding: 10px 30px;
    border-radius: 2px;
    font-size: 14px;
    cursor: pointer;
}
@media (max-width: 768px) {
    .form-group-row {
        flex-direction: column;
        align-items: flex-start;
    }
    .form-group-row label {
        width: 100%;
        text-align: left;
        margin-bottom: 10px;
    }
}
</style>
