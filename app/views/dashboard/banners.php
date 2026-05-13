<?php 
$action = 'banners';
include 'app/views/dashboard/header.php'; 
?>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Quản lý Banner (Carousel)</h2>
        <form action="<?php echo BASE_URL; ?>index.php?url=Product/uploadBanner" method="POST" enctype="multipart/form-data">
            <input type="file" name="banner_images[]" multiple class="d-none" id="bannerInput" onchange="this.form.submit()" accept="image/*,application/pdf">
            <button type="button" class="btn btn-primary" onclick="document.getElementById('bannerInput').click()">
                <i class="fas fa-upload mr-1"></i> Upload Banner mới
            </button>
        </form>
    </div>

    <div class="row">
        <?php if (!empty($banners)): ?>
            <?php foreach ($banners as $banner): ?>
                <div class="col-md-6 mb-4">
                    <div class="card h-100 shadow-sm border-0">
                        <a href="<?php echo BASE_URL; ?>public/images/<?php echo $banner->image; ?>" target="_blank" title="Xem chi tiết">
                            <?php 
                            $ext = strtolower(pathinfo($banner->image, PATHINFO_EXTENSION));
                            if ($ext === 'pdf'): 
                            ?>
                                <div class="card-img-top bg-light" style="width: 100%; height: 200px; overflow: hidden; position: relative;">
                                    <embed src="<?php echo BASE_URL; ?>public/images/<?php echo $banner->image; ?>#toolbar=0&navpanes=0&scrollbar=0&view=FitH" type="application/pdf" width="100%" height="200px" style="border: none;">
                                    <div style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; background: transparent; cursor: pointer;"></div>
                                </div>
                            <?php else: ?>
                                <img src="<?php echo BASE_URL; ?>public/images/<?php echo $banner->image; ?>" class="card-img-top" style="width: 100%; height: auto; max-height: 300px; object-fit: contain; background: #f8f9fa;" alt="Banner">
                            <?php endif; ?>
                        </a>
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="text-muted small">ID: <?php echo $banner->id; ?></span>
                                <a href="<?php echo BASE_URL; ?>index.php?url=Product/deleteBanner/<?php echo $banner->id; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Bạn có chắc muốn xóa banner này?')">
                                    <i class="fas fa-trash"></i> Xóa
                                </a>
                            </div>
                            

                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="col-12 text-center py-5">
                <p class="text-muted">Chưa có banner nào được upload.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include 'app/views/dashboard/footer.php'; ?>
