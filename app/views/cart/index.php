<?php include 'app/views/shares/header.php'; ?>
<style>
    /* Ẩn các nút điều chỉnh số lượng mặc định của trình duyệt */
    input[type=number]::-webkit-outer-spin-button,
    input[type=number]::-webkit-inner-spin-button {
      -webkit-appearance: none;
      appearance: none;
      margin: 0;
    }
    input[type=number] {
      -moz-appearance: textfield;   
      appearance: textfield;
    }
</style>
<div class="container my-5" style="min-height: 50vh;">
    <h1 class="mb-4" style="color: var(--primary-color); font-weight: 800; font-size: 2.5rem;">Giỏ hàng của bạn</h1>
    
    <?php if (isset($_SESSION['error_message'])): ?>
        <div class="alert alert-danger shadow-sm mb-4" style="border-radius: 10px; border-left: 5px solid #dc3545;">
            <i class="fas fa-exclamation-circle mr-2"></i> <?php echo $_SESSION['error_message']; unset($_SESSION['error_message']); ?>
        </div>
    <?php endif; ?>

    <?php if (empty($cart)): ?>
        <div class="alert alert-info text-center py-5 shadow-sm" style="border-radius: 15px; background-color: #fdfdfd; border-color: #eee;">
            <i class="fas fa-shopping-cart fa-4x text-muted mb-4"></i>
            <h4 class="mb-3" style="color: #555;">Giỏ hàng trống</h4>
            <p class="text-muted mb-4">Bạn chưa chọn mua sản phẩm nào.</p>
            <a href="<?php echo BASE_URL; ?>index.php?url=Product/index" class="btn btn-primary px-4 py-2" style="background-color: var(--primary-color); border: none; border-radius: 30px; font-weight: 500;">
                <i class="fas fa-arrow-left mr-2"></i> Quay lại mua sắm
            </a>
        </div>
    <?php else: ?>
        <form id="cartForm" action="<?php echo BASE_URL; ?>index.php?url=Cart/update" method="POST">
            <div class="table-responsive shadow-sm mb-4" style="border-radius: 15px; overflow: hidden;">
                <table class="table table-hover align-middle mb-0 bg-white">
                    <thead style="background-color: #fcfcfc;">
                        <tr>
                            <th class="border-0 py-3 text-center" style="width: 50px;">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" id="selectAll" checked>
                                    <label class="custom-control-label" for="selectAll"></label>
                                </div>
                            </th>
                            <th class="border-0 py-3">Sản phẩm</th>
                            <th class="border-0 py-3 text-center">Đơn giá</th>
                            <th class="border-0 py-3 text-center" style="width: 150px;">Số lượng</th>
                            <th class="border-0 py-3 text-right">Thành tiền</th>
                            <th class="border-0 py-3 text-center">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $totalAmount = 0;
                        foreach ($cart as $id => $item):
                            $subtotal = $item['price'] * $item['quantity'];
                            $totalAmount += $subtotal;
                        ?>
                            <tr class="cart-item-row" data-id="<?php echo $id; ?>">
                                <td class="py-4 text-center">
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" name="selected_items[]" value="<?php echo $id; ?>" class="custom-control-input item-checkbox" id="check-<?php echo $id; ?>" checked 
                                               data-price="<?php echo $item['price']; ?>" 
                                               data-qty="<?php echo $item['quantity']; ?>">
                                        <label class="custom-control-label" for="check-<?php echo $id; ?>"></label>
                                    </div>
                                </td>
                                <td class="py-3">
                                    <div class="d-flex align-items-center">
                                        <?php if (!empty($item['image'])): ?>
                                            <?php 
                                            $cartImg = $item['image'];
                                            $finalCartImg = (strpos($cartImg, 'public/') === false) ? 
                                                            ((strpos($cartImg, 'uploads/') !== false) ? 'public/' . $cartImg : 'public/uploads/' . $cartImg) : 
                                                            $cartImg;
                                            ?>
                                            <img src="<?php echo BASE_URL . htmlspecialchars($finalCartImg, ENT_QUOTES, 'UTF-8'); ?>" alt="Product" class="mr-3 rounded shadow-sm" style="width: 70px; height: 70px; object-fit: cover;">
                                        <?php else: ?>
                                            <div class="mr-3 bg-light d-flex align-items-center justify-content-center rounded shadow-sm" style="width: 70px; height: 70px;">
                                                <i class="fas fa-image text-muted fa-lg"></i>
                                            </div>
                                        <?php endif; ?>
                                        <h5 class="mb-0 mx-2" style="color: var(--primary-color); font-weight: bold; font-size: 1.4rem;"><?php echo htmlspecialchars($item['name'], ENT_QUOTES, 'UTF-8'); ?></h5>
                                    </div>
                                </td>
                                <td class="text-center py-3"><strong class="text-dark" style="font-weight: 600; font-size: 1.25rem;"><?php echo number_format($item['price'], 0, ',', '.'); ?> ₫</strong></td>
                                <td class="py-3">
                                    <div class="d-flex align-items-center justify-content-center">
                                        <span class="qty-btn" style="cursor: pointer; color: #471522ff; font-size: 0.9rem; padding: 0 8px; user-select: none;" onclick="changeQty('<?php echo $id; ?>', -1)">
                                            <i class="fas fa-minus"></i>
                                        </span>
                                        <input type="number" id="qty-<?php echo $id; ?>" name="quantities[<?php echo $id; ?>]" 
                                               class="form-control text-center mx-1 qty-input" data-id="<?php echo $id; ?>" 
                                               value="<?php echo $item['quantity']; ?>" min="1" 
                                               max="<?php echo $item['stock']; ?>"
                                               style="max-width: 60px; border-radius: 4px; height: 35px; border: 1px solid #ddd; padding: 0; font-size: 1.1rem; font-weight: bold;">
                                        <span class="qty-btn" style="cursor: pointer; color: #47091aff; font-size: 0.9rem; padding: 0 8px; user-select: none;" onclick="changeQty('<?php echo $id; ?>', 1)">
                                            <i class="fas fa-plus"></i>
                                        </span>
                                    </div>
                                </td>
                                <td class="text-right py-3"><strong class="text-danger subtotal-price" id="subtotal-<?php echo $id; ?>" style="font-size: 1.4rem;"><?php echo number_format($subtotal, 0, ',', '.'); ?> ₫</strong></td>
                                <td class="text-center py-3">
                                    <a href="<?php echo BASE_URL; ?>index.php?url=Cart/remove/<?php echo $id; ?>" class="btn btn-outline-danger btn-sm" style="border-radius: 20px; padding: 5px 15px;" onclick="return confirm('Bạn có chắc chắn muốn xóa khỏi giỏ?');">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <div class="row align-items-center bg-white p-4 shadow-sm" style="border-radius: 15px;">
                <div class="col-md-6 mb-3 mb-md-0 d-flex flex-wrap align-items-center" style="gap: 15px;">
                    <a href="<?php echo BASE_URL; ?>index.php?url=Product/index" class="btn btn-secondary px-4 py-2" style="border-radius: 30px; font-weight: 500;">
                        <i class="fas fa-arrow-left mr-1"></i> Tiếp tục mua sắm
                    </a>
                </div>
                <div class="col-md-6 text-md-right text-center">
                    <div class="d-inline-flex flex-column align-items-md-end align-items-center">
                        <div class="mb-3 d-flex align-items-baseline">
                            <span class="text-uppercase text-muted mr-3" style="font-size: 0.9rem; letter-spacing: 1px;">Tổng cộng (<span id="selected-count"><?php echo count($cart); ?></span>):</span>
                             <span class="text-danger font-weight-bold" id="grand-total" style="font-size: 2.5rem;"><?php echo number_format($totalAmount, 0, ',', '.'); ?> ₫</span>
                        </div>
                        <button type="button" id="btnCheckout" class="btn btn-success px-5 py-3 shadow" style="border-radius: 30px; font-weight: bold; font-size: 1.5rem; width: 100%; background-color: #28a745; border: none; text-decoration: none; color: white;">
                            <i class="fas fa-credit-card mr-2"></i> Đặt hàng
                        </button>
                    </div>
                </div>
            </div>
        </form>
    <?php endif; ?>
</div>
<script>
    function changeQty(id, delta) {
        const input = document.getElementById('qty-' + id);
        if (input) {
            let newVal = parseInt(input.value) + delta;
            const maxVal = parseInt(input.getAttribute('max') || 999);
            
            if (newVal < 1) newVal = 1;
            if (newVal > maxVal) {
                newVal = maxVal;
                alert('Rất tiếc, chỉ còn ' + maxVal + ' sản phẩm trong kho.');
            }
            input.value = newVal;
            updateCartServer(id, newVal);
        }
    }

    // Handle manual input change
    document.querySelectorAll('.qty-input').forEach(input => {
        input.addEventListener('change', function() {
            const id = this.getAttribute('data-id');
            const maxVal = parseInt(this.getAttribute('max') || 999);
            let quantity = parseInt(this.value);
            
            if (quantity > maxVal) {
                alert('Rất tiếc, chỉ còn ' + maxVal + ' sản phẩm trong kho.');
                quantity = maxVal;
                this.value = quantity;
            }
            
            if (quantity >= 1) {
                updateCartServer(id, quantity);
            } else {
                this.value = 1;
                updateCartServer(id, 1);
            }
        });
    });

    function updateCartServer(id, quantity) {
        const formData = new FormData();
        formData.append('id', id);
        formData.append('quantity', quantity);

        fetch('<?php echo BASE_URL; ?>index.php?url=Cart/updateAjax', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Update item subtotal
                const subtotalEl = document.getElementById('subtotal-' + id);
                if (subtotalEl) subtotalEl.innerText = data.itemSubtotal;

                // Update data-qty on checkbox for total calculation
                const checkbox = document.getElementById('check-' + id);
                if (checkbox) {
                    checkbox.setAttribute('data-qty', quantity);
                }

                // Update grand total based on CURRENT selection (not necessarily the full cart)
                updateSelectionTotal();

                // Update header cart count badge if exists
                const cartBadge = document.querySelector('.cart-btn-red .badge');
                if (cartBadge) {
                    cartBadge.innerText = data.cartCount;
                    if (data.cartCount <= 0) cartBadge.style.display = 'none';
                    else cartBadge.style.display = 'block';
                }
            } else {
                if (data.message) {
                    alert(data.message);
                    // Reload to sync with correct stock/quantity
                    location.reload();
                }
            }
        })
        .catch(error => console.error('Error updating cart:', error));
    }

    // --- SELECTION LOGIC ---
    const selectAll = document.getElementById('selectAll');
    const itemCheckboxes = document.querySelectorAll('.item-checkbox');
    const selectedCountDisp = document.getElementById('selected-count');
    const grandTotalDisp = document.getElementById('grand-total');

    if (selectAll) {
        selectAll.addEventListener('change', function() {
            itemCheckboxes.forEach(cb => cb.checked = this.checked);
            updateSelectionTotal();
        });
    }

    itemCheckboxes.forEach(cb => {
        cb.addEventListener('change', function() {
            if (!this.checked) selectAll.checked = false;
            else if (Array.from(itemCheckboxes).every(c => c.checked)) selectAll.checked = true;
            updateSelectionTotal();
        });
    });

    function updateSelectionTotal() {
        let total = 0;
        let count = 0;
        document.querySelectorAll('.item-checkbox:checked').forEach(cb => {
            const price = parseFloat(cb.getAttribute('data-price'));
            const qty = parseInt(cb.getAttribute('data-qty'));
            total += price * qty;
            count++;
        });
        grandTotalDisp.innerText = new Intl.NumberFormat('vi-VN').format(total) + ' ₫';
        selectedCountDisp.innerText = count;
    }

    // Checkout button logic - only proceed with selected IDs
    const btnCheckout = document.getElementById('btnCheckout');
    if (btnCheckout) {
        btnCheckout.addEventListener('click', function() {
            // Check if user is logged in
            const isLoggedIn = <?php echo isset($_SESSION['user_id']) ? 'true' : 'false'; ?>;
            
            if (!isLoggedIn) {
                $('#loginReminderModal').modal('show');
                return;
            }

            const selectedIds = Array.from(document.querySelectorAll('.item-checkbox:checked')).map(cb => cb.value);
            if (selectedIds.length === 0) {
                alert('Vui lòng chọn ít nhất một sản phẩm để thanh toán!');
                return;
            }
            // Pass selected IDs to checkout page
            window.location.href = '<?php echo BASE_URL; ?>index.php?url=Cart/checkout&ids=' + selectedIds.join(',');
        });
    }

    // Initial calculation
    updateSelectionTotal();
</script>

<?php include 'app/views/shares/footer.php'; ?>