<?php include 'app/views/shares/header.php'; ?>

<div class="container py-5 text-center" style="min-height: 70vh; display: flex; align-items: center; justify-content: center;">
    <div class="social-connecting-card p-5 shadow-sm" style="background: white; border-radius: 8px; max-width: 500px; width: 100%;">
        <div class="mb-4">
            <?php if ($provider === 'google'): ?>
                <img src="https://www.gstatic.com/images/branding/product/1x/gsa_512dp.png" alt="Google" style="width: 80px;">
            <?php else: ?>
                <i class="fab fa-facebook text-primary" style="font-size: 80px;"></i>
            <?php endif; ?>
        </div>
        <h3 class="mb-3">Đang kết nối với <?php echo ucfirst($provider); ?>...</h3>
        <p class="text-muted mb-4">Vui lòng đợi trong giây lát để hệ thống xác thực tài khoản của bạn.</p>
        
        <div class="spinner-border text-danger" role="status" style="width: 3rem; height: 3rem; color: #ed2e64ff !important;">
            <span class="sr-only">Loading...</span>
        </div>

        <script>
            // Simulate OAuth redirect
            setTimeout(() => {
                window.location.href = "<?php echo BASE_URL; ?>index.php?url=Page/processSocialLogin&provider=<?php echo $provider; ?>";
            }, 2000);
        </script>
    </div>
</div>

<?php include 'app/views/shares/footer.php'; ?>
