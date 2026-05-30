<?php
// If action is already set in controller, don't overwrite it
if (!isset($action)) $action = 'my_products';
include 'app/views/dashboard/header.php';
?>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <?php echo ($action === 'pending_products') ? 'Sản phẩm chờ duyệt' : 'Sản phẩm của tôi'; ?>
        </h1>
        <a href="<?php echo BASE_URL; ?>index.php?url=Product/add" class="btn btn-primary">
            <i class="fas fa-plus"></i> Đăng sản phẩm mới
        </a>
    </div>

    <?php if (isset($_SESSION['success_message'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?php echo $_SESSION['success_message'];
            unset($_SESSION['success_message']); ?>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    <?php endif; ?>

    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover" id="dataTable" width="100%" cellspacing="0">
                    <thead class="thead-light">
                        <tr>
                            <th>Ảnh</th>
                            <th>Tên sản phẩm</th>
                            <th>Giá</th>
                            <th>Kho</th>
                            <th>Trạng thái</th>
                            <th class="text-center">Lượt thích</th>
                            <th>Ngày tạo</th>
                            <th>Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($products)): ?>
                            <tr>
                                <td colspan="7" class="text-center">Bạn chưa đăng sản phẩm nào.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($products as $product): ?>
                                <tr>
                                    <td>
                                        <?php
                                        $pImg = $product->image;
                                        $finalPImg = (strpos($pImg, 'public/') === false) ?
                                            ((strpos($pImg, 'uploads/') !== false) ? 'public/' . $pImg : 'public/uploads/' . $pImg) :
                                            $pImg;
                                        ?>
                                        <img src="<?php echo BASE_URL . $finalPImg; ?>" alt="<?php echo $product->name; ?>" style="width: 50px; height: 50px; object-fit: cover; border-radius: 4px;">
                                    </td>
                                    <td>
                                        <strong><?php echo htmlspecialchars($product->name); ?></strong>
                                    </td>
                                    <td class="text-danger font-weight-bold"><?php echo number_format($product->price, 0, ',', '.'); ?> ₫</td>
                                    <td><?php echo $product->stock; ?></td>
                                    <td>
                                        <?php if ($product->status === 'approved'): ?>
                                            <span class="badge badge-success"><i class="fas fa-check-circle"></i> Đã duyệt</span>
                                        <?php elseif ($product->status === 'pending'): ?>
                                            <span class="badge badge-warning text-dark"><i class="fas fa-clock"></i> Chờ duyệt</span>
                                        <?php elseif ($product->status === 'rejected'): ?>
                                            <span class="badge badge-danger"><i class="fas fa-times-circle"></i> Bị từ chối</span>
                                            <?php if (!empty($product->rejection_reason)): ?>
                                                <div class="small text-danger mt-1" style="max-width: 200px; line-height: 1.2;">
                                                    <strong>Lý do:</strong> <?php echo htmlspecialchars($product->rejection_reason); ?>
                                                </div>
                                            <?php endif; ?>
                                        <?php else: ?>
                                            <span class="badge badge-secondary"><?php echo $product->status; ?></span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge badge-pill shadow-sm" style="background-color: #fce4ec; color: #d81b60; padding: 5px 12px;">
                                            <i class="fas fa-heart mr-1"></i> <?php echo number_format($product->likes ?? 0); ?>
                                        </span>
                                    </td>
                                    <td><?php echo !empty($product->created_at) ? date('d/m/Y', strtotime($product->created_at)) : 'Chưa cập nhật'; ?></td>
                                    <td>
                                        <div class="btn-group">
                                            <a href="<?php echo BASE_URL; ?>index.php?url=Product/show/<?php echo $product->id; ?>" target="_blank" class="btn btn-outline-success btn-sm">
                                                <i class="fas fa-eye"></i> Xem
                                            </a>
                                            <a href="<?php echo BASE_URL; ?>index.php?url=Product/edit/<?php echo $product->id; ?>" class="btn btn-outline-primary btn-sm">
                                                <i class="fas fa-edit"></i> Sửa
                                            </a>
                                            <a href="<?php echo BASE_URL; ?>index.php?url=Product/delete/<?php echo $product->id; ?>" class="btn btn-outline-danger btn-sm" onclick="return confirm('Bạn có chắc muốn xóa sản phẩm này?')">
                                                <i class="fas fa-trash"></i> Xóa
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include 'app/views/dashboard/footer.php'; ?>