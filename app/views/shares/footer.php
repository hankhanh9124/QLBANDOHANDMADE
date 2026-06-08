</div> <!-- Close container from header.php -->

<style>
.custom-footer {
    background-color: #f4f8f5;
    color: #555;
    font-size: 13.5px;
    line-height: 1.5;
    padding: 28px 0 0;
    border-top: 2px solid rgba(66,126,89,0.12);
}
.custom-footer .footer-brand {
    color: var(--nav-dark-green);
    font-weight: 800;
    font-size: 1.05rem;
    letter-spacing: 1px;
    margin-bottom: 8px;
}
.custom-footer .footer-subtitle {
    color: var(--primary-color);
    font-weight: 700;
    text-transform: uppercase;
    font-size: 0.78rem;
    letter-spacing: 1.5px;
    margin-bottom: 5px;
}
.custom-footer .color-meaning {
    font-size: 0.82rem;
    color: #666;
    text-align: justify;
    line-height: 1.55;
    margin-bottom: 0;
}
/* Nav column title — boxed badge style */
.custom-footer .footer-col-title {
    color: var(--nav-dark-green);
    font-weight: 700;
    font-size: 0.78rem;
    text-transform: uppercase;
    letter-spacing: 1.2px;
    margin-bottom: 10px;
}
.custom-footer .footer-col-title.nav-title {
    display: inline-block;
    background: var(--nav-dark-green);
    color: #fff;
    padding: 3px 14px;
    border-radius: 3px;
    font-size: 0.75rem;
    letter-spacing: 1.5px;
    margin-bottom: 0;
}
/* Nav list with divider lines between items */
.custom-footer .footer-nav-col {
    border: 1.5px solid rgba(66,126,89,0.2);
    border-radius: 6px;
    overflow: hidden;
    margin-top: 10px;
    background: #fff;
}
.custom-footer .footer-links {
    list-style: none;
    padding: 0;
    margin: 0;
}
.custom-footer .footer-links li {
    border-bottom: 1px solid rgba(66,126,89,0.13);
    margin: 0;
}
.custom-footer .footer-links li:last-child {
    border-bottom: none;
}
.custom-footer .footer-links a {
    color: #4a4a4a;
    text-decoration: none;
    font-size: 0.8rem;
    font-weight: 600;
    text-transform: uppercase;
    transition: background 0.18s, color 0.18s, padding-left 0.18s;
    display: block;
    padding: 6px 12px;
    letter-spacing: 0.4px;
}
.custom-footer .footer-links a:hover {
    background: rgba(66,126,89,0.08);
    color: var(--nav-dark-green);
    padding-left: 18px;
}
.custom-footer .contact-info-text {
    font-size: 0.82rem;
    color: #666;
    line-height: 1.7;
}
.custom-footer .contact-info-text a {
    color: #555;
    text-decoration: none;
}
.custom-footer .contact-info-text a:hover {
    color: var(--nav-dark-green);
}
.custom-footer .social-icons {
    margin-top: 10px;
    display: flex;
    gap: 12px;
    align-items: center;
}
.custom-footer .social-icons a {
    color: #666;
    font-size: 1.15rem;
    transition: color 0.2s, transform 0.2s;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 30px;
    height: 30px;
    border-radius: 50%;
    border: 1.5px solid rgba(66,126,89,0.25);
}
.custom-footer .social-icons a:hover {
    transform: translateY(-2px);
    border-color: transparent;
}
.custom-footer .social-icons a.facebook-link:hover { color: #1877F2; border-color: #1877F2; }
.custom-footer .social-icons a.instagram-link:hover { color: #E1306C; border-color: #E1306C; }
.footer-divider-v {
    border-left: 1px solid rgba(66,126,89,0.15);
}
.copyright-bar {
    background-color: var(--nav-dark-green);
    font-weight: 500;
    font-size: 0.8rem;
    letter-spacing: 0.4px;
    margin-top: 20px;
    padding: 10px 0;
}
@media (max-width: 767px) {
    .footer-divider-v { border-left: none; border-top: 1px solid rgba(66,126,89,0.15); padding-top: 14px; margin-top: 14px; }
    .custom-footer { padding: 20px 0 0; }
}
</style>

<footer class="custom-footer">
    <div class="container">
        <div class="row no-gutters">

            <!-- Cột 1: Navigation links (2 cột con, mỗi cột 4 mục) -->
            <div class="col-md-3 col-12 px-3 pb-3">
                <div class="text-center">
                    <div class="footer-col-title nav-title">Điều hướng</div>
                </div>
                <div class="d-flex mt-2" style="gap: 10px;">
                    <!-- Cột trái: 4 mục đầu -->
                    <div class="flex-fill" style="margin-top:0;">
                        <ul class="footer-links">
                            <li><a href="<?php echo BASE_URL; ?>index.php?url=Product/">Trang chủ</a></li>
                            <li><a href="<?php echo BASE_URL; ?>index.php?url=Product/group/handmade">Sản phẩm len</a></li>
                            <li><a href="<?php echo BASE_URL; ?>index.php?url=Product/group/keychain">Móc khóa</a></li>
                            <li><a href="<?php echo BASE_URL; ?>index.php?url=Product/group/flowers">Hoa len</a></li>
                        </ul>
                    </div>
                    <!-- Cột phải: 4 mục sau -->
                    <div class="flex-fill" style="margin-top:0;">
                        <ul class="footer-links">
                            <li><a href="<?php echo BASE_URL; ?>index.php?url=Product/group/yarn">Len sợi</a></li>
                            <li><a href="<?php echo BASE_URL; ?>index.php?url=Product/group/tools">Dụng cụ đan móc</a></li>
                            <li><a href="<?php echo BASE_URL; ?>index.php?url=Page/about">Giới thiệu</a></li>
                            <li><a href="<?php echo BASE_URL; ?>index.php?url=Page/contact">Liên hệ</a></li>
                        </ul>
                    </div>
                </div>
            </div>


            <!-- Cột 2: Giới thiệu thương hiệu -->
            <div class="col-md-3 col-12 px-3 pb-3 footer-divider-v">
                <div class="text-center">
                    <div class="footer-col-title nav-title">GÌ CŨNG MÓC</div>
                </div>
                <div class="mt-2 text-center" style="padding: 12px 14px; min-height: 122px;">
                    <div class="footer-subtitle mb-1">Màu chủ đạo</div>
                    <p class="color-meaning mb-0">
                        Website sử dụng màu xanh làm tông màu chủ đạo — mang lại cảm giác nhẹ nhàng, sáng tạo, tỉ mỉ và giá trị thủ công đặc trưng của các sản phẩm len handmade, tạo nên không gian mua sắm gần gũi và tinh tế.
                    </p>
                </div>
            </div>

            <!-- Cột 3: Mạng xã hội -->
            <div class="col-md-2 col-12 px-3 pb-3 footer-divider-v">
                <div class="text-center">
                    <div class="footer-col-title nav-title">Theo dõi</div>
                </div>
                <div class="mt-2 text-center d-flex align-items-center justify-content-center" style="padding: 14px 12px; min-height: 122px;">
                    <div class="social-icons justify-content-center" style="margin-top:0; width: 100%;">
                        <a href="https://www.facebook.com/nguyen.lan.phuong.1303" target="_blank" class="facebook-link" title="Facebook"><i class="fab fa-facebook-f"></i></a>
                        <a href="https://www.instagram.com/gi_cung_moc/" target="_blank" class="instagram-link" title="Instagram"><i class="fab fa-instagram"></i></a>
                    </div>
                </div>
            </div>

            <!-- Cột 4: Liên hệ -->
            <div class="col-md-4 col-12 px-3 pb-3 footer-divider-v">
                <div class="text-center">
                    <div class="footer-col-title nav-title">Liên hệ</div>
                </div>
                <div class="mt-2 text-center d-flex align-items-center justify-content-center" style="padding: 10px 14px; min-height: 122px;">
                    <div class="contact-info-text">
                        <div class="mb-1"><i class="fas fa-map-marker-alt mr-1" style="color:var(--primary-color);font-size:0.75rem;"></i> 13/6, Khu phố Tân Hòa, Đông Hòa, Dĩ An, Bình Dương</div>
                        <div class="mb-1"><i class="fas fa-phone mr-1" style="color:var(--primary-color);font-size:0.75rem;"></i> <a href="tel:0382613031">0382.613.031</a></div>
                        <div><i class="fas fa-phone mr-1" style="color:var(--primary-color);font-size:0.75rem;"></i> <a href="tel:0964325348">0964.325.348</a></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Dòng bản quyền -->
    <div class="copyright-bar text-center text-white">
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
<?php if (isset($load_datatables) && $load_datatables): ?>
    <!-- DataTables JS for Product Lists -->
    <script type="text/javascript" src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/1.10.21/js/dataTables.bootstrap4.min.js"></script>
<?php endif; ?>
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
