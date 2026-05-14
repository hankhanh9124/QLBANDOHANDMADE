<?php include 'app/views/dashboard/header.php'; ?>

<h2 class="mb-4">Quản lý Shop</h2>

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

<div class="card shadow-sm border-0" style="border-radius: 10px;">
    <div class="card-body">
        <?php if (empty($shops)): ?>
            <div class="text-center p-5">
                <i class="fas fa-store-slash text-muted mb-3" style="font-size: 3rem; opacity: 0.5;"></i>
                <h5 class="text-muted">Chưa có cửa hàng nào trên hệ thống.</h5>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="bg-light">
                        <tr>
                            <th class="border-0">Logo</th>
                            <th class="border-0">Thông tin Shop</th>
                            <th class="border-0">Chủ cửa hàng</th>
                            <th class="border-0 text-center">Trạng thái</th>
                            <th class="border-0 text-right">Ngày tạo</th>
                            <th class="border-0 text-right">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($shops as $shop): ?>
                            <tr>
                                <td>
                                    <?php $logoUrl = !empty($shop->logo) ? (strpos($shop->logo, 'http') === 0 ? $shop->logo : BASE_URL . $shop->logo) : BASE_URL . 'public/images/logolen.jpg'; ?>
                                    <img src="<?php echo $logoUrl; ?>" class="rounded-circle shadow-sm" style="width: 50px; height: 50px; object-fit: cover;">
                                </td>
                                <td>
                                    <strong style="color: var(--primary-color); font-size: 1.1rem;"><?php echo htmlspecialchars($shop->name); ?></strong><br>
                                    <small class="text-muted text-truncate d-inline-block" style="max-width: 250px;"><?php echo htmlspecialchars($shop->description ?? 'Không có mô tả'); ?></small>
                                </td>
                                <td>
                                    <strong><?php echo htmlspecialchars($shop->seller_name); ?></strong><br>
                                    <small class="text-muted"><a href="mailto:<?php echo htmlspecialchars($shop->seller_email ?? ''); ?>"><?php echo htmlspecialchars($shop->seller_email ?? ''); ?></a></small>
                                </td>
                                <td class="text-center">
                                    <?php if ($shop->status === 'active'): ?>
                                        <span class="badge badge-success px-3 py-2" style="border-radius: 20px;">Hoạt động</span>
                                    <?php elseif ($shop->status === 'suspended'): ?>
                                        <span class="badge badge-danger px-3 py-2" style="border-radius: 20px;">Bị đình chỉ</span>
                                    <?php else: ?>
                                        <span class="badge badge-secondary px-3 py-2" style="border-radius: 20px;"><?php echo ucfirst($shop->status); ?></span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-right text-muted small">
                                    <?php echo date('d/m/Y', strtotime($shop->created_at)); ?>
                                </td>
                                <td class="text-right">
                                    <a href="<?php echo BASE_URL; ?>index.php?url=Shop/profile/<?php echo $shop->id ?: $shop->seller_id; ?>" target="_blank" class="btn btn-sm btn-info" title="Xem Shop">
                                        <i class="fas fa-external-link-alt"></i>
                                    </a>
                                    
                                    <?php if ($shop->status === 'active'): ?>
                                        <a href="<?php echo BASE_URL; ?>index.php?url=Dashboard/updateShopStatus/<?php echo $shop->id; ?>/suspended" 
                                           class="btn btn-sm btn-outline-danger ml-1" 
                                           onclick="return confirm('Đình chỉ hoạt động cửa hàng này? (Sản phẩm của họ sẽ không hiển thị trên trang chủ)');"
                                           title="Đình chỉ">
                                            <i class="fas fa-ban"></i>
                                        </a>
                                    <?php else: ?>
                                        <a href="<?php echo BASE_URL; ?>index.php?url=Dashboard/updateShopStatus/<?php echo $shop->id; ?>/active" 
                                           class="btn btn-sm btn-outline-success ml-1" 
                                           onclick="return confirm('Mở lại hoạt động cho cửa hàng này?');"
                                           title="Mở khóa">
                                            <i class="fas fa-unlock"></i>
                                        </a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include 'app/views/dashboard/footer.php'; ?>
