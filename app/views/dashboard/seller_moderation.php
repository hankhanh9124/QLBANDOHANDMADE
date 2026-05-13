<?php
// app/views/dashboard/seller_moderation.php
$action = 'manage_sellers';
include 'app/views/dashboard/header.php';
$requests = $requests ?? [];
?>

<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="font-weight-bold mb-1">Duyệt Người Bán Mới</h2>
            <p class="text-muted mb-0">Xem xét và phê duyệt các yêu cầu mở gian hàng handmade</p>
        </div>
        <div class="badge badge-primary px-3 py-2 shadow-sm" style="background: #75c794; border: none;">
            <?php echo count($requests); ?> yêu cầu đang chờ
        </div>
    </div>

    <?php if (isset($_SESSION['success_message'])): ?>
        <div class="alert alert-success border-0 shadow-sm mb-4 animate__animated animate__fadeIn">
            <i class="fas fa-check-circle mr-2"></i> <?php echo $_SESSION['success_message']; unset($_SESSION['success_message']); ?>
        </div>
    <?php endif; ?>

    <?php if (isset($_SESSION['error_message'])): ?>
        <div class="alert alert-danger border-0 shadow-sm mb-4 animate__animated animate__fadeIn">
            <i class="fas fa-exclamation-circle mr-2"></i> <?php echo $_SESSION['error_message']; unset($_SESSION['error_message']); ?>
        </div>
    <?php endif; ?>

    <div class="card border-0 shadow-sm rounded-lg overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="bg-light text-muted small text-uppercase">
                    <tr>
                        <th class="border-0 px-4 py-3">Người đăng ký</th>
                        <th class="border-0 py-3">Tên Shop</th>
                        <th class="border-0 py-3">Loại sản phẩm</th>
                        <th class="border-0 py-3">Ngày gửi</th>
                        <th class="border-0 px-4 py-3 text-right">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($requests)): ?>
                        <tr>
                            <td colspan="5" class="text-center py-5 text-muted">
                                <i class="fas fa-inbox fa-3x mb-3 d-block opacity-2"></i>
                                <p class="mb-0">Hiện không có yêu cầu nào cần xử lý.</p>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($requests as $request): ?>
                            <tr>
                                <td class="px-4 py-3 align-middle">
                                    <div class="font-weight-bold"><?php echo htmlspecialchars($request->user_name ?? ''); ?></div>
                                    <div class="small text-muted"><?php echo htmlspecialchars($request->user_email ?? ''); ?></div>
                                </td>
                                <td class="py-3 align-middle">
                                    <span class="font-weight-bold" style="color: #75c794;"><?php echo htmlspecialchars($request->shop_name ?? ''); ?></span>
                                </td>
                                <td class="py-3 align-middle">
                                    <span class="badge badge-light px-2 py-1"><?php echo htmlspecialchars($request->product_types ?? ''); ?></span>
                                </td>
                                <td class="py-3 align-middle text-muted small">
                                    <?php echo date('d/m/Y H:i', strtotime($request->created_at)); ?>
                                </td>
                                <td class="px-4 py-3 align-middle text-right">
                                    <button class="btn btn-sm btn-outline-info mr-1" data-toggle="modal" data-target="#modal-<?php echo $request->id; ?>">
                                        <i class="fas fa-eye mr-1"></i> Chi tiết
                                    </button>
                                    <a href="<?php echo BASE_URL; ?>index.php?url=Dashboard/approveSeller/<?php echo $request->id; ?>" 
                                       class="btn btn-sm btn-success" 
                                       style="background: #75c794; border: none;"
                                       onclick="return confirm('Xác nhận phê duyệt shop này?')">
                                        <i class="fas fa-check mr-1"></i> Duyệt
                                    </a>
                                </td>
                            </tr>

                            <!-- Detail Modal -->
                            <div class="modal fade" id="modal-<?php echo $request->id; ?>" tabindex="-1">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content border-0 shadow">
                                        <div class="modal-header border-0 text-white" style="background: #75c794;">
                                            <h5 class="modal-title font-weight-bold">Hồ sơ đăng ký: <?php echo htmlspecialchars($request->shop_name ?? ''); ?></h5>
                                            <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
                                        </div>
                                        <div class="modal-body p-4">
                                            <div class="row">
                                                <div class="col-md-7">
                                                    <h6 class="font-weight-bold text-uppercase small text-muted mb-3 border-bottom pb-2">Thông tin Shop</h6>
                                                    <p><strong>Tên Shop:</strong> <?php echo htmlspecialchars($request->shop_name ?? ''); ?></p>
                                                    <p><strong>Mô tả:</strong> <?php echo nl2br(htmlspecialchars($request->shop_description ?? '')); ?></p>
                                                    <p><strong>Sản phẩm:</strong> <?php echo htmlspecialchars($request->product_types ?? ''); ?></p>
                                                    <p><strong>Tài khoản:</strong> <span class="text-primary"><?php echo htmlspecialchars($request->bank_account ?? ''); ?></span></p>

                                                    <?php if ($request->portfolio_links): ?>
                                                        <p><strong>Portfolio:</strong> <a href="<?php echo $request->portfolio_links; ?>" target="_blank" class="text-info"><?php echo $request->portfolio_links; ?></a></p>
                                                    <?php endif; ?>
                                                </div>
                                                <div class="col-md-5">
                                                    <h6 class="font-weight-bold text-uppercase small text-muted mb-3 border-bottom pb-2">Giấy tờ xác minh</h6>
                                                    <?php if ($request->identity_proof): ?>
                                                        <div class="verification-img-container">
                                                            <img src="<?php echo BASE_URL . 'public/uploads/verification/' . $request->identity_proof; ?>" class="img-fluid rounded shadow-sm border" style="cursor: zoom-in;" onclick="window.open(this.src)">
                                                            <p class="small text-muted text-center mt-2">Nhấn vào ảnh để xem kích thước đầy đủ</p>
                                                        </div>
                                                    <?php else: ?>
                                                        <div class="alert alert-light text-center py-4 border">Không có ảnh xác minh</div>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal-footer border-0 bg-light">
                                            <button type="button" class="btn btn-secondary px-4" data-dismiss="modal">Đóng</button>
                                            <button class="btn btn-danger px-4" data-toggle="collapse" data-target="#reject-form-<?php echo $request->id; ?>">
                                                Từ chối
                                            </button>
                                            <a href="<?php echo BASE_URL; ?>index.php?url=Dashboard/approveSeller/<?php echo $request->id; ?>" class="btn btn-success px-4" style="background: #75c794; border: none;">Duyệt Shop</a>
                                        </div>
                                        <!-- Reject Reason Form -->
                                        <div class="collapse p-4 bg-white border-top" id="reject-form-<?php echo $request->id; ?>">
                                            <form action="<?php echo BASE_URL; ?>index.php?url=Dashboard/rejectSeller" method="POST">
                                                <input type="hidden" name="request_id" value="<?php echo $request->id; ?>">
                                                <div class="form-group">
                                                    <label class="font-weight-bold">Lý do từ chối <span class="text-danger">*</span></label>
                                                    <textarea name="reject_reason" class="form-control border-danger" rows="3" placeholder="Giải thích lý do không phê duyệt để người dùng chỉnh sửa..." required></textarea>
                                                </div>
                                                <div class="text-right">
                                                    <button type="submit" class="btn btn-danger px-4">Xác nhận Từ chối</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include 'app/views/dashboard/footer.php'; ?>

<style>
    .table td { vertical-align: middle; }
    .card { border-radius: 12px; }
    .badge-primary { background-color: #75c794; }
    .modal-content { border-radius: 15px; overflow: hidden; }
    .verification-img-container img:hover { transform: scale(1.02); transition: 0.3s; }
</style>
