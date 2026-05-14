<div class="container mt-4 mb-5" style="min-height: 50vh;">
    <!-- Breadcrumbs -->
    <?php if (!empty($breadcrumbs)): ?>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb bg-white px-0 shadow-sm rounded-pill px-4 mb-4" style="font-size: 14px;">
                <?php
                $count = count($breadcrumbs);
                $i = 1;
                foreach ($breadcrumbs as $label => $link):
                ?>
                    <?php if ($i < $count): ?>
                        <li class="breadcrumb-item"><a href="<?php echo $link; ?>" style="color: var(--primary-color); font-weight: 500;"><?php echo $label; ?></a></li>
                    <?php else: ?>
                        <li class="breadcrumb-item active text-muted" aria-current="page"><?php echo $label; ?></li>
                    <?php endif; ?>
                <?php
                    $i++;
                endforeach;
                ?>
            </ol>
        </nav>
    <?php endif; ?>

    <div class="row">
        <!-- Sidebar -->
        <div class="col-md-3 mb-4">
            <?php include 'app/views/shares/account_sidebar.php'; ?>
        </div>

        <!-- Main Content -->
        <div class="col-md-9">
            <div class="card shadow-sm border-0" style="border-radius: 15px; overflow: hidden;">
                <div class="card-header bg-white border-bottom pb-3 pt-4 px-4 d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="mb-1 font-weight-bold" style="color: #333;"><i class="fas fa-heart text-danger mr-2"></i>Sản Phẩm Yêu Thích</h4>
                        <p class="text-muted small mb-0">Quản lý những món đồ mà bạn đã thả tim</p>
                    </div>
                </div>
                <div class="card-body p-4">
                    <div class="row mx-n2" id="wishlist-container">
                        <?php if (!empty($wishlistProducts)): ?>
                            <?php foreach ($wishlistProducts as $product): ?>
                                <div class="col-lg-4 col-md-6 col-6 p-2 wishlist-item-<?php echo $product->id; ?>">
                                    <div class="handmade-card-unique w-100 h-100 m-0 position-relative shadow-sm" style="border-radius: 12px; overflow: hidden;">
                                        <div class="card-img-wrapper position-relative">
                                            <?php
                                            $discount = isset($product->discount_percent) ? (int)$product->discount_percent : 0;
                                            $newPrice = ($discount > 0) ? $product->price * (1 - $discount / 100) : $product->price;
                                            $pImg = $product->image;
                                            $finalPImg = (strpos($pImg, 'public/') === false) ?
                                                ((strpos($pImg, 'uploads/') !== false) ? 'public/' . $pImg : 'public/uploads/' . $pImg) :
                                                $pImg;
                                            ?>
                                            <?php if ($discount > 0): ?>
                                                <div class="card-badge-sale position-absolute" style="top: 10px; left: 10px; background: #ee4d2d; color: #fff; padding: 2px 6px; font-size: 11px; font-weight: bold; z-index: 2;">-<?php echo $discount; ?>%</div>
                                            <?php endif; ?>
                                            


                                            <a href="<?php echo BASE_URL; ?>index.php?url=Product/show/<?php echo $product->id; ?>">
                                                <img src="<?php echo BASE_URL . $finalPImg; ?>" alt="<?php echo htmlspecialchars($product->name); ?>" style="width: 100%; height: 200px; object-fit: cover;">
                                            </a>
                                        </div>
                                        <div class="handmade-card-body d-flex flex-column p-3 bg-white" style="height: calc(100% - 200px);">
                                            <div class="card-cat-label text-truncate text-muted text-uppercase mb-1" style="font-size: 11px;"><?php echo $product->category_name; ?></div>
                                            <h6 class="card-name-unique mb-2">
                                                <a href="<?php echo BASE_URL; ?>index.php?url=Product/show/<?php echo $product->id; ?>" class="text-dark" style="text-decoration: none; font-size: 14px; font-weight: 500; display: -webkit-box; -webkit-line-clamp: 2; line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                                                    <?php echo $product->name; ?>
                                                </a>
                                            </h6>
                                            <div class="mt-auto d-flex align-items-end justify-content-between">
                                                <div>
                                                    <span class="text-danger font-weight-bold" style="font-size: 15px;"><?php echo number_format($newPrice, 0, ',', '.'); ?>đ</span>
                                                    <div class="text-muted small mt-1"><i class="fas fa-star text-warning"></i> <?php echo number_format($product->rating, 1); ?></div>
                                                </div>
                                                
                                                <!-- Remove button (Wishlist Heart) -->
                                                <button class="btn btn-sm btn-light rounded-circle shadow-sm wishlist-btn mb-1" data-id="<?php echo $product->id; ?>" style="width: 32px; height: 32px; color: #ee225b; border: 1px solid #ffebee;" title="Bỏ yêu thích">
                                                    <i class="fas fa-heart"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="col-12 text-center py-5">
                                <i class="fas fa-heart text-muted mb-3" style="font-size: 4rem; opacity: 0.3;"></i>
                                <h5 class="text-muted font-weight-bold">Danh sách yêu thích trống</h5>
                                <p class="text-muted small mb-4">Bạn chưa lưu sản phẩm nào vào danh sách yêu thích.</p>
                                <a href="<?php echo BASE_URL; ?>index.php?url=Product/" class="btn btn-primary rounded-pill px-4" style="background-color: var(--primary-color); border: none;">Khám phá ngay</a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.custom-sidebar-nav .nav-link {
    color: #495057;
    font-weight: 500;
    padding: 12px 20px;
    border-radius: 8px;
    margin-bottom: 5px;
    transition: all 0.2s;
}
.custom-sidebar-nav .nav-link:hover {
    background-color: #f8f9fa;
    color: var(--primary-color);
}
.custom-sidebar-nav .nav-link.active {
    background-color: var(--primary-color);
    color: white;
}
.wishlist-btn {
    transition: transform 0.2s;
}
.wishlist-btn:hover {
    transform: scale(1.1);
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    $('.wishlist-btn').on('click', function(e) {
        e.preventDefault();
        const btn = $(this);
        const icon = btn.find('i');
        const productId = btn.data('id');
        const itemContainer = $('.wishlist-item-' + productId);
        
        // Optimistic UI update: Turn icon grey and start fading out immediately
        icon.removeClass('fas').addClass('far');
        btn.css('color', '#ccc');
        
        // Start fading out immediately for better UX
        itemContainer.fadeOut(400, function() {
            $(this).remove();
            
            // Check if there are any wishlist items left in the container
            // We check for any remaining child elements with grid classes
            if ($('#wishlist-container').children().length === 0) {
                $('#wishlist-container').html(`
                    <div class="col-12 text-center py-5">
                        <i class="fas fa-heart text-muted mb-3" style="font-size: 4rem; opacity: 0.3;"></i>
                        <h5 class="text-muted font-weight-bold">Danh sách yêu thích trống</h5>
                        <p class="text-muted small mb-4">Bạn chưa lưu sản phẩm nào vào danh sách yêu thích.</p>
                        <a href="<?php echo BASE_URL; ?>index.php?url=Product/" class="btn btn-primary rounded-pill px-4" style="background-color: var(--primary-color); border: none;">Khám phá ngay</a>
                    </div>
                `);
            }
        });

        // Backend sync
        $.ajax({
            url: '<?php echo BASE_URL; ?>index.php?url=Wishlist/toggle',
            type: 'POST',
            data: { product_id: productId },
            dataType: 'json',
            success: function(response) {
                if(!response.success) {
                    console.error("Failed to sync wishlist removal");
                }
            },
            error: function() {
                location.reload();
            }
        });
    });
});
</script>
