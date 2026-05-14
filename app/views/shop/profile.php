<?php 
$isFollowing = $isFollowing ?? false; 
$products = $products ?? [];
$followerCount = $followerCount ?? 0;
?>
<div class="shop-profile-container container mt-4">
    <!-- Header Section (Instagram Style) -->
    <div class="shop-header mb-5">
        <div class="row align-items-center">
            <div class="col-md-4 text-center mb-4 mb-md-0">
                <div class="shop-avatar-wrapper">
                    <?php if (!empty($shop->logo)): ?>
                        <img src="<?php echo BASE_URL . $shop->logo; ?>" alt="<?php echo htmlspecialchars($shop->name); ?>" class="shop-avatar img-fluid rounded-circle shadow-sm">
                    <?php else: ?>
                        <div class="shop-avatar-placeholder rounded-circle d-flex align-items-center justify-content-center shadow-sm">
                            <i class="fas fa-store fa-3x text-muted"></i>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            <div class="col-md-8">
                <div class="shop-info-header d-flex align-items-center flex-wrap mb-3">
                    <h1 class="shop-name mr-4 mb-2"><?php echo htmlspecialchars($shop->name); ?></h1>
                    <div class="shop-actions d-flex mb-2">
                        <?php if (isset($_SESSION['user_id']) && $_SESSION['user_id'] != $shop->seller_id): ?>
                            <button class="btn <?php echo $isFollowing ? 'btn-following' : 'btn-follow'; ?> mr-2" id="btnFollowShop" data-id="<?php echo $shop->id; ?>">
                                <?php echo $isFollowing ? 'Đang theo dõi' : 'Theo dõi'; ?>
                            </button>
                            <button class="btn btn-message open-chat-with-product" data-seller-id="<?php echo $shop->seller_id; ?>">
                                Nhắn tin
                            </button>
                        <?php elseif (!isset($_SESSION['user_id'])): ?>
                            <a href="<?php echo BASE_URL; ?>index.php?url=Page/login" class="btn btn-follow mr-2">Theo dõi</a>
                            <a href="<?php echo BASE_URL; ?>index.php?url=Page/login" class="btn btn-message">Nhắn tin</a>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="shop-stats d-flex mb-4">
                    <div class="stat-item mr-5">
                        <span class="stat-count"><?php echo count($products); ?></span> sản phẩm
                    </div>
                    <div class="stat-item mr-5">
                        <span class="stat-count" id="followerCount"><?php echo number_format($followerCount); ?></span> người theo dõi
                    </div>
                </div>

                <div class="shop-bio">
                    <h2 class="seller-name mb-2"><?php echo htmlspecialchars($shop->seller_name); ?></h2>
                    <div class="bio-content">
                        <?php echo nl2br(htmlspecialchars($shop->description)); ?>
                    </div>
                    <div class="shop-details mt-2">
                        <?php if (!empty($shop->location)): ?>
                            <div class="detail-item"><i class="fas fa-map-marker-alt mr-2"></i><?php echo htmlspecialchars($shop->location); ?></div>
                        <?php endif; ?>
                        <!-- Additional links if needed -->
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabs Section -->
    <div class="shop-tabs-wrapper border-top">
        <ul class="nav nav-tabs justify-content-center border-0" id="shopTabs" role="tablist">
            <li class="nav-item">
                <a class="nav-link active" id="products-tab" data-toggle="tab" href="#products" role="tab">
                    <i class="fas fa-th mr-1"></i> SẢN PHẨM
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="reviews-tab" data-toggle="tab" href="#reviews" role="tab">
                    <i class="fas fa-star mr-1"></i> ĐÁNH GIÁ
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="about-tab" data-toggle="tab" href="#about" role="tab">
                    <i class="fas fa-info-circle mr-1"></i> GIỚI THIỆU
                </a>
            </li>
        </ul>
    </div>

    <!-- Tab Content -->
    <div class="tab-content mt-4 mb-5" id="shopTabContent">
        <!-- Products Tab -->
        <div class="tab-pane fade show active" id="products" role="tabpanel">
            <div class="row" id="productGrid">
                <?php if (!empty($products)): ?>
                    <?php foreach ($products as $p): ?>
                        <div class="col-6 col-md-4 col-lg-3 mb-4">
                            <!-- Product Card (Reused style from main site) -->
                            <div class="product-card h-100 shadow-sm border-0" style="border-radius: 12px; overflow: hidden; transition: all 0.3s ease; background: #fff;">
                                <div class="position-relative">
                                    <a href="<?php echo BASE_URL; ?>index.php?url=Product/show/<?php echo $p->id; ?>" class="d-block">
                                        <?php if (!empty($p->image)): ?>
                                            <img src="<?php echo BASE_URL . (strpos($p->image, 'public/') === false ? 'public/uploads/' . $p->image : $p->image); ?>" alt="<?php echo htmlspecialchars($p->name); ?>" class="img-fluid" style="width: 100%; aspect-ratio: 1/1; object-fit: cover;">
                                        <?php else: ?>
                                            <div class="bg-light d-flex align-items-center justify-content-center" style="width: 100%; aspect-ratio: 1/1;">
                                                <i class="fas fa-image text-muted fa-3x"></i>
                                            </div>
                                        <?php endif; ?>
                                    </a>
                                    <?php if ($p->discount_percent > 0): ?>
                                        <div class="badge badge-danger position-absolute" style="top: 10px; left: 10px; border-radius: 4px; padding: 4px 8px;">
                                            -<?php echo $p->discount_percent; ?>%
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <div class="p-3">
                                    <h3 class="product-title mb-1" style="font-size: 0.95rem; font-weight: 500; height: 2.4em; overflow: hidden; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical;">
                                        <a href="<?php echo BASE_URL; ?>index.php?url=Product/show/<?php echo $p->id; ?>" class="text-dark text-decoration-none"><?php echo htmlspecialchars($p->name); ?></a>
                                    </h3>
                                    <div class="d-flex align-items-center mb-2">
                                        <div class="product-price font-weight-bold" style="color: var(--primary-color); font-size: 1.1rem;">
                                            <?php echo number_format($p->price * (1 - ($p->discount_percent / 100)), 0, ',', '.'); ?>₫
                                        </div>
                                        <?php if ($p->discount_percent > 0): ?>
                                            <div class="text-muted small ml-2" style="text-decoration: line-through;">
                                                <?php echo number_format($p->price, 0, ',', '.'); ?>₫
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center mt-2">
                                        <div class="product-rating text-warning small">
                                            <i class="fas fa-star"></i> <?php echo number_format($p->rating, 1); ?>
                                        </div>
                                        <div class="text-muted small">
                                            Đã bán <?php echo number_format($p->sold); ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="col-12 text-center py-5">
                        <p class="text-muted">Shop chưa có sản phẩm nào.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Reviews Tab -->
        <div class="tab-pane fade" id="reviews" role="tabpanel">
            <div class="shop-reviews-container">
                <?php if (!empty($shopReviews)): ?>
                    <?php foreach ($shopReviews as $review): ?>
                        <div class="review-item mb-4 pb-4 border-bottom">
                            <div class="d-flex align-items-start">
                                <div class="review-user-avatar mr-3">
                                    <div class="rounded-circle bg-light d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; font-weight: bold; color: #777; border: 1px solid #eee;">
                                        <?php echo strtoupper(substr($review->user_name ?? 'U', 0, 1)); ?>
                                    </div>
                                </div>
                                <div class="review-content flex-grow-1">
                                    <div class="d-flex justify-content-between">
                                        <h5 class="mb-1" style="font-size: 1rem; font-weight: 600;"><?php echo htmlspecialchars($review->user_name); ?></h5>
                                        <span class="text-muted small"><?php echo date('d/m/Y', strtotime($review->created_at)); ?></span>
                                    </div>
                                    <div class="review-rating text-warning mb-2">
                                        <?php for($i=1; $i<=5; $i++) echo '<i class="' . ($i <= $review->rating ? 'fas' : 'far') . ' fa-star"></i>'; ?>
                                    </div>
                                    <p class="review-text mb-2"><?php echo nl2br(htmlspecialchars($review->comment)); ?></p>
                                    <div class="review-product-ref bg-light p-2 rounded d-flex align-items-center" style="font-size: 0.85rem;">
                                        <img src="<?php echo BASE_URL . (strpos($review->product_image, 'public/') === false ? 'public/uploads/' . $review->product_image : $review->product_image); ?>" alt="" style="width: 30px; height: 30px; object-fit: cover;" class="mr-2 rounded">
                                        <span>Sản phẩm: <strong><?php echo htmlspecialchars($review->product_name); ?></strong></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="text-center py-5">
                        <i class="far fa-star fa-3x text-light mb-3"></i>
                        <p class="text-muted">Chưa có đánh giá nào cho shop này.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- About Tab -->
        <div class="tab-pane fade" id="about" role="tabpanel">
            <div class="shop-about-card p-4 bg-light rounded shadow-sm">
                <h4 class="mb-4">Thông tin chi tiết</h4>
                <div class="about-item mb-3">
                    <strong>Mô tả:</strong>
                    <p class="mt-2 text-secondary"><?php echo nl2br(htmlspecialchars($shop->description)); ?></p>
                </div>
                <hr>
                <div class="row mt-4">
                    <div class="col-md-6 mb-3">
                        <div class="d-flex align-items-center">
                            <div class="bg-white p-2 rounded-circle shadow-sm mr-3" style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;">
                                <i class="fas fa-map-marker-alt text-primary"></i>
                            </div>
                            <div>
                                <small class="text-muted d-block">Địa chỉ</small>
                                <strong><?php echo htmlspecialchars($shop->location ?? 'Đang cập nhật'); ?></strong>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="d-flex align-items-center">
                            <div class="bg-white p-2 rounded-circle shadow-sm mr-3" style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;">
                                <i class="fas fa-calendar-alt text-success"></i>
                            </div>
                            <div>
                                <small class="text-muted d-block">Ngày tham gia</small>
                                <strong><?php echo date('d/m/Y', strtotime($shop->created_at)); ?></strong>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Instagram Style Shop Profile */
.shop-profile-container {
    max-width: 935px;
    margin: 0 auto;
}

.shop-avatar-wrapper {
    position: relative;
    padding: 3px;
    background: linear-gradient(45deg, #f09433 0%, #e6683c 25%, #dc2743 50%, #cc2366 75%, #bc1888 100%);
    border-radius: 50%;
    display: inline-block;
}

.shop-avatar {
    width: 150px;
    height: 150px;
    object-fit: cover;
    border: 5px solid #fff;
    background: #fff;
}

.shop-avatar-placeholder {
    width: 150px;
    height: 150px;
    background: #fafafa;
    border: 5px solid #fff;
}

.shop-name {
    font-size: 28px;
    font-weight: 300;
    color: #262626;
}

.btn-follow {
    background-color: var(--primary-color);
    color: #fff;
    font-weight: 600;
    padding: 5px 24px;
    border-radius: 4px;
}

.btn-follow:hover {
    background-color: #357ebd;
    color: #fff;
}

.btn-following {
    background-color: #fff;
    color: #262626;
    border: 1px solid #dbdbdb;
    font-weight: 600;
    padding: 5px 24px;
    border-radius: 4px;
}

.btn-message {
    background-color: #fff;
    color: #262626;
    border: 1px solid #dbdbdb;
    font-weight: 600;
    padding: 5px 24px;
    border-radius: 4px;
}

.stat-item {
    font-size: 16px;
    color: #262626;
}

.stat-count {
    font-weight: 600;
}

.seller-name {
    font-size: 16px;
    font-weight: 600;
    color: #262626;
}

.bio-content {
    font-size: 16px;
    line-height: 24px;
    color: #262626;
}

.shop-tabs-wrapper .nav-tabs .nav-link {
    color: #8e8e8e;
    font-weight: 600;
    font-size: 12px;
    letter-spacing: 1px;
    padding: 15px 30px;
    border-top: 1px solid transparent;
    margin-top: -1px;
}

.shop-tabs-wrapper .nav-tabs .nav-link.active {
    color: #262626;
    border-top-color: #262626;
    background: transparent;
}

.product-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 20px rgba(0,0,0,0.08) !important;
}

@media (max-width: 768px) {
    .shop-avatar {
        width: 80px;
        height: 80px;
    }
    .shop-avatar-placeholder {
        width: 80px;
        height: 80px;
    }
    .shop-name {
        font-size: 22px;
    }
    .shop-stats {
        justify-content: space-around;
        border-top: 1px solid #efefef;
        border-bottom: 1px solid #efefef;
        padding: 10px 0;
    }
    .stat-item {
        margin-right: 0 !important;
        text-align: center;
        flex: 1;
        font-size: 14px;
    }
    .stat-count {
        display: block;
    }
    .shop-tabs-wrapper .nav-tabs .nav-link {
        padding: 15px 15px;
    }
}
</style>

<script>
$(document).ready(function() {
    $('#btnFollowShop').on('click', function() {
        const btn = $(this);
        const shopId = btn.data('id');
        
        btn.prop('disabled', true);
        
        $.ajax({
            url: '<?php echo BASE_URL; ?>index.php?url=Shop/follow/' + shopId,
            type: 'POST',
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    if (response.action === 'followed') {
                        btn.removeClass('btn-follow').addClass('btn-following').text('Đang theo dõi');
                    } else {
                        btn.removeClass('btn-following').addClass('btn-follow').text('Theo dõi');
                    }
                    $('#followerCount').text(new Intl.NumberFormat().format(response.followerCount));
                } else {
                    alert(response.message);
                    if (response.message.includes('đăng nhập')) {
                        window.location.href = '<?php echo BASE_URL; ?>index.php?url=Page/login';
                    }
                }
            },
            error: function() {
                alert('Có lỗi xảy ra, vui lòng thử lại.');
            },
            complete: function() {
                btn.prop('disabled', false);
            }
        });
    });

    // Follow logic is handled above.
    // Chat functionality is handled by the global .open-chat-with-product listener in chat_v2.js
});
</script>
