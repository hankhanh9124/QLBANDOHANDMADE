<div class="container-fluid">
    <h2 class="mb-4">Thông tin Shop</h2>

    <?php if (isset($_SESSION['success_message'])): ?>
        <div class="alert alert-success">
            <i class="fas fa-check-circle mr-2"></i> <?php echo $_SESSION['success_message']; unset($_SESSION['success_message']); ?>
        </div>
    <?php endif; ?>

    <?php if (isset($_SESSION['error_message'])): ?>
        <div class="alert alert-danger">
            <i class="fas fa-exclamation-circle mr-2"></i> <?php echo $_SESSION['error_message']; unset($_SESSION['error_message']); ?>
        </div>
    <?php endif; ?>

    <?php if ($pendingUpdate): ?>
        <div class="alert alert-warning shadow-sm border-0" style="border-radius: 10px; border-left: 5px solid #ffc107;">
            <i class="fas fa-hourglass-half mr-2"></i> Bạn đang có một yêu cầu cập nhật thông tin Shop gửi đi lúc <?php echo date('d/m/Y H:i', strtotime($pendingUpdate->created_at)); ?>. Yêu cầu đang được Admin xem xét.
            <br>Tên mới chờ duyệt: <strong><?php echo htmlspecialchars($pendingUpdate->new_name); ?></strong>
        </div>
    <?php endif; ?>

    <div class="card shadow-sm border-0 mb-5" style="border-radius: 15px;">
        <div class="card-body p-4 p-md-5">
            <form action="<?php echo BASE_URL; ?>index.php?url=Seller/submitShopUpdate" method="POST" enctype="multipart/form-data">
                
                <div class="row">
                    <div class="col-md-8">
                        <div class="form-group mb-4">
                            <label class="font-weight-bold">Tên Shop <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control form-control-lg" value="<?php echo htmlspecialchars($shop->name); ?>" required <?php echo $pendingUpdate ? 'disabled' : ''; ?>>
                        </div>

                        <div class="form-group mb-4">
                            <label class="font-weight-bold">Mô tả Shop</label>
                            <textarea name="description" class="form-control" rows="5" <?php echo $pendingUpdate ? 'disabled' : ''; ?>><?php echo htmlspecialchars($shop->description ?? ''); ?></textarea>
                        </div>
                    </div>
                    <div class="col-md-4 text-center">
                        <div class="form-group mb-4">
                            <label class="font-weight-bold d-block">Ảnh đại diện (Logo)</label>
                            <?php $logoUrl = !empty($shop->logo) ? (strpos($shop->logo, 'http') === 0 ? $shop->logo : BASE_URL . $shop->logo) : BASE_URL . 'public/images/logolen.jpg'; ?>
                            <img src="<?php echo $logoUrl; ?>" class="rounded-circle shadow-sm mb-3" style="width: 150px; height: 150px; object-fit: cover; border: 3px solid #f8f9fa;">
                            <?php if (!$pendingUpdate): ?>
                            <div class="custom-file mt-2 text-left">
                                <input type="file" name="logo" class="custom-file-input" id="logoUpload" accept="image/*">
                                <label class="custom-file-label" for="logoUpload">Chọn logo mới</label>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <div class="form-group mb-4">
                    <label class="font-weight-bold d-block">Ảnh bìa (Banner)</label>
                    <?php if (!empty($shop->banner)): ?>
                        <img src="<?php echo (strpos($shop->banner, 'http') === 0 ? $shop->banner : BASE_URL . $shop->banner); ?>" class="img-fluid rounded shadow-sm mb-3" style="max-height: 250px; width: 100%; object-fit: cover;">
                    <?php else: ?>
                        <div class="bg-light rounded shadow-sm d-flex align-items-center justify-content-center mb-3" style="height: 150px; width: 100%; border: 2px dashed #ddd;">
                            <span class="text-muted"><i class="fas fa-image mr-2"></i>Chưa có ảnh bìa</span>
                        </div>
                    <?php endif; ?>
                    
                    <?php if (!$pendingUpdate): ?>
                    <div class="custom-file" style="max-width: 400px;">
                        <input type="file" name="banner" class="custom-file-input" id="bannerUpload" accept="image/*">
                        <label class="custom-file-label" for="bannerUpload">Tải ảnh bìa mới</label>
                    </div>
                    <?php endif; ?>
                </div>

                <?php if (!$pendingUpdate): ?>
                <div class="text-right mt-5">
                    <button type="submit" class="btn btn-lg px-5 text-white" style="background-color: var(--primary-color); border-radius: 30px; box-shadow: 0 4px 10px rgba(238, 34, 91, 0.3);">
                        <i class="fas fa-paper-plane mr-2"></i> Gửi yêu cầu cập nhật
                    </button>
                </div>
                <?php endif; ?>
            </form>
        </div>
    </div>
</div>

<script>
    // Update custom file label on file select
    document.querySelectorAll('.custom-file-input').forEach(function(input) {
        input.addEventListener('change', function(e) {
            var fileName = document.getElementById(e.target.id).files[0].name;
            var nextSibling = e.target.nextElementSibling;
            nextSibling.innerText = fileName;
        });
    });
</script>
