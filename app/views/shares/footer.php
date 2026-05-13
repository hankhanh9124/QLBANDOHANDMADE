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

    </a>
    <a href="javascript:void(0)" class="social-btn back-to-top-btn" id="socialBackToTop" title="Lên đầu trang">
        <i class="fas fa-chevron-up"></i>
    </a>
</div>
        <i class="fas fa-chevron-up"></i>
    </a>
</div>

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script src="<?php echo BASE_URL; ?>public/js/orders.js?v=<?php echo time(); ?>"></script>

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
</script>
</body>
</html>
