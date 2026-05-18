<?php
// app/views/dashboard/manage_withdrawals.php
include 'app/views/dashboard/header.php';
?>
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="h3 font-weight-bold text-dark mb-0">Quản lý yêu cầu rút tiền</h2>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb bg-transparent mb-0 p-0">
                <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>index.php?url=Dashboard" class="text-muted">Admin</a></li>
                <li class="breadcrumb-item active text-primary" aria-current="page">Yêu cầu rút tiền</li>
            </ol>
        </nav>
    </div>

    <!-- Alert Messages -->
    <?php if (isset($_SESSION['success_message'])): ?>
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4" role="alert" style="border-left: 5px solid #2ecc71;">
            <i class="fas fa-check-circle mr-2 text-success"></i>
            <?php echo $_SESSION['success_message']; unset($_SESSION['success_message']); ?>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    <?php endif; ?>

    <?php if (isset($_SESSION['error_message'])): ?>
        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-4" role="alert" style="border-left: 5px solid #e74c3c;">
            <i class="fas fa-exclamation-circle mr-2 text-danger"></i>
            <?php echo $_SESSION['error_message']; unset($_SESSION['error_message']); ?>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    <?php endif; ?>

    <!-- Main Card -->
    <div class="card border-0 shadow-sm" style="border-radius: 16px;">
        <div class="card-header bg-white border-0 py-3 px-4 d-flex justify-content-between align-items-center" style="border-top-left-radius: 16px; border-top-right-radius: 16px;">
            <h5 class="mb-0 font-weight-bold text-dark"><i class="fas fa-university text-primary mr-2"></i> Danh sách yêu cầu rút tiền</h5>
        </div>
        <div class="card-body p-0">
            <?php if (empty($withdrawals)): ?>
                <div class="text-center py-5">
                    <img src="https://cdn-icons-png.flaticon.com/512/9908/9908124.png" alt="Empty" style="width: 120px; opacity: 0.7;">
                    <p class="text-muted mt-3">Không tìm thấy yêu cầu rút tiền nào trên hệ thống.</p>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light text-uppercase small text-muted font-weight-bold border-0">
                            <tr>
                                <th class="border-0 px-4">Mã yêu cầu</th>
                                <th class="border-0">Thời gian tạo</th>
                                <th class="border-0">Người bán (Seller)</th>
                                <th class="border-0">Tài khoản Ngân hàng nhận</th>
                                <th class="border-0">Số tiền rút</th>
                                <th class="border-0 text-center">Trạng thái</th>
                                <th class="border-0 text-center px-4">Hành động</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($withdrawals as $wdr): ?>
                                <tr style="transition: background-color 0.2s;">
                                    <td class="px-4 font-weight-bold text-dark align-middle">
                                        <code><?php echo $wdr->request_code; ?></code>
                                    </td>
                                    <td class="align-middle text-secondary small">
                                        <?php echo date('H:i d/m/Y', strtotime($wdr->created_at)); ?>
                                    </td>
                                    <td class="align-middle text-dark font-weight-bold">
                                        <?php echo htmlspecialchars($wdr->seller_name ?? 'Không rõ'); ?>
                                    </td>
                                    <td class="align-middle">
                                        <div class="d-flex flex-column" style="background-color: #f8f9fa; padding: 10px; border-radius: 8px; border: 1px dashed #dee2e6; min-width: 250px;">
                                            <span class="text-dark font-weight-bold" style="font-size: 0.9rem;">
                                                <i class="fas fa-university text-muted mr-1"></i> <?php echo htmlspecialchars($wdr->bank_name); ?>
                                            </span>
                                            <span class="text-secondary small">
                                                <i class="fas fa-credit-card text-muted mr-1"></i> STK: <strong class="text-dark"><?php echo htmlspecialchars($wdr->bank_account); ?></strong>
                                            </span>
                                            <span class="text-secondary small text-uppercase">
                                                <i class="fas fa-user text-muted mr-1"></i> Chủ TK: <strong><?php echo htmlspecialchars($wdr->bank_owner); ?></strong>
                                            </span>
                                        </div>
                                    </td>
                                    <td class="align-middle font-weight-bold text-pink" style="font-size: 1.15rem; color: #c2255c;">
                                        <?php echo number_format($wdr->amount, 0, ',', '.'); ?> ₫
                                    </td>
                                    <td class="align-middle text-center">
                                        <?php if ($wdr->status === 'pending'): ?>
                                            <span class="badge badge-warning px-3 py-2 text-white" style="border-radius: 30px; font-weight: 600;">Chờ duyệt</span>
                                        <?php elseif ($wdr->status === 'approved'): ?>
                                            <span class="badge badge-success px-3 py-2" style="border-radius: 30px; font-weight: 600;">Đã duyệt</span>
                                        <?php else: ?>
                                            <span class="badge badge-danger px-3 py-2" style="border-radius: 30px; font-weight: 600;">Từ chối</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="align-middle px-4 text-center">
                                        <?php if ($wdr->status === 'pending'): ?>
                                            <div class="btn-group" role="group">
                                                <a href="<?php echo BASE_URL; ?>index.php?url=Dashboard/approveWithdrawal/<?php echo $wdr->id; ?>" 
                                                   class="btn btn-sm btn-success font-weight-bold px-3 py-2" 
                                                   style="border-radius: 8px 0 0 8px;"
                                                   onclick="return confirm('Bạn xác nhận ĐÃ CHUYỂN KHOẢN số tiền này và muốn DUYỆT yêu cầu?')">
                                                    <i class="fas fa-check mr-1"></i> Duyệt
                                                </a>
                                                <a href="<?php echo BASE_URL; ?>index.php?url=Dashboard/rejectWithdrawal/<?php echo $wdr->id; ?>" 
                                                   class="btn btn-sm btn-danger font-weight-bold px-3 py-2" 
                                                   style="border-radius: 0 8px 8px 0;"
                                                   onclick="return confirm('Bạn xác nhận muốn TỪ CHỐI yêu cầu rút tiền này? Số tiền sẽ được hoàn trả lại cho Seller.')">
                                                    <i class="fas fa-times mr-1"></i> Từ chối
                                                </a>
                                            </div>
                                        <?php else: ?>
                                            <span class="text-muted small"><i class="fas fa-lock mr-1"></i> Đã xử lý</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<style>
    .table th {
        font-weight: 700;
        border-top: none !important;
    }
    .table td {
        border-top: 1px solid #f1f3f5 !important;
    }
    .btn-group .btn {
        transition: all 0.2s;
    }
    .btn-group .btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 6px rgba(0,0,0,0.08);
    }
</style>

<?php
include 'app/views/dashboard/footer.php';
?>
