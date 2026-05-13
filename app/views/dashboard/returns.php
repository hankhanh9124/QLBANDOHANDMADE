<?php 
/**
 * @var array $returns
 */
include 'app/views/dashboard/header.php'; 
?>

<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Quản lý Trả hàng / Hoàn tiền</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb bg-transparent p-0 mt-2">
                    <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>index.php?url=Dashboard">Tổng quan</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Yêu cầu trả hàng</li>
                </ol>
            </nav>
        </div>
    </div>

    <?php if (isset($_SESSION['success_message'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle mr-2"></i> <?php echo $_SESSION['success_message']; unset($_SESSION['success_message']); ?>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    <?php endif; ?>

    <div class="card shadow-sm border-0 mb-4" style="border-radius: 15px; overflow: hidden;">
        <div class="card-header bg-white py-3 border-0">
            <div class="row align-items-center">
                <div class="col">
                    <h5 class="m-0 font-weight-bold text-dark d-flex align-items-center">
                        <i class="fas fa-exchange-alt mr-2 text-primary"></i> Danh sách khiếu nại
                    </h5>
                </div>
                <div class="col-auto">
                    <span class="badge badge-primary px-3 py-2" style="border-radius: 10px; font-weight: 600;">
                        <?php echo count($returns ?? []); ?> Yêu cầu
                    </span>
                </div>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-items-center mb-0" style="border-collapse: separate; border-spacing: 0;">
                    <thead>
                        <tr class="bg-light text-muted small text-uppercase" style="border-bottom: 2px solid #f8f9fc;">
                            <th class="px-4 py-3">Mã yêu cầu</th>
                            <th class="py-3">Đơn hàng</th>
                            <th class="py-3">Khách hàng</th>
                            <th class="py-3">Lý do</th>
                            <th class="py-3 text-right">Số tiền</th>
                            <th class="py-3 text-center">Trạng thái</th>
                            <th class="py-3 text-center">Ngày gửi</th>
                            <th class="px-4 py-3 text-right">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($returns)): ?>
                            <tr>
                                <td colspan="8" class="text-center py-5 text-muted">
                                    <div class="py-4">
                                        <i class="fas fa-inbox fa-3x mb-3 d-block opacity-25"></i>
                                        <h6 class="font-weight-bold">Chưa có yêu cầu nào</h6>
                                        <p class="small mb-0">Tất cả yêu cầu trả hàng sẽ xuất hiện tại đây.</p>
                                    </div>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($returns as $r): ?>
                                <tr style="transition: all 0.2s; border-bottom: 1px solid #f8f9fc;">
                                    <td class="px-4 py-4">
                                        <span class="font-weight-bold text-dark" style="font-size: 0.9rem;">#RET-<?php echo str_pad($r->id, 4, '0', STR_PAD_LEFT); ?></span>
                                    </td>
                                    <td class="py-4">
                                        <a href="<?php echo BASE_URL; ?>index.php?url=Dashboard/orderDetail/<?php echo $r->order_id; ?>" class="text-primary font-weight-bold hover-underline" style="font-size: 0.9rem;">
                                            #<?php echo $r->order_number; ?>
                                        </a>
                                    </td>
                                    <td class="py-4">
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-sm mr-2 bg-gradient-primary rounded-circle d-flex align-items-center justify-content-center text-white" style="width: 32px; height: 32px; background: #eef2ff;">
                                                <i class="fas fa-user text-primary small"></i>
                                            </div>
                                            <span class="text-dark font-weight-500"><?php echo htmlspecialchars($r->user_name ?? ''); ?></span>
                                        </div>
                                    </td>
                                    <td class="py-4">
                                        <span class="text-truncate d-inline-block text-muted" style="max-width: 150px; font-size: 0.85rem;" title="<?php echo htmlspecialchars($r->reason ?? ''); ?>">
                                            <?php echo htmlspecialchars($r->reason ?? ''); ?>
                                        </span>
                                    </td>
                                    <td class="py-4 text-right">
                                        <span class="font-weight-bold text-danger" style="font-size: 1rem;">₫<?php echo number_format($r->amount, 0, ',', '.'); ?></span>
                                    </td>
                                    <td class="py-4 text-center">
                                        <?php 
                                        $badgeStyle = '';
                                        $statusText = '';
                                        switch($r->status) {
                                            case 'pending': 
                                                $badgeStyle = 'background: #fff8e1; color: #f6a700; border: 1px solid #ffe082;'; 
                                                $statusText = 'Chờ xử lý'; break;
                                            case 'reviewing': 
                                                $badgeStyle = 'background: #e1f5fe; color: #0288d1; border: 1px solid #b3e5fc;'; 
                                                $statusText = 'Đang xem xét'; break;
                                            case 'approved': 
                                                $badgeStyle = 'background: #e8f5e9; color: #2e7d32; border: 1px solid #c8e6c9;'; 
                                                $statusText = 'Đã phê duyệt'; break;
                                            case 'rejected': 
                                                $badgeStyle = 'background: #ffebee; color: #c62828; border: 1px solid #ffcdd2;'; 
                                                $statusText = 'Bị từ chối'; break;
                                            case 'refunded': 
                                                $badgeStyle = 'background: #f3e5f5; color: #7b1fa2; border: 1px solid #e1bee7;'; 
                                                $statusText = 'Đã hoàn tiền'; break;
                                        }
                                        ?>
                                        <span class="badge px-3 py-2" style="<?php echo $badgeStyle; ?> border-radius: 6px; font-weight: 700; font-size: 0.75rem; letter-spacing: 0.3px;">
                                            <?php echo $statusText; ?>
                                        </span>
                                    </td>
                                    <td class="py-4 text-center text-muted small">
                                        <?php echo date('d/m/Y', strtotime($r->created_at)); ?>
                                    </td>
                                    <td class="px-4 py-4 text-right">
                                        <a href="<?php echo BASE_URL; ?>index.php?url=Dashboard/returnDetail/<?php echo $r->id; ?>" class="btn btn-sm btn-white shadow-sm border" style="border-radius: 8px; font-weight: 600; padding: 6px 12px; transition: all 0.2s;">
                                            <i class="fas fa-eye mr-1 text-primary"></i> Xem chi tiết
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer bg-white border-0 py-3">
            <div class="row align-items-center">
                <div class="col-sm-6">
                    <p class="mb-0 text-muted small">Hiển thị <?php echo count($returns); ?> yêu cầu</p>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .breadcrumb-item + .breadcrumb-item::before { content: "\f105"; font-family: "Font Awesome 5 Free"; font-weight: 900; color: #ccc; }
    .table thead th { font-size: 0.85rem; text-transform: uppercase; letter-spacing: 1px; color: #858796; }
    .table tbody td { vertical-align: middle; font-size: 0.9rem; }
    .btn-white:hover { background-color: #f8f9fa; transform: translateY(-1px); }
</style>

<?php include 'app/views/dashboard/footer.php'; ?>
