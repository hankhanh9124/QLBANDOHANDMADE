<?php include 'app/views/shares/header.php'; ?>

<style>
.table-hover tbody tr:hover {
    background-color: #f1f8f4;
    cursor: pointer;
}
.custom-checkbox .custom-control-label::before {
    border-radius: 4px;
    width: 20px;
    height: 20px;
}
.custom-checkbox .custom-control-label::after {
    width: 20px;
    height: 20px;
}
.custom-control-input:checked ~ .custom-control-label::before {
    border-color: #75c794;
    background-color: #75c794;
}

/* Category sidebar */
.category-sidebar {
    position: sticky;
    top: 100px;
}
.category-sidebar .card {
    border-radius: 15px;
    border: none;
    box-shadow: 0 2px 12px rgba(0,0,0,0.08);
    overflow: hidden;
}
.category-sidebar .card-header {
    background: linear-gradient(135deg, #427e59 0%, #5da87a 100%);
    color: #fff;
    font-weight: 700;
    font-size: 16px;
    padding: 16px 20px;
    border: none;
}
.category-sidebar .card-header i {
    margin-right: 8px;
}
.category-item {
    display: flex;
    align-items: center;
    padding: 10px 18px;
    border-bottom: 1px solid #f0f0f0;
    transition: background 0.2s;
    cursor: pointer;
}
.category-item:last-child {
    border-bottom: none;
}
.category-item:hover {
    background-color: #f1f8f4;
}
.category-item input[type="checkbox"] {
    width: 18px;
    height: 18px;
    margin-right: 12px;
    accent-color: #75c794;
    cursor: pointer;
    flex-shrink: 0;
}
.category-item label {
    margin: 0;
    cursor: pointer;
    font-size: 14px;
    font-weight: 500;
    color: #333;
    flex: 1;
}
.category-item .badge-count {
    background: #e8f5e9;
    color: #427e59;
    font-size: 12px;
    padding: 3px 8px;
    border-radius: 10px;
    font-weight: 600;
}
.category-select-all {
    padding: 10px 18px;
    border-bottom: 2px solid #e8f5e9;
    background: #fafffe;
}
.category-select-all label {
    font-weight: 700;
    color: #427e59;
    font-size: 14px;
}
</style>

</div><!-- close header container -->
<div class="container-fluid mt-5 mb-5 pb-5" style="max-width: 1400px;">
    <form action="<?php echo BASE_URL; ?>index.php?url=Product/saveFeatured/<?php echo isset($section_id) ? $section_id : 'all_products'; ?>" method="POST" id="featuredForm">
    <div class="row">
        <!-- LEFT SIDEBAR: Category Selection -->
        <div class="col-lg-3 col-md-4 mb-4">
            <div class="category-sidebar">
                <div class="card">
                    <div class="card-header">
                        <i class="fas fa-layer-group"></i> Danh Mục Hiển Thị
                    </div>
                    <div class="card-body p-0">
                        <!-- Select All -->
                        <div class="category-select-all category-item">
                            <input type="checkbox" id="selectAllCats" onclick="toggleAllCategories(this)">
                            <label for="selectAllCats"><i class="fas fa-check-double mr-1"></i> Chọn tất cả</label>
                            <span id="visibleProductCount" class="badge-count" style="background: #fff3e0; color: #e65100; font-weight: 700;">0 sản phẩm</span>
                        </div>
                        
                        <?php if (!empty($allCategories)): ?>
                            <?php foreach ($allCategories as $cat): ?>
                                <?php 
                                    $catName = trim($cat->name);
                                    $isSelected = in_array($catName, $selectedCategoryNames ?? []);
                                    // Count products in this category
                                    $catProductCount = 0;
                                    foreach ($allProducts as $p) {
                                        if (trim($p->category_name) === $catName) $catProductCount++;
                                    }
                                ?>
                                <div class="category-item" onclick="toggleCatCheckbox(event, 'cat_<?php echo $cat->id; ?>')">
                                    <input type="checkbox" 
                                           class="cat-checkbox"
                                           id="cat_<?php echo $cat->id; ?>" 
                                           name="section_category_names[]" 
                                           value="<?php echo htmlspecialchars($catName); ?>" 
                                           <?php echo $isSelected ? 'checked' : ''; ?>
                                           onchange="filterProductsByCategory()">
                                    <label for="cat_<?php echo $cat->id; ?>"><?php echo htmlspecialchars($catName); ?></label>
                                    <span class="badge-count"><?php echo $catProductCount; ?></span>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="p-3 text-muted text-center">Không có danh mục nào.</div>
                        <?php endif; ?>
                    </div>
                    <div class="card-footer bg-white border-0 p-3">
                        <small class="text-muted"><i class="fas fa-info-circle mr-1"></i> Tích chọn danh mục để lọc sản phẩm hiển thị trong mục này.</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- RIGHT COLUMN: Product Table -->
        <div class="col-lg-9 col-md-8">
            <div class="card shadow-sm border-0" style="border-radius: 15px;">
                <div class="card-header bg-white border-0 pt-4 pb-0 text-center">
                    <div class="d-flex flex-column align-items-center mb-2">
                        <input type="text" name="page_title" value="<?php echo isset($currentTitle) ? htmlspecialchars($currentTitle) : 'SẢN PHẨM HANDMADE'; ?>" 
                               class="form-control text-center bg-transparent" 
                               style="font-weight: 900; color: #427e59; font-size: 32px; max-width: 600px; border: none; border-bottom: 2px dashed #75c794; border-radius: 0; padding: 0 10px; box-shadow: none !important; transition: all 0.3s;"
                               onfocus="this.style.backgroundColor='#f1f8f4';" onblur="this.style.backgroundColor='transparent';">
                        <small class="text-muted mt-2" style="font-size: 13px;"><i class="fas fa-edit" style="color: #f39c12;"></i> Tự do gõ chữ để đổi tên tiêu đề này (Nhớ bấm nút Lưu Thiết Lập ở dưới cùng)</small>
                    </div>
                    <p class="text-muted" style="font-size: 15px;">Chọn sản phẩm và điền số thứ tự ưu tiên để hiển thị ra ngoài trang chủ tại mục "<?php echo isset($currentTitle) ? htmlspecialchars($currentTitle) : 'TẤT CẢ SẢN PHẨM'; ?>".</p>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle" id="productTable">
                            <thead style="background-color: #f8f9fa;">
                                <tr>
                                    <th style="width: 60px; text-align: center;">Chọn</th>
                                    <th style="width: 100px; text-align: center;">Sắp xếp</th>
                                    <th style="width: 60px; text-align: center;">STT</th>
                                    <th style="width: 80px;">Hình ảnh</th>
                                    <th>Tên sản phẩm</th>
                                    <th>Danh mục</th>
                                    <th>Giá</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($allProducts)): ?>
                                    <?php $stt_count = 1; ?>
                                    <?php foreach ($allProducts as $p): ?>
                                        <?php 
                                            $pid = (string)$p->id;
                                            $isChecked = in_array($pid, $featuredIds) ? 'checked' : ''; 
                                            
                                            $orderVal = isset($featuredOrders[$pid]) ? (int)$featuredOrders[$pid] : 0;
                                        

                                            $imgSrc = $p->image;
                                            if (strpos($imgSrc, 'public/') === false) {
                                                $imgSrc = (strpos($imgSrc, 'uploads/') !== false) ? 'public/' . $imgSrc : 'public/uploads/' . $imgSrc;
                                            }
                                        ?>
                                        <tr data-category="<?php echo htmlspecialchars(trim($p->category_name)); ?>">
                                            <td class="text-center align-middle">
                                                <div class="custom-control custom-checkbox ml-2">
                                                    <input type="checkbox" class="custom-control-input checkbox-featured" 
                                                           id="check_<?php echo $p->id; ?>" 
                                                           name="featured_ids[]" 
                                                           value="<?php echo $p->id; ?>" <?php echo $isChecked; ?>>
                                                    <label class="custom-control-label" style="cursor: pointer;" for="check_<?php echo $p->id; ?>"></label>
                                                </div>
                                            </td>
                                            <td class="align-middle">
                                                <input type="number" name="orders[<?php echo $p->id; ?>]" value="<?php echo $orderVal; ?>" 
                                                       class="form-control form-control-sm mx-auto text-center order-input" 
                                                       style="width: 60px; font-weight: bold; border-radius: 6px;" min="0">
                                            </td>
                                            <td class="text-center align-middle font-weight-bold text-muted stt-cell"><?php echo $stt_count++; ?></td>
                                            <td class="align-middle">
                                                <?php if ($p->image): ?>
                                                <img src="<?php echo BASE_URL . $imgSrc; ?>" style="width: 50px; height: 50px; object-fit: cover; border-radius: 8px;">
                                                <?php else: ?>
                                                <div style="width: 50px; height: 50px; background: #eee; border-radius: 8px; display: flex; align-items: center; justify-content: center;"><i class="fas fa-image text-muted"></i></div>
                                                <?php endif; ?>
                                            </td>
                                            <td class="align-middle" style="font-weight: 500; font-size: 15px;"><?php echo htmlspecialchars($p->name); ?></td>
                                            <td class="align-middle"><span class="badge badge-info" style="background-color: #e3f2fd; color: #1976d2; padding: 6px 10px; font-size: 13px;"><?php echo htmlspecialchars($p->category_name); ?></span></td>
                                            <td class="align-middle" style="color: #ee225b; font-weight: bold; font-size: 15px;"><?php echo number_format($p->price, 0, ',', '.'); ?>đ</td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr><td colspan="7" class="text-center py-4">Không có sản phẩm nào.</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                    <div class="d-flex justify-content-end mt-4 pt-3 border-top">
                        <a href="<?php echo BASE_URL; ?>index.php?url=Product/" class="btn btn-secondary mr-3" style="border-radius: 8px; padding: 10px 25px; font-weight: 600; background-color: #e0e0e0; border: none; color: #555;">Về trang chủ   </a>
                        <button type="submit" class="btn btn-primary" style="background-color: #75c794; border: none; border-radius: 8px; padding: 10px 30px; font-weight: bold; box-shadow: 0 4px 10px rgba(117,199,148,0.3); transition: all 0.3s;">
                            <i class="fas fa-save mr-2"></i> Lưu
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initial filter on page load
    filterProductsByCategory();
    updateSelectAllState();

    // 1. Row click -> Toggle Checkbox
    const rows = document.querySelectorAll('#productTable tbody tr');
    rows.forEach(row => {
        row.addEventListener('click', function(e) {
            const tagName = e.target.tagName.toLowerCase();
            if (tagName === 'input' || tagName === 'label' || tagName === 'a' || tagName === 'td') {
                if (tagName === 'td' && e.target.querySelector('input')) {
                    // pass
                } else if (tagName !== 'td') {
                    return;
                }
            }
            
            const checkbox = this.querySelector('.checkbox-featured');
            if (checkbox && tagName === 'td') {
                checkbox.checked = !checkbox.checked;
                checkbox.dispatchEvent(new Event('change'));
            }
        });
    });

    // 2. Auto order logic when checkbox changes
    const checkboxes = document.querySelectorAll('.checkbox-featured');
    checkboxes.forEach(cb => {
        cb.addEventListener('change', function() {
            const row = this.closest('tr');
            const orderInput = row.querySelector('.order-input');
            
            if (this.checked) {
                if (orderInput.value == 0 || orderInput.value === '') {
                    let maxNum = 0;
                    document.querySelectorAll('.order-input').forEach(input => {
                        const val = parseInt(input.value) || 0;
                        if (val > maxNum) {
                            maxNum = val;
                        }
                    });
                    orderInput.value = maxNum + 1;
                }
            } else {
                const removedValue = parseInt(orderInput.value) || 0;
                orderInput.value = 0;
                
                if (removedValue > 0) {
                    document.querySelectorAll('.order-input').forEach(input => {
                        const val = parseInt(input.value) || 0;
                        if (val > removedValue) {
                            input.value = val - 1;
                        }
                    });
                }
            }
        });
    });
});

// Filter products by selected categories
function filterProductsByCategory() {
    const checkedCats = [];
    document.querySelectorAll('.cat-checkbox:checked').forEach(cb => {
        checkedCats.push(cb.value.trim());
    });

    const rows = document.querySelectorAll('#productTable tbody tr');
    let visibleCount = 0;
    rows.forEach(row => {
        const rowCat = (row.getAttribute('data-category') || '').trim();
        if (checkedCats.length === 0) {
            row.style.display = 'none';
        } else {
            const isVisible = checkedCats.includes(rowCat);
            row.style.display = isVisible ? '' : 'none';
            if (isVisible) visibleCount++;
        }
    });

    // Đánh lại STT theo thứ tự hiển thị
    let stt = 1;
    rows.forEach(row => {
        if (row.style.display !== 'none') {
            const sttCell = row.querySelector('.stt-cell');
            if (sttCell) sttCell.textContent = stt++;
        }
    });

    // Cập nhật số sản phẩm hiện có
    const countBadge = document.getElementById('visibleProductCount');
    if (countBadge) {
        countBadge.textContent = visibleCount + ' sản phẩm';
    }
    
    updateSelectAllState();
}

// Toggle category checkbox from the item click
function toggleCatCheckbox(event, checkboxId) {
    if (event.target.tagName.toLowerCase() === 'input') return;
    const cb = document.getElementById(checkboxId);
    if (cb) {
        cb.checked = !cb.checked;
        filterProductsByCategory();
    }
}

// Select/Deselect all categories
function toggleAllCategories(masterCheckbox) {
    const catCheckboxes = document.querySelectorAll('.cat-checkbox');
    catCheckboxes.forEach(cb => {
        cb.checked = masterCheckbox.checked;
    });
    filterProductsByCategory();
}

// Update select-all checkbox state
function updateSelectAllState() {
    const allCbs = document.querySelectorAll('.cat-checkbox');
    const checkedCbs = document.querySelectorAll('.cat-checkbox:checked');
    const selectAll = document.getElementById('selectAllCats');
    if (selectAll) {
        selectAll.checked = allCbs.length > 0 && allCbs.length === checkedCbs.length;
        selectAll.indeterminate = checkedCbs.length > 0 && checkedCbs.length < allCbs.length;
    }
}
</script>

<div class="container">
<?php include 'app/views/shares/footer.php'; ?>
