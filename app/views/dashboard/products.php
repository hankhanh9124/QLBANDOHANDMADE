<?php 
$action = 'products';
include 'app/views/dashboard/header.php'; 
?>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div class="d-flex flex-column">
            <h2 class="mb-0">
                <?php 
                    $sort = $_GET['sort'] ?? 'id';
                    if ($sort === 'sold_only') echo 'Sản phẩm đã có lượt bán';
                    elseif ($sort === 'sold') echo 'Sản phẩm bán chạy nhất';
                    else echo 'Danh sách sản phẩm';
                ?>
            </h2>
            <?php if (!empty($search)): ?>
                <div class="mt-2 text-muted small">
                    <i class="fas fa-filter mr-1 text-primary"></i> Đang hiển thị kết quả cho: <strong>"<?php echo htmlspecialchars($search ?? ''); ?>"</strong>
                    <a href="<?php echo BASE_URL; ?>index.php?url=Dashboard/products" class="ml-2 text-danger font-weight-bold">
                        <i class="fas fa-times-circle"></i> Xóa bộ lọc
                    </a>
                </div>
            <?php endif; ?>
        </div>
        
        <form action="<?php echo BASE_URL; ?>index.php" method="GET" class="d-flex align-items-center">
            <input type="hidden" name="url" value="Dashboard/products">
            <div class="position-relative">
                <input type="text" name="search" class="form-control rounded-pill border-0 shadow-sm px-4" 
                       style="width: 350px; height: 45px;" 
                       placeholder="Tìm kiếm sản phẩm..." 
                       value="<?php echo htmlspecialchars($search ?? ''); ?>"
                       required>
                <button type="submit" class="btn position-absolute" style="right: 15px; top: 10px; color: #6c757d; background: none; border: none; padding: 0;">
                    <i class="fas fa-search"></i>
                </button>
            </div>
        </form>

        <a href="<?php echo BASE_URL; ?>index.php?url=Product/add" class="btn btn-primary px-4 py-2 shadow-sm rounded-pill">
            <i class="fas fa-plus mr-2"></i> Thêm sản phẩm
        </a>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="border-0 px-4 text-center">STT</th>
                            <th class="border-0 px-4">Ảnh</th>
                            <th class="border-0">
                                <?php echo ($sort === 'sold_only') ? 'Sản phẩm' : 'Tên sản phẩm'; ?>
                            </th>
                            <?php if ($sort === 'sold_only'): ?>
                                <th class="border-0">Người đăng</th>
                                <th class="border-0">Người mua</th>
                                <th class="border-0">Ngày bán</th>
                                <th class="border-0 text-right">Đơn giá</th>
                                <th class="border-0 text-center">SL</th>
                                <th class="border-0" style="width: 250px;">Địa chỉ</th>
                            <?php else: ?>
                                <th class="border-0">Người đăng</th>
                                <th class="border-0">Giá</th>
                                <th class="border-0">Tồn kho</th>
                                <th class="border-0 text-danger"><i class="fas fa-fire mr-1"></i>Đã bán</th>
                            <?php endif; ?>
                            <th class="border-0 px-4 text-right">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($products)): ?>
                            <?php $stt = 1; ?>
                            <?php foreach ($products as $product): ?>
                                <tr>
                                    <td class="px-4 text-center align-middle font-weight-bold text-muted"><?php echo $stt++; ?></td>
                                    <td class="px-4 align-middle">
                                        <?php 
                                            // Handle both product list data and sold report data
                                            $image = ($sort === 'sold_only') ? $product->product_image : $product->image;
                                            $name = ($sort === 'sold_only') ? $product->product_name : $product->name;
                                        ?>
                                        <a href="<?php echo BASE_URL; ?>public/uploads/<?php echo $image; ?>" target="_blank" title="Xem ảnh gốc">
                                            <img src="<?php echo BASE_URL; ?>public/uploads/<?php echo $image; ?>" style="width: 50px; height: 50px; object-fit: contain; background: #f8f9fa; border-radius: 4px;" alt="p">
                                        </a>
                                    </td>
                                    <td class="align-middle"><?php echo htmlspecialchars($name ?? ''); ?></td>
                                    
                                    <?php if ($sort === 'sold_only'): ?>
                                        <td class="align-middle">
                                            <div class="small font-weight-bold"><?php echo htmlspecialchars($product->seller_name ?? 'Hệ thống'); ?></div>
                                        </td>
                                        <td class="align-middle">
                                            <div class="font-weight-bold"><?php echo htmlspecialchars($product->recipient_name ?? ''); ?></div>
                                            <div class="small text-muted"><?php echo htmlspecialchars($product->recipient_phone ?? ''); ?></div>
                                        </td>
                                        <td class="align-middle">
                                            <div class="small"><?php echo date('H:i', strtotime($product->sale_date)); ?></div>
                                            <div class="small font-weight-bold"><?php echo date('d/m/Y', strtotime($product->sale_date)); ?></div>
                                        </td>
                                        <td class="align-middle text-right">
                                            <?php echo number_format($product->sold_price, 0, ',', '.'); ?> ₫
                                            <?php if ((float)$product->sold_price >= 55000): ?>
                                                <img src="<?php echo BASE_URL; ?>public/images/freeship_new.png" alt="FREE" title="Miễn phí vận chuyển" style="height: 22px; width: auto; vertical-align: middle; margin-left: 5px;">
                                            <?php endif; ?>
                                        </td>
                                        <td class="align-middle text-center font-weight-bold">x<?php echo $product->quantity; ?></td>
                                        <td class="align-middle">
                                            <div class="address-container" style="font-size: 0.85rem;">
                                                <span class="address-text" style="display: -webkit-box; -webkit-line-clamp: 1; line-clamp: 1; -webkit-box-orient: vertical; overflow: hidden;">
                                                    <?php echo htmlspecialchars($product->recipient_address ?? ''); ?>
                                                </span>
                                                <a href="javascript:void(0)" class="toggle-address btn-link" style="color: #007bff; font-weight: 500; font-size: 0.8rem;" onclick="toggleAddress(this)">Xem thêm</a>
                                            </div>
                                        </td>
                                    <?php else: ?>
                                        <td class="align-middle">
                                            <div class="small font-weight-bold text-primary"><?php echo htmlspecialchars($product->seller_name ?? 'Hệ thống'); ?></div>
                                        </td>
                                        <td class="align-middle">
                                            <?php echo number_format($product->price, 0, ',', '.'); ?> ₫
                                            <?php if ((float)$product->price >= 55000): ?>
                                                <img src="<?php echo BASE_URL; ?>public/images/freeship_new.png" alt="FREE" title="Miễn phí vận chuyển" style="height: 22px; width: auto; vertical-align: middle; margin-left: 5px;">
                                            <?php endif; ?>
                                        </td>
                                        <td class="align-middle"><?php echo $product->stock; ?></td>
                                        <td class="align-middle font-weight-bold text-danger"><?php echo number_format($product->sold ?? 0); ?></td>
                                    <?php endif; ?>

                                    <td class="px-4 text-right align-middle">
                                        <?php if ($sort === 'sold_only'): ?>
                                            <a href="<?php echo BASE_URL; ?>index.php?url=Dashboard/orders" class="btn btn-sm btn-outline-primary" title="Xem đơn hàng">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        <?php else: ?>
                                            <?php 
                                            $currentUserId = $_SESSION['user_id'] ?? 0;
                                            $isOwner = (isset($product->user_id) && (int)$product->user_id === (int)$currentUserId);
                                            ?>
                                            <?php if ($isOwner): ?>
                                                <a href="<?php echo BASE_URL; ?>index.php?url=Product/edit/<?php echo $product->id; ?>" class="btn btn-sm btn-info mr-1" title="Sửa">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <a href="<?php echo BASE_URL; ?>index.php?url=Product/delete/<?php echo $product->id; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Xóa sản phẩm này?');" title="Xóa">
                                                    <i class="fas fa-trash"></i>
                                                </a>
                                            <?php elseif ($_SESSION['user_role'] === 'admin'): ?>
                                                <a href="javascript:void(0)" class="btn btn-sm btn-outline-danger" onclick="adminDeleteProduct(<?php echo $product->id; ?>)" title="Gỡ bỏ sản phẩm (Vi phạm)">
                                                    <i class="fas fa-ban"></i>
                                                </a>
                                            <?php else: ?>
                                                <span class="badge badge-light text-muted"><i class="fas fa-lock mr-1"></i>Chế độ xem</span>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="8" class="text-center py-5 text-muted">
                                    <i class="fas fa-search-minus fa-3x mb-3 d-block opacity-50"></i>
                                    <?php echo ($sort === 'sold_only') ? 'Hiện tại chưa có sản phẩm nào được bán.' : 'Chưa có sản phẩm nào phù hợp với từ khóa của bạn.'; ?>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
function toggleAddress(btn) {
    const container = btn.parentElement.querySelector('.address-text');
    if (container.style.webkitLineClamp === '1' || container.style.display === '-webkit-box') {
        container.style.display = 'block';
        container.style.webkitLineClamp = 'unset';
        btn.innerText = 'Thu gọn';
    } else {
        container.style.display = '-webkit-box';
        container.style.webkitLineClamp = '1';
        btn.innerText = 'Xem thêm';
    }
}

function adminDeleteProduct(id) {
    const reason = prompt("Lý do gỡ bỏ sản phẩm này (Seller sẽ nhìn thấy lý do này):");
    if (reason === null) return; // User cancelled
    
    if (reason.trim() === "") {
        alert("Bạn phải nhập lý do gỡ bỏ.");
        return;
    }
    
    // Create a form to submit POST request
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = '<?php echo BASE_URL; ?>index.php?url=Product/delete/' + id;
    
    const reasonInput = document.createElement('input');
    reasonInput.type = 'hidden';
    reasonInput.name = 'rejection_reason';
    reasonInput.value = reason;
    
    form.appendChild(reasonInput);
    document.body.appendChild(form);
    form.submit();
}
</script>

<?php include 'app/views/dashboard/footer.php'; ?>
