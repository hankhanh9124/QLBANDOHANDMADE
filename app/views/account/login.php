<?php include 'app/views/shares/header.php'; ?>

<div class="login-page-container py-5" style="background-color: #f5f5f5; min-height: 80vh; display: flex; align-items: center; justify-content: center;">
    <div class="login-card shadow-sm p-4" style="background: white; border-radius: 8px; width: 100%; max-width: 500px; padding: 40px !important;">
        <?php if (isset($error)): ?>
            <div class="alert alert-danger py-2 small mb-3" style="border-radius: 2px;"><?php echo $error; ?></div>
        <?php endif; ?>

        <form action="<?php echo BASE_URL; ?>index.php?url=Page/processLogin" method="POST">
            <div class="form-group mb-4">
                <input type="text" name="identifier" class="form-control shopee-input" placeholder="Email / Số điện thoại" required style="padding: 15px; border: 1px solid #dbdbdb; border-radius: 4px; font-size: 1.1rem;">
            </div>

            <div class="form-group mb-4 position-relative">
                <input type="password" name="password" class="form-control shopee-input" id="loginPassword" placeholder="Mật khẩu" required style="padding: 15px; border: 1px solid #dbdbdb; border-radius: 4px; font-size: 1.1rem;">
                <button type="button" class="btn position-absolute" style="right: 15px; top: 50%; transform: translateY(-50%); color: #666; background: none; border: none;" onclick="togglePasswordVisibility()">
                    <i class="far fa-eye-slash" id="toggleIcon" style="font-size: 1.1rem;"></i>
                </button>
            </div>

            <button type="submit" class="btn btn-block py-3 mb-3" style="background-color: var(--primary-color); color: white; border: none; border-radius: 4px; font-weight: 700; text-transform: uppercase; font-size: 1.25rem;">
                Đăng nhập
            </button>

            <div class="d-flex justify-content-end mb-4">
                <a href="<?php echo BASE_URL; ?>index.php?url=Page/forgotPassword" style="color: var(--primary-color); text-decoration: none; font-size: 1rem; font-weight: 600;">Quên mật khẩu?</a>
            </div>

            <div class="login-divider mb-4 d-flex align-items-center">
                <div class="flex-grow-1" style="height: 1px; background: #dbdbdb;"></div>
                <div class="px-3 small text-muted text-uppercase" style="font-weight: 700;">Hoặc</div>
                <div class="flex-grow-1" style="height: 1px; background: #dbdbdb;"></div>
            </div>

            <div class="row gx-2 mb-4">
                <div class="col-6">
                    <button type="button" class="btn btn-outline-secondary btn-block d-flex align-items-center justify-content-center py-3" style="border: 1px solid #dbdbdb; border-radius: 4px; color: #333; font-weight: 600;" onclick="window.location.href='<?php echo BASE_URL; ?>index.php?url=Page/socialLogin&provider=facebook'">
                        <i class="fab fa-facebook text-primary mr-2" style="font-size: 1.5rem;"></i> Facebook
                    </button>
                </div>
                <div class="col-6">
                    <button type="button" class="btn btn-outline-secondary btn-block d-flex align-items-center justify-content-center py-3" style="border: 1px solid #dbdbdb; border-radius: 4px; color: #333; font-weight: 600;" onclick="window.location.href='<?php echo BASE_URL; ?>index.php?url=Page/socialLogin&provider=google'">
                        <img src="https://www.gstatic.com/images/branding/product/1x/gsa_512dp.png" alt="G" class="mr-2" style="width: 20px;"> Google
                    </button>
                </div>
            </div>

            <div class="text-center text-muted mb-3" style="line-height: 1.6; font-size: 1rem;">
                Bằng việc đăng ký, bạn đã đồng ý với GÌ CŨNG MÓC về <br>
                <a href="#" style="color: var(--primary-color); text-decoration: none; font-weight: 600;">Điều khoản dịch vụ</a> & <a href="#" style="color: var(--primary-color); text-decoration: none; font-weight: 600;">Chính sách bảo mật</a>
            </div>
        </form>
    </div>
</div>

<!-- Bottom Link -->
<div class="text-center py-4 border-top" style="background-color: #f5f5f5;">
    <span class="text-muted" style="font-size: 1.1rem;">Bạn mới biết đến GÌ CŨNG MÓC?</span>
    <a href="<?php echo BASE_URL; ?>index.php?url=Page/register" style="color: var(--primary-color); font-weight: 700; text-decoration: none; font-size: 1.1rem;" class="ml-1">Đăng ký</a>
</div>

<script>
    function togglePasswordVisibility() {
        const passwordInput = document.getElementById('loginPassword');
        const toggleIcon = document.getElementById('toggleIcon');
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