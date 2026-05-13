<?php include 'app/views/dashboard/header.php'; ?>

<h2 class="mb-4">Duyệt thông tin Shop</h2>

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
        <?php if (empty($updates)): ?>
            <div class="text-center p-5">
                <i class="fas fa-check-circle text-success mb-3" style="font-size: 3rem;"></i>
                <h5>Không có yêu cầu cập nhật thông tin shop nào.</h5>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="bg-light">
                        <tr>
                            <th>Cửa hàng</th>
                            <th>Thông tin cũ</th>
                            <th>Thông tin mới</th>
                            <th>Hình ảnh mới</th>
                            <th>Ngày gửi</th>
                            <th class="text-right">Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($updates as $req): ?>
                            <tr>
                                <td>
                                    <strong><?php echo htmlspecialchars($req->old_name); ?></strong><br>
                                    <small class="text-muted">Chủ shop: <?php echo htmlspecialchars($req->seller_name); ?></small>
                                </td>
                                <td>Tên: <?php echo htmlspecialchars($req->old_name); ?></td>
                                <td>
                                    <strong>Tên mới:</strong> <?php echo htmlspecialchars($req->new_name); ?><br>
                                    <small><strong>Mô tả mới:</strong> <?php echo htmlspecialchars($req->new_description ?? 'Không có'); ?></small>
                                </td>
                                <td>
                                    <?php if ($req->new_logo): ?>
                                        <a href="<?php echo BASE_URL . $req->new_logo; ?>" target="_blank" class="badge badge-info">Xem Logo mới</a>
                                    <?php endif; ?>
                                    <?php if ($req->new_banner): ?>
                                        <a href="<?php echo BASE_URL . $req->new_banner; ?>" target="_blank" class="badge badge-primary">Xem Banner mới</a>
                                    <?php endif; ?>
                                    <?php if (!$req->new_logo && !$req->new_banner): ?>
                                        <span class="text-muted small">Không đổi ảnh</span>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo date('d/m/Y H:i', strtotime($req->created_at)); ?></td>
                                <td class="text-right">
                                    <a href="<?php echo BASE_URL; ?>index.php?url=Dashboard/approveShopUpdate/<?php echo $req->id; ?>" 
                                       class="btn btn-sm btn-success shadow-sm mb-1" 
                                       onclick="return confirm('Bạn chắc chắn muốn duyệt yêu cầu này?');">
                                        <i class="fas fa-check"></i> Duyệt
                                    </a>
                                    <a href="<?php echo BASE_URL; ?>index.php?url=Dashboard/rejectShopUpdate/<?php echo $req->id; ?>" 
                                       class="btn btn-sm btn-danger shadow-sm mb-1"
                                       onclick="return confirm('Bạn chắc chắn muốn từ chối yêu cầu này?');">
                                        <i class="fas fa-times"></i> Từ chối
                                    </a>
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
