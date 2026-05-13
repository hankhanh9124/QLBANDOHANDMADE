<?php 
$action = 'categories';
include 'app/views/dashboard/header.php'; 
?>

<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">Quản lý danh mục</h2>
        <button class="btn btn-primary" data-toggle="modal" data-target="#addCategoryModal">
            <i class="fas fa-plus mr-1"></i> Thêm danh mục
        </button>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <table class="table table-hover mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="border-0 px-4">ID</th>
                        <th class="border-0">Tên danh mục</th>
                        <th class="border-0">Mô tả</th>
                        <th class="border-0 px-4 text-right">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($categories)): ?>
                        <?php foreach ($categories as $cat): ?>
                            <tr>
                                <td class="px-4"><?php echo $cat->id; ?></td>
                                <td><strong><?php echo htmlspecialchars($cat->name); ?></strong></td>
                                <td class="text-muted"><?php echo htmlspecialchars($cat->description); ?></td>
                                <td class="px-4 text-right">
                                    <button class="btn btn-sm btn-info mr-1 edit-cat-btn" 
                                            data-id="<?php echo $cat->id; ?>" 
                                            data-name="<?php echo htmlspecialchars($cat->name); ?>" 
                                            data-desc="<?php echo htmlspecialchars($cat->description); ?>"
                                            title="Sửa">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <a href="<?php echo BASE_URL; ?>index.php?url=Dashboard/deleteCategory/<?php echo $cat->id; ?>" 
                                       class="btn btn-sm btn-danger" 
                                       onclick="return confirm('Xác nhận xóa danh mục này? Các sản phẩm trong danh mục này có thể bị ảnh hưởng.');" 
                                       title="Xóa">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4" class="text-center py-5 text-muted">
                                <i class="fas fa-folder-open fa-3x mb-3 opacity-25"></i>
                                <p>Chưa có danh mục nào trong hệ thống.</p>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Add Category Modal -->
<div class="modal fade" id="addCategoryModal" tabindex="-1">
    <div class="modal-dialog">
        <form action="<?php echo BASE_URL; ?>index.php?url=Dashboard/addCategory" method="POST">
            <div class="modal-content border-0 shadow">
                <div class="modal-header">
                    <h5 class="modal-title font-weight-bold">Thêm danh mục mới</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label class="font-weight-bold">Tên danh mục</label>
                        <input type="text" name="name" class="form-control" placeholder="Nhập tên danh mục..." required>
                    </div>
                    <div class="form-group">
                        <label class="font-weight-bold">Mô tả</label>
                        <textarea name="description" class="form-control" rows="3" placeholder="Mô tả ngắn gọn về danh mục..."></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light border-0">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-primary px-4">Lưu danh mục</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Edit Category Modal -->
<div class="modal fade" id="editCategoryModal" tabindex="-1">
    <div class="modal-dialog">
        <form id="editCategoryForm" method="POST">
            <div class="modal-content border-0 shadow">
                <div class="modal-header">
                    <h5 class="modal-title font-weight-bold">Chỉnh sửa danh mục</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label class="font-weight-bold">Tên danh mục</label>
                        <input type="text" name="name" id="edit_name" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label class="font-weight-bold">Mô tả</label>
                        <textarea name="description" id="edit_description" class="form-control" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light border-0">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-info px-4">Cập nhật thay đổi</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Handle Edit Button Clicks
    const editBtns = document.querySelectorAll('.edit-cat-btn');
    const editForm = document.getElementById('editCategoryForm');
    const editName = document.getElementById('edit_name');
    const editDesc = document.getElementById('edit_description');

    editBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            const name = this.getAttribute('data-name');
            const desc = this.getAttribute('data-desc');

            editName.value = name;
            editDesc.value = desc;
            editForm.action = '<?php echo BASE_URL; ?>index.php?url=Dashboard/updateCategory/' + id;
            
            $('#editCategoryModal').modal('show');
        });
    });
});
</script>

<?php include 'app/views/dashboard/footer.php'; ?>
