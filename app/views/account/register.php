<?php include 'app/views/shares/header.php'; ?>

<div class="register-page-container py-5" style="background-color: #f5f5f5; min-height: 80vh; display: flex; align-items: center; justify-content: center;">
    <div class="register-card shadow-sm p-4" style="background: white; border-radius: 4px; width: 100%; max-width: 400px;">
        <h4 class="mb-4" style="font-size: 1.25rem; font-weight: 500; color: #222;">Đăng ký</h4>
        
        <?php if (isset($error)): ?>
            <div class="alert alert-danger py-2 small mb-3" style="border-radius: 2px;"><?php echo $error; ?></div>
        <?php endif; ?>

        <form action="<?php echo BASE_URL; ?>index.php?url=Page/processRegister" method="POST">
            <div class="form-group mb-3">
                <input type="text" name="name" class="form-control shopee-input" placeholder="Họ và tên" required style="padding: 12px; border: 1px solid #dbdbdb; border-radius: 2px;">
            </div>

            <div class="form-group mb-3">
                <input type="text" name="identifier" class="form-control shopee-input" placeholder="Số điện thoại / Email" required style="padding: 12px; border: 1px solid #dbdbdb; border-radius: 2px;">
            </div>
            
            <div class="form-group mb-3 position-relative">
                <input type="password" name="password" class="form-control shopee-input" id="registerPassword" placeholder="Mật khẩu" required style="padding: 12px; border: 1px solid #dbdbdb; border-radius: 2px;">
                <button type="button" class="btn position-absolute" style="right: 10px; top: 50%; transform: translateY(-50%); color: #666; background: none; border: none;" onclick="togglePasswordVisibility('registerPassword', 'toggleIconReg')">
                    <i class="far fa-eye-slash" id="toggleIconReg"></i>
                </button>
            </div>

            <button type="submit" class="btn btn-shopee-orange btn-block py-2 mb-4" style="background-color: #ed2e64ff; color: white; border: none; border-radius: 2px; font-weight: 500; text-transform: uppercase;">
                Đăng ký
            </button>
            
            <div class="login-divider mb-4 d-flex align-items-center">
                <div class="flex-grow-1" style="height: 1px; background: #dbdbdb;"></div>
                <div class="px-3 small text-muted text-uppercase">Hoặc</div>
                <div class="flex-grow-1" style="height: 1px; background: #dbdbdb;"></div>
            </div>
            
            <div class="row gx-2 mb-4">
                <div class="col-6">
                    <button type="button" class="btn btn-outline-secondary btn-block small d-flex align-items-center justify-content-center py-2" style="border: 1px solid #dbdbdb; border-radius: 2px; color: #333;" onclick="window.location.href='<?php echo BASE_URL; ?>index.php?url=Page/socialLogin&provider=facebook'">
                        <i class="fab fa-facebook text-primary mr-2" style="font-size: 1.2rem;"></i> Facebook
                    </button>
                </div>
                <div class="col-6">
                    <button type="button" class="btn btn-outline-secondary btn-block small d-flex align-items-center justify-content-center py-2" style="border: 1px solid #dbdbdb; border-radius: 2px; color: #333;" onclick="window.location.href='<?php echo BASE_URL; ?>index.php?url=Page/socialLogin&provider=google'">
                        <img src="https://www.gstatic.com/images/branding/product/1x/gsa_512dp.png" alt="G" class="mr-2" style="width: 18px;"> Google
                    </button>
                </div>
            </div>
            
            <div class="text-center small text-muted mb-4" style="line-height: 1.4;">
                Bằng việc đăng ký, bạn đã đồng ý với GÌ CŨNG MÓC về <br>
                <a href="#" style="color: #ee4d2d; text-decoration: none;">Điều khoản dịch vụ</a> & <a href="#" style="color: #ee4d2d; text-decoration: none;">Chính sách bảo mật</a>
            </div>

            <div class="text-center small">
                <span class="text-muted">Bạn đã có tài khoản?</span>
                <a href="<?php echo BASE_URL; ?>index.php?url=Page/login" style="color: #ee4d2d; font-weight: 500; text-decoration: none;" class="ml-1">Đăng nhập</a>
            </div>
        </form>
    </div>
</div>

<script>
function togglePasswordVisibility(inputId, iconId) {
    const passwordInput = document.getElementById(inputId);
    const toggleIcon = document.getElementById(iconId);
    if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        toggleIcon.classList.remove('fa-eye-slash');
        toggleIcon.classList.add('fa-eye');
    } else {
        passwordInput.type = 'password';
        toggleIcon.classList.remove('fa-eye');
        toggleIcon.classList.add('fa-eye-slash');
    }
}
</script>

<?php include 'app/views/shares/footer.php'; ?>
