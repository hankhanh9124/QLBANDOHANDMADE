<?php 
$action = 'categories';
include 'app/views/dashboard/header.php'; 
?>

<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">Danh sách danh mục</h2>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <table class="table table-hover mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="border-0 px-4">ID</th>
                        <th class="border-0">Tên danh mục</th>
                        <th class="border-0">Mô tả</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($categories)): ?>
                        <?php foreach ($categories as $cat): ?>
                            <tr>
                                <td class="px-4"><?php echo $cat->id; ?></td>
                                <td><strong><?php echo htmlspecialchars($cat->name); ?></strong></td>
                                <td class="text-muted"><?php echo htmlspecialchars($cat->description); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="3" class="text-center py-5 text-muted">
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

<?php include 'app/views/dashboard/footer.php'; ?>
