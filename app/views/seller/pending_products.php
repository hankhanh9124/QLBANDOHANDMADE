<?php include 'app/views/dashboard/header.php'; ?>

<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Sản phẩm chờ duyệt</h1>

    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Danh sách sản phẩm chưa được duyệt</h6>
            <a href="<?php echo BASE_URL; ?>index.php?url=Product/add" class="btn btn-primary btn-sm shadow-sm">
                <i class="fas fa-plus fa-sm text-white-50"></i> Đăng sản phẩm mới
            </a>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Hình ảnh</th>
                            <th>Tên sản phẩm</th>
                            <th>Giá</th>
                            <th>Trạng thái</th>
                            <th>Ngày đăng</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($products)): ?>
                            <?php foreach ($products as $product): ?>
                                <tr>
                                    <td><?php echo $product->id; ?></td>
                                    <td>
                                        <?php if ($product->image): ?>
                                            <img src="public/uploads/<?php echo $product->image; ?>" alt="<?php echo $product->name; ?>" style="width: 50px; height: 50px; object-fit: cover; border-radius: 4px;">
                                        <?php else: ?>
                                            <div class="bg-gray-200 text-center py-2" style="width: 50px; height: 50px; border-radius: 4px;"><i class="fas fa-image text-gray-400"></i></div>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo $product->name; ?></td>
                                    <td><?php echo number_format($product->price, 0, ',', '.'); ?> ₫</td>
                                    <td>
                                        <span class="badge badge-warning">Đang chờ duyệt</span>
                                    </td>
                                    <td><?php echo !empty($product->created_at) ? date('d/m/Y H:i', strtotime($product->created_at)) : 'Chưa cập nhật'; ?></td>
                                    <td>
                                        <a href="<?php echo BASE_URL; ?>index.php?url=Product/edit/<?php echo $product->id; ?>" class="btn btn-sm btn-info" title="Sửa">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" class="text-center">Không có sản phẩm nào đang chờ duyệt.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include 'app/views/dashboard/footer.php'; ?>
