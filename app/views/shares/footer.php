</div> <!-- Close container from header.php -->

<footer class="bg-light text-center text-lg-start mt-5 border-top">
    <div class="container p-4">
        <div class="row">
            <!-- Cột thông tin liên hệ -->
            <div class="col-lg-6 col-md-12 mb-4">
                <h5 class="text-uppercase font-weight-bold" style="color: var(--primary-color);">GÌ CŨNG MÓC</h5>
                <p>
                    Hệ thống quản lý sản phẩm giúp bạn theo dõi và cập nhật thông tin sản phẩm dễ dàng. Cảm ơn bạn đã đồng hành cùng "GÌ CŨNG MÓC".
                </p>
            </div>
            <!-- Cột liên kết nhanh -->
            <div class="col-lg-3 col-md-6 mb-4">
                <h5 class="text-uppercase font-weight-bold">Liên kết nhanh</h5>
                <ul class="list-unstyled mb-0">
                    <li><a href="<?php echo BASE_URL; ?>index.php?url=Product/" class="text-dark">Trang chủ</a></li>
                    <li><a href="<?php echo BASE_URL; ?>index.php?url=Page/about" class="text-dark">Giới thiệu</a></li>
                    <li><a href="<?php echo BASE_URL; ?>index.php?url=Product/index" class="text-dark">Sản phẩm</a></li>
                    <li><a href="<?php echo BASE_URL; ?>index.php?url=Page/contact" class="text-dark">Liên hệ</a></li>
                </ul>
            </div>
            <!-- Cột mạng xã hội -->
            <div class="col-lg-3 col-md-6 mb-4">
                <h5 class="text-uppercase font-weight-bold">Kết nối với chúng tôi</h5>
                <a href="https://facebook.com/gicungmoc" target="_blank" class="text-muted mr-3" style="font-size: 24px;"><i class="fab fa-facebook-f"></i></a>
                <a href="https://twitter.com/gicungmoc" target="_blank" class="text-muted mr-3" style="font-size: 24px;"><i class="fab fa-twitter"></i></a>
                <a href="https://instagram.com/gicungmoc" target="_blank" class="text-muted mr-3" style="font-size: 24px;"><i class="fab fa-instagram"></i></a>
            </div>
        </div>
    </div>
    <!-- Dòng bản quyền -->
    <div class="text-center p-3 text-white" style="background-color: #333;">
        © 2026 "GÌ CŨNG MÓC - ĐỘC ĐÁO - TỰ TAY - ĐẸP MẮT".
    </div>
</footer>

<?php include_once 'app/views/shares/chat_widget.php'; ?>

<!-- Floating Social Icons (Global) -->
<div class="social-floating">
    <a href="https://www.facebook.com/nguyen.lan.phuong.1303" target="_blank" class="social-btn facebook" title="Facebook">
        <i class="fab fa-facebook-f"></i>
    </a>
    <a href="https://www.instagram.com/gi_cung_moc/" target="_blank" class="social-btn instagram" title="Instagram">
        <i class="fab fa-instagram"></i>
    </a>

    <a href="javascript:void(0)" class="social-btn back-to-top-btn" id="socialBackToTop" title="Lên đầu trang">
        <i class="fas fa-chevron-up"></i>
    </a>
</div>


<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script src="<?php echo BASE_URL; ?>public/js/orders.js?v=<?php echo time(); ?>"></script>

<!-- LogIn Reminder Modal (Global) -->
<div class="modal fade" id="loginReminderModal" tabindex="-1" role="dialog" aria-hidden="true" style="z-index: 9999;">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px; overflow: hidden;">
            <div class="modal-header border-0 bg-light p-4">
                <h5 class="modal-title font-weight-bold" style="color: var(--primary-color); font-size: 1.4rem;">
                    <i class="fas fa-shopping-bag mr-2"></i> Thông báo mua hàng
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="outline: none;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body text-center p-5">
                <div class="mb-4">
                    <i class="fas fa-user-circle fa-5x" style="color: #e0e0e0; background: linear-gradient(135deg, var(--primary-color) 0%, #427e59 100%); -webkit-background-clip: text; background-clip: text; -webkit-text-fill-color: transparent;"></i>
                </div>
                <h4 class="font-weight-bold mb-3" style="color: #333;">Bạn cần đăng nhập để đặt hàng!</h4>
                <p class="text-muted mb-0" style="font-size: 1.1rem;">Vui lòng đăng nhập hoặc đăng ký tài khoản mới để tiếp tục đặt hàng và nhận nhiều ưu đãi hơn từ cửa hàng.</p>
            </div>
            <div class="modal-footer border-0 p-4 bg-light d-flex justify-content-center">
                <button type="button" class="btn btn-secondary px-4 py-2 mr-2" data-dismiss="modal" style="border-radius: 10px; font-weight: 600;">Để sau</button>
                <a href="<?php echo BASE_URL; ?>index.php?url=Page/login" class="btn px-5 py-2" style="background-color: #ff9800; border: 2px solid var(--primary-color); border-radius: 10px; font-weight: 600; box-shadow: 0 4px 15px rgba(255,152,0,0.3); color: white; text-decoration: none;">
                    Đăng nhập ngay
                </a>
            </div>
        </div>
    </div>
</div>

<script>
window.onscroll = function() {
    var btn = document.getElementById("socialBackToTop");
    if (btn) {
        if (document.body.scrollTop > 300 || document.documentElement.scrollTop > 300) {
            btn.style.opacity = "1";
            btn.style.visibility = "visible";
            btn.style.transform = "scale(1)";
        } else {
            btn.style.opacity = "0";
            btn.style.visibility = "hidden";
            btn.style.transform = "scale(0.5)";
        }
    }
};

document.addEventListener('DOMContentLoaded', function() {
    var btn = document.getElementById('socialBackToTop');
    if (btn) {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });
    }
});

// Global Checkout Login Check for mini-cart
document.addEventListener('DOMContentLoaded', function() {
    const miniCheckoutBtn = document.getElementById('btnMiniCheckout');
    if (miniCheckoutBtn) {
        miniCheckoutBtn.addEventListener('click', function(e) {
            const isLoggedIn = <?php echo isset($_SESSION['user_id']) ? 'true' : 'false'; ?>;
            if (!isLoggedIn) {
                e.preventDefault();
                $('#loginReminderModal').modal('show');
            }
        });
    }
});
</script>
</body>
</html>
