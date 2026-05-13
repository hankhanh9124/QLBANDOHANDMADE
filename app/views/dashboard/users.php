<?php
$action = 'users';
include 'app/views/dashboard/header.php';
?>

<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">Quản lý người dùng</h2>
        <button class="btn btn-primary" data-toggle="modal" data-target="#addEmployeeModal">
            <i class="fas fa-user-plus mr-1"></i> Thêm nhân viên
        </button>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <table class="table table-hover mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="border-0 px-4">ID</th>
                        <th class="border-0">Tên người dùng</th>
                        <th class="border-0">Email</th>
                        <th class="border-0">Số điện thoại</th>
                        <th class="border-0">Vai trò</th>
                        <th class="border-0 px-4 text-right">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($users)): ?>
                        <?php foreach ($users as $user): ?>
                            <tr>
                                <td class="px-4 align-middle">
                                    <?php
                                    $prefix = 'US-';
                                    if (($user->role ?? '') === 'admin') $prefix = 'AD-';
                                    elseif (($user->role ?? '') === 'seller') $prefix = 'SL-';
                                    ?>
                                    <span class="badge badge-light text-muted font-weight-normal"><?php echo $prefix . $user->id; ?></span>
                                </td>
                                <td class="px-4">
                                    <div class="d-flex align-items-center">
                                        <div class="bg-secondary text-white rounded-circle d-flex align-items-center justify-content-center mr-3" style="width: 35px; height: 35px;">
                                            <i class="fas fa-user"></i>
                                        </div>
                                        <strong><?php echo htmlspecialchars($user->name ?? ''); ?></strong>
                                    </div>
                                </td>
                                <td class="align-middle"><?php echo htmlspecialchars($user->email ?? ''); ?></td>
                                <td class="align-middle"><?php echo htmlspecialchars($user->phone ?? 'Chưa cập nhật'); ?></td>
                                <td class="align-middle">
                                    <div class="btn-group">
                                        <?php
                                        $roleClass = 'badge-light text-dark';
                                        $roleText = 'Khách hàng';
                                        if ($user->role == 'admin') {
                                            $roleClass = 'badge-danger';
                                            $roleText = 'Admin';
                                        } elseif ($user->role == 'seller') {
                                            $roleClass = 'badge-warning';
                                            $roleText = 'Người bán';
                                        }
                                        ?>
                                        <button type="button" class="badge <?php echo $roleClass; ?> px-2 py-1 border-0 dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            <?php echo $roleText; ?>
                                        </button>
                                        <div class="dropdown-menu shadow-sm border-0">
                                            <h6 class="dropdown-header small text-muted">Phân quyền vai trò</h6>
                                            <a class="dropdown-item small" href="<?php echo BASE_URL; ?>index.php?url=Dashboard/updateRole/<?php echo $user->id; ?>/admin">
                                                <i class="fas fa-user-shield text-danger mr-2"></i> Admin
                                            </a>
                                            <a class="dropdown-item small" href="<?php echo BASE_URL; ?>index.php?url=Dashboard/updateRole/<?php echo $user->id; ?>/seller">
                                                <i class="fas fa-store text-warning mr-2"></i> Người bán
                                            </a>
                                            <a class="dropdown-item small" href="<?php echo BASE_URL; ?>index.php?url=Dashboard/updateRole/<?php echo $user->id; ?>/user">
                                                <i class="fas fa-user text-primary mr-2"></i> Khách hàng
                                            </a>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 text-right align-middle">
                                    <button class="btn btn-sm btn-outline-info mr-1 edit-user-btn" 
                                            data-id="<?php echo $user->id; ?>"
                                            data-name="<?php echo htmlspecialchars($user->name ?? ''); ?>"
                                            data-email="<?php echo htmlspecialchars($user->email ?? ''); ?>"
                                            data-phone="<?php echo htmlspecialchars($user->phone ?? ''); ?>"
                                            title="Sửa">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <a href="<?php echo BASE_URL; ?>index.php?url=Dashboard/deleteUser/<?php echo $user->id; ?>" 
                                       class="btn btn-sm btn-outline-danger" 
                                       onclick="return confirm('Xóa người dùng này? Thao tác này không thể hoàn tác.');" 
                                       title="Xóa">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                <p>Chưa có người dùng nào trong hệ thống.</p>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Add Employee Modal -->
<div class="modal fade" id="addEmployeeModal" tabindex="-1">
    <div class="modal-dialog">
        <form action="<?php echo BASE_URL; ?>index.php?url=Dashboard/addUser" method="POST">
            <div class="modal-content border-0 shadow">
                <div class="modal-header">
                    <h5 class="modal-title font-weight-bold">Thêm nhân viên/người dùng mới</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label class="font-weight-bold">Họ và tên</label>
                        <input type="text" name="name" class="form-control" placeholder="Nhập họ tên..." required>
                    </div>
                    <div class="form-group">
                        <label class="font-weight-bold">Email</label>
                        <input type="email" name="email" class="form-control" placeholder="example@gmail.com">
                    </div>
                    <div class="form-group">
                        <label class="font-weight-bold">Số điện thoại</label>
                        <input type="text" name="phone" class="form-control" placeholder="0123456789">
                    </div>
                    <div class="form-group">
                        <label class="font-weight-bold">Mật khẩu</label>
                        <input type="password" name="password" class="form-control" placeholder="Nhập mật khẩu..." required>
                    </div>
                    <div class="form-group">
                        <label class="font-weight-bold">Vai trò</label>
                        <select name="role" class="form-control">
                            <option value="seller">Người bán (Seller)</option>
                            <option value="admin">Quản trị viên (Admin)</option>
                            <option value="user">Khách hàng (User)</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer bg-light border-0">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-primary px-4">Tạo tài khoản</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Edit User Modal -->
<div class="modal fade" id="editUserModal" tabindex="-1">
    <div class="modal-dialog">
        <form id="editUserForm" method="POST">
            <div class="modal-content border-0 shadow">
                <div class="modal-header">
                    <h5 class="modal-title font-weight-bold">Chỉnh sửa thông tin người dùng</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label class="font-weight-bold">Tên người dùng</label>
                        <input type="text" name="name" id="edit_user_name" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label class="font-weight-bold">Email</label>
                        <input type="email" name="email" id="edit_user_email" class="form-control">
                    </div>
                    <div class="form-group">
                        <label class="font-weight-bold">Số điện thoại</label>
                        <input type="text" name="phone" id="edit_user_phone" class="form-control">
                    </div>
                </div>
                <div class="modal-footer bg-light border-0">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-primary px-4">Lưu thay đổi</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const editBtns = document.querySelectorAll('.edit-user-btn');
    const editForm = document.getElementById('editUserForm');
    const editName = document.getElementById('edit_user_name');
    const editEmail = document.getElementById('edit_user_email');
    const editPhone = document.getElementById('edit_user_phone');

    editBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            const name = this.getAttribute('data-name');
            const email = this.getAttribute('data-email');
            const phone = this.getAttribute('data-phone');

            editName.value = name;
            editEmail.value = email;
            editPhone.value = phone;
            editForm.action = '<?php echo BASE_URL; ?>index.php?url=Dashboard/updateUser/' + id;
            
            $('#editUserModal').modal('show');
        });
    });
});
</script>

<?php include 'app/views/dashboard/footer.php'; ?>