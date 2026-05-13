<?php
// app/views/account/seller_onboarding.php
$existingRequest = $existingRequest ?? null;
?>
<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card border-0 shadow-sm rounded-lg overflow-hidden" style="background: #FFF9F5;">
                <div class="card-header border-0 py-4 text-center" style="background: linear-gradient(135deg, #75c794 0%, #5da378 100%); color: white;">
                    <h3 class="mb-0 font-weight-bold">Trở thành Người bán Handmade</h3>
                    <p class="mb-0 opacity-8">Tham gia cộng đồng và bắt đầu kinh doanh ngay hôm nay</p>
                </div>
                
                <div class="card-body p-4 p-md-5">
                    <?php if (isset($_SESSION['success_message'])): ?>
                        <div class="alert alert-success border-0 shadow-sm mb-4">
                            <i class="fas fa-check-circle mr-2"></i> <?php echo $_SESSION['success_message']; unset($_SESSION['success_message']); ?>
                        </div>
                    <?php endif; ?>

                    <?php if (isset($_SESSION['error_message'])): ?>
                        <div class="alert alert-danger border-0 shadow-sm mb-4">
                            <i class="fas fa-exclamation-circle mr-2"></i> <?php echo $_SESSION['error_message']; unset($_SESSION['error_message']); ?>
                        </div>
                    <?php endif; ?>

                    <?php if ($existingRequest && $existingRequest->status === 'pending'): ?>
                        <div class="text-center py-5">
                            <div class="mb-4">
                                <i class="fas fa-clock fa-4x text-warning"></i>
                            </div>
                            <h4 class="font-weight-bold">Yêu cầu đang chờ xét duyệt</h4>
                            <p class="text-muted">Chúng tôi đang xem xét hồ sơ của bạn. Vui lòng quay lại sau 24-48 giờ.</p>
                            <a href="<?php echo BASE_URL; ?>" class="btn btn-outline-secondary mt-3">Quay lại Trang chủ</a>
                        </div>
                    <?php elseif ($existingRequest && $existingRequest->status === 'rejected'): ?>
                        <div class="alert alert-warning border-0 shadow-sm mb-4">
                            <h5 class="font-weight-bold"><i class="fas fa-info-circle mr-2"></i> Yêu cầu trước đó bị từ chối</h5>
                            <p class="mb-1"><strong>Lý do:</strong> <?php echo htmlspecialchars($existingRequest->reject_reason ?? ''); ?></p>
                            <p class="small mb-0">Bạn có thể chỉnh sửa thông tin bên dưới và gửi lại yêu cầu.</p>
                        </div>
                        <?php $showForm = true; ?>
                    <?php else: ?>
                        <?php $showForm = true; ?>
                    <?php endif; ?>

                    <?php if (isset($showForm)): ?>
                        <form action="<?php echo BASE_URL; ?>index.php?url=Seller/submitRegistration" method="POST" enctype="multipart/form-data">
                            <!-- Section: Shop Info -->
                            <div class="mb-5">
                                <h5 class="font-weight-bold text-uppercase mb-4 pb-2 border-bottom" style="color: #75c794; border-color: #d1f0db !important;">
                                    <i class="fas fa-store mr-2"></i> Thông tin Cửa hàng
                                </h5>
                                <div class="form-group mb-4">
                                    <label class="font-weight-bold">Tên Shop của bạn <span class="text-danger">*</span></label>
                                    <input type="text" name="shop_name" class="form-control form-control-lg border-0 shadow-sm" placeholder="VD: Gốm Xinh Handmade" required>
                                </div>
                                <div class="form-group mb-4">
                                    <label class="font-weight-bold">Mô tả Shop <span class="text-danger">*</span></label>
                                    <textarea name="shop_description" class="form-control border-0 shadow-sm" rows="3" placeholder="Giới thiệu đôi nét về shop của bạn..." required></textarea>
                                </div>
                                <div class="form-group mb-4">
                                    <label class="font-weight-bold">Loại sản phẩm chính <span class="text-danger">*</span></label>
                                    <input type="text" name="product_types" class="form-control border-0 shadow-sm" placeholder="VD: Đồ len, Trang sức, Gốm sứ..." required>
                                </div>
                            </div>

                            <!-- Section: Verification -->
                            <div class="mb-5">
                                <h5 class="font-weight-bold text-uppercase mb-4 pb-2 border-bottom" style="color: #75c794; border-color: #d1f0db !important;">
                                    <i class="fas fa-id-card mr-2"></i> Xác minh & Thanh toán
                                </h5>
                                <div class="row">
                                    <div class="col-md-6 form-group mb-4">
                                        <label class="font-weight-bold">Tài khoản Ngân hàng (Để nhận tiền) <span class="text-danger">*</span></label>
                                        <input type="text" name="bank_account" class="form-control border-0 shadow-sm" placeholder="Số TK - Ngân hàng - Chủ TK" required>
                                    </div>
                                    <div class="col-md-6 form-group mb-4">
                                        <label class="font-weight-bold">Ảnh CMND/CCCD mặt trước <span class="text-danger">*</span></label>
                                        <div class="custom-file shadow-sm">
                                            <input type="file" name="identity_proof" class="custom-file-input" id="idProof" required>
                                            <label class="custom-file-label border-0" for="idProof">Chọn ảnh...</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group mb-4">
                                    <label class="font-weight-bold">Link Portfolio / Facebook / TikTok Shop (Nếu có)</label>
                                    <input type="text" name="portfolio_links" class="form-control border-0 shadow-sm" placeholder="https://facebook.com/yourshop">
                                </div>
                            </div>

                            <div class="custom-control custom-checkbox mb-4">
                                <input type="checkbox" class="custom-control-input" id="terms" required>
                                <label class="custom-control-label small text-muted" for="terms">
                                    Tôi đồng ý với <a href="#" class="text-primary">Điều khoản & Chính sách</a> dành cho người bán của sàn thương mại điện tử.
                                </label>
                            </div>

                            <button type="submit" class="btn btn-lg btn-block text-white font-weight-bold shadow-lg" style="background: #75c794; border-radius: 10px; padding: 15px;">
                                GỬI YÊU CẦU ĐĂNG KÝ
                            </button>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.form-control:focus {
    box-shadow: 0 0 0 0.2rem rgba(117, 199, 148, 0.25) !important;
}
.custom-file-input:focus ~ .custom-file-label {
    box-shadow: 0 0 0 0.2rem rgba(117, 199, 148, 0.25) !important;
}
</style>

<script>
// Hiển thị tên file khi chọn
document.querySelector('.custom-file-input').addEventListener('change',function(e){
    var fileName = document.getElementById("idProof").files[0].name;
    var nextSibling = e.target.nextElementSibling
    nextSibling.innerText = fileName
})
</script>
